<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user']['role'];
$is_admin = ($role === 'admin');

$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

// Hapus data
if (isset($_GET['hapus']) && $is_admin) {
    $hapus_nomor = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM goods_form WHERE nomor_goods_form = ?");
    $stmt->bind_param("s", $hapus_nomor);
    if ($stmt->execute()) {
        $_SESSION['pesan'] = "<p style='color:green;'>Data dengan nomor $hapus_nomor berhasil dihapus.</p>";
    } else {
        $_SESSION['pesan'] = "<p style='color:red;'>Gagal menghapus data: {$stmt->error}</p>";
    }
    $stmt->close();
    header("Location: goods_form.php");
    exit();
}

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $nomor = $_POST['nomor_goods_form'] ?? '';
    $tanggal = $_POST['tanggal_goods_form'] ?? '';
    $chargo = $_POST['chargo_manifest'] ?? '';
    $nama = $_POST['name_of_goods'] ?? '';
    $kategori = $_POST['kategori_aset'] ?? '';
    $deskripsi = $_POST['description'] ?? '';
    $qty = intval($_POST['qty'] ?? 0);
    $remarks = $_POST['remarks'] ?? '';

    if (!$nomor || !$tanggal || !$chargo || !$nama || !$deskripsi || $qty <= 0) {
        $_SESSION['pesan'] = "<p style='color:red;'>Semua field wajib diisi dan qty harus lebih dari 0.</p>";
    } else {
        // Cek jika nomor goods form sudah dipakai untuk chargo berbeda
        $cek_chargo = $conn->prepare("SELECT COUNT(*) FROM goods_form WHERE nomor_goods_form = ? AND chargo_manifest != ?");
        $cek_chargo->bind_param("ss", $nomor, $chargo);
        $cek_chargo->execute();
        $cek_chargo->bind_result($salah_manifest);
        $cek_chargo->fetch();
        $cek_chargo->close();

        if ($salah_manifest > 0) {
            $_SESSION['pesan'] = "<p style='color:red;'>Nomor Goods Form <b>$nomor</b> sudah digunakan untuk Chargo Manifest lain.</p>";
        } else {
            // Cek apakah data persis sudah ada
            $cek = $conn->prepare("SELECT COUNT(*) FROM goods_form WHERE nomor_goods_form = ? AND chargo_manifest = ? AND name_of_goods = ? AND kategori_aset = ?");
            $cek->bind_param("ssss", $nomor, $chargo, $nama, $kategori);
            $cek->execute();
            $cek->bind_result($jumlah);
            $cek->fetch();
            $cek->close();

            if ($jumlah > 0) {
                $_SESSION['pesan'] = "<p style='color:red;'>Kombinasi ini sudah tercatat dalam goods form.</p>";
            } else {
                $stmt = $conn->prepare("INSERT INTO goods_form 
                    (nomor_goods_form, tanggal_goods_form, chargo_manifest, name_of_goods, kategori_aset, description, qty, remarks) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssis", $nomor, $tanggal, $chargo, $nama, $kategori, $deskripsi, $qty, $remarks);
                if ($stmt->execute()) {
                    $_SESSION['pesan'] = "<p style='color:green;'>Data berhasil disimpan.</p>";
                } else {
                    $_SESSION['pesan'] = "<p style='color:red;'>Gagal menyimpan data: {$stmt->error}</p>";
                }
                $stmt->close();
            }
        }
    }

    header("Location: goods_form.php");
    exit();
}

