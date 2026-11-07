<?php

namespace Eksemplar\Controllers;

use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * EksemplarImportController
 *
 * Menangani import eksemplar dari Excel dan download template.
 */
class EksemplarImportController extends \Base\Controllers\BaseController
{
    use ResponseTrait;
    use EksemplarBase;

    /** Isi tabel master yang sudah dibaca, agar tidak query ulang per baris. */
    private array $masterCache = [];

    /** Nilai setting FormatNomorBarcode dan FormatNomorRFID. */
    private array $numberFormats = [];

    function __construct()
    {
        $this->initEksemplarBase();
    }

    // ----------------------------------------------------------------
    // VIEWS
    // ----------------------------------------------------------------

    public function importviews()
    {
        $this->data['title'] = 'Import eksemplar Excel';
        return view('Eksemplar\Views\import', $this->data);
    }

    // ----------------------------------------------------------------
    // UPLOAD & PROCESS
    // ----------------------------------------------------------------

    public function uploadexcel()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('katalog/import'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'excel_file' => [
                'rules'  => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,10240]',
                'errors' => [
                    'uploaded' => 'Silakan pilih file Excel terlebih dahulu.',
                    'ext_in'   => 'File harus berformat Excel (.xlsx atau .xls).',
                    'max_size' => 'Ukuran file maksimal 10 MB.',
                ],
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return $this->fail(['excel_file' => 'File gagal diunggah. Silakan coba unggah ulang.']);
        }

        try {
            if (!is_dir(WRITEPATH . 'uploads/temp')) {
                mkdir(WRITEPATH . 'uploads/temp', 0755, true);
            }

            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $fileName);
            $filePath = WRITEPATH . 'uploads/temp/' . $fileName;

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray(null, true, false, false);

            [$header, $firstRowNumber, $rows] = $this->extractHeader($rows);
            if ($header === null) {
                unlink($filePath);
                return $this->fail(['message' => 'File ini bukan template import eksemplar. Kolom JUDUL_UTAMA tidak ditemukan. Unduh template di halaman ini, lalu isi kembali.']);
            }

            $result = $this->processImport($rows, $header, $firstRowNumber);

            unlink($filePath);

