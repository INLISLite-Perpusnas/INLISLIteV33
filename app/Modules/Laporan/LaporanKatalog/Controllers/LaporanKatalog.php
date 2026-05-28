<?php

namespace LaporanKatalog\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $katalogs = $query->limit(100)->find();

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

    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        if (!$this->validate(['columns' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');

        $countQuery = $this->katalogModel
            ->join('users as creator', 'catalogs.CreateBy = creator.id', 'left')
            ->join('users as updater', 'catalogs.UpdateBy = updater.id', 'left');
        $this->applyFilters($countQuery);
        $totalRecords = $countQuery->countAllResults();

        $maxRecords = 5000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with('error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export PDF adalah {$maxRecords} records. " .
                "Silakan gunakan filter yang lebih spesifik atau gunakan export Excel."
            );
        }

        $query = $this->katalogModel->select($this->buildSelectColumns($selectedColumns))
            ->join('users as creator', 'catalogs.CreateBy = creator.id', 'left')
            ->join('users as updater', 'catalogs.UpdateBy = updater.id', 'left');
        $this->applyFilters($query);
        $katalogs = $query->find();

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
               . '<p>Laporan Katalog &mdash; Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN DATA KATALOG</h3>';

        // Table
        $html .= '<table><thead><tr>';
        $html .= '<th>#</th>';
        foreach ($selectedColumns as $col) {
            $html .= '<th>' . esc($this->getColumnLabel($col)) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($katalogs as $katalog) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($selectedColumns as $col) {
                $html .= '<td>' . esc($this->getFormattedValue($katalog, $col)) . '</td>';
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

        $fileName = 'Laporan_Katalog_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
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