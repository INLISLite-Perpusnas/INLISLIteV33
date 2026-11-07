<?php

namespace Anggota\Controllers;

use Anggota\Models\AnggotaModel;
use CodeIgniter\Database\BaseBuilder;
use DateTimeImmutable;
use User\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AnggotaExportController extends \Base\Controllers\BaseController
{
    private const COLUMNS = [
        'MemberNo', 'Fullname', 'PlaceOfBirth', 'DateOfBirth', 'Address',
        'Kecamatan', 'Kelurahan', 'RT', 'RW', 'Province', 'City',
        'AddressNow', 'KecamatanNow', 'KelurahanNow', 'RTNow', 'RWNow', 'ProvinceNow', 'CityNow',
        'NoHp', 'Phone', 'IdentityType_id_label', 'IdentityNo', 'Sex_id_label',
        'Agama_id_label', 'Job_id_label', 'MotherMaidenName', 'Email', 'JenisAnggota_id_label',
        'JenjangPendidikan_id_label', 'MaritalStatus_id_label', 'RegisterDate', 'EndDate',
        'JenisPermohonan_id_label', 'StatusAnggota_id_label', 'InstitutionName',
        'InstitutionAddress', 'InstitutionPhone', 'NamaDarurat', 'AlamatDarurat', 'TelpDarurat', 'StatusHubunganDarurat',
        'UnitKerja_id_label', 'TahunAjaran', 'Fakultas_id_label', 'Jurusan_id_label',
        'ProgramStudi_id_label', 'Kelas_id_label', 'PhotoUrl',
    ];

    public function index()
    {
        if (!is_allowed('anggota/export_view/index', login_id(), false)) {
            return $this->response->setStatusCode(403)->setBody('Akses export anggota ditolak.');
        }

        $memberModel = new AnggotaModel();
        $creatorRows = $memberModel->builder()->select('CreateBy')->distinct()
            ->where('IsKeranjang', 0)
            ->where('CreateBy IS NOT NULL', null, false)
            ->get()->getResultArray();
        $creatorIds = array_values(array_unique(array_map('intval', array_column($creatorRows, 'CreateBy'))));

        $usernames = [];
        if ($creatorIds) {
            $users = (new UserModel())->select('id, username')->whereIn('id', $creatorIds)->findAll();
            foreach ($users as $user) {
                $usernames[$user->id] = $user->username;
            }
        }

        $creators = [];
        foreach ($creatorIds as $id) {
            $creators[$id] = $usernames[$id] ?? 'Pengguna #' . $id;
        }
        natcasesort($creators);

        return view('Anggota\Views\export', [
            'title' => 'Export Data Anggota',
            'creators' => $creators,
        ]);
    }

    private function applyFilters(BaseBuilder $query, string $creatorId, string $startDate, string $endDate): void
    {
        $query->where('members.IsKeranjang', 0);
        if ($creatorId !== '') {
            $query->where('members.CreateBy', (int) $creatorId);
        }
        if ($startDate !== '') {
            $query->where('members.CreateDate >=', $startDate . ' 00:00:00');
            $query->where('members.CreateDate <', (new DateTimeImmutable($endDate))->modify('+1 day')->format('Y-m-d 00:00:00'));
        }
    }

    private function validDate(string $value): bool
    {
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $value)) {
            return false;
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    public function export()
    {
        $template = FCPATH . 'uploads/master-template/template_anggota.xlsx';
        if (!is_file($template)) {
            return $this->response->setStatusCode(404)->setBody('Template anggota tidak ditemukan.');
        }

        if (!is_allowed('anggota/export', login_id(), false)) {
            return $this->response->setStatusCode(403)->setBody('Akses export anggota ditolak.');
        }

        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';
        $creatorId = $this->request->getGet('create_by') ?? '';
        if (!is_string($startDate) || !is_string($endDate) || !is_string($creatorId)) {
            return $this->response->setStatusCode(422)->setBody('Filter export tidak valid.');
        }
        if (($startDate === '') !== ($endDate === '')) {
            return $this->response->setStatusCode(422)->setBody('Isi tanggal mulai dan tanggal akhir.');
        }
        if ($startDate !== '' && (!$this->validDate($startDate) || !$this->validDate($endDate) || $startDate > $endDate)) {
            return $this->response->setStatusCode(422)->setBody('Periode export tidak valid.');
        }
        if ($creatorId !== '' && (!ctype_digit($creatorId) || filter_var($creatorId, FILTER_VALIDATE_INT) === false)) {
            return $this->response->setStatusCode(422)->setBody('Pembuat data tidak valid.');
        }

        $lookups = [
            'IdentityType_id' => ['master_jenis_identitas', 'Nama'],
            'Sex_id' => ['jenis_kelamin', 'Name'],
            'Agama_id' => ['agama', 'Name'],
            'Job_id' => ['master_pekerjaan', 'Pekerjaan'],
            'JenisAnggota_id' => ['jenis_anggota', 'jenisanggota'],
            'JenjangPendidikan_id' => ['master_pendidikan', 'Nama'],
            'MaritalStatus_id' => ['master_status_perkawinan', 'Nama'],
            'JenisPermohonan_id' => ['jenis_permohonan', 'Name'],
            'StatusAnggota_id' => ['status_anggota', 'Nama'],
            'UnitKerja_id' => ['departments', 'Name'],
            'Fakultas_id' => ['master_fakultas', 'Nama'],
            'Jurusan_id' => ['master_jurusan', 'Nama'],
            'ProgramStudi_id' => ['master_program_studi', 'Nama'],
            'Kelas_id' => ['kelas_siswa', 'namakelassiswa'],
        ];

        $model = new AnggotaModel();
        $query = $model->builder()->select('members.*');
        foreach ($lookups as $field => [$table, $label]) {
            $query->select("{$table}.{$label} AS {$field}_label")
                ->join($table, "{$table}.id = members.{$field}", 'left');
        }
        $this->applyFilters($query, $creatorId, $startDate, $endDate);
        $query->orderBy('members.ID', 'ASC');

        $members = (clone $query)->limit(500)->get()->getResultArray();
        if ($members === []) {
            return redirect()->to(base_url('anggota/export_view'))
                ->with('error', 'Tidak ada data anggota di database untuk filter yang dipilih.');
        }

        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getHighestRow() > 2) {
            $sheet->removeRow(3, $sheet->getHighestRow() - 2);
        }

        $columns = self::COLUMNS;

        $rowNumber = 2;
        $offset = 0;
        do {
            foreach ($members as $member) {
                $values = array_merge([$rowNumber - 1], array_map(
                    static function ($field) use ($member) {
                        $value = $field === null ? '' : ($member[$field] ?? '');
                        if ($field === 'NoHp' && ($value === '' || $value === null)) {
                            $value = $member['Phone'] ?? '';
                        }
                        if ($value !== '' && in_array($field, ['DateOfBirth', 'RegisterDate', 'EndDate'], true)) {
                            $time = strtotime((string) $value);
                            return $time === false ? $value : date('d-m-Y', $time);
                        }
                        return $value;
                    },
                    $columns
                ));
                foreach ($values as $columnIndex => $value) {
                    $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($columnIndex + 1) . $rowNumber, (string) $value, DataType::TYPE_STRING);
                }
                $rowNumber++;
            }
            $offset += count($members);
            $members = count($members) === 500
                ? (clone $query)->limit(500, $offset)->get()->getResultArray()
                : [];
        } while ($members !== []);

        $filename = 'Export_Anggota_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store');
        (new Xlsx($spreadsheet))->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }
}