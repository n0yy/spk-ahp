<?php
include "includes/sidebar.php";
include "config/database.php";

$success = '';
$error = '';

// Tambah kategori
if (isset($_POST['tambah_kategori'])) {
    $nama = $_POST['nama'];

    if (!empty($nama)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO kategori (nama) VALUES (?)");
            $stmt->execute([$nama]);
            $success = "Kategori berhasil ditambahkan.";
        } catch (PDOException $e) {
            $error = "Gagal menambah kategori: " . $e->getMessage();
        }
    } else {
        $error = "Nama wajib diisi.";
    }
}

// Update kategori
if (isset($_POST['update_kategori'])) {
    $id = $_POST['id_kategori'];
    $nama = $_POST['nama_kategori'];
    
    if (!empty($nama)) {
        try {
            $stmt = $pdo->prepare("UPDATE kategori SET nama = ? WHERE id = ?");
            $stmt->execute([$nama, $id]);
            $success = "Kategori berhasil diubah.";
        } catch (PDOException $e) {
            $error = "Gagal mengubah kategori: " . $e->getMessage();
        }
    } else {
        $error = "Nama tidak boleh kosong.";
    }
}

// Hapus kategori
if (isset($_POST['hapus_kategori'])) {
    $id = $_POST['id_kategori'];
    
    // Cek apakah kategori digunakan di sub_kriteria
    $check = $pdo->prepare("SELECT COUNT(*) FROM sub_kriteria WHERE kategori_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();
    
    if ($count > 0) {
        $error = "Kategori tidak dapat dihapus karena masih digunakan di sub-kriteria.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Kategori berhasil dihapus.";
        } catch (PDOException $e) {
            $error = "Gagal menghapus kategori: " . $e->getMessage();
        }
    }
}

// Ambil data kategori dengan hitung penggunaan
$stmt = $pdo->query("
    SELECT k.*, 
           COUNT(s.id) as jumlah_sub_kriteria
    FROM kategori k
    LEFT JOIN sub_kriteria s ON k.id = s.kategori_id
    GROUP BY k.id
    ORDER BY k.nama ASC
");
$kategoriList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-1 p-8 pl-96 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-light text-gray-800 mb-8">Data Kategori</h1>

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
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-600 uppercase tracking-wider">Digunakan</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-600 uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($kategoriList as $index => $kategori): 
                        $isEdit = isset($_POST['edit_kategori']) && $_POST['id_kategori'] == $kategori['id'];
                    ?>
                        <?php if ($isEdit): ?>
                            <!-- Form Edit -->
                            <tr class="bg-yellow-50">
                                <form method="POST">
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="nama_kategori" 
                                               value="<?= htmlspecialchars($kategori['nama']) ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                               required>
                                        <input type="hidden" name="id_kategori" value="<?= $kategori['id'] ?>">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <?= $kategori['jumlah_sub_kriteria'] ?> sub-kriteria
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= date('d/m/Y H:i', strtotime($kategori['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button type="submit" name="update_kategori" 
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
                                    <?= htmlspecialchars($kategori['nama']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($kategori['jumlah_sub_kriteria'] > 0): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $kategori['jumlah_sub_kriteria'] ?> sub-kriteria
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            Tidak digunakan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($kategori['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-center space-x-3">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id_kategori" value="<?= $kategori['id'] ?>">
                                        <button type="submit" name="edit_kategori" 
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                                            Edit
                                        </button>
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        <input type="hidden" name="id_kategori" value="<?= $kategori['id'] ?>">
                                        <button type="submit" name="hapus_kategori" 
                                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors <?= $kategori['jumlah_sub_kriteria'] > 0 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                                <?= $kategori['jumlah_sub_kriteria'] > 0 ? 'disabled' : '' ?>>
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
                + Tambah Kategori
            </button>
        </div>

        <!-- Form Tambah -->
        <div id="form-tambah" class="hidden mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Tambah Kategori Baru</h3>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" name="nama" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                           placeholder="Masukkan nama kategori..." required>
                </div>
                <div class="flex space-x-3">
                    <input type="hidden" name="tambah_kategori" value="1">
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

        <?php if (empty($kategoriList)): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center mt-6">
                <p class="text-gray-500">Belum ada kategori. Silakan tambah kategori baru.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function toggleTambahForm() {
    document.getElementById('form-tambah').classList.toggle('hidden');
}
</script>