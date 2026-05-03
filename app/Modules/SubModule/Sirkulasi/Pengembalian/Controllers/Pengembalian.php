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
// Tambahkan di dalam class Pengembalian

    public function getJenisPelanggaran()
    {
        $data = $this->db->table('jenis_pelanggaran')
            ->select('ID, JenisPelanggaran')
            ->where('active', 1)
            ->get()->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function getJenisDenda()
    {
        $data = $this->db->table('jenis_denda')
            ->select('ID, Name')
            ->where('active', 1)
            ->get()->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }
    public function processReturn()
{
    try {
        $itemIdsJson   = $this->request->getPost('item_ids');
        $processVio    = $this->request->getPost('process_violation'); // '1' atau '0'
        $vioDataJson   = $this->request->getPost('violation_data');    // JSON object

        if (empty($itemIdsJson)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk dikembalikan']);
        }

        $itemIds = json_decode($itemIdsJson, true);
        if (!is_array($itemIds) || count($itemIds) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
        }

        $violationData = [];
        if ($processVio === '1' && !empty($vioDataJson)) {
            $violationData = json_decode($vioDataJson, true);
        }

        $this->db->transStart();

        $loanItems = $this->db->table('collectionloanitems')
            ->whereIn('ID', $itemIds)
            ->where('LoanStatus', 'Loan')
            ->where('ActualReturn IS NULL')
            ->get()->getResult();

        if (count($loanItems) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku sudah dikembalikan atau tidak ditemukan']);
        }

        $returnDate       = date('Y-m-d H:i:s');
        $returnedCount    = 0;
        $violationCount   = 0;
        $collectionLoanId = null;
        $memberId         = null;

        foreach ($loanItems as $item) {
            if (!$collectionLoanId) $collectionLoanId = $item->CollectionLoan_id;
            if (!$memberId)         $memberId         = $item->member_id;

            $dueDate           = new \DateTime($item->DueDate);
            $actualReturnDate  = new \DateTime($returnDate);
            $isOverdue         = $actualReturnDate > $dueDate;
            $lateDays          = $isOverdue ? $actualReturnDate->diff($dueDate)->days : 0;

            // Update loan item
            $this->db->table('collectionloanitems')
                ->where('ID', $item->ID)
                ->update([
                    'ActualReturn'   => $returnDate,
                    'LateDays'       => $lateDays,
                    'LoanStatus'     => 'Return',
                    'UpdateBy'       => user_id(),
                    'UpdateDate'     => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);

            // Update status koleksi ke Available
            $this->db->table('collections')
                ->where('ID', $item->Collection_id)
                ->update([
                    'Status_id'      => 1,
                    'UpdateBy'       => user_id(),
                    'UpdateDate'     => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);

            // Catat pelanggaran jika buku terlambat dan user memilih proses pelanggaran
            if ($processVio === '1' && $isOverdue && !empty($violationData)) {
                $jumlahDenda = isset($violationData['per_hari']) && $violationData['per_hari']
                    ? ($lateDays * (float)($violationData['jumlah_denda'] ?? 0))
                    : (float)($violationData['jumlah_denda'] ?? 0);

                $this->db->table('pelanggaran')->insert([
                    'CollectionLoan_id'     => $item->CollectionLoan_id,
                    'CollectionLoanItem_id' => $item->ID,
                    'JenisPelanggaran_id'   => $violationData['jenis_pelanggaran_id'] ?? null,
                    'JenisDenda_id'         => $violationData['jenis_denda_id'] ?? null,
                    'JumlahDenda'           => $jumlahDenda,
                    'JumlahSuspend'         => 0,
                    'Paid'                  => 0,
                    'Member_id'             => $item->member_id,
                    'Collection_id'         => $item->Collection_id,
                    'Branch_id'             => null,
                    'active'                => 1,
                    'CreateBy'              => user_id(),
                    'CreateDate'            => $returnDate,
                    'CreateTerminal'        => $this->request->getIPAddress()
                ]);

                $violationCount++;
            }

            $returnedCount++;
        }

        if ($returnedCount > 0 && $collectionLoanId) {
            $this->db->table('collectionloans')
                ->where('ID', $collectionLoanId)
                ->set('ReturnCount', 'ReturnCount + ' . $returnedCount, false)
                ->set('UpdateBy',       user_id())
                ->set('UpdateDate',     $returnDate)
                ->set('UpdateTerminal', $this->request->getIPAddress())
                ->update();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses database']);
        }

        $msg = "<b>$returnedCount buku</b> berhasil dikembalikan!";
        if ($violationCount > 0) {
            $msg .= "<br><small>&#9888; $violationCount pelanggaran keterlambatan telah dicatat.</small>";
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $msg,
            'data'    => ['member_id' => $memberId, 'returned_count' => $returnedCount, 'violation_count' => $violationCount]
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
