<?php
/**
 * Template Kertas Label Golden Cock 121 - Model GC121-3
 * Layout  : Header penuh + warna | Kolom kiri: Judul+Barcode | Kolom kanan: No. Panggil
 * Kertas  : A4 | Grid: 2 kolom × 4 baris = 8 label/hal
 *
 * Variabel: $LabelData (array), $pdf (TCPDF instance)
 */

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(0, 0, 0, true);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetCellPadding(0);

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

$cols       = 2;
$rows       = 4;
$labelW     = 101.0;
$labelH     = 68.0;
$marginLeft = 1.0;
$marginTop  = 19.0;
$gapCol     = 3.5;
$gapRow     = 10.0;

$col = 0; $row = 0; $pageCount = 0;
$total = count($LabelData); $idx = 0;

$pdf->AddPage();

foreach ($LabelData as $data) {
    $idx++;

    $x = $marginLeft + $col * ($labelW + $gapCol);
    $y = $marginTop  + $row * ($labelH + $gapRow);

    $barcodeSrc = 'data:image/png;base64,' . base64_encode(
        $generator->getBarcode($data['Barcode'], $generator::TYPE_CODE_128, 1, 30)
    );

    $warna      = ($data['Warna1'] !== '') ? 'background-color:' . $data['Warna1'] . ';' : '';
    $callNumber = str_replace(' ', '<br>', htmlspecialchars($data['CallNumber']));

    $html = '
    <table cellpadding="1" cellspacing="0" style="width:' . $labelW . 'mm;">
        <tr>
            <td colspan="2" style="border:solid 1px #CCC; height:13mm; text-align:center;
                       font-size:9pt; font-weight:bold; ' . $warna . '">
                ' . htmlspecialchars($data['NamaPerpustakaan']) . '
            </td>
        </tr>
        <tr>
            <td style="width:75%; border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align:center; font-size:7pt; height:50mm; padding:2mm;">
                ' . htmlspecialchars($data['Title']) . '<br><br>
                <img src="' . $barcodeSrc . '" style="width:60mm; height:11mm;"><br>
                *' . htmlspecialchars($data['Barcode']) . '*
            </td>
            <td style="width:25%; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align:center; font-size:8pt; font-weight:bold; vertical-align:middle;">
                ' . $callNumber . '
            </td>
        </tr>
    </table>';

    $pdf->SetXY($x, $y);
    $pdf->writeHTML($html, false, false, false, false, '');

    $col++;
    if ($col >= $cols) { $col = 0; $row++; }
    if ($row >= $rows)  { $row = 0; $col = 0; $pageCount++; if ($idx < $total) $pdf->AddPage(); }
}

$pdf->Output('label-gc121-3.pdf', 'D');
die;