<?php

namespace Eksemplar\Controllers;

use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

/**
 * EksemplarExportController
 *
 * Export eksemplar ke Excel dengan format yang sama persis dengan template import
 * (EksemplarImportController::downloadTemplate), sehingga hasilnya bisa diimport ulang.
 *
 * Proses dibagi per batch agar tidak timeout untuk data besar:
 *  1. start   → hitung jumlah data & buat job
 *  2. process → dipanggil berulang, tiap panggilan menulis BATCH_SIZE baris ke file sementara
 *  3. finish  → rakit file .xlsx (sheet ditulis streaming, tanpa memuat semua baris ke memori)
 *  4. download
 */
class EksemplarExportController extends \Base\Controllers\BaseController
{
    use ResponseTrait;
    use EksemplarBase;

    private const BATCH_SIZE = 500;

    // Job yang lebih tua dari ini dibersihkan saat export baru dimulai
    private const JOB_LIFETIME = 86400;

    // Panjang teks maksimum untuk perhitungan lebar kolom otomatis
    private const MAX_WIDTH_CHARS = 100;

    // Sama dengan header di EksemplarImportController::downloadTemplate()
    public const HEADERS = [
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

    // Dasar tanggal filter: nilai form => kolom collections
    public const DATE_FIELDS = [
        'pengadaan' => ['TanggalPengadaan', 'Tanggal Pengadaan'],
        'entri'     => ['CreateDate', 'Tanggal Entri Data'],
    ];

    // Tag MARC yang dibaca dari catalog_ruas
    private const MARC_TAGS = ['008', '022', '024', '100', '110', '245', '250', '260', '264', '300', '520', '650', '700', '710'];

    function __construct()
    {
        $this->initEksemplarBase();
    }

    // ----------------------------------------------------------------
    // VIEWS
    // ----------------------------------------------------------------

    public function index()
    {
        $this->data['title']       = 'Export Eksemplar Excel';
        $this->data['date_fields'] = self::DATE_FIELDS;
        $this->data['batch_size']  = self::BATCH_SIZE;
        return view('Eksemplar\Views\export', $this->data);
    }

    // ----------------------------------------------------------------
    // BATCH PROCESS
    // ----------------------------------------------------------------

    public function start()
    {
        if ($denied = $this->denyUnlessAllowed()) {
            return $denied;
        }

        [$from, $to, $label, $error] = $this->resolvePeriod();
        if ($error) {
            return $this->respondJson(['status' => 'error', 'message' => $error], 422);
        }

        $dateKey = $this->request->getPost('dasar_tanggal');
        if (!isset(self::DATE_FIELDS[$dateKey])) {
            $dateKey = 'pengadaan';
        }

        $this->cleanupOldJobs();

        $state = [
            'user_id'  => login_id(),
            'date_key' => $dateKey,
            'from'     => $from,
            'to'       => $to,
            'label'    => $label,
            'total'    => $this->baseQuery($dateKey, $from, $to)->countAllResults(),
            'done'     => 0,
            'last_id'  => 0,
            // Nilai data terpanjang per kolom, dasar lebar kolom otomatis
            'longest'  => array_fill(0, count(self::HEADERS), ''),
            // Shared string 0..n-1 dipakai header, data mulai setelahnya
            'sst_count' => count(self::HEADERS),
            'finished' => false,
            'created'  => time(),
        ];

        if ($state['total'] === 0) {
            return $this->respondJson([
                'status'  => 'empty',
                'message' => 'Tidak ada data eksemplar pada periode ' . $label . '.',
            ]);
        }

        $jobId = bin2hex(random_bytes(16));
        $dir   = $this->jobDir($jobId);
        mkdir($dir, 0755, true);
        file_put_contents($dir . 'rows.xml', '');
        file_put_contents($dir . 'strings.xml', '');
        $this->saveState($jobId, $state);

        return $this->respondJson([
            'status' => 'success',
            'job'    => $jobId,
            'total'  => $state['total'],
            'label'  => $label,
        ]);
    }

    public function process()
    {
        if ($denied = $this->denyUnlessAllowed()) {
            return $denied;
        }

        $jobId = (string) $this->request->getPost('job');
        $state = $this->loadState($jobId);
        if (!$state) {
            return $this->respondJson(['status' => 'error', 'message' => 'Proses export tidak ditemukan atau sudah kedaluwarsa.'], 404);
        }

        if ($state['done'] < $state['total']) {
            @set_time_limit(300);

            $rows = $this->baseQuery($state['date_key'], $state['from'], $state['to'])
                ->select('c.ID, c.TanggalPengadaan, c.NoInduk, c.NomorBarcode, c.RFID, c.Currency, c.Price, c.CallNumber,
                    c.EDISISERIAL, c.TANGGAL_TERBIT_EDISI_SERIAL, c.BAHAN_SERTAAN, c.KETERANGAN_LAIN, c.Catalog_id,
                    src.Name AS JenisSumber, p.Name AS NamaSumber, ll.Code AS KodeLokasiPerpustakaan,
                    l.Code AS KodeLokasiRuang, r.Name AS Akses, cat.Name AS Kategori, m.Name AS Media,
                    st.Name AS Ketersediaan, w.Name AS JenisBahan,
                    k.Title, k.Author, k.Edition, k.Publisher, k.PublishLocation, k.PublishYear, k.Subject,
                    k.ISBN, k.CallNumber AS CallNumberKatalog, k.Note, k.Languages, k.DeweyNo')
                ->join('collectionsources src', 'src.ID = c.Source_id', 'left')
                ->join('partners p', 'p.ID = c.Partner_id', 'left')
                ->join('location_library ll', 'll.ID = c.Location_Library_id', 'left')
                ->join('locations l', 'l.ID = c.Location_id', 'left')
                ->join('collectionrules r', 'r.ID = c.Rule_id', 'left')
                ->join('collectioncategorys cat', 'cat.ID = c.Category_id', 'left')
                ->join('collectionmedias m', 'm.ID = c.Media_id', 'left')
                ->join('collectionstatus st', 'st.ID = c.Status_id', 'left')
                ->join('catalogs k', 'k.ID = c.Catalog_id', 'left')
                ->join('worksheets w', 'w.ID = k.Worksheet_id', 'left')
                ->where('c.ID >', $state['last_id'])
                ->orderBy('c.ID', 'ASC')
                ->limit(self::BATCH_SIZE)
                ->get()->getResultArray();

            $marc = $this->loadMarc(array_unique(array_filter(array_column($rows, 'Catalog_id'))));

            $xml     = '';
            $sst     = '';
            $strings = [];
            foreach ($rows as $row) {
                $state['done']++;
                $values = $this->buildRow($state['done'], $row, $marc[$row['Catalog_id']] ?? []);
                foreach ($values as $i => $value) {
                    $value = (string) $value;
                    if (mb_strlen($value) > mb_strlen($state['longest'][$i])) {
                        // Batas lebar kolom Excel 255 karakter
                        $state['longest'][$i] = mb_substr($value, 0, self::MAX_WIDTH_CHARS);
                    }
                }
                $xml .= $this->rowXml($state['done'] + 1, $values, $strings, $sst, $state['sst_count']);
                $state['last_id'] = (int) $row['ID'];
            }
            file_put_contents($this->jobDir($jobId) . 'rows.xml', $xml, FILE_APPEND | LOCK_EX);
            file_put_contents($this->jobDir($jobId) . 'strings.xml', $sst, FILE_APPEND | LOCK_EX);

            // Data berkurang selama proses (dihapus/dikarantina): anggap selesai
            if (count($rows) < self::BATCH_SIZE) {
                $state['total'] = $state['done'];
            }
            $this->saveState($jobId, $state);
        }

        return $this->respondJson([
            'status' => 'success',
            'done'   => $state['done'],
            'total'  => $state['total'],
            'finish' => $state['done'] >= $state['total'],
        ]);
    }

    public function finish()
    {
        if ($denied = $this->denyUnlessAllowed()) {
            return $denied;
        }

        $jobId = (string) $this->request->getPost('job');
        $state = $this->loadState($jobId);
        if (!$state || $state['done'] < $state['total']) {
            return $this->respondJson(['status' => 'error', 'message' => 'Proses export belum selesai atau tidak ditemukan.'], 404);
        }

        @set_time_limit(600);

        try {
            $this->buildXlsx($jobId, $state);
        } catch (\Throwable $e) {
            log_message('error', 'Export eksemplar gagal: ' . $e->getMessage());
            return $this->respondJson(['status' => 'error', 'message' => 'Gagal membuat file Excel: ' . $e->getMessage()], 500);
        }

        $state['finished'] = true;
        $this->saveState($jobId, $state);

        return $this->respondJson([
            'status' => 'success',
            'url'    => base_url('export-eksemplar/download?job=' . $jobId),
        ]);
    }

    public function download()
    {
        $jobId = (string) $this->request->getGet('job');
        $state = $this->loadState($jobId);
        $file  = $this->jobDir($jobId) . 'export.xlsx';

        if (!$state || empty($state['finished']) || !is_file($file)) {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'File export tidak ditemukan atau sudah kedaluwarsa. Silakan export ulang.');
            return redirect()->to('/export-eksemplar');
        }

        $filename = 'template_import_katalog_' . $state['label'] . '.xlsx';

        return $this->response->download($file, null)->setFileName($filename);
    }

    // ----------------------------------------------------------------
    // QUERY
    // ----------------------------------------------------------------

    /** Eksemplar non-karantina dengan tanggal di [$from, $to) */
    private function baseQuery(string $dateKey, string $from, string $to)
    {
        $column = 'c.' . self::DATE_FIELDS[$dateKey][0];

        return $this->db->table('collections c')
            ->where('c.IsQUARANTINE', 0)
            ->where($column . ' >=', $from)
            ->where($column . ' <', $to);
    }

    /** @return array{0:string,1:string,2:string,3:?string} [from, to (eksklusif), label nama file, pesan error] */
    private function resolvePeriod(): array
    {
        $mode = $this->request->getPost('mode');

        if ($mode === 'harian') {
            $day = $this->parseInputDate($this->request->getPost('tanggal'));
            if (!$day) {
                return ['', '', '', 'Tanggal wajib diisi.'];
            }
            return [$day->format('Y-m-d'), $day->modify('+1 day')->format('Y-m-d'), $day->format('Y-m-d'), null];
        }

        if ($mode === 'bulanan') {
            $month = \DateTimeImmutable::createFromFormat('!Y-m', (string) $this->request->getPost('bulan'));
            if (!$month) {
                return ['', '', '', 'Bulan wajib diisi.'];
            }
            return [$month->format('Y-m-d'), $month->modify('+1 month')->format('Y-m-d'), $month->format('Y-m'), null];
        }

        $start = $this->parseInputDate($this->request->getPost('tanggal_awal'));
        $end   = $this->parseInputDate($this->request->getPost('tanggal_akhir'));
        if (!$start || !$end) {
            return ['', '', '', 'Tanggal awal dan tanggal akhir wajib diisi.'];
        }
        if ($start > $end) {
            return ['', '', '', 'Tanggal awal tidak boleh melebihi tanggal akhir.'];
        }

        return [
            $start->format('Y-m-d'),
            $end->modify('+1 day')->format('Y-m-d'),
            $start->format('Y-m-d') . '_sd_' . $end->format('Y-m-d'),
            null,
        ];
    }

    private function parseInputDate($value): ?\DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);
        return $date ?: null;
    }

    /** @return array<int, array<string, array<int, array<string,string>>>> CatalogId => Tag => [subfield => nilai][] */
    private function loadMarc(array $catalogIds): array
    {
        if (!$catalogIds) {
            return [];
        }

        $result = [];
        $rows   = $this->db->table('catalog_ruas')
            ->select('CatalogId, Tag, Value')
            ->whereIn('CatalogId', $catalogIds)
            ->whereIn('Tag', self::MARC_TAGS)
            ->orderBy('CatalogId')->orderBy('Sequence')
            ->get()->getResultArray();

        foreach ($rows as $row) {
            $result[$row['CatalogId']][$row['Tag']][] = $row['Tag'] === '008'
                ? ['_' => (string) $row['Value']]
                : $this->parseSubfields((string) $row['Value']);
        }

        return $result;
    }

    /** "$a Judul :$b Anak judul /$c Pengarang" → ['a' => 'Judul', 'b' => 'Anak judul', 'c' => 'Pengarang'] */
    private function parseSubfields(string $value): array
    {
        $subfields = [];
        $parts     = preg_split('/\$([a-z0-9])/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 1; $i < count($parts); $i += 2) {
            $code = strtolower($parts[$i]);
            if (!isset($subfields[$code])) {
                $subfields[$code] = $this->clean($parts[$i + 1] ?? '');
            }
        }

        return $subfields;
    }

    /** Buang tanda baca ISBD di ujung nilai MARC */
    private function clean(?string $value): string
    {
        return trim(rtrim(trim((string) $value), ' /:;,='));
    }

    // ----------------------------------------------------------------
    // ROW BUILDER
    // ----------------------------------------------------------------

    private function buildRow(int $no, array $row, array $marc): array
    {
        $sub = static function (string $tag, string $code) use ($marc): string {
            return $marc[$tag][0][$code] ?? '';
        };
        $all = static function (string $tag) use ($marc): string {
            return implode('; ', array_filter(array_map(static fn ($f) => $f['a'] ?? '', $marc[$tag] ?? [])));
        };

        // Judul katalog berformat "Judul : Anak judul / Penanggung jawab" bila ruas 245 tidak ada
        $judul = $sub('245', 'a');
        $anak  = $sub('245', 'b');
        $tjawab = $sub('245', 'c');
        if ($judul === '' && !empty($row['Title'])) {
            [$judulAnak, $tjawabTitle] = array_pad(explode(' / ', $row['Title'], 2), 2, '');
            [$judulTitle, $anakTitle]  = array_pad(explode(' : ', $judulAnak, 2), 2, '');
            $judul  = $this->clean($judulTitle);
            $anak   = $anak ?: $this->clean($anakTitle);
            $tjawab = $tjawab ?: $this->clean($tjawabTitle);
        }

        // Penerbitan dari ruas 260 (atau 264); kolom katalog bisa berisi gabungan beberapa ruas "a; b"
        $terbit = $marc['260'][0] ?? $marc['264'][0] ?? [];
        $first  = fn (?string $value): string => $this->clean(explode(';', (string) $value)[0]);

        $bahasa = trim((string) $row['Languages']);
        if (!preg_match('/^[a-z]{3}$/i', $bahasa)) {
            $bahasa = substr($marc['008'][0]['_'] ?? '', 35, 3);
            $bahasa = preg_match('/^[a-z]{3}$/i', $bahasa) ? $bahasa : '';
        }

        $price = (float) $row['Price'];

        return [
            $no,
            $this->formatDate($row['TanggalPengadaan']),
            (string) $row['NoInduk'],
            (string) $row['NomorBarcode'],
            (string) $row['RFID'],
            (string) $row['JenisSumber'],
            (string) $row['NamaSumber'],
            (string) $row['Currency'],
            floor($price) == $price ? (int) $price : $price,
            (string) $row['KodeLokasiPerpustakaan'],
            (string) $row['KodeLokasiRuang'],
            (string) $row['Akses'],
            (string) $row['Kategori'],
            (string) $row['Media'],
            (string) $row['Ketersediaan'],
            (string) $row['CallNumber'],
            (string) $row['JenisBahan'],
            $judul,
            $anak,
            $tjawab,
            $sub('100', 'a') ?: $this->clean($row['Author']),
            $sub('110', 'a'),
            $all('700'),
            $all('710'),
            $sub('250', 'a') ?: $this->clean($row['Edition']),
            ($terbit['a'] ?? '') ?: $first($row['PublishLocation']),
            ($terbit['b'] ?? '') ?: $first($row['Publisher']),
            ($terbit['c'] ?? '') ?: $first($row['PublishYear']),
            $sub('300', 'a'),
            $sub('300', 'c'),
            $this->clean($row['ISBN']),
            $sub('022', 'a'),
            $sub('024', 'a'),
            $this->clean($row['DeweyNo']),
            (string) $row['CallNumberKatalog'],
            $sub('520', 'a') ?: trim((string) $row['Note']),
            $bahasa,
            $all('650') ?: $this->clean($row['Subject']),
            (string) $row['EDISISERIAL'],
            $this->formatDate($row['TANGGAL_TERBIT_EDISI_SERIAL']),
            (string) $row['BAHAN_SERTAAN'],
            (string) $row['KETERANGAN_LAIN'],
        ];
    }

    private function formatDate(?string $value): string
    {
        if (empty($value) || str_starts_with($value, '0000')) {
            return '';
        }
        $time = strtotime($value);
        return $time === false ? '' : date('d-m-Y', $time);
    }

    /**
     * Satu baris <row> seperti hasil PhpSpreadsheet::fromArray():
     * nilai kosong / 0 jadi sel kosong bergaris, tipe data ditentukan DefaultValueBinder,
     * teks disimpan sebagai shared string ($strings: dedup per batch, $sst: XML <si> yang ditambahkan).
     */
    private function rowXml(int $rowNumber, array $values, array &$strings, string &$sst, int &$sstCount): string
    {
        $xml = '<row r="' . $rowNumber . '" spans="1:' . count(self::HEADERS) . '">';
        foreach ($values as $i => $value) {
            $ref = Coordinate::stringFromColumnIndex($i + 1) . $rowNumber;
            if ($value == null) {
                $xml .= '<c r="' . $ref . '" s="2"/>';
                continue;
            }
            $value = is_string($value) ? $this->xmlSafe($value) : $value;
            // Teks diawali "=" tetap teks (bukan formula)
            $isText = is_string($value) && str_starts_with($value, '=');
            if (!$isText && DefaultValueBinder::dataTypeForValue($value) === DataType::TYPE_NUMERIC) {
                $xml .= '<c r="' . $ref . '" s="2"><v>' . ($value + 0) . '</v></c>';
            } else {
                $value = (string) $value;
                if (!isset($strings[$value])) {
                    $strings[$value] = $sstCount++;
                    $sst .= $this->sharedStringXml($value);
                }
                $xml .= '<c r="' . $ref . '" s="2" t="s"><v>' . $strings[$value] . '</v></c>';
            }
        }
        return $xml . '</row>';
    }

    private function sharedStringXml(string $value): string
    {
        $preserve = $value !== trim($value) || str_contains($value, "
") ? ' xml:space="preserve"' : '';
        return '<si><t' . $preserve . '>' . htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></si>';
    }

    /** Buang karakter kontrol yang tidak valid di XML */
    private function xmlSafe(string $value): string
    {
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $value) ?? '';
    }

    // ----------------------------------------------------------------
    // XLSX BUILDER
    // ----------------------------------------------------------------

    /**
     * Kerangka workbook (styles, lebar kolom, header) dibuat oleh PhpSpreadsheet dengan styling
     * yang sama dengan downloadTemplate(); hanya baris data yang disisipkan secara streaming.
     */
    private function buildXlsx(string $jobId, array $state): void
    {
        $dir       = $this->jobDir($jobId);
        $colCount  = count(self::HEADERS);
        $lastCol   = Coordinate::stringFromColumnIndex($colCount);
        $lastRow   = $state['done'] + 1;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->fromArray([self::HEADERS], null, 'A1');
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        // Baris contoh berisi nilai terpanjang tiap kolom → dasar perhitungan auto size
        foreach ($state['longest'] as $i => $value) {
            $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($i + 1) . '2', (string) $value, DataType::TYPE_STRING);
        }
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        for ($col = 1; $col <= $colCount; $col++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $skeleton = $dir . 'skeleton.xlsx';
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($skeleton);
        $spreadsheet->disconnectWorksheets();

        $zip = new \ZipArchive();
        if ($zip->open($skeleton) !== true) {
            throw new \RuntimeException('Tidak dapat membuka kerangka xlsx');
        }
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false || !preg_match('#^(.*<sheetData>)(<row r="1".*?</row>).*?(</sheetData>.*)$#s', $sheetXml, $m)) {
            throw new \RuntimeException('Struktur sheet tidak dikenali');
        }
        [, $head, $headerRow, $tail] = $m;
        $head = preg_replace('#<dimension ref="[^"]*"/>#', '<dimension ref="A1:' . $lastCol . $lastRow . '"/>', $head);
        $head = preg_replace('#sqref="[^"]*"#', 'sqref="A2:' . $lastCol . $lastRow . '"', $head);

        $sheetFile = $dir . 'sheet1.xml';
        $out = fopen($sheetFile, 'wb');
        fwrite($out, $head . $headerRow);
        $in = fopen($dir . 'rows.xml', 'rb');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fwrite($out, $tail);
        fclose($out);

        // Shared strings: header (indeks 0..n-1, sama dengan kerangka) lalu teks data
        $sstFile = $dir . 'sharedStrings.xml';
        $out = fopen($sstFile, 'wb');
        fwrite($out, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "
"
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" uniqueCount="' . $state['sst_count'] . '">'
            . implode('', array_map([$this, 'sharedStringXml'], self::HEADERS)));
        $in = fopen($dir . 'strings.xml', 'rb');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fwrite($out, '</sst>');
        fclose($out);

        $zip->deleteName('xl/worksheets/sheet1.xml');
        $zip->addFile($sheetFile, 'xl/worksheets/sheet1.xml');
        $zip->deleteName('xl/sharedStrings.xml');
        $zip->addFile($sstFile, 'xl/sharedStrings.xml');
        if (!$zip->close()) {
            throw new \RuntimeException('Gagal menulis file xlsx');
        }

        rename($skeleton, $dir . 'export.xlsx');
        @unlink($sheetFile);
        @unlink($sstFile);
        @unlink($dir . 'rows.xml');
        @unlink($dir . 'strings.xml');
    }

    // ----------------------------------------------------------------
    // JOB STATE
    // ----------------------------------------------------------------

    private function jobDir(string $jobId): string
    {
        return WRITEPATH . 'exports/eksemplar/' . $jobId . '/';
    }

    private function loadState(string $jobId): ?array
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $jobId)) {
            return null;
        }
        $file = $this->jobDir($jobId) . 'state.json';
        if (!is_file($file)) {
            return null;
        }
        $state = json_decode((string) file_get_contents($file), true);

        // Job hanya boleh dilanjutkan / diunduh oleh user yang membuatnya
        return is_array($state) && ($state['user_id'] ?? null) == login_id() ? $state : null;
    }

    private function saveState(string $jobId, array $state): void
    {
        file_put_contents($this->jobDir($jobId) . 'state.json', json_encode($state), LOCK_EX);
    }

    private function cleanupOldJobs(): void
    {
        foreach (glob(WRITEPATH . 'exports/eksemplar/*', GLOB_ONLYDIR) ?: [] as $dir) {
            if (filemtime($dir) < time() - self::JOB_LIFETIME) {
                array_map('unlink', glob($dir . '/*') ?: []);
                @rmdir($dir);
            }
        }
    }

    // ----------------------------------------------------------------
    // RESPONSE
    // ----------------------------------------------------------------

    private function denyUnlessAllowed()
    {
        if (!is_allowed('export-eksemplar/export', null, false)) {
            return $this->respondJson(['status' => 'error', 'message' => lang('App.permission.not.have')], 403);
        }
        return null;
    }

    /** Sertakan token CSRF baru karena token diregenerasi tiap POST */
    private function respondJson(array $data, int $code = 200)
    {
        $data['csrf'] = csrf_hash();
        return $this->response->setStatusCode($code)->setJSON($data);
    }
}
