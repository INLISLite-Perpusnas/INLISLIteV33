<?php

namespace User\Controllers;

use Base\Models\BaseModel;
use Base\Models\BaseModelWithoutTimeStamps;

class User extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $userModel;
    public $groupModel;
    public $password;

    function __construct()
    {
        $this->userModel = new \User\Models\UserModel();
        $this->groupModel = new \Group\Models\GroupModel();

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        $this->password = new \Myth\Auth\Password();

        helper(['adminigniter', 'reference']);
    }

    public function login()
    {
        echo "Auth Login";
    }

    public function index()
    {
        $groups = $this->groupModel->findAll();
        $slug = $this->request->getGet('slug') ?? '';
        $branch_id = $this->request->getGet('branch_id') ?? '';
        $this->data['title'] = 'User';
        $this->data['groups'] = $groups;
        $this->data['slug'] = $slug;
        // $this->data['branch_id'] = $branch_id;

      
            // $branchModel = new \MitraPerpustakaan\Models\MitraPerpustakaanModel();
            // $branchBuilder = $branchModel
            //     ->select('ID as code,  concat(Code, " - ", Name) as name')
            //     ->where('Code <>', '')
            //     ->orderBy('Name', 'ASC');

      

            // $branchs = $branchBuilder->findAll();
            // foreach ($branchs as &$branch) {
            //     $branch->name = preg_replace('/\s+/', ' ', $branch->name);
            // }
            // $this->data['branchs'] = $branchs;
        

        echo view('User\Views\list', $this->data);
    }

    public function profile()
    {
        $this->detail(user_id(), true);
    }
    public function detail(int $id, $is_profile = false)
    {
        $this->data['title'] = ($is_profile) ? 'Profil Saya' : 'Detail User';
        $user = $this->userModel->find($id);
        $groups = $this->groupModel->orderBy('id')->findAll();
        $currentGroups = $this->userModel->getGroups($id);

        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;
        $this->data['is_profile'] = $is_profile;

        echo view('User\Views\profile', $this->data);
    }
    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $userDelete = $this->userModel->delete($id);
        if ($userDelete) {
            reloadPermission();
            set_message('toastr_msg', 'User berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User failed to delete');
            set_message('toastr_type', 'warning');
            set_message('message', 'Error');
            return redirect()->to('/user');
        }
    }

    public function enable(int $id, string $code = '')
    {
        $activation = false;
        if ($code) {
            $activation = $this->userModel->update($id, array('active' => 1));
        } else if (is_member('admin')) {
            $activation = $this->userModel->update($id, array('active' => 1));
        }

        if ($activation) {
            reloadPermission();
            set_message('toastr_msg', 'User berhasil diaktifkan');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User gagal diaktifkan');
            set_message('toastr_type', 'warning');
            set_message('message', 'User gagal diaktifkan');
            return redirect()->to('/user');
        }
    }

    public function disable(int $id)
    {
        $deactivation = false;
        if (is_member('admin')) {
            $deactivation = $this->userModel->update($id, array('active' => 0));
        }

        if ($deactivation) {
            reloadPermission();
            set_message('toastr_msg', 'User berhasil dinonaktifkan');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User gagal dinonaktifkan');
            set_message('toastr_type', 'warning');
            set_message('message', 'User gagal dinonaktifkan');
            return redirect()->to('/user');
        }
    }

    public function change_password()
    {
        $this->data['title'] = 'Change Password';
        $this->validation->setRule('password_old', 'Password Lama', 'required');
        $this->validation->setRule('password', 'Password', 'required|min_length[8]|max_length[15]|regex_match[/^(?=.*[A-Z])(?=.*[!@#%])(?=.*[0-9])(?=.*[a-z]).{8,15}$/]');
        $this->validation->setRule('password_confirm', 'Konfirmasi Password', 'required|matches[password]');
        if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
            $this->data['message'] = $this->validation->listErrors();
            echo view('User\Views\password\change', $this->data);
        } else {
            $username      = $this->session->get('username');
            $logged_in     = $this->session->get('logged_in');
            $password_hash = $this->password->hash($this->request->getPost('password'));
            $change        = $this->userModel->update($logged_in, array('password_hash' => $password_hash));
            if ($change) {
                session()->set('password', $this->request->getPost('password')); // update dulu passwordnya
                reloadPermission();
                set_message('toastr_msg', 'Password berhasil disimpan');
                set_message('toastr_type', 'success');
                return redirect()->back();
            } else {
                set_message('toastr_msg', 'Password gagal disimpan');
                set_message('toastr_type', 'warning');
                return redirect()->back();
            }
        }
    }

    public function change_avatar()
    {
        try {
            $user = user();
            $this->data['title'] = 'Change Avatar';
            $this->data['user'] = $user;
            $this->validation->setRule('file_image', 'Gambar', 'required');
            if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
                $this->data['message'] = ($this->validation->getErrors()) ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
                echo view('User\Views\profile\change', $this->data);
            } else {
                $update_data = array();
                $files = (array) $this->request->getPost('file_image');
                if (count($files)) {
                    $listed_file = array();
                    foreach ($files as $uuid => $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } else {
                            if (file_exists($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName = $file->getFileName(); //$file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                    }
                    $update_data['file_image'] = implode(',', $listed_file);
                }
                $change = $this->userModel->update($user->id, $update_data);
                if ($change) {
                    reloadPermission();
                    set_message('toastr_msg', 'Avatar berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/user/change_avatar');
                }
            }
        } catch (\Exception $ex) {
            $this->data['message'] = $ex->getMessage();
            echo view('User\Views\profile\change', $this->data);
        }
    }

    public function apply_status($id)
    {
        $field  = $this->request->getGet('field') ?? '';
        $value  = $this->request->getGet('value') ?? '';
        $userUpdate = $this->userModel->update($id, array($field => $value));
        if ($userUpdate) {
            reloadPermission();
            set_message('toastr_msg', ' User berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' User gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/user');
    }

    public function permissions($user_id = null)
    {
        $this->data['title'] = 'Permission - User';
        $this->data['user'] = get_single('users', '*', 'id =' . $user_id);
        if (!$this->data['user']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $modelAuthUserPermissions = new BaseModelWithoutTimeStamps('auth_users_permissions');
        $this->validation->setRule('permissions', 'Permission', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $modelAuthUserPermissions->where('user_id', $user_id)->delete();
            $permissions    = $this->request->getPost('permissions');
            $save_data      = [];
            foreach ($permissions as $permission_id => $data_arr) {
                $save_data[] = [
                    'user_id'       => $user_id,
                    'permission_id' => $permission_id,
                ];
            }
            if (!empty($save_data)) {
                if ($modelAuthUserPermissions->insertBatch($save_data)) {
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
        $permissionModel           = new BaseModel('auth_permissions');
        $permissions               = $permissionModel->orderby('name', 'asc')->findAll();
        $this->data['permissions'] = $permissions;

        $db_permissions_user       = $modelAuthUserPermissions->where('user_id', $user_id)->findAll();
        $permissions_user          = [];
        foreach ($db_permissions_user as $db_permission_user) {
            $permissions_user[] = $db_permission_user->permission_id;
        }
        if (empty($permissions_user)) { // Jika ngak ada ambil dari group permission
            $permissions_user = permissions_users($user_id);
        }
        $this->data['permissions_users'] = $permissions_user;
        return view('User\Views\permissions', $this->data);
    }
}
