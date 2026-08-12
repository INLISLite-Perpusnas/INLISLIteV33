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

	$html .= '
			<td style="width:33%;padding-bottom: 55px; padding-right: 25px; text-align: left;">
				<table style="width:195px;" cellpadding="0" cellspacing="0" nobr="true">
					<tr>
						<td style="height:71px; text-align: center; vertical-align: middle; border:solid 1px #CCC;">
							<span style="font-size:12px;">' . $data['Title'] . '<br>
							<img style="padding-top:5px;" src="' . $data['BarcodePNG'] . '" width="150" height="30">
							<br>
							*' . $data['Barcode'] . '*
							</span>
						</td>
					</tr>
				</table>
			</td>';

	$i = 0;
	if ($no == 2 || $i == ($jumlahData - 1)) {
		if ($i == ($jumlahData - 1)) {
			$html .= '<td style="width:33%;padding-bottom: 55px; padding-right: 25px; text-align: left;">&nbsp;</td>';
		}
		$html .= '</tr>';
		$no = 0;
	} else {
		$no++;
	}

	if ($item == 23 || $rec == $jumlahData) {
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
$pdf->Output('example_012.pdf', 'D');
die;
