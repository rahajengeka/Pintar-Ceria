<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// Proses hapus pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM User WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header("Location: admin-pengguna.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Daftar Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
        .user-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .no-content {
            color: #7f8c8d;
            font-style: italic;
            padding: 1rem 0;
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
    <header class="w-full p-4 flex justify-between items-center fixed top-0 bg-white shadow-md z-10">
        <div class="text-2xl font-bold text-blue-800">Pintar Ceria</div>
        <nav>
            <a href="logout.php" class="text-blue-500 hover:underline">Keluar</a>
        </nav>
    </header>
    <main>
        <div class="section-card">
            <div class="section-title">Daftar Pengguna</div>
            <ul>
                <?php
                $stmt = $pdo->prepare("SELECT user_id, last_active FROM User WHERE is_admin = 0");
                $stmt->execute();
                $users = $stmt->fetchAll();
                foreach ($users as $u): ?>
                    <li class="user-item">
                        <span>ID: <?php echo htmlspecialchars($u['user_id']); ?></span>
                        <span>Terakhir Aktif: <?php echo $u['last_active'] ? date('d-m-Y H:i', strtotime($u['last_active'])) : 'Belum ada aktivitas'; ?></span>
                        <form method="POST" onsubmit="return confirm('Yakin hapus pengguna ini?')" class="inline">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <button type="submit" name="delete_user" class="text-red-500">Hapus</button>
                        </form>
                    </li>
                <?php endforeach;
                if (empty($users)) echo "<li class='no-content'>Belum ada pengguna non-admin.</li>";
                ?>
            </ul>
        </div>
        <a href="admin-dashboard.php" class="back-button">Kembali</a>
    </main>
</body>
</html>