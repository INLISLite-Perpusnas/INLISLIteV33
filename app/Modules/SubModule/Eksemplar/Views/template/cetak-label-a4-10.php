<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->AddPage();
$pdf->setCellPaddings($left = '2', $top = '', $right = '', $bottom = '4');

$html = '';
$no = 0;
$item = 0;
$rec = 0;
$jumlahData = count($LabelData);

foreach ($LabelData as $data) :
	$rec++;

	if ($item == 0) {
		$html .= '<div style="padding:58px;">';
		$html .= '<table style="1px solid transparent;">';
	}

	if ($no == 0) {
		$html .= '<tr>';
	}

	$loclib = $data['NamaCabang'] ?? $data['NamaPerpustakaan'];

	$barcodeBlock = '
					<td style="text-align: center; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC; border-left:solid 1px #CCC;">
						<span style="font-size:11px; font-weight: bold;">' . $loclib . '</span><br>
						<img style="padding-top:5px;" src="' . $data['BarcodePNG'] . '" width="260" height="39">
						<br>
						<span style="font-size:12px;">*' . $data['Barcode'] . '*</span><br>
						<span style="font-size:11px; font-weight: bold;">' . $data['NamaPerpustakaan'] . '</span>
					</td>';

	$html .= '
			<td style="width:50%;padding-bottom: 15px; padding-right: 55px; text-align: left;">
				<table style="width:283px;" cellpadding="0" cellspacing="0" nobr="true">
					<tr>
						<td style="border:solid 1px #CCC; height:53px; text-align: center; vertical-align: middle; width:283px; padding: 5px; font-size: 12px; background-color:#000; color:#FFF;">' . $loclib . '<br>' . $data['NamaPerpustakaan'] . '</td>
					</tr>
					<tr>
						<td style="height:90px; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC; border-left:solid 1px #CCC; text-align: left; vertical-align: middle; padding-left: 60px; padding-right: 60px;">' . implode('<br>', array_map('htmlspecialchars', preg_split('/[\s\/]+/', trim($data['CallNumber'])))) . '</td>
					</tr>
					<tr>' . $barcodeBlock . '</tr>
					<tr>' . $barcodeBlock . '</tr>
				</table>
			</td>';

	$i = 0;
	if ($no == 1 || $i == ($jumlahData - 1)) {
		if ($i == ($jumlahData - 1)) {
			$html .= '<td style="width:50%;padding-bottom: 15px; padding-right: 55px; text-align: left;">&nbsp;</td>';
		}
		$html .= '</tr>';
		$no = 0;
	} else {
		$no++;
	}

	if ($item == 5 || $rec == $jumlahData) {
		if ($no > 0) {
			$html .= '</tr>';
			$no = 0;
		}
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
$pdf->Output('example_010.pdf', 'D');
die;
