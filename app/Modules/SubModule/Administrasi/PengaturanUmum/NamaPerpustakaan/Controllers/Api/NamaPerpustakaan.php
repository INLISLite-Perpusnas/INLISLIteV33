<?php

namespace NamaPerpustakaan\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\API\ResponseTrait;
class NamaPerpustakaan extends  \Base\Controllers\BaseResourceController
{
    // Property yang sudah ada...
    protected $modulePath;
    protected $uploadPath;

    public function __construct()
    {
        // Constructor yang sudah ada...
        
        // Tambahkan konfigurasi path untuk logo upload
        $this->modulePath = ROOTPATH . 'public/uploads/branch/';
        $this->uploadPath = WRITEPATH . 'uploads/';
        
        // Buat direktori jika belum ada
        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath, 0777, true);
        }
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    // Method yang sudah ada: index(), update(), edit(), searchNpp(), dll...

    /**
     * Upload logo file
     * Route: POST master-nama-perpustakaan/api/logo/upload
     */
   
    public function uploadLogo()
{
   
    // Set header untuk JSON response
    $this->response->setContentType('application/json');

 

    try {
        // PERBAIKAN: Validasi manual tanpa is_image untuk menghindari finfo_file error
        $validation = \Config\Services::validation();
        $category = $this->request->getPost('category') ?? 'logo';
        if($category === 'logo') {
            $validation->setRules([
                'logo_file' => [
                    'label' => 'Logo File',
                    'rules' => 'uploaded[logo_file]|max_size[logo_file,1024]|is_image[logo_file]', // Hapus mime_in
                    'errors' => [
                        'uploaded' => 'File logo harus dipilih',
                        'max_size' => 'Ukuran file maksimal 1MB',
                        'is_image' => 'File harus berupa gambar'
                    ]
                ]
            ]);
        } else {
            
            $validation->setRules([
                'logo_file' => [
                    'label' => 'Logo Kop',
                    'rules' => 'uploaded[kop_file]|max_size[kop_file,1024]|is_image[kop_file]', // Hapus mime_in
                    'errors' => [
                        'uploaded' => 'File logo kop harus dipilih',
                        'max_size' => 'Ukuran file maksimal 1MB',
                        'is_image' => 'File harus berupa gambar'
                    ]
                ]
            ]);
        }
      

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Ambil file yang di-upload
        if($category === 'logo') {
            $logoFile = $this->request->getFile('logo_file');
        } else {
            $logoFile = $this->request->getFile('kop_file');
        }
       

        if (!$logoFile || !$logoFile->isValid()) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'File upload error: ' . ($logoFile ? $logoFile->getErrorString() : 'No file uploaded')
            ]);
        }

        // VALIDASI MANUAL untuk gambar (menggantikan is_image)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        $fileExtension = strtolower($logoFile->getClientExtension());
        $clientMimeType = $logoFile->getClientMimeType();

        // Validasi ekstensi file
        if (!in_array($fileExtension, $allowedExtensions)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Format file tidak diizinkan. Gunakan: JPG, JPEG, PNG, atau GIF'
            ]);
        }

        // Validasi MIME type (menggunakan client mime type untuk menghindari finfo error)
        if (!in_array($clientMimeType, $allowedMimeTypes)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Tipe file tidak valid. File harus berupa gambar.'
            ]);
        }

        // Validasi ukuran file (1MB = 1048576 bytes)
        if ($logoFile->getSize() > 1048576) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Ukuran file terlalu besar. Maksimal 1MB.'
            ]);
        }

        // VALIDASI TAMBAHAN: Cek apakah benar-benar file gambar dengan getimagesize
        $tempPath = $logoFile->getTempName();
        $imageInfo = @getimagesize($tempPath);
        
        if ($imageInfo === false) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'File bukan gambar yang valid.'
            ]);
        }

        // Generate nama file unik
        $newFileName = $this->generateLogoFileName($logoFile);
        
        // Pindahkan file ke direktori tujuan
        if (!$logoFile->move($this->modulePath, $newFileName)) {
            throw new \Exception('Failed to move uploaded file');
        }

        // Hapus file logo lama jika ada
        $this->deleteOldLogoFile();

        // Simpan nama file ke database
        $this->saveLogoToDatabase($newFileName);

        return $this->response->setJSON([
            'status' => 200,
            'message' => 'Logo berhasil diupload',
            'data' => [
                'filename' => $newFileName,
                'url' => base_url('uploads/branch/' . $newFileName),
                'size' => $logoFile->getSize(),
                'type' => $logoFile->getClientMimeType(),
                'dimensions' => $imageInfo[0] . 'x' . $imageInfo[1] . ' pixels'
            ]
        ]);

    } catch (\Exception $e) {
        // Hapus file jika ada error setelah upload
        if (isset($newFileName) && file_exists($this->modulePath . $newFileName)) {
            unlink($this->modulePath . $newFileName);
        }

        log_message('error', 'Upload logo error: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => 500,
            'message' => 'Error uploading file: ' . $e->getMessage()
        ]);
    }
}

