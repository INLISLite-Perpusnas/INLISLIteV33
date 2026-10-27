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

	// Toggle fitur di settingparameters: Name => [label, nilai aktif, nilai nonaktif]
	private const FEATURE_TOGGLES = [
		'is_opac_cache'            => ['Cache Halaman OPAC', '1', '0'],
		'is_dashboard_cache'       => ['Cache Dashboard', '1', '0'],
		'is_home_cache'            => ['Cache Halaman Beranda', '1', '0'],
		'isdrm'                    => ['DRM Konten Digital', '1', '0'],
		'is_keanggotaan_online'    => ['Pendaftaran Keanggotaan Online', '1', '0'],
		'is_perpustakaan_keliling' => ['Perpustakaan Keliling', '1', '0'],
		'is_populer'               => ['Koleksi Populer', '1', '0'],
		'is_sering_dipinjam'       => ['Koleksi Sering Dipinjam', '1', '0'],
		'is_sering_dibaca'         => ['Koleksi Sering Dibaca', '1', '0'],
		'UsulanKoleksiLKD'         => ['Usulan Koleksi', 'True', 'False'],
	];


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
		$this->data['feature_toggles'] = [];
		foreach (self::FEATURE_TOGGLES as $name => [$label, $on]) {
			$value = $this->settingModel->where('Name', $name)->first()->Value ?? '';
			$this->data['feature_toggles'][$name] = [
				'label'   => $label,
				'checked' => strtoupper(trim((string) $value)) === strtoupper($on),
			];
		}
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

		// Ensure region IDs are integers or null (JS sends integer row IDs from t_region)
		$toIntOrNull = fn($v) => ($v === null || $v === '' || $v === 'null') ? null : (int)$v;
		$provinsi_id  = $toIntOrNull($provinsi_id);
		$kabkota_id   = $toIntOrNull($kabkota_id);
		$kecamatan_id = $toIntOrNull($kecamatan_id);
		$kelurahan_id = $toIntOrNull($kelurahan_id);

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

			$toggles = (array) ($this->request->getPost('feature_toggles') ?? []);
			foreach (self::FEATURE_TOGGLES as $name => [, $on, $off]) {
				$dataToUpdate[$name] = !empty($toggles[$name]) ? $on : $off;
			}

			$success = true;
			foreach ($dataToUpdate as $name => $value) {
				$row = $this->settingModel->where('Name', $name)->first();
				if ($row) {
					if (!$this->settingModel->update($row->ID, ['Value' => $value])) {
						$success = false;
					}
				} else {
					// Baris settingnya belum ada di settingparameters (mis. 'JamOperasional'
					// yang baru ditambahkan) — sebelumnya value ini dibuang begitu saja
					// karena hanya di-update, tidak pernah di-insert.
					if (!$this->settingModel->insert(['Name' => $name, 'Value' => $value])) {
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
			$this->session->setFlashdata('swal_icon', 'error');
			$this->session->setFlashdata('swal_title', 'Validasi Gagal');
			$this->session->setFlashdata('swal_text', $errors);
			return redirect()->back();
		}
	}

	public function updateDatabase()
	{
		if (!$this->request->is('post') || trim(str_replace('index.php/', '', uri_string()), '/') !== 'master-nama-perpustakaan/update-database') {
			return $this->response->setStatusCode(405)->setBody('Metode tidak diizinkan.');
		}

		return $this->runDatabaseMaintenance(function () {
			$runner = \Config\Services::migrations();
			if (!$runner->latest()) {
				throw new \RuntimeException('Migrasi database gagal.');
			}

			$db = \Config\Database::connect('data');
			$seeders = glob(APPPATH . 'Database/Seeds/*Seeder.php');
			if ($seeders === false) {
				throw new \RuntimeException('Daftar seeder tidak dapat dibaca.');
			}
			sort($seeders, SORT_STRING);
			$seeder = \Config\Database::seeder('data');
			$history = $db->table('database_seeder_runs');
			$completed = 0;
			foreach ($seeders as $file) {
				$name = pathinfo($file, PATHINFO_FILENAME);
				$checksum = hash_file('sha256', $file);
				if ($checksum === false) {
					throw new \RuntimeException('Seeder tidak dapat dibaca: ' . $name);
				}
				$previous = $history->where('name', $name)->get()->getRow();
				if ($previous && $previous->checksum === $checksum) {
					continue;
				}

				$seeder->call($name);
				if ($previous) {
					$saved = $history->where('name', $name)->update(['checksum' => $checksum, 'executed_at' => date('Y-m-d H:i:s')]);
				} else {
					$saved = $history->insert(['name' => $name, 'checksum' => $checksum, 'executed_at' => date('Y-m-d H:i:s')]);
				}
				if (!$saved) {
					throw new \RuntimeException('Riwayat seeder gagal disimpan: ' . $name);
				}
				$completed++;
			}
			return $completed;
		}, 'Update database selesai.');
	}

	public function backupDatabase()
	{
		return $this->downloadBackup('database', 'sql');
	}

	public function backupUploads()
	{
		return $this->downloadBackup('uploads', 'zip');
	}

	private function downloadBackup(string $type, string $extension)
	{
		if (!$this->request->is('post')) {
			return $this->response->setStatusCode(405)->setBody('Metode tidak diizinkan.');
		}
		if (!is_member('admin')) {
			return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
		}

		$path = tempnam(WRITEPATH . 'cache', 'backup-');
		if ($path === false) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Tidak dapat membuat file backup sementara.');
		}

		try {
			$backup = new BackupService();
			if ($type === 'database') {
				$db = \Config\Database::connect('data', false);
				try {
					$db->initialize();
					$backup->database($db->getConnection(), $path);
				} finally {
					$db->close();
				}
			} else {
				$backup->uploads(ROOTPATH . 'public/uploads', $path);
			}
			$filename = 'backup-' . $type . '-' . date('Ymd-His') . '.' . $extension;
			register_shutdown_function(static function () use ($path) {
				@unlink($path);
			});
			return $this->response->download($path, null)->setFileName($filename);
		} catch (\Throwable $exception) {
			@unlink($path);
			log_message('error', 'Backup ' . $type . ' gagal: ' . $exception->getMessage());
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Backup gagal. Periksa log aplikasi dan ruang penyimpanan.');
		}
	}

	/**
	 * Ubah CI_ENVIRONMENT di file .env (production/development). Berlaku mulai request berikutnya.
	 */
	public function updateEnvironment()
	{
		if (!is_member('admin')) {
			return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
		}

		$mode = (string) $this->request->getPost('environment');
		if (!in_array($mode, ['production', 'development'], true)) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Mode tidak dikenal.');
		}

		$envFile = ROOTPATH . '.env';
		$content = is_file($envFile) ? file_get_contents($envFile) : '';
		if ($content === false || (is_file($envFile) && !is_writable($envFile))) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'File .env tidak dapat dibaca atau ditulis oleh server.');
		}

		// Ganti baris CI_ENVIRONMENT yang aktif (bukan komentar); tambahkan bila belum ada.
		$line = 'CI_ENVIRONMENT = ' . $mode;
		$updated = preg_replace('/^[ \t]*CI_ENVIRONMENT[ \t]*=.*$/m', $line, $content, 1, $count);
		if ($count === 0) {
			$updated = rtrim($content) . PHP_EOL . $line . PHP_EOL;
		}

		if ($updated !== $content && file_put_contents($envFile, $updated, LOCK_EX) === false) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'File .env gagal disimpan.');
		}

		log_message('notice', 'CI_ENVIRONMENT diubah ke ' . $mode . ' oleh user ID ' . $this->auth->id());
		return redirect()->back()->with('swal_icon', 'success')
			->with('swal_title', 'Berhasil')->with('swal_text', 'Mode aplikasi diubah ke ' . $mode . '.');
	}

	private function runDatabaseMaintenance(callable $task, string $successMessage)
	{
		if (!is_member('admin')) {
			return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
		}

		$lock = @fopen(WRITEPATH . 'cache/database-maintenance.lock', 'c');
		if ($lock === false) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Tidak dapat membuat kunci pemeliharaan database.');
		}
		if (!flock($lock, LOCK_EX | LOCK_NB)) {
			fclose($lock);
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Pemeliharaan database sedang berjalan.');
		}

		try {
			$completed = $task();
			return redirect()->back()->with('swal_icon', 'success')
				->with('swal_title', 'Berhasil')->with('swal_text', $successMessage . ' Seeder dijalankan: ' . $completed . '.');
		} catch (\Throwable $exception) {
			log_message('error', 'Pemeliharaan database gagal: ' . $exception->getMessage());
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')
				->with('swal_text', 'Proses gagal. Periksa koneksi database dan log aplikasi sebelum mengulang.');
		} finally {
			flock($lock, LOCK_UN);
			fclose($lock);
		}
	}

	public function updateSourceCode()
	{
		if (!is_member('admin')) {
			return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
		}

		if (!function_exists('proc_open')) {
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Eksekusi proses dinonaktifkan pada server.');
		}

		$lock = @fopen(WRITEPATH . 'cache/source-update.lock', 'c');
		if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
			if (is_resource($lock)) {
				fclose($lock);
			}
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', 'Update source code sedang berjalan.');
		}

		try {
			[$code, $root] = $this->runGit(['rev-parse', '--show-toplevel']);
			if ($code !== 0 || realpath(trim($root)) !== realpath(ROOTPATH)) {
				throw new \RuntimeException('Folder aplikasi bukan root repository Git.');
			}

			[$code, $branch] = $this->runGit(['branch', '--show-current']);
			if ($code !== 0 || trim($branch) !== 'main') {
				throw new \RuntimeException('Update hanya tersedia saat branch aktif main.');
			}

			[$code, $origin] = $this->runGit(['remote', 'get-url', 'origin']);
			if ($code !== 0 || !preg_match('~^(?:https://github\.com/|git@github\.com:)INLISLite-Perpusnas/INLISLIteV33(?:\.git)?/?$~i', trim($origin))) {
				throw new \RuntimeException('Remote origin bukan repository GitHub proyek ini.');
			}

			[$code, $changes] = $this->runGit(['status', '--porcelain']);
			if ($code !== 0 || $changes !== '') {
				throw new \RuntimeException('Ada perubahan lokal. Commit atau amankan perubahan sebelum update.');
			}

			[$code, $before] = $this->runGit(['rev-parse', '--short', 'HEAD']);
			if ($code !== 0) {
				throw new \RuntimeException('Tidak dapat membaca versi kode saat ini.');
			}

			[$code] = $this->runGit(['pull', '--ff-only', 'origin', 'main'], 90);
			if ($code !== 0) {
				throw new \RuntimeException('Git pull gagal. Periksa koneksi GitHub, izin folder, dan riwayat branch.');
			}

			[$code, $after] = $this->runGit(['rev-parse', '--short', 'HEAD']);
			if ($code !== 0) {
				throw new \RuntimeException('Kode terbarui, tetapi versi terbaru gagal dibaca.');
			}

			$message = trim($before) === trim($after)
				? 'Source code sudah versi terbaru.'
				: 'Source code berhasil diperbarui dari origin/main (' . trim($before) . ' ke ' . trim($after) . '). Periksa kebutuhan migrasi dan dependensi.';
			return redirect()->back()->with('swal_icon', 'success')
				->with('swal_title', 'Berhasil')->with('swal_text', $message);
		} catch (\Throwable $exception) {
			$message = $exception instanceof \RuntimeException
				? $exception->getMessage()
				: 'Update gagal. Periksa konfigurasi Git dan izin proses web di server.';
			return redirect()->back()->with('swal_icon', 'error')
				->with('swal_title', 'Gagal')->with('swal_text', $message);
		} finally {
			flock($lock, LOCK_UN);
			fclose($lock);
		}
	}

	private function runGit(array $arguments, int $timeout = 15): array
	{
		$stdout = @tempnam(WRITEPATH . 'cache/', 'git_');
		$stderr = @tempnam(WRITEPATH . 'cache/', 'git_');
		if ($stdout === false || $stderr === false) {
			if ($stdout !== false) unlink($stdout);
			if ($stderr !== false) unlink($stderr);
			throw new \RuntimeException('Tidak dapat menyiapkan berkas sementara untuk Git.');
		}

		$process = null;
		try {
			$command = array_merge([(string) env('GIT_BINARY', 'git'), '-C', ROOTPATH], $arguments);
			$environment = array_merge(getenv() ?: [], [
				'GIT_TERMINAL_PROMPT' => '0',
				'GCM_INTERACTIVE' => 'Never',
			]);
			$process = @proc_open($command, [
				0 => ['pipe', 'r'],
				1 => ['file', $stdout, 'w'],
				2 => ['file', $stderr, 'w'],
			], $pipes, ROOTPATH, $environment, ['bypass_shell' => true]);
			if (!is_resource($process)) {
				throw new \RuntimeException('Git tidak dapat dijalankan oleh server.');
			}
			fclose($pipes[0]);

			$started = microtime(true);
			while (true) {
				$status = proc_get_status($process);
				if (!$status['running']) {
					break;
				}
				if (microtime(true) - $started > $timeout) {
					proc_terminate($process);
					throw new \RuntimeException('Proses Git melewati batas waktu. Periksa status repository sebelum mengulang.');
				}
				usleep(100000);
			}

			$exitCode = $status['exitcode'];
			$closed = proc_close($process);
			$process = null;
			return [$exitCode >= 0 ? $exitCode : $closed, trim((string) file_get_contents($stdout, false, null, 0, 8192))];
		} finally {
			if (is_resource($process)) {
				proc_close($process);
			}
			unlink($stdout);
			unlink($stderr);
		}
	}
}
