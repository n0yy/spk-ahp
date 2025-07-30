<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// For simplicity, we show results from the admin user (id_pengguna = 1)
// In a real-world scenario, you might want a more sophisticated way to determine which results to show.
$id_pengguna_admin = 1;

// Fetch data
$stmt_kriteria = $pdo->query("SELECT id_kriteria, nama FROM kriteria");
$kriteria = $stmt_kriteria->fetchAll(PDO::FETCH_KEY_PAIR);

$stmt_alternatif = $pdo->query("SELECT id_alternatif, nama FROM alternatif");
$alternatif = $stmt_alternatif->fetchAll(PDO::FETCH_KEY_PAIR);

$stmt_hasil_kriteria = $pdo->prepare("SELECT * FROM hasil_ahp WHERE id_pengguna = ?");
$stmt_hasil_kriteria->execute([$id_pengguna_admin]);
$hasil_kriteria = $stmt_hasil_kriteria->fetch();

$stmt_hasil_alternatif = $pdo->prepare("SELECT * FROM hasil_alternatif WHERE id_pengguna = ?");
$stmt_hasil_alternatif->execute([$id_pengguna_admin]);
$hasil_alternatif_raw = $stmt_hasil_alternatif->fetchAll();

$hasil_alternatif = [];
foreach ($hasil_alternatif_raw as $row) {
    $hasil_alternatif[$row['id_kriteria']][$row['id_alternatif']] = $row['bobot'];
}

// Calculate final scores
$final_scores = [];
if ($hasil_kriteria && $hasil_alternatif) {
    $bobot_kriteria = json_decode($hasil_kriteria['bobot_kriteria'], true);
    foreach ($alternatif as $id_alt => $nama_alt) {
        $final_scores[$id_alt] = 0;
        foreach ($kriteria as $id_kri => $nama_kri) {
            $final_scores[$id_alt] += ($bobot_kriteria[$id_kri] ?? 0) * ($hasil_alternatif[$id_kri][$id_alt] ?? 0);
        }
    }
    arsort($final_scores);
}

include '../../includes/header.php';
?>

<h2>Hasil Akhir Peringkat</h2>

<?php if ($final_scores): ?>
<div class="card">
    <div class="card-header">Peringkat Alternatif</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Alternatif</th>
                    <th>Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($final_scores as $id_alt => $skor): ?>
                <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo $alternatif[$id_alt]; ?></td>
                    <td><?php echo number_format($skor, 4); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">Hasil perhitungan belum tersedia.</div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>