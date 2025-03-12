<?php

namespace JenisPelanggaran\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisPelanggaran extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenispelanggaranModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->jenispelanggaranModel = new \JenisPelanggaran\Models\JenisPelanggaranModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenispelanggaran/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('jenis_pelanggaran as a')
			->select('a.ID as id, a.ID as action, a.JenisPelanggaran, a.Keterangan, a.UpdateDate')
			->select('a.description,a.sort, a.active');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('JenisPelanggaran', function ($row) {
				$html  =  '<b>' . $row->JenisPelanggaran . '</b>';
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
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/jenis-pelanggaran/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('jenis-pelanggaran/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('jenis-pelanggaran/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('jenis-pelanggaran/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenispelanggaranModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenispelanggaranModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'JenisPelanggaran' => $this->request->getPost('JenisPelanggaran'),
			'Keterangan' => $this->request->getPost('Keterangan'),
		);

		$save_data_id = $this->jenispelanggaranModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Pelanggaran berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Pelanggaran berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Pelanggaran gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'JenisPelanggaran' => $this->request->getPost('JenisPelanggaran'),
			'Keterangan' => $this->request->getPost('Keterangan'),
		);

		$update_data_id = $this->jenispelanggaranModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Pelanggaran berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Pelanggaran berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Pelanggaran gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jenispelanggaranModel->find($id);
		if ($data) {
			$this->jenispelanggaranModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Pelanggaran berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('JenisPelanggaran.info.not_found') . ' ID:' . $id);
		}
	}
}
