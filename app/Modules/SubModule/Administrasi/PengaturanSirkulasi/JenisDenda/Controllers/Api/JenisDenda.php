<?php

namespace JenisDenda\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisDenda extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenisdendaModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		$this->jenisdendaModel = new \JenisDenda\Models\JenisDendaModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenisdenda/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('jenis_denda as a')
			->select('a.ID, a.ID as action, a.Name as Nama, a.Code, a.UpdateDate')
			->select('a.active');

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
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/jenis-denda/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" aria-label="Ubah" class="ina-action show-data"><i class="fas fa-pen" aria-hidden="true"></i></a>';
				$active = '<a href="' . base_url('/master-jenis-denda/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Aktifkan" aria-label="Aktifkan" class="ina-action active-data"><i class="fas fa-eye" aria-hidden="true"></i></a>';
				$inactive = '<a href="' . base_url('/master-jenis-denda/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Nonaktifkan" aria-label="Nonaktifkan" class="ina-action draft-data"><i class="fas fa-eye-slash" aria-hidden="true"></i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('/master-jenis-denda/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus" aria-label="Hapus" class="ina-action ina-action-danger remove-data"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenisdendaModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenisdendaModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'Name' => $this->request->getPost('Name'),
			'Code' => $this->request->getPost('Code'),
		);

		$save_data_id = $this->jenisdendaModel->insert($save_data);
		if ($save_data_id) {
			$response = [
				'error' => false,
				'message' => 'Jenis Denda berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Denda gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Name' => $this->request->getPost('Name'),
			'Code' => $this->request->getPost('Code'),
		);

		$update_data_id = $this->jenisdendaModel->update($id, $update_data);
		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Jenis Denda berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Denda gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jenisdendaModel->find($id);
		if ($data) {
			$this->jenisdendaModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Denda berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('JenisDenda.info.not_found') . ' ID:' . $id);
		}
	}
}
