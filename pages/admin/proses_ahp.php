<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Fetch kriteria and alternatif
$stmt_kriteria = $pdo->query("SELECT * FROM kriteria");
$kriteria = $stmt_kriteria->fetchAll();
$stmt_alternatif = $pdo->query("SELECT * FROM alternatif");
$alternatif = $stmt_alternatif->fetchAll();

include '../../includes/header.php';
?>

<h2>Proses AHP</h2>

<!-- Perbandingan Kriteria -->
<div class="card mb-4">
    <div class="card-header">Perbandingan Kriteria</div>
    <div class="card-body">
        <form action="proses_ahp_action.php" method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kriteria 1</th>
                        <th>Nilai</th>
                        <th>Kriteria 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($kriteria); $i++): ?>
                        <?php for ($j = $i; $j < count($kriteria); $j++): ?>
                            <tr>
                                <td><?php echo $kriteria[$i]['nama']; ?></td>
                                <td>
                                    <input type="number" step="0.01" name="kriteria[<?php echo $kriteria[$i]['id_kriteria']; ?>][<?php echo $kriteria[$j]['id_kriteria']; ?>]" class="form-control" value="1" <?php echo ($i == $j) ? 'readonly' : ''; ?>>
                                </td>
                                <td><?php echo $kriteria[$j]['nama']; ?></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </tbody>
            </table>
            <button type="submit" name="submit_kriteria" class="btn btn-primary">Hitung Bobot Kriteria</button>
        </form>
    </div>
</div>

<!-- Perbandingan Alternatif -->
<?php foreach ($kriteria as $k): ?>
<div class="card mb-4">
    <div class="card-header">Perbandingan Alternatif berdasarkan Kriteria: <?php echo $k['nama']; ?></div>
    <div class="card-body">
        <form action="proses_ahp_action.php" method="POST">
            <input type="hidden" name="id_kriteria" value="<?php echo $k['id_kriteria']; ?>">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Alternatif 1</th>
                        <th>Nilai</th>
                        <th>Alternatif 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                        <?php for ($j = $i; $j < count($alternatif); $j++): ?>
                            <tr>
                                <td><?php echo $alternatif[$i]['nama']; ?></td>
                                <td>
                                    <input type="number" step="0.01" name="alternatif[<?php echo $alternatif[$i]['id_alternatif']; ?>][<?php echo $alternatif[$j]['id_alternatif']; ?>]" class="form-control" value="1" <?php echo ($i == $j) ? 'readonly' : ''; ?>>
                                </td>
                                <td><?php echo $alternatif[$j]['nama']; ?></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </tbody>
            </table>
            <button type="submit" name="submit_alternatif" class="btn btn-primary">Hitung Bobot Alternatif</button>
        </form>
    </div>
</div>
<?php endforeach; ?>

<?php include '../../includes/footer.php'; ?>