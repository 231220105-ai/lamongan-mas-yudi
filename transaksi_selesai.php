<?php
session_start();
include 'koneksi.php';

// 1. Ambil data dari URL dengan validasi agar tidak terjadi error "Undefined index"
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 2. PROTEKSI FATAL ERROR: Paksa menjadi float dan tangani jika kosong agar tidak "string - string"
$bayar_input = isset($_GET['bayar']) ? $_GET['bayar'] : 0;
$bayar = ($bayar_input === "" || !is_numeric($bayar_input)) ? 0 : (float)$bayar_input;

// 3. Ambil data transaksi dari database
$query_trx = mysqli_query($conn, "SELECT * FROM transactions WHERE id = '$id'");
$trx = mysqli_fetch_assoc($query_trx);

if (!$trx) {
    die("<div class='container mt-5 alert alert-danger text-center'>
            <h4>Transaksi Tidak Ditemukan!</h4>
            <p>Pastikan ID transaksi benar.</p>
            <a href='index.php' class='btn btn-primary'>Kembali ke Kasir</a>
         </div>");
}

// 4. Pastikan 'total' juga dikonversi ke angka sebelum dikurangi
$total_tagihan = (float)$trx['total'];
$kembalian = $bayar - $total_tagihan;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-purple: #764ba2;
            --hover-purple: #5a3a7d;
        }

        body { background-color: #f4f7fe; }

        .navbar-custom {
            background: var(--primary-gradient);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .receipt-card { 
            max-width: 450px; 
            margin: auto; 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .btn-custom {
            background: var(--dark-purple);
            color: white;
            border: none;
            border-radius: 10px;
        }
        .btn-custom:hover { background: var(--hover-purple); color: white; }

        .text-custom { color: var(--dark-purple); }

        @media print { 
            .no-print { display: none; } 
            body { background-color: white; }
            .receipt-card { box-shadow: none; border: 1px solid #eee; margin: 0; }
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Lamongan Mas Yudi</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white-50" href="index.php">Kasir</a>
            <a class="nav-link text-white-50" href="laporan.php">Laporan</a>
            <a class="nav-link text-warning fw-bold" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container mt-2 mb-5">
    <div class="card receipt-card p-4">
        <div class="card-body text-center">
            <div class="text-success mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            
            <h3 class="fw-bold mb-1">Pembayaran Berhasil!</h3>
            <p class="text-muted small">ID Transaksi: #<?= $id ?> | <?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></p>
            
            <hr class="my-4">

            <div class="text-start mb-4">
                <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Rincian Pesanan</h6>
                <table class="table table-sm table-borderless align-middle">
                    <?php
                    // Ambil detail item dengan JOIN antara transaction_items dan products
                    $items = mysqli_query($conn, "SELECT ti.*, p.name FROM transaction_items ti 
                             JOIN products p ON ti.product_id = p.id WHERE ti.transaction_id = '$id'");
                    while($item = mysqli_fetch_assoc($items)):
                    ?>
                    <tr>
                        <td class="small fw-bold text-dark">
                            <?= $item['name'] ?> <span class="text-muted fw-normal small">x<?= $item['qty'] ?></span>
                        </td>
                        <td class="text-end small">Rp <?= number_format($item['subtotal']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <hr class="border-secondary border-1 opacity-25">

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Total Tagihan</span>
                <span class="fw-bold text-custom">Rp <?= number_format($total_tagihan) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Tunai</span>
                <span class="fw-bold text-dark">Rp <?= number_format($bayar) ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="fw-bold text-danger">Kembalian</span>
                <span class="fw-bold text-danger fs-5">Rp <?= number_format($kembalian) ?></span>
            </div>

            <hr class="my-4">

            <div class="mt-4 no-print">
                <button onclick="window.print()" class="btn btn-custom w-100 mb-2 py-2 fw-bold shadow-sm">
                    CETAK STRUK (PDF)
                </button>
                <a href="index.php" class="btn btn-outline-secondary w-100 py-2">
                    Kembali ke Kasir
                </a>
            </div>
            
            <p class="mt-4 mb-0 small text-muted italic text-center">"Terima kasih atas kunjungannya!"</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>