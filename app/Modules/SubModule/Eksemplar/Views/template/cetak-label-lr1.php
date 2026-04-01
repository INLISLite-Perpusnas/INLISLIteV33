<?php
/**
 * Template Label Roll - Model LR1
 * Layout  : Header penuh | Kolom kiri: Judul+Barcode | Kolom kanan: No. Panggil
 * Kertas  : Label Roll (lebar ~57mm)
 * Variabel: $LabelData (array), $pdf (TCPDF instance)
 */

$pdf = new TCPDF('L', 'mm', [57, 40], true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(0, 0, 0, true);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetCellPadding(1);

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

foreach ($LabelData as $row) {
    $pdf->AddPage();

    $barcodeSrc = 'data:image/png;base64,' . base64_encode(
        $generator->getBarcode($row['Barcode'], $generator::TYPE_CODE_128, 1, 30)
    );

    $callNumber = str_replace(' ', '<br>', htmlspecialchars($row['CallNumber']));

    $html = '
    <table cellpadding="1" cellspacing="0" style="width:57mm;">
        <tr>
            <td colspan="2" style="border:solid 1px #CCC; height:10mm; text-align:center; font-size:7pt; font-weight:bold;">
                ' . htmlspecialchars($row['NamaPerpustakaan']) . '
            </td>
        </tr>
        <tr>
            <td style="width:75%; border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align:center; font-size:6pt; height:25mm; padding:1mm;">
                ' . htmlspecialchars($row['Title']) . '<br><br>
                <img src="' . $barcodeSrc . '" style="width:38mm; height:9mm;"><br>
                *' . htmlspecialchars($row['Barcode']) . '*
            </td>
            <td style="width:25%; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       text-align:center; font-size:7pt; font-weight:bold; vertical-align:middle;">
                ' . $callNumber . '
            </td>
        </tr>
    </table>';

    $pdf->writeHTML($html, true, false, false, false, '');
}

$pdf->Output('label-roll-lr1.pdf', 'D');
die;