<?php

namespace SelfReturn\Controllers;

use \CodeIgniter\Files\File;

class SelfReturn extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $pengembalianModel;
    protected $uploadPath;
    protected $modulePath;
    
    function __construct()
    {
        $this->language = \Config\Services::language();
		$this->language->setLocale('id');
        
        $this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
        $this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
        $this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        $this->session = service('session');
		$this->cart = new \App\Libraries\Cart();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		helper('member');
		helper('lokasiruang');
    }

	public function index()
    {
		if(!isset($_COOKIE['location_code'])) return redirect()->to('lokasi?slug=pengembalian-mandiri');
		if($_COOKIE['location_key'] != hash('sha256', $_COOKIE['location_code'])) return redirect()->to('lokasi?slug=pengembalian-mandiri');

		$member_no = $this->request->getVar('member_no')??'';
		$this->data['member_no'] = $member_no;

		if(!empty($member_no)){
			$member = get_ref_single('members','MemberNo="'.$member_no.'"','inlis');
			if(!empty($member)){
				$jenis_anggota = get_ref_single('jenis_anggota','id="'.$member->JenisAnggota_id.'"','inlis');
				
				$this->data['member'] = $member;
				$this->data['jenis_anggota'] = $jenis_anggota;
			} else {
				set_message('toastr_msg','Nomor Anggota tidak ditemukan');
				set_message('toastr_type', 'warning');
				return redirect()->back();
			}
		}

        $this->data['title'] = 'Pengembalian Mandiri';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('SelfReturn\Views\add', $this->data);
    }

	public function do_return($id = null)
	{
		$carts = get_cart_return();
		$cli_update_data = array();
		if(!empty($id))
		{
			$cli_update_data[] = array(
				'ID' => $id,
				'LoanStatus' => 'Return',
				'ActualReturn' => date('Y-m-d'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => $this->request->getIPAddress(),
			);
		} else {	
			if(!empty($carts))
			{
				foreach($carts as $row){
					$cli_update_data[] = array(
						'ID' => str_replace('R','',$row->id),
						'LoanStatus' => 'Return',
						'ActualReturn' => date('Y-m-d'),
						'UpdateBy' => user_id(),
						'UpdateTerminal' => $this->request->getIPAddress(),
					);
				}
			}
		}

		$cli_update = $this->collectionLoanItemModel->updateBatch($cli_update_data,'ID');
		if ($cli_update) {
			if(!empty($carts))
			{				
				foreach($carts as $row){
					$this->cart->remove($row->id);
				}
			}

			$this->session->setFlashdata('toastr_msg', 'Pengembalian Mandiri berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Pengembalian berhasil disimpan',
			];
		} else {
			$this->session->setFlashdata('toastr_msg', 'Pengembalian Mandiri gagal disimpan. Silakan coba lagi');
			$this->session->setFlashdata('toastr_type', 'error');
			$response = [
				'error' => true,
				'message' => 'Pengembalian Mandiri gagal disimpan. Silakan coba lagi',
			];
		}

		return redirect()->back();
	}
}
