<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
              header("Location: login.php");
              exit();
}

// Load library DOMPDF
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Ambil data manifest perbaikan
$query = "
    SELECT dp.chargo_manifest, dp.tanggal_pengajuan, u.username, k.nama_departemen
    FROM detail_perbaikan dp
    LEFT JOIN users u ON dp.user_id = u.id
    LEFT JOIN karyawann k ON u.nomor_badge = k.nomor_badge
    GROUP BY dp.chargo_manifest
    ORDER BY dp.tanggal_pengajuan DESC
";

$result = mysqli_query($conn, $query);

// Siapkan HTML untuk laporan
$html = '
<h2 style="text-align:center;">Laporan Manifest Perbaikan Aset IT Support</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%" style="border-collapse: collapse; font-size:12px;">
    <thead style="background-color: #f0f0f0;">
        <tr>
            <th>No</th>
            <th>Tanggal Pengajuan</th>
            <th>Nama Akun (Departemen)</th>
            <th>Chargo Manifest</th>
        </tr>
    </thead>
    <tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
              $tanggal = date('d-m-Y', strtotime($row['tanggal_pengajuan']));
              $akun = htmlspecialchars($row['username']);
              $departemen = htmlspecialchars($row['nama_departemen'] ?? '-');
              $chargo = htmlspecialchars($row['chargo_manifest']);

              $html .= "
        <tr>
            <td>{$no}</td>
            <td>{$tanggal}</td>
            <td>{$akun} ({$departemen})</td>
            <td>{$chargo}</td>
        </tr>
    ";
              $no++;
}

$html .= '</tbody></table>';

// Inisialisasi DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Tampilkan ke browser (tanpa download otomatis)
$dompdf->stream("Laporan_Manifest_Perbaikan.pdf", ["Attachment" => false]);
exit;
