<?php

namespace BukuTamu\Controllers;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Import buku tamu anggota, non anggota, dan rombongan dari Excel.
 *
 * Semua baris diperiksa dulu. Bila ada satu saja yang salah, tidak ada data yang disimpan,
 * karena buku tamu tidak punya kunci unik: unggah ulang setelah sebagian masuk akan menggandakan data.
 *
 * Rute memakai awalan bukutamu/create agar mengikuti izin bukutamu/create.
 */
class BukuTamuImport extends \Base\Controllers\BaseController
{
    private const MAX_ROWS = 5000;
    private const MAX_ERRORS_SHOWN = 100;
    private const TEMPLATE_INPUT_ROWS = 1000;

    private const TYPES = [
        'anggota'     => ['label' => 'Anggota', 'table' => 'memberguesses', 'purposeFlag' => 'Member'],
        'non_anggota' => ['label' => 'Non Anggota', 'table' => 'memberguesses', 'purposeFlag' => 'NonMember'],
        'rombongan'   => ['label' => 'Rombongan', 'table' => 'groupguesses', 'purposeFlag' => 'Rombongan'],
    ];

    private const COUNT_GENDER = ['JUMLAH_LAKI_LAKI' => 'CountLaki', 'JUMLAH_PEREMPUAN' => 'CountPerempuan'];

    private const COUNT_PROFESSION = [
        'PROFESI_PNS' => 'CountPNS',
        'PROFESI_GURU' => 'CountGuru',
        'PROFESI_PEGAWAI_SWASTA' => 'CountPSwasta',
        'PROFESI_PENELITI' => 'CountPeneliti',
        'PROFESI_DOSEN' => 'CountDosen',
        'PROFESI_PENSIUNAN' => 'CountPensiunan',
        'PROFESI_TNI' => 'CountTNI',
        'PROFESI_WIRASWASTA' => 'CountWiraswasta',
        'PROFESI_PELAJAR' => 'CountPelajar',
        'PROFESI_MAHASISWA' => 'CountMahasiswa',
        'PROFESI_LAINNYA' => 'CountLainnya',
    ];

    private const COUNT_EDUCATION = [
        'PENDIDIKAN_TK' => 'CountTk',
        'PENDIDIKAN_SD' => 'CountSD',
        'PENDIDIKAN_SMP' => 'CountSMP',
        'PENDIDIKAN_SMA' => 'CountSMA',
        'PENDIDIKAN_D1' => 'CountD1',
        'PENDIDIKAN_D2' => 'CountD2',
        'PENDIDIKAN_D3' => 'CountD3',
        'PENDIDIKAN_S1' => 'CountS1',
        'PENDIDIKAN_S2' => 'CountS2',
        'PENDIDIKAN_S3' => 'CountS3',
    ];

    private $guestDb;
    private array $masterCache = [];

    public function __construct()
    {
        $this->guestDb = \Config\Database::connect('data');
    }

    // ----------------------------------------------------------------
    // ENDPOINT
    // ----------------------------------------------------------------

    public function template(string $type = '')
    {
        if (!isset(self::TYPES[$type]) || !$this->canImport()) {
            return redirect()->to('bukutamu');
        }

        $spreadsheet = $this->buildTemplate($type);
        $path = tempnam(sys_get_temp_dir(), 'gb');
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($path);
        $content = file_get_contents($path);
        unlink($path);

        return $this->response->download('template_buku_tamu_' . $type . '.xlsx', $content);
    }

    public function import(string $type = '')
    {
        if (!$this->canImport()) {
            return $this->importFailed(403, 'Anda tidak memiliki izin untuk mengimport buku tamu.');
        }
        if (!isset(self::TYPES[$type])) {
            return $this->importFailed(404, 'Jenis buku tamu tidak dikenal.');
        }

        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return $this->importFailed(422, 'File gagal diunggah. Pilih file Excel, lalu coba lagi.');
        }
        if (!in_array(strtolower($file->getClientExtension()), ['xlsx', 'xls'], true)) {
            return $this->importFailed(422, 'File harus berformat Excel (.xlsx atau .xls).');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->importFailed(422, 'Ukuran file maksimal 5 MB. Bagi data menjadi beberapa file.');
        }

