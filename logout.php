<?php
session_start(); // Memulai sesi yang ada

// Menghapus semua data sesi
$_SESSION = array();

// Menghancurkan sesi sepenuhnya
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.php");
exit();
?>