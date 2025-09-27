<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pesan = "";

// Reset manifest berdasarkan user tertentu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_manifest']) && isset($_POST['target_manifest'])) {
    $target_manifest = mysqli_real_escape_string($conn, $_POST['target_manifest']);
    $delete = mysqli_query($conn, "DELETE FROM detail_perbaikan WHERE chargo_manifest = '$target_manifest'");
    if ($delete) {
        $pesan = "<p style='color:orange;'>Manifest <strong>$target_manifest</strong> berhasil dihapus.</p>";
    } else {
        $pesan = "<p style='color:red;'>Gagal menghapus manifest: " . mysqli_error($conn) . "</p>";
    }
}

// Ambil data manifest
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
    <title>Manifest Perbaikan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            background-color: #ecf0f1;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar h3 {
            margin-bottom: 20px;
        }

        .sidebar .menu {
            flex-grow: 1;
        }

        .sidebar .menu a {
            color: white;
            text-decoration: none;
            margin-bottom: 12px;
            display: block;
            padding: 10px;
            border-radius: 6px;
        }

        .sidebar .menu a:hover,
        .sidebar .menu a.active {
            background-color: #34495e;
        }

        .main {
            flex-grow: 1;
            padding: 30px 40px;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
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

        .pesan {
            margin-bottom: 15px;
            font-weight: bold;
        }

        form button {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        form button:hover {
            background-color: #e74c3c;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>IT Support</h3>
        <div class="menu">
            <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
            <a href="kelola_akun.php"><i class="fas fa-users"></i> Kelola Akun</a>
            <a href="data_karyawan.php"><i class="fas fa-building"></i> Karyawan</a>
            <a href="permintaan_perbaikan.php" class="active"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
            <a href="detail_perbaikan.php"><i class="fas fa-file-alt"></i> Detail Perbaikan</a>
            <a href="goods_form.php"><i class="fas fa-clipboard-list"></i> Goods Form</a>
            <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan Keseluruhan</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main">
        <h2>Manifest Permintaan Perbaikan</h2>

        <?php if (!empty($pesan)): ?>
            <div class="pesan"><?= $pesan ?></div>
        <?php endif; ?>

        <form action="laporan_manifest.php" method="post" target="_blank" style="margin-bottom:20px;">
            <button style="padding:10px 20px; background:#2c3e50; color:white; border:none; border-radius:5px;">
                <i class="fas fa-file-pdf"></i> Unduh PDF Manifest
            </button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama Akun (Departemen)</th>
                    <th>Chargo Manifest</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['nama_departemen'] ?? '-') ?>)</td>
                        <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Yakin ingin menghapus manifest ini?')">
                                <input type="hidden" name="reset_manifest" value="1">
                                <input type="hidden" name="target_manifest" value="<?= htmlspecialchars($row['chargo_manifest']) ?>">
                                <button type="submit"><i class="fas fa-trash"></i> Akhiri Manifest</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>