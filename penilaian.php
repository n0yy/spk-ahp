<?php
include "includes/sidebar.php";
include "config/database.php";

// Buat tabel jika belum ada - DIPINDAH KE ATAS
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS perbandingan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(20) NOT NULL,
        kriteria1_id INT,
        kriteria2_id INT,
        alternatif1_id INT,
        alternatif2_id INT,
        nilai DECIMAL(10,6) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Table exists or other error
    error_log("Error creating table: " . $e->getMessage());
}

// Ambil data
$kriteria = $pdo->query("SELECT * FROM kriteria ORDER BY kode")->fetchAll(PDO::FETCH_ASSOC);
$alternatif = $pdo->query("SELECT * FROM alternatif ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Skala AHP
$skala_ahp = [
    1 => '1 - Sama penting',
    2 => '2 - Sedikit lebih penting',
    3 => '3 - Lebih penting',
    4 => '4 - Sangat lebih penting',
    5 => '5 - Mutlak lebih penting',
    6 => '6 - Antara 5 dan 7',
    7 => '7 - Sangat mutlak lebih penting',
    8 => '8 - Antara 7 dan 9',
    9 => '9 - Ekstrim lebih penting'
];

// Proses input perbandingan kriteria
if (isset($_POST['save_kriteria'])) {
    $pdo->exec("DELETE FROM perbandingan WHERE type='kriteria'");
    
    for ($i = 0; $i < count($kriteria); $i++) {
        for ($j = $i + 1; $j < count($kriteria); $j++) {
            $nilai = $_POST['kriteria'][$i][$j];
            $stmt = $pdo->prepare("INSERT INTO perbandingan (type, kriteria1_id, kriteria2_id, nilai) VALUES (?, ?, ?, ?)");
            $stmt->execute(['kriteria', $kriteria[$i]['id'], $kriteria[$j]['id'], $nilai]);
        }
    }
}

// Proses input perbandingan alternatif
if (isset($_POST['save_alternatif'])) {
    $kriteria_id = $_POST['kriteria_id'];
    $pdo->prepare("DELETE FROM perbandingan WHERE type='alternatif' AND kriteria1_id=?")->execute([$kriteria_id]);
    
    for ($i = 0; $i < count($alternatif); $i++) {
        for ($j = $i + 1; $j < count($alternatif); $j++) {
            $nilai = $_POST['alternatif'][$i][$j];
            $stmt = $pdo->prepare("INSERT INTO perbandingan (type, kriteria1_id, alternatif1_id, alternatif2_id, nilai) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['alternatif', $kriteria_id, $alternatif[$i]['id'], $alternatif[$j]['id'], $nilai]);
        }
    }
}

// Fungsi untuk membuat matriks perbandingan
function buatMatriksKriteria($pdo, $kriteria) {
    $n = count($kriteria);
    $matriks = array_fill(0, $n, array_fill(0, $n, 1));
    
    $stmt = $pdo->query("SELECT * FROM perbandingan WHERE type='kriteria'");
    $perbandingan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($perbandingan as $p) {
        $i = array_search($p['kriteria1_id'], array_column($kriteria, 'id'));
        $j = array_search($p['kriteria2_id'], array_column($kriteria, 'id'));
        $matriks[$i][$j] = $p['nilai'];
        $matriks[$j][$i] = 1 / $p['nilai'];
    }
    
    return $matriks;
}

function buatMatriksAlternatif($pdo, $kriteria_id, $alternatif) {
    $n = count($alternatif);
    $matriks = array_fill(0, $n, array_fill(0, $n, 1));
    
    $stmt = $pdo->prepare("SELECT * FROM perbandingan WHERE type='alternatif' AND kriteria1_id=?");
    $stmt->execute([$kriteria_id]);
    $perbandingan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($perbandingan as $p) {
        $i = array_search($p['alternatif1_id'], array_column($alternatif, 'id'));
        $j = array_search($p['alternatif2_id'], array_column($alternatif, 'id'));
        $matriks[$i][$j] = $p['nilai'];
        $matriks[$j][$i] = 1 / $p['nilai'];
    }
    
    return $matriks;
}

// Hitung bobot dari matriks
function hitungBobot($matriks) {
    $n = count($matriks);
    $totalKolom = array_fill(0, $n, 0);
    
    // Jumlah setiap kolom
    for ($j = 0; $j < $n; $j++) {
        for ($i = 0; $i < $n; $i++) {
            $totalKolom[$j] += $matriks[$i][$j];
        }
    }
    
    // Normalisasi
    $matriksNormal = array_fill(0, $n, array_fill(0, $n, 0));
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $matriksNormal[$i][$j] = $matriks[$i][$j] / $totalKolom[$j];
        }
    }
    
    // Rata-rata baris (eigen vector)
    $bobot = array();
    for ($i = 0; $i < $n; $i++) {
        $bobot[$i] = array_sum($matriksNormal[$i]) / $n;
    }
    
    return $bobot;
}

