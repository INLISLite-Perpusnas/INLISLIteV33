<?php
$file = 'app/Modules/SubModule/Eksemplar/Views/template/cetak-label-a4-5.php';
$content = file_get_contents($file);
$content = preg_replace('/<\/span>/', '', $content, 1);
file_put_contents($file, $content);
echo "Fixed a4-5.php\n";
