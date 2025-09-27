<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$karyawan_list = $conn->query("SELECT nama_karyawan, nomor_badge FROM karyawann ORDER BY nama_karyawan ASC");
$role_query = $conn->query("SELECT kode_departemen, nama_departemen FROM departemen ORDER BY nama_departemen ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $plain_password = $_POST['password'];
    $password = password_hash($plain_password, PASSWORD_DEFAULT);
    $role = strtolower(trim($_POST['role']));
    $nomor_badge = $_POST['nomor_badge'];

    $cek_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $cek_username->bind_param("s", $username);
    $cek_username->execute();
    $result_check = $cek_username->get_result();
    if ($result_check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.location='kelola_akun.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, plain_password, nomor_badge) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $role, $plain_password, $nomor_badge);
    $stmt->execute();

    header("Location: kelola_akun.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: kelola_akun.php");
    exit();
}

$result = $conn->query("
    SELECT u.id, u.username, u.plain_password, u.role, u.nomor_badge, 
           k.nama_karyawan, d.nama_departemen
    FROM users u 
    LEFT JOIN karyawann k ON u.nomor_badge = k.nomor_badge 
    LEFT JOIN departemen d ON u.role = d.kode_departemen
    ORDER BY u.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Akun</title>
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
        input[type="password"],
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

        a.delete-link {
            color: #e74c3c;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>IT Support</h3>
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
        <a href="kelola_akun.php" class="active"><i class="fas fa-users-cog"></i> Kelola Akun</a>
        <a href="data_karyawan.php"><i class="fas fa-id-card"></i> Karyawan</a>
        <a href="departemen.php"><i class="fas fa-sitemap"></i> Departemen</a>
        <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
        <a href="detail_perbaikan.php"><i class="fas fa-clipboard-list"></i> Detail Perbaikan</a>
        <a href="goods_form.php"><i class="fas fa-truck-loading"></i> Goods Form</a>
        <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2>Tambah Akun</h2>
        <form method="POST">
            <label for="username">Username (untuk login)</label>
            <input type="text" name="username" id="username" required>

            <label for="karyawan">Pilih Nama Karyawan</label>
            <select name="karyawan" id="karyawan" required>
                <option value="">-- Pilih Karyawan --</option>
                <?php while ($k = $karyawan_list->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($k['nomor_badge']) ?>" data-nama="<?= $k['nama_karyawan'] ?>">
                        <?= $k['nama_karyawan'] ?> (<?= $k['nomor_badge'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="hidden" name="nomor_badge" id="nomor_badge">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="role">Role (Departemen)</label>
            <select name="role" id="role" required>
                <option value="">-- Pilih Departemen --</option>
                <?php while ($r = $role_query->fetch_assoc()): ?>
                    <option value="<?= strtolower($r['kode_departemen']) ?>">
                        <?= htmlspecialchars($r['nama_departemen']) ?> (<?= strtolower($r['kode_departemen']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Tambah Akun</button>
        </form>

        <h2>Daftar Semua Akun</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nomor Badge</th>
                    <th>Username</th>
                    <th>Nama Karyawan</th>
                    <th>Password</th>
                    <th>Departemen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nomor_badge'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['nama_karyawan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['plain_password']) ?></td>
                        <td><?= htmlspecialchars($row['nama_departemen'] ?? ucfirst($row['role'])) ?></td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('karyawan').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('nomor_badge').value = selected.value;
        });
    </script>
</body>

</html>