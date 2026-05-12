<?php

namespace Katalog\Controllers;

use Base\Models\DataModel;

/**
 * KatalogFormController
 * * Menangani create dan edit katalog dengan gaya linear CI4.
 * Memastikan parameter RDA tetap terjaga saat terjadi error validasi.
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
        $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
        
        // Status RDA untuk handle redirect (Default 0 jika belum dipost)
         $is_rda = $this->request->getPost('IsRDA') ?? 0;
       
        // $rda_param = ($is_rda == 1) ? '1' : '0';
       
        $rules = [
            'judul.a' => [
                'label'  => 'Judul Utama',
                'rules'  => 'trim|required',
                'errors' => ['required' => '{field} tidak boleh kosong.']
            ]
        ];

        // 1. TAMBAHKAN PENGECEKAN METHOD POST DI SINI
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
                return redirect()->back()->withInput();
            }

            $post = $this->request->getPost();
            $db = db_connect();
            $db->transBegin();

            try {
                $ControlNumber = get_control_number();
                $BIBID         = get_bib_id();

                // 1. Mapping Data Catalogs
                $save_data = [
                    'ControlNumber' => $ControlNumber,
                    'BIBID'         => $BIBID,
                    'Branch_id'     => $branch_id,
                    'CoverURL'      => '',
                    'CreateBy'      => user_id(),
                    'CreateDate'    => date("Y-m-d H:i:s"),
                    'UpdateBy'      => user_id(),
                    'UpdateDate'    => date("Y-m-d H:i:s"),
                    'Worksheet_id'  => $post['Worksheet_id'] ?? 1,
                   'IsRDA'         => $is_rda,
                   'IsOPAC'        => isset($post['IsOPAC']) ? 1 : 0, // Lebih aman pakai isset
                ];

                if (!empty($post['judul'])) {
                    $judul = $post['judul'];
                    $save_data['Title'] = trim(($judul['a'] ?? '') . (!empty($judul['b']) ? ' ' . $judul['b'] : '') . (!empty($judul['c']) ? ' / ' . $judul['c'] : ''));
                }

                $save_data['Author'] = implode_data([
                    multi_array($post['pengarangUtama'] ?? []),
                    multi_array($post['pengarangTambahan'] ?? []),
                ]);

                $penerbit = $post['penerbit'] ?? [];
                $save_data['Publisher']       = $penerbit['b'] ?? null;
                $save_data['PublishLocation'] = $penerbit['a'] ?? null;
                $save_data['PublishYear']     = $penerbit['c'] ?? null;
                $save_data['Publikasi']       = trim(($penerbit['a'] ?? '') . (!empty($penerbit['b']) ? ' : ' . $penerbit['b'] : '') . (!empty($penerbit['c']) ? ', ' . $penerbit['c'] : ''));

                $save_data['PhysicalDescription'] = !empty($post['PhysicalDescription']) ? implode_data($post['PhysicalDescription'], ' ') : null;
                $save_data['Edition']             = $post['Edition'] ?? null;
                $save_data['Subject']             = !empty($post['subject']) ? multi_array($post['subject']) : null;
                $save_data['DeweyNo']             = !empty($post['DeweyNo']) ? implode_data($post['DeweyNo'], ' ') : null;
                $save_data['ISBN']                = !empty($post['ISBN']) ? implode_data($post['ISBN'], ' ; ') : null;
                $save_data['CallNumber']          = !empty($post['CallNumber']) ? implode_data($post['CallNumber'], ';') : null;
                $save_data['Note']                = !empty($post['catatan']) ? multi_array($post['catatan'], ';') : null;
                $save_data['Languages']           = !empty($post['Languages']['lang']) ? implode_data($post['Languages']['lang'], ' ') : null;
               
                $catalog_id = $this->katalogModel->insert($save_data);
                
                // 2. Simpan Ruas
                $post_merged = array_merge($post, [
                    'ControlNumber' => $ControlNumber,
                    'BIBID'         => $BIBID,
                    'tag005'        => date("YmdHis"),
                    'cat_id'        => $catalog_id,
                    'language'      => str_pad(date("ymd"), 22, "#") . str_pad(($post['Languages']['ks'] ?? '#'), 11, "#") . str_pad(($post['Languages']['bkt'] ?? '#'), 2, "#") . str_pad(($post['Languages']['lang'] ?? '#'), 5, "#"),
                ]);

                $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();
                $this->katalogRuasModel->insert_catalog_ruas(data_catalog_ruas($post_merged, $catalog_id));

                $db->transCommit();
                
                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Katalog berhasil disimpan');

                if ($post['IsRedirect'] == 1) return redirect()->to('katalog');
                return redirect()->to("katalog/edit/$catalog_id?rda=$is_rda");

            } catch (\Throwable $th) {
                $db->transRollback();
                
                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', 'Gagal Simpan: ' . $th->getMessage());
                
                return redirect()->to('katalog/create?rda=' . $is_rda)->withInput();
            }
        
        } // 2. TUTUP KURUNG KURAWAL IF POST DI SINI

        $data['worksheets'] = (new DataModel('worksheets', null, 'ID'))->orderBy('NoUrut')->findAll();
        $data['validation'] = $this->validator ?? \Config\Services::validation();
        echo view('Katalog\Views\add', $data);
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------
    public function edit(int $catalog_id = null)
    {
        $catalog = $this->katalogModel->find($catalog_id);
        if (!$catalog) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Katalog tidak ditemukan');
            return redirect()->to('katalog');
        }

        $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
       
        $is_rda = $this->request->getPost('IsRDA') ?? $catalog->IsRDA ?? 0;

     
     

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

            $post = $this->request->getPost();
            $db   = db_connect();
            $db->transBegin();

            try {
                // 1. Mapping Update
                $update_data = [
                    'UpdateBy'     => user_id(),
                    'UpdateDate'   => date("Y-m-d H:i:s"),
                    'Worksheet_id' => $post['Worksheet_id'] ?? $catalog->Worksheet_id,
                    'IsRDA'        => $is_rda,
                    'IsOPAC'       => !empty($post['IsOPAC']) ? 1 : 0,
                ];

                if (!empty($branch_id)) $update_data['Branch_id'] = $branch_id;

                if (!empty($post['judul'])) {
                    $judul = $post['judul'];
                    $update_data['Title'] = trim(($judul['a'] ?? '') . (!empty($judul['b']) ? ' ' . $judul['b'] : '') . (!empty($judul['c']) ? ' / ' . $judul['c'] : ''));
                }

                $update_data['Author'] = implode_data([
                    multi_array($post['pengarangUtama'] ?? []),
                    multi_array($post['pengarangTambahan'] ?? []),
                ]);

                $penerbit = $post['penerbit'] ?? [];
                $update_data['Publisher']       = $penerbit['b'] ?? null;
                $update_data['PublishLocation'] = $penerbit['a'] ?? null;
                $update_data['PublishYear']     = $penerbit['c'] ?? null;
                $update_data['Publikasi']       = trim(($penerbit['a'] ?? '') . (!empty($penerbit['b']) ? ' : ' . $penerbit['b'] : '') . (!empty($penerbit['c']) ? ', ' . $penerbit['c'] : ''));

                $update_data['PhysicalDescription'] = !empty($post['PhysicalDescription']) ? implode_data($post['PhysicalDescription'], ' ') : null;
                $update_data['Edition']             = $post['Edition'] ?? null;
                $update_data['Subject']             = !empty($post['subject']) ? multi_array($post['subject']) : null;
                $update_data['DeweyNo']             = !empty($post['DeweyNo']) ? implode_data($post['DeweyNo'], ' ') : null;
                $update_data['ISBN']                = !empty($post['ISBN']) ? implode_data($post['ISBN'], ' ; ') : null;
                $update_data['CallNumber']          = !empty($post['CallNumber']) ? implode_data($post['CallNumber'], ';') : null;
                $update_data['Note']                = !empty($post['catatan']) ? multi_array($post['catatan'], ';') : null;
                $update_data['Languages']           = !empty($post['Languages']['lang']) ? implode_data($post['Languages']['lang'], ' ') : null;
              
                $this->katalogModel->update($catalog_id, $update_data);
               
                // 2. Simpan Ruas
                $relatorBackup = $post['pengarangTambahanRelator'] ?? [];
                $post_merged = array_merge($post, [
                    'ControlNumber' => $catalog->ControlNumber,
                    'BIBID'         => $catalog->BIBID,
                    'tag005'        => date("YmdHis"),
                    'cat_id'        => $catalog_id,
                    'language'      => str_pad(date("ymd"), 22, "#") . str_pad(($post['Languages']['ks'] ?? '#'), 11, "#") . str_pad(($post['Languages']['bkt'] ?? '#'), 2, "#") . str_pad(($post['Languages']['lang'] ?? '#'), 5, "#"),
                ]);
                $post_merged['pengarangTambahanRelator'] = $relatorBackup;

                $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();
                $this->katalogRuasModel->insert_catalog_ruas(data_catalog_ruas($post_merged, $catalog_id));

                $db->transCommit();
                
                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Katalog berhasil diperbarui');

                if ($post['IsRedirect'] == 1) return redirect()->to('katalog');
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
   

        $cr_008 = get_catalog_ruas_tag($catalog_id, '008');
        $data['cr_008_ks']   = substr($cr_008[0]->Value ?? "", 25, 1);
        $data['cr_008_bkt']  = substr($cr_008[0]->Value ?? "", 36, 1);
        $data['cr_008_lang'] = substr($cr_008[0]->Value ?? "", 38, 3);

        $data['files']           = $this->fileModel->where('Catalog_id', $catalog_id)->orderBy('UpdateDate', 'desc')->findAll();
        $data['article_files']   = $this->serialArticleFilesModel->getWithArticle($catalog_id);
        $data['serial_articles'] = $this->articleModel->findAll();
        $data['validation']      = $this->validator ?? \Config\Services::validation();

        echo view('Katalog\Views\update', $data);
    }
}