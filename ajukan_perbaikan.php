<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pesan = "";

// Ambil nomor badge dari session login
$nomor_badge = $_SESSION['user']['nomor_badge'] ?? '';

// Ambil nama departemen dari relasi karyawann → departemen
$q = $conn->query("
    SELECT d.nama_departemen 
    FROM karyawann k 
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen 
    WHERE k.nomor_badge = '$nomor_badge'
");
$data = $q && $q->num_rows > 0 ? $q->fetch_assoc() : null;
$nama_departemen = $data['nama_departemen'] ?? 'Unknown';

// Reset manifest
if (isset($_POST['reset_manifest'])) {
    unset($_SESSION['last_chargo_manifest']);
    $pesan = "<p style='color:orange;'>Chargo Manifest telah direset.</p>";
}

// Generate nomor manifest jika belum ada di session
if (!isset($_SESSION['last_chargo_manifest'])) {
    $tanggal_hari_ini = date("Ymd");
    $result = $conn->query("
        SELECT chargo_manifest FROM manifest_perbaikan 
        WHERE chargo_manifest LIKE 'CM-$tanggal_hari_ini%' 
        ORDER BY chargo_manifest DESC LIMIT 1
    ");
    if ($result && $row = $result->fetch_assoc()) {
        $last_number = (int)substr($row['chargo_manifest'], -3);
        $new_number = str_pad($last_number + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $new_number = "001";
    }
    $_SESSION['last_chargo_manifest'] = "CM-$tanggal_hari_ini-$new_number";
}

// Ajukan manifest
if (isset($_POST['submit_manifest'])) {
    $tanggal = date('Y-m-d');
    $username = $_SESSION['user']['username'];
    $chargo_manifest = $_SESSION['last_chargo_manifest'];

    $stmt = $conn->prepare("
        INSERT INTO manifest_perbaikan (tanggal_pengajuan, username, nama_departemen, chargo_manifest) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $tanggal, $username, $nama_departemen, $chargo_manifest);

    if ($stmt->execute()) {
        $pesan = "<p style='color:green;'>Manifest berhasil diajukan. Nomor: <strong>$chargo_manifest</strong></p>";
    } else {
        $pesan = "<p style='color:red;'>Gagal menyimpan data: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ajukan Manifest Perbaikan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
        }

        .sidebar h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 8px 0;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }

        .sidebar a i {
            margin-right: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
        }

        .main {
            margin-left: 240px;
            padding: 30px;
            width: 100%;
        }

        .main h2 {
            margin-bottom: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[readonly] {
            background-color: #eee;
        }

        button {
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #34495e;
        }

        .info-box {
            margin-top: 15px;
            font-style: italic;
            color: #555;
        }

        .pesan {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Karyawan</h2>
        <a href="dashboard_staff.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ajukan_perbaikan.php" class="active"><i class="fas fa-screwdriver-wrench"></i> Ajukan Perbaikan</a>
        <a href="status_perbaikan.php"><i class="fas fa-clipboard-list"></i> Status Perbaikan</a>
        <a href="detail_perbaikan.php"><i class="fas fa-file-alt"></i> Detail Perbaikan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2>Ajukan Manifest Perbaikan</h2>
        <?= $pesan ?>

        <!-- Form Ajukan Manifest -->
        <form method="POST">
            <label for="tanggal_pengajuan">Tanggal Pengajuan:</label>
            <input type="text" id="tanggal_pengajuan" disabled value="<?= date('d-m-Y') ?>">

            <label for="username">Nama Akun (Departemen):</label>
            <input type="text" id="username" disabled value="<?= htmlspecialchars($_SESSION['user']['username']) ?>">

            <label for="departemen">Nama Departemen:</label>
            <input type="text" id="departemen" disabled value="<?= htmlspecialchars($nama_departemen) ?>">

            <label for="chargo_manifest">Chargo Manifest (Otomatis):</label>
            <input type="text" id="chargo_manifest" name="chargo_manifest" readonly value="<?= htmlspecialchars($_SESSION['last_chargo_manifest']) ?>">

            <button type="submit" name="submit_manifest">Kirim Manifest</button>
        </form>

        <!-- Tombol Reset Manifest -->
        <form method="POST">
            <input type="hidden" name="reset_manifest" value="1">
            <button type="submit" style="background-color: #c0392b;">Akhiri Manifest / Reset Chargo Manifest</button>
            <div class="info-box">
                Manifest aktif: <strong><?= htmlspecialchars($_SESSION['last_chargo_manifest']) ?></strong>
            </div>
        </form>
    </div>

</body>

</html>