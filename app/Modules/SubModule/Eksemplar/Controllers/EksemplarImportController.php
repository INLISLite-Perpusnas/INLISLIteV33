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
            'excel_file' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,10240]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return $this->fail(['excel_file' => 'File tidak valid']);
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
            $rows        = $worksheet->toArray();

            $header = array_shift($rows);
            $result = $this->processImport($rows, $header);

            unlink($filePath);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Import berhasil',
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }

            return $this->fail(['message' => 'Error saat import: ' . $e->getMessage()]);
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
                [3,'16-03-2015','X0024/2016','X0024/2016','X0024/2016','Pembelian','Toko Buku Mandiri','IDR',75000,'Pusat','0102','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','004.678 BUD p','Monograf','Pemrograman Web dengan PHP','Panduan Lengkap untuk Pemula','Budi Raharjo','Raharjo, Budi','','','','Edisi 2','Bandung','Informatika','2015','350 hlm.','24 cm.','978-602-1234-567-8','','','004.678','004.678 BUD p','Buku panduan pemrograman web menggunakan PHP','ind','Teknologi Informasi; Pemrograman','','','',''],
                [4,'20-03-2015','X0025/2016','X0025/2016','X0025/2016','Hadiah/Hibah','Dinas Pendidikan','IDR',0,'Pusat','0103','Dapat dipinjam','Koleksi Umum','Buku','Tersedia','899.221 DEW s','Monograf','Sastra Indonesia Kontemporer','Analisis dan Apresiasi','Dewi Lestari','Lestari, Dewi','','Pusat Bahasa','','Edisi 3','Jakarta','Gramedia Pustaka Utama','2015','320 hlm.','20 cm.','978-602-0307-456-7','','','899.221','899.221 DEW s','Kumpulan analisis sastra Indonesia modern','ind','Sastra Indonesia; Literatur','','','',''],
                [5,'25-03-2015','X0026/2016','X0026/2016','X0026/2016','Pembelian','CV. Pustaka Ilmu','IDR',85000,'Pusat','0104','Dapat dipinjam','Koleksi Referensi','Buku','Tersedia','904.598 BAM s','Monograf','Sejarah Perkembangan Teknologi Digital di Indonesia','','Prof. Dr. Bambang Sutrisno; Dr. Maya Sari','Sutrisno, Bambang','','','Sari, Maya','Edisi 1','Jakarta','Erlangga','2015','500 hlm.','24 cm.','978-602-2989-345-6','','','904.598','904.598 BAM s','Dokumentasi lengkap perkembangan teknologi digital di Indonesia','ind','Sejarah; Teknologi; Indonesia','','','',''],
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

    private function processImport($rows, $header)
    {
        $successCount = 0;
        $errorCount   = 0;
        $errors       = [];

        $this->db->transBegin();

        try {
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) continue;

                $rowNumber = $rowIndex + 2;

                try {
                    $catalogData     = $this->parseCatalogData($row, $header);
                    $collectionsData = $this->parseCollectionsData($row, $header);
                    $marcFields      = $this->generateBasicMarcFields($catalogData);

                    $catalogId = $this->insertCatalog($catalogData);

                    if (!empty($marcFields)) {
                        $this->insertMarcFields($catalogId, $marcFields);
                    }

                    if (!empty($collectionsData)) {
                        $this->insertCollections($catalogId, [$collectionsData]);
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            if ($errorCount > 0 && $successCount == 0) {
                $this->db->transRollback();
                throw new \Exception("Semua data gagal diimport. Errors: " . implode('; ', array_slice($errors, 0, 5)));
            }

            $this->db->transCommit();

            return [
                'success_count' => $successCount,
                'error_count'   => $errorCount,
                'errors'        => array_slice($errors, 0, 10),
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function generateBasicMarcFields($catalogData)
    {
        $marcFields = [];
        $sequence   = 1;

        $baseField = [
            'Indicator1'     => '#',
            'Indicator2'     => '#',
            'CreateBy'       => user()->id ?? 1,
            'CreateDate'     => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id'      => user()->branch_id ?? 1,
            'active'         => 1,
        ];

        if (!empty($catalogData['ControlNumber'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '001', 'Value' => $catalogData['ControlNumber'], 'Sequence' => $sequence++]);
        }

        $marcFields[] = array_merge($baseField, ['Tag' => '005', 'Value' => date('YmdHis'), 'Sequence' => $sequence++]);

        if (!empty($catalogData['ISBN'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '020', 'Value' => '$a ' . $catalogData['ISBN'], 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['DeweyNo'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '082', 'Value' => '$a ' . $catalogData['DeweyNo'], 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['Author'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '100', 'Indicator1' => '1', 'Value' => '$a ' . $catalogData['Author'], 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['Title'])) {
            $titleValue   = '$a ' . $catalogData['Title'];
            if (!empty($catalogData['Author'])) {
                $titleValue .= ' /$c ' . $catalogData['Author'];
            }
            $marcFields[] = array_merge($baseField, ['Tag' => '245', 'Indicator1' => '1', 'Indicator2' => '0', 'Value' => $titleValue, 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['Edition'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '250', 'Value' => '$a ' . $catalogData['Edition'], 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['PublishLocation']) || !empty($catalogData['Publisher']) || !empty($catalogData['PublishYear'])) {
            $pubValue = '';
            if (!empty($catalogData['PublishLocation'])) $pubValue .= '$a ' . $catalogData['PublishLocation'] . ' :';
            if (!empty($catalogData['Publisher']))       $pubValue .= '$b ' . $catalogData['Publisher'] . ',';
            if (!empty($catalogData['PublishYear']))     $pubValue .= '$c ' . $catalogData['PublishYear'];

            if (!empty($pubValue)) {
                $marcFields[] = array_merge($baseField, ['Tag' => '260', 'Value' => $pubValue, 'Sequence' => $sequence++]);
            }
        }

        if (!empty($catalogData['PhysicalDescription'])) {
            $marcFields[] = array_merge($baseField, ['Tag' => '300', 'Value' => '$a ' . $catalogData['PhysicalDescription'], 'Sequence' => $sequence++]);
        }

        if (!empty($catalogData['Subject'])) {
            foreach (explode(';', $catalogData['Subject']) as $subject) {
                $subject = trim($subject);
                if (!empty($subject)) {
                    $marcFields[] = array_merge($baseField, ['Tag' => '650', 'Value' => '$a ' . $subject, 'Sequence' => $sequence++]);
                }
            }
        }

        return $marcFields;
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
            return is_string($value) ? trim($value) : $value;
        } catch (\Exception $e) {
            log_message('error', "Error in getValue for {$columnName}: " . $e->getMessage());
            return $default;
        }
    }

    private function parseCatalogData($row, $header)
    {
        $headerMap     = array_flip($header);
        $controlNumber = $this->generateUniqueControlNumber();

        $judulUtama = $this->getValue($row, $headerMap, 'JUDUL_UTAMA');
        $anakJudul  = $this->getValue($row, $headerMap, 'ANAK_JUDUL');
        $title      = $judulUtama . ($anakJudul ? ' : ' . $anakJudul : '');

        $jumlahHalaman       = $this->getValue($row, $headerMap, 'JUMLAH_HALAMAN');
        $dimensi             = $this->getValue($row, $headerMap, 'DIMENSI');
        $physicalDescription = $jumlahHalaman . ($dimensi ? ' ; ' . $dimensi : '');

        $data = [
            'ControlNumber'       => $controlNumber,
            'BIBID'               => $this->generateBibId($controlNumber),
            'Title'               => $title,
            'Author'              => $this->getValue($row, $headerMap, 'TAJUK_PENGARANG'),
            'Worksheet_id'        => 1,
            'Edition'             => $this->getValue($row, $headerMap, 'EDISI'),
            'Publisher'           => $this->getValue($row, $headerMap, 'PENERBIT'),
            'PublishLocation'     => $this->getValue($row, $headerMap, 'KOTA_TERBIT'),
            'PublishYear'         => $this->getValue($row, $headerMap, 'TAHUN_TERBIT'),
            'Subject'             => $this->getValue($row, $headerMap, 'SUBJEK_TOPIK'),
            'PhysicalDescription' => $physicalDescription,
            'ISBN'                => $this->getValue($row, $headerMap, 'ISBN'),
            'CallNumber'          => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_KATALOG'),
            'Note'                => $this->getValue($row, $headerMap, 'ABSTRAK'),
            'Languages'           => $this->getValue($row, $headerMap, 'BAHASA'),
            'DeweyNo'             => $this->getValue($row, $headerMap, 'NO_DDC'),
            'CreateBy'            => user()->id ?? 1,
            'CreateDate'          => date('Y-m-d H:i:s'),
            'CreateTerminal'      => $this->request->getIPAddress(),
            'Branch_id'           => user()->branch_id ?? 1,
            'Location_id'         => 1,
            'IsOPAC'              => 1,
            'IsBNI'               => 1,
            'IsKIN'               => 1,
            'IsRDA'               => 1,
            'active'              => 1,
        ];

        if (empty($data['Title'])) {
            throw new \Exception('Judul utama tidak boleh kosong');
        }

        return $data;
    }

    private function parseCollectionsData($row, $header)
    {
        $headerMap        = array_flip($header);
        $tglPengadaan     = $this->getValue($row, $headerMap, 'TGL_PENGADAAN');
        $tanggalPengadaan = $this->parseDate($tglPengadaan);

        $data = [
            'NomorBarcode'       => $this->getValue($row, $headerMap, 'NO_BARCODE'),
            'NoInduk'            => $this->getValue($row, $headerMap, 'NO_INDUK'),
            'RFID'               => $this->getValue($row, $headerMap, 'NO_RFID'),
            'Currency'           => $this->getValue($row, $headerMap, 'MATA_UANG', 'IDR'),
            'Price'              => (int) $this->getValue($row, $headerMap, 'HARGA', 0),
            'PriceType'          => 'Per eksemplar',
            'TanggalPengadaan'   => $tanggalPengadaan,
            'CallNumber'         => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_EKSEMPLAR'),
            'Branch_id'          => user()->branch_id ?? 1,
            'Partner_id'         => $this->parsePartnerId($this->getValue($row, $headerMap, 'NAMA_SUMBER')),
            'Location_id'        => $this->parseLocationId($this->getValue($row, $headerMap, 'KODE_LOKASI_RUANG')),
            'Rule_id'            => $this->parseRuleId($this->getValue($row, $headerMap, 'AKSES')),
            'Category_id'        => $this->parseCategoryId($this->getValue($row, $headerMap, 'KATEGORI')),
            'Media_id'           => $this->parseMediaId($this->getValue($row, $headerMap, 'MEDIA')),
            'Source_id'          => $this->parseSourceId($this->getValue($row, $headerMap, 'JENIS_SUMBER')),
            'Status_id'          => $this->parseStatusId($this->getValue($row, $headerMap, 'KETERSEDIAAN')),
            'Location_Library_id'=> $this->parseLocationLibraryId($this->getValue($row, $headerMap, 'KODE_LOKASI_PERPUSTAKAAN')),
            'CreateBy'           => user()->id ?? 1,
            'CreateDate'         => date('Y-m-d H:i:s'),
            'CreateTerminal'     => $this->request->getIPAddress(),
        ];

        if (empty($data['NomorBarcode'])) {
            throw new \Exception('Nomor Barcode tidak boleh kosong');
        }

        if (empty($data['NoInduk'])) {
            throw new \Exception('No Induk tidak boleh kosong');
        }

        return $data;
    }

    private function insertCatalog($data)
    {
        $existing = $this->katalogModel->where('ControlNumber', $data['ControlNumber'])->first();
        if ($existing) {
            throw new \Exception("ControlNumber {$data['ControlNumber']} sudah ada");
        }

        if (!$this->katalogModel->insert($data)) {
            throw new \Exception("Gagal insert catalog: " . implode(', ', $this->katalogModel->errors()));
        }

        return $this->katalogModel->getInsertID();
    }

    private function insertMarcFields($catalogId, $marcFields)
    {
        foreach ($marcFields as $field) {
            $field['CatalogId'] = $catalogId;

            if (!$this->katalogRuasModel->insert($field)) {
                throw new \Exception("Gagal insert MARC field: " . implode(', ', $this->katalogRuasModel->errors()));
            }
        }
    }

    private function insertCollections($catalogId, $collections)
    {
        foreach ($collections as $collection) {
            $collection['Catalog_id'] = $catalogId;

            $existing = $this->eksemplarModel->where('NomorBarcode', $collection['NomorBarcode'])->first();
            if ($existing) {
                throw new \Exception("Barcode {$collection['NomorBarcode']} sudah ada");
            }

            if (!$this->eksemplarModel->insert($collection)) {
                throw new \Exception("Gagal insert collection: " . implode(', ', $this->eksemplarModel->errors()));
            }
        }
    }

    private function generateBibId($controlNumber)
    {
        $numberPart    = substr($controlNumber, 5);
        $lastSevenDigits = substr($numberPart, -7);
        return '0010-092' . $lastSevenDigits;
    }

    private function generateControlNumber()
    {
        $prefix      = 'INLIS';
        $numberLength = 19 - strlen($prefix);

        try {
            $lastRecord = $this->katalogModel
                ->select('ControlNumber')
                ->where('ControlNumber LIKE', $prefix . '%')
                ->orderBy('ControlNumber', 'DESC')
                ->first();

            $nextNumber = ($lastRecord && !empty($lastRecord->ControlNumber))
                ? (int) substr($lastRecord->ControlNumber, strlen($prefix)) + 1
                : 1;

            $controlNumber = $prefix . str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);

            if (strlen($controlNumber) !== 19) {
                throw new \Exception("Generated ControlNumber length mismatch: " . strlen($controlNumber));
            }

            return $controlNumber;
        } catch (\Exception $e) {
            log_message('error', 'Error generating ControlNumber: ' . $e->getMessage());
            $fallback = time() % 99999999999999;
            return $prefix . str_pad($fallback, $numberLength, '0', STR_PAD_LEFT);
        }
    }

    private function isControlNumberExists($controlNumber)
    {
        return $this->katalogModel->where('ControlNumber', $controlNumber)->countAllResults() > 0;
    }

    private function generateUniqueControlNumber($maxRetries = 5)
    {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $controlNumber = $this->generateControlNumber();

            if (!$this->isControlNumberExists($controlNumber)) {
                return $controlNumber;
            }

            $attempt++;
            usleep(100000);
        }

        $timestamp    = microtime(true);
        $uniqueNumber = substr(str_replace('.', '', $timestamp), -14);
        return 'INLIS' . str_pad($uniqueNumber, 14, '0', STR_PAD_LEFT);
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return date('Y-m-d H:i:s');
        }

        foreach (['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'] as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        return date('Y-m-d H:i:s');
    }

    private function parsePartnerId($namaSumber)
    {
        return 1;
    }

    private function parseLocationId($kodeLokasi)
    {
        $mapping = ['0101' => 466, '0102' => 467, '0103' => 468, '0104' => 469];
        return $mapping[$kodeLokasi] ?? 466;
    }

    private function parseRuleId($akses)
    {
        $mapping = ['Dapat dipinjam' => 1, 'Tidak dapat dipinjam' => 2, 'Referensi' => 3];
        return $mapping[$akses] ?? 1;
    }

    private function parseCategoryId($kategori)
    {
        $mapping = ['Koleksi Umum' => 7, 'Koleksi Referensi' => 8, 'Koleksi Langka' => 9];
        return $mapping[$kategori] ?? 7;
    }

    private function parseMediaId($media)
    {
        $mapping = ['Buku' => 2, 'CD/DVD' => 3, 'Majalah' => 4, 'Jurnal' => 5, 'E-Book' => 6];
        return $mapping[$media] ?? 2;
    }

    private function parseSourceId($jenisSumber)
    {
        $mapping = ['Pembelian' => 1, 'Hadiah/Hibah' => 2, 'Tukar Menukar' => 3, 'Deposit' => 4];
        return $mapping[$jenisSumber] ?? 1;
    }

    private function parseStatusId($ketersediaan)
    {
        $mapping = ['Tersedia' => 1, 'Dipinjam' => 2, 'Hilang' => 3, 'Rusak' => 4, 'Dalam Perbaikan' => 5];
        return $mapping[$ketersediaan] ?? 1;
    }

    private function parseLocationLibraryId($kodeLokasiPerpustakaan)
    {
        $mapping = ['Pusat' => 1, 'Cabang1' => 2, 'Cabang2' => 3];
        return $mapping[$kodeLokasiPerpustakaan] ?? 1;
    }
}
