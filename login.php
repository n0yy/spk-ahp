<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id_pengguna'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

include 'includes/header.php';
?>

<div class="row align-items-center justify-content-center" style="min-height: 80vh;">
    <!-- Kolom Overview Proyek -->
    <div class="col-md-6 login-overview">
        <h1>Selamat Datang di SPK AHP</h1>
        <p class="lead">Sistem Pendukung Keputusan untuk Prioritas Peningkatan Layanan Rumah Sakit.</p>
        <hr style="border-color: var(--senja-primary);">
        <p>Aplikasi ini menggunakan metode <strong>Analytic Hierarchy Process (AHP)</strong> untuk membantu manajemen dalam mengambil keputusan strategis. Dengan sistem ini, Anda dapat:</p>
        <ul>
            <li>Mendefinisikan kriteria evaluasi layanan.</li>
            <li>Menentukan alternatif-alternatif perbaikan.</li>
            <li>Melakukan penilaian perbandingan berpasangan untuk mendapatkan bobot prioritas.</li>
            <li>Melihat hasil akhir berupa peringkat alternatif yang paling direkomendasikan.</li>
        </ul>
        <p>Silakan login untuk memulai atau hubungi administrator jika Anda belum memiliki akun.</p>
    </div>

    <!-- Kolom Form Login -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="register.php" class="text-decoration-none">Belum punya akun? Daftar di sini.</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>