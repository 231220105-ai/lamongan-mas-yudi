<?php
$host = "localhost:4306"; // Menambahkan port 1306 sesuai phpMyAdmin kamu
$user = "root";
$pass = ""; 
$db   = "kasir_umkm";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>