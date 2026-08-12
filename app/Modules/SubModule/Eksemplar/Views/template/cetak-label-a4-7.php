<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->AddPage();
$pdf->setCellPaddings('2', '', '', '4');

$html = '';
$no = 0;      // 0 = kolom kiri, 1 = kolom kanan
$item = 0;    // hitung item per halaman (0-7 = 8 label/halaman)
$rec = 0;
$jumlahData = count($LabelData);

foreach ($LabelData as $data) :
    $rec++;

    if ($item == 0) {
        $html .= '<div style="padding:58px;">';
        $html .= '<table cellpadding="0" cellspacing="0">'; // TANPA width:100%
    }

    if ($no == 0) {
        $html .= '<tr>';
    }

    $warna = $data['Warna1'] == '' ? '' : ';background-color:' . $data['Warna1'];

    // Gap antar kolom hanya di kolom kiri, kolom kanan tidak perlu padding-right
    $paddingRight = ($no == 0) ? '55px' : '0';

  $html .= '
        <td style="padding-bottom:25px; padding-right:' . $paddingRight . '; text-align:left;">
            <table style="width:283px; table-layout:fixed;" cellpadding="0" cellspacing="0" nobr="true">
                <tr>
                    <td style="border:solid 1px #CCC; height:53px; text-align:center; vertical-align:middle; width:283px; font-size:12px' . $warna . '">' . $data['NamaPerpustakaan'] . '</td>
                </tr>
                <tr>
                    <td style="height:90px; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC; border-left:solid 1px #CCC; text-align:center; vertical-align:middle; width:283px;">' . implode('<br>', array_map('htmlspecialchars', preg_split('/[\s\/]+/', trim($data['CallNumber'])))) . '</td>
                </tr>
                <tr>
                    <td style="text-align:center; vertical-align:middle; width:283px;">
                        <img style="padding-top:5px;" src="' . $data['BarcodePNG'] . '" width="260" height="39">
                        <br>
                        *' . $data['Barcode'] . '*
                    </td>
                </tr>
            </table>
        </td>';

    if ($no == 1) {
        $html .= '</tr>';
        $no = 0;
    } elseif ($rec == $jumlahData) {
        // Data ganjil, item terakhir jatuh di kolom kiri -> tambah sel kosong penyeimbang
        $html .= '<td>&nbsp;</td></tr>';
        $no = 0;
    } else {
        $no = 1;
    }

    if ($item == 7 || $rec == $jumlahData) {
        $html .= '</table>';
        $html .= '</div>';
        $item = 0;
    } else {
        $item++;
    }
endforeach;

if (isset($outputFormat) && $outputFormat == 'word') {
    echo $html;
    return;
}

$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Output('example_007.pdf', 'D');
die;