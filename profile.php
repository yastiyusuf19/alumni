<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Alumni.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$alumniModel = new Alumni($db);

$user_id = getUserId();
$user_name = getUserName();

$message = '';
$error = '';

// Fetch current user's alumni profile
$alumni = $alumniModel->getByUserId($user_id);

// If no alumni record found, initialize as empty array to avoid errors
if (!$alumni || !is_array($alumni)) {
    $alumni = [];
}

// Helper function to get value safely from $alumni array
function safeGet($array, $key, $default = '') {
    return (is_array($array) && isset($array[$key]) && $array[$key] !== null) ? $array[$key] : $default;
}

// Handle form submission for updating profile and uploading photo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $angkatan = trim($_POST['angkatan'] ?? '');
    $program_studi = trim($_POST['program_studi'] ?? '');
    $tahun_lulus = trim($_POST['tahun_lulus'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $email_alternatif = trim($_POST['email_alternatif'] ?? '');

    if (!$nama_lengkap || !$angkatan || !$program_studi) {
        $error = "Nama lengkap, angkatan, dan program studi wajib diisi.";
    } else {
        $data = [
            'nama_lengkap' => $nama_lengkap,
            'angkatan' => $angkatan,
            'program_studi' => $program_studi,
            'tahun_lulus' => $tahun_lulus,
            'alamat' => $alamat,
            'no_telepon' => $no_telepon,
            'email_alternatif' => $email_alternatif
        ];

        // Handle profile photo upload if file chosen
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName = basename($_FILES['profile_photo']['name']);
            $fileSize = $_FILES['profile_photo']['size'];
            $fileType = $_FILES['profile_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = 'uploads/profile_photos/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $data['profile_photo'] = $dest_path;
                } else {
                    $error = 'Terjadi kesalahan saat mengunggah foto profil.';
                }
            } else {
                $error = 'Format file foto profil tidak diperbolehkan. Hanya jpg, jpeg, png, dan gif yang diterima.';
            }
        }

        if (!$error) {
            try {
                // If user has alumni record, update it, else create new record
                if (isset($alumni['id'])) {
                    $success = $alumniModel->update($alumni['id'], $data);
                } else {
                    // Assign user_id and some defaults if creating new record
                    $data['user_id'] = $user_id;
                    $data['nim'] = ''; // You can add input field later if needed
                    $newId = $alumniModel->create($data);
                    $success = $newId !== false;
                }
                if ($success) {
                    $message = "Profil berhasil diperbarui.";
                    // Refresh updated profile
                    $alumni = $alumniModel->getByUserId($user_id);
                    if (!$alumni || !is_array($alumni)) {
                        $alumni = [];
                    }
                } else {
                    $error = "Gagal memperbarui profil.";
                }
            } catch (Exception $e) {
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil Alumni - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<header class="sticky top-0 backdrop-blur-lg bg-white bg-opacity-70 border-b border-gray-300 shadow-sm h-16 flex items-center px-6 z-50">
    <div class="flex items-center space-x-2 text-indigo-700 font-semibold text-lg">
        <span class="material-icons md-24" aria-hidden="true">person</span>
        <span>Profil Alumni</span>
    </div>
</header>

<main class="flex-1 p-6 max-w-3xl mx-auto">

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded flex items-center gap-2" role="alert">
            <span class="material-icons text-green-700">check_circle</span>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-200 text-red-800 rounded flex items-center gap-2" role="alert">
            <span class="material-icons text-red-700">error</span>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6" novalidate>
        <div class="flex flex-col items-center space-y-4">
            <?php if (!empty($alumni['profile_photo']) && file_exists($alumni['profile_photo'])): ?>
                <img src="<?php echo htmlspecialchars($alumni['profile_photo']); ?>" alt="Foto profil <?php echo htmlspecialchars(safeGet($alumni, 'nama_lengkap', $user_name)); ?>" class="w-32 h-32 rounded-full object-cover shadow-md" loading="lazy" />
            <?php else: ?>
                <span class="material-icons text-indigo-500 w-32 h-32 text-9xl rounded-full bg-indigo-100 flex items-center justify-center">person</span>
            <?php endif; ?>
            <label for="profile_photo" class="cursor-pointer text-indigo-600 hover:text-indigo-800 underline font-semibold">
                Unggah / Ganti Foto Profil
            </label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden" />
        </div>

        <div>
            <label for="nama_lengkap" class="block font-semibold text-indigo-900 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
            <input id="nama_lengkap" name="nama_lengkap" type="text" required aria-required="true"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'nama_lengkap', $user_name)); ?>"
            />
        </div>
        <div>
            <label for="angkatan" class="block font-semibold text-indigo-900 mb-1">Angkatan <span class="text-red-600">*</span></label>
            <input id="angkatan" name="angkatan" type="number" min="1900" max="<?php echo date('Y'); ?>" required aria-required="true"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'angkatan')); ?>"
            />
        </div>
        <div>
            <label for="program_studi" class="block font-semibold text-indigo-900 mb-1">Program Studi <span class="text-red-600">*</span></label>
            <input id="program_studi" name="program_studi" type="text" required aria-required="true"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'program_studi')); ?>"
            />
        </div>
        <div>
            <label for="tahun_lulus" class="block font-semibold text-indigo-900 mb-1">Tahun Lulus</label>
            <input id="tahun_lulus" name="tahun_lulus" type="number" min="1900" max="<?php echo date('Y'); ?>"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'tahun_lulus')); ?>"
            />
        </div>
        <div>
            <label for="alamat" class="block font-semibold text-indigo-900 mb-1">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y"
            ><?php echo htmlspecialchars(safeGet($alumni, 'alamat')); ?></textarea>
        </div>
        <div>
            <label for="no_telepon" class="block font-semibold text-indigo-900 mb-1">No. Telepon</label>
            <input id="no_telepon" name="no_telepon" type="tel"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'no_telepon')); ?>"
            />
        </div>
        <div>
            <label for="email_alternatif" class="block font-semibold text-indigo-900 mb-1">Email Alternatif</label>
            <input id="email_alternatif" name="email_alternatif" type="email"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value="<?php echo htmlspecialchars(safeGet($alumni, 'email_alternatif')); ?>"
            />
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-3 rounded-md shadow-md transition">
            Perbarui Profil
        </button>
    </form>
</main>

<script>
    // Show filename on file select (optional)
    const inputFile = document.getElementById('profile_photo');
    const label = inputFile.previousElementSibling;
    inputFile.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            label.textContent = 'Foto telah dipilih: ' + this.files[0].name;
        } else {
            label.textContent = 'Unggah / Ganti Foto Profil';
        }
    });
</script>

</body>
</html>

