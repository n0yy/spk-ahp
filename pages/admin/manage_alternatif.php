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
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $pdo->prepare("INSERT INTO alternatif (nama, deskripsi) VALUES (?, ?)");
        $stmt->execute([$nama, $deskripsi]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id_alternatif'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $pdo->prepare("UPDATE alternatif SET nama = ?, deskripsi = ? WHERE id_alternatif = ?");
        $stmt->execute([$nama, $deskripsi, $id]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM alternatif WHERE id_alternatif = ?");
    $stmt->execute([$id]);
}

// Fetch all alternatif
$stmt = $pdo->query("SELECT * FROM alternatif");
$alternatif = $stmt->fetchAll();

include '../../includes/header.php';
?>

<h2>Manajemen Alternatif</h2>

<!-- Add/Edit Form -->
<div class="card mb-4">
    <div class="card-header">
        <?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah'; ?> Alternatif
    </div>
    <div class="card-body">
        <form action="manage_alternatif.php" method="POST">
            <?php if (isset($_GET['edit'])): ?>
                <?php
                $id = $_GET['edit'];
                $stmt = $pdo->prepare("SELECT * FROM alternatif WHERE id_alternatif = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch();
                ?>
                <input type="hidden" name="id_alternatif" value="<?php echo $data['id_alternatif']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Alternatif</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $data['nama'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi"><?php echo $data['deskripsi'] ?? ''; ?></textarea>
            </div>

            <?php if (isset($_GET['edit'])): ?>
                <button type="submit" name="edit" class="btn btn-primary">Update</button>
                <a href="manage_alternatif.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Alternatif List -->
<div class="card">
    <div class="card-header">Daftar Alternatif</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alternatif as $row): ?>
                    <tr>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td>
                            <a href="manage_alternatif.php?edit=<?php echo $row['id_alternatif']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manage_alternatif.php?delete=<?php echo $row['id_alternatif']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>