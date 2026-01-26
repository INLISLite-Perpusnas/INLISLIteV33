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
}