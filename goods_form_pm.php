<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_manager') {
              header("Location: login.php");
              exit();
}

$username = $_SESSION['user']['username'];

// Ambil data goods_form dari admin
$query = "SELECT * FROM goods_form ORDER BY nomor_goods_form DESC, id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Goods Form - Project Manager</title>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
              <style>
                            body {
                                          font-family: sans-serif;
                                          margin: 0;
                                          display: flex;
                                          background-color: #f0f2f5;
                            }

                            .sidebar {
                                          width: 240px;
                                          background-color: #2c3e50;
                                          color: white;
                                          padding: 20px;
                                          height: 100vh;
                            }

                            .sidebar h2 {
                                          text-align: center;
                                          font-size: 22px;
                                          margin-bottom: 30px;
                                          color: white;
                            }

                            .sidebar a {
                                          color: white;
                                          text-decoration: none;
                                          display: block;
                                          padding: 10px;
                                          margin-bottom: 10px;
                                          border-radius: 6px;
                            }

                            .sidebar a:hover,
                            .sidebar a.active {
                                          background-color: #34495e;
                            }

                            .main {
                                          flex-grow: 1;
                                          padding: 30px;
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
                                          padding: 12px;
                                          border: 1px solid #ddd;
                                          text-align: left;
                            }

                            th {
                                          background-color: #2c3e50;
                                          color: white;
                            }

                            tr.header-row {
                                          background-color: #f9f9f9;
                                          font-weight: bold;
                            }
              </style>
</head>

<body>

              <div class="sidebar">
                            <h2>Project Manager</h2>
                            <a href="dashboard_pm.php">Dashboard</a>
                            <a href="permintaan_perbaikan_pm.php">Manifest Perbaikan</a>
                            <a href="detail_perbaikan_pm.php">Detail Perbaikan</a>
                            <a href="goods_form_pm.php" class="active">Goods Form</a>
                            <a href="laporan_seluruhpm.php">Laporan Keseluruhan</a>
                            <a href="logout.php">Logout</a>
              </div>

              <div class="main">
                            <h2>Data Goods Form Project Manager</h2>
                            <p>Berikut adalah data Goods Form yang diinput oleh admin.</p>

                            <table>
                                          <thead>
                                                        <tr>
                                                                      <th>No</th>
                                                                      <th>Nomor Goods Form</th>
                                                                      <th>Tanggal</th>
                                                                      <th>Chargo Manifest</th>
                                                                      <th>Name of Goods</th>
                                                                      <th>Qty</th>
                                                                      <th>Remarks</th>
                                                        </tr>
                                          </thead>
                                          <tbody>
                                                        <?php
                                                        if ($result && $result->num_rows > 0):
                                                                      $no = 1;
                                                                      $last_nomor = '';
                                                                      while ($row = $result->fetch_assoc()):
                                                                                    $curr_nomor = $row['nomor_goods_form'];
                                                                                    if ($curr_nomor !== $last_nomor): ?>
                                                                                                  <tr class="header-row">
                                                                                                                <td><?= $no++ ?></td>
                                                                                                                <td><?= htmlspecialchars($row['nomor_goods_form']) ?></td>
                                                                                                                <td><?= htmlspecialchars($row['tanggal_goods_form']) ?></td>
                                                                                                                <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                                                                                                                <td colspan="3"></td>
                                                                                                  </tr>
                                                                                    <?php endif; ?>
                                                                                    <tr>
                                                                                                  <td></td>
                                                                                                  <td></td>
                                                                                                  <td></td>
                                                                                                  <td></td>
                                                                                                  <td><?= htmlspecialchars($row['name_of_goods']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['qty']) ?></td>
                                                                                                  <td><?= htmlspecialchars($row['remarks']) ?></td>
                                                                                    </tr>
                                                                                    <?php $last_nomor = $curr_nomor; ?>
                                                                      <?php endwhile;
                                                        else: ?>
                                                                      <tr>
                                                                                    <td colspan="7">Tidak ada data Goods Form.</td>
                                                                      </tr>
                                                        <?php endif; ?>
                                          </tbody>
                            </table>
              </div>

</body>

</html>