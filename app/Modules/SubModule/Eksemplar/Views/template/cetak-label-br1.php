<?php
/**
 * Template Barcode Roll - Model BR1
 * Layout  : Hanya barcode + nomor barcode (tanpa judul, tanpa no. panggil)
 * Kertas  : Barcode Roll (lebar ~57mm, tinggi ~25mm)
 * Variabel: $LabelData (array), $pdf (TCPDF instance)
 */

$pdf = new TCPDF('L', 'mm', [57, 25], true, 'UTF-8', false);
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

    $html = '
    <table cellpadding="2" cellspacing="0" style="width:57mm;">
        <tr>
            <td style="border:solid 1px #CCC; height:20mm; text-align:center; vertical-align:middle;">
                <img src="' . $barcodeSrc . '" style="width:80%; height:10mm;"><br>
                <span style="font-size:7pt;">*' . htmlspecialchars($row['Barcode']) . '*</span>
            </td>
        </tr>
    </table>';

    $pdf->writeHTML($html, true, false, false, false, '');
}

$pdf->Output('label-barcode-roll-br1.pdf', 'D');
die;