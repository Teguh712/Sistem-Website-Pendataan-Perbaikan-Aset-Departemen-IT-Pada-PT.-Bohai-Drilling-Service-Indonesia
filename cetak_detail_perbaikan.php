<?php
session_start();
include 'config.php';

// Include manual DOMPDF (tanpa Composer)
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Cek admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data dari database
$query = $conn->query("SELECT dp.*, u.username FROM detail_perbaikan dp
    LEFT JOIN users u ON dp.user_id = u.id 
    ORDER BY dp.tanggal_pengajuan DESC");

// Buat HTML
$html = '<h2 style="text-align:center;">Laporan Detail Perbaikan Aset IT Support</h2>';
$html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Chargo</th>
                    <th>Qty</th>
                    <th>Kode Aset</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Pengaju</th>
                </tr>
            </thead>
            <tbody>';

$no = 1;
while ($row = $query->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$row['tanggal_pengajuan']}</td>
        <td>{$row['chargo_manifest']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['kode_aset']}</td>
        <td>{$row['nama_aset']}</td>
        <td>{$row['kategori_aset']}</td>
        <td>{$row['deskripsi']}</td>
        <td>{$row['status']}</td>
        <td>{$row['username']}</td>
    </tr>";
    $no++;
}

$html .= '</tbody></table>';

// Inisialisasi DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'landscape');

// Render ke PDF
$dompdf->render();

// Output ke browser
$dompdf->stream("Laporan_Detail_Perbaikan.pdf", ["Attachment" => false]);
exit;
