<?php

namespace Pengembalian\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
use Peminjaman\Models\CollectionLoanModel;

//use Hermawan\DataTables\DataTable;

class Pengembalian extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $pengembalianModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;
	protected $collectionLoanModel;
	public $collectionModel;
	protected $collectionLoanItemModel;
	protected $cart;

	function __construct()
	{
		$this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/pengembalian/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		$this->cart = new \App\Libraries\Cart();

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('pengembalian');
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
			->join('members m', 'm.ID = cli.member_id')
			->join('location_library loc', 'loc.ID = col.Location_Library_id')
			->where('cli.LoanStatus', 'Return');

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
			->edit('LoanDate', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . $row->LoanDate . '</badge>';
				$html .=  '<badge class="badge badge-warning badge-pill">' . $row->DueDate . '</badge>';
				return $html;
			})
			->edit('ActualReturn', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->ActualReturn . '</badge>';
				return $html;
			})
			->edit('LateDays', function ($row) {
				$periods = \Carbon\CarbonPeriod::create($row->DueDate, $row->ActualReturn);
				$dates = [];
				foreach ($periods as $period) {
					if (in_array($period->format('N'), ['6', '7'])) continue;
					$dates[] = $period->format('Y-m-d');
				}

				$diff = '+' . count($dates);
				if (count($dates) <= 3) {
					if (count($dates) == 0) {
						$diff_class = 'info';
						$diff = count($dates);
					} else {
						$diff_class = 'warning';
					}
				} else {
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
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/sirkulasi-pengembalian/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('pengembalian/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function create()
	{
		// Ambil data JSON dari request
		$json = $this->request->getJSON();

		if (!$json) {
			return $this->fail('Invalid JSON input', 400);
		}

		// $member_id = $json->member_id ?? null;
		// $branch_id = $json->branch_id ?? null;
		$collection_id = $json->collection_id ?? null;
		$user_id = $json->user_id ?? null;


		// Validasi input
		if (!$collection_id || !$user_id) {
			return $this->fail('Missing required fields', 400);
		}

		try {
			// Check if member exists
			$collectionloanitems = $this->collectionLoanItemModel
				->where('LoanStatus', 'Loan')
				->where('Collection_id', $collection_id)
				->first();

			if (!$collectionloanitems) { // Use `!` to check if $collectionloanitems is null
				return $this->fail('Collection loan items not found', 404);
			}

			// $member_id = $collectionloanitems->member_id;
			$ID=$collectionloanitems->ID;



			// Simpan ke tabel collectionloanitems
			$loanItemData = [
				'LoanStatus' => "Return", // Atur status peminjaman sesuai kebutuhan
				'ActualReturn' => date('Y-m-d H:i:s'),

				'UpdateBy' => $user_id,
				'UpdateDate' => date('Y-m-d H:i:s'),
				'CreateTerminal' => $this->request->getIPAddress(),
			];

			$this->collectionLoanItemModel->update($ID, $loanItemData);

			// Update status koleksi menjadi 5
			$this->collectionModel->where('id', $collection_id)
				->set(['Status_id' => 1])
				->update();

			// Kirim respons sukses
			return $this->respond(['message' => 'Loan return created successfully'], 200);
		} catch (\Exception $e) {
			return $this->fail('Error: ' . $e->getMessage(), 500);
		}
	}
	public function loan_datatable()
	{
		$db = db_connect();
		$builder = $db->table('collections col')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('col.NomorBarcode, col.UpdateDate')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->join('catalogs cat', 'cat.ID = col.Catalog_id')
			->join('collectionloanitems cli', 'cli.Collection_id = col.ID')
			->join('members m', 'm.ID = cli.member_id')
			->where('cli.LoanStatus', 'Loan');
			if(!empty(branch_id())) {
				$builder->where('col.Branch_id', branch_id());
			}

		$cart_cli_arr = array();
		$carts = get_cart_return();
		if (!empty($carts)) {
			foreach ($carts as $row) {
				$cart_cli_arr[] = $row->options->collection->ID;
			}
			if (!empty($cart_cli_arr)) {
				$builder->whereNotIn('col.ID', $cart_cli_arr);
			}
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function ($row) {
				$html = '';
				if ($row->DueDate > date('Y-m-d')) {
					$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
				}
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
							<div class="widget-subheading">' . $row->MemberNo . '</div>
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
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/sirkulasi-pengembalian/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('pengembalian/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->pengembalianModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->pengembalianModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function edit($id = null)
	{
		$update_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$update_data_id = $this->pengembalianModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Pengembalian berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Pengembalian berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Pengembalian gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->pengembalianModel->find($id);
		if ($data) {
			$this->pengembalianModel->delete($id);
			$response = [
				'error' => false,
				'message' => 'Pengembalian berhasil dihapus',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Pengembalian gagal dihapus. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function switch($id = null)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$update_data_id = $this->pengembalianModel->update($id, array($field => ($value == 'true') ? 1 : 0));

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
}