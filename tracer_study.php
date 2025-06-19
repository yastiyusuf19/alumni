<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Tracer.php';
require_once 'Alumni.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();
$tracer = new Tracer($db);
$alumni = new Alumni($db);

$user_id = $_SESSION['user_id'];
$alumni_data = $alumni->getByUserId($user_id);
$alumni_id = $alumni_data['id'] ?? null;
$tahun_ini = date('Y');

// Cek apakah sudah mengisi tahun ini
$existing = $tracer->getByAlumniIdAndYear($alumni_id, $tahun_ini);
$sukses = false;

// Jika form dikirim dan belum ada isian tahun ini
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existing) {
    $data = [
        'alumni_id' => $alumni_id,
        'kepuasan_program_studi' => $_POST['kepuasan_program_studi'],
        'relevansi_pekerjaan' => $_POST['relevansi_pekerjaan'],
        'saran_perbaikan' => $_POST['saran_perbaikan'],
        'kesediaan_rekrutmen' => $_POST['kesediaan_rekrutmen'],
        'kesediaan_mentor' => $_POST['kesediaan_mentor'],
        'tahun_survey' => $tahun_ini
    ];
    if ($tracer->create($data)) {
        $sukses = true;
        $existing = $tracer->getByAlumniIdAndYear($alumni_id, $tahun_ini);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tracer Study - Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto py-8 px-4">
        <div class="bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold text-blue-700 mb-4">Formulir Tracer Study</h1>

            <?php if ($existing): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    <strong>✅ Terima kasih!</strong> Anda telah mengisi tracer study untuk tahun <strong><?= $tahun_ini ?></strong>.
                </div>

                <div class="space-y-2 text-gray-700">
                    <p><strong>Kepuasan Program Studi:</strong> <?= $existing['kepuasan_program_studi'] ?> / 5</p>
                    <p><strong>Relevansi Pekerjaan:</strong> <?= $existing['relevansi_pekerjaan'] ?> / 5</p>
                    <p><strong>Saran Perbaikan:</strong><br><?= nl2br(htmlspecialchars($existing['saran_perbaikan'])) ?></p>
                    <p><strong>Bersedia Rekrut Alumni:</strong> <?= ucfirst($existing['kesediaan_rekrutmen']) ?></p>
                    <p><strong>Bersedia Jadi Mentor:</strong> <?= ucfirst($existing['kesediaan_mentor']) ?></p>
                </div>

            <?php else: ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block font-medium">Kepuasan terhadap Program Studi (1–5)</label>
                        <input type="number" name="kepuasan_program_studi" min="1" max="5" required class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Relevansi Pekerjaan dengan Studi (1–5)</label>
                        <input type="number" name="relevansi_pekerjaan" min="1" max="5" required class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Saran Perbaikan Program Studi</label>
                        <textarea name="saran_perbaikan" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
                    </div>

                    <div>
                        <label class="block font-medium">Bersedia merekrut alumni?</label>
                        <select name="kesediaan_rekrutmen" class="w-full border rounded px-3 py-2" required>
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium">Bersedia menjadi mentor?</label>
                        <select name="kesediaan_mentor" class="w-full border rounded px-3 py-2" required>
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
