<?php

namespace Eksemplar\Controllers;

use Eksemplar\Models\EksemplarModel as AkuisisiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;

class Eksemplar extends \Base\Controllers\BaseController
{
    use ResponseTrait;
    protected $eksemplarModel;

    protected $katalogModel;
    protected $katalogRuasModel;
    protected $db;

    function __construct()
    {
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();
        $this->db = \Config\Database::connect('data');



        helper('reference');
        helper('katalog');
        helper('eksemplar');
    }

    public function index()
    {
         if (!is_allowed('eksemplar/index')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('eksemplar');
        }
        $this->data['title'] = 'Eksemplar';
        echo view('Eksemplar\Views\list', $this->data);
    }

    public function karantina()
    {
        $data['title'] = 'Karantina Eksemplar';
        echo view('Eksemplar\Views\list_karantina', $data);
    }



    /**
     * Corrected logic untuk generateNomorBarcode
     * Array index genap = formatOptions, ganjil = separatorOptions
     */

    public function create()
{
    $this->data['title'] = 'Tambah Eksemplar';
    $slug = $this->request->getGet('slug');

    $NomorInduk = $this->db->table('settingparameters')->where('Name', 'NomorInduk')->get()->getRow()->Value ?: "Otomatis";
    
    if ($NomorInduk == "Manual") {
        $this->data['NomorInduk'] = "True";
    } else {
        $this->data['NomorInduk'] = "False";
    }

    $this->validation->setRules([
        'Catalog_id'          => ['label' => 'Judul Katalog',       'rules' => 'required'],
        'Location_Library_id' => ['label' => 'Lokasi Perpustakaan', 'rules' => 'required'],
        'Location_id'         => ['label' => 'Lokasi Ruang',        'rules' => 'required'],
        'Source_id'           => ['label' => 'Sumber Pengadaan',    'rules' => 'required'],
        'Partner_id'          => ['label' => 'Nama Sumber',         'rules' => 'required'],
        'Media_id'            => ['label' => 'Bentuk Fisik',        'rules' => 'required'],
        'Category_id'         => ['label' => 'Kategori Koleksi',    'rules' => 'required'],
        'TanggalPengadaan'    => ['label' => 'Tanggal Pengadaan',   'rules' => 'required'],
    ]);

    if ($this->request->getPost()) {
        if ($this->validation->withRequest($this->request)->run()) {
            helper('form');
            $post     = $this->request->getPost();
            $redirect = $post['redirect'] ?? '';

            $collections = [];
            $total      = $post['JumlahEksemplar'];
            $Catalog_id = $post['Catalog_id'] ?? null;

            if (empty($Catalog_id)) {
                $this->session->setFlashdata('swal_icon',  'warning');
                $this->session->setFlashdata('swal_title', 'Peringatan');
                $this->session->setFlashdata('swal_text',  'Judul Katalog tidak boleh kosong');
                return redirect()->back()->withInput();
            }

            $worksheet_id = $this->db->table('catalogs')
                                     ->where('ID', $Catalog_id)
                                     ->get()->getRow()->Worksheet_id ?? '';

            for ($i = 1; $i <= $total; $i++) {

                // =====================================================
                // Logic NoInduk: Manual vs Otomatis
                // =====================================================
                if ($NomorInduk == "Manual") {
                    // Ambil dari input manual view
                    // index input view dimulai dari 0, loop $i dari 1
                    $idx          = $i - 1;
                    $noInduk      = $post['NoInduk'      . $idx] ?? '';
                    $nomorBarcode = $post['NomorBarcode' . $idx] ?? '';
                    $rfid         = $post['RFID'         . $idx] ?? '';
                } else {
                    // Generate otomatis
                    $collectionData = [
                        'worksheet_id' => $worksheet_id        ?? null,
                        'category_id'  => $post['Category_id'] ?? null,
                        'media_id'     => $post['Media_id']    ?? null,
                        'source_id'    => $post['Source_id']   ?? null,
                        'partner_id'   => $post['Partner_id']  ?? null,
                    ];
                    $nomorBarcode = $this->generateNomorBarcode($collectionData, $i);
                    $noInduk      = $nomorBarcode;
                    $rfid         = $nomorBarcode;
                }
                // =====================================================

                $save = [
                    'Catalog_id'          => $post['Catalog_id'],
                    'Branch_id'           => $post['Branch_id'],
                    'ISDRM'               => $post['ISDRM'],
                    'Location_Library_id' => $post['Location_Library_id'],
                    'Location_id'         => $post['Location_id'],
                    'NomorBarcode'        => $nomorBarcode,
                    'NoInduk'             => $noInduk,
                    'RFID'                => $rfid,
                    'CallNumber'          => $post['CallNumber'] ?? '',
                    'IsQUARANTINE'        => '0',
                    'CreateBy'            => user_id(),
                    'CreateDate'          => date("Y-m-d H:i:s"),
                    'UpdateBy'            => user_id(),
                    'UpdateDate'          => date("Y-m-d H:i:s"),
                ];
          
                if (!empty($post['TanggalPengadaan'])) $save['TanggalPengadaan'] = $post['TanggalPengadaan'];
                if (!empty($post['Rule_id']))          $save['Rule_id']          = $post['Rule_id'];
                if (!empty($post['Category_id']))      $save['Category_id']      = $post['Category_id'];
                if (!empty($post['Currency']))      $save['Currency']         = $post['Currency'];
                if (!empty($post['Media_id']))         $save['Media_id']         = $post['Media_id'];
                if (!empty($post['Source_id']))        $save['Source_id']        = $post['Source_id'];
                if (!empty($post['Status_id']))        $save['Status_id']        = $post['Status_id'];
                if (!empty($post['Partner_id']))       $save['Partner_id']       = $post['Partner_id'];
                if (!empty($post['Price']))            $save['Price']            = $post['Price'];
                if (!empty($post['PriceType']))        $save['PriceType']        = $post['PriceType'];
                
                $save['ISOPAC'] = !empty($post['ISOPAC']) ? 1 : 0;

                array_push($collections, $save);
            }

            if (!empty($collections)) {
                try {
                    $insert = $this->eksemplarModel->insertBatch($collections);

                    if ($insert === false) {
                        $modelErrors = $this->eksemplarModel->errors();
                        $errorString = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Unknown Model Validation Error';

                        $this->session->setFlashdata('swal_icon',  'error');
                        $this->session->setFlashdata('swal_title', 'Validasi Model Gagal');
                        $this->session->setFlashdata('swal_text',  'Eksemplar gagal ditambah. Penyebab: ' . $errorString);
                        return redirect()->back()->withInput();
                    }

                    $this->session->setFlashdata('swal_icon',  'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text',  'Eksemplar berhasil ditambah');

                    $IsRedirect = $this->request->getPost('IsRedirect');
                    if ($IsRedirect == 1) {
                        return !empty($redirect)
                            ? redirect()->to($redirect)
                            : redirect()->to('eksemplar');
                    } else {
                        return redirect()->back();
                    }

                } catch (\Throwable $e) {
                    $errorMessage = $e->getMessage();

                    if (str_contains($errorMessage, 'Duplicate entry')) {
                        preg_match("/Duplicate entry '(.+?)' for key/", $errorMessage, $m);
                        $dupValue = $m[1] ?? '';
                        $friendlyMsg = "Nomor Barcode <strong>{$dupValue}</strong> sudah terdaftar di sistem. Gunakan nomor barcode yang berbeda.";
                    } else {
                        $friendlyMsg = 'Eksemplar gagal ditambah. Silakan coba lagi atau hubungi administrator.';
                    }

                    $this->session->setFlashdata('swal_icon',  'error');
                    $this->session->setFlashdata('swal_title', 'Gagal Menyimpan');
                    $this->session->setFlashdata('swal_html',  $friendlyMsg);

                    log_message('error', '[Eksemplar Create DB Error] ' . $errorMessage);
                    return redirect()->back()->withInput();
                }
            }

        } else {
            $error_msg = '<ul>';
            foreach ($this->validation->getErrors() as $error) {
                $error_msg .= '<li>' . esc($error) . '</li>';
            }
            $error_msg .= '</ul>';

            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Validasi Gagal');
            $this->session->setFlashdata('swal_html',  $error_msg);

            return redirect()->back()->withInput();
        }
    }

    $this->data['redirect'] = base_url('eksemplar/create');
    echo view('Eksemplar\Views\add', $this->data);
}


