<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Pengumuman.php';

isAdmin(); // Cek akses admin

$database = new Database();
$db = $database->getConnection();
$pengumumanModel = new Pengumuman($db);

// Proses Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pengumuman'])) {
    $data = [
        'judul' => $_POST['judul'],
        'konten' => $_POST['konten'],
        'tanggal_mulai' => $_POST['tanggal_mulai'],
        'tanggal_selesai' => $_POST['tanggal_selesai'],
        'status' => $_POST['status'],
        'created_by' => getUserId()
    ];
    $pengumumanModel->create($data);
    header("Location: kelola_pengumuman.php");
    exit;
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $pengumumanModel->delete($_GET['hapus']);
    header("Location: kelola_pengumuman.php");
    exit;
}

// Proses Edit (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_pengumuman'])) {
    $pengumumanModel->update($_POST['id'], $_POST);
    echo json_encode(['success' => true]);
    exit;
}

$daftar_pengumuman = $pengumumanModel->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengumuman - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function editField(el, id, field) {
        const value = el.innerText;
        const input = document.createElement("input");
        input.value = value;
        input.className = "border px-2 py-1 w-full";
        input.onblur = function() {
            fetch("kelola_pengumuman.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    edit_pengumuman: 1,
                    id: id,
                    [field]: input.value
                })
            }).then(() => location.reload());
        };
        el.innerHTML = '';
        el.appendChild(input);
        input.focus();
    }
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Kelola Pengumuman</h2>
                <button onclick="document.getElementById('modal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah</button>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 border">Judul</th>
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar_pengumuman as $p): ?>
                        <tr class="text-sm">
                            <td class="border px-2 py-1 cursor-pointer" onclick="editField(this, <?php echo $p['id']; ?>, 'judul')"><?php echo htmlspecialchars($p['judul']); ?></td>
                            <td class="border px-2 py-1"><?php echo $p['tanggal_mulai'] . ' s/d ' . ($p['tanggal_selesai'] ?? '-'); ?></td>
                            <td class="border px-2 py-1 cursor-pointer" onclick="editField(this, <?php echo $p['id']; ?>, 'status')"><?php echo $p['status']; ?></td>
                            <td class="border px-2 py-1 text-center">
                                <a href="?hapus=<?php echo $p['id']; ?>" onclick="return confirm('Hapus pengumuman ini?')" class="text-red-600 hover:underline">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <form method="POST" class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg space-y-4">
            <h3 class="text-lg font-semibold mb-2">Tambah Pengumuman</h3>
            <input type="hidden" name="tambah_pengumuman" value="1">
            <input type="text" name="judul" placeholder="Judul" required class="w-full border rounded px-3 py-2">
            <textarea name="konten" placeholder="Konten" required class="w-full border rounded px-3 py-2"></textarea>
            <div class="grid grid-cols-2 gap-4">
                <input type="date" name="tanggal_mulai" required class="border rounded px-3 py-2">
                <input type="date" name="tanggal_selesai" class="border rounded px-3 py-2">
            </div>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal').classList.add('hidden')" class="px-4 py-2 border rounded">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
