<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Lowongan.php';
require_once 'Alumni.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

$lowongan = new Lowongan($db);
$alumni = new Alumni($db);

$user_id = getUserId();
$alumni_data = $alumni->getByUserId($user_id);

// Redirect jika belum isi data alumni
if (!$alumni_data) {
    header("Location: profile.php");
    exit;
}

$lowongan_id = $_GET['id'] ?? null;
$lowongan_data = $lowongan->getById($lowongan_id);

if (!$lowongan_data) {
    echo "Lowongan tidak ditemukan.";
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surat_lamaran = $_POST['surat_lamaran'];
    $cv_file_name = null;

    // Upload file CV
    $ext = pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION);
$cv_file_name = uniqid() . '.' . $ext;
$targetDir = 'uploads/cv/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
move_uploaded_file($_FILES['cv_file']['tmp_name'], $targetDir . $cv_file_name);


    $stmt = $db->prepare("INSERT INTO lamaran_kerja (lowongan_id, alumni_id, cv_file, surat_lamaran) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$lowongan_id, $alumni_data['id'], $cv_file_name, $surat_lamaran]);

    if ($result) {
        $success = "Lamaran berhasil dikirim.";
    } else {
        $error = "Gagal mengirim lamaran.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lamar Pekerjaan - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">SI Alumni</h1>
        <div>👤 <?php echo htmlspecialchars(getUserName()); ?></div>
    </nav>

    <div class="max-w-3xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-blue-700">Lamar Pekerjaan: <?php echo htmlspecialchars($lowongan_data['posisi']); ?></h2>

        <p class="text-sm text-gray-600 mb-2">Perusahaan: <strong><?php echo htmlspecialchars($lowongan_data['nama_perusahaan']); ?></strong></p>
        <p class="text-sm text-gray-600 mb-4">Lokasi: <?php echo htmlspecialchars($lowongan_data['lokasi']); ?></p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700 mb-1">Unggah CV (PDF, max 2MB)</label>
                <input type="file" name="cv_file" accept=".pdf" class="w-full border border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Surat Lamaran</label>
                <textarea name="surat_lamaran" rows="5" class="w-full border border-gray-300 rounded p-2" required></textarea>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Kirim Lamaran
                </button>
            </div>
        </form>
    </div>
</body>
</html>
