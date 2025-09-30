<?php

namespace JenisIdentitas\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisIdentitas extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenisidentitasModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		$this->jenisidentitasModel = new \JenisIdentitas\Models\JenisIdentitasModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenisidentitas/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$branchId = user()->branch_id;
		$builder = $db->table('master_jenis_identitas as a')
			->select('a.id, a.id as action,a.Branch_id, a.Nama, a.UpdateDate')
			->select('a.active');
			//->whereIn('a.Branch_id', [$branchId, 0]);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Nama', function ($row) {
				$html  =  '<b>' . $row->Nama . '</b>';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) use ($branchId) {
				$edit = '';
				//if ($row->Branch_id == $branchId) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/jenis-identitas/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-jenis-identitas/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-jenis-identitas/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jenis-identitas/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			
		})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenisidentitasModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenisidentitasModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		
		$save_data = array(
			'Nama' => $this->request->getPost('Nama'),
			'Branch_id' => user()->branch_id,
		);

		$save_data_id = $this->jenisidentitasModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Identitas berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Identitas berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Identitas gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Nama' => $this->request->getPost('Nama'),
		);

		$update_data_id = $this->jenisidentitasModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Identitas berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Identitas berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Identitas gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jenisidentitasModel->find($id);
		if ($data) {
			$this->jenisidentitasModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Identitas berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('JenisIdentitas.info.not_found') . ' ID:' . $id);
		}
	}
}
