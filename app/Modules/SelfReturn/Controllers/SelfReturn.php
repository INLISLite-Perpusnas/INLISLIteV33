<?php

namespace SelfReturn\Controllers;

class SelfReturn extends \App\Controllers\BaseController 
{
    protected $db;
    protected $session;
    protected $data = [];
    protected $language;
    
    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');
        
        $this->db = \Config\Database::connect('data');
        $this->session = service('session');
    }
    
    public function index()
    {
        // Get barcode parameter from URL
        $this->data['title'] = 'Pengembalian Mandiri';
        $nomorBarcode = $this->request->getGet('NomorBarcode');
        
        // Initialize data
        $this->data['nomorBarcode'] = $nomorBarcode;
        
        // Get location data from cookie
        $locationId = $this->request->getCookie('Location_id');
        if (!$locationId) {
            return redirect()->to('buku-tamu/lokasi');
        }
        
        return view('SelfReturn\Views\simple_return_view', $this->data);
    }
    
    public function processReturn()
    {
        try {
            $nomorBarcode = $this->request->getPost('nomorBarcode');
            
            if (empty($nomorBarcode)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nomor barcode tidak boleh kosong'
                ]);
            }
            
            // Start transaction
            $this->db->transStart();
            
            // Find the book collection
            $collection = $this->db->table('collections')
                ->select('collections.*, catalogs.Title, catalogs.Author')
                ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left')
                ->where('collections.NomorBarcode', $nomorBarcode)
                ->get()
                ->getRow();
            
            if (!$collection) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Buku dengan barcode tersebut tidak ditemukan'
                ]);
            }
            
            // Check if book is currently on loan
            $loanItem = $this->db->table('collectionloanitems')
                ->where('Collection_id', $collection->ID)
                ->where('LoanStatus', 'Loan')
                ->where('ActualReturn IS NULL')
                ->get()
                ->getRow();
            
            if (!$loanItem) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Buku ini tidak sedang dipinjam atau sudah dikembalikan'
                ]);
            }
            
            $returnDate = date('Y-m-d H:i:s');
            $dueDate = new \DateTime($loanItem->DueDate);
            $actualReturnDate = new \DateTime($returnDate);
            
            // Calculate late days
            $lateDays = 0;
            if ($actualReturnDate > $dueDate) {
                $lateDays = $actualReturnDate->diff($dueDate)->days;
            }
            
            // Update loan item
            $this->db->table('collectionloanitems')
                ->where('ID', $loanItem->ID)
                ->update([
                    'ActualReturn' => $returnDate,
                    'LateDays' => $lateDays,
                    'LoanStatus' => 'Return',
                    'UpdateBy' => 1,
                    'UpdateDate' => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);
            
            // Update collection status to available
            $this->db->table('collections')
                ->where('ID', $collection->ID)
                ->update([
                    'Status_id' => 1, // Available status
                    'UpdateBy' => 1,
                    'UpdateDate' => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);
            
            // Update collection loan return count
            $this->db->table('collectionloans')
                ->where('ID', $loanItem->CollectionLoan_id)
                ->set('ReturnCount', 'ReturnCount + 1', false)
                ->set('UpdateBy', 1)
                ->set('UpdateDate', $returnDate)
                ->set('UpdateTerminal', $this->request->getIPAddress())
                ->update();
            
            // Complete transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat memproses pengembalian'
                ]);
            }
            
            $response = [
                'status' => 'success',
                'message' => 'Buku berhasil dikembalikan',
                'data' => [
                    'title' => $collection->Title,
                    'author' => $collection->Author,
                    'barcode' => $nomorBarcode,
                    'return_date' => $returnDate,
                    'due_date' => $loanItem->DueDate,
                    'late_days' => $lateDays,
                    'is_late' => $lateDays > 0
                ]
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
    
    public function checkBook()
    {
        $nomorBarcode = $this->request->getPost('nomorBarcode');
        
        if (empty($nomorBarcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor barcode tidak boleh kosong'
            ]);
        }
        
        // Find the book collection with loan information
        $query = $this->db->table('collections c')
            ->select('c.*, cat.Title, cat.Author, cli.DueDate, cli.LoanDate, cli.LoanStatus')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->join('collectionloanitems cli', 'cli.Collection_id = c.ID AND cli.LoanStatus = "Loan" AND cli.ActualReturn IS NULL', 'left')
            ->where('c.NomorBarcode', $nomorBarcode)
            ->get();
        
        $book = $query->getRow();
        
        if (!$book) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Buku dengan barcode tersebut tidak ditemukan'
            ]);
        }
        
        $response = [
            'status' => 'success',
            'data' => [
                'title' => $book->Title,
                'author' => $book->Author,
                'barcode' => $nomorBarcode,
                'is_on_loan' => !empty($book->LoanStatus),
                'loan_date' => $book->LoanDate,
                'due_date' => $book->DueDate,
                'status_id' => $book->Status_id
            ]
        ];
        
        if (!empty($book->LoanStatus)) {
            $dueDate = new \DateTime($book->DueDate);
            $today = new \DateTime();
            $response['data']['is_overdue'] = $today > $dueDate;
            $response['data']['days_overdue'] = $today > $dueDate ? $today->diff($dueDate)->days : 0;
        }
        
        return $this->response->setJSON($response);
    }
    
    public function getReturnHistory()
    {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        
        $query = $this->db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.LoanStatus', 'Return')
            ->orderBy('cli.ActualReturn', 'DESC')
            ->limit($limit, $offset);
        
        $history = $query->get()->getResult();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $history
        ]);
    }
}