<?php
// Tambahkan fungsi serializeTCPDFtagParameters jika tidak ada
if (!method_exists('TCPDF', 'serializeTCPDFtagParameters')) {
    function serializeTCPDFtagParameters($params) {
        if (is_array($params)) {
            $str = '';
            foreach ($params as $k => $v) {
                if (is_array($v)) {
                    $str .= ','.serializeTCPDFtagParameters($v);
                } else if (is_string($v)) {
                    $str .= ',"'.addslashes($v).'"';
                } else {
                    $str .= ','.$v;
                }
            }
            if (!empty($str)) {
                $str = substr($str, 1);
            }
            return $str;
        }
        return $params;
    }
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->AddPage();
$pdf->setCellPaddings($left = '2', $top = '', $right = '', $bottom = '4');

// Gunakan fungsi yang kita definisikan jika TCPDF tidak memilikinya
$params_code128 = function_exists('serializeTCPDFtagParameters') ? 
    serializeTCPDFtagParameters(array('CODE 128', 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N')) : 
    (method_exists($pdf, 'serializeTCPDFtagParameters') ? 
        $pdf->serializeTCPDFtagParameters(array('CODE 128', 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N')) : 
        '"CODE 128","C128","","",60,20,0.4,{"position":"S","border":true,"padding":2,"fgcolor":[0,0,0],"bgcolor":[255,255,255],"text":false,"font":"helvetica"},"N"'
    );

$html = '';
$no = 0;
$item = 0;
$rec = 0;
$jumlahData = count($LabelData);

foreach ($LabelData as $index => $data) :
    // Gunakan fungsi yang kita definisikan jika TCPDF tidak memilikinya
    $params = function_exists('serializeTCPDFtagParameters') ? 
        serializeTCPDFtagParameters(array($data['Barcode'], 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N')) : 
        (method_exists($pdf, 'serializeTCPDFtagParameters') ? 
            $pdf->serializeTCPDFtagParameters(array($data['Barcode'], 'C128', '', '', 60, 20, 0.4, array('position' => 'S', 'border' => true, 'padding' => 2, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica'), 'N')) : 
            '"'.$data['Barcode'].'","C128","","",60,20,0.4,{"position":"S","border":true,"padding":2,"fgcolor":[0,0,0],"bgcolor":[255,255,255],"text":false,"font":"helvetica"},"N"'
        );
    
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
                    <td style="border:solid 1px #CCC; height:53px; width:212px; text-align: center; ">' . $data['NamaPerpustakaan'] . '</td>
                    <td style="width:17%;border-top:solid 1px #CCC;border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;text-align: center " rowspan="2">' . str_replace(' ', '<br>', $data['CallNumber']) . '</td>
                    </tr>
                    <tr>
                        <td style="height:90px; width:83%; text-align: center;padding-left: 3px; padding-right: 3px;border-left:solid 1px #CCC; border-bottom:solid 1px #CCC; border-right:solid 1px #CCC;">
                            <span style="font-size: 12px;">
                            ' . $data['Title'] . '
                            <br>
                            <br>
                            <img src="' . $data['BarcodePNG'] . '" width="150" height="30">
                            <br>                           
                            </span>
                            *' . $data['Barcode'] . '*
                        </td>                        
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
endforeach;

$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Output('example_006.pdf', 'D');
die;