<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// Ambil semua soal untuk Umum
$stmt = $pdo->prepare("SELECT s.soal_id, s.teks_soal, s.option_a, s.option_b, s.option_c, s.correct_answer, s.gambar_path FROM SoalKuis s JOIN Kuis k ON s.kuis_id = k.kuis_id WHERE k.nama_kuis = 'Umum'");
$stmt->execute();
$soal_umum = $stmt->fetchAll();

// Proses tambah soal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_soal'])) {
    $kuis_id = 3; // Kuis_id untuk Umum (sesuaikan dengan ID di tabel Kuis)
    $teks_soal = trim($_POST['teks_soal']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $correct_answer = strtoupper(trim($_POST['correct_answer']));

    if (empty($teks_soal)) {
        die("Teks soal wajib diisi.");
    }

    if (
        (empty($option_a) && (!isset($_FILES['gambar_a']) || $_FILES['gambar_a']['error'] != 0)) ||
        (empty($option_b) && (!isset($_FILES['gambar_b']) || $_FILES['gambar_b']['error'] != 0)) ||
        (empty($option_c) && (!isset($_FILES['gambar_c']) || $_FILES['gambar_c']['error'] != 0))
    ) {
        die("Minimal isi teks atau gambar untuk setiap opsi A, B, dan C.");
    }

    if (!in_array($correct_answer, ['A', 'B', 'C'])) {
        die("Jawaban harus A, B, atau C.");
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        die("Gagal membuat direktori uploads. Periksa izin folder.");
    }
    $gambar_path = '';
    $gambar_a = '';
    $gambar_b = '';
    $gambar_c = '';

    if (isset($_FILES['gambar_path']) && $_FILES['gambar_path']['error'] == 0) {
        $gambar_path = $upload_dir . basename($_FILES['gambar_path']['name']);
        if (!move_uploaded_file($_FILES['gambar_path']['tmp_name'], $gambar_path)) {
            die("Gagal mengunggah gambar soal. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_a']) && $_FILES['gambar_a']['error'] == 0) {
        $gambar_a = $upload_dir . basename($_FILES['gambar_a']['name']);
        if (!move_uploaded_file($_FILES['gambar_a']['tmp_name'], $gambar_a)) {
            die("Gagal mengunggah gambar opsi A. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_b']) && $_FILES['gambar_b']['error'] == 0) {
        $gambar_b = $upload_dir . basename($_FILES['gambar_b']['name']);
        if (!move_uploaded_file($_FILES['gambar_b']['tmp_name'], $gambar_b)) {
            die("Gagal mengunggah gambar opsi B. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_c']) && $_FILES['gambar_c']['error'] == 0) {
        $gambar_c = $upload_dir . basename($_FILES['gambar_c']['name']);
        if (!move_uploaded_file($_FILES['gambar_c']['tmp_name'], $gambar_c)) {
            die("Gagal mengunggah gambar opsi C. Periksa izin folder.");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO SoalKuis (kuis_id, teks_soal, option_a, option_b, option_c, correct_answer, gambar_path, gambar_a, gambar_b, gambar_c) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$kuis_id, $teks_soal, $option_a, $option_b, $option_c, $correct_answer, $gambar_path, $gambar_a, $gambar_b, $gambar_c]);
    header("Location: admin-soal-umum.php");
    exit;
}

// Proses edit soal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_soal'])) {
    $soal_id = $_POST['soal_id'];
    $kuis_id = 3; // Kuis_id untuk Umum (sesuaikan dengan ID di tabel Kuis)
    $teks_soal = trim($_POST['teks_soal']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $correct_answer = strtoupper(trim($_POST['correct_answer']));

    if (empty($teks_soal)) {
        die("Teks soal wajib diisi.");
    }

    if (
        (empty($option_a) && empty($_POST['current_gambar_a']) && (empty($_FILES['gambar_a']['name']) || $_FILES['gambar_a']['error'] != 0)) ||
        (empty($option_b) && empty($_POST['current_gambar_b']) && (empty($_FILES['gambar_b']['name']) || $_FILES['gambar_b']['error'] != 0)) ||
        (empty($option_c) && empty($_POST['current_gambar_c']) && (empty($_FILES['gambar_c']['name']) || $_FILES['gambar_c']['error'] != 0))
    ) {
        die("Opsi A, B, atau C wajib memiliki teks atau gambar.");
    }

    if (!in_array($correct_answer, ['A', 'B', 'C'])) {
        die("Jawaban harus A, B, atau C.");
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        die("Gagal membuat direktori uploads. Periksa izin folder.");
    }
    $gambar_path = $_POST['current_gambar_path'];
    $gambar_a = $_POST['current_gambar_a'];
    $gambar_b = $_POST['current_gambar_b'];
    $gambar_c = $_POST['current_gambar_c'];

    if (isset($_FILES['gambar_path']) && $_FILES['gambar_path']['error'] == 0) {
        $gambar_path = $upload_dir . basename($_FILES['gambar_path']['name']);
        if (!move_uploaded_file($_FILES['gambar_path']['tmp_name'], $gambar_path)) {
            die("Gagal mengunggah gambar soal. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_a']) && $_FILES['gambar_a']['error'] == 0) {
        $gambar_a = $upload_dir . basename($_FILES['gambar_a']['name']);
        if (!move_uploaded_file($_FILES['gambar_a']['tmp_name'], $gambar_a)) {
            die("Gagal mengunggah gambar opsi A. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_b']) && $_FILES['gambar_b']['error'] == 0) {
        $gambar_b = $upload_dir . basename($_FILES['gambar_b']['name']);
        if (!move_uploaded_file($_FILES['gambar_b']['tmp_name'], $gambar_b)) {
            die("Gagal mengunggah gambar opsi B. Periksa izin folder.");
        }
    }
    if (isset($_FILES['gambar_c']) && $_FILES['gambar_c']['error'] == 0) {
        $gambar_c = $upload_dir . basename($_FILES['gambar_c']['name']);
        if (!move_uploaded_file($_FILES['gambar_c']['tmp_name'], $gambar_c)) {
            die("Gagal mengunggah gambar opsi C. Periksa izin folder.");
        }
    }

    $stmt = $pdo->prepare("UPDATE SoalKuis SET kuis_id = ?, teks_soal = ?, option_a = ?, option_b = ?, option_c = ?, correct_answer = ?, gambar_path = ?, gambar_a = ?, gambar_b = ?, gambar_c = ? WHERE soal_id = ?");
    $stmt->execute([$kuis_id, $teks_soal, $option_a, $option_b, $option_c, $correct_answer, $gambar_path, $gambar_a, $gambar_b, $gambar_c, $soal_id]);
    header("Location: admin-soal-umum.php");
    exit;
}

// Proses hapus soal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_soal'])) {
    $soal_id = $_POST['soal_id'];
    $stmt = $pdo->prepare("DELETE FROM SoalKuis WHERE soal_id = ?");
    $stmt->execute([$soal_id]);
    header("Location: admin-soal-umum.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Kelola Soal Umum</title>
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
        header {
            height: 60px;
            padding: 0 1rem;
            background: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        main {
            padding-top: 80px;
            padding-bottom: 20px;
            width: 100%;
            max-width: 900px;
        }
        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .section-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .section-title {
            color: #2980b9;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }
        .soal-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .soal-item:last-child {
            border-bottom: none;
        }
        .no-content {
            color: #7f8c8d;
            font-style: italic;
            padding: 1rem 0;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.25rem;
            color: #34495e;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group button {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #2980b9;
        }
        .action-links a {
            margin-left: 1rem;
            color: #e74c3c;
            text-decoration: none;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .back-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 150px;
            padding: 0.75rem;
            margin-top: 1rem;
            background-color: #95a5a6;
            color: white;
            border-radius: 15px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .back-button:hover {
            background-color: #7f8c8d;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <?php include 'admin-header.php'; ?>
    <main>
        <!-- Daftar Soal Umum -->
        <div class="section-card">
            <div class="section-title">Daftar Soal Umum</div>
            <ul>
                <?php foreach ($soal_umum as $s): ?>
                    <li class="soal-item">
                        <span><?php echo htmlspecialchars(substr($s['teks_soal'], 0, 50)) . (strlen($s['teks_soal']) > 50 ? '...' : ''); ?></span>
                        <?php if ($s['gambar_path'] && file_exists($s['gambar_path'])): ?>
                            <img src="<?php echo htmlspecialchars($s['gambar_path']); ?>" alt="Gambar Soal" class="w-16 h-16">
                        <?php endif; ?>
                        <div class="action-links">
                            <a href="?edit=<?php echo $s['soal_id']; ?>">Edit</a>
                            <form method="POST" onsubmit="return confirm('Yakin hapus soal ini?')" class="inline">
                                <input type="hidden" name="soal_id" value="<?php echo $s['soal_id']; ?>">
                                <button type="submit" name="delete_soal" class="text-red-500">Hapus</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach;
                if (empty($soal_umum)) echo "<li class='no-content'>Belum ada soal untuk Umum.</li>";
                ?>
            </ul>
        </div>

        <!-- Form Tambah Soal dengan Upload Foto -->
        <div class="section-card">
            <div class="section-title">Tambah Soal</div>
            <form method="POST" class="space-y-4 max-w-md mx-auto" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="teks_soal">Teks Soal</label>
                    <textarea name="teks_soal" id="teks_soal" class="w-full p-2 border rounded" placeholder="Teks Soal" required></textarea>
                </div>
                <div class="form-group">
                    <label for="gambar_path">Gambar Soal</label>
                    <input type="file" name="gambar_path" id="gambar_path" class="w-full p-2 border rounded" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="option_a">Opsi A</label>
                    <input type="text" name="option_a" id="option_a" class="w-full p-2 border rounded" placeholder="Opsi A">
                </div>
                <div class="form-group">
                    <label for="gambar_a">Gambar Opsi A</label>
                    <input type="file" name="gambar_a" id="gambar_a" class="w-full p-2 border rounded" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="option_b">Opsi B</label>
                    <input type="text" name="option_b" id="option_b" class="w-full p-2 border rounded" placeholder="Opsi B">
                </div>
                <div class="form-group">
                    <label for="gambar_b">Gambar Opsi B</label>
                    <input type="file" name="gambar_b" id="gambar_b" class="w-full p-2 border rounded" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="option_c">Opsi C</label>
                    <input type="text" name="option_c" id="option_c" class="w-full p-2 border rounded" placeholder="Opsi C">
                </div>
                <div class="form-group">
                    <label for="gambar_c">Gambar Opsi C</label>
                    <input type="file" name="gambar_c" id="gambar_c" class="w-full p-2 border rounded" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="correct_answer">Jawaban Benar</label>
                    <select name="correct_answer" id="correct_answer" class="w-full p-2 border rounded" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_soal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Soal</button>
                </div>
            </form>
        </div>

        <!-- Form Edit Soal -->
        <?php
        $edit_soal_id = $_GET['edit'] ?? null;
        if ($edit_soal_id) {
            $stmt = $pdo->prepare("SELECT * FROM SoalKuis WHERE soal_id = ?");
            $stmt->execute([$edit_soal_id]);
            $soal = $stmt->fetch();
            if ($soal): ?>
                <div class="section-card">
                    <div class="section-title">Edit Soal</div>
                    <form method="POST" class="space-y-4 max-w-md mx-auto" enctype="multipart/form-data">
                        <input type="hidden" name="soal_id" value="<?php echo $soal['soal_id']; ?>">
                        <div class="form-group">
                            <label for="teks_soal">Teks Soal</label>
                            <textarea name="teks_soal" id="teks_soal" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($soal['teks_soal']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="gambar_path">Gambar Soal</label>
                            <input type="file" name="gambar_path" id="gambar_path" class="w-full p-2 border rounded" accept="image/*">
                            <input type="hidden" name="current_gambar_path" value="<?php echo htmlspecialchars($soal['gambar_path']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="option_a">Opsi A</label>
                            <input type="text" name="option_a" id="option_a" class="w-full p-2 border rounded" value="<?php echo htmlspecialchars($soal['option_a']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gambar_a">Gambar Opsi A</label>
                            <input type="file" name="gambar_a" id="gambar_a" class="w-full p-2 border rounded" accept="image/*">
                            <input type="hidden" name="current_gambar_a" value="<?php echo htmlspecialchars($soal['gambar_a']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="option_b">Opsi B</label>
                            <input type="text" name="option_b" id="option_b" class="w-full p-2 border rounded" value="<?php echo htmlspecialchars($soal['option_b']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gambar_b">Gambar Opsi B</label>
                            <input type="file" name="gambar_b" id="gambar_b" class="w-full p-2 border rounded" accept="image/*">
                            <input type="hidden" name="current_gambar_b" value="<?php echo htmlspecialchars($soal['gambar_b']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="option_c">Opsi C</label>
                            <input type="text" name="option_c" id="option_c" class="w-full p-2 border rounded" value="<?php echo htmlspecialchars($soal['option_c']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gambar_c">Gambar Opsi C</label>
                            <input type="file" name="gambar_c" id="gambar_c" class="w-full p-2 border rounded" accept="image/*">
                            <input type="hidden" name="current_gambar_c" value="<?php echo htmlspecialchars($soal['gambar_c']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="correct_answer">Jawaban Benar</label>
                            <select name="correct_answer" id="correct_answer" class="w-full p-2 border rounded" required>
                                <option value="A" <?php echo $soal['correct_answer'] == 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo $soal['correct_answer'] == 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="C" <?php echo $soal['correct_answer'] == 'C' ? 'selected' : ''; ?>>C</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="edit_soal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            <?php endif;
        }
        ?>
        <a href="admin-soal.php" class="back-button">Kembali</a>
    </main>

    <!-- SweetAlert Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['message'])): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['message_type']; ?>',
            title: '<?php echo $_SESSION['message']; ?>',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php unset($_SESSION['message']); endif; ?>

    <script>
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const optionA = form.querySelector('[name="option_a"]');
            const gambarA = form.querySelector('[name="gambar_a"]');
            const optionB = form.querySelector('[name="option_b"]');
            const gambarB = form.querySelector('[name="gambar_b"]');
            const optionC = form.querySelector('[name="option_c"]');
            const gambarC = form.querySelector('[name="gambar_c"]');

            if (!(optionA && gambarA && optionB && gambarB && optionC && gambarC)) return;

            let errorMsg = "";

            if (optionA.value.trim() === "" && gambarA.files.length === 0) {
                errorMsg = "Opsi A harus diisi dengan teks atau gambar.";
            } else if (optionB.value.trim() === "" && gambarB.files.length === 0) {
                errorMsg = "Opsi B harus diisi dengan teks atau gambar.";
            } else if (optionC.value.trim() === "" && gambarC.files.length === 0) {
                errorMsg = "Opsi C harus diisi dengan teks atau gambar.";
            }

            if (errorMsg !== "") {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: errorMsg
                });
            }
        });
    });
    </script>
</body>
</html>