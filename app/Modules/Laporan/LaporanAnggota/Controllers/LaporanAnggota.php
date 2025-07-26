<?php

namespace LaporanAnggota\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanAnggota extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $anggotaModel;

	function __construct()
	{
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
	}

	public function index()
    {
        // Get all available columns
        $columns = [
            'MemberNo' => 'Nomor Anggota',
            'Fullname' => 'Nama Lengkap',
            'PlaceOfBirth' => 'Tempat Lahir',
            'DateOfBirth' => 'Tanggal Lahir',
            'Address' => 'Alamat',
            'Phone' => 'Telepon',
            'Email' => 'Email',
            'InstitutionName' => 'Nama Institusi',
            'IdentityNo' => 'Nomor Identitas',
            'RegisterDate' => 'Tanggal Registrasi',
            'EndDate' => 'Tanggal Berakhir'
            // Add more columns as needed
        ];

        $data = [
            'columns' => $columns
        ];
		
        return view('LaporanAnggota\Views\index', $data);
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
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
	

        // Build query based on filter
        $query = $this->anggotaModel->select($selectedColumns);
		

        switch ($filterType) {
            case 'date':
                if ($startDate && $endDate) {
                    $query->where('RegisterDate >=', $startDate)
                          ->where('RegisterDate <=', $endDate);
                }
                break;
            case 'month':
                if ($month && $year) {
                    $query->where('MONTH(RegisterDate)', $month)
                          ->where('YEAR(RegisterDate)', $year);
                }
                break;
            case 'year':
                if ($year) {
                    $query->where('YEAR(RegisterDate)', $year);
                }
                break;
        }

        $members = $query->findAll();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $sheet->setCellValue($col . '1', $column);
            $col++;
        }

        // Add data rows
        $row = 2;
        foreach ($members as $member) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $member->$column;
                if (in_array($column, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
                }
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'members_export_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
       
		redirect()->back();
    }
	public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $filterType = $this->request->getPost('filter_type');
		$branch_id = branch_id();
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query based on filter
        $query = $this->anggotaModel->select($columns);
		
		

        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('RegisterDate >=', $startDate)
                          ->where('RegisterDate <=', $endDate);
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(RegisterDate)', $month)
                          ->where('YEAR(RegisterDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(RegisterDate)', $year);
                }
                break;
        }

        // Get first 20 rows
        $members = $query->limit(20)->find();

        if (empty($members)) {
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

        foreach ($members as $member) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $member->$column;
                if (in_array($column, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
                }
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
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


		$this->data['title'] = 'Laporan - Anggota';
		echo view('Report\Views\member_list', $this->data);
	}
}
