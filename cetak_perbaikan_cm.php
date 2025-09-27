<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$chargo = $_GET['chargo_manifest'] ?? '';
if (empty($chargo)) {
    echo "Nomor Chargo Manifest tidak ditemukan.";
    exit();
}

// Ambil data
$stmt = $conn->prepare("SELECT dp.*, u.username 
                        FROM detail_perbaikan dp
                        LEFT JOIN users u ON dp.user_id = u.id
                        WHERE dp.chargo_manifest = ?
                        ORDER BY dp.tanggal_pengajuan DESC");
$stmt->bind_param("s", $chargo);
$stmt->execute();
$result = $stmt->get_result();

$dompdf = new Dompdf();
$html = "<h2 style='text-align:center;'>Laporan Detail Perbaikan Per Chargo Manifest</h2>";
$html .= "<h3 style='margin-top:20px;'>Chargo Manifest: <strong>{$chargo}</strong></h3>";

$html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
            <thead>
                <tr style='background:#eee;'>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Qty</th>
                    <th>Kode Aset</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Pengaju</th>
                    <th>Status</th>
                </tr>
            </thead><tbody>";

$no = 1;
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$row['tanggal_pengajuan']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['kode_aset']}</td>
        <td>{$row['nama_aset']}</td>
        <td>{$row['kategori_aset']}</td>
        <td>{$row['deskripsi']}</td>
        <td>{$row['username']}</td>
        <td>{$row['status']}</td>
    </tr>";
    $no++;
}
$html .= "</tbody></table>";

$stmt->close();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Chargo_{$chargo}.pdf", ["Attachment" => false]);
exit();
