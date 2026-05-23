<?php

namespace MitraPerpustakaan\Controllers;

use \CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;

class MitraPerpustakaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $mitraperpustakaanModel;
    public $uploadPath;
    public $modulePath;
    public $authPermissions;

    function __construct()
    {
          // Get the permissions from session
          $this->authPermissions = session()->get('auth_permissions');
        
          // Check if the current method is allowed to be accessed
          $this->checkMethodAccess();
        $this->mitraperpustakaanModel = new \MitraPerpustakaan\Models\MitraPerpustakaanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/master-mitra-perpustakaan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('reference');
    }
    protected function checkMethodAccess()
    {
        // Get the current URI path
        $currentPath = uri_string();
     
        
        // Check if this path exists in auth_permissions
        if (!isset($this->authPermissions[$currentPath])) {
            // Method not allowed - redirect to dashboard
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
        }
    }
    public function index()
    {
       
     
      
       
        $this->data['title'] = 'Mitra Perpustakaan';
        echo view('MitraPerpustakaan\Views\list', $this->data);
    }

    public function sync()
    {
        ini_set("memory_limit", "2048M");

        $this->data['title'] = 'Sinkronisasi';

        $this->validation->setRule('url', 'URL', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $url = $this->request->getPost('url');
          
            // Initialize cURL session
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Add more cURL options as needed (e.g., SSL options)

            // Execute cURL session and get the JSON content
            $json_str = curl_exec($ch);

            // Close cURL session
            curl_close($ch);

            // Check for cURL errors
            if ($json_str === false) {
                // Handle error, maybe log it or return an error response
                die('cURL error: ' . curl_error($ch));
            }

            $json = json_decode($json_str, false);
            $rows = $json->data;
           

            $save_data = array();
            $update_data = array();
            foreach ($rows as $row) {
                $existing = $this->mitraperpustakaanModel->where('NPP_id', $row->id)->first();
                if (!empty($existing)) {
                    $update_data[] = array(
                        'ID' => $existing->ID,
                        'NPP_Provinsi_id' => $row->provinsi_id,
                        'NPP_KabKota_id' => $row->kabkota_id,
                        'NPP_Kecamatan_id' => $row->kecamatan_id,
                        'NPP_Kelurahan_id' => $row->kelurahan_id,
                        'NPP_Jenis' => $row->jenis,
                        'NPP_SubJenis' => $row->subjenis,
                        'Code' => $row->npp,
                        'Name' => $row->nama,
                        'Address' => $row->alamat,
                        'Email' => $row->email,
                        'Phone' => $row->telepon,
                    );
                } else {
                    $alias = strtoupper(generate_key(6));
                    $save_data[] = array(
                        'NPP_id' => $row->id,
                        'NPP_Provinsi_id' => $row->provinsi_id,
                        'NPP_KabKota_id' => $row->kabkota_id,
                        'NPP_Kecamatan_id' => $row->kecamatan_id,
                        'NPP_Kelurahan_id' => $row->kelurahan_id,
                        'NPP_Jenis' => $row->jenis,
                        'NPP_SubJenis' => $row->subjenis,
                        'Code' => $row->npp,
                        'Name' => $row->nama,
                        'Address' => $row->alamat,
                        'Email' => $row->email,
                        'Phone' => $row->telepon,
                        'Alias' => $alias,
                        'slug' => 'perpusnas-' . strtolower($alias),
                        'active' => 1,
                    );
                }
            }

            if (!empty($save_data)) {
                $this->mitraperpustakaanModel->insertBatch($save_data);
            }

            if (!empty($update_data)) {
                $this->mitraperpustakaanModel->updateBatch($update_data, 'ID');
            }

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Sinkronisasi Mitra Perpustakaan berhasil');

            return redirect()->back()->withInput();
        } else {
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('MitraPerpustakaan\Views\sync', $this->data);
        }
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-mitra-perpustakaan');
        }
        $mitraperpustakaanDelete = $this->mitraperpustakaanModel->delete($id);
        if ($mitraperpustakaanDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Mitra Perpustakaan berhasil dihapus');
            return redirect()->to('master-mitra-perpustakaan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Mitra Perpustakaan gagal dihapus');
            return redirect()->to('master-mitra-perpustakaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $mitraperpustakaanUpdate = $this->mitraperpustakaanModel->update($id, array($field => $value));

        if ($mitraperpustakaanUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Mitra Perpustakaan berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Mitra Perpustakaan gagal diubah');
        }
        return redirect()->to('master-mitra-perpustakaan');
    }
}
