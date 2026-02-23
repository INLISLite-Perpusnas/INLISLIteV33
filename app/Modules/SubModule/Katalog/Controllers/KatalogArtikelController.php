<?php

namespace Katalog\Controllers;

/**
 * KatalogArtikelController
 * * Menangani semua operasi CRUD untuk artikel serial
 * yang terhubung dengan data katalog secara linear.
 */
class KatalogArtikelController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create_artikel()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'catalog_id' => 'required|integer',
            'title'      => 'required|string',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status'   => 400,
                'messages' => ['error' => $validation->getErrors()],
            ]);
        }

        $catalog_id = $this->request->getPost('catalog_id');
        $save_data = [
            'Catalog_id'                  => $catalog_id,
            'Title'                       => $this->request->getPost('title'),
            'Creator'                     => $this->request->getPost('creator_final'),
            'Contributor'                 => $this->request->getPost('contributor_final'),
            'StartPage'                   => $this->request->getPost('start_page'),
            'Pages'                       => $this->request->getPost('pages'),
            'Subject'                     => $this->request->getPost('subject_final'),
            'EDISISERIAL'                 => $this->request->getPost('edisi_serial'),
            'TANGGAL_TERBIT_EDISI_SERIAL' => $this->request->getPost('tanggal_terbit'),
            'ISOPAC'                      => $this->request->getPost('isopac') ? 1 : 0,
            'CreateBy'                    => user_id(),
            'UpdateBy'                    => user_id(),
        ];

        $save_data_id = $this->artikelModel->insert($save_data);

        if ($save_data_id) {
            set_message('toastr_msg', 'Artikel berhasil disimpan');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Artikel gagal disimpan');
            set_message('toastr_type', 'warning');
        }

        return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=artikel'));
    }

    // ----------------------------------------------------------------
    // READ
    // ----------------------------------------------------------------

    public function get_artikel($id = null)
    {
        if (!$id) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'ID is required'])
                ->setStatusCode(400);
        }

        $artikel = $this->artikelModel->get_by_id($id);

        if (!$artikel) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'Article not found'])
                ->setStatusCode(404);
        }

        return $this->response->setJSON(['error' => false, 'data' => $artikel]);
    }

    // ----------------------------------------------------------------
    // UPDATE
    // ----------------------------------------------------------------

    public function edit_artikel($id = null)
    {
        if (!$id) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'ID is required'])
                ->setStatusCode(400);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'catalog_id' => 'required|integer',
            'title'      => 'required|string',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response
                ->setJSON(['error' => true, 'message' => $validation->getErrors()])
                ->setStatusCode(400);
        }

        $db      = db_connect();
        $builder = $db->table('serial_articles');

        $existing = $builder->where('id', $id)->get()->getRow();
        if (!$existing) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'Artikel tidak ditemukan'])
                ->setStatusCode(404);
        }

        $catalog_id = $this->request->getPost('catalog_id');

        // Mapping data update langsung di sini (Pengganti _buildArtikelData)
        $update_data = [
            'Catalog_id'                  => $catalog_id,
            'Title'                       => $this->request->getPost('title'),
            'Creator'                     => $this->request->getPost('creator_final'),
            'Contributor'                 => $this->request->getPost('contributor_final'),
            'StartPage'                   => $this->request->getPost('start_page'),
            'Pages'                       => $this->request->getPost('pages'),
            'Subject'                     => $this->request->getPost('subject_final'),
            'EDISISERIAL'                 => $this->request->getPost('edisi_serial'),
            'TANGGAL_TERBIT_EDISI_SERIAL' => $this->request->getPost('tanggal_terbit'),
            'ISOPAC'                      => $this->request->getPost('isopac') ? 1 : 0,
            'UpdateBy'                    => user_id(),
        ];

        try {
            $builder->where('id', $id)->update($update_data);
        } catch (\Exception $e) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'Artikel gagal disimpan: ' . $e->getMessage()])
                ->setStatusCode(500);
        }

        set_message('toastr_msg', 'Artikel berhasil diperbarui');
        set_message('toastr_type', 'success');

        return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=artikel'));
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete_artikel($id = null)
    {
        if (!$id) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'ID is required'])
                ->setStatusCode(400);
        }

        $db      = db_connect();
        $builder = $db->table('serial_articles');
        $artikel = $builder->where('id', $id)->get()->getRow();

        if (!$artikel) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'Artikel tidak ditemukan'])
                ->setStatusCode(404);
        }

        try {
            $builder->where('id', $id)->delete();
            return $this->response->setJSON([
                'error'   => false,
                'message' => 'Artikel berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return $this->response
                ->setJSON(['error' => true, 'message' => 'Gagal menghapus artikel: ' . $e->getMessage()])
                ->setStatusCode(500);
        }
    }
}