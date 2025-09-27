<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
              header("Location: login.php");
              exit();
}

// Proses tambah/edit departemen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $kode = trim($_POST['kode_departemen']);
              $nama = trim($_POST['nama_departemen']);

              if (isset($_POST['edit_mode'])) {
                            $stmt = $conn->prepare("UPDATE departemen SET nama_departemen=? WHERE kode_departemen=?");
                            $stmt->bind_param("ss", $nama, $kode);
                            $stmt->execute();
              } else {
                            $stmt = $conn->prepare("INSERT INTO departemen (kode_departemen, nama_departemen) VALUES (?, ?)");
                            $stmt->bind_param("ss", $kode, $nama);
                            $stmt->execute();
              }

              header("Location: departemen.php");
              exit();
}

// Hapus
if (isset($_GET['hapus'])) {
              $kode = $_GET['hapus'];
              $conn->query("DELETE FROM departemen WHERE kode_departemen = '$kode'");
              header("Location: data_departemen.php");
              exit();
}

// Edit mode
$edit_data = null;
if (isset($_GET['edit'])) {
              $kode = $_GET['edit'];
              $res = $conn->query("SELECT * FROM departemen WHERE kode_departemen = '$kode'");
              $edit_data = $res->fetch_assoc();
}

$result = $conn->query("SELECT * FROM departemen ORDER BY nama_departemen ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Data Departemen</title>
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
                                          display: flex;
                                          flex-direction: column;
                            }

                            .sidebar h3 {
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
                            }

                            h2 {
                                          color: #2c3e50;
                                          margin-bottom: 24px;
                            }

                            form {
                                          background: white;
                                          padding: 20px;
                                          margin-bottom: 30px;
                                          border-radius: 10px;
                                          box-shadow: 0 0 5px #ccc;
                                          max-width: 500px;
                            }

                            label {
                                          display: block;
                                          margin-bottom: 8px;
                                          font-weight: bold;
                            }

                            input {
                                          width: 100%;
                                          padding: 10px;
                                          margin-bottom: 16px;
                                          border: 1px solid #ccc;
                                          border-radius: 6px;
                            }

                            button {
                                          background-color: #3498db;
                                          color: white;
                                          padding: 10px 15px;
                                          border: none;
                                          border-radius: 6px;
                                          font-weight: bold;
                                          cursor: pointer;
                            }

                            table {
                                          width: 100%;
                                          background: white;
                                          border-collapse: collapse;
                                          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                            }

                            th,
                            td {
                                          padding: 12px;
                                          border: 1px solid #ccc;
                                          text-align: center;
                            }

                            th {
                                          background-color: #2c3e50;
                                          color: white;
                            }

                            .aksi a {
                                          margin: 0 6px;
                                          text-decoration: none;
                                          color: #2980b9;
                                          font-weight: bold;
                            }

                            .aksi a.hapus {
                                          color: #e74c3c;
                            }
              </style>
</head>

<body>

              <div class="sidebar">
                            <h3>IT Support</h3>
                            <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
                            <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
                            <a href="kelola_akun.php"><i class="fas fa-users-cog"></i> Kelola Akun</a>
                            <a href="data_karyawan.php"><i class="fas fa-building"></i> Karyawan</a>
                            <a href="data_departemen.php" class="active"><i class="fas fa-sitemap"></i> Departemen</a>
                            <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
                            <a href="detail_perbaikan.php"><i class="fas fa-clipboard-list"></i> Detail Perbaikan</a>
                            <a href="goods_form.php"><i class="fas fa-clipboard-list"></i> Goods Form</a>
                            <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan Keseluruhan</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
              </div>

              <div class="main">
                            <h2><?= $edit_data ? 'Edit Departemen' : 'Tambah Departemen Baru' ?></h2>
                            <form method="POST">
                                          <label for="kode_departemen">Kode Departemen</label>
                                          <input type="text" name="kode_departemen" id="kode_departemen" required value="<?= $edit_data['kode_departemen'] ?? '' ?>" <?= $edit_data ? 'readonly' : '' ?>>

                                          <label for="nama_departemen">Nama Departemen</label>
                                          <input type="text" name="nama_departemen" id="nama_departemen" required value="<?= $edit_data['nama_departemen'] ?? '' ?>">

                                          <?php if ($edit_data): ?>
                                                        <input type="hidden" name="edit_mode" value="1">
                                          <?php endif; ?>

                                          <button type="submit"><?= $edit_data ? 'Update' : 'Simpan' ?></button>
                            </form>

                            <h2>Data Departemen</h2>
                            <table>
                                          <thead>
                                                        <tr>
                                                                      <th>Kode Departemen</th>
                                                                      <th>Nama Departemen</th>
                                                                      <th>Aksi</th>
                                                        </tr>
                                          </thead>
                                          <tbody>
                                                        <?php while ($row = $result->fetch_assoc()): ?>
                                                                      <tr>
                                                                                    <td><?= htmlspecialchars($row['kode_departemen']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['nama_departemen']) ?></td>
                                                                                    <td class="aksi">
                                                                                                  <a href="?edit=<?= $row['kode_departemen'] ?>">Edit</a>
                                                                                                  <a href="?hapus=<?= $row['kode_departemen'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                                                                    </td>
                                                                      </tr>
                                                        <?php endwhile; ?>
                                          </tbody>
                            </table>
              </div>

</body>

</html>