<?php

namespace Pengembalian\Controllers;

use \CodeIgniter\Files\File;

class Pengembalian extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $pengembalianModel;
	public $collectionModel;
	public $uploadPath;
	public $modulePath;

	function __construct()
	{
		$this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/pengembalian/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();



		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
	}
	public function index()
	{
		$carts = get_cart_return();
		$this->data['carts'] = $carts;

		$this->data['title'] = 'Pengembalian';
		echo view('Pengembalian\Views\list', $this->data);
	}

	public function create()
	{
		$member_no = $this->request->getGet('member_no') ?? '';
		$carts = get_cart_return($member_no);
		$loan_cart = count($carts);

		$this->data['member_no'] = $member_no;
		$this->data['carts'] = $carts;
		$this->data['loan_cart'] = $loan_cart;

		if (!empty($member_no)) {
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			$jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $member->JenisAnggota_id . '"', 'data');
			$max_loan_days = $jenis_anggota->MaxLoanDays ?? 3;
			$loan_count = get_loan_count($member->ID);
			$loan_limit = $jenis_anggota->MaxPinjamKoleksi;

			$collection_loan_id = $this->request->getPost('collection_loan_id');
			$loan = $this->request->getPost('loan_date');
			$loan_date = new \DateTime($loan);
			$due = new \DateTime($loan);
			$due_date = $due->add(new \DateInterval('P' . $max_loan_days . 'D'));

			$this->data['member'] = $member;
			$this->data['jenis_anggota'] = $jenis_anggota;
			$this->data['collection_loan_id'] = $collection_loan_id;
			$this->data['loan_count'] = $loan_count;
			$this->data['loan_limit'] = $loan_limit;
		}

		$this->validation->setRule('member_no', 'Nomor Anggota', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			if (($loan_cart + $loan_count) > $loan_limit) {
				set_message('toastr_msg', 'Koleksi gagal disimpan, melebihi Limit Jumlah Pengembalian');
				set_message('toastr_type', 'error');
				return redirect()->back();
			}

			$i = 0;
			$save_collection_loans = array();
			$save_collection_loan_items = array();
			foreach ($carts as $row) {
				if ($i == 0) {
					$save_collection_loans = array(
						'ID' => $collection_loan_id,
						'Member_id' => $row->options->member->ID,
						'LocationLibrary_id' => $row->options->collection->Location_Library_id,
						'CreateBy' => user_id(),
						'CreateTerminal' => $this->request->getIPAddress(),
					);
				}

				$save_collection_loan_items[] = array(
					'CollectionLoan_id' => $collection_loan_id,
					'LoanDate' => date_format($loan_date, "Y/m/d H:i:s"),
					'DueDate' => date_format($due_date, "Y/m/d H:i:s"),
					'LoanStatus' => 'Loan',
					'Collection_id' => $row->options->collection->ID,
					'member_id' => $row->options->member->ID,
					'CreateBy' => user_id(),
					'CreateTerminal' => $this->request->getIPAddress(),
				);
				$i++;
			}

			if (!empty($save_collection_loans)) {
				$save_collection_loans['CollectionCount'] = $this->cart->totalItems();
				try {
					$cl = $this->collectionLoanModel->find($collection_loan_id);
					if (!empty($cl)) {
						$this->collectionLoanModel->update($collection_loan_id, [
							'CollectionCount' => $cl->CollectionCount + $loan_cart,
							'UpdateBy' => user_id(),
							'UpdateTerminal' => $this->request->getIPAddress(),
						]);
					} else {
						$this->collectionLoanModel->insert($save_collection_loans);
					}

					if (!empty($save_collection_loan_items)) {
						$this->collectionLoanItemModel->insertBatch($save_collection_loan_items);
					}

					$this->cart->destroy();

					set_message('toastr_msg', 'Koleksi berhasil disimpan ke Daftar Pengembalian');
					set_message('toastr_type', 'success');
				} catch (\Exception $e) {
					exit($e->getMessage());
					set_message('toastr_msg', 'Koleksi gagal disimpan ke Daftar Pengembalian');
					set_message('toastr_type', 'error');
				}
			}

			return redirect()->to('sirkulasi-pengembalian/create?member_no=' . $member_no);
		}

		$this->data['title'] = 'Tambah Pengembalian';
		echo view('Pengembalian\Views\add', $this->data);
	}

	public function do_return($id = null)
	{
		$carts = get_cart_return();
		$cli_update_data = array();
		if (!empty($id)) {
			$cli_update_data[] = array(
				'ID' => $id,
				'LoanStatus' => 'Return',
				'ActualReturn' => date('Y-m-d'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => $this->request->getIPAddress(),
			);
		} else {
			if (!empty($carts)) {
				foreach ($carts as $row) {
					$cli_update_data[] = array(
						'ID' => str_replace('R', '', $row->id),
						'LoanStatus' => 'Return',
						'ActualReturn' => date('Y-m-d'),
						'UpdateBy' => user_id(),
						'UpdateTerminal' => $this->request->getIPAddress(),
					);
				}
			}
		}

		$cli_update = $this->collectionLoanItemModel->updateBatch($cli_update_data, 'ID');
		if ($cli_update) {
			if (!empty($carts)) {
				foreach ($carts as $row) {
					$this->cart->remove($row->id);
				}
			}

			$this->session->setFlashdata('toastr_msg', 'Pengembalian berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Pengembalian berhasil disimpan',
			];
		} else {
			$this->session->setFlashdata('toastr_msg', 'Pengembalian gagal disimpan. Silakan coba lagi');
			$this->session->setFlashdata('toastr_type', 'error');
			$response = [
				'error' => true,
				'message' => 'Pengembalian gagal disimpan. Silakan coba lagi',
			];
		}

		return redirect()->back();
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('pengembalian');
		}
		$pengembalianDelete = $this->pengembalianModel->delete($id);
		if ($pengembalianDelete) {
			set_message('toastr_msg', 'Pengembalian berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('pengembalian');
		} else {
			set_message('toastr_msg', 'Pengembalian gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Pengembalian gagal dihapus');
			return redirect()->to('pengembalian');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$pengembalianUpdate = $this->pengembalianModel->update($id, array($field => $value));

		if ($pengembalianUpdate) {
			set_message('toastr_msg', 'Pengembalian berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Pengembalian gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/pengembalian');
	}

	public function cart_insert()
	{
		$IDs = $this->request->getvar('ID');
		foreach ($IDs as $ID) {
			$cli = get_ref_single('collectionloanitems', 'ID="' . $ID . '"', 'data');
			$member = get_ref_single('members', 'ID="' . $cli->member_id . '"', 'data');
			$collection = get_ref_single('collections', 'ID="' . $cli->Collection_id . '"', 'data');
			$catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');

			$this->cart->insert(array(
				'id'      => 'R' . $ID,
				'name'    => 'RETURN',
				'qty'     => 1,
				'price'   =>  0,
				'options' => array(
					'collection' 			=> $collection,
					'catalog' 				=> $catalog,
					'member' 				=> $member,
				),
			), 'R' . $ID);
		}

		set_message('toastr_msg', 'Berhasil ditambahkan ke Troli Pengembalian');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil ditambahkan ke Troli Pengembalian');

		return redirect()->back();
	}

	public function cart_remove($id = null)
	{
		$this->cart->remove($id);

		set_message('toastr_msg', 'Berhasil dihapus dari Troli Pengembalian');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil dihapus dari Troli Pengembalian');

		return redirect()->back();
	}

	public function cart_destroy()
	{
		$carts = get_cart_return();
		foreach ($carts as $row) {
			$this->cart->remove($row->id);
		}

		set_message('toastr_msg', 'Troli Pengembalian berhasil dikosongkan');
		set_message('toastr_type', 'success');
		set_message('message', 'Troli Pengembalian berhasil dikosongkan');

		return redirect()->back();
	}
}
