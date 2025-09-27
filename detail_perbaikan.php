    <?php
    session_start();
    include 'config.php';

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user']['id'];
    $role = $_SESSION['user']['role'];
    $is_admin = ($role === 'admin');

    $pesan = "";
    if (isset($_SESSION['success_message'])) {
        $pesan = "<p style='color:green;'>" . $_SESSION['success_message'] . "</p>";
        unset($_SESSION['success_message']);
    }

    // Ambil data aset
    $aset_result = $conn->query("SELECT a.kode_aset, a.nama_aset, k.nama_kategori 
    FROM assetss a
    LEFT JOIN kategori_aset k ON a.kategori_id = k.id");
    $aset_list = [];
    while ($row = $aset_result->fetch_assoc()) {
        $aset_list[] = $row;
    }


    // Auto generate Chargo Manifest
    $prefix = 'CM-';
    $start_number = 1900;

    // Ambil nomor terbesar dari chargo_manifest yang sudah digunakan (tidak NULL/kosong)
    $query = "SELECT MAX(CAST(SUBSTRING(chargo_manifest, 4) AS UNSIGNED)) AS max_chargo 
          FROM detail_perbaikan 
          WHERE chargo_manifest LIKE 'CM-%' AND chargo_manifest IS NOT NULL";

    $result_chargo = $conn->query($query);

    if ($result_chargo && $row = $result_chargo->fetch_assoc()) {
        $last_chargo = intval($row['max_chargo']);
        // Pastikan minimal dimulai dari $start_number
        $next_number = ($last_chargo >= $start_number) ? $last_chargo + 1 : $start_number;
    } else {
        $next_number = $start_number;
    }

    // Buat default chargo_manifest
    $default_chargo = $prefix . $next_number;




    // Simpan data (staff)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_admin && !isset($_POST['update_status'])) {
        $tanggal = $_POST['tanggal_pengajuan'] ?? '';
        $chargo = $default_chargo; // Gunakan yang otomatis
        $qty = intval($_POST['quantity'] ?? 0);
        $kode_aset = trim($_POST['kode_aset'] ?? '');
        $nama_aset = trim($_POST['nama_aset'] ?? '');
        $kategori_aset = trim($_POST['kategori_aset'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($tanggal && $chargo && $qty > 0 && $deskripsi && $kode_aset && $nama_aset && $kategori_aset) {
            $stmt = $conn->prepare("INSERT INTO detail_perbaikan 
            (tanggal_pengajuan, chargo_manifest, quantity, kode_aset, nama_aset, kategori_aset, deskripsi, user_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')");
            $stmt->bind_param("ssissssi", $tanggal, $chargo, $qty, $kode_aset, $nama_aset, $kategori_aset, $deskripsi, $user_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data berhasil disimpan.";
                header("Location: detail_perbaikan.php");
                exit();
            } else {
                $pesan = "<p style='color:red;'>Gagal menyimpan data: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $pesan = "<p style='color:red;'>Semua field wajib diisi dan quantity harus lebih dari 0.</p>";
        }
    }

    // Admin update status atau hapus
    if ($is_admin) {

        if (isset($_POST['simpan'])) {
            $tanggal = $_POST['tanggal'];
            $bulan = date('m', strtotime($tanggal));
            $tahun = date('Y', strtotime($tanggal));
            $kode_aset = $_POST['kode_aset'];
            $nama_aset = $_POST['nama_aset'];
            $kategori_aset = $_POST['kategori_aset'];
            $departemen = $_POST['departemen'];
            $chargo_manifest = $_POST['chargo_manifest'];
            $status = $_POST['status'];
            $keterangan = $_POST['keterangan'];

            // Simpan ke tabel detail_perbaikan
            $conn->query("INSERT INTO detail_perbaikan (tanggal, kode_aset, nama_aset, kategori_aset, departemen, chargo_manifest, status, keterangan)
                  VALUES ('$tanggal', '$kode_aset', '$nama_aset', '$kategori_aset', '$departemen', '$chargo_manifest', '$status', '$keterangan')");

            // Simpan juga ke tabel laporan_perbaikan (arsip)
            $conn->query("INSERT INTO laporan_perbaikan (tanggal, bulan, tahun, kode_aset, nama_aset, kategori_aset, departemen, chargo_manifest, status, keterangan)
                  VALUES ('$tanggal', '$bulan', '$tahun', '$kode_aset', '$nama_aset', '$kategori_aset', '$departemen', '$chargo_manifest', '$status', '$keterangan')");
        }

        if (isset($_POST['update_status'])) {
            $chargo_manifest = $_POST['chargo_manifest'] ?? '';
            $new_status = $_POST['status'] ?? '';

            $stmt = $conn->prepare("UPDATE detail_perbaikan SET status = ? WHERE chargo_manifest = ?");
            $stmt->bind_param("ss", $new_status, $chargo_manifest);
            $stmt->execute();
            $stmt->close();
        }

        if (isset($_POST['hapus'])) {
            $chargo_manifest = $_POST['chargo_manifest'] ?? '';
            $stmt = $conn->prepare("UPDATE detail_perbaikan SET deleted_at = NOW() WHERE chargo_manifest = ?");
            $stmt->bind_param("s", $chargo_manifest);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Ambil data perbaikan
    if ($is_admin) {
        $query = "SELECT dp.*, u.username FROM detail_perbaikan dp 
    LEFT JOIN users u ON dp.user_id = u.id 
    WHERE dp.deleted_at IS NULL";


        // Filter berdasarkan tanggal
        if (!empty($_GET['filter_tanggal'])) {
            $tanggal = $_GET['filter_tanggal'];
            $query .= " AND dp.tanggal_pengajuan = '$tanggal'";
        }

        // Filter berdasarkan bulan dan/atau tahun
        if (!empty($_GET['filter_bulan'])) {
            $bulan = $_GET['filter_bulan'];
            $query .= " AND MONTH(dp.tanggal_pengajuan) = $bulan";
        }
        if (!empty($_GET['filter_tahun'])) {
            $tahun = $_GET['filter_tahun'];
            $query .= " AND YEAR(dp.tanggal_pengajuan) = $tahun";
        }

        $query .= " ORDER BY dp.tanggal_pengajuan DESC";
        $result = $conn->query($query);
    } else {
        $query = "SELECT * FROM detail_perbaikan WHERE user_id = ? AND deleted_at IS NULL";


        if (!empty($_GET['filter_tanggal'])) {
            $query .= " AND tanggal_pengajuan = ?";
        }
        if (!empty($_GET['filter_bulan'])) {
            $query .= " AND MONTH(tanggal_pengajuan) = ?";
        }
        if (!empty($_GET['filter_tahun'])) {
            $query .= " AND YEAR(tanggal_pengajuan) = ?";
        }

        $query .= " ORDER BY tanggal_pengajuan DESC";

        $stmt = $conn->prepare($query);

        // Bind parameter sesuai filter yang digunakan
        $params = [$user_id];
        $types = "i";

        if (!empty($_GET['filter_tanggal'])) {
            $params[] = $_GET['filter_tanggal'];
            $types .= "s";
        }
        if (!empty($_GET['filter_bulan'])) {
            $params[] = $_GET['filter_bulan'];
            $types .= "i";
        }
        if (!empty($_GET['filter_tahun'])) {
            $params[] = $_GET['filter_tahun'];
            $types .= "i";
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Detail Perbaikan</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
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
                text-align: center;
                margin-bottom: 30px;
            }

            .sidebar a {
                display: flex;
                align-items: center;
                padding: 10px;
                color: white;
                text-decoration: none;
                margin-bottom: 10px;
                border-radius: 6px;
            }

            .sidebar a i {
                margin-right: 10px;
            }

            .sidebar a:hover,
            .sidebar a.active {
                background-color: #34495e;
            }

            .main {
                margin-left: 260px;
                padding: 30px;
                width: calc(100% - 260px);
                overflow-x: auto;
            }

            form {
                background: white;
                padding: 20px;
                margin-bottom: 30px;
                border-radius: 8px;
                box-shadow: 0 0 5px #ccc;
            }

            form label {
                display: block;
                margin-bottom: 6px;
                font-weight: bold;
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

            .form-status {
                display: flex;
                gap: 5px;
            }

            @media screen and (max-width: 768px) {
                .main {
                    margin-left: 0;
                    width: 100%;
                }

                .sidebar {
                    position: relative;
                    width: 100%;
                    height: auto;
                }

                table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }
            }
        </style>
    </head>

    <body>
        <div class="sidebar">
            <h2><?= $is_admin ? 'IT Support' : 'Karyawan' ?></h2>
            <?php if ($is_admin): ?>
                <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="kelola_aset.php"><i class="fas fa-box"></i> Kelola Aset</a>
                <a href="kelola_akun.php"><i class="fas fa-users"></i> Kelola Akun</a>
                <a href="data_karyawan.php"><i class="fas fa-building"></i> Karyawan</a>
                <a href="permintaan_perbaikan.php"><i class="fas fa-tools"></i> Manifest Perbaikan</a>
                <a href="detail_perbaikan.php"><i class="fas fa-tools"></i> Detail Perbaikan</a>


            <?php else: ?>
                <a href="dashboard_staff.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                <a href="detail_perbaikan.php" class="active"><i class="fas fa-file-alt"></i> Ajukan Perbaikan</a>
                <a href="status_perbaikan.php"><i class="fas fa-clipboard-list"></i> Status Perbaikan</a>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <a href="goods_form.php"><i class="fas fa-clipboard-list"></i> Goods Form</a>
                <a href="laporan_keseluruhan.php"><i class="fas fa-file-alt"></i> Laporan Keseluruhan</a>

            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="main">
            <h2>Detail Perbaikan Aset</h2>
            <?= $pesan ?>

            <!-- Form input khusus untuk STAFF -->
            <?php if (!$is_admin): ?>
                <form method="POST">
                    <label>Tanggal Pengajuan:</label>
                    <input type="date" name="tanggal_pengajuan" required value="<?= date('Y-m-d') ?>">

                    <label>Nomor Chargo Manifest:</label>
                    <input type="text" name="chargo_manifest" required value="<?= htmlspecialchars($default_chargo) ?>">

                    <label>Quantity:</label>
                    <input type="number" name="quantity" min="1" required>

                    <label>Nama Aset:</label>
                    <select name="nama_aset" id="nama_aset" required>
                        <option value="">-- Pilih Nama Aset --</option>
                        <?php foreach ($aset_list as $aset): ?>
                            <option value="<?= $aset['nama_aset'] ?>"
                                data-kode="<?= $aset['kode_aset'] ?>"
                                data-kategori="<?= $aset['nama_kategori'] ?>">
                                <?= $aset['nama_aset'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Kode Aset:</label>
                    <input type="text" name="kode_aset" id="kode_aset" readonly required>

                    <label>Kategori Aset:</label>
                    <input type="text" name="kategori_aset" id="kategori_aset" readonly required>




                    <label>Deskripsi:</label>
                    <textarea name="deskripsi" required></textarea>

                    <button type="submit">Simpan</button>
                </form>
            <?php endif; ?>

            <!-- Cetak PDF untuk admin -->
            <?php if ($is_admin): ?>
                <form action="cetak_detail_perbaikan.php" method="POST" target="_blank" style="margin-bottom: 20px;">
                    <button type="submit" style="padding:10px 20px; background-color:#2c3e50; color:white; border:none; border-radius:5px;">
                        <i class="fas fa-file-pdf"></i> Unduh PDF
                    </button>
                </form>
            <?php endif; ?>

            <!-- Filter Laporan -->
            <form method="GET" style="margin-bottom: 20px; display:flex; gap:10px; flex-wrap:wrap;">
                <label for="filter_tanggal">Tanggal:</label>
                <input type="date" name="filter_tanggal" id="filter_tanggal" value="<?= $_GET['filter_tanggal'] ?? '' ?>">

                <label for="filter_bulan">Bulan:</label>
                <select name="filter_bulan" id="filter_bulan">
                    <option value="">-- Pilih Bulan --</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == $m) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <label for="filter_tahun">Tahun:</label>
                <select name="filter_tahun" id="filter_tahun">
                    <option value="">-- Pilih Tahun --</option>
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= (isset($_GET['filter_tahun']) && $_GET['filter_tahun'] == $y) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <button type="submit">Tampilkan</button>
            </form>

            <!-- Tabel data -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <?php if (!$is_admin): ?><th>Tanggal</th><?php endif; ?>
                        <th>Chargo</th>
                        <th>Qty</th>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <?php if ($is_admin): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $shown_manifest = [];
                    while ($row = $result->fetch_assoc()):
                        $chargo = $row['chargo_manifest'];
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <?php if (!$is_admin): ?><td><?= $row['tanggal_pengajuan'] ?></td><?php endif; ?>
                            <td><?= $chargo ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= $row['kode_aset'] ?></td>
                            <td><?= $row['nama_aset'] ?></td>
                            <td><?= $row['kategori_aset'] ?? '-' ?></td>
                            <td><?= $row['deskripsi'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <?php if (!in_array($chargo, $shown_manifest)): ?>
                                        <form method="POST" class="form-status" onsubmit="return confirm('Yakin ingin mengubah atau menghapus semua data dengan Chargo ini?')">
                                            <input type="hidden" name="chargo_manifest" value="<?= $chargo ?>">
                                            <select name="status">
                                                <option value="Menunggu" <?= $row['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                                <option value="Diproses" <?= $row['status'] === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                                <option value="Selesai" <?= $row['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                            </select>
                                            <button type="submit" name="update_status">OK</button>
                                            <button type="submit" name="hapus" style="background:red;color:white;">Hapus</button>
                                        </form>

                                        <!-- Tombol cetak langsung -->
                                        <form action="cetak_perbaikan_cm.php" method="GET" target="_blank" style="margin-top:5px;">
                                            <input type="hidden" name="chargo_manifest" value="<?= $chargo ?>">
                                            <button type="submit" style="padding:5px 10px; background:#2c3e50; color:white; border:none; border-radius:4px;">
                                                <i class="fas fa-file-pdf"></i> Cetak
                                            </button>
                                        </form>

                                        <?php $shown_manifest[] = $chargo; ?>
                                    <?php else: echo '-'; ?>
                                    <?php endif; ?>



                                    <?php $shown_manifest[] = $chargo; ?>
                                <?php else: echo '-';
                            endif; ?>
                                </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('nama_aset')?.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                document.getElementById('kode_aset').value = selected.getAttribute('data-kode') || '';
                document.getElementById('kategori_aset').value = selected.getAttribute('data-kategori') || '';
            });
        </script>

    </body>

    </html>