<?php
$dir = 'app/Modules/SubModule/Eksemplar/Views/template/';
$files = glob($dir . 'cetak-label-*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    // find the td containing the call number
    if (preg_match('/<td[^>]*implode\(\'<br>\'/', $content, $matches) || preg_match('/<td[^>]*\$callNumber/', $content, $matches)) {
        $td = $matches[0];
        if (strpos($td, 'height:') === false) {
            echo "MISSING HEIGHT: " . basename($file) . "\n";
        } else {
            // echo "HAS HEIGHT: " . basename($file) . "\n";
        }
    }
}
echo "Done.\n";
