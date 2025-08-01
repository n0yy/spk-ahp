<?php
include "includes/sidebar.php";
include "config/database.php";

$success = '';
$error = '';

// Tambah alternatif
if (isset($_POST['tambah_alternatif'])) {
    $nama = $_POST['nama'];

    if (!empty($nama)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO alternatif (nama) VALUES (?)");
            $stmt->execute([$nama]);
            $success = "Alternatif berhasil ditambahkan.";
        } catch (PDOException $e) {
            $error = "Gagal menambah alternatif: " . $e->getMessage();
        }
    } else {
        $error = "Nama wajib diisi.";
    }
}

// Update alternatif
if (isset($_POST['update_alternatif'])) {
    $id = $_POST['id_alternatif'];
    $nama = $_POST['nama_alternatif'];
    
    if (!empty($nama)) {
        try {
            $stmt = $pdo->prepare("UPDATE alternatif SET nama = ? WHERE id = ?");
            $stmt->execute([$nama, $id]);
            $success = "Alternatif berhasil diubah.";
        } catch (PDOException $e) {
            $error = "Gagal mengubah alternatif: " . $e->getMessage();
        }
    } else {
        $error = "Nama tidak boleh kosong.";
    }
}

// Hapus alternatif
if (isset($_POST['hapus_alternatif'])) {
    $id = $_POST['id_alternatif'];
    try {
        $stmt = $pdo->prepare("DELETE FROM alternatif WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Alternatif berhasil dihapus.";
    } catch (PDOException $e) {
        $error = "Gagal menghapus alternatif: " . $e->getMessage();
    }
}

// Ambil data alternatif
$stmt = $pdo->query("SELECT * FROM alternatif ORDER BY id ASC");
$alternatifList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-1 p-8 pl-96 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-light text-gray-800 mb-8">Data Alternatif</h1>

        <?php if ($success): ?>
            <div class="bg-white border-l-4 border-green-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-white border-l-4 border-red-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $error ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nama Alternatif</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-600 uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($alternatifList as $index => $alternatif): 
                        $isEdit = isset($_POST['edit_alternatif']) && $_POST['id_alternatif'] == $alternatif['id'];
                    ?>
                        <?php if ($isEdit): ?>
                            <!-- Form Edit -->
                            <tr class="bg-yellow-50">
                                <form method="POST">
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="nama_alternatif" 
                                               value="<?= htmlspecialchars($alternatif['nama']) ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                               required>
                                        <input type="hidden" name="id_alternatif" value="<?= $alternatif['id'] ?>">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= date('d/m/Y H:i', strtotime($alternatif['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button type="submit" name="update_alternatif" 
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
                                <td class="px-6 py-4 text-sm text-gray-500"><?= $index + 1 ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($alternatif['nama']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($alternatif['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-center space-x-3">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id_alternatif" value="<?= $alternatif['id'] ?>">
                                        <button type="submit" name="edit_alternatif" 
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                                            Edit
                                        </button>
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        <input type="hidden" name="id_alternatif" value="<?= $alternatif['id'] ?>">
                                        <button type="submit" name="hapus_alternatif" 
                                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <button onclick="toggleTambahForm()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                + Tambah Alternatif
            </button>
        </div>

        <!-- Form Tambah -->
        <div id="form-tambah" class="hidden mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Tambah Alternatif Baru</h3>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Alternatif</label>
                    <input type="text" name="nama" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                           placeholder="Masukkan nama alternatif..." required>
                </div>
                <div class="flex space-x-3">
                    <input type="hidden" name="tambah_alternatif" value="1">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Simpan
                    </button>
                    <button type="button" onclick="toggleTambahForm()" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($alternatifList)): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center mt-6">
                <p class="text-gray-500">Belum ada alternatif. Silakan tambah alternatif baru.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function toggleTambahForm() {
    document.getElementById('form-tambah').classList.toggle('hidden');
}
</script>