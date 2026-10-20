<?php

namespace Parameter\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Parameter extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $parameterModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->parameterModel = new \Parameter\Models\ParameterModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/parameter/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['reference', 'thumbnail']);
	}

	public function datatable()
	{
		$db = db_connect('data');
		$builder = $db->table('settingparameters as a')
			->select('a.ID, a.ID as action, a.Name, a.Value, a.Branch_id');


		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Name', function($row) { return esc($row->Name); })
			->edit('Value', function($row) { return esc($row->Value ?? ''); })
			->edit('Branch_id', function($row) { return esc($row->Branch_id ?? ''); })
			->edit('action', function($row){
				$edit = '<a href="javascript:void(0);" data-href="'.base_url('api/parameter/show/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Ubah Parameter" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('parameter/delete/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Hapus Parameter" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit ." ". $delete;
			})
			->toJson();

		return $dataTable;
	}

	public function create()
	{
		$this->validation->setRule('Name', 'Nama Parameter', 'required|max_length[50]');
		$this->validation->setRule('Value', 'Nilai Parameter', 'permit_empty');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$name = $this->request->getPost('Name');

				$param = $this->parameterModel->where('Name', $name)->where('Branch_id', null)->first();

				if ($param) {
					$update_data = array(
						'Name' => $this->request->getPost('Name'),
						'Value' => $this->request->getPost('Value'),
					);

					$paramSave = $this->parameterModel->update($param->ID, $update_data);
				} else {
					$save_data = array(
						'Name' => $this->request->getPost('Name'),
						'Value' => $this->request->getPost('Value'),
					);

					$save_data['Branch_id'] = null;
					$paramSave = $this->parameterModel->insert($save_data);
				}

				if ($paramSave) {
					$response = [
						'error'    => false,
						'messages' => 'Parameter berhasil disimpan'
					];
					return $this->respond($response);
				} else {
					$response = [
						'error'    => true,
						'messages' => 'Parameter gagal disimpan'
					];
					return $this->respond($response);
				}
			} else {
				$message = $this->validation->listErrors();
				return $this->fail($message, 400);
			}
		}
	}

	public function show($id = null)
    {
		$data = $this->parameterModel->find($id);
		if ($data) {
			return $this->respond($data, 200);
		} else {
			return $this->failNotFound('Not found ID: ' . $id);
		}
    }

	public function edit($id = null)
	{
		$this->validation->setRule('Name', 'Nama Parameter', 'required|max_length[50]');
		$this->validation->setRule('Value', 'Nilai Parameter', 'permit_empty');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'Name' => $this->request->getPost('Name'),
				'Value' => $this->request->getPost('Value'),
			);

			$parameterUpdate = $this->parameterModel->update($id, $update_data);
			if ($parameterUpdate) {
				$response = [
					'error'    => false,
					'messages' => 'Parameter berhasil diubah'
				];
				return $this->respond($response);
			} else {
				$response = [
					'error'    => true,
					'messages' => 'Parameter gagal diubah'
				];
				return $this->respond($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function setting()
	{
		$this->validation->setRule('status', 'Status', 'trim');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$name = $this->request->getPost('Name');
				$param = $this->parameterModel->where('Name', $name)->where('Branch_id', null)->first();

				$update_data = array(
					'Name' => $name,
					'Value' => $this->request->getPost('status'),
				);

				$paramUpdate = $this->parameterModel->update($param->ID, $update_data);
				if ($paramUpdate) {
					$this->session->setFlashdata('toastr_msg', 'Parameter berhasil diupdate');
					$this->session->setFlashdata('toastr_type', 'success');
					$response = [
						'status'   => 200,
						'error'    => null,
						'messages' => [
							'success' => 'Parameter berhasil diupdate'
						]
					];
					return $this->respondUpdated($response);
				} else {
					$this->session->setFlashdata('toastr_msg', 'Parameter gagal diupdate');
					$this->session->setFlashdata('toastr_type', 'warning');
					$response = [
						'status'   => 200,
						'error'    => null,
						'messages' => [
							'success' => 'Parameter gagal diupdate'
						]
					];
					return $this->respondUpdated($response);
				}
			} else {
				$message = $this->validation->listErrors();
				return $this->fail($message, 400);
			}
		}
	}
}
