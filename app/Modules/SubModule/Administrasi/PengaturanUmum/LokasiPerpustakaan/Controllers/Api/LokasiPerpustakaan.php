<?php

namespace LokasiPerpustakaan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class LokasiPerpustakaan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $lokasiperpustakaanModel;
	public $validation;
	public $session;
	public $modulePath;
	public $uploadPath;

	function __construct()
	{
		$this->lokasiperpustakaanModel = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/lokasiperpustakaan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
		$builder = $db->table('location_library as a')
			->select('a.ID, a.ID as action, a.Code, a.Name, a.Address')
			->select('a.description, a.sort, a.active');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Code', function ($row) {
				$html  =  '<b>' . $row->Code . '</b>';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('Address', function ($row) {
				return $row->Address;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api-lokasi-perpustakaan/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-lokasi-perpustakaan/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-lokasi-perpustakaan/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-lokasi-perpustakaan/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->lokasiperpustakaanModel->findAll();
		return $this->respond($data, 200);
	}

	public function location_library($branch_id = '')
	{
		$response = $this->lokasiperpustakaanModel
			->select('Code,Name')
			->where('branch_id', $branch_id)
			->findAll();
		return $this->simpleResponse($response);
	}


	public function detail($id = null)
	{
		$data = $this->lokasiperpustakaanModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('Name'),
			'Address' => $this->request->getPost('Address'),
			'Branch_id' => branch_id(),
		);

		$save_data_id = $this->lokasiperpustakaanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Lokasi Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Lokasi Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Lokasi Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('Name'),
			'Address' => $this->request->getPost('Address'),
		);

		$update_data_id = $this->lokasiperpustakaanModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Lokasi Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Lokasi Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Lokasi Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->lokasiperpustakaanModel->find($id);
		if ($data) {
			$this->lokasiperpustakaanModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Lokasi Perpustakaan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('LokasiPerpustakaan.info.not_found') . ' ID:' . $id);
		}
	}
}
