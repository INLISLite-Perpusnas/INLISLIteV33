<?php

namespace LaporanSirkulasi\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanSirkulasi extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;

    public $collectionLoanModel;
    public $db;
    function __construct()
    {
        $this->collectionLoanModel = new \LaporanSirkulasi\Models\CollectionLoanModel();
        $this->db = db_connect('data');
    }

    public function index()
    {
        // Get all available columns
        $columns = [
            'TglPinjam' => 'Tanggal Pinjam',
            'TglJatuhTempo' => 'Tanggal Jatuh Tempo',
            'TglDikembalikan' => 'Tanggal Dikembalikan',
            'JumlahHariTelat' => 'Jumlah Hari Telat',
            'no_induk' => 'No Induk',
            'DataBib' => 'Data Bibliografi',
            'NoAnggota' => 'No Anggota',
            'NamaAnggota' => 'Nama Anggota',
            'J_kelamin' => 'Jenis Kelamin',
            'umur' => 'Umur',
            'nomor_klass' => 'Nomor DDC',
            'PetugasPeminjaman' => 'Petugas Peminjaman',
            'PetugasPengembalian' => 'Petugas Pengembalian'
            // Add more columns as needed
        ];

        //  // Get gender options for filter
        // $genderOptions = $this->anggotaModel
        //     ->select('jenis_kelamin.id, jenis_kelamin.Name')
        //     ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
        //     ->groupBy('jenis_kelamin.ID')
        //     ->orderBy('jenis_kelamin.Name')
        //     ->findAll();

        //  // Get location options for filter
        // $locationOptions = $this->lokasiperpustakaanModel
        //     ->select('ID as code, Name as name')
        //     ->groupBy('ID')
        //     ->orderBy('Name')
        //     ->findAll();

        // $destinationOptions = $this->tujuanKunjunganModel
        //     ->select('ID as code, TujuanKunjungan as name')
        //     ->groupBy('ID')
        //     ->orderBy('TujuanKunjungan')
        //     ->findAll();

        $data = [
            'columns' => $columns,
            // 'genderOptions' => $genderOptions,
            // 'locationOptions' => $locationOptions,
            // 'destinationOptions' => $destinationOptions
        ];

        return view('LaporanSirkulasi\Views\index', $data);
    }

    public function export()
    {
        // Validasi request
        if (!$this->validate([
            'columns' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns   = $this->request->getPost('columns');
        $filterType        = $this->request->getPost('filter_type');
        $startDate         = $this->request->getPost('start_date');
        $endDate           = $this->request->getPost('end_date');
        $month             = $this->request->getPost('month');
        $year              = $this->request->getPost('year');
        // $genderId          = $this->request->getPost('gender_id');
        // $visitor_type      = $this->request->getPost('visitor_type');
        // $locationlibrary   = $this->request->getPost('location');
        // $room              = $this->request->getPost('room');
        // $destination       = $this->request->getPost('destination');
        // $branch_id         = branch_id();
        $kop               = $this->request->getPost('kop');

        // Ambil builder
        $query = $this->collectionLoanModel->getPeminjaman($startDate, $endDate);

        // // Filter role user
        // if (user()->category == 'admin') {
        //     // semua data
        // } elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
        //     $npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
        //     $query->where('b.NPP_Provinsi_id', $npp_provinsi_id);
        // } elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
        //     $query->where('mg.Branch_id', branch_id());
        // } elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
        //     $npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
        //     $query->where('b.NPP_KabKota_id', $npp_kabkota_id);
        // } else {
        //     $query->where('mg.Branch_id', branch_id());
        // }

        // Filter tambahan
        switch ($filterType) {
            case 'month':
                if ($month && $year) {
                    $query->where('MONTH(TglPinjam)', $month)
                        ->where('YEAR(TglPinjam)', $year);
                }
                break;
            case 'year':
                if ($year) {
                    $query->where('YEAR(TglPinjam)', $year);
                }
                break;
        }

        // if ($genderId)      $query->like('gender', $genderId);
        // if ($visitor_type)  $query->where('ket', $visitor_type);
        // if ($locationlibrary) $query->where('Library_id', $locationlibrary);
        // if ($room)          $query->where('lok_ruang', $room);
        // if ($destination)   $query->where('tujuan', $destination);

        $peminjaman = $query->get()->getResult();

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // === HEADER: LOGO + JUDUL + TANGGAL ===
        $titleRow = 1;
        $dateRow  = 2;

        if ($kop === 'Ya') {
            $this->db = db_connect('data');
            $logokop = $this->db->table('settingparameters')
                ->where('Name', 'LogoKop')
                ->get()
                ->getRow('Value') ?? "";

            if ($logokop) {
                $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setResizeProportional(true); // jaga rasio
                    $drawing->setWorksheet($sheet);

                    // Sesuaikan tinggi row 1 otomatis
                    $imgSize = getimagesize($logoPath);
                    $sheet->getRowDimension('1')->setRowHeight($imgSize[1] / 1.33);

                    $titleRow = 2;
                    $dateRow  = 3;

                    // Optional: kolom A lebih lebar supaya logo muat
                    $sheet->getColumnDimension('A')->setWidth(20);
                }
            }
        }

        $lastColumn = chr(ord('A') + count($selectedColumns) - 1);

        // Judul laporan
        $sheet->mergeCells("A{$titleRow}:{$lastColumn}{$titleRow}");
        $sheet->setCellValue("A{$titleRow}", "LAPORAN SIRKULASI PEMINJAMAN KOLEKSI");
        $sheet->getStyle("A{$titleRow}")->getFont()
            ->setBold(true)
            ->setSize(16);
        $sheet->getStyle("A{$titleRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Tanggal export
        $sheet->mergeCells("A{$dateRow}:{$lastColumn}{$dateRow}");
        $sheet->setCellValue("A{$dateRow}", "Tanggal Export: " . date('d-m-Y H:i'));
        $sheet->getStyle("A{$dateRow}")->getFont()
            ->setItalic(true)
            ->setSize(11);
        $sheet->getStyle("A{$dateRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // === HEADER TABEL ===
        $startRowHeader = $dateRow + 2;
        $columnHeaders = [
            'TglPinjam' => 'Tanggal Pinjam',
            'TglJatuhTempo' => 'Tanggal Jatuh Tempo',
            'TglDikembalikan' => 'Tanggal Dikembalikan',
            'JumlahHariTelat' => 'Jumlah Hari Telat',
            'no_induk' => 'No Induk',
            'DataBib' => 'Data Bibliografi',
            'NoAnggota' => 'No Anggota',
            'NamaAnggota' => 'Nama Anggota',
            'J_kelamin' => 'Jenis Kelamin',
            'umur' => 'Umur',
            'nomor_klass' => 'Nomor DDC',
            'PetugasPeminjaman' => 'Petugas Peminjaman',
            'PetugasPengembalian' => 'Petugas Pengembalian'
        ];

        $col = 'A';
        foreach ($selectedColumns as $column) {
            $headerName = $columnHeaders[$column] ?? $column;
            $sheet->setCellValue($col . $startRowHeader, $headerName);
            $sheet->getStyle($col . $startRowHeader)->getFont()->setBold(true);
            $sheet->getStyle($col . $startRowHeader)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $col++;
        }

        // === DATA ROWS ===
        $row = $startRowHeader + 1;
        foreach ($peminjaman as $item) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $item->$column ?? '';
                if ($column == 'TglPinjam' && $value) {
                    $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($value));
                    $sheet->setCellValue($col . $row, $dateTime);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                $col++;
            }
            $row++;
        }

        // Auto size kolom
        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Border tabel
        $lastRow = $row - 1;
        $sheet->getStyle("A{$startRowHeader}:{$lastColumn}{$lastRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Freeze header
        $sheet->freezePane('A' . ($startRowHeader + 1));

        // Output file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Export_Sirkulasi_' . date('d-m-Y_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $filterType = $this->request->getPost('filter_type');
        $startDate  = $this->request->getPost('start_date');
        $endDate    = $this->request->getPost('end_date');
        $month      = $this->request->getPost('month');
        $year       = $this->request->getPost('year');


        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // ambil builder
        $query = $this->collectionLoanModel->getPeminjaman($startDate, $endDate, 20,0);
        
        // filter tambahan sesuai tipe
        switch ($filterType) {
            case 'month':
                if ($month && $year) {
                    $query->where('MONTH(TglPinjam)', $month)
                        ->where('YEAR(TglPinjam)', $year);
                }
                break;

            case 'year':
                if ($year) {
                    $query->where('YEAR(TglPinjam)', $year);
                }
                break;
        }

        // eksekusi query
        $peminjaman = $query->get()->getResult();
        // $peminjaman = $this->db->query($query)->getResult();
        // dd($peminjaman);

        if (empty($peminjaman)) {
            return '<div class="alert alert-info">Tidak ada data yang ditemukan dengan filter yang dipilih</div>';
        }

        // Build preview table
        $html = '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>';

        foreach ($columns as $key => $label) {
            $html .= '<th>' . esc($label) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($peminjaman as $item) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $item->$column;
                if (in_array($column, ['TglPinjam']) && $value) {
                    $value = date('d-m-Y', strtotime($value));
                }
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }
}
