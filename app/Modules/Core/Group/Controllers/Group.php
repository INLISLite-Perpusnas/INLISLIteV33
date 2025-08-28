<?php

namespace Group\Controllers;

use Base\Models\BaseModel;
use Group\Models\GroupPermissionModel;

class Group extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $userModel;
	public $groupModel;
	public $permissionModel;

	function __construct()
	{
		$this->userModel = new \User\Models\UserModel();
		$this->groupModel = new \Group\Models\GroupModel();
		$this->permissionModel = new \Permission\Models\PermissionModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}

	public function index()
	{
		$groups = $this->groupModel->orderBy('id', 'asc')->findAll();
		$this->data['title'] = 'Role';
		$this->data['groups'] = $groups;
		echo view('Group\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		$this->data['title'] = 'Detail Role';
		$user = $this->auth->user($id)->first();
		$currentGroups = $this->auth->getUsersGroups($id)->getResult();
		$this->data['user'] = $user;
		$this->data['currentGroups'] = $currentGroups;
		$this->data['auth'] = $this->auth;
		echo view('Group\Views\view', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter(id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$groupDelete = $this->authorize->deleteGroup($id);
		if ($groupDelete) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group berhasil dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/group');
		}
	}

	public function enable($id = null)
	{
		$groupUpdate = $this->groupModel->update($id, array('active' => 1));

		if ($groupUpdate) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil diaktifkan');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group gagal diaktifkan');
			set_message('toastr_type', 'warning');
			set_message('message', 'Group gagal diaktifkan');
			return redirect()->to('/group');
		}
	}

	public function disable($id = null)
	{
		$groupUpdate = $this->groupModel->update($id, array('active' => 0));

		if ($groupUpdate) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil dinonaktifkan');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group gagal dinonaktifkan');
			set_message('toastr_type', 'warning');
			set_message('message', 'Group gagal dinonaktifkan');
			return redirect()->to('/group');
		}
	}

	public function permissions($group_id = null)
	{
		$this->data['title'] = 'Permission - Role';
		$group = get_single('auth_groups', 'id, name', 'id =' . $group_id);
		$groupPermissionModel = new GroupPermissionModel();
		$permissionModel = new BaseModel('auth_permissions');

		$permissions = $permissionModel->orderby('name', 'asc')->findAll();
		$this->data['permissions'] = $permissions;
		$this->data['permissions_users'] = permissions_users(null, $group_id);
		// dd($this->data['permissions_users']);
		$this->data['group'] = $group;
		$this->data['group_id'] = $group_id;

		$this->validation->setRule('permissions', 'Permission', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$groupPermissionModel->where('group_id', $group_id)->delete();
			$permissions = $this->request->getPost('permissions');
			$save_data = [];
			foreach ($permissions as $permission_id => $data_arr) {
				$save_data[] = [
					'group_id' => $group_id,
					'permission_id' => $permission_id,
				];
			}
			if (!empty($save_data)) {
				$upsertGroupPermission = $groupPermissionModel->insertBatch($save_data);
				if ($upsertGroupPermission) {
					reloadPermission();
					set_message('toastr_msg', 'Permission berhasil disimpan');
					set_message('toastr_type', 'success');
				} else {
					set_message('toastr_msg', 'Permission gagal disimpan');
					set_message('toastr_type', 'warning');
				}
			}

			return redirect()->back();
		}
		echo view('Group\Views\permissions', $this->data);
	}
}
