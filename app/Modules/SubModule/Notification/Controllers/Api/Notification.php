<?php

namespace App\Modules\Backend\Notification\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\NotificationModel;
use CodeIgniter\Files\File;

class Notification extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $notificationModel;
    protected $validation;
    protected $session;
    protected $modulePath;
    protected $uploadPath;

    function __construct()
    {
        $this->notificationModel = new \App\Modules\Backend\Notification\Models\NotificationModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->modulePath = ROOTPATH . 'public/uploads/notif/';
        $this->uploadPath = WRITEPATH . 'uploads/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $data = $this->notificationModel->findAll();
        return $this->respond($data, 200);
    }
    public function show($id = null)
    {
        $data = $this->notificationModel->find($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }
    public function update($id = null)
    {
        $this->validation->setRule('id', 'Id', 'trim|required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $update_data = array(
                'is_read' => $this->request->getPost('is_read')
            );
            $notifUpdate = $this->notificationModel->update($id, $update_data);
            if ($notifUpdate) {
                // $this->session->setFlashdata('toastr_msg', 'Notification berhasil diupdate');
                // $this->session->setFlashdata('toastr_type', 'success');
                $response = [
                    'status'   => 201,
                    'error'    => null,
                    'messages' => [
                        'success' => 'Notification berhasil diubah'
                    ]
                ];
                return $this->respond($response);
            } else {
                return $this->fail('<div class="alert alert-danger fade show" role="alert">Notification gagal diubah</div>', 400);
            }
        } else {
            $message = $this->validation->listErrors();
            return $this->fail($message, 400);
        }
    }
}
