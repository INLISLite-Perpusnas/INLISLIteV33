<?php

namespace Sumbangan\Controllers;

use \CodeIgniter\Files\File;

class Sumbangan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $sumbanganModel;
    public $anggotaModel;
    public $uploadPath;
    public $modulePath;
   

    function __construct()
    {
        $this->sumbanganModel = new \Sumbangan\Models\SumbanganModel();
        $this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/sumbangan/';
        helper(['reference']);
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $query = $this->sumbanganModel
            ->select('sumbangan.*')
            ->select('Member_id.Fullname as nama')
            ->select('Member_id.MemberNo as MembersNo')
            ->join('members Member_id', 'Member_id.ID = sumbangan.Member_id', 'left');

        $sumbangans = $query->findAll();


        $this->data['title'] = 'Sumbangan';
        $this->data['sumbangans'] = $sumbangans;
        echo view('Sumbangan\Views\list', $this->data);
    }

    public function create()
    {
        $db = db_connect($DBGroup = 'data');
        $builder = $db->table('members')->select('ID,Fullname,MemberNo,NoHp,Email,JenisAnggota_id,EndDate,Address');
        $anggotas = $builder->get()->getResult();
        $this->data['title'] = 'Tambah Sumbangan';
        $this->data['anggotas'] = $anggotas;



        $this->validation->setRule('Member_id', 'Nama Anggota', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $save_data = [
                'Member_id' => $this->request->getPost('Member_id'),
                'Jumlah' => $this->request->getPost('Jumlah'),
                'Keterangan' => $this->request->getPost('Keterangan'),
                'CreateBy' => user_id(),
            ];

            $newSumbanganId = $this->sumbanganModel->insert($save_data);

            if ($newSumbanganId) {
                set_message('toastr_msg', lang('Sumbangan.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/sumbangan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Sumbangan.info.failed_saved'));
                echo view('Sumbangan\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('sumbangan/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Sumbangan\Views\add', $this->data);
        }
    }

    public function edit(int $id = 0)
    {
        // 1. Ambil data sumbangan yang akan diedit
        $sumbangan = $this->sumbanganModel->find($id);
     
        if (!$sumbangan) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

        // 2. Ambil data members untuk dropdown (sama seperti fungsi create)
        $db = db_connect($DBGroup = 'data');
        $anggotas = $db->table('members')
                       ->select('ID,Fullname,MemberNo')
                       ->get()
                       ->getResult();
    $this->data['title'] = 'Ubah Sumbangan';
    $this->data['sumbangan'] = $sumbangan;
    $this->data['anggotas'] = $anggotas;

    // 3. Set Rules (Samakan dengan create)
    $this->validation->setRule('Member_id', 'Nama Anggota', 'required');
    
    if ($this->request->getPost()) {
        if ($this->validation->withRequest($this->request)->run()) {
            
            $update_data = [
                'Member_id'  => $this->request->getPost('Member_id'),
                'Jumlah'     => $this->request->getPost('Jumlah'),
                'Keterangan' => $this->request->getPost('Keterangan'), // pastikan huruf kecil/besar sesuai DB
                // 'UpdatedBy' => user_id(), // Jika ada field updated_by
            ];

            $sumbanganUpdate = $this->sumbanganModel->update($id, $update_data);

            if ($sumbanganUpdate) {
                set_message('toastr_msg', lang('Sumbangan.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/sumbangan');
            } else {
                set_message('message', lang('Sumbangan.info.failed_saved'));
                // Lanjut ke view di bawah
            }
        }
    }

    $this->data['redirect'] = base_url('sumbangan/edit/' . $id);
    set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
    
    echo view('Sumbangan\Views\update', $this->data);
}

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/sumbangan');
        }
        $sumbanganDelete = $this->sumbanganModel->delete($id);
        if ($sumbanganDelete) {
            set_message('toastr_msg', lang('Sumbangan.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/sumbangan');
        } else {
            set_message('toastr_msg', lang('Sumbangan.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Sumbangan.info.failed_deleted'));
            return redirect()->to('/sumbangan/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $sumbanganUpdate = $this->sumbanganModel->update($id, array($field => $value));

        if ($sumbanganUpdate) {
            set_message('toastr_msg', ' Sumbangan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Sumbangan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/sumbangan');
    }
}
