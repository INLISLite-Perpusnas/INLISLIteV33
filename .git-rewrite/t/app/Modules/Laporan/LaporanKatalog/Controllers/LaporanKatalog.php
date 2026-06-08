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

	function __construct()
	{
		$this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->masterkelasbesarModel = new \MasterKelasBesar\Models\MasterKelasBesarModel();
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
            'IsOPAC' => 'Status OPAC',
            'IsBNI' => 'Status BNI',
            'IsKIN' => 'Status KIN',
            'IsRDA' => 'Status RDA',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        $masterkelasbesarOptions = $this->masterkelasbesarModel->where('active', 1)->findAll();

        $data = [
            'columns' => $columns,
            'masterkelasbesarOptions' => $masterkelasbesarOptions
        ];

        return view('LaporanKatalog\Views\index', $data);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $filterType = $this->request->getPost('filter_type');    
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query based on filter
        $query = $this->katalogModel->select($columns);

       
        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('CreateDate >=', $startDate)
                          ->where('CreateDate <=', $endDate);
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(CreateDate)', $month)
                          ->where('YEAR(CreateDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(CreateDate)', $year);
                }
                break;

             case 'author':
                $author = $this->request->getPost('author');
                if ($author) {
                    $query->like('catalogs.Author', $author);
                }
                break;
            case 'subject':
                $subject = $this->request->getPost('subject');
                if ($subject) {
                    $query->like('catalogs.Subject', $subject);
                }
                break;
            case 'publisher':
                $publisher = $this->request->getPost('publisher'); 
                if ($publisher) {
                    $query->like('catalogs.Publisher', $publisher);
                }
                break;
            case 'publishlocation':
                $publishLocation = $this->request->getPost('publishlocation'); 
                if ($publishLocation) {
                    $query->like('catalogs.PublishLocation', $publishLocation);
                }
                break;
        }

        
        $masterkelasbesarId = $this->request->getPost('masterkelasbesar_id');
        if ($masterkelasbesarId &&  $masterkelasbesarId != '') {
            $query->like('catalogs.DeweyNo',  $masterkelasbesarId);
        }

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
            if($column == 'ControlNumber') $column = 'No Kontrol';
            else if ($column == 'BIBID') $column = 'BIB ID';
            else if ($column == 'Title') $column = 'Judul';
            else if ($column == 'Author') $column = 'Pengarang';
            else if ($column == 'Edition') $column = 'Edisi';
            else if ($column == 'Publisher') $column = 'Penerbit';
            else if ($column == 'PublishLocation') $column = 'Tempat Terbit';
            else if ($column == 'PublishYear') $column = 'Tahun Terbit';
            else if ($column == 'Subject') $column = 'Subjek';
            else if ($column == 'PhysicalDescription') $column = 'Deskripsi Fisik';
            else if ($column == 'ISBN') $column = 'ISBN';
            else if ($column == 'CallNumber') $column = 'No Panggil';
            else if ($column == 'Languages') $column = 'Bahasa';
            else if ($column == 'DeweyNo') $column = 'Klas DDC';
            else if ($column == 'IsOPAC') $column = 'Status OPAC';
            else if ($column == 'IsBNI') $column = 'Status BNI';
            else if ($column == 'IsKIN') $column = 'Status KIN';
            else if ($column == 'IsRDA') $column = 'Status RDA';
            else if ($column == 'CreateDate') $column = 'Tanggal Dibuat';
            else if ($column == 'UpdateDate') $column = 'Tanggal Diperbarui';          
            $html .= '<th>' . esc($column) . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';

        foreach ($katalogs as $katalog) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $katalog->$column;
                // Format boolean values
                if (in_array($column, ['IsOPAC', 'IsBNI', 'IsKIN', 'IsRDA'])) {
                    $value = $value ? 'Ya' : 'Tidak';
                }
                // Format dates
                if (in_array($column, ['CreateDate', 'UpdateDate']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
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
        // Validate request
        if (!$this->validate([
            'columns' => 'required',
            'filter_type' => 'required|in_list[date,month,year,author,subject,publisher,publishlocation]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');
        $filterType = $this->request->getPost('filter_type');
        $branch_id = branch_id();
        
        // Build query
        $query = $this->katalogModel->select($selectedColumns);

  
        // Apply filters
        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('CreateDate >=', $startDate)
                          ->where('CreateDate <=', $endDate);
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(CreateDate)', $month)
                          ->where('YEAR(CreateDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(CreateDate)', $year);
                }
                break;
            case 'author':
                $author = $this->request->getPost('author');
                if ($author) {
                    $query->like('catalogs.Author', $author);
                }
                break;
            case 'subject':
                $subject = $this->request->getPost('subject');
                if ($subject) {
                    $query->like('catalogs.Subject', $subject);
                }
                break;
            case 'publisher':
                $publisher = $this->request->getPost('publisher'); 
                if ($publisher) {
                    $query->like('catalogs.Publisher', $publisher);
                }
                break;
            case 'publishlocation':
                $publishLocation = $this->request->getPost('publishlocation'); 
                if ($publishLocation) {
                    $query->like('catalogs.PublishLocation', $publishLocation);
                }
                break;
        }

        $masterkelasbesarId = $this->request->getPost('masterkelasbesar_id');
        if ($masterkelasbesarId &&  $masterkelasbesarId != '') {
            $query->like('catalogs.DeweyNo',  $masterkelasbesarId);
        }

        $katalogs = $query->findAll();

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat mapping nama field ke label header
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
            'IsOPAC' => 'Status OPAC',
            'IsBNI' => 'Status BNI',
            'IsKIN' => 'Status KIN',
            'IsRDA' => 'Status RDA',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $label = isset($columnLabels[$column]) ? $columnLabels[$column] : $column;
            $sheet->setCellValue($col . '1', $label);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
    

        // Add data rows
        $row = 2;
        foreach ($katalogs as $katalog) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $katalog->$column;
                // Format boolean values
                if (in_array($column, ['IsOPAC', 'IsBNI', 'IsKIN', 'IsRDA'])) {
                    $value = $value ? 'Ya' : 'Tidak';
                }
                // Format dates
                if (in_array($column, ['CreateDate', 'UpdateDate']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
                }
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'katalog_export_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

	public function visitor_export()
	{


		$from_date = $this->request->getGet('from_date');
		$to_date = $this->request->getGet('to_date');

		$query = $this->visitorModel;
		if (!empty($from_date)) {
			$query->where('timestamp >=', $from_date);
		}

		if (!empty($to_date)) {
			$query->where('timestamp <=', $to_date);
		}

		$visitors = $query->orderBy('timestamp', 'desc')->findAll();

		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A1', 'No')
			->setCellValue('B1', 'Tanggal Kunjungan')
			->setCellValue('C1', 'Jumlah Kunjungan')
			->setCellValue('D1', 'Alamat IP')
			->setCellValue('E1', 'Kota')
			->setCellValue('F1', 'Negara');

		$col = 2;
		$no = 1;
		foreach ($visitors as $row) {
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A' . $col, $no)
				->setCellValue('B' . $col, $row->timestamp)
				->setCellValue('C' . $col, $row->hits)
				->setCellValue('D' . $col, $row->ip_address)
				->setCellValue('E' . $col, $row->ip_city)
				->setCellValue('F' . $col, $row->ip_country);
			$col++;
			$no++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Laporan Kujungan';
		$filename = ucwords($subject) . '-' . date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		ob_end_clean();
		$writer->save('php://output');
	}

	public function member()
	{


		$this->data['title'] = 'Laporan - Katalog';
		echo view('Report\Views\member_list', $this->data);
	}
}
