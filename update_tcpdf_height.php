<?php
$dir = 'app/Modules/SubModule/Eksemplar/Views/template/';
$files = glob($dir . 'cetak-label-*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    $changed = false;

    // Pattern 1: a4-1 style (adjacent td has height:90px)
    if (preg_match('/<td style="height:([0-9a-zA-Z]+); width:[0-9]+%;[^>]*>\s*<span[^>]*>\s*\' \. \$[a-zA-Z]+\[\'Title\'\] \. \'/s', $content, $matches)) {
        $height = $matches[1];
        $content = preg_replace(
            '/(<td style=")(width:[0-9]+%;(?:border[^;]+; )*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\' \. implode\(\'<br>\'/s',
            '$1height:' . $height . '; $2$3\' . implode(\'<br>\'',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }

    // Pattern 2: a4-2 style (rowspan="2")
    if (preg_match('/<td style="border:[^;]+; height:([0-9]+px);[^"]*">\' \. \$[a-zA-Z]+\[\'NamaPerpustakaan\'\] \. \'<\/td>\s*<td style="(width:[0-9]+%;border[^"]*") rowspan="2">\' \. implode/s', $content, $matches1)) {
        if (preg_match('/<tr>\s*<td style="height:([0-9]+px);/s', $content, $matches2)) {
            $h1 = (int)str_replace('px', '', $matches1[1]);
            $h2 = (int)str_replace('px', '', $matches2[1]);
            $totalHeight = ($h1 + $h2) . 'px';
            
            $content = preg_replace(
                '/(<td style=")(width:[0-9]+%;border[^"]*") rowspan="2">\' \. implode/s',
                '$1height:' . $totalHeight . '; $2 rowspan="2">\' . implode',
                $content,
                -1,
                $count
            );
            if ($count > 0) $changed = true;
        }
    }

    // Pattern 3: lr1 style (adjacent td has height:25mm)
    if (preg_match('/<td style="width:[0-9]+%; border[^"]*height:([0-9a-zA-Z]+);[^>]*>\s*\' \. htmlspecialchars\(\$[a-zA-Z]+\[\'Title\'\]/s', $content, $matches)) {
        $height = $matches[1];
        $content = preg_replace(
            '/(<td style=")(width:[0-9]+%; border[^"]*text-align:\s*center;\s*vertical-align:\s*middle;[^"]*")([^>]*>)\s*\' \. \$callNumber/s',
            '$1height:' . $height . '; $2$3\' . $callNumber',
            $content,
            -1,
            $count
        );
        if ($count > 0) $changed = true;
    }

    if ($changed) {
        file_put_contents($file, $content);
        echo "Added height to: " . basename($file) . "\n";
    }
}
echo "Done.\n";
