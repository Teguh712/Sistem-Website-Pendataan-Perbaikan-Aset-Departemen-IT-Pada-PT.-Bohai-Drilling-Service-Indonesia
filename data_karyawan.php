<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

// Hapus data karyawan
if (isset($_GET['hapus'])) {
    $badge = $_GET['hapus'];
    $conn->query("DELETE FROM karyawann WHERE nomor_badge = '$badge'");
    header("Location: data_karyawan.php");
    exit();
}

// Tambah / Edit karyawan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_badge = trim($_POST['nomor_badge']);
    $nama_karyawan = trim($_POST['nama_karyawan']);
    $kode_departemen = strtolower(trim($_POST['kode_departemen']));

    if (isset($_POST['edit_mode'])) {
        $stmt = $conn->prepare("UPDATE karyawann SET nama_karyawan=?, kode_departemen=? WHERE nomor_badge=?");
        $stmt->bind_param("sss", $nama_karyawan, $kode_departemen, $nomor_badge);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO karyawann (nomor_badge, nama_karyawan, kode_departemen) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nomor_badge, $nama_karyawan, $kode_departemen);
        $stmt->execute();
    }

    header("Location: data_karyawan.php");
    exit();
}

// Form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $badge = $_GET['edit'];
    $res = $conn->query("SELECT * FROM karyawann WHERE nomor_badge = '$badge'");
    $edit_data = $res->fetch_assoc();
}

// Ambil semua data karyawan
$result = $conn->query("
    SELECT k.nomor_badge, k.nama_karyawan, k.kode_departemen, d.nama_departemen
    FROM karyawann k
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen
    ORDER BY k.nama_karyawan ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Karyawan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            background-color: #ecf0f1;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
            height: 100vh;
            flex-shrink: 0;
        }

        .sidebar h3 {
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            margin-bottom: 12px;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
        }

        .sidebar a i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        .main {
            flex-grow: 1;
            padding: 30px 40px;
        }

        form {
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            max-width: 500px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        form label {
            font-weight: 500;
            display: block;
            margin-top: 14px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 18px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .aksi a {
            text-decoration: none;
            margin: 0 6px;
            font-weight: bold;
        }

        .aksi a.hapus {
            color: #e74c3c;
        }

        .aksi a.edit {
            color: #3498db;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>IT Support</h3>
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
        <a href="kelola_akun.php"><i class="fas fa-users-cog"></i> Kelola Akun</a>
        <a href="data_karyawan.php" class="active"><i class="fas fa-id-card"></i> Karyawan</a>
        <a href="departemen.php"><i class="fas fa-sitemap"></i> Departemen</a>
        <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
        <a href="detail_perbaikan.php"><i class="fas fa-clipboard-list"></i> Detail Perbaikan</a>
        <a href="goods_form.php"><i class="fas fa-truck-loading"></i> Goods Form</a>
        <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2><?= $edit_data ? 'Edit Karyawan' : 'Tambah Karyawan' ?></h2>
        <form method="POST">
            <label for="nomor_badge">Nomor Badge</label>
            <input type="text" name="nomor_badge" value="<?= $edit_data['nomor_badge'] ?? '' ?>" required <?= $edit_data ? 'readonly' : '' ?>>

            <label for="nama_karyawan">Nama Karyawan</label>
            <input type="text" name="nama_karyawan" value="<?= $edit_data['nama_karyawan'] ?? '' ?>" required>

            <label for="kode_departemen">Kode Departemen</label>
            <select name="kode_departemen" required>
                <option value="">-- Pilih Departemen --</option>
                <?php
                $dep = $conn->query("SELECT kode_departemen, nama_departemen FROM departemen");
                while ($d = $dep->fetch_assoc()):
                ?>
                    <option value="<?= strtolower($d['kode_departemen']) ?>" <?= ($edit_data && $edit_data['kode_departemen'] == $d['kode_departemen']) ? 'selected' : '' ?>>
                        <?= $d['kode_departemen'] ?> - <?= $d['nama_departemen'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <?php if ($edit_data): ?>
                <input type="hidden" name="edit_mode" value="1">
            <?php endif; ?>

            <button type="submit"><?= $edit_data ? 'Update' : 'Simpan' ?></button>
        </form>

        <h2>Data Karyawan</h2>
        <table>
            <thead>
                <tr>
                    <th>Nomor Badge</th>
                    <th>Nama Karyawan</th>
                    <th>Kode Departemen</th>
                    <th>Nama Departemen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nomor_badge']) ?></td>
                        <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                        <td><?= htmlspecialchars($row['kode_departemen']) ?></td>
                        <td><?= htmlspecialchars($row['nama_departemen'] ?? '-') ?></td>
                        <td class="aksi">
                            <a href="?edit=<?= $row['nomor_badge'] ?>" class="edit">Edit</a>
                            <a href="?hapus=<?= $row['nomor_badge'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>