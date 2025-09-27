<?php
session_start();
include 'config.php';

$error = '';
$success = '';

// Daftar role yang diizinkan
$allowed_roles = [
              'admin',
              'project_manager',
              'staff_rig_18',
              'staff_rig_19',
              'staff_rig_21',
              'staff_rig_27',
              'staff_rig_28',
              'staff_rig_29',
              'staff_hrd',
              'staff_hse',
              'staff_maintenance',
              'staff_transport',
              'staff_logistic'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $username = trim($_POST['username']);
              $password = trim($_POST['password']);
              $role = trim(strtolower(str_replace(' ', '_', $_POST['role']))); // normalisasi

              // Validasi input kosong
              if (empty($username) || empty($password) || empty($role)) {
                            $error = "Semua field wajib diisi.";
              } elseif (!in_array($role, $allowed_roles)) {
                            $error = "Role tidak valid.";
              } else {
                            // Cek apakah username sudah digunakan
                            $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
                            $check->bind_param("s", $username);
                            $check->execute();
                            $check->store_result();

                            if ($check->num_rows > 0) {
                                          $error = "Username sudah digunakan.";
                            } else {
                                          // Simpan user baru
                                          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                          $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                                          $stmt->bind_param("sss", $username, $hashed_password, $role);

                                          if ($stmt->execute()) {
                                                        $success = "Registrasi berhasil. Silakan login.";
                                          } else {
                                                        $error = "Gagal mendaftar. Coba lagi.";
                                          }

                                          $stmt->close();
                            }

                            $check->close();
              }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Register - Sistem Aset IT</title>
              <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
              <style>
                            body {
                                          font-family: 'Inter', sans-serif;
                                          background: linear-gradient(135deg, #e0f7fa, #80deea);
                                          margin: 0;
                                          height: 100vh;
                                          display: flex;
                                          justify-content: center;
                                          align-items: center;
                            }

                            .register-container {
                                          background: #fff;
                                          padding: 30px;
                                          border-radius: 12px;
                                          box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                                          width: 100%;
                                          max-width: 400px;
                            }

                            h2 {
                                          text-align: center;
                                          margin-bottom: 25px;
                                          color: #333;
                            }

                            label {
                                          display: block;
                                          margin-bottom: 6px;
                                          font-size: 14px;
                            }

                            input,
                            select {
                                          width: 100%;
                                          padding: 10px;
                                          margin-bottom: 15px;
                                          border: 1px solid #ccc;
                                          border-radius: 6px;
                            }

                            button {
                                          width: 100%;
                                          padding: 12px;
                                          background-color: #2c3e50;
                                          border: none;
                                          border-radius: 6px;
                                          color: #fff;
                                          font-size: 16px;
                                          font-weight: 600;
                                          cursor: pointer;
                            }

                            button:hover {
                                          background-color: #4cae4c;
                            }

                            .error {
                                          color: red;
                                          font-size: 14px;
                                          text-align: center;
                            }

                            .success {
                                          color: green;
                                          font-size: 14px;
                                          text-align: center;
                            }

                            .login-link {
                                          text-align: center;
                                          margin-top: 15px;
                                          font-size: 14px;
                            }

                            .login-link a {
                                          color: #5cb85c;
                                          text-decoration: none;
                                          font-weight: 500;
                            }
              </style>
</head>

<body>
              <div class="register-container">
                            <h2>Register</h2>
                            <form method="POST">
                                          <label for="username">Username</label>
                                          <input type="text" name="username" id="username" required>

                                          <label for="password">Password</label>
                                          <input type="password" name="password" id="password" required>

                                          <label for="role">Role</label>
                                          <select name="role" id="role" required>
                                                        <option value="">-- Pilih Role --</option>
                                                        <?php foreach ($allowed_roles as $r): ?>
                                                                      <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r))) ?></option>
                                                        <?php endforeach; ?>
                                          </select>

                                          <button type="submit">Daftar</button>
                            </form>

                            <?php if (!empty($error)): ?>
                                          <p class="error"><?= htmlspecialchars($error) ?></p>
                            <?php elseif (!empty($success)): ?>
                                          <p class="success"><?= htmlspecialchars($success) ?></p>
                            <?php endif; ?>

                            <p class="login-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
              </div>
</body>

</html>