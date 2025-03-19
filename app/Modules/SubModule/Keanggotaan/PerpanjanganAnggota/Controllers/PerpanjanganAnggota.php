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
        $this->modulePath = ROOTPATH . 'public/uploads/perpanjangananggota/';

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
            ->select('b.ID as Branch_id, b.Name as Perpustakaan, b.Name, b.Code, b.NPP_Provinsi_id, b.NPP_KabKota_id, b.NPP_Kecamatan_id, b.NPP_Kelurahan_id, b.NPP_id')
            ->join('members', 'members.ID = member_perpanjangan.Member_id', 'left')
            ->join('branchs b', 'b.ID = member_perpanjangan.Branch_id', 'inner'); // INNER JOIN ke branchs
    
        // Filter berdasarkan kategori user
        if (user()->category == 'admin') {
            // Tidak ada filter tambahan
        } elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
            $npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
            $query->where('b.NPP_Provinsi_id', $npp_provinsi_id);
        } elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
            $npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
            $query->where('b.NPP_KabKota_id', $npp_kabkota_id);
        } elseif (user()->category == 'sa_umum') {
            $query->where('member_perpanjangan.Branch_id', branch_id());
        } else {
            $query->where('member_perpanjangan.Branch_id', branch_id());
        }
    
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

        $this->validation->setRule('t_anggota_id', 't_anggota_id', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $id_anggota = $this->request->getPost('Member_id');

            $save_data = [
                'Member_id' =>  $id_anggota,
                'biaya' => $this->request->getPost('biaya'),
                'Keterangan' => $this->request->getPost('Keterangan'),
                'Branch_id'=> branch_id(),
                'UpdateBy' => user_id(),
            ];
            //  dd($save_data);
            $newPerpanjanganId = $this->perpanjanganModel->insert($save_data);
            // dd($newPerpanjanganId);
            if ($newPerpanjanganId) {
                $data = [
                    'EndDate' => $this->request->getPost('EndDate'),
                    'Jenisanggota_id' => $this->request->getPost('Jenisanggota_id')
                ];
                // $query = $this->anggotaModel->where('id', $id);
                $this->anggotaModel->update($id_anggota, $data);
            }

            if ($newPerpanjanganId) {
                set_message('toastr_msg', lang('PerpanjanganAnggota.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/perpanjangananggota');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('PerpanjanganAnggota.info.failed_saved'));
                echo view('PerpanjanganAnggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('perpanjangananggota/create');
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
                    return redirect()->to('/perpanjangananggota');
                } else {
                    set_message('toastr_msg', 'PerpanjanganAnggota gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'PerpanjanganAnggota gagal diubah');
                    return redirect()->to('/perpanjangananggota/edit/' . $id);
                }
            }
        }


        $this->data['redirect'] = base_url('perpanjangananggota/edit/' . $id);
        echo view('PerpanjanganAnggota\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/perpanjangananggota');
        }
        $perpanjanganDelete = $this->anggotaModel->delete($id);
        if ($perpanjanganDelete) {
            set_message('toastr_msg', lang('PerpanjanganAnggota.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/perpanjangananggota');
        } else {
            set_message('toastr_msg', lang('PerpanjanganAnggota.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('PerpanjanganAnggota.info.failed_deleted'));
            return redirect()->to('/perpanjangananggota/delete/' . $id);
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
        return redirect()->to('/perpanjangananggota');
    }
}