<?php
require_once 'session.php';
require_once 'koneksi.php';
require_once 'Users.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $confirm_password === '' || $nama_lengkap === '') {
        $error_message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Password dan konfirmasi password tidak cocok!";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        if ($user->checkUsernameExists($username)) {
            $error_message = "Username sudah digunakan!";
        } elseif ($user->checkEmailExists($email)) {
            $error_message = "Email sudah digunakan!";
        } else {
            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            $user->role = 'alumni'; // default role
            $user->nama_lengkap = $nama_lengkap;

            if ($user->register()) {
                $success_message = "Registrasi berhasil! Silakan login.";
            } else {
                $error_message = "Registrasi gagal! Silakan coba lagi.";
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
    <title>Registrasi - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-indigo-700 via-blue-700 to-indigo-800 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white bg-opacity-80 backdrop-blur-lg rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-extrabold text-center text-indigo-900 mb-6">Daftar Akun SI Alumni</h1>

        <?php if ($error_message): ?>
            <div role="alert" class="mb-4 text-red-700 bg-red-100 p-3 rounded-md flex items-center gap-2">
                <span class="material-icons text-red-700">error</span>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div role="alert" class="mb-4 text-green-700 bg-green-100 p-3 rounded-md flex items-center gap-2">
                <span class="material-icons text-green-700">check_circle</span>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" novalidate class="space-y-5">
            <div>
                <label for="nama_lengkap" class="block text-indigo-900 font-semibold mb-1">Nama Lengkap</label>
                <input
                    id="nama_lengkap"
                    name="nama_lengkap"
                    type="text"
                    required
                    aria-required="true"
                    aria-describedby="namaHelp"
                    value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>"
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Masukkan nama lengkap"
                />
                <p id="namaHelp" class="text-xs text-indigo-600 mt-1">Pastikan nama sesuai dengan data resmi.</p>
            </div>
            <div>
                <label for="username" class="block text-indigo-900 font-semibold mb-1">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    required
                    aria-required="true"
                    aria-describedby="usernameHelp"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>"
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Pilih username unik"
                />
                <p id="usernameHelp" class="text-xs text-indigo-600 mt-1">Username digunakan untuk login dan harus unik.</p>
            </div>
            <div>
                <label for="email" class="block text-indigo-900 font-semibold mb-1">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    aria-required="true"
                    aria-describedby="emailHelp"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>"
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Masukkan email aktif"
                />
                <p id="emailHelp" class="text-xs text-indigo-600 mt-1">Email akan digunakan untuk komunikasi penting.</p>
            </div>
            <div>
                <label for="password" class="block text-indigo-900 font-semibold mb-1">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    aria-required="true"
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Buat password yang kuat"
                />
            </div>
            <div>
                <label for="confirm_password" class="block text-indigo-900 font-semibold mb-1">Konfirmasi Password</label>
                <input
                    id="confirm_password"
                    name="confirm_password"
                    type="password"
                    required
                    aria-required="true"
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Ulangi password"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold px-4 py-3 rounded-md shadow-md transition"
            >
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-indigo-900">
            Sudah punya akun? <a href="login.php" class="underline font-semibold hover:text-indigo-700">Masuk di sini</a>
        </p>
    </div>
</body>
</html>

