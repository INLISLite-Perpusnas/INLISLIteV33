<?php

namespace Report\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $visitorModel;

	function __construct()
	{
		$this->visitorModel = new \Report\Models\VisitorModel();
	}

	public function index()
	{
		echo "Report";
	}

	public function visitor()
	{


		$this->data['title'] = 'Laporan - Visitor';
		echo view('Report\Views\visitor_list', $this->data);
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
