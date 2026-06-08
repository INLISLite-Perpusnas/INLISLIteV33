<?php

namespace Access\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Access extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $auth;
    public $authorize;
	public $groupModel;
	public $permissionModel;
	public $validation;
	public $session;
	function __construct()
	{
		$this->session = session();
		$this->validation = service('validation');
		$this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
		
		helper(['access']);

		$this->groupModel = new \Group\Models\GroupModel();
		$this->permissionModel = new \Permission\Models\PermissionModel();
		$this->validation = \Config\Services::validation();
	}

	public function add_to_group($group_id)
	{
		$response = false;
		$permissions = $this->permissionModel->findAll();
		foreach ($permissions as $permission){
			$this->authorize->removePermissionFromGroup($permission->name, $group_id);
		}
		
		$access = '';
		$new_permissions = $this->request->getPost('permissions');
		foreach($new_permissions as $permission){
			$permission = clean_fullscreen($permission);
			$exist_permission = $this->authorize->permission($permission);
			if(empty($exist_permission)){
				$this->authorize->createPermission($permission, '');
			}

			$access .= $permission. ',';
			$this->authorize->addPermissionToGroup($permission, $group_id);
		}
		
		$this->session->setFlashdata('toastr_msg', 'Access berhasil diupdate');
		$this->session->setFlashdata('toastr_type', 'success');
		$response = [
			'status'   => 201,
			'error'    => null,
			'messages' => [
				'success' => 'Access berhasil diupdate'
			]
		];
		return $this->respondCreated($response);

	}
}
