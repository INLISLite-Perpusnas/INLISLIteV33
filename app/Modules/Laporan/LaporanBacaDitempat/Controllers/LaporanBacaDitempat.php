<?php

namespace LaporanBacaDitempat\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanBacaDitempat extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $anggotaModel;
    public $guestModel;
    public $lokasiperpustakaanModel;
    public $tujuanKunjunganModel;
    public $locationModel;

	function __construct()
	{
     
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->guestModel = new \LaporanBacaDitempat\Models\GuestModel();
        $this->lokasiperpustakaanModel = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
        $this->locationModel = new \LokasiRuang\Models\LokasiRuangModel();
	}

	public function index()
    {
        // Get all available columns
        $columns = [
            'no_pengunjung' => 'Nomor Pengunjung',
            'lokasi' => 'Lokasi Perpustakaan',
            'lok_ruang' => 'Lokasi Ruang',
            'periode' => 'Tanggal Kunjungan',
            'nama' => 'Nama',
            'noinduk' => 'No. Induk',
            'noanggota' => 'No. Anggota'
        // Add more columns as needed
        ];

        // Get member type options for filter
        $memberTypeOptions = $this->anggotaModel
            ->select('jenis_anggota.id, jenis_anggota.jenisanggota')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->groupBy('jenis_anggota.id')
            ->orderBy('jenis_anggota.jenisanggota')
            ->findAll();

         // Get location options for filter
        $locationOptions = $this->lokasiperpustakaanModel
            ->select('ID as code, Name as name')
            ->groupBy('ID')
            ->orderBy('Name')
            ->findAll();

        // Get lokasi ruang options for filter
        $roomOptions = $this->locationModel
            ->select('ID as code, Name as name')
            ->groupBy('ID')
            ->orderBy('Name')
            ->findAll();


        $data = [
            'columns' => $columns,
            'memberTypeOptions' => $memberTypeOptions,
            'locationOptions' => $locationOptions,
            'roomOptions' => $roomOptions
        ];
		
        /** @var array<string, mixed> $data */
        return view('LaporanBacaDitempat\Views\index', $data);
    }

    
	public function preview()
    { 
        $columns = json_decode($this->request->getPost('columns'), true);

        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        $query=$this->guestModel->select('NoPengunjung as no_pengunjung,
                location_library.Name AS lokasi, Location_Library_id,
                locations.Name AS lok_ruang, catalogs.Publisher AS penerbit,
                NoInduk AS noinduk,
                members.MemberNo AS noanggota, members.JenisAnggota_id,
                (CASE WHEN bacaditempat.Member_id IS NULL 
                      THEN bacaditempat.NoPengunjung 
                      ELSE members.FullName END) AS nama,
                bacaditempat.CreateDate AS tgl_kunjungan,
                bacaditempat.CreateDate AS periode')
            ->join('collections', 'bacaditempat.Collection_id = collections.ID')
            ->join('catalogs', 'collections.Catalog_id = catalogs.ID')
            ->join('worksheets', 'catalogs.Worksheet_id = worksheets.ID')   
            ->join('members', 'bacaditempat.Member_id = members.ID', 'left')
            ->join('location_library', 'collections.Location_Library_id = location_library.ID', 'left')
            ->join('locations', 'collections.Location_id = locations.ID', 'left');
    
        $this->applyFilters($query);

        // Get first 20 rows
        $members = $query->limit(20)->get()->getResult();
        // dd($members);


        if (empty($members)) {
            return '<div class="alert alert-info">Tidak ada data yang ditemukan dengan filter yang dipilih</div>';
        }

        // Build preview table
        $html = '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>';
        
        foreach ($columns as $column) {
            if($column == 'no_pengunjung') $column = 'Nomor Pengunjung';
            else if($column == 'lokasi') $column = 'Lokasi Perpustakaan';
            else if($column == 'lok_ruang') $column = 'Lokasi Ruang';
            else if($column == 'periode') $column = 'Tanggal Kunjungan';
            else if($column == 'noinduk') $column = 'No. Induk';
            else if($column == 'noanggota') $column = 'No. Anggota';
            else if($column == 'nama') $column = 'Nama';
            $html .= '<th>' . esc($column) . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';

        foreach ($members as $member) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $member->$column;
                if (in_array($column, ['periode']) && $value) {
                    $value = date('d-m-Y', strtotime($value));
                }
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

        // Simplified validation - only require columns
        if (!$this->validate([
            'columns' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');

        // Check estimated record count first
        $countQuery=$this->guestModel->select('NoPengunjung as no_pengunjung,
                location_library.Name AS lokasi, Location_Library_id,
                locations.Name AS lok_ruang, catalogs.Publisher AS penerbit,
                NoInduk AS noinduk,
                members.MemberNo AS noanggota, members.JenisAnggota_id,
                (CASE WHEN bacaditempat.Member_id IS NULL 
                      THEN bacaditempat.NoPengunjung 
                      ELSE members.FullName END) AS nama,
                bacaditempat.CreateDate AS tgl_kunjungan,
                bacaditempat.CreateDate AS periode')
            ->join('collections', 'bacaditempat.Collection_id = collections.ID')
            ->join('catalogs', 'collections.Catalog_id = catalogs.ID')
            ->join('worksheets', 'catalogs.Worksheet_id = worksheets.ID')   
            ->join('members', 'bacaditempat.Member_id = members.ID', 'left')
            ->join('location_library', 'collections.Location_Library_id = location_library.ID', 'left')
            ->join('locations', 'collections.Location_id = locations.ID', 'left');

        $this->applyFilters($countQuery);
        $totalRecords = $countQuery->countAllResults();

        // Limit maksimum export untuk mencegah memory issue
        $maxRecords = 50000; // Adjust sesuai kebutuhan server
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with(
                'error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export adalah {$maxRecords} records. " .
                    "Silakan gunakan filter yang lebih spesifik untuk mengurangi jumlah data."
            );
        }

        // Build main query
        $query=$this->guestModel->select('NoPengunjung as no_pengunjung,
                location_library.Name AS lokasi, Location_Library_id,
                locations.Name AS lok_ruang, catalogs.Publisher AS penerbit,
                NoInduk AS noinduk,
                members.MemberNo AS noanggota, members.JenisAnggota_id,
                (CASE WHEN bacaditempat.Member_id IS NULL 
                      THEN bacaditempat.NoPengunjung 
                      ELSE members.FullName END) AS nama,
                bacaditempat.CreateDate AS tgl_kunjungan,
                bacaditempat.CreateDate AS periode')
            ->join('collections', 'bacaditempat.Collection_id = collections.ID')
            ->join('catalogs', 'collections.Catalog_id = catalogs.ID')
            ->join('worksheets', 'catalogs.Worksheet_id = worksheets.ID')   
            ->join('members', 'bacaditempat.Member_id = members.ID', 'left')
            ->join('location_library', 'collections.Location_Library_id = location_library.ID', 'left')
            ->join('locations', 'collections.Location_id = locations.ID', 'left');

        // Apply multiple filters
        $this->applyFilters($query);

        // Create Excel with optimized settings
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Anggota');

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $headerName = $this->getColumnLabel($column);
            $sheet->setCellValue($col . '1', $headerName);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Process data in chunks to manage memory
        $chunkSize = 1000; // Process 1000 records at a time
        $offset = 0;
        $row = 2;

        do {
            // Clear previous query and get chunk
            $chunkQuery = clone $query;
            $members = $chunkQuery->limit($chunkSize, $offset)->find();

            if (empty($members)) {
                break;
            }

            // Add data rows
            foreach ($members as $member) {
                $col = 'A';
                foreach ($selectedColumns as $column) {
                    $value = $this->getFormattedValue($member, $column);
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            $offset += $chunkSize;

            // Force garbage collection
            if ($offset % ($chunkSize * 5) === 0) {
                gc_collect_cycles();
            }
        } while (count($members) === $chunkSize);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->setUseDiskCaching(true);
        $fileName = 'Laporan_Baca ditempat_' . date('d-m-Y_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');

        // Cleanup
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        exit();
    }

     // Helper function untuk apply multiple filters
    private function applyFilters($query)
    {
        // Filter berdasarkan tanggal dibuat
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        if ($startDate && $endDate) {
            $query->where('bacaditempat.CreateDate >=', $startDate)
                  ->where('bacaditempat.CreateDate <=', $endDate);
        }

        // Filter berdasarkan bulan dan tahun dibuat
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        if ($month && $year) {
            $query->where('MONTH(bacaditempat.CreateDate)', $month)
                  ->where('YEAR(bacaditempat.CreateDate)', $year);
        }

        // Filter berdasarkan tahun saja (jika tidak ada bulan)
        $yearOnly = $this->request->getPost('year_only');
        if ($yearOnly && !$month) {
            $query->where('YEAR(bacaditempat.CreateDate)', $yearOnly);
        }

        // Filter berdasarkan jenis anggota
        $memberTypeId = $this->request->getPost('member_type_id');
        if ($memberTypeId) {
            $query->where('members.JenisAnggota_id', $memberTypeId);
        }

        // // Filter berdasarkan lokasi ruang
        $locationPerpus = $this->request->getPost('location_library_id');
        if ($locationPerpus) {
            $query->where('Location_Library_id', $locationPerpus);
        }

        // Filter berdasarkan no induk
        $noinduk = $this->request->getPost('noinduk'); 
        if ($noinduk) {
            $query->like('NoInduk', $noinduk);
        }

        // // Filter berdasarkan penerbit
        $publisher = $this->request->getPost('penerbit');
        if ($publisher) {
            $query->like('catalogs.Publisher', $publisher);
        }

    }

    // Helper function untuk get column label
    private function getColumnLabel($column)
    {
        $columnHeaders = [
            'no_pengunjung' => 'Nomor Pengunjung',
            'lokasi' => 'Lokasi Perpustakaan',
            'lok_ruang' => 'Lokasi Ruang',
            'periode' => 'Tanggal Kunjungan',
            'nama' => 'Nama',
            'noinduk' => 'No. Induk',
            'noanggota' => 'No. Anggota'
        ];

        return isset($columnHeaders[$column]) ? $columnHeaders[$column] : $column;
    }

    // Helper function untuk format nilai
    private function getFormattedValue($member, $column)
    {
        $value = '';

        // Handle special columns
        if ($column == 'jenis_anggota.jenisanggota') {
            $value = isset($member->jenisanggota) ? $member->jenisanggota : '';
        } elseif ($column == 'periode') {
            $value = isset($member->periode) ? date('d-m-Y', strtotime($member->periode)) : '';
        }
        else {
            // Extract column name without table prefix for object property access
            $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
            $value = isset($member->$columnName) ? $member->$columnName : '';
        }

        // Format date columns
        $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
        if (in_array($columnName, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
            $value = date('d-m-Y', strtotime($value));
        }

        return $value;
    }

}
