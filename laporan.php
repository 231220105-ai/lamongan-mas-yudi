<?php
session_start();

// Proteksi halaman login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// 1. Query untuk menghitung total omzet per hari
$query_omzet = mysqli_query($conn, "SELECT DATE(created_at) as tanggal, SUM(total) as total_harian 
                                    FROM transactions 
                                    GROUP BY DATE(created_at) 
                                    ORDER BY tanggal DESC");

// 2. Query untuk mengambil semua riwayat transaksi
$query_riwayat = mysqli_query($conn, "SELECT * FROM transactions ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tema Warna Seragam */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-purple: #764ba2;
        }

        body { background-color: #f4f7fe; }

        .navbar-custom {
            background: var(--primary-gradient);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
        }

        .card-header-custom {
            background: var(--dark-purple);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }

        .text-custom { color: var(--dark-purple); }
        
        .table thead th {
            border-top: none;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Lamongan Mas Yudi</a>
        <div class="navbar-nav ms-auto text-white-50 small">
            <a class="nav-link text-white-50" href="index.php">Kasir</a>
            <a class="nav-link text-white-50" href="produk.php">Kelola Menu</a> 
            <a class="nav-link active fw-bold text-white" href="laporan.php">Laporan</a>
            <a class="nav-link text-white-50" href="users.php">User</a>
            <a class="nav-link text-warning fw-bold" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header card-header-custom py-3 text-center">
                    Omzet Harian
                </div>
                <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query_omzet)): ?>
                            <tr>
                                <td class="small fw-bold"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($row['total_harian']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0 fw-bold text-secondary">Riwayat Transaksi</h5>
                    <span class="badge bg-primary px-3 py-2">Total Transaksi: <?= mysqli_num_rows($query_riwayat) ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Waktu Transaksi</th>
                                    <th class="text-end">Total Bayar</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($trx = mysqli_fetch_assoc($query_riwayat)): ?>
                                <tr>
                                    <td class="fw-bold text-muted">#<?= $trx['id'] ?></td>
                                    <td class="small"><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></td>
                                    <td class="text-end fw-bold text-custom">Rp <?= number_format($trx['total']) ?></td>
                                    <td class="text-center">
                                        <a href="transaksi_selesai.php?id=<?= $trx['id'] ?>&bayar=<?= $trx['total'] ?>" 
                                           class="btn btn-outline-info btn-sm px-3 rounded-pill">
                                             Detail Struk
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>