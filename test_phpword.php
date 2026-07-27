<?php
require 'vendor/autoload.php';

$html = '
<table style="width:100%; border: 1px solid black;">
    <tr>
        <td style="width:50%; border: 1px solid red; text-align:center;">
            <b>Test Left</b>
            <br>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==" width="50" height="50">
        </td>
        <td style="width:50%; border: 1px solid blue; text-align:center;">
            <b>Test Right</b>
        </td>
    </tr>
</table>
';

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection();
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('test_doc.docx');
echo "Done\n";