    private function generateNomorBarcode($collectionData = [], $increment = 1)
    {

        $db = db_connect();
        $query = $db->table('collections')->select('MAX(RIGHT(NomorBarcode,5)) as no')->get();
        $no = $query->getRow()->no;

        // Cek FormatNomorInduk dari settingparameters
        $formatQuery = $db->table('settingparameters')
            ->select('Value')
            ->where('Name', 'FormatNomorInduk')
            ->get();

        $formatResult = $formatQuery->getRow();

        if ($formatResult && !empty($formatResult->Value)) {
            $formatString = $formatResult->Value;
        } else {
            // Fallback ke parameter lama
            $formatString = get_parameter('nomor-barcode', '{yyyy}/PN/{99999}');
        }

        // Jika format tidak menggunakan array format (tidak ada |), process seperti biasa
        if (strpos($formatString, '|') === false) {
            $format2 = str_replace('{yyyy}', date('Y'), $formatString);
            $format3 = str_replace('{99999}', '', $format2);

            if (empty($no)) {
                $no = 1;
            } else {
                $no = intval($no) + $increment;
                $no = str_pad($no, 5, "0", STR_PAD_LEFT);
            }

            return $format3 . $no;
        }

        // Process format array menggunakan logic yang sama seperti getFormatPreview()
        $formatArray = explode('|', $formatString);
        $result = '';

        $separatorOptions = [
            '2' => '',              // -Kosong-
            '3' => '/',
            '4' => '-',
            '5' => '.'
        ];

        // Loop melalui array dengan pattern: Format → Separator → Format → Separator
        for ($i = 0; $i < count($formatArray); $i++) {
            $value = trim($formatArray[$i]);


            // Index genap (0,2,4,6,8) = Format Options
            if ($i % 2 === 0) {
                // Process Format Options

                if ($value !== '0') { // Skip jika kosong

                    // Cek jika ada manual input dengan {}
                    if (preg_match('/\{([^}]+)\}/', $value, $matches)) {
                        if ($matches[1] === '99999') {
                            $result .= '{99999}'; // Placeholder untuk auto number
                        } else {
                            $result .= $matches[1]; // Manual input seperti Dispusip, Nias
                        }
                    } else if (is_numeric($value)) {
                        // Process format code
                        switch ($value) {
                            case '1': // Manual Input (sudah diproses di atas dengan {})
                                break;

                            case '2': // Kode Jenis Bahan dari worksheets.Code
                                if (!empty($collectionData['worksheet_id'])) {
                                    $queryWS = $db->table('worksheets')->select('Code')->where('ID', $collectionData['worksheet_id'])->get();
                                    $resultWS = $queryWS->getRow();
                                    $result .= $resultWS ? $resultWS->Code : '';
                                }
                                break;

                            case '3': // Kode Kategori Koleksi dari collectioncategorys.Code
                                if (!empty($collectionData['category_id'])) {
                                    $queryCat = $db->table('collectioncategorys')->select('Code')->where('ID', $collectionData['category_id'])->get();
                                    $resultCat = $queryCat->getRow();
                                    $result .= $resultCat ? $resultCat->Code : '';
                                }
                                break;

                            case '4': // Kode Bentuk Fisik dari collectionmedias.Code
                                if (!empty($collectionData['media_id'])) {
                                    $queryMedia = $db->table('collectionmedias')->select('Code')->where('ID', $collectionData['media_id'])->get();
                                    $resultMedia = $queryMedia->getRow();
                                    $result .= $resultMedia ? $resultMedia->Code : '';
                                }
                                break;

                            case '5': // Kode Jenis Sumber Pengadaan dari partners.Name
                                if (!empty($collectionData['partner_id'])) {
                                    $queryPartner = $db->table('partners')->select('Name')->where('ID', $collectionData['partner_id'])->get();
                                    $resultPartner = $queryPartner->getRow();
                                    $result .= $resultPartner ? $resultPartner->Name : '';
                                }
                                break;

                            case '6': // 99999 - Auto increment
                                $result .= '{99999}';
                                break;

                            case '7': // YYYY - Current year
                                $result .= date('Y');
                                break;

                            default:
                                // Format tidak dikenal, skip
                                break;
                        }
                    }
                }
            }
            // Index ganjil (1,3,5,7,9) = Separator Options
            else {
                // Process Separator Options
                if ($value !== '2' && isset($separatorOptions[$value])) { // Skip jika kosong
                    $result .= $separatorOptions[$value];
                }
            }
        }

        // Remove {99999} placeholder
        $result = str_replace('{99999}', '', $result);

        // Generate auto increment number
        if (empty($no)) {
            $no = 1;
        } else {
            $no = intval($no) + $increment;
            $no = str_pad($no, 5, "0", STR_PAD_LEFT);
        }

        return $result . $no;
    }
public function edit($id)
    {
        $this->data['title'] = 'Ubah Eksemplar';
        $slug = $this->request->getGet('slug');
        
        $eksemplar = $this->eksemplarModel->find($id);
        $this->data['eksemplar'] = $eksemplar;
        
        if (!empty($eksemplar)) {
            $katalog = $this->katalogModel->find($eksemplar->Catalog_id);
            $this->data['katalog'] = $katalog;
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data eksemplar tidak ditemukan');
            return redirect()->to('/eksemplar');
        }

        $CreateBy = get_username($eksemplar->CreateBy ?? 0);
        $UpdateBy = get_username($eksemplar->UpdateBy ?? 0);
        $this->data['CreateBy'] = $CreateBy;
        $this->data['UpdateBy'] = $UpdateBy;

        // --- Aturan Validasi (Disamakan dengan fungsi create) ---
        $this->validation->setRules([
            'Catalog_id'          => ['label' => 'Judul Katalog', 'rules' => 'required'],
            'Location_Library_id' => ['label' => 'Lokasi Perpustakaan', 'rules' => 'required'],
            'Location_id'         => ['label' => 'Lokasi Ruang', 'rules' => 'required'],
            'Source_id'           => ['label' => 'Sumber Pengadaan', 'rules' => 'required'],
            'Partner_id'          => ['label' => 'Nama Sumber', 'rules' => 'required'],
            'Media_id'            => ['label' => 'Bentuk Fisik', 'rules' => 'required'],
            'Category_id'         => ['label' => 'Kategori Koleksi', 'rules' => 'required'],
            'TanggalPengadaan'    => ['label' => 'Tanggal Pengadaan', 'rules' => 'required'],
        ]);

        if ($this->request->getPost()) {
            // Cek Validasi Form
            if ($this->validation->withRequest($this->request)->run()) {
                helper('form');
                $post = $this->request->getPost();
                $redirect = $post['redirect'] ?? '';

                $update = [
                    'Location_Library_id' => $post['Location_Library_id'],
                    'Location_id'         => $post['Location_id'],
                    'ISDRM'               => $post['ISDRM'],
                    // Fallback pengecekan key array jika namanya tanpa '0' di view
                    'NomorBarcode'        => $post['NomorBarcode0'] ?? ($post['NomorBarcode'] ?? ''),
                    'NoInduk'             => $post['NoInduk0'] ?? ($post['NoInduk'] ?? ''),
                    'RFID'                => $post['RFID0'] ?? ($post['RFID'] ?? ''),
                    'UpdateBy'            => user_id(),
                    'UpdateDate'          => date("Y-m-d H:i:s")
                ];

                if (!empty($post['TanggalPengadaan'])) {
                    $update['TanggalPengadaan'] = $post['TanggalPengadaan'];
                }
                if (!empty($post['Rule_id'])) {
                    $update['Rule_id'] = $post['Rule_id'];
                }
                if (!empty($post['Category_id'])) {
                    $update['Category_id'] = $post['Category_id'];
                }
                if (!empty($post['Currency'])) {
                    $update['Currency'] = $post['Currency'];
                }
                if (!empty($post['Media_id'])) {
                    $update['Media_id'] = $post['Media_id'];
                }
                if (!empty($post['Source_id'])) {
                    $update['Source_id'] = $post['Source_id'];
                }
                if (!empty($post['Status_id'])) {
                    $update['Status_id'] = $post['Status_id'];
                }
                if (!empty($post['Partner_id'])) {
                    $update['Partner_id'] = $post['Partner_id'];
                }
                if (!empty($post['Price'])) {
                    $update['Price'] = $post['Price'];
                }
                if (!empty($post['PriceType'])) {
                    $update['PriceType'] = $post['PriceType'];
                }

                try {
                    $this->eksemplarModel->update($id, $update);
                    
                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Eksemplar berhasil diubah');
                    
                    $IsRedirect = $this->request->getPost('IsRedirect');
                    if ($IsRedirect == 1) {
                        if (!empty($redirect)) {
                            return redirect()->to($redirect);
                        } else {
                            return redirect()->to('eksemplar');
                        }
                    } else {
                        // Jika berhasil tapi disuruh stay di form
                        return redirect()->back(); 
                    }

                } catch (\Throwable $e) {
                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                    $this->session->setFlashdata('swal_text', 'Eksemplar gagal diubah. Error DB: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }

            } else {
                // Tampilkan SweetAlert Error Validasi form
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg); 
                
                return redirect()->back()->withInput();
            }
        } 

        // Load View (Tidak perlu ngelempar validation error lagi karena sudah ditarik di swal_html)
        echo view('Eksemplar\Views\update', $this->data);
    }

    public function delete($id)
    {
        if (!is_allowed('eksemplar/delete')) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Akses Ditolak');
            $this->session->setFlashdata('swal_text', 'Maaf, Anda tidak memiliki akses');
            return redirect()->to('eksemplar');
        }

        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Parameter ID tidak valid');
            return redirect()->to('eksemplar');
        }

