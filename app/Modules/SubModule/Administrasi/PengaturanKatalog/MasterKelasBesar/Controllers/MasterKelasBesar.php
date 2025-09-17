<?php

namespace MasterKelasBesar\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;

class MasterKelasBesar extends \Base\Controllers\BaseController
{
    use ResponseTrait;
    
    public $auth;
    public $authorize;
    public $masterkelasbesar;

    function __construct()
    {
        $this->masterkelasbesar = new \MasterKelasBesar\Models\MasterKelasBesarModel();
        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
    }

    public function index()
    {
        $this->data['title'] = 'Master Kelas Besar';
        return view('MasterKelasBesar\Views\list', $this->data);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'kdKelas' => 'required|max_length[3]',
                'namakelas' => 'required|max_length[255]',
                'warna' => 'max_length[50]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->fail($validation->getErrors());
            }

            $data = [
                'kdKelas' => $this->request->getPost('kdKelas'),
                'namakelas' => $this->request->getPost('namakelas'),
                'warna' => $this->request->getPost('warna'),
                'CreateBy' => user()->id,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress(),
                'Branch_id' => user()->branch_id ?? 0,
                'active' => 1
            ];

            if ($this->masterkelasbesar->insert($data)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Data berhasil ditambahkan'
                ]);
            } else {
                return $this->fail('Gagal menambahkan data');
            }
        }

        return redirect()->to(base_url('master-kelas-besar'));
    }

    public function detail($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->masterkelasbesar->find($id);
            if ($data) {
                return $this->respond($data);
            } else {
                return $this->failNotFound('Data tidak ditemukan');
            }
        }
        
        return redirect()->to(base_url('master-kelas-besar'));
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'kdKelas' => "required|max_length[3]",
                'namakelas' => 'required|max_length[255]',
                'warna' => 'max_length[50]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->fail($validation->getErrors());
            }

            $data = [
                'kdKelas' => $this->request->getPost('kdKelas'),
                'namakelas' => $this->request->getPost('namakelas'),
                'warna' => $this->request->getPost('warna'),
                'UpdateBy' => user()->id,
                'UpdateDate' => date('Y-m-d H:i:s'),
                'UpdateTerminal' => $this->request->getIPAddress()
            ];

            if ($this->masterkelasbesar->update($id, $data)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return $this->fail('Gagal mengupdate data');
            }
        }

        return redirect()->to(base_url('master-kelas-besar'));
    }

    public function delete($id)
    {
        if ($this->masterkelasbesar->delete($id)) {
            session()->setFlashdata('message', alert_success('Data berhasil dihapus'));
        } else {
            session()->setFlashdata('message', alert_error('Gagal menghapus data'));
        }

        return redirect()->to(base_url('master-kelas-besar'));
    }

  public function apply_status($id)
{
    $field = $this->request->getGet('field');
    $value = $this->request->getGet('value');

    if ($field === 'active') {
        $this->masterkelasbesar->update($id, [
            $field => $value,
            'UpdateBy' => user()->id,
            'UpdateDate' => date('Y-m-d H:i:s'),
            'UpdateTerminal' => $this->request->getIPAddress()
        ]);

        $status = $value == 1 ? 'diaktifkan' : 'dinonaktifkan';
        
        // Return JSON response untuk AJAX
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Data berhasil {$status}.",
                'status' => $status
            ]);
        }
        
        $this->session->setFlashdata('success', "Data berhasil {$status}.");
    }
    
    return redirect()->to(base_url('master-kelas-besar'));
}

    public function datatable($slug = null)
    {
        $db = db_connect();
        $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
        
        $builder = $db->table('master_kelas_besar as a')
            ->select('a.ID, a.ID as action, a.kdKelas, a.namakelas, a.warna, a.active');

        // if ($branch_id) {
        //     $builder->where('a.Branch_id', $branch_id);
        // }

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('kdKelas', function ($row) {
                return '<b>' . $row->kdKelas . '</b>';
            })
            ->edit('warna', function ($row) {
                if ($row->warna) {
                    return '<span class="badge" style="background-color: ' . $row->warna . '; color: white;">' . $row->warna . '</span>';
                }
                return '-';
            })
            ->edit('active', function ($row) {
                if ($row->active == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Nonaktif</span>';
                }
            })
            ->edit('action', function ($row) {
                $edit = '<a href="javascript:void(0);" data-href="' . base_url('master-kelas-besar/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-sm btn-primary show-data"><i class="pe-7s-note"></i></a>';
                
                if ($row->active == 1) {
                    $status = '<a href="' . base_url('master-kelas-besar/apply_status/' . $row->ID . '?field=active&value=0') . '" data-toggle="tooltip" data-placement="top" title="Nonaktifkan" class="btn btn-sm btn-warning"><i class="pe-7s-close"></i></a>';
                } else {
                    $status = '<a href="' . base_url('master-kelas-besar/apply_status/' . $row->ID . '?field=active&value=1') . '" data-toggle="tooltip" data-placement="top" title="Aktifkan" class="btn btn-sm btn-success"><i class="pe-7s-check"></i></a>';
                }
                
                $delete = '<a href="javascript:void(0);" data-href="' . base_url('master-kelas-besar/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-sm btn-danger remove-data"><i class="pe-7s-trash"></i></a>';
                
                return $edit . ' ' . $status . ' ' . $delete;
            })
            ->toJson();
            
        return $dataTable;
    }
}