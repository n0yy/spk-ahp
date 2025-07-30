<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];

// Fetch data
$stmt_kriteria = $pdo->query("SELECT id_kriteria, nama FROM kriteria");
$kriteria = $stmt_kriteria->fetchAll(PDO::FETCH_KEY_PAIR);

$stmt_alternatif = $pdo->query("SELECT id_alternatif, nama FROM alternatif");
$alternatif = $stmt_alternatif->fetchAll(PDO::FETCH_KEY_PAIR);

$stmt_hasil_kriteria = $pdo->prepare("SELECT * FROM hasil_ahp WHERE id_pengguna = ?");
$stmt_hasil_kriteria->execute([$id_pengguna]);
$hasil_kriteria = $stmt_hasil_kriteria->fetch();

$stmt_hasil_alternatif = $pdo->prepare("SELECT * FROM hasil_alternatif WHERE id_pengguna = ?");
$stmt_hasil_alternatif->execute([$id_pengguna]);
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

<h2>Hasil Perhitungan AHP</h2>

<!-- Hasil Kriteria -->
<?php if ($hasil_kriteria): ?>
<div class="card mb-4">
    <div class="card-header">Bobot Kriteria</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kriteria</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (json_decode($hasil_kriteria['bobot_kriteria'], true) as $id => $bobot): ?>
                <tr>
                    <td><?php echo $kriteria[$id] ?? 'N/A'; ?></td>
                    <td><?php echo number_format($bobot, 4); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Lambda Max:</strong> <?php echo number_format($hasil_kriteria['lambda_max'], 4); ?></p>
        <p><strong>CI (Consistency Index):</strong> <?php echo number_format($hasil_kriteria['ci'], 4); ?></p>
        <p><strong>CR (Consistency Ratio):</strong> <?php echo number_format($hasil_kriteria['cr'], 4); ?> (<?php echo ($hasil_kriteria['cr'] <= 0.1) ? '<span class="text-success">Konsisten</span>' : '<span class="text-danger">Tidak Konsisten</span>'; ?>)</p>
    </div>
</div>
<?php endif; ?>

<!-- Hasil Alternatif -->
<?php if ($hasil_alternatif): ?>
<div class="card mb-4">
    <div class="card-header">Bobot Alternatif per Kriteria</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($kriteria as $nama_kri): ?>
                        <th><?php echo $nama_kri; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alternatif as $id_alt => $nama_alt): ?>
                <tr>
                    <td><?php echo $nama_alt; ?></td>
                    <?php foreach ($kriteria as $id_kri => $nama_kri): ?>
                        <td><?php echo number_format($hasil_alternatif[$id_kri][$id_alt] ?? 0, 4); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Peringkat Akhir -->
<?php if ($final_scores): ?>
<div class="card">
    <div class="card-header">Peringkat Akhir</div>
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
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>