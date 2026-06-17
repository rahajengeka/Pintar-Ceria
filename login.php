<?php
session_start();
require_once 'db_connect.php';

$showSuccess = false;
if (isset($_SESSION['registration_success'])) {
    $showSuccess = true;
    unset($_SESSION['registration_success']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM User WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: " . ($_SESSION['is_admin'] ? "admin-dashboard.php" : "main-menu.php"));
        exit;
    } else {
        $loginError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
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
        .login-container {
            position: relative;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
            margin-left: 10%;
        }
        @media (max-width: 640px) {
            .login-container {
                max-width: 80%;
                padding: 1rem;
                margin-left: 5%;
            }
        }
        @media (min-width: 1024px) {
            .login-container {
                max-width: 400px;
                margin-left: 15%;
            }
        }
        .login-container h1 {
            font-size: 2rem;
        }
        .login-container input {
            padding: 0.5rem 1rem;
        }
        .login-container button {
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <img src="Uploads/background.svg" alt="Background SVG" class="svg-background">
    <div class="login-container">
        <h1 class="text-3xl font-bold text-blue-800 mb-6">Login</h1>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-full hover:bg-blue-600 transition duration-300">Login</button>
            </form>
            <a href="index.php" class="mt-4 inline-block text-blue-500 hover:underline">Kembali ke Beranda</a>
        </div>
    </div>
    <?php if ($showSuccess): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pendaftaran berhasil! Silakan login.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
    <?php if (isset($loginError) && $loginError): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: 'Login Gagal',
                text: 'Username atau password salah!',
                icon: 'error',
                confirmButtonText: 'Coba Lagi'
            });
        </script>
    <?php endif; ?>
</body>
</html>