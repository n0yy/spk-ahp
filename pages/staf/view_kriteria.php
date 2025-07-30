<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM kriteria");
$kriteria = $stmt->fetchAll();

include '../../includes/header.php';
?>

<h2>Daftar Kriteria</h2>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kriteria as $row): ?>
                    <tr>
                        <td><?php echo $row['kode_kriteria']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>