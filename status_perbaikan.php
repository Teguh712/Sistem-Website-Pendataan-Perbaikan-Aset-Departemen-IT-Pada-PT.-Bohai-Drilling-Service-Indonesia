<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
$nama_departemen = $role;

// Ambil nama_departemen dari relasi user → karyawann → departemen
$stmt = $conn->prepare("
    SELECT d.nama_departemen 
    FROM karyawann k 
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen 
    WHERE k.nomor_badge = (
        SELECT nomor_badge FROM users WHERE id = ?
    ) LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nama_departemen_db);
if ($stmt->fetch()) {
        $nama_departemen = $nama_departemen_db;
}
$stmt->close();

// Ambil data perbaikan untuk user ini
$sql = "SELECT dp.*, a.nama_aset, a.kategori_id, ka.nama_kategori
        FROM detail_perbaikan dp
        LEFT JOIN assetss a ON dp.kode_aset = a.kode_aset
        LEFT JOIN kategori_aset ka ON a.kategori_id = ka.id
        WHERE dp.user_id = ?
        ORDER BY dp.tanggal_pengajuan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
        <meta charset="UTF-8">
        <title>Status Perbaikan</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
                * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                }

                body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background-color: #f0f2f5;
                        display: flex;
                }

                .sidebar {
                        width: 240px;
                        height: 100vh;
                        background-color: #2c3e50;
                        color: white;
                        padding: 20px;
                        position: fixed;
                }

                .sidebar h2 {
                        font-size: 22px;
                        text-align: center;
                        margin-bottom: 30px;
                }

                .sidebar a {
                        display: flex;
                        align-items: center;
                        color: white;
                        text-decoration: none;
                        padding: 12px 20px;
                        border-radius: 6px;
                        margin: 8px 0;
                        transition: background-color 0.3s ease;
                }

                .sidebar a i {
                        margin-right: 12px;
                        font-size: 16px;
                        width: 20px;
                        text-align: center;
                }

                .sidebar a:hover,
                .sidebar a.active {
                        background-color: #34495e;
                }

                .logout {
                        margin-top: 20px;
                        color: #e74c3c;
                }

                .logout:hover {
                        background-color: #c0392b;
                }

                .main {
                        margin-left: 240px;
                        padding: 30px;
                        width: 100%;
                }

                .main h2 {
                        margin-bottom: 20px;
                }

                table {
                        width: 100%;
                        border-collapse: collapse;
                        background-color: white;
                        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
                        border-radius: 8px;
                        overflow: hidden;
                }

                th,
                td {
                        padding: 12px 15px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                }

                th {
                        background-color: #2c3e50;
                        color: white;
                }

                tr:hover {
                        background-color: #f2f2f2;
                }

                @media (max-width: 768px) {
                        body {
                                flex-direction: column;
                        }

                        .sidebar {
                                width: 100%;
                                height: auto;
                                flex-direction: row;
                                display: flex;
                                overflow-x: auto;
                        }

                        .sidebar a {
                                flex: 1;
                                text-align: center;
                                padding: 10px;
                        }

                        .main {
                                margin-left: 0;
                                padding: 20px;
                        }
                }
        </style>
</head>

<body>

        <div class="sidebar">
                <h2>Karyawan</h2>
                <a href="dashboard_staff.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                <a href="detail_perbaikan.php"><i class="fas fa-file-alt"></i> Ajukan Perbaikan</a>
                <a href="status_perbaikan.php" class="active"><i class="fas fa-clipboard-list"></i> Status Perbaikan</a>
                <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="main">
                <h2>Status Permintaan Perbaikan (<?= htmlspecialchars($nama_departemen) ?>)</h2>

                <table>
                        <thead>
                                <tr>
                                        <th>No</th>
                                        <th>Nama Aset</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Chargo Manifest</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                                                <td><?= htmlspecialchars($row['kategori_aset'] ?: $row['nama_kategori']) ?></td>
                                                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                                <td><?= htmlspecialchars($row['status']) ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                                                <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                                        </tr>
                                <?php endwhile; ?>
                        </tbody>
                </table>
        </div>

</body>

</html>