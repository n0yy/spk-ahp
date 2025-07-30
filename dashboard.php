<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Adjust the base path for includes
$base_path = __DIR__;

include $base_path . '/includes/header.php';
?>

<h1>Dashboard</h1>
<p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
<p>Peran Anda: <?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></p>

<?php
if ($role === 'admin') {
    // No need to include, the links are now correct
    include $base_path . '/pages/admin/dashboard_admin.php';
} else {
    include $base_path . '/pages/staf/dashboard_staf.php';
}
?>

<?php include $base_path . '/includes/footer.php'; ?>