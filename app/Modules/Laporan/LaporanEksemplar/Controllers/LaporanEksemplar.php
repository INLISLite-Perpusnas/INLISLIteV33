<?php

namespace LaporanEksemplar\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanEksemplar extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $eksemplarModel;

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
		helper('reference');
	}

	public function index()
    {
        if ($this->request->getGet('criterion_options') === '1') {
            return $this->criterionOptions();
        }
        // Definisi kolom yang bisa diekspor
        $columns = [
            'NomorBarcode' => 'No. Barcode',
            'TanggalPengadaan' => 'Tanggal Pengadaan',
            'NoInduk' => 'No. Induk',
            'Title' => 'Judul',
            'Author' => 'Pengarang',
            'Edition' => 'Edisi',
            'Publisher' => 'Penerbit',
            'PublishLocation' => 'Tempat Terbit',
            'PublishYear' => 'Tahun Terbit',
            'Subject' => 'Subjek',
            'ISBN' => 'ISBN',
            'CallNumber' => 'No. Panggil',
            'Languages' => 'Bahasa',
            'DeweyNo' => 'No. Dewey',
            'RFID' => 'No. RFID',
            'JenisSumber' => 'Jenis Sumber',
            'BentukFisik' => 'Bentuk Fisik',
            'Kategori' => 'Kategori',
            'Akses' => 'Akses',
            'LokasiRuang' => 'Lokasi Ruang',
            'NamaSumber' => 'Nama Sumber',
            'Ketersediaan' => 'Ketersediaan',
            'IsOPAC' => 'Status OPAC',
            'IsDRM' => 'Status DRM',
            'Currency' => 'Mata Uang',
            'Price' => 'Harga',
            'PriceType' => 'Satuan Harga',
            'Perpustakaan' => 'Lokasi Perpustakaan',
            'CreateBy' => 'Dibuat Oleh',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateBy' => 'Diperbarui Oleh',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        $data = [
            'columns' => $columns,
            'criteriaLabels' => array_map(function ($definition) {
                return $definition['label'];
            }, $this->criterionDefinitions()),
        ];

        return view('LaporanEksemplar\Views\index', $data);
    }

    private function criterionDefinitions(): array
    {
        return [
            'publishlocation' => ['label' => 'Kota Terbit', 'column' => 'catalogs.PublishLocation', 'source' => ['catalogs', 'PublishLocation', 'PublishLocation']],
            'publisher' => ['label' => 'Nama Penerbit', 'column' => 'catalogs.Publisher', 'source' => ['catalogs', 'Publisher', 'Publisher']],
            'publishyear' => ['label' => 'Tahun Terbit', 'column' => 'catalogs.PublishYear', 'source' => ['catalogs', 'PublishYear', 'PublishYear']],
            'location' => ['label' => 'Lokasi Perpustakaan', 'column' => 'collections.Location_Library_id', 'source' => ['location_library', 'ID', 'Name']],
            'location_ruang' => ['label' => 'Ruang Perpustakaan', 'column' => 'collections.Location_id', 'source' => ['locations', 'ID', 'Name']],
            'source_id' => ['label' => 'Jenis Sumber Perolehan', 'column' => 'collections.Source_id', 'source' => ['collectionsources', 'ID', 'Name']],
            'currency' => ['label' => 'Mata Uang', 'column' => 'collections.Currency', 'source' => ['collections', 'Currency', 'Currency']],
            'price' => ['label' => 'Harga', 'column' => 'collections.Price', 'source' => ['collections', 'Price', 'Price']],
            'category_id' => ['label' => 'Kategori', 'column' => 'collections.Category_id', 'source' => ['collectioncategorys', 'ID', 'Name']],
            'rule_id' => ['label' => 'Jenis Akses', 'column' => 'collections.Rule_id', 'source' => ['collectionrules', 'ID', 'Name']],
            'worksheet_id' => ['label' => 'Jenis Bahan', 'column' => 'catalogs.Worksheet_id', 'source' => ['worksheets', 'ID', 'Name']],
            'media_id' => ['label' => 'Jenis Media', 'column' => 'collections.Media_id', 'source' => ['collectionmedias', 'ID', 'Name']],
            'subject' => ['label' => 'Subjek', 'column' => 'catalogs.Subject', 'source' => ['catalogs', 'Subject', 'Subject']],
            'author' => ['label' => 'Pengarang', 'column' => 'catalogs.Author', 'source' => ['catalogs', 'Author', 'Author']],
            'createby' => ['label' => 'Dibuat Oleh', 'column' => 'collections.CreateBy', 'source' => ['users', 'id', 'username']],
            'updateby' => ['label' => 'Diperbarui Oleh', 'column' => 'collections.UpdateBy', 'source' => ['users', 'id', 'username']],
            'tp_date' => ['label' => 'Tanggal Pengadaan'],
        ];
    }

    private function criterionOptions()
    {
        $field = $this->request->getGet('field');
        $definitions = $this->criterionDefinitions();
        if (!is_string($field) || !isset($definitions[$field]['source'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Kriteria tidak valid.']);
        }

        [$table, $idColumn, $labelColumn] = $definitions[$field]['source'];
        $search = $this->request->getGet('q');
        $search = is_string($search) ? trim($search) : '';
        $page = max(1, (int) $this->request->getGet('page'));
        $builder = \Config\Database::connect('data')->table($table)
            ->distinct()
            ->select($idColumn . ' AS id, ' . $labelColumn . ' AS text')
            ->where($labelColumn . ' IS NOT NULL', null, false)
            ->where('TRIM(' . $labelColumn . ') !=', '');
        if ($search !== '') {
            $builder->like($labelColumn, $search);
        }
        $results = $builder->orderBy($labelColumn)->get(26, ($page - 1) * 25)->getResultArray();

        return $this->response->setJSON([
            'results' => array_slice($results, 0, 25),
            'pagination' => ['more' => count($results) > 25],
        ]);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query with JOIN to users table
        $query = $this->eksemplarModel
                ->join('(SELECT ID, Title, Author, Edition, Publisher, PublishLocation, PublishYear, Subject, ISBN, Languages, DeweyNo, Worksheet_id FROM catalogs) AS catalogs', 'catalogs.ID = collections.Catalog_ID', 'INNER')
                ->join('(SELECT ID, Name as JenisSumber FROM collectionsources) AS sources','collections.Source_id = sources.ID', 'LEFT')
                ->join('(SELECT ID, Name as BentukFisik FROM collectionmedias) AS medias','collections.Media_id = medias.ID', 'LEFT')
                ->join('(SELECT ID, Name as Kategori FROM collectioncategorys) AS categories','collections.Category_id = categories.ID', 'LEFT')
                ->join('(SELECT ID, Name as Ketersediaan FROM collectionstatus) AS status','collections.Status_id = status.ID', 'LEFT')
                ->join('(SELECT ID, Name as Akses FROM collectionrules) AS rules','collections.Rule_id = rules.ID', 'LEFT')
                ->join('(SELECT ID, Name as NamaSumber FROM partners) AS partners','collections.Partner_id = partners.ID', 'LEFT')
                ->join('(SELECT ID, Name as LokasiRuang FROM locations) AS locations','collections.Location_id = locations.ID', 'LEFT')
                ->join('(SELECT ID, Name as Perpustakaan FROM location_library) AS libraries','collections.Location_Library_id = libraries.ID', 'LEFT')
                ->join('users as creator', 'collections.CreateBy = creator.id', 'left')
                ->join('users as updater', 'collections.UpdateBy = updater.id', 'left')
                ->select($this->buildSelectColumns($columns));

        // Apply multiple filters
        $this->applyFilters($query);

        // Get first 20 rows
        $eksemplars = $query->limit(100)->find();
        log_message('debug', 'Last Query Preview: ' . $this->eksemplarModel->getLastQuery());

        if (empty($eksemplars)) {
            return '<div class="alert alert-info">Tidak ada data yang ditemukan dengan filter yang dipilih</div>';
        }

        // Build preview table
        $html = '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>';
        
        foreach ($columns as $column) {
            $html .= '<th>' . esc($this->getColumnLabel($column)) . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';

        foreach ($eksemplars as $eksemplar) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $this->getFormattedValue($eksemplar, $column);
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    public function export()
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 minutes
        
        // Simplified validation - hanya require columns
        if (!$this->validate([
            'columns' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');
      
        // Build query with JOIN to users table
        $query = $this->eksemplarModel
                ->join('(SELECT ID, Title, Author, Edition, Publisher, PublishLocation, PublishYear, Subject, ISBN, Languages, DeweyNo, Worksheet_id FROM catalogs) AS catalogs', 'catalogs.ID = collections.Catalog_ID', 'INNER')
                ->join('(SELECT ID, Name as JenisSumber, Code FROM collectionsources) AS sources','collections.Source_id = sources.ID', 'LEFT')
                ->join('(SELECT ID, Name as BentukFisik, Code FROM collectionmedias) AS medias','collections.Media_id = medias.ID', 'LEFT')
                ->join('(SELECT ID, Name as Kategori FROM collectioncategorys) AS categories','collections.Category_id = categories.ID', 'LEFT')
                ->join('(SELECT ID, Name as Ketersediaan FROM collectionstatus) AS status','collections.Status_id = status.ID', 'LEFT')
                ->join('(SELECT ID, Name as Akses FROM collectionrules) AS rules','collections.Rule_id = rules.ID', 'LEFT')
                ->join('(SELECT ID, Name as NamaSumber FROM partners) AS partners','collections.Partner_id = partners.ID', 'LEFT')
                ->join('(SELECT ID, Name as LokasiRuang FROM locations) AS locations','collections.Location_id = locations.ID', 'LEFT')
                ->join('(SELECT ID, Name as Perpustakaan FROM location_library) AS libraries','collections.Location_Library_id = libraries.ID', 'LEFT')
                ->join('users as creator', 'collections.CreateBy = creator.id', 'left')
                ->join('users as updater', 'collections.UpdateBy = updater.id', 'left')
                ->select($this->buildSelectColumns($selectedColumns));

        // Apply multiple filters
        $this->applyFilters($query);

        $eksemplars = $query->findAll();

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $label = $this->getColumnLabel($column);
            $sheet->setCellValue($col . '1', $label);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Add data rows
        $row = 2;
        foreach ($eksemplars as $eksemplar) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $this->getFormattedValue($eksemplar, $column);
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Eksemplar_' . date('d-m-Y_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        if (!$this->validate(['columns' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');

        $query = $this->eksemplarModel
            ->join('(SELECT ID, Title, Author, Edition, Publisher, PublishLocation, PublishYear, Subject, ISBN, Languages, DeweyNo, Worksheet_id FROM catalogs) AS catalogs', 'catalogs.ID = collections.Catalog_ID', 'INNER')
            ->join('(SELECT ID, Name as JenisSumber FROM collectionsources) AS sources', 'collections.Source_id = sources.ID', 'LEFT')
            ->join('(SELECT ID, Name as BentukFisik FROM collectionmedias) AS medias', 'collections.Media_id = medias.ID', 'LEFT')
            ->join('(SELECT ID, Name as Kategori FROM collectioncategorys) AS categories', 'collections.Category_id = categories.ID', 'LEFT')
            ->join('(SELECT ID, Name as Ketersediaan FROM collectionstatus) AS status', 'collections.Status_id = status.ID', 'LEFT')
            ->join('(SELECT ID, Name as Akses FROM collectionrules) AS rules', 'collections.Rule_id = rules.ID', 'LEFT')
            ->join('(SELECT ID, Name as NamaSumber FROM partners) AS partners', 'collections.Partner_id = partners.ID', 'LEFT')
            ->join('(SELECT ID, Name as LokasiRuang FROM locations) AS locations', 'collections.Location_id = locations.ID', 'LEFT')
            ->join('(SELECT ID, Name as Perpustakaan FROM location_library) AS libraries', 'collections.Location_Library_id = libraries.ID', 'LEFT')
            ->join('users as creator', 'collections.CreateBy = creator.id', 'left')
            ->join('users as updater', 'collections.UpdateBy = updater.id', 'left')
            ->select($this->buildSelectColumns($selectedColumns));

        $this->applyFilters($query);
        $totalRecords = (clone $query)->countAllResults(false);

        $maxRecords = 5000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with('error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export PDF adalah {$maxRecords} records. " .
                "Silakan gunakan filter yang lebih spesifik atau gunakan export Excel."
            );
        }

        $eksemplars = $query->find();

        // Ambil logo kop dari settingparameters
        $db = db_connect();
        $logokop = $db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow('Value') ?? '';
        $namaPerpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow('Value') ?? 'Perpustakaan';

        $logoBase64 = '';
        if ($logokop) {
            $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        // Build HTML
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

        // Header kop
        $html .= '<div class="kop">';
        if ($logoBase64) {
            $html .= '<img src="' . $logoBase64 . '" alt="Logo">';
        }
        $html .= '<div class="kop-text"><h2>' . esc($namaPerpustakaan) . '</h2>'
               . '<p>Laporan Eksemplar &mdash; Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN DATA EKSEMPLAR</h3>';

        // Table
        $html .= '<table><thead><tr><th>#</th>';
        foreach ($selectedColumns as $col) {
            $html .= '<th>' . esc($this->getColumnLabel($col)) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($eksemplars as $eksemplar) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($selectedColumns as $col) {
                $html .= '<td>' . esc($this->getFormattedValue($eksemplar, $col)) . '</td>';
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
        $dompdf->setPaper('A4', count($selectedColumns) > 8 ? 'landscape' : 'portrait');
        $dompdf->render();

        $fileName = 'Laporan_Eksemplar_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }

    // Helper function untuk apply multiple filters
    private function applyFilters($query)
    {
        switch ($this->request->getPost('filter_type') ?? 'date') {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate) {
                    $query->where('collections.CreateDate >=', $startDate);
                }
                if ($endDate) {
                    $end = \DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);
                    if ($end && $end->format('Y-m-d') === $endDate) {
                        $query->where('collections.CreateDate <', $end->modify('+1 day')->format('Y-m-d'));
                    }
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(collections.CreateDate)', $month)
                        ->where('YEAR(collections.CreateDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(collections.CreateDate)', $year);
                }
                break;
        }

        // Filter berdasarkan tanggal pengadaan
        $tpStartDate = $this->request->getPost('tp_start_date');
        $tpEndDate = $this->request->getPost('tp_end_date');
        if ($tpStartDate && $tpEndDate) {
            $query->where('collections.TanggalPengadaan >=', $tpStartDate)
                  ->where('collections.TanggalPengadaan <=', $tpEndDate);
        }

        // Filter berdasarkan lokasi ruang
        $locationRuang = $this->request->getPost('location_ruang');
        if ($locationRuang) {
            $query->where('collections.Location_id', $locationRuang);
        }

        // Filter berdasarkan pengarang
        $author = $this->request->getPost('author');
        if ($author) {
            $query->like('catalogs.Author', $author);
        }

        // Filter berdasarkan tempat terbit
        $publishLocation = $this->request->getPost('publishlocation');
        if ($publishLocation) {
            $query->like('catalogs.PublishLocation', $publishLocation);
        }

        // Filter berdasarkan subjek
        $subject = $this->request->getPost('subject');
        if ($subject) {
            $query->like('catalogs.Subject', $subject);
        }

        // Filter berdasarkan penerbit
        $publisher = $this->request->getPost('publisher');
        if ($publisher) {
            $query->like('catalogs.Publisher', $publisher);
        }

        // Filter berdasarkan dibuat oleh
        $createBy = $this->request->getPost('createby');
        if ($createBy) {
            $query->where('collections.CreateBy', $createBy);
        }

        // Filter berdasarkan diperbarui oleh
        $updateBy = $this->request->getPost('updateby');
        if ($updateBy) {
            $query->where('collections.UpdateBy', $updateBy);
        }

        $criteria = json_decode((string) $this->request->getPost('criteria'), true);
        if (!is_array($criteria)) {
            return;
        }
        $definitions = $this->criterionDefinitions();
        foreach (array_slice($criteria, 0, 20) as $criterion) {
            if (!is_array($criterion) || !is_string($criterion['field'] ?? null) || !isset($definitions[$criterion['field']])) {
                continue;
            }
            if ($criterion['field'] === 'tp_date' && is_array($criterion['value'] ?? null)) {
                $start = $criterion['value']['start'] ?? null;
                $end = $criterion['value']['end'] ?? null;
                if (is_string($start) && $start !== '') {
                    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $start);
                    if ($date && $date->format('Y-m-d') === $start) {
                        $query->where('collections.TanggalPengadaan >=', $start);
                    }
                }
                if (is_string($end) && $end !== '') {
                    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $end);
                    if ($date && $date->format('Y-m-d') === $end) {
                        $query->where('collections.TanggalPengadaan <', $date->modify('+1 day')->format('Y-m-d'));
                    }
                }
                continue;
            }
            $value = $criterion['value'] ?? null;
            if (isset($definitions[$criterion['field']]['column']) && is_scalar($value) && (string) $value !== '') {
                $query->where($definitions[$criterion['field']]['column'], $value);
            }
        }
    }

    // Method untuk mengambil data ruang berdasarkan lokasi perpustakaan
    public function getRuang()
    {
        $locationId = $this->request->getPost('location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([]);
        }
        
        // Ambil data ruang berdasarkan lokasi perpustakaan
        $ruangData = get_ref_table('locations', 'ID, Name', 'Location_Library_id = ' . $locationId, 'data');
        
        return $this->response->setJSON($ruangData);
    }

    // Helper function untuk build select columns dengan JOIN
    private function buildSelectColumns($selectedColumns)
    {
        $selectFields = [];
        
        // Mapping kolom ke tabel yang tepat
        $columnMapping = [
            // Kolom dari collections table
            'NomorBarcode' => 'collections.NomorBarcode',
            'TanggalPengadaan' => 'collections.TanggalPengadaan',
            'NoInduk' => 'collections.NoInduk',
            'RFID' => 'collections.RFID',
            'IsOPAC' => 'collections.IsOPAC',
            'IsDRM' => 'collections.IsDRM',
            'Currency' => 'collections.Currency',
            'Price' => 'collections.Price',
            'PriceType' => 'collections.PriceType',
            'CreateDate' => 'collections.CreateDate',
            'UpdateDate' => 'collections.UpdateDate',
            'CallNumber' => 'collections.CallNumber',
            
            // Kolom dari catalogs table (melalui JOIN)
            'Title' => 'catalogs.Title',
            'Author' => 'catalogs.Author',
            'Edition' => 'catalogs.Edition',
            'Publisher' => 'catalogs.Publisher',
            'PublishLocation' => 'catalogs.PublishLocation',
            'PublishYear' => 'catalogs.PublishYear',
            'Subject' => 'catalogs.Subject',
            'ISBN' => 'catalogs.ISBN',
            'Languages' => 'catalogs.Languages',
            'DeweyNo' => 'catalogs.DeweyNo',
            
            // Kolom dari joined tables dengan alias
            'JenisSumber' => 'sources.JenisSumber',
            'BentukFisik' => 'medias.BentukFisik',
            'Kategori' => 'categories.Kategori',
            'Ketersediaan' => 'status.Ketersediaan',
            'Akses' => 'rules.Akses',
            'NamaSumber' => 'partners.NamaSumber',
            'LokasiRuang' => 'locations.LokasiRuang',
            'Perpustakaan' => 'libraries.Perpustakaan'
        ];
        
        foreach ($selectedColumns as $column) {
            if ($column == 'CreateBy') {
                $selectFields[] = 'creator.username as CreateBy';
            } elseif ($column == 'UpdateBy') {
                $selectFields[] = 'updater.username as UpdateBy';
            } elseif (isset($columnMapping[$column])) {
                $selectFields[] = $columnMapping[$column];
            } else {
                // Fallback untuk kolom yang tidak ada di mapping
                $selectFields[] = 'collections.' . $column;
            }
        }
        
        return implode(', ', $selectFields);
    }

    // Helper function untuk get column label
    private function getColumnLabel($column)
    {
        $columnLabels = [
            'NomorBarcode' => 'No. Barcode',
            'TanggalPengadaan' => 'Tanggal Pengadaan',
            'NoInduk' => 'No. Induk',
            'Title' => 'Judul',
            'Author' => 'Pengarang',
            'Edition' => 'Edisi',
            'Publisher' => 'Penerbit',
            'PublishLocation' => 'Tempat Terbit',
            'PublishYear' => 'Tahun Terbit',
            'Subject' => 'Subjek',
            'ISBN' => 'ISBN',
            'CallNumber' => 'No. Panggil',
            'Languages' => 'Bahasa',
            'DeweyNo' => 'No. Dewey',
            'RFID' => 'No. RFID',
            'JenisSumber' => 'Jenis Sumber',
            'BentukFisik' => 'Bentuk Fisik',
            'Kategori' => 'Kategori',
            'Akses' => 'Akses',
            'LokasiRuang' => 'Lokasi Ruang',
            'NamaSumber' => 'Nama Sumber',
            'Ketersediaan' => 'Ketersediaan',
            'IsOPAC' => 'Status OPAC',
            'IsDRM' => 'Status DRM',
            'Currency' => 'Mata Uang',
            'Price' => 'Harga',
            'PriceType' => 'Satuan Harga',
            'Perpustakaan' => 'Lokasi Perpustakaan',
            'CreateBy' => 'Dibuat Oleh',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateBy' => 'Diperbarui Oleh',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        return isset($columnLabels[$column]) ? $columnLabels[$column] : $column;
    }

    // Helper function untuk format nilai
    private function getFormattedValue($eksemplar, $column)
    {
        $value = $eksemplar->$column;
        
        // Format boolean values
        if (in_array($column, ['IsOPAC', 'IsDRM'])) {
            $value = $value ? 'Ya' : 'Tidak';
        }
        
        // Format dates
        if (in_array($column, ['CreateDate', 'UpdateDate', 'TanggalPengadaan']) && $value) {
            $value = date('d-m-Y', strtotime($value));
        }
        
        // Format number
        if (in_array($column, ['Price']) && $value) {
            $value = number_format($value, 2);
        }
        
        return $value;
    }
}
