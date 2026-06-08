<?php

namespace App\Modules\Backend\Notification\Controllers;

use \CodeIgniter\Files\File;

class Notification extends \Base\Controllers\BaseController
{
    public $auth;
    public $modulePath;
    public $uploadFile;
    function __construct()
    {
        $this->notificationModel = new \App\Modules\Backend\Notification\Models\NotificationModel();
        $this->modulePath = ROOTPATH . 'public/uploads/notification/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Notificationication';
        $this->data['notifications'] = $this->notificationModel->where('to', get_user_id())->findAll();
        echo view(APPPATH . 'Modules/Backend/Notification/Views/list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        $notificationDelete = $this->notificationModel->delete($id);
        if ($notificationDelete) {
            set_message('toastr_msg', 'notificationication berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/notificationication');
        } else {
            set_message('toastr_msg', 'notificationication gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/notificationication/delete/' . $id);
        }
    }
}
