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
		$request = service('request');
		$selected_npp = $request->getGet('npp') ?? '';

		// Tidak load semua branch untuk menghindari memory exhausted
		$this->data['selected_npp'] = $selected_npp;

		// If NPP is selected, get specific branch data
		if (!empty($selected_npp)) {
			$current_branch = $this->branchModel->where('Code', $selected_npp)->first();
			$this->data['current_branch'] = $current_branch;
			
			// Jika NPP tidak ditemukan, set pesan
			if (!$current_branch) {
				$this->data['npp_not_found'] = true;
			}
		} else {
			$this->data['current_branch'] = null;
		}

		$this->data['title'] = 'Nama Perpustakaan';

		echo view('NamaPerpustakaan\Views\update', $this->data);
	}

	public function update()
	{
		$this->validation->setRule('Name', 'Nama Perpustakaan', 'required');
		$this->validation->setRule('Url', 'URL Perpustakaan', 'required');
		$this->validation->setRule('Code', 'NPP', 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$Url = $this->request->getPost('Url') ?? '';
			$Code = $this->request->getPost('Code') ?? '';
			$ID = $this->request->getPost('ID') ?? '';
			$selected_npp = $this->request->getPost('selected_npp') ?? '';
			
			// Check if URL already exists (except for current record)
			$existing_branch = $this->branchModel->where('slug', $Url);
			if (!empty($ID)) {
				$existing_branch = $existing_branch->where('ID !=', $ID);
			}
			$existing_branch = $existing_branch->first();

			if ($existing_branch) {
				set_message('toastr_msg', 'URL Perpustakaan sudah digunakan');
				set_message('toastr_type', 'error');
				return redirect()->to(base_url('master-nama-perpustakaan?npp=' . $selected_npp));
			}

			// Escape the HTML content to prevent XSS attacks
			$LayananOperasionl = $this->request->getPost('LayananOperasionl') ?? '';
			$LayananOperasionl_Str = htmlspecialchars($LayananOperasionl, ENT_QUOTES, 'UTF-8');

			$update_data = array(
				'Code' => $Code,
				'Name' => $this->request->getPost('Name'),
				'Email' => $this->request->getPost('Email'),
				'Phone' => $this->request->getPost('Phone'),
				'Address' => $this->request->getPost('Address'),
				'IG' => $this->request->getPost('IG'),
				'FB' => $this->request->getPost('FB'),
				'YT' => $this->request->getPost('YT'),
				'TW' => $this->request->getPost('TW'),
				'slug' => $Url,
				'LayananOperasionl' => $LayananOperasionl_Str,
				'UpdateDate' => date('Y-m-d H:i:s'),
				'UpdateBy' => user_id(), // Menggunakan helper user_id() yang sudah ada
				'UpdateTerminal' => $this->request->getIPAddress()
			);

			try {
				if (!empty($ID)) {
					// Update existing record
					$updateBranch = $this->branchModel->update($ID, $update_data);
					$message = 'Data perpustakaan berhasil diperbarui';
				} else {
					// Create new record
					$update_data['CreateDate'] = date('Y-m-d H:i:s');
					$update_data['CreateBy'] = user_id();
					$update_data['CreateTerminal'] = $this->request->getIPAddress();
					$update_data['active'] = 1;
					
					// Set default values untuk logo jika belum ada
					if (empty($update_data['Logo'])) {
						$update_data['Logo'] = 'perpusnas.png';
					}
					if (empty($update_data['CoverLetter'])) {
						$update_data['CoverLetter'] = 'perpusnas.png';
					}

					$updateBranch = $this->branchModel->insert($update_data);
					$message = 'Data perpustakaan berhasil ditambahkan';
				}

				if ($updateBranch) {
					set_message('toastr_msg', $message);
					set_message('toastr_type', 'success');
				} else {
					set_message('toastr_msg', 'Gagal menyimpan data perpustakaan');
					set_message('toastr_type', 'error');
				}

			} catch (\Exception $e) {
				log_message('error', 'Error saving library data: ' . $e->getMessage());
				set_message('toastr_msg', 'Terjadi kesalahan: ' . $e->getMessage());
				set_message('toastr_type', 'error');
			}

			return redirect()->to(base_url('master-nama-perpustakaan?npp=' . $selected_npp));

		} else {
			$errors = $this->validation->getErrors();
			$error_message = '';
			foreach ($errors as $error) {
				$error_message .= $error . '<br>';
			}
			
			set_message('toastr_msg', $error_message);
			set_message('toastr_type', 'error');
			
			$selected_npp = $this->request->getPost('selected_npp') ?? '';
			return redirect()->to(base_url('master-nama-perpustakaan?npp=' . $selected_npp));
		}
	}

	/**
	 * Get branch data by NPP code (AJAX endpoint)
	 */
	public function getBranchByNpp($npp_code = null)
	{
		if (!$npp_code) {
			return $this->response->setJSON([
				'status' => 400,
				'message' => 'NPP code required'
			]);
		}

		$branch = $this->branchModel->where('Code', $npp_code)->first();
		
		if ($branch) {
			return $this->response->setJSON([
				'status' => 200,
				'data' => $branch
			]);
		} else {
			return $this->response->setJSON([
				'status' => 404,
				'message' => 'Branch not found'
			]);
		}
	}

	/**
	 * Search NPP dengan AJAX untuk autocomplete
	 */
	public function searchNpp()
	{
		$request = service('request');
		$search = $request->getGet('term') ?? '';
		
		if (strlen($search) < 2) {
			return $this->response->setJSON([]);
		}

		$branches = $this->branchModel
			->select('Code, Name')
			->like('Code', $search)
			->orLike('Name', $search)
			->limit(10)
			->findAll();

		$results = [];
		foreach ($branches as $branch) {
			$results[] = [
				'value' => $branch['Code'],
				'label' => $branch['Code'] . ' - ' . $branch['Name']
			];
		}

		return $this->response->setJSON($results);
	}

	/**
	 * Check if URL is available
	 */
	public function checkUrlAvailability()
	{
		$url = $this->request->getPost('url');
		$id = $this->request->getPost('id');
		
		$query = $this->branchModel->where('slug', $url);
		if ($id) {
			$query = $query->where('ID !=', $id);
		}
		
		$existing = $query->first();
		
		return $this->response->setJSON([
			'available' => !$existing,
			'message' => $existing ? 'URL sudah digunakan' : 'URL tersedia'
		]);
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

	/**
	 * Delete branch record
	 */
	public function delete($id = null)
	{
		if (!$id) {
			set_message('toastr_msg', 'ID perpustakaan tidak valid');
			set_message('toastr_type', 'error');
			return redirect()->back();
		}

		try {
			// Get branch data first to get logo and cover letter files
			$branch = $this->branchModel->find($id);
			
			if (!$branch) {
				set_message('toastr_msg', 'Data perpustakaan tidak ditemukan');
				set_message('toastr_type', 'error');
				return redirect()->back();
			}

			// Delete associated files
			if (!empty($branch['Logo']) && $branch['Logo'] !== 'perpusnas.png') {
				$logoPath = $this->modulePath . $branch['Logo'];
				if (file_exists($logoPath)) {
					unlink($logoPath);
				}
			}

			if (!empty($branch['CoverLetter']) && $branch['CoverLetter'] !== 'perpusnas.png') {
				$coverPath = $this->modulePath . $branch['CoverLetter'];
				if (file_exists($coverPath)) {
					unlink($coverPath);
				}
			}

			// Delete record from database
			$deleteResult = $this->branchModel->delete($id);

			if ($deleteResult) {
				set_message('toastr_msg', 'Data perpustakaan berhasil dihapus');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Gagal menghapus data perpustakaan');
				set_message('toastr_type', 'error');
			}

		} catch (\Exception $e) {
			log_message('error', 'Error deleting library data: ' . $e->getMessage());
			set_message('toastr_msg', 'Terjadi kesalahan: ' . $e->getMessage());
			set_message('toastr_type', 'error');
		}

		return redirect()->to(base_url('master-nama-perpustakaan'));
	}

	/**
	 * Validate form data via AJAX
	 */
	public function validateForm()
	{
		$rules = [
			'Name' => 'required|max_length[255]',
			'Url' => 'required|max_length[255]|alpha_dash',
			'Email' => 'permit_empty|valid_email|max_length[255]',
			'Phone' => 'permit_empty|max_length[20]'
		];

		$validation = \Config\Services::validation();
		$validation->setRules($rules);

		if ($validation->withRequest($this->request)->run()) {
			return $this->response->setJSON([
				'valid' => true,
				'message' => 'Data valid'
			]);
		} else {
			return $this->response->setJSON([
				'valid' => false,
				'errors' => $validation->getErrors()
			]);
		}
	}
}