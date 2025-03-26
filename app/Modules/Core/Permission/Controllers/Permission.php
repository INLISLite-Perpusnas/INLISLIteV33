<?php

namespace Permission\Controllers;

use Base\Models\BaseModel;

class Permission extends \Base\Controllers\BaseController
{
    protected $permissionModel;

    function __construct()
    {
        $this->permissionModel = new \Permission\Models\PermissionModel();
    }

    public function index()
    {
        $db=db_connect();
        $permisson=$db->table('auth_groups_permissions')->where('permission_id', 600)->where('group_id', session()->get('group_id'))->get()->getRow();
       
        if (!$permisson) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

        if (!($display_menu_option = read_cache('display_menu_option_1'))) {
            $display_menu_option = display_menu_option(1, 0);
            write_cache('display_menu_option_1', $display_menu_option);
        }
        $permissions = $this->permissionModel->findAll();

        $this->data['title'] = 'Permission';
        $this->data['groups'] = groups();
        $this->data['groups_users'] = groups_users();
        $this->data['permissions'] = $permissions;
        $this->data['parent_menus'] = $display_menu_option;

        echo view('\Permission\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter(id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $permissionDelete = $this->permissionModel->delete($id);
        if ($permissionDelete) {
            db_connect()->table('auth_groups_permissions')->where('permission_id', $id)->delete();
            db_connect()->table('auth_users_permissions')->where('permission_id', $id)->delete();
            reloadPermission();
            set_message('toastr_msg', 'Permission berhasil dihapus');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Permission gagal dihapus');
            set_message('toastr_type', 'warning');
        }
        return redirect()->back();
    }
}
