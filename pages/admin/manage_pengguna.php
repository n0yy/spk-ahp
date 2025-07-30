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
        $username = $_POST['username'];
        $password = md5($_POST['password']); // MD5 for simplicity, consider password_hash()
        $email = $_POST['email'];
        $role = $_POST['role'];
        $stmt = $pdo->prepare("INSERT INTO pengguna (nama, username, password, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $username, $password, $email, $role]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id_pengguna'];
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = !empty($_POST['password']) ? md5($_POST['password']) : null;

        if ($password) {
            $stmt = $pdo->prepare("UPDATE pengguna SET nama = ?, username = ?, email = ?, role = ?, password = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama, $username, $email, $role, $password, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE pengguna SET nama = ?, username = ?, email = ?, role = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama, $username, $email, $role, $id]);
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$id]);
}

// Fetch all pengguna
$stmt = $pdo->query("SELECT * FROM pengguna");
$pengguna = $stmt->fetchAll();

include '../../includes/header.php';
?>

<h2>Manajemen Pengguna</h2>

<!-- Add/Edit Form -->
<div class="card mb-4">
    <div class="card-header">
        <?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah'; ?> Pengguna
    </div>
    <div class="card-body">
        <form action="manage_pengguna.php" method="POST">
            <?php if (isset($_GET['edit'])): ?>
                <?php
                $id = $_GET['edit'];
                $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch();
                ?>
                <input type="hidden" name="id_pengguna" value="<?php echo $data['id_pengguna']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $data['nama'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $data['username'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $data['email'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password <?php echo isset($_GET['edit']) ? '(Kosongkan jika tidak ingin diubah)' : ''; ?></label>
                <input type="password" class="form-control" id="password" name="password" <?php echo !isset($_GET['edit']) ? 'required' : ''; ?>>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin" <?php echo (isset($data) && $data['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="staf" <?php echo (isset($data) && $data['role'] == 'staf') ? 'selected' : ''; ?>>Staf</option>
                </select>
            </div>

            <?php if (isset($_GET['edit'])): ?>
                <button type="submit" name="edit" class="btn btn-primary">Update</button>
                <a href="manage_pengguna.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Pengguna List -->
<div class="card">
    <div class="card-header">Daftar Pengguna</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pengguna as $row): ?>
                    <tr>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <a href="manage_pengguna.php?edit=<?php echo $row['id_pengguna']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manage_pengguna.php?delete=<?php echo $row['id_pengguna']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>