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

if (!isset($_SESSION['kuis_started']) || $_SESSION['kuis_started'] != $kuis_id) {
    unset($_SESSION['soal_ids'], $_SESSION['current_soal_index'], $_SESSION['score']);
    $_SESSION['kuis_started'] = $kuis_id;
}

$stmt = $pdo->prepare("SELECT nama FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$nama = $user['nama'] ?? 'Pengguna';

$stmt = $pdo->prepare("SELECT soal_id, teks_soal, option_a, option_b, option_c, correct_answer, gambar_path, gambar_a, gambar_b, gambar_c FROM SoalKuis WHERE kuis_id = ?");
$stmt->execute([$kuis_id]);
$soal_all = $stmt->fetchAll();

if (empty($soal_all)) {
    die("Tidak ada soal untuk kuis ini. Silakan tambahkan soal melalui panel admin.");
}

if (!isset($_SESSION['soal_ids']) || !isset($_SESSION['kuis_started']) || $_SESSION['kuis_started'] != $kuis_id) {
    $soal_ids = array_column($soal_all, 'soal_id');
    if (empty($soal_ids)) {
        die("Tidak ada soal_id yang valid untuk kuis_id: $kuis_id. Periksa data di database.");
    }
    shuffle($soal_ids);
    $_SESSION['soal_ids'] = $soal_ids;
    $_SESSION['kuis_started'] = $kuis_id;
} else {
    $current_soal_ids = array_column($soal_all, 'soal_id');
    $session_soal_ids = $_SESSION['soal_ids'];
    if (count(array_diff($session_soal_ids, $current_soal_ids)) > 0) {
        $soal_ids = array_intersect($session_soal_ids, $current_soal_ids);
        if (empty($soal_ids)) {
            $soal_ids = $current_soal_ids;
            shuffle($soal_ids);
        }
        $_SESSION['soal_ids'] = $soal_ids;
    } else {
        $soal_ids = $session_soal_ids;
    }
}

if (!isset($_SESSION['current_soal_index']) || $_SESSION['current_soal_index'] >= count($soal_ids)) {
    $_SESSION['current_soal_index'] = 0;
}
$current_index = $_SESSION['current_soal_index'];
$current_soal_id = $soal_ids[$current_index];

$soal = null;
foreach ($soal_all as $s) {
    if ($s['soal_id'] == $current_soal_id) {
        $soal = $s;
        break;
    }
}
if (!$soal) {
    unset($_SESSION['soal_ids'], $_SESSION['current_soal_index']);
    header("Location: kuis-play.php?kuis_id=$kuis_id");
    exit;
}

$next_index = ($current_index + 1 < count($soal_ids)) ? $current_index + 1 : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $selected_answer = $_POST['answer'];
    $correct_answer = $soal['correct_answer'];
    $score = $_SESSION['score'] ?? 0;
    if ($selected_answer == $correct_answer) $score += 10;
    $_SESSION['score'] = $score;

    if ($next_index !== null) {
        $_SESSION['current_soal_index'] = $next_index;
        header("Location: kuis-play.php?kuis_id=$kuis_id");
        exit;
    } else {
        $_SESSION['current_soal_index'] = $current_index;
    }
}

if (isset($_POST['finish'])) {
    header("Location: kuis-result.php?kuis_id=$kuis_id");
    exit;
}

