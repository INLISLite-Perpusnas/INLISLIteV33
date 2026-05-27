<?php

namespace Eksemplar\Controllers;

/**
 * EksemplarFormController
 *
 * Menangani create dan edit eksemplar.
 */
class EksemplarFormController extends \Base\Controllers\BaseController
{
    use EksemplarBase;

    function __construct()
    {
        $this->initEksemplarBase();
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create()
    {
        $this->data['title'] = 'Tambah Eksemplar';

        $NomorInduk = $this->db->table('settingparameters')->where('Name', 'NomorInduk')->get()->getRow()->Value ?: "Otomatis";

        $this->data['NomorInduk'] = ($NomorInduk == "Manual") ? "True" : "False";

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
                $redirect = isset($post['redirect']) ? $post['redirect'] : '';

                $collections = [];
                $total       = $post['JumlahEksemplar'];
                $Catalog_id  = isset($post['Catalog_id']) ? $post['Catalog_id'] : null;

                if (empty($Catalog_id)) {
                    $this->session->setFlashdata('swal_icon',  'warning');
                    $this->session->setFlashdata('swal_title', 'Peringatan');
                    $this->session->setFlashdata('swal_text',  'Judul Katalog tidak boleh kosong');
                    return redirect()->back()->withInput();
                }

                $catalogRow   = $this->db->table('catalogs')
                                         ->where('ID', $Catalog_id)
                                         ->get()->getRow();
                $worksheet_id = (!empty($catalogRow) && isset($catalogRow->Worksheet_id)) ? $catalogRow->Worksheet_id : '';

                for ($i = 1; $i <= $total; $i++) {
                    if ($NomorInduk == "Manual") {
                        $idx          = $i - 1;
                        $noInduk      = isset($post['NoInduk' . $idx]) ? $post['NoInduk' . $idx] : '';
                        $nomorBarcode = isset($post['NomorBarcode' . $idx]) ? $post['NomorBarcode' . $idx] : '';
                        $rfid         = isset($post['RFID' . $idx]) ? $post['RFID' . $idx] : '';
                    } else {
                        $collectionData = [
                            'worksheet_id' => $worksheet_id,
                            'category_id'  => isset($post['Category_id']) ? $post['Category_id'] : null,
                            'media_id'     => isset($post['Media_id']) ? $post['Media_id'] : null,
                            'source_id'    => isset($post['Source_id']) ? $post['Source_id'] : null,
                            'partner_id'   => isset($post['Partner_id']) ? $post['Partner_id'] : null,
                        ];
                        $nomorBarcode = $this->generateNomorBarcode($collectionData, $i);
                        $noInduk      = $nomorBarcode;
                        $rfid         = $nomorBarcode;
                    }

                    $save = [
                        'Catalog_id'          => $post['Catalog_id'],
                        'Branch_id'           => $post['Branch_id'],
                        'ISDRM'               => $post['ISDRM'],
                        'Location_Library_id' => $post['Location_Library_id'],
                        'Location_id'         => $post['Location_id'],
                        'NomorBarcode'        => $nomorBarcode,
                        'NoInduk'             => $noInduk,
                        'RFID'                => $rfid,
                        'CallNumber'          => isset($post['CallNumber']) ? $post['CallNumber'] : '',
                        'IsQUARANTINE'        => '0',
                        'CreateBy'            => user_id(),
                        'CreateDate'          => date("Y-m-d H:i:s"),
                        'UpdateBy'            => user_id(),
                        'UpdateDate'          => date("Y-m-d H:i:s"),
                    ];

                    if (!empty($post['TanggalPengadaan'])) $save['TanggalPengadaan'] = $post['TanggalPengadaan'];
                    if (!empty($post['Rule_id']))          $save['Rule_id']           = $post['Rule_id'];
                    if (!empty($post['Category_id']))      $save['Category_id']       = $post['Category_id'];
                    if (!empty($post['Currency']))         $save['Currency']          = $post['Currency'];
                    if (!empty($post['Media_id']))         $save['Media_id']          = $post['Media_id'];
                    if (!empty($post['Source_id']))        $save['Source_id']         = $post['Source_id'];
                    if (!empty($post['Status_id']))        $save['Status_id']         = $post['Status_id'];
                    if (!empty($post['Partner_id']))       $save['Partner_id']        = $post['Partner_id'];
                    if (!empty($post['Price']))            $save['Price']             = $post['Price'];
                    if (!empty($post['PriceType']))        $save['PriceType']         = $post['PriceType'];

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
                            $dupValue    = isset($m[1]) ? $m[1] : '';
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

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------

    public function edit($id)
    {
        $this->data['title'] = 'Ubah Eksemplar';

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

        $this->data['CreateBy'] = get_username(isset($eksemplar->CreateBy) ? $eksemplar->CreateBy : 0);
        $this->data['UpdateBy'] = get_username(isset($eksemplar->UpdateBy) ? $eksemplar->UpdateBy : 0);

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
                $redirect = isset($post['redirect']) ? $post['redirect'] : '';

                $update = [
                    'Location_Library_id' => $post['Location_Library_id'],
                    'Location_id'         => $post['Location_id'],
                    'ISDRM'               => $post['ISDRM'],
                    'NomorBarcode'        => isset($post['NomorBarcode0']) ? $post['NomorBarcode0'] : (isset($post['NomorBarcode']) ? $post['NomorBarcode'] : ''),
                    'NoInduk'             => isset($post['NoInduk0']) ? $post['NoInduk0'] : (isset($post['NoInduk']) ? $post['NoInduk'] : ''),
                    'RFID'                => isset($post['RFID0']) ? $post['RFID0'] : (isset($post['RFID']) ? $post['RFID'] : ''),
                    'UpdateBy'            => user_id(),
                    'UpdateDate'          => date("Y-m-d H:i:s"),
                ];

                if (!empty($post['TanggalPengadaan'])) $update['TanggalPengadaan'] = $post['TanggalPengadaan'];
                if (!empty($post['Rule_id']))          $update['Rule_id']           = $post['Rule_id'];
                if (!empty($post['Category_id']))      $update['Category_id']       = $post['Category_id'];
                if (!empty($post['Currency']))         $update['Currency']          = $post['Currency'];
                if (!empty($post['Media_id']))         $update['Media_id']          = $post['Media_id'];
                if (!empty($post['Source_id']))        $update['Source_id']         = $post['Source_id'];
                if (!empty($post['Status_id']))        $update['Status_id']         = $post['Status_id'];
                if (!empty($post['Partner_id']))       $update['Partner_id']        = $post['Partner_id'];
                if (!empty($post['Price']))            $update['Price']             = $post['Price'];
                if (!empty($post['PriceType']))        $update['PriceType']         = $post['PriceType'];

                try {
                    $this->eksemplarModel->update($id, $update);

                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Eksemplar berhasil diubah');

                    $IsRedirect = $this->request->getPost('IsRedirect');
                    if ($IsRedirect == 1) {
                        return !empty($redirect)
                            ? redirect()->to($redirect)
                            : redirect()->to('eksemplar');
                    } else {
                        return redirect()->back();
                    }

                } catch (\Throwable $e) {
                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                    $this->session->setFlashdata('swal_text', 'Eksemplar gagal diubah. Error DB: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }

            } else {
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

        echo view('Eksemplar\Views\update', $this->data);
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS
    // ----------------------------------------------------------------

    /**
     * Generate nomor barcode otomatis.
     * Array index genap = formatOptions, ganjil = separatorOptions.
     */
    private function generateNomorBarcode($collectionData = [], $increment = 1)
    {
        $db    = db_connect();
        $query = $db->table('collections')->select('MAX(RIGHT(NomorBarcode,5)) as no')->get();
        $no    = $query->getRow()->no;

        $formatResult = $db->table('settingparameters')
            ->select('Value')
            ->where('Name', 'FormatNomorInduk')
            ->get()
            ->getRow();

        $formatString = ($formatResult && !empty($formatResult->Value))
            ? $formatResult->Value
            : get_parameter('nomor-barcode', '{yyyy}/PN/{99999}');

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

        $formatArray = explode('|', $formatString);
        $result      = '';

        $separatorOptions = [
            '2' => '',
            '3' => '/',
            '4' => '-',
            '5' => '.',
        ];

        for ($i = 0; $i < count($formatArray); $i++) {
            $value = trim($formatArray[$i]);

            if ($i % 2 === 0) {
                if ($value !== '0') {
                    if (preg_match('/\{([^}]+)\}/', $value, $matches)) {
                        $result .= ($matches[1] === '99999') ? '{99999}' : $matches[1];
                    } elseif (is_numeric($value)) {
                        switch ($value) {
                            case '2':
                                if (!empty($collectionData['worksheet_id'])) {
                                    $r = $db->table('worksheets')->select('Code')->where('ID', $collectionData['worksheet_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '3':
                                if (!empty($collectionData['category_id'])) {
                                    $r = $db->table('collectioncategorys')->select('Code')->where('ID', $collectionData['category_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '4':
                                if (!empty($collectionData['media_id'])) {
                                    $r = $db->table('collectionmedias')->select('Code')->where('ID', $collectionData['media_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '5':
                                if (!empty($collectionData['partner_id'])) {
                                    $r = $db->table('partners')->select('Name')->where('ID', $collectionData['partner_id'])->get()->getRow();
                                    $result .= $r ? $r->Name : '';
                                }
                                break;
                            case '6':
                                $result .= '{99999}';
                                break;
                            case '7':
                                $result .= date('Y');
                                break;
                        }
                    }
                }
            } else {
                if ($value !== '2' && isset($separatorOptions[$value])) {
                    $result .= $separatorOptions[$value];
                }
            }
        }

        $result = str_replace('{99999}', '', $result);

        if (empty($no)) {
            $no = 1;
        } else {
            $no = intval($no) + $increment;
            $no = str_pad($no, 5, "0", STR_PAD_LEFT);
        }

        return $result . $no;
    }
}
