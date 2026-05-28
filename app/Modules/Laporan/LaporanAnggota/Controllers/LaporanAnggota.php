<?php

namespace LaporanAnggota\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanAnggota extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $anggotaModel;
    public $userModel; // Tambahkan userModel

    function __construct()
    {
        $this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->userModel = new \User\Models\UserModel(); // Inisialisasi userModel
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
            'CreateBy' => 'Dibuat Oleh',
            'UpdateBy' => 'Diperbarui Oleh',
            'jenis_kelamin.Name' => 'Jenis Kelamin',
            'jenis_anggota.jenisanggota' => 'Jenis Anggota'
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

        // Get user options for CreateBy/UpdateBy filter
        $userOptions = $this->userModel->select('id, username')
            ->where('active', 1)
            ->whereNotIn('category', ['anggota'])
            ->orderBy('username', 'ASC')
            ->findAll();

        $data = [
            'columns' => $columns,
            'genderOptions' => $genderOptions,
            'memberTypeOptions' => $memberTypeOptions,
            'userOptions' => $userOptions
        ];

        return view('LaporanAnggota\Views\index', $data);
    }

    public function preview()
    {
        $columns = json_decode($this->request->getPost('columns'), true);
        
        if (empty($columns)) {
            return '<div class="alert alert-warning">Pilih minimal satu kolom untuk preview data</div>';
        }

        // Build query dengan JOIN ke users
        $query = $this->anggotaModel
            ->select($this->buildSelectColumns($columns))
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('users as creator', 'members.CreateBy = creator.id', 'left')
            ->join('users as updater', 'members.UpdateBy = updater.id', 'left');

        // Apply multiple filters
        $this->applyFilters($query);

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
            $html .= '<th>' . esc($this->getColumnLabel($column)) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($members as $member) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = $this->getFormattedValue($member, $column);
                $html .= '<td>' . esc($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    public function export()
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 minutes

        // Simplified validation - only require columns
        if (!$this->validate([
            'columns' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');

        // Check estimated record count first
        $countQuery = $this->anggotaModel
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('users as creator', 'members.CreateBy = creator.id', 'left')
            ->join('users as updater', 'members.UpdateBy = updater.id', 'left');

        $this->applyFilters($countQuery);
        $totalRecords = $countQuery->countAllResults();

        // Limit maksimum export untuk mencegah memory issue
        $maxRecords = 50000; // Adjust sesuai kebutuhan server
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with(
                'error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export adalah {$maxRecords} records. " .
                    "Silakan gunakan filter yang lebih spesifik untuk mengurangi jumlah data."
            );
        }

        // Build main query
        $query = $this->anggotaModel
            ->select($this->buildSelectColumns($selectedColumns))
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('users as creator', 'members.CreateBy = creator.id', 'left')
            ->join('users as updater', 'members.UpdateBy = updater.id', 'left');

        // Apply multiple filters
        $this->applyFilters($query);

        // Create Excel with optimized settings
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Anggota');

        // Add header row
        $col = 'A';
        foreach ($selectedColumns as $column) {
            $headerName = $this->getColumnLabel($column);
            $sheet->setCellValue($col . '1', $headerName);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Process data in chunks to manage memory
        $chunkSize = 1000; // Process 1000 records at a time
        $offset = 0;
        $row = 2;

        do {
            // Clear previous query and get chunk
            $chunkQuery = clone $query;
            $members = $chunkQuery->limit($chunkSize, $offset)->find();

            if (empty($members)) {
                break;
            }

            // Add data rows
            foreach ($members as $member) {
                $col = 'A';
                foreach ($selectedColumns as $column) {
                    $value = $this->getFormattedValue($member, $column);
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            $offset += $chunkSize;

            // Force garbage collection
            if ($offset % ($chunkSize * 5) === 0) {
                gc_collect_cycles();
            }
        } while (count($members) === $chunkSize);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->setUseDiskCaching(true);
        $fileName = 'Laporan_Anggota_' . date('d-m-Y_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');

        // Cleanup
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        exit();
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        if (!$this->validate(['columns' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedColumns = $this->request->getPost('columns');

        $countQuery = $this->anggotaModel
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('users as creator', 'members.CreateBy = creator.id', 'left')
            ->join('users as updater', 'members.UpdateBy = updater.id', 'left');
        $this->applyFilters($countQuery);
        $totalRecords = $countQuery->countAllResults();

        $maxRecords = 5000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with('error',
                "Jumlah data terlalu besar ({$totalRecords} records). Maksimum export PDF adalah {$maxRecords} records. " .
                "Silakan gunakan filter yang lebih spesifik atau gunakan export Excel."
            );
        }

        $query = $this->anggotaModel
            ->select($this->buildSelectColumns($selectedColumns))
            ->join('jenis_kelamin', 'jenis_kelamin.ID = members.Sex_id', 'left')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('users as creator', 'members.CreateBy = creator.id', 'left')
            ->join('users as updater', 'members.UpdateBy = updater.id', 'left');
        $this->applyFilters($query);
        $members = $query->find();

        // Ambil logo kop dari settingparameters
        $db = db_connect();
        $logokop = $db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow('Value') ?? '';
        $namaPerpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow('Value') ?? 'Perpustakaan';

        $logoBase64 = '';
        if ($logokop) {
            $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        // Build HTML
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
               . '<p>Laporan Anggota &mdash; Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN DATA ANGGOTA</h3>';

        $html .= '<table><thead><tr><th>#</th>';
        foreach ($selectedColumns as $col) {
            $html .= '<th>' . esc($this->getColumnLabel($col)) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($members as $member) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($selectedColumns as $col) {
                $html .= '<td>' . esc($this->getFormattedValue($member, $col)) . '</td>';
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
        $dompdf->setPaper('A4', count($selectedColumns) > 8 ? 'landscape' : 'portrait');
        $dompdf->render();

        $fileName = 'Laporan_Anggota_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }

    // Helper function untuk apply multiple filters
    private function applyFilters($query)
    {
        // Filter berdasarkan tanggal registrasi (range)
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        if ($startDate && $endDate) {
            $query->where('members.RegisterDate >=', $startDate)
                ->where('members.RegisterDate <=', $endDate);
        }

        // Filter berdasarkan bulan dan tahun registrasi
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        if ($month && $year) {
            $query->where('MONTH(members.RegisterDate)', $month)
                ->where('YEAR(members.RegisterDate)', $year);
        }

        // Filter berdasarkan tahun registrasi saja
        $yearOnly = $this->request->getPost('year_only');
        if ($yearOnly && !$month) {
            $query->where('YEAR(members.RegisterDate)', $yearOnly);
        }

        // Filter berdasarkan tanggal lahir (range)
        $birthStartDate = $this->request->getPost('birth_start_date');
        $birthEndDate = $this->request->getPost('birth_end_date');
        if ($birthStartDate && $birthEndDate) {
            $query->where('members.DateOfBirth >=', $birthStartDate)
                ->where('members.DateOfBirth <=', $birthEndDate);
        }

        // Filter berdasarkan jenis kelamin
        $genderId = $this->request->getPost('gender_id');
        if ($genderId) {
            $query->where('members.Sex_id', $genderId);
        }

        // Filter berdasarkan jenis anggota
        $memberTypeId = $this->request->getPost('member_type_id');
        if ($memberTypeId) {
            $query->where('members.JenisAnggota_id', $memberTypeId);
        }

        // Filter berdasarkan nama lengkap
        $fullname = $this->request->getPost('fullname');
        if ($fullname) {
            $query->like('members.Fullname', $fullname);
        }

        // Filter berdasarkan tempat lahir
        $placeOfBirth = $this->request->getPost('place_of_birth');
        if ($placeOfBirth) {
            $query->like('members.PlaceOfBirth', $placeOfBirth);
        }

        // Filter berdasarkan alamat
        $address = $this->request->getPost('address');
        if ($address) {
            $query->like('members.Address', $address);
        }

        // Filter berdasarkan provinsi
        $province = $this->request->getPost('province');
        if ($province) {
            $query->like('members.Province', $province);
        }

        // Filter berdasarkan kota
        $city = $this->request->getPost('city');
        if ($city) {
            $query->like('members.City', $city);
        }

        // Filter berdasarkan institusi
        $institutionName = $this->request->getPost('institution_name');
        if ($institutionName) {
            $query->like('members.InstitutionName', $institutionName);
        }

        // Filter berdasarkan email
        $email = $this->request->getPost('email');
        if ($email) {
            $query->like('members.Email', $email);
        }

        // Filter berdasarkan dibuat oleh
        $createBy = $this->request->getPost('createby');
        if ($createBy) {
            $query->where('members.CreateBy', $createBy);
        }

        // Filter berdasarkan diperbarui oleh
        $updateBy = $this->request->getPost('updateby');
        if ($updateBy) {
            $query->where('members.UpdateBy', $updateBy);
        }
    }

    // Helper function untuk build select columns
    private function buildSelectColumns($selectedColumns)
    {
        $selectFields = [];
        
        foreach ($selectedColumns as $column) {
            if ($column == 'CreateBy') {
                $selectFields[] = 'creator.username as CreateBy';
            } elseif ($column == 'UpdateBy') {
                $selectFields[] = 'updater.username as UpdateBy';
            } else {
                $selectFields[] = $column;
            }
        }
        
        return implode(', ', $selectFields);
    }

    // Helper function untuk get column label
    private function getColumnLabel($column)
    {
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
            'CreateBy' => 'Dibuat Oleh',
            'UpdateBy' => 'Diperbarui Oleh',
            'jenis_kelamin.Name' => 'Jenis Kelamin',
            'jenis_anggota.jenisanggota' => 'Jenis Anggota'
        ];

        return isset($columnHeaders[$column]) ? $columnHeaders[$column] : $column;
    }

    // Helper function untuk format nilai
    private function getFormattedValue($member, $column)
    {
        $value = '';

        // Handle special columns
        if ($column == 'jenis_kelamin.Name') {
            $value = isset($member->Name) ? $member->Name : '';
        } elseif ($column == 'jenis_anggota.jenisanggota') {
            $value = isset($member->jenisanggota) ? $member->jenisanggota : '';
        } elseif ($column == 'CreateBy') {
            $value = isset($member->CreateBy) ? $member->CreateBy : '';
        } elseif ($column == 'UpdateBy') {
            $value = isset($member->UpdateBy) ? $member->UpdateBy : '';
        } else {
            // Extract column name without table prefix for object property access
            $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
            $value = isset($member->$columnName) ? $member->$columnName : '';
        }

        // Format date columns
        $columnName = strpos($column, '.') !== false ? substr($column, strrpos($column, '.') + 1) : $column;
        if (in_array($columnName, ['DateOfBirth', 'RegisterDate', 'EndDate']) && $value) {
            $value = date('d-m-Y', strtotime($value));
        }

        return $value;
    }
}