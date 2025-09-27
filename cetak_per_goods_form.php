<?php
require 'config.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if (!isset($_GET['nomor'])) {
              die("Nomor Goods Form tidak ditemukan.");
}

$nomor = $_GET['nomor'];

// Ambil data berdasarkan nomor goods form
$stmt = $conn->prepare("SELECT * FROM goods_form WHERE nomor_goods_form = ? ORDER BY id ASC");
$stmt->bind_param("s", $nomor);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
              die("Data tidak ditemukan.");
}

$data_rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ambil informasi utama dari salah satu baris (karena shared)
$info = $data_rows[0];

// Buat HTML untuk PDF
$html = '
<style>
    body { font-family: Arial; font-size: 12px; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #f0f0f0; }
</style>

<h2 style="text-align:center;">RECEIVING THE GOODS FORM</h2>

<table style="border:none;">
    <tr>
        <td style="border:none; width:60%;">
            <b>PT BOHAI DRILLING SERVICE INDONESIA</b><br>
            Ruko Atap Merah Blok A 5-6<br>
            Jl. Peconengan Raya No 72,<br>
            Kebon Kelapa Gambir<br>
            Jakarta Pusat DKI Jakarta 10120<br>
            Telp/Fax. No.: +6221-38900070/68 , Fax: +6221-385-4180
        </td>
        <td style="border:none;">
            <table style="border:1px solid #000; width:100%;">
                <tr><td>Number</td><td>' . htmlspecialchars($info['nomor_goods_form']) . '</td></tr>
                <tr><td>Date</td><td>' . date('d F Y', strtotime($info['tanggal_goods_form'])) . '</td></tr>
                <tr><td>Package</td><td>-</td></tr>
            </table>
        </td>
    </tr>
</table>

<br><b>PO / Project:</b> CASH YARD

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name of the Goods</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Price / unit</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($data_rows as $row) {
              $html .= '
        <tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($row['name_of_goods']) . '</td>
            <td>' . htmlspecialchars($row['description']) . '</td>
            <td>' . htmlspecialchars($row['qty']) . ' Ea</td>
            <td>Rp</td>
            <td>' . htmlspecialchars($row['remarks']) . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<br><br>
<table style="width:100%; border:none; text-align:center;">
    <tr>
        <td>Prepare by,<br><br><br><br>IT</td>
        <td>Checked by,<br><br><br><br>IT</td>
        <td>Acknowledge by,<br><br><br><br>Manager</td>
    </tr>
</table>';

// Cetak PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("GoodsForm_$nomor.pdf", ["Attachment" => false]);
exit;