        try {
            $reader = IOFactory::createReaderForFile($file->getTempName(), ['Xlsx', 'Xls']);
            $reader->setReadDataOnly(true);
            $rows = $reader->load($file->getTempName())->getSheet(0)->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            log_message('error', 'Import buku tamu, file tidak terbaca: ' . $e->getMessage());
            return $this->importFailed(422, 'File tidak bisa dibaca. Simpan ulang sebagai .xlsx dari template, lalu coba lagi.');
        }

        $header = array_map(static fn ($name) => strtoupper(trim((string) $name)), array_shift($rows) ?? []);
        $missing = array_diff(array_keys($this->columns($type)), $header);
        if ($missing !== []) {
            return $this->importFailed(422, 'File ini bukan template ' . self::TYPES[$type]['label'] . '. Kolom ' . implode(', ', array_slice($missing, 0, 3)) . ' tidak ditemukan. Unduh template di halaman ini, lalu isi kembali.');
        }

        try {
            [$records, $errors] = $this->buildRecords($type, $rows, $header);
        } catch (\Throwable $e) {
            log_message('error', 'Import buku tamu gagal diperiksa: ' . $e->getMessage());
            return $this->importFailed(500, 'Data tidak dapat diperiksa. Coba lagi atau hubungi administrator.');
        }

        if ($errors !== []) {
            $total = count($errors);
            return $this->importFailed(422, "Ada {$total} isian yang perlu diperbaiki. Belum ada data yang disimpan.", array_slice($errors, 0, self::MAX_ERRORS_SHOWN));
        }
        if ($records === []) {
            return $this->importFailed(422, 'File belum berisi data. Isi data mulai dari baris kedua.');
        }
        if (count($records) > self::MAX_ROWS) {
            return $this->importFailed(422, 'Maksimal ' . self::MAX_ROWS . ' baris per file. Bagi data menjadi beberapa file.');
        }

