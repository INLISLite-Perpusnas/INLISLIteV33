<?php

namespace Perpanjangan\Controllers;

use \CodeIgniter\Files\File;

class Perpanjangan extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $perpanjanganModel;
	public $uploadPath;
	public $modulePath;
	public $collectionModel;
	public $collectionLoanModel;
	public $collectionLoanItemModel;
	public $collectionLoanExtendModel;
	public $cart;

	function __construct()
	{
		$this->perpanjanganModel = new \Perpanjangan\Models\PerpanjanganModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->collectionLoanExtendModel = new \Peminjaman\Models\CollectionLoanExtendModel();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/perpanjangan/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();



		helper('reference');
		helper('peminjaman');
		helper('perpanjangan');
	}
	public function index()
	{
		$carts = get_cart_extend();
		$this->data['carts'] = $carts;

		$this->data['title'] = 'Perpanjangan';
		echo view('Perpanjangan\Views\list', $this->data);
	}



	
public function create()
{
    $this->data['title'] = 'Perpanjangan Mandiri';
    return view('Perpanjangan\Views\add', $this->data);
}

public function checkBook()
{
    $nomorBarcode = $this->request->getPost('nomorBarcode');

    if (empty($nomorBarcode)) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor barcode tidak boleh kosong']);
    }

    $db = db_connect();

    // Cari buku yang sedang dipinjam
    $scannedItem = $db->table('collections c')
        ->select('cli.CollectionLoan_id, cli.member_id, m.Fullname')
        ->join('collectionloanitems cli', 'cli.Collection_id = c.ID AND cli.LoanStatus = "Loan" AND cli.ActualReturn IS NULL', 'inner')
        ->join('members m', 'm.ID = cli.member_id', 'left')
        ->where('c.NomorBarcode', $nomorBarcode)
        ->get()->getRow();

    if (!$scannedItem) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Buku tidak ditemukan atau tidak sedang dalam status pinjam']);
    }

    // Ambil semua buku dalam transaksi yang sama
    $allItems = $db->table('collectionloanitems cli')
        ->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
        ->join('collections c', 'c.ID = cli.Collection_id')
        ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
        ->where('cli.CollectionLoan_id', $scannedItem->CollectionLoan_id)
        ->where('cli.LoanStatus', 'Loan')
        ->where('cli.ActualReturn IS NULL')
        ->get()->getResult();

    $today          = new \DateTime();
    $formattedItems = [];

    foreach ($allItems as $item) {
        $extendCount = $db->table('collectionloanextends')
            ->where('CollectionLoanItem_id', $item->ID)
            ->where('active', 1)
            ->countAllResults();

        $dueDate   = new \DateTime($item->DueDate);
        $isOverdue = $today > $dueDate;

        $formattedItems[] = [
            'id'            => $item->ID,
            'collection_id' => $item->Collection_id,
            'title'         => $item->Title,
            'author'        => $item->Author,
            'barcode'       => $item->NomorBarcode,
            'loan_date'     => $item->LoanDate,
            'due_date'      => $item->DueDate,
            'extend_count'  => $extendCount,
            'is_overdue'    => $isOverdue,
            'days_overdue'  => $isOverdue ? $today->diff($dueDate)->days : 0,
        ];
    }

    return $this->response->setJSON([
        'status' => 'success',
        'data'   => [
            'member_id'          => $scannedItem->member_id,
            'member_name'        => $scannedItem->Fullname,
            'collection_loan_id' => $scannedItem->CollectionLoan_id,
            'items'              => $formattedItems
        ]
    ]);
}

