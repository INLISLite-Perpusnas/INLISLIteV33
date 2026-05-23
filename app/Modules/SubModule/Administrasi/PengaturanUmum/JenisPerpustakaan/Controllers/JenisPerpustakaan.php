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
        $db = db_connect();
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
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-jenis-perpustakaan');
        }
        $jenisperpustakaanDelete = $this->jenisperpustakaanModel->delete($id);
        if ($jenisperpustakaanDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Jenis Perpustakaan berhasil dihapus');
            return redirect()->to('master-jenis-perpustakaan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Jenis Perpustakaan gagal dihapus');
            return redirect()->to('master-jenis-perpustakaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $jenisperpustakaanUpdate = $this->jenisperpustakaanModel->update($id, array($field => $value));
        if ($jenisperpustakaanUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Jenis Perpustakaan berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Jenis Perpustakaan gagal diubah');
        }
        return redirect()->to('master-jenis-perpustakaan');
    }
}
