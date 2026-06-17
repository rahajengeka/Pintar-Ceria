<?php
session_start();
require_once 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$nama = $user['nama'] ?? 'Pengguna';

$deleteStmt = $pdo->prepare("DELETE FROM SkorKuis WHERE kuis_date < DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$deleteStmt->execute();

$stmt = $pdo->prepare("
    SELECT s.skor, s.kuis_date, k.nama_kuis
    FROM SkorKuis s
    JOIN Kuis k ON s.kuis_id = k.kuis_id
    WHERE s.user_id = ?
    AND s.kuis_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ORDER BY s.kuis_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$skor = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Papan Skor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e0f7fa, #fff3e0);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            z-index: -1;
            opacity: 0.5;
        }
        main {
            padding-top: 80px;
            padding-bottom: 20px;
            width: 100%;
            max-width: 720px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .score-card {
            width: 100%;
            max-width: 680px;
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .score-card:hover {
            transform: translateY(-5px);
        }
        .category-title {
            color: #2980b9;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }
        .score-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #ecf0f1;
            color: #34495e;
        }
        .score-item:last-child {
            border-bottom: none;
        }
        .no-score {
            color: #7f8c8d;
            font-style: italic;
            padding: 1rem 0;
        }
        .back-button {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background-color: #3498db;
            color: white;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="bg-video">
        <source src="Uploads/bg.mp4" type="video/mp4">
        Browser kamu tidak mendukung video HTML5.
    </video>
    <main>
        <h1>Papan Skor</h1>
        <p class="text-center text-gray-600 mb-4">Menampilkan skor 7 hari terakhir</p>

        <div class="score-card">
            <div class="category-title">Bahasa</div>
            <ul>
                <?php
                $bahasa_skor = array_filter($skor, fn($s) => $s['nama_kuis'] === 'Bahasa');
                foreach ($bahasa_skor as $s):
                    $date = date('Y-m-d', strtotime($s['kuis_date']));
                    $today = date('Y-m-d');
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    $keterangan = ($date === $today) ? 'Hari ini' : (($date === $yesterday) ? 'Kemarin' : $date);
                ?>
                    <li class="score-item">
                        <span><?= is_numeric($s['skor']) ? $s['skor'] : 0; ?></span>
                        <span><?= $keterangan; ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($bahasa_skor)): ?>
                    <li class="no-score">Belum ada skor untuk Bahasa.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="score-card">
            <div class="category-title">Matematika</div>
            <ul>
                <?php
                $matematika_skor = array_filter($skor, fn($s) => $s['nama_kuis'] === 'Matematika');
                foreach ($matematika_skor as $s):
                    $date = date('Y-m-d', strtotime($s['kuis_date']));
                    $today = date('Y-m-d');
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    $keterangan = ($date === $today) ? 'Hari ini' : (($date === $yesterday) ? 'Kemarin' : $date);
                ?>
                    <li class="score-item">
                        <span><?= is_numeric($s['skor']) ? $s['skor'] : 0; ?></span>
                        <span><?= $keterangan; ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($matematika_skor)): ?>
                    <li class="no-score">Belum ada skor untuk Matematika.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="score-card">
            <div class="category-title">Umum</div>
            <ul>
                <?php
                $umum_skor = array_filter($skor, fn($s) => $s['nama_kuis'] === 'Umum');
                foreach ($umum_skor as $s):
                    $date = date('Y-m-d', strtotime($s['kuis_date']));
                    $today = date('Y-m-d');
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    $keterangan = ($date === $today) ? 'Hari ini' : (($date === $yesterday) ? 'Kemarin' : $date);
                ?>
                    <li class="score-item">
                        <span><?= is_numeric($s['skor']) ? $s['skor'] : 0; ?></span>
                        <span><?= $keterangan; ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($umum_skor)): ?>
                    <li class="no-score">Belum ada skor untuk Umum.</li>
                <?php endif; ?>
            </ul>
        </div>

        <a href="main-menu.php" class="back-button">Kembali</a>
    </main>
</body>
</html>