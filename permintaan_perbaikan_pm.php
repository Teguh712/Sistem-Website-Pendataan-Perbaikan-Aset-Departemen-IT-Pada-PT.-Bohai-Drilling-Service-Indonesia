<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_manager') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['username'];

// Ambil data manifest dari semua user/admin
$query = "
    SELECT dp.chargo_manifest, dp.tanggal_pengajuan, u.username, d.nama_departemen
    FROM detail_perbaikan dp
    LEFT JOIN users u ON dp.user_id = u.id
    LEFT JOIN karyawann k ON u.nomor_badge = k.nomor_badge
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen
    GROUP BY dp.chargo_manifest
    ORDER BY dp.tanggal_pengajuan DESC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manifest Perbaikan - PM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            background: #f0f2f5;
        }

        .sidebar {
            width: 240px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 30px;
            color: #ecf0f1;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
        }

        .main {
            flex-grow: 1;
            padding: 30px;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        th,
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        tbody tr:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Project Manager</h2>
        <a href="dashboard_pm.php">Dashboard</a>
        <a href="permintaan_perbaikan_pm.php" class="active">Manifest Perbaikan</a>
        <a href="detail_perbaikan_pm.php">Detail Perbaikan</a>
        <a href="goods_form_pm.php">Goods Form</a>
        <a href="laporan_seluruhpm.php">Laporan Keseluruhan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <h2>Data Manifest Permintaan Perbaikan</h2>
        <p>Berikut adalah daftar manifest yang diajukan oleh staff/karyawan:</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama Akun (Departemen)</th>
                    <th>Chargo Manifest</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['nama_departemen']) ?>)</td>
                        <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>