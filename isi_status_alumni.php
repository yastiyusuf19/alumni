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

$user_id = getUserId();
$alumni_data = $alumni->getByUserId($user_id);

if (!$alumni_data) {
    die("Data alumni tidak ditemukan.");
}

$status_data = $statusModel->getByAlumniId($alumni_data['id']);
$is_edit = $status_data ? true : false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'alumni_id'        => $alumni_data['id'],
        'status_saat_ini'  => $_POST['status_saat_ini'] ?? '',
        'nama_instansi'    => $_POST['nama_instansi'] ?? '',
        'jabatan'          => $_POST['jabatan'] ?? '',
        'bidang_kerja'     => $_POST['bidang_kerja'] ?? '',
        'alamat_instansi'  => $_POST['alamat_instansi'] ?? '',
        'tahun_mulai'      => $_POST['tahun_mulai'] ?? '',
        'tahun_selesai'    => $_POST['tahun_selesai'] ?? null,
        'gaji_range'       => $_POST['gaji_range'] ?? '',
        'deskripsi'        => $_POST['deskripsi'] ?? '',
    ];

    if ($is_edit) {
        $statusModel->update($status_data['id'], $data);
    } else {
        $statusModel->create($data);
    }

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Isi Status Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
        <div class="text-xl font-bold">Status Alumni</div>
        <div><?php echo htmlspecialchars(getUserName()); ?></div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded shadow p-6 max-w-3xl mx-auto">
            <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Perbarui' : 'Isi'; ?> Status Alumni</h2>

            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-1 font-medium">Status Saat Ini</label>
                        <select name="status_saat_ini" required class="w-full border px-3 py-2 rounded">
                            <option value="">-- Pilih --</option>
                            <?php
                            $opsi = ['bekerja', 'kuliah', 'wirausaha', 'mencari_kerja', 'lainnya'];
                            foreach ($opsi as $val) {
                                $sel = ($status_data && $status_data['status_saat_ini'] === $val) ? 'selected' : '';
                                echo "<option value='$val' $sel>" . ucwords(str_replace('_', ' ', $val)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Nama Instansi</label>
                        <input type="text" name="nama_instansi" value="<?php echo htmlspecialchars($status_data['nama_instansi'] ?? ''); ?>" class="w-full border px-3 py-2 rounded">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Jabatan</label>
                        <input type="text" name="jabatan" value="<?php echo htmlspecialchars($status_data['jabatan'] ?? ''); ?>" class="w-full border px-3 py-2 rounded">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Bidang Kerja</label>
                        <input type="text" name="bidang_kerja" value="<?php echo htmlspecialchars($status_data['bidang_kerja'] ?? ''); ?>" class="w-full border px-3 py-2 rounded">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Tahun Mulai</label>
                        <input type="number" name="tahun_mulai" min="2000" max="2099" value="<?php echo htmlspecialchars($status_data['tahun_mulai'] ?? ''); ?>" class="w-full border px-3 py-2 rounded">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Tahun Selesai (Opsional)</label>
                        <input type="number" name="tahun_selesai" min="2000" max="2099" value="<?php echo htmlspecialchars($status_data['tahun_selesai'] ?? ''); ?>" class="w-full border px-3 py-2 rounded">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Range Gaji</label>
                        <select name="gaji_range" class="w-full border px-3 py-2 rounded">
                            <option value="">-- Pilih --</option>
                            <?php
                            $gaji_opsi = ['< 3 juta', '3-5 juta', '5-10 juta', '10-15 juta', '> 15 juta'];
                            foreach ($gaji_opsi as $gaji) {
                                $sel = ($status_data && $status_data['gaji_range'] === $gaji) ? 'selected' : '';
                                echo "<option value='$gaji' $sel>$gaji</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-1 font-medium">Alamat Instansi</label>
                        <textarea name="alamat_instansi" rows="2" class="w-full border px-3 py-2 rounded"><?php echo htmlspecialchars($status_data['alamat_instansi'] ?? ''); ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="w-full border px-3 py-2 rounded"><?php echo htmlspecialchars($status_data['deskripsi'] ?? ''); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?php echo $is_edit ? 'Perbarui' : 'Simpan'; ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
