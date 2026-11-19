<?php

namespace LaporanHistoriPencarianOpac\Controllers;

use CodeIgniter\Database\BaseBuilder;
use DateTimeImmutable;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanHistoriPencarianOpac extends \Base\Controllers\BaseController
{
    private const COLUMNS = [
        'waktu' => 'Waktu',
        'User_id' => 'No. Anggota',
        'ip' => 'IP',
        'jenis_pencarian' => 'Jenis Pencarian',
        'keyword' => 'Kata Kunci',
        'jenis_bahan' => 'Jenis Bahan',
        'url' => 'URL Pencarian',
    ];

    private function allowed(string $action): bool
    {
        return is_allowed('master-opac-histori-pencarian/index', login_id(), false)
            && is_allowed('laporan-opac-histori-pencarian/' . $action, login_id(), false);
    }

    private function validDate(string $value): bool
    {
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $value)) {
            return false;
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function options(): array
    {
        $selected = $this->request->getPost('columns');
        $startDate = $this->request->getPost('start_date') ?? '';
        $endDate = $this->request->getPost('end_date') ?? '';
        $keyword = $this->request->getPost('keyword') ?? '';
        $filterType = $this->request->getPost('filter_type') ?? 'date';
        $month = (string) ($this->request->getPost('month') ?? '');
        $year = (string) ($this->request->getPost('year') ?? '');

        if (!is_array($selected) || $selected === [] || count($selected) > count(self::COLUMNS)
            || count(array_filter($selected, static fn ($column) => is_string($column) && array_key_exists($column, self::COLUMNS))) !== count($selected)
            || count($selected) !== count(array_unique($selected))) {
            return ['error' => 'Pilih minimal satu kolom laporan yang valid.'];
        }
        if (!is_string($startDate) || !is_string($endDate) || !is_string($keyword)
            || mb_strlen($keyword) > 200) {
            return ['error' => 'Filter laporan tidak valid.'];
        }
        if (!in_array($filterType, ['date', 'month', 'year'], true)) {
            return ['error' => 'Jenis filter periode tidak valid.'];
        }
        if ($filterType === 'date') {
            $month = $year = '';
            if (($startDate === '') !== ($endDate === '')
                || ($startDate !== '' && (!$this->validDate($startDate) || !$this->validDate($endDate) || $startDate > $endDate))) {
                return ['error' => 'Isi tanggal mulai dan akhir dengan periode yang valid.'];
            }
        } else {
            // Bulan/tahun diubah menjadi rentang tanggal supaya query tetap memakai start_date dan end_date.
            if (!preg_match('/^[0-9]{4}$/D', $year) || (int) $year < 1000) {
                return ['error' => 'Isi tahun dengan 4 digit yang valid.'];
            }
            if ($filterType === 'month') {
                if (!preg_match('/^(0?[1-9]|1[0-2])$/D', $month)) {
                    return ['error' => 'Pilih bulan yang valid.'];
                }
                $month = (string) (int) $month;
                $first = new DateTimeImmutable(sprintf('%s-%02d-01', $year, $month));
                $startDate = $first->format('Y-m-d');
                $endDate = $first->format('Y-m-t');
            } else {
                $month = '';
                $startDate = $year . '-01-01';
                $endDate = $year . '-12-31';
            }
        }

        return [
            'columns' => $selected,
            'filter_type' => $filterType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'month' => $month,
            'year' => $year,
            'keyword' => trim($keyword),
        ];
    }

    /**
     * Teks periode laporan untuk ditampilkan di atas tabel export (berdasarkan waktu pencarian).
     */
    private function periodeLabel(array $options): string
    {
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if ($options['filter_type'] === 'month') {
            return 'Periode (waktu pencarian): Bulan ' . $bulan[(int) $options['month']] . ' ' . $options['year'];
        }
        if ($options['filter_type'] === 'year') {
            return 'Periode (waktu pencarian): Tahun ' . $options['year'];
        }
        if ($options['start_date'] === '') {
            return 'Periode (waktu pencarian): Semua periode';
        }
        $tanggal = static function (string $value) use ($bulan): string {
            $date = new DateTimeImmutable($value);
            return (int) $date->format('j') . ' ' . $bulan[(int) $date->format('n')] . ' ' . $date->format('Y');
        };
        return 'Periode (waktu pencarian): ' . $tanggal($options['start_date']) . ' s.d. ' . $tanggal($options['end_date']);
    }

    private function applyFilters(BaseBuilder $query, array $options): void
    {
        if ($options['start_date'] !== '') {
            $query->where('waktu >=', $options['start_date'] . ' 00:00:00');
            $query->where('waktu <', (new DateTimeImmutable($options['end_date']))->modify('+1 day')->format('Y-m-d 00:00:00'));
        }
        if ($options['keyword'] !== '') {
            $literal = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $options['keyword']);
            $query->like('keyword', $literal);
        }
    }

    private function query(array $options): BaseBuilder
    {
        $query = db_connect()->table('opaclogs')->select('ID, waktu, User_id, ip, jenis_pencarian, keyword, jenis_bahan, url');
        $this->applyFilters($query, $options);
        return $query->orderBy('waktu', 'DESC')->orderBy('ID', 'DESC');
    }

    public function index()
    {
        if (!$this->allowed('index')) {
            return $this->response->setStatusCode(403)->setBody('Akses laporan histori pencarian ditolak.');
        }

        $options = ['columns' => array_keys(self::COLUMNS), 'filter_type' => 'date', 'start_date' => '', 'end_date' => '', 'month' => '', 'year' => date('Y'), 'keyword' => ''];
        $error = null;
        $rows = [];
        $tableExists = db_connect()->tableExists('opaclogs');
        if ($this->request->getMethod() === 'POST') {
            $input = $this->options();
            if (isset($input['error'])) {
                $error = $input['error'];
                // Pertahankan pilihan periode di form walau validasi gagal
                foreach (['filter_type', 'start_date', 'end_date', 'month', 'year', 'keyword'] as $field) {
                    $value = $this->request->getPost($field);
                    if (is_string($value)) {
                        $options[$field] = $value;
                    }
                }
            } else {
                $options = $input;
                if ($tableExists) {
                    $rows = $this->query($options)->limit(20)->get()->getResultArray();
                }
            }
        }

        return view('LaporanHistoriPencarianOpac\Views\index', [
            'title' => 'Laporan Histori Pencarian OPAC',
            'columns' => self::COLUMNS,
            'options' => $options,
            'rows' => $rows,
            'error' => $error ?? session()->getFlashdata('error'),
            'tableExists' => $tableExists,
        ]);
    }

    private function exportOptions(string $action): ?array
    {
        if (!$this->allowed($action)) {
            return null;
        }
        if (!db_connect()->tableExists('opaclogs')) {
            return ['error' => 'Tabel opaclogs belum tersedia.'];
        }
        return $this->options();
    }

    private function failure(array $options)
    {
        return redirect()->to(base_url('laporan-opac-histori-pencarian'))
            ->with('error', $options['error']);
    }

    public function export()
    {
        $options = $this->exportOptions('export');
        if ($options === null) {
            return $this->response->setStatusCode(403)->setBody('Akses ekspor laporan ditolak.');
        }
        if (isset($options['error'])) {
            return $this->failure($options);
        }

        $query = $this->query($options);
        $members = (clone $query)->limit(500)->get()->getResultArray();
        if (!$members) {
            return $this->failure(['error' => 'Tidak ada histori pencarian untuk filter yang dipilih.']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Histori Pencarian OPAC');
        $lastColumn = Coordinate::stringFromColumnIndex(count($options['columns']));
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', 'LAPORAN HISTORI PENCARIAN OPAC');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', 'Dicetak: ' . date('d-m-Y H:i'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->setCellValue('A3', $this->periodeLabel($options));
        $sheet->getStyle('A3')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach ($options['columns'] as $index => $key) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '5', self::COLUMNS[$key]);
            $sheet->getColumnDimension($column)->setWidth(in_array($key, ['keyword', 'url'], true) ? 48 : 22);
        }
        $sheet->getStyle("A5:{$lastColumn}5")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A5:{$lastColumn}5")->getFill()->setFillType('solid')->getStartColor()->setRGB('1D5FD1');
        $sheet->freezePane('A6');

        $rowNumber = 6;
        $offset = 0;
        do {
            foreach ($members as $member) {
                foreach ($options['columns'] as $index => $key) {
                    $sheet->setCellValueExplicit(
                        Coordinate::stringFromColumnIndex($index + 1) . $rowNumber,
                        (string) ($member[$key] ?? ''),
                        DataType::TYPE_STRING
                    );
                }
                $rowNumber++;
            }
            $offset += count($members);
            $members = count($members) === 500 ? (clone $query)->limit(500, $offset)->get()->getResultArray() : [];
        } while ($members);
        $sheet->setAutoFilter("A5:{$lastColumn}" . ($rowNumber - 1));

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan_Histori_Pencarian_OPAC_' . date('Y-m-d_His') . '.xlsx"');
        header('Cache-Control: no-store');
        (new Xlsx($spreadsheet))->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }

    public function exportPdf()
    {
        $options = $this->exportOptions('export_pdf');
        if ($options === null) {
            return $this->response->setStatusCode(403)->setBody('Akses ekspor laporan ditolak.');
        }
        if (isset($options['error'])) {
            return $this->failure($options);
        }

        $rows = $this->query($options)->limit(2001)->get()->getResultArray();
        if (!$rows) {
            return $this->failure(['error' => 'Tidak ada histori pencarian untuk filter yang dipilih.']);
        }
        if (count($rows) > 2000) {
            return $this->failure(['error' => 'PDF dibatasi 2.000 baris. Persempit periode atau gunakan Excel.']);
        }

        $html = '<html><head><meta charset="UTF-8"><style>@page{size:A4 landscape;margin:20mm 12mm}'
            . 'body{font-family:DejaVu Sans,sans-serif;font-size:9px;color:#1f2937}'
            . 'h1{text-align:center;font-size:16px}p{text-align:center}'
            . 'table{width:100%;border-collapse:collapse;table-layout:fixed}th,td{border:1px solid #cbd5e1;padding:5px;word-wrap:break-word}'
            . 'th{background:#1d5fd1;color:#fff}</style></head><body><h1>Laporan Histori Pencarian OPAC</h1>'
            . '<p>Dicetak: ' . date('d-m-Y H:i') . '<br>' . esc($this->periodeLabel($options)) . '</p><table><thead><tr>';
        foreach ($options['columns'] as $column) {
            $html .= '<th>' . esc(self::COLUMNS[$column]) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($options['columns'] as $column) {
                $html .= '<td>' . esc((string) ($row[$column] ?? '')) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="Laporan_Histori_Pencarian_OPAC_' . date('Y-m-d_His') . '.pdf"')
            ->setBody($pdf->output());
    }
}
