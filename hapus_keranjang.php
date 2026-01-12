<?php
session_start();

// Menghapus hanya isi keranjang
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Kembali ke halaman kasir
header("Location: index.php");
exit;
?>