<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';
require_once 'Lowongan.php';
require_once 'Tracer.php';
require_once 'Pengumuman.php';
requireLogin();

// Cek role
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Model
$alumni = new Alumni($db);
$lowongan = new Lowongan($db);
$tracer = new Tracer($db);
$pengumuman = new Pengumuman($db);

// Data
$jumlah_alumni = count($alumni->getAll());
$jumlah_lowongan = count($lowongan->getAll());
$jumlah_tracer = count($tracer->getAll());
$jumlah_pengumuman = count($pengumuman->getAll());
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="favicon.ico" />
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <!-- Navbar -->
    <header class="bg-blue-700 text-white shadow">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">📊 Dashboard Admin</h1>
            <div class="text-sm">👤 <?php echo htmlspecialchars(getUserName()); ?> (Admin)</div>
        </div>
    </header>

    <!-- Content -->
    <main class="container mx-auto px-6 py-8">

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-lg shadow text-center border-t-4 border-blue-500">
                <p class="text-gray-600 mb-1">Total Alumni</p>
                <p class="text-3xl font-bold text-blue-700"><?php echo $jumlah_alumni; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center border-t-4 border-green-500">
                <p class="text-gray-600 mb-1">Lowongan Kerja</p>
                <p class="text-3xl font-bold text-green-700"><?php echo $jumlah_lowongan; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center border-t-4 border-purple-500">
                <p class="text-gray-600 mb-1">Tracer Study</p>
                <p class="text-3xl font-bold text-purple-700"><?php echo $jumlah_tracer; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center border-t-4 border-yellow-500">
                <p class="text-gray-600 mb-1">Pengumuman</p>
                <p class="text-3xl font-bold text-yellow-700"><?php echo $jumlah_pengumuman; ?></p>
            </div>
        </div>

        <!-- Navigasi Fitur -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="kelola_alumni.php" class="block bg-white hover:bg-blue-50 p-6 rounded-lg shadow border-l-4 border-blue-500 transition">
                <h2 class="text-xl font-semibold text-blue-800 mb-1">👥 Kelola Data Alumni</h2>
                <p class="text-gray-600 text-sm">Tambah, ubah, dan hapus data alumni secara terpusat.</p>
            </a>
            <a href="kelola_lowongan.php" class="block bg-white hover:bg-green-50 p-6 rounded-lg shadow border-l-4 border-green-500 transition">
                <h2 class="text-xl font-semibold text-green-800 mb-1">💼 Kelola Lowongan Kerja</h2>
                <p class="text-gray-600 text-sm">Atur dan publikasikan informasi lowongan pekerjaan.</p>
            </a>
            <a href="kelola_pengumuman.php" class="block bg-white hover:bg-yellow-50 p-6 rounded-lg shadow border-l-4 border-yellow-500 transition">
                <h2 class="text-xl font-semibold text-yellow-800 mb-1">📢 Kelola Pengumuman</h2>
                <p class="text-gray-600 text-sm">Buat dan kelola pengumuman penting untuk alumni.</p>
            </a>
            <a href="hasil_tracer_study.php" class="block bg-white hover:bg-purple-50 p-6 rounded-lg shadow border-l-4 border-purple-500 transition">
                <h2 class="text-xl font-semibold text-purple-800 mb-1">📈 Hasil Tracer Study</h2>
                <p class="text-gray-600 text-sm">Lihat dan analisis hasil pengisian tracer study alumni.</p>
            </a>
        </div>

    </main>

</body>
</html>
