<?php

namespace Peminjaman\Controllers;

use \CodeIgniter\Files\File;

class Peminjaman extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $peminjamanModel;
	public $uploadPath;
	public $modulePath;
	public $collectionModel;
	public $collectionLoanModel;
	public $collectionLoanItemModel;
	public $cart;

	function __construct()
	{
		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/peminjaman/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();

		helper('reference');
		helper('peminjaman');
		helper('member');
		helper('menu');
	}

	//mandiri
	public function mandiri()
	{
		$this->data['title'] = 'Peminjaman Mandiri';
		echo view('Peminjaman\Views\mandiri\index', $this->data);
	}


	public function index()
	{
		// $carts = json_decode(json_encode($this->cart->contents()), FALSE);
		// $carts_arr = get_object_array($carts,'id');

		// dd($carts_arr);
		$this->data['title'] = 'Daftar Peminjaman';
		echo view('Peminjaman\Views\list', $this->data);
	}

	public function create_loan(){
		
		echo view('Peminjaman\Views\create_loan');
	}

	public function create()
{
    $member_no = $this->request->getGet('member_no') ?? '';
    $carts = get_cart_loan($member_no);

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
            set_message('toastr_msg', 'Koleksi gagal disimpan, melebihi Limit Jumlah Peminjaman');
            set_message('toastr_type', 'error');
            return redirect()->back();
        }

        $i = 0;
        $save_collection_loans = array();
        $save_collection_loan_items = array();
        $collection_ids_to_update = array(); // Array to hold collection IDs for status update

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
            
            // Collect the collection ID for the status update
            $collection_ids_to_update[] = $row->options->collection->ID;
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

                    // --- START: Update collection status ---
                    if (!empty($collection_ids_to_update)) {
                        $this->collectionModel->whereIn('ID', $collection_ids_to_update)
                                              ->set(['Status_id' => 5]) // 5 represents 'Dipinjam'
                                              ->update();
                    }
                    // --- END: Update collection status ---
                }

                $this->cart->destroy();

                set_message('toastr_msg', 'Koleksi berhasil disimpan ke Daftar Peminjaman');
                set_message('toastr_type', 'success');
            } catch (\Exception $e) {
                exit($e->getMessage());
                set_message('toastr_msg', 'Koleksi gagal disimpan ke Daftar Peminjaman');
                set_message('toastr_type', 'error');
            }
        }

        return redirect()->to('sirkulasi-peminjaman/create?member_no=' . $member_no);
    }

    $this->data['title'] = 'Tambah Peminjaman';
    echo view('Peminjaman\Views\add', $this->data);
}


	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('sirkulasi-peminjaman');
		}

		$cli = $this->collectionLoanItemModel->find($id);
		$cli_delete = $this->collectionLoanItemModel->delete($id);
		if ($cli_delete) {
			$cl = $this->collectionLoanModel->find($cli->CollectionLoan_id);
			$this->collectionLoanModel->update($cli->CollectionLoan_id, [
				'CollectionCount' => $cl->CollectionCount - 1,
				'UpdateBy' => user_id(),
				'UpdateTerminal' => $this->request->getIPAddress(),
			]);

			set_message('toastr_msg', 'Peminjaman berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->back();
		} else {
			set_message('toastr_msg', 'Peminjaman gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Peminjaman gagal dihapus');
			return redirect()->back();
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$peminjamanUpdate = $this->peminjamanModel->update($id, array($field => $value));

		if ($peminjamanUpdate) {
			set_message('toastr_msg', 'Peminjaman berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Peminjaman gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/peminjaman');
	}

	public function cart_insert($member_no)
	{
		
		$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
		$IDs = $this->request->getvar('ID');
		foreach ($IDs as $ID) {
			$collection = get_ref_single('collections', 'ID="' . $ID . '"', 'data');
			$catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');

			$this->cart->insert(array(
				'id'      => 'L' . $ID,
				'name'    => 'LOAN',
				'qty'     => 1,
				'price'   =>  0,
				'options' => array(
					'collection' 			=> $collection,
					'catalog' 				=> $catalog,
					'member' 				=> $member,
				),
			), 'L' . $ID);
		}

		set_message('toastr_msg', 'Berhasil ditambahkan ke Troli Peminjaman');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil ditambahkan ke Troli Peminjaman');

		return redirect()->back();
	}

	public function cart_remove($id = null)
	{
		$this->cart->remove($id);

		set_message('toastr_msg', 'Berhasil dihapus dari Troli Peminjaman');
		set_message('toastr_type', 'success');
		set_message('message', 'Berhasil dihapus dari Troli Peminjaman');

		return redirect()->back();
	}

	public function cart_destroy()
	{
		$this->cart->destroy();

		set_message('toastr_msg', 'troli Peminjaman berhasil dikosongkan');
		set_message('toastr_type', 'success');
		set_message('message', 'troli Peminjaman berhasil dikosongkan');

		return redirect()->back();
	}
}