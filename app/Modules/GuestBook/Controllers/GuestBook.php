<?php

namespace GuestBook\Controllers;

use KiosBukuTamu\Libraries\KioskAuth;

class GuestBook extends \App\Controllers\BaseController
{
	public $memberguestModel;
	public $groupguestModel;
	public $uploadPath;
	public $modulePath;
	public $data = [];
	public $language;
	public $session;
	public $validation;
	public $db;
	public $settingModel;

	function __construct()
	{
		$this->language = \Config\Services::language();
		$this->language->setLocale('id');
		$this->session = session();
		$this->db = \Config\Database::connect('data');
		$this->validation = \Config\Services::validation();
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->groupguestModel = new \BukuTamu\Models\GroupGuestModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		// helper('member');
		helper('lokasiruang');
		helper('home');
	}


	public function index()
	{

		$location = $this->getSelectedLocation();
		if (!$location) {
			return redirect()->to('buku-tamu/lokasi');
		}

		$this->data['SettingBukuTamu'] = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

		// Get member number from request — trim to handle barcode scanner spaces
		// Bukan string (mis. member_no[]=x) diperlakukan kosong
		$member_no_input = $this->request->getGet('member_no');
		$member_no = is_string($member_no_input) ? trim($member_no_input) : '';

		// Batasi pencarian per IP: nomor anggota berurutan sehingga nama anggota bisa dipanen
		$throttled = false;
		if ($member_no !== '') {
			$throttleKey = hash('sha256', 'guestbook-member:' . $this->request->getIPAddress());
			$throttled = !service('throttler')->check($throttleKey, 20, MINUTE);
		}

		// Get member data from database
		$member = ($member_no && !$throttled) ? $this->db->table('members')->where('MemberNo', $member_no)->get()->getRow() : null;

		$this->data['totalKunjungan'] = $this->countTodayVisits((int) $location->ID);

		// Set data for view
		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['data'] = $location;
		$this->data['member'] = $member;
		$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'Member=1', 'data');
		$this->data['visit_autosave_seconds'] = 30;
		$this->data['message'] = $this->validation->getErrors()
			? $this->validation->listErrors()
			: $this->session->getFlashdata('message');
		if ($throttled) {
			$this->data['message'] = 'Terlalu banyak pencarian. Silakan tunggu satu menit.';
			$this->response->setStatusCode(429);
		} elseif ($member_no !== '' && !$member && empty($this->data['message'])) {
			$this->data['message'] = 'Nomor Anggota ' . $member_no . ' tidak ditemukan.';
		}

