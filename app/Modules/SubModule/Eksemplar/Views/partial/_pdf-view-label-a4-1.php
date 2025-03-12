<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->AddPage();
$pdf->setCellPaddings($left = '2', $top = '', $right = '', $bottom = '4');
$params = $pdf->serializeTCPDFtagParameters(array('CODE 128', 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N'));

$html = '';
$no = 0;
$item = 0;
$rec = 0;
$jumlahData = 2;
foreach ($LabelData as $LabelData) :
	$params = $pdf->serializeTCPDFtagParameters(array($LabelData['Barcode'], 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N'));
	// for ($ix = 1; $ix <= 18; $ix++) {
	$rec++;

	if ($item == 0) {
		$html .= '<div style="padding:58px;">';
		$html .= '<table style="1px solid transparent;">';
	}

	if ($no == 0) {
		$html .= '<tr>';
	}
	$html .= '		
			<td style="width:50%;">
				<table cellpadding="0" cellspacing="0" style="width:255px;" nobr="true">
					<tr style="vertical-align:center">
						<td style="border:solid 1px #CCC; height:47px; width:255px; text-align: center; " colspan="2">' . $LabelData['NamaPerpustakaan'] . '</td>
					</tr>
					<tr>
						<td style="height:90px; width:75%; text-align: center;padding-left: 3px; padding-right: 3px;border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;">
							<span style="font-size: 12px;">
							' . $LabelData['Title'] . '
							<br>
							<tcpdf method="write1DBarcode" params="' . $params . '" />
							<br>							
							</span>
							*' . $LabelData['Barcode'] . '*
						</td>
						<td style="width:25%;border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;text-align: center ">' . $LabelData['CallNumber'] . '</td>
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
		$html .= '</table>';
		$html .= '</div>';
		$item = 0;
	} else {
		$item++;
	}
// }
endforeach;
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Output('example_006.pdf', 'D');
die;