        $eksemplar = $this->eksemplarModel->find($id);
        if (!$eksemplar) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data eksemplar tidak ditemukan');
            return redirect()->to('eksemplar');
        }

        $EksemplarDelete = $this->eksemplarModel->delete($id);
        
        if ($EksemplarDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Eksemplar berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Eksemplar gagal dihapus');
        }
        
        return redirect()->to('eksemplar');
    }


public function print_label()
{
    helper(['thumbnail', 'form']);
 
    $this->data['title'] = 'Cetak Label Eksemplar';
 
    // ── Validasi ───────────────────────────────────────────────────────────
    $this->validation->setRules([
        'eksemplar_ids' => ['label' => 'Eksemplar',       'rules' => 'required'],
        'eksemplar_tpl' => ['label' => 'Template Label',  'rules' => 'required'],
    ]);
 
    if (! $this->request->getPost() || ! $this->validation->withRequest($this->request)->run()) {
        $this->session->setFlashdata('swal_icon',  'error');
        $this->session->setFlashdata('swal_title', 'Gagal');
        $this->session->setFlashdata('swal_html',
            $this->validation->getErrors()
                ? $this->validation->listErrors()
                : 'Tidak ada eksemplar yang dipilih.'
        );
        return redirect()->back();
    }
 
    // ── Ambil & sanitasi input ─────────────────────────────────────────────
    $post      = $this->request->getPost();
    $template  = $post['eksemplar_tpl'];
    $paperSize = $post['paper_size'] ?? 'a4';
    $idsArr    = array_filter(
        array_map('intval', explode(',', preg_replace('/[^0-9,]/', '', $post['eksemplar_ids']))),
        fn($id) => $id > 0
    );
 
    if (empty($idsArr)) {
        $this->session->setFlashdata('swal_icon',  'warning');
        $this->session->setFlashdata('swal_title', 'Peringatan');
        $this->session->setFlashdata('swal_html',  'Tidak ada ID eksemplar yang valid.');
        return redirect()->back();
    }
 
    // ── Whitelist template (cegah path traversal) ─────────────────────────
    $allowedTemplates = [
        'cetak-label-a4-1', 'cetak-label-a4-2', 'cetak-label-a4-3',
        'cetak-label-a4-4', 'cetak-label-a4-5', 'cetak-label-a4-4-qrcode',
        'cetak-label-lr1',  'cetak-label-lr2',  'cetak-label-lr3',
        'cetak-label-lr4',  'cetak-label-lr5',  'cetak-label-lr6',
        'cetak-label-br1',  'cetak-label-br2',
        'cetak-label-tj107-1',
        'cetak-label-tj121-1', 'cetak-label-tj121-2',
        'cetak-label-gc121-1', 'cetak-label-gc121-2',
        'cetak-label-gc121-3', 'cetak-label-gc121-4',
    ];
 
    if (! in_array($template, $allowedTemplates, true)) {
        $this->session->setFlashdata('swal_icon',  'error');
        $this->session->setFlashdata('swal_title', 'Gagal');
        $this->session->setFlashdata('swal_html',  'Template tidak dikenali: ' . esc($template));
        return redirect()->back();
    }
 
    // ── Query database ─────────────────────────────────────────────────────
    $db = db_connect();
 
    $eksemplarData = $db->table('collections as a')
        ->select('a.ID, a.NomorBarcode, b.Title, b.CallNumber')
        ->join('catalogs b', 'b.ID = a.Catalog_id')
        ->whereIn('a.ID', $idsArr)
        ->get()
        ->getResultObject();
 
    if (empty($eksemplarData)) {
        $this->session->setFlashdata('swal_icon',  'error');
        $this->session->setFlashdata('swal_title', 'Gagal');
        $this->session->setFlashdata('swal_html',  'Data eksemplar tidak ditemukan.');
        return redirect()->back();
    }
 
    // ── Ambil nama perpustakaan ────────────────────────────────────────────
    $namaPerpustakaan = $db->table('settingparameters')
        ->where('Name', 'NamaPerpustakaan')
        ->get()
        ->getRow()
        ->Value ?? 'Perpustakaan Mitra';
 
    // ── Tentukan jenis gambar: Barcode atau QR Code ────────────────────────
    $useQrCode = str_contains($template, 'qrcode') || str_contains($paperSize, 'qrcode');
 
    // ── Bangun LabelData ───────────────────────────────────────────────────
    $LabelData = [];
 
    foreach ($eksemplarData as $row) {
        $LabelData[] = [
            'Title'            => character_limiter($row->Title, 50),
            'Barcode'          => $row->NomorBarcode,
            'CallNumber'       => $row->CallNumber,
            'NamaPerpustakaan' => $namaPerpustakaan,
            'Warna1'           => '#FFFF66',
            'BarcodePNG'       => $useQrCode
                                    ? get_qrcode_png($row->NomorBarcode)
                                    : get_barcode_png($row->NomorBarcode),
        ];
    }
 
    // ── Render view → TCPDF output PDF langsung (download) ────────────────
    return view('Eksemplar\Views\template\\' . $template, ['LabelData' => $LabelData]);
}

   