        if (!$this->saveRecords(self::TYPES[$type]['table'], $records)) {
            return $this->importFailed(500, 'Data tidak dapat disimpan. Coba lagi atau hubungi administrator.');
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'count'   => count($records),
            'message' => count($records) . ' data buku tamu ' . self::TYPES[$type]['label'] . ' berhasil ditambahkan.',
        ]);
    }

    private function canImport(): bool
    {
        return is_allowed('bukutamu/create', null, false);
    }

    private function importFailed(int $status, string $message, array $errors = [])
    {
        return $this->response->setStatusCode($status)->setJSON([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ]);
    }

    private function saveRecords(string $table, array $records): bool
    {
        $this->guestDb->transBegin();

        try {
            foreach (array_chunk($records, 500) as $chunk) {
                if (!$this->guestDb->table($table)->insertBatch($chunk)) {
                    throw new \RuntimeException($this->guestDb->error()['message']);
                }
            }
            $this->guestDb->transCommit();

            return true;
        } catch (\Throwable $e) {
            $this->guestDb->transRollback();
            log_message('error', 'Import buku tamu gagal disimpan: ' . $e->getMessage());

            return false;
        }
    }

    // ----------------------------------------------------------------
    // KOLOM TEMPLATE
    // ----------------------------------------------------------------

    /** Nama kolom => [wajib, keterangan]. */
    private function columns(string $type): array
    {
        $visit = ['TANGGAL_KUNJUNGAN' => [true, 'Format dd-mm-yyyy atau dd-mm-yyyy hh:mm, contoh 25-05-2026 09:30. Tidak boleh tanggal yang akan datang.']];
        $place = [
            'KODE_LOKASI_RUANG' => [true, 'Kode lokasi ruang, lihat daftar di bawah.'],
            'TUJUAN_KUNJUNGAN'  => [false, 'Lihat daftar di bawah.'],
        ];

        if ($type === 'anggota') {
            return $visit + ['NOMOR_ANGGOTA' => [true, 'Harus sudah terdaftar. Nama dan data lain diambil dari data anggota.']] + $place;
        }

        if ($type === 'non_anggota') {
            return $visit + [
                'NAMA'                => [true, 'Maksimal 100 karakter.'],
                'JENIS_KELAMIN'       => [true, 'Lihat daftar di bawah.'],
                'PEKERJAAN'           => [true, 'Lihat daftar di bawah.'],
                'PENDIDIKAN_TERAKHIR' => [true, 'Lihat daftar di bawah.'],
                'ALAMAT'              => [false, 'Maksimal 255 karakter.'],
            ] + $place;
        }

        $columns = $visit + [
            'NAMA_KETUA'       => [true, 'Maksimal 255 karakter.'],
            'TELEPON_KETUA'    => [false, 'Maksimal 20 karakter.'],
            'ASAL_INSTANSI'    => [true, 'Maksimal 255 karakter.'],
            'ALAMAT_INSTANSI'  => [false, 'Maksimal 255 karakter.'],
            'TELEPON_INSTANSI' => [false, 'Maksimal 20 karakter.'],
            'EMAIL_INSTANSI'   => [false, 'Contoh humas@instansi.go.id.'],
            'JUMLAH_PERSONEL'  => [true, 'Angka, minimal 1.'],
        ];
        foreach (array_keys(self::COUNT_GENDER + self::COUNT_PROFESSION + self::COUNT_EDUCATION) as $count) {
            $columns[$count] = [false, 'Angka. Kosong dihitung 0. Total per kelompok tidak boleh melebihi JUMLAH_PERSONEL.'];
        }

        return $columns + $place + ['KETERANGAN' => [false, 'Maksimal 255 karakter.']];
    }

    private function buildTemplate(string $type): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $columns = $this->columns($type);

        $data = $spreadsheet->getActiveSheet()->setTitle('Data');
        $last = Coordinate::stringFromColumnIndex(count($columns));
        $this->writeRow($data, 1, array_keys($columns));
        $this->styleHeader($data, "A1:{$last}1");
        foreach ($this->sampleRows($type) as $offset => $sample) {
            $this->writeRow($data, $offset + 2, array_map(static fn ($column) => $sample[$column] ?? '', array_keys($columns)));
        }
        // Format teks agar kode seperti 0101 tidak berubah menjadi 101
        $data->getStyle('A2:' . $last . (self::TEMPLATE_INPUT_ROWS + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $data->freezePane('A2');
        foreach (range(1, count($columns)) as $index) {
            $data->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setAutoSize(true);
        }

        $guide = $spreadsheet->createSheet()->setTitle('Petunjuk');
        $row = 1;
        $this->writeRow($guide, $row, ['Kolom', 'Wajib', 'Keterangan']);
        $this->styleHeader($guide, "A{$row}:C{$row}");
        foreach ($columns as $name => [$required, $note]) {
            $this->writeRow($guide, ++$row, [$name, $required ? 'Ya' : 'Tidak', $note]);
        }

        $row += 2;
        $this->writeRow($guide, $row, ['Baris pada lembar Data hanya contoh. Ganti atau hapus sebelum diimport, karena baris yang tersisa ikut tersimpan.']);

        foreach ($this->choiceLists($type) as $title => $choices) {
            $row += 2;
            $this->writeRow($guide, $row, [$title]);
            $this->styleHeader($guide, "A{$row}:C{$row}");
            foreach ($choices as $choice) {
                $this->writeRow($guide, ++$row, [$choice]);
            }
        }
        foreach (['A', 'B', 'C'] as $column) {
            $guide->getColumnDimension($column)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Empat baris contoh. Nilai pilihan diambil dari data master agar sesuai instalasi ini.
     * NOMOR_ANGGOTA sengaja kosong, diisi sendiri oleh petugas.
     */
    private function sampleRows(string $type): array
    {
        $rooms = array_column($this->guestDb->table('locations')->select('Code')->where('active', 1)->orderBy('Code')->get()->getResultArray(), 'Code');
        $pick = static function (array $options, int $index): string {
            $options = array_values($options);
            return $options === [] ? '' : (string) $options[$index % count($options)];
        };
        $names = static fn (array $map) => array_column($map, 'name');
        $purposes = $names($this->purposeMap($type));

        $rows = [];
        foreach (range(0, 3) as $i) {
            $rows[$i] = [
                'TANGGAL_KUNJUNGAN' => date('d-m-Y', strtotime('-' . (4 - $i) . ' days')) . ' ' . sprintf('%02d:00', 9 + $i),
                'KODE_LOKASI_RUANG' => $pick($rooms, $i),
                'TUJUAN_KUNJUNGAN'  => $pick($purposes, $i),
            ];
        }

        if ($type === 'non_anggota') {
            $people = [
                ['Rina Wulandari', 'Perempuan', 'Jl. Melati No. 12, Jakarta'],
                ['Agus Santoso', 'Laki-laki', 'Jl. Kenanga No. 5, Bogor'],
                ['Dewi Anggraini', 'Perempuan', 'Jl. Mawar No. 8, Depok'],
                ['Bima Pratama', 'Laki-laki', 'Jl. Anggrek No. 21, Bekasi'],
            ];
            $genders = $this->masterMap('jenis_kelamin', 'Name');
            $jobs = $names($this->masterMap('master_pekerjaan', 'Pekerjaan'));
            $levels = $names($this->masterMap('master_pendidikan', 'Nama'));
            foreach ($people as $i => [$name, $gender, $address]) {
                $rows[$i] += [
                    'NAMA'                => $name,
                    'JENIS_KELAMIN'       => $genders[mb_strtolower($gender)]['name'] ?? $pick($names($genders), $i),
                    'PEKERJAAN'           => $pick($jobs, $i),
                    'PENDIDIKAN_TERAKHIR' => $pick($levels, $i),
                    'ALAMAT'              => $address,
                ];
            }
        }

        if ($type === 'rombongan') {
            $groups = [
                ['Budi Hartono', '081234567801', 'SMA Negeri 1 Jakarta', 'Jl. Budi Utomo No. 7, Jakarta', '0213840001', 'humas@sman1.sch.id', ['JUMLAH_LAKI_LAKI' => 14, 'JUMLAH_PEREMPUAN' => 16, 'PROFESI_GURU' => 2, 'PROFESI_PELAJAR' => 28, 'PENDIDIKAN_SMA' => 28, 'PENDIDIKAN_S1' => 2]],
                ['Siti Rahmawati', '081234567802', 'Universitas Nusantara', 'Jl. Pendidikan No. 10, Bandung', '0222500002', 'info@unusantara.ac.id', ['JUMLAH_LAKI_LAKI' => 9, 'JUMLAH_PEREMPUAN' => 11, 'PROFESI_DOSEN' => 1, 'PROFESI_MAHASISWA' => 19, 'PENDIDIKAN_SMA' => 19, 'PENDIDIKAN_S2' => 1]],
                ['Andi Wijaya', '081234567803', 'Dinas Pendidikan Kota Bogor', 'Jl. Pajajaran No. 3, Bogor', '0251830003', 'disdik@bogorkota.go.id', ['JUMLAH_LAKI_LAKI' => 6, 'JUMLAH_PEREMPUAN' => 6, 'PROFESI_PNS' => 12, 'PENDIDIKAN_S1' => 10, 'PENDIDIKAN_S2' => 2]],
                ['Maya Lestari', '081234567804', 'SD Harapan Bangsa', 'Jl. Merdeka No. 15, Depok', '0217720004', 'admin@sdharapan.sch.id', ['JUMLAH_LAKI_LAKI' => 12, 'JUMLAH_PEREMPUAN' => 13, 'PROFESI_GURU' => 3, 'PROFESI_PELAJAR' => 22, 'PENDIDIKAN_SD' => 22, 'PENDIDIKAN_S1' => 3]],
            ];
            foreach ($groups as $i => [$chief, $phone, $agency, $address, $office, $email, $counts]) {
                $rows[$i] += $counts + [
                    'NAMA_KETUA'       => $chief,
                    'TELEPON_KETUA'    => $phone,
                    'ASAL_INSTANSI'    => $agency,
                    'ALAMAT_INSTANSI'  => $address,
                    'TELEPON_INSTANSI' => $office,
                    'EMAIL_INSTANSI'   => $email,
                    'JUMLAH_PERSONEL'  => $counts['JUMLAH_LAKI_LAKI'] + $counts['JUMLAH_PEREMPUAN'],
                    'KETERANGAN'       => 'Kunjungan studi',
                ];
            }
        }

        return $rows;
    }

    private function choiceLists(string $type): array
    {
        $names = fn (array $map) => array_values(array_map(static fn ($item) => $item['name'], $map));

        $rooms = [];
        foreach ($this->guestDb->table('locations')->select('Code, Name')->where('active', 1)->orderBy('Code')->get()->getResultArray() as $room) {
            $rooms[] = $room['Code'] . ' = ' . $room['Name'];
        }

        $lists = [];
        if ($type === 'non_anggota') {
            $lists['Pilihan JENIS_KELAMIN'] = $names($this->masterMap('jenis_kelamin', 'Name'));
            $lists['Pilihan PEKERJAAN'] = $names($this->masterMap('master_pekerjaan', 'Pekerjaan'));
            $lists['Pilihan PENDIDIKAN_TERAKHIR'] = $names($this->masterMap('master_pendidikan', 'Nama'));
        }
        $lists['Pilihan KODE_LOKASI_RUANG'] = $rooms;
        $lists['Pilihan TUJUAN_KUNJUNGAN'] = $names($this->purposeMap($type));

        return $lists;
    }

    private function writeRow(Worksheet $sheet, int $row, array $values): void
    {
        foreach (array_values($values) as $index => $value) {
            $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($index + 1) . $row, (string) $value, DataType::TYPE_STRING);
        }
    }

    private function styleHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EAF1F8']],
        ]);
    }

    // ----------------------------------------------------------------
    // PEMERIKSAAN BARIS
    // ----------------------------------------------------------------

    /** @return array{0: array, 1: array} data siap simpan dan daftar pesan untuk pengguna */
    private function buildRecords(string $type, array $rows, array $header): array
    {
        $positions = array_flip($header);
        $records = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $values = [];
            foreach (array_keys($this->columns($type)) as $column) {
                $values[$column] = $this->cellText($row[$positions[$column]] ?? null);
            }
            if (implode('', $values) === '') {
                continue;
            }

            $rowErrors = [];
            foreach ($values as $column => $value) {
                // Isi buku tamu ditampilkan apa adanya di tabel admin
                if (strpbrk($value, '<>') !== false) {
                    $rowErrors[] = "{$column} tidak boleh berisi tanda < atau >.";
                }
            }

            $common = $this->commonFields($type, $values, $rowErrors);
            $own = match ($type) {
                'anggota'     => $this->memberFields($values, $rowErrors),
                'non_anggota' => $this->visitorFields($values, $rowErrors),
                default       => $this->groupFields($values, $rowErrors),
            };

            if ($rowErrors === []) {
                $records[] = $own + $common;
            }
            foreach ($rowErrors as $message) {
                $errors[] = 'Baris ' . ($index + 2) . ': ' . $message;
            }
        }

        return [$records, $errors];
    }

    private function commonFields(string $type, array $values, array &$errors): array
    {
        $visitDate = $this->parseVisitDate($values['TANGGAL_KUNJUNGAN'], $errors);
        $locationId = $this->parseLocation($values['KODE_LOKASI_RUANG'], $errors);
        $purposeId = null;

        if ($values['TUJUAN_KUNJUNGAN'] !== '') {
            $purposeId = $this->purposeMap($type)[mb_strtolower($values['TUJUAN_KUNJUNGAN'])]['id'] ?? null;
            if ($purposeId === null) {
                $errors[] = "Tujuan kunjungan \"{$values['TUJUAN_KUNJUNGAN']}\" tidak tersedia untuk " . self::TYPES[$type]['label'] . '. Lihat daftar di lembar Petunjuk pada template.';
            }
        }

        $isGroup = $type === 'rombongan';

        return [
            'CreateDate'     => $visitDate,
            'UpdateDate'     => date('Y-m-d H:i:s'),
            'CreateBy'       => login_id(),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id'      => branch_id(),
            ($isGroup ? 'Location_ID' : 'Location_Id') => $locationId,
            ($isGroup ? 'TujuanKunjungan_ID' : 'TujuanKunjungan_Id') => $purposeId,
        ];
    }

    private function memberFields(array $values, array &$errors): array
    {
        $number = $values['NOMOR_ANGGOTA'];
        if ($number === '') {
            $errors[] = 'Nomor anggota belum diisi.';
            return [];
        }

        $member = $this->guestDb->table('members')
            ->select('MemberNo, Fullname, EducationLevel_id, Job_id, Sex_id, Address')
            ->where('MemberNo', $number)
            ->get()->getRow();

        if (!$member) {
            $errors[] = "Nomor anggota \"{$number}\" tidak terdaftar. Periksa kembali nomornya.";
            return [];
        }

        return [
            'NoAnggota'             => $member->MemberNo,
            'Nama'                  => $member->Fullname,
            'PendidikanTerakhir_id' => $member->EducationLevel_id,
            'Profesi_id'            => $member->Job_id,
            'JenisKelamin_id'       => $member->Sex_id,
            'Alamat'                => $member->Address,
        ];
    }

    private function visitorFields(array $values, array &$errors): array
    {
        return [
            'Nama'                  => $this->requiredText($values['NAMA'], 'Nama', 100, $errors),
            'JenisKelamin_id'       => $this->requiredMaster($values['JENIS_KELAMIN'], 'Jenis kelamin', 'jenis_kelamin', 'Name', $errors),
            'Profesi_id'            => $this->requiredMaster($values['PEKERJAAN'], 'Pekerjaan', 'master_pekerjaan', 'Pekerjaan', $errors),
            'PendidikanTerakhir_id' => $this->requiredMaster($values['PENDIDIKAN_TERAKHIR'], 'Pendidikan terakhir', 'master_pendidikan', 'Nama', $errors),
            'Alamat'                => $this->optionalText($values['ALAMAT'], 'Alamat', 255, $errors),
        ];
    }

    private function groupFields(array $values, array &$errors): array
    {
        $personnel = $values['JUMLAH_PERSONEL'];
        if ($personnel === '') {
            $errors[] = 'Jumlah personel belum diisi.';
        } elseif (!ctype_digit($personnel) || (int) $personnel < 1) {
            $errors[] = "Jumlah personel harus berupa angka minimal 1, bukan \"{$personnel}\".";
        }
        $total = ctype_digit($personnel) ? (int) $personnel : 0;

        $counts = [];
        $groups = [
            'jenis kelamin' => self::COUNT_GENDER,
            'profesi'       => self::COUNT_PROFESSION,
            'pendidikan'    => self::COUNT_EDUCATION,
        ];
        foreach ($groups as $label => $group) {
            $sum = 0;
            foreach ($group as $column => $field) {
                $value = $values[$column];
                if ($value !== '' && !ctype_digit($value)) {
                    $errors[] = "{$column} harus berupa angka, bukan \"{$value}\".";
                    continue;
                }
                $counts[$field] = (int) $value;
                $sum += $counts[$field];
            }
            if ($total > 0 && $sum > $total) {
                $errors[] = "Total per {$label} ({$sum}) melebihi jumlah personel ({$total}).";
            }
        }

        $email = $values['EMAIL_INSTANSI'];
        if ($email !== '' && (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 255)) {
            $errors[] = "Email instansi \"{$email}\" tidak valid. Contoh yang benar: humas@instansi.go.id.";
        }

        return [
            'NamaKetua'        => $this->requiredText($values['NAMA_KETUA'], 'Nama ketua', 255, $errors),
            'NomerTelponKetua' => $this->optionalText($values['TELEPON_KETUA'], 'Telepon ketua', 20, $errors),
            'AsalInstansi'     => $this->requiredText($values['ASAL_INSTANSI'], 'Asal instansi', 255, $errors),
            // Kolom NOT NULL, sama seperti form buku tamu
            'AlamatInstansi'   => $this->optionalText($values['ALAMAT_INSTANSI'], 'Alamat instansi', 255, $errors) ?? '',
            'TeleponInstansi'  => $this->optionalText($values['TELEPON_INSTANSI'], 'Telepon instansi', 20, $errors),
            'EmailInstansi'    => $email === '' ? null : $email,
            'CountPersonel'    => $total,
            'Information'      => $this->optionalText($values['KETERANGAN'], 'Keterangan', 255, $errors),
            'NoPengunjung'     => date('Ymd') . sprintf('%04d', random_int(1, 9999)),
        ] + $counts;
    }

    private function requiredText(string $value, string $label, int $max, array &$errors): ?string
    {
        if ($value === '') {
            $errors[] = "{$label} belum diisi.";
            return null;
        }

        return $this->optionalText($value, $label, $max, $errors);
    }

    private function optionalText(string $value, string $label, int $max, array &$errors): ?string
    {
        if (mb_strlen($value) > $max) {
            $errors[] = "{$label} terlalu panjang. Maksimal {$max} karakter.";
        }

        return $value === '' ? null : $value;
    }

    private function requiredMaster(string $value, string $label, string $table, string $column, array &$errors): ?int
    {
        if ($value === '') {
            $errors[] = "{$label} belum diisi.";
            return null;
        }

        $id = $this->masterMap($table, $column)[mb_strtolower($value)]['id'] ?? null;
        if ($id === null) {
            $errors[] = "{$label} \"{$value}\" belum ada di data master. Lihat daftar di lembar Petunjuk pada template.";
        }

        return $id;
    }

    private function parseLocation(string $code, array &$errors): ?int
    {
        if ($code === '') {
            $errors[] = 'Kode lokasi ruang belum diisi.';
            return null;
        }

        if (!isset($this->masterCache['locations'])) {
            $this->masterCache['locations'] = [];
            foreach ($this->guestDb->table('locations')->select('ID, Code')->where('active', 1)->get()->getResultArray() as $room) {
                $this->masterCache['locations'][mb_strtolower(trim((string) $room['Code']))][] = (int) $room['ID'];
            }
        }

        $ids = $this->masterCache['locations'][mb_strtolower($code)] ?? [];
        if (count($ids) === 1) {
            return $ids[0];
        }

        $errors[] = $ids === []
            ? "Kode lokasi ruang \"{$code}\" tidak ditemukan. Lihat daftar di lembar Petunjuk pada template."
            : "Kode lokasi ruang \"{$code}\" dipakai oleh lebih dari satu ruang. Ubah kodenya di menu lokasi ruang agar berbeda.";

        return null;
    }

    private function parseVisitDate(string $value, array &$errors): ?string
    {
        if ($value === '') {
            $errors[] = 'Tanggal kunjungan belum diisi.';
            return null;
        }

        $date = null;
        if (is_numeric($value)) {
            // Sel bertipe tanggal terbaca sebagai nomor seri Excel
            $date = ExcelDate::excelToDateTimeObject((float) $value);
        } else {
            foreach (['d-m-Y H:i', 'd-m-Y', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'] as $format) {
                $parsed = \DateTime::createFromFormat('!' . $format, $value);
                $problems = \DateTime::getLastErrors();
                if ($parsed !== false && (!$problems || ($problems['warning_count'] === 0 && $problems['error_count'] === 0))) {
                    $date = $parsed;
                    break;
                }
            }
        }

        if ($date === null || (int) $date->format('Y') < 1990) {
            $errors[] = "Tanggal kunjungan \"{$value}\" tidak dikenali. Gunakan format dd-mm-yyyy, contoh 25-05-2026.";
            return null;
        }
        if ($date->format('Y-m-d H:i:s') > date('Y-m-d H:i:s')) {
            $errors[] = "Tanggal kunjungan \"{$value}\" belum terjadi. Periksa kembali tanggalnya.";
            return null;
        }

        return $date->format('Y-m-d H:i:s');
    }

    /** Sel kosong jadi string kosong; angka bulat tanpa desimal agar 12345.0 tetap 12345. */
    private function cellText($value): string
    {
        if (is_float($value) && floor($value) === $value) {
            $value = (string) (int) $value;
        }

        return trim((string) $value);
    }

    /** Nama huruf kecil => [id, nama asli]. */
    private function masterMap(string $table, string $column): array
    {
        if (!isset($this->masterCache[$table])) {
            $this->masterCache[$table] = [];
            foreach ($this->guestDb->table($table)->select("ID, {$column} AS label")->orderBy('ID')->get()->getResultArray() as $row) {
                $label = trim((string) $row['label']);
                $this->masterCache[$table][mb_strtolower($label)] ??= ['id' => (int) $row['ID'], 'name' => $label];
            }
        }

        return $this->masterCache[$table];
    }

    private function purposeMap(string $type): array
    {
        $key = 'purpose_' . $type;
        if (!isset($this->masterCache[$key])) {
            $this->masterCache[$key] = [];
            $rows = $this->guestDb->table('tujuan_kunjungan')
                ->select('ID, TujuanKunjungan')
                ->where(self::TYPES[$type]['purposeFlag'], 1)
                ->orderBy('ID')
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $label = trim((string) $row['TujuanKunjungan']);
                $this->masterCache[$key][mb_strtolower($label)] ??= ['id' => (int) $row['ID'], 'name' => $label];
            }
        }

        return $this->masterCache[$key];
    }
}
