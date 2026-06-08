<?php

namespace RuasDataBibliografis\Controllers;

use \CodeIgniter\Files\File;

class RuasDataBibliografis extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $worksheetFieldModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
       $this->worksheetFieldModel = new \Katalog\Models\WorksheetFieldModel();
    }

    public function index()
    {
        $this->data['title'] = 'Ruas Data Bibliografis';
        
        // Ambil worksheet_id dari parameter GET jika ada
        $worksheet_id = $this->request->getGet('worksheet_id');
        
        // Initialize data_bibliografis sebagai array kosong
        $data_bibliografis = [];
        
        // Jika worksheet_id dipilih, ambil data berdasarkan filter
        if (!empty($worksheet_id)) {
            $data_bibliografis = $this->worksheetFieldModel
                ->select('worksheetfields.ID, worksheetfields.Worksheet_id, worksheetfields.Field_id, worksheetfields.Active, fields.Tag, fields.Name')
                ->join('fields', 'fields.ID = worksheetfields.Field_id')
                ->where('worksheetfields.Worksheet_id', $worksheet_id)
                ->findAll();
        }
        
        $this->data['data_bibliografis'] = $data_bibliografis;
        $this->data['selected_worksheet_id'] = $worksheet_id;

        echo view('RuasDataBibliografis\Views\list', $this->data);
    }

    // Method untuk update status active via AJAX
    public function updateActive()
    {
        if ($this->request->isAJAX()) {
            $worksheetfield_id = $this->request->getPost('id');
            $active_status = $this->request->getPost('active');
            
            try {
                $result = $this->worksheetFieldModel->update($worksheetfield_id, [
                    'Active' => $active_status
                ]);
                
                if ($result) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Status berhasil diupdate'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengupdate status'
                    ]);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request'
        ]);
    }
}