<?php

namespace LaporanEksemplar\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanEksemplar extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $eksemplarModel;
    public $userModel;

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->userModel = new \User\Models\UserModel();
		helper('reference');
	}

	public function index()
    {
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

        // Ambil data user untuk dropdown filter
        $userOptions = $this->userModel->select('id, username')
            ->where('active', 1)
            ->whereNotIn('category', ['anggota'])
            ->orderBy('username', 'ASC')
            ->findAll();

        $data = [
            'columns' => $columns,
            'userOptions' => $userOptions
        ];

        return view('LaporanEksemplar\Views\index', $data);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query with JOIN to users table
        $query = $this->eksemplarModel
                ->join('(SELECT ID, Title, Author, Edition, Publisher, PublishLocation, PublishYear, Subject, ISBN, Languages, DeweyNo FROM catalogs) AS catalogs', 'catalogs.ID = collections.Catalog_ID', 'INNER')
                ->join('(SELECT ID, Name as JenisSumber FROM collectionsources) AS sources','collections.Source_id = sources.ID', 'LEFT')
                ->join('(SELECT ID, Name as BentukFisik FROM collectionmedias) AS medias','collections.Source_id = medias.ID', 'LEFT')
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
        $eksemplars = $query->limit(20)->find();
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
                ->join('(SELECT ID, Title, Author, Edition, Publisher, PublishLocation, PublishYear, Subject, ISBN, Languages, DeweyNo FROM catalogs) AS catalogs', 'catalogs.ID = collections.Catalog_ID', 'INNER')
                ->join('(SELECT ID, Name as JenisSumber, Code FROM collectionsources) AS sources','collections.Source_id = sources.ID', 'LEFT')
                ->join('(SELECT ID, Name as BentukFisik, Code FROM collectionmedias) AS medias','collections.Source_id = medias.ID', 'LEFT')
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

    // Helper function untuk apply multiple filters
    private function applyFilters($query)
    {
        // Filter berdasarkan tanggal dibuat
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        if ($startDate && $endDate) {
            $query->where('collections.CreateDate >=', $startDate)
                  ->where('collections.CreateDate <=', $endDate);
        }

        // Filter berdasarkan bulan dan tahun dibuat
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        if ($month && $year) {
            $query->where('MONTH(collections.CreateDate)', $month)
                  ->where('YEAR(collections.CreateDate)', $year);
        }

        // Filter berdasarkan tahun saja (jika tidak ada bulan)
        $yearOnly = $this->request->getPost('year_only');
        if ($yearOnly && !$month) {
            $query->where('YEAR(collections.CreateDate)', $yearOnly);
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