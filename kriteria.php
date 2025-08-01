<?php
include 'config/database.php';
include 'includes/sidebar.php';

$success = '';
$error = '';

// Proses tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kriteria'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];

    if ($kode && $nama) {
        try {
            $stmt = $pdo->prepare("INSERT INTO kriteria (kode, nama) VALUES (?, ?)");
            $stmt->execute([$kode, $nama]);
            $success = "Kriteria berhasil ditambahkan.";
        } catch (PDOException $e) {
            $error = "Gagal menambah kriteria: " . $e->getMessage();
        }
    } else {
        $error = "Kode dan Nama wajib diisi.";
    }
}

// Proses edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kriteria'])) {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];

    if ($kode && $nama) {
        try {
            $stmt = $pdo->prepare("UPDATE kriteria SET kode = ?, nama = ? WHERE id = ?");
            $stmt->execute([$kode, $nama, $id]);
            $success = "Kriteria berhasil diubah.";
        } catch (PDOException $e) {
            $error = "Gagal mengubah kriteria: " . $e->getMessage();
        }
    } else {
        $error = "Kode dan Nama wajib diisi.";
    }
}

// Ambil semua data kriteria
$stmt = $pdo->query("SELECT * FROM kriteria");
$kriteria = $stmt->fetchAll();
?>

<main class="flex-1 p-8 pl-96 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-light text-gray-800 mb-8">Data Kriteria</h1>

        <?php if ($success): ?>
            <div class="bg-white border-l-4 border-green-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-white border-l-4 border-red-400 text-gray-700 p-4 rounded shadow-sm mb-6"><?= $error ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($kriteria as $row): ?>
                    <tr class="hover:bg-gray-50 transition-colors" id="row-<?= $row['id'] ?>">
                        <td class="px-6 py-4 text-sm font-mono text-gray-900"><?= htmlspecialchars($row['kode']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($row['nama']) ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button onclick="showEditForm(<?= $row['id'] ?>, '<?= htmlspecialchars($row['kode']) ?>', '<?= htmlspecialchars($row['nama']) ?>')" 
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">Edit</button>
                            <a href="hapus_kriteria.php?id=<?= $row['id'] ?>" 
                               class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors" 
                               onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <button onclick="toggleTambahForm()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                + Tambah Kriteria
            </button>
        </div>

        <!-- Form Tambah -->
        <div id="form-tambah" class="hidden mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Tambah Kriteria Baru</h3>
            <form method="post" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode</label>
                        <input type="text" name="kode" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                               placeholder="C1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                               placeholder="Nama Kriteria" required>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <input type="hidden" name="tambah_kriteria" value="1">
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

        <!-- Form Edit -->
        <div id="form-edit" class="hidden mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Edit Kriteria</h3>
            <form method="post" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode</label>
                        <input type="text" name="kode" id="edit-kode"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" id="edit-nama"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                               required>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="edit_kriteria" value="1">
                    <button type="submit" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Update
                    </button>
                    <button type="button" onclick="cancelEdit()" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function toggleTambahForm() {
    const form = document.getElementById('form-tambah');
    form.classList.toggle('hidden');
    
    // Tutup form edit jika terbuka
    const editForm = document.getElementById('form-edit');
    if (!editForm.classList.contains('hidden')) {
        editForm.classList.add('hidden');
    }
}

function showEditForm(id, kode, nama) {
    const tambahForm = document.getElementById('form-tambah');
    if (!tambahForm.classList.contains('hidden')) {
        tambahForm.classList.add('hidden');
    }
    
    const editForm = document.getElementById('form-edit');
    editForm.classList.remove('hidden');
    
    // Isi data form
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    
    editForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cancelEdit() {
    document.getElementById('form-edit').classList.add('hidden');
}
</script>