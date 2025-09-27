<?php
session_start();
include 'config.php';

// Cek role login (hanya untuk staff biasa, bukan admin/PM)
if (!isset($_SESSION['user']) || in_array($_SESSION['user']['role'], ['admin', 'project_manager'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$nomor_badge = $_SESSION['user']['nomor_badge'];

// Ambil nama departemen berdasarkan nomor_badge melalui JOIN
$query = mysqli_query($conn, "
    SELECT d.nama_departemen 
    FROM karyawann k
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen
    WHERE k.nomor_badge = '$nomor_badge'
    LIMIT 1
");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);
$nama_departemen = $data ? $data['nama_departemen'] : 'Departemen Tidak Diketahui';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Karyawan</title>
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
            margin-top: 0;
            margin-bottom: 30px;
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
            width: 20px;
            text-align: center;
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
            margin-bottom: 10px;
            font-weight: 600;
        }

        .main p {
            margin-bottom: 20px;
        }

        .action-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-box {
            background-color: #3498db;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
        }

        .action-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .action-box i {
            font-size: 36px;
            margin-right: 15px;
        }

        .action-content h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .action-content p {
            margin: 4px 0 0;
            font-size: 14px;
            color: #ecf0f1;
        }

        .bg-purple {
            background-color: #8e44ad;
        }

        .bg-green {
            background-color: #27ae60;
        }

        .bg-orange {
            background-color: #f39c12;
        }

        .bg-blue {
            background-color: #2980b9;
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
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>Karyawan</h3>
        <a href="dashboard_staff.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="detail_perbaikan.php"><i class="fas fa-file-alt"></i> Ajukan Perbaikan</a>
        <a href="status_perbaikan.php"><i class="fas fa-clipboard-list"></i> Status Perbaikan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2>Selamat datang, <?= htmlspecialchars($username) ?>!</h2>
        <p><strong>Departemen:</strong> <?= htmlspecialchars($nama_departemen) ?></p>
        <hr>
        <p>Ini adalah halaman dashboard untuk karyawan departemen Anda.</p>

        <div class="action-boxes">
            <a href="ajukan_perbaikan.php" class="action-box bg-green">
                <i class="fas fa-screwdriver-wrench"></i>
                <div class="action-content">
                    <h4>Ajukan Perbaikan</h4>
                    <p>Permintaan perbaikan aset</p>
                </div>
            </a>

            <a href="status_perbaikan.php" class="action-box bg-orange">
                <i class="fas fa-clipboard-list"></i>
                <div class="action-content">
                    <h4>Status</h4>
                    <p>Lihat status perbaikan</p>
                </div>
            </a>

            <a href="detail_perbaikan.php" class="action-box bg-blue">
                <i class="fas fa-file-alt"></i>
                <div class="action-content">
                    <h4>Detail</h4>
                    <p>Data detail perbaikan</p>
                </div>
            </a>

            <a href="profile.php" class="action-box bg-purple">
                <i class="fas fa-user"></i>
                <div class="action-content">
                    <h4>Profil</h4>
                    <p>Edit data login</p>
                </div>
            </a>
        </div>
    </div>

</body>

</html>