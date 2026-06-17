<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO User (nama, umur, username, password, is_admin) VALUES (?, ?, ?, ?, FALSE)");
        $stmt->execute([$nama, $umur, $username, $password]);
        $_SESSION['registration_success'] = true;
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['registration_error'] = "Username sudah digunakan atau terjadi kesalahan.";
        header("Location: register.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: block;
            overflow-y: auto;
            position: relative;
        }
        .svg-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
            opacity: 0.8;
            animation: pulse 5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }
        .register-container {
            position: relative;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
            margin: 5rem auto;
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 640px) {
            .register-container {
                max-width: 80%;
                padding: 1rem;
                margin: 4rem auto;
            }
        }
        @media (min-width: 1024px) {
            .register-container {
                max-width: 400px;
                margin: 6rem auto;
            }
        }
        .register-container h1 {
            font-size: 2rem;
        }
        .register-container input {
            padding: 0.5rem 1rem;
        }
        .register-container button {
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <img src="Uploads/background2.svg" alt="Background SVG" class="svg-background">
    <div class="register-container">
        <h1 class="text-3xl font-bold text-blue-800 mb-6">Register</h1>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <form method="POST">
                <div class="mb-4">
                    <label for="nama" class="block text-gray-700 mb-2">Nama</label>
                    <input type="text" id="nama" name="nama" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="umur" class="block text-gray-700 mb-2">Umur</label>
                    <input type="number" id="umur" name="umur" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-full hover:bg-green-600 transition duration-300">Register</button>
            </form>
            <a href="index.php" class="mt-4 inline-block text-blue-500 hover:underline">Kembali ke Beranda</a>
        </div>
    </div>
    <?php if (isset($_SESSION['registration_error'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: '<?= $_SESSION['registration_error']; ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['registration_error']); ?>
    <?php endif; ?>
</body>
</html>