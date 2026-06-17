<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama, profile_picture FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$nama = $user['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Menu Utama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-image: url('Uploads/background1.svg');
            background-size: cover;
            background-repeat: no-repeat;
            overflow: hidden;
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .star {
            position: absolute;
            background: rgba(255, 215, 0, 0.8);
            border-radius: 50%;
            animation: twinkle 2s infinite alternate, move 10s infinite linear;
        }
        @keyframes twinkle {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
        @keyframes move {
            0% { transform: translateY(100vh); }
            100% { transform: translateY(-10vh); }
        }
        .star:nth-child(1) { width: 20px; height: 20px; top: 10%; left: 20%; animation-delay: 0s; }
        .star:nth-child(2) { width: 24px; height: 24px; top: 30%; left: 40%; animation-delay: 1s; }
        .star:nth-child(3) { width: 10px; height: 10px; top: 50%; left: 60%; animation-delay: 2s; }
        .star:nth-child(4) { width: 16px; height: 16px; top: 70%; left: 80%; animation-delay: 0.5s; }
        .star:nth-child(5) { width: 10px; height: 10px; top: 20%; left: 70%; animation-delay: 1.5s; }
        .star:nth-child(6) { width: 7px; height: 7px; top: 15%; left: 30%; animation-delay: 0.8s; }
        .star:nth-child(7) { width: 13px; height: 13px; top: 40%; left: 50%; animation-delay: 1.2s; }
        .star:nth-child(8) { width: 20px; height: 20px; top: 60%; left: 10%; animation-delay: 0.3s; }
        .profile-img {
            object-fit: cover;
        }
        header {
            animation: slideDown 1s ease-out;
        }
        @keyframes slideDown {
            0% { transform: translateY(-100%); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        main {
            animation: fadeIn 1.5s ease-in-out;
        }
        .grid a {
            animation: bounceIn 1s ease-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center">
    <div class="stars">
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
    </div>
    <header class="w-full p-4 flex items-center fixed top-0 bg-white shadow-md z-10">
        <div class="flex items-center space-x-2 cursor-pointer" onclick="window.location='profil.php'">
            <?php if ($user['profile_picture']): ?>
                <img src="Uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil: <?php echo htmlspecialchars($nama); ?>" class="w-10 h-10 rounded-full profile-img">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">👤</div>
            <?php endif; ?>
            <span class="text-blue-800 font-semibold">Halo <?php echo htmlspecialchars($nama); ?>!</span>
        </div>
        <div class="flex-grow text-center">
            <div class="text-2xl font-bold text-blue-800">Pintar Ceria</div>
        </div>
        <nav>
            <button id="logoutBtn" class="text-blue-500 hover:underline ml-4">Keluar</button>
        </nav>
    </header>
    <main class="pt-0">
        <div class="grid grid-cols-1 gap-6 ml-96 mt-20 relative z-20">
            <a href="kuis-list.php" class="bg-pink-500 text-white px-20 py-10 rounded-3xl hover:bg-pink-400 transition duration-300 shadow-lg text-center text-xl font-semibold">Mainkan Kuis</a>
            <a href="papan-skor.php" class="bg-pink-500 text-white px-20 py-10 rounded-3xl hover:bg-pink-400 transition duration-300 shadow-lg text-center text-xl font-semibold">Lihat Papan Skor</a>
        </div>
        <div class="character fixed top-10 right-10 opacity-0 transition-opacity duration-1000" style="animation: fadeIn 1s forwards 0.5s;">
            <img src="Uploads/character2.png" alt="Karakter Pintar Ceria" class="w-67 h-67">
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const character = document.querySelector('.character');
            if (character) character.classList.add('opacity-100');

            document.getElementById('logoutBtn').addEventListener('click', (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin untuk keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('logout.php', { method: 'POST' })
                            .then(() => {
                                Swal.fire({
                                    title: 'Logout berhasil!',
                                    text: 'Terima kasih sudah bermain dan belajar. Sampai jumpa!',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = 'index.php';
                                });
                            });
                    }
                });
            });
        });
    </script>
</body>
</html>