            if ($result['success_count'] === 0) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => $result['error_count'] > 0
                        ? 'Tidak ada data yang berhasil diimport. Perbaiki baris berikut, lalu unggah ulang file.'
                        : 'File tidak berisi data. Isi data mulai dari baris kedua.',
                    'errors'  => $result['errors'],
                ], 422);
            }

            return $this->respond([
                'status'  => 'success',
                'message' => 'Import berhasil',
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }

            log_message('error', 'Import eksemplar gagal: ' . $e->getMessage());

            return $this->fail(['message' => 'File tidak bisa diproses. Pastikan file memakai template yang disediakan, lalu coba lagi.']);
        }
    }

    // ----------------------------------------------------------------
    // DOWNLOAD TEMPLATE
    // ----------------------------------------------------------------

    public function downloadTemplate()
    {
        ob_clean();
        ini_set('memory_limit', '1024M');

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();

            $headers = [
                'NO', 'TGL_PENGADAAN', 'NO_INDUK', 'NO_BARCODE', 'NO_RFID',
                'JENIS_SUMBER', 'NAMA_SUMBER', 'MATA_UANG', 'HARGA',
                'KODE_LOKASI_PERPUSTAKAAN', 'KODE_LOKASI_RUANG', 'AKSES',
                'KATEGORI', 'MEDIA', 'KETERSEDIAAN', 'NOMOR_PANGGIL_EKSEMPLAR',
                'JENIS_BAHAN', 'JUDUL_UTAMA', 'ANAK_JUDUL', 'PERNYATAAN_TANGGUNGJAWAB',
                'TAJUK_PENGARANG', 'TAJUK_PENGARANG_BADAN_KOOPERASI',
                'PENGARANG_TAMBAHAN_NAMA_ORANG', 'PENGARANG_TAMBAHAN_NAMA_BADAN',
                'EDISI', 'KOTA_TERBIT', 'PENERBIT', 'TAHUN_TERBIT',
                'JUMLAH_HALAMAN', 'DIMENSI', 'ISBN', 'ISSN', 'ISMN',
                'NO_DDC', 'NOMOR_PANGGIL_KATALOG', 'ABSTRAK', 'BAHASA',
                'SUBJEK_TOPIK', 'EDISI_SERIAL', 'TGL_TERBIT_EDISI_SERIAL',
                'BAHAN_SERTAAN_SERIAL', 'KETERANGAN_LAIN_SERIAL',
            ];

            $sheet->fromArray([$headers], null, 'A1');

            $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);

            $sampleData = [
                [1,'14-02-2015','X0022/2016','X0022/2016','X0022/2016','Hadiah/Hibah','---Belum ditentukan---','IDR',0,'Pusat','0101','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','123 PRA m','Monograf','Mahligai Biru','','Mamik Pradana','Pradana, Mamik','','','','','Jakarta','Grafika','2015','120 hlm.','25 cm.','978-222-666-444','','','123','123 PRA m','','ind','Rumah Tangga','','','',''],
                [2,'15-02-2015','X0023/2016','X0023/2016','X0023/2016','Pembelian','---Belum ditentukan---','IDR',0,'Pusat','0101','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','201 SAM k','Monograf','Kancil dan Kerbau','','Deni Saman','Saman, Deni','','','','','Jakarta','Prabu','2015','68 hlm.','21 cm.','856-225-456-78','','','201','201 SAM k','','ind','Fiksi','','','',''],
                [3,'16-03-2015','X0024/2016','X0024/2016','X0024/2016','Pembelian','---Belum ditentukan---','IDR',75000,'Pusat','0102','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','004.678 BUD p','Monograf','Pemrograman Web dengan PHP','Panduan Lengkap untuk Pemula','Budi Raharjo','Raharjo, Budi','','','','Edisi 2','Bandung','Informatika','2015','350 hlm.','24 cm.','978-602-1234-567-8','','','004.678','004.678 BUD p','Buku panduan pemrograman web menggunakan PHP','ind','Teknologi Informasi; Pemrograman','','','',''],
                [4,'20-03-2015','X0025/2016','X0025/2016','X0025/2016','Hadiah/Hibah','---Belum ditentukan---','IDR',0,'Pusat','0103','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','899.221 DEW s','Monograf','Sastra Indonesia Kontemporer','Analisis dan Apresiasi','Dewi Lestari','Lestari, Dewi','','Pusat Bahasa','','Edisi 3','Jakarta','Gramedia Pustaka Utama','2015','320 hlm.','20 cm.','978-602-0307-456-7','','','899.221','899.221 DEW s','Kumpulan analisis sastra Indonesia modern','ind','Sastra Indonesia; Literatur','','','',''],
                [5,'25-03-2015','X0026/2016','X0026/2016','X0026/2016','Pembelian','---Belum ditentukan---','IDR',85000,'Pusat','0101','Dapat dipinjam','Koleksi Referensi','Buku','Tersedia','904.598 BAM s','Monograf','Sejarah Perkembangan Teknologi Digital di Indonesia','','Prof. Dr. Bambang Sutrisno; Dr. Maya Sari','Sutrisno, Bambang','','','Sari, Maya','Edisi 1','Jakarta','Erlangga','2015','500 hlm.','24 cm.','978-602-2989-345-6','','','904.598','904.598 BAM s','Dokumentasi lengkap perkembangan teknologi digital di Indonesia','ind','Sejarah; Teknologi; Indonesia','','','',''],
            ];

            $sheet->fromArray($sampleData, null, 'A2');

            for ($col = 1; $col <= count($headers); $col++) {
                $sheet->getColumnDimension(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col)
                )->setAutoSize(true);
            }

            $dataRange = 'A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . (count($sampleData) + 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);

            $filename = 'template_import_katalog_' . date('Y-m-d') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        } catch (\Exception $e) {
            log_message('error', 'Download template error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
        }

        exit;
    }

    // ----------------------------------------------------------------
    // PRIVATE IMPORT HELPERS
    // ----------------------------------------------------------------

    /**
     * Baris judul kolom bisa di baris 1 (template ini) atau baris 2 (template INLISLite 3,
     * yang baris 1-nya berisi judul kelompok dan nama kolomnya memakai spasi).
     *
     * @return array{0: ?array, 1: int, 2: array} nama kolom, nomor baris data pertama, baris data
     */
    private function extractHeader(array $rows): array
    {
        foreach (array_slice($rows, 0, 2) as $index => $row) {
            $header = array_map(static fn ($name) => trim(preg_replace('/[^A-Z0-9]+/', '_', strtoupper((string) $name)), '_'), $row);
            if (in_array('JUDUL_UTAMA', $header, true)) {
                return [$header, $index + 2, array_slice($rows, $index + 1)];
            }
        }

        return [null, 0, []];
    }

    /**
     * Tiap baris punya transaksi sendiri: baris gagal dibatalkan utuh, baris lain tetap masuk.
     * DomainException berisi pesan untuk pengguna. Error lain hanya dicatat di log.
     */
    private function processImport($rows, $header, int $firstRowNumber = 2)
    {
        $successCount = 0;
        $errors       = [];

        foreach ($rows as $rowIndex => $row) {
            if (empty(array_filter($row, static fn ($value) => trim((string) $value) !== ''))) continue;

            $rowNumber = $rowIndex + $firstRowNumber;

            $this->db->transBegin();

            try {
                $catalogData     = $this->parseCatalogData($row, $header);
                $collectionsData = $this->parseCollectionsData($row, $header);
                $catalogData['Location_id'] = $collectionsData['Location_id'];

                $this->assertBarcodeAvailable($collectionsData['NomorBarcode']);

                $collectionsData['Catalog_id'] = $this->saveCatalog($catalogData, $collectionsData['NoInduk']);
                $this->insertCollection($collectionsData);

                $this->db->transCommit();
                $successCount++;
            } catch (\DomainException $e) {
                $this->db->transRollback();
                $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                log_message('error', "Import eksemplar baris {$rowNumber}: " . $e->getMessage());
                $errors[] = "Baris {$rowNumber}: Data tidak dapat disimpan. Periksa isi baris ini atau hubungi administrator.";
            }
        }

        return [
            'success_count' => $successCount,
            'error_count'   => count($errors),
            'errors'        => $errors,
        ];
    }

    /** Katalog yang sama dipakai ulang; bila belum ada, katalog dan ruasnya dibuat. Mengembalikan ID katalog. */
    private function saveCatalog(array $catalogData, $noInduk): int
    {
        $ruas = $catalogData['_ruas'];
        unset($catalogData['_ruas']);

        $existing = $this->db->table('catalogs')->select('ID')->where([
            'Worksheet_id'    => $catalogData['Worksheet_id'],
            'Title'           => $catalogData['Title'],
            'Author'          => $catalogData['Author'],
            'Edition'         => $catalogData['Edition'],
            'Publisher'       => $catalogData['Publisher'],
            'PublishLocation' => $catalogData['PublishLocation'],
            'PublishYear'     => $catalogData['PublishYear'],
        ])->get()->getRow();

        if ($existing) {
            $catalogId = (int) $existing->ID;
            if ((string) $noInduk !== '') {
                $last = $this->db->table('catalog_ruas')->selectMax('Sequence', 'Sequence')->where('CatalogId', $catalogId)->get()->getRow();
                $this->insertMarcFields($catalogId, [$this->ruasRow('990', '$a ' . $noInduk)], (int) ($last->Sequence ?? 0) + 1);
            }

            return $catalogId;
        }

        $catalogData['ControlNumber'] = get_control_number();
        $catalogData['BIBID']         = get_bib_id();
        $catalogId = $this->insertCatalog($catalogData);

        $head = [
            ['Tag' => '001', 'Indicator1' => null, 'Indicator2' => null, 'Value' => $catalogData['ControlNumber']],
            ['Tag' => '005', 'Indicator1' => null, 'Indicator2' => null, 'Value' => date('YmdHis')],
        ];
        if ($catalogData['Languages'] !== '') {
            $head[] = $this->ruasRow('008', str_repeat('#', 35) . $catalogData['Languages'] . '##');
        }
        $head[] = $this->ruasRow('035', '$a ' . $catalogData['BIBID']);
        $tail = (string) $noInduk !== '' ? [$this->ruasRow('990', '$a ' . $noInduk)] : [];

        $this->insertMarcFields($catalogId, array_merge($head, $ruas, $tail), 1);

        return $catalogId;
    }

    private function ruasRow(string $tag, string $value): array
    {
        return ['Tag' => $tag, 'Indicator1' => '#', 'Indicator2' => '#', 'Value' => $value];
    }

    /** Ruas MARC katalog, urutannya mengikuti import INLISLite 3. */
    private function buildRuas(array $v): array
    {
        $rows = [];
        $single = function (string $tag, string $value) use (&$rows) {
            if ($value !== '') {
                $rows[] = $this->ruasRow($tag, '$a ' . $value);
            }
        };
        $repeat = function (string $tag, string $value) use (&$rows) {
            foreach (explode(';', $value) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $rows[] = $this->ruasRow($tag, '$a ' . $part);
                }
            }
        };
        $marc = function (string $tag, array $subfields) use (&$rows) {
            $value = marc_build_value($tag, $subfields);
            if ($value !== '') {
                $rows[] = $this->ruasRow($tag, $value);
            }
        };

        $marc('245', [['a', $v['title_a']], ['b', $v['title_b']], ['c', $v['title_c']]]);
        $single('100', $v['author']);
        $single('110', $v['author_body']);
        $repeat('700', $v['author_added']);
        $repeat('710', $v['author_added_body']);
        $single('250', $v['edition']);
        $marc('260', [['a', $v['publish_location']], ['b', $v['publisher']], ['c', $v['publish_year']]]);
        $marc('300', [['a', $v['pages']], ['c', $v['dimension']]]);
        $repeat('020', $v['isbn']);
        $repeat('022', $v['issn']);
        $repeat('024', $v['ismn']);
        $repeat('084', $v['call_number']);
        $repeat('520', $v['abstract']);
        $single('082', $v['dewey']);
        $repeat('650', $v['subject']);
        $single('863', $v['serial_edition']);

        return $rows;
    }

    private function getValue($row, $headerMap, $columnName, $default = '')
    {
        try {
            if (!is_array($headerMap) || !is_array($row)) {
                return $default;
            }

            if (!isset($headerMap[$columnName])) {
                return $default;
            }

            $columnIndex = (int) $headerMap[$columnName];

            if (!isset($row[$columnIndex])) {
                return $default;
            }

            $value = $row[$columnIndex];
            // Angka bulat dari Excel terbaca sebagai float, mis. 12345.0
            if (is_float($value) && floor($value) === $value) {
                $value = (string) (int) $value;
            }
            return is_string($value) ? trim(preg_replace('/\s+/', ' ', $value)) : $value;
        } catch (\Exception $e) {
            log_message('error', "Error in getValue for {$columnName}: " . $e->getMessage());
            return $default;
        }
    }

    private function parseCatalogData($row, $header)
    {
        $headerMap = array_flip($header);
        $text = fn (string $column) => trim((string) $this->getValue($row, $headerMap, $column));

        $v = [
            'title_a'           => $text('JUDUL_UTAMA'),
            'title_b'           => $text('ANAK_JUDUL'),
            'title_c'           => $text('PERNYATAAN_TANGGUNGJAWAB'),
            'author'            => $text('TAJUK_PENGARANG'),
            'author_body'       => $text('TAJUK_PENGARANG_BADAN_KOOPERASI'),
            'author_added'      => $text('PENGARANG_TAMBAHAN_NAMA_ORANG'),
            'author_added_body' => $text('PENGARANG_TAMBAHAN_NAMA_BADAN'),
            'edition'           => $text('EDISI'),
            'publish_location'  => $text('KOTA_TERBIT'),
            'publisher'         => $text('PENERBIT'),
            'publish_year'      => $text('TAHUN_TERBIT'),
            'pages'             => $text('JUMLAH_HALAMAN'),
            'dimension'         => $text('DIMENSI'),
            'isbn'              => $text('ISBN'),
            'issn'              => $text('ISSN'),
            'ismn'              => $text('ISMN'),
            'dewey'             => $text('NO_DDC'),
            'call_number'       => $text('NOMOR_PANGGIL_KATALOG'),
            'abstract'          => $text('ABSTRAK'),
            'languages'         => $text('BAHASA'),
            'subject'           => $text('SUBJEK_TOPIK'),
            'serial_edition'    => $text('EDISI_SERIAL'),
        ];

        if ($v['title_a'] === '') {
            throw new \DomainException('Judul utama belum diisi.');
        }

        $plain = static fn (string $marc) => trim(preg_replace('/\s+/', ' ', preg_replace('/\$\w/', ' ', $marc)));
        $publication = static fn (string $value) => preg_replace('/[^a-zA-Z0-9 ]/', '', $value);

        return [
            'Title'               => $plain(marc_build_value('245', [['a', $v['title_a']], ['b', $v['title_b']], ['c', $v['title_c']]])),
            'Author'              => $v['author'],
            'Worksheet_id'        => $this->masterId('worksheets', 'Jenis bahan', $this->getValue($row, $headerMap, 'JENIS_BAHAN'), 'Monograf'),
            'Edition'             => $v['edition'],
            'Publisher'           => $v['publisher'],
            'PublishLocation'     => $v['publish_location'],
            'PublishYear'         => $v['publish_year'],
            'Publikasi'           => $publication($v['publish_location']) . ' : ' . $publication($v['publisher']) . ', ' . $publication($v['publish_year']),
            'Subject'             => $v['subject'],
            'PhysicalDescription' => $plain(marc_build_value('300', [['a', $v['pages']], ['c', $v['dimension']]])),
            'ISBN'                => implode(' ; ', array_filter([$v['isbn'], $v['issn'], $v['ismn']], 'strlen')),
            'CallNumber'          => $v['call_number'],
            'Note'                => $v['abstract'],
            'Languages'           => $v['languages'],
            'DeweyNo'             => $v['dewey'],
            'CreateBy'            => user()->id ?? 1,
            'CreateDate'          => date('Y-m-d H:i:s'),
            'CreateTerminal'      => $this->request->getIPAddress(),
            'Branch_id'           => user()->branch_id ?? 1,
            'IsOPAC'              => 1,
            'IsBNI'               => 1,
            'IsKIN'               => 1,
            'IsRDA'               => 0, // Ruas memakai 260, sama seperti import AACR INLISLite 3
            'active'              => 1,
            '_ruas'               => $this->buildRuas($v),
        ];
    }

    private function parseCollectionsData($row, $header)
    {
        $headerMap         = array_flip($header);
        $locationLibraryId = $this->parseLocationLibraryId($this->getValue($row, $headerMap, 'KODE_LOKASI_PERPUSTAKAAN'));
        $noInduk           = trim((string) $this->getValue($row, $headerMap, 'NO_INDUK'));

        $price = trim((string) $this->getValue($row, $headerMap, 'HARGA'));
        if ($price !== '' && !is_numeric($price)) {
            throw new \DomainException("Harga \"{$price}\" harus berupa angka tanpa titik atau koma.");
        }

        $purchaseDate = $this->parseImportDate($this->getValue($row, $headerMap, 'TGL_PENGADAAN'), 'Tanggal pengadaan');
        if ($purchaseDate === null) {
            throw new \DomainException('Tanggal pengadaan belum diisi.');
        }

        return [
            'NomorBarcode'       => $this->resolveItemNumber($this->getValue($row, $headerMap, 'NO_BARCODE'), 'FormatNomorBarcode', $noInduk),
            'NoInduk'            => $noInduk,
            'RFID'               => $this->resolveItemNumber($this->getValue($row, $headerMap, 'NO_RFID'), 'FormatNomorRFID', $noInduk),
            'Currency'           => $this->parseCurrency($this->getValue($row, $headerMap, 'MATA_UANG')),
            'Price'              => $price === '' ? 0 : $price,
            'TanggalPengadaan'   => $purchaseDate,
            'CallNumber'         => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_EKSEMPLAR'),
            'Branch_id'          => user()->branch_id ?? 1,
            'Partner_id'         => $this->parsePartnerId($this->getValue($row, $headerMap, 'NAMA_SUMBER')),
            'Location_id'        => $this->parseLocationId($this->getValue($row, $headerMap, 'KODE_LOKASI_RUANG'), $locationLibraryId),
            'Rule_id'            => $this->parseRuleId($this->getValue($row, $headerMap, 'AKSES')),
            'Category_id'        => $this->parseCategoryId($this->getValue($row, $headerMap, 'KATEGORI')),
            'Media_id'           => $this->parseMediaId($this->getValue($row, $headerMap, 'MEDIA')),
            'Source_id'          => $this->parseSourceId($this->getValue($row, $headerMap, 'JENIS_SUMBER')),
            'Status_id'          => $this->parseStatusId($this->getValue($row, $headerMap, 'KETERSEDIAAN')),
            'Location_Library_id'=> $locationLibraryId,
            'IsVerified'         => 0,
            'EDISISERIAL'        => $this->getValue($row, $headerMap, 'EDISI_SERIAL'),
            'TANGGAL_TERBIT_EDISI_SERIAL' => $this->parseImportDate($this->getValue($row, $headerMap, 'TGL_TERBIT_EDISI_SERIAL'), 'Tanggal terbit edisi serial'),
            'BAHAN_SERTAAN'      => $this->getValue($row, $headerMap, 'BAHAN_SERTAAN_SERIAL'),
            'KETERANGAN_LAIN'    => $this->getValue($row, $headerMap, 'KETERANGAN_LAIN_SERIAL'),
            'CreateBy'           => user()->id ?? 1,
            'CreateDate'         => date('Y-m-d H:i:s'),
            'CreateTerminal'     => $this->request->getIPAddress(),
        ];
    }

    /**
     * Isi kolom, atau nomor otomatis 11 digit bila kosong.
     * Setting bernilai "No. Induk" mendahulukan nomor induk.
     */
    private function resolveItemNumber($value, string $setting, string $noInduk): string
    {
        if (!array_key_exists($setting, $this->numberFormats)) {
            $row = $this->db->table('settingparameters')->select('Value')->where('Name', $setting)->get()->getRow();
            $this->numberFormats[$setting] = str_replace(' ', '', strtolower(trim((string) ($row->Value ?? ''))));
        }

        if ($this->numberFormats[$setting] === 'no.induk' && $noInduk !== '') {
            return $noInduk;
        }

        $value = trim((string) $value);
        if ($value !== '') {
            return $value;
        }

        $last = $this->db->table('collections')->selectMax('ID', 'ID')->get()->getRow();

        return str_pad((string) ((int) ($last->ID ?? 0) + 1), 11, '0', STR_PAD_LEFT);
    }

    private function parseCurrency($currency): string
    {
        $currency = trim((string) $currency);
        if ($currency === '') {
            return 'IDR';
        }

        $found = $this->db->table('currency')->select('Currency')->where('Currency', $currency)->get()->getRow();
        if (!$found) {
            throw new \DomainException("Mata uang \"{$currency}\" tidak dikenal. Gunakan kode seperti IDR atau USD.");
        }

        return $found->Currency;
    }

    private function insertCatalog($data)
    {
        $existing = $this->katalogModel->where('ControlNumber', $data['ControlNumber'])->first();
        if ($existing) {
            throw new \RuntimeException("ControlNumber {$data['ControlNumber']} sudah ada");
        }

        if (!$this->katalogModel->insert($data)) {
            throw new \RuntimeException("Gagal insert catalog: " . implode(', ', $this->katalogModel->errors()));
        }

        return $this->katalogModel->getInsertID();
    }

    private function insertMarcFields($catalogId, $marcFields, int $sequence)
    {
        foreach ($marcFields as $field) {
            $field += [
                'CatalogId'      => $catalogId,
                'Sequence'       => $sequence++,
                'CreateBy'       => user()->id ?? 1,
                'CreateDate'     => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id'      => user()->branch_id ?? 1,
                'active'         => 1,
            ];

            // Koneksi yang sama dengan katalog dan eksemplar, supaya ikut batal bila barisnya gagal
            if (!$this->db->table('catalog_ruas')->insert($field)) {
                throw new \RuntimeException('Gagal insert MARC field: ' . $this->db->error()['message']);
            }
        }
    }

    private function assertBarcodeAvailable(string $barcode): void
    {
        if ($this->eksemplarModel->where('NomorBarcode', $barcode)->first()) {
            throw new \DomainException("Nomor barcode {$barcode} sudah dipakai eksemplar lain.");
        }
    }

    private function insertCollection(array $collection): void
    {
        if (!$this->eksemplarModel->insert($collection)) {
            throw new \RuntimeException('Gagal insert collection: ' . implode(', ', $this->eksemplarModel->errors()) . ' ' . $this->db->error()['message']);
        }
    }

    /** Tanggal dd-mm-yyyy atau nomor seri Excel. Null bila kosong. */
    private function parseImportDate($value, string $label): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $date = null;
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
        } else {
            foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
                $parsed = \DateTime::createFromFormat('!' . $format, $value);
                $problems = \DateTime::getLastErrors();
                if ($parsed !== false && (!$problems || ($problems['warning_count'] === 0 && $problems['error_count'] === 0))) {
                    $date = $parsed;
                    break;
                }
            }
        }

        if ($date === null || (int) $date->format('Y') < 1900) {
            throw new \DomainException("{$label} \"{$value}\" tidak dikenali. Gunakan format dd-mm-yyyy, contoh 14-02-2015.");
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * ID data master berdasarkan nama, tanpa membedakan huruf besar-kecil.
     * Nilai kosong memakai $default. Tanpa $default, nilai wajib diisi.
     */
    private function masterId(string $table, string $label, $value, ?string $default = null): int
    {
        $value = trim((string) $value);
        if ($value === '') {
            if ($default === null) {
                throw new \DomainException("{$label} belum diisi.");
            }
            $value = $default;
        }

        if (!isset($this->masterCache[$table])) {
            $this->masterCache[$table] = [];
            foreach ($this->db->table($table)->select('ID, Name')->orderBy('ID')->get()->getResultArray() as $master) {
                $this->masterCache[$table][mb_strtolower(trim((string) $master['Name']))] ??= (int) $master['ID'];
            }
        }

        $id = $this->masterCache[$table][mb_strtolower($value)] ?? null;
        if ($id === null) {
            throw new \DomainException("{$label} \"{$value}\" belum ada di data master. Perbaiki isi Excel atau tambahkan dulu di menu master.");
        }

        return $id;
    }

    private function parsePartnerId($namaSumber)
    {
        return $this->masterId('partners', 'Nama sumber', $namaSumber);
    }

    private function parseLocationId($kodeLokasi, $locationLibraryId)
    {
        $kodeLokasi = trim((string) $kodeLokasi);
        if ($kodeLokasi === '') {
            throw new \DomainException('Kode lokasi ruang belum diisi.');
        }

        $location = $this->db->table('locations')
            ->select('ID')
            ->where('Code', $kodeLokasi)
            ->where('LocationLibrary_id', $locationLibraryId)
            ->get()->getRowArray();

        if (!$location) {
            throw new \DomainException("Kode lokasi ruang \"{$kodeLokasi}\" tidak ada di perpustakaan ini. Perbaiki isi Excel atau tambahkan dulu lokasi ruangnya.");
        }

        return (int) $location['ID'];
    }

    private function parseRuleId($akses)
    {
        return $this->masterId('collectionrules', 'Akses', $akses, 'Dapat dipinjam');
    }

    private function parseCategoryId($kategori)
    {
        return $this->masterId('collectioncategorys', 'Kategori', $kategori, 'Koleksi Umum');
    }

    private function parseMediaId($media)
    {
        return $this->masterId('collectionmedias', 'Media', $media, 'Buku');
    }

    private function parseSourceId($jenisSumber)
    {
        return $this->masterId('collectionsources', 'Jenis sumber', $jenisSumber, 'Pembelian');
    }

    private function parseStatusId($ketersediaan)
    {
        return $this->masterId('collectionstatus', 'Ketersediaan', $ketersediaan, 'Tersedia');
    }

    private function parseLocationLibraryId($kodeLokasiPerpustakaan)
    {
        $kodeLokasiPerpustakaan = trim((string) $kodeLokasiPerpustakaan);
        if ($kodeLokasiPerpustakaan === '') {
            throw new \DomainException('Kode lokasi perpustakaan belum diisi.');
        }

        $library = $this->db->table('location_library')
            ->select('ID')
            ->where('Code', $kodeLokasiPerpustakaan)
            ->get()->getRowArray();

        if (!$library) {
            throw new \DomainException("Kode lokasi perpustakaan \"{$kodeLokasiPerpustakaan}\" tidak ditemukan. Perbaiki isi Excel atau tambahkan dulu lokasi perpustakaannya.");
        }

        return (int) $library['ID'];
    }
}
