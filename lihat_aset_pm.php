<?php
session_start();
include 'config.php';

// Proteksi halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_manager') {
              header("Location: login.php");
              exit();
}

// Proses pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM assets";
if ($search !== '') {
              $query = "SELECT * FROM assets WHERE nama_aset LIKE '%$search%' OR jenis LIKE '%$search%'";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Daftar Aset - Project Manager</title>
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
                                          display: block;
                                          color: white;
                                          text-decoration: none;
                                          padding: 12px 20px;
                                          border-radius: 6px;
                                          margin: 8px 0;
                                          transition: background-color 0.3s ease;
                            }

                            .sidebar a:hover {
                                          background-color: #34495e;
                            }

                            .main {
                                          margin-left: 240px;
                                          padding: 30px;
                                          width: 100%;
                            }

                            h2 {
                                          margin-bottom: 20px;
                            }

                            table {
                                          width: 100%;
                                          border-collapse: collapse;
                                          background: white;
                                          border-radius: 8px;
                                          overflow: hidden;
                                          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            }

                            thead tr {
                                          background-color: #2c3e50;
                                          color: white;
                            }

                            th,
                            td {
                                          padding: 14px 16px;
                                          text-align: center;
                                          border-bottom: 1px solid #ddd;
                            }

                            tbody tr:hover {
                                          background-color: #f9f9f9;
                            }

                            .no-data {
                                          color: red;
                                          text-align: center;
                                          margin-top: 20px;
                            }

                            .logout {
                                          margin-top: 30px;
                                          color: #e74c3c;
                            }

                            .logout:hover {
                                          background-color: #c0392b;
                            }

                            @media (max-width: 768px) {
                                          body {
                                                        flex-direction: column;
                                                        height: auto;
                                          }

                                          .sidebar {
                                                        width: 100%;
                                                        height: auto;
                                                        position: relative;
                                                        overflow-x: auto;
                                                        white-space: nowrap;
                                          }

                                          .sidebar a {
                                                        display: inline-block;
                                                        margin-right: 12px;
                                                        margin-bottom: 0;
                                          }

                                          .main {
                                                        margin-left: 0;
                                                        padding: 20px;
                                          }
                            }

                            .search-form input,
                            .search-form button,
                            .search-form a {
                                          padding: 10px;
                                          border-radius: 6px;
                                          border: 1px solid #ccc;
                                          margin-right: 8px;
                            }

                            .search-form button {
                                          background-color: #3498db;
                                          color: white;
                                          border: none;
                                          cursor: pointer;
                            }

                            .search-form a {
                                          background-color: #aaa;
                                          color: white;
                                          text-decoration: none;
                            }

                            .btn-print {
                                          display: inline-block;
                                          margin-bottom: 20px;
                                          background-color: #3498db;
                                          color: white;
                                          padding: 10px 16px;
                                          border-radius: 6px;
                                          text-decoration: none;
                            }
              </style>
</head>

<body>
              <div class="sidebar">
                            <h2>Project Manager</h2>
                            <a href="dashboard_pm.php">Dashboard</a>
                            <a href="permintaan_perbaikan_pm.php">Permintaan Perbaikan</a>
                            <a href="lihat_aset_pm.php">Lihat Aset</a>
                            <a href="logout.php" class="logout">Logout</a>
              </div>

              <div class="main">
                            <h2>Daftar Aset</h2>

                            <form method="GET" class="search-form" style="margin-bottom: 20px;">
                                          <input type="text" name="search" placeholder="Cari aset berdasarkan nama atau jenis..."
                                                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                          <button type="submit">Cari</button>
                                          <a href="lihat_aset_pm.php">Reset</a>
                            </form>

                            <a href="cetak_laporan_asetPM.php" target="_blank" class="btn-print">Cetak Laporan</a>

                            <?php if ($result && $result->num_rows > 0): ?>
                                          <table>
                                                        <thead>
                                                                      <tr>
                                                                                    <th>No</th>
                                                                                    <th>Nama Aset</th>
                                                                                    <th>Jenis</th>
                                                                      </tr>
                                                        </thead>
                                                        <tbody>
                                                                      <?php $no = 1;
                                                                      while ($row = $result->fetch_assoc()): ?>
                                                                                    <tr>
                                                                                                  <td><?= $no++ ?></td>
                                                                                                  <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['jenis']) ?></td>
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