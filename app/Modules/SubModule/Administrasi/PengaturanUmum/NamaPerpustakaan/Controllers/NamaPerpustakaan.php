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
		$this->db= db_connect('data');
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
			'JamOperasional' => trim($this->request->getPost('jam_operasional')),
			'Instagram' => trim($this->request->getPost('instagram')),
			'Facebook' => trim($this->request->getPost('facebook')),
			'Youtube' => trim($this->request->getPost('youtube')),
			'Phone' => trim($this->request->getPost('phone')),
			'TulisanBanner' => trim($this->request->getPost('tulisan_banner')),
			'TentangKami' => trim($this->request->getPost('tentang_kami')),
			'IsUseKop' => $this->request->getPost('IsUseKop') ? 1 : 0,
			'LayananOperasionl' => $LayananOperasionl_Str,
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
			set_message('toastr_msg', 'Perubahan berhasil disimpan');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Gagal menyimpan perubahan');
			set_message('toastr_type', 'error');
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
