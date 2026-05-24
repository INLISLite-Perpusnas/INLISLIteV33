<?php

namespace GuestBook\Controllers;

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
		 $this->db=\Config\Database::connect('data');
		 $this->validation = \Config\Services::validation();
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->groupguestModel  =  new \BukuTamu\Models\GroupGuestModel();
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

		$this->data['SettingBukuTamu'] = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';
		
		// Get member number from request — trim to handle barcode scanner spaces
		$member_no = trim($this->request->getGet('member_no') ?? '');

		// Get member data from database
		$member = $member_no ? $this->db->table('members')->where('MemberNo', $member_no)->get()->getRow() : null;

		// Get location id from cookie
		$locationId = $this->request->getCookie('Location_id');
		// Check if location id is available
		if (!$locationId) {
			return redirect()->to('buku-tamu/lokasi');
		}
	    $today = date('Y-m-d');
		$builderAnggota = $this->db->table('memberguesses');
        $builderAnggota->where('DATE(CreateDate)', $today);
		 $totalAnggota = $builderAnggota->countAllResults();
		$builderRombongan = $this->db->table('groupguesses');
        $builderRombongan->selectSum('CountPersonel', 'total_personel');
        $builderRombongan->where('DATE(CreateDate)', $today);
		$resultRombongan = $builderRombongan->get()->getRow();
        $totalRombongan = (int)($resultRombongan->total_personel ?? 0);
		$totalKunjungan = $totalAnggota + $totalRombongan;

		$this->data['totalKunjungan'] = $totalKunjungan;


		// Query for location data
		$builder = $this->db->table('locations as a')
			->select('a.ID, a.Code, a.Name')
			->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
			->select('a.Branch_id, c.Name as Branch_name')
			->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
			->join('branchs as c', 'c.ID = a.Branch_id', 'left')
			->where('a.ID', $locationId);

		// Get location data
		$data = $builder->get()->getRow();

		

		// Set data for view
		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['data'] = $data;
		$this->data['member'] = $member;
		$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'Member=1', 'data');
		$this->data['message'] = $this->validation->getErrors()
			? $this->validation->listErrors()
			: $this->session->getFlashdata('message');

		// Render view
		return view('GuestBook\Views\add', $this->data);
	}


	public function lokasi()
	{
		$this->data['title'] = 'Setting- Lokasi';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\lokasi', $this->data);
	}

	public function store_anggota()
{
    $locationId = $this->request->getCookie('Location_id');
    if (!$locationId) {
        return redirect()->to('buku-tamu');
    }

    $this->validation->setRule('member_no', 'Nomor Anggota', 'trim|required');

    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
        $member_no = $this->request->getPost('member_no');
        $member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
        
        if (!empty($member)) {
            // 1. Buat array data dengan informasi yang pasti disimpan
            $save_data = [
                'NoAnggota'             => $member->MemberNo,
                'Nama'                  => $member->Fullname,
                'PendidikanTerakhir_id' => $member->EducationLevel_id,
                'Profesi_id'            => $member->Job_id,
                'Alamat'                => $member->Address,
                'Location_id'           => $locationId,
                'Branch_id'             => $member->Branch_id,
            ];

            // 2. Periksa setting dari database
            $SettingBukuTamu = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

            // 3. HANYA JIKA setting = 1, tambahkan 'TujuanKunjungan_id' ke dalam array
            if ($SettingBukuTamu == '1') {
                $save_data['TujuanKunjungan_id'] = $this->request->getPost('TujuanKunjungan_id');
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
		$locationId = $this->request->getCookie('Location_id');
		// Check if location id is available
		if (!$locationId) {
			return redirect()->to('buku-tamu/lokasi');
		}
		$this->data['SettingBukuTamu'] = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';
		$builder = $this->db->table('locations as a')
			->select('a.ID, a.Code, a.Name')
			->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
			->select('a.Branch_id, c.Name as Branch_name')
			->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
			->join('branchs as c', 'c.ID = a.Branch_id', 'left')
			->where('a.ID', $locationId);

		// Get location data
		$data = $builder->get()->getRow();
		$this->data['title'] = 'Buku Tamu - Non Anggota';
		$this->data['data'] = $data;
		$branch_id = $data->Branch_id;

		 $today = date('Y-m-d');
		$builderAnggota = $this->db->table('memberguesses');
        $builderAnggota->where('DATE(CreateDate)', $today);
		 $totalAnggota = $builderAnggota->countAllResults();
		$builderRombongan = $this->db->table('groupguesses');
        $builderRombongan->selectSum('CountPersonel', 'total_personel');
        $builderRombongan->where('DATE(CreateDate)', $today);
		$resultRombongan = $builderRombongan->get()->getRow();
        $totalRombongan = (int)($resultRombongan->total_personel ?? 0);
		$totalKunjungan = $totalAnggota + $totalRombongan;
		$this->data['totalKunjungan'] = $totalKunjungan;


		$this->validation->setRules(
			[
				'Nama' => [
					'label'  => 'Nama Pengunjung',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Nama Pengunjung tidak boleh kosong',
					],
				],
			]
		);

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
		
			$save_data = [
				'Nama' => $this->request->getPost('Nama'),
				'Profesi_id' => $this->request->getPost('Profesi_id'),
				'PendidikanTerakhir_id' => $this->request->getPost('PendidikanTerakhir_id'),
				'JenisKelamin_id' => $this->request->getPost('JenisKelamin_id'),
				'Alamat' => $this->request->getPost('Alamat'),
				'Location_id' => $locationId,
				'Branch_id' => $branch_id,
				'CreateBy' => user_id(),
			];
             // 2. Periksa setting dari database
            $SettingBukuTamu = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

            // 3. HANYA JIKA setting = 1, tambahkan 'TujuanKunjungan_id' ke dalam array
            if ($SettingBukuTamu == '1') {
                $save_data['TujuanKunjungan_id'] = $this->request->getPost('TujuanKunjungan_id');
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
	$locationId = $this->request->getCookie('Location_id');
	// Check if location id is available
	if (!$locationId) {
		return redirect()->to('buku-tamu/lokasi');
	}
	 $today = date('Y-m-d');
		$builderAnggota = $this->db->table('memberguesses');
        $builderAnggota->where('DATE(CreateDate)', $today);
		 $totalAnggota = $builderAnggota->countAllResults();
		$builderRombongan = $this->db->table('groupguesses');
        $builderRombongan->selectSum('CountPersonel', 'total_personel');
        $builderRombongan->where('DATE(CreateDate)', $today);
		$resultRombongan = $builderRombongan->get()->getRow();
        $totalRombongan = (int)($resultRombongan->total_personel ?? 0);
		$totalKunjungan = $totalAnggota + $totalRombongan;
		$this->data['totalKunjungan'] = $totalKunjungan;
	$builder = $this->db->table('locations as a')
		->select('a.ID, a.Code, a.Name')
		->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
		->select('a.Branch_id, c.Name as Branch_name')
		->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
		->join('branchs as c', 'c.ID = a.Branch_id', 'left')
		->where('a.ID', $locationId);

	// Get location data
	$data = $builder->get()->getRow();
	$this->data['title'] = 'Buku Tamu - Rombongan';
	$this->data['data'] = $data;
	$branch_id = $data->Branch_id;
	
	// Enhanced validation rules
	$this->validation->setRules([
		'NamaKetua' => [
			'label'  => 'Nama Penanggung Jawab',
			'rules'  => 'required|min_length[3]|max_length[100]',
			'errors' => [
				'required' => 'Nama Penanggung Jawab tidak boleh kosong',
				'min_length' => 'Nama Penanggung Jawab minimal 3 karakter',
				'max_length' => 'Nama Penanggung Jawab maksimal 100 karakter',
			],
		],
		'NomerTelponKetua' => [
			'label'  => 'Nomor Telepon',
			'rules'  => 'required|min_length[10]|max_length[15]',
			'errors' => [
				'required' => 'Nomor Telepon tidak boleh kosong',
				'min_length' => 'Nomor Telepon minimal 10 digit',
				'max_length' => 'Nomor Telepon maksimal 15 digit',
			],
		],
		'AsalInstansi' => [
			'label'  => 'Nama Instansi/Lembaga',
			'rules'  => 'required|min_length[3]|max_length[200]',
			'errors' => [
				'required' => 'Nama Instansi/Lembaga tidak boleh kosong',
				'min_length' => 'Nama Instansi minimal 3 karakter',
				'max_length' => 'Nama Instansi maksimal 200 karakter',
			],
		],
		'CountPersonel' => [
			'label'  => 'Total Anggota',
			'rules'  => 'required|integer|greater_than[0]',
			'errors' => [
				'required' => 'Total Anggota tidak boleh kosong',
				'integer' => 'Total Anggota harus berupa angka',
				'greater_than' => 'Total Anggota harus lebih dari 0',
			],
		],
		'EmailInstansi' => [
			'label'  => 'Email Instansi',
			'rules'  => 'permit_empty|valid_email|max_length[100]',
			'errors' => [
				'valid_email' => 'Format Email tidak valid',
				'max_length' => 'Email maksimal 100 karakter',
			],
		],
		'TeleponInstansi' => [
			'label'  => 'Telepon Instansi',
			'rules'  => 'permit_empty|min_length[8]|max_length[20]',
			'errors' => [
				'min_length' => 'Telepon Instansi minimal 8 digit',
				'max_length' => 'Telepon Instansi maksimal 20 digit',
			],
		],
		'CountLaki' => [
			'label'  => 'Jumlah Laki-laki',
			'rules'  => 'permit_empty|integer|greater_than_equal_to[0]',
			'errors' => [
				'integer' => 'Jumlah Laki-laki harus berupa angka',
				'greater_than_equal_to' => 'Jumlah Laki-laki tidak boleh negatif',
			],
		],
		'CountPerempuan' => [
			'label'  => 'Jumlah Perempuan',
			'rules'  => 'permit_empty|integer|greater_than_equal_to[0]',
			'errors' => [
				'integer' => 'Jumlah Perempuan harus berupa angka',
				'greater_than_equal_to' => 'Jumlah Perempuan tidak boleh negatif',
			],
		],
	]);

	if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
		try {
			// Generate nomor pengunjung (optional)
			$noPengunjung = date('Ymd') . sprintf('%04d', rand(1, 9999));
			
			// Prepare save data with all fields
			$save_data = [
				'NamaKetua' => $this->request->getPost('NamaKetua'),
				'NomerTelponKetua' => $this->request->getPost('NomerTelponKetua'),
				'AsalInstansi' => $this->request->getPost('AsalInstansi'),
				'AlamatInstansi' => $this->request->getPost('AlamatInstansi'),
				'TeleponInstansi' => $this->request->getPost('TeleponInstansi'),
				'EmailInstansi' => $this->request->getPost('EmailInstansi'),
				'CountPersonel' => (int)$this->request->getPost('CountPersonel'),
				'CountLaki' => (int)($this->request->getPost('CountLaki') ?: 0),
				'CountPerempuan' => (int)($this->request->getPost('CountPerempuan') ?: 0),
				
				// Profesi counts
				'CountPNS' => (int)($this->request->getPost('CountPNS') ?: 0),
				'CountGuru' => (int)($this->request->getPost('CountGuru') ?: 0),
				'CountPSwasta' => (int)($this->request->getPost('CountPSwasta') ?: 0),
				'CountPeneliti' => (int)($this->request->getPost('CountPeneliti') ?: 0),
				'CountDosen' => (int)($this->request->getPost('CountDosen') ?: 0),
				'CountPensiunan' => (int)($this->request->getPost('CountPensiunan') ?: 0),
				'CountTNI' => (int)($this->request->getPost('CountTNI') ?: 0),
				'CountWiraswasta' => (int)($this->request->getPost('CountWiraswasta') ?: 0),
				'CountPelajar' => (int)($this->request->getPost('CountPelajar') ?: 0),
				'CountMahasiswa' => (int)($this->request->getPost('CountMahasiswa') ?: 0),
				'CountLainnya' => (int)($this->request->getPost('CountLainnya') ?: 0),
				
				// Education counts
				'CountSD' => (int)($this->request->getPost('CountSD') ?: 0),
				'CountSMP' => (int)($this->request->getPost('CountSMP') ?: 0),
				'CountSMA' => (int)($this->request->getPost('CountSMA') ?: 0),
				'CountD1' => (int)($this->request->getPost('CountD1') ?: 0),
				'CountD2' => (int)($this->request->getPost('CountD2') ?: 0),
				'CountD3' => (int)($this->request->getPost('CountD3') ?: 0),
				'CountS1' => (int)($this->request->getPost('CountS1') ?: 0),
				'CountS2' => (int)($this->request->getPost('CountS2') ?: 0),
				'CountS3' => (int)($this->request->getPost('CountS3') ?: 0),
				
				// Additional information
				'TujuanKunjungan_ID' => $this->request->getPost('TujuanKunjungan_ID') ?: null,
				'Information' => $this->request->getPost('Information'),
				'NoPengunjung' => $noPengunjung,
				
				// System fields
				'Location_ID' => $locationId,
				'Branch_id' => $branch_id,
				'CreateBy' => user_id(),
				'CreateDate' => date('Y-m-d H:i:s'),
				'CreateTerminal' => $this->request->getIPAddress(),
			];
       
			// Additional validation logic
			$totalPersonel = $save_data['CountPersonel'];
			
			// Check gender total
			$genderTotal = $save_data['CountLaki'] + $save_data['CountPerempuan'];
			if ($genderTotal > $totalPersonel) {
				$this->session->setFlashdata('error', 'Total laki-laki + perempuan (' . $genderTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
				return redirect()->back()->withInput();
			}
			
			// Check profession total
			$professionTotal = $save_data['CountPNS'] + $save_data['CountGuru'] + $save_data['CountPSwasta'] + 
							  $save_data['CountPeneliti'] + $save_data['CountDosen'] + $save_data['CountPensiunan'] + 
							  $save_data['CountTNI'] + $save_data['CountWiraswasta'] + $save_data['CountPelajar'] + 
							  $save_data['CountMahasiswa'] + $save_data['CountLainnya'];
			
			if ($professionTotal > $totalPersonel) {
				$this->session->setFlashdata('error', 'Total berdasarkan profesi (' . $professionTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
				return redirect()->back()->withInput();
			}
			
			// Check education total
			$educationTotal = $save_data['CountSD'] + $save_data['CountSMP'] + $save_data['CountSMA'] + 
							 $save_data['CountD1'] + $save_data['CountD2'] + $save_data['CountD3'] + 
							 $save_data['CountS1'] + $save_data['CountS2'] + $save_data['CountS3'];
			
			if ($educationTotal > $totalPersonel) {
				$this->session->setFlashdata('error', 'Total berdasarkan pendidikan (' . $educationTotal . ') tidak boleh melebihi total anggota (' . $totalPersonel . ')');
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
					'location_id' => $locationId,
					'created_by' => user_id()
				]));
			} else {
				$this->session->setFlashdata('error', 'Buku Tamu Rombongan gagal disimpan. Silakan coba lagi.');
			}

		} catch (\Exception $e) {
			// Log the error
			log_message('error', 'Error saving group guest book: ' . $e->getMessage());
			
			$this->session->setFlashdata('error', 'Terjadi kesalahan sistem. Silakan hubungi administrator.');
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

	public function getKnownFaces($prefix = '')
{
	$db = \Config\Database::connect();
	
    $query = $db->query("SELECT id, name, PhotoUrl FROM members");
    $results = $query->getResult();

    $knownFaces = [];
    foreach ($results as $row) {
        $photoPath = FCPATH . 'uploads/anggota/' . $row->PhotoUrl;
        if (file_exists($photoPath)) {
            $knownFaces[] = [
                'id' => $row->id,
                'name' => $row->name,
                'photoUrl' => base_url('uploads/anggota/' . $row->PhotoUrl)
            ];
        }
    }

    return $this->response->setJSON($knownFaces);
}
}
