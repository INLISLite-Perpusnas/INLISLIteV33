<?php

namespace TujuanKunjungan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class TujuanKunjungan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $tujuankunjunganModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->tujuankunjunganModel = new \TujuanKunjungan\Models\TujuanKunjunganModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/tujuankunjungan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('tujuan_kunjungan as a')
			->select('a.ID as id, a.ID as action, a.Code, a.TujuanKunjungan, a.Member, a.NonMember, a.Rombongan, a.active, a.UpdateDate');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')

			->edit('Member', function ($row) {
				$color = $row->Member == 1 ? 'success' : 'warning';
				$label = $row->Member == 1 ? 'Aktif' : 'Non Aktif';
				$html = '<span class="badge badge-' . $color . '" style="min-width: 100px">' . $label . '</span>';
				return $html;
			})

			->edit('NonMember', function ($row) {
				$color = $row->NonMember == 1 ? 'success' : 'warning';
				$label = $row->NonMember == 1 ? 'Aktif' : 'Non Aktif';
				$html = '<span class="badge badge-' . $color . '" style="min-width: 100px">' . $label . '</span>';
				return $html;
			})

			->edit('Rombongan', function ($row) {
				$color = $row->Rombongan == 1 ? 'success' : 'warning';
				$label = $row->Rombongan == 1 ? 'Aktif' : 'Non Aktif';
				$html = '<span class="badge badge-' . $color . '" style="min-width: 100px">' . $label . '</span>';
				return $html;
			})

			->edit('active', function ($row) {
				$color = $row->active == 1 ? 'primary' : 'secondary';
				$label = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$html = '<span class="badge badge-' . $color . ' badge-pill" style="min-width: 100px">' . $label . '</span>';
				return $html;
			})

			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/tujuan-kunjungan/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-tujuan-kunjungan/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-tujuan-kunjungan/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-tujuan-kunjungan/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->tujuankunjunganModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->tujuankunjunganModel->find($id);
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
			'TujuanKunjungan' => $this->request->getPost('TujuanKunjungan'),
			'Member' => $this->request->getPost('Member'),
			'NonMember' => $this->request->getPost('NonMember'),
			'Rombongan' => $this->request->getPost('Rombongan'),
		);

		$save_data_id = $this->tujuankunjunganModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Tujuan Kunjungan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Kelamin berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Kelamin gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Code' => $this->request->getPost('Code'),
			'TujuanKunjungan' => $this->request->getPost('TujuanKunjungan'),
			'Member' => $this->request->getPost('Member'),
			'NonMember' => $this->request->getPost('NonMember'),
			'Rombongan' => $this->request->getPost('Rombongan'),
		);

		$update_data_id = $this->tujuankunjunganModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Kelamin berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Kelamin berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Kelamin gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->tujuankunjunganModel->find($id);
		if ($data) {
			$this->tujuankunjunganModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Kelamin berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('TujuanKunjungan.info.not_found') . ' ID:' . $id);
		}
	}
}
