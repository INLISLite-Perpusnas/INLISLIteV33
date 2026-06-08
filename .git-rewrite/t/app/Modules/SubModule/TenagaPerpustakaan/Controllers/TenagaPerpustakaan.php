<?php

namespace TenagaPerpustakaan\Controllers;

class TenagaPerpustakaan extends \Base\Controllers\BaseController
{
    protected $apiBaseUrl;
    protected $httpClient;

    public function __construct()
    {
        // Load helper for messages
        helper(['url', 'form']);
        
        $this->apiBaseUrl = env('FLASK_API_BASEURL');
        if (!$this->apiBaseUrl) {
            die('Error: Harap set FLASK_API_BASEURL di file .env Anda.');
        }
        $this->httpClient = \Config\Services::curlrequest([
            'baseURI' => $this->apiBaseUrl,
            'timeout' => 10,
        ]);
    }

    public function index()
    {
        $this->data['title'] = 'Tenaga Perpustakaan';
        $this->data['pustakawan_list'] = [];

        try {
            $perpus_id = user()->branch_id;

            if ($perpus_id) {
                $response = $this->httpClient->get('pustakawan/perpustakaan/' . $perpus_id);

                if ($response->getStatusCode() === 200) {
                    $api_data = json_decode($response->getBody());
                    $this->data['pustakawan_list'] = $api_data->data;
                }
            } else {
                $this->data['error_message'] = "Profil Anda tidak terhubung dengan perpustakaan manapun.";
            }

        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] API Fetch Error: ' . $e->getMessage());
            $this->data['error_message'] = "Gagal terhubung ke server data. Silakan coba lagi nanti.";
        }

