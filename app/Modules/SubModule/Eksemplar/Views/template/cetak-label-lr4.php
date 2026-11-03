<?php
/**
 * Template Label Roll - Model LR4
 * Layout  : Header kiri | No. Panggil rowspan kanan + warna background
 * Kertas  : Label Roll (lebar ~57mm)
 * Variabel: $LabelData (array), $pdf (TCPDF instance)
 */

$pdf = new TCPDF('L', 'mm', [57, 40], true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(0, 0, 0, true);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetCellPadding(1);

foreach ($LabelData as $row) {
    $pdf->AddPage();

    // Lebar modul tetap 0,25 mm agar bisa discan di printer roll (lihat get_barcode_roll).
    // Kode yang terlalu panjang untuk kolom judul dipindah ke baris selebar label.
    $barcode = get_barcode_roll($row['Barcode'], 35);
    $barcodeFullRow = !$barcode['fits'];
    if ($barcodeFullRow) {
        $barcode = get_barcode_roll($row['Barcode'], 50);
    }
    $barcodeHtml = '<img src="' . $barcode['src'] . '" style="width:' . $barcode['width'] . 'mm; height:9mm;"><br>*'
        . htmlspecialchars($barcode['code']) . '*';
    $bodyHeight = $barcodeFullRow ? 12 : 25;
    $barcodeRow = $barcodeFullRow
        ? '<tr><td colspan="2" style="border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                   text-align:center; vertical-align:middle; font-size:6pt; height:14mm;">' . $barcodeHtml . '</td></tr>'
        : '';

    $warna      = ($row['Warna1'] !== '') ? 'background-color:' . $row['Warna1'] . ';' : '';
    $parts = preg_split('/[\s\/]+/', trim($row['CallNumber']));
    $callNumber = implode('<br>', array_map('htmlspecialchars', $parts));

    $html = '
    <table cellpadding="1" cellspacing="0" style="width:57mm;">
        <tr>
            <td style="border:solid 1px #CCC; height:10mm; text-align: center; vertical-align: middle;
                       font-size:7pt; font-weight:bold; width:75%;">
                ' . htmlspecialchars($row['NamaPerpustakaan']) . '
            </td>
            <td rowspan="2" style="width:25%; border-top:solid 1px #CCC; border-bottom:solid 1px #CCC;
                       border-right:solid 1px #CCC; text-align: center; vertical-align: middle; font-size:7pt;
                       font-weight:bold; vertical-align:middle; ' . $warna . '">
                ' . $callNumber . '
            </td>
        </tr>
        <tr>
            <td style="width:75%; border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align: center; vertical-align: middle; font-size:6pt; height:' . $bodyHeight . 'mm; padding:1mm;">
                ' . htmlspecialchars($row['Title']) . ($barcodeFullRow ? '' : '<br><br>' . $barcodeHtml) . '
            </td>
        </tr>
        ' . $barcodeRow . '
    </table>';

    if (isset($outputFormat) && $outputFormat == 'word') {
        echo $html . '<br>';
        continue;
    }
$pdf->writeHTML($html, true, false, false, false, '');
}

if (isset($outputFormat) && $outputFormat == 'word') {
    return;
}

$pdf->Output('label-roll-lr4.pdf', 'D');
die;