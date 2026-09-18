<?php

namespace LaporanSirkulasi\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanSirkulasi extends \Base\Controllers\BaseController
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    public function index()
    {
        // Gunakan route laporan yang sama agar izin akses tetap mengikuti halaman ini.
        if ($this->request->getGet('criterion_options') === '1') {
            return $this->criterionOptions();
        }
        $data = [
            'title' => 'Laporan Peminjaman Buku',
            'criteriaLabels' => array_map(function ($definitions) {
                return array_map(fn ($definition) => $definition['label'], $definitions);
            }, $this->criterionDefinitions()),
        ];
        
        return view('LaporanSirkulasi\Views\index', $data);
    }

    private function criterionDefinitions(): array
    {
        // Semua identifier SQL berasal dari daftar tetap, bukan dari input pengguna.
        return [
            'member' => [
                'number' => ['label' => 'Nomor Anggota', 'column' => 'm.MemberNo'],
                'name' => ['label' => 'Nama Anggota', 'column' => 'm.Fullname'],
                'age' => ['label' => 'Kelompok Umur (saat peminjaman)'],
                'sex' => ['label' => 'Jenis Kelamin', 'column' => 'm.Sex_id', 'lookup' => ['jenis_kelamin', 'ID', 'Name']],
                'type' => ['label' => 'Jenis Anggota', 'column' => 'm.JenisAnggota_id', 'lookup' => ['jenis_anggota', 'id', 'jenisanggota']],
                'job' => ['label' => 'Pekerjaan', 'column' => 'm.Job_id', 'lookup' => ['master_pekerjaan', 'id', 'Pekerjaan']],
                'education' => ['label' => 'Pendidikan', 'column' => 'm.EducationLevel_id', 'lookup' => ['master_pendidikan', 'id', 'Nama']],
            ],
            'catalog' => [
                'title' => ['label' => 'Judul', 'column' => 'cat.Title'],
                'author' => ['label' => 'Pengarang', 'column' => 'cat.Author'],
                'publisher' => ['label' => 'Nama Penerbit', 'column' => 'cat.Publisher'],
                'subject' => ['label' => 'Subjek', 'column' => 'cat.Subject'],
                'dewey' => ['label' => 'Nomor Dewey', 'column' => 'cat.DeweyNo'],
                'isbn' => ['label' => 'ISBN', 'column' => 'cat.ISBN'],
                'year' => ['label' => 'Tahun Terbit', 'column' => 'cat.PublishYear'],
            ],
        ];
    }

    private function ageGroups(): array
    {
        return ['0-5' => [0, 5], '6-11' => [6, 11], '12-17' => [12, 17],
            '18-25' => [18, 25], '26-35' => [26, 35], '36-45' => [36, 45],
            '46-55' => [46, 55], '56-65' => [56, 65], '66+' => [66, null]];
    }

    private function criterionOptions()
    {
        $group = $this->request->getGet('group');
        $field = $this->request->getGet('field');
        $definitions = $this->criterionDefinitions();
        if (!is_string($group) || !is_string($field) || !isset($definitions[$group][$field])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Kriteria tidak valid.']);
        }
        $search = $this->request->getGet('q');
        $search = is_string($search) ? trim($search) : '';
        if ($group === 'member' && $field === 'age') {
            $results = [];
            foreach ($this->ageGroups() as $id => $range) {
                $text = $id . ' tahun';
                if ($search === '' || stripos($text, $search) !== false) {
                    $results[] = ['id' => $id, 'text' => $text];
                }
            }
            return $this->response->setJSON(['results' => $results, 'pagination' => ['more' => false]]);
        }
        $definition = $definitions[$group][$field];
        $column = $definition['column'];
        $label = $column;
        $builder = $this->db->table($group === 'member' ? 'members m' : 'catalogs cat');
        if (isset($definition['lookup'])) {
            [$table, $id, $name] = $definition['lookup'];
            $builder->join($table . ' ref', 'ref.' . $id . ' = ' . $column);
            $label = 'ref.' . $name;
        }
        $builder->distinct()->select($column . ' AS id, ' . $label . ' AS text')
            ->where($column . ' IS NOT NULL', null, false)
            ->where('TRIM(' . $label . ') !=', '');
        if ($search !== '') {
            $builder->like($label, $search);
        }
        $page = max(1, (int) $this->request->getGet('page'));
        $results = $builder->orderBy($label)->orderBy($column)->get(26, ($page - 1) * 25)->getResultArray();
        $more = count($results) > 25;
        $results = array_slice($results, 0, 25);
        foreach ($results as &$result) {
            $result['id'] = (string) $result['id'];
            $result['text'] = (string) $result['text'];
        }
        unset($result);
        return $this->response->setJSON(['results' => $results, 'pagination' => ['more' => $more]]);
    }

    private function applyCriteriaFilters($query): void
    {
        foreach ($this->criterionDefinitions() as $group => $definitions) {
            $raw = $this->request->getPost($group . '_criteria');
            if ($raw === null || $raw === '') continue;
            $criteria = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($criteria) || count($criteria) > 20) {
                $query->where('1 = 0', null, false);
                continue;
            }
            foreach ($criteria as $criterion) {
                $field = is_array($criterion) ? ($criterion['field'] ?? null) : null;
                $value = is_array($criterion) ? ($criterion['value'] ?? null) : null;
                if (!is_string($field) || !isset($definitions[$field]) || !is_string($value)) {
                    $query->where('1 = 0', null, false);
                    continue;
                }
                if ($value === '') continue; // Semua nilai, tidak membatasi hasil.
                if ($group === 'member' && $field === 'age') {
                    $range = $this->ageGroups()[$value] ?? null;
                    if ($range === null) {
                        $query->where('1 = 0', null, false);
                        continue;
                    }
                    $age = 'TIMESTAMPDIFF(YEAR, m.DateOfBirth, cli.LoanDate)';
                    $query->where($age . ' >=', $range[0]);
                    if ($range[1] !== null) $query->where($age . ' <=', $range[1]);
                } else {
                    $query->where($definitions[$field]['column'], $value);
                }
            }
        }
    }
    
    private function applyPeriodFilter($query)
    {
        // Terapkan hanya periode yang aktif, sama untuk preview, Excel, dan PDF.
        $filterType = $this->request->getPost('filter_type') ?? 'date';
        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate) {
                    $query->where('cli.LoanDate >=', $startDate);
                }
                if ($endDate) {
                    $end = \DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);
                    if ($end && $end->format('Y-m-d') === $endDate) {
                        // Batas eksklusif hari berikutnya mencakup seluruh tanggal akhir.
                        $query->where('cli.LoanDate <', $end->modify('+1 day')->format('Y-m-d'));
                    }
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(cli.LoanDate)', $month)
                          ->where('YEAR(cli.LoanDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(cli.LoanDate)', $year);
                }
                break;
        }

    }

    private function applyReportFilters($builder): void
    {
        $this->applyPeriodFilter($builder);
        $this->applyCriteriaFilters($builder);
        $status = $this->request->getPost('loan_status');
        $name = $this->request->getPost('member_name');
        if ($status) $builder->where('cli.LoanStatus', $status);
        if ($name) $builder->like('m.Fullname', $name);
    }

   
    private function getTopBorrowers(): ?array
    {
        $limit = (int) $this->request->getPost('top_borrowers');
        if (!in_array($limit, [5, 10, 25, 50], true)) {
            return null;
        }
 
        $ranking = $this->db->table('collectionloanitems cli')
            ->select("
                m.ID AS member_id,
                m.Fullname AS nama_anggota,
                m.MemberNo AS MemberNo,
                CASE
                    WHEN m.Sex_id = 1 THEN 'Laki-laki'
                    WHEN m.Sex_id = 2 THEN 'Perempuan'
                    ELSE 'Tidak Diketahui'
                END AS jenis_kelamin,
                COUNT(DISTINCT cli.ID) AS jumlah_peminjaman
            ", false)
            ->join('members m', 'm.ID = cli.member_id')
            ->join('collections col', 'col.ID = cli.Collection_id', 'left')
            ->join('catalogs cat', 'cat.ID = col.Catalog_id', 'left');
 
        $this->applyReportFilters($ranking);
 
        $members = $ranking->groupBy('m.ID')
            ->orderBy('jumlah_peminjaman', 'DESC')
            ->orderBy('m.ID', 'ASC')
            ->get($limit)
            ->getResultArray();
 
        foreach ($members as $index => &$member) {
            $member['peringkat'] = $index + 1;
        }
        unset($member);
 
        return $members;
    }
 
    /**
     * Kolom yang valid untuk mode ringkasan (top borrowers).
     * Kolom seputar buku (judul_buku, penerbit, dll) sengaja tidak
     * disertakan karena tidak bisa direduksi jadi satu nilai per anggota.
     */
    private function summaryColumnWhitelist(): array
    {
        return ['peringkat', 'nama_anggota', 'MemberNo', 'jenis_kelamin', 'jumlah_peminjaman'];
    }
 
    public function preview()
    {
        $columns = $this->request->getPost('columns') ?? [];
 
        if (empty($columns)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pilih minimal satu kolom untuk ditampilkan'
            ]);
        }
 
        $topBorrowers = $this->getTopBorrowers();
 
        if ($topBorrowers !== null) {
            // Mode ringkasan: 1 baris per anggota, tidak ada limit tambahan
            // karena jumlah baris sudah dibatasi oleh pilihan top_borrowers.
            $columns = array_values(array_intersect(
                $this->summaryColumnWhitelist(),
                array_unique(array_merge(['peringkat', 'jumlah_peminjaman'], $columns))
            ));
 
            $filteredData = [];
            foreach ($topBorrowers as $row) {
                $filteredRow = [];
                foreach ($columns as $col) {
                    if (isset($row[$col])) {
                        $filteredRow[$col] = $row[$col];
                    }
                }
                $filteredData[] = $filteredRow;
            }
 
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $filteredData,
                'columns' => $columns,
                'total' => count($filteredData)
            ]);
        }
 
        // Mode detail (tanpa filter top borrowers) — seperti patch v1
        $builder = $this->buildBaseLoanQuery();
        $this->applyReportFilters($builder);
        $builder->orderBy('MAX(cli.LoanDate)', 'DESC', false);
        $builder->limit(100);
 
        $data = $builder->get()->getResultArray();
 
        $filteredData = [];
        foreach ($data as $row) {
            $filteredRow = [];
            foreach ($columns as $col) {
                if (isset($row[$col])) {
                    $filteredRow[$col] = $row[$col];
                }
            }
            $filteredData[] = $filteredRow;
        }
 
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $filteredData,
            'columns' => $columns,
            'total' => count($filteredData)
        ]);
    }
 
    public function export()
    {
        $columns = $this->request->getPost('columns') ?? [];
 
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diexport');
        }
 
        $topBorrowers = $this->getTopBorrowers();
 
        if ($topBorrowers !== null) {
            $columns = array_values(array_intersect(
                $this->summaryColumnWhitelist(),
                array_unique(array_merge(['peringkat', 'jumlah_peminjaman'], $columns))
            ));
            $data = $topBorrowers;
        } else {
            $builder = $this->buildBaseLoanQuery();
            $this->applyReportFilters($builder);
            $builder->orderBy('MAX(cli.LoanDate)', 'DESC', false);
            $data = $builder->get()->getResultArray();
        }
 
        $columnNames = [
            'peringkat' => 'Peringkat',
            'jumlah_peminjaman' => 'Jumlah Buku Dipinjam',
            'nama_anggota' => 'Nama Anggota',
            'MemberNo' => 'Nomor Anggota',
            'NomorBarcode' => 'Nomor Barcode',
            'judul_buku' => 'Judul Buku',
            'penerbit' => 'Penerbit',
            'kelas_ddc' => 'Kelas DDC',
            'subjek' => 'Subjek',
            'tanggal_peminjaman' => 'Tanggal Peminjaman',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'tanggal_pengembalian' => 'Tanggal Pengembalian',
            'petugas_peminjaman' => 'Petugas Peminjaman',
            'petugas_pengembalian' => 'Petugas Pengembalian',
            'jenis_kelamin' => 'Jenis Kelamin',
            'status_peminjaman' => 'Status Peminjaman'
        ];
 
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
 
        $col = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($col . '1', $columnNames[$column] ?? $column);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
 
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($columns as $column) {
                $value = $item[$column] ?? '-';
 
                if (in_array($column, ['tanggal_peminjaman', 'tanggal_jatuh_tempo', 'tanggal_pengembalian'])) {
                    $value = $value ? date('d-m-Y H:i', strtotime($value)) : '-';
                }
 
                if ($column === 'kelas_ddc') {
                    $sheet->setCellValueExplicit($col . $row, (string) $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                $col++;
            }
            $row++;
        }
 
        $filename = 'Laporan_Peminjaman_' . date('YmdHis') . '.xlsx';
 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
 
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
 
    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
 
        $columns = $this->request->getPost('columns') ?? [];
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diexport');
        }
 
        $topBorrowers = $this->getTopBorrowers();
 
        if ($topBorrowers !== null) {
            $columns = array_values(array_intersect(
                $this->summaryColumnWhitelist(),
                array_unique(array_merge(['peringkat', 'jumlah_peminjaman'], $columns))
            ));
            $data = $topBorrowers;
            // Mode ringkasan jumlah barisnya = jumlah anggota (max 50),
            // jadi tidak perlu cek batas 5000 baris.
        } else {
            $builder = $this->buildBaseLoanQuery();
            $this->applyReportFilters($builder);
            $builder->orderBy('MAX(cli.LoanDate)', 'DESC', false);
 
            $totalRecords = (clone $builder)->countAllResults(false);
            $maxRecords = 5000;
            if ($totalRecords > $maxRecords) {
                return redirect()->back()->with('error',
                    "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export PDF adalah {$maxRecords} records. " .
                    "Silakan gunakan filter yang lebih spesifik atau gunakan export Excel."
                );
            }
 
            $data = $builder->get()->getResultArray();
        }
 
        $columnNames = [
            'peringkat' => 'Peringkat',
            'jumlah_peminjaman' => 'Jumlah Buku Dipinjam',
            'nama_anggota'        => 'Nama Anggota',
            'MemberNo'            => 'Nomor Anggota',
            'NomorBarcode'        => 'Nomor Barcode',
            'judul_buku'          => 'Judul Buku',
            'penerbit' => 'Penerbit',
            'kelas_ddc' => 'Kelas DDC',
            'subjek' => 'Subjek',
            'tanggal_peminjaman'  => 'Tanggal Peminjaman',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'tanggal_pengembalian'=> 'Tanggal Pengembalian',
            'petugas_peminjaman'  => 'Petugas Peminjaman',
            'petugas_pengembalian'=> 'Petugas Pengembalian',
            'jenis_kelamin'       => 'Jenis Kelamin',
            'status_peminjaman'   => 'Status Peminjaman',
        ];
 
        $logokop = $this->db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow('Value') ?? '';
        $namaPerpustakaan = $this->db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow('Value') ?? 'Perpustakaan';
 
        $logoBase64 = '';
        if ($logokop) {
            $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
            if (file_exists($logoPath)) {
                $ext  = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
 
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 8px; margin: 0; }
            .kop { display: flex; align-items: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
            .kop img { max-height: 60px; max-width: 120px; margin-right: 12px; }
            .kop-text { flex: 1; }
            .kop-text h2 { margin: 0; font-size: 13px; }
            .kop-text p { margin: 2px 0; font-size: 8px; color: #555; }
            h3.report-title { text-align: center; font-size: 11px; margin: 6px 0 10px 0; }
            table { width: 100%; border-collapse: collapse; font-size: 7px; }
            th { background-color: #3e5c8b; color: #fff; padding: 4px 5px; text-align: left; border: 1px solid #ccc; }
            td { padding: 3px 5px; border: 1px solid #ddd; vertical-align: top; }
            tr:nth-child(even) td { background-color: #f5f5f5; }
            .footer { margin-top: 8px; font-size: 7px; color: #888; text-align: right; }
        </style></head><body>';
 
        $html .= '<div class="kop">';
        if ($logoBase64) {
            $html .= '<img src="' . $logoBase64 . '" alt="Logo">';
        }
        $html .= '<div class="kop-text"><h2>' . esc($namaPerpustakaan) . '</h2>'
               . '<p>Laporan Peminjaman &mdash; Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN DATA PEMINJAMAN</h3>';
 
        $html .= '<table><thead><tr><th>#</th>';
        foreach ($columns as $col) {
            $html .= '<th>' . esc($columnNames[$col] ?? $col) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
 
        $no = 1;
        foreach ($data as $item) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($columns as $col) {
                $value = $item[$col] ?? '-';
                if (in_array($col, ['tanggal_peminjaman', 'tanggal_jatuh_tempo', 'tanggal_pengembalian'])) {
                    $value = $value && $value !== '-' ? date('d-m-Y H:i', strtotime($value)) : '-';
                }
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }
 
        $html .= '</tbody></table>';
        $html .= '<div class="footer">Total: ' . ($no - 1) . ' data</div>';
        $html .= '</body></html>';
 
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
 
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', count($columns) > 6 ? 'landscape' : 'portrait');
        $dompdf->render();
 
        $fileName = 'Laporan_Peminjaman_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }
}
