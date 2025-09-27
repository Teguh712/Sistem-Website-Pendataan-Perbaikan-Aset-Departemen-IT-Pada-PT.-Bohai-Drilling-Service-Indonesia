<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_manager') {
              header("Location: login.php");
              exit();
}
$username = $_SESSION['user']['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Dashboard PM - Sistem Aset IT</title>
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
                            }

                            .sidebar h2 {
                                          text-align: center;
                            }

                            .sidebar a {
                                          color: white;
                                          text-decoration: none;
                                          display: block;
                                          padding: 10px;
                                          margin: 5px 0;
                                          border-radius: 6px;
                            }

                            .sidebar a:hover {
                                          background: #34495e;
                            }

                            .main {
                                          flex-grow: 1;
                                          padding: 30px;
                            }

                            .action-boxes {
                                          display: grid;
                                          grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                                          gap: 20px;
                            }

                            .action-box {
                                          background: #2980b9;
                                          color: white;
                                          padding: 20px;
                                          border-radius: 10px;
                                          text-decoration: none;
                                          display: flex;
                                          align-items: center;
                            }

                            .action-box i {
                                          font-size: 36px;
                                          margin-right: 15px;
                            }
              </style>
</head>

<body>

              <div class="sidebar">
                            <h2>Project Manager</h2>
                            <a href="dashboard_pm.php" class="active">Dashboard</a>
                            <a href="permintaan_perbaikan_pm.php">Manifest Perbaikan</a>
                            <a href="detail_perbaikan_pm.php">Detail Perbaikan</a>
                            <a href="goods_form_pm.php">Goods Form</a>
                            <a href="laporan_seluruhpm.php">Laporan Keseluruhan</a>
                            <a href="logout.php">Logout</a>
              </div>

              <div class="main">
                            <h2>Selamat datang, <?= htmlspecialchars($username) ?> (Project Manager)</h2>
                            <p>Ini adalah dashboard khusus Project Manager.</p>

                            <div class="action-boxes">
                                          <a href="permintaan_perbaikan_pm.php" class="action-box"><i class="fas fa-tools"></i>
                                                        <div>Permintaan Perbaikan</div>
                                          </a>
                                          <a href="detail_perbaikan_pm.php" class="action-box"><i class="fas fa-clipboard-list"></i>
                                                        <div>Detail Perbaikan</div>
                                          </a>
                                          <a href="goods_form_pm.php" class="action-box"><i class="fas fa-box"></i>
                                                        <div>Goods Form</div>
                                          </a>
                                          <a href="laporan_keseluruhan_pm.php" class="action-box"><i class="fas fa-file-alt"></i>
                                                        <div>Laporan</div>
                                          </a>
                            </div>
              </div>

</body>

</html>