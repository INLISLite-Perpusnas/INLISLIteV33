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

	// 3 pita warna, masing-masing mewakili 1 digit pertama DeweyNo item ini
	// (mis. "201" -> digit 2, 0, 1), saling menempel tanpa jarak/border.
	$warnaBands = '';
	for ($w = 1; $w <= 3; $w++) {
		$kode  = $data['KodeWarna' . $w] ?? '';
		$warna = empty($data['Warna' . $w]) ? '' : ';background-color:' . $data['Warna' . $w];
		$warnaBands .= '<tr><td style="height:46px; text-align:center; vertical-align:middle' . $warna . '">' . htmlspecialchars((string) $kode) . '</td></tr>';
	}

	$html .= '
			<td style="width:50%;padding-bottom: 25px; padding-right: 55px; text-align: left;">
				<table style="width:283px; border:solid 1px #CCC;" cellpadding="0" cellspacing="0" nobr="true">
					<tr>
						<td style="height:70px; text-align: center; vertical-align: middle; width:283px; padding: 5px; font-size: 12px;">' . $data['NamaPerpustakaan'] . '</td>
					</tr>
					' . $warnaBands . '
					<tr>
						<td style="height:110px;">&nbsp;</td>
					</tr>
					<tr>
						<td style="height: 40px; font-size: 14px; border-top:solid 1px #CCC; text-align: center; vertical-align: middle;">' . htmlspecialchars((string) $data['CallNumber']) . '</td>
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
$pdf->Output('example_011.pdf', 'D');
die;
