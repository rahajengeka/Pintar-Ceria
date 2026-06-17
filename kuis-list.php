<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT kuis_id, nama_kuis FROM Kuis");
$kuis = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Daftar Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        video {
            pointer-events: none;
        }
    </style>
</head>
<body class="min-h-screen font-poppins flex flex-col items-center relative overflow-hidden">
    <video autoplay muted loop class="absolute top-0 left-0 w-full h-full object-cover -z-10">
        <source src="Uploads/bg.mp4" type="video/mp4">
        Browser Anda tidak mendukung video HTML5.
    </video>

    <?php require_once 'navbar.php'; ?>

    <main class="p-6 relative z-10 w-full flex flex-col justify-center min-h-screen pt-16">
        <h1 class="text-2xl font-bold text-blue-800 mb-6 text-center drop-shadow-lg">Petualangan dimulai! Pilih mata pelajaran favoritmu dan raih skor tertinggi!</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto text-center">
            <?php foreach ($kuis as $k): 
                $namaKuis = strtolower($k['nama_kuis']);
                if (strpos($namaKuis, 'matematika') !== false) {
                    $icon = 'mtk.png';
                } elseif (strpos($namaKuis, 'bahasa') !== false) {
                    $icon = 'bahasa.png';
                } elseif (strpos($namaKuis, 'umum') !== false) {
                    $icon = 'umum.png';
                } else {
                    $icon = 'default.png';
                }
            ?>
                <a href="kuis-start.php?kuis_id=<?php echo $k['kuis_id']; ?>" class="flex flex-col items-center bg-white bg-opacity-80 rounded-2xl shadow-lg p-4 hover:scale-105 transition duration-300">
                    <img src="Uploads/<?php echo $icon; ?>" alt="Ikon <?php echo htmlspecialchars($k['nama_kuis']); ?>" class="w-28 h-28 object-contain mb-2">
                    <span class="text-lg font-semibold text-blue-800"><?php echo htmlspecialchars($k['nama_kuis']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>