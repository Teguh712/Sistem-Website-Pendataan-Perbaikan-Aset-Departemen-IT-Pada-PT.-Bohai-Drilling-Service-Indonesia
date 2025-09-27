<?php
require_once 'dompdf/autoload.inc.php';
require_once 'config.php';

use Dompdf\Dompdf;

$query = "
    SELECT * FROM goods_form
    ORDER BY nomor_goods_form DESC, id ASC
";
$result = $conn->query($query);

// Mulai HTML
$html = '
<h2 style="text-align:center;">Laporan Goods Form IT Support</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background-color:#eee;">
            <th>No</th>
            <th>Nomor Goods Form</th>
            <th>Tanggal</th>
            <th>Chargo Manifest</th>
            <th>Name of Goods</th>
            <th>Qty</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
';

$no = 1;
$last_nomor = null;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $curr_nomor = $row['nomor_goods_form'];
        if ($curr_nomor !== $last_nomor) {
            $html .= '
            <tr style="background-color:#f5f5f5; font-weight:bold;">
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row['nomor_goods_form']) . '</td>
                <td>' . htmlspecialchars($row['tanggal_goods_form']) . '</td>
                <td>' . htmlspecialchars($row['chargo_manifest']) . '</td>
                <td colspan="3"></td>
            </tr>';
        }

        $html .= '
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>' . htmlspecialchars($row['name_of_goods']) . '</td>
            <td>' . htmlspecialchars($row['qty']) . '</td>
            <td>' . htmlspecialchars($row['remarks']) . '</td>
        </tr>';

        $last_nomor = $curr_nomor;
    }
} else {
    $html .= '<tr><td colspan="7">Tidak ada data.</td></tr>';
}

$html .= '
    </tbody>
</table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // atau 'portrait'
$dompdf->render();
$dompdf->stream("laporan_goods_form.pdf", ["Attachment" => false]);
exit();