/**
 * Generate unique filename for logo - DIPERBAIKI
 */
private function generateLogoFileName(UploadedFile $file)
{
    $extension = strtolower($file->getClientExtension());
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(4)); // Kurangi random bytes
    
    return "logo_{$timestamp}_{$randomString}.{$extension}";
}

/**
 * Validasi gambar alternatif menggunakan GD library
 */
private function isValidImage($filePath)
{
    try {
        // Cek dengan getimagesize (lebih reliable di macOS)
        $imageInfo = @getimagesize($filePath);
        
        if ($imageInfo === false) {
            return false;
        }

        // Cek tipe gambar yang didukung
        $supportedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        
        return in_array($imageInfo[2], $supportedTypes);
        
    } catch (\Exception $e) {
        log_message('error', 'Image validation error: ' . $e->getMessage());
        return false;
    }
}



    /**
     * Get current logo
     * Route: GET master-nama-perpustakaan/api/logo/current
     */
    public function getCurrentLogo()
    {
        $this->response->setContentType('application/json');

        try {
            $db = db_connect('data');
            $query = $db->table('settingparameters')
                       ->select('Value')
                       ->where('Name', 'Logo')
                       ->get();
            
            $result = $query->getRow();
            $logoFile = $result->Value ?? null;

            if ($logoFile && file_exists($this->modulePath . $logoFile)) {
                return $this->response->setJSON([
                    'status' => 200,
                    'data' => [
                        'filename' => $logoFile,
                        'url' => base_url('uploads/branch/' . $logoFile),
                        'exists' => true
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 404,
                    'message' => 'Logo not found',
                    'data' => [
                        'exists' => false
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => 'Error getting logo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete logo
     * Route: DELETE master-nama-perpustakaan/api/logo/delete
     */
    public function deleteLogo()
    {
        $this->response->setContentType('application/json');

        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'DELETE') {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            // Hapus file fisik
            $this->deleteOldLogoFile();

            // Hapus dari database
            $db = db_connect('data');
            $db->table('settingparameters')
               ->where('Name', 'Logo')
               ->delete();

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'Logo berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => 'Error deleting logo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique filename for logo
     */
    

    /**
     * Save logo filename to database
     */
    private function saveLogoToDatabase($newFilename)
    {
      
        $db = db_connect('data');
        $category = $this->request->getPost('category') ?? 'logo';

        if ($category === 'logo') {
            $data = [
                'Name' => 'Logo',
                'Value' => $newFilename,
            ];
        } else {
            $data = [
                'Name' => 'LogoKop',
                'Value' => $newFilename,
            ];
           
        }

        // Cek apakah sudah ada entry untuk logo
        $db->table('settingparameters')->update($data, ['Name' => $data['Name']]);
    }
  
    /**
     * Delete old logo file
     */
    private function deleteOldLogoFile()
    {
        $db = db_connect('data');
        $category = $this->request->getPost('category') ?? 'logo';
        if($category === 'logo') {
        $query = $db->table('settingparameters')
                   ->select('Value')
                   ->where('Name', 'Logo')
                   ->get();
        } else {
        $query = $db->table('settingparameters')
                   ->select('Value')
                   ->where('Name', 'LogoKop')
                   ->get();
        
        $result = $query->getRow();
        $oldFile = $result->Value ?? null;

        if ($oldFile && file_exists($this->modulePath . $oldFile)) {
            unlink($this->modulePath . $oldFile);
        }
    }
}
}