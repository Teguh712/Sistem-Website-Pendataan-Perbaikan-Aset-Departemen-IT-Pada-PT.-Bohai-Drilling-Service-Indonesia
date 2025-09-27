<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
              header("Location: login.php");
              exit();
}

$user_id = $_SESSION['user']['id'];
$tanggal_pengajuan = date("Y-m-d");
$quantity = $_POST['quantity'];
$kode_aset = $_POST['kode_aset'];
$nama_aset = $_POST['nama_aset'];
$kategori_aset = $_POST['kategori_aset'];
$deskripsi = $_POST['deskripsi'];
$status = "Menunggu";

// === Auto Generate Chargo Manifest ===
$tanggal_hari_ini = date("Ymd");

// Ambil manifest terakhir hari ini
$query = "SELECT chargo_manifest FROM detail_perbaikan 
          WHERE chargo_manifest LIKE 'CM-$tanggal_hari_ini%' 
          ORDER BY chargo_manifest DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {
              // Ambil nomor urut terakhir dan tambahkan 1
              $last_manifest = $data['chargo_manifest'];
              $last_number = (int)substr($last_manifest, -3); // ambil 3 digit terakhir
              $new_number = str_pad($last_number + 1, 3, "0", STR_PAD_LEFT);
} else {
              $new_number = "001";
}

$chargo_manifest = "CM-$tanggal_hari_ini-$new_number";

// Simpan ke database
$insert = "INSERT INTO detail_perbaikan 
           (user_id, tanggal_pengajuan, chargo_manifest, quantity, kode_aset, nama_aset, kategori_aset, deskripsi, status)
           VALUES 
           ('$user_id', '$tanggal_pengajuan', '$chargo_manifest', '$quantity', '$kode_aset', '$nama_aset', '$kategori_aset', '$deskripsi', '$status')";

if (mysqli_query($conn, $insert)) {
              echo "<script>alert('Berhasil diajukan. Nomor manifest: $chargo_manifest'); window.location.href='status_perbaikan.php';</script>";
} else {
              echo "Gagal menyimpan data: " . mysqli_error($conn);
}
