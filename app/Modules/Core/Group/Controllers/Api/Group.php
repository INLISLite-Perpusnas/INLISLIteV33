<?php

namespace Group\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Group extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $groupModel;
	protected $validation;
	protected $session;
	function __construct()
	{
		$this->groupModel = new \Group\Models\GroupModel();
		$this->session = session();
		$this->validation = service('validation');
	}
	public function index()
	{
		$data = $this->groupModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->groupModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Role', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'description' => $this->request->getPost('description'),
			);
			$saveGroup = $this->groupModel->insert($save_data);
			if ($saveGroup) {
				$this->session->setFlashdata('toastr_msg', 'Group berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 200,
					'error'    => null,
					'messages' => [
						'success' => 'Group berhasil disimpan'
					]
				];
				reloadPermission();
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' => 'Group gagal disimpan'
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('name', 'Nama Role', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'description' => $this->request->getPost('description'),
			);
			$updateGroup = $this->groupModel->update($id, $update_data);
			if ($updateGroup) {
				$this->session->setFlashdata('toastr_msg', 'Group berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Group berhasil disimpan'
					]
				];
				reloadPermission();
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Group gagal disimpan</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->groupModel->find($id);
		if ($data) {
			$this->groupModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Group berhasil dihapus'
				]
			];
			reloadPermission();
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}
}
