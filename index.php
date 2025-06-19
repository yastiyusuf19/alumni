<?php
// If user is already logged in, redirect to dashboard
require_once 'session.php';
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SI Alumni - Selamat Datang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <style>
      /* Smooth button animations */
      .btn-primary {
        @apply bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-transform duration-300 ease-out hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-400;
      }
      /* Glass morphism card */
      .hero-card {
        @apply bg-white bg-opacity-80 backdrop-blur-lg rounded-3xl p-10 shadow-xl max-w-4xl mx-auto;
      }
      /* Custom styles for the logo */
      .logo {
        @apply h-24 w-24 mx-auto mb-6 rounded-full border-4 border-indigo-600 object-cover transition-transform duration-300 ease-in-out transform hover:scale-110;
      }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-800 via-blue-800 to-indigo-900 min-h-screen flex flex-col">

<header class="sticky top-0 h-16 backdrop-blur-lg bg-white bg-opacity-30 border-b border-indigo-400 flex items-center px-6 shadow-md z-50">
    <div class="flex items-center space-x-3">
        <img src="LogoITH.png.png" alt="Logo ITH" class="h-10 w-auto" />
        <h1 class="text-indigo-900 font-extrabold text-xl tracking-wide select-none">SI Alumni</h1>
    </div>
</header>

<main class="flex-grow flex items-center justify-center px-4 py-16">
  <section class="hero-card text-center">
    <h2 class="text-4xl font-extrabold text-white mb-6 leading-none sm:text-5xl">
      Selamat Datang di <br />
      <span class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">
        Sistem Informasi Alumni
      </span>
    </h2>
    <p class="text-white text-lg max-w-xl mx-auto mb-10">
      Platform terintegrasi untuk mengelola data alumni, melacak perkembangan karir, dan memfasilitasi komunikasi antara alumni dan institusi.
    </p>
    <div class="flex flex-col sm:flex-row justify-center gap-6">
      <a href="login.php" class="btn-primary bg-indigo-500 hover:bg-indigo-600 focus:ring-indigo-300 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-transform transform hover:scale-105" role="button" aria-label="Masuk ke akun">Masuk</a>
      <a href="registrasi.php" class="btn-primary bg-indigo-500 hover:bg-indigo-600 focus:ring-indigo-300 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-transform transform hover:scale-105" role="button" aria-label="Daftar akun baru">Daftar</a>
    </div>
  </section>
</main>



<footer class="bg-indigo-900 text-indigo-200 py-6 text-center text-sm select-none">
    &copy; <?php echo date("Y"); ?> SI Alumni. Semua hak cipta dilindungi.
</footer>

</body>
</html>
