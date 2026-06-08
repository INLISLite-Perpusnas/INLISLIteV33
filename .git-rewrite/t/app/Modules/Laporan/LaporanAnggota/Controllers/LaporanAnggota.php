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
        // Get all available columns with proper table prefixes
        $columns = [
            'members.MemberNo' => 'Nomor Anggota',
            'members.Fullname' => 'Nama Lengkap',
            'members.PlaceOfBirth' => 'Tempat Lahir',
            'members.DateOfBirth' => 'Tanggal Lahir',
            'members.Address' => 'Alamat',
            'members.Phone' => 'Telepon',
            'members.Email' => 'Email',
            'members.Province' => 'Provinsi',
            'members.City' => 'Kota',
            'members.Kecamatan' => 'Kecamatan',
            'members.Kelurahan' => 'Kelurahan',
            'members.RT' => 'RT',
            'members.RW' => 'RW',
            'members.InstitutionName' => 'Nama Institusi',
            'members.IdentityNo' => 'Nomor Identitas',
            'members.RegisterDate' => 'Tanggal Registrasi',
            'members.EndDate' => 'Tanggal Berakhir',
            'members.CreateBy' => 'Dibuat Oleh',
            'jenis_kelamin.Name' => 'Jenis Kelamin',
            'jenis_anggota.jenisanggota' => 'Jenis Anggota'
            // Add more columns as needed
        ];

        // Get gender options for filter
        $genderOptions = $this->anggotaModel
            ->select('jenis_kelamin.id, jenis_kelamin.Name')
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->groupBy('jenis_kelamin.ID')
            ->orderBy('jenis_kelamin.Name')
            ->findAll();

        // Get member type options for filter
        $memberTypeOptions = $this->anggotaModel
            ->select('jenis_anggota.id, jenis_anggota.jenisanggota')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->groupBy('jenis_anggota.id')
            ->orderBy('jenis_anggota.jenisanggota')
            ->findAll();

        $data = [
            'columns' => $columns,
            'genderOptions' => $genderOptions,
            'memberTypeOptions' => $memberTypeOptions
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
        $genderId = $this->request->getPost('gender_id'); // Gender filter
        $memberTypeId = $this->request->getPost('member_type_id'); // New member type filter

        // Build query based on filter with proper table prefixes
        $query = $this->anggotaModel
            ->select(implode(', ', $selectedColumns))
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left');

        // Apply gender filter if selected
        if ($genderId && $genderId != '') {
            $query->where('members.Sex_id', $genderId);
        }

        // Apply member type filter if selected
        if ($memberTypeId && $memberTypeId != '') {
            $query->where('members.JenisAnggota_id', $memberTypeId);
        }

        switch ($filterType) {
            case 'date':
                if ($startDate && $endDate) {
                    $query->where('members.RegisterDate >=', $startDate)
                          ->where('members.RegisterDate <=', $endDate);
                }
                break;
            case 'month':
                if ($month && $year) {
                    $query->where('MONTH(members.RegisterDate)', $month)
                          ->where('YEAR(members.RegisterDate)', $year);
                }
                break;
            case 'year':
                if ($year) {
                    $query->where('YEAR(members.RegisterDate)', $year);
                }
                break;
        }

        $members = $query->findAll();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Get column headers for display with proper table prefixes
        $columnHeaders = [
            'members.MemberNo' => 'Nomor Anggota',
            'members.Fullname' => 'Nama Lengkap',
            'members.PlaceOfBirth' => 'Tempat Lahir',
            'members.DateOfBirth' => 'Tanggal Lahir',
            'members.Address' => 'Alamat',
            'members.Phone' => 'Telepon',
            'members.Email' => 'Email',
            'members.Province' => 'Provinsi',
            'members.City' => 'Kota',
            'members.Kecamatan' => 'Kecamatan',
            'members.Kelurahan' => 'Kelurahan',
            'members.RT' => 'RT',
            'members.RW' => 'RW',
            'members.InstitutionName' => 'Nama Institusi',
            'members.IdentityNo' => 'Nomor Identitas',
            'members.RegisterDate' => 'Tanggal Registrasi',
            'members.EndDate' => 'Tanggal Berakhir',
            'members.CreateBy' => 'Dibuat Oleh',
            'jenis_kelamin.Name' => 'Jenis Kelamin',
            'jenis_anggota.jenisanggota' => 'Jenis Anggota'
        ];

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $headerName = isset($columnHeaders[$column]) ? $columnHeaders[$column] : $column;
            $sheet->setCellValue($col . '1', $headerName);
            $col++;
        }

        // Add data rows
        $row = 2;
        foreach ($members as $member) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = '';
                
                // Handle joined columns and table-prefixed columns
                if ($column == 'jenis_kelamin.Name') {
                    $value = isset($member->Name) ? $member->Name : '';
                } elseif ($column == 'jenis_anggota.jenisanggota') {
                    $value = isset($member->jenisanggota) ? $member->jenisanggota : '';
                } else {
                    // Extract column name without table prefix for object property access
                    $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
                    $value = isset($member->$columnName) ? $member->$columnName : '';
                }
                
                // Format date columns
                $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
                if (in_array($columnName, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
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
        
        exit(); // Use exit() instead of redirect() for file downloads
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        $filterType = $this->request->getPost('filter_type');
        $genderId = $this->request->getPost('gender_id'); // Gender filter
        $memberTypeId = $this->request->getPost('member_type_id'); // Member type filter
        $branch_id = branch_id();

        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query based on filter with proper table prefixes
        $query = $this->anggotaModel
            ->select(implode(', ', $columns))
            ->join('jenis_kelamin', 'jenis_kelamin.id = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left');

        // Apply gender filter if selected
        if ($genderId && $genderId != '') {
            $query->where('members.Sex_id', $genderId);
        }

        // Apply member type filter if selected
        if ($memberTypeId && $memberTypeId != '') {
            $query->where('members.JenisAnggota_id', $memberTypeId);
        }

        switch ($filterType) {
            case 'date':
                $startDate = $this->request->getPost('start_date');
                $endDate = $this->request->getPost('end_date');
                if ($startDate && $endDate) {
                    $query->where('members.RegisterDate >=', $startDate)
                          ->where('members.RegisterDate <=', $endDate);
                }
                break;
            case 'month':
                $month = $this->request->getPost('month');
                $year = $this->request->getPost('year');
                if ($month && $year) {
                    $query->where('MONTH(members.RegisterDate)', $month)
                          ->where('YEAR(members.RegisterDate)', $year);
                }
                break;
            case 'year':
                $year = $this->request->getPost('year');
                if ($year) {
                    $query->where('YEAR(members.RegisterDate)', $year);
                }
                break;
        }

        // Get first 20 rows
        $members = $query->limit(20)->find();

        if (empty($members)) {
            return '<div class="alert alert-info">Tidak ada data yang ditemukan dengan filter yang dipilih</div>';
        }

        // Get column headers for display with proper table prefixes
        $columnHeaders = [
            'members.MemberNo' => 'Nomor Anggota',
            'members.Fullname' => 'Nama Lengkap',
            'members.PlaceOfBirth' => 'Tempat Lahir',
            'members.DateOfBirth' => 'Tanggal Lahir',
            'members.Address' => 'Alamat',
            'members.Phone' => 'Telepon',
            'members.Email' => 'Email',
            'members.Province' => 'Provinsi',
            'members.City' => 'Kota',
            'members.Kecamatan' => 'Kecamatan',
            'members.Kelurahan' => 'Kelurahan',
            'members.RT' => 'RT',
            'members.RW' => 'RW',
            'members.InstitutionName' => 'Nama Institusi',
            'members.IdentityNo' => 'Nomor Identitas',
            'members.RegisterDate' => 'Tanggal Registrasi',
            'members.EndDate' => 'Tanggal Berakhir',
            'members.CreateBy' => 'Dibuat Oleh',
            'jenis_kelamin.Name' => 'Jenis Kelamin',
            'jenis_anggota.jenisanggota' => 'Jenis Anggota'
        ];

        // Build preview table
        $html = '<div class="table-responsive">
        <table class="table table-bordered table-striped">
        <thead>
        <tr>';

        foreach ($columns as $column) {
            $headerName = isset($columnHeaders[$column]) ? $columnHeaders[$column] : $column;
            $html .= '<th>' . esc($headerName) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($members as $member) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = '';
                
                // Handle joined columns and table-prefixed columns
                if ($column == 'jenis_kelamin.Name') {
                    $value = isset($member->Name) ? $member->Name : '';
                } elseif ($column == 'jenis_anggota.jenisanggota') {
                    $value = isset($member->jenisanggota) ? $member->jenisanggota : '';
                } else {
                    // Extract column name without table prefix for object property access
                    $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
                    $value = isset($member->$columnName) ? $member->$columnName : '';
                }
                
                // Format date columns
                $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
                if (in_array($columnName, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
                    $value = date('Y-m-d', strtotime($value));
                }
                
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    public function member()
    {
        $this->data['title'] = 'Laporan - Anggota';
        echo view('Report\Views\member_list', $this->data);
    }
}