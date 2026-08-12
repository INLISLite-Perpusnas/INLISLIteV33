<?php

namespace Eksemplar\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

/**
 * EksemplarLabelController
 *
 * Menangani cetak label eksemplar (PDF dan Word DOCX).
 */
class EksemplarLabelController extends \Base\Controllers\BaseController
{
    use EksemplarBase;

    function __construct()
    {
        $this->initEksemplarBase();
    }

    public function print_label()
    {
        helper(['thumbnail', 'form']);

        $this->data['title'] = 'Cetak Label Eksemplar';

        $this->validation->setRules([
            'eksemplar_ids' => ['label' => 'Eksemplar',      'rules' => 'required'],
            'eksemplar_tpl' => ['label' => 'Template Label', 'rules' => 'required'],
        ]);

        if (!$this->request->getPost() || !$this->validation->withRequest($this->request)->run()) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : 'Tidak ada eksemplar yang dipilih.'
            );
            return redirect()->back();
        }

        $post         = $this->request->getPost();
        $template     = $post['eksemplar_tpl'];
        $paperSize    = $post['paper_size'] ?? 'a4';
        $outputFormat = $post['output_format'] ?? 'pdf';
        $idsArr       = array_filter(
            array_map('intval', explode(',', preg_replace('/[^0-9,]/', '', $post['eksemplar_ids']))),
            fn($id) => $id > 0
        );

        if (empty($idsArr)) {
            $this->session->setFlashdata('swal_icon',  'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_html',  'Tidak ada ID eksemplar yang valid.');
            return redirect()->back();
        }

        $allowedTemplates = [
            'cetak-label-a4-1', 'cetak-label-a4-2', 'cetak-label-a4-3',
            'cetak-label-a4-4', 'cetak-label-a4-5', 'cetak-label-a4-6', 'cetak-label-a4-7','cetak-label-a4-8', 'cetak-label-a4-9', 'cetak-label-a4-10', 'cetak-label-a4-11', 'cetak-label-a4-12', 'cetak-label-a4-4-qrcode',
            'cetak-label-lr1',  'cetak-label-lr2',  'cetak-label-lr3',
            'cetak-label-lr4',  'cetak-label-lr5',  'cetak-label-lr6',
            'cetak-label-br1',  'cetak-label-br2',
            'cetak-label-tj107-1',
            'cetak-label-tj121-1', 'cetak-label-tj121-2',
            'cetak-label-gc121-1', 'cetak-label-gc121-2',
            'cetak-label-gc121-3', 'cetak-label-gc121-4',
        ];

        if (!in_array($template, $allowedTemplates, true)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Template tidak dikenali: ' . esc($template));
            return redirect()->back();
        }

        $db = db_connect();

        $eksemplarData = $db->table('collections as a')
            ->select('a.ID, a.NomorBarcode, b.Title, b.CallNumber, b.DeweyNo')
            ->join('catalogs b', 'b.ID = a.Catalog_id')
            ->whereIn('a.ID', $idsArr)
            ->get()
            ->getResultObject();

        $firstChars = array_values(array_unique(array_filter(
            array_map(fn($row) => strtoupper(substr((string) ($row->DeweyNo ?? ''), 0, 1)), $eksemplarData),
            fn($c) => $c !== ''
        )));

        $warnaMap = [];
        if (!empty($firstChars)) {
            $placeholders = implode(',', array_fill(0, count($firstChars), '?'));
            $kelasRows = $db->query(
                "SELECT KdKelas, Warna FROM master_kelas_besar WHERE LEFT(KdKelas, 1) IN ($placeholders)",
                $firstChars
            )->getResultArray();
            foreach ($kelasRows as $kelas) {
                $key = strtoupper(substr((string) $kelas['KdKelas'], 0, 1));
                if (!isset($warnaMap[$key])) {
                    $warnaMap[$key] = $kelas['Warna'];
                }
            }
        }

        // Model A4-9 menampilkan legenda 5 kode kelas besar per label, dengan kode
        // yang cocok dengan kelas item disorot menggunakan warnanya.
        $legendTemplates = ['cetak-label-a4-9'];
        $legendKelas = [];
        if (in_array($template, $legendTemplates, true)) {
            $legendKelas = $db->query(
                "SELECT KdKelas, Warna FROM master_kelas_besar WHERE active = 1 ORDER BY KdKelas ASC LIMIT 5"
            )->getResultArray();
        }

        // Model A4-11 mewarnai tiap digit (1-3 digit pertama) dari DeweyNo item
        // menurut warna kelas besar-nya masing-masing (mis. "201" -> digit 2, 0, 1
        // masing-masing diwarnai sesuai kelasnya sendiri), bukan legenda tetap.
        $digitColorMap = [];
        if ($template === 'cetak-label-a4-11') {
            $allKelasRows = $db->query(
                "SELECT KdKelas, Warna FROM master_kelas_besar WHERE active = 1 ORDER BY KdKelas ASC"
            )->getResultArray();
            foreach ($allKelasRows as $kelas) {
                $key = strtoupper(substr((string) $kelas['KdKelas'], 0, 1));
                if (!isset($digitColorMap[$key])) {
                    $digitColorMap[$key] = $kelas['Warna'];
                }
            }
        }

        if (empty($eksemplarData)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Data eksemplar tidak ditemukan.');
            return redirect()->back();
        }

        $namaPerpustakaan = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Mitra';

        // Model A4-10 menampilkan nama cabang/lokasi operator yang sedang mencetak
        // (mis. nama perpustakaan cabang) di atas nama perpustakaan induk.
        $namaCabang = $namaPerpustakaan;
        if ($template === 'cetak-label-a4-10') {
            $branchRow = $db->table('branchs')->where('ID', branch_id())->get()->getRow();
            $namaCabang = $branchRow->Name ?? $namaPerpustakaan;
        }

        $useQrCode = str_contains($template, 'qrcode') || str_contains($paperSize, 'qrcode');
        $useVerticalBarcode = in_array($template, ['cetak-label-a4-6', 'cetak-label-a4-9'], true);
        $usesColorLegend = in_array($template, $legendTemplates, true);

        $LabelData = [];
        foreach ($eksemplarData as $row) {
            $firstChar = strtoupper(substr((string) ($row->DeweyNo ?? ''), 0, 1));

            $entry = [
                'Title'              => character_limiter($row->Title, 50),
                'Barcode'            => $row->NomorBarcode,
                'CallNumber'         => $row->CallNumber,
                'NamaPerpustakaan'   => $namaPerpustakaan,
                'NamaCabang'         => $namaCabang,
                'Warna1'             => $warnaMap[$firstChar] ?? '#FFFF66',
                'BarcodePNG'         => $useQrCode
                                        ? get_qrcode_png($row->NomorBarcode)
                                        : get_barcode_png($row->NomorBarcode),
                'BarcodePNGVertical' => $useVerticalBarcode
                                        ? get_barcode_png_vertical($row->NomorBarcode)
                                        : null,
            ];

            if ($usesColorLegend) {
                // Kode kelas legenda selalu ditampilkan (sampai 5 kelas); hanya baris
                // yang cocok dengan kelas item ini diberi warna latar.
                for ($i = 1; $i <= 5; $i++) {
                    $kelas = $legendKelas[$i - 1] ?? null;
                    $entry['KodeWarna' . $i] = $kelas['KdKelas'] ?? '';
                    $entry['Warna' . $i]     = ($kelas && strtoupper(substr((string) $kelas['KdKelas'], 0, 1)) === $firstChar)
                        ? $kelas['Warna']
                        : '';
                }
            }

            if ($template === 'cetak-label-a4-11') {
                // 3 digit pertama DeweyNo, masing-masing diwarnai sesuai kelasnya sendiri.
                $dewey = (string) ($row->DeweyNo ?? '');
                for ($i = 1; $i <= 3; $i++) {
                    $digit = strtoupper($dewey[$i - 1] ?? '');
                    $entry['KodeWarna' . $i] = $digit;
                    $entry['Warna' . $i]     = $digitColorMap[$digit] ?? '';
                }
            }

            $LabelData[] = $entry;
        }

        if ($outputFormat === 'word') {
            $html = view('Eksemplar\Views\template\\' . $template, [
                'LabelData' => $LabelData,
                'outputFormat' => $outputFormat
            ]);
            return $this->_generateWordDocHtml($html);
        }

        return view('Eksemplar\Views\template\\' . $template, ['LabelData' => $LabelData]);
    }

    private function _generateWordDocHtml(string $html)
    {
        // Generate MHTML so images (base64) are fully embedded and layout is preserved
        $boundary = "----=_NextPart_000_0000_01D1B1B1.1B1B1B1B";
        $images = [];
        
        $mhtmlContent = preg_replace_callback('/<img[^>]+src="([^"]+)"[^>]*>/i', function($matches) use (&$images) {
            $src = $matches[1];
            if (strpos($src, 'data:image') === 0) {
                $parts = explode(',', $src);
                if (count($parts) > 1) {
                    $mime = explode(';', explode(':', $parts[0])[1])[0];
                    $base64 = $parts[1];
                    $cid = "img_" . md5($base64) . '.' . explode('/', $mime)[1];
                    $images[$cid] = ['mime' => $mime, 'data' => $base64];
                    return str_replace($src, $cid, $matches[0]);
                }
            }
            return $matches[0];
        }, $html);

        $mhtml = "MIME-Version: 1.0\r\n";
        $mhtml .= "Content-Type: multipart/related; boundary=\"$boundary\"\r\n\r\n";
        
        $mhtml .= "--$boundary\r\n";
        $mhtml .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $mhtml .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        
        // Clean up transparent borders which show as gridlines in MS Word
        $mhtmlContent = str_replace('1px solid transparent', 'none', $mhtmlContent);
        // Ensure page breaks are respected for label roll formats (which use <br> between labels)
        $mhtmlContent = str_replace('</table><br>', '</table><br clear="all" style="page-break-before:always" />', $mhtmlContent);

        // Ensure complete HTML structure for MS Word, with necessary CSS to collapse borders
        $fullHtml = "<html><head><meta charset=\"utf-8\"><title>Export Word</title>
<style>
    table { border-collapse: collapse; }
    td { mso-cellspacing: 0px; mso-padding-alt: 0px 0px 0px 0px; }
</style>
</head><body>\r\n" . $mhtmlContent . "\r\n</body></html>";
        $mhtml .= quoted_printable_encode($fullHtml) . "\r\n\r\n";
        
        foreach ($images as $cid => $img) {
            $mhtml .= "--$boundary\r\n";
            $mhtml .= "Content-Type: " . $img['mime'] . "\r\n";
            $mhtml .= "Content-Transfer-Encoding: base64\r\n";
            $mhtml .= "Content-Location: $cid\r\n\r\n";
            $mhtml .= chunk_split($img['data'], 76, "\r\n") . "\r\n";
        }
        $mhtml .= "--$boundary--";

        $response = service('response');
        $response->setHeader('Content-Type', 'application/vnd.ms-word');
        $response->setHeader('Content-Disposition', 'attachment; filename="label-eksemplar.doc"');
        $response->setHeader('Cache-Control', 'max-age=0');
        $response->setBody($mhtml);

        return $response;
    }
}
