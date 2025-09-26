<?php

namespace Anggota\Controllers;
// namespace hamkamannan\adminigniter\Modules\Core\Parameter\Controllers;

use Base\Models\BaseModel;
use Base\Models\DataModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \CodeIgniter\Files\File;
use chillerlan\QRCode\{QRCode, QROptions};
use Dompdf\Dompdf;
use Dompdf\Options;

class Anggota extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $anggotaModel;
	public $uploadPath;
	public $lokasiperpustakaanModel;
	public $jenisanggotaModel;
	public $anggotahakaksesModel;
	public $AksesKoleksiModel;
	public $modulePath;
	public $pengaturananggotaModel;
	public $templateKartuModel;
	public $kartuanggotaModel;
	public $regionModel;
	public $settingModel;
	function __construct()
	{
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		$this->lokasiperpustakaanModel = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
		$this->anggotahakaksesModel = new \Anggota\Models\Anggotahakakses();
		$this->AksesKoleksiModel = new \Anggota\Models\Hak_akses_koleksi();
		$this->regionModel = new \Region\Models\RegionModel();
		$this->jenisanggotaModel = new \JenisAnggota\Models\JenisAnggotaModel();
		$this->templateKartuModel = new \KartuAnggota\Models\KartuAnggotaModel();
		$this->kartuanggotaModel = new \KartuAnggota\Models\KartuAnggotaModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/anggota/';
		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}
		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();

		helper('adminigniter');
		helper('reference');
		helper('region');
		helper('anggota');
		helper('date_id_helper');
		helper('url');
		helper('thumbnail');
		helper('sirkulasi');
	}
	public function online()
	{
		// $this->data['title'] = 'Keanggotaan Online';

		$slug = $this->request->getGet('slug');

		// Fallback ke "profile" kalau kosong
		if (empty($slug)) {
			$slug = 'profile';
		}

		// Simpan ke array data
		$this->data['slug'] = $slug;
		
		$db = db_connect();

		$jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";
		$member_no = user()->username;
		// $member = get_ref_single('members', 'memberNo="' . $member_no . '"', 'data');
		$member = get_member($member_no);
		$this->data['member_no'] = $member_no;
		$this->data['member'] = $member;
		if ($jenisperpustakaan == "UMUM") {
			$this->data['jenis_perpustakaan_id'] = 1;
		} elseif ($jenisperpustakaan == "KHUSUS") {
			$this->data['jenis_perpustakaan_id'] = 2;
		} elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
			$this->data['jenis_perpustakaan_id'] = 3;
		} else {
			$this->data['jenis_perpustakaan_id'] = 4;
		}
		$hak_akses_koleksi = $this->AksesKoleksiModel->where('member_id', $member->ID)->findAll();

		$arr_hak_akses_koleksi = [];
		foreach ($hak_akses_koleksi as $row) {
			$tmp_row = $row->CategoryLoan_id;
			array_push($arr_hak_akses_koleksi, $tmp_row);
		}
		$this->data['arr_hak_akses_koleksi'] = $arr_hak_akses_koleksi;
		$hak_akses_lokasi = $this->anggotahakaksesModel->where('Member_id', $member->ID)->findAll();
		$arr_hak_akses_lokasi = [];

		foreach ($hak_akses_lokasi as $row) {
			$temp_row = $row->LocationLoan_id;
			array_push($arr_hak_akses_lokasi, $temp_row);
		}
		$this->data['arr_hak_akses_lokasi'] = $arr_hak_akses_lokasi;
		$peminjaman = get_peminjaman($member->ID);
		$this->data['peminjaman'] = $peminjaman;
		return view('Anggota\Views\online\index', $this->data);
	}

	public function extend($member_no = null)
	{
		if (empty($member_no)) {
			$member_no = user()->username;
		}
		$member = get_member($member_no);

		$jenis_anggota = db_get_single('m_jenis_anggota', 'id = ' . $member->ref_jenisanggota);
		$start_date = $member->EndDate;
		$end_date = date('Y-m-d', strtotime($start_date . ' + ' . $jenis_anggota->expiry_days . ' days'));

		$updateAnggota = $this->anggotaModel->protect(false)->update($member->id, array('EndDate' => $end_date));

		if ($updateAnggota) {
			set_message('toastr_msg', 'Perpanjangan Masa Berlaku Anggota berhasil');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Perpanjangan Masa Berlaku Anggota gagal');
			set_message('toastr_type', 'error');
		}
		return redirect()->back();
	}

	public function index()
	{
		// is_allowed('anggota/index');
		if (!is_allowed('anggota/index')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('dashboard');
		}
		$this->data['title'] = ' Anggota';
		$this->data['message'] = $this->validation->getErrors()
			? $this->validation->listErrors()
			: $this->session->getFlashdata('message');
		echo view('Anggota\Views\list', $this->data);
	}

	public function keranjang()
	{
		$this->data['title'] = 'Anggota - Keranjang';
		$this->data['message'] = $this->validation->getErrors()
			? $this->validation->listErrors()
			: $this->session->getFlashdata('message');
		echo view('Anggota\Views\list_keranjang', $this->data);
	}


	public function create()
	{
		if (!is_allowed('anggota/create')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('anggota');
		}

		$db = db_connect();
		$this->data['db'] = $db;
		$jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";

		if ($jenisperpustakaan == "UMUM") {
			$this->data['jenis_perpustakaan_id'] = 1;
		} elseif ($jenisperpustakaan == "KHUSUS") {
			$this->data['jenis_perpustakaan_id'] = 2;
		} elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
			$this->data['jenis_perpustakaan_id'] = 3;
		} else {
			$this->data['jenis_perpustakaan_id'] = 4;
		}

		$TipeNomorAnggota = $db->table('settingparameters')->where('Name', 'TipeNomorAnggota')->get()->getRow()->Value ?: "Manual";
		$identityNo = $this->request->getPost('IdentityNo');
		if ($TipeNomorAnggota == "Otomatis") {
			// UBAH BAGIAN INI - Generate MemberNo berdasarkan setting
			$this->data['TipeNomorAnggota'] = "Otomatis";
			$MemberNo = generateMemberNumber($identityNo);
		} else {
			$this->data['TipeNomorAnggota'] = "Manual";
			$MemberNo = $this->request->getPost('MemberNo');
		}



		$jenis_anggota = get_ref_single('jenis_anggota', 'UPPER(jenisanggota) = "UMUM"', 'data');
		$masa_berlaku = $jenis_anggota->MasaBerlakuAnggota ?? 365;
		$start = date('Y-m-d');
		$start_date = new \DateTime($start);
		$end = new \DateTime($start);
		$end_date = $end->add(new \DateInterval('P' . $masa_berlaku . 'D'));
		$this->data['date'] = date_format($start_date, "Y-m-d");
		// $this->data['EndDate'] = date_format($end_date, "Y-m-d H:i:s");



		$this->data['title'] = 'Tambah Anggota';

		// Validation rules sama seperti sebelumnya...
		$this->validation->setRules([
			'Fullname' => [
				'label'  => 'Fullname',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Nama Tidak boleh kosong',
				],
			],
			'Email' => [
				'label'  => 'Email',
				'rules'  => 'required|valid_email|is_unique[users.Email]',
				'errors' => [
					'valid_email' => 'Masukan email yang benar',
					'required'    => 'Email Tidak boleh Kosong',
					'is_unique'   => 'Email ini sudah terdaftar.',
				],
			],
			'JenisAnggota_id' => [
				'label'  => 'Jenis Anggota',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Jenis Anggota tidak boleh kosong',
				],
			],
			'StatusAnggota_id' => [
				'label'  => 'Status Anggota',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Status Anggota tidak boleh kosong',
				],
			],
		]);

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

			// PASTIKAN MemberNo unik
			$db = db_connect();
			$existingMember = $db->table('members')->where('MemberNo', $MemberNo)->get()->getRow();

			// Jika MemberNo sudah ada, generate ulang
			while ($existingMember) {
				$MemberNo = generateMemberNumber($identityNo);
				$existingMember = $db->table('members')->where('MemberNo', $MemberNo)->get()->getRow();
			}

			$save_data = [
				'Fullname' => $this->request->getPost('Fullname'),
				'MemberNo' => $MemberNo, // Gunakan MemberNo yang sudah di-generate
				'IdentityNo' => $this->request->getPost('IdentityNo'),
				// ... field lainnya sama seperti sebelumnya
				'PlaceOfBirth' => $this->request->getPost('PlaceOfBirth'),
				'DateOfBirth' =>  $this->request->getPost('DateOfBirth'),
				'Address' => $this->request->getPost('Address'),
				'AddressNow' => $this->request->getPost('AddressNow'),
				'Phone' => $this->request->getPost('Phone'),
				'InstitutionName' => $this->request->getPost('InstitutionName'),
				'InstitutionAddress' => $this->request->getPost('InstitutionAddress'),
				'InstitutionPhone' => $this->request->getPost('InstitutionPhone'),
				'MotherMaidenName' => $this->request->getPost('MotherMaidenName'),
				'Email' => $this->request->getPost('Email'),
				'RT' => $this->request->getPost('RT'),
				'RTNow' => $this->request->getPost('RTNow'),
				'RWNow' => $this->request->getPost('RWNow'),
				'RW' => $this->request->getPost('RW'),
				'TahunAjaran' => $this->request->getPost('TahunAjaran'),
				'IdentityType_id' => $this->request->getPost('IdentityType_id'),
				'MaritalStatus_id' => $this->request->getPost('MaritalStatus_id'),
				'Sex_id' 	=> $this->request->getPost('Sex_id'),
				'JenjangPendidikan_id' 	=> $this->request->getPost('JenjangPendidikan_id'),
				'Job_id' 	=> $this->request->getPost('Job_id'),
				'JenisAnggota_id' 	=> $this->request->getPost('JenisAnggota_id'),
				'Agama_id' 	=> $this->request->getPost('Agama_id'),
				'UnitKerja_id' 	=> $this->request->getPost('UnitKerja_id'),
				'Fakultas_id' 	=> $this->request->getPost('Fakultas_id'),
				'Jurusan_id' 	=> $this->request->getPost('Jurusan_id'),
				'IsKeranjang'  => 0,
				'StatusAnggota_id' => $this->request->getPost('StatusAnggota_id'),
				'RegisterDate' => date("Y-m-d H:i:s"),
				'EndDate' => $this->request->getPost('EndDate'),
				'CreateBy' => login_id(),
				'Branch_id' => branch_id()
			];
			// dd($save_data);

			$province = $this->request->getPost('Province');
			if (!empty($province)) {
				$region = $this->regionModel->where('code', $province)->first();
				$save_data['Province'] = $region->name;
			}

			$city = $this->request->getPost('City');
			if (!empty($city)) {
				$region = $this->regionModel->where('code', $city)->first();
				$save_data['City'] = $region->name;
			}

			$kecamatan = $this->request->getPost('Kecamatan');
			if (!empty($kecamatan)) {
				$region = $this->regionModel->where('code', $kecamatan)->first();
				$save_data['Kecamatan'] = $region->name;
			}

			$kelurahan = $this->request->getPost('Kelurahan');
			if (!empty($kelurahan)) {
				$region = $this->regionModel->where('code', $kelurahan)->first();
				$save_data['Kelurahan'] = $region->name;
			}

			$provinceNow = $this->request->getPost('ProvinceNow');
			if (!empty($provinceNow)) {
				$region = $this->regionModel->where('code', $provinceNow)->first();
				$save_data['ProvinceNow'] = $region->name;
			}

			$cityNow = $this->request->getPost('CityNow');
			if (!empty($cityNow)) {
				$region = $this->regionModel->where('code', $cityNow)->first();
				$save_data['CityNow'] = $region->name;
			}

			$kecamatanNow = $this->request->getPost('KecamatanNow');
			if (!empty($kecamatanNow)) {
				$region = $this->regionModel->where('code', $kecamatanNow)->first();
				$save_data['KecamatanNow'] = $region->name;
			}

			$kelurahanNow = $this->request->getPost('KelurahanNow');
			if (!empty($kelurahanNow)) {
				$region = $this->regionModel->where('code', $kelurahanNow)->first();
				$save_data['KelurahanNow'] = $region->name;
			}
			

			// Logic Upload
			$files = (array) $this->request->getPost('PhotoUrl');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;
					}
				}
				$save_data['PhotoUrl'] = implode(',', $listed_file);
			}

			$base64_string = $this->request->getPost('camera_image');
			if (!empty($base64_string)) {
				$file = new File($this->uploadPath);
				$newFileName = $file->getRandomName() . '.jpg';
				base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
				$save_data['PhotoUrl'] =  $newFileName;
			}

			// simpan data ke tabel members
			$newAnggotaId = $this->anggotaModel->protect(false)->insert($save_data);



			if ($newAnggotaId) {
				//    simpan data ke akses jenis buku
				$Koleksi = $this->request->getPost('CategoryLoan_id');
				if (!empty($Koleksi)) {
					$Count = count($Koleksi);

					$save_akses_koleksi_temp = [];
					$save_akses_koleksi = [];
					for ($x = 0; $x < $Count; $x++) {
						$save_akses_koleksi_temp = [
							'Member_id' => $newAnggotaId,
							'CategoryLoan_id' => $Koleksi[$x],
						];
						array_push($save_akses_koleksi, $save_akses_koleksi_temp);
					}

					if (!empty($save_akses_koleksi)) {
						$this->AksesKoleksiModel->insertBatch($save_akses_koleksi);
					}
				}

				//    simpan data ke akses lokasi peprustakaan
				$Locations = $this->request->getPost('LocationLoan_id');
				$CountLocation = count($Locations);
				$save_akses_lokasi_temp = [];
				$save_akses_lokasi = [];
				for ($x = 0; $x < $CountLocation; $x++) {
					$save_akses_lokasi_temp = [
						'Member_id' => $newAnggotaId,
						'LocationLoan_id' => $Locations[$x],
					];
					array_push($save_akses_lokasi, $save_akses_lokasi_temp);
				}

				if (!empty($save_akses_lokasi)) {
					$this->anggotahakaksesModel->insertBatch($save_akses_lokasi);
				}
				//  simpan data ke tabel users
				// $email = $this->request->getPost('Email');
				$password = get_parameter('password-default', 'inlislite=');
				$activate_hash = bin2hex(random_bytes(16));
				$db = db_connect('default');
				$users = $db->table('users');

				$data_user = [
					'username' => $MemberNo,
					'password_hash' => $password,
					'anggota' => $newAnggotaId,
					'email' => $save_data['Email'],
					'activate_hash' => $activate_hash,
					'active' => 0
				];

				$users->insert($data_user);

				set_message('toastr_msg', "Data Anggota berhasil disimpan dengan Nomor Anggota: {$MemberNo}");
				set_message('toastr_type', 'success');
				return redirect()->to('/anggota');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Anggota.info.failed_saved'));
				echo view('Anggota\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('anggota/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Anggota\Views\add', $this->data);
		}
	}

	public function camera()
	{
		// $files = (array) $this->request->getPost('file_image');
		$filename = 'pic_' . date('YmdHis') . '.jpeg';
		$url = '';
		if (move_uploaded_file($_FILES['file_image']['tmp_name'], 'upload/' . $filename)) {
			$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/upload/' . $filename;
		}
		// Return image url
		echo $url;
	}

	public function profile()
	{
		$member_no = user()->username;
		$member = get_ref_single('members', 'memberNo="' . $member_no . '"', 'data');

		$this->edit($member->ID, true);
	}

	public function edit(int $ID = null, $is_anggota = false)
	{
		if (!is_allowed('anggota/edit')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('anggota');
		}
		$db = db_connect();
		$jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";

		if ($jenisperpustakaan == "UMUM") {
			$this->data['jenis_perpustakaan_id'] = 1;
		} elseif ($jenisperpustakaan == "KHUSUS") {
			$this->data['jenis_perpustakaan_id'] = 2;
		} elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
			$this->data['jenis_perpustakaan_id'] = 3;
		} else {
			$this->data['jenis_perpustakaan_id'] = 4;
		}
		$member_id = $ID;
		$MemberNo = $this->request->getPost('IdentityNo');
		$hak_akses_koleksi = $this->AksesKoleksiModel->where('member_id', $member_id)->findAll();

		$arr_hak_akses_koleksi = [];
		foreach ($hak_akses_koleksi as $row) {
			$tmp_row = $row->CategoryLoan_id;
			array_push($arr_hak_akses_koleksi, $tmp_row);
		}

		$hak_akses_lokasi = $this->anggotahakaksesModel->where('Member_id', $member_id)->findAll();
		$arr_hak_akses_lokasi = [];

		foreach ($hak_akses_lokasi as $row) {
			$temp_row = $row->LocationLoan_id;
			array_push($arr_hak_akses_lokasi, $temp_row);
		}

		$anggota = $this->anggotaModel->find($ID);
    $CreateBy = get_username($anggota->CreateBy ?? 0);
		$UpdateBy = get_username($anggota->UpdateBy ?? 0);
		$this->data['title'] = 'Ubah Anggota';
		$this->data['anggota'] = $anggota;
		$this->data['CreateBy'] = $CreateBy;
		$this->data['UpdateBy'] = $UpdateBy;
		$this->data['hak_akses_koleksi'] = $hak_akses_koleksi;
		$this->data['arr_hak_akses_koleksi'] = $arr_hak_akses_koleksi;
		$this->data['hak_akses_lokasi'] = $hak_akses_lokasi;
		$this->data['arr_hak_akses_lokasi'] = $arr_hak_akses_lokasi;

		$this->validation->setRules([
			'Fullname' => [
				'label'  => 'Fullname',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Nama Tidak boleh kosong',
				],
			],
			'Email' => [
				'label'  => 'Email',
				'rules'  => 'required',
				'errors' => ['required'    => 'Email Tidak boleh Kosong',],
			],
			'JenisAnggota_id' => [
				'label'  => 'Jenis Anggota',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Jenis Anggota tidak boleh kosong',
				],
			],
			'StatusAnggota_id' => [
				'label'  => 'Status Anggota',
				'rules'  => 'required',
				'errors' => [
					'required' => 'Status Anggota tidak boleh kosong',
				],
			],
		]);
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$update_data = [
					'Fullname' => $this->request->getPost('Fullname'),
					'MemberNo' => $MemberNo,
					'IdentityNo' => $this->request->getPost('IdentityNo'),
					'PlaceOfBirth' => $this->request->getPost('PlaceOfBirth'),
					'DateOfBirth' =>  $this->request->getPost('DateOfBirth'),
					'Address' => $this->request->getPost('Address'),
					'AddressNow' => $this->request->getPost('AddressNow'),
					'Phone' => $this->request->getPost('Phone'),
					'InstitutionName' => $this->request->getPost('InstitutionName'),
					'InstitutionAddress' => $this->request->getPost('InstitutionAddress'),
					'InstitutionPhone' => $this->request->getPost('InstitutionPhone'),
					'MotherMaidenName' => $this->request->getPost('MotherMaidenName'),
					'Email' => $this->request->getPost('Email'),
					'RT' => $this->request->getPost('RT'),
					'RTNow' => $this->request->getPost('RTNow'),
					'RWNow' => $this->request->getPost('RWNow'),
					'RW' => $this->request->getPost('RW'),
					'TahunAjaran' => $this->request->getPost('TahunAjaran'),
					'IdentityType_id' => $this->request->getPost('IdentityType_id'),
					'MaritalStatus_id' => $this->request->getPost('MaritalStatus_id'),
					'Sex_id' 	=> $this->request->getPost('Sex_id'),
					'JenjangPendidikan_id' 	=> $this->request->getPost('JenjangPendidikan_id'),
					'Job_id' 	=> $this->request->getPost('Job_id'),
					'JenisAnggota_id' 	=> $this->request->getPost('JenisAnggota_id'),
					'Agama_id' 	=> $this->request->getPost('Agama_id'),
					'UnitKerja_id' 	=> $this->request->getPost('UnitKerja_id'),
					'Fakultas_id' 	=> $this->request->getPost('Fakultas_id'),
					'Jurusan_id' 	=> $this->request->getPost('Jurusan_id'),
					'StatusAnggota_id' => $this->request->getPost('StatusAnggota_id'),
					'UpdateBy' => login_id(),
				];

				$province = $this->request->getPost('Province');
				if (!empty($province)) {
					$region = $this->regionModel->where('code', $province)->first();
					$update_data['Province'] = $region->name;
				}

				$city = $this->request->getPost('City');
				if (!empty($city)) {
					$region = $this->regionModel->where('code', $city)->first();
					$update_data['City'] = $region->name;
				}

				$kecamatan = $this->request->getPost('Kecamatan');
				if (!empty($kecamatan)) {
					$region = $this->regionModel->where('code', $kecamatan)->first();
					$update_data['Kecamatan'] = $region->name;
				}

				$kelurahan = $this->request->getPost('Kelurahan');
				if (!empty($kelurahan)) {
					$region = $this->regionModel->where('code', $kelurahan)->first();
					$update_data['Kelurahan'] = $region->name;
				}

				$provinceNow = $this->request->getPost('ProvinceNow');
				if (!empty($provinceNow)) {
					$region = $this->regionModel->where('code', $provinceNow)->first();
					$update_data['ProvinceNow'] = $region->name;
				}

				$cityNow = $this->request->getPost('CityNow');
				if (!empty($cityNow)) {
					$region = $this->regionModel->where('code', $cityNow)->first();
					$update_data['CityNow'] = $region->name;
				}

				$kecamatanNow = $this->request->getPost('KecamatanNow');
				if (!empty($kecamatanNow)) {
					$region = $this->regionModel->where('code', $kecamatanNow)->first();
					$update_data['KecamatanNow'] = $region->name;
				}

				$kelurahanNow = $this->request->getPost('KelurahanNow');
				if (!empty($kelurahanNow)) {
					$region = $this->regionModel->where('code', $kelurahanNow)->first();
					$update_data['KelurahanNow'] = $region->name;
				}
				// dd($update_data);

				$is_camera = $this->request->getPost('is_camera');
				if ($is_camera) {
					// Logic Upload
					$base64_string = $this->request->getPost('camera_image');
					if (!empty($base64_string)) {
						$file = new File($this->uploadPath);
						$newFileName = $file->getRandomName() . '.jpg';
						base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
						$update_data['PhotoUrl'] =  $newFileName;
					}
				} else {
					// Logic Upload
					$files = (array) $this->request->getPost('file_image');
					if (count($files)) {
						$listed_file = array();
						foreach ($files as $uuid => $name) {
							if (file_exists($this->modulePath . $name)) {
								$listed_file[] = $name;
							} else {
								if (file_exists($this->uploadPath . $name)) {
									$file = new File($this->uploadPath . $name);
									$newFileName = $file->getRandomName();
									$file->move($this->modulePath, $newFileName);
									$listed_file[] = $newFileName;
								}
							}
						}
						$update_data['PhotoUrl'] = implode(',', $listed_file);
					}
				}

				$anggotaUpdate = $this->anggotaModel->update($ID, $update_data);
				if ($anggotaUpdate) {
					$arr_Data_ID = [];
					foreach ($hak_akses_lokasi as $row) {
						$tmp_id = $row->DataID;
						array_push($arr_Data_ID, $tmp_id);
					}

					$Koleksi = $this->request->getPost('CategoryLoan_id');
					$this->AksesKoleksiModel->where('member_id', $member_id)->delete();

					$save_akses_koleksi_temp = [];
					$save_akses_koleksi = [];
					for ($x = 0; $x < count($Koleksi); $x++) {
						$save_akses_koleksi_temp = [
							'Member_id' => $member_id,
							'CategoryLoan_id' => $Koleksi[$x],
						];

						array_push($save_akses_koleksi, $save_akses_koleksi_temp);
						if (!empty($save_akses_koleksi)) {
							$this->AksesKoleksiModel->insertBatch($save_akses_koleksi);
						}
					}

					$Locations = $this->request->getPost('LocationLoan_id');
					$this->anggotahakaksesModel->where('member_id', $member_id)->delete();
					$save_akses_lokasi_temp = [];
					$save_akses_lokasi = [];
					for ($x = 0; $x < count($Locations); $x++) {
						$save_akses_lokasi_temp = [
							'Member_id' => $member_id,
							'LocationLoan_id' => $Locations[$x],
						];
						array_push($save_akses_lokasi, $save_akses_lokasi_temp);
					}

					if (!empty($save_akses_lokasi)) {
						$this->anggotahakaksesModel->insertBatch($save_akses_lokasi);
					}

					if ($is_anggota) {
						set_message('toastr_msg', 'Data Anggota berhasil disimpan');
						set_message('toastr_type', 'success');

						return redirect()->back();
					} else {
						set_message('toastr_msg', 'Data Anggota berhasil disimpan');
						set_message('toastr_type', 'success');
						return redirect()->to('/anggota');
					}
				} else {
					if ($is_anggota) {
						set_message('toastr_msg', 'Anggota gagal disimpan');
						set_message('toastr_type', 'warning');
						set_message('message', 'Anggota gagal disimpan');

						return redirect()->back();
					} else {
						set_message('toastr_msg', 'Anggota gagal disimpan');
						set_message('toastr_type', 'warning');
						set_message('message', 'Anggota gagal disimpan');

						return redirect()->to('/anggota/edit/' . $ID);
					}
				}
			}
		}

		$this->data['redirect'] = base_url('anggota/edit/' . $ID);
		$this->data['is_anggota'] = $is_anggota;
		echo view('Anggota\Views\update', $this->data);
	}

	public function proses_keranjang()
	{
		$IDs = $this->request->getvar('ID');
		$update_data = array();

		if (!empty($IDs)) {
			foreach ($IDs as $ID) {
				$update_data[] = array(
					'id' => $ID,
					'IsKeranjang' => 1,
				);
			}

			if (!empty($update_data)) {
				$this->anggotaModel->updateBatch($update_data, 'id');

				set_message('toastr_msg', 'Berhasil dipindahkan ke keranjang');
				set_message('toastr_type', 'success');
				set_message('message', 'Berhasil dipindahkan ke keranjang');
			}
		} else {
			set_message('toastr_msg', 'Pilih anggota yang akan dipindahkan ke keranjang terlebih dahulu');
			set_message('toastr_type', 'warning');
			set_message('message', 'Pilih anggota yang akan dipindahkan ke keranjang terlebih dahulu');
		}

		return redirect()->back();
	}

	public function pulihkan_keranjang()
	{
		$IDs = $this->request->getvar('ID');
		$update_data = array();

		if (!empty($IDs)) {
			foreach ($IDs as $ID) {
				$update_data[] = array(
					'ID' => $ID,
					'IsKeranjang' => 0,
				);
			}

			if (!empty($update_data)) {
				$this->anggotaModel->updateBatch($update_data, 'ID');

				set_message('toastr_msg', 'Berhasil dipulihkan dari keranjang anggota');
				set_message('toastr_type', 'success');
				set_message('message', 'Berhasil dipulihkan dari keranjang anggota');
			}
		} else {
			set_message('toastr_msg', 'Pilih anggota yang akan dipulihkan terlebih dahulu');
			set_message('toastr_type', 'warning');
			set_message('message', 'Pilih anggota yang akan dipulihkan terlebih dahulu');
		}

		return redirect()->back();
	}

	public function hapus_permanen()
	{
		$IDs = $this->request->getvar('ID');
		$update_data = array();

		if (!empty($IDs)) {
			$this->anggotaModel->delete($IDs);

			set_message('toastr_msg', 'Anggota Berhasil dihapus permanen');
			set_message('toastr_type', 'success');
			set_message('message', 'Anggota Berhasil dihapus permanen');
		} else {
			set_message('toastr_msg', 'Pilih Anggota yang akan dihapus permanen terlebih dahulu');
			set_message('toastr_type', 'warning');
			set_message('message', 'Pilih Anggota yang akan dihapus permanen terlebih dahulu');
		}

		return redirect()->back();
	}

	public function detail(int $id = null)
	{

		$anggota = $this->anggotaModel->find($id);
		$this->data['redirect'] = base_url('anggota/detail/' . $id);
		$this->data['anggota'] = $anggota;
		echo view('Anggota\Views\detail', $this->data);
	}
	public function delete(int $id = 0)
	{
		if (!is_allowed('anggota/delete')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('anggota');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/anggota');
		}
		$anggotaDelete = $this->anggotaModel->delete($id);
		if ($anggotaDelete) {
			set_message('toastr_msg', 'Data Anggota berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/anggota');
		} else {
			set_message('toastr_msg', lang('Anggota.info.failed_deleted'));
			set_message('toastr_type', 'warning');
			set_message('message', lang('Anggota.info.failed_deleted'));
			return redirect()->to('/anggota/delete/' . $id);
		}
	}
	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');
		$anggotaUpdate = $this->anggotaModel->update($id, array($field => $value));
		// dd($anggotaUpdate);
		if ($anggotaUpdate) {
			set_message('toastr_msg', ' Anggota berhasil disimpan');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Anggota gagal disimpan');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/anggota');
	}
	// Daftar data Pelanggaran
	public function D_pelanggaran()
	{

		$query = $this->anggotaModel
			->select('t_anggota.*')
			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_anggota.created_by', 'left')
			->join('users updated', 'updated.id = t_anggota.updated_by', 'left');
		$anggotas = $query->findAll();
		// $Nomember=$this->anggotaModel->MemberNo();
		$this->data['title'] = 'Data-Pelanggaran';
		$this->data['anggotas'] = $anggotas;
		// $this->data['MemberNo'] = $this->AnggotaModel->MemberNo();
		// $this->data['MemberNo']
		echo view('Anggota\Views\Data-pelanggaran', $this->data);
	}
	// Daftar data Peminjaman
	public function D_peminjaman()
	{

		$query = $this->anggotaModel
			->select('t_anggota.*')
			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_anggota.created_by', 'left')
			->join('users updated', 'updated.id = t_anggota.updated_by', 'left');
		$anggotas = $query->findAll();
		// $Nomember=$this->anggotaModel->MemberNo();
		$this->data['title'] = 'Data-Pelanggaran';
		$this->data['anggotas'] = $anggotas;
		// $this->data['MemberNo'] = $this->AnggotaModel->MemberNo();
		// $this->data['MemberNo']
		echo view('Anggota\Views\Data-Peminjaman', $this->data);
	}
	// Daftar data Perpanjangan
	public function D_perpanjangan()
	{

		$query = $this->anggotaModel
			->select('t_anggota.*')
			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_anggota.created_by', 'left')
			->join('users updated', 'updated.id = t_anggota.updated_by', 'left');
		$anggotas = $query->findAll();
		// $Nomember=$this->anggotaModel->MemberNo();
		$this->data['title'] = 'Data-Pelanggaran';
		$this->data['anggotas'] = $anggotas;
		// $this->data['MemberNo'] = $this->AnggotaModel->MemberNo();
		// $this->data['MemberNo']
		echo view('Anggota\Views\Data-Perpanjangan', $this->data);
	}
	// Daftar data sumbangan
	public function D_sumbangan(int $id = null)
	{

		$query = $this->anggotaModel
			->select('t_anggota.*')
			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_anggota.created_by', 'left')
			->join('users updated', 'updated.id = t_anggota.updated_by', 'left');
		$anggotas = $query->findAll();
		$anggota = $this->anggotaModel->find($id);
		$this->data['title'] = 'Data-Pelanggaran';
		$this->data['anggotas'] = $anggotas;
		$this->data['anggota'] = $anggota;
		echo view('Anggota\Views\Data-Sumbangan', $this->data);
	}
	// Import Data dari EXCEL

	public function import_view()
	{
		$this->data['title'] = 'Import Data Anggota';
		echo view('Anggota\Views\import');
	}

	public function import()
	{
		$db = db_connect();
		// Check if the request contains a file
		if (!$this->request->getFile('excel_file')) {
			return redirect()->back()->with('message', 'No file selected for upload.');
		}

		$file = $this->request->getFile('excel_file');
		// dd($file);

		if ($file->isValid() && !$file->hasMoved()) {
			$filePath = $file->getTempName();
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
			$data = $spreadsheet->getActiveSheet()->toArray();

			$memberModel = $this->anggotaModel;
			$branch_id = user()->branch_id;

			// Ambil header dari file Excel
			$header = array_map('strtolower', $data[0]);

			// Pemetaan kolom Excel ke kolom database
			$columnMap = [
				'nomor anggota' => 'MemberNo',
				'nama' => 'FullName',
				'tempat lahir' => 'PlaceOfBirth',
				'tanggal lahir' => 'DateOfBirth',
				'alamat' => 'Address',
				'provinsi sesuai ktp' => 'Province',
				'kota sesuai ktp' => 'City',
				'kecamatan sesuai ktp' => 'Kecamatan',
				'kelurahan sesuai ktp' => 'Kelurahan',
				'jenis identitas' => 'IdentityType_id',
				'nomor identitas' => 'IdentityNo',
			];

			foreach ($data as $key => $row) {
				// Skip header row
				if ($key == 0) {
					continue;
				}

				// Buat data yang akan dimasukkan ke database
				$memberData = ['Branch_id' => $branch_id];


				foreach ($header as $index => $columnName) {
					if (isset($columnMap[$columnName])) {
						$dbColumnName = $columnMap[$columnName];


						// If column is 'IdentityType_id', we need to fetch the corresponding id
						if ($dbColumnName == 'IdentityType_id') {
							// Get the id from the master_jenis_identitas table
							$jenisIdentitasValue = $row[$index];

							$identityType = $db->table('master_jenis_identitas')
								->like('Nama', $jenisIdentitasValue)
								->get()
								->getRow();
							// dd($identityType);		

							// If found, assign the id to 'IdentityType_id'
							if ($identityType) {
								$memberData['IdentityType_id'] = $identityType->id;
							} else {
								// Handle case if jenis identitas value does not exist in master table
								$memberData['IdentityType_id'] = null; // or some default value
							}
						} else {
							$memberData[$dbColumnName] = $row[$index];
						}
					}
				}

				// Check if nomor_anggota already exists
				$existingMember = $memberModel->where('MemberNo', $memberData['MemberNo'])->first();
				if ($existingMember) {
					continue; // Skip this row if MemberNo already exists
				}

				// Insert the member data
				$memberModel->insert($memberData);
			}

			return redirect()->to('/anggota/import')->with('message', 'Import sukses');
		}

		return redirect()->to('/anggota/import')->with('message', 'File tidak valid');
	}

	public function importold()
	{

		$this->data['title'] = 'Import Anggota';
		$this->validation->setRule('file_template', 'File Template', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// Logic Upload
			$files =  $this->request->getPost('file_template');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
						// dd($spreadsheet);
						$spreadsheet_arr = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
						// dd($spreadsheet_arr);
						$inserts = array();
						foreach ($spreadsheet_arr as $row) {
							$inserts[] = array(
								'MemberNo' => $row['A'],
								'FullName' => $row['B'],
								'PlaceOfBirth' => $row['C'],
								'DateOfBirth' => $row['D'],
								'Address' => $row['E'],
								'Branch_id' => user()->branch_id
							);
						}
						dd($inserts);
						$anggotaSaved = $this->anggotaModel->insertBatch($inserts);
						if ($anggotaSaved) {
							set_message('toastr_msg', 'Import Anggota berhasil');
							set_message('toastr_type', 'success');
							return redirect()->to('/anggota');
						} else {
							set_message('toastr_msg', 'Import Anggota gagal');
							set_message('toastr_type', 'warning');
							set_message('message', 'Import Anggota gagal');
							return redirect()->to('/anggota/import');
						}
					}
				}
			}
		} else {
			$this->data['redirect'] = base_url('anggota/import');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Anggota\Views\import', $this->data);
		}
	}



	public function printanggota($id = null)
	{

		$templateModel = new BaseModel('t_template');
		$template = $templateModel->where('active', 1)->first();



		if (empty($template)) {
			echo "Tidak ada template untuk di cetak";
			exit;
		}
		$bg = file_get_contents(ROOTPATH . 'public/uploads/master-template/' . $template->file_image);
		$bg_base64 = 'data:image/png;base64,' . base64_encode($bg);
		$this->data['bg_base64'] = $bg_base64;
		// Load required libraries
		$db = db_connect(); // Use 'data' database connect();

		// Get member data with related tables
		$anggota = $db->table('members m')
			->select('m.*')
			->where('m.id', $id)
			->get()
			->getRow();

		if (!$anggota) {
			throw new \Exception('Data anggota tidak ditemukan');
		}

		$this->data['anggota'] = $anggota;

		// Get library name
		$perpus_name = $db->table('settingparameters')
			->where('Name', 'NamaPerpustakaan')
			->get()
			->getRow()
			->Value ?? "Perpustakaan Nasional";
		$this->data['perpus_name'] = $perpus_name;

		// Get logo
		$logo_setting = $db->table('settingparameters')
			->where('Name', 'Logo')
			->get()
			->getRow();

		$logo_base64 = '';
		if ($logo_setting && $logo_setting->Value) {
			$logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
			if (file_exists($logo_path)) {
				$logo = file_get_contents($logo_path);
				$logo_base64 = 'data:image/png;base64,' . base64_encode($logo);
			}
		}
		// Fallback if logo is not found
		if (!$logo_base64) {
			// You can provide a path to a default logo here
			$logo_base64 = 'https://placehold.co/80x80/cccccc/666666?text=LOGO';
		}

		$this->data['logo_base64'] = $logo_base64;


		// Get member photo if exists
		$photo_base64 = '';
		if ($anggota->PhotoUrl && $anggota->PhotoUrl != '') {
			$photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
			if (file_exists($photo_path)) {
				$photo = file_get_contents($photo_path);
				$photo_base64 = 'data:image/jpeg;base64,' . base64_encode($photo);
			}
		}
		$this->data['photo_base64'] = $photo_base64;
		// Fallback for photo
		$photo_src = $photo_base64 ?: 'https://placehold.co/250x280/cccccc/666666?text=FOTO';


		// Generate QR Code content
		$qr_content = $anggota->MemberNo;
		$options = new QROptions([
			'scale' => 7, // Increased scale for better quality
			'imageBase64' => true,
		]);
		$qrcode = new QRCode($options);
		$qr_image = $qrcode->render($qr_content);

		$this->data['qr_image'] = $qr_image;

		// Format end date
		$end_date = date('d F Y', strtotime($anggota->EndDate));
		$this->data['end_date'] = $end_date;
		$jenis_anggota_id = $anggota->JenisAnggota_id;
		// Assuming $this->jenisanggotaModel is available in your controller
		$jenis_anggota = $this->jenisanggotaModel->find($jenis_anggota_id);
		$jenis_anggota_nama = $jenis_anggota ? $jenis_anggota->jenisanggota : 'UMUM';
		$this->data['jenis_anggota_nama'] = $jenis_anggota_nama;

		$background_image_filename = $db->table('settingparameters')
			->where('Name', 'KartuAnggota1')
			->get()
			->getRow()->Value ?? null;

		$backgroundStyle = ''; // Default kosong, agar CSS yang berlaku

		if (!empty($background_image_filename)) {
			// Jika ada file gambar di database, buat URL-nya
			$imageUrl = base_url('uploads/card_backgrounds/' . $background_image_filename);

			// Siapkan style inline untuk menimpa CSS default
			$backgroundStyle = "background: url('{$imageUrl}') no-repeat center center / cover;";
			$this->data['backgroundStyle'] = $backgroundStyle;
		}


		echo view('Anggota\Views\pdf\pdf1', $this->data);
	}

	public function printkartubelakang($id = null)
	{
		// Load required libraries
		$db = db_connect(); // Use 'data' database connect();



		// Get library name
		$perpus_name = $db->table('settingparameters')
			->where('Name', 'NamaPerpustakaan')
			->get()
			->getRow()
			->Value ?? "Perpustakaan Nasional";
		$this->data['perpus_name'] = $perpus_name;
		$lokasi_perpustakaan = $db->table('settingparameters')
			->where('Name', 'NamaLokasiPerpustakaan')
			->get()
			->getRow()
			->Value ?? "Perpustakaan Nasional";
		$this->data['lokasi_perpustakaan'] = $lokasi_perpustakaan;

		// Get logo
		$logo_setting = $db->table('settingparameters')
			->where('Name', 'Logo')
			->get()
			->getRow();

		$logo_base64 = '';
		if ($logo_setting && $logo_setting->Value) {
			$logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
			if (file_exists($logo_path)) {
				$logo = file_get_contents($logo_path);
				$logo_base64 = 'data:image/png;base64,' . base64_encode($logo);
			}
		}
		// Fallback if logo is not found
		if (!$logo_base64) {
			// You can provide a path to a default logo here
			$logo_base64 = 'https://placehold.co/80x80/cccccc/666666?text=LOGO';
		}

		$this->data['logo_base64'] = $logo_base64;


		echo view('Anggota\Views\pdf\cetak-kartubelakang', $this->data);
	}


	public function print_card2(int $id = null)
	{
		$templateModel = new BaseModel('t_template');
		$template = $templateModel->where('active', 1)->first();



		if (empty($template)) {
			echo "Tidak ada template untuk di cetak";
			exit;
		}

		$anggota = $this->anggotaModel->find($id);
		$options = new QROptions([
			'version'      => 5,
			'cssClass'     => 'barcode',
		]);

		$bg = file_get_contents(ROOTPATH . 'public/uploads/master-template/' . $template->file_image);
		$bg_base64 = 'data:image/png;base64,' . base64_encode($bg);
		$photo = file_get_contents(ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl);
		$photo_base64 = 'data:image/png;base64,' . base64_encode($photo);
		if ($template->layout == 'Landscape') {
			$photo_html = '<img src="' . $photo_base64 . '" width="200" height="200" style="background-color: #ffffff; margin-left: 10px; padding: 5px; margin-top:7px"/>';
		} else {
			$photo_html = '<img src="' . $photo_base64 . '" width="225px" height="250px" style="background-color: #ffffff; padding: 5px; margin-bottom: 15px"/>';
		}
		$db = db_connect();
		$nama_perpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?: "Perpustakaan Mitra";
		// Corrected line 6
		$logo = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value ?: "Perpustakaan Mitra";
		$perpus_logo = file_get_contents(ROOTPATH . 'public/uploads/branch/' . $logo);
		$perpus_logo_base64 = 'data:image/png;base64,' . base64_encode($perpus_logo);
		$photo_html2 = '<img src="' . $perpus_logo_base64 . '" width="100" height="100" style="background-color: #ffffff; padding: 5px; margin-bottom: 15px"/>';

		$barcode = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode_base64 = 'data:image/png;base64,' . base64_encode($barcode->getBarcode($anggota->MemberNo ?? '0000000000000', $barcode::TYPE_CODE_39, 2, 100, [0, 0, 0]));
		$barcode_html = '<img src="' . $barcode_base64 . '" alt="Barcode" style="background-color: #ffffff; padding: 5px;"/>';
		$qrcode_base64 = (new QRCode($options))->render($anggota->MemberNo);
		$qrcode_html = '<img src="' . $qrcode_base64 . '" alt="Qrcode" style="background-color: #ffffff; padding: 2px; margin-top: 20px" width="150px" height="150px"/>';

		$content = $template->content ?? '';
		$jenis_anggota_id = $anggota->JenisAnggota_id;
		$jenis_anggota = $this->jenisanggotaModel->find($jenis_anggota_id);
		$content = str_replace('{perpus_bg}', $bg_base64, $content);
		$content = str_replace('{perpus_logo}', $photo_html2, $content);
		$content = str_replace('{perpus_nama}', $nama_perpustakaan, $content);
		$content = str_replace('{perpus_kartu}', 'KARTU ANGGOTA', $content);
		$content = str_replace('{perpus_nama}', 'Perpustakaan Mitra Perpusnas', $content);
		$content = str_replace('{perpus_alamat}', 'Jl. Medan Merdeka Selatan, No. 11A', $content);
		$content = str_replace('{anggota_nomor}', $anggota->MemberNo ?? '', $content);
		$content = str_replace('{anggota_nama}', strtoupper($anggota->Fullname ?? ''), $content);
		$content = str_replace('{anggota_jenis}', strtoupper($jenis_anggota->jenisanggota ?? ''), $content);
		$content = str_replace('{anggota_foto}', $photo_html, $content);
		$content = str_replace('{anggota_qrcode}', $qrcode_html, $content);
		$content = str_replace('{anggota_barcode}', $barcode_html, $content);
		$this->data['content'] = $content;

		$dompdf = new \Dompdf\Dompdf();
		$options = new \Dompdf\Options();
		$options->setIsRemoteEnabled(true);
		$dompdf->setOptions($options);

		$html = view('KartuAnggota\Views\card_' . strtolower($template->layout ?? 'landscape'), $this->data);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', strtolower($template->layout ?? 'landscape'));
		$dompdf->render();
		$dompdf->stream('Kartu_Anggota.pdf', array("Attachment" => false));
		exit();
	}

	public function bebaspustaka(int $id = null)
	{
		$this->data['title'] = 'Bebas Pustaka';
		$this->data['kartu'] = array();
		$anggota = $this->anggotaModel->find($id);
		$this->data['anggota'] = $anggota;
		$dompdf = new \Dompdf\Dompdf();
		$html = view('Anggota\Views\bebas-pustaka', $this->data);
		$dompdf->loadHtml($html);
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'portrait');
		// Render the HTML as PDF
		$dompdf->render();
		// Output the generated PDF to Browser
		$dompdf->stream();
	}

	public function report()
	{
		$db = db_connect();
		$builder = $db->table('members as a')
			->select('a.ID, a.ID as action, a.ID as cid')
			->select('a.IsKeranjang, a.FullName, a.Phone, a.Email, a.PhotoUrl, a.MemberNo,  a.RegisterDate, a.EndDate, a.Branch_id')
			->select('a.JenisAnggota_id, jenis_anggota.jenisanggota as JenisAnggota, a.StatusAnggota_id, status_anggota.Nama as StatusAnggota')
			->select('branchs.ID as Branch_id, branchs.Name as Perpustakaan, branchs.Name, branchs.Code, branchs.NPP_Provinsi_id, branchs.NPP_KabKota_id, branchs.NPP_Kecamatan_id, branchs.NPP_Kelurahan_id, branchs.NPP_id')
			->join('branchs', 'branchs.ID = a.Branch_id', 'left')
			->join('jenis_anggota', 'jenis_anggota.id = a.JenisAnggota_id')
			->join('status_anggota', 'status_anggota.id = a.StatusAnggota_id');

		if (user()->category == 'admin') {
		} elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
			$npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
			$builder->where('branchs.NPP_Provinsi_id', $npp_provinsi_id);
		} elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
			$npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
			$builder->where('branchs.NPP_KabKota_id', $npp_kabkota_id);
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} else {
			$builder->where('a.Branch_id', branch_id());
		}

		$results = $builder->get()->getResult();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:G1');
		$sheet->setCellValue("A1", "Laporan Anggota");
		$sheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "Branch ID");
		$sheet->setCellValue("B2", "NPP");
		$sheet->setCellValue("C2", "Mitra Perpustakaan");
		$sheet->setCellValue("D2", "Nomor Anggota");
		$sheet->setCellValue("E2", "Tanggal Register");
		$sheet->setCellValue("F2", "Tanggal Berakhir");
		$sheet->setCellValue("G2", "Staus Anggota");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(75);
		$sheet->getColumnDimension('D')->setWidth(100);
		$sheet->getColumnDimension('E')->setWidth(10);
		$sheet->getColumnDimension('F')->setWidth(10);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->getStyle('A2:G2')->getFont()->setBold(true)->setSize(12);

		$col = 3;
		$no = 1;
		$i = 1;

		foreach ($results as $row) {
			$sheet->setCellValue("A" . $col, $row->Branch_id);
			$sheet->setCellValue("B" . $col, $row->Code);
			$sheet->setCellValue("C" . $col, $row->Perpustakaan);
			$sheet->setCellValue("D" . $col, $row->MemberNo);
			$sheet->setCellValue("E" . $col, $row->RegisterDate);
			$sheet->setCellValue("F" . $col, $row->EndDate);
			$sheet->setCellValue("G" . $col, $row->ID);

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Laporan Anggota';
		$filename = ucwords($subject) . '-' . date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function uploadBackground()
	{
		// Aturan validasi untuk file yang diunggah
		$validationRule = [
			'bgImage' => [
				'label' => 'Background Image',
				'rules' => [
					'uploaded[bgImage]', // Memastikan file diunggah
					'is_image[bgImage]', // Memastikan file adalah gambar
					'mime_in[bgImage,image/jpg,image/jpeg,image/png,image/gif]', // Membatasi tipe MIME
					'max_size[bgImage,2048]', // Ukuran maksimal 2MB
				],
			],
		];

		// Jalankan validasi
		if (!$this->validate($validationRule)) {
			// Jika gagal, kirim response error dalam format JSON
			return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
		}

		$img = $this->request->getFile('bgImage');

		if ($img->isValid() && !$img->hasMoved()) {
			// Dapatkan nama file lama untuk dihapus nanti
			$settingName = 'KartuAnggota1';
			$setting = $this->settingModel->where('Name', $settingName)->first();

			$oldFileName = $setting->Value ?? null;

			// Buat nama acak baru untuk file agar unik
			$newName = $img->getRandomName();
			$uploadPath = FCPATH . 'uploads/card_backgrounds/'; // Path ke folder publik

			// Pastikan direktori tujuan ada
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0777, true);
			}

			// Pindahkan file ke direktori tujuan
			$img->move($uploadPath, $newName);

			try {
				// Hapus file lama jika ada
				if ($oldFileName && file_exists($uploadPath . $oldFileName)) {
					unlink($uploadPath . $oldFileName);
				}

				// Lakukan INSERT atau UPDATE (Upsert)
				if ($setting) {
					// Jika data sudah ada, UPDATE
					// Akses properti 'id' menggunakan sintaks objek ->id
					$this->settingModel->update($setting->ID, ['Value' => $newName]);
				} else {
					// Jika data belum ada, INSERT
					// Buat objek data untuk disisipkan
					$data = new \stdClass();
					$data->Name  = $settingName;
					$data->Value = $newName;

					$this->settingModel->insert($data);
				}

				// Kirim response sukses
				return $this->response->setJSON([
					'success'  => true,
					'message'  => '✅ Background kartu berhasil diupdate.',
					'file_url' => base_url('uploads/card_backgrounds/' . $newName)
				]);
			} catch (\Exception $e) {
				// Tangani error database
				return $this->response->setJSON(['success' => false, 'message' => '❌ Database error: ' . $e->getMessage()]);
			}
		}

		// Kirim response error jika pemindahan file gagal
		return $this->response->setJSON(['success' => false, 'message' => '❌ Gagal memproses file yang diunggah.']);
	}

	public function multipleprint()
	{
		$member_ids = $this->request->getPost('member_ids');

		if (!$member_ids || !is_array($member_ids)) {
			throw new \Exception('ID anggota tidak valid');
		}

		$db = db_connect(); // Use 'data' database

		// Get library settings once
		$perpus_name = $db->table('settingparameters')
			->where('Name', 'NamaPerpustakaan')
			->get()
			->getRow()
			->Value ?? "Perpustakaan Nasional";

		// Get logo once
		$logo_setting = $db->table('settingparameters')
			->where('Name', 'Logo')
			->get()
			->getRow();

		$logo_base64 = '';
		if ($logo_setting && $logo_setting->Value) {
			$logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
			if (file_exists($logo_path)) {
				$logo = file_get_contents($logo_path);
				$logo_base64 = 'data:image/png;base64,' . base64_encode($logo);
			}
		}

		// Fallback if logo is not found
		if (!$logo_base64) {
			$logo_base64 = 'https://placehold.co/80x80/cccccc/666666?text=LOGO';
		}

		$members_data = [];

		// Loop through each member ID
		foreach ($member_ids as $member_id) {
			// Get member data
			$anggota = $db->table('members m')
				->select('m.*')
				->where('m.id', $member_id)
				->get()
				->getRow();

			if (!$anggota) {
				continue; // Skip if member not found
			}

			// Get member photo if exists
			$photo_base64 = '';
			if ($anggota->PhotoUrl && $anggota->PhotoUrl != '') {
				$photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
				if (file_exists($photo_path)) {
					$photo = file_get_contents($photo_path);
					$photo_base64 = 'data:image/jpeg;base64,' . base64_encode($photo);
				}
			}

			// Fallback for photo
			$photo_src = $photo_base64 ?: 'https://placehold.co/250x280/cccccc/666666?text=FOTO';

			// Generate QR Code content
			$qr_content = $anggota->MemberNo;
			$options = new QROptions([
				'scale' => 7,
				'imageBase64' => true,
			]);
			$qrcode = new QRCode($options);
			$qr_image = $qrcode->render($qr_content);

			// Format end date
			$end_date = date('d F Y', strtotime($anggota->EndDate));

			// Get jenis anggota using query builder for more control
			$jenis_anggota_id = $anggota->JenisAnggota_id;
			$jenis_anggota_data = $db->table('jenis_anggota')
				->select('jenisanggota')
				->where('id', $jenis_anggota_id)
				->get()
				->getRow();

			$jenis_anggota_nama = $jenis_anggota_data ? $jenis_anggota_data->jenisanggota : 'UMUM';

			// Store member data
			$members_data[] = [
				'anggota' => $anggota,
				'photo_base64' => $photo_src,
				'qr_image' => $qr_image,
				'end_date' => $end_date,
				'jenis_anggota_nama' => $jenis_anggota_nama
			];
		}

		// Prepare data for view
		$this->data['members_data'] = $members_data;
		$this->data['perpus_name'] = $perpus_name;
		$this->data['logo_base64'] = $logo_base64;
		$this->data['title'] = 'Cetak Kartu Anggota - Multiple';

		return view('Anggota\Views\pdf\multiple-pdf1', $this->data);
	}
}
