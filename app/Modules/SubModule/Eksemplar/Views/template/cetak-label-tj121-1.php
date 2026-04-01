<?php
/**
 * Template Kertas Label Tom & Jerry 121 - Model TJ121-1
 * Layout  : Header penuh | Kolom kiri: Judul+Barcode | Kolom kanan: No. Panggil
 * Kertas  : A4 | Grid: 2 kolom × 5 baris = 10 label/hal
 * Ukuran label : 99mm × 57mm
 * Margin atas  : 18.5mm | kiri: 7mm | gap kolom: 3mm | gap baris: 0mm
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

// ── Konfigurasi grid ────────────────────────────────────────────────────
$cols       = 2;
$rows       = 5;
$labelW     = 99.0;
$labelH     = 57.0;
$marginLeft = 7.0;
$marginTop  = 18.5;
$gapCol     = 3.0;
$gapRow     = 0;

$col       = 0;
$row       = 0;
$pageCount = 0;
$total     = count($LabelData);
$idx       = 0;

$pdf->AddPage();

foreach ($LabelData as $data) {
    $idx++;

    $x = $marginLeft + $col * ($labelW + $gapCol);
    $y = $marginTop  + $row * ($labelH + $gapRow);

    $barcodeSrc = 'data:image/png;base64,' . base64_encode(
        $generator->getBarcode($data['Barcode'], $generator::TYPE_CODE_128, 1, 30)
    );

    $callNumber = str_replace(' ', '<br>', htmlspecialchars($data['CallNumber']));

    $html = '
    <table cellpadding="1" cellspacing="0" style="width:' . $labelW . 'mm;">
        <tr>
            <td colspan="2" style="border:solid 1px #CCC; height:13mm; text-align:center;
                       font-size:9pt; font-weight:bold;">
                ' . htmlspecialchars($data['NamaPerpustakaan']) . '
            </td>
        </tr>
        <tr>
            <td style="width:75%; border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align:center; font-size:7pt; height:40mm; padding:2mm;">
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

    // ── Navigasi grid ──────────────────────────────────────────────────
    $col++;
    if ($col >= $cols) {
        $col = 0;
        $row++;
    }
    if ($row >= $rows) {
        $row = 0;
        $col = 0;
        $pageCount++;
        if ($idx < $total) {
            $pdf->AddPage();
        }
    }
}

$pdf->Output('label-tj121-1.pdf', 'D');
die;