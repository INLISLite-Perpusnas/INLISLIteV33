<?php

namespace Katalog\Controllers;

use Base\Models\DataModel;

/**
 * KatalogController
 * * Menangani: tampilan daftar, karantina, status OPAC,
 * hapus permanen, dan toggle status.
 */
class KatalogController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // INDEX & LIST
    // ----------------------------------------------------------------

    public function index()
    {
        $data['title'] = 'Daftar Katalog';
        echo view('Katalog\Views\list', $data);
    }

    public function karantina()
    {
        $data['title'] = 'Karantina Katalog';
        echo view('Katalog\Views\list_karantina', $data);
    }

    // ----------------------------------------------------------------
    // KARANTINA
    // ----------------------------------------------------------------

    public function proses_karantina()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsQUARANTINE' => 1];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil ditambahkan ke Troli Karantina');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dikarantina terlebih dahulu');
        }

        return redirect()->back();
    }

    public function pulihkan_katalog()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsQUARANTINE' => 0];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil dipulihkan ke daftar katalog');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dipulihkan terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // OPAC
    // ----------------------------------------------------------------

    public function proses_opac()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsOPAC' => 1];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil ditampilkan ke OPAC');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan ditampilkan ke OPAC terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id = 0)
    {
        // ← cek apakah bulk delete dari query string
        $ids = $this->request->getGet('check_data');

        // ← single delete dari URL parameter
        if ($id) {
            $ids = [$id];
        }

        if (empty($ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada katalog yang dipilih');
            return redirect()->to('katalog');
        }

        $this->db->transStart();

        foreach ($ids as $catalog_id) {
            $catalog_id = (int) $catalog_id;
            if ($catalog_id > 0) {
                $this->katalogModel->delete($catalog_id);
                $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === true) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Katalog berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Katalog gagal dihapus');
        }

        return redirect()->to('katalog');
    }

    public function hapus_permanen()
    {
        // 1. Ambil ID dari request (get atau post)
        $IDs = $this->request->getVar('ID');
        $ids_to_delete = [];

        if (!empty($IDs) && is_array($IDs)) {
            // 2. Loop untuk memastikan data bersih dan masuk ke array penampung
            foreach ($IDs as $ID) {
                $ids_to_delete[] = $ID;
            }

            // 3. Eksekusi penghapusan
            // Hapus detailnya dulu (Ruas) agar tidak ada data yatim (orphaned data)
            $this->katalogRuasModel->whereIn('CatalogId', $ids_to_delete)->delete();
            
            // Hapus data utama (Katalog)
            // Memberikan array ID langsung ke fungsi delete() adalah cara batch delete di CI4
            $this->katalogModel->delete($ids_to_delete);

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil dihapus permanen');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dihapus permanen terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // STATUS
    // ----------------------------------------------------------------

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $updated = $this->katalogModel->update($id, [$field => $value]);

        if ($updated) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Katalog berhasil disimpan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Katalog gagal disimpan');
        }

        return redirect()->to('katalog');
    }

    // ----------------------------------------------------------------
    // EDISI SERIAL
    // ----------------------------------------------------------------

    public function deleteEdisiSerial(int $id, int $catalog_id)
    {
        $data = $this->edisiSerialModel->find($id);

        if ($data) {
            $this->edisiSerialModel->delete($id);
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Edisi serial berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data edisi serial tidak ditemukan (ID: ' . $id . ')');
        }

        return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=edisi_serial'));
    }
}