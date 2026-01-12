<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
include 'koneksi.php';

// --- LOGIKA PROSES (CRUD) ---

// 1. Tambah Produk
if (isset($_POST['tambah'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    // Proses Upload Gambar
    $filename = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    
    if ($filename != "") {
        move_uploaded_file($tmp_name, "assets/img/" . $filename);
    } else {
        $filename = "default.jpg";
    }

    $query = "INSERT INTO products (name, price, stock, image) VALUES ('$name', '$price', '$stock', '$filename')";
    mysqli_query($conn, $query);
    header('Location: produk.php');
}

// 2. Edit Produk
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $filename = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if ($filename != "") {
        move_uploaded_file($tmp_name, "assets/img/" . $filename);
        $query = "UPDATE products SET name='$name', price='$price', stock='$stock', image='$filename' WHERE id='$id'";
    } else {
        $query = "UPDATE products SET name='$name', price='$price', stock='$stock' WHERE id='$id'";
    }
    mysqli_query($conn, $query);
    header('Location: produk.php');
}

// 3. Hapus Produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header('Location: produk.php');
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Menu - Lamongan Mas Yudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); --dark-purple: #764ba2; }
        body { background-color: #f4f7fe; }
        .navbar-custom { background: var(--primary-gradient); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-custom { background: var(--dark-purple); color: white; border: none; border-radius: 8px; }
        .btn-custom:hover { background: #5a3a7d; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Lamongan Mas Yudi</a>
        <div class="navbar-nav ms-auto text-white-50 small">
            <a class="nav-link" href="index.php">Kasir</a>
            <a class="nav-link active fw-bold text-white" href="produk.php">Kelola Menu</a>
            <a class="nav-link" href="laporan.php">Laporan</a>
            <a class="nav-link" href="users.php">User</a>
            <a class="nav-link text-warning fw-bold" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold py-3">Tambah Menu Baru</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="small fw-bold">Nama Menu</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Foto Menu</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" name="tambah" class="btn btn-custom w-100 py-2">SIMPAN MENU</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <h5 class="fw-bold mb-3 text-secondary">Daftar Produk</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td><img src="assets/img/<?= $row['image'] ?>" width="50" height="50" class="rounded object-fit-cover"></td>
                                <td class="fw-bold"><?= $row['name'] ?></td>
                                <td>Rp <?= number_format($row['price']) ?></td>
                                <td><?= $row['stock'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                    <a href="produk.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="border-radius: 15px;">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Menu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-2 text-center">
                                                    <img src="assets/img/<?= $row['image'] ?>" width="100" class="rounded border mb-2">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold">Nama Menu</label>
                                                    <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold">Harga</label>
                                                    <input type="number" name="price" class="form-control" value="<?= $row['price'] ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold">Stok</label>
                                                    <input type="number" name="stock" class="form-control" value="<?= $row['stock'] ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold">Ganti Foto (Opsional)</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit" class="btn btn-custom w-100">SIMPAN PERUBAHAN</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>