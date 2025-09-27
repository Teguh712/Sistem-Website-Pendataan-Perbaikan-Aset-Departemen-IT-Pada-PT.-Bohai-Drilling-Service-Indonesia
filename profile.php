<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin') {
              header("Location: login.php");
              exit();
}

$username = $_SESSION['user']['username'];
$pesan = "";

// Ambil data pengguna
$stmt = $conn->prepare("SELECT u.nomor_badge, k.nama_karyawan, u.plain_password 
                        FROM users u 
                        LEFT JOIN karyawann k ON u.nomor_badge = k.nomor_badge 
                        WHERE u.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($nomor_badge, $nama_karyawan, $password_lama_plain);
$stmt->fetch();
$stmt->close();

// Handle update password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $input_lama = $_POST['password_lama'] ?? '';
              $password_baru = $_POST['password_baru'] ?? '';
              $konfirmasi = $_POST['password_konfirmasi'] ?? '';

              if ($input_lama !== $password_lama_plain) {
                            $pesan = "<p style='color:red;'>Password lama tidak cocok.</p>";
              } elseif ($password_baru !== $konfirmasi) {
                            $pesan = "<p style='color:red;'>Konfirmasi password tidak cocok.</p>";
              } elseif (strlen($password_baru) < 6) {
                            $pesan = "<p style='color:red;'>Password minimal 6 karakter.</p>";
              } else {
                            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("UPDATE users SET password = ?, plain_password = ? WHERE username = ?");
                            $stmt->bind_param("sss", $hash, $password_baru, $username);
                            if ($stmt->execute()) {
                                          $pesan = "<p style='color:green;'>Password berhasil diperbarui.</p>";
                            } else {
                                          $pesan = "<p style='color:red;'>Gagal memperbarui password.</p>";
                            }
                            $stmt->close();
              }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Profil - Ganti Password</title>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
              <style>
                            body {
                                          font-family: 'Segoe UI', sans-serif;
                                          background-color: #f0f2f5;
                                          margin: 0;
                                          display: flex;
                            }

                            .sidebar {
                                          position: fixed;
                                          top: 0;
                                          left: 0;
                                          width: 240px;
                                          height: 100vh;
                                          background-color: #2c3e50;
                                          color: white;
                                          padding: 20px;
                                          box-sizing: border-box;
                            }

                            .sidebar h2 {
                                          font-size: 22px;
                                          text-align: center;
                                          margin-bottom: 30px;
                                          font-weight: bold;
                            }

                            .sidebar a {
                                          display: block;
                                          color: white;
                                          text-decoration: none;
                                          padding: 12px 20px;
                                          margin: 8px 0;
                                          border-radius: 6px;
                                          transition: background-color 0.3s ease;
                                          font-size: 14px;
                            }

                            .sidebar a i {
                                          margin-right: 8px;
                            }

                            .sidebar a:hover,
                            .sidebar a.active {
                                          background-color: #34495e;
                            }

                            .main {
                                          margin-left: 240px;
                                          padding: 30px;
                                          width: 100%;
                            }

                            form {
                                          background: #fff;
                                          padding: 20px;
                                          max-width: 500px;
                                          border-radius: 8px;
                                          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            }

                            h2 {
                                          margin-bottom: 10px;
                            }

                            label {
                                          font-weight: 600;
                                          display: block;
                                          margin-top: 14px;
                            }

                            input[type="password"] {
                                          width: 100%;
                                          padding: 10px;
                                          margin-top: 6px;
                                          border-radius: 5px;
                                          border: 1px solid #ccc;
                            }

                            button {
                                          margin-top: 20px;
                                          padding: 10px 18px;
                                          background-color: #2980b9;
                                          color: white;
                                          border: none;
                                          border-radius: 6px;
                                          cursor: pointer;
                            }

                            button:hover {
                                          background-color: #3498db;
                            }

                            .pesan {
                                          margin-top: 20px;
                            }
              </style>
</head>

<body>
              <div class="sidebar">
                            <h2>Karyawan</h2>
                            <a href="dashboard_staff.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

                            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profil</a>
                            <a href="detail_perbaikan.php"><i class="fas fa-file-alt"></i> Ajukan Perbaikan</a>
                            <a href="status_perbaikan.php"><i class="fas fa-clipboard-list"></i> Status Perbaikan</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
              </div>

              <div class="main">
                            <h2>Profil Pengguna</h2>
                            <p><strong>Nama:</strong> <?= htmlspecialchars($nama_karyawan ?: $username) ?></p>

                            <form method="POST">
                                          <?= $pesan ?>
                                          <label for="password_lama">Password Lama:</label>
                                          <input type="password" name="password_lama" id="password_lama" required>

                                          <label for="password_baru">Password Baru:</label>
                                          <input type="password" name="password_baru" id="password_baru" required>

                                          <label for="password_konfirmasi">Konfirmasi Password Baru:</label>
                                          <input type="password" name="password_konfirmasi" id="password_konfirmasi" required>

                                          <button type="submit">Simpan Password</button>
                            </form>
              </div>
</body>

</html>