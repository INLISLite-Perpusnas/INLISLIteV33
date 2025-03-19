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
	$svgString = "<svg width=\"10\" height=\"150\">
	<text x=\"-150\" y=\"10\" transform=\"rotate(-90)\">*" . $LabelData['Barcode'] . "*</text>
					</svg>";
	$paramsSvgString = $pdf->serializeTCPDFtagParameters(array('@' . $svgString, $x = '', $y = '', $w = '', $h = '', $link = 'http://www.tcpdf.org', $align = '', $palign = '', $border = 0, $fitonpage = false));
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
				<table cellpadding="0" cellspacing="0" style="width:184px;" nobr="true">
					<tr>
						<td style="text-align: center; width:60px; height: 212px" rowspan="2">
							
							<tcpdf method="ImageSVG" params="' . $paramsSvgString . '" />
						</td>
						<td style="border:solid 1px #CCC; height:62px; text-align: center; width:212px; padding: 5px; font-size: 12px ">' . $LabelData['NamaPerpustakaan'] . '</td>				
					</tr>
					<tr>
						<td style="height:150px; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;border-left:solid 1px #CCC; font-weight: normal; font-size: 16px; text-align: center">' . LabelCallNumber_helper($LabelData['CallNumber']) . '</td>
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
// $xhtml = '		
// <tcpdf method="ImageSVG" params="' . $paramsSvgString . '" />';
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Output('example_006.pdf', 'D');
die;
