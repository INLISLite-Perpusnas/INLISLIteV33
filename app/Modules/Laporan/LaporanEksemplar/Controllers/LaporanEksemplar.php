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

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
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
            'CreateDate' => 'Tanggal Dibuat',
            'UpdateDate' => 'Tanggal Diperbarui'
        ];

        $data = [
            'columns' => $columns
        ];

        return view('LaporanEksemplar\Views\index', $data);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $filterType = $this->request->getPost('filter_type');
     
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query based on filter     
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
                ->select($columns);

     

        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('collections.CreateDate >=', $startDate)
                          ->where('collections.CreateDate <=', $endDate);
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

        // Get first 20 rows
        $eksemplars = $query->limit(20)->find();

        if (empty($eksemplars)) {
            return '<div class="alert alert-info">Tidak ada data yang ditemukan dengan filter yang dipilih</div>';
        }

        // Build preview table
        $html = '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>';
        
        foreach ($columns as $column) {
            if($column == 'NomorBarcode') $column = 'Nomor Barcode';
            else if($column == 'TanggalPengadaan') $column = 'Tanggal Pengadaan';
            else if($column == 'NoInduk') $column = 'No. Induk';
            else if($column == 'Title') $column = 'Judul';
            else if($column == 'Author') $column = 'Pengarang';
            else if($column == 'Edition') $column = 'Edisi'; 
            else if($column == 'Publisher') $column = 'Penerbit';
            else if($column == 'Subject') $column = ' Subjek';
            else if($column == 'ISBN') $column = ' ISBN';
            else if($column == 'CallNumber') $column = ' No. Panggil';
            else if($column == 'Languages') $column = ' Bahasa';
            else if($column == 'DeweyNo') $column = ' No. Dewey';
            else if($column == 'RFID') $column = ' No. RFID';
            else if($column == 'JenisSumber') $column = ' Jenis Sumber';
            else if($column == 'BentukFisik') $column = ' Bentuk Fisik';
            else if($column == 'Kategori') $column = ' Kategori';
            else if($column == 'Akses') $column = ' Akses';
            else if($column == 'LokasiRuang') $column = ' Lokasi Ruang';
            else if($column == 'NamaSumber') $column = ' Nama Sumber';
            else if($column == 'Ketersediaan') $column = ' Ketersediaan';
            else if($column == 'IsOPAC') $column = ' Status OPAC';
            else if($column == 'IsDRM') $column = ' Status DRM';
            else if($column == 'Currency') $column = ' Mata Uang';
            else if($column == 'Price') $column = ' Harga';
            else if($column == 'PriceType') $column = ' Satuan Harga';
            else if($column == 'Perpustakaan') $column = ' Lokasi Perpustakaan';
            else if($column == 'CreateDate') $column = ' Tanggal Dibuat';
            else if($column == 'UpdateDate') $column = ' Tanggal Diperbarui';
            else $column = $column;
            $html .= '<th>' . esc($column).'</th>';
        }
        
        $html .= '</tr></thead><tbody>';

        foreach ($eksemplars as $eksemplar) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $eksemplar->$column;
                // Format boolean values
                if (in_array($column, ['IsOPAC', 'IsDRM'])) {
                    $value = $value ? 'Ya' : 'Tidak';
                }
                // Format dates
                if (in_array($column, ['CreateDate', 'UpdateDate','TanggalPengadaan']) && $value) {
                    $value = date('d-m-Y', strtotime($value));
                }
                // Format number
                if (in_array($column, ['Price']) && $value) {
                    $value =  number_format($value,2);
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
      
        
        // Build query
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
                ->select($selectedColumns);

      
        // Apply filters
        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('collections.CreateDate >=', $startDate)
                          ->where('collections.CreateDate <=', $endDate);
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

        $eksemplars = $query->findAll();

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            if($column == 'NomorBarcode') $column = 'Nomor Barcode';
            else if($column == 'TanggalPengadaan') $column = 'Tanggal Pengadaan';
            else if($column == 'NoInduk') $column = 'No. Induk';
            else if($column == 'Title') $column = 'Judul';
            else if($column == 'Author') $column = 'Pengarang';
            else if($column == 'Edition') $column = 'Edisi'; 
            else if($column == 'Publisher') $column = 'Penerbit';
            else if($column == 'Subject') $column = ' Subjek';
            else if($column == 'ISBN') $column = ' ISBN';
            else if($column == 'CallNumber') $column = ' No. Panggil';
            else if($column == 'Languages') $column = ' Bahasa';
            else if($column == 'DeweyNo') $column = ' No. Dewey';
            else if($column == 'RFID') $column = ' No. RFID';
            else if($column == 'JenisSumber') $column = ' Jenis Sumber';
            else if($column == 'BentukFisik') $column = ' Bentuk Fisik';
            else if($column == 'Kategori') $column = ' Kategori';
            else if($column == 'Akses') $column = ' Akses';
            else if($column == 'LokasiRuang') $column = ' Lokasi Ruang';
            else if($column == 'NamaSumber') $column = ' Nama Sumber';
            else if($column == 'Ketersediaan') $column = ' Ketersediaan';
            else if($column == 'IsOPAC') $column = ' Status OPAC';
            else if($column == 'IsDRM') $column = ' Status DRM';
            else if($column == 'Currency') $column = ' Mata Uang';
            else if($column == 'Price') $column = ' Harga';
            else if($column == 'PriceType') $column = ' Satuan Harga';
            else if($column == 'Perpustakaan') $column = ' Lokasi Perpustakaan';
            else if($column == 'CreateDate') $column = ' Tanggal Dibuat';
            else if($column == 'UpdateDate') $column = ' Tanggal Diperbarui';
            else $column = $column;
            $sheet->setCellValue($col . '1', $column);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Add data rows
        $row = 2;
        foreach ($eksemplars as $eksemplar) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $eksemplar->$column;
                // Format boolean values
                if (in_array($column, ['IsOPAC', 'IsDRM'])) {
                    $value = $value ? 'Ya' : 'Tidak';
                }
                // Format dates
                if (in_array($column, ['collections.CreateDate', 'collections.UpdateDate','collections.TanggalPengadaan']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
                }
                // Format number
                if (in_array($column, ['Price']) && $value) {
                    $value =  number_format($value,2);
                }
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


		$this->data['title'] = 'Laporan - Eksemplar';
		echo view('Report\Views\member_list', $this->data);
	}
}
