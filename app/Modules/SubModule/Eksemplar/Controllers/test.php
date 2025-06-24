<?php

namespace Katalog\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class KatalogImport extends \Base\Controllers\BaseController
{
    use ResponseTrait;
    
    protected $catalogModel;
    protected $catalogRuasModel;
    protected $collectionsModel;
    protected $db;
    
    public function __construct()
    {
        $this->catalogModel = new \Katalog\Models\CatalogModel();
        $this->catalogRuasModel = new \Katalog\Models\CatalogRuasModel();
        $this->collectionsModel = new \Katalog\Models\CollectionsModel();
        $this->db = \Config\Database::connect();
    }
    
    public function index()
    {
        $this->data['title'] = 'Import Katalog Excel';
        return view('Katalog\Views\import', $this->data);
    }
    
    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('katalog/import'));
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'excel_file' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,10240]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $validation->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        $file = $this->request->getFile('excel_file');
        
        if (!$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File tidak valid'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        try {
            // Pastikan direktori upload ada
            if (!is_dir(WRITEPATH . 'uploads/temp')) {
                mkdir(WRITEPATH . 'uploads/temp', 0755, true);
            }
            
            // Move file to temp directory
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $fileName);
            $filePath = WRITEPATH . 'uploads/temp/' . $fileName;
            
            // Load spreadsheet menggunakan PhpSpreadsheet
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath);
            } catch (\Exception $e) {
                // Coba dengan reader XLS jika XLSX gagal
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($filePath);
                } catch (\Exception $e2) {
                    throw new \Exception('Gagal membaca file Excel: ' . $e->getMessage());
                }
            }
            
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            $header = array_shift($rows);
            
            // Process import
            $result = $this->processImport($rows, $header);
            
            // Delete temp file
            unlink($filePath);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Import berhasil',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            // Delete temp file if exists
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error saat import: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function detail($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->catalogModel->find($id);
            if ($data) {
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }
        }
        
        return redirect()->to(base_url('katalog/import'));
    }
    
    private function processImport($rows, $header)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        $this->db->transBegin();
        
        try {
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows
                
                $rowNumber = $rowIndex + 2; // +2 karena array dimulai dari 0 dan ada header
                
                try {
                    // Parse data dari format baru
                    $catalogData = $this->parseNewCatalogData($row, $header);
                    $collectionData = $this->parseNewCollectionData($row, $header);
                    $marcFields = $this->generateBasicMarcFields($catalogData); // Generate basic MARC fields
                    
                    // Debug: Log data yang akan diinsert
                    log_message('debug', "Row {$rowNumber} - Catalog Data: " . json_encode($catalogData));
                    log_message('debug', "Row {$rowNumber} - Collection Data: " . json_encode($collectionData));
                    log_message('debug', "Row {$rowNumber} - MARC Fields: " . json_encode($marcFields));
                    
                    // Cek apakah catalog sudah ada berdasarkan kombinasi judul + pengarang
                    $existingCatalog = $this->findExistingCatalog($catalogData);
                    
                    if ($existingCatalog) {
                        $catalogId = $existingCatalog->ID;
                        log_message('debug', "Row {$rowNumber} - Using existing catalog ID: {$catalogId}");
                    } else {
                        // Insert catalog baru
                        $catalogId = $this->insertCatalog($catalogData);
                        log_message('debug', "Row {$rowNumber} - Created new catalog ID: {$catalogId}");
                        
                        // Insert MARC fields untuk catalog baru
                        if (!empty($marcFields)) {
                            $this->insertMarcFields($catalogId, $marcFields);
                            log_message('debug', "Row {$rowNumber} - Inserted " . count($marcFields) . " MARC fields");
                        }
                    }
                    
                    // Insert collection (selalu insert collection baru)
                    if (!empty($collectionData)) {
                        $collectionData['Catalog_id'] = $catalogId;
                        $collectionId = $this->insertCollection($collectionData);
                        log_message('debug', "Row {$rowNumber} - Created collection ID: {$collectionId}");
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    log_message('error', "Row {$rowNumber} error: " . $e->getMessage());
                }
            }
            
            if ($errorCount > 0 && $successCount == 0) {
                $this->db->transRollback();
                throw new \Exception("Semua data gagal diimport. Errors: " . implode('; ', array_slice($errors, 0, 5)));
            }
            
            $this->db->transCommit();
            
            return [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => array_slice($errors, 0, 10)
            ];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
    
    private function parseNewCatalogData($row, $header)
    {
        $headerMap = array_flip($header);
        
        // Generate ControlNumber unik
        $controlNumber = $this->generateControlNumber();
        
        // Parse judul lengkap
        $judulUtama = $this->getValue($row, $headerMap, 'JUDUL_UTAMA');
        $anakJudul = $this->getValue($row, $headerMap, 'ANAK_JUDUL');
        $title = $judulUtama . ($anakJudul ? ' : ' . $anakJudul : '');
        
        // Parse pengarang
        $author = $this->getValue($row, $headerMap, 'TAJUK_PENGARANG');
        
        // Parse penerbit info
        $publisher = $this->getValue($row, $headerMap, 'PENERBIT');
        $publishLocation = $this->getValue($row, $headerMap, 'KOTA_TERBIT');
        $publishYear = $this->getValue($row, $headerMap, 'TAHUN_TERBIT');
        
        // Parse physical description
        $jumlahHalaman = $this->getValue($row, $headerMap, 'JUMLAH_HALAMAN');
        $dimensi = $this->getValue($row, $headerMap, 'DIMENSI');
        $physicalDescription = $jumlahHalaman . ($dimensi ? ' ; ' . $dimensi : '');
        
        $data = [
            'ControlNumber' => $controlNumber,
            'BIBID' => $controlNumber, // Use same as control number
            'Title' => $title,
            'Author' => $author,
            'Edition' => $this->getValue($row, $headerMap, 'EDISI'),
            'Publisher' => $publisher,
            'PublishLocation' => $publishLocation,
            'PublishYear' => $publishYear,
            'Subject' => $this->getValue($row, $headerMap, 'SUBJEK_TOPIK'),
            'PhysicalDescription' => $physicalDescription,
            'ISBN' => $this->getValue($row, $headerMap, 'ISBN'),
            'CallNumber' => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_KATALOG'),
            'Note' => $this->getValue($row, $headerMap, 'ABSTRAK'),
            'Languages' => $this->getValue($row, $headerMap, 'BAHASA'),
            'DeweyNo' => $this->getValue($row, $headerMap, 'NO_DDC'),
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id' => user()->branch_id ?? 1,
            'Location_id' => 1,
            'IsOPAC' => 1,
            'IsBNI' => 1,
            'IsKIN' => 1,
            'IsRDA' => 1,
            'active' => 1
        ];
        
        // Validasi required fields
        if (empty($data['Title'])) {
            throw new \Exception('Judul utama tidak boleh kosong');
        }
        
        return $data;
    }
    
    private function parseNewCollectionData($row, $header)
    {
        $headerMap = array_flip($header);
        
        // Parse tanggal pengadaan
        $tglPengadaan = $this->getValue($row, $headerMap, 'TGL_PENGADAAN');
        $tanggalPengadaan = $this->parseDate($tglPengadaan);
        
        $data = [
            'NomorBarcode' => $this->getValue($row, $headerMap, 'NO_BARCODE'),
            'NoInduk' => $this->getValue($row, $headerMap, 'NO_INDUK'),
            'RFID' => $this->getValue($row, $headerMap, 'NO_RFID'),
            'Currency' => $this->getValue($row, $headerMap, 'MATA_UANG', 'IDR'),
            'Price' => (int)$this->getValue($row, $headerMap, 'HARGA', 0),
            'PriceType' => 'Per eksemplar',
            'TanggalPengadaan' => $tanggalPengadaan,
            'CallNumber' => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_EKSEMPLAR'),
            'Branch_id' => user()->branch_id ?? 1,
            'Partner_id' => $this->parsePartnerId($this->getValue($row, $headerMap, 'NAMA_SUMBER')),
            'Location_id' => $this->parseLocationId($this->getValue($row, $headerMap, 'KODE_LOKASI_RUANG')),
            'Rule_id' => $this->parseRuleId($this->getValue($row, $headerMap, 'AKSES')),
            'Category_id' => $this->parseCategoryId($this->getValue($row, $headerMap, 'KATEGORI')),
            'Media_id' => $this->parseMediaId($this->getValue($row, $headerMap, 'MEDIA')),
            'Source_id' => $this->parseSourceId($this->getValue($row, $headerMap, 'JENIS_SUMBER')),
            'Status_id' => $this->parseStatusId($this->getValue($row, $headerMap, 'KETERSEDIAAN')),
            'Location_Library_id' => $this->parseLocationLibraryId($this->getValue($row, $headerMap, 'KODE_LOKASI_PERPUSTAKAAN')),
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress()
        ];
        
        // Validasi required fields
        if (empty($data['NomorBarcode'])) {
            throw new \Exception('Nomor Barcode tidak boleh kosong');
        }
        
        if (empty($data['NoInduk'])) {
            throw new \Exception('No Induk tidak boleh kosong');
        }
        
        return $data;
    }
    
    private function parseNewCatalogData($row, $header)
    {
        $headerMap = array_flip($header);
        
        // Generate ControlNumber unik dengan format INLIS000000000004123
        $controlNumber = $this->generateUniqueControlNumber();
        
        // Parse judul lengkap
        $judulUtama = $this->getValue($row, $headerMap, 'JUDUL_UTAMA');
        $anakJudul = $this->getValue($row, $headerMap, 'ANAK_JUDUL');
        $title = $judulUtama . ($anakJudul ? ' : ' . $anakJudul : '');
        
        // Parse pengarang
        $author = $this->getValue($row, $headerMap, 'TAJUK_PENGARANG');
        
        // Parse penerbit info
        $publisher = $this->getValue($row, $headerMap, 'PENERBIT');
        $publishLocation = $this->getValue($row, $headerMap, 'KOTA_TERBIT');
        $publishYear = $this->getValue($row, $headerMap, 'TAHUN_TERBIT');
        
        // Parse physical description
        $jumlahHalaman = $this->getValue($row, $headerMap, 'JUMLAH_HALAMAN');
        $dimensi = $this->getValue($row, $headerMap, 'DIMENSI');
        $physicalDescription = $jumlahHalaman . ($dimensi ? ' ; ' . $dimensi : '');
        
        $data = [
            'ControlNumber' => $controlNumber,
            'BIBID' => $this->generateBibId($controlNumber), // Generate BIBID based on ControlNumber
            'Title' => $title,
            'Author' => $author,
            'Edition' => $this->getValue($row, $headerMap, 'EDISI'),
            'Publisher' => $publisher,
            'PublishLocation' => $publishLocation,
            'PublishYear' => $publishYear,
            'Subject' => $this->getValue($row, $headerMap, 'SUBJEK_TOPIK'),
            'PhysicalDescription' => $physicalDescription,
            'ISBN' => $this->getValue($row, $headerMap, 'ISBN'),
            'CallNumber' => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_KATALOG'),
            'Note' => $this->getValue($row, $headerMap, 'ABSTRAK'),
            'Languages' => $this->getValue($row, $headerMap, 'BAHASA'),
            'DeweyNo' => $this->getValue($row, $headerMap, 'NO_DDC'),
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id' => user()->branch_id ?? 1,
            'Location_id' => 1,
            'IsOPAC' => 1,
            'IsBNI' => 1,
            'IsKIN' => 1,
            'IsRDA' => 1,
            'active' => 1
        ];
        
        // Validasi required fields
        if (empty($data['Title'])) {
            throw new \Exception('Judul utama tidak boleh kosong');
        }
        
        // Log untuk debugging
        log_message('debug', 'Generated ControlNumber: ' . $controlNumber . ' for title: ' . $title);
        
        return $data;
    }
    
    private function parseNewCollectionData($row, $header)
    {
        $headerMap = array_flip($header);
        
        // Parse tanggal pengadaan
        $tglPengadaan = $this->getValue($row, $headerMap, 'TGL_PENGADAAN');
        $tanggalPengadaan = $this->parseDate($tglPengadaan);
        
        $data = [
            'NomorBarcode' => $this->getValue($row, $headerMap, 'NO_BARCODE'),
            'NoInduk' => $this->getValue($row, $headerMap, 'NO_INDUK'),
            'RFID' => $this->getValue($row, $headerMap, 'NO_RFID'),
            'Currency' => $this->getValue($row, $headerMap, 'MATA_UANG', 'IDR'),
            'Price' => (int)$this->getValue($row, $headerMap, 'HARGA', 0),
            'PriceType' => 'Per eksemplar',
            'TanggalPengadaan' => $tanggalPengadaan,
            'CallNumber' => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_EKSEMPLAR'),
            'Branch_id' => user()->branch_id ?? 1,
            'Partner_id' => $this->parsePartnerId($this->getValue($row, $headerMap, 'NAMA_SUMBER')),
            'Location_id' => $this->parseLocationId($this->getValue($row, $headerMap, 'KODE_LOKASI_RUANG')),
            'Rule_id' => $this->parseRuleId($this->getValue($row, $headerMap, 'AKSES')),
            'Category_id' => $this->parseCategoryId($this->getValue($row, $headerMap, 'KATEGORI')),
            'Media_id' => $this->parseMediaId($this->getValue($row, $headerMap, 'MEDIA')),
            'Source_id' => $this->parseSourceId($this->getValue($row, $headerMap, 'JENIS_SUMBER')),
            'Status_id' => $this->parseStatusId($this->getValue($row, $headerMap, 'KETERSEDIAAN')),
            'Location_Library_id' => $this->parseLocationLibraryId($this->getValue($row, $headerMap, 'KODE_LOKASI_PERPUSTAKAAN')),
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress()
        ];
        
        // Validasi required fields
        if (empty($data['NomorBarcode'])) {
            throw new \Exception('Nomor Barcode tidak boleh kosong');
        }
        
        if (empty($data['NoInduk'])) {
            throw new \Exception('No Induk tidak boleh kosong');
        }
        
        return $data;
    }
    
    private function generateControlNumber()
    {
        // Format: INLIS000000000004123
        $prefix = 'INLIS';
        $totalLength = 19; // Total length termasuk prefix
        $numberLength = $totalLength - strlen($prefix); // 14 digits untuk angka
        
        try {
            // Ambil ControlNumber tertinggi yang ada
            $lastRecord = $this->catalogModel
                ->select('ControlNumber')
                ->where('ControlNumber LIKE', $prefix . '%')
                ->orderBy('ControlNumber', 'DESC')
                ->first();
            
            if ($lastRecord && !empty($lastRecord->ControlNumber)) {
                // Extract angka dari ControlNumber terakhir
                $lastNumber = substr($lastRecord->ControlNumber, strlen($prefix));
                
                // Convert ke integer dan tambah 1
                $nextNumber = (int)$lastNumber + 1;
            } else {
                // Jika belum ada data, mulai dari 1
                $nextNumber = 1;
            }
            
            // Format dengan padding zero
            $formattedNumber = str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);
            
            // Gabungkan prefix dengan number
            $controlNumber = $prefix . $formattedNumber;
            
            // Validasi panjang hasil
            if (strlen($controlNumber) !== $totalLength) {
                throw new \Exception("Generated ControlNumber length mismatch: " . strlen($controlNumber));
            }
            
            return $controlNumber;
            
        } catch (\Exception $e) {
            // Fallback jika ada error
            log_message('error', 'Error generating ControlNumber: ' . $e->getMessage());
            
            // Generate dengan timestamp sebagai fallback
            $fallbackNumber = time() % 99999999999999; // 14 digits max
            return $prefix . str_pad($fallbackNumber, $numberLength, '0', STR_PAD_LEFT);
        }
    }
    
    private function generateUniqueControlNumber($maxRetries = 5)
    {
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            $controlNumber = $this->generateControlNumber();
            
            // Cek apakah sudah digunakan
            if (!$this->isControlNumberExists($controlNumber)) {
                return $controlNumber;
            }
            
            $attempt++;
            
            // Jika sudah digunakan, tunggu sebentar untuk menghindari collision
            usleep(100000); // 0.1 second
        }
        
        // Jika masih gagal setelah retry, gunakan timestamp
        $timestamp = microtime(true);
        $uniqueNumber = str_replace('.', '', $timestamp);
        $uniqueNumber = substr($uniqueNumber, -14); // Ambil 14 digit terakhir
        
        return 'INLIS' . str_pad($uniqueNumber, 14, '0', STR_PAD_LEFT);
    }
    
    private function validateControlNumberFormat($controlNumber)
    {
        $pattern = '/^INLIS\d{14}$/'; // INLIS + 14 digits
        return preg_match($pattern, $controlNumber);
    }
    
    private function isControlNumberExists($controlNumber)
    {
        $existing = $this->catalogModel
            ->where('ControlNumber', $controlNumber)
            ->countAllResults();
            
        return $existing > 0;
    }
    
    private function generateBibId($controlNumber)
    {
        // Extract number part dari ControlNumber
        $numberPart = substr($controlNumber, 5); // Remove 'INLIS' prefix
        
        // Format BIBID: 0010-092 + last 7 digits
        $lastSevenDigits = substr($numberPart, -7);
        $bibId = '0010-092' . $lastSevenDigits;
        
        return $bibId;
    }
    
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return date('Y-m-d H:i:s');
        }
        
        // Try various date formats
        $formats = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return date('Y-m-d H:i:s');
    }
    
    private function generateBasicMarcFields($catalogData)
    {
        $marcFields = [];
        $sequence = 1;
        
        // 001 - Control Number
        if (!empty($catalogData['ControlNumber'])) {
            $marcFields[] = [
                'Tag' => '001',
                'Indicator1' => '#',
                'Indicator2' => '#',
                'Value' => $catalogData['ControlNumber'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 005 - Date and Time of Latest Transaction
        $marcFields[] = [
            'Tag' => '005',
            'Indicator1' => '#',
            'Indicator2' => '#',
            'Value' => date('YmdHis'),
            'Sequence' => $sequence++,
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id' => user()->branch_id ?? 1,
            'active' => 1
        ];
        
        // 020 - ISBN
        if (!empty($catalogData['ISBN'])) {
            $marcFields[] = [
                'Tag' => '020',
                'Indicator1' => '#',
                'Indicator2' => '#',
                'Value' => '$a ' . $catalogData['ISBN'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 082 - Dewey Decimal Classification
        if (!empty($catalogData['DeweyNo'])) {
            $marcFields[] = [
                'Tag' => '082',
                'Indicator1' => '#',
                'Indicator2' => '#',
                'Value' => '$a ' . $catalogData['DeweyNo'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 100 - Main Entry Personal Name
        if (!empty($catalogData['Author'])) {
            $marcFields[] = [
                'Tag' => '100',
                'Indicator1' => '1',
                'Indicator2' => '#',
                'Value' => '$a ' . $catalogData['Author'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 245 - Title Statement
        if (!empty($catalogData['Title'])) {
            $titleValue = '$a ' . $catalogData['Title'];
            if (!empty($catalogData['Author'])) {
                $titleValue .= ' /$c ' . $catalogData['Author'];
            }
            
            $marcFields[] = [
                'Tag' => '245',
                'Indicator1' => '1',
                'Indicator2' => '0',
                'Value' => $titleValue,
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 250 - Edition Statement
        if (!empty($catalogData['Edition'])) {
            $marcFields[] = [
                'Tag' => '250',
                'Indicator1' => '#',
                'Indicator2' => '#',
                'Value' => '$a ' . $catalogData['Edition'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 260 - Publication, Distribution, etc.
        if (!empty($catalogData['PublishLocation']) || !empty($catalogData['Publisher']) || !empty($catalogData['PublishYear'])) {
            $pubValue = '';
            if (!empty($catalogData['PublishLocation'])) {
                $pubValue .= '$a ' . $catalogData['PublishLocation'] . ' :';
            }
            if (!empty($catalogData['Publisher'])) {
                $pubValue .= '$b ' . $catalogData['Publisher'] . ',';
            }
            if (!empty($catalogData['PublishYear'])) {
                $pubValue .= '$c ' . $catalogData['PublishYear'];
            }
            
            if (!empty($pubValue)) {
                $marcFields[] = [
                    'Tag' => '260',
                    'Indicator1' => '#',
                    'Indicator2' => '#',
                    'Value' => $pubValue,
                    'Sequence' => $sequence++,
                    'CreateBy' => user()->id ?? 1,
                    'CreateDate' => date('Y-m-d H:i:s'),
                    'CreateTerminal' => $this->request->getIPAddress(),
                    'Branch_id' => user()->branch_id ?? 1,
                    'active' => 1
                ];
            }
        }
        
        // 300 - Physical Description
        if (!empty($catalogData['PhysicalDescription'])) {
            $marcFields[] = [
                'Tag' => '300',
                'Indicator1' => '#',
                'Indicator2' => '#',
                'Value' => '$a ' . $catalogData['PhysicalDescription'],
                'Sequence' => $sequence++,
                'CreateBy' => user()->id ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 1,
                'active' => 1
            ];
        }
        
        // 650 - Subject Added Entry
        if (!empty($catalogData['Subject'])) {
            $subjects = explode(';', $catalogData['Subject']);
            foreach ($subjects as $subject) {
                $subject = trim($subject);
                if (!empty($subject)) {
                    $marcFields[] = [
                        'Tag' => '650',
                        'Indicator1' => '#',
                        'Indicator2' => '#',
                        'Value' => '$a ' . $subject,
                        'Sequence' => $sequence++,
                        'CreateBy' => user()->id ?? 1,
                        'CreateDate' => date('Y-m-d H:i:s'),
                        'CreateTerminal' => $this->request->getIPAddress(),
                        'Branch_id' => user()->branch_id ?? 1,
                        'active' => 1
                    ];
                }
            }
        }
        
        return $marcFields;
    }
    
    private function insertMarcFields($catalogId, $marcFields)
    {
        foreach ($marcFields as $field) {
            $field['CatalogId'] = $catalogId;
            
            if (!$this->catalogRuasModel->insert($field)) {
                $errors = $this->catalogRuasModel->errors();
                throw new \Exception("Gagal insert MARC field: " . implode(', ', $errors));
            }
        }
    }
    
    private function generateControlNumber()
    {
        // Generate unique control number
        $prefix = 'INLIS';
        $lastControl = $this->catalogModel->selectMax('ControlNumber')->first();
        
        if ($lastControl && $lastControl->ControlNumber) {
            $lastNumber = (int) substr($lastControl->ControlNumber, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 15, '0', STR_PAD_LEFT);
    }
    
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return date('Y-m-d H:i:s');
        }
        
        // Try various date formats
        $formats = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return date('Y-m-d H:i:s');
    }
    
    private function findExistingCatalog($catalogData)
    {
        return $this->catalogModel
            ->where('Title', $catalogData['Title'])
            ->where('Author', $catalogData['Author'])
            ->first();
    }
    
    private function insertCatalog($data)
    {
        // Check if ControlNumber already exists
        $existing = $this->catalogModel->where('ControlNumber', $data['ControlNumber'])->first();
        if ($existing) {
            throw new \Exception("ControlNumber {$data['ControlNumber']} sudah ada");
        }
        
        if (!$this->catalogModel->insert($data)) {
            $errors = $this->catalogModel->errors();
            throw new \Exception("Gagal insert catalog: " . implode(', ', $errors));
        }
        
        return $this->catalogModel->getInsertID();
    }
    
    private function insertCollection($collectionData)
    {
        // Check if barcode already exists
        $existing = $this->collectionsModel->where('NomorBarcode', $collectionData['NomorBarcode'])->first();
        if ($existing) {
            throw new \Exception("Barcode {$collectionData['NomorBarcode']} sudah ada");
        }
        
        if (!$this->collectionsModel->insert($collectionData)) {
            $errors = $this->collectionsModel->errors();
            throw new \Exception("Gagal insert collection: " . implode(', ', $errors));
        }
        
        return $this->collectionsModel->getInsertID();
    }
    
    // Helper methods untuk mapping ID berdasarkan nama/kode
    private function parsePartnerId($namaSumber)
    {
        // Default partner atau buat logic untuk mapping nama sumber ke partner_id
        return 1;
    }
    
    private function parseLocationId($kodeLokasi)
    {
        // Mapping kode lokasi ruang ke location_id
        $mapping = [
            '0101' => 466,
            '0102' => 467,
            '0103' => 468,
            '0104' => 469
        ];
        
        return $mapping[$kodeLokasi] ?? 466;
    }
    
    private function parseRuleId($akses)
    {
        // Mapping akses ke rule_id
        $mapping = [
            'Dapat dipinjam' => 1,
            'Tidak dapat dipinjam' => 2,
            'Referensi' => 3
        ];
        
        return $mapping[$akses] ?? 1;
    }
    
    private function parseCategoryId($kategori)
    {
        // Mapping kategori ke category_id
        $mapping = [
            'Koleksi Umum' => 7,
            'Koleksi Referensi' => 8,
            'Koleksi Langka' => 9
        ];
        
        return $mapping[$kategori] ?? 7;
    }
    
    private function parseMediaId($media)
    {
        // Mapping media ke media_id
        $mapping = [
            'Buku' => 2,
            'CD/DVD' => 3,
            'Majalah' => 4,
            'Jurnal' => 5,
            'E-Book' => 6
        ];
        
        return $mapping[$media] ?? 2;
    }
    
    private function parseSourceId($jenisSumber)
    {
        // Mapping jenis sumber ke source_id
        $mapping = [
            'Pembelian' => 1,
            'Hadiah/Hibah' => 2,
            'Tukar Menukar' => 3,
            'Deposit' => 4
        ];
        
        return $mapping[$jenisSumber] ?? 1;
    }
    
    private function parseStatusId($ketersediaan)
    {
        // Mapping ketersediaan ke status_id
        $mapping = [
            'Tersedia' => 1,
            'Dipinjam' => 2,
            'Hilang' => 3,
            'Rusak' => 4,
            'Dalam Perbaikan' => 5
        ];
        
        return $mapping[$ketersediaan] ?? 1;
    }
    
    private function parseLocationLibraryId($kodeLokasiPerpustakaan)
    {
        // Mapping kode lokasi perpustakaan ke location_library_id
        $mapping = [
            'Pusat' => 1,
            'Cabang1' => 2,
            'Cabang2' => 3
        ];
        
        return $mapping[$kodeLokasiPerpustakaan] ?? 1;
    }
    
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers sesuai format yang diminta
        $headers = [
            'NO', 'TGL_PENGADAAN', 'NO_INDUK', 'NO_BARCODE', 'NO_RFID', 'JENIS_SUMBER', 'NAMA_SUMBER',
            'MATA_UANG', 'HARGA', 'KODE_LOKASI_PERPUSTAKAAN', 'KODE_LOKASI_RUANG', 'AKSES', 'KATEGORI',
            'MEDIA', 'KETERSEDIAAN', 'NOMOR_PANGGIL_EKSEMPLAR', 'JENIS_BAHAN', 'JUDUL_UTAMA', 'ANAK_JUDUL',
            'PERNYATAAN_TANGGUNGJAWAB', 'TAJUK_PENGARANG', 'TAJUK_PENGARANG_BADAN_KOOPERASI',
            'PENGARANG_TAMBAHAN_NAMA_ORANG', 'PENGARANG_TAMBAHAN_NAMA_BADAN', 'EDISI', 'KOTA_TERBIT',
            'PENERBIT', 'TAHUN_TERBIT', 'JUMLAH_HALAMAN', 'DIMENSI', 'ISBN', 'ISSN', 'ISMN', 'NO_DDC',
            'NOMOR_PANGGIL_KATALOG', 'ABSTRAK', 'BAHASA', 'SUBJEK_TOPIK', 'EDISI_SERIAL', 'TGL_TERBIT_EDISI_SERIAL',
            'BAHAN_SERTAAN_SERIAL', 'KETERANGAN_LAIN_SERIAL'
        ];
        
        $sheet->fromArray([$headers], null, 'A1');
        
        // Style header
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);
        
        // Add sample data (5 rows)
        $sampleData = [
            [
                1, '14-02-2015', 'X0022/2016', 'X0022/2016', 'X0022/2016', 'Hadiah/Hibah', '---Belum ditentukan---',
                'IDR', 0, 'Pusat', '0101', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '123 PRA m',
                'Monograf', 'Mahligai Biru', '', 'Mamik Pradana', 'Pradana, Mamik', '', '', '', '', 'Jakarta',
                'Grafika', '2015', '120 hlm.', '25 cm.', '978-222-666-444', '', '', '123', '123 PRA m', '',
                'ind', 'Rumah Tangga', '', '', '', ''
            ],
            [
                2, '15-02-2015', 'X0023/2016', 'X0023/2016', 'X0023/2016', 'Pembelian', '---Belum ditentukan---',
                'IDR', 0, 'Pusat', '0101', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '201 SAM k',
                'Monograf', 'Kancil dan Kerbau', '', 'Deni Saman', 'Saman, Deni', '', '', '', '', 'Jakarta',
                'Prabu', '2015', '68 hlm.', '21 cm.', '856-225-456-78', '', '', '201', '201 SAM k', '',
                'ind', 'Fiksi', '', '', '', ''
            ],
            [
                3, '16-03-2015', 'X0024/2016', 'X0024/2016', 'X0024/2016', 'Pembelian', 'Toko Buku Mandiri',
                'IDR', 75000, 'Pusat', '0102', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '004.678 BUD p',
                'Monograf', 'Pemrograman Web dengan PHP', 'Panduan Lengkap untuk Pemula', 'Budi Raharjo', 'Raharjo, Budi', '', '', '', 'Edisi 2', 'Bandung',
                'Informatika', '2015', '350 hlm.', '24 cm.', '978-602-1234-567-8', '', '', '004.678', '004.678 BUD p', 'Buku panduan pemrograman web menggunakan PHP',
                'ind', 'Teknologi Informasi; Pemrograman', '', '', '', ''
            ],
            [
                4, '20-03-2015', 'X0025/2016', 'X0025/2016', 'X0025/2016', 'Hadiah/Hibah', 'Dinas Pendidikan',
                'IDR', 0, 'Pusat', '0103', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '899.221 DEW s',
                'Monograf', 'Sastra Indonesia Kontemporer', 'Analisis dan Apresiasi', 'Dewi Lestari', 'Lestari, Dewi', '', 'Pusat Bahasa', '', 'Edisi 3', 'Jakarta',
                'Gramedia Pustaka Utama', '2015', '320 hlm.', '20 cm.', '978-602-0307-456-7', '', '', '899.221', '899.221 DEW s', 'Kumpulan analisis sastra Indonesia modern',
                'ind', 'Sastra Indonesia; Literatur', '', '', '', ''
            ],
            [
                5, '25-03-2015', 'X0026/2016', 'X0026/2016', 'X0026/2016', 'Pembelian', 'CV. Pustaka Ilmu',
                'IDR', 85000, 'Pusat', '0104', 'Dapat dipinjam', 'Koleksi Referensi', 'Buku', 'Tersedia', '904.598 BAM s',
                'Monograf', 'Sejarah Perkembangan Teknologi Digital di Indonesia', '', 'Prof. Dr. Bambang Sutrisno; Dr. Maya Sari', 'Sutrisno, Bambang', '', '', 'Sari, Maya', 'Edisi 1', 'Jakarta',
                'Erlangga', '2015', '500 hlm.', '24 cm.', '978-602-2989-345-6', '', '', '904.598', '904.598 BAM s', 'Dokumentasi lengkap perkembangan teknologi digital di Indonesia',
                'ind', 'Sejarah; Teknologi; Indonesia', '', '', '', ''
            ]
        ];
        
        $sheet->fromArray($sampleData, null, 'A2');
        
        // Auto-size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set border untuk semua data
        $dataRange = 'A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . (count($sampleData) + 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);
        
        // Set response headers
        $filename = 'template_import_katalog_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}