public function proses_karantina()
    {
        // Ambil data dari POST
        $eksemplar_ids = $this->request->getPost('eksemplar_ids');

        // Jika eksemplar_ids berupa string "3,4", ubah menjadi array
        if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
            $eksemplar_ids = array_map('trim', explode(',', $eksemplar_ids));
        }

        // Validasi: Pastikan eksemplar_ids berisi angka
        if (empty($eksemplar_ids) || !is_array($eksemplar_ids) || !array_filter($eksemplar_ids, 'is_numeric')) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih atau data tidak valid');
            return redirect()->back();
        }

        // Koneksi ke database
        $db = db_connect();
        try {
            // Perbarui status IsQUARANTINE menjadi 1
            $builder = $db->table('collections');
            $builder->whereIn('ID', $eksemplar_ids);
            $builder->update(['IsQUARANTINE' => 1]);

            // Periksa apakah ada baris yang diperbarui
            if ($db->affectedRows() > 0) {
                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Eksemplar berhasil dikarantina');
            } else {
                $this->session->setFlashdata('swal_icon', 'info');
                $this->session->setFlashdata('swal_title', 'Informasi');
                $this->session->setFlashdata('swal_text', 'Tidak ada perubahan data, mungkin eksemplar sudah dikarantina');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Gagal memproses karantina: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function pulihkan_eksemplar()
    {
        $eksemplar_ids = $this->request->getVar('ID');

        // We need to modify the JavaScript to properly pass the IDs
        $form = $this->request->getVar();

        // If we have IDs as checkboxes (ID[]), handle that format
        if (isset($form['ID']) && is_array($form['ID'])) {
            $eksemplar_ids = $form['ID'];
        }

        // If no IDs, redirect with error
        if (empty($eksemplar_ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih');
            return redirect()->back();
        }

        try {
            // Update IsQUARANTINE value to 0 for selected items
            $builder = $this->db->table('collections');
            $builder->whereIn('ID', $eksemplar_ids);
            $builder->update(['IsQUARANTINE' => 0]);

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Eksemplar berhasil dipulihkan');
            
            return redirect()->to(base_url('eksemplar'));
        } catch (\Exception $e) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Gagal memulihkan eksemplar: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function hapus_permanen()
    {
        $IDs = $this->request->getPost('ID');

        if (empty($IDs) || !is_array($IDs)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih eksemplar yang akan dihapus permanen terlebih dahulu');
            return redirect()->back();
        }

        $ids = array_filter(array_map('intval', $IDs));

        try {
            $db = db_connect();
            $db->table('collections')->whereIn('ID', $ids)->delete();

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Eksemplar berhasil dihapus permanen');
        } catch (\Exception $e) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Gagal menghapus eksemplar: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Process OPAC display for selected collection items
     * * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function proses_opac()
    {
        // Get the eksemplar_ids from the POST data
        $eksemplar_ids = $this->request->getPost('eksemplar_ids');

        // If eksemplar_ids is a string like "3,4", convert it to an array
        if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
            $eksemplar_ids = explode(',', $eksemplar_ids);
            $eksemplar_ids = array_map('trim', $eksemplar_ids);
        }

        // If still empty or not an array, redirect with error
        if (empty($eksemplar_ids) || !is_array($eksemplar_ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih');
            return redirect()->back();
        }

        try {
            // Update IsOPAC value to 1 for selected items
            $builder = $this->db->table('collections');
            $builder->whereIn('ID', $eksemplar_ids);
            $builder->update(['IsOPAC' => 1]);

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Eksemplar berhasil ditampilkan ke OPAC');
            
            return redirect()->back();
        } catch (\Exception $e) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Gagal memproses OPAC: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Other controller methods would go here...
    // bagian import eksemplar
    public function importviews()
    {
        $this->data['title'] = 'Import eksemplar Excel';
        return view('Eksemplar\Views\import', $this->data);
    }

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
            // Pastikan direktori upload ada
            if (!is_dir(WRITEPATH . 'uploads/temp')) {
                mkdir(WRITEPATH . 'uploads/temp', 0755, true);
            }

            // Move file to temp directory
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $fileName);
            $filePath = WRITEPATH . 'uploads/temp/' . $fileName;

            // Load spreadsheet menggunakan PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();


            // Remove header row
            $header = array_shift($rows);

            // Process import
            $result = $this->processImport($rows, $header);

            // Delete temp file
            unlink($filePath);

            return $this->respond([
                'status' => 'success',
                'message' => 'Import berhasil',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            // Delete temp file if exists
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }

            return $this->fail([
                'message' => 'Error saat import: ' . $e->getMessage()
            ]);
        }
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

                    // Parse row data
                    $catalogData = $this->parseCatalogData($row, $header);


                    $collectionsData = $this->parseCollectionsData($row, $header);


                    $marcFields = $this->generateBasicMarcFields($catalogData); // Generate basic MARC fields

                    // Insert catalog
                    $catalogId = $this->insertCatalog($catalogData);

                    // Insert MARC fields
                    if (!empty($marcFields)) {
                        $this->insertMarcFields($catalogId, $marcFields);
                    }

                    // Insert collections
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
                'error_count' => $errorCount,
                'errors' => array_slice($errors, 0, 10) // Batasi error yang ditampilkan
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
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
    private function getValue($row, $headerMap, $columnName, $default = '')
    {
        try {
            // Pastikan $headerMap adalah array
            if (!is_array($headerMap)) {
                log_message('error', 'headerMap is not an array: ' . gettype($headerMap));
                return $default;
            }

            // Pastikan $row adalah array
            if (!is_array($row)) {
                log_message('error', 'row is not an array: ' . gettype($row));
                return $default;
            }

            // Cek apakah column name ada di header mapping
            if (isset($headerMap[$columnName])) {
                $columnIndex = $headerMap[$columnName];

                // Pastikan index adalah integer
                if (!is_numeric($columnIndex)) {
                    log_message('error', "Column index is not numeric for {$columnName}: " . gettype($columnIndex));
                    return $default;
                }

                $columnIndex = (int)$columnIndex;

                // Pastikan index ada di row
                if (!isset($row[$columnIndex])) {
                    log_message('debug', "Column index {$columnIndex} not found in row for {$columnName}");
                    return $default;
                }

                $value = $row[$columnIndex];

                // Trim whitespace jika value adalah string
                return is_string($value) ? trim($value) : $value;
            }

            return $default;
        } catch (\Exception $e) {
            log_message('error', "Error in getValue for {$columnName}: " . $e->getMessage());
            return $default;
        }
    }

    private function parseCatalogData($row, $header)
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
            'Worksheet_id'=>1,
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

    // Method untuk generate BIBID berdasarkan ControlNumber
    private function generateBibId($controlNumber)
    {
        // Extract number part dari ControlNumber
        $numberPart = substr($controlNumber, 5); // Remove 'INLIS' prefix

        // Format BIBID: 0010-092 + last 7 digits
        $lastSevenDigits = substr($numberPart, -7);
        $bibId = '0010-092' . $lastSevenDigits;

        return $bibId;
    }

    // Method untuk generate sequence yang aman untuk concurrent access
    private function getNextSequenceNumber()
    {
        // Gunakan database untuk generate sequence yang thread-safe
        $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(ControlNumber, 6) AS UNSIGNED)), 0) + 1 as next_num 
                FROM catalog 
                WHERE ControlNumber LIKE 'INLIS%'";

        $result = $this->db->query($sql)->getRow();

        return $result ? $result->next_num : 1;
    }

    // Alternative method dengan database sequence (lebih robust)
    private function generateControlNumberWithSequence()
    {
        try {
            // Start transaction untuk ensure atomicity
            $this->db->transBegin();

            // Get next sequence number
            $nextNumber = $this->getNextSequenceNumber();

            // Format ControlNumber
            $controlNumber = 'INLIS' . str_pad($nextNumber, 14, '0', STR_PAD_LEFT);

            // Commit transaction
            $this->db->transCommit();

            return $controlNumber;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw new \Exception('Failed to generate ControlNumber: ' . $e->getMessage());
        }
    }
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
    private function parseCollectionsData($row, $header)
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

    private function parseMarcFields($row, $header)
    {
        $marcFields = [];
        $headerMap = array_flip($header);

        if (isset($headerMap['MARC_Fields'])) {
            $marcText = $row[$headerMap['MARC_Fields']] ?? '';

            if (!empty($marcText)) {
                // Parse MARC fields (format: tag1:indicator1:indicator2:value1;tag2:indicator1:indicator2:value2)
                $marcLines = explode(';', $marcText);
                $sequence = 1;

                foreach ($marcLines as $line) {
                    if (empty(trim($line))) continue;

                    $parts = explode(':', $line, 4);
                    if (count($parts) >= 4) {
                        $marcFields[] = [
                            'Tag' => trim($parts[0]),
                            'Indicator1' => trim($parts[1]) ?: '#',
                            'Indicator2' => trim($parts[2]) ?: '#',
                            'Value' => trim($parts[3]),
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
        }

        return $marcFields;
    }

    private function insertCatalog($data)
    {
        // Check if ControlNumber already exists
        $existing = $this->katalogModel->where('ControlNumber', $data['ControlNumber'])->first();
        if ($existing) {
            throw new \Exception("ControlNumber {$data['ControlNumber']} sudah ada");
        }

        if (!$this->katalogModel->insert($data)) {
            $errors = $this->katalogModel->errors();
            throw new \Exception("Gagal insert catalog: " . implode(', ', $errors));
        }

        return $this->katalogModel->getInsertID();
    }

    private function insertMarcFields($catalogId, $marcFields)
    {
        foreach ($marcFields as $field) {
            $field['CatalogId'] = $catalogId;

            if (!$this->katalogRuasModel->insert($field)) {
                $errors = $this->katalogRuasModel->errors();
                throw new \Exception("Gagal insert MARC field: " . implode(', ', $errors));
            }
        }
    }

    private function insertCollections($catalogId, $collections)
    {
        foreach ($collections as $collection) {
            $collection['Catalog_id'] = $catalogId;


            // Check if barcode already exists
            $existing = $this->eksemplarModel->where('NomorBarcode', $collection['NomorBarcode'])->first();
            if ($existing) {
                throw new \Exception("Barcode {$collection['NomorBarcode']} sudah ada");
            }

            if (!$this->eksemplarModel->insert($collection)) {
                $errors = $this->eksemplarModel->errors();
                throw new \Exception("Gagal insert collection: " . implode(', ', $errors));
            }
        }
    }

   public function downloadTemplate()
{
    // Clear any previous output and increase memory limit
    ob_clean();
    ini_set('memory_limit', '1024M');
    
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers sesuai format yang diminta
        $headers = [
            'NO',
            'TGL_PENGADAAN',
            'NO_INDUK',
            'NO_BARCODE',
            'NO_RFID',
            'JENIS_SUMBER',
            'NAMA_SUMBER',
            'MATA_UANG',
            'HARGA',
            'KODE_LOKASI_PERPUSTAKAAN',
            'KODE_LOKASI_RUANG',
            'AKSES',
            'KATEGORI',
            'MEDIA',
            'KETERSEDIAAN',
            'NOMOR_PANGGIL_EKSEMPLAR',
            'JENIS_BAHAN',
            'JUDUL_UTAMA',
            'ANAK_JUDUL',
            'PERNYATAAN_TANGGUNGJAWAB',
            'TAJUK_PENGARANG',
            'TAJUK_PENGARANG_BADAN_KOOPERASI',
            'PENGARANG_TAMBAHAN_NAMA_ORANG',
            'PENGARANG_TAMBAHAN_NAMA_BADAN',
            'EDISI',
            'KOTA_TERBIT',
            'PENERBIT',
            'TAHUN_TERBIT',
            'JUMLAH_HALAMAN',
            'DIMENSI',
            'ISBN',
            'ISSN',
            'ISMN',
            'NO_DDC',
            'NOMOR_PANGGIL_KATALOG',
            'ABSTRAK',
            'BAHASA',
            'SUBJEK_TOPIK',
            'EDISI_SERIAL',
            'TGL_TERBIT_EDISI_SERIAL',
            'BAHAN_SERTAAN_SERIAL',
            'KETERANGAN_LAIN_SERIAL'
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
                1,
                '14-02-2015',
                'X0022/2016',
                'X0022/2016',
                'X0022/2016',
                'Hadiah/Hibah',
                '---Belum ditentukan---',
                'IDR',
                0,
                'Pusat',
                '0101',
                'Dapat dipinjam',
                'Koleksi Umum',
                'Buku',
                'Tersedia',
                '123 PRA m',
                'Monograf',
                'Mahligai Biru',
                '',
                'Mamik Pradana',
                'Pradana, Mamik',
                '',
                '',
                '',
                '',
                'Jakarta',
                'Grafika',
                '2015',
                '120 hlm.',
                '25 cm.',
                '978-222-666-444',
                '',
                '',
                '123',
                '123 PRA m',
                '',
                'ind',
                'Rumah Tangga',
                '',
                '',
                '',
                ''
            ],
            [
                2,
                '15-02-2015',
                'X0023/2016',
                'X0023/2016',
                'X0023/2016',
                'Pembelian',
                '---Belum ditentukan---',
                'IDR',
                0,
                'Pusat',
                '0101',
                'Dapat dipinjam',
                'Koleksi Umum',
                'Buku',
                'Tersedia',
                '201 SAM k',
                'Monograf',
                'Kancil dan Kerbau',
                '',
                'Deni Saman',
                'Saman, Deni',
                '',
                '',
                '',
                '',
                'Jakarta',
                'Prabu',
                '2015',
                '68 hlm.',
                '21 cm.',
                '856-225-456-78',
                '',
                '',
                '201',
                '201 SAM k',
                '',
                'ind',
                'Fiksi',
                '',
                '',
                '',
                ''
            ],
            [
                3,
                '16-03-2015',
                'X0024/2016',
                'X0024/2016',
                'X0024/2016',
                'Pembelian',
                'Toko Buku Mandiri',
                'IDR',
                75000,
                'Pusat',
                '0102',
                'Dapat dipinjam',
                'Koleksi Umum',
                'Buku',
                'Tersedia',
                '004.678 BUD p',
                'Monograf',
                'Pemrograman Web dengan PHP',
                'Panduan Lengkap untuk Pemula',
                'Budi Raharjo',
                'Raharjo, Budi',
                '',
                '',
                '',
                'Edisi 2',
                'Bandung',
                'Informatika',
                '2015',
                '350 hlm.',
                '24 cm.',
                '978-602-1234-567-8',
                '',
                '',
                '004.678',
                '004.678 BUD p',
                'Buku panduan pemrograman web menggunakan PHP',
                'ind',
                'Teknologi Informasi; Pemrograman',
                '',
                '',
                '',
                ''
            ],
            [
                4,
                '20-03-2015',
                'X0025/2016',
                'X0025/2016',
                'X0025/2016',
                'Hadiah/Hibah',
                'Dinas Pendidikan',
                'IDR',
                0,
                'Pusat',
                '0103',
                'Dapat dipinjam',
                'Koleksi Umum',
                'Buku',
                'Tersedia',
                '899.221 DEW s',
                'Monograf',
                'Sastra Indonesia Kontemporer',
                'Analisis dan Apresiasi',
                'Dewi Lestari',
                'Lestari, Dewi',
                '',
                'Pusat Bahasa',
                '',
                'Edisi 3',
                'Jakarta',
                'Gramedia Pustaka Utama',
                '2015',
                '320 hlm.',
                '20 cm.',
                '978-602-0307-456-7',
                '',
                '',
                '899.221',
                '899.221 DEW s',
                'Kumpulan analisis sastra Indonesia modern',
                'ind',
                'Sastra Indonesia; Literatur',
                '',
                '',
                '',
                ''
            ],
            [
                5,
                '25-03-2015',
                'X0026/2016',
                'X0026/2016',
                'X0026/2016',
                'Pembelian',
                'CV. Pustaka Ilmu',
                'IDR',
                85000,
                'Pusat',
                '0104',
                'Dapat dipinjam',
                'Koleksi Referensi',
                'Buku',
                'Tersedia',
                '904.598 BAM s',
                'Monograf',
                'Sejarah Perkembangan Teknologi Digital di Indonesia',
                '',
                'Prof. Dr. Bambang Sutrisno; Dr. Maya Sari',
                'Sutrisno, Bambang',
                '',
                '',
                'Sari, Maya',
                'Edisi 1',
                'Jakarta',
                'Erlangga',
                '2015',
                '500 hlm.',
                '24 cm.',
                '978-602-2989-345-6',
                '',
                '',
                '904.598',
                '904.598 BAM s',
                'Dokumentasi lengkap perkembangan teknologi digital di Indonesia',
                'ind',
                'Sejarah; Teknologi; Indonesia',
                '',
                '',
                '',
                ''
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        for ($col = 1; $col <= count($headers); $col++) {
            $columnID = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
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
        header('Cache-Control: max-age=1'); //IE
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        
    } catch (\Exception $e) {
        log_message('error', 'Download template error: ' . $e->getMessage());
        echo 'Error: ' . $e->getMessage();
    }
    
    exit;
}

    // generate controlnumber
    private function generateControlNumber()
    {
        // Format: INLIS000000000004123
        $prefix = 'INLIS';
        $totalLength = 19; // Total length termasuk prefix
        $numberLength = $totalLength - strlen($prefix); // 14 digits untuk angka

        try {
            // Ambil ControlNumber tertinggi yang ada
            $lastRecord = $this->katalogModel
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

    // Method tambahan untuk validasi ControlNumber format
    private function validateControlNumberFormat($controlNumber)
    {
        $pattern = '/^INLIS\d{14}$/'; // INLIS + 14 digits
        return preg_match($pattern, $controlNumber);
    }

    // Method untuk mengecek apakah ControlNumber sudah digunakan
    private function isControlNumberExists($controlNumber)
    {
        $existing = $this->katalogModel
            ->where('ControlNumber', $controlNumber)
            ->countAllResults();

        return $existing > 0;
    }

    // Method yang lebih robust dengan retry mechanism
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
}
