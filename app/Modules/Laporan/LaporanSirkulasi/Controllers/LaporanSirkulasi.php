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
        $columnsAnggota = [
            'NoAnggota' => 'Nomor Anggota',
            'NamaAnggota' => 'Nama Anggota',
            'Alamat' => 'Alamat',
            'Phone' => 'No. Telepon',
            'Email' => 'Email',
            'JumlahPeminjaman' => 'Jumlah Peminjaman'
        ];
        $columnsKoleksi = [
            'JudulBuku' => 'Judul Buku',
            'Pengarang' => 'Pengarang',
            'Penerbit' => 'Penerbit',
            'TahunTerbit' => 'Tahun Terbit',
            'TempatTerbit' => 'Tempat Terbit',
            'ISBN' => 'ISBN',
            'NoPanggil' => 'No. Panggil',
            'NoDDC' => 'No. DDC',
            'JumlahPeminjaman' => 'Jumlah Peminjaman'
        ];
        // $petugas = $this->userModel
        //     ->select('ID, FullName')
        //     ->where('active', 1)
        //     ->orderBy('FullName', 'ASC')
        //     ->findAll();
        // dd($petugas);
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
            'columnsAnggota' => $columnsAnggota,
            'columnsKoleksi' => $columnsKoleksi,
            // 'petugas' => $petugas,
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
            'columns' => 'required',
            'report_type' => 'required' // Tambahkan validasi untuk report_type
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');
        $filterType = $this->request->getPost('filter_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        $kop = $this->request->getPost('kop');
        $reportType = $this->request->getPost('report_type'); // Ambil jenis laporan

        $query = null;
        $reportTitle = "";
        $data = [];

        // Pilih fungsi builder model dan judul laporan berdasarkan jenis laporan
        switch ($reportType) {
            case 'peminjaman':
                $query = $this->collectionLoanModel->getPeminjaman($startDate, $endDate);
                $reportTitle = "LAPORAN SIRKULASI PEMINJAMAN KOLEKSI";
                // Filter tambahan khusus untuk laporan peminjaman
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
                $data = $query->get()->getResultArray(); // Gunakan getResultArray() untuk kemudahan
                break;

            case 'anggota':
                $query = $this->collectionLoanModel->getAnggotaSeringMeminjam($startDate, $endDate);
                $reportTitle = "LAPORAN ANGGOTA PALING SERING MEMINJAM";
                // Filter tambahan khusus untuk laporan anggota
                switch ($filterType) {
                    case 'month':
                        if ($month && $year) {
                            $query->where('MONTH(collectionloanitems.LoanDate)', $month)
                                ->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                    case 'year':
                        if ($year) {
                            $query->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                }
                $data = $query->get()->getResultArray();
                break;

            case 'koleksi':
                $query = $this->collectionLoanModel->getKoleksiSeringDipinjam($startDate, $endDate);
                $reportTitle = "LAPORAN KOLEKSI PALING SERING DIPINJAM";
                // Filter tambahan khusus untuk laporan koleksi
                switch ($filterType) {
                    case 'month':
                        if ($month && $year) {
                            $query->where('MONTH(collectionloanitems.LoanDate)', $month)
                                ->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                    case 'year':
                        if ($year) {
                            $query->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                }
                $data = $query->get()->getResultArray();
                break;
        }

        if (empty($data)) {
            return redirect()->back()->with('errors', ['Tidak ada data yang ditemukan dengan filter yang dipilih.']);
        }

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === HEADER: LOGO + JUDUL + TANGGAL ===
        // Logika kop laporan tetap sama
        $titleRow = 1;
        $dateRow = 2;
        // ... (kode kop tetap sama) ...
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
                    $drawing->setResizeProportional(true);
                    $drawing->setWorksheet($sheet);
                    $imgSize = getimagesize($logoPath);
                    $sheet->getRowDimension('1')->setRowHeight($imgSize[1] / 1.33);
                    $titleRow = 2;
                    $dateRow = 3;
                    $sheet->getColumnDimension('A')->setWidth(20);
                }
            }
        }

        $lastColumn = chr(ord('A') + count($selectedColumns) - 1);

        // Judul laporan dinamis
        $sheet->mergeCells("A{$titleRow}:{$lastColumn}{$titleRow}");
        $sheet->setCellValue("A{$titleRow}", $reportTitle);
        $sheet->getStyle("A{$titleRow}")->getFont()
            ->setBold(true)
            ->setSize(16);
        $sheet->getStyle("A{$titleRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Tanggal export tetap sama
        $sheet->mergeCells("A{$dateRow}:{$lastColumn}{$dateRow}");
        $sheet->setCellValue("A{$dateRow}", "Tanggal Export: " . date('d-m-Y H:i'));
        $sheet->getStyle("A{$dateRow}")->getFont()
            ->setItalic(true)
            ->setSize(11);
        $sheet->getStyle("A{$dateRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // === HEADER TABEL ===
        $startRowHeader = $dateRow + 2;
        // Definisikan semua header yang mungkin
        $allColumnHeaders = [
            'peminjaman' => [
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
            ],
            'anggota' => [
                'NoAnggota' => 'Nomor Anggota',
                'NamaAnggota' => 'Nama Anggota',
                'Alamat' => 'Alamat',
                'Phone' => 'No. Telepon',
                'Email' => 'Email',
                'JumlahPeminjaman' => 'Jumlah Peminjaman'
            ],
            'koleksi' => [
                'JudulBuku' => 'Judul Buku',
                'Pengarang' => 'Pengarang',
                'Penerbit' => 'Penerbit',
                'TahunTerbit' => 'Tahun Terbit',
                'TempatTerbit' => 'Tempat Terbit',
                'ISBN' => 'ISBN',
                'NoPanggil' => 'No. Panggil',
                'NoDDC' => 'No. DDC',
                'JumlahPeminjaman' => 'Jumlah Peminjaman'
            ]
        ];
        $columnHeaders = $allColumnHeaders[$reportType];

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
        foreach ($data as $item) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = $item[$column] ?? ''; // Menggunakan array key untuk data dari getResultArray()

                // Format tanggal
                if (in_array($column, ['TglPinjam', 'TglDikembalikan', 'TglJatuhTempo']) && !empty($value)) {
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
        $fileName = str_replace(' ', '_', $reportTitle) . '_' . date('d-m-Y_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $reportType = $this->request->getPost('report_type'); // Ambil jenis laporan dari POST
        $filterType = $this->request->getPost('filter_type');
        $startDate  = $this->request->getPost('start_date');
        $endDate    = $this->request->getPost('end_date');
        $month      = $this->request->getPost('month');
        $year       = $this->request->getPost('year');
        $petugas    = $this->request->getPost('petugas');


        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }
        $query = null;
        $result = [];
        switch ($reportType) {
            case 'peminjaman':
                $query = $this->collectionLoanModel->getPeminjaman($startDate, $endDate, 20, 0);

                // Filter tambahan khusus untuk laporan peminjaman
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
                $result = $query->get()->getResult();
                break;

            case 'anggota':
                $query = $this->collectionLoanModel->getAnggotaSeringMeminjam($startDate, $endDate, 20, 0);

                // Filter tambahan khusus untuk laporan anggota
                switch ($filterType) {
                    case 'month':
                        if ($month && $year) {
                            $query->where('MONTH(collectionloanitems.LoanDate)', $month)
                                ->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                    case 'year':
                        if ($year) {
                            $query->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                }
                $result = $query->get()->getResult();
                break;

            case 'koleksi':
                $query = $this->collectionLoanModel->getKoleksiSeringDipinjam($startDate, $endDate, 20, 0);

                // Filter tambahan khusus untuk laporan koleksi
                switch ($filterType) {
                    case 'month':
                        if ($month && $year) {
                            $query->where('MONTH(collectionloanitems.LoanDate)', $month)
                                ->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                    case 'year':
                        if ($year) {
                            $query->where('YEAR(collectionloanitems.LoanDate)', $year);
                        }
                        break;
                }
                $result = $query->get()->getResult();
                break;

            default:
                // Jika reportType tidak valid, berikan pesan error
                return '<div class="alert alert-danger">Jenis laporan tidak valid.</div>';
        }
        if (empty($result)) {
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

        foreach ($result as $item) {
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
