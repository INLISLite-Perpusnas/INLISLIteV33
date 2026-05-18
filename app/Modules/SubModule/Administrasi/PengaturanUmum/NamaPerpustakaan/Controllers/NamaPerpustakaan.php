<?php

namespace NamaPerpustakaan\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class NamaPerpustakaan extends \Base\Controllers\BaseController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $branchModel;
	public $modulePath;
	public $settingModel;
	public $db;
	public $uploadPath;
	public $validation;
	public $session;


	function __construct()
	{
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->db= db_connect();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->branchModel = new \NamaPerpustakaan\Models\BranchModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		$this->modulePath = ROOTPATH . 'public/uploads/branch/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function index()
	{
		$logo=$this->db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value?:"Perpustakaan Mitra";
		$logokop=$this->db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow()->Value?:"Perpustakaan Mitra";
		
        $this->data['logo']=$logo;
		$this->data['logo_kop']=$logokop;

		$this->data['nama_perpustakaan'] = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
		$this->data['nama_lokasi_perpustakaan'] = $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? 'Alamat Perpustakaan Mitra';
		$this->data['npp_perpustakaan'] = $this->settingModel->where('Name', 'NPPPerpustakaan')->first()->Value ?? 'NPP Perpustakaan Mitra';
		$this->data['Branch_id'] = $this->settingModel->where('Name', 'Branch_id')->first()->Value ?? 'ID Perpustakaan Mitra';
		$this->data['provinsi_id'] = $this->settingModel->where('Name', 'ProvinsiID')->first()->Value ?? '';
        $this->data['kabkota_id'] = $this->settingModel->where('Name', 'KabKotaID')->first()->Value ?? '';
        $this->data['kecamatan_id'] = $this->settingModel->where('Name', 'KecamatanID')->first()->Value ?? '';
        $this->data['kelurahan_id'] = $this->settingModel->where('Name', 'KelurahanID')->first()->Value ?? '';
		$this->data['lokasi_perpustakaan'] = $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? 'Lokasi Perpustakaan Mitra';
		$this->data['email_perpustakaan'] = $this->settingModel->where('Name', 'EmailPerpustakaan')->first()->Value ?? 'email@perpustakaan.mitra';
		$this->data['jam_operasional'] = $this->settingModel->where('Name', 'JamOperasional')->first()->Value ?? 'Jam Operasional Perpustakaan Mitra';
		$this->data['instagram'] = $this->settingModel->where('Name', 'Instagram')->first()->Value ?? '';
		$this->data['facebook'] = $this->settingModel->where('Name', 'Facebook')->first()->Value ?? '';
		$this->data['youtube'] = $this->settingModel->where('Name', 'Youtube')->first()->Value ?? '';
		$this->data['phone'] = $this->settingModel->where('Name', 'Phone')->first()->Value ?? '';
		$this->data['is_use_kop'] = $this->settingModel->where('Name', 'IsUseKop')->first()->Value ?? 0;
		$this->data['jenis_perpustakaan'] = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? '';
		$this->data['tulisan_banner'] = $this->settingModel->where('Name', 'TulisanBanner')->first()->Value ?? '';
		$this->data['tentang_kami'] = $this->settingModel->where('Name', 'TentangKami')->first()->Value ?? '';
		$this->data['title'] = 'Nama Perpustakaan';

		echo view('NamaPerpustakaan\Views\update', $this->data);
	}

	/**
	 * API endpoint untuk pencarian perpustakaan
	 */
	public function searchPerpustakaan()
{
    $keyword = $this->request->getGet('q');
    if (!$keyword || strlen(trim($keyword)) < 3) {
        return $this->failValidationErrors('Keyword minimal 3 karakter');
    }
    $url = env('FLASK_API_BASEURL') . '/api/perpustakaan?q=' . urlencode($keyword);


    $apiKey = env('API_KEY');
    if (!$apiKey) {
        log_message('error', 'API_KEY tidak di-set di .env');
        return $this->failServerError('Konfigurasi sisi server tidak lengkap.');
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_USERAGENT      => 'curl/8.8.0',
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, 
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'x-api-key: ' . $apiKey, 
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errNo    = curl_errno($ch);
    $errMsg   = curl_error($ch);
    curl_close($ch);

    if ($errNo) {
        log_message('error', "cURL Error [{$errNo}]: {$errMsg}");
        return $this->failServerError('Gagal terhubung ke API eksternal');
    }

    if ($httpCode !== 200) {
        log_message('error', 'API eksternal mengembalikan HTTP ' . $httpCode . ': ' . $response);
        if ($httpCode === 401) {
            return $this->failUnauthorized('Autentikasi ke API eksternal gagal (API Key salah?).');
        }
        return $this->failServerError('Terjadi kesalahan pada API eksternal');
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', 'JSON decode error: ' . json_last_error_msg());
        return $this->failServerError('Respons JSON dari API eksternal tidak valid');
    }

    return $this->respond([
        'status'  => 'success',
        'data'    => $data['data'] ?? $data ?? [],
        'message' => 'Data berhasil diambil'
    ]);
}

	public function daftarkanInlisLite()
	{
		$npp          = $this->settingModel->where('Name', 'NPPPerpustakaan')->first()->Value ?? '';
		$nama         = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? '';
		$alamat       = $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? '';
		$email        = $this->settingModel->where('Name', 'EmailPerpustakaan')->first()->Value ?? '';
		$jenis        = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? '';
		$phone        = $this->settingModel->where('Name', 'Phone')->first()->Value ?? '';
		$provinsi_id  = $this->settingModel->where('Name', 'ProvinsiID')->first()->Value ?? '';
		$kabkota_id   = $this->settingModel->where('Name', 'KabKotaID')->first()->Value ?? '';
		$kecamatan_id = $this->settingModel->where('Name', 'KecamatanID')->first()->Value ?? '';
		$kelurahan_id = $this->settingModel->where('Name', 'KelurahanID')->first()->Value ?? '';

		// Override with request body if provided (from form fields)
		$body = $this->request->getJSON(true) ?? [];
		if (!empty($body)) {
			$npp          = $body['npp']          ?? $npp;
			$nama         = $body['nama']         ?? $nama;
			$alamat       = $body['alamat']       ?? $alamat;
			$email        = $body['email']        ?? $email;
			$jenis        = $body['jenis']        ?? $jenis;
			$phone        = $body['phone']        ?? $phone;
			$provinsi_id  = $body['provinsi_id']  ?? $provinsi_id;
			$kabkota_id   = $body['kabkota_id']   ?? $kabkota_id;
			$kecamatan_id = $body['kecamatan_id'] ?? $kecamatan_id;
			$kelurahan_id = $body['kelurahan_id'] ?? $kelurahan_id;
		}

		$flaskUrl = env('FLASK_API_BASEURL') . '/api/pengguna-inlislite';
		$apiKey   = env('API_KEY');

		if (!$apiKey) {
			return $this->respond(['status' => 'error', 'message' => 'Konfigurasi API_KEY tidak ditemukan.'], 500);
		}

		$payload = json_encode([
			'npp'          => $npp,
			'nama'         => $nama,
			'alamat'       => $alamat,
			'email'        => $email,
			'jenis'        => $jenis,
			'phone'        => $phone,
			'prov_id'      => $provinsi_id,
			'kabkota_id'   => $kabkota_id,
			'kecamatan_id' => $kecamatan_id,
			'kelurahan_id' => $kelurahan_id,
		]);

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL            => $flaskUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS      => 5,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json',
				'x-api-key: ' . $apiKey,
			],
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errNo    = curl_errno($ch);
		$errMsg   = curl_error($ch);
		curl_close($ch);

		if ($errNo) {
			log_message('error', "daftarkanInlisLite cURL Error [{$errNo}]: {$errMsg}");
			return $this->respond(['status' => 'error', 'message' => 'Gagal terhubung ke API eksternal.'], 500);
		}

		$data = json_decode($response, true);

		if ($httpCode >= 200 && $httpCode < 300) {
			return $this->respond(['status' => 'success', 'message' => 'Pendaftaran InlisLite berhasil.', 'data' => $data]);
		}

		log_message('error', "daftarkanInlisLite HTTP {$httpCode}: {$response}");
		return $this->respond([
			'status'   => 'error',
			'message'  => $data['error'] ?? $data['message'] ?? 'Pendaftaran InlisLite gagal.',
			'http_code' => $httpCode,
			'data'     => $data ?? $response,
		], 200);
	}

	/**
	 * Update data perpustakaan dengan data dari pencarian
	 */

	public function update()
	{
		$this->validation->setRule('nama_perpustakaan', 'Nama Perpustakaan', 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {	
			$LayananOperasionl_Str = htmlspecialchars($this->request->getPost('LayananOperasionl') ?? '', ENT_QUOTES, 'UTF-8');
			
			// Update settings
			$dataToUpdate = [
				'NamaPerpustakaan' => trim($this->request->getPost('nama_perpustakaan')),
				'NamaLokasiPerpustakaan' => trim($this->request->getPost('nama_lokasi_perpustakaan')),
				'NPPPerpustakaan' => trim($this->request->getPost('npp_perpustakaan')),
				'EmailPerpustakaan' => trim($this->request->getPost('email_perpustakaan')),
				'Instagram' => trim($this->request->getPost('instagram')),
				'Facebook' => trim($this->request->getPost('facebook')),
				'Youtube' => trim($this->request->getPost('youtube')),
				'Phone' => trim($this->request->getPost('phone')),
				'Branch_id' => trim($this->request->getPost('branch_id')) ?: '',
				'TulisanBanner' => trim($this->request->getPost('tulisan_banner')),
				'TentangKami' => trim($this->request->getPost('tentang_kami')),
				'IsUseKop' => $this->request->getPost('IsUseKop') ? 1 : 0,
				'JamOperasional' => $LayananOperasionl_Str,
				'JenisPerpustakaan' => trim($this->request->getPost('jenis_perpustakaan')),
				'Branch_id' => trim($this->request->getPost('branch_id')),
				'ProvinsiID' => trim($this->request->getPost('provinsi_id')),
                'KabKotaID' => trim($this->request->getPost('kabkota_id')),
                'KecamatanID' => trim($this->request->getPost('kecamatan_id')),
                'KelurahanID' => trim($this->request->getPost('kelurahan_id')),
			];

			$success = true;
			foreach ($dataToUpdate as $name => $value) {
				$row = $this->settingModel->where('Name', $name)->first();
				if ($row) {
					if (!$this->settingModel->update($row->ID, ['Value' => $value])) {
						$success = false;
					}
				}
			}

			if ($success) {
				$this->session->setFlashdata('swal_icon', 'success');
				$this->session->setFlashdata('swal_title', 'Berhasil');
				$this->session->setFlashdata('swal_text', 'Data berhasil disimpan');
			} else {
				$this->session->setFlashdata('swal_icon', 'error');
				$this->session->setFlashdata('swal_title', 'Gagal');
				$this->session->setFlashdata('swal_text', 'Data gagal disimpan');
			}
			return redirect()->back();
		} else {
			$errors = implode(', ', $this->validation->getErrors());
			set_message('toastr_msg', $errors);
			set_message('toastr_type', 'error');
			return redirect()->back();
		}
	}
}