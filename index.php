<?php
session_start();
// 1. Proteksi Login
if (!isset($_SESSION['user'])) { 
    header('Location: login.php'); 
    exit; 
}

include 'koneksi.php';

// 2. Inisialisasi Keranjang
if (!isset($_SESSION['cart'])) { 
    $_SESSION['cart'] = []; 
}

// 3. Logika Tambah Item ke Keranjang
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $_SESSION['cart'][$id] = [
            'nama' => $data['name'], 
            'harga' => $data['price'], 
            'qty' => isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['qty'] + 1 : 1
        ];
    }
    header('Location: index.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { 
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            --dark-purple: #764ba2; 
            --hover-purple: #5a3a7d; 
        }
        body { background-color: #f4f7fe; }
        .navbar-custom { background: var(--primary-gradient); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .card-product { 
            transition: 0.3s; border: none; border-radius: 15px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
        }
        .card-product:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        
        .img-menu { height: 160px; object-fit: cover; width: 100%; background-color: #eee; }
        .btn-custom { background: var(--dark-purple); color: white; border: none; border-radius: 8px; font-weight: bold; }
        .btn-custom:hover { background: var(--hover-purple); color: white; }
        .text-custom { color: var(--dark-purple); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Lamongan Mas Yudi</a>
        <div class="navbar-nav ms-auto text-white-50 small">
            <a class="nav-link active text-white" href="index.php">Kasir</a>
            <a class="nav-link text-white-50" href="produk.php">Kelola Menu</a> 
            <a class="nav-link text-white-50" href="laporan.php">Laporan</a>
            <a class="nav-link text-white-50" href="users.php">User</a>
            <a class="nav-link text-warning fw-bold" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-secondary mb-0">Daftar Menu Makanan</h4>
                <a href="produk.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">+ Tambah Menu</a>
            </div>
            
            <div class="row">
                <?php
                $products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($products)) :
                    $foto_file = !empty($row['image']) ? $row['image'] : 'default.jpg';
                    $path_foto = "assets/img/" . $foto_file;
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 card-product">
                        <img src="<?= $path_foto ?>" class="img-menu" alt="<?= $row['name'] ?>" onerror="this.src='assets/img/default.jpg'">
                        <div class="card-body">
                            <h6 class="fw-bold text-truncate mb-1"><?= $row['name'] ?></h6> 
                            <p class="text-custom fw-bold mb-1">Rp <?= number_format($row['price']) ?></p> 
                            <small class="text-muted d-block mb-3 small">Stok: <?= $row['stock'] ?></small>
                            <a href="index.php?add=<?= $row['id'] ?>" class="btn btn-custom btn-sm w-100">Tambah ke Order</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white py-3" style="border-radius: 15px 15px 0 0;">
                    <span class="fw-bold">Ringkasan Pesanan</span>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless align-middle">
                        <?php 
                        $total = 0;
                        if(!empty($_SESSION['cart'])):
                            foreach ($_SESSION['cart'] as $id => $item) : 
                                $subtotal = $item['harga'] * $item['qty'];
                                $total += $subtotal;
                        ?>
                        <tr>
                            <td class="small fw-bold"><?= $item['nama'] ?></td>
                            <td class="small">x<?= $item['qty'] ?></td>
                            <td class="text-end fw-bold small">Rp <?= number_format($subtotal) ?></td>
                        </tr>
                        <?php endforeach; 
                        else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada pesanan terpilih</td></tr>
                        <?php endif; ?>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                        <span class="text-muted">Total Bayar:</span>
                        <h4 class="text-custom fw-bold mb-0">Rp <?= number_format($total) ?></h4>
                    </div>
                    
                    <form action="pembayaran.php" method="POST">
                        <input type="hidden" name="total" value="<?= $total ?>">
                        <button type="submit" class="btn btn-custom w-100 py-3 fw-bold shadow-sm" <?= $total == 0 ? 'disabled' : '' ?>>
                            PROSES PEMBAYARAN
                        </button>
                    </form>

                    <a href="hapus_keranjang.php" 
                       class="btn btn-link btn-sm text-danger w-100 mt-2 text-decoration-none"
                       onclick="return confirm('Kosongkan keranjang?')">
                        Hapus Semua Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>