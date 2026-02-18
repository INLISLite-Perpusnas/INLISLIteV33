<?php

namespace PerpanjanganAnggota\Controllers;

use \CodeIgniter\Files\File;

class PerpanjanganAnggota extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    // public $anggotaModel;
    public $anggotaModel;
    public $perpanjanganModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->perpanjanganModel = new \PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
        $this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/perpanjangan-anggota/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
        helper('adminigniter');
        helper('reference');
        helper('anggota');
        helper('tgl_indo');
        helper('url');
        helper('thumbnail');
    }
    public function index()
    {
        $query = $this->perpanjanganModel
            ->select('member_perpanjangan.*, members.Fullname as nama, members.MemberNo as MembersNo')
            ->join('members', 'members.ID = member_perpanjangan.Member_id', 'left')
            ->orderBy('member_perpanjangan.ID', 'DESC'); // This line sorts the results by ID in descending order

        // Eksekusi query
        $perpanjangans = $query->findAll();

        // Data untuk view
        $this->data['title'] = 'Daftar Perpanjangan Anggota';
        $this->data['perpanjangans'] = $perpanjangans;

        return view('PerpanjanganAnggota\Views\list', $this->data);
    }
    public function create()
    {
        $this->data['title'] = 'Tambah PerpanjanganAnggota';

        $this->validation->setRules([
            'Member_id' => ['label' => 'Anggota', 'rules' => 'required'],
            'EndDate' => ['label' => 'Tanggal Berakhir', 'rules' => 'required'],
            'Jenisanggota_id' => ['label' => 'Jenis Anggota', 'rules' => 'required'],
        ]);
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $member_ids = (array) $this->request->getPost('Member_id');
            $biaya = $this->request->getPost('biaya');
            $keterangan = $this->request->getPost('Keterangan');
            $endDate = $this->request->getPost('EndDate');
            $jenisId = $this->request->getPost('Jenisanggota_id');
            $fakultasId = $this->request->getPost('Fakultas_id');
            $jurusanId = $this->request->getPost('Jurusan_id');

            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($member_ids as $id_anggota) {
                $save_data = [
                    'Member_id' => $id_anggota,
                    'biaya' => $biaya,
                    'Keterangan' => $keterangan,
                    'Branch_id' => branch_id(),
                    'UpdateBy' => user_id(),
                ];

                $newPerpanjanganId = $this->perpanjanganModel->insert($save_data, true); // true = return insert ID

                if ($newPerpanjanganId !== false && $newPerpanjanganId > 0) {
                    $data = [
                        'EndDate' => $endDate,
                        'Jenisanggota_id' => $jenisId
                    ];

                    if ($jenisId == '12') {
                        $data['Fakultas_id'] = $fakultasId;
                        $data['Jurusan_id'] = $jurusanId;
                    }

                    $this->anggotaModel->update($id_anggota, $data);
                }
            }

            $db->transComplete();

            if ($db->transStatus() !== false) {
                set_message('toastr_msg', lang('PerpanjanganAnggota.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/perpanjangan-anggota');
            } else {
                set_message('toastr_msg', lang('PerpanjanganAnggota.info.failed_saved'));
                set_message('toastr_type', 'error');
                set_message('message', lang('PerpanjanganAnggota.info.failed_saved'));
                echo view('PerpanjanganAnggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('perpanjangan-anggota/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('PerpanjanganAnggota\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah PerpanjanganAnggota';
        $perpanjangananggota = $this->anggotaModel->find($id);
        $this->data['perpanjangananggota'] = $perpanjangananggota;

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $slug,
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user_id(),
                ];

                $perpanjanganUpdate = $this->anggotaModel->update($id, $update_data);

                if ($perpanjanganUpdate) {
                    set_message('toastr_msg', 'PerpanjanganAnggota berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/perpanjangan-anggota');
                } else {
                    set_message('toastr_msg', 'PerpanjanganAnggota gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'PerpanjanganAnggota gagal diubah');
                    return redirect()->to('/perpanjangan-anggota/edit/' . $id);
                }
            }
        }


        $this->data['redirect'] = base_url('perpanjangan-anggota/edit/' . $id);
        echo view('PerpanjanganAnggota\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/perpanjangan-anggota');
        }
        $perpanjanganDelete = $this->anggotaModel->delete($id);
        if ($perpanjanganDelete) {
            set_message('toastr_msg', lang('PerpanjanganAnggota.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/perpanjangan-anggota');
        } else {
            set_message('toastr_msg', lang('PerpanjanganAnggota.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('PerpanjanganAnggota.info.failed_deleted'));
            return redirect()->to('/perpanjangan-anggota/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $perpanjanganUpdate = $this->anggotaModel->update($id, array($field => $value));

        if ($perpanjanganUpdate) {
            set_message('toastr_msg', ' PerpanjanganAnggota berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' PerpanjanganAnggota gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/perpanjangan-anggota');
    }
}