// Hitung Consistency Ratio
function hitungCR($matriks, $bobot) {
    $n = count($matriks);
    $RI = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49]; // Random Index
    
    // Hitung λmax
    $lambdaMax = 0;
    for ($i = 0; $i < $n; $i++) {
        $sum = 0;
        for ($j = 0; $j < $n; $j++) {
            $sum += $matriks[$i][$j] * $bobot[$j];
        }
        $lambdaMax += $sum / $bobot[$i];
    }
    $lambdaMax = $lambdaMax / $n;
    
    $CI = ($lambdaMax - $n) / ($n - 1);
    $CR = $CI / $RI[$n - 1];
    
    return ['CI' => $CI, 'CR' => $CR, 'lambda_max' => $lambdaMax];
}

// Hitung hasil akhir
$bobotKriteria = [];
$bobotAlternatif = [];
$matriksKriteria = [];
$matriksAlternatif = [];

if (!empty($kriteria)) {
    $matriksKriteria = buatMatriksKriteria($pdo, $kriteria);
    $bobotKriteria = hitungBobot($matriksKriteria);
    
    foreach ($kriteria as $k) {
        $matriksAlt = buatMatriksAlternatif($pdo, $k['id'], $alternatif);
        $matriksAlternatif[$k['id']] = $matriksAlt;
        $bobotAlternatif[$k['id']] = hitungBobot($matriksAlt);
    }
}

// Hitung skor akhir
$skorAkhir = [];
if (!empty($alternatif) && !empty($bobotKriteria)) {
    foreach ($alternatif as $i => $alt) {
        $skor = 0;
        foreach ($kriteria as $j => $krit) {
            $skor += ($bobotAlternatif[$krit['id']][$i] ?? 0) * $bobotKriteria[$j];
        }
        $skorAkhir[] = ['alternatif' => $alt['nama'], 'skor' => $skor];
    }
    
    // Urutkan berdasarkan skor tertinggi
    usort($skorAkhir, function($a, $b) { return $b['skor'] <=> $a['skor']; });
}
?>

