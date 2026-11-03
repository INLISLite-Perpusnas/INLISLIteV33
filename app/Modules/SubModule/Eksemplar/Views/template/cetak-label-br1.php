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

foreach ($LabelData as $row) {
    $pdf->AddPage();

    // Lebar modul tetap 0,25 mm agar bisa discan di printer roll (lihat get_barcode_roll)
    $barcode = get_barcode_roll($row['Barcode'], 48);

    $html = '
    <table cellpadding="2" cellspacing="0" style="width:57mm;">
        <tr>
            <td style="border:solid 1px #CCC; height:20mm; text-align:center; vertical-align:middle;">
                <img src="' . $barcode['src'] . '" style="width:' . $barcode['width'] . 'mm; height:10mm;"><br>
                <span style="font-size:7pt;">*' . htmlspecialchars($barcode['code']) . '*</span>
            </td>
        </tr>
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

$pdf->Output('label-barcode-roll-br1.pdf', 'D');
die;