<?php
include 'config.php';
?>
<h2>Laporan Detail Perbaikan Aset</h2>

<form method="GET">
              <label>Filter:</label>
              <select name="filter" id="filter" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="tanggal" <?= ($_GET['filter'] ?? '') == 'tanggal' ? 'selected' : '' ?>>Per Tanggal</option>
                            <option value="bulan" <?= ($_GET['filter'] ?? '') == 'bulan' ? 'selected' : '' ?>>Per Bulan</option>
                            <option value="tahun" <?= ($_GET['filter'] ?? '') == 'tahun' ? 'selected' : '' ?>>Per Tahun</option>
              </select>

              <?php if (($_GET['filter'] ?? '') == 'tanggal'): ?>
                            <input type="date" name="tanggal" value="<?= $_GET['tanggal'] ?? '' ?>" onchange="this.form.submit()">
              <?php elseif (($_GET['filter'] ?? '') == 'bulan'): ?>
                            <input type="month" name="bulan" value="<?= $_GET['bulan'] ?? '' ?>" onchange="this.form.submit()">
              <?php elseif (($_GET['filter'] ?? '') == 'tahun'): ?>
                            <input type="number" name="tahun" placeholder="Tahun" value="<?= $_GET['tahun'] ?? '' ?>" onchange="this.form.submit()">
              <?php endif; ?>
</form>

<table border="1" cellpadding="5" cellspacing="0">
              <tr>
                            <th>Tanggal</th>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Departemen</th>
                            <th>Chargo Manifest</th>
                            <th>Status</th>
                            <th>Keterangan</th>
              </tr>
              <?php
              $query = "SELECT * FROM laporan_perbaikan";
              if (isset($_GET['filter'])) {
                            if ($_GET['filter'] == 'tanggal' && !empty($_GET['tanggal'])) {
                                          $tanggal = $_GET['tanggal'];
                                          $query .= " WHERE tanggal = '$tanggal'";
                            } elseif ($_GET['filter'] == 'bulan' && !empty($_GET['bulan'])) {
                                          list($tahun, $bulan) = explode('-', $_GET['bulan']);
                                          $query .= " WHERE bulan = '$bulan' AND tahun = '$tahun'";
                            } elseif ($_GET['filter'] == 'tahun' && !empty($_GET['tahun'])) {
                                          $tahun = $_GET['tahun'];
                                          $query .= " WHERE tahun = '$tahun'";
                            }
              }
              $query .= " ORDER BY tanggal DESC";
              $result = $conn->query($query);
              while ($row = $result->fetch_assoc()):
              ?>
                            <tr>
                                          <td><?= $row['tanggal'] ?></td>
                                          <td><?= $row['kode_aset'] ?></td>
                                          <td><?= $row['nama_aset'] ?></td>
                                          <td><?= $row['kategori_aset'] ?></td>
                                          <td><?= $row['departemen'] ?></td>
                                          <td><?= $row['chargo_manifest'] ?></td>
                                          <td><?= $row['status'] ?></td>
                                          <td><?= $row['keterangan'] ?></td>
                            </tr>
              <?php endwhile; ?>
</table>