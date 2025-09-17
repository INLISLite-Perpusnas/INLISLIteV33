<?php

namespace SelfLoan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\Exceptions\DatabaseException;

//use Hermawan\DataTables\DataTable;

class SelfLoan extends \Base\Controllers\BaseResourceController
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

	function __construct()
	{
		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
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
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('cl.UpdateDate')
			->select('col.NomorBarcode')
			->select('a.Title, a.PublishLocation, a.Publisher, a.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->select('loc.Name as LocationLibrary')
			->join('collectionloanitems cli', 'cli.CollectionLoan_id = cl.ID')
			->join('collections col', 'col.ID = cli.Collection_id')
			->join('catalogs a', 'a.ID = col.Catalog_id')
			->join('branchs b', 'b.ID = a.Branch_id', 'inner')
			->join('members m', 'm.ID = cli.member_id')
			->join('location_library loc', 'loc.ID = col.Location_Library_id')
			->where('cli.LoanStatus', 'Loan');

		if (user()->category == 'admin') {
		} elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
			$npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
			$builder->where('b.NPP_Provinsi_id', $npp_provinsi_id);
		} elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
			$npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
			$builder->where('b.NPP_KabKota_id', $npp_kabkota_id);
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} else {
			$builder->where('a.Branch_id', branch_id());
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('CollectionLoan_id', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-id-card fa-3x text-secondary"></i>
						</div>
						<div class="widget-content-left text-secondary">
							<dl class="dl-horizontal mb-0">
								<dt class="font-weight-bold mb-0"><i class="fa fa-user text-secondary"></i> No. Anggota</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->MemberNo . '  <span class="text-secondary">(' . $row->Fullname . ')</span></a></dd>
								<dt class="font-weight-bold mb-0"><i class="fa fa-hashtag text-secondary"></i> No. Transaksi</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->CollectionLoan_id . '</a></dd>
							</dl>
						</div>
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
							<div class="widget-heading">' . $row->NomorBarcode . '</div>
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
							<div class="widget-heading text-primary">' . $row->Publisher . '</div>
							<div class="widget-heading">' . $row->Title . '</div>
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
			->edit('LateDays', function ($row) {
				if ($row->DueDate > date('Y-m-d')) {
					$periods = \Carbon\CarbonPeriod::create(date('Y-m-d'), $row->DueDate);
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '-' . count($dates);
					if (count($dates) <= 3) {
						if (count($dates) == 0) {
							$diff_class = 'info';
							$diff = '+' . count($dates);
						} else {
							$diff_class = 'warning';
						}
					} else {
						$diff_class = 'secondary';
					}
				} else {
					$periods = \Carbon\CarbonPeriod::create($row->DueDate, date('Y-m-d'));
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '+' . count($dates);
					$diff_class = 'danger';
				}

				$html  =  '<badge class="badge badge-' . $diff_class . ' badge-pill">' . $diff . ' hari</badge>';
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

	public function loan_datatable($member_no = null)
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
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . $row->CollectionLoan_id . '</div>
							<div class="widget-subheading">' . $row->NomorBarcode . '</div>
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
							<div class="widget-heading text-primary">' . $row->Publisher . '</div>
							<div class="widget-heading">' . $row->Title . '</div>
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
			->edit('LateDays', function ($row) {
				if ($row->DueDate > date('Y-m-d')) {
					$periods = \Carbon\CarbonPeriod::create(date('Y-m-d'), $row->DueDate);
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '-' . count($dates);
					if (count($dates) <= 3) {
						if (count($dates) == 0) {
							$diff_class = 'info';
							$diff = '+' . count($dates);
						} else {
							$diff_class = 'warning';
						}
					} else {
						$diff_class = 'secondary';
					}
				} else {
					$periods = \Carbon\CarbonPeriod::create($row->DueDate, date('Y-m-d'));
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '+' . count($dates);
					$diff_class = 'danger';
				}

				$html  =  '<badge class="badge badge-' . $diff_class . ' badge-pill">' . $diff . ' hari</badge>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('sirkulasi-pengembalian/do_return/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Kembalikan" class="btn btn-primary return-data"><i class="pe-7s-refresh font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('sirkulasi-peminjaman/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
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
				$html  = '<b>' . $row->NomorBarcode . '</b>';
				return $html;
			})
			->edit('Title', function ($row) {
				$html  = '<b>' . $row->Publisher . '</b><br>';
				$html .= $row->Title;
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
				if ($row->DueDate > date('Y-m-d')) {
					$periods = \Carbon\CarbonPeriod::create(date('Y-m-d'), $row->DueDate);
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '-' . count($dates);
					if (count($dates) <= 3) {
						if (count($dates) == 0) {
							$diff_class = 'info';
							$diff = '+' . count($dates);
						} else {
							$diff_class = 'warning';
						}
					} else {
						$diff_class = 'secondary';
					}
				} else {
					$periods = \Carbon\CarbonPeriod::create($row->DueDate, date('Y-m-d'));
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '+' . count($dates);
					$diff_class = 'danger';
				}

				$html  =  '<b>' . $diff . ' hari</b>';
				return $html;
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

			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			$member_loc = get_ref_table('memberloanauthorizelocation', 'LocationLoan_id', 'Member_id="' . $member->ID . '"', 'data');
			$member_loc_arr = get_object_array($member_loc, 'LocationLoan_id');
			if (!empty($member_loc_arr)) {
				$builder->whereIn('col.Location_Library_id', $member_loc_arr);
			}

			$member_cat = get_ref_table('memberloanauthorizecategory', 'CategoryLoan_id', 'Member_id="' . $member->ID . '"', 'data');
			$member_cat_arr = get_object_array($member_cat, 'CategoryLoan_id');
			if (!empty($member_cat_arr)) {
				$builder->whereIn('col.Category_id', $member_cat_arr);
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
							<div class="widget-heading">' . $row->NomorBarcode . '</div>
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
							<div class="widget-heading text-primary">' . $row->Publisher . '</div>
							<div class="widget-heading">' . $row->Title . '</div>
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

	public function index()
	{
		$data = $this->peminjamanModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->peminjamanModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$save_data_id = $this->peminjamanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Peminjaman berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Peminjaman berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Peminjaman gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$update_data_id = $this->peminjamanModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Peminjaman berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Peminjaman berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Peminjaman gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->peminjamanModel->find($id);
		if ($data) {
			$this->peminjamanModel->delete($id);
			$response = [
				'error' => false,
				'message' => 'Peminjaman berhasil dihapus',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Peminjaman gagal dihapus. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
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
	   
		$collection_loan = get_ref_single('collectionloans', 'ID IS NOT NULL', 'data');
		$increment = ((int) substr($collection_loan->ID, -5)) + 1;
		$collection_loan_id = get_pad_number($increment, date('ymd'), 5);
	
		// Validasi input
		if (!$member_id || !$branch_id || !$collection_id || !$user_id) {
			return $this->fail('Missing required fields', 400);
		}
	
		try {
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
			$branchid=$this->collectionModel
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
			return $this->respond(['message' => 'Loan created successfully'], 200);
	
		} catch (\Exception $e) {
			return $this->fail('Error: ' . $e->getMessage(), 500);
		}
	}

	public function loan_history() {

		// Mengambil parameter dari request
		$requestedFields = $this->request->getVar('fields') ?? [
			'collectionloanitems.*', 
			'members.FullName', 
			'collections.Catalog_id',
			'collections.NomorBarcode'  // Added NomorBarcode to default fields
		];
		$search = $this->request->getVar('search') ?? '';
		$limit = (int)($this->request->getVar('limit') ?? 10);
		$page = (int)($this->request->getVar('page') ?? 1);
		$offset = ($page - 1) * $limit;
		$order = $this->request->getVar('order') ?? 'collectionloanitems.ID';
		$direction = $this->request->getVar('direction') ?? 'desc';
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