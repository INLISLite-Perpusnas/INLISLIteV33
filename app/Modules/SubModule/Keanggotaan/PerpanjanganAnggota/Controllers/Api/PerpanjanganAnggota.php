<?php

namespace PerpanjanganAnggota\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PerpanjanganAnggotaModel;
use CodeIgniter\Files\File;

class PerpanjanganAnggota extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $perpanjanganModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->perpanjanganModel = new PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/perpanjangan-anggota/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function index()
	{
		$data = $this->perpanjanganModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->perpanjanganModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newPerpanjanganId = $this->perpanjanganModel->insert($save_data);
			if ($newPerpanjanganId) {
				$this->session->setFlashdata('toastr_msg', lang('PerpanjanganAnggota.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status' => 201,
					'error' => null,
					'messages' => [
						'success' => lang('PerpanjanganAnggota.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status' => 400,
					'error' => null,
					'messages' => [
						'error' => lang('PerpanjanganAnggota.info.failed_saved')
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
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$perpanjanganUpdate = $this->perpanjanganModel->update($id, $update_data);
			if ($perpanjanganUpdate) {
				$this->session->setFlashdata('toastr_msg', lang('PerpanjanganAnggota.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status' => 201,
					'error' => null,
					'messages' => [
						'success' => lang('PerpanjanganAnggota.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('PerpanjanganAnggota.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->perpanjanganModel->find($id);
		if ($data) {
			$this->perpanjanganModel->delete($id);
			$response = [
				'status' => 200,
				'error' => null,
				'messages' => [
					'success' => lang('PerpanjanganAnggota.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('PerpanjanganAnggota.info.not_found') . ' ID:' . $id);
		}
	}
}
