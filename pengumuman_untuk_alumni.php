<?php
require_once 'koneksi.php';
require_once 'Pengumuman.php';

$database = new Database();
$db = $database->getConnection();
$pengumumanModel = new Pengumuman($db);

// Get all published announcements sorted by date (newest first)
$pengumuman = $pengumumanModel->getAllPublished();

// If we want to filter by current date (only show active announcements)
// $pengumuman = $pengumumanModel->getActivePublished();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .announcement-card {
            transition: all 0.3s ease;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .read-more {
            display: none;
        }
        .fade-out {
            position: relative;
            max-height: 100px;
            overflow: hidden;
        }
        .fade-out:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1));
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Pengumuman Alumni</h1>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="hover:underline">Login Admin</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Search and Filter Section -->
        <div class="mb-8 bg-white p-4 rounded-lg shadow">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative flex-grow">
                    <input type="text" placeholder="Cari pengumuman..." 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="absolute right-2 top-2 text-gray-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="flex space-x-2">
                    <select class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Semua Kategori</option>
                        <option>Lowongan Kerja</option>
                        <option>Event</option>
                        <option>Informasi</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Terbaru</option>
                        <option>Terlama</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Announcements Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($pengumuman)): ?>
                <div class="col-span-full text-center py-10">
                    <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Belum ada pengumuman yang tersedia</p>
                </div>
            <?php else: ?>
                <?php foreach ($pengumuman as $item): ?>
                    <div class="announcement-card bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <!-- Date Badge -->
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="far fa-calendar-alt mr-2"></i>
                                <?php 
                                    $startDate = date('d M Y', strtotime($item['tanggal_mulai']));
                                    $endDate = $item['tanggal_selesai'] ? date('d M Y', strtotime($item['tanggal_selesai'])) : null;
                                    echo $startDate;
                                    if ($endDate && $endDate != $startDate) {
                                        echo " - " . $endDate;
                                    }
                                ?>
                            </div>
                            
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-800 mb-3"><?php echo htmlspecialchars($item['judul']); ?></h3>
                            
                            <!-- Content (with read more functionality) -->
                            <div class="text-gray-600 mb-4 fade-out" id="content-<?php echo $item['id']; ?>">
                                <?php echo nl2br(htmlspecialchars($item['konten'])); ?>
                            </div>
                            
                            <!-- Read More Button -->
                            <button onclick="toggleReadMore(<?php echo $item['id']; ?>)" 
                                    class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                Baca selengkapnya
                                <i class="fas fa-chevron-down ml-1 text-sm"></i>
                            </button>
                        </div>
                        
                        <!-- Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span>
                                    <i class="far fa-user mr-1"></i>
                                    Admin
                                </span>
                                <span>
                                    <?php 
                                        $statusClass = [
                                            'published' => 'bg-green-100 text-green-800',
                                            'draft' => 'bg-yellow-100 text-yellow-800',
                                            'archived' => 'bg-gray-100 text-gray-800'
                                        ];
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs <?php echo $statusClass[strtolower($item['status'])]; ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination (if needed) -->
        <div class="mt-8 flex justify-center">
            <nav class="inline-flex rounded-md shadow">
                <a href="#" class="px-3 py-1 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="#" class="px-3 py-1 border-t border-b border-gray-300 bg-white text-blue-600 hover:bg-blue-50">1</a>
                <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">2</a>
                <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">3</a>
                <a href="#" class="px-3 py-1 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-6 md:mb-0">
                    <h3 class="text-xl font-bold mb-4">SI Alumni</h3>
                    <p class="text-gray-400">Sistem informasi alumni untuk menjaga silaturahmi dan informasi terbaru.</p>
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Tautan</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Beranda</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Pengumuman</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Lowongan Kerja</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Jl. Contoh No. 123
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone-alt mr-2"></i>
                                (021) 123-4567
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                info@sialumni.example
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> SI Alumni. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle read more functionality
        function toggleReadMore(id) {
            const content = document.getElementById(`content-${id}`);
            const button = content.nextElementSibling;
            
            if (content.classList.contains('fade-out')) {
                content.classList.remove('fade-out');
                content.style.maxHeight = 'none';
                button.innerHTML = 'Tutup <i class="fas fa-chevron-up ml-1 text-sm"></i>';
            } else {
                content.classList.add('fade-out');
                content.style.maxHeight = '100px';
                button.innerHTML = 'Baca selengkapnya <i class="fas fa-chevron-down ml-1 text-sm"></i>';
            }
        }

        // Initialize all content with fade-out
        document.addEventListener('DOMContentLoaded', function() {
            const contents = document.querySelectorAll('.fade-out');
            contents.forEach(content => {
                content.style.maxHeight = '100px';
            });
        });
    </script>
</body>
</html>
