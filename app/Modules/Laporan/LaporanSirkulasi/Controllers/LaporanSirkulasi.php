<?php

namespace LaporanSirkulasi\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanSirkulasi extends \Base\Controllers\BaseController
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Laporan Peminjaman Buku'
        ];
        
        return view('LaporanSirkulasi\Views\index', $data);
    }
    
    public function preview()
    {
        // Ambil kolom yang dipilih
        $columns = $this->request->getPost('columns') ?? [];
        
        if (empty($columns)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pilih minimal satu kolom untuk ditampilkan'
            ]);
        }
        
        // Ambil filter
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $loanStatus = $this->request->getPost('loan_status');
        $memberName = $this->request->getPost('member_name');
        $bookTitle = $this->request->getPost('book_title');
        
        // Query data
        $builder = $this->db->table('collectionloanitems cli');
        $builder->select('
            cli.ID,
            m.Fullname as nama_anggota,
            m.MemberNo,
            col.NomorBarcode,
            cat.Title as judul_buku,
            cli.LoanDate as tanggal_peminjaman,
            cli.DueDate as tanggal_jatuh_tempo,
            cli.ActualReturn as tanggal_pengembalian,
            u_loan.first_name as petugas_peminjaman,
            u_return.first_name as petugas_pengembalian,
            CASE 
                WHEN m.Sex_id = 1 THEN \'Laki-laki\'
                WHEN m.Sex_id = 2 THEN \'Perempuan\'
                ELSE \'Tidak Diketahui\'
            END as jenis_kelamin,
            cli.LoanStatus as status_peminjaman
        ', false);
        $builder->join('members m', 'm.ID = cli.member_id', 'left');
        $builder->join('collections col', 'col.ID = cli.Collection_id', 'left');
        $builder->join('catalogs cat', 'cat.ID = col.Catalog_id', 'left');
        $builder->join('users u_loan', 'u_loan.id = cli.CreateBy', 'left');
        $builder->join('users u_return', 'u_return.id = cli.UpdateBy', 'left');
        
        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('DATE(cli.LoanDate) >=', $startDate);
            $builder->where('DATE(cli.LoanDate) <=', $endDate);
        }
        
        if ($loanStatus) {
            $builder->where('cli.LoanStatus', $loanStatus);
        }
        
        if ($memberName) {
            $builder->like('m.Fullname', $memberName);
        }
        
        if ($bookTitle) {
            $builder->like('cat.Title', $bookTitle);
        }
        
        $builder->orderBy('cli.LoanDate', 'DESC');
        $builder->limit(100);
        
        $data = $builder->get()->getResultArray();
        
        // Filter kolom sesuai yang dipilih
        $filteredData = [];
        foreach ($data as $row) {
            $filteredRow = [];
            foreach ($columns as $col) {
                if (isset($row[$col])) {
                    $filteredRow[$col] = $row[$col];
                }
            }
            $filteredData[] = $filteredRow;
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $filteredData,
            'columns' => $columns,
            'total' => count($filteredData)
        ]);
    }
    
    public function export()
    {
        // Ambil kolom yang dipilih
        $columns = $this->request->getPost('columns') ?? [];
        
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diexport');
        }
        
        // Ambil filter
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $loanStatus = $this->request->getPost('loan_status');
        $memberName = $this->request->getPost('member_name');
        $bookTitle = $this->request->getPost('book_title');
        
        // Query semua data (tanpa limit untuk export)
        $builder = $this->db->table('collectionloanitems cli');
        $builder->select('
            cli.ID,
            m.Fullname as nama_anggota,
            m.MemberNo,
            col.NomorBarcode,
            cat.Title as judul_buku,
            cli.LoanDate as tanggal_peminjaman,
            cli.DueDate as tanggal_jatuh_tempo,
            cli.ActualReturn as tanggal_pengembalian,
            u_loan.first_name as petugas_peminjaman,
            u_return.first_name as petugas_pengembalian,
            CASE 
                WHEN m.Sex_id = 1 THEN \'Laki-laki\'
                WHEN m.Sex_id = 2 THEN \'Perempuan\'
                ELSE \'Tidak Diketahui\'
            END as jenis_kelamin,
            cli.LoanStatus as status_peminjaman
        ', false);
        $builder->join('members m', 'm.ID = cli.member_id', 'left');
        $builder->join('collections col', 'col.ID = cli.Collection_id', 'left');
        $builder->join('catalogs cat', 'cat.ID = col.Catalog_id', 'left');
        $builder->join('users u_loan', 'u_loan.id = cli.CreateBy', 'left');
        $builder->join('users u_return', 'u_return.id = cli.UpdateBy', 'left');
        
        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('DATE(cli.LoanDate) >=', $startDate);
            $builder->where('DATE(cli.LoanDate) <=', $endDate);
        }
        
        if ($loanStatus) {
            $builder->where('cli.LoanStatus', $loanStatus);
        }
        
        if ($memberName) {
            $builder->like('m.Fullname', $memberName);
        }
        
        if ($bookTitle) {
            $builder->like('cat.Title', $bookTitle);
        }
        
        $builder->orderBy('cli.LoanDate', 'DESC');
        $data = $builder->get()->getResultArray();

        // Mapping nama kolom
        $columnNames = [
            'nama_anggota' => 'Nama Anggota',
            'MemberNo' => 'Nomor Anggota',
            'NomorBarcode' => 'Nomor Barcode',
            'judul_buku' => 'Judul Buku',
            'tanggal_peminjaman' => 'Tanggal Peminjaman',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'tanggal_pengembalian' => 'Tanggal Pengembalian',
            'petugas_peminjaman' => 'Petugas Peminjaman',
            'petugas_pengembalian' => 'Petugas Pengembalian',
            'jenis_kelamin' => 'Jenis Kelamin',
            'status_peminjaman' => 'Status Peminjaman'
        ];
        
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $col = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($col . '1', $columnNames[$column] ?? $column);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Isi data
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($columns as $column) {
                $value = $item[$column] ?? '-';
                
                // Format tanggal jika kolom tanggal
                if (in_array($column, ['tanggal_peminjaman', 'tanggal_jatuh_tempo', 'tanggal_pengembalian'])) {
                    $value = $value ? date('d-m-Y H:i', strtotime($value)) : '-';
                }
                
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Set header untuk download
        $filename = 'Laporan_Peminjaman_' . date('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $columns = $this->request->getPost('columns') ?? [];
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diexport');
        }

        $startDate  = $this->request->getPost('start_date');
        $endDate    = $this->request->getPost('end_date');
        $loanStatus = $this->request->getPost('loan_status');
        $memberName = $this->request->getPost('member_name');
        $bookTitle  = $this->request->getPost('book_title');

        $builder = $this->db->table('collectionloanitems cli');
        $builder->select('
            cli.ID,
            m.Fullname as nama_anggota,
            m.MemberNo,
            col.NomorBarcode,
            cat.Title as judul_buku,
            cli.LoanDate as tanggal_peminjaman,
            cli.DueDate as tanggal_jatuh_tempo,
            cli.ActualReturn as tanggal_pengembalian,
            u_loan.first_name as petugas_peminjaman,
            u_return.first_name as petugas_pengembalian,
            CASE
                WHEN m.Sex_id = 1 THEN \'Laki-laki\'
                WHEN m.Sex_id = 2 THEN \'Perempuan\'
                ELSE \'Tidak Diketahui\'
            END as jenis_kelamin,
            cli.LoanStatus as status_peminjaman
        ', false);
        $builder->join('members m', 'm.ID = cli.member_id', 'left');
        $builder->join('collections col', 'col.ID = cli.Collection_id', 'left');
        $builder->join('catalogs cat', 'cat.ID = col.Catalog_id', 'left');
        $builder->join('users u_loan', 'u_loan.id = cli.CreateBy', 'left');
        $builder->join('users u_return', 'u_return.id = cli.UpdateBy', 'left');

        if ($startDate && $endDate) {
            $builder->where('DATE(cli.LoanDate) >=', $startDate);
            $builder->where('DATE(cli.LoanDate) <=', $endDate);
        }
        if ($loanStatus) {
            $builder->where('cli.LoanStatus', $loanStatus);
        }
        if ($memberName) {
            $builder->like('m.Fullname', $memberName);
        }
        if ($bookTitle) {
            $builder->like('cat.Title', $bookTitle);
        }
        $builder->orderBy('cli.LoanDate', 'DESC');

        $totalRecords = (clone $builder)->countAllResults(false);
        $maxRecords = 5000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with('error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export PDF adalah {$maxRecords} records. " .
                "Silakan gunakan filter yang lebih spesifik atau gunakan export Excel."
            );
        }

        $data = $builder->get()->getResultArray();

        $columnNames = [
            'nama_anggota'        => 'Nama Anggota',
            'MemberNo'            => 'Nomor Anggota',
            'NomorBarcode'        => 'Nomor Barcode',
            'judul_buku'          => 'Judul Buku',
            'tanggal_peminjaman'  => 'Tanggal Peminjaman',
            'tanggal_jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'tanggal_pengembalian'=> 'Tanggal Pengembalian',
            'petugas_peminjaman'  => 'Petugas Peminjaman',
            'petugas_pengembalian'=> 'Petugas Pengembalian',
            'jenis_kelamin'       => 'Jenis Kelamin',
            'status_peminjaman'   => 'Status Peminjaman',
        ];

        // Ambil logo kop dari settingparameters
        $logokop = $this->db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow('Value') ?? '';
        $namaPerpustakaan = $this->db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow('Value') ?? 'Perpustakaan';

        $logoBase64 = '';
        if ($logokop) {
            $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
            if (file_exists($logoPath)) {
                $ext  = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

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

        $html .= '<div class="kop">';
        if ($logoBase64) {
            $html .= '<img src="' . $logoBase64 . '" alt="Logo">';
        }
        $html .= '<div class="kop-text"><h2>' . esc($namaPerpustakaan) . '</h2>'
               . '<p>Laporan Peminjaman &mdash; Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN DATA PEMINJAMAN</h3>';

        $html .= '<table><thead><tr><th>#</th>';
        foreach ($columns as $col) {
            $html .= '<th>' . esc($columnNames[$col] ?? $col) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($data as $item) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($columns as $col) {
                $value = $item[$col] ?? '-';
                if (in_array($col, ['tanggal_peminjaman', 'tanggal_jatuh_tempo', 'tanggal_pengembalian'])) {
                    $value = $value && $value !== '-' ? date('d-m-Y H:i', strtotime($value)) : '-';
                }
                $html .= '<td>' . esc($value) . '</td>';
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
        $dompdf->setPaper('A4', count($columns) > 6 ? 'landscape' : 'portrait');
        $dompdf->render();

        $fileName = 'Laporan_Peminjaman_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }
}