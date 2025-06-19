<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Lowongan.php';

requireLogin();
if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$lowongan = new Lowongan($db);

// Tambah Lowongan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $data = [
        'posisi' => $_POST['posisi'],
        'nama_perusahaan' => $_POST['nama_perusahaan'],
        'lokasi' => $_POST['lokasi'],
        'deskripsi' => $_POST['deskripsi'],
    ];
    $lowongan->create($data);
    header("Location: kelola_lowongan.php");
    exit();
}

// Update Lowongan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $lowongan->update($_POST['id'], $_POST);
    header("Location: kelola_lowongan.php");
    exit();
}

// Hapus Lowongan
if (isset($_GET['hapus'])) {
    $lowongan->delete($_GET['hapus']);
    header("Location: kelola_lowongan.php");
    exit();
}

$data = $lowongan->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Lowongan Kerja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between">
        <h1 class="text-xl font-bold">Kelola Lowongan Kerja</h1>
        <div><?php echo htmlspecialchars(getUserName()); ?></div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <!-- Tombol Tambah -->
        <button onclick="document.getElementById('modal').classList.remove('hidden')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4">+ Tambah Lowongan</button>

        <!-- Tabel Lowongan -->
        <div class="bg-white p-6 rounded-lg shadow overflow-auto">
            <table class="table-auto w-full text-sm text-left">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="p-2">Posisi</th>
                        <th class="p-2">Perusahaan</th>
                        <th class="p-2">Lokasi</th>
                        <th class="p-2">Deskripsi</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $row): ?>
                    <tr class="border-b">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <td class="p-2"><input name="posisi" value="<?= htmlspecialchars($row['posisi']) ?>" class="border w-full px-2 py-1"></td>
                            <td class="p-2"><input name="nama_perusahaan" value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" class="border w-full px-2 py-1"></td>
                            <td class="p-2"><input name="lokasi" value="<?= htmlspecialchars($row['lokasi']) ?>" class="border w-full px-2 py-1"></td>
                            <td class="p-2"><textarea name="deskripsi" class="border w-full px-2 py-1"><?= htmlspecialchars($row['deskripsi']) ?></textarea></td>
                            <td class="p-2 space-x-2">
                                <button name="update" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
                                <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Hapus</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg relative">
            <h2 class="text-xl font-bold mb-4">Tambah Lowongan</h2>
            <form method="POST">
                <input type="hidden" name="tambah" value="1">
                <div class="mb-3">
                    <label>Posisi</label>
                    <input type="text" name="posisi" class="border w-full px-2 py-1" required>
                </div>
                <div class="mb-3">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="border w-full px-2 py-1" required>
                </div>
                <div class="mb-3">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" class="border w-full px-2 py-1" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="border w-full px-2 py-1" rows="4" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                    <button type="button" onclick="document.getElementById('modal').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Batal</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
