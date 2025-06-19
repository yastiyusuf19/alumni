<?php
// login.php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'Users.php';

if (isLoggedIn()) {
    // Jika sudah login, arahkan sesuai role
    if (getUserRole() === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

$database = new Database();
$db = $database->getConnection();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['nama_lengkap'];

        if ($user['role'] === 'admin') {
            header('Location: dashboard_admin.php');
        } else {
            header('Location: dashboard.php'); // alumni
        }
        exit;
    } else {
        $error_message = 'Username atau password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login - SI Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-700 to-indigo-800 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white bg-opacity-80 backdrop-blur-lg rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-extrabold text-center text-indigo-900 mb-6">Masuk ke SI Alumni</h1>

        <?php if (!empty($error_message)): ?>
            <div role="alert" class="mb-4 text-red-700 bg-red-100 p-3 rounded-md flex items-center gap-2">
                <span class="material-icons text-red-700">error</span>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6" novalidate>
            <div>
                <label for="username" class="block text-indigo-900 font-semibold mb-1">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    required
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>"
                />
            </div>
            <div>
                <label for="password" class="block text-indigo-900 font-semibold mb-1">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="w-full rounded-md border border-indigo-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan password"
                />
            </div>

            <button
                type="submit"
                name="login"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-3 rounded-md"
            >
                Masuk
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-indigo-900">
            Belum punya akun? <a href="registrasi.php" class="underline font-semibold hover:text-indigo-700">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
