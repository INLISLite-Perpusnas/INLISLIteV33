<?php

namespace Peminjaman\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Carbon\CarbonPeriod;

//use Hermawan\DataTables\DataTable;

class Peminjaman extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $peminjamanModel;
	protected $collectionModel;
	protected $anggotaModel;
	protected $collectionLoanModel;
	protected $collectionLoanItemModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;
	protected $db;
	protected $cart;
	protected $pelanggaranModel;
	protected $settingModel;

	function __construct()
	{
		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		 $this->pelanggaranModel = new \Pelanggaran\Models\PelanggaranModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/peminjaman/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		$this->cart = new \App\Libraries\Cart();
		$this->db = \Config\Database::connect('data');

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('peminjaman');
		helper('member');
	}

	public function datatable($slug = null)
{
    $db = db_connect();
    $builder = $db->table('collectionloans cl')
        ->select('cli.ID, cli.ID as action, cli.ID as action_notif')
        ->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
        ->select('cl.UpdateDate')
        ->select('col.NomorBarcode, col.ISDRM')
        ->select('a.Title, a.PublishLocation, a.Publisher, a.PublishYear')
        ->select('m.Fullname, m.MemberNo')
        ->select('loc.Name as LocationLibrary')
        ->join('collectionloanitems cli', 'cli.CollectionLoan_id = cl.ID')
        ->join('collections col', 'col.ID = cli.Collection_id')
        ->join('catalogs a', 'a.ID = col.Catalog_id')
        ->join('members m', 'm.ID = cli.member_id')
        ->join('location_library loc', 'loc.ID = col.Location_Library_id')
        ->where('cli.LoanStatus', 'Loan')
		->orderBy('cli.LoanDate', 'DESC'); // Urutkan berdasarkan tanggal pinjam terbaru				;
		



    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->onGlobalSearch('CollectionLoan_id', function ($builder, $column, $search) {
            $builder->orGroupStart()
                ->orLike('m.MemberNo', $search)
                ->orLike('m.Fullname', $search)
                ->orLike('cl.ID', $search)
                ->groupEnd();
        })
        ->onSearch('CollectionLoan_id', function ($builder, $column, $search) {
            $builder->groupStart()
                ->like('m.MemberNo', $search)
                ->orLike('m.Fullname', $search)
                ->orLike('cl.ID', $search)
                ->groupEnd();
        })
        ->edit('CollectionLoan_id', function ($row) {
            // Tombol "Cetak Struk" pakai ulang halaman struk yang sudah ada
            // (Peminjaman::success) — halaman itu memang mengambil data loan
            // dari DB berdasarkan ?loan_id=, bukan dari session, jadi bisa
            // dipakai untuk mencetak ulang struk transaksi lama kapan saja.
            $strukUrl = base_url('sirkulasi-peminjaman/success?loan_id=' . $row->CollectionLoan_id);

            $html =
                '<div class="widget-content p-0">
                <div class="widget-content-wrapper d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="widget-content-left mr-3">
                            <i class="far fa-id-card fa-3x text-secondary"></i>
                        </div>
                        <div class="widget-content-left text-secondary">
                            <dl class="dl-horizontal mb-0">
                                <dt class="font-weight-bold mb-0"><i class="fa fa-user text-secondary"></i> No. Anggota</dt>
                                <dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . esc($row->MemberNo) . '  <span class="text-secondary">(' . esc($row->Fullname) . ')</span></a></dd>
                                <dt class="font-weight-bold mb-0"><i class="fa fa-hashtag text-secondary"></i> No. Transaksi</dt>
                                <dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . esc($row->CollectionLoan_id) . '</a></dd>
                            </dl>
                        </div>
                    </div>
                    <a href="' . $strukUrl . '" target="_blank" class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation();" title="Cetak ulang struk bukti peminjaman untuk transaksi ini">
                        <i class="fa fa-print"></i> Cetak Struk
                    </a>
                </div>
            </div>';
            return $html;
        })
        ->edit('NomorBarcode', function ($row) {
            $html =
                '<div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left mr-3">
                        <i class="fa fa-qrcode fa-2x text-info"></i>
                    </div>
                    <div class="widget-content-left">
                        <div class="widget-heading">' . esc($row->NomorBarcode) . '</div>
                    </div>
                </div>
            </div>';
            return $html;
        })
        ->edit('Title', function ($row) {
            $html =
                '<div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left mr-3">
                        <i class="fa fa-book fa-2x text-info"></i>
                    </div>
                    <div class="widget-content-left">
                        <div class="widget-heading text-primary">' . esc($row->Publisher) . '</div>
                        <div class="widget-heading">' . esc($row->Title) . '</div>
                    </div>
                </div>
            </div>';
            return $html;
        })
        ->edit('LoanDate', function ($row) {
            $html  =  '<badge class="badge badge-primary badge-pill">' . $row->LoanDate . '</badge>';
            $html .=  '<badge class="badge badge-warning badge-pill">' . $row->DueDate . '</badge>';
            return $html;
        })
        // Hari terlambat dihitung helper hari_terlambat() agar sama di semua menu sirkulasi
        ->edit('LateDays', function ($row) {
            $status = status_jatuh_tempo($row->DueDate);
            return '<badge class="badge badge-' . $status['class'] . ' badge-pill">' . $status['text'] . ' hari</badge>';
        })
        ->edit('UpdateDate', function ($row) {
            $html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
            return $html;
        })
        // Dibaca halaman lewat innerHTML (stripHtml), jadi harus sudah aman sebagai HTML
        ->edit('Publisher', fn ($row) => esc($row->Publisher))
        ->edit('LocationLibrary', fn ($row) => esc($row->LocationLibrary))
        ->toJson();
    return $dataTable;
}



	public function loan_datatable($member_no = null)
{
    $db = db_connect();
    $builder = $db->table('collectionloans cl')
        ->select('cli.ID, cli.ID as action')
        ->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
        ->select('cl.UpdateDate')
        ->select('col.NomorBarcode, col.ID as collection_id')
        ->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
        ->select('m.Fullname, m.MemberNo, m.ID as member_id')
        ->select('loc.Name as LocationLibrary')
        ->join('collectionloanitems cli', 'cli.CollectionLoan_id = cl.ID')
        ->join('collections col', 'col.ID = cli.Collection_id')
        ->join('catalogs cat', 'cat.ID = col.Catalog_id')
        ->join('members m', 'm.ID = cli.member_id')
        ->join('location_library loc', 'loc.ID = col.Location_Library_id')
        ->where('cli.LoanStatus', 'Loan');

    if (!empty($member_no)) {
        $builder->where('m.MemberNo', $member_no);
    }


    // dd($builder->get()->getResult());

    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->edit('NomorBarcode', function ($row) {
            $html =
                '<div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left mr-3">
                        <i class="far fa-qrcode fa-2x text-info"></i>
                    </div>
                    <div class="widget-content-left">
                        <div class="widget-heading">' . esc($row->CollectionLoan_id) . '</div>
                        <div class="widget-subheading">' . esc($row->NomorBarcode) . '</div>
                    </div>
                </div>
            </div>';
            return $html;
        })
        ->edit('Title', function ($row) {
            $html =
                '<div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left mr-3">
                        <i class="far fa-book fa-2x text-info"></i>
                    </div>
                    <div class="widget-content-left">
                        <div class="widget-heading text-primary">' . esc($row->Publisher) . '</div>
                        <div class="widget-heading">' . esc($row->Title) . '</div>
                    </div>
                </div>
            </div>';
            return $html;
        })
        ->edit('LoanDate', function ($row) {
            $html  =  '<badge class="badge badge-primary badge-pill">' . $row->LoanDate . '</badge>';
            $html .=  '<badge class="badge badge-warning badge-pill">' . $row->DueDate . '</badge>';
            return $html;
        })
        // Hari terlambat dihitung helper hari_terlambat() agar sama di semua menu sirkulasi
        ->edit('LateDays', function ($row) {
            $status = status_jatuh_tempo($row->DueDate);
            return '<badge class="badge badge-' . $status['class'] . ' badge-pill">' . $status['text'] . ' hari</badge>';
        })
        ->edit('UpdateDate', function ($row) {
            $html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
            return $html;
        })
        ->edit('Publisher', fn ($row) => esc($row->Publisher))
        ->edit('action', function ($row) {
            // --- INI ADALAH BLOK YANG ANDA SEDIAKAN DI PROMPT ---
            $isLate = false;
            $lateDays = 0;
            $db = db_connect();

            $pelanggaran = $db->table('pelanggaran')
            ->select('CollectionLoanItem_id')->where('CollectionLoanItem_id', $row->ID)->get()->getRow();
            
            $lateDays = hari_terlambat($row->DueDate);
            $isLate = $lateDays > 0;


            $actionButtons = '';

            if ($pelanggaran == null && $isLate) {
                // ADDED ALL data-* ATTRIBUTES HERE
                $violationBtn = '<a href="javascript:void(0);" 
                     data-toggle="modal" 
                                data-target="#modalViolation"
                                data-id="' . $row->ID . '" 
                                data-loan-id="' . esc($row->CollectionLoan_id) . '"
                                data-barcode="' . esc($row->NomorBarcode) . '"
                                data-title="' . htmlspecialchars($row->Title, ENT_QUOTES) . '"
                                data-late-days="' . $lateDays . '"
                                 data-member-id="' . $row->member_id . '"
                               data-collection-id="' . $row->collection_id . '"
                                data-toggle-tooltip="tooltip" 
                                data-placement="top" 
                                title="Tambah Pelanggaran" 
                                class="btn btn-warning add-violation">
                                <i class="pe-7s-attention font-weight-bold"></i> Pelanggaran
                </a>';

                

                $actionButtons = $violationBtn;
            } else {
                $returnBtn = '<a href="javascript:void(0);" 
                    data-href="' . base_url('sirkulasi-pengembalian/do_return/' . $row->ID) . '" 
                    data-toggle="tooltip" data-placement="top" title="Kembalikan" class="btn btn-primary return-data">
                    <i class="pe-7s-refresh font-weight-bold"></i>
                </a>';
                $actionButtons = $returnBtn;
            }

            $delete = '<a href="javascript:void(0);" 
                data-href="' . base_url('sirkulasi-peminjaman/delete/' . $row->ID) . '" 
                data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger remove-data">
                <i class="pe-7s-trash font-weight-bold"></i>
            </a>';

            return $actionButtons . ' ' . $delete;
        })
        ->toJson();
    return $dataTable;
}


	

	public function loan_datatable_simple($member_no = null)
	{
		$db = db_connect();
		$builder = $db->table('collectionloans cl')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('cl.UpdateDate')
			->select('col.NomorBarcode')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->select('loc.Name as LocationLibrary')
			->join('collectionloanitems cli', 'cli.CollectionLoan_id = cl.ID')
			->join('collections col', 'col.ID = cli.Collection_id')
			->join('catalogs cat', 'cat.ID = col.Catalog_id')
			->join('members m', 'm.ID = cli.member_id')
			->join('location_library loc', 'loc.ID = col.Location_Library_id')
			->where('cli.LoanStatus', 'Loan');

		if (!empty($member_no)) {
			$builder->where('m.MemberNo', $member_no);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('NomorBarcode', function ($row) {
				$html  = '<b>' . esc($row->NomorBarcode) . '</b>';
				return $html;
			})
			->edit('Title', function ($row) {
				$html  = '<b>' . esc($row->Publisher) . '</b><br>';
				$html .= esc($row->Title);
				return $html;
			})
			->edit('LoanDate', function ($row) {
				$html  =  '<b>' . $row->LoanDate . '</b><br>';
				return $html;
			})
			->edit('DueDate', function ($row) {
				$html  =  '<b>' . $row->DueDate . '</b><br>';
				return $html;
			})
			->edit('LateDays', function ($row) {
				return '<b>' . status_jatuh_tempo($row->DueDate)['text'] . ' hari</b>';
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('sirkulasi/pengembalian/do_return/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Kembalikan" class="btn btn-primary return-data"><i class="pe-7s-refresh font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('sirkulasi-peminjaman/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function koleksi($member_no = null)
	{
		$db = db_connect();
		$builder = $db->table('collections col')
			->select('col.ID,col.Branch_id, col.ID as action')
			->select('col.NomorBarcode, col.UpdateDate')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->join('catalogs cat', 'cat.ID = col.Catalog_id');
		// if(!empty(branch_id())){
		// 	$builder->where('col.Branch_id', branch_id());

		// }

		if (!empty($member_no)) {
			$cli = get_ref_table('collectionloanitems', 'Collection_id', 'LoanStatus="Loan"', 'data');
			$cli_arr = get_object_array($cli, 'Collection_id');
			$cart_cli_arr = array();
			$carts = get_cart_loan();
			if (!empty($carts)) {
				foreach ($carts as $row) {
					$cart_cli_arr[] = $row->options->collection->ID;
				}
			}

			$col_id_arr = array_merge($cli_arr, $cart_cli_arr);
			if (!empty($col_id_arr)) {
				$builder->whereNotIn('col.ID', $col_id_arr);
			}

			// Nomor anggota dari URL dipakai sebagai nilai terikat, bukan potongan SQL
			$member = $db->table('members')->select('ID')->where('MemberNo', $member_no)->get()->getRow();
			if (!$member) {
				$builder->where('1 = 0', null, false);
			} else {
				$member_loc_arr = array_column($db->table('memberloanauthorizelocation')->select('LocationLoan_id')->where('Member_id', $member->ID)->get()->getResultArray(), 'LocationLoan_id');
				if (!empty($member_loc_arr)) {
					$builder->whereIn('col.Location_Library_id', $member_loc_arr);
				}

				$member_cat_arr = array_column($db->table('memberloanauthorizecategory')->select('CategoryLoan_id')->where('Member_id', $member->ID)->get()->getResultArray(), 'CategoryLoan_id');
				if (!empty($member_cat_arr)) {
					$builder->whereIn('col.Category_id', $member_cat_arr);
				}
			}
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function ($row) {
				$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
				return $html;
			})
			->edit('NomorBarcode', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . esc($row->NomorBarcode) . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('Title', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-book fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading text-primary">' . esc($row->Publisher) . '</div>
							<div class="widget-heading">' . esc($row->Title) . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/sirkulasi-peminjaman/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('peminjaman/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	
	public function switch($id = null)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$update_data_id = $this->peminjamanModel->update($id, array($field => ($value == 'true') ? 1 : 0));

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Upload Dokumen Keanggotaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Upload Dokumen Keanggotaan gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function CreateLoan()
	{
		// Ambil data JSON dari request
		$json = $this->request->getJSON();

		if (!$json) {
			return $this->fail('Invalid JSON input', 400);
		}

		$member_id = $json->member_id ?? null;
		$branch_id = $json->branch_id ?? null;
		$collection_id = $json->collection_id ?? null;
		$user_id = $json->user_id ?? null;

		// Validasi input
		if (!$member_id || !$branch_id || !$collection_id || !$user_id) {
			return $this->fail('Missing required fields', 400);
		}

		try {
			$this->db->transBegin();
			// Lock the latest row while generating the sequential loan number so
			// concurrent requests cannot receive the same collectionloans.ID.
			$collection_loan = $this->db->query(
				'SELECT ID FROM collectionloans WHERE ID IS NOT NULL ORDER BY ID DESC LIMIT 1 FOR UPDATE'
			)->getRow();
			$increment = ($collection_loan ? (int) substr((string) $collection_loan->ID, -5) : 0) + 1;
			$collection_loan_id = get_pad_number($increment, date('ymd'), 5);

			// Check if member exists
			$member = $this->anggotaModel->where('ID', $member_id)->first();


			if (!$member) {
				return $this->fail('Member not found', 404);
			}

			// Check if collection exists
			$collection = $this->collectionModel->where('ID', $collection_id)->first();

			if (!$collection) {
				return $this->fail('Collection not found', 404);
			}
			$branchid = $this->collectionModel
				->where('ID', $collection_id)
				->where('Branch_id', $branch_id)->first();

			if (!$branchid) {
				return $this->fail('Collection access denied', 404);
			}

			// Check if collection is available (Status_id = 1)
			if ($collection->Status_id != 1) {
				return $this->fail('Collection is not available for loan', 400);
			}

			// Simpan ke tabel collectionloans
			$loanData = [
				'ID' => $collection_loan_id,
				'Member_id' => $member_id,
				'Branch_id' => $branch_id,
				'CreateBy' => $user_id,
				'CollectionCount' => 1,
				'CreateDate' => date('Y-m-d H:i:s'),
			];

			try {
				$this->collectionLoanModel->insert($loanData);
				$loanId = $this->collectionLoanModel->insertID();
			} catch (\Exception $e) {
				$this->db->transRollback();
				return $this->fail('Failed to create loan: ' . $e->getMessage(), 500);
			}

			$loanDate = date('Y-m-d H:i:s');

			// Simpan ke tabel collectionloanitems
			$loanItemData = [
				'Collection_id' => $collection_id,
				'CollectionLoan_id' => $loanId,
				'member_id' => $member_id,
				'branch_id' => $branch_id,
				'LoanStatus' => "Loan", // Atur status peminjaman sesuai kebutuhan
				'LoanDate' => $loanDate,
				'DueDate' => date('Y-m-d H:i:s', strtotime($loanDate . ' +7 days')),
				'CreateBy' => $user_id,
				'CreateDate' => date('Y-m-d H:i:s'),
			];

			$this->collectionLoanItemModel->insert($loanItemData);

			// Update status koleksi menjadi 5
			$this->collectionModel->where('id', $collection_id)
				->set(['Status_id' => 5])
				->update();

			// Kirim respons sukses
			if ($this->db->transStatus() === false) {
				$this->db->transRollback();
				return $this->fail('Failed to create loan', 500);
			}
			$this->db->transCommit();
			return $this->respond(['message' => 'Loan created successfully'], 200);
		} catch (\Exception $e) {
			$this->db->transRollback();
			return $this->fail('Error: ' . $e->getMessage(), 500);
		}
	}

	public function loan_history()
	{

		// Kolom yang boleh diminta klien. Data pribadi anggota (NIK, nama ibu kandung, dll.) tidak termasuk.
		$allowedFields = [
			'collectionloanitems.*',
			'collectionloanitems.ID', 'collectionloanitems.CollectionLoan_id', 'collectionloanitems.LoanDate',
			'collectionloanitems.DueDate', 'collectionloanitems.ActualReturn', 'collectionloanitems.LateDays',
			'collectionloanitems.LoanStatus', 'collectionloanitems.Collection_id', 'collectionloanitems.member_id',
			'members.FullName', 'members.MemberNo',
			'collections.Catalog_id', 'collections.NomorBarcode', 'collections.CallNumber',
			'catalogs.Title', 'catalogs.Author', 'catalogs.Publisher', 'catalogs.PublishYear',
		];
		$defaultFields = ['collectionloanitems.*', 'members.FullName', 'collections.Catalog_id', 'collections.NomorBarcode'];

		$requestedFields = $this->request->getVar('fields');
		if (is_string($requestedFields)) {
			$requestedFields = explode(',', $requestedFields);
		}
		$requestedFields = array_values(array_intersect(array_map('trim', (array) ($requestedFields ?? [])), $allowedFields));
		if ($requestedFields === []) {
			$requestedFields = $defaultFields;
		}

		$search = $this->request->getVar('search') ?? '';
		$limit = min(100, max(1, (int)($this->request->getVar('limit') ?? 10)));
		$page = max(1, (int)($this->request->getVar('page') ?? 1));
		$offset = ($page - 1) * $limit;
		$order = $this->request->getVar('order');
		$order = in_array($order, $allowedFields, true) && !str_ends_with($order, '.*') ? $order : 'collectionloanitems.ID';
		$direction = strtolower((string) $this->request->getVar('direction')) === 'asc' ? 'asc' : 'desc';
		$loanStatus = $this->request->getVar('LoanStatus');
		$member_id = $this->request->getVar('member_id');
		$barcode = $this->request->getVar('NomorBarcode');

		// Memastikan catalogs.Title selalu dimasukkan
		$fields = array_unique(array_merge($requestedFields, ['catalogs.Title', 'collections.NomorBarcode']));

		// Memulai membangun query
		$builder = $this->db->table('collectionloanitems')
			->select($fields)
			->join('members', 'members.ID = collectionloanitems.member_id', 'left')
			->join('collections', 'collections.ID = collectionloanitems.Collection_id', 'left')
			->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left');

		// Menerapkan filter
		if ($loanStatus !== null) {
			$builder->where('collectionloanitems.LoanStatus', $loanStatus);
		}

		if ($member_id !== null) {
			$builder->where('collectionloanitems.member_id', $member_id);
		}
		if ($barcode !== null) {
			$builder->where('collections.NomorBarcode', $barcode);
		}

		if (!empty($search)) {
			$builder->groupStart()
				->like('catalogs.Title', $search)
				->orLike('members.FullName', $search)
				->groupEnd();
		}

		// Menerapkan urutan
		$builder->orderBy($order, $direction);

		// Mendapatkan jumlah total
		$total_record = $builder->countAllResults(false);
		$total_page = ceil($total_record / $limit);

		// Mendapatkan data berdasarkan halaman
		$data = $builder->limit($limit, $offset)->get()->getResultArray();

		// Menyiapkan respon
		$response = [
			"total_record" => $total_record,
			"per_page" => $limit,
			"total_page" => $total_page,
			"current_page" => $page,
			"result" => [
				"error" => false,
				"param" => [
					"limit" => $limit,
					"offset" => $offset,
					"page" => $page,
					"fields" => $requestedFields,  // Menggunakan field yang diminta awalnya di sini
					"search" => $search,
					"order" => $order,
					"member_id" => $this->request->getVar('member_id') ?? null,
					"direction" => $direction,
					"LoanStatus" => $loanStatus,
				],
				"data" => $data
			]
		];

		return $this->respond($response, 200);
	}


	


}
