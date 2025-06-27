<?php

namespace ReadOnSpot\Controllers;

class ReadOnSpot extends \App\Controllers\BaseController
{
    public $bacaditempatModel;
    public $uploadPath;
    public $modulePath;
    public $collectionModel;
    public $catalogModel;

    public $data = [];
    public $language;

    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->bacaditempatModel = new \ReadOnSpot\Models\ReadOnSpotModel();
        $this->collectionModel   = new \Peminjaman\Models\CollectionModel();
        $this->catalogModel      = new \Katalog\Models\KatalogModel();

        helper('reference');
        helper('peminjaman');
        helper('pengembalian');
        helper('member');
        helper('lokasiruang');
        helper('home');
    }

    /**
     * Main index page
     */
    public function index()
    {
        // Get location id from cookie
        $locationId = $this->request->getCookie('Location_id');

        // Check if location id is available
        if (!$locationId) {
            return redirect()->to('buku-tamu/lokasi');
        }

        // Get today's data
        $todayData = $this->bacaditempatModel->getTodayData($locationId);

        $this->data['title'] = 'Baca Ditempat';
        $this->data['todayData'] = $todayData;
        $this->data['locationId'] = $locationId;
        
        echo view('ReadOnSpot\Views\index', $this->data);
    }

    /**
     * Add new member data via member number
     */
    public function addByMemberNumber()
    {
        $locationId = $this->request->getCookie('Location_id');
        $memberNumber = $this->request->getPost('member_number');

        if (!$locationId || !$memberNumber) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Find member by number
        $member = $this->getMemberByNumber($memberNumber);
        
        if (!$member) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor anggota tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $member
        ]);
    }

    /**
     * Add book via barcode
     */
    public function addByBarcode()
    {
        $locationId = $this->request->getCookie('Location_id');
        $barcode = $this->request->getPost('barcode');
        $memberNumber = $this->request->getPost('member_number');

        if (!$locationId || !$barcode || !$memberNumber) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Find collection by barcode
        $collection = $this->collectionModel->getByBarcode($barcode);
        
        if (!$collection) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode buku tidak ditemukan'
            ]);
        }

        // Get member data
        $member = $this->getMemberByNumber($memberNumber);
        
        if (!$member) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor anggota tidak ditemukan'
            ]);
        }

        // Save to database
        $data = [
            'NoPengunjung' => $this->generateVisitorNumber(),
            'collection_id' => $collection['ID'],
            'CreateDate' => date('Y-m-d H:i:s'),
            'Member_id' => $member['ID'],
            'Location_Id' => $locationId,
            'Is_return' => '0'
        ];

        $result = $this->bacaditempatModel->insert($data);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data'
            ]);
        }
    }

    /**
     * Get today's reading data via AJAX
     */
    public function getTodayData()
    {
        $locationId = $this->request->getCookie('Location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Location ID tidak ditemukan'
            ]);
        }

        $data = $this->bacaditempatModel->getTodayData($locationId);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Reset form
     */
    public function resetForm()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Form berhasil direset'
        ]);
    }

    /**
     * Helper function to get member by number
     */
    private function getMemberByNumber($memberNumber)
    {
        // Implement based on your member model
        // This is just an example
        $memberModel = new \Member\Models\MemberModel();
        return $memberModel->where('member_number', $memberNumber)->first();
    }

    /**
     * Generate visitor number
     */
    private function generateVisitorNumber()
    {
        $date = date('Ymd');
        $lastNumber = $this->bacaditempatModel
            ->where('DATE(CreateDate)', date('Y-m-d'))
            ->countAllResults();
        
        return $date . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}