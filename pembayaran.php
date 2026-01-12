<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
include 'koneksi.php';

$total = isset($_POST['total']) ? (float)$_POST['total'] : 0;
if ($total <= 0) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); --dark-purple: #764ba2; }
        body { background-color: #f4f7fe; }
        .navbar-custom { background: var(--primary-gradient); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-header-custom { background: var(--dark-purple); color: white; border-radius: 15px 15px 0 0 !important; }
        .btn-custom { background: var(--dark-purple); color: white; border: none; border-radius: 8px; }
        .btn-custom:hover { background: #5a3a7d; color: white; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark navbar-custom mb-4">
    <div class="container"><span class="navbar-brand fw-bold">Lamongan Mas Yudi - Pembayaran</span></div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header card-header-custom text-center py-3">
                    <h5 class="mb-0 fw-bold">INPUT PEMBAYARAN</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Belanja:</span>
                        <h4 class="fw-bold text-primary">Rp <?= number_format($total) ?></h4>
                    </div>
                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="total" value="<?= $total ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">UANG TUNAI</label>
                            <input type="number" name="bayar" id="bayar" class="form-control form-control-lg border-primary" placeholder="0" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">KEMBALIAN</label>
                            <input type="text" id="kembalian_display" class="form-control form-control-lg bg-light fw-bold text-danger" value="Rp 0" readonly>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><a href="index.php" class="btn btn-light w-100 py-2">BATAL</a></div>
                            <div class="col-6"><button type="submit" id="btn_simpan" class="btn btn-custom w-100 py-2 fw-bold" disabled>SIMPAN</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const total = <?= $total ?>;
    document.getElementById('bayar').addEventListener('input', function() {
        const bayar = parseFloat(this.value) || 0;
        const sisa = bayar - total;
        if (sisa >= 0) {
            document.getElementById('kembalian_display').value = "Rp " + sisa.toLocaleString('id-ID');
            document.getElementById('btn_simpan').disabled = false;
        } else {
            document.getElementById('kembalian_display').value = "Uang Kurang";
            document.getElementById('btn_simpan').disabled = true;
        }
    });
</script>
</body>
</html>