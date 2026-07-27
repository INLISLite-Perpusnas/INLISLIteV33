<?php
$dir = 'app/Modules/SubModule/Eksemplar/Views/template/';
$files = glob($dir . 'cetak-label-*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    $changed = false;

    // A4-1, A4-3, A4-5 (Height 90px)
    if (strpos($file, 'a4-1') !== false || strpos($file, 'a4-3') !== false || strpos($file, 'a4-5') !== false || strpos($file, 'a4-4') !== false || strpos($file, 'a4-6') !== false) {
        $content = preg_replace(
            '/(<td style=")(width:25%;[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\' \. implode/s',
            '$1height:90px; $2$3\' . implode',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }
    
    // lr series height is 25mm
    if (strpos($file, 'lr') !== false) {
        $content = preg_replace(
            '/(<td style=")(width:25%;[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\s*\' \. \$callNumber/s',
            '$1height:25mm; $2$3\' . $callNumber',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }
    
    // br series height is 25mm
    if (strpos($file, 'br') !== false) {
        $content = preg_replace(
            '/(<td style=")(width:25%;[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\s*\' \. \$callNumber/s',
            '$1height:25mm; $2$3\' . $callNumber',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }
    
    // gc series height is 90px
    if (strpos($file, 'gc') !== false) {
         $content = preg_replace(
            '/(<td style=")(width:[0-9]+%;[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\' \. implode/s',
            '$1height:90px; $2$3\' . implode',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }
    
    // tj series height is 90px
    if (strpos($file, 'tj') !== false) {
         $content = preg_replace(
            '/(<td style=")(width:[0-9]+%;[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\' \. implode/s',
            '$1height:90px; $2$3\' . implode',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }
    
    // Clean up if it was added multiple times
    $content = str_replace('height:90px; height:90px;', 'height:90px;', $content);
    $content = str_replace('height:25mm; height:25mm;', 'height:25mm;', $content);

    if ($changed) {
        file_put_contents($file, $content);
        echo "Added height to: " . basename($file) . "\n";
    }
}
echo "Done.\n";