if (isset($_SESSION['kuis_started']) && basename($_SERVER['PHP_SELF']) !== 'kuis-play.php') {
    unset($_SESSION['kuis_started'], $_SESSION['soal_ids'], $_SESSION['current_soal_index'], $_SESSION['score']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .answer-button {
            transition: background-color 0.3s;
        }
        body {
            background-image: url('Uploads/background4.svg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            animation: fadeIn 1s ease-out 1;
            overflow: auto;
            scrollbar-width: none;
        }
        body::-webkit-scrollbar {
            display: none;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
    <script>
        function markAnswer(result) {
            const buttons = document.querySelectorAll('.answer-button');
            buttons.forEach(button => {
                const value = button.value;
                if (result.isLast && value === result.selected) {
                    button.classList.add('incorrect');
                } else if (!result.isLast && value === result.correct) {
                    button.classList.add('correct');
                } else if (!result.isLast && value === result.selected) {
                    button.classList.add('incorrect');
                }
                button.disabled = true;
            });
        }

        function showFinalPopup() {
            const popup = document.createElement('div');
            popup.textContent = "Kuis selesai! Klik selesai untuk lihat skormu!";
            popup.style.position = 'fixed';
            popup.style.top = '20px';
            popup.style.left = '50%';
            popup.style.transform = 'translateX(-50%)';
            popup.style.backgroundColor = '#facc15';
            popup.style.color = '#000';
            popup.style.padding = '1rem 2rem';
            popup.style.borderRadius = '999px';
            popup.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            popup.style.zIndex = '1000';
            popup.style.fontWeight = '600';
            document.body.appendChild(popup);
            setTimeout(() => popup.remove(), 3000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])): ?>
                markAnswer({
                    correct: '<?php echo htmlspecialchars($soal['correct_answer']); ?>',
                    selected: '<?php echo htmlspecialchars($_POST['answer']); ?>',
                    isLast: <?php echo ($next_index === null) ? 'true' : 'false'; ?>
                });
                <?php if ($next_index === null): ?>
                    showFinalPopup();
                <?php endif; ?>
            <?php endif; ?>
        });
    </script>
</head>
<body class="min-h-screen font-poppins flex flex-col items-center">
    <main class="pt-20 p-6 text-center">
        <?php if ($soal['gambar_path'] && file_exists($soal['gambar_path'])): ?>
            <img src="<?php echo htmlspecialchars($soal['gambar_path']); ?>" alt="Gambar Soal" class="mx-auto mb-4 max-w-xs">
        <?php endif; ?>
        <div class="bg-white bg-opacity-50 p-6 rounded-lg shadow-md max-w-xl mx-auto">
            <p class="text-lg text-gray-700 font-bold mb-8 mt-6 max-w-xl mx-auto"><?php echo htmlspecialchars($soal['teks_soal'] ?? 'Soal tidak tersedia'); ?></p>
            <form method="POST" class="space-y-4 max-w-md mx-auto">
                <?php
                $options = [];
                if ($soal['option_a'] !== null) $options['A'] = ['text' => $soal['option_a'], 'image' => $soal['gambar_a']];
                if ($soal['option_b'] !== null) $options['B'] = ['text' => $soal['option_b'], 'image' => $soal['gambar_b']];
                if ($soal['option_c'] !== null) $options['C'] = ['text' => $soal['option_c'], 'image' => $soal['gambar_c']];

                if (empty($options)) {
                    echo "<p>Tidak ada opsi jawaban untuk soal ini.</p>";
                } else {
                    $keys = array_keys($options);
                    if ($next_index === null) {
                        // Soal terakhir, tidak diacak
                    } else {
                        shuffle($keys);
                    }
                    foreach ($keys as $key) {
                        $opt = $options[$key];
                        echo "<button type='submit' name='answer' value='$key' class='answer-button w-full bg-white text-blue-800 px-6 py-3 border rounded-lg hover:bg-blue-100 transition duration-300 flex items-center justify-center space-x-2'>";
                        if ($opt['image'] && file_exists($opt['image'])) {
                            echo "<img src='" . htmlspecialchars($opt['image']) . "' alt='Gambar Opsi' class='w-12 h-12'>";
                        }
                        echo htmlspecialchars($opt['text'] ?? 'Opsi tidak tersedia');
                        echo "</button>";
                    }
                }
                ?>
            </form>
        </div>
        <?php if ($next_index === null): ?>
            <form method="POST" class="mt-4">
                <button type="submit" name="finish" class="bg-yellow-500 text-white px-6 py-3 rounded-full hover:bg-yellow-600 transition duration-300">Selesai</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>