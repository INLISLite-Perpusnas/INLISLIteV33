<?php

namespace Dashboard\Controllers;

class Dashboard extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $userModel;
	public $anggotaModel;
	public $memberguestModel;
	public $katalogModel;
	public $koleksiModel;
	public $peminjamanModel;
	public $settingModel;

	function __construct()
	{
		helper('app');
        helper('anggota');
		$this->userModel = new \Auth\Models\UserModel();
		$this->anggotaModel= new \Member\Models\MemberModel();
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->katalogModel = new \Katalog\Models\KatalogModel();
		$this->koleksiModel = new \Peminjaman\Models\CollectionModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
		$this->peminjamanModel=new \Peminjaman\Models\CollectionLoanItemModel();
	}

	private function getSettingValue($name, $default = null)
	{
		$setting = $this->settingModel->where('Name', $name)->first();
		return $setting ? $setting->Value : $default;
	}

	public function index()
{
    if(is_member('anggota')){
        $page = 'anggota';
        $member_no = user()->username;
        $member = get_member($member_no);
        $peminjaman = get_peminjaman($member->ID);
        $pelanggaran = get_pelanggaran($member->ID);

        $this->data['total_peminjaman'] = count($peminjaman);
        $this->data['total_pelanggaran'] = count($pelanggaran);

        echo view('Dashboard\Views\\' . $page, $this->data);
    }else{
        $page = 'index';

        $useCache = env('is_dashboard_cache') == 1;
        $cacheKey = 'dashboard_stats_data';
        $cacheTTL = 3600; // 1 Jam

        $cachedData = cache($cacheKey);

        if ($cachedData === null) {
            $cachedData = [
                'nama_perpustakaan' => $this->getSettingValue('NamaPerpustakaan', 'Perpustakaan Mitra'),
                'nama_lokasi_perpustakaan' => $this->getSettingValue('NamaLokasiPerpustakaan', 'Alamat Perpustakaan Mitra'),
                'npp_perpustakaan' => $this->getSettingValue('NPPPerpustakaan', 'NPP Perpustakaan Mitra'),
                'total_user_active' => $this->userModel->where('active', 1)->countAllResults(),
                'total_user_inactive' => $this->userModel->where('active', 0)->countAllResults(),
                'total_anggota' => $this->anggotaModel->countAllResults(),
                'total_anggota_guest' => $this->memberguestModel->where('NoAnggota !=', null)->countAllResults(),
                'total_nonanggota_guest' => $this->memberguestModel->where('NoAnggota', null)->countAllResults(),
                'total_anggota_bebas_pustaka' => $this->anggotaModel->where('StatusAnggota_id', 5)->countAllResults(),
                'total_katalog' => $this->katalogModel->countAllResults(),
                'total_koleksi' => $this->koleksiModel->countAllResults(),
                'total_peminjaman' => $this->peminjamanModel->countAllResults(),
            ];

            if ($useCache) {
                cache()->save($cacheKey, $cachedData, $cacheTTL);
            }
        }

        $this->data = array_merge($this->data, $cachedData);

        $this->data['title'] = 'Dashboard';

        echo view('Dashboard\Views\\' . $page, $this->data);
    }
}

public function kirimlaporan()
{
    // 1. Validasi Tanggal (Server Side)
    if (date('d') > 30) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Pengiriman hanya bisa dilakukan tanggal 1-30.'
        ])->setStatusCode(403);
    }

    $payload = [
        'nama_perpustakaan'    => $this->getSettingValue('NamaPerpustakaan', 'Perpustakaan Mitra'),
        'npp'                  => $this->getSettingValue('NPPPerpustakaan', 'NPP Perpustakaan Mitra'),
        'alamat'               => $this->getSettingValue('NamaLokasiPerpustakaan', 'Alamat Perpustakaan Mitra'),
        'email'                => $this->getSettingValue('EmailPerpustakaan', 'Email Perpustakaan Mitra'),
        'Provinsi_kode'        => $this->getSettingValue('ProvinsiID', '32'),        // <-- disamakan jadi "Provinsi_kode"
        'kabkota_kode'    => str_replace('.', '', $this->getSettingValue('KabKotaID', '31.71')),
'kecamatan_kode'  => str_replace('.', '', $this->getSettingValue('KecamatanID', '31.71.01')),
'kelurahan_kode'  => str_replace('.', '', $this->getSettingValue('KelurahanID', '31.71.01.1001')),
        'periode'              => date('Y-m-d'),
        'jumlah_anggota'       => $this->anggotaModel->countAllResults(),
        'kunjungan_anggota'    => $this->memberguestModel->where('NoAnggota !=', null)->countAllResults(),
        'kunjungan_non_anggota'=> $this->memberguestModel->where('NoAnggota', null)->countAllResults(),
        'total_katalog'        => $this->katalogModel->countAllResults(),
        'total_koleksi'        => $this->koleksiModel->countAllResults(),
        'total_peminjaman'     => $this->peminjamanModel->countAllResults(),
        'total_baca_ditempat'  => $this->memberguestModel->where('NoAnggota !=', null)->countAllResults()
                                 + $this->memberguestModel->where('NoAnggota', null)->countAllResults(),
    ];

    // 3. Kirim ke Flask menggunakan CI4 HTTP Client
    $flaskUrl = rtrim(env('FLASK_API_BASEURL'), '/') . '/api/dashboard/rekap'; // hindari double slash
    $apiKey   = env('API_KEY');

    if (empty($flaskUrl) || empty($apiKey)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'FLASK_API_BASEURL atau API_KEY belum diset di .env'
        ])->setStatusCode(500);
    }

    $client = \Config\Services::curlrequest([
        'timeout'         => 10,
        'connect_timeout' => 5,
    ]);

    try {
        $response = $client->request('POST', $flaskUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-KEY'    => $apiKey
            ],
            'json'        => $payload,
            'http_errors' => false
        ]);

        $statusCode = $response->getStatusCode();
        $rawBody    = $response->getBody();
        $body       = json_decode($rawBody);

        // Jika body bukan JSON valid (misal Flask error 500 balikin HTML traceback)
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'Response Flask bukan JSON. Status: ' . $statusCode . ' Body: ' . $rawBody);
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Server pusat mengembalikan response tidak valid (bukan JSON). Status: ' . $statusCode
            ])->setStatusCode(500);
        }

        if ($statusCode == 201) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Laporan berhasil dikirim ke Pusat!'
            ]);
        } elseif ($statusCode == 409) {
            return $this->response->setJSON([
                'status'  => 'warning',
                'message' => 'Data periode ini sudah ada di server pusat.'
            ]);
        } elseif ($statusCode == 404) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $body->error ?? 'NPP tidak terdaftar di server pusat.'
            ])->setStatusCode(404);
        } else {
            // Log detail lengkap ke server, tapi jangan expose payload mentah ke user/frontend
            log_message('error', 'Gagal kirim laporan. Status: ' . $statusCode . ' Payload: ' . json_encode($payload) . ' Body: ' . $rawBody);

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $body->error ?? ('Gagal mengirim data. Status: ' . $statusCode)
            ])->setStatusCode(500);
        }

    } catch (\Exception $e) {
        log_message('error', 'Koneksi ke server pusat gagal: ' . $e->getMessage());
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Koneksi ke server pusat gagal: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}
}
