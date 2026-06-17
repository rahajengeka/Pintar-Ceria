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

if (isset($_SESSION['score'], $_SESSION['kuis_started']) && $_SESSION['kuis_started'] == $kuis_id) {
    $score = $_SESSION['score'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO SkorKuis (user_id, kuis_id, skor, kuis_date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $kuis_id, $score]);

    unset($_SESSION['score'], $_SESSION['current_soal_index'], $_SESSION['kuis_started']);
} else {
    header("Location: kuis-list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$nama = $user['nama'] ?? 'Pengguna';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM SoalKuis WHERE kuis_id = ?");
$stmt->execute([$kuis_id]);
$total_soal = $stmt->fetchColumn();

$score_display = $score ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Hasil Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        header {
            height: 60px;
            padding: 0 1rem;
        }
        .home-button img {
            height: 40px;
            width: 40px;
            object-fit: contain;
        }
        .score-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .score-value {
            font-size: 4rem;
            font-weight: bold;
            color: #ff6347;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.5rem;
        }
        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        main {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="min-h-screen font-poppins flex flex-col items-center">
    <video autoplay muted loop>
        <source src="Uploads/bg1.mp4" type="video/mp4">
        Maaf, browser kamu tidak mendukung video ini.
    </video>
    <main class="pt-24 p-6 text-center">
        <h1 class="text-3xl font-bold text-blue-800 mb-4">Hasil Kuis</h1>
        <div class="score-container">
            <p class="score-label">Skor kamu:</p>
            <p class="score-value"><?php echo $score_display; ?></p>
        </div>
        <div class="flex justify-center space-x-4 mt-4">
            <a href="papan-skor.php" class="bg-yellow-500 text-white px-6 py-3 rounded-full hover:bg-yellow-600 transition duration-300">Lihat Papan Skor</a>
            <a href="kuis-list.php" class="bg-green-500 text-white px-6 py-3 rounded-full hover:bg-green-600 transition duration-300">Kerjakan Lagi</a>
        </div>
        <a href="main-menu.php" class="mt-4 inline-block text-blue-500 hover:underline">Kembali ke Menu Utama</a>
    </main>
</body>
</html>