		// Render view
		return view('GuestBook\Views\add', $this->data);
	}


	public function lokasi()
	{
		$this->response
			->setHeader('X-Frame-Options', 'SAMEORIGIN')
			->setHeader('X-Content-Type-Options', 'nosniff')
			->setHeader('Referrer-Policy', 'same-origin')
			->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

		$this->data['title'] = 'Atur Lokasi Buku Tamu';
		$this->data['meta_description'] = 'Pilih lokasi ruang perpustakaan untuk layanan buku tamu.';
		$this->data['is_guestbook_location'] = true;

		if ($this->request->getMethod() === 'POST') {
			$throttler = service('throttler');
			$throttleKey = hash('sha256', 'guestbook-location:' . $this->request->getIPAddress());
			if (!$throttler->check($throttleKey, 10, MINUTE)) {
				return redirect()->back()->withInput()->with('message', 'Terlalu banyak percobaan. Silakan tunggu satu menit.');
			}

			if ($this->hasArrayInput()) {
				return redirect()->back()->with('message', 'Data yang dikirim tidak valid.');
			}

			$code = KioskAuth::normalizeCode((string) $this->request->getPost('Code'));
			$isValid = $this->validation->setRules([
				'Code' => [
					'label' => 'Kode Aktivasi',
					'rules' => 'required|exact_length[8]|alpha_numeric',
				],
			])->run(['Code' => $code]);

			if (!$isValid) {
				return redirect()->back()->withInput()->with('message', 'Kode aktivasi harus 8 huruf/angka, contoh: K7QM-4XPD.');
			}

			$kioskAuth = new KioskAuth($this->db);
			try {
				$cookie = $kioskAuth->activate($code, $this->request->getIPAddress());
			} catch (\RuntimeException $e) {
				log_message('error', 'Gagal aktivasi kios: ' . $e->getMessage());
				return redirect()->back()->with('message', 'Kios tidak dapat diaktifkan. Hubungi administrator.');
			}

			if ($cookie === null) {
				return redirect()->back()->withInput()->with('message', 'Kode aktivasi salah, kedaluwarsa, atau sudah dipakai.');
			}

			$response = redirect()->to(base_url('buku-tamu'));
			$response->setCookie(KioskAuth::COOKIE, $cookie, KioskAuth::TOKEN_TTL, '', '/', '', $this->request->isSecure(), true, 'Lax');
			// Modul Peminjaman (CMS) masih membaca cookie ini untuk cek otorisasi lokasi anggota; layanan kios tidak mempercayainya
			$response->setCookie(
				'Location_id',
				(string) $kioskAuth->locationIdFromCookie($cookie),
				KioskAuth::TOKEN_TTL,
				'',
				'/',
				'',
				$this->request->isSecure(),
				true,
				'Lax'
			);

			return $response;
		}

		$this->data['current_location'] = $this->getSelectedLocation();
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		return view('GuestBook\Views\lokasi', $this->data);
	}

	private function locationQuery()
	{
		return $this->db->table('locations as a')
			->select('a.ID, a.Code, a.Name, a.Branch_id')
			->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
			->select('c.Name as Branch_name')
			->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
			->join('branchs as c', 'c.ID = a.Branch_id', 'left');
	}

	/**
	 * Total kunjungan hari ini di ruang ini: pengunjung perorangan + jumlah personel rombongan.
	 */
	private function countTodayVisits(int $locationId): int
	{
		$start = date('Y-m-d 00:00:00');
		$end = date('Y-m-d 00:00:00', strtotime('+1 day'));

		$totalPerorangan = $this->db->table('memberguesses')
			->where('Location_Id', $locationId)
			->where('CreateDate >=', $start)
			->where('CreateDate <', $end)
			->countAllResults();

		$rombongan = $this->db->table('groupguesses')
			->selectSum('CountPersonel', 'total_personel')
			->where('Location_ID', $locationId)
			->where('CreateDate >=', $start)
			->where('CreateDate <', $end)
			->get()
			->getRow();

		return $totalPerorangan + (int) ($rombongan->total_personel ?? 0);
	}

	/**
	 * Kosong dianggap sah (tujuan tidak dipilih). Selain itu harus ID yang ada
	 * dan berlaku untuk jenis pengunjung ini (kolom Member, NonMember, atau Rombongan).
	 */
	private function isValidTujuanKunjungan($value, string $column): bool
	{
		if ($value === null || $value === '') {
			return true;
		}
		if (!is_string($value) || !ctype_digit($value)) {
			return false;
		}

		return $this->db->table('tujuan_kunjungan')
			->where('ID', $value)
			->where($column, 1)
			->countAllResults() > 0;
	}

	/** Field form selalu string; bentuk array (mis. Nama[]=x) membuat validator error */
	private function hasArrayInput(): bool
	{
		return array_filter((array) $this->request->getPost(), 'is_array') !== [];
	}

	private function getSelectedLocation(): ?object
	{
		// Lokasi hanya dipercaya dari cookie kios bertanda tangan
		$locationId = (new KioskAuth($this->db))->locationIdFromCookie($this->request->getCookie(KioskAuth::COOKIE));

		if ($locationId === null) {
			return null;
		}

		return $this->locationQuery()
			->where('a.ID', $locationId)
			->where('a.active', 1)
			->get()
			->getRow();
	}

	public function store_anggota()
	{
		$location = $this->getSelectedLocation();
		if (!$location) {
			return redirect()->to('buku-tamu/lokasi');
		}
		$locationId = $location->ID;

		if ($this->hasArrayInput()) {
			$this->session->setFlashdata('message', 'Terjadi kesalahan validasi.');
			return redirect()->to(base_url('buku-tamu'));
		}

		$this->validation->setRule('member_no', 'Nomor Anggota', 'trim|required|max_length[50]');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$member_no = trim((string) $this->request->getPost('member_no'));
			$member = $this->db->table('members')
				->where('MemberNo', $member_no)
				->get()
				->getRow();

			if (!empty($member)) {
				// 1. Buat array data dengan informasi yang pasti disimpan
				$save_data = [
					'NoAnggota' => $member->MemberNo,
					'Nama' => $member->Fullname,
					'PendidikanTerakhir_id' => $member->EducationLevel_id,
					'Profesi_id' => $member->Job_id,
					'JenisKelamin_id' => $member->Sex_id,
					'Alamat' => $member->Address,
					'Location_id' => $locationId,
					'CreateTerminal' => $this->request->getIPAddress(),
				];

				// 2. Periksa setting dari database
				$SettingBukuTamu = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

				// 3. HANYA JIKA setting = 1, tambahkan 'TujuanKunjungan_id' ke dalam array
				//    (tidak dipilih saat simpan otomatis -> NULL, bukan tujuan pertama)
				if ($SettingBukuTamu == '1') {
					$tujuanKunjungan = $this->request->getPost('TujuanKunjungan_id');
					if (!$this->isValidTujuanKunjungan($tujuanKunjungan, 'Member')) {
						$this->session->setFlashdata('message', 'Tujuan kunjungan tidak valid.');
						return redirect()->to(base_url('buku-tamu'));
					}
					$save_data['TujuanKunjungan_id'] = $tujuanKunjungan ?: null;
				}

				// 4. Simpan array yang sudah final ke database
				$newBukuTamuId = $this->memberguestModel->insert($save_data);

				if ($newBukuTamuId) {
					$this->session->setFlashdata('success', 'Kunjungan berhasil dicatat.');
				} else {
					$this->session->setFlashdata('message', 'Buku Tamu gagal disimpan');
				}

				return redirect()->to(base_url('buku-tamu'));
			} else {
				// Tambahkan pesan jika member tidak ditemukan
				$this->session->setFlashdata('message', 'Nomor Anggota tidak ditemukan.');
				return redirect()->to(base_url('buku-tamu'));
			}
		}

		// Jika validasi gagal atau bukan request POST
		$this->session->setFlashdata('message', 'Terjadi kesalahan validasi.');
		return redirect()->to(base_url('buku-tamu'));
	}
	public function non_anggota($prefix = '')
	{
		$location = $this->getSelectedLocation();
		if (!$location) {
			return redirect()->to('buku-tamu/lokasi');
		}
		$locationId = $location->ID;
		$this->data['SettingBukuTamu'] = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';
		$this->data['title'] = 'Buku Tamu - Non Anggota';
		$this->data['data'] = $location;
		$this->data['totalKunjungan'] = $this->countTodayVisits((int) $locationId);

		// Field bertanda * di form juga wajib di server (tidak hanya atribut required di browser)
		$this->validation->setRules(
			[
				'Nama' => [
					'label' => 'Nama Pengunjung',
					'rules' => 'required|max_length[100]',
					'errors' => [
						'required' => 'Nama Pengunjung tidak boleh kosong',
					],
				],
				'Profesi_id' => [
					'label' => 'Pekerjaan',
					'rules' => 'required|is_natural_no_zero',
					'errors' => ['required' => 'Pekerjaan wajib dipilih', 'is_natural_no_zero' => 'Pekerjaan wajib dipilih'],
				],
				'PendidikanTerakhir_id' => [
					'label' => 'Pendidikan Terakhir',
					'rules' => 'required|is_natural_no_zero',
					'errors' => ['required' => 'Pendidikan Terakhir wajib dipilih', 'is_natural_no_zero' => 'Pendidikan Terakhir wajib dipilih'],
				],
				'JenisKelamin_id' => [
					'label' => 'Jenis Kelamin',
					'rules' => 'required|is_natural_no_zero',
					'errors' => ['required' => 'Jenis Kelamin wajib dipilih', 'is_natural_no_zero' => 'Jenis Kelamin wajib dipilih'],
				],
				'Alamat' => [
					'label' => 'Alamat',
					'rules' => 'permit_empty|max_length[255]',
				],
				'TujuanKunjungan_id' => [
					'label' => 'Tujuan Kunjungan',
					'rules' => 'permit_empty|is_natural_no_zero',
				],
			]
		);

		if ($this->request->getMethod() === 'POST') {
			if ($this->hasArrayInput()) {
				return redirect()->back()->with('message', 'Data yang dikirim tidak valid.');
			}

			// Gagal validasi: kembali ke form dengan isian lama (old()) dan daftar error
			if (!$this->validation->withRequest($this->request)->run()) {
				return redirect()->back()->withInput()->with('message', implode('<br>', array_map('esc', $this->validation->getErrors())));
			}

			$save_data = [
				'Nama' => $this->request->getPost('Nama'),
				'Profesi_id' => $this->request->getPost('Profesi_id'),
				'PendidikanTerakhir_id' => $this->request->getPost('PendidikanTerakhir_id'),
				'JenisKelamin_id' => $this->request->getPost('JenisKelamin_id'),
				'Alamat' => $this->request->getPost('Alamat'),
				'Location_id' => $locationId,
				'CreateTerminal' => $this->request->getIPAddress(),
			];
			// 2. Periksa setting dari database
			$SettingBukuTamu = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

			// 3. HANYA JIKA setting = 1, tambahkan 'TujuanKunjungan_id' ke dalam array
			if ($SettingBukuTamu == '1') {
				$tujuanKunjungan = $this->request->getPost('TujuanKunjungan_id');
				if (!$this->isValidTujuanKunjungan($tujuanKunjungan, 'NonMember')) {
					return redirect()->back()->withInput()->with('message', 'Tujuan kunjungan tidak valid.');
				}
				$save_data['TujuanKunjungan_id'] = $tujuanKunjungan ?: null;
			}

			$newRombonganId = $this->memberguestModel->insert($save_data);
			if ($newRombonganId) {
				$this->session->setFlashdata('success', 'Kunjungan berhasil dicatat.');
			} else {
				$this->session->setFlashdata('message', 'Buku Tamu gagal disimpan');
			}

			return redirect()->to('/buku-tamu/non_anggota');
		}

		$this->data['title'] = 'Buku Tamu - Bukan Anggota';
		$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'nonmember=1', 'data');
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\add_non_anggota', $this->data);
	}

	public function rombongan()
	{
		$location = $this->getSelectedLocation();
		if (!$location) {
			return redirect()->to('buku-tamu/lokasi');
		}
		$locationId = $location->ID;
		$this->data['totalKunjungan'] = $this->countTodayVisits((int) $locationId);
		$this->data['title'] = 'Buku Tamu - Rombongan';
		$this->data['data'] = $location;
		

		// Enhanced validation rules
		$this->validation->setRules([
			'NamaKetua' => [
				'label' => 'Nama Penanggung Jawab',
				'rules' => 'required|min_length[3]|max_length[100]',
				'errors' => [
					'required' => 'Nama Penanggung Jawab tidak boleh kosong',
					'min_length' => 'Nama Penanggung Jawab minimal 3 karakter',
					'max_length' => 'Nama Penanggung Jawab maksimal 100 karakter',
				],
			],
			'NomerTelponKetua' => [
				'label' => 'Nomor Telepon',
				'rules' => 'required|min_length[10]|max_length[15]',
				'errors' => [
					'required' => 'Nomor Telepon tidak boleh kosong',
					'min_length' => 'Nomor Telepon minimal 10 digit',
					'max_length' => 'Nomor Telepon maksimal 15 digit',
				],
			],
			'AsalInstansi' => [
				'label' => 'Nama Instansi/Lembaga',
				'rules' => 'required|min_length[3]|max_length[200]',
				'errors' => [
					'required' => 'Nama Instansi/Lembaga tidak boleh kosong',
					'min_length' => 'Nama Instansi minimal 3 karakter',
					'max_length' => 'Nama Instansi maksimal 200 karakter',
				],
			],
			'CountPersonel' => [
				'label' => 'Total Anggota',
				'rules' => 'required|integer|greater_than[0]',
				'errors' => [
					'required' => 'Total Anggota tidak boleh kosong',
					'integer' => 'Total Anggota harus berupa angka',
					'greater_than' => 'Total Anggota harus lebih dari 0',
				],
			],
			'EmailInstansi' => [
				'label' => 'Email Instansi',
				'rules' => 'permit_empty|valid_email|max_length[100]',
				'errors' => [
					'valid_email' => 'Format Email tidak valid',
					'max_length' => 'Email maksimal 100 karakter',
				],
			],
			'TeleponInstansi' => [
				'label' => 'Telepon Instansi',
				'rules' => 'permit_empty|min_length[8]|max_length[20]',
				'errors' => [
					'min_length' => 'Telepon Instansi minimal 8 digit',
					'max_length' => 'Telepon Instansi maksimal 20 digit',
				],
			],
			'AlamatInstansi' => [
				'label' => 'Alamat Instansi',
				'rules' => 'permit_empty|max_length[255]',
				'errors' => [
					'max_length' => 'Alamat Instansi maksimal 255 karakter',
				],
			],
			'Information' => [
				'label' => 'Keterangan Tambahan',
				'rules' => 'permit_empty|max_length[255]',
				'errors' => [
					'max_length' => 'Keterangan Tambahan maksimal 255 karakter',
				],
			],
			'CountLaki' => [
				'label' => 'Jumlah Laki-laki',
				'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
				'errors' => [
					'integer' => 'Jumlah Laki-laki harus berupa angka',
					'greater_than_equal_to' => 'Jumlah Laki-laki tidak boleh negatif',
				],
			],
			'CountPerempuan' => [
				'label' => 'Jumlah Perempuan',
				'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
				'errors' => [
					'integer' => 'Jumlah Perempuan harus berupa angka',
					'greater_than_equal_to' => 'Jumlah Perempuan tidak boleh negatif',
				],
			],
			'CountTk' => [
				'label' => 'Jumlah TK',
				'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
				'errors' => [
					'integer' => 'Jumlah TK harus berupa angka',
					'greater_than_equal_to' => 'Jumlah TK tidak boleh negatif',
				],
			],
		]);

		if ($this->request->getMethod() === 'POST') {
			if ($this->hasArrayInput()) {
				return redirect()->back()->with('message', 'Data yang dikirim tidak valid.');
			}

			// Gagal validasi: kembali ke form dengan isian lama (old()) dan daftar error
			if (!$this->validation->withRequest($this->request)->run()) {
				return redirect()->back()->withInput()->with('message', implode('<br>', array_map('esc', $this->validation->getErrors())));
			}
			if (!$this->isValidTujuanKunjungan($this->request->getPost('TujuanKunjungan_ID'), 'Rombongan')) {
				return redirect()->back()->withInput()->with('message', 'Tujuan kunjungan tidak valid.');
			}
			try {
				// Generate nomor pengunjung (optional)
				$noPengunjung = date('Ymd') . sprintf('%04d', rand(1, 9999));

				// Prepare save data with all fields
				$save_data = [
					'NamaKetua' => $this->request->getPost('NamaKetua'),
					'NomerTelponKetua' => $this->request->getPost('NomerTelponKetua'),
					'AsalInstansi' => $this->request->getPost('AsalInstansi'),
					// Kolom NOT NULL: field yang tidak terkirim disimpan sebagai string kosong
					'AlamatInstansi' => $this->request->getPost('AlamatInstansi') ?? '',
					'TeleponInstansi' => $this->request->getPost('TeleponInstansi'),
					'EmailInstansi' => $this->request->getPost('EmailInstansi'),
					'CountPersonel' => (int) $this->request->getPost('CountPersonel'),
					'CountLaki' => (int) ($this->request->getPost('CountLaki') ?: 0),
					'CountPerempuan' => (int) ($this->request->getPost('CountPerempuan') ?: 0),

					// Profesi counts
					'CountPNS' => (int) ($this->request->getPost('CountPNS') ?: 0),
					'CountGuru' => (int) ($this->request->getPost('CountGuru') ?: 0),
					'CountPSwasta' => (int) ($this->request->getPost('CountPSwasta') ?: 0),
					'CountPeneliti' => (int) ($this->request->getPost('CountPeneliti') ?: 0),
					'CountDosen' => (int) ($this->request->getPost('CountDosen') ?: 0),
					'CountPensiunan' => (int) ($this->request->getPost('CountPensiunan') ?: 0),
					'CountTNI' => (int) ($this->request->getPost('CountTNI') ?: 0),
					'CountWiraswasta' => (int) ($this->request->getPost('CountWiraswasta') ?: 0),
					'CountPelajar' => (int) ($this->request->getPost('CountPelajar') ?: 0),
					'CountMahasiswa' => (int) ($this->request->getPost('CountMahasiswa') ?: 0),
					'CountLainnya' => (int) ($this->request->getPost('CountLainnya') ?: 0),

					// Education counts
					'CountTk' => (int) ($this->request->getPost('CountTk') ?: 0),
					'CountSD' => (int) ($this->request->getPost('CountSD') ?: 0),
					'CountSMP' => (int) ($this->request->getPost('CountSMP') ?: 0),
					'CountSMA' => (int) ($this->request->getPost('CountSMA') ?: 0),
					'CountD1' => (int) ($this->request->getPost('CountD1') ?: 0),
					'CountD2' => (int) ($this->request->getPost('CountD2') ?: 0),
					'CountD3' => (int) ($this->request->getPost('CountD3') ?: 0),
					'CountS1' => (int) ($this->request->getPost('CountS1') ?: 0),
					'CountS2' => (int) ($this->request->getPost('CountS2') ?: 0),
					'CountS3' => (int) ($this->request->getPost('CountS3') ?: 0),

					// Additional information
					'TujuanKunjungan_ID' => $this->request->getPost('TujuanKunjungan_ID') ?: null,
					'Information' => $this->request->getPost('Information'),
					'NoPengunjung' => $noPengunjung,

					// System fields
					'Location_ID' => $locationId,
					'CreateDate' => date('Y-m-d H:i:s'),
					'CreateTerminal' => $this->request->getIPAddress(),
				];

				// Additional validation logic
				$totalPersonel = $save_data['CountPersonel'];

				// Check gender total
				$genderTotal = $save_data['CountLaki'] + $save_data['CountPerempuan'];
				if ($genderTotal > $totalPersonel) {
					$this->session->setFlashdata('message', 'Total laki-laki + perempuan (' . $genderTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
					return redirect()->back()->withInput();
				}

				// Check profession total
				$professionTotal = $save_data['CountPNS'] + $save_data['CountGuru'] + $save_data['CountPSwasta'] +
					$save_data['CountPeneliti'] + $save_data['CountDosen'] + $save_data['CountPensiunan'] +
					$save_data['CountTNI'] + $save_data['CountWiraswasta'] + $save_data['CountPelajar'] +
					$save_data['CountMahasiswa'] + $save_data['CountLainnya'];

				if ($professionTotal > $totalPersonel) {
					$this->session->setFlashdata('message', 'Total berdasarkan profesi (' . $professionTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
					return redirect()->back()->withInput();
				}

				// Check education total
				$educationTotal = $save_data['CountTk'] + $save_data['CountSD'] + $save_data['CountSMP'] + $save_data['CountSMA'] +
					$save_data['CountD1'] + $save_data['CountD2'] + $save_data['CountD3'] +
					$save_data['CountS1'] + $save_data['CountS2'] + $save_data['CountS3'];

				if ($educationTotal > $totalPersonel) {
					$this->session->setFlashdata('message', 'Total berdasarkan pendidikan (' . $educationTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
					return redirect()->back()->withInput();
				}

				// Save to database
				$newRombonganId = $this->groupguestModel->insert($save_data);

				if ($newRombonganId) {
					// Create detailed success message
					$successMsg = 'Kunjungan rombongan berhasil dicatat! ';
					$successMsg .= 'Nama: ' . $save_data['NamaKetua'] . ', ';
					$successMsg .= 'Instansi: ' . $save_data['AsalInstansi'] . ', ';
					$successMsg .= 'Total Anggota: ' . $save_data['CountPersonel'] . ' orang. ';
					$successMsg .= 'No. Pengunjung: ' . $noPengunjung;

					$this->session->setFlashdata('success', $successMsg);

					// Log the activity
					log_message('info', 'Group guest book saved: ' . json_encode([
						'id' => $newRombonganId,
						'nama_ketua' => $save_data['NamaKetua'],
						'asal_instansi' => $save_data['AsalInstansi'],
						'total_personel' => $save_data['CountPersonel'],
						'no_pengunjung' => $noPengunjung,
						'location_id' => $locationId
					]));
				} else {
					$this->session->setFlashdata('message', 'Buku Tamu Rombongan gagal disimpan. Silakan coba lagi.');
					return redirect()->back()->withInput();
				}

			} catch (\Exception $e) {
				// Log the error
				log_message('error', 'Error saving group guest book: ' . $e->getMessage());

				$this->session->setFlashdata('message', 'Terjadi kesalahan sistem. Silakan hubungi administrator.');
				return redirect()->back()->withInput();
			}

			return redirect()->to('/buku-tamu/rombongan');
		}

		// Prepare data for view
		$this->data['title'] = 'Buku Tamu - Rombongan';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');

		// Get reference data for dropdowns
		//$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'active=1', 'data');
		$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'rombongan=1', 'data');

		echo view('GuestBook\Views\add_rombongan', $this->data);
	}

}
