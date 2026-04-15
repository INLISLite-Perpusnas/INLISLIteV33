<?php

namespace Menu\Controllers;

use \CodeIgniter\Files\File;

class Menu extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $menuModel;
    public $menuCategoryModel;
    public $uploadPath;
    public $modulePath;
    public $permissionModel;

    function __construct()
    {
        $this->menuModel = new \Menu\Models\MenuModel();
        $this->menuCategoryModel = new \Menu\Models\MenuCategoryModel();
        $this->permissionModel = new \Permission\Models\PermissionModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/menu/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        if (!is_member('admin')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
    }

    public function slug($slug = false)
    {
        $this->data['title'] = str_replace('-', ' ', ucfirst($slug));

        $this->data['slug'] = $slug;
        $this->data['title'] = 'Menus';
        echo view('Menu\Views\slug', $this->data);
    }

    public function index()
    {
        if (!is_allowed('menu/index')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('dashboard');
        }

        $slug = $this->request->getGet('slug') ?? 'backend-menu';
        $category = get_single('c_categories', 'id, name', 'slug = "' . $slug . '"');
        $parent_id = $this->request->getGet('parent_id') ?? 0;;
        $menu_id = $this->request->getGet('menu_id') ?? 0;
        $reference = get_single('c_menus', 'id, name', 'slug = "ref-permission"');
        $type = 'menu';
        $form = 'Tambah';
        $action = base_url('menu/create?slug=' . $slug);
        $menu = [];
        $permissions = ['access', 'index', 'create', 'edit', 'delete'];
        $menu_permissions = [];
        if (!empty($menu_id)) {
            $menu = get_single('c_menus', 'id, name, controller, icon, type, parent, slug, permission', 'id = ' . $menu_id);
            $menu_permissions = explode('|', $menu->permission);
            $parent_id = $menu->parent ?? 0;
            $type = $menu->type;
            $form = 'Edit';
            $action = base_url('menu/edit/' . $menu_id . '?slug=' . $slug);
        }

        if (!($display_menu_module = read_cache('display_menu_module'))) {
            $display_menu_module = display_menu_module($category->id, 0, 1);
            write_cache('display_menu_module', $display_menu_module);
        }

        if (!($display_menu_option = read_cache('display_menu_option'))) {
            $display_menu_option = display_menu_option($category->id, 0);
            write_cache('display_menu_option', $display_menu_option);
        }

        $this->data['title'] = 'Daftar Menu';
        $this->data['slug'] = $slug;
        $this->data['title'] = 'Menu';
        $this->data['category'] = $category;
        $this->data['parent_id'] = $parent_id;
        $this->data['menu_id'] = $menu_id;
        $this->data['reference'] = $reference;
        $this->data['type'] = $type;
        $this->data['form'] = $form;
        $this->data['action'] = $action;
        $this->data['menu'] = $menu;
        $this->data['menus'] = $display_menu_module;
        $this->data['parent_menus'] = $display_menu_option;
        $this->data['permissions'] = $permissions;
        $this->data['menu_permissions'] = $menu_permissions;

        echo view('Menu\Views\list', $this->data);
    }

    public function create()
    {
       

        $slug = $this->request->getGet('slug') ?? 'backend-menu';
        try {
            $this->data['title'] = 'Tambah Menu';
            $this->validation->setRule('name', 'Label', 'required');
            if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                $name  = $this->request->getPost('name');
                $type  = $this->request->getPost('type');
                $route = $this->request->getPost('controller');
                $form_slug   = url_title($name, '-', TRUE);
                $permission  = (array) $this->request->getPost('permission');
                $permissions = [];
                foreach ($permission as $key => $value) {
                    $permissions[] = $key;
                }
                $permissions = array_unique($permissions);
                $menu_permissions = implode('|', $permissions);

                $save_data = array(
                    'name' => $name,
                    'slug' => $form_slug,
                    'controller' => $route,
                    'icon' => $this->request->getPost('icon'),
                    'permission' => $menu_permissions,
                    'sort' => 0,
                    'description' => $this->request->getPost('description'),
                    'type' => $type,
                    'parent' => $this->request->getPost('parent') ?? 0,
                    'category_id' => $this->request->getPost('category_id'),
                );

                $newMenuId = $this->menuModel->insert($save_data);
                if ($newMenuId) {
                    foreach ($permissions as $key => $permission) {
                        $permissionName = $route . "/" . $permission;
                        $permissionData = $this->permissionModel->where('name', $permissionName)->first();
                        if (empty($permissionData)) {
                            $save_permission[] = [
                                'name' => $permissionName,
                                'route' => $route,
                                'menu' => $name,
                                'description' => 'Create Permission for menu ' . $name
                            ];
                        } else {
                            $update_permission[] = [
                                'id' => $permissionData->id,
                                'name' => $permissionName,
                                'route' => $route,
                                'menu' => $name,
                                'description' => 'Update Permission for menu ' . $name
                            ];
                        }
                    }

                    if (!empty($save_permission)) {
                        $this->permissionModel->insertBatch($save_permission);
                    }

                    if (!empty($update_data)) {
                        $this->permissionModel->updateBatch($update_permission, 'id');
                    }
                    reloadPermission();
                         $this->session->setFlashdata('swal_icon', 'success');
                        $this->session->setFlashdata('swal_title', 'Berhasil');
                        $this->session->setFlashdata('swal_text', 'Eksemplar berhasil ditambah');
                    return redirect()->to('/menu?slug=' . $slug);
                } else {
                    $this->session->setFlashdata('toastr_msg', 'Menu gagal disimpan');
                    $this->session->setFlashdata('toastr_type', 'warning');
                    $this->session->setFlashdata('message', 'Menu gagal disimpan');
                    $this->data['toastr_type'] = 'warning';
                    return redirect()->to('/menu?slug=' . $slug);
                }
            } else {
                $message = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
                $this->data['message'] = $message;
                echo view('Menu\Views\add', $this->data);
            }
        } catch (\Exception $ex) {
            $this->session->setFlashdata('message', $ex->getMessage());
            return redirect()->to('/menu?slug=' . $slug);
        }
    }

    public function edit(int $id = 0)
    {
       

        $slug = $this->request->getGet('slug') ?? 'backend-menu';

        try {
            $this->data['title'] = 'Ubah Menu';
            $menu = $this->menuModel->find($id);
            $this->validation->setRule('name', 'Label', 'required');
            if ($this->request->getPost()) {
                if ($this->validation->withRequest($this->request)->run()) {
                    $name = $this->request->getPost('name');
                    $type = $this->request->getPost('type');
                    $route = $this->request->getPost('controller');
                    $form_slug = url_title($this->request->getPost('form_slug'), '-', TRUE);
                    $permission = (array) $this->request->getPost('permission');
                    $permissions = [];
                    foreach ($permission as $key => $value) {
                        $permissions[] = $key;
                    }
                    $permissions = array_unique($permissions);
                    $menu_permissions = implode('|', $permissions);

                    $update_data = array(
                        'name' => $name,
                        'slug' => $form_slug,
                        'controller' => $route,
                        'icon' => $this->request->getPost('icon'),
                        'permission' => $menu_permissions,
                        'description' => $this->request->getPost('description'),
                        'type' => $type,
                        'parent' => $this->request->getPost('parent') ?? 0,
                    );
                  

                    $menuUpdate = $this->menuModel->update($id, $update_data);
                    if ($menuUpdate) {
                        foreach ($permissions as $key => $permission) {
                            $permissionName = $route . "/" . $permission;
                            $permissionData = $this->permissionModel->where('name', $permissionName)->first();
                            if (empty($permissionData)) {
                                $save_permission[] = [
                                    'name' => $permissionName,
                                    'route' => $route,
                                    'menu' => $name,
                                    'description' => 'Create Permission for menu ' . $name
                                ];
                            } else {
                                $update_permission[] = [
                                    'id' => $permissionData->id,
                                    'name' => $permissionName,
                                    'route' => $route,
                                    'menu' => $name,
                                    'description' => 'Update Permission for menu ' . $name
                                ];
                            }
                       
                        }
                    
                        if (!empty($save_permission)) {
                            $this->permissionModel->insertBatch($save_permission);
                        }

                        if (!empty($update_permission)) {
                            $this->permissionModel->updateBatch($update_permission, 'id');
                        }
                    //    dd($save_permission);
                        reloadPermission();
                    //     dd(123);
                        $this->session->setFlashdata('swal_icon', 'success');
                        $this->session->setFlashdata('swal_title', 'Berhasil');
                        $this->session->setFlashdata('swal_text', 'Menu berhasil diubah');
                        return redirect()->to('/menu?slug=' . $slug . '&menu_id=' . $id);
                    } else {
                        $this->session->setFlashdata('swal_icon', 'error');
                        $this->session->setFlashdata('swal_title', 'Gagal');
                        $this->session->setFlashdata('swal_text', 'Menu gagal diubah');
                        return redirect()->to('/menu?slug=' . $slug . '&menu_id=' . $id);
                    }
                }
            }

            $this->data['menu'] = $menu;
            echo view('Menu\Views\update', $this->data);
        } catch (\Exception $ex) {
            $this->session->setFlashdata('message', $ex->getMessage());
            return redirect()->to('/menu?slug=' . $slug);
        }
    }

    public function delete(int $id = 0) // id menu, cari permission yang menggandung id menu tersebut., ada juga permission yang menggandung group permission misal dashboard berarti permissionnya namenya bisa jadi dashboard/index, dashboard/create dll
    {
        if (!is_allowed('menu/delete')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('dashboard');
        }

        $slug       = $this->request->getGet('slug') ?? 'backend-menu';
        $menuDelete = $this->menuModel->delete($id);
        if ($menuDelete) {
            reloadPermission();
            set_message('toastr_msg', 'Menu berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/menu?slug=' . $slug);
        } else {
            set_message('toastr_msg', 'Menu gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Menu gagal dihapus');
            return redirect()->to('/menu?slug=' . $slug);
        }
    }

    public function category_delete($id = null)
    {
        if (!is_allowed('menu/delete')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('dashboard');
        }

        $menuCategoryDelete = $this->menuCategoryModel->delete($id);
        if ($menuCategoryDelete) {
            reloadPermission();
            set_message('toastr_msg', 'Kategori Menu berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/menu');
        } else {
            set_message('toastr_msg', 'Kategori Menu gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Kategori Menu gagal dihapus');
            return redirect()->to('/menu');
        }
    }
}
