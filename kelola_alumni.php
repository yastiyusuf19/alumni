<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';
requireLogin();
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$alumni = new Alumni($db);

// Handle tambah alumni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $alumni->create([
        'user_id' => $_POST['user_id'] ?? null,
        'nim' => $_POST['nim'],
        'nama_lengkap' => $_POST['nama_lengkap'],
        'angkatan' => $_POST['angkatan'],
        'program_studi' => $_POST['program_studi'],
        'tahun_lulus' => $_POST['tahun_lulus'],
        'alamat' => $_POST['alamat'],
        'no_telepon' => $_POST['no_telepon'],
        'email_alternatif' => $_POST['email_alternatif']
    ]);
    header("Location: kelola_alumni.php");
    exit;
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
    $alumni->update($_POST['id'], $_POST);
    echo json_encode(['status' => 'success']);
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'hapus') {
    $alumni->delete($_POST['id']);
    echo json_encode(['status' => 'deleted']);
    exit;
}

$data_alumni = $alumni->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function editField(id, field, value) {
            fetch('kelola_alumni.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({aksi: 'edit', id: id, [field]: value})
            });
        }

        function deleteAlumni(id) {
            if (confirm('Yakin ingin menghapus alumni ini?')) {
                fetch('kelola_alumni.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({aksi: 'hapus', id: id})
                }).then(() => location.reload());
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="p-6">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-700">Kelola Data Alumni</h1>
            <button onclick="document.getElementById('modal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow">+ Tambah Alumni</button>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Angkatan</th>
                        <th class="px-4 py-2">Program Studi</th>
                        <th class="px-4 py-2">Tahun Lulus</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_alumni as $a): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td contenteditable onblur="editField(<?php echo $a['id']; ?>, 'nama_lengkap', this.innerText)" class="px-4 py-2"><?php echo htmlspecialchars($a['nama_lengkap']); ?></td>
                            <td contenteditable onblur="editField(<?php echo $a['id']; ?>, 'angkatan', this.innerText)" class="px-4 py-2"><?php echo $a['angkatan']; ?></td>
                            <td contenteditable onblur="editField(<?php echo $a['id']; ?>, 'program_studi', this.innerText)" class="px-4 py-2"><?php echo htmlspecialchars($a['program_studi']); ?></td>
                            <td contenteditable onblur="editField(<?php echo $a['id']; ?>, 'tahun_lulus', this.innerText)" class="px-4 py-2"><?php echo $a['tahun_lulus']; ?></td>
                            <td class="px-4 py-2 text-red-600 cursor-pointer" onclick="deleteAlumni(<?php echo $a['id']; ?>)">🗑 Hapus</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Alumni -->
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-xl relative">
                <button onclick="document.getElementById('modal').classList.add('hidden')" class="absolute top-2 right-4 text-gray-500 text-xl">✕</button>
                <h2 class="text-xl font-bold mb-4">Tambah Alumni</h2>
                <form method="POST">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold">NIM</label>
                            <input type="text" name="nim" required class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">Angkatan</label>
                            <input type="number" name="angkatan" required class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">Program Studi</label>
                            <input type="text" name="program_studi" required class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" required class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">No Telepon</label>
                            <input type="text" name="no_telepon" class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold">Email Alternatif</label>
                            <input type="email" name="email_alternatif" class="w-full border p-2 rounded">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold">Alamat</label>
                            <textarea name="alamat" rows="2" class="w-full border p-2 rounded"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
