<?php

namespace Pelanggaran\Controllers;

use \CodeIgniter\Files\File;

class Pelanggaran extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $pelanggaranModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->pelanggaranModel = new \Pelanggaran\Models\PelanggaranModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/pelanggaran/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('reference');
        helper('pelanggaran');
    }
    public function index()
    {
        // $db = db_connect('data');
        // $data = get_ref_table('collectionloanitems','ID, LoanDate, DueDate, ActualReturn','ID=1','inlis', true);
        // dd($data);
        // $abc = late_days($data->ActualReturn, $data->DueDate);
        // dd($abc);
        $this->data['title'] = 'Pelanggaran';
        echo view('Pelanggaran\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('pelanggaran');
        }
        $pelanggaranDelete = $this->pelanggaranModel->delete($id);
        if ($pelanggaranDelete) {
            set_message('toastr_msg', 'Pelanggaran berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('pelanggaran');
        } else {
            set_message('toastr_msg', 'Pelanggaran gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Pelanggaran gagal dihapus');
            return redirect()->to('pelanggaran');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $pelanggaranUpdate = $this->pelanggaranModel->update($id, array($field => $value));

        if ($pelanggaranUpdate) {
            set_message('toastr_msg', 'Pelanggaran berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Pelanggaran gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/pelanggaran');
    }
}
