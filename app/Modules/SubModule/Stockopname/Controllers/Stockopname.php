<?php

namespace Stockopname\Controllers;

use \CodeIgniter\Files\File;

class Stockopname extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $stockopnameModel;
    public $stockopnamedetailModel;
    public $collectionModel;
    public $catalogModel;
    public $locationModel;
    public $collectionstatusModel;
    public $collectionrulesModel;
    public $groupguestModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->stockopnameModel = new \Stockopname\Models\StockopnameModel();
        $this->stockopnamedetailModel = new \Stockopname\Models\StockopnamedetailModel();
        $this->collectionModel = new \Eksemplar\Models\EksemplarModel();
        $this->catalogModel =  new \Katalog\Models\KatalogModel();
        $this->locationModel =new \LokasiRuang\Models\LokasiRuangModel();
 
        
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

    $stockopname = $this->stockopnameModel->find($id);

    if (!$stockopname) {
        session()->setFlashdata('message', [
            'type' => 'error',
            'text' => 'Data stockopname tidak ditemukan.'
        ]);
        return redirect()->to(base_url('stockopname'));
    }
   
    // --- PAGINATION SETUP ---
    $pager = \Config\Services::pager();
   $locationSummary=$this->stockopnamedetailModel->countStockopnameDetailsByLocationAndStatus($id);
    // dd($summary);
   
    // -- Pagination for Stockopname Details --
    $pageDetails = $this->request->getVar('page_details') ? (int)$this->request->getVar('page_details') : 1;
    $perPageDetails = 25; // Items per page for the details table
    $totalDetails = $this->stockopnamedetailModel->getDetailCount($id);
    $offsetDetails = ($pageDetails - 1) * $perPageDetails;
    $details = $this->stockopnamedetailModel->getStockopnameDetails($id, $perPageDetails, $offsetDetails);
    $detailsPager = $pager->makeLinks($pageDetails, $perPageDetails, $totalDetails, 'default_full', 0, 'details');

    // -- Pagination for Collections Not In Stockopname --
    $pageNotIn = $this->request->getVar('page_notIn') ? (int)$this->request->getVar('page_notIn') : 1;
    $perPageNotIn = 10; // Items per page for the "not in" list
    $totalNotIn = $this->stockopnamedetailModel->getCollectionsNotInStockopnameCount($id);
    $offsetNotIn = ($pageNotIn - 1) * $perPageNotIn;
    $collectionsNotInStockopname = $this->stockopnamedetailModel->getCollectionsNotInStockopname($id, $perPageNotIn, $offsetNotIn);
    $notInPager = $pager->makeLinks($pageNotIn, $perPageNotIn, $totalNotIn, 'default_full', 0, 'notIn');


    // Get reference data for dropdowns
    $db = db_connect('data');
    $locations = $this->locationModel->findAll();
    $statuses  = $db->table('collectionstatus')->orderBy('ID')->get()->getResultObject();
    $rules     = $db->table('collectionrules')->orderBy('ID')->get()->getResultObject();

    $this->data['title'] = 'Detail Stockopname - ' . $stockopname->ProjectName;
    $this->data['locationSummary'] = $locationSummary;
    $this->data['stockopname'] = $stockopname;
    $this->data['stockopnamedetailModel'] = $this->stockopnamedetailModel;

    // --- PASS PAGINATION DATA TO VIEW ---
    $this->data['details'] = $details;
    $this->data['detailsPager'] = $detailsPager;

    $this->data['collectionsNotInStockopname'] = $collectionsNotInStockopname;
    $this->data['notInPager'] = $notInPager;
    $this->data['totalNotInStockopname'] = $totalNotIn;

    $this->data['locations'] = $locations;
    $this->data['statuses']  = $statuses;
    $this->data['rules']     = $rules;

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

    // ===================== STOCKOPNAME DETAIL METHODS =====================

    public function scanBarcode()
    {
        $barcode = $this->request->getPost('barcode');
        $stockopnameId = $this->request->getPost('stockopname_id');

        if (empty($barcode) || empty($stockopnameId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode dan ID stockopname harus diisi.'
            ]);
        }

        try {
            // Check if collection exists with this barcode
            $collection = $this->collectionModel->where('NomorBarcode', $barcode)->first();
          
          
            if (!$collection) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Koleksi dengan barcode ini tidak ditemukan.'
                ]);
            }

            // Check if this collection is already in stockopname
            $existingDetail = $this->stockopnamedetailModel->where([
                'StockOpnameID' => $stockopnameId,
                'CollectionID' => $collection->ID
            ])->first();
          

            if ($existingDetail) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Koleksi ini sudah ada dalam stockopname.'
                ]);
            }

            // Get next ID for stockopname detail
            $nextId = $this->stockopnamedetailModel->getNextId();

            // Insert new stockopname detail
            $detailData = [
                'ID' => $nextId,
                'StockOpnameID' => $stockopnameId,
                'CollectionID' => $collection->ID,
                'PrevLocationID' => $collection->Location_id,
                'CurrentLocationID' => $collection->Location_id,
                'PrevStatusID' => $collection->Status_id,
                'CurrentStatusID' => $collection->Status_id,
                'PrevCollectionRuleID' => $collection->Rule_id,
                'CurrentCollectionRuleID' => $collection->Rule_id,
                'CreateBy' => session()->get('user_id') ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ];
        

            $result = $this->stockopnamedetailModel->insert($detailData);

            $insertedDetail = $this->stockopnamedetailModel->getDetailById($nextId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Koleksi berhasil ditambahkan ke stockopname.',
                'data' => $insertedDetail,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDetail()
    {
        $detailId = $this->request->getPost('detail_id');
        $currentLocationId = $this->request->getPost('current_location_id');
        $currentStatusId = $this->request->getPost('current_status_id');
        $currentCollectionRuleId = $this->request->getPost('current_collection_rule_id');

        if (empty($detailId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID detail tidak valid.'
            ]);
        }

        try {
            $detail = $this->stockopnamedetailModel->find($detailId);
            
            if (!$detail) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Detail stockopname tidak ditemukan.'
                ]);
            }

            $updateData = [
                'UpdateBy' => session()->get('user_id') ?? 1,
                'UpdateDate' => date('Y-m-d H:i:s'),
                'UpdateTerminal' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ];

            if (!empty($currentLocationId)) {
                $updateData['CurrentLocationID'] = $currentLocationId;
            }
            
            if (!empty($currentStatusId)) {
                $updateData['CurrentStatusID'] = $currentStatusId;
            }
            
            if (!empty($currentCollectionRuleId)) {
                $updateData['CurrentCollectionRuleID'] = $currentCollectionRuleId;
            }

            $result = $this->stockopnamedetailModel->update($detailId, $updateData);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Detail stockopname berhasil diperbarui.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui detail stockopname.'
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteDetail($detailId = null)
    {
        if (empty($detailId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID detail tidak valid.'
            ]);
        }

        try {
            $detail = $this->stockopnamedetailModel->find($detailId);
            
            if (!$detail) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Detail stockopname tidak ditemukan.'
                ]);
            }

            $result = $this->stockopnamedetailModel->delete($detailId);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Detail stockopname berhasil dihapus.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus detail stockopname.'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getCollectionInfo()
    {
        $barcode = $this->request->getGet('barcode');

        if (empty($barcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode harus diisi.'
            ]);
        }

        try {
            $collection = $this->collectionModel->getCollectionByBarcode($barcode);
            
            if ($collection) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $collection
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Koleksi tidak ditemukan.'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function exportStockopname($id = null)
    {
        if (empty($id)) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'ID tidak valid.'
            ]);
            return redirect()->to(base_url('stockopname'));
        }

        try {
            $stockopname = $this->stockopnameModel->find($id);
            $details = $this->stockopnamedetailModel->getStockopnameDetails($id);

            // Create CSV content
            $csvContent = "Nama Projek,Tahun,Koordinator,Barcode,Judul,Lokasi Sebelum,Lokasi Sekarang,Status Sebelum,Status Sekarang\n";
            
            foreach ($details as $detail) {
                $csvContent .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $stockopname['ProjectName'],
                    $stockopname['Tahun'],
                    $stockopname['Koordinator'],
                    $detail['NomorBarcode'],
                    $detail['Title'],
                    $detail['PrevLocationName'],
                    $detail['CurrentLocationName'],
                    $detail['PrevStatusName'],
                    $detail['CurrentStatusName']
                );
            }

            $filename = 'stockopname_' . $stockopname['ProjectName'] . '_' . date('Y-m-d') . '.csv';
            
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csvContent);

        } catch (\Exception $e) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Gagal mengekspor data: ' . $e->getMessage()
            ]);
            return redirect()->back();
        }
    }

    private function updateCollectionFromStockopname($collectionId, $updateData)
    {
        $collectionUpdateData = [];
        
        if (isset($updateData['CurrentLocationID'])) {
            $collectionUpdateData['location_id'] = $updateData['CurrentLocationID'];
        }
        
        if (isset($updateData['CurrentStatusID'])) {
            $collectionUpdateData['status_id'] = $updateData['CurrentStatusID'];
        }
        
        if (isset($updateData['CurrentCollectionRuleID'])) {
            $collectionUpdateData['collection_rule_id'] = $updateData['CurrentCollectionRuleID'];
        }

        if (!empty($collectionUpdateData)) {
            $collectionUpdateData['updated_at'] = date('Y-m-d H:i:s');
            $collectionUpdateData['updated_by'] = session()->get('user_id') ?? 1;
            
            $this->collectionModel->update($collectionId, $collectionUpdateData);
        }
    }
}