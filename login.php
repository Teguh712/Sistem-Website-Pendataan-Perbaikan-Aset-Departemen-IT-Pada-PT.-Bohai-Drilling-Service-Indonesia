<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $username = trim($_POST['username']);
              $password = trim($_POST['password']);

              if (empty($username) || empty($password)) {
                            $error = "Username dan password wajib diisi.";
              } else {
                            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows === 1) {
                                          $user = $result->fetch_assoc();

                                          if (password_verify($password, $user['password'])) {
                                                        $_SESSION['user'] = $user;
                                                        $role = strtolower($user['role']);

                                                        if ($role === 'admin') {
                                                                      header("Location: dashboard_admin.php");
                                                        } elseif ($role === 'project_manager') {
                                                                      header("Location: dashboard_pm.php");
                                                        } else {
                                                                      header("Location: dashboard_staff.php");
                                                        }
                                                        exit();
                                          } else {
                                                        $error = "Password salah.";
                                          }
                            } else {
                                          $error = "Username tidak ditemukan.";
                            }

                            $stmt->close();
              }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
              <meta charset="UTF-8">
              <title>Login - Sistem Aset IT</title>
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
              <style>
                            * {
                                          box-sizing: border-box;
                            }

                            body {
                                          font-family: 'Inter', sans-serif;
                                          background: linear-gradient(135deg, #e0f7fa, #80deea);
                                          margin: 0;
                                          display: flex;
                                          height: 100vh;
                                          align-items: center;
                                          justify-content: center;
                            }

                            .login-container {
                                          background-color: #fff;
                                          padding: 30px 25px;
                                          border-radius: 12px;
                                          box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                                          max-width: 350px;
                                          width: 100%;
                                          text-align: center;
                            }

                            .logo {
                                          width: 120px;
                                          margin-bottom: 20px;
                            }

                            h2 {
                                          color: #333;
                                          margin-bottom: 20px;
                                          font-weight: 600;
                            }

                            form {
                                          text-align: left;
                            }

                            label {
                                          display: block;
                                          margin-bottom: 6px;
                                          font-size: 14px;
                                          color: #444;
                            }

                            input[type="text"],
                            input[type="password"] {
                                          width: 100%;
                                          padding: 10px 12px;
                                          margin-bottom: 15px;
                                          border: 1px solid #ccc;
                                          border-radius: 6px;
                                          font-size: 14px;
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
                                          margin-top: 10px;
                                          color: red;
                                          font-size: 14px;
                                          text-align: center;
                            }
              </style>
</head>

<body>
              <div class="login-container">
                            <img src="images/bdsi.png" alt="Logo" class="logo">
                            <h2>Login</h2>

                            <form method="POST">
                                          <label for="username">Username</label>
                                          <input type="text" name="username" id="username" required>

                                          <label for="password">Password</label>
                                          <input type="password" name="password" id="password" required>

                                          <button type="submit">Login</button>
                            </form>

                            <?php if (!empty($error)): ?>
                                          <p class="error"><?= htmlspecialchars($error) ?></p>
                            <?php endif; ?>
              </div>
</body>

</html>