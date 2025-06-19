<?php

namespace GuestBook\Controllers;

class GuestBook extends \App\Controllers\BaseController
{
	public $memberguestModel;
	public $groupguestModel;
	public $uploadPath;
	public $modulePath;

	function __construct()
	{
		$this->language = \Config\Services::language();
		$this->language->setLocale('id');

		$this->memberguestModel = new \GuestBook\Models\MemberGuestModel();
		$this->groupguestModel  = new \GuestBook\Models\GroupGuestModel();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		// helper('member');
		helper('lokasiruang');
		helper('home');
	}

	public function index($prefix = '')
	{
		

		$this->data = [];
		if (empty($prefix)) {
			return redirect()->to('/search');
		}

		$branch_prefix = get_branch($prefix);
		if (!empty($branch_prefix)) {
			$this->session->set('branch', $branch_prefix);
			$locations = get_locations($branch_prefix->ID);
			$this->session->set('locations', $locations);

			setcookie('location_code', '', time() - 3600, "/");
			setcookie('location_key', '', time() - 3600, "/");
		}

		$branch = $this->session->get('branch');
		if (!empty($branch)) {
			$this->data['branch'] = $branch;
			$this->data['prefix'] = $branch->slug;
			$this->data['branch_id'] = $branch->ID;
		} else {
			return redirect()->to('/search');
		}

		if (!isset($_COOKIE['Location_id'])) {
			return redirect()->to($prefix . '/lokasi?slug=buku-tamu');
		}
		$cookie_location=$_COOKIE['Location_id'];
		

		// if ($_COOKIE['location_key'] != hash('sha256', $_COOKIE['location_code'])) {
		// 	return redirect()->to($prefix . '/lokasi?slug=buku-tamu');
		// }

		$slug = $this->request->getPost('slug') ?? 'anggota';
		if ($slug == 'anggota') {
			$member_no = $this->request->getGet('member_no') ?? '';
			if (!empty($member_no)) {
				$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
				$this->data['member'] = $member;
			}
		}

		$this->validation->setRule('member_no', 'Nomor Anggota', 'trim');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$member_no = $this->request->getPost('member_no') ?? '';
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			if (!empty($member)) {
				// $cookie_location = cookie_location();
				$save_data = [
					'NoAnggota' => $member->MemberNo,
					'Nama' => $member->Fullname,
					'PendidikanTerakhir_id' => $member->EducationLevel_id,
					'Profesi_id' => $member->Job_id,
					'Alamat' => $member->Address,
					'Location_id' => $cookie_location,
					'Branch_id' => $branch->ID,
					'CreateBy' => user_id(),
				];

				$newBukuTamuId = $this->memberguestModel->insert($save_data);
				if ($newBukuTamuId) {
					set_message('toastr_msg', 'Buku Tamu berhasil disimpan');
					set_message('toastr_type', 'success');
				} else {
					set_message('toastr_msg', 'Buku Tamu gagal disimpan');
					set_message('toastr_type', 'warning');
				}

				return redirect()->to($prefix . '/buku-tamu');
			}
		}

		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\add', $this->data);
	}


	public function store_anggota()
	{

		$this->validation->setRule('member_no', 'Nomor Anggota', 'trim');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$member_no = $this->request->getPost('member_no') ?? '';
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			if (!empty($member)) {
				$cookie_location = cookie_location();
				$save_data = [
					'NoAnggota' => $member->MemberNo,
					'Nama' => $member->Fullname,
					'PendidikanTerakhir_id' => $member->EducationLevel_id,
					'Profesi_id' => $member->Job_id,
					'Alamat' => $member->Address,
					'Location_id' => $cookie_location->ID,
					'Branch_id' => $cookie_location->Branch_id,
					'CreateBy' => user_id(),
				];

				$newBukuTamuId = $this->memberguestModel->insert($save_data);
				if ($newBukuTamuId) {
					set_message('toastr_msg', 'Buku Tamu berhasil disimpan');
					set_message('toastr_type', 'success');
				} else {
					set_message('toastr_msg', 'Buku Tamu gagal disimpan');
					set_message('toastr_type', 'warning');
				}

				return redirect()->to($prefix . '/buku-tamu');
			}
		}

		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\add', $this->data);
		
	}
	public function non_anggota($prefix = '')
	{
		if (empty($prefix)) {
			return redirect()->to('/search');
		}

		$branch = $this->session->get('branch');
		
		if (!empty($branch)) {
			$this->data['branch'] = $branch;
			$this->data['prefix'] = $branch->slug;
			$this->data['branch_id'] = $branch->ID;
		}

		if (!isset($_COOKIE['Location_id'])) {
			return redirect()->to($prefix . '/lokasi?slug=buku-tamu');
		}
		$cookie_location = isset($_COOKIE['Location_id']) ? intval($_COOKIE['Location_id']) : null;

	
		

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
				'Location_id' => $cookie_location,
				'Branch_id' => $branch->ID,
				'CreateBy' => user_id(),
			];
			

			$newRombonganId = $this->memberguestModel->insert($save_data);
			if ($newRombonganId) {
				set_message('toastr_msg', 'Buku Tamu berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Buku Tamu gagal disimpan');
				set_message('toastr_type', 'warning');
			}

			return redirect()->to($prefix . '/buku-tamu/non_anggota');
		}

		$this->data['title'] = 'Buku Tamu - Bukan Anggota';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\add_non_anggota', $this->data);
	}

	public function rombongan($prefix = '')
	{
		if (empty($prefix)) {
			return redirect()->to('/search');
		}

		$branch = $this->session->get('branch');
		if (!empty($branch)) {
			$this->data['branch'] = $branch;
			$this->data['prefix'] = $branch->slug;
			$this->data['branch_id'] = $branch->ID;
		}
		if (!isset($_COOKIE['Location_id'])) {
			return redirect()->to($prefix . '/lokasi?slug=buku-tamu');
		}
		$cookie_location = isset($_COOKIE['Location_id']) ? intval($_COOKIE['Location_id']) : null;

		$this->validation->setRules(
			[
				'NamaKetua' => [
					'label'  => 'Nama Ketua',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Nama Ketua tidak boleh kosong',
					],
				],
				'AsalInstansi' => [
					'label'  => 'Nama Instansi/Lembaga',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Nama Instansi/Lembaga tidak boleh kosong',
					],
				],
			]
		);

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// $cookie_location = cookie_location();
			$save_data = [
				'NamaKetua' => $this->request->getPost('NamaKetua'),
				'NomerTelponKetua' => $this->request->getPost('NomerTelponKetua'),
				'AsalInstansi' => $this->request->getPost('AsalInstansi'),
				'AlamatInstansi' => $this->request->getPost('AlamatInstansi'),
				'EmailInstansi' => $this->request->getPost('EmailInstansi'),
				'CountPersonel' => $this->request->getPost('CountPersonel'),
				'CountLaki' => $this->request->getPost('CountLaki'),
				'CountPerempuan' => $this->request->getPost('CountPerempuan'),
				'CountPNS' => $this->request->getPost('CountPNS'),
				'CountGuru' => $this->request->getPost('CountGuru'),
				'CountPSwasta' => $this->request->getPost('CountPSwasta'),
				'CountPeneliti' => $this->request->getPost('CountPeneliti'),
				'CountDosen' => $this->request->getPost('CountDosen'),
				'CountPensiunan' => $this->request->getPost('CountPensiunan'),
				'AlamatInstansi' => $this->request->getPost('AlamatInstansi'),
				'Location_id' => $cookie_location,
				'Branch_id' => $branch->ID,
				'CreateBy' => user_id(),
			];


			$newRombonganId = $this->groupguestModel->insert($save_data);
			if ($newRombonganId) {
				set_message('toastr_msg', 'Buku Tamu berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Buku Tamu gagal disimpan');
				set_message('toastr_type', 'warning');
			}

			return redirect()->to($prefix . '/buku-tamu/rombongan');
		}

		$this->data['title'] = 'Buku Tamu - Rombongan';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\add_rombongan', $this->data);
	}

	public function getKnownFaces($prefix = '')
{
	$db = \Config\Database::connect();
	dd(123);
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
