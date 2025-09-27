<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_manager') {
              header("Location: login.php");
              exit();
}

$username = $_SESSION['user']['username'];

// Ambil semua data detail perbaikan
$query = "SELECT dp.*, u.username 
          FROM detail_perbaikan dp 
          LEFT JOIN users u ON dp.user_id = u.id 
          ORDER BY dp.tanggal_pengajuan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Detail Perbaikan - Project Manager</title>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
              <style>
                            body {
                                          font-family: sans-serif;
                                          margin: 0;
                                          display: flex;
                                          background: #f0f2f5;
                            }

                            .sidebar {
                                          width: 240px;
                                          background: #2c3e50;
                                          color: white;
                                          padding: 20px;
                                          height: 100vh;
                                          box-sizing: border-box;
                            }

                            .sidebar h2 {
                                          text-align: center;
                                          margin-bottom: 30px;
                                          color: white;
                                          /* PENTING: agar terlihat */
                                          font-size: 22px;
                            }

                            .sidebar a {
                                          color: white;
                                          text-decoration: none;
                                          display: block;
                                          padding: 10px;
                                          margin: 5px 0;
                                          border-radius: 6px;
                            }

                            .sidebar a:hover,
                            .sidebar a.active {
                                          background: #34495e;
                            }

                            .main {
                                          flex-grow: 1;
                                          padding: 30px;
                                          overflow-x: auto;
                            }

                            h2 {
                                          color: #2c3e50;
                                          margin-bottom: 20px;
                            }

                            table {
                                          width: 100%;
                                          border-collapse: collapse;
                                          background: white;
                                          border-radius: 8px;
                                          box-shadow: 0 0 5px #ccc;
                                          overflow: hidden;
                            }

                            th,
                            td {
                                          padding: 12px 16px;
                                          border: 1px solid #ddd;
                                          text-align: left;
                            }

                            th {
                                          background: #2c3e50;
                                          color: white;
                            }

                            tbody tr:hover {
                                          background-color: #f2f2f2;
                            }

                            @media screen and (max-width: 768px) {
                                          .sidebar {
                                                        width: 200px;
                                          }

                                          .sidebar h2 {
                                                        font-size: 18px;
                                          }

                                          table {
                                                        font-size: 13px;
                                          }
                            }
              </style>
</head>

<body>

              <div class="sidebar">
                            <h2>Project Manager</h2>
                            <a href="dashboard_pm.php">Dashboard</a>
                            <a href="permintaan_perbaikan_pm.php">Manifest Perbaikan</a>
                            <a href="detail_perbaikan_pm.php" class="active">Detail Perbaikan</a>
                            <a href="goods_form_pm.php">Goods Form</a>
                            <a href="laporan_seluruhpm.php">Laporan Keseluruhan</a>
                            <a href="logout.php">Logout</a>
              </div>

              <div class="main">
                            <h2>Detail Perbaikan (Read-Only)</h2>
                            <p>Berikut adalah daftar data perbaikan yang telah diajukan oleh staff/karyawan.</p>

                            <table>
                                          <thead>
                                                        <tr>
                                                                      <th>No</th>
                                                                      <th>Tanggal Pengajuan</th>
                                                                      <th>Username</th>
                                                                      <th>Chargo Manifest</th>
                                                                      <th>Quantity</th>
                                                                      <th>Kode Aset</th>
                                                                      <th>Nama Aset</th>
                                                                      <th>Kategori</th>
                                                                      <th>Deskripsi</th>
                                                                      <th>Status</th>
                                                        </tr>
                                          </thead>
                                          <tbody>
                                                        <?php $no = 1;
                                                        while ($row = mysqli_fetch_assoc($result)): ?>
                                                                      <tr>
                                                                                    <td><?= $no++ ?></td>
                                                                                    <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['kode_aset']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['kategori_aset']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                                                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                                                      </tr>
                                                        <?php endwhile; ?>
                                          </tbody>
                            </table>
              </div>

</body>

</html>