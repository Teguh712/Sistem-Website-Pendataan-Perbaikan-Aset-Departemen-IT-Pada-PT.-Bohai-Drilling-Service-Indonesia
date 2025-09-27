<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// === Ambil Data ===

// 1. Laporan Manifest
$manifest_result = $conn->query("
    SELECT DISTINCT dp.chargo_manifest, dp.tanggal_pengajuan, u.username, d.nama_departemen
    FROM detail_perbaikan dp
    LEFT JOIN users u ON dp.user_id = u.id
    LEFT JOIN karyawann k ON u.nomor_badge = k.nomor_badge
    LEFT JOIN departemen d ON k.kode_departemen = d.kode_departemen
    ORDER BY dp.tanggal_pengajuan DESC
");
if (!$manifest_result) {
    die("Query Manifest Error: " . $conn->error);
}

// 2. Laporan Detail Perbaikan
$perbaikan_result = $conn->query("
    SELECT dp.*, u.username 
    FROM detail_perbaikan dp 
    LEFT JOIN users u ON dp.user_id = u.id 
    ORDER BY dp.tanggal_pengajuan DESC
");
if (!$perbaikan_result) {
    die("Query Detail Perbaikan Error: " . $conn->error);
}

// 3. Laporan Goods Form
$goods_result = $conn->query("
    SELECT * FROM goods_form 
    ORDER BY nomor_goods_form DESC, id ASC
");
if (!$goods_result) {
    die("Query Goods Form Error: " . $conn->error);
}

// === HTML PDF ===
$html = '';

// --- Bagian 1: Manifest ---
$html .= '<h2 style="text-align:center;">Laporan Manifest Perbaikan Aset IT Support</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="6" width="100%" style="border-collapse: collapse; font-size:12px;">
<thead style="background-color: #f0f0f0;">
<tr>
    <th>No</th>
    <th>Tanggal Pengajuan</th>
    <th>Nama Akun (Departemen)</th>
    <th>Chargo Manifest</th>
</tr>
</thead><tbody>';

$no = 1;
while ($row = $manifest_result->fetch_assoc()) {
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
    </tr>";
    $no++;
}
$html .= '</tbody></table><div style="page-break-after: always;"></div>';

// --- Bagian 2: Detail Perbaikan ---
$html .= '<h2 style="text-align:center;">Laporan Detail Perbaikan Aset IT Support</h2>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse: collapse; font-size:12px;">
<thead style="background:#f0f0f0;">
<tr>
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
</thead><tbody>';

$no = 1;
while ($row = $perbaikan_result->fetch_assoc()) {
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
$html .= '</tbody></table><div style="page-break-after: always;"></div>';

// --- Bagian 3: Goods Form ---
$html .= '<h2 style="text-align:center;">Laporan Goods Form IT Support</h2>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse: collapse; font-size:12px;">
<thead style="background:#f0f0f0;">
<tr>
    <th>No</th>
    <th>Nomor Goods Form</th>
    <th>Tanggal</th>
    <th>Chargo Manifest</th>
    <th>Nama Barang</th>
    <th>Qty</th>
    <th>Remarks</th>
</tr>
</thead><tbody>';

$no = 1;
$last_nomor = null;

while ($row = $goods_result->fetch_assoc()) {
    $curr_nomor = $row['nomor_goods_form'];

    if ($curr_nomor !== $last_nomor) {
        $html .= "<tr style='background:#e8e8e8;font-weight:bold;'>
            <td>{$no}</td>
            <td>{$row['nomor_goods_form']}</td>
            <td>{$row['tanggal_goods_form']}</td>
            <td>{$row['chargo_manifest']}</td>
            <td colspan='3'></td>
        </tr>";
        $no++;
    }

    $html .= "<tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{$row['name_of_goods']}</td>
        <td>{$row['qty']}</td>
        <td>{$row['remarks']}</td>
    </tr>";

    $last_nomor = $curr_nomor;
}
$html .= '</tbody></table>';

// === Render ke PDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Keseluruhan_IT_Support.pdf", ["Attachment" => false]);
exit;
