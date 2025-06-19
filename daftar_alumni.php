<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';

requireLogin();
$user_role = getUserRole();
$user_name = getUserName();

// Redirect jika bukan alumni
if ($user_role !== 'alumni') {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$alumni = new Alumni($db);
$all_alumni = $alumni->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alumni - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <i class="fas fa-graduation-cap text-2xl"></i>
                <h1 class="text-xl font-bold">SI Alumni</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm">Selamat datang, <?php echo htmlspecialchars($user_name); ?></span>
                <div class="relative group">
                    <button class="flex items-center space-x-2 bg-blue-700 px-3 py-2 rounded-lg hover:bg-blue-800">
                        <i class="fas fa-user"></i>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="profile.php" class="block px-4 py-2 hover:bg-gray-100 rounded-t-lg">
                            <i class="fas fa-user-circle mr-2"></i>Profil
                        </a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-gray-100 rounded-b-lg text-red-600">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Seluruh Alumni</h2>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Angkatan</th>
                    <th class="px-4 py-3">Program Studi</th>
                    <th class="px-4 py-3">Tahun Lulus</th>
                    <th class="px-4 py-3">Pekerjaan</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                <?php foreach ($all_alumni as $al): ?>
                <tr>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($al['nama_lengkap']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($al['angkatan']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($al['program_studi']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($al['tahun_lulus']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($al['pekerjaan']); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($all_alumni)): ?>
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada data alumni.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
