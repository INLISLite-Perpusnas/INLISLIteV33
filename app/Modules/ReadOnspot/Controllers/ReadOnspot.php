<?php

namespace ReadOnspot\Controllers;

class ReadOnspot extends \App\Controllers\BaseController
{
	public $bacaditempatModel;
	public $uploadPath;
	public $modulePath;
	public $collectionModel;

	function __construct()
	{
		$this->language = \Config\Services::language();
		$this->language->setLocale('id');

		$this->bacaditempatModel = new \ReadOnspot\Models\ReadOnspotModel();
		$this->collectionModel   = new \Peminjaman\Models\CollectionModel();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		helper('member');
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
			$NomorBarcode  = $this->request->getPost('NomorBarcode');
			//			if (!isset($NomorBarcode)) return redirect()->to($prefix.'/readonthespot/anggota');
			if (!isset($NomorBarcode)) {
				set_message('toastr_msg', 'Nomor Barcode tidak ditemukan');
				set_message('toastr_type', 'warning');
				return redirect()->to($prefix . '/baca-ditempat');
			}

			$member_no = $this->request->getPost('member_no') ?? '';
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			if (!empty($member)) {
				// $cookie_location = cookie_location();
				$save_data = [
					'Member_id'  => $member->ID,
					'Location_Id' => $cookie_location,
					'CreateBy' => user_id(),
				];

				$collection = get_ref_single('collections', 'NomorBarcode="' . $NomorBarcode . '"', 'data');
				if (!empty($collection)) {
					$save_data['collection_id'] = $collection->ID;

					$newBacaDitempatId = $this->bacaditempatModel->insert($save_data);
					if ($newBacaDitempatId) {
						set_message('toastr_msg', 'Baca Ditempat berhasil disimpan');
						set_message('toastr_type', 'success');
					} else {
						set_message('toastr_msg', 'Baca Ditempat gagal disimpan');
						set_message('toastr_type', 'warning');
					}
				} else {
					set_message('toastr_msg', 'Nomor Barcode tidak ditemukan');
					set_message('toastr_type', 'warning');
					return redirect()->to($prefix . '/baca-ditempat?member_no=' . $member_no);
				}

				return redirect()->to($prefix . '/baca-ditempat');
			}
		}

		$this->data['title']   = 'Baca Ditempat';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('ReadOnspot\Views\add', $this->data);
	}
}
