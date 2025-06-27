<?php

namespace PenomoranKoleksi\Controllers;

use PenomoranKoleksi\Models\SettingParametersModel;

class PenomoranKoleksi extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    protected $settingModel;
 
    
    function __construct()
    {
        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        $this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Display the setting form
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Setting Nomor Induk';
        $this->data['validation'] = \Config\Services::validation();
        
        // Ambil data dari database
        $nomorIndukData = $this->settingModel->where('Name','NomorInduk')->first();
        $NomorInduk = $nomorIndukData ? $nomorIndukData->Value : 'Manual';
        
        $formatNomorIndukData = $this->settingModel->where('Name','FormatNomorInduk')->first();
        $FormatnomorInduk = $formatNomorIndukData ? $formatNomorIndukData->Value : '0|0|0|0|0|0|0|0|0';
        
        $formatNomorBarcodeData = $this->settingModel->where('Name','FormatNomorBarcode')->first();
        $FormatNomorBarcode = $formatNomorBarcodeData ? $formatNomorBarcodeData->Value : 'No. Induk';
        
        $formatNomorRFIDData = $this->settingModel->where('Name','FormatNomorRFID')->first();
        $FormatNomorRFID = $formatNomorRFIDData ? $formatNomorRFIDData->Value : 'No. Induk';
        
        $this->data['NomorInduk'] = $NomorInduk;
        $this->data['FormatNomorInduk'] = $FormatnomorInduk;
        $this->data['FormatNomorBarcode'] = $FormatNomorBarcode;
        $this->data['FormatNomorRFID'] = $FormatNomorRFID;
        
        echo view('PenomoranKoleksi\Views\nomor_induk', $this->data);
    }
    
    /**
     * Handle form submission and update database
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        // Validasi input
        $rules = [
            'NomorInduk' => 'required|in_list[Manual,Otomatis]',
            'FormatNomorInduk' => 'required',
            'FormatNomorBarcode' => 'required|in_list[No. Induk,Item ID]',
            'FormatNomorRFID' => 'required|in_list[No. Induk,Item ID]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        try {
            // Ambil data dari form
            $nomorInduk = $this->request->getPost('NomorInduk');
            $formatNomorInduk = $this->request->getPost('FormatNomorInduk');
            $formatNomorBarcode = $this->request->getPost('FormatNomorBarcode');
            $formatNomorRFID = $this->request->getPost('FormatNomorRFID');
            $manualInputs = $this->request->getPost('ManualInput') ?? [];
            
            // Process FormatNomorInduk dengan manual input
            if (is_array($formatNomorInduk)) {
                $processedFormat = [];
                $manualInputIndex = 0;
                
                foreach ($formatNomorInduk as $index => $value) {
                    if ($value == '1') { // Manual Input
                        // Gunakan input manual jika ada
                        if (isset($manualInputs[$manualInputIndex])) {
                            $processedFormat[] = '{' . $manualInputs[$manualInputIndex] . '}';
                        } else {
                            $processedFormat[] = '{Manual}';
                        }
                        $manualInputIndex++;
                    } else {
                        $processedFormat[] = $value;
                    }
                }
                $formatNomorIndukString = implode('|', $processedFormat);
            } else {
                $formatNomorIndukString = $formatNomorInduk;
            }
            
            // Update atau insert NomorInduk
            $existingNomorInduk = $this->settingModel->where('Name', 'NomorInduk')->first();
            if ($existingNomorInduk) {
                $this->settingModel->update($existingNomorInduk->ID, ['Value' => $nomorInduk]);
            } else {
                $this->settingModel->insert(['Name' => 'NomorInduk', 'Value' => $nomorInduk]);
            }
            
            // Update atau insert FormatNomorInduk
            $existingFormatNomorInduk = $this->settingModel->where('Name', 'FormatNomorInduk')->first();
            if ($existingFormatNomorInduk) {
                $this->settingModel->update($existingFormatNomorInduk->ID, ['Value' => $formatNomorIndukString]);
            } else {
                $this->settingModel->insert(['Name' => 'FormatNomorInduk', 'Value' => $formatNomorIndukString]);
            }
            
            // Update atau insert FormatNomorBarcode
            $existingFormatNomorBarcode = $this->settingModel->where('Name', 'FormatNomorBarcode')->first();
            if ($existingFormatNomorBarcode) {
                $this->settingModel->update($existingFormatNomorBarcode->ID, ['Value' => $formatNomorBarcode]);
            } else {
                $this->settingModel->insert(['Name' => 'FormatNomorBarcode', 'Value' => $formatNomorBarcode]);
            }
            
            // Update atau insert FormatNomorRFID
            $existingFormatNomorRFID = $this->settingModel->where('Name', 'FormatNomorRFID')->first();
            if ($existingFormatNomorRFID) {
                $this->settingModel->update($existingFormatNomorRFID->ID, ['Value' => $formatNomorRFID]);
            } else {
                $this->settingModel->insert(['Name' => 'FormatNomorRFID', 'Value' => $formatNomorRFID]);
            }
            
            // Set success message
            session()->setFlashdata('success', 'Pengaturan penomoran koleksi berhasil disimpan.');
            
        } catch (\Exception $e) {
            // Set error message
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
        
        return redirect()->to(base_url('master-penomoran-koleksi'));
    }
    
    /**
     * Method untuk mendapatkan preview format nomor induk (AJAX)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getFormatPreview()
    {
        $formatArray = $this->request->getPost('format');
        $manualInputs = $this->request->getPost('manualInputs') ?? [];
        
        if (!$formatArray || !is_array($formatArray)) {
            return $this->response->setJSON(['preview' => 'Format tidak valid']);
        }
        
        $formatLabels = [
            '0' => '',
            '1' => '[Manual]',
            '2' => '[Jenis]', 
            '3' => '[Kategori]',
            '4' => '[Bentuk]',
            '5' => '[Sumber]',
            '6' => '99999',
            '7' => 'YYYY'
        ];
        
        $separatorLabels = [
            '2' => '',
            '3' => '/',
            '4' => '-', 
            '5' => '.'
        ];
        
        $preview = '';
        $manualInputIndex = 0;
        
        foreach ($formatArray as $index => $value) {
            $isEven = (($index + 1) % 2 === 0);
            
            if ($value !== '0') {
                if ($isEven) {
                    $preview .= $separatorLabels[$value] ?? '';
                } else {
                    if ($value == '1') { // Manual Input
                        if (isset($manualInputs[$manualInputIndex]) && !empty($manualInputs[$manualInputIndex])) {
                            $preview .= '[' . $manualInputs[$manualInputIndex] . ']';
                        } else {
                            $preview .= '[Manual]';
                        }
                        $manualInputIndex++;
                    } else {
                        $preview .= $formatLabels[$value] ?? '';
                    }
                }
            }
        }
        
        return $this->response->setJSON(['preview' => $preview ?: 'Format belum dipilih']);
    }
}