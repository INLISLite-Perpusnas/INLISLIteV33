<?php

namespace Katalog\Controllers;

use Base\Models\DataModel;

/**
 * KatalogFormController
 * * Menangani create dan edit katalog dengan gaya linear CI4.
 * Memastikan parameter RDA tetap terjaga saat terjadi error validasi.
 * Alur simpan mengikuti INLISLite 3 (actionSave + actionCreateTaglistSimple):
 * POST form -> daftar ruas MARC -> catalog_ruas, lalu kolom catalogs diturunkan dari ruas.
 */
class KatalogFormController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------
    public function create()
    {
        $data['title'] = 'Tambah Katalog Form Sederhana';

        $is_rda = (int) ($this->request->getPost('IsRDA') ?? 0);

        $rules = [
            'judul.a' => [
                'label'  => 'Judul Utama',
                'rules'  => 'trim|required',
                'errors' => ['required' => '{field} tidak boleh kosong.']
            ]
        ];

        if ($this->request->getPost()) {

            if (!$this->validate($rules)) {
                // Ambil semua list error untuk SweetAlert
                $error_msg = '<ul>';
                foreach ($this->validator->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);

                // Kembalikan input agar form tidak kosong. Pakai redirect eksplisit
                // dengan ?rda=$is_rda (BUKAN redirect()->back()) supaya pilihan
                // pedoman katalog (RDA/AACR) tidak hilang saat validasi gagal —
                // redirect()->back() tidak menjamin query string rda ikut terbawa.
                return redirect()->to('katalog/create?rda=' . $is_rda)->withInput();
            }

            $post         = $this->request->getPost();
            $worksheet_id = (int) ($post['Worksheet_id'] ?? 1);
            $db = db_connect();
            $db->transBegin();

            try {
                $now           = date("Y-m-d H:i:s");
                $ControlNumber = get_control_number();
                $BIBID         = get_bib_id();
                $ruas          = build_simple_ruas($post, $is_rda == 1, $worksheet_id);

                // 1. Simpan catalogs, kolom bibliografis diturunkan dari ruas
                $save_data = array_merge(ruas_to_catalog_fields($ruas), [
                    'ControlNumber'  => $ControlNumber,
                    'BIBID'          => $BIBID,
                    'CoverURL'       => '',
                    'Worksheet_id'   => $worksheet_id,
                    'IsRDA'          => $is_rda,
                    'IsOPAC'         => isset($post['IsOPAC']) ? 1 : 0, // Lebih aman pakai isset
                    'Branch_id'      => user()->branch_id ?? null,
                    'CreateBy'       => user_id(),
                    'CreateDate'     => $now,
                    'CreateTerminal' => get_ip_address(),
                    'UpdateBy'       => user_id(),
                    'UpdateDate'     => $now,
                    'UpdateTerminal' => get_ip_address(),
                ]);

                $db->table('catalogs')->insert($save_data);
                $catalog_id = $db->insertID();

                // 2. Simpan Ruas (001, 005, 035 dari sistem + ruas form)
                save_katalog_ruas($catalog_id, $ControlNumber, $BIBID, $ruas);

                $db->transCommit();

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Katalog berhasil disimpan');

                if (($post['IsRedirect'] ?? 0) == 1) return redirect()->to('katalog');
                return redirect()->to("katalog/edit/$catalog_id?rda=$is_rda");

            } catch (\Throwable $th) {
                $db->transRollback();

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', 'Gagal Simpan: ' . $th->getMessage());

                return redirect()->to('katalog/create?rda=' . $is_rda)->withInput();
            }
        }

        $data['worksheets'] = (new DataModel('worksheets', null, 'ID'))->orderBy('NoUrut')->findAll();
        $data['validation'] = $this->validator ?? \Config\Services::validation();
        echo view('Katalog\Views\add', $data);
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------
    public function edit(?int $catalog_id = null)
    {
        $catalog = $this->katalogModel->find($catalog_id);
        if (!$catalog) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Katalog tidak ditemukan');
            return redirect()->to('katalog');
        }
        // Cast ke int - kolom IsRDA bertipe bit(1)
        $is_rda = (int) ($this->request->getPost('IsRDA') ?? $catalog->IsRDA ?? 0);

        $rules = [
            'judul.a' => [
                'label'  => 'Judul Utama',
                'rules'  => 'trim|required',
                'errors' => ['required' => '{field} harus diisi.']
            ]
        ];

        if ($this->request->getPost()) {

            if (!$this->validate($rules)) {
                // Ambil semua list error untuk SweetAlert
                $error_msg = '<ul>';
                foreach ($this->validator->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);

                // Kembalikan input agar form tidak kosong
                return redirect()->to("katalog/edit/$catalog_id?rda=$is_rda");
            }

            $post         = $this->request->getPost();
            $worksheet_id = (int) ($post['Worksheet_id'] ?? $catalog->Worksheet_id);
            $db   = db_connect();
            $db->transBegin();

            try {
                // Ruas lama dipakai untuk mempertahankan tag di luar form sederhana & isi 008
                $existing = $db->table('catalog_ruas')
                    ->where('CatalogId', $catalog_id)
                    ->orderBy('Sequence', 'ASC')->orderBy('ID', 'ASC')
                    ->get()->getResult();
                $ruas = build_simple_ruas($post, $is_rda == 1, $worksheet_id, $existing);

                // 1. Update catalogs, kolom bibliografis diturunkan dari ruas
                $update_data = array_merge(ruas_to_catalog_fields($ruas), [
                    'Worksheet_id'   => $worksheet_id,
                    'IsRDA'          => $is_rda,
                    'IsOPAC'         => !empty($post['IsOPAC']) ? 1 : 0,
                    'UpdateBy'       => user_id(),
                    'UpdateDate'     => date("Y-m-d H:i:s"),
                    'UpdateTerminal' => get_ip_address(),
                ]);

                $db->table('catalogs')->where('ID', $catalog_id)->update($update_data);

                // 2. Simpan Ruas (001, 005, 035, 990 dari sistem + ruas form)
                save_katalog_ruas($catalog_id, $catalog->ControlNumber, $catalog->BIBID, $ruas);

                $db->transCommit();

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Katalog berhasil diperbarui');

                if (($post['IsRedirect'] ?? 0) == 1) return redirect()->to('katalog');
                return redirect()->to("katalog/edit/$catalog_id?rda=$is_rda");

            } catch (\Throwable $th) {
                $db->transRollback();

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', 'Gagal Update: ' . $th->getMessage());

                return redirect()->to("katalog/edit/$catalog_id?rda=$is_rda");
            }
        }

        // --- PREPARE DATA VIEW ---
        $data['catalog']    = $catalog;
        $data['title']      = 'Edit Katalog Form Sederhana';
        $data['CreateBy']   = get_username($catalog->CreateBy ?? 0);
        $data['UpdateBy']   = get_username($catalog->UpdateBy ?? 0);
        $data['worksheet']  = (new DataModel('worksheets', null, 'ID'))->find($catalog->Worksheet_id);
        $data['worksheets'] = (new DataModel('worksheets', null, 'ID'))->orderBy('NoUrut')->findAll();

        foreach (['245','260','264','300','240','247','310','336','337','338','082'] as $tag) {
            $data['str_' . $tag] = get_array_tag($catalog_id, $tag);
        }

        // 008: 22 kelompok sasaran, 33 bentuk karya tulis, 35-37 bahasa
        // (awalan "$a " dari data lama dibuang dulu agar posisinya tepat)
        $cr_008 = get_catalog_ruas_tag($catalog_id, '008');
        $val_008 = preg_replace('/^\$a\s*/', '', $cr_008[0]->Value ?? '');
        $data['cr_008_ks']   = substr($val_008, 22, 1);
        $data['cr_008_bkt']  = substr($val_008, 33, 1);
        $data['cr_008_lang'] = substr($val_008, 35, 3);

        $data['files']           = $this->fileModel->where('Catalog_id', $catalog_id)->orderBy('UpdateDate', 'desc')->findAll();
        $data['article_files']   = $this->serialArticleFilesModel->getWithArticle($catalog_id);
        // Dibatasi ke artikel milik katalog ini saja (sebelumnya menampilkan
        // artikel dari SEMUA katalog di dropdown upload konten digital artikel).
        $data['serial_articles'] = $this->articleModel->where('Catalog_id', $catalog_id)->findAll();
        $data['validation']      = $this->validator ?? \Config\Services::validation();

        echo view('Katalog\Views\update', $data);
    }
}
