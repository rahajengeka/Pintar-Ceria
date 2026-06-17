<?php
session_start();

// Hapus sesi kuis jika pengguna keluar sebelum selesai
unset($_SESSION['score']);
unset($_SESSION['current_soal_index']);
unset($_SESSION['kuis_started']);
unset($_SESSION['soal_ids']);

header("Location: main-menu.php");
exit;
