<?php

namespace Access\Controllers;

class Access extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $permissionModel;
    public $groupModel;
    public $menuModel;

    function __construct()
    {
        $this->permissionModel = new \Permission\Models\PermissionModel();
        $this->groupModel = new \Group\Models\GroupModel();
        $this->menuModel = new \Menu\Models\MenuModel();

        helper(['access']);
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $group_id = $this->request->getGet('group_id') ?? 1;
        $group = $this->groupModel->find($group_id);
        $this->data['group'] = $group;
        $permissions = $this->groupModel->getPermissions($group_id);
        $access = array();
        foreach ($permissions as $permission) {
            $access[] = $permission['name'];
        }
        $query = $this->menuModel->where('parent', '0')->where('category_id', '1');
        if (!empty($keyword)) {
            $query->groupStart();
            $query->like('name', $keyword);
            $query->orLike('controller', $keyword);
            $query->groupEnd();
        }
        $groups = $this->groupModel->findAll();
      
        $menus = $query->findAll();
        $this->data['title'] = 'Access';
        $this->data['groups'] = $groups;
        $this->data['menus'] = $menus;
        $this->data['access'] = $access;
        $this->data['group'] = $group;

        echo view('Access\Views\list', $this->data);
    }
}
