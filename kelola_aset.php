<?php
session_start();
include 'config.php';

// Proteksi halaman admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data kategori
$kategori_result = $conn->query("SELECT * FROM kategori_aset");
$kategori_list = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategori_list[] = $row;
}

$edit_mode = false;
$edit_data = ['id' => '', 'kode_aset' => '', 'nama_aset' => '', 'kategori_id' => ''];

// Edit data
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = (int)$_GET['edit'];
    $result_edit = $conn->query("SELECT * FROM assetss WHERE id=$id");
    if ($result_edit && $result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
    }
}

// Hapus data
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM assetss WHERE id=$id");
    header("Location: kelola_aset.php");
    exit();
}

// Simpan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $kode_aset = $_POST['kode_aset'];
    $nama = $_POST['nama'];
    $kategori_id = $_POST['kategori_id'];

    if (!empty($id)) {
        $stmt = $conn->prepare("UPDATE assetss SET kode_aset=?, nama_aset=?, kategori_id=? WHERE id=?");
        $stmt->bind_param("ssii", $kode_aset, $nama, $kategori_id, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO assetss (kode_aset, nama_aset, kategori_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $kode_aset, $nama, $kategori_id);
    }

    $stmt->execute();
    header("Location: kelola_aset.php");
    exit();
}

// Pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "
    SELECT a.id, a.kode_aset, a.nama_aset, k.nama_kategori 
    FROM assetss a
    LEFT JOIN kategori_aset k ON a.kategori_id = k.id
";
if ($search !== '') {
    $query .= " WHERE a.kode_aset LIKE '%$search%' OR a.nama_aset LIKE '%$search%' OR k.nama_kategori LIKE '%$search%'";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Aset - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #ecf0f1;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar h3 {
            margin: 0 0 30px 0;
            font-weight: 600;
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

        .sidebar a i {
            margin-right: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
        }

        .main {
            flex-grow: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 24px;
            font-weight: 600;
        }

        form {
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin-bottom: 40px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #34495e;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background-color: #2c3e50;
        }

        .button-cancel {
            padding: 10px 16px;
            background-color: #aaa;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            margin-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        table thead tr {
            background-color: #2c3e50;
            color: white;
        }

        table th,
        table td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .action-links a {
            margin: 0 5px;
            color: #3498db;
            text-decoration: none;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .no-data {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            width: 300px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-form button,
        .search-form a {
            padding: 10px 16px;
            background-color: #3498db;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                padding: 10px;
                overflow-x: auto;
            }

            .sidebar a {
                margin-right: 12px;
                margin-bottom: 0;
                white-space: nowrap;
            }

            .main {
                padding: 20px;
            }

            table th,
            table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>IT Support</h3>
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kelola_aset.php" class="active"><i class="fas fa-box"></i> Kelola Aset</a>
        <a href="kelola_akun.php"><i class="fas fa-users-cog"></i> Kelola Akun</a>
        <a href="data_karyawan.php"><i class="fas fa-building"></i> Karyawan</a>
        <a href="departemen.php"><i class="fas fa-building"></i> Departemen</a>
        <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
        <a href="detail_perbaikan.php"><i class="fas fa-clipboard-list"></i> Detail Perbaikan</a>
        <a href="goods_form.php"><i class="fas fa-clipboard-list"></i> Goods Form</a>
        <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan Keseluruhan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2><?= $edit_mode ? "Edit Aset" : "Tambah Aset Baru" ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">
            <label for="kode_aset">Kode Aset:</label>
            <input type="text" id="kode_aset" name="kode_aset" required value="<?= htmlspecialchars($edit_data['kode_aset']) ?>">

            <label for="nama">Nama Aset:</label>
            <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($edit_data['nama_aset']) ?>">

            <label for="kategori_id">Kategori:</label>
            <select name="kategori_id" id="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_list as $kategori): ?>
                    <option value="<?= $kategori['id'] ?>" <?= $edit_data['kategori_id'] == $kategori['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit"><?= $edit_mode ? "Update" : "Tambah" ?> Aset</button>
            <?php if ($edit_mode): ?>
                <a href="kelola_aset.php" class="button-cancel">Batal</a>
            <?php endif; ?>
        </form>

        <h2>Daftar Aset</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari kode, nama, atau kategori..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">Cari</button>
            <a href="kelola_aset.php">Reset</a>

        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['kode_aset']) ?></td>
                            <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td class="action-links">
                                <a href="kelola_aset.php?edit=<?= $row['id'] ?>">Edit</a>
                                <a href="kelola_aset.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus aset ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Tidak ada data aset ditemukan.</p>
        <?php endif; ?>
    </div>
</body>

</html>