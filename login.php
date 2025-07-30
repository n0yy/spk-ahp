<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
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
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK AHP - Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>
         
    <div class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Login Card -->
                    <div class="col-md-5 col-lg-4">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold text-primary">Login</h3>
                                    <p class="text-muted mb-0">Masuk ke akun Anda</p>
                                </div>

                                <?php if ($error): ?>
                                    <div class="alert alert-danger alert-sm py-2"><?= htmlspecialchars($error) ?></div>
                                <?php endif; ?>

                                <form action="login.php" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label small fw-bold">Username</label>
                                        <input type="text" class="form-control form-control-sm" id="username" name="username" required autofocus>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label small fw-bold">Password</label>
                                        <input type="password" class="form-control form-control-sm" id="password" name="password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-sm">Login</button>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <small>
                                        Belum punya akun? 
                                        <a href="register.php" class="text-decoration-none text-primary fw-medium">Daftar</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Section (opsional, bisa dihilangkan jika ingin lebih minimal) -->
                    <div class="col-md-5 col-lg-4 d-none d-md-block">
                        <div class="bg-light p-4 h-100 d-flex flex-column justify-content-center rounded-4">
                            <h5 class="fw-bold text-dark">SPK AHP Rumah Sakit</h5>
                            <p class="text-muted small mb-3">
                                Sistem Pendukung Keputusan menggunakan metode <strong>Analytic Hierarchy Process (AHP)</strong> untuk menentukan prioritas layanan rumah sakit.
                            </p>
                            <ul class="list-unstyled small text-muted">
                                <li class="mb-1">• Analisis perbandingan berpasangan</li>
                                <li class="mb-1">• Penentuan bobot kriteria</li>
                                <li class="mb-1">• Rekomendasi perbaikan layanan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
        
</body>
</html>

<?php include 'includes/footer.php'; ?>