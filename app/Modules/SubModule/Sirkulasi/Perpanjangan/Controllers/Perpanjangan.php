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

		$this->uploadPath = WRITEPATH . 'uploads/';
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

    $member         = $this->loadMember($db, (int) $scannedItem->member_id);
    $formattedItems = [];

    foreach ($allItems as $item) {
        $check = $this->checkExtendRules($db, $item, $member);

        $formattedItems[] = [
            'id'            => $item->ID,
            'collection_id' => $item->Collection_id,
            'title'         => $item->Title,
            'author'        => $item->Author,
            'barcode'       => $item->NomorBarcode,
            'loan_date'     => $item->LoanDate,
            'due_date'      => $item->DueDate,
            'extend_count'  => $check['extend_count'],
            'max_extend'    => $check['max_extend'],
            'is_overdue'    => $check['days_overdue'] > 0,
            'days_overdue'  => $check['days_overdue'],
            'can_extend'    => $check['ok'],
            'reason'        => $check['reason'],
            'extend_days'   => $check['extend_days'],
            'new_due_date'  => $check['new_due_date'],
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

        if (empty($itemIdsJson)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk diperpanjang']);
        }

        $itemIds = json_decode($itemIdsJson, true);
        if (!is_array($itemIds) || count($itemIds) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
        }
        $itemIds = array_values(array_unique(array_map('intval', $itemIds)));

        $db = db_connect();

        $loanItems = $db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->whereIn('cli.ID', $itemIds)
            ->where('cli.LoanStatus', 'Loan')
            ->where('cli.ActualReturn IS NULL')
            ->get()->getResult();

        if (count($loanItems) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku tidak ditemukan atau sudah dikembalikan']);
        }

        // Syarat diperiksa ulang di server; lama perpanjangan dihitung dari aturan, bukan dari browser
        $plans    = [];
        $rejected = [];
        $members  = [];
        foreach ($loanItems as $item) {
            $memberId = (int) $item->member_id;
            $members[$memberId] ??= $this->loadMember($db, $memberId);
            $check = $this->checkExtendRules($db, $item, $members[$memberId]);
            if ($check['ok']) {
                $plans[] = [$item, $check];
            } else {
                $rejected[] = esc($item->NomorBarcode) . ': ' . esc($check['reason']);
            }
        }

        if ($rejected !== []) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada buku yang diperpanjang. Buku berikut tidak memenuhi syarat:<br>' . implode('<br>', $rejected),
            ]);
        }

        $extendDate       = date('Y-m-d H:i:s');
        $extendedCount    = 0;
        $collectionLoanId = null;
        $memberId         = null;
        $daysUsed         = [];
        $extendIds        = [];

        $db->transStart();

        foreach ($plans as [$item, $check]) {
            if (!$collectionLoanId) $collectionLoanId = $item->CollectionLoan_id;
            if (!$memberId)         $memberId         = $item->member_id;
            $daysUsed[] = $check['extend_days'];

            $db->table('collectionloanextends')->insert([
                'CollectionLoan_id'     => $item->CollectionLoan_id,
                'CollectionLoanItem_id' => $item->ID,
                'Collection_id'         => $item->Collection_id,
                'Member_id'             => $item->member_id,
                'DateExtend'            => $extendDate,
                'DueDateExtend'         => $check['new_due_date'],
                'CreateBy'              => user_id(),
                'CreateDate'            => $extendDate,
                'CreateTerminal'        => $this->request->getIPAddress(),
                'active'                => 1,
            ]);
            $extendIds[] = (int) $db->insertID();

            $db->table('collectionloanitems')
                ->where('ID', $item->ID)
                ->update([
                    'DueDate'        => $check['new_due_date'],
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

        $daysUsed = array_values(array_unique($daysUsed));
        sort($daysUsed);
        $daysLabel = count($daysUsed) === 1 ? (string) $daysUsed[0] : $daysUsed[0] . '–' . end($daysUsed);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => "<b>$extendedCount buku</b> berhasil diperpanjang selama <b>$daysLabel hari kerja</b>!",
            // Halaman berhasil membaca data dari database, jadi bisa dibuka ulang atau dicetak lagi
            'struk_url' => base_url('sirkulasi-perpanjangan/success?ids=' . implode(',', $extendIds)),
            'data'      => ['member_id' => $memberId, 'extended_count' => $extendedCount]
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Perpanjangan gagal: ' . $e->getMessage());
        return $this->response->setJSON(['status' => 'error', 'message' => 'Perpanjangan gagal diproses. Coba lagi atau hubungi administrator.']);
    }
}

// ----------------------------------------------------------------
// SYARAT PERPANJANGAN (setara SirkulasiHelpers INLISLite 3)
// ----------------------------------------------------------------

private function loadMember($db, int $memberId): ?object
{
    return $db->table('members')
        ->select('ID, MemberNo, StatusAnggota_id, EndDate, JenisAnggota_id')
        ->where('ID', $memberId)
        ->get()->getRow();
}

/**
 * Nilai aturan sirkulasi dengan urutan sama seperti peminjaman:
 * peraturan hari, peraturan tanggal, jenis bahan, lalu jenis anggota. Nilai 0 atau kosong dilewati.
 */
private function circulationRule($db, string $field, int $collectionId, ?object $member): int
{
    if (!in_array($field, ['DayPerpanjang', 'CountPerpanjang'], true)) {
        return 0;
    }

    $today = date('Y-m-d');
    $candidates = [
        $db->table('peraturan_peminjaman_hari')->select($field)->where('DayIndex', date('N'))->where('active', 1)->get()->getRow(),
        $db->table('peraturan_peminjaman_tanggal')->select($field)->where('DATE(TanggalAwal) <=', $today)->where('DATE(TanggalAkhir) >=', $today)->where('active', 1)->get()->getRow(),
        $db->table('collections c')->select('w.' . $field)->join('catalogs cat', 'cat.ID = c.Catalog_id')->join('worksheets w', 'w.ID = cat.Worksheet_id')->where('c.ID', $collectionId)->get()->getRow(),
        $member ? $db->table('jenis_anggota')->select($field)->where('id', $member->JenisAnggota_id)->get()->getRow() : null,
    ];

    foreach ($candidates as $row) {
        if ($row && (int) $row->$field > 0) {
            return (int) $row->$field;
        }
    }

    return 0;
}

/** Hari ini ditambah $days hari kerja, melewati Sabtu/Minggu sesuai setting dan tanggal libur. */
private function extendDueDate(int $days): string
{
    $libur = sirkulasi_hari_libur();
    $date  = new \DateTime(date('Y-m-d'));
    $added = 0;
    while ($added < $days) {
        $date->modify('+1 day');
        if (!in_array($date->format('N'), $libur['weekend'], true) && !isset($libur['tanggal'][$date->format('Y-m-d')])) {
            $added++;
        }
    }

    return $date->format('Y-m-d') . ' ' . date('H:i:s');
}

/** Hasil pemeriksaan syarat satu buku. 'reason' kosong bila boleh diperpanjang. */
private function checkExtendRules($db, object $item, ?object $member): array
{
    $extendCount = $db->table('collectionloanextends')
        ->where('CollectionLoanItem_id', $item->ID)
        ->where('active', 1)
        ->countAllResults();
    $maxExtend   = $this->circulationRule($db, 'CountPerpanjang', (int) $item->Collection_id, $member);
    $extendDays  = $this->circulationRule($db, 'DayPerpanjang', (int) $item->Collection_id, $member);
    $daysOverdue = hari_terlambat($item->DueDate, date('Y-m-d'));
    $newDueDate  = $extendDays > 0 ? $this->extendDueDate($extendDays) : null;

    $result = [
        'ok'           => false,
        'reason'       => '',
        'extend_count' => $extendCount,
        'max_extend'   => $maxExtend,
        'extend_days'  => $extendDays,
        'days_overdue' => $daysOverdue,
        'new_due_date' => $newDueDate,
    ];

    if (!$member) {
        $result['reason'] = 'Data anggota tidak ditemukan.';
    } elseif ((int) $member->StatusAnggota_id !== 3) {
        $result['reason'] = 'Status anggota tidak aktif.';
    } elseif ($member->EndDate && strtotime($member->EndDate) < time()) {
        $result['reason'] = 'Masa keanggotaan sudah berakhir.';
    } elseif ($daysOverdue > 0) {
        $result['reason'] = 'Sudah terlambat ' . $daysOverdue . ' hari, buku harus dikembalikan.';
    } elseif ($maxExtend <= 0) {
        $result['reason'] = 'Aturan sirkulasi tidak mengizinkan perpanjangan.';
    } elseif ($extendCount >= $maxExtend) {
        $result['reason'] = 'Batas perpanjangan (' . $maxExtend . ' kali) sudah tercapai.';
    } elseif ($db->table('collectionloanextends')->where('Collection_id', $item->Collection_id)->where('DATE(DateExtend)', date('Y-m-d'))->where('active', 1)->countAllResults() > 0) {
        $result['reason'] = 'Sudah diperpanjang hari ini.';
    } elseif ($extendDays <= 0) {
        $result['reason'] = 'Lama perpanjangan belum diatur di aturan sirkulasi.';
    } elseif (date('Y-m-d', strtotime($newDueDate)) <= date('Y-m-d', strtotime($item->DueDate))) {
        $result['reason'] = 'Jatuh tempo baru tidak lebih lama dari jatuh tempo sekarang. Perpanjang mendekati tanggal jatuh tempo.';
    } else {
        $result['ok'] = true;
    }

    return $result;
}

public function success()
{
    $ids = array_slice(array_values(array_filter(array_map('intval', explode(',', (string) $this->request->getGet('ids'))))), 0, 100);
    if ($ids === []) {
        return redirect()->to('sirkulasi-perpanjangan/create');
    }

    $db = db_connect();
    $items = $db->table('collectionloanextends as ce')
        ->select('ce.ID, ce.CollectionLoan_id, ce.CollectionLoanItem_id, ce.DateExtend, ce.DueDateExtend, ce.Member_id, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
        ->join('collections as col', 'col.ID = ce.Collection_id')
        ->join('catalogs as cat', 'cat.ID = col.Catalog_id', 'left')
        ->whereIn('ce.ID', $ids)
        ->orderBy('ce.ID', 'ASC')
        ->get()->getResult();

    if (empty($items)) {
        return redirect()->to('sirkulasi-perpanjangan/create');
    }

    // Lama perpanjangan dalam hari kerja, dihitung dari data yang tersimpan
    $days = [];
    foreach ($items as $item) {
        $days[] = hari_kerja_antara($item->DateExtend, $item->DueDateExtend);
    }
    $days = array_values(array_unique($days));
    sort($days);

    $this->data['title']       = 'Perpanjangan Berhasil';
    $this->data['member']      = $db->table('members')->select('ID, MemberNo, Fullname, Email, Phone')->where('ID', $items[0]->Member_id)->get()->getRow();
    $this->data['items']       = $items;
    $this->data['extend_days'] = count($days) === 1 ? (string) $days[0] : $days[0] . '–' . end($days);
    $this->data['extend_date'] = $items[0]->DateExtend;
    $this->data['loan_ids']    = array_values(array_unique(array_map(static fn ($item) => $item->CollectionLoan_id, $items)));
    $this->data['first_due']   = min(array_map(static fn ($item) => $item->DueDateExtend, $items));

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
