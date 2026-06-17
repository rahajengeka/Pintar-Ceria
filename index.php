<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: profil.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            max-width: 500px;
            max-height: 70vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.7);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .modal-character {
            display: none;
            position: fixed;
            right: 5%;
            top: 10%;
            z-index: 999;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .modal-text {
            z-index: 1001;
            padding-right: 20px;
            width: 100%;
        }
        .show {
            opacity: 1;
        }
        .hide {
            opacity: 0;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-200 to-yellow-100 min-h-screen font-poppins flex flex-col items-center justify-center">
    <header class="w-full p-4 flex justify-between items-center fixed top-0 shadow-md z-10">
        <div class="text-2xl font-bold text-blue-800">Pintar Ceria</div>
        <nav class="flex-1 flex justify-end space-x-4 pr-4">
            <a href="#" id="bantuan-btn" class="text-gray-600 hover:text-blue-800">Bantuan</a>
            <a href="#" id="tentang-kami-btn" class="text-gray-600 hover:text-blue-800">Tentang Kami</a>
        </nav>
        <div class="space-x-2">
            <a href="login.php" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">Login</a>
            <a href="register.php" class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600">Register</a>
        </div>
    </header>
    <main class="flex-grow flex items-center justify-center p-6 pt-24">
        <div class="flex w-full max-w-5xl items-center">
            <div class="w-1/2 text-left pr-8">
                <h2 class="text-xl text-gray-600 mb-4">Selamat Datang di Pintar Ceria</h2>
                <h1 class="text-5xl font-bold text-blue-800 mb-4">Yuk bermain sambil belajar!</h1>
                <p class="text-gray-600 mb-6">Isi nama dan umurmu, lalu mulai petualangan seru dengan kuis dan game! 🌟</p>
            </div>
            <div class="w-1/2 flex justify-end">
                <img src="uploads/logo.png" alt="Logo Pintar Ceria" class="w-80 h-80">
            </div>
        </div>
    </main>

    <!-- Modal Bantuan -->
    <div id="bantuan-modal" class="modal">
        <div class="modal-content">
            <div class="modal-text">
                <h2 class="text-4xl font-bold text-blue-500 mb-4">Butuh bantuan? Tenang aja, kami bantuin!</h2>
                <p class="font-bold text-gray-500 mb-2">✨ Gimana cara main di Pintar Ceria?</p>
                <ul class="text-gray-500 list-disc pl-5 mb-4">
                    <li>Daftar dulu, atau langsung login kalau udah punya akun.</li>
                    <li>Masuk dan lengkapi profil kamu.</li>
                    <li>Pilih menu Kuis, dan pilih mau belajar apa.</li>
                    <li>Selesaikan kuisnya, dan dapatkan skor!</li>
                    <li>Cek skor kamu di menu Papan Skor.</li>
                </ul>
                <p class="font-bold text-gray-500 mb-2">❓ Gagal Login?</p>
                <p class="text-gray-500">Coba cek lagi username dan password-nya, ya. Kalau masih gak bisa, kamu bisa minta bantuan lewat email kami. 📩 <a href="mailto:rahajengg29@gmail.com" class="text-blue-500">rahajengg29@gmail.com</a></p>
            </div>
        </div>
        <div class="modal-character" id="bantuan-character">
            <img src="uploads/character.png" alt="Character Pintar Ceria" class="w-180 h-180">
        </div>
    </div>

    <!-- Modal Tentang Kami -->
    <div id="tentang-kami-modal" class="modal">
        <div class="modal-content">
            <div class="modal-text">
                <h2 class="text-4xl font-bold text-blue-500 mb-4">Hai, selamat datang di Pintar Ceria!</h2>
                <p class="text-gray-500 mb-2">Pintar Ceria adalah tempat belajar yang seru buat kamu yang masih duduk di bangku SD. Di sini, kamu bisa mengerjakan kuis pelajaran dan main game sambil belajar. Pokoknya, belajar jadi gak ngebosenin!</p>
                <p class="font-bold text-gray-500 mb-2">Apa saja yang bisa kamu lakukan disini?</p>
                <ul class="text-gray-500 list-disc pl-5 mb-4">
                    <li>Pilih pelajaran seperti Bahasa, Matematika, atau Umum 📚</li>
                    <li>Mainkan kuis yang asik dan penuh warna 🎮</li>
                    <li>Lihat skor kamu dan coba kalahin skor sebelumnya! 🏆</li>
                    <li>Bisa isi dan edit profil kamu juga, lho! 🙋</li>
                </ul>
                <p class="text-gray-500">Kami ingin kamu bisa belajar dengan senyum dan menikmati setiap soal seperti lagi main game.</p>
            </div>
        </div>
        <div class="modal-character" id="tentang-kami-character">
            <img src="Uploads/character.png" alt="Character Pintar Ceria" class="w-180 h-180">
        </div>
    </div>

    <script>
        const showModal = (modalId, characterId) => {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('.modal-content');
            const character = document.getElementById(characterId);
            if (modal && content && character) {
                modal.style.display = 'block';
                character.style.display = 'block';
                setTimeout(() => {
                    modal.classList.add('show');
                    content.classList.add('show');
                    character.classList.add('show');
                }, 10);
            }
        };

        const hideModal = (modalId, characterId) => {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('.modal-content');
            const character = document.getElementById(characterId);
            if (modal && content && character) {
                modal.classList.add('hide');
                content.classList.add('hide');
                character.classList.add('hide');
                setTimeout(() => {
                    modal.style.display = 'none';
                    character.style.display = 'none';
                    modal.classList.remove('hide', 'show');
                    content.classList.remove('hide', 'show');
                    character.classList.remove('hide', 'show');
                }, 500);
            }
        };

        document.getElementById('bantuan-btn').addEventListener('click', () => showModal('bantuan-modal', 'bantuan-character'));
        document.getElementById('tentang-kami-btn').addEventListener('click', () => showModal('tentang-kami-modal', 'tentang-kami-character'));

        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('bantuan-modal')) {
                hideModal('bantuan-modal', 'bantuan-character');
            }
            if (event.target === document.getElementById('tentang-kami-modal')) {
                hideModal('tentang-kami-modal', 'tentang-kami-character');
            }
        });
    </script>
</body>
</html>