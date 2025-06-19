<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';
require_once 'Users.php';
require_once 'Tracer.php';
require_once 'StatusAlumni.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$alumni = new Alumni($db);
$user = new User($db);

// Get statistics
$stats = $alumni->getStatistics();
$user_role = getUserRole();
$user_name = getUserName();

// Count totals
$total_alumni = 0;
foreach ($stats['angkatan'] as $stat) {
    $total_alumni += $stat['jumlah'];
}

// Get recent alumni (for admin)
$recent_alumni = [];
if ($user_role === 'admin') {
    $all_alumni = $alumni->getAll();
    $recent_alumni = array_slice($all_alumni, 0, 5);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
               <img src="LogoITH.png" alt="Logo ITH" class="h-10 w-10">
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

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-wrap lg:flex-nowrap gap-6">
            <!-- Sidebar -->
            <aside class="w-full lg:w-64 bg-white rounded-lg shadow-md p-6">
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center space-x-3 bg-blue-100 text-blue-600 px-4 py-3 rounded-lg">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <?php if ($user_role === 'admin'): ?>
                    <a href="kelola_alumni.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-users"></i>
                        <span>Kelola Alumni</span>
                    </a>
                    <a href="kelola_lowongan.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-briefcase"></i>
                        <span>Lowongan Kerja</span>
                    </a>
                    <a href="kelola_pengumuman.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-bullhorn"></i>
                        <span>Pengumuman</span>
                    </a>
                    <a href="statistik.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistik</span>
                    </a>
                    <?php else: ?>
                    <a href="profile_alumni.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-user-edit"></i>
                        <span>Profil Alumni</span>
                    </a>
                    <a href="cari_lowongan.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-search"></i>
                        <span>Cari Lowongan</span>
                    </a>
                    <a href="tracer_study.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-poll"></i>
                        <span>Tracer Study</span>
                    </a>
                    <a href="daftar_alumni.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                    <i class="fas fa-users"></i>
                    <span>Daftar Alumni</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="pengumuman.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-bell"></i>
                        <span>Pengumuman</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-2">
                        Selamat datang, <?php echo htmlspecialchars($user_name); ?>!
                    </h2>
                    <p class="text-blue-100">
                        <?php if ($user_role === 'admin'): ?>
                        Kelola sistem informasi alumni dengan mudah melalui dashboard ini.
                        <?php else: ?>
                        Pantau perkembangan karir dan tetap terhubung dengan almamater Anda.
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Alumni</p>
                                <p class="text-2xl font-bold text-blue-600"><?php echo $total_alumni; ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Program Studi</p>
                                <p class="text-2xl font-bold text-green-600"><?php echo count($stats['program_studi']); ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-book text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Tahun Angkatan</p>
                                <p class="text-2xl font-bold text-purple-600"><?php echo count($stats['angkatan']); ?></p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-calendar text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Alumni Bekerja</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    <?php 
                                    $bekerja = 0;
                                    foreach ($stats['status_pekerjaan'] as $status) {
                                        if ($status['status_saat_ini'] === 'bekerja') {
                                            $bekerja = $status['jumlah'];
                                            break;
                                        }
                                    }
                                    echo $bekerja;
                                    ?>
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-briefcase text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Alumni by Year Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Alumni per Angkatan</h3>
                        <canvas id="angkatanChart"></canvas>
                    </div>

                    <!-- Alumni by Status Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Status Pekerjaan Alumni</h3>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <?php if ($user_role === 'admin' && !empty($recent_alumni)): ?>
                <!-- Recent Alumni Table -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Alumni Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nama</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">NIM</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Program Studi</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Angkatan</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_alumni as $alum): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($alum['nama_lengkap']); ?></td>
                                    <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($alum['nim']); ?></td>
                                    <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($alum['program_studi']); ?></td>
                                    <td class="px-4 py-2 text-sm"><?php echo $alum['angkatan']; ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <?php if ($alum['status_saat_ini']): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                            <?php echo ucfirst(str_replace('_', ' ', $alum['status_saat_ini'])); ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                            Belum diisi
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
    // Alumni by Year Chart
    const angkatanCtx = document.getElementById('angkatanChart').getContext('2d');
    const angkatanChart = new Chart(angkatanCtx, {
        type: 'bar',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . $item['angkatan'] . "'"; }, $stats['angkatan'])); ?>],
            datasets: [{
                label: 'Jumlah Alumni',
                data: [<?php echo implode(',', array_map(function($item) { return $item['jumlah']; }, $stats['angkatan'])); ?>],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Alumni by Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . ucfirst(str_replace('_', ' ', $item['status_saat_ini'])) . "'"; }, $stats['status_pekerjaan'])); ?>],
            datasets: [{
                data: [<?php echo implode(',', array_map(function($item) { return $item['jumlah']; }, $stats['status_pekerjaan'])); ?>],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';
require_once 'StatusAlumni.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

$alumni = new Alumni($db);
$statusModel = new StatusAlumni($db);

$user_id = getUserId(); // dari session
$alumni_data = $alumni->getByUserId($user_id);

if (!$alumni_data) {
    echo "Data alumni tidak ditemukan.";
    exit;
}

$status_data = $statusModel->getByAlumniId($alumni_data['id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
        <div class="text-xl font-bold">Dashboard Alumni</div>
        <div><?php echo htmlspecialchars(getUserName()); ?></div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4">Status Alumni Saat Ini</h2>

            <?php if ($status_data): ?>
                <div class="grid md:grid-cols-2 gap-4 text-gray-700 text-sm">
                    <div><strong>Status Saat Ini:</strong> <?php echo ucwords(str_replace('_', ' ', $status_data['status_saat_ini'])); ?></div>
                    <div><strong>Nama Instansi:</strong> <?php echo htmlspecialchars($status_data['nama_instansi']); ?></div>
                    <div><strong>Jabatan:</strong> <?php echo htmlspecialchars($status_data['jabatan']); ?></div>
                    <div><strong>Bidang Kerja:</strong> <?php echo htmlspecialchars($status_data['bidang_kerja']); ?></div>
                    <div><strong>Alamat Instansi:</strong> <?php echo htmlspecialchars($status_data['alamat_instansi']); ?></div>
                    <div><strong>Tahun Mulai:</strong> <?php echo $status_data['tahun_mulai']; ?></div>
                    <div><strong>Tahun Selesai:</strong> <?php echo $status_data['tahun_selesai'] ?? '-'; ?></div>
                    <div><strong>Gaji:</strong> <?php echo $status_data['gaji_range']; ?></div>
                    <div class="md:col-span-2"><strong>Deskripsi:</strong><br><?php echo nl2br(htmlspecialchars($status_data['deskripsi'])); ?></div>
                </div>
            <?php else: ?>
                <p class="text-gray-500 mb-4">Anda belum mengisi status alumni.</p>
                <a href="isi_status_alumni.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Isi Status Sekarang
                </a>
            <?php endif; ?>
        </div>

        <!-- Tambahan informasi lainnya bisa ditaruh di sini -->
    </div>
</body>
</html>

</body>
</html>