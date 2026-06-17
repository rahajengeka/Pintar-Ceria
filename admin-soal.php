<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Ceria - Kelola Soal</title>
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
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .lesson-button {
            display: inline-block;
            width: 250px;
            padding: 1.5rem;
            margin: 1rem;
            background-color: #3498db;
            color: white;
            border-radius: 15px;
            text-decoration: none;
            font-size: 1.3rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .lesson-button:hover {
            background-color: #2980b9;
            transform: translateY(-5px);
        }
        .back-button {
            display: inline-block;
            width: 150px;
            padding: 0.75rem;
            margin-top: 3rem;
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
        <h1 class="text-5xl text-gray-800 mb-10">Kelola Soal</h1>
        <div class="flex flex-wrap justify-center">
            <a href="admin-soal-bahasa.php" class="lesson-button">Bahasa</a>
            <a href="admin-soal-matematika.php" class="lesson-button">Matematika</a>
            <a href="admin-soal-umum.php" class="lesson-button">Umum</a>
        </div>
        <a href="admin-dashboard.php" class="back-button">Kembali</a>
    </main>
</body>
</html>