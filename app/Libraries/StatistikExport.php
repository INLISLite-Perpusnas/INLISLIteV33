<?php

namespace App\Libraries;

use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Export halaman statistik OPAC (kunjungan, sirkulasi) ke PDF/Excel dengan tata letak mirip versi HTML:
 * kartu ringkasan, heatmap jam, grafik tren (gambar dari canvas browser), dan tabel.
 *
 * Struktur $report:
 *  - title, subtitle, period_label
 *  - stats:   [['label', 'value', 'sub', 'color'], ...]
 *  - heatmap: ['title', 'unit', 'days' => [n => nama], 'hours' => [..], 'matrix' => [n][jam] => nilai]
 *  - chart:   ['title', 'image' => biner PNG|null, 'labels' => [..], 'series' => [nama => [nilai..]]]
 *  - tables:  [['title', 'headers' => [..], 'rows' => [[..], ..], 'widths' => [..] opsional], ..]
 */
class StatistikExport
{
    private const PRIMARY = '1E3A8A';
    private const INK = '111827';
    private const MUTED = '6B7280';
    private const BORDER = 'E5E7EB';
    private const MAX_CHART_BYTES = 5242880;

    public static function download(array $report, string $format, ResponseInterface $response): ResponseInterface
    {
        $fileName = preg_replace('/[^A-Za-z0-9]+/', '_', $report['title']) . '_' . date('Ymd_His');

        if ($format === 'pdf') {
            return $response->setContentType('application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '.pdf"')
                ->setBody(self::pdf($report));
        }

        return $response->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '.xlsx"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody(self::excel($report));
    }

    /**
     * Ambil PNG grafik dari data URI kiriman browser; null bila kosong/tidak valid.
     */
    public static function decodeChartImage(?string $dataUri): ?string
    {
        $prefix = 'data:image/png;base64,';
        if (!is_string($dataUri) || strncmp($dataUri, $prefix, strlen($prefix)) !== 0 || strlen($dataUri) > self::MAX_CHART_BYTES * 4 / 3) {
            return null;
        }
        $binary = base64_decode(substr($dataUri, strlen($prefix)), true);
        if ($binary === false || strlen($binary) > self::MAX_CHART_BYTES) {
            return null;
        }
        $info = @getimagesizefromstring($binary);

        return ($info && $info[2] === IMAGETYPE_PNG) ? $binary : null;
    }

    /**
     * Warna sel heatmap, sama dengan CSS: hsl(211 52% calc(97% - level * 40%)).
     */
    public static function heatColor(float $level): string
    {
        return self::hslToHex(211, 0.52, 0.97 - max(0, min(1, $level)) * 0.40);
    }

    private static function pdf(array $report): string
    {
        $library = self::libraryHeader();
        $heatmap = $report['heatmap'] ?? null;
        $chart = $report['chart'] ?? null;

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            @page { margin: 18px 22px; }
            body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #111827; margin: 0; }
            .kop { border-bottom: 2px solid #1e3a8a; padding-bottom: 6px; margin-bottom: 10px; }
            .kop img { height: 42px; vertical-align: middle; margin-right: 10px; }
            .kop-name { font-size: 13px; font-weight: bold; vertical-align: middle; }
            .kop-sub { font-size: 8px; color: #6b7280; }
            .head { text-align: center; margin-bottom: 10px; }
            .head h1 { font-size: 18px; margin: 0 0 2px; }
            .head p { color: #6b7280; margin: 0; }
            .period { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 10px; margin-bottom: 10px; font-weight: bold; }
            .period span { color: #1e3a8a; }
            .grid { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px 10px; }
            .grid td { vertical-align: top; padding: 0; }
            .stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 9px 10px; }
            .stat-bar { width: 6px; border-radius: 3px; }
            .stat-value { font-size: 17px; font-weight: bold; }
            .stat-label { color: #6b7280; font-size: 9px; }
            .stat-sub { color: #9ca3af; font-size: 7.5px; }
            .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 10px; page-break-inside: avoid; }
            .chart { text-align: center; }
            .card-h { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold; font-size: 10px; }
            .card-b { padding: 8px 10px; }
            .heat { width: 100%; border-collapse: separate; border-spacing: 2px; }
            .heat th { color: #6b7280; font-size: 8px; text-align: center; }
            .heat th.day { text-align: left; width: 50px; }
            .heat td { height: 18px; border-radius: 5px; text-align: center; color: #20558e; font-size: 8px; }
            .tbl { width: 100%; border-collapse: collapse; }
            .tbl th { background: #f8f9fc; text-transform: uppercase; font-size: 7.5px; letter-spacing: .4px; text-align: left; padding: 5px 7px; }
            .tbl td { border-top: 1px solid #f0f1f5; padding: 5px 7px; }
            .empty { color: #6b7280; text-align: center; padding: 14px; }
            .foot { color: #9ca3af; font-size: 7.5px; text-align: right; }
        </style></head><body>';

        $html .= '<div class="kop">';
        if ($library['logo']) {
            $html .= '<img src="' . $library['logo'] . '" alt="">';
        }
        $html .= '<span class="kop-name">' . esc($library['name']) . '</span>';
        if ($library['address'] !== '') {
            $html .= '<div class="kop-sub">' . esc($library['address']) . '</div>';
        }
        $html .= '</div>';

        $html .= '<div class="head"><h1>' . esc($report['title']) . '</h1><p>' . esc($report['subtitle'] ?? '') . '</p></div>';
        $html .= '<div class="period">Periode Statistik: <span>' . esc($report['period_label']) . '</span></div>';

        $html .= '<table class="grid"><tr>';
        foreach ($report['stats'] as [$label, $value, $sub, $color]) {
            $html .= '<td style="width:' . (100 / max(1, count($report['stats']))) . '%"><div class="stat"><table style="border-collapse:collapse"><tr>'
                . '<td class="stat-bar" style="background:#' . $color . '"></td><td style="padding-left:8px">'
                . '<div class="stat-value">' . number_format((float) $value) . '</div>'
                . '<div class="stat-label">' . esc($label) . '</div><div class="stat-sub">' . esc($sub) . '</div>'
                . '</td></tr></table></div></td>';
        }
        $html .= '</tr></table>';

        if ($heatmap) {
            $max = self::heatmapMax($heatmap);
            $html .= '<div class="card"><div class="card-h">' . esc($heatmap['title']) . '</div><div class="card-b"><table class="heat"><tr><th class="day"></th>';
            foreach ($heatmap['hours'] as $hour) {
                $html .= '<th>' . $hour . '</th>';
            }
            $html .= '</tr>';
            foreach ($heatmap['days'] as $dayNumber => $dayName) {
                $html .= '<tr><th class="day">' . esc($dayName) . '</th>';
                foreach ($heatmap['hours'] as $hour) {
                    $value = (int) ($heatmap['matrix'][$dayNumber][$hour] ?? 0);
                    $html .= '<td style="background:#' . self::heatColor($max > 0 ? $value / $max : 0) . '">' . ($value > 0 ? number_format($value) : '') . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table></div></div>';
        }

        if ($chart) {
            $html .= '<div class="card"><div class="card-h">' . esc($chart['title']) . '</div><div class="card-b chart">';
            if (!empty($chart['image'])) {
                // Tinggi tetap, lebar mengikuti rasio gambar (dibatasi lebar halaman landscape).
                [$width, $height] = getimagesizefromstring($chart['image']);
                $displayWidth = min(1040, (int) round(300 * $width / max(1, $height)));
                $html .= '<img src="data:image/png;base64,' . base64_encode($chart['image']) . '" style="width:' . $displayWidth . 'px; height:' . (int) round($displayWidth * $height / max(1, $width)) . 'px">';
            } else {
                $html .= self::pdfTable(array_merge(['Periode'], array_keys($chart['series'])), self::chartRows($chart));
            }
            $html .= '</div></div>';
        }

        foreach ($report['tables'] as $table) {
            $html .= '<div class="card"><div class="card-h">' . esc($table['title']) . '</div>' . self::pdfTable($table['headers'], $table['rows']) . '</div>';
        }

        $html .= '<div class="foot">Dicetak: ' . date('d-m-Y H:i') . '</div></body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    private static function pdfTable(array $headers, array $rows): string
    {
        $html = '<table class="tbl"><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . esc($header) . '</th>';
        }
        $html .= '</tr>';
        if (empty($rows)) {
            return $html . '<tr><td colspan="' . count($headers) . '" class="empty">Belum ada data.</td></tr></table>';
        }
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . esc(is_int($cell) || is_float($cell) ? number_format($cell) : (string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</table>';
    }

    private static function excel(array $report): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Statistik');
        $sheet->setShowGridlines(false);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $heatmap = $report['heatmap'] ?? null;
        // Lebar grid: kolom A label hari + satu kolom per jam heatmap (minimal 12 kolom).
        $lastCol = max(12, $heatmap ? count($heatmap['hours']) + 1 : 0);
        $lastLetter = self::col($lastCol);
        $sheet->getColumnDimension('A')->setWidth(14);
        for ($c = 2; $c <= $lastCol; $c++) {
            $sheet->getColumnDimension(self::col($c))->setWidth(9);
        }

        $library = self::libraryHeader();
        $row = 1;
        $sheet->setCellValue("A{$row}", $library['name']);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(self::PRIMARY);
        $row++;
        $sheet->mergeCells("A{$row}:{$lastLetter}{$row}");
        $sheet->setCellValue("A{$row}", $report['title']);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(18)->getColor()->setRGB(self::INK);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row++;
        $sheet->mergeCells("A{$row}:{$lastLetter}{$row}");
        $sheet->setCellValue("A{$row}", $report['subtitle'] ?? '');
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB(self::MUTED);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $sheet->mergeCells("A{$row}:{$lastLetter}{$row}");
        $sheet->setCellValue("A{$row}", 'Periode Statistik: ' . $report['period_label']);
        self::box($sheet, "A{$row}:{$lastLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row += 2;

        // Kartu ringkasan: tiap kartu selebar 3 kolom, strip warna di kolom pertama.
        $statCount = count($report['stats']);
        $span = max(2, intdiv($lastCol, max(1, $statCount)));
        foreach ($report['stats'] as $i => [$label, $value, $sub, $color]) {
            $first = $i * $span + 1;
            $from = self::col($first);
            // Kartu terakhir melebar sampai kolom akhir agar baris kartu rata dengan heatmap.
            $to = $i === $statCount - 1 ? $lastLetter : self::col($first + $span - 1);
            foreach ([[$value, 20, true, self::INK], [$label, 10, false, self::MUTED], [$sub, 8, false, '9CA3AF']] as $offset => [$text, $size, $bold, $fontColor]) {
                $r = $row + $offset;
                $sheet->mergeCells("{$from}{$r}:{$to}{$r}");
                $sheet->setCellValue("{$from}{$r}", $offset === 0 ? (float) $value : (string) $text);
                $style = $sheet->getStyle("{$from}{$r}");
                $style->getFont()->setSize($size)->setBold($bold)->getColor()->setRGB($fontColor);
                $style->getAlignment()->setIndent(1);
                if ($offset === 0) {
                    $style->getNumberFormat()->setFormatCode('#,##0');
                    $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            }
            self::box($sheet, "{$from}{$row}:{$to}" . ($row + 2));
            $sheet->getStyle("{$from}{$row}:{$to}" . ($row + 2))->getBorders()->getLeft()
                ->setBorderStyle(Border::BORDER_THICK)->getColor()->setRGB($color);
        }
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row += 4;

        if ($heatmap) {
            $row = self::sectionTitle($sheet, $row, $heatmap['title'], $lastLetter);
            $max = self::heatmapMax($heatmap);
            $col = 2;
            foreach ($heatmap['hours'] as $hour) {
                $sheet->setCellValue(self::col($col++) . $row, $hour);
            }
            $sheet->getStyle("A{$row}:{$lastLetter}{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::MUTED);
            $sheet->getStyle("B{$row}:{$lastLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            foreach ($heatmap['days'] as $dayNumber => $dayName) {
                $sheet->setCellValue("A{$row}", $dayName);
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::MUTED);
                $col = 2;
                foreach ($heatmap['hours'] as $hour) {
                    $value = (int) ($heatmap['matrix'][$dayNumber][$hour] ?? 0);
                    $cell = self::col($col++) . $row;
                    if ($value > 0) {
                        $sheet->setCellValue($cell, $value);
                    }
                    $style = $sheet->getStyle($cell);
                    $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::heatColor($max > 0 ? $value / $max : 0));
                    $style->getFont()->getColor()->setRGB('20558E');
                    $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
                }
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;
            }
            $row++;
        }

        $tempImage = null;
        $chart = $report['chart'] ?? null;
        if ($chart) {
            $row = self::sectionTitle($sheet, $row, $chart['title'], $lastLetter);
            if (!empty($chart['image'])) {
                $tempImage = tempnam(WRITEPATH . 'cache', 'chart_');
                file_put_contents($tempImage, $chart['image']);
                [$width, $height] = getimagesizefromstring($chart['image']);
                $targetWidth = 900;
                $targetHeight = (int) round($height * $targetWidth / max(1, $width));
                $drawing = new Drawing();
                $drawing->setPath($tempImage);
                $drawing->setCoordinates("A{$row}");
                $drawing->setWidth($targetWidth);
                $drawing->setHeight($targetHeight);
                $drawing->setWorksheet($sheet);
                $row += (int) ceil($targetHeight / 20) + 1;
            }
            // Data tren tetap disertakan agar angka bisa diolah di Excel.
            $row = self::excelTable($sheet, $row, array_merge(['Periode'], array_keys($chart['series'])), self::chartRows($chart));
            $row++;
        }

        foreach ($report['tables'] as $table) {
            $row = self::sectionTitle($sheet, $row, $table['title'], $lastLetter);
            $row = self::excelTable($sheet, $row, $table['headers'], $table['rows'], $table['widths'] ?? null) + 1;
        }

        $sheet->setCellValue("A{$row}", 'Dicetak: ' . date('d-m-Y H:i'));
        $sheet->getStyle("A{$row}")->getFont()->setSize(8)->getColor()->setRGB('9CA3AF');

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)->setFitToHeight(0);

        try {
            ob_start();
            (new Xlsx($spreadsheet))->save('php://output');
            return (string) ob_get_clean();
        } finally {
            if ($tempImage && is_file($tempImage)) {
                unlink($tempImage);
            }
        }
    }

    private static function sectionTitle(Worksheet $sheet, int $row, string $title, string $lastLetter): int
    {
        $sheet->mergeCells("A{$row}:{$lastLetter}{$row}");
        $sheet->setCellValue("A{$row}", $title);
        $style = $sheet->getStyle("A{$row}:{$lastLetter}{$row}");
        $style->getFont()->setBold(true)->setSize(11)->getColor()->setRGB(self::INK);
        $style->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::BORDER);
        $sheet->getRowDimension($row)->setRowHeight(22);

        return $row + 1;
    }

    /**
     * Tabel dengan sel gabungan; $widths = jumlah kolom grid per kolom tabel (bawaan: 3 untuk kolom pertama, 2 sisanya).
     */
    private static function excelTable(Worksheet $sheet, int $row, array $headers, array $rows, ?array $widths = null): int
    {
        $columns = [];
        $next = 1;
        foreach (array_keys($headers) as $i) {
            $width = max(1, (int) ($widths[$i] ?? ($i === 0 ? 3 : 2)));
            $columns[] = [self::col($next), self::col($next + $width - 1)];
            $next += $width;
        }
        $lastLetter = end($columns)[1];

        foreach ($headers as $i => $header) {
            [$from, $to] = $columns[$i];
            if ($from !== $to) {
                $sheet->mergeCells("{$from}{$row}:{$to}{$row}");
            }
            $sheet->setCellValue("{$from}{$row}", strtoupper($header));
        }
        $style = $sheet->getStyle("A{$row}:{$lastLetter}{$row}");
        $style->getFont()->setBold(true)->setSize(8)->getColor()->setRGB(self::INK);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FC');
        $row++;

        if (empty($rows)) {
            $sheet->mergeCells("A{$row}:{$lastLetter}{$row}");
            $sheet->setCellValue("A{$row}", 'Belum ada data.');
            $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB(self::MUTED);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            return $row + 1;
        }

        foreach ($rows as $cells) {
            foreach (array_values($cells) as $i => $cell) {
                [$from, $to] = $columns[$i] ?? $columns[count($columns) - 1];
                if ($from !== $to) {
                    $sheet->mergeCells("{$from}{$row}:{$to}{$row}");
                }
                $sheet->setCellValue("{$from}{$row}", $cell);
                if (is_int($cell) || is_float($cell)) {
                    $sheet->getStyle("{$from}{$row}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("{$from}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            }
            $sheet->getStyle("A{$row}:{$lastLetter}{$row}")->getBorders()->getTop()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('F0F1F5');
            $row++;
        }

        return $row;
    }

    private static function box(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::BORDER);
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private static function chartRows(array $chart): array
    {
        $rows = [];
        foreach ($chart['labels'] as $i => $label) {
            $row = [$label];
            foreach ($chart['series'] as $values) {
                $row[] = (int) ($values[$i] ?? 0);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private static function heatmapMax(array $heatmap): int
    {
        $max = 0;
        foreach ($heatmap['matrix'] as $hours) {
            $max = max($max, ...array_values($hours ?: [0]));
        }

        return (int) $max;
    }

    private static function libraryHeader(): array
    {
        $db = \Config\Database::connect('data');
        $value = static fn($name) => (string) ($db->table('settingparameters')->where('Name', $name)->get()->getRow('Value') ?? '');

        $logo = '';
        $logoFile = basename($value('LogoKop') ?: $value('Logo'));
        $logoPath = ROOTPATH . 'public/uploads/branch/' . $logoFile;
        if ($logoFile !== '' && is_file($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'], true)) {
                $mime = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;
                $logo = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
            }
        }

        return [
            'name' => $value('NamaPerpustakaan') ?: 'Perpustakaan',
            'address' => $value('NamaLokasiPerpustakaan'),
            'logo' => $logo,
        ];
    }

    private static function col(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }

    private static function hslToHex(float $h, float $s, float $l): string
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        [$r, $g, $b] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        return sprintf('%02X%02X%02X', (int) round(($r + $m) * 255), (int) round(($g + $m) * 255), (int) round(($b + $m) * 255));
    }
}
