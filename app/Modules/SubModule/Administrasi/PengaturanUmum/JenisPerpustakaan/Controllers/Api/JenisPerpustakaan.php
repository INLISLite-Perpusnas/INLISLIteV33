<?php

namespace JenisPerpustakaan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisPerpustakaan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenisperpustakaanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->jenisperpustakaanModel = new \JenisPerpustakaan\Models\JenisPerpustakaanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenisperpustakaan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		helper('jenis_perpustakaan');
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('jenis_perpustakaan as a')
			->select('a.ID as id, a.ID as action, a.Name, a.UpdateDate')
			->select('a.description,a.sort, a.active')
			->select('a.ID as form');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Name', function ($row) {
				$html  =  '<b>' . $row->Name . '</b>';
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
			->edit('form', function ($row) {
				$html = '<a href="javascript:void(0);" data-href="' . base_url('api-jenis-perpustakaan/form/' . $row->id) . '" data-id="' . $row->id . '" data-name="' . $row->Name . '" data-toggle="modal" data-target="#modal_form" class="btn btn-info btn-block show-form"><i class="pe-7s-note2 font-weight-bold"> </i> Form Anggota</a>';
				return $html;
			})
			->edit('action', function ($row) {
				if (is_member('admin')) {
					$edit = '<a href="javascript:void(0);" data-href="' . base_url('api-jenis-perpustakaan/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
					$active = '<a href="' . base_url('master-jenis-perpustakaan/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
					$inactive = '<a href="' . base_url('master-jenis-perpustakaan/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
					$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jenis-perpustakaan/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
					return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
				} else {
				}
			})
			->toJson();
		return $dataTable;
	}

	public function form($id = null)
	{
		$db = db_connect('data');
		$builder = $db->table('members_form as a')
			->select('a.ID as id, a.ID as form_id, a.Member_Field_id as field_id, a.Jenis_Perpustakaan_id as jenis_perpustakaan_id, b.name as field_name, a.active, b.mandatory ')
			->join('member_fields as b', 'b.id = a.Member_Field_id')
			->where('a.Jenis_Perpustakaan_id', $id);
		

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('mandatory', function ($row) {
				$status = $row->mandatory == 1 ? 'Ya' : 'Tidak';
				$class = $row->mandatory == 1 ? 'warning' : 'info';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('active', function ($row) {
				if ($row->mandatory == 0) {
					$active = get_member_form($row->field_id, $row->jenis_perpustakaan_id);
					$checked = $active == 1 ? 'checked' : '';
					$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api-jenis-perpustakaan/update_field/' . $row->id . '?form_id=' . $row->form_id . '&field_id=' . $row->field_id . '&jenis_perpustakaan_id=' . $row->jenis_perpustakaan_id) . '" data-checked="' . $checked . '" data-field="active" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
					return $html;
				}
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenisperpustakaanModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenisperpustakaanModel->find($id);
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
		);

		$save_data_id = $this->jenisperpustakaanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Name' => $this->request->getPost('Name'),
		);

		$update_data_id = $this->jenisperpustakaanModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jenisperpustakaanModel->find($id);
		if ($data) {
			$this->jenisperpustakaanModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Perpustakaan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('JenisPerpustakaan.info.not_found') . ' ID:' . $id);
		}
	}

	public function update_field($id = null)
	{
		$table = $this->request->getGet('table');
		$form_id = $this->request->getGet('form_id');
		$field_id = $this->request->getGet('field_id');
		$jenis_perpustakaan_id = $this->request->getGet('jenis_perpustakaan_id');
		$field = $this->request->getPost('field');
		$value = $this->request->getPost('value');
		$active = ($value == 'true') ? 1 : 0;

		try {
			set_member_form($form_id, $field_id, $jenis_perpustakaan_id, $active, branch_id());
			$response = [
				'error' => false,
				'message' => 'Field ' . ($field) . ' berhasil disimpan',
			];
		} catch (\Throwable $th) {
			$response = [
				'error' => true,
				'message' => 'Field ' . ($field) . ' gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}
}
