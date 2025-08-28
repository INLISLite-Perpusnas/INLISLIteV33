<?php

namespace Permission\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Permission extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $permissionModel;
	protected $validation;
	protected $session;
	protected $menuModel;
	function __construct()
	{
		$this->permissionModel = new \Permission\Models\PermissionModel();
		$this->validation = service('validation');
		$this->session = service('session');
		 $this->menuModel = new \Menu\Models\MenuModel();
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
    $this->validation->setRules([
        'name' => 'trim|required',
    ]);

    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
		$menu=$this->menuModel->where('id', $this->request->getPost('menu'))->first()->name;
        $data = [
            'name'        => $this->request->getPost('name'),
            'route'       => $this->request->getPost('name'),
            'menu'        => $menu,
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
        ];
        try {
            $this->permissionModel->insert($data);

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
