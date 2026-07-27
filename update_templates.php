<?php
$dir = __DIR__ . '/app/Modules/SubModule/Eksemplar/Views/template';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '$outputFormat == \'word\'') !== false) {
        continue;
    }
    
    $replacement = <<<EOD
if (isset(\$outputFormat) && \$outputFormat == 'word') {
    echo \$html;
    return;
}
\$pdf->writeHTML(
EOD;

    // Find the line with $pdf->writeHTML
    $newContent = preg_replace('/\$pdf->writeHTML\(/', $replacement, $content, 1);
    
    if ($newContent !== null && $newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Updated: " . basename($file) . "\n";
    }
}
echo "Done.\n";
