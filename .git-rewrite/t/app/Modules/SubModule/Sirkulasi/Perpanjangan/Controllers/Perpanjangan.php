<?php

namespace Perpanjangan\Controllers;

use \CodeIgniter\Files\File;

class Perpanjangan extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $perpanjanganModel;
	public $uploadPath;
	public $modulePath;

	function __construct()
	{
		$this->perpanjanganModel = new \Perpanjangan\Models\PerpanjanganModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->collectionLoanExtendModel = new \Peminjaman\Models\CollectionLoanExtendModel();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/perpanjangan/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();



		helper('reference');
		helper('peminjaman');
		helper('perpanjangan');
	}
	public function index()
	{
		$carts = get_cart_extend();
		$this->data['carts'] = $carts;

		$this->data['title'] = 'Perpanjangan';
		echo view('Perpanjangan\Views\list', $this->data);
	}

	public function do_extend($id = null)
	{
		$carts = get_cart_extend();
		$cle_save_data = array();
		if (!empty($id)) {
			$collectionloanitem = get_ref_single('collectionloanitems', 'ID="' . $id . '"', 'data');
			$member = get_ref_single('members', 'ID="' . $collectionloanitem->member_id . '"', 'data');
			$collection = get_ref_single('collections', 'ID="' . $collectionloanitem->Collection_id . '"', 'data');
			$catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');

			$jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $member->JenisAnggota_id . '"', 'data');
			$max_extend_days = $jenis_anggota->DayPerpanjang ?? 3;

			$extend = date('Y-m-d');
			$extend_date = new \DateTime($extend);
			$due = new \DateTime($extend);
			$due_date = $due->add(new \DateInterval('P' . $max_extend_days . 'D'));

			$cle_save_data[] = array(
				'CollectionLoan_id' => $collectionloanitem->CollectionLoan_id,
				'CollectionLoanItem_id' => str_replace('E', '', $id),
				'Collection_id' =>  $collection->ID,
				'Member_id' => $member->ID,
				'DateExtend' => date_format($extend_date, "Y/m/d H:i:s"),
				'DueDateExtend' => date_format($due_date, "Y/m/d H:i:s"),
				'Branch_id' => branch_id(),
				'CreateBy' => user_id(),
				'CreateTerminal' => $this->request->getIPAddress(),
			);
		} else {
			if (!empty($carts)) {
				foreach ($carts as $row) {
					$jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $row->options->member->JenisAnggota_id . '"', 'data');
					$max_extend_days = $jenis_anggota->DayPerpanjang ?? 3;

					$extend = date('Y-m-d');
					$extend_date = new \DateTime($extend);
					$due = new \DateTime($extend);
					$due_date = $due->add(new \DateInterval('P' . $max_extend_days . 'D'));

					$cle_save_data[] = array(
						'CollectionLoan_id' => $row->options->collectionloanitem->CollectionLoan_id,
						'CollectionLoanItem_id' => str_replace('E', '', $row->id),
						'Collection_id' =>  $row->options->collection->ID,
						'Member_id' => $row->options->member->ID,
						'DateExtend' => date_format($extend_date, "Y/m/d H:i:s"),
						'DueDateExtend' => date_format($due_date, "Y/m/d H:i:s"),
						'Branch_id' => branch_id(),
						'CreateBy' => user_id(),
						'CreateTerminal' => $this->request->getIPAddress(),
					);
				}
			}
		}

		$cle_save = $this->collectionLoanExtendModel->insertBatch($cle_save_data);
		if ($cle_save) {
			if (!empty($carts)) {
				foreach ($carts as $row) {
					$this->cart->remove($row->id);
				}
			}

			$this->session->setFlashdata('toastr_msg', 'Perpajangan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Perpajangan berhasil disimpan',
			];
		} else {
			$this->session->setFlashdata('toastr_msg', 'Perpajangan gagal disimpan. Silakan coba lagi');
			$this->session->setFlashdata('toastr_type', 'error');
			$response = [
				'error' => true,
				'message' => 'Perpajangan gagal disimpan. Silakan coba lagi',
			];
		}

		return redirect()->back();
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('perpanjangan');
		}
		$perpanjanganDelete = $this->perpanjanganModel->delete($id);
		if ($perpanjanganDelete) {
			set_message('toastr_msg', 'Perpanjangan berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('perpanjangan');
		} else {
			set_message('toastr_msg', 'Perpanjangan gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Perpanjangan gagal dihapus');
			return redirect()->to('perpanjangan');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$perpanjanganUpdate = $this->perpanjanganModel->update($id, array($field => $value));

		if ($perpanjanganUpdate) {
			set_message('toastr_msg', 'Perpanjangan berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Perpanjangan gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/perpanjangan');
	}

	public function cart_insert()
	{
		$IDs = $this->request->getvar('ID');
		foreach ($IDs as $ID) {
			$collectionloanitem = get_ref_single('collectionloanitems', 'ID="' . $ID . '"', 'data');
			$member = get_ref_single('members', 'ID="' . $collectionloanitem->member_id . '"', 'data');
			$collection = get_ref_single('collections', 'ID="' . $collectionloanitem->Collection_id . '"', 'data');
			$catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');

			$this->cart->insert(array(
				'id'      => 'E' . $ID,
				'name'    => 'EXTEND',
				'qty'     => 1,
				'price'   =>  0,
				'options' => array(
					'collection' 			=> $collection,
					'catalog' 				=> $catalog,
					'member' 				=> $member,
					'collectionloanitem' 	=> $collectionloanitem,
				),
			), 'E' . $ID);
		}

		set_message('toastr_msg', 'Berhasil ditambahkan ke Troli Perpajangan');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil ditambahkan ke Troli Perpajangan');

		return redirect()->back();
	}

	public function cart_remove($id = null)
	{
		$this->cart->remove($id);

		set_message('toastr_msg', 'Berhasil dihapus dari Troli Perpajangan');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil dihapus dari Troli Perpajangan');

		return redirect()->back();
	}

	public function cart_destroy()
	{
		$carts = get_cart_extend();
		foreach ($carts as $row) {
			$this->cart->remove($row->id);
		}

		set_message('toastr_msg', 'Troli Perpajangan berhasil dikosongkan');
		set_message('toastr_type', 'success');
		set_message('message', 'Troli Perpajangan berhasil dikosongkan');

		return redirect()->back();
	}
}
