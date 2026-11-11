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

        $this->uploadPath = WRITEPATH . 'uploads/';
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

        $inputReturnDate = $this->request->getPost('returnDate');
        $today = $inputReturnDate ? new \DateTime($inputReturnDate) : new \DateTime();
        $formattedItems = [];

        foreach ($allItems as $item) {
            // Hari kerja terlambat (jam diabaikan); terlambat hanya jika > 0
            $daysOverdue = hari_terlambat($item->DueDate, $today->format('Y-m-d'));
            $isOverdue = $daysOverdue > 0;

            $formattedItems[] = [
                'id' => $item->ID, // Primary key collectionloanitems
                'title' => $item->Title,
                'author' => $item->Author,
                'barcode' => $item->NomorBarcode,
                'loan_date' => $item->LoanDate,
                'due_date' => $item->DueDate,
                'is_overdue' => $isOverdue,
                'days_overdue' => $daysOverdue
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
            $itemIdsJson = $this->request->getPost('item_ids');
            $processVio = $this->request->getPost('process_violation'); // '1' atau '0'
            $vioDataJson = $this->request->getPost('violation_data');    // JSON object
            $inputReturnDate = $this->request->getPost('returnDate');

            if (empty($itemIdsJson)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk dikembalikan']);
            }

            $itemIds = json_decode($itemIdsJson, true);
            if (!is_array($itemIds) || count($itemIds) === 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
            }

            // Tanggal pengembalian hanya boleh hari ini atau backdate (mundur),
            // tidak boleh tanggal yang akan datang.
            if ($inputReturnDate && date('Y-m-d', strtotime($inputReturnDate)) > date('Y-m-d')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal pengembalian tidak boleh tanggal yang akan datang.']);
            }

            $itemIds = array_values(array_unique(array_map('intval', $itemIds)));

            // Data pelanggaran divalidasi di server; isian dari browser tidak dipercaya begitu saja
            $violation = null;
            if ($processVio === '1') {
                $violation = $this->validateViolationInput(json_decode((string) $vioDataJson, true), $itemIds);
                if (is_string($violation)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $violation]);
                }
            }

            $loanItems = $this->db->table('collectionloanitems')
                ->whereIn('ID', $itemIds)
                ->where('LoanStatus', 'Loan')
                ->where('ActualReturn IS NULL')
                ->get()->getResult();

            if (count($loanItems) === 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Buku sudah dikembalikan atau tidak ditemukan']);
            }

            $this->db->transBegin();

            $returnDate = $inputReturnDate ? date('Y-m-d', strtotime($inputReturnDate)) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
            $returnedCount = 0;
            $violationCount = 0;
            $totalDenda = 0;
            $collectionLoanId = null;
            $memberId = null;

            foreach ($loanItems as $item) {
                if (!$collectionLoanId)
                    $collectionLoanId = $item->CollectionLoan_id;
                if (!$memberId)
                    $memberId = $item->member_id;

                // Hari kerja terlambat (jam diabaikan, libur dilewati); sama dengan tampilan daftar
                $lateDays = hari_terlambat($item->DueDate, $returnDate);
                $isOverdue = $lateDays > 0;

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

                // Update status koleksi ke Available
                $this->db->table('collections')
                    ->where('ID', $item->Collection_id)
                    ->update([
                        'Status_id' => 1,
                        'UpdateBy' => user_id(),
                        'UpdateDate' => $returnDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);

                // Catat pelanggaran hanya untuk buku yang dipilih petugas di modal
                if ($violation !== null && in_array((int) $item->ID, $violation['item_ids'], true)) {
                    $jumlahDenda = $violation['per_hari']
                        ? $lateDays * $violation['jumlah_denda']
                        : $violation['jumlah_denda'];

                    $this->db->table('pelanggaran')->insert([
                        'CollectionLoan_id' => $item->CollectionLoan_id,
                        'CollectionLoanItem_id' => $item->ID,
                        'JenisPelanggaran_id' => $violation['jenis_pelanggaran_id'],
                        'JenisDenda_id' => $violation['jenis_denda_id'],
                        'JumlahDenda' => $jumlahDenda,
                        'JumlahSuspend' => $violation['jumlah_suspend'],
                        'Paid' => 0,
                        'Member_id' => $item->member_id,
                        'Collection_id' => $item->Collection_id,
                        'Branch_id' => null,
                        'active' => 1,
                        'CreateBy' => user_id(),
                        'CreateDate' => $returnDate,
                        'CreateTerminal' => $this->request->getIPAddress()
                    ]);

                    $totalDenda += $jumlahDenda;
                    $violationCount++;
                }

                $returnedCount++;
            }

            if ($returnedCount > 0 && $collectionLoanId) {
                $this->db->table('collectionloans')
                    ->where('ID', $collectionLoanId)
                    ->set('ReturnCount', 'ReturnCount + ' . $returnedCount, false)
                    ->set('UpdateBy', user_id())
                    ->set('UpdateDate', $returnDate)
                    ->set('UpdateTerminal', $this->request->getIPAddress())
                    ->update();
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses database. Tidak ada data yang disimpan.']);
            }
            $this->db->transCommit();

            $memberEmail = '';
            if ($memberId) {
                $memberObj = $this->db->table('members')->where('ID', $memberId)->get()->getRow();
                if ($memberObj) {
                    $memberEmail = $memberObj->Email;
                }
            }

            $msg = "<b>$returnedCount buku</b> berhasil dikembalikan!";
            if ($violationCount > 0) {
                $msg .= "<br><small>&#9888; $violationCount pelanggaran telah dicatat.</small>";
            }

            // Store minimal struk data in session for the success page
            session()->set('struk_pengembalian', [
                'item_ids' => $itemIds,
                'violation_count' => $violationCount,
                'total_denda' => $totalDenda,
                'return_date' => $returnDate,
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $msg,
                'struk_url' => base_url('sirkulasi-pengembalian/success'),
                'data' => [
                    'member_id' => $memberId,
                    'member_email' => $memberEmail,
                    'returned_count' => $returnedCount,
                    'violation_count' => $violationCount,
                    'item_ids' => $itemIds
                ]
            ]);

        } catch (\Throwable $e) {
            if ($this->db->transDepth > 0) {
                $this->db->transRollback();
            }
            log_message('error', 'Proses pengembalian gagal: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pengembalian gagal diproses. Tidak ada data yang disimpan, silakan coba lagi.']);
        }
    }

    /**
     * Validasi isian modal pelanggaran.
     *
     * @return array|string Data bersih, atau pesan kesalahan untuk petugas.
     */
    private function validateViolationInput($data, array $returnItemIds)
    {
        if (!is_array($data)) {
            return 'Data pelanggaran tidak terbaca. Silakan isi ulang form pelanggaran.';
        }

        $itemIds = array_values(array_unique(array_map('intval', (array) ($data['item_ids'] ?? []))));
        $itemIds = array_values(array_intersect($itemIds, $returnItemIds));
        if (!$itemIds) {
            return 'Pilih minimal satu buku yang melanggar.';
        }

        $jenisPelanggaranId = (int) ($data['jenis_pelanggaran_id'] ?? 0);
        $jenisDendaId = (int) ($data['jenis_denda_id'] ?? 0);
        if (!$jenisPelanggaranId || !$this->db->table('jenis_pelanggaran')->where('ID', $jenisPelanggaranId)->where('active', 1)->countAllResults()) {
            return 'Jenis pelanggaran tidak valid. Silakan pilih ulang.';
        }
        if (!$jenisDendaId || !$this->db->table('jenis_denda')->where('ID', $jenisDendaId)->where('active', 1)->countAllResults()) {
            return 'Jenis denda tidak valid. Silakan pilih ulang.';
        }

        $jumlahDenda = $data['jumlah_denda'] ?? 0;
        if ($jumlahDenda === '' || $jumlahDenda === null) {
            $jumlahDenda = 0;
        }
        if (!is_numeric($jumlahDenda) || (float) $jumlahDenda < 0 || (float) $jumlahDenda > 100000000 || floor((float) $jumlahDenda) != (float) $jumlahDenda) {
            return 'Jumlah denda harus angka bulat antara 0 dan 100.000.000.';
        }

        $jumlahSuspend = $data['jumlah_suspend'] ?? 0;
        if ($jumlahSuspend === '' || $jumlahSuspend === null) {
            $jumlahSuspend = 0;
        }
        if (!is_numeric($jumlahSuspend) || floor((float) $jumlahSuspend) != (float) $jumlahSuspend || (float) $jumlahSuspend < 0 || (float) $jumlahSuspend > 365) {
            return 'Lama suspend harus angka bulat antara 0 dan 365 hari.';
        }

        return [
            'item_ids' => $itemIds,
            'jenis_pelanggaran_id' => $jenisPelanggaranId,
            'jenis_denda_id' => $jenisDendaId,
            'jumlah_denda' => (int) $jumlahDenda,
            'jumlah_suspend' => (int) $jumlahSuspend,
            'per_hari' => !empty($data['per_hari']),
        ];
    }


    public function sendStruk(): \CodeIgniter\HTTP\ResponseInterface
    {
        $itemIds = json_decode($this->request->getPost('item_ids') ?? '[]', true);
        $memberId = (int) $this->request->getPost('member_id');

        if (empty($itemIds) || !$memberId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
        }

        $member = $this->db->table('members')->where('ID', $memberId)->get()->getRow();
        if (!$member) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data anggota tidak ditemukan.']);
        }

        $items = $this->db->table('collectionloanitems as cli')
            ->select('cli.ID, cli.DueDate, cli.ActualReturn, cli.LateDays, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
            ->join('collections as col', 'col.ID = cli.Collection_id')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
            ->whereIn('cli.ID', $itemIds)
            ->get()->getResult();

        $emailLib = new \App\Libraries\EmailNotificationLibrary();
        $result = $emailLib->sendStrukPengembalianEmail($member, $items);

        return $this->response->setJSON($result);
    }

    public function success()
    {
        $struk = session()->get('struk_pengembalian');
        $loanId = $this->request->getGet('loan_id');

        if (empty($struk) && empty($loanId)) {
            return redirect()->to('sirkulasi-pengembalian/create');
        }

        if (!empty($struk)) {
            // Jalur normal: baru saja selesai memproses pengembalian.
            // session()->remove('struk_pengembalian'); // Commented out so it can be printed multiple times

            $items = $this->db->table('collectionloanitems as cli')
                ->select('cli.ID, cli.DueDate, cli.ActualReturn, cli.LateDays, cli.CollectionLoan_id, cli.member_id, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
                ->join('collections as col', 'col.ID = cli.Collection_id')
                ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
                ->whereIn('cli.ID', $struk['item_ids'])
                ->get()->getResult();

            $returnDate = $struk['return_date'];
            $violationCount = $struk['violation_count'];
            $totalDenda = $struk['total_denda'];
        } else {
            // Cetak ulang struk transaksi lama lewat ?loan_id= (data session sudah
            // tidak ada lagi, mis. lupa dicetak saat itu juga / beda perangkat).
            // Jumlah denda & pelanggaran dihitung ulang dari tabel `pelanggaran`
            // yang memang dicatat permanen saat pengembalian diproses — bukan
            // dari session — jadi hasilnya tetap akurat meski dicetak belakangan.
            $items = $this->db->table('collectionloanitems as cli')
                ->select('cli.ID, cli.DueDate, cli.ActualReturn, cli.LateDays, cli.CollectionLoan_id, cli.member_id, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
                ->join('collections as col', 'col.ID = cli.Collection_id')
                ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
                ->where('cli.CollectionLoan_id', $loanId)
                ->where('cli.LoanStatus', 'Return')
                ->get()->getResult();

            if (empty($items)) {
                set_message('message', '<div class="alert alert-warning">Data pengembalian untuk transaksi tersebut tidak ditemukan.</div>');
                return redirect()->to('sirkulasi-pengembalian');
            }

            $itemIds = array_column($items, 'ID');
            $pelanggaran = $this->db->table('pelanggaran')
                ->whereIn('CollectionLoanItem_id', $itemIds)
                ->get()->getResult();

            $returnDate = $items[0]->ActualReturn;
            $violationCount = count($pelanggaran);
            $totalDenda = array_sum(array_column($pelanggaran, 'JumlahDenda'));
        }

        $member = null;
        if (!empty($items)) {
            $member = $this->db->table('members')
                ->where('ID', $items[0]->member_id)
                ->get()->getRow();
        }

        $this->data['title'] = 'Struk Pengembalian';
        $this->data['member'] = $member;
        $this->data['items'] = $items;
        $this->data['return_date'] = $returnDate;
        $this->data['violation_count'] = $violationCount;
        $this->data['total_denda'] = $totalDenda;

        return view('Pengembalian\Views\success', $this->data);
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
