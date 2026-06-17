<?php
session_start();
session_destroy();
echo "<script>alert('Logout berhasil, Sampai jumpa!'); window.location='index.php';</script>";
exit;
?>