<?php
session_start();
include 'config.php';

// Proteksi halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
              header("Location: login.php");
              exit();
}

$result = $conn->query("SELECT * FROM assets");
?>
<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Daftar Aset - Admin</title>
              <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
                            }

                            .sidebar h2 {
                                          font-size: 22px;
                                          text-align: center;
                                          margin-bottom: 30px;
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

              <nav class="sidebar">
                            <h3>IT Support</h3>
                            <a href="dashboard_admin.php">Dashboard</a>
                            <a href="kelola_aset.php">Kelola Aset</a>
                            <a href="permintaan_perbaikan.php" class="active">Permintaan Perbaikan</a>
                            <a href="lihat_assets.php">Lihat Aset</a>
                            <a href="laporan.php">Laporan</a>
                            <a href="logout.php">Logout</a>
              </nav>

              <main class="main">
                            <h2>Daftar Aset</h2>

                            <?php if ($result && $result->num_rows > 0): ?>
                                          <table>
                                                        <thead>
                                                                      <tr>
                                                                                    <th>No</th>
                                                                                    <th>Nama Aset</th>
                                                                                    <th>Jenis</th>
                                                                                    <th>Lokasi</th>
                                                                                    <th>Kondisi</th>
                                                                                    <th>Aksi</th>
                                                                      </tr>
                                                        </thead>
                                                        <tbody>
                                                                      <?php $no = 1;
                                                                      while ($row = $result->fetch_assoc()): ?>
                                                                                    <tr>
                                                                                                  <td><?= $no++ ?></td>
                                                                                                  <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['jenis']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['kondisi']) ?></td>
                                                                                                  <td class="action-links">
                                                                                                                <a href="kelola_aset.php?edit=<?= $row['id'] ?>">Edit</a>
                                                                                                                <a href="kelola_aset.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus aset ini?')">Hapus</a>
                                                                                                  </td>
                                                                                    </tr>
                                                                      <?php endwhile; ?>
                                                        </tbody>
                                          </table>
                            <?php else: ?>
                                          <p class="no-data">Tidak ada data aset ditemukan.</p>
                            <?php endif; ?>
              </main>

</body>

</html>