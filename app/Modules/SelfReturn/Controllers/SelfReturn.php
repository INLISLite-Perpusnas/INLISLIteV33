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

    /**
     * Scan a barcode: find its loan transaction and return every item from
     * that same transaction that is still on loan, so the member can review
     * the whole transaction (and remove items) before confirming the return.
     */
    public function checkBook()
    {
        $nomorBarcode = trim($this->request->getPost('nomorBarcode'));

        if (empty($nomorBarcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor barcode tidak boleh kosong'
            ]);
        }

        // Cari buku yang discan beserta transaksi peminjamannya
        $scannedItem = $this->db->table('collections c')
            ->select('cli.CollectionLoan_id, cli.member_id, m.Fullname')
            ->join('collectionloanitems cli', 'cli.Collection_id = c.ID AND cli.LoanStatus = "Loan" AND cli.ActualReturn IS NULL', 'inner')
            ->join('members m', 'm.ID = cli.member_id', 'left')
            ->where('c.NomorBarcode', $nomorBarcode)
            ->get()
            ->getRow();

        if (!$scannedItem) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Buku dengan barcode tersebut tidak ditemukan atau sudah dikembalikan'
            ]);
        }

        // Ambil semua buku yang ada dalam transaksi peminjaman yang sama
        $allItems = $this->db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.CollectionLoan_id', $scannedItem->CollectionLoan_id)
            ->where('cli.LoanStatus', 'Loan')
            ->where('cli.ActualReturn IS NULL')
            ->get()
            ->getResult();

        $today = new \DateTime();
        $items = [];

        foreach ($allItems as $item) {
            $dueDate = new \DateTime($item->DueDate);
            $isOverdue = $today > $dueDate;

            $items[] = [
                'id' => $item->ID, // Primary key collectionloanitems
                'title' => $item->Title ?: 'Tidak diketahui',
                'author' => $item->Author ?: 'Tidak diketahui',
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
                'member_name' => $scannedItem->Fullname,
                'collection_loan_id' => $scannedItem->CollectionLoan_id,
                'items' => $items
            ]
        ]);
    }

    /**
     * Process the return for the given loan item IDs (the list the member
     * ended up with on screen after reviewing/removing items).
     */
    public function processReturn()
    {
        try {
            $itemIdsJson = $this->request->getPost('item_ids');

            if (empty($itemIdsJson)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada buku yang dipilih untuk dikembalikan'
                ]);
            }

            $itemIds = json_decode($itemIdsJson, true);
            if (!is_array($itemIds) || count($itemIds) === 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Format data tidak valid'
                ]);
            }

            $this->db->transStart();

            $loanItems = $this->db->table('collectionloanitems cli')
                ->select('cli.*, c.NomorBarcode, cat.Title')
                ->join('collections c', 'c.ID = cli.Collection_id')
                ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
                ->whereIn('cli.ID', $itemIds)
                ->where('cli.LoanStatus', 'Loan')
                ->where('cli.ActualReturn IS NULL')
                ->get()
                ->getResult();

            if (count($loanItems) === 0) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Buku sudah dikembalikan atau tidak ditemukan'
                ]);
            }

            $returnDate = date('Y-m-d H:i:s');
            $results = [];
            $loanCounts = [];

            foreach ($loanItems as $item) {
                $dueDate = new \DateTime($item->DueDate);
                $actualReturnDate = new \DateTime($returnDate);
                $isOverdue = $actualReturnDate > $dueDate;
                $lateDays = $isOverdue ? $actualReturnDate->diff($dueDate)->days : 0;

                $this->db->table('collectionloanitems')
                    ->where('ID', $item->ID)
                    ->update([
                        'ActualReturn' => $returnDate,
                        'LateDays' => $lateDays,
                        'LoanStatus' => 'Return',
                        'UpdateBy' => 1,
                        'UpdateDate' => $returnDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);

                $this->db->table('collections')
                    ->where('ID', $item->Collection_id)
                    ->update([
                        'Status_id' => 1, // Available
                        'UpdateBy' => 1,
                        'UpdateDate' => $returnDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);

                $loanCounts[$item->CollectionLoan_id] = ($loanCounts[$item->CollectionLoan_id] ?? 0) + 1;

                $results[] = [
                    'title' => $item->Title,
                    'barcode' => $item->NomorBarcode,
                    'late_days' => $lateDays,
                    'is_late' => $lateDays > 0
                ];
            }

            foreach ($loanCounts as $collectionLoanId => $count) {
                $this->db->table('collectionloans')
                    ->where('ID', $collectionLoanId)
                    ->set('ReturnCount', 'ReturnCount + ' . (int) $count, false)
                    ->set('UpdateBy', 1)
                    ->set('UpdateDate', $returnDate)
                    ->set('UpdateTerminal', $this->request->getIPAddress())
                    ->update();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Status transaksi false, dibatalkan oleh sistem.');
            }

            $lateCount = count(array_filter($results, fn($r) => $r['is_late']));
            $message = count($results) . ' buku berhasil dikembalikan';
            if ($lateCount > 0) {
                $message .= " ({$lateCount} buku terlambat)";
            }

            // Simpan data minimal untuk halaman struk
            $this->session->set('self_return_receipt', [
                'item_ids' => array_column($loanItems, 'ID'),
                'return_date' => $returnDate,
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'data' => $results,
                'struk_url' => base_url('pengembalian-mandiri/success')
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'SelfReturn processReturn error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    public function success()
    {
        $this->data['title'] = 'Pengembalian Berhasil';
        $receipt = $this->session->get('self_return_receipt');

        if (empty($receipt)) {
            return redirect()->to('pengembalian-mandiri');
        }

        $items = $this->db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode, c.CallNumber, cat.Title, cat.Author')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->whereIn('cli.ID', $receipt['item_ids'])
            ->get()
            ->getResult();

        if (empty($items)) {
            return redirect()->to('pengembalian-mandiri');
        }

        $member = $this->db->table('members')
            ->where('ID', $items[0]->member_id)
            ->get()
            ->getRow();

        $lateCount = 0;
        foreach ($items as $item) {
            if (($item->LateDays ?? 0) > 0) {
                $lateCount++;
            }
        }

        $this->data['member'] = $member;
        $this->data['items'] = $items;
        $this->data['return_date'] = $receipt['return_date'];
        $this->data['late_count'] = $lateCount;

        return view('SelfReturn\Views\success', $this->data);
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
