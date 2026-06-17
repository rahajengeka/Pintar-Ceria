<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama, umur, username, profile_picture FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #bfdbfe, #fef9c3);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .profile-img {
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .profile-img:hover {
            transform: scale(1.1);
        }
        .profile-card {
            animation: fadeIn 1s ease-in-out;
        }
        .edit-button {
            transition: transform 0.2s ease;
        }
        .edit-button:hover {
            transform: translateY(-3px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex flex-col items-center">
    <header class="w-full p-3 flex justify-between items-center fixed top-0 bg-white shadow-md z-10">
        <div class="flex items-center space-x-2 cursor-pointer" onclick="window.location='profil.php'">
            <?php if ($user['profile_picture']): ?>
                <img src="Uploads/<?= htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil: <?= htmlspecialchars($user['nama']); ?>" class="w-10 h-10 rounded-full profile-img">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">👤</div>
            <?php endif; ?>
            <span class="text-blue-800 font-semibold">Halo, <?= htmlspecialchars($user['nama']); ?>!</span>
        </div>
    </header>
    <main class="pt-20 p-6 w-full max-w-md">
        <h1 class="text-2xl font-bold text-blue-800 mb-6 text-center">Ayo Lengkapi Profilmu!</h1>
        <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col items-center profile-card">
            <div class="mb-4">
                <?php if ($user['profile_picture']): ?>
                    <img src="Uploads/<?= htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil: <?= htmlspecialchars($user['nama']); ?>" class="w-24 h-24 rounded-full mx-auto profile-img">
                <?php else: ?>
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-3xl">👤</div>
                <?php endif; ?>
            </div>
            <div class="text-center space-y-2">
                <p><span class="font-bold">Nama:</span> <?= htmlspecialchars($user['nama']); ?></p>
                <p><span class="font-bold">Umur:</span> <?= htmlspecialchars($user['umur']); ?></p>
                <p><span class="font-bold">Username:</span> <?= htmlspecialchars($user['username']); ?></p>
            </div>
            <a href="edit-profil.php" class="mt-6 bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 edit-button">Edit Profil</a>
            <a href="main-menu.php" class="mt-4 text-blue-500 hover:underline">Kembali</a>
        </div>
    </main>
</body>
</html>