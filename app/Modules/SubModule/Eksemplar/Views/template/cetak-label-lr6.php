<?php
/**
 * Template Label Roll - Model LR6
 * Layout  : Header atas + warna background | No. Panggil (besar, tanpa barcode)
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

    $warna = ($row['Warna1'] !== '') ? 'background-color:' . $row['Warna1'] . ';' : '';

  

    $html = '
    <table cellpadding="1" cellspacing="0" style="width:57mm;">
        <tr>
            <td style="border:solid 1px #CCC; height:10mm; text-align:center;
                       font-size:7pt; font-weight:bold; padding:2mm; ' . $warna . '">
                ' . htmlspecialchars($row['NamaPerpustakaan']) . '
            </td>
        </tr>
        <tr>
            <td style="height:25mm; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;
                       border-left:solid 1px #CCC; text-align:center;
                       font-size:9pt; font-weight:bold; vertical-align:middle;">
                ' . htmlspecialchars($row['CallNumber']) . '
            </td>
        </tr>
    </table>';

    $pdf->writeHTML($html, true, false, false, false, '');
}

$pdf->Output('label-roll-lr6.pdf', 'D');
die;