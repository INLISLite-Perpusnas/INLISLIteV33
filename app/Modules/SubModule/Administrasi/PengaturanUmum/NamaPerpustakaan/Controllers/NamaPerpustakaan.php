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


	function __construct()
	{
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->branchModel = new \NamaPerpustakaan\Models\BranchModel();

		$this->modulePath = ROOTPATH . 'public/uploads/branch/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function index()
	{
	
		if (branch_id() == 0) {
			set_message('toastr_msg', 'Informasi Branch ID belum ada');
			set_message('toastr_type', 'info');
			return redirect()->to('/user/profile');
		}

		$branch = $this->branchModel->find(branch_id());
	
		$this->data['branch'] = $branch;
		$this->data['title'] = 'Nama Perpustakaan';

		echo view('NamaPerpustakaan\Views\update', $this->data);
	}

	public function update()
	{
		$this->validation->setRule('Name', 'Nama Perpustakaan', 'required');
		$this->validation->setRule('Url', 'URL Perpustakaan', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$Url = $this->request->getPost('Url') ?? '';
			$branch = $this->branchModel->where('slug', $Url)->first();
			// if ($branch) {
			// 	set_message('toastr_msg', 'URL Perpustakaan sudah digunakan');
			// 	set_message('toastr_type', 'error');
			// 	return redirect()->back();
			// }

			// Escape the HTML content to prevent XSS attacks
			$LayananOperasionl = $this->request->getPost('LayananOperasionl') ?? '';
			$LayananOperasionl_Str = htmlspecialchars($LayananOperasionl, ENT_QUOTES, 'UTF-8');

			$update_data = array(
				'Name' => $this->request->getPost('Name'),
				'Email' => $this->request->getPost('Email'),
				'Phone' => $this->request->getPost('Phone'),
				'Address' => $this->request->getPost('Address'),
				'IG' => $this->request->getPost('IG'),
				'FB' => $this->request->getPost('FB'),
				'YT' => $this->request->getPost('YT'),
				'TW' => $this->request->getPost('TW'),
				'slug' => $this->request->getPost('Url'),
				'LayananOperasionl' => $LayananOperasionl_Str,
			);

			$updateBranch = $this->branchModel->update(branch_id(), $update_data);

			if ($updateBranch) {
				set_message('toastr_msg', 'Nama Perpustakaan berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Nama Perpustakaan gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->back();
		} else {
			set_message('toastr_msg', $this->validation->getErrors());
			set_message('toastr_type', 'error');
			return redirect()->back();
		}
	}

	public function upload_file()
	{
		$upload_id = $this->request->getPost('upload_id');
		$upload_field = $this->request->getPost('upload_field');
		$upload_title = $this->request->getPost('upload_title');

		$update_data = [];

		$files = (array) $this->request->getPost('file_pendukung');
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
			$update_data[$upload_field] = implode(',', $listed_file);
		}

		$updateData = $this->branchModel->update($upload_id, $update_data);
		if ($updateData) {
			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}
}
