<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$alumniModel = new Alumni($db);

// Get statistics data
$statsAngkatan = [];
$statsBidangKerja = [];
$statsLokasiDomisili = [];

try {
    // Statistik berdasarkan tahun angkatan
    $stmt = $db->prepare("SELECT angkatan, COUNT(*) AS jumlah FROM alumni GROUP BY angkatan ORDER BY angkatan ASC");
    $stmt->execute();
    $statsAngkatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistik berdasarkan bidang pekerjaan (menggabungkan status_alumni.bidang_kerja)
    $stmt = $db->prepare("
        SELECT COALESCE(s.bidang_kerja, 'Belum diisi') AS bidang_kerja, COUNT(*) AS jumlah
        FROM alumni a
        LEFT JOIN status_alumni s ON a.id = s.alumni_id
        GROUP BY bidang_kerja
        ORDER BY jumlah DESC
    ");
    $stmt->execute();
    $statsBidangKerja = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistik berdasarkan lokasi domisili (menggunakan alumni.alamat)
    $stmt = $db->prepare("
        SELECT 
            CASE
                WHEN alamat IS NULL OR alamat = '' THEN 'Belum diisi'
                ELSE alamat
            END AS alamat_domisili, COUNT(*) AS jumlah
        FROM alumni
        GROUP BY alamat_domisili
        ORDER BY jumlah DESC
        LIMIT 10
    ");
    $stmt->execute();
    $statsLokasiDomisili = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // handle error
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Statistik Alumni - SI Alumni</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<header class="sticky top-0 backdrop-blur-lg bg-white bg-opacity-70 border-b border-gray-300 shadow-sm h-16 flex items-center px-6 z-50">
    <div class="flex items-center space-x-2 text-indigo-700 font-semibold text-lg">
        <span class="material-icons md-24" aria-hidden="true">bar_chart</span>
        <span>Statistik Alumni</span>
    </div>
</header>

<main class="flex-1 p-6 max-w-7xl mx-auto space-y-10">

    <section aria-labelledby="heading-angkatan" class="bg-white rounded-lg shadow p-6">
        <h2 id="heading-angkatan" class="text-xl font-bold text-indigo-900 mb-4">Alumni per Tahun Angkatan</h2>
        <canvas id="angkatanChart" class="w-full max-w-4xl" aria-label="Diagram batang jumlah alumni per tahun angkatan" role="img"></canvas>
    </section>

    <section aria-labelledby="heading-bidang" class="bg-white rounded-lg shadow p-6">
        <h2 id="heading-bidang" class="text-xl font-bold text-indigo-900 mb-4">Statistik Berdasarkan Bidang Pekerjaan</h2>
        <canvas id="bidangKerjaChart" class="w-full max-w-4xl" aria-label="Diagram pai bidang pekerjaan alumni" role="img"></canvas>
    </section>

    <section aria-labelledby="heading-domisili" class="bg-white rounded-lg shadow p-6">
        <h2 id="heading-domisili" class="text-xl font-bold text-indigo-900 mb-4">Statistik Berdasarkan Lokasi Domisili Teratas</h2>
        <canvas id="domisiliChart" class="w-full max-w-4xl" aria-label="Diagram batang lokasi domisili alumni teratas" role="img"></canvas>
    </section>

</main>

<script>
    // Alumni per tahun angkatan - Bar chart
    const angkatanCtx = document.getElementById('angkatanChart').getContext('2d');
    const angkatanChart = new Chart(angkatanCtx, {
        type: 'bar',
        data: {
            labels: [<?php echo implode(',', array_map(fn($item) => "'" . $item['angkatan'] . "'", $statsAngkatan)); ?>],
            datasets: [{
                label: 'Jumlah Alumni',
                data: [<?php echo implode(',', array_map(fn($item) => $item['jumlah'], $statsAngkatan)); ?>],
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision:0 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Statistik bidang pekerjaan - Doughnut chart
    const bidangCtx = document.getElementById('bidangKerjaChart').getContext('2d');
    const bidangLabels = [<?php echo implode(',', array_map(fn($item) => "'" . addslashes($item['bidang_kerja']) . "'", $statsBidangKerja)); ?>];
    const bidangData = [<?php echo implode(',', array_map(fn($item) => $item['jumlah'], $statsBidangKerja)); ?>];
    const bidangColors = [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(250, 204, 21, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(107, 114, 128, 0.8)',
        'rgba(147, 197, 253, 0.8)',
        'rgba(132, 204, 22, 0.8)',
        'rgba(250, 59, 59, 0.8)',
        'rgba(244, 114, 182, 0.8)',
    ];
    const bidangKerjaChart = new Chart(bidangCtx, {
        type: 'doughnut',
        data: {
            labels: bidangLabels,
            datasets: [{
                data: bidangData,
                backgroundColor: bidangColors.slice(0, bidangLabels.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // Statistik lokasi domisili - Horizontal bar chart
    const domisiliCtx = document.getElementById('domisiliChart').getContext('2d');
    const domisiliLabels = [<?php echo implode(',', array_map(fn($item) => "'" . addslashes($item['alamat_domisili']) . "'", $statsLokasiDomisili)); ?>];
    const domisiliData = [<?php echo implode(',', array_map(fn($item) => $item['jumlah'], $statsLokasiDomisili)); ?>];
    const domisiliChart = new Chart(domisiliCtx, {
        type: 'bar',
        data: {
            labels: domisiliLabels,
            datasets: [{
                label: 'Jumlah Alumni',
                data: domisiliData,
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision:0 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
</body>
</html>

