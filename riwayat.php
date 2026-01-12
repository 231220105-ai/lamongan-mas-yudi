<?php
include 'koneksi.php';
?>
<div class="container mt-5">
    <h3>Riwayat Transaksi</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Kasir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT transactions.*, users.username FROM transactions 
                    JOIN users ON transactions.user_id = users.id";
            $res = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>Rp <?= number_format($row['total_price']) ?></td>
                <td><?= $row['username'] ?></td>
                <td><a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>