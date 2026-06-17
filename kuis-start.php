<?php
session_start();
require_once 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$kuis_id = $_GET['kuis_id'] ?? null;
if (!$kuis_id) {
    header("Location: kuis-list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$nama = $user['nama'] ?? 'Pengguna';

$stmt = $pdo->prepare("SELECT nama_kuis FROM Kuis WHERE kuis_id = ?");
$stmt->execute([$kuis_id]);
$kuis_nama = $stmt->fetch()['nama_kuis'] ?? 'Kuis';

$introText = "Sudah siap untuk mengerjakan kuis ini?";
if ($kuis_id == 1) {
    $introText = "Siap menjawab soal Bahasa Indonesia? Yuk uji kemampuan berbahasamu!";
} elseif ($kuis_id == 2) {
    $introText = "Ayo uji logika dan hitunganmu di Kuis Matematika!";
} elseif ($kuis_id == 3) {
    $introText = "Mari jawab soal-soal umum yang seru dan menambah pengetahuan!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start'])) {
    $_SESSION['kuis_started'] = $kuis_id;
    header("Location: kuis-play.php?kuis_id=$kuis_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Mulai Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: #f0f4f8;
        }
        .background-svg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
            opacity: 0.9;
            animation: pulse 6s infinite ease-in-out;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(1); opacity: 0.9; }
        }
        .fade-in {
            animation: fadeIn 1s ease-in forwards;
            opacity: 0;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <img src="Uploads/background3.svg" alt="Background" class="background-svg">
    <main class="fade-in text-center p-6 bg-white/80 backdrop-blur-md rounded-2xl shadow-lg max-w-md w-full mx-4">
        <h1 class="text-3xl font-bold text-blue-800 mb-4"><?php echo htmlspecialchars($kuis_nama); ?></h1>
        <p class="text-gray-700 text-base sm:text-lg mb-6"><?php echo $introText; ?></p>
        <form method="POST" class="space-x-4">
            <a href="kuis-list.php" class="bg-gray-500 text-white px-6 py-3 rounded-full hover:bg-gray-600 transition duration-300">Kembali</a>
            <button type="submit" name="start" class="bg-green-500 text-white px-6 py-3 rounded-full hover:bg-green-600 transition duration-300">Mulai</button>
        </form>
    </main>
</body>
</html>