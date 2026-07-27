<?php
$file = 'app/Modules/SubModule/Eksemplar/Views/template/cetak-label-a4-5.php';
$content = file_get_contents($file);
$content = str_replace(
    '<img src="\' . $LabelData[\'BarcodePNG\'] . \'" width="150" height="30">
                            <br>                           
                            </span>',
    '<img src="\' . $LabelData[\'BarcodePNG\'] . \'" width="150" height="30">
                            <br>',
    $content
);
file_put_contents($file, $content);
echo "Fixed a4-5.php\n";
