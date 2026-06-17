<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT nama, umur, username, profile_picture FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Proses hapus foto profil jika ada permintaan
if (isset($_GET['action']) && $_GET['action'] === 'delete_photo' && $user['profile_picture']) {
    $old_photo = $user['profile_picture'];
    if (file_exists('Uploads/' . $old_photo)) {
        unlink('Uploads/' . $old_photo);
    }
    $stmt = $pdo->prepare("UPDATE User SET profile_picture = NULL WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    header("Location: edit-profil.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $username = $_POST['username'];
    $profile_picture = $user['profile_picture']; // Default ke foto lama

    // Cek apakah username sudah ada di database, kecuali untuk user saat ini
    if ($username !== $user['username']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM User WHERE username = ? AND user_id != ?");
        $stmt->execute([$username, $_SESSION['user_id']]);
        $username_exists = $stmt->fetchColumn();

        if ($username_exists > 0) {
            $error = "Username '$username' sudah digunakan. Pilih username lain.";
        }
    }

    if (!isset($error)) {
        // Handle upload foto profil
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_picture']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = uniqid() . '.' . $ext;
                $target = 'Uploads/' . $new_name;
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
                    // Hapus foto lama jika ada
                    if ($user['profile_picture'] && file_exists('Uploads/' . $user['profile_picture'])) {
                        unlink('Uploads/' . $user['profile_picture']);
                    }
                    $profile_picture = $new_name;
                } else {
                    error_log("Gagal memindahkan file ke $target. Periksa izin folder atau batas unggah.");
                }
            } else {
                error_log("Ekstensi file $filename tidak diizinkan.");
            }
        } else {
            error_log("Error unggah file: Kode " . (isset($_FILES['profile_picture']) ? $_FILES['profile_picture']['error'] : 'Tidak ada file diunggah'));
        }

        $stmt = $pdo->prepare("UPDATE User SET nama = ?, umur = ?, username = ?, profile_picture = ? WHERE user_id = ?");
        $stmt->execute([$nama, $umur, $username, $profile_picture, $_SESSION['user_id']]);

        $_SESSION['nama'] = $nama;
        header("Location: edit-profil.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Edit Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .profile-img, .profile-placeholder {
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .profile-img:hover, .profile-placeholder:hover {
            transform: scale(1.1);
        }
        .popup, .error-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            animation: popupFadeIn 0.3s ease-in-out;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .profile-container {
            position: relative;
            display: inline-block;
        }
        .hidden-input {
            display: none;
        }
        .form-container {
            animation: fadeIn 1s ease-in-out;
        }
        .form-label {
            animation: slideIn 0.5s ease-in-out;
        }
        .submit-button {
            transition: transform 0.2s ease;
        }
        .submit-button:hover {
            transform: scale(1.05);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes popupFadeIn {
            from { opacity: 0; scale: 0.9; }
            to { opacity: 1; scale: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-200 to-yellow-100 min-h-screen font-poppins flex flex-col items-center">
    <header class="w-full p-3 flex justify-between items-center fixed top-0 bg-white shadow-md z-10">
        <div class="flex items-center space-x-2 cursor-pointer" onclick="window.location='profil.php'">
            <?php if ($user['profile_picture']): ?>
                <img src="Uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil <?php echo htmlspecialchars($user['nama']); ?>" class="w-10 h-10 rounded-full profile-img">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">👤</div>
            <?php endif; ?>
            <span class="text-blue-800 font-semibold">Halo <?php echo htmlspecialchars($user['nama']); ?>!</span>
        </div>
    </header>
    <main class="pt-20 p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-6 text-center">Edit Profil</h1>
        <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md mx-auto flex flex-col items-center form-container">
            <form method="POST" enctype="multipart/form-data">
                <!-- Container Foto Profil dengan Pop-up -->
                <div class="mb-4 flex justify-center items-center profile-container">
                    <?php if ($user['profile_picture']): ?>
                        <img src="Uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil" class="w-24 h-24 rounded-full mx-auto profile-img cursor-pointer" onclick="showPopup('existing')">
                    <?php else: ?>
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-3xl cursor-pointer profile-placeholder" onclick="showPopup('new')">👤</div>
                    <?php endif; ?>
                    <!-- Hidden Input untuk Upload -->
                    <input type="file" id="profile_picture" name="profile_picture" class="hidden-input" onchange="this.form.submit()">
                    <!-- Pop-up untuk Foto Profil -->
                    <div id="popup" class="popup">
                        <div id="popup-content"></div>
                        <button type="button" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-full hover:bg-gray-600" onclick="hidePopup()">Batal</button>
                    </div>
                    <!-- Pop-up untuk Error Username -->
                    <div id="error-popup" class="error-popup">
                        <div id="error-popup-content" class="text-red-700 text-center"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></div>
                        <button type="button" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-full hover:bg-gray-600" onclick="hideErrorPopup()">Tutup</button>
                    </div>
                    <div id="overlay" class="overlay" onclick="hidePopup();hideErrorPopup();"></div>
                </div>
                <div class="mb-4">
                    <label for="nama" class="block text-gray-700 mb-2 font-bold form-label">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="umur" class="block text-gray-700 mb-2 font-bold form-label">Umur</label>
                    <input type="number" id="umur" name="umur" value="<?php echo $user['umur']; ?>" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 mb-2 font-bold form-label">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-full hover:bg-green-600 transition duration-300 submit-button">Simpan</button>
            </form>
            <a href="profil.php" class="mt-4 inline-block text-blue-500 hover:underline text-center">Kembali</a>
        </div>
    </main>
    <script>
        function showPopup(type) {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            const popupContent = document.getElementById('popup-content');
            popup.style.display = 'block';
            overlay.style.display = 'block';

            if (type === 'new') {
                popupContent.innerHTML = `
                    <h2 class="text-xl font-semibold mb-4">Unggah Foto Profil</h2>
                    <button type="button" class="w-full bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600" onclick="document.getElementById('profile_picture').click()">Unggah Foto</button>
                `;
            } else if (type === 'existing') {
                popupContent.innerHTML = `
                    <h2 class="text-xl font-semibold mb-4">Pilih Aksi</h2>
                    <button type="button" class="w-full bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 mb-2" onclick="document.getElementById('profile_picture').click()">Ganti Foto Profil</button>
                    <a href="edit-profil.php?action=delete_photo" class="w-full bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 inline-block text-center" onclick="return confirm('Yakin ingin menghapus foto profil ini?')">Hapus Foto Profil</a>
                `;
            }
        }

        function hidePopup() {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }

        function showErrorPopup() {
            const errorPopup = document.getElementById('error-popup');
            const overlay = document.getElementById('overlay');
            errorPopup.style.display = 'block';
            overlay.style.display = 'block';
        }

        function hideErrorPopup() {
            const errorPopup = document.getElementById('error-popup');
            const overlay = document.getElementById('overlay');
            errorPopup.style.display = 'none';
            overlay.style.display = 'none';
        }

        // Tampilkan pop-up error jika ada error
        <?php if (isset($error)): ?>
            showErrorPopup();
        <?php endif; ?>
    </script>
</body>
</html>