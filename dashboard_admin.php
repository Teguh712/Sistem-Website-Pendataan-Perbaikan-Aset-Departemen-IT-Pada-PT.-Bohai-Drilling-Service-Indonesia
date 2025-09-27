<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
              header("Location: login.php");
              exit();
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Dashboard Admin - Sistem Aset IT</title>
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
                                          margin-bottom: 24px;
                                          font-weight: 600;
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

                            .bg-green {
                                          background-color: #27ae60;
                            }

                            .bg-orange {
                                          background-color: #f39c12;
                            }

                            .bg-purple {
                                          background-color: #8e44ad;
                            }

                            .bg-red {
                                          background-color: #e74c3c;
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
                            <h3>IT Support</h3>
                            <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
                            <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
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
                            <h2>Selamat datang, IT Support <?= htmlspecialchars($_SESSION['user']['username']); ?>!</h2>
                            <hr>
                            <p>Ini adalah halaman dashboard untuk admin IT support.</p>

                            <div class="action-boxes">
                                          <a href="kelola_aset.php" class="action-box bg-green">
                                                        <i class="fas fa-box"></i>
                                                        <div class="action-content">
                                                                      <h4>Kelola Aset</h4>
                                                                      <p>Manajemen aset IT</p>
                                                        </div>
                                          </a>

                                          <a href="kelola_akun.php" class="action-box bg-purple">
                                                        <i class="fas fa-users-cog"></i>
                                                        <div class="action-content">
                                                                      <h4>Kelola Akun</h4>
                                                                      <p>Manajemen akun karyawan</p>
                                                        </div>
                                          </a>

                                          <a href="data_karyawan.php" class="action-box bg-purple">
                                                        <i class="fas fa-building"></i>
                                                        <div class="action-content">
                                                                      <h4>Karyawan</h4>
                                                                      <p>Data Karyawan</p>
                                                        </div>
                                          </a>

                                          <a href="departemen.php" class="action-box bg-yellow">
                                                        <i class="fas fa-building"></i>
                                                        <div class="action-content">
                                                                      <h4>Departemen</h4>
                                                                      <p>Data Departemen</p>
                                                        </div>
                                          </a>

                                          <a href="permintaan_perbaikan.php" class="action-box bg-orange">
                                                        <i class="fas fa-tools"></i>
                                                        <div class="action-content">
                                                                      <h4>Manifest</h4>
                                                                      <p>Perbaikan perangkat</p>
                                                        </div>
                                          </a>

                                          <a href="detail_perbaikan.php" class="action-box bg-blue">
                                                        <i class="fas fa-clipboard-list"></i>
                                                        <div class="action-content">
                                                                      <h4>Detail Perbaikan</h4>
                                                                      <p>Status & data perbaikan</p>
                                                        </div>
                                          </a>

                                          <a href="goods_form.php" class="action-box bg-blue">
                                                        <i class="fas fa-clipboard-list"></i>
                                                        <div class="action-content">
                                                                      <h4>Goods Form</h4>
                                                                      <p>Pengajuan barang baru</p>
                                                        </div>
                                          </a>

                                          <a href="laporan_keseluruhan.php" class="action-box bg-red">
                                                        <i class="fas fa-file-alt"></i>
                                                        <div class="action-content">
                                                                      <h4>Laporan Keseluruhan</h4>
                                                                      <p>Download PDF laporan</p>
                                                        </div>
                                          </a>
                            </div>
              </div>

</body>

</html>