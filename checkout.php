<?php
include 'koneksi.php';
session_start();

if (isset($_POST['total']) && $_POST['total'] > 0) {
    $total_harga = $_POST['total'];
    $uang_bayar = $_POST['bayar']; 
    $tanggal = date('Y-m-d H:i:s');

    // 1. Simpan ke tabel transactions
    $query_transaksi = "INSERT INTO transactions (total, created_at) VALUES ('$total_harga', '$tanggal')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        $transaction_id = mysqli_insert_id($conn);

        // 2. Simpan setiap item ke transaction_items
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $qty = $item['qty'];
            $harga = $item['harga'];
            $subtotal = $qty * $harga;

            $query_item = "INSERT INTO transaction_items (transaction_id, product_id, qty, subtotal) 
                           VALUES ('$transaction_id', '$product_id', '$qty', '$subtotal')";
            mysqli_query($conn, $query_item);

            // 3. Potong stok otomatis
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = '$product_id'");
        }

        // Bersihkan keranjang
        unset($_SESSION['cart']);
        
        // REDIRECT ke halaman konfirmasi sukses
        header("Location: transaksi_selesai.php?id=$transaction_id&bayar=$uang_bayar");
        exit;
    }
}
?>