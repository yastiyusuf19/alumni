<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Tracer.php';
require_once 'Alumni.php';

requireLogin();
isAdmin();

$db = (new Database())->getConnection();
$tracer = new Tracer($db);
$alumni = new Alumni($db);

$data = $tracer->getAll(); // Ambil semua data tracer
$stats = $tracer->getStatistics(); // Statistik tracer study

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Tracer Study</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Header -->
<nav class="bg-blue-600 text-white px-6 py-4 flex justify-between">
    <div class="text-xl font-bold">Admin - Hasil Tracer Study</div>
    <div><?php echo htmlspecialchars(getUserName()); ?></div>
</nav>

<div class="container mx-auto p-6">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Statistik Tracer Study</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white p-4 shadow rounded">
                <h3 class="text-sm text-gray-600">Rata-rata Kepuasan</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['rata_kepuasan'], 2); ?></p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h3 class="text-sm text-gray-600">Rata-rata Relevansi</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['rata_relevansi'], 2); ?></p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h3 class="text-sm text-gray-600">Jumlah Data Terisi</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $stats['jumlah_data']; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <h2 class="text-xl font-semibold text-gray-700 px-6 py-4 border-b">Detail Data Tracer Alumni</h2>
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2">Nama Alumni</th>
                    <th class="px-4 py-2">Tahun Lulus</th>
                    <th class="px-4 py-2">Kepuasan</th>
                    <th class="px-4 py-2">Relevansi</th>
                    <th class="px-4 py-2">Saran</th>
                    <th class="px-4 py-2">Rekrutmen</th>
                    <th class="px-4 py-2">Mentor</th>
                    <th class="px-4 py-2">Tahun Survey</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td class="px-4 py-2"><?php echo $row['tahun_lulus']; ?></td>
                        <td class="px-4 py-2"><?php echo $row['kepuasan_program_studi']; ?></td>
                        <td class="px-4 py-2"><?php echo $row['relevansi_pekerjaan']; ?></td>
                        <td class="px-4 py-2"><?php echo nl2br(htmlspecialchars($row['saran_perbaikan'])); ?></td>
                        <td class="px-4 py-2"><?php echo ucfirst($row['kesediaan_rekrutmen']); ?></td>
                        <td class="px-4 py-2"><?php echo ucfirst($row['kesediaan_mentor']); ?></td>
                        <td class="px-4 py-2"><?php echo $row['tahun_survey']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
