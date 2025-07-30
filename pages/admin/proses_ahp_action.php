<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/ahp_functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];

if (isset($_POST['submit_kriteria'])) {
    $kriteria_values = $_POST['kriteria'];
    $matrix = [];

    // Build the matrix and save comparisons
    $pdo->beginTransaction();
    try {
        $stmt_delete = $pdo->prepare("DELETE FROM perbandingan_berpasangan WHERE id_pengguna = ?");
        $stmt_delete->execute([$id_pengguna]);

        $stmt_insert = $pdo->prepare("INSERT INTO perbandingan_berpasangan (id_kriteria_1, id_kriteria_2, nilai, id_pengguna) VALUES (?, ?, ?, ?)");

        foreach ($kriteria_values as $id1 => $row) {
            foreach ($row as $id2 => $nilai) {
                $matrix[$id1][$id2] = $nilai;
                if ($id1 <= $id2) {
                    $stmt_insert->execute([$id1, $id2, $nilai, $id_pengguna]);
                    if ($id1 != $id2) {
                        $stmt_insert->execute([$id2, $id1, 1 / $nilai, $id_pengguna]);
                        $matrix[$id2][$id1] = 1 / $nilai;
                    }
                }
            }
        }

        $normalized = normalizeMatrix($matrix);
        $weights = calculateWeights($normalized);
        $consistency = calculateConsistency($matrix, $weights);

        $stmt_delete_hasil = $pdo->prepare("DELETE FROM hasil_ahp WHERE id_pengguna = ?");
        $stmt_delete_hasil->execute([$id_pengguna]);

        $stmt_insert_hasil = $pdo->prepare("INSERT INTO hasil_ahp (jumlah_kriteria, bobot_kriteria, lambda_max, ci, cr, id_pengguna) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert_hasil->execute([count($matrix), json_encode($weights), $consistency['lambda_max'], $consistency['ci'], $consistency['cr'], $id_pengguna]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }

    header("Location: hasil_ahp.php");
    exit();

} elseif (isset($_POST['submit_alternatif'])) {
    $id_kriteria = $_POST['id_kriteria'];
    $alternatif_values = $_POST['alternatif'];
    $matrix = [];

    $pdo->beginTransaction();
    try {
        $stmt_delete = $pdo->prepare("DELETE FROM perbandingan_alternatif WHERE id_kriteria = ? AND id_pengguna = ?");
        $stmt_delete->execute([$id_kriteria, $id_pengguna]);

        $stmt_insert = $pdo->prepare("INSERT INTO perbandingan_alternatif (id_kriteria, id_alternatif_1, id_alternatif_2, nilai, id_pengguna) VALUES (?, ?, ?, ?, ?)");

        foreach ($alternatif_values as $id1 => $row) {
            foreach ($row as $id2 => $nilai) {
                $matrix[$id1][$id2] = $nilai;
                if ($id1 <= $id2) {
                    $stmt_insert->execute([$id_kriteria, $id1, $id2, $nilai, $id_pengguna]);
                    if ($id1 != $id2) {
                        $stmt_insert->execute([$id_kriteria, $id2, $id1, 1 / $nilai, $id_pengguna]);
                        $matrix[$id2][$id1] = 1 / $nilai;
                    }
                }
            }
        }

        $normalized = normalizeMatrix($matrix);
        $weights = calculateWeights($normalized);

        $stmt_delete_hasil = $pdo->prepare("DELETE FROM hasil_alternatif WHERE id_kriteria = ? AND id_pengguna = ?");
        $stmt_delete_hasil->execute([$id_kriteria, $id_pengguna]);

        $stmt_insert_hasil = $pdo->prepare("INSERT INTO hasil_alternatif (id_kriteria, id_alternatif, bobot, id_pengguna) VALUES (?, ?, ?, ?)");
        foreach ($weights as $id_alternatif => $bobot) {
            $stmt_insert_hasil->execute([$id_kriteria, $id_alternatif, $bobot, $id_pengguna]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }

    header("Location: hasil_ahp.php");
    exit();
}
?>