        echo view('TenagaPerpustakaan\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Tenaga Perpustakaan';
        
        if ($this->request->getMethod() === 'post') {
            return $this->store();
        }

        echo view('TenagaPerpustakaan\Views\create', $this->data);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]',
            'nip' => 'required|min_length[8]',
            'no_hp' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email',
            // 'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('message', [
                'type' => 'error',
                'text' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        try {
            $data = [
                'perpus_id' => user()->branch_id,
                'nama' => $this->request->getPost('nama'),
                'nip' => $this->request->getPost('nip'),
                'no_hp' => $this->request->getPost('no_hp'),
                'email' => $this->request->getPost('email'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'pendidikan' => $this->request->getPost('pendidikan'),
                'status_pegawai' => $this->request->getPost('status_pegawai'),
                'bidang_pendidikan' => $this->request->getPost('bidang_pendidikan'),
                'pangkat' => $this->request->getPost('pangkat'),
                'tmt_pangkat' => $this->request->getPost('tmt_pangkat') ?: null,
                'jabatan' => $this->request->getPost('jabatan'),
                'tmt_jabatan' => $this->request->getPost('tmt_jabatan') ?: null,
                'linkdrive' => $this->request->getPost('linkdrive'),
                'created_by' => user()->id ?? 1
            ];

            // Debug: Log data yang akan dikirim
            log_message('info', '[TenagaPerpustakaan] Sending data: ' . json_encode($data));

            $response = $this->httpClient->post('pustakawan', [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            
            // Debug: Log response
            log_message('info', '[TenagaPerpustakaan] API Response Code: ' . $statusCode);
            log_message('info', '[TenagaPerpustakaan] API Response Body: ' . $responseBody);

            if ($statusCode === 201) {
                return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                    'type' => 'success',
                    'text' => 'Data tenaga perpustakaan berhasil ditambahkan.'
                ]);
            } else {
                $error_data = json_decode($responseBody, true);
                $errorMessage = $error_data['error'] ?? 'Gagal menambahkan data. Response: ' . $responseBody;
                throw new \Exception($errorMessage);
            }

        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] Create Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('message', [
                'type' => 'error',
                'text' => 'Gagal menambahkan data: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $this->data['title'] = 'Edit Tenaga Perpustakaan';
        
        try {
            $response = $this->httpClient->get('pustakawan/' . $id);
            
            if ($response->getStatusCode() === 200) {
                $api_data = json_decode($response->getBody());
                $this->data['pustakawan'] = $api_data->data;
            //    dd($this->data['pustakawan']);
            } else {
                return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                    'type' => 'error',
                    'text' => 'Data tidak ditemukan.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] Edit Error: ' . $e->getMessage());
            return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                'type' => 'error',
                'text' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }

        if ($this->request->getMethod() === 'post') {
            return $this->update($id);
        }

        echo view('TenagaPerpustakaan\Views\edit', $this->data);
    }

    public function update($id)
    {
        $rules = [
            'nama' => 'required|min_length[3]',
            'nip' => 'required|min_length[8]',
            'no_hp' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email',
            // 'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('message', [
                'type' => 'error',
                'text' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        try {
            $data = [
                'nama' => $this->request->getPost('nama'),
                'nip' => $this->request->getPost('nip'),
                'no_hp' => $this->request->getPost('no_hp'),
                'email' => $this->request->getPost('email'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'pendidikan' => $this->request->getPost('pendidikan'),
                'status_pegawai' => $this->request->getPost('status_pegawai'),
                'bidang_pendidikan' => $this->request->getPost('bidang_pendidikan'),
                'pangkat' => $this->request->getPost('pangkat'),
                'tmt_pangkat' => $this->request->getPost('tmt_pangkat') ?: null,
                'jabatan' => $this->request->getPost('jabatan'),
                'tmt_jabatan' => $this->request->getPost('tmt_jabatan') ?: null,
                'linkdrive' => $this->request->getPost('linkdrive')
            ];

            // Debug: Log data yang akan dikirim
            log_message('info', '[TenagaPerpustakaan] Updating data for ID ' . $id . ': ' . json_encode($data));

            $response = $this->httpClient->put('pustakawan/' . $id, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            
            // Debug: Log response
            log_message('info', '[TenagaPerpustakaan] API Update Response Code: ' . $statusCode);
            log_message('info', '[TenagaPerpustakaan] API Update Response Body: ' . $responseBody);

            if ($statusCode === 200) {
                return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                    'type' => 'success',
                    'text' => 'Data tenaga perpustakaan berhasil diupdate.'
                ]);
            } else {
                $error_data = json_decode($responseBody, true);
                $errorMessage = $error_data['error'] ?? 'Gagal mengupdate data. Response: ' . $responseBody;
                throw new \Exception($errorMessage);
            }

        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('message', [
                'type' => 'error',
                'text' => 'Gagal mengupdate data: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $response = $this->httpClient->delete('pustakawan/' . $id);

            if ($response->getStatusCode() === 200) {
                return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                    'type' => 'success',
                    'text' => 'Data tenaga perpustakaan berhasil dihapus.'
                ]);
            } else {
                $error_data = json_decode($response->getBody());
                throw new \Exception($error_data->error ?? 'Gagal menghapus data');
            }

        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] Delete Error: ' . $e->getMessage());
            return redirect()->to(base_url('tenaga-perpustakaan'))->with('message', [
                'type' => 'error',
                'text' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    // Method untuk AJAX DataTables
    public function datatable()
    {
        try {
            $perpus_id = user()->branch_id;
            $response = $this->httpClient->get('pustakawan/perpustakaan/' . $perpus_id);
            
            if ($response->getStatusCode() === 200) {
                $api_data = json_decode($response->getBody());
                $data = $api_data->data ?? [];
                
                // Format data untuk DataTables
                $result = [];
                foreach ($data as $index => $item) {
                    $result[] = [
                        'no' => $index + 1,
                        'nama' => $item->nama ?? '-',
                        'nip' => $item->nip ?? '-',
                        'no_hp' => $item->no_hp ?? '-',
                        'email' => $item->email ?? '-',
                        'jenis_kelamin' => $item->jenis_kelamin ?? '-',
                        'pendidikan' => $item->pendidikan ?? '-',
                        'jabatan' => $item->jabatan ?? '-',
                        'action' => '
                            <div class="btn-group">
                                <a href="' . base_url('tenaga-perpustakaan/edit/' . $item->id) . '" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="' . base_url('tenaga-perpustakaan/delete/' . $item->id) . '" 
                                   class="btn btn-sm btn-danger" title="Hapus"
                                   onclick="return confirm(\'Yakin ingin menghapus data ini?\')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        '
                    ];
                }
                
                return $this->response->setJSON([
                    'data' => $result
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[TenagaPerpustakaan] DataTable Error: ' . $e->getMessage());
        }
        
        return $this->response->setJSON(['data' => []]);
    }

    // Method debug untuk test koneksi API
    public function testApi()
    {
        try {
            // Test basic connection
            $response = $this->httpClient->get('test');
            echo "<h3>Test Basic API:</h3>";
            echo "<pre>" . $response->getBody() . "</pre>";
            
            // Test database connection  
            $response = $this->httpClient->get('test-db');
            echo "<h3>Test Database:</h3>";
            echo "<pre>" . $response->getBody() . "</pre>";
            
            // Test table
            $response = $this->httpClient->get('test-table');
            echo "<h3>Test Table:</h3>";
            echo "<pre>" . $response->getBody() . "</pre>";
            
            // Test POST data
            $testData = [
                'nama' => 'Test User',
                'nip' => '123456789',
                'perpus_id' => 1
            ];
            
            $response = $this->httpClient->post('debug-post', [
                'json' => $testData,
                'headers' => ['Content-Type' => 'application/json']
            ]);
            echo "<h3>Test POST Data:</h3>";
            echo "<pre>" . $response->getBody() . "</pre>";
            
        } catch (\Exception $e) {
            echo "<h3>Error:</h3>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}