public function processExtend()
{
    try {
        $itemIdsJson = $this->request->getPost('item_ids');
        $extendDays  = (int)($this->request->getPost('extend_days') ?? 7);

        if (empty($itemIdsJson)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk diperpanjang']);
        }

        $itemIds = json_decode($itemIdsJson, true);
        if (!is_array($itemIds) || count($itemIds) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
        }

        $db = db_connect();
        $db->transStart();

        $loanItems = $db->table('collectionloanitems')
            ->whereIn('ID', $itemIds)
            ->where('LoanStatus', 'Loan')
            ->where('ActualReturn IS NULL')
            ->get()->getResult();

        if (count($loanItems) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku tidak ditemukan atau sudah dikembalikan']);
        }

        $extendDate       = date('Y-m-d H:i:s');
        $extendedCount    = 0;
        $collectionLoanId = null;
        $memberId         = null;

        foreach ($loanItems as $item) {
            if (!$collectionLoanId) $collectionLoanId = $item->CollectionLoan_id;
            if (!$memberId)         $memberId         = $item->member_id;

            // Hitung DueDate baru dari DueDate saat ini
            $currentDueDate = new \DateTime($item->DueDate);
            $newDueDate     = clone $currentDueDate;
            $newDueDate->modify('+' . $extendDays . ' days');

            // Insert ke collectionloanextends
            $db->table('collectionloanextends')->insert([
                'CollectionLoan_id'     => $item->CollectionLoan_id,
                'CollectionLoanItem_id' => $item->ID,
                'Collection_id'         => $item->Collection_id,
                'Member_id'             => $item->member_id,
                'DateExtend'            => $extendDate,
                'DueDateExtend'         => $newDueDate->format('Y-m-d H:i:s'),
                'CreateBy'              => user_id(),
                'CreateDate'            => $extendDate,
                'CreateTerminal'        => $this->request->getIPAddress(),
                'active'                => 1,
            ]);

            // Update DueDate di collectionloanitems
            $db->table('collectionloanitems')
                ->where('ID', $item->ID)
                ->update([
                    'DueDate'        => $newDueDate->format('Y-m-d H:i:s'),
                    'UpdateBy'       => user_id(),
                    'UpdateDate'     => $extendDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);

            $extendedCount++;
        }

        // Update ExtendCount di tabel induk
        if ($extendedCount > 0 && $collectionLoanId) {
            $db->table('collectionloans')
                ->where('ID', $collectionLoanId)
                ->set('ExtendCount', 'ExtendCount + ' . $extendedCount, false)
                ->set('UpdateBy',       user_id())
                ->set('UpdateDate',     $extendDate)
                ->set('UpdateTerminal', $this->request->getIPAddress())
                ->update();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses database']);
        }

        session()->set('struk_perpanjangan', [
            'item_ids'       => $itemIds,
            'extend_days'    => $extendDays,
            'extend_date'    => $extendDate,
        ]);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => "<b>$extendedCount buku</b> berhasil diperpanjang selama <b>$extendDays hari</b>!",
            'struk_url' => base_url('sirkulasi-perpanjangan/success'),
            'data'      => ['member_id' => $memberId, 'extended_count' => $extendedCount]
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
    }
}

public function success()
{
    $struk = session()->get('struk_perpanjangan');
    if (empty($struk)) {
        return redirect()->to('sirkulasi-perpanjangan/create');
    }
    session()->remove('struk_perpanjangan');

    $db = db_connect();

    // Get the latest extend record per item (MAX ID per CollectionLoanItem_id)
    $maxIds = $db->table('collectionloanextends')
        ->select('MAX(ID) as max_id')
        ->whereIn('CollectionLoanItem_id', $struk['item_ids'])
        ->groupBy('CollectionLoanItem_id')
        ->get()->getResultArray();
    $maxIdList = array_column($maxIds, 'max_id');

    $items = [];
    if (!empty($maxIdList)) {
        $items = $db->table('collectionloanextends as ce')
            ->select('ce.ID, ce.DateExtend, ce.DueDateExtend, ce.Member_id, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
            ->join('collections as col', 'col.ID = ce.Collection_id')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
            ->whereIn('ce.ID', $maxIdList)
            ->get()->getResult();
    }

    $member = null;
    if (!empty($items)) {
        $member = $db->table('members')->where('ID', $items[0]->Member_id)->get()->getRow();
    }

    $this->data['title']       = 'Struk Perpanjangan';
    $this->data['member']      = $member;
    $this->data['items']       = $items;
    $this->data['extend_days'] = $struk['extend_days'];
    $this->data['extend_date'] = $struk['extend_date'];

    return view('Perpanjangan\Views\success', $this->data);
}

public function sendStruk(): \CodeIgniter\HTTP\ResponseInterface
{
    $itemIds  = json_decode($this->request->getPost('item_ids') ?? '[]', true);
    $memberId = (int) $this->request->getPost('member_id');

    if (empty($itemIds) || !$memberId) {
        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
    }

    $db     = db_connect();
    $member = $db->table('members')->where('ID', $memberId)->get()->getRow();
    if (!$member) {
        return $this->response->setJSON(['success' => false, 'message' => 'Data anggota tidak ditemukan.']);
    }

    $maxIds = $db->table('collectionloanextends')
        ->select('MAX(ID) as max_id')
        ->whereIn('CollectionLoanItem_id', $itemIds)
        ->groupBy('CollectionLoanItem_id')
        ->get()->getResultArray();
    $maxIdList = array_column($maxIds, 'max_id');

    $items = [];
    if (!empty($maxIdList)) {
        $items = $db->table('collectionloanextends as ce')
            ->select('ce.ID, ce.DateExtend, ce.DueDateExtend, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
            ->join('collections as col', 'col.ID = ce.Collection_id')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
            ->whereIn('ce.ID', $maxIdList)
            ->get()->getResult();
    }

    $emailLib = new \App\Libraries\EmailNotificationLibrary();
    $result   = $emailLib->sendStrukPerpanjanganEmail($member, $items);

    return $this->response->setJSON($result);
}

public function getExtendHistory()
{
    $limit     = $this->request->getGet('limit') ?? 5;
    $offset    = $this->request->getGet('offset') ?? 0;
    $member_id = $this->request->getGet('member_id');

    $db    = db_connect();
    $query = $db->table('collectionloanextends cle')
        ->select('cle.*, c.NomorBarcode, cat.Title, cat.Author')
        ->join('collectionloanitems cli', 'cli.ID = cle.CollectionLoanItem_id')
        ->join('collections c', 'c.ID = cle.Collection_id')
        ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
        ->where('cle.active', 1);

    if (!empty($member_id)) {
        $query->where('cle.Member_id', $member_id);
    }

    $history = $query->orderBy('cle.DateExtend', 'DESC')
        ->limit($limit, $offset)
        ->get()->getResult();

    return $this->response->setJSON(['status' => 'success', 'data' => $history]);
}

}
