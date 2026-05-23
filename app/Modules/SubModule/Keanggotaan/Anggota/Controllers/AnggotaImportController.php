<?php

namespace Anggota\Controllers;

/**
 * AnggotaImportController
 *
 * Menangani: import data anggota dari file Excel.
 */
class AnggotaImportController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    // ----------------------------------------------------------------
    // IMPORT VIEW
    // ----------------------------------------------------------------

    public function import_view()
    {
        $this->data['title'] = 'Import Data Anggota';
        echo view('Anggota\Views\import');
    }

    // ----------------------------------------------------------------
    // IMPORT PROCESS
    // ----------------------------------------------------------------

    public function import()
    {
        header('Content-Type: application/json');

        $db = db_connect();

        if (!$this->request->getFile('excel_file')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada file yang dipilih untuk upload.',
            ]);
        }

        $file = $this->request->getFile('excel_file');

        if (!$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid atau sudah dipindahkan.',
            ]);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $memberModel = $this->anggotaModel;
            $branch_id   = user()->branch_id;

            $header    = array_map('strtolower', $data[0]);
            $columnMap = [
                'no anggota'                     => 'MemberNo',
                'nama'                           => 'FullName',
                'tempat lahir'                   => 'PlaceOfBirth',
                'tanggal lahir'                  => 'DateOfBirth',
                'alamat sesuai ktp'              => 'Address',
                'propinsi sesuai ktp'            => 'Province',
                'kabupaten/kota sesuai ktp'      => 'City',
                'kecamatan sesuai ktp'           => 'Kecamatan',
                'kelurahan sesuai ktp'           => 'Kelurahan',
                'rt sesuai ktp'                  => 'RT',
                'rw sesuai ktp'                  => 'RW',
                'alamat tempat tinggal sekarang' => 'AddressNow',
                'propinsi sekarang'              => 'ProvinceNow',
                'kabupaten/kota sekarang'        => 'CityNow',
                'kecamatan sekarang'             => 'KecamatanNow',
                'kelurahan sekarang'             => 'KelurahanNow',
                'rt sekarang'                    => 'RTNow',
                'rw sekarang'                    => 'RWNow',
                'no. hp'                         => 'NoHp',
                'agama'                          => 'Agama_id',
                'jenis identitas'                => 'IdentityType_id',
                'nomor identitas'                => 'IdentityNo',
                'jenis kelamin'                  => 'Sex_id',
                'photo url'                      => 'PhotoUrl',
                'pekerjaan'                      => 'Job_id',
                'ibu kandung'                    => 'MotherMaidenName',
                'alamat email'                   => 'Email',
                'jenis anggota'                  => 'JenisAnggota_id',
                'pendidikan terakhir'            => 'JenjangPendidikan_id',
                'status perkawinan'              => 'MaritalStatus_id',
                'tanggal pendaftaran'            => 'RegisterDate',
                'tanggal akhir berlaku'          => 'EndDate',
                'jenis permohonan'               => 'JenisPermohonan_id',
                'status anggota'                 => 'StatusAnggota_id',
                'nama institusi'                 => 'InstitutionName',
                'alamat institusi'               => 'InstitutionAddress',
                'no telp institusi'              => 'InstitutionPhone',
                'unit kerja'                     => 'UnitKerja_id',
                'tahun ajaran'                   => 'TahunAjaran',
                'fakultas'                       => 'Fakultas_id',
                'kelas'                          => 'Kelas_id',
                'program studi'                  => 'ProgramStudi_id',
                'phone'                          => 'Phone',
            ];

            $importedCount = 0;
            $skippedCount  = 0;
            $errorRows     = [];

            foreach ($data as $key => $row) {
                if ($key == 0) continue;
                if (empty(array_filter($row))) continue;

                try {
                    $memberData = ['Branch_id' => $branch_id];

                    foreach ($header as $index => $columnName) {
                        if (!isset($columnMap[$columnName])) continue;

                        $dbColumnName = $columnMap[$columnName];
                        $cellValue    = $row[$index] ?? '';

                        if ($dbColumnName == 'Sex_id') {
                            $row_data = $db->table('jenis_kelamin')->like('Name', $cellValue)->get()->getRow();
                            $memberData['Sex_id'] = $row_data ? $row_data->ID : null;
                        } elseif ($dbColumnName == 'Agama_id') {
                            $row_data = $db->table('agama')->like('Name', $cellValue)->get()->getRow();
                            $memberData['Agama_id'] = $row_data ? $row_data->ID : null;
                        } elseif ($dbColumnName == 'Job_id') {
                            $row_data = $db->table('master_pekerjaan')->like('Pekerjaan', $cellValue)->get()->getRow();
                            $memberData['Job_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'JenisAnggota_id') {
                            $row_data = $db->table('jenis_anggota')->like('jenisanggota', $cellValue)->get()->getRow();
                            $memberData['JenisAnggota_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'JenjangPendidikan_id') {
                            $row_data = $db->table('master_pendidikan')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['JenjangPendidikan_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'MaritalStatus_id') {
                            $row_data = $db->table('master_status_perkawinan')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['MaritalStatus_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'JenisPermohonan_id') {
                            $row_data = $db->table('jenis_permohonan')->like('Name', $cellValue)->get()->getRow();
                            $memberData['JenisPermohonan_id'] = $row_data ? $row_data->ID : null;
                        } elseif ($dbColumnName == 'StatusAnggota_id') {
                            $row_data = $db->table('status_anggota')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['StatusAnggota_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'IdentityType_id') {
                            $row_data = $db->table('master_jenis_identitas')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['IdentityType_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'UnitKerja_id') {
                            $row_data = $db->table('departments')->like('Name', $cellValue)->get()->getRow();
                            $memberData['UnitKerja_id'] = $row_data ? $row_data->ID : null;
                        } elseif ($dbColumnName == 'Fakultas_id') {
                            $row_data = $db->table('master_fakultas')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['Fakultas_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'Kelas_id') {
                            $row_data = $db->table('kelas_siswa')->like('namakelassiswa', $cellValue)->get()->getRow();
                            $memberData['Kelas_id'] = $row_data ? $row_data->id : null;
                        } elseif ($dbColumnName == 'ProgramStudi_id') {
                            $row_data = $db->table('master_program_studi')->like('Nama', $cellValue)->get()->getRow();
                            $memberData['ProgramStudi_id'] = $row_data ? $row_data->id : null;
                        } elseif (in_array($dbColumnName, ['DateOfBirth', 'RegisterDate', 'EndDate'])) {
                            $memberData[$dbColumnName] = $this->parseExcelDate($cellValue);
                        } else {
                            $memberData[$dbColumnName] = $cellValue;
                        }
                    }

                    $existingMember = $memberModel->where('MemberNo', $memberData['MemberNo'])->first();
                    if ($existingMember) {
                        $skippedCount++;
                        continue;
                    }

                    if (empty($memberData['RegisterDate'])) {
                        $memberData['RegisterDate'] = date('Y-m-d');
                    }

                    $memberModel->insert($memberData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errorRows[] = ['row' => $key + 1, 'error' => $e->getMessage()];
                }
            }

            $message = "Import berhasil! {$importedCount} data berhasil diimport";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} data dilewati (duplikat)";
            }
            if (count($errorRows) > 0) {
                $message .= ", " . count($errorRows) . " data gagal diimport";
            }

            return $this->response->setJSON([
                'success'  => true,
                'message'  => $message,
                'imported' => $importedCount,
                'skipped'  => $skippedCount,
                'errors'   => $errorRows,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    private function parseExcelDate($cellValue)
    {
        if (empty($cellValue)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $cellValue)) {
            return $cellValue;
        }

        if (is_numeric($cellValue) && $cellValue > 0) {
            try {
                $dateTimeObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                return $dateTimeObject->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $cellValue, $matches)) {
                $day   = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year  = $matches[3];
                return "{$year}-{$month}-{$day}";
            }

            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cellValue, $matches)) {
                $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $day   = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year  = $matches[3];
                return "{$year}-{$month}-{$day}";
            }

            $timestamp = strtotime($cellValue);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
