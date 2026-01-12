<?php
session_start();

// 1. Proteksi Halaman: Cek apakah kasir sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// 2. Proses Tambah User Baru
if (isset($_POST['tambah_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query simpan ke tabel users
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User berhasil ditambah!'); window.location='users.php';</script>";
    } else {
        $error = "Gagal menambah user: " . mysqli_error($conn);
    }
}

// 3. Proses Hapus User
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Jangan biarkan user menghapus dirinya sendiri yang sedang login
    if ($id == $_SESSION['user']['id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun yang sedang digunakan!'); window.location='users.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
        header('Location: users.php');
    }
}

// 4. Ambil data semua user untuk ditampilkan
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tema Warna Seragam */
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

        .btn-custom {
            background: var(--dark-purple);
            color: white;
            border: none;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background: var(--hover-purple);
            color: white;
        }
        
        .table thead th {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-top: none;
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
            <a class="nav-link text-white-50" href="laporan.php">Laporan</a>
            <a class="nav-link active fw-bold text-white" href="users.php">User</a>
            <a class="nav-link text-warning fw-bold" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header card-header-custom py-3 text-center">
                    Tambah Akun Kasir
                </div>
                <div class="card-body p-4">
                    <?php if(isset($error)) echo "<div class='alert alert-danger small'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Username</label>
                            <input type="text" name="username" class="form-control p-2" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Password</label>
                            <input type="password" name="password" class="form-control p-2" placeholder="Masukkan password" required>
                        </div>
                        <button type="submit" name="tambah_user" class="btn btn-custom w-100 py-2 fw-bold">SIMPAN PENGGUNA</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-secondary">Daftar Pengguna Sistem</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID Akun</th>
                                    <th>Username</th>
                                    <th class="text-center">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($query_users)): ?>
                                <tr>
                                    <td class="fw-bold text-muted small">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= $row['username'] ?></td>
                                    <td class="text-center">
                                        <?php if($row['id'] != $_SESSION['user']['id']): ?>
                                            <a href="users.php?hapus=<?= $row['id'] ?>" 
                                               class="btn btn-outline-danger btn-sm px-3 rounded-pill" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                Hapus Akun
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">Akun Aktif</span>
                                        <?php endif; ?>
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