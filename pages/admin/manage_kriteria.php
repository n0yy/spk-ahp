<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $kode = $_POST['kode_kriteria'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $pdo->prepare("INSERT INTO kriteria (kode_kriteria, nama, deskripsi) VALUES (?, ?, ?)");
        $stmt->execute([$kode, $nama, $deskripsi]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id_kriteria'];
        $kode = $_POST['kode_kriteria'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $pdo->prepare("UPDATE kriteria SET kode_kriteria = ?, nama = ?, deskripsi = ? WHERE id_kriteria = ?");
        $stmt->execute([$kode, $nama, $deskripsi, $id]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM kriteria WHERE id_kriteria = ?");
    $stmt->execute([$id]);
}

// Fetch all kriteria
$stmt = $pdo->query("SELECT * FROM kriteria");
$kriteria = $stmt->fetchAll();

include '../../includes/header.php';
?>

<h2>Manajemen Kriteria</h2>

<!-- Add/Edit Form -->
<div class="card mb-4">
    <div class="card-header">
        <?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah'; ?> Kriteria
    </div>
    <div class="card-body">
        <form action="manage_kriteria.php" method="POST">
            <?php if (isset($_GET['edit'])): ?>
                <?php
                $id = $_GET['edit'];
                $stmt = $pdo->prepare("SELECT * FROM kriteria WHERE id_kriteria = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch();
                ?>
                <input type="hidden" name="id_kriteria" value="<?php echo $data['id_kriteria']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="kode_kriteria" class="form-label">Kode Kriteria</label>
                <input type="text" class="form-control" id="kode_kriteria" name="kode_kriteria" value="<?php echo $data['kode_kriteria'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kriteria</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $data['nama'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi"><?php echo $data['deskripsi'] ?? ''; ?></textarea>
            </div>

            <?php if (isset($_GET['edit'])): ?>
                <button type="submit" name="edit" class="btn btn-primary">Update</button>
                <a href="manage_kriteria.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Kriteria List -->
<div class="card">
    <div class="card-header">Daftar Kriteria</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kriteria as $row): ?>
                    <tr>
                        <td><?php echo $row['kode_kriteria']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td>
                            <a href="manage_kriteria.php?edit=<?php echo $row['id_kriteria']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manage_kriteria.php?delete=<?php echo $row['id_kriteria']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>