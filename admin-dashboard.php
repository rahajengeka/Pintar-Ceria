<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e0f7fa, #fff3e0);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="w-full p-4 flex justify-between items-center fixed top-0 bg-white shadow-md z-10">
        <div class="text-2xl font-bold text-blue-800">Pintar Ceria</div>
        <nav>
            <a href="logout.php" class="text-blue-500 hover:underline text-base">Keluar</a>
        </nav>
    </header>

    <!-- Konten -->
    <main class="flex flex-col items-center justify-center min-h-screen pt-13 px-4">
        <h1 class="text-4xl font-bold text-center text-blue-900 mb-10">Dashboard Admin</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl w-full px-4">
            <a href="admin-soal.php" class="bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-xl py-10 text-center transition duration-300 shadow-md">
                Kelola Soal
            </a>
            <a href="admin-pengguna.php" class="bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-xl py-10 text-center transition duration-300 shadow-md">
                Kelola Pengguna
            </a>
        </div>
    </main>
</body>
</html>