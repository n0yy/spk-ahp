<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <h2 class="fw-bold text-primary">Dashboard</h2>
                <p class="text-muted mb-1">Selamat datang, <strong><?= $username ?></strong>.</p>
                <p class="mb-0"><span class="badge bg-info text-dark"><?= ucfirst($role) ?></span></p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <?php if ($role === 'admin'): ?>
                    <?php include 'pages/admin/dashboard_admin.php'; ?>
                <?php else: ?>
                    <?php include 'pages/staf/dashboard_staf.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>