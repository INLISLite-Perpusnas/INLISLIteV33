<?php

namespace SelfLoan\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;

class SelfLoan extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $peminjamanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;
	protected $cart;

	function __construct()
	{
		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/peminjaman/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		$this->cart = new \App\Libraries\Cart();

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		helper('reference');
		helper('peminjaman');
		// helper('member');
	}

	public function loan_datatable($member_no = null)
	{
		$db = db_connect('data');
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
			->edit('action', function ($row) {
				$html  = '<a href="javascript:void(0);" data-href="' . base_url('pengembalian-mandiri/do_return/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Kembalikan" class="btn btn-primary return-data"><i class="pe-7s-refresh font-weight-bold"> </i></a>';
				return $html;
			})
			->toJson(true);
		return $dataTable;
	}

	public function koleksi($member_no = null)
	{
		$db = db_connect('data');
		$builder = $db->table('collections col')
			->select('col.ID, col.ID as action')
			->select('col.NomorBarcode, col.UpdateDate')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->join('catalogs cat', 'cat.ID = col.Catalog_id');

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
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/peminjaman/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('peminjaman/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function detail($id = null)
	{
		if (!is_allowed('peminjaman/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->peminjamanModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		if (!is_allowed('peminjaman/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

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
		if (!is_allowed('peminjaman/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

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
		if (!is_allowed('peminjaman/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->peminjamanModel->find($id);
		if ($data) {
			$this->peminjamanModel->delete($id);
			add_log('Hapus Peminjaman', 'peminjaman', 'delete', 'collectionloanitems', $id);
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
}
