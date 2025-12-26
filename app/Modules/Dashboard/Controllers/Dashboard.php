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
		$this->userModel = new \Auth\Models\UserModel();
		$this->anggotaModel= new \Member\Models\MemberModel();
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->katalogModel = new \Katalog\Models\KatalogModel();
		$this->koleksiModel = new \Peminjaman\Models\CollectionModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
		$this->peminjamanModel=new \Peminjaman\Models\CollectionLoanItemModel();
	}

	public function index()
{
    $page = 'index';
    
    // 1. Cek konfigurasi dari .env
    // Jika nilai 'is_dashboard_cache' adalah 1, maka $useCache bernilai true.
    $useCache = env('is_dashboard_cache') == 1;
    
    $cacheKey = 'dashboard_stats_data';
    $cacheTTL = 3600; // 1 Jam

    $cachedData = null;

    // 2. Cek Cache HANYA jika fitur diaktifkan di .env
    if ($useCache) {
        $cachedData = cache($cacheKey);
    }

    // 3. Jika data tidak ditemukan di cache (Cache Miss) ATAU fitur cache dimatikan
    if ($cachedData === null) {
        
        // --- Jalankan Query Database (Data Segar) ---
        
        // Group A: Pengaturan
        $cachedData['nama_perpustakaan']        = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $cachedData['nama_lokasi_perpustakaan'] = $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? 'Alamat Perpustakaan Mitra';
        $cachedData['npp_perpustakaan']         = $this->settingModel->where('Name', 'NPPPerpustakaan')->first()->Value ?? 'NPP Perpustakaan Mitra';
        
        // Group B: Statistik User & Anggota
        $cachedData['total_user_active']            = $this->userModel->where('active', 1)->countAllResults();
        $cachedData['total_user_inactive']          = $this->userModel->where('active', 0)->countAllResults();
        $cachedData['total_anggota']                = $this->anggotaModel->countAllResults();
        $cachedData['total_anggota_guest']          = $this->memberguestModel->where('NoAnggota !=', null)->countAllResults();
        $cachedData['total_nonanggota_guest']       = $this->memberguestModel->where('NoAnggota', null)->countAllResults();
        $cachedData['total_anggota_bebas_pustaka']  = $this->anggotaModel->where('StatusAnggota_id', 5)->countAllResults();
        
        // Group C: Statistik Koleksi
        $cachedData['total_katalog']    = $this->katalogModel->countAllResults();
        $cachedData['total_koleksi']    = $this->koleksiModel->countAllResults();
        $cachedData['total_peminjaman'] = $this->peminjamanModel->countAllResults();

        // 4. Simpan ke cache HANYA jika fitur diaktifkan
        if ($useCache) {
            cache()->save($cacheKey, $cachedData, $cacheTTL);
        }
    }

    // 5. Gabungkan data
    if (!isset($this->data)) {
        $this->data = [];
    }
    $this->data = array_merge($this->data, $cachedData);

    $this->data['title'] = 'Dashboard';

    echo view('Dashboard\Views\\' . $page, $this->data);
}

	public function kirimlaporan()
{
    // 1. Validasi Tanggal (Server Side)
    if (date('d') > 5) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Pengiriman hanya bisa dilakukan tanggal 1-5.'
        ])->setStatusCode(403);
    }

    // 2. Hitung Ulang Data (Agar tidak bisa dimanipulasi user dari browser)
    $payload = [
        'nama_perpustakaan' => $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra', // Sebaiknya ambil dari Config/DB
        'npp' => $this->settingModel->where('Name', 'NPPPerpustakaan')->first()->Value ?? 'NPP Perpustakaan Mitra',
        'alamat' => $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? 'Alamat Perpustakaan Mitra',
        'email' => $this->settingModel->where('Name', 'EmailPerpustakaan')->first()->Value ?? 'Email Perpustakaan Mitra',
        'Provinsi_kode' => $this->settingModel->where('Name', 'ProvinsiID')->first()->Value ?? '32',
        'kabkota_kode' => $this->settingModel->where('Name', 'KabKotaID')->first()->Value ?? '3171',
        'kecamatan_kode' => $this->settingModel->where('Name', 'KecamatanID')->first()->Value ?? '3171010',
        'kelurahan_kode' => $this->settingModel->where('Name', 'KelurahanID')->first()->Value ?? '3171010001',
        'periode' => date('Y-m-d'),
        'jumlah_anggota' => $this->anggotaModel->countAllResults(),
        'kunjungan_anggota' => $this->memberguestModel->where('NoAnggota !=', null)->countAllResults(),
        'kunjungan_non_anggota' => $this->memberguestModel->where('NoAnggota', null)->countAllResults(),
        'total_katalog' => $this->katalogModel->countAllResults(),
        'total_koleksi' => $this->koleksiModel->countAllResults(),
        'total_peminjaman' => $this->peminjamanModel->countAllResults(),
        'total_baca_ditempat' => $this->memberguestModel->where('NoAnggota !=', null)->countAllResults()+$this->memberguestModel->where('NoAnggota', null)->countAllResults(),
    ];

    // 3. Kirim ke Flask menggunakan CURL / CI4 HTTP Client
    $flaskUrl = 'http://127.0.0.1:4000/api/dashboard/rekap';
    $apiKey   = env('API_KEY'); // Simpan ini di .env lebih aman

    $client = \Config\Services::curlrequest();

    try {
        $response = $client->request('POST', $flaskUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-KEY'    => $apiKey
            ],
            'json' => $payload,
            'http_errors' => false // Agar kita bisa handle error code manual
        ]);

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody());

        if ($statusCode == 201) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Laporan berhasil dikirim ke Pusat!'
            ]);
        } elseif ($statusCode == 409) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Data periode ini sudah ada di server pusat.'
            ]);
        } else {
             return $this->response->setJSON([
                'status' => 'error',
                'message' => $body->error ?? 'Gagal mengirim data.'
            ])->setStatusCode(500);
        }

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Koneksi ke server pusat gagal: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}
}
