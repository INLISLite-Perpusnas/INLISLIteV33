<?php

namespace Permission\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Permission extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $permissionModel;
	protected $validation;
	protected $session;
	function __construct()
	{
		$this->permissionModel = new \Permission\Models\PermissionModel();
		$this->validation = service('validation');
		$this->session = service('session');
	}

	public function detail($id = null)
	{
		$data = $this->permissionModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Permission', 'trim');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$menu = $this->request->getPost('menu');
			$route = $this->request->getPost('route');
			$category = $this->request->getPost('category');
			$description = $this->request->getPost('description');

			$access = (bool) $this->request->getPost('access');
			$index = (bool) $this->request->getPost('index');
			$create = (bool) $this->request->getPost('create');
			$edit = (bool) $this->request->getPost('edit');
			$delete = (bool) $this->request->getPost('delete');

			$permissions = [];
			$permissions[] = $route;
			if ($access) {
				$permissions[] = 'access';
			}

			if ($index) {
				$permissions[] = 'index';
			}

			if ($create) {
				$permissions[] = 'create';
			}

			if ($edit) {
				$permissions[] = 'edit';
			}

			if ($delete) {
				$permissions[] = 'delete';
			}

			$save_data = [];
			$update_data = [];
			$permissions = array_unique($permissions);
			foreach ($permissions as $index => $permissionKey) {
				$permissionName = $route . "/" . $permissionKey;
				$permission = $this->permissionModel->where('name', $permissionName)->first();
				if (empty($permission)) {
					$save_data[] = [
						'name' => $permissionName,
						'route' => $route,
						'menu' => $menu,
						'category' => $category,
						'description' => $description
					];
				} else {
					$update_data[] = [
						'id' => $permission->id,
						'name' => $permissionName,
						'route' => $route,
						'menu' => $menu,
						'category' => $category,
						'description' => $description
					];
				}
			}

			try {
				if (!empty($save_data) || !empty($update_data)) {

					if (!empty($save_data)) {
						$this->permissionModel->insertBatch($save_data);
					}

					if (!empty($update_data)) {
						$this->permissionModel->updateBatch($update_data, 'id');
					}

					$this->session->setFlashdata('toastr_msg', 'Permission berhasil disimpan');
					$this->session->setFlashdata('toastr_type', 'success');
					$response = [
						'status'   => 200,
						'error'    => null,
						'messages' => [
							'success' => 'Permission berhasil disimpan'
						]
					];
					reloadPermission();
					return $this->respondCreated($response);
				}
			} catch (\Exception $e) {
				$response = [
					'status'   => 400,
					'error'    => $e->getMessage(),
					'messages' => [
						'error' => 'Permission gagal disimpan'
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
		$this->validation->setRule('name', 'Nama Permission', 'trim');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'route' => $this->request->getPost('route'),
				'category' => $this->request->getPost('category'),
				'description' => $this->request->getPost('description'),
			);
			$updatePermission = $this->permissionModel->update($id, $update_data);
			if ($updatePermission) {
				$this->session->setFlashdata('toastr_msg', 'Permission berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Permission berhasil disimpan'
					]
				];
				reloadPermission();
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Permission gagal disimpan</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->permissionModel->find($id);
		if ($data) {
			$this->permissionModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Permission berhasil dihapus'
				]
			];
			db_connect()->table('auth_groups_permissions')->where('permission_id', $id)->delete();
			db_connect()->table('auth_users_permissions')->where('permission_id', $id)->delete();
			reloadPermission();
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}
}
