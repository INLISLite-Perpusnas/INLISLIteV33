<?php

namespace LaporanKatalog\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanKatalog extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $katalogModel;
    public $masterkelasbesarModel;
    public $userModel;

	function __construct()
	{
		$this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->masterkelasbesarModel = new \MasterKelasBesar\Models\MasterKelasBesarModel();
        $this->userModel = new \User\Models\UserModel();
	}

	public function index()
    {
        // Definisi kolom yang bisa diekspor
        $columns = [
            'ControlNumber' => 'No. Kontrol',
            'BIBID' => 'BIB ID',
            'Title' => 'Judul',
            'Author' => 'Pengarang',
            'Edition' => 'Edisi',
            'Publisher' => 'Penerbit',
            'PublishLocation' => 'Tempat Terbit',
            'PublishYear' => 'Tahun Terbit',
            'Subject' => 'Subjek',
            'PhysicalDescription' => 'Deskripsi Fisik',
            'ISBN' => 'ISBN',
            'CallNumber' => 'No. Panggil',
            'Languages' => 'Bahasa',
            'DeweyNo' => 'Klas DDC',
            'Note' => 'Abstrak',
            'IsOPAC' => 'Status OPAC',
            'IsBNI' => 'Status BNI',
            'IsKIN' => 'Status KIN',
            'IsRDA' => 'Status RDA',
            'CreateBy' => 'Dibuat Oleh',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateBy' => 'Diperbarui Oleh',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        $masterkelasbesarOptions = $this->masterkelasbesarModel->where('active', 1)->findAll();
        
        // Ambil data user untuk dropdown filter
        $userOptions = $this->userModel->select('id, username')
            ->where('active', 1)
            ->whereNotIn('category', ['anggota'])
            ->orderBy('username', 'ASC')
            ->findAll();

        $data = [
            'columns' => $columns,
            'masterkelasbesarOptions' => $masterkelasbesarOptions,
            'userOptions' => $userOptions
        ];

        return view('LaporanKatalog\Views\index', $data);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query dengan JOIN ke tabel users
        $query = $this->katalogModel->select($this->buildSelectColumns($columns))
            ->join('users as creator', 'catalogs.CreateBy = creator.id', 'left')
            ->join('users as updater', 'catalogs.UpdateBy = updater.id', 'left');

        // Apply multiple filters
        $this->applyFilters($query);

        // Get first 20 rows
        $katalogs = $query->limit(20)->find();

        if (empty($katalogs)) {
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

        foreach ($katalogs as $katalog) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $this->getFormattedValue($katalog, $column);
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
        ini_set('max_execution_time', 300);

        // Simplified validation - hanya require columns
        if (!$this->validate([
            'columns' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');
        
        // Check estimated record count first
        $countQuery = $this->katalogModel
            ->join('users as creator', 'catalogs.CreateBy = creator.id', 'left')
            ->join('users as updater', 'catalogs.UpdateBy = updater.id', 'left');

        $this->applyFilters($countQuery);
        $totalRecords = $countQuery->countAllResults();

        // Limit maksimum export untuk mencegah memory issue
        $maxRecords = 50000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with('error', 
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export adalah {$maxRecords} records. " .
                "Silakan gunakan filter yang lebih spesifik untuk mengurangi jumlah data."
            );
        }
        
        // Build query dengan JOIN ke tabel users
        $query = $this->katalogModel->select($this->buildSelectColumns($selectedColumns))
            ->join('users as creator', 'catalogs.CreateBy = creator.id', 'left')
            ->join('users as updater', 'catalogs.UpdateBy = updater.id', 'left');

        // Apply multiple filters
        $this->applyFilters($query);

        // Create Excel with optimized settings
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Katalog');

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $label = $this->getColumnLabel($column);
            $sheet->setCellValue($col . '1', $label);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Process data in chunks to manage memory
        $chunkSize = 1000;
        $offset = 0;
        $row = 2;

        do {
            $chunkQuery = clone $query;
            $katalogs = $chunkQuery->limit($chunkSize, $offset)->find();
            
            if (empty($katalogs)) {
                break;
            }

            foreach ($katalogs as $katalog) {
                $col = 'A';
                foreach ($selectedColumns as $column) {
                    $value = $this->getFormattedValue($katalog, $column);
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            $offset += $chunkSize;
            
            if ($offset % ($chunkSize * 5) === 0) {
                gc_collect_cycles();
            }

        } while (count($katalogs) === $chunkSize);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->setUseDiskCaching(true);
        $fileName = 'Laporan_Katalog_' . date('d-m-Y_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        
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
            $query->where('catalogs.CreateDate >=', $startDate)
                  ->where('catalogs.CreateDate <=', $endDate);
        }

        // Filter berdasarkan bulan dan tahun dibuat
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        if ($month && $year) {
            $query->where('MONTH(catalogs.CreateDate)', $month)
                  ->where('YEAR(catalogs.CreateDate)', $year);
        }

        // Filter berdasarkan tahun saja
        $yearOnly = $this->request->getPost('year_only');
        if ($yearOnly && !$month) {
            $query->where('YEAR(catalogs.CreateDate)', $yearOnly);
        }

        // Filter berdasarkan pengarang
        $author = $this->request->getPost('author');
        if ($author) {
            $query->like('catalogs.Author', $author);
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

        // Filter berdasarkan tempat terbit
        $publishLocation = $this->request->getPost('publishlocation');
        if ($publishLocation) {
            $query->like('catalogs.PublishLocation', $publishLocation);
        }

        // Filter berdasarkan dibuat oleh
        $createBy = $this->request->getPost('createby');
        if ($createBy) {
            $query->where('catalogs.CreateBy', $createBy);
        }

        // Filter berdasarkan diperbarui oleh
        $updateBy = $this->request->getPost('updateby');
        if ($updateBy) {
            $query->where('catalogs.UpdateBy', $updateBy);
        }

        // Filter berdasarkan Klas DDC
        $masterkelasbesarId = $this->request->getPost('masterkelasbesar_id');
        if ($masterkelasbesarId) {
            $query->like('catalogs.DeweyNo', $masterkelasbesarId);
        }
    }

    // Helper function untuk build select columns dengan JOIN
    private function buildSelectColumns($selectedColumns)
    {
        $selectFields = [];
        
        foreach ($selectedColumns as $column) {
            if ($column == 'CreateBy') {
                $selectFields[] = 'creator.username as CreateBy';
            } elseif ($column == 'UpdateBy') {
                $selectFields[] = 'updater.username as UpdateBy';
            } else {
                $selectFields[] = 'catalogs.' . $column;
            }
        }
        
        return implode(', ', $selectFields);
    }

    // Helper function untuk get column label
    private function getColumnLabel($column)
    {
        $columnLabels = [
            'ControlNumber' => 'No. Kontrol',
            'BIBID' => 'BIB ID',
            'Title' => 'Judul',
            'Author' => 'Pengarang',
            'Edition' => 'Edisi',
            'Publisher' => 'Penerbit',
            'PublishLocation' => 'Tempat Terbit',
            'PublishYear' => 'Tahun Terbit',
            'Subject' => 'Subjek',
            'PhysicalDescription' => 'Deskripsi Fisik',
            'ISBN' => 'ISBN',
            'CallNumber' => 'No. Panggil',
            'Languages' => 'Bahasa',
            'DeweyNo' => 'Klas DDC',
            'Note' => 'Abstrak',
            'IsOPAC' => 'Status OPAC',
            'IsBNI' => 'Status BNI',
            'IsKIN' => 'Status KIN',
            'IsRDA' => 'Status RDA',
            'CreateBy' => 'Dibuat Oleh',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateBy' => 'Diperbarui Oleh',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        return isset($columnLabels[$column]) ? $columnLabels[$column] : $column;
    }

    // Helper function untuk format nilai
    private function getFormattedValue($katalog, $column)
    {
        $value = $katalog->$column;
        
        // Format boolean values
        if (in_array($column, ['IsOPAC', 'IsBNI', 'IsKIN', 'IsRDA'])) {
            $value = $value ? 'Ya' : 'Tidak';
        }
        
        // Format dates
        if (in_array($column, ['CreateDate', 'UpdateDate']) && $value) {
            $value = date('d-m-Y', strtotime($value));
        }
        
        return $value;
    }
}