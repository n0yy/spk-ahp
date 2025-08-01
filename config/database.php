<?php
$host = 'localhost';
$dbname = 'spk_ahp_rs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
