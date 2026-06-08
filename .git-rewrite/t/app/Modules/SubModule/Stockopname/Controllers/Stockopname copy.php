<?php

namespace Stockopname\Controllers;

use \CodeIgniter\Files\File;

class Stockopname extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $stockopnameModel;
    public $stockopnamedetailModel;
    public $groupguestModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->stockopnameModel = new \Stockopname\Models\StockopnameModel();
        $this->stockopnamedetailModel = new \Stockopname\Models\StockopnamedetailModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/stockopname/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath, 0755, true);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Stockopname';
        
        // Get search parameters
        $search = $this->request->getGet('search');
        $tahun = $this->request->getGet('tahun');
        $status = $this->request->getGet('status');
        
        // Setup pagination
        $pager = \Config\Services::pager();
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Get filtered data
        $stockopnames = $this->stockopnameModel->getStockopnameWithSearch(
            $search, 
            $tahun, 
            $status, 
            $perPage, 
            $offset
        );
        
        // Get total count for pagination
        $total = $this->stockopnameModel->getStockopnameCount($search, $tahun, $status);
        
        // Manual pagination setup
        $pager->setPath(base_url('stockopname'));
        $pager->store('default', $page, $perPage, $total);
        
        $this->data['stockopnames'] = $stockopnames;
        $this->data['pager'] = $pager;
        $this->data['total'] = $total;
        
        echo view('Stockopname\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Stockopname';
        echo view('Stockopname\Views\create', $this->data);
    }

    public function store()
    {
        // Validation rules
        $validation = \Config\Services::validation();
        
        $rules = [
            'ProjectName' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Nama Projek wajib diisi.',
                    'min_length' => 'Nama Projek minimal 3 karakter.',
                    'max_length' => 'Nama Projek maksimal 255 karakter.'
                ]
            ],
            'Tahun' => [
                'rules' => 'required|integer|greater_than_equal_to[2020]|less_than_equal_to[2030]',
                'errors' => [
                    'required' => 'Tahun wajib diisi.',
                    'integer' => 'Tahun harus berupa angka.',
                    'greater_than_equal_to' => 'Tahun minimal 2020.',
                    'less_than_equal_to' => 'Tahun maksimal 2030.'
                ]
            ],
            'Koordinator' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Koordinator wajib diisi.',
                    'min_length' => 'Nama Koordinator minimal 3 karakter.',
                    'max_length' => 'Nama Koordinator maksimal 255 karakter.'
                ]
            ],
            'TglMulai' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal Mulai wajib diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],
            'Keterangan' => [
                'rules' => 'max_length[1000]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 1000 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }

        // Prepare data for insertion
        $data = [
             'ProjectName' => $this->request->getPost('ProjectName'),
            'Tahun' => $this->request->getPost('Tahun'),
            'Koordinator' => $this->request->getPost('Koordinator'),
            'TglMulai' => $this->request->getPost('TglMulai'),
            'Keterangan' => $this->request->getPost('Keterangan'),
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateBy' => session()->get('user_id') ?? 1
        ];

        try {
            $result = $this->stockopnameModel->insert($data);
            
            if ($result) {
                session()->setFlashdata('message', [
                    'type' => 'success',
                    'text' => 'Data stockopname berhasil disimpan.'
                ]);
                return redirect()->to(base_url('stockopname'));
            } else {
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'Gagal menyimpan data stockopname.'
                ]);
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            return redirect()->back()->withInput();
        }
    }

    public function edit($id = null)
    {
        if (empty($id)) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'ID tidak valid.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        $stockopname = $this->stockopnameModel->find($id);
        
        if (!$stockopname) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Data stockopname tidak ditemukan.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        $this->data['title'] = 'Edit Stockopname';
        $this->data['stockopname'] = $stockopname;
        
        echo view('Stockopname\Views\edit', $this->data);
    }

    public function update($id = null)
    {
        if (empty($id)) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'ID tidak valid.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        $stockopname = $this->stockopnameModel->find($id);
        
        if (!$stockopname) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Data stockopname tidak ditemukan.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        // Validation rules
        $validation = \Config\Services::validation();
        
        $rules = [
            'ProjectName' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Nama Projek wajib diisi.',
                    'min_length' => 'Nama Projek minimal 3 karakter.',
                    'max_length' => 'Nama Projek maksimal 255 karakter.'
                ]
            ],
            'Tahun' => [
                'rules' => 'required|integer|greater_than_equal_to[2020]|less_than_equal_to[2030]',
                'errors' => [
                    'required' => 'Tahun wajib diisi.',
                    'integer' => 'Tahun harus berupa angka.',
                    'greater_than_equal_to' => 'Tahun minimal 2020.',
                    'less_than_equal_to' => 'Tahun maksimal 2030.'
                ]
            ],
            'Koordinator' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Koordinator wajib diisi.',
                    'min_length' => 'Nama Koordinator minimal 3 karakter.',
                    'max_length' => 'Nama Koordinator maksimal 255 karakter.'
                ]
            ],
            'TglMulai' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal Mulai wajib diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],
            'Keterangan' => [
                'rules' => 'max_length[1000]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 1000 karakter.'
                ]
            ],
            'Status' => [
                'rules' => 'required|in_list[Draft,Active,Completed,Cancelled]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list' => 'Status tidak valid.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }

        // Prepare data for update
        $data = [
            'ProjectName' => $this->request->getPost('ProjectName'),
            'Tahun' => $this->request->getPost('Tahun'),
            'Koordinator' => $this->request->getPost('Koordinator'),
            'TglMulai' => $this->request->getPost('TglMulai'),
            'Keterangan' => $this->request->getPost('Keterangan'),
            'UpdateDate' => date('Y-m-d H:i:s'),
            'UpdateBy' => session()->get('user_id') ?? 1
        ];

        try {
            $result = $this->stockopnameModel->update($id, $data);
            
            if ($result) {
                session()->setFlashdata('message', [
                    'type' => 'success',
                    'text' => 'Data stockopname berhasil diperbarui.'
                ]);
                return redirect()->to(base_url('stockopname'));
            } else {
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'Gagal memperbarui data stockopname.'
                ]);
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            return redirect()->back()->withInput();
        }
    }

    public function delete($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid.'
            ]);
        }

        $stockopname = $this->stockopnameModel->find($id);
        
        if (!$stockopname) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data stockopname tidak ditemukan.'
            ]);
        }

        try {
            $result = $this->stockopnameModel->delete($id);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data stockopname berhasil dihapus.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data stockopname.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function detail($id = null)
    {
        if (empty($id)) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'ID tidak valid.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        $stockopnamedetail=$this->stockopnamedetailModel ->select('
                c.NomorBarcode,
                c.CallNumber,
                cat.Title,
                cat.Author,
                cat.Publisher,
                prevLoc.Name as PrevLocationName,
                currLoc.Name as CurrentLocationName,
                prevStatus.Name as PrevStatusName,
                currStatus.Name as CurrentStatusName,
                prevRule.Name as PrevRuleName,
                currRule.Name as CurrentRuleName
            ')
            ->join('collections c', 'CollectionID = c.id', 'left')
            ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
            ->join('locations prevLoc', 'PrevLocationID = prevLoc.ID', 'left')
            ->join('locations currLoc', 'CurrentLocationID = currLoc.ID', 'left')
            ->join('collectionstatus prevStatus', 'PrevStatusID = prevStatus.ID', 'left')
            ->join('collectionstatus currStatus', 'CurrentStatusID = currStatus.ID', 'left')
            ->join('collectionrules prevRule', 'PrevCollectionRuleID = prevRule.ID', 'left')
            ->join('collectionrules currRule', 'CurrentCollectionRuleID = currRule.ID', 'left')
            ->where('StockOpnameID', $id)
            ->orderBy('CreateDate', 'DESC')->findAll();
            dd($stockopnamedetail);
        $this->data['title'] = 'Detail Stockopname';
        $this->data['stockopnamedetail'] = $stockopnamedetail;
        
        echo view('Stockopname\Views\detail', $this->data);
    }

    public function changeStatus($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid.'
            ]);
        }

        $status = $this->request->getPost('status');
        
        if (!in_array($status, ['Draft', 'Active', 'Completed', 'Cancelled'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Status tidak valid.'
            ]);
        }

        $stockopname = $this->stockopnameModel->find($id);
        
        if (!$stockopname) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data stockopname tidak ditemukan.'
            ]);
        }

        try {
            $result = $this->stockopnameModel->update($id, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session()->get('user_id') ?? 1
            ]);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Status berhasil diperbarui.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui status.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}