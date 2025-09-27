<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
              header("Location: login.php");
              exit();
}
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $id = $_POST['id'];
              $status = $_POST['status'];

              // Validasi nilai status
              $allowed_status = ['Menunggu', 'Diproses', 'Selesai'];
              if (in_array($status, $allowed_status)) {
                            $stmt = $conn->prepare("UPDATE perbaikan SET status=? WHERE id=?");
                            $stmt->bind_param("si", $status, $id);
                            $stmt->execute();
              }
}

header("Location: permintaan_perbaikan.php");
exit();