<main class="flex-1 p-8 pl-96 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-light text-gray-800 mb-8">Penilaian AHP</h1>

        <?php if (empty($kriteria) || empty($alternatif)): ?>
            <div class="bg-white border-l-4 border-yellow-400 text-gray-700 p-4 rounded shadow-sm mb-6">
                Pastikan data kriteria dan alternatif sudah ada sebelum melakukan penilaian.
            </div>
        <?php else: ?>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button onclick="showTab('kriteria')" id="tab-kriteria" class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                        Perbandingan Kriteria
                    </button>
                    <button onclick="showTab('alternatif')" id="tab-alternatif" class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                        Perbandingan Alternatif
                    </button>
                    <button onclick="showTab('hasil')" id="tab-hasil" class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                        Hasil & Ranking
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Perbandingan Kriteria -->
        <div id="content-kriteria" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Matriks Perbandingan Kriteria</h3>
                <form method="post">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-4 py-2">Kriteria</th>
                                    <?php foreach ($kriteria as $k): ?>
                                        <th class="border border-gray-300 px-4 py-2"><?= $k['kode'] ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kriteria as $i => $k1): ?>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2 font-medium bg-gray-50"><?= $k1['kode'] ?></td>
                                        <?php foreach ($kriteria as $j => $k2): ?>
                                            <td class="border border-gray-300 px-2 py-2">
                                                <?php if ($i == $j): ?>
                                                    <span class="text-center block">1</span>
                                                <?php elseif ($i < $j): ?>
                                                    <select name="kriteria[<?= $i ?>][<?= $j ?>]" class="w-full px-2 py-1 text-sm">
                                                        <?php foreach ($skala_ahp as $nilai => $desc): ?>
                                                            <option value="<?= $nilai ?>"><?= $desc ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else:
                                                    // Ambil nilai dari matriks yang sudah dihitung
                                                    $nilaiTabel = isset($matriksKriteria[$i][$j]) ? number_format($matriksKriteria[$i][$j], 3) : '1/x';
                                                ?>
                                                    <span class="text-center block text-gray-500"><?= $nilaiTabel ?></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="save_kriteria" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Simpan Perbandingan
                    </button>
                </form>
            </div>

            <?php if (!empty($bobotKriteria)): ?>
                <!-- Hasil Perhitungan Kriteria -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4">Hasil Perhitungan Kriteria</h3>
                    
                    <!-- Matriks Normalisasi -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Matriks Perbandingan</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-2">Kriteria</th>
                                        <?php foreach ($kriteria as $k): ?>
                                            <th class="border border-gray-300 px-4 py-2"><?= $k['kode'] ?></th>
                                        <?php endforeach; ?>
                                        <th class="border border-gray-300 px-4 py-2">Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kriteria as $i => $k): ?>
                                        <tr>
                                            <td class="border border-gray-300 px-4 py-2 font-medium bg-gray-50"><?= $k['kode'] ?></td>
                                            <?php foreach ($matriksKriteria[$i] as $nilai): ?>
                                                <td class="border border-gray-300 px-4 py-2 text-center"><?= number_format($nilai, 3) ?></td>
                                            <?php endforeach; ?>
                                            <td class="border border-gray-300 px-4 py-2 text-center font-medium"><?= number_format($bobotKriteria[$i], 4) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Consistency Check -->
                    <?php 
                    $consistency = hitungCR($matriksKriteria, $bobotKriteria);
                    $isConsistent = $consistency['CR'] <= 0.1;
                    ?>
                    <div class="p-4 rounded-lg <?= $isConsistent ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?>">
                        <h4 class="font-medium mb-2">Uji Konsistensi</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>λmax: <?= number_format($consistency['lambda_max'], 4) ?></div>
                            <div>CI: <?= number_format($consistency['CI'], 4) ?></div>
                            <div>CR: <?= number_format($consistency['CR'], 4) ?></div>
                        </div>
                        <p class="mt-2 text-sm <?= $isConsistent ? 'text-green-700' : 'text-red-700' ?>">
                            <?= $isConsistent ? 'Matriks konsisten (CR ≤ 0.1)' : 'Matriks tidak konsisten (CR > 0.1), perlu perbaikan' ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Perbandingan Alternatif -->
        <div id="content-alternatif" class="tab-content hidden">
            <?php foreach ($kriteria as $krit): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4">Perbandingan Alternatif untuk <?= $krit['nama'] ?></h3>
                    <form method="post">
                        <input type="hidden" name="kriteria_id" value="<?= $krit['id'] ?>">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-2">Alternatif</th>
                                        <?php foreach ($alternatif as $alt): ?>
                                            <th class="border border-gray-300 px-4 py-2"><?= $alt['nama'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt1): ?>
                                        <tr>
                                            <td class="border border-gray-300 px-4 py-2 font-medium bg-gray-50"><?= $alt1['nama'] ?></td>
                                            <?php foreach ($alternatif as $j => $alt2): ?>
                                                <td class="border border-gray-300 px-2 py-2">
                                                    <?php if ($i == $j): ?>
                                                        <span class="text-center block">1</span>
                                                    <?php elseif ($i < $j): ?>
                                                        <select name="alternatif[<?= $i ?>][<?= $j ?>]" class="w-full px-2 py-1 text-sm">
                                                            <?php foreach ($skala_ahp as $nilai => $desc): ?>
                                                                <option value="<?= $nilai ?>"><?= $desc ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else:
                                                        // Ambil nilai dari matriks alternatif yang sudah dihitung
                                                        $nilaiTabelAlt = isset($matriksAlternatif[$krit['id']][$i][$j]) ? number_format($matriksAlternatif[$krit['id']][$i][$j], 3) : '1/x';
                                                    ?>
                                                        <span class="text-center block text-gray-500"><?= $nilaiTabelAlt ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" name="save_alternatif" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            Simpan Perbandingan
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tab Hasil -->
        <div id="content-hasil" class="tab-content hidden">
            <?php if (!empty($skorAkhir)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4">Ranking Alternatif</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Rank</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Alternatif</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Skor</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 uppercase">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($skorAkhir as $rank => $data): ?>
                                    <tr class="<?= $rank == 0 ? 'bg-green-50' : '' ?>">
                                        <td class="px-6 py-4">
                                            <?php if ($rank == 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    #<?= $rank + 1 ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-600">#<?= $rank + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium"><?= $data['alternatif'] ?></td>
                                        <td class="px-6 py-4"><?= number_format($data['skor'], 4) ?></td>
                                        <td class="px-6 py-4"><?= number_format($data['skor'] * 100, 2) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Detail Bobot -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium mb-4">Bobot Kriteria</h3>
                        <div class="space-y-2">
                            <?php foreach ($kriteria as $i => $k): ?>
                                <div class="flex justify-between">
                                    <span><?= $k['nama'] ?></span>
                                    <span class="font-medium"><?= number_format($bobotKriteria[$i] * 100, 2) ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium mb-4">Bobot Alternatif per Kriteria</h3>
                        <?php foreach ($kriteria as $k): ?>
                            <div class="mb-4">
                                <h4 class="font-medium text-sm mb-2"><?= $k['nama'] ?></h4>
                                <div class="space-y-1">
                                    <?php if (isset($bobotAlternatif[$k['id']])): ?>
                                        <?php foreach ($alternatif as $i => $alt): ?>
                                            <div class="flex justify-between text-sm">
                                                <span><?= $alt['nama'] ?></span>
                                                <span><?= number_format($bobotAlternatif[$k['id']][$i] * 100, 2) ?>%</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <p class="text-gray-500">Lengkapi perbandingan kriteria dan alternatif untuk melihat hasil.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php endif; ?>
    </div>
</main>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activate selected tab button
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
}

// Initialize first tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('kriteria');
});
</script>