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

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('c_parameters as a')
			->select('a.id, a.id as action, a.name,  a.value, a.description, a.category');

		if(!empty($slug)){
			$builder->where('category', $slug);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('category', function($row){
				return '<b>'.$row->category.'</b>';
			})
			->edit('description', function($row){
				return character_limiter($row->description??'',100);
			})
			->edit('action', function($row){
				$edit = '<a href="javascript:void(0);" data-href="'.base_url('api/parameter/show/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah Parameter" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('parameter/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus  Profil" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit ." ". $delete;
			})
			// ->filter(function ($builder, $request) {
			// 	if ($request->category){
			// 		$builder->where('b.slug', $request->category);
			// 	}
			// })
			->toJson();

		return $dataTable;
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Parameter', 'required');
		$this->validation->setRule('value', 'Nilai Parameter', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$name = $this->request->getPost('name');
				$value = $this->request->getPost('value');

				$param = $this->parameterModel->where('name', $name)->first();

				if ($param) {
					$update_data = array(
						'name' => $this->request->getPost('name'),
						'value' => $this->request->getPost('value'),
						'description' => $this->request->getPost('description'),
						'category' => $this->request->getPost('category'),
					);

					$paramSave = $this->parameterModel->update($param->id, $update_data);
				} else {
					$save_data = array(
						'name' => $this->request->getPost('name'),
						'value' => $this->request->getPost('value'),
						'description' => $this->request->getPost('description'),
						'category' => $this->request->getPost('category'),
					);

					$paramSave = $this->parameterModel->insert($save_data);
				}

				if ($paramSave) {
					$response = [
						'error'    => false,
						'messages' => 'Parameter brhasil disimpan'
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
		$this->validation->setRule('name', 'Nama Parameter', 'required');
		$this->validation->setRule('value', 'Nilai Parameter', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'value' => $this->request->getPost('value'),
				'description' => $this->request->getPost('description'),
				'category' => $this->request->getPost('category'),
			);

			$parameterUpdate = $this->parameterModel->update($id, $update_data);
			if ($parameterUpdate) {
				$response = [
					'error'    => false,
					'messages' => 'Reference berhasil diubah'
				];
				return $this->respond($response);
			} else {
				$response = [
					'error'    => true,
					'messages' => 'Reference gagal diubah'
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
				$name = $this->request->getPost('name');
				$param = $this->parameterModel->where('name', $name)->first();

				$update_data = array(
					'name' => $name,
					'value' => $this->request->getPost('status'),
				);

				$paramUpdate = $this->parameterModel->update($param->id, $update_data);
				if ($paramUpdate) {
					$this->session->setFlashdata('toastr_msg', 'Parameter berhasil diupdate');
					$this->session->setFlashdata('toastr_type', 'success');
					$response = [
						'status'   => 200,
						'error'    => null,
						'messages' => [
							'success' => 'Parameter brhasil diupdate'
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
