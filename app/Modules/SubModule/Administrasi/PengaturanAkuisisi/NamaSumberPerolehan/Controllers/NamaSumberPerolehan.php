<?php

namespace NamaSumberPerolehan\Controllers;

/**
 * Master Nama Sumber Perolehan (tabel partners): rekanan/penerbit/donatur asal koleksi,
 * dipakai di eksemplar lewat collections.Partner_id.
 */
class NamaSumberPerolehan extends \Base\Controllers\BaseController
{
    public $namaSumberPerolehanModel;

    function __construct()
    {
        $this->namaSumberPerolehanModel = new \NamaSumberPerolehan\Models\NamaSumberPerolehanModel();
    }

    public function index()
    {
        if (!is_allowed('master-nama-sumber-perolehan/index', null, false)) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('dashboard');
        }
        $this->data['title'] = 'Nama Sumber Perolehan';
        return view('NamaSumberPerolehan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('master-nama-sumber-perolehan/delete', null, false)) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('master-nama-sumber-perolehan');
        }

        $partner = $this->namaSumberPerolehanModel->find($id);
        if (!$partner) {
            set_message('toastr_msg', 'Nama Sumber Perolehan tidak ditemukan');
            set_message('toastr_type', 'error');
            return redirect()->to('master-nama-sumber-perolehan');
        }

        // Jangan sampai eksemplar menunjuk ke sumber perolehan yang sudah tidak ada
        $eksemplar = db_connect()->table('collections')->where('Partner_id', $id)->countAllResults();
        if ($eksemplar > 0) {
            set_message('toastr_msg', 'Tidak dapat dihapus: masih dipakai ' . number_format($eksemplar, 0, ',', '.') . ' eksemplar');
            set_message('toastr_type', 'warning');
            return redirect()->to('master-nama-sumber-perolehan');
        }

        if ($this->namaSumberPerolehanModel->delete($id)) {
            set_message('toastr_msg', 'Nama Sumber Perolehan berhasil dihapus');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Nama Sumber Perolehan gagal dihapus');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-nama-sumber-perolehan');
    }
}