// Ambil data Chargo yang belum masuk goods_form
$chargo_result = $conn->query("
    SELECT dp.chargo_manifest, dp.nama_aset, dp.kategori_aset
    FROM detail_perbaikan dp
    WHERE NOT EXISTS (
        SELECT 1 FROM goods_form gf
        WHERE gf.chargo_manifest = dp.chargo_manifest
          AND gf.name_of_goods = dp.nama_aset
          AND gf.kategori_aset = dp.kategori_aset
    )
    ORDER BY dp.chargo_manifest DESC
");

$chargo_options = [];
if ($chargo_result) {
    while ($row = $chargo_result->fetch_assoc()) {
        $chargo_options[] = $row;
    }
}

$query = "SELECT * FROM goods_form ORDER BY nomor_goods_form DESC, id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Goods Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 240px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
        }

        .main {
            margin-left: 260px;
            padding: 30px;
            width: calc(100% - 260px);
        }

        form {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 0 5px #ccc;
        }

        form input,
        form select,
        form textarea,
        form button {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 5px #bbb;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        .btn-cetak {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #3498db;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        td.aksi-icons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        td.aksi-icons a i {
            font-size: 20px;
            transition: transform 0.2s ease;
        }

        td.aksi-icons a:hover i {
            transform: scale(1.2);
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
        <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
        <a href="detail_perbaikan.php"><i class="fas fa-clipboard-list"></i> Detail Perbaikan</a>
        <a href="goods_form.php" class="active"><i class="fas fa-clipboard-list"></i> Goods Form</a>
        <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan Keseluruhan</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h2>Goods Form</h2>
        <?= $pesan ?>

        <?php if ($is_admin): ?>
            <form method="POST">
                <label>Nomor Goods Form:</label>
                <input type="text" name="nomor_goods_form" required>
                <label>Tanggal:</label>
                <input type="date" name="tanggal_goods_form" value="<?= date('Y-m-d') ?>" required>
                <label>Chargo Manifest:</label>
                <select name="chargo_manifest" id="chargo_manifest" required>
                    <option value="">-- Pilih Chargo --</option>
                    <?php foreach ($chargo_options as $opt): ?>
                        <option value="<?= $opt['chargo_manifest'] ?>" data-nama="<?= htmlspecialchars($opt['nama_aset']) ?>" data-kategori="<?= htmlspecialchars($opt['kategori_aset']) ?>">
                            <?= $opt['chargo_manifest'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Name of Goods:</label>
                <input type="text" name="name_of_goods" id="name_of_goods" required>
                <label>Kategori Aset:</label>
                <input type="text" name="kategori_aset" id="kategori_aset" readonly required>
                <label>Description:</label>
                <textarea name="description" required></textarea>
                <label>Quantity:</label>
                <input type="number" name="qty" min="1" required>
                <label>Remarks:</label>
                <textarea name="remarks"></textarea>
                <button type="submit">Simpan</button>
            </form>
        <?php endif; ?>

        <a href="cetak_goods_form.php" target="_blank" class="btn-cetak"><i class="fas fa-print"></i> Cetak PDF</a>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Goods Form</th>
                    <th>Tanggal</th>
                    <th>Chargo Manifest</th>
                    <th>Name of Goods</th>
                    <th>Kategori</th>
                    <th>Qty</th>
                    <th>Description</th>
                    <th>Remarks</th>
                    <?php if ($is_admin): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0):
                    $no = 1;
                    $last_nomor = null;
                    while ($row = $result->fetch_assoc()):
                        $curr_nomor = $row['nomor_goods_form'];
                        if ($curr_nomor !== $last_nomor): ?>
                            <tr style="background:#f8f8f8; font-weight:bold;">
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($curr_nomor) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_goods_form']) ?></td>
                                <td><?= htmlspecialchars($row['chargo_manifest']) ?></td>
                                <td colspan="5"></td>
                                <?php if ($is_admin): ?>
                                    <td class="aksi-icons">
                                        <a href="cetak_per_goods_form.php?nomor=<?= urlencode($curr_nomor) ?>" target="_blank" style="color:#2980b9;" title="Cetak">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="?hapus=<?= urlencode($curr_nomor) ?>" onclick="return confirm('Yakin hapus semua data dengan nomor ini?')" style="color:#c0392b;" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>


                                <?php endif; ?>

                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?= htmlspecialchars($row['name_of_goods']) ?></td>
                            <td><?= htmlspecialchars($row['kategori_aset']) ?></td>
                            <td><?= htmlspecialchars($row['qty']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['remarks']) ?></td>
                            <?php if ($is_admin): ?><td></td><?php endif; ?>
                        </tr>
                        <?php $last_nomor = $curr_nomor; ?>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="<?= $is_admin ? '10' : '9' ?>">Belum ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('chargo_manifest')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('name_of_goods').value = selected.getAttribute('data-nama') || '';
            document.getElementById('kategori_aset').value = selected.getAttribute('data-kategori') || '';
        });
    </script>
</body>

</html>