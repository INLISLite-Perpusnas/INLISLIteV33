<?php

namespace Reference\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Reference extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $referenceModel;
	public $menuModel;

	public $validation;
	public $session;
	function __construct()
	{
		$this->session = session();
		$this->validation = service('validation');
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();

		helper(['app', 'auth']);

		$this->referenceModel = new \Reference\Models\ReferenceModel();
		$this->menuModel = new \Menu\Models\MenuModel();
	}

	public function detail($id = null)
	{
		$data = $this->referenceModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$menu_id = $this->request->getPost('menu_id');
		$this->validation->setRule('name', 'Nama Referensi', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$form_slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $form_slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
				'menu_id' => $menu_id,
			);

			$newReferenceId = $this->referenceModel->insert($save_data);
			if ($newReferenceId) {
				$this->session->setFlashdata('toastr_msg', 'Referensi berhasil ditambah');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Referensi berhasil ditambah'
					],
					'data'    => $newReferenceId,
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' => 'Referensi gagal ditambah'
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
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			if (!empty($this->request->getPost('slug'))) {
				$update_data['slug'] = $this->request->getPost('slug');
			}

			$files = (array) $this->request->getPost('file_image');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;
					}
				}
				$update_data['file_image'] = implode(',', $listed_file);
			}

			$referenceUpdate = $this->referenceModel->update($id, $update_data);
			if ($referenceUpdate) {
				$this->session->setFlashdata('toastr_msg', 'Reference berhasil diubah');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Reference berhasil diubah'
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Reference gagal diubah</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function list($slug)
	{
		$data = $this->referenceModel
			->select('c_references.*')
			->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner')
			->where('c_menus.slug', $slug)
			->orderBy('c_references.sort', 'asc')
			->findAll();

		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with slug ' . $slug);
		}
	}
}
