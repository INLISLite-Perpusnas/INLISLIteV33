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
            'ISBN' => 'ISBN',
            'CallNumber' => 'No. Panggil',
            'Languages' => 'Bahasa',
            'DeweyNo' => 'No. Dewey',
            'IsOPAC' => 'Status OPAC',
            'IsBNI' => 'Status BNI',
            'IsKIN' => 'Status KIN',
            'IsRDA' => 'Status RDA',
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        $data = [
            'columns' => $columns
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
            'filter_type' => 'required|in_list[date,month,year]',
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
        }

        $katalogs = $query->findAll();

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $sheet->setCellValue($col . '1', $column);
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
