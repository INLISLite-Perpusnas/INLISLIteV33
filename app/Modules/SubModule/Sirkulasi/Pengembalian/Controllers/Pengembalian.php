<?php

namespace Pengembalian\Controllers;

use \CodeIgniter\Files\File;

class Pengembalian extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $pengembalianModel;
	public $collectionModel;
	public $uploadPath;
	public $modulePath;
	public $collectionLoanModel;
	public $collectionLoanItemModel;
	public $cart;
	public $db;

	function __construct()
	{
		$this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->cart = new \App\Libraries\Cart();
		$this->db = db_connect();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/pengembalian/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();



		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
	}
	public function index()
	{
		$carts = get_cart_return();
		$this->data['carts'] = $carts;

		$this->data['title'] = 'Pengembalian';
		echo view('Pengembalian\Views\list', $this->data);
	}

	public function create()
	{
		// Get barcode parameter from URL
		$this->data['title'] = 'Pengembalian Mandiri';
		$nomorBarcode = $this->request->getGet('NomorBarcode');

		// Initialize data
		$this->data['nomorBarcode'] = $nomorBarcode;


		return view('Pengembalian\Views\add', $this->data);
	}

	public function checkBook()
    {
        $nomorBarcode = $this->request->getPost('nomorBarcode');
        
        if (empty($nomorBarcode)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor barcode tidak boleh kosong']);
        }
        
        // 1. Cari buku yang discan (JOIN dengan tabel members untuk ambil Fullname)
        $scannedItem = $this->db->table('collections c')
            ->select('cli.CollectionLoan_id, cli.member_id, m.Fullname')
            ->join('collectionloanitems cli', 'cli.Collection_id = c.ID AND cli.LoanStatus = "Loan" AND cli.ActualReturn IS NULL', 'inner')
            ->join('members m', 'm.ID = cli.member_id', 'left') // Join tabel member
            ->where('c.NomorBarcode', $nomorBarcode)
            ->get()->getRow();
            
        if (!$scannedItem) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku dengan barcode tersebut tidak ditemukan atau sudah dikembalikan']);
        }

        // 2. Ambil SEMUA buku yang ada dalam transaksi yang sama
        $allItems = $this->db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.CollectionLoan_id', $scannedItem->CollectionLoan_id)
            ->where('cli.LoanStatus', 'Loan')
            ->where('cli.ActualReturn IS NULL')
            ->get()->getResult();

        $today = new \DateTime();
        $formattedItems = [];

        foreach ($allItems as $item) {
            $dueDate = new \DateTime($item->DueDate);
            $isOverdue = $today > $dueDate;
            
            $formattedItems[] = [
                'id' => $item->ID, // Primary key collectionloanitems
                'title' => $item->Title,
                'author' => $item->Author,
                'barcode' => $item->NomorBarcode,
                'loan_date' => $item->LoanDate,
                'due_date' => $item->DueDate,
                'is_overdue' => $isOverdue,
                'days_overdue' => $isOverdue ? $today->diff($dueDate)->days : 0
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'member_id' => $scannedItem->member_id,
                'member_name' => $scannedItem->Fullname, // Kirim nama member
                'collection_loan_id' => $scannedItem->CollectionLoan_id,
                'items' => $formattedItems
            ]
        ]);
    }

    public function processReturn()
    {
        try {
            // Kita terima input berupa JSON Array ID item yang mau dikembalikan
            $itemIdsJson = $this->request->getPost('item_ids');
            
            if (empty($itemIdsJson)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk dikembalikan']);
            }
            
            $itemIds = json_decode($itemIdsJson, true);
            if (!is_array($itemIds) || count($itemIds) === 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
            }
            
            $this->db->transStart();
            
            // Ambil data buku yang akan dikembalikan sesuai ID yang di-passing
            $loanItems = $this->db->table('collectionloanitems')
                ->whereIn('ID', $itemIds)
                ->where('LoanStatus', 'Loan')
                ->where('ActualReturn IS NULL')
                ->get()->getResult();

            if (count($loanItems) === 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Buku sudah dikembalikan atau tidak ditemukan']);
            }

            $returnDate = date('Y-m-d H:i:s');
            $returnedCount = 0;
            $collectionLoanId = null;
            $memberId = null;
            
            foreach ($loanItems as $item) {
                if (!$collectionLoanId) $collectionLoanId = $item->CollectionLoan_id;
                if (!$memberId) $memberId = $item->member_id;

                $dueDate = new \DateTime($item->DueDate);
                $actualReturnDate = new \DateTime($returnDate);
                $lateDays = ($actualReturnDate > $dueDate) ? $actualReturnDate->diff($dueDate)->days : 0;
                
                // Update loan item
                $this->db->table('collectionloanitems')
                    ->where('ID', $item->ID)
                    ->update([
                        'ActualReturn' => $returnDate,
                        'LateDays' => $lateDays,
                        'LoanStatus' => 'Return',
                        'UpdateBy' => user_id(),
                        'UpdateDate' => $returnDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);
                
                // Update collection status ke Available (1)
                $this->db->table('collections')
                    ->where('ID', $item->Collection_id)
                    ->update([
                        'Status_id' => 1, 
                        'UpdateBy' => user_id(),
                        'UpdateDate' => $returnDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);
                
                $returnedCount++;
            }
            
            // Update total pengembalian di tabel induk
            if ($returnedCount > 0 && $collectionLoanId) {
                $this->db->table('collectionloans')
                    ->where('ID', $collectionLoanId)
                    ->set('ReturnCount', 'ReturnCount + ' . $returnedCount, false)
                    ->set('UpdateBy', user_id())
                    ->set('UpdateDate', $returnDate)
                    ->set('UpdateTerminal', $this->request->getIPAddress())
                    ->update();
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses database']);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "<b>$returnedCount buku</b> berhasil dikembalikan!",
                'data' => [
                    'member_id' => $memberId,
                    'returned_count' => $returnedCount
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }


	public function getReturnHistory()
	{
		$limit = $this->request->getGet('limit') ?? 5;
		$offset = $this->request->getGet('offset') ?? 0;
		$member_id = $this->request->getGet('member_id'); // Menangkap member_id

		$query = $this->db->table('collectionloanitems cli')
			->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
			->join('collections c', 'c.ID = cli.Collection_id')
			->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
			->where('cli.LoanStatus', 'Return');

		// Filter history jika member_id tersedia
		if (!empty($member_id)) {
			$query->where('cli.member_id', $member_id);
		}

		$history = $query->orderBy('cli.ActualReturn', 'DESC')
			->limit($limit, $offset)
			->get()->getResult();

		return $this->response->setJSON([
			'status' => 'success',
			'data' => $history
		]);
	}
}
