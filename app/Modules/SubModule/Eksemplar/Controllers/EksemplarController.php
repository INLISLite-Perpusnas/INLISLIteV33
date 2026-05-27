<?php

namespace Eksemplar\Controllers;

/**
 * EksemplarController
 *
 * Menangani: tampilan daftar, karantina, status OPAC,
 * hapus permanen, pulihkan, dan delete.
 */
class EksemplarController extends \Base\Controllers\BaseController
{
    use EksemplarBase;

    function __construct()
    {
        $this->initEksemplarBase();
    }

    // ----------------------------------------------------------------
    // INDEX & LIST
    // ----------------------------------------------------------------

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

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

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

    // ----------------------------------------------------------------
    // KARANTINA
    // ----------------------------------------------------------------

    public function proses_karantina()
    {
        $eksemplar_ids = $this->request->getPost('eksemplar_ids');

        if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
            $eksemplar_ids = array_map('trim', explode(',', $eksemplar_ids));
        }

        if (empty($eksemplar_ids) || !is_array($eksemplar_ids) || !array_filter($eksemplar_ids, 'is_numeric')) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih atau data tidak valid');
            return redirect()->back();
        }

        $db = db_connect();
        try {
            $builder = $db->table('collections');
            $builder->whereIn('ID', $eksemplar_ids);
            $builder->update(['IsQUARANTINE' => 1]);

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
        $form          = $this->request->getVar();

        if (isset($form['ID']) && is_array($form['ID'])) {
            $eksemplar_ids = $form['ID'];
        }

        if (empty($eksemplar_ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih');
            return redirect()->back();
        }

        try {
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

    // ----------------------------------------------------------------
    // OPAC
    // ----------------------------------------------------------------

    public function proses_opac()
    {
        $eksemplar_ids = $this->request->getPost('eksemplar_ids');

        if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
            $eksemplar_ids = explode(',', $eksemplar_ids);
            $eksemplar_ids = array_map('trim', $eksemplar_ids);
        }

        if (empty($eksemplar_ids) || !is_array($eksemplar_ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada eksemplar yang dipilih');
            return redirect()->back();
        }

        try {
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
}
