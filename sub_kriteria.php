<?php
include "includes/sidebar.php";
include "config/database.php";

$success = '';
$error = '';

// Tambah sub-kriteria
if (isset($_POST['tambah_sub'])) {
    $nama = $_POST['nama'];
    $kriteria_id = $_POST['kriteria_id'];
    $kategori_id = $_POST['kategori_id'];

    if (!empty($nama) && !empty($kriteria_id) && !empty($kategori_id)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sub_kriteria (nama, kriteria_id, kategori_id) VALUES (?, ?, ?)");
            $stmt->execute([$nama, $kriteria_id, $kategori_id]);
            $success = "Sub-kriteria berhasil ditambahkan.";
        } catch (PDOException $e) {
            $error = "Gagal menambah sub-kriteria: " . $e->getMessage();
        }
    } else {
        $error = "Nama, Kriteria, dan Kategori wajib diisi.";
    }
}

// Update sub-kriteria
if (isset($_POST['update'])) {
    $id = $_POST['id_sub'];
    $nama = $_POST['nama_sub'];
    $kategori_id = $_POST['kategori_id'];
    
    if (!empty($nama) && !empty($kategori_id)) {
        try {
            $stmt = $pdo->prepare("UPDATE sub_kriteria SET nama = ?, kategori_id = ? WHERE id = ?");
            $stmt->execute([$nama, $kategori_id, $id]);
            $success = "Sub-kriteria berhasil diubah.";
        } catch (PDOException $e) {
            $error = "Gagal mengubah sub-kriteria: " . $e->getMessage();
        }
    } else {
        $error = "Nama dan Kategori tidak boleh kosong.";
    }
}

// Hapus sub-kriteria
if (isset($_POST['hapus'])) {
    $id = $_POST['id_sub'];
    try {
        $stmt = $pdo->prepare("DELETE FROM sub_kriteria WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Sub-kriteria berhasil dihapus.";
    } catch (PDOException $e) {
        $error = "Gagal menghapus sub-kriteria: " . $e->getMessage();
    }
}

// Ambil semua kriteria dan kategori
$kriteria = $pdo->query("SELECT * FROM kriteria ORDER BY kode")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-1 p-8 pl-96 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-light text-gray-800 mb-8">Manajemen Sub-Kriteria</h1>

        <?php if ($success): ?>
            <div class="bg-white border-l-4 border-green-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-white border-l-4 border-red-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $error ?></div>
        <?php endif; ?>

        <?php foreach ($kriteria as $k): ?>
            <div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-800">
                        <span class="font-mono text-sm text-gray-600"><?= htmlspecialchars($k['kode']) ?></span> - 
                        <?= htmlspecialchars($k['nama']) ?>
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
                                    Nama Sub-Kriteria
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-600 uppercase tracking-wider w-40">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            // Query dengan JOIN untuk ambil nama kategori
                            $subs = $pdo->prepare("
                                SELECT s.*, k.nama as kategori_nama 
                                FROM sub_kriteria s 
                                LEFT JOIN kategori k ON s.kategori_id = k.id 
                                WHERE s.kriteria_id = ? 
                                ORDER BY s.nama
                            ");
                            $subs->execute([$k['id']]);
                            $subList = $subs->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($subList as $s):
                                $isEdit = isset($_POST['edit']) && $_POST['id_sub'] == $s['id'];
                            ?>
                                <?php if ($isEdit): ?>
                                    <!-- Form Edit -->
                                    <tr class="bg-yellow-50">
                                        <form method="POST">
                                            <td class="px-6 py-4">
                                                <input type="text" name="nama_sub" 
                                                       value="<?= htmlspecialchars($s['nama']) ?>" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                                       required>
                                                <input type="hidden" name="id_sub" value="<?= $s['id'] ?>">
                                            </td>
                                            <td class="px-6 py-4">
                                                <select name="kategori_id" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                                        required>
                                                    <option value="">Pilih Kategori...</option>
                                                    <?php foreach ($kategori as $kat): ?>
                                                        <option value="<?= $kat['id'] ?>" <?= $s['kategori_id'] == $kat['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($kat['nama']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 text-center space-x-2">
                                                <button type="submit" name="update" 
                                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                    Simpan
                                                </button>
                                                <button type="submit" 
                                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                    Batal
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php else: ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?= htmlspecialchars($s['nama']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= htmlspecialchars($s['kategori_nama'] ?? 'Tidak ada kategori') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-3">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id_sub" value="<?= $s['id'] ?>">
                                                <button type="submit" name="edit" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                                                    Edit
                                                </button>
                                            </form>
                                            <form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                <input type="hidden" name="id_sub" value="<?= $s['id'] ?>">
                                                <button type="submit" name="hapus" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Form Tambah -->
                            <tr class="bg-blue-50" id="form-tambah-<?= $k['id'] ?>">
                                <form method="POST">
                                    <td class="px-6 py-4">
                                        <input type="hidden" name="kriteria_id" value="<?= $k['id'] ?>">
                                        <input type="text" name="nama" 
                                               placeholder="Masukkan nama sub-kriteria baru..." 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                               required>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="kategori_id" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                                required>
                                            <option value="">Pilih Kategori...</option>
                                            <?php foreach ($kategori as $kat): ?>
                                                <option value="<?= $kat['id'] ?>">
                                                    <?= htmlspecialchars($kat['nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="submit" name="tambah_sub" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                                            + Tambah
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($kriteria)): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <p class="text-gray-500">Belum ada kriteria. Silakan tambah kriteria terlebih dahulu.</p>
            </div>
        <?php endif; ?>
    </div>
</main>