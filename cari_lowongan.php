<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Lowongan.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();
$lowongan = new Lowongan($db);

$keyword = $_GET['keyword'] ?? '';
$data_lowongan = $lowongan->search($keyword);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Lowongan Pekerjaan - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">SI Alumni</h1>
        <div>👤 <?php echo htmlspecialchars(getUserName()); ?></div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Cari Lowongan Pekerjaan</h2>

            <!-- Search Form -->
            <form method="GET" class="mb-6">
                <div class="flex gap-2">
                    <input type="text" name="keyword" placeholder="Cari berdasarkan nama perusahaan, posisi..." value="<?php echo htmlspecialchars($keyword); ?>" class="w-full p-2 border border-gray-300 rounded">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Cari
                    </button>
                </div>
            </form>

            <!-- Job List -->
            <?php if (!empty($data_lowongan)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($data_lowongan as $job): ?>
                        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-md transition">
                            <h3 class="text-xl font-semibold text-blue-700">
                                <?= htmlspecialchars($job['posisi']) ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <strong>Perusahaan:</strong> <?= htmlspecialchars($job['nama_perusahaan']) ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <strong>Lokasi:</strong> <?= htmlspecialchars($job['lokasi']) ?>
                            </p>
                            <?php if (!empty($job['deskripsi'])): ?>
                                <p class="text-sm text-gray-700 mb-3">
                                    <?= nl2br(htmlspecialchars(substr($job['deskripsi'], 0, 100))) ?>...
                                </p>
                            <?php endif; ?>
                            <a href="lamar_sekarang.php?id=<?= $job['id'] ?>" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                Lamar Pekerjaan
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600 mt-4">Tidak ada lowongan ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
