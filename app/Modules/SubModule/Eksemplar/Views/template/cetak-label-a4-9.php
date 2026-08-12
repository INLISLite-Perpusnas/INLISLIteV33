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

	$warnaRows = '';
	for ($w = 1; $w <= 5; $w++) {
		$kode  = $data['KodeWarna' . $w] ?? '';
		$warna = empty($data['Warna' . $w]) ? '' : ';background-color:' . $data['Warna' . $w];
		$warnaRows .= '<tr><td style="text-align:center' . $warna . '">' . htmlspecialchars((string) $kode) . '</td></tr>';
	}

	$html .= '
			<td style="width:50%;padding-bottom: 25px; padding-right: 55px; text-align: left;">
				<table style="width:212px;" cellpadding="0" cellspacing="0" nobr="true">
					<tr>
						<td style="text-align: center; vertical-align: middle; width:60px; height: 212px" rowspan="3">
							<img src="' . $data['BarcodePNGVertical'] . '" width="39" height="190">
						</td>
						<td style="border:solid 1px #CCC; height:62px; text-align: center; vertical-align: middle; width:212px; padding: 5px; font-size: 12px;">' . $data['NamaPerpustakaan'] . '</td>
					</tr>
					<tr>
						<td style="border-bottom:solid 1px #CCC; border-right:solid 1px #CCC; border-left:solid 1px #CCC; padding:0;">
							<table width="100%" cellspacing="1" cellpadding="3" bgcolor="#FFF" style="margin:0;">
								' . $warnaRows . '
							</table>
						</td>
					</tr>
					<tr>
						<td style="height: 40px; font-size: 14px; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC; border-left:solid 1px #CCC; text-align: center; vertical-align: middle;">' . implode('<br>', array_map('htmlspecialchars', preg_split('/[\s\/]+/', trim($data['CallNumber'])))) . '</td>
					</tr>
				</table>
			</td>';

	$i = 0;
	if ($no == 1 || $i == ($jumlahData - 1)) {
		if ($i == ($jumlahData - 1)) {
			$html .= '<td style="width:50%;padding-bottom: 25px; padding-right: 55px; text-align: left;">&nbsp;</td>';
		}
		$html .= '</tr>';
		$no = 0;
	} else {
		$no++;
	}

	if ($item == 7 || $rec == $jumlahData) {
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
$pdf->Output('example_009.pdf', 'D');
die;
