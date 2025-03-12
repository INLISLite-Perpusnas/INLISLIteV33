<?php

namespace JenisPerpustakaan\Controllers;

use \CodeIgniter\Files\File;

class JenisPerpustakaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisperpustakaanModel;
    public $memberformModel;
    public $memberfieldModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisperpustakaanModel = new \JenisPerpustakaan\Models\JenisPerpustakaanModel();
        $this->memberformModel = new \JenisPerpustakaan\Models\MemberFormModel();
        $this->memberfieldModel = new \JenisPerpustakaan\Models\MemberFieldModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisperpustakaan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
        helper('jenis_perpustakaan');
    }

    public function index()
    {
        $this->data['title'] = 'Jenis Perpustakaan';
        echo view('JenisPerpustakaan\Views\list', $this->data);
    }

    public function form(int $id = 0)
    {
        $this->data['title'] = 'Form Anggota';
        $db = db_connect('data');
        $builder = $db->table('members_form as a')
            ->select('a.ID as form_id, b.id as field_id, b.name as field_name, b.mandatory ')
            ->join('member_fields as b', 'b.id = a.Member_Field_id')
            ->where('a.Jenis_Perpustakaan_id', $id);
        $data = $builder->get()->getResult();

        echo view('JenisPerpustakaan\Views\form', $this->data);
    }


    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-perpustakaan');
        }
        $jenisperpustakaanDelete = $this->jenisperpustakaanModel->delete($id);
        if ($jenisperpustakaanDelete) {
            set_message('toastr_msg', 'Jenis Perpustakaan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-perpustakaan');
        } else {
            set_message('toastr_msg', 'Jenis Perpustakaan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Perpustakaan gagal dihapus');
            return redirect()->to('master-jenis-perpustakaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $jenisperpustakaanUpdate = $this->jenisperpustakaanModel->update($id, array($field => $value));
        if ($jenisperpustakaanUpdate) {
            set_message('toastr_msg', 'Jenis Perpustakaan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Perpustakaan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-perpustakaan');
    }
}
