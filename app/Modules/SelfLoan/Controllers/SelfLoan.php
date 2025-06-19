<?php

namespace SelfLoan\Controllers;

use \CodeIgniter\Files\File;

class SelfLoan extends \App\Controllers\BaseController
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
	public $db;

	function __construct()
	{
		$this->language = \Config\Services::language();
		$this->language->setLocale('id');
		$this->db = \Config\Database::connect('data');
        $this->session = \Config\Services::session();
		if (!$this->session->has('cart')) {
            $this->session->set('cart', []);
        }

		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
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
		// helper('member');
		helper('lokasiruang');
		helper('home_helper');
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
		if (!isset($_COOKIE['Location_id'])) {
			return redirect()->to($prefix . '/lokasi');
		}
		$cookie_location=$_COOKIE['Location_id'];

		$branch = $this->session->get('branch');
		if (!empty($branch)) {
			$this->data['branch'] = $branch;
			$this->data['prefix'] = $branch->slug;
			$this->data['branch_id'] = $branch->ID;
		} else {
			return redirect()->to('/search');
		}


		$member_no = $this->request->getGet('member_no') ?? '';
		$branch_id =  $this->session->get('branch')->ID;
		
		$carts = session()->get('cart') ?? [];
		
		$loan_cart = count($carts);
		$this->data['member_no'] = $member_no;
		$this->data['carts'] = $carts;
		$this->data['loan_cart'] = $loan_cart;

		if (!empty($member_no)) {
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			
			if (!empty($member)) {
				$jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $member->JenisAnggota_id . '"', 'data');
				$max_loan_days = $jenis_anggota->MaxLoanDays ?? 3;
				$loan_count = get_loan_count($member->ID);
			
				$loan_limit = $jenis_anggota->MaxPinjamKoleksi;
				

				$collection_loan = get_ref_single('collectionloans', 'ID IS NOT NULL', 'data');
				$increment = ((int) substr($collection_loan->ID, -5)) + 1;
				$collection_loan_id = get_pad_number($increment, date('ymd'), 5);
				
				
				$loan = date('y-m-d');
				$loan_date = new \DateTime($loan);
				$due = new \DateTime($loan);
				$due_date = $due->add(new \DateInterval('P' . $max_loan_days . 'D'));

				$this->data['member'] = $member;
				$this->data['jenis_anggota'] = $jenis_anggota;
				$this->data['collection_loan_id'] = $collection_loan_id;
				$this->data['loan_count'] = $loan_count;
				$this->data['loan_limit'] = $loan_limit;
			} else {
				set_message('toastr_msg', 'Nomor Anggota tidak ditemukan');
				set_message('toastr_type', 'warning');
				return redirect()->back();
			}
		}

	
		$this->data['title'] = 'Peminjaman Mandiri';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('SelfLoan\Views\add', $this->data);
	}

	public function create($prefix = '')
	{
		$branch = $this->session->get('branch');
        $prefix=$branch->slug;
		
		$member_no = $this->request->getPost('member_no') ?? '';
		$branch_id =  $this->session->get('branch')->ID;
		$carts = session()->get('cart') ?? [];
		$loan_cart = count($carts);
		$this->data['member_no'] = $member_no;
		$this->data['carts'] = $carts;
		$this->data['loan_cart'] = $loan_cart;

		if (!empty($member_no)) {
			$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
			if (!empty($member)) {
				$jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $member->JenisAnggota_id . '"', 'data');
				$max_loan_days = $jenis_anggota->MaxLoanDays ?? 3;
				$loan_count = get_loan_count($member->ID);
				$loan_limit = $jenis_anggota->MaxPinjamKoleksi;

				$collection_loan = get_ref_single('collectionloans', 'ID IS NOT NULL', 'data');
				$increment = ((int) substr($collection_loan->ID, -5)) + 1;
				$collection_loan_id = get_pad_number($increment, date('ymd'), 5);
				
				
				$loan = date('y-m-d');
				$loan_date = new \DateTime($loan);
				$due = new \DateTime($loan);
				$due_date = $due->add(new \DateInterval('P' . $max_loan_days . 'D'));

				$this->data['member'] = $member;
				$this->data['jenis_anggota'] = $jenis_anggota;
				$this->data['collection_loan_id'] = $collection_loan_id;
				$this->data['loan_count'] = $loan_count;
				$this->data['loan_limit'] = $loan_limit;
			} else {
				set_message('toastr_msg', 'Nomor Anggota tidak ditemukan');
				set_message('toastr_type', 'warning');
				return redirect()->back();
			}
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
			foreach ($carts as $row) {
				if ($i == 0) {
					$save_collection_loans = array(
						'ID' => $collection_loan_id,
						'Member_id' => $member->ID,
						'LocationLibrary_id' => $row['Location_Library_id'],
						'Branch_id'=>$branch_id,
						'CreateBy' => user_id(),
						'CreateTerminal' => $this->request->getIPAddress(),
					);
				}
			

				$save_collection_loan_items[] = array(
					'CollectionLoan_id' => $collection_loan_id,
					'LoanDate' => date_format($loan_date, "Y/m/d H:i:s"),
					'DueDate' => date_format($due_date, "Y/m/d H:i:s"),
					'LoanStatus' => 'Loan',
					'Branch_id'=>$branch_id,
					'Collection_id' => $row['ID'],
					'member_id' => $member->ID,
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
							'CollectionCount' => $cl['CollectionCount'] + $loan_cart,
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
					$this->session->set('cart', []);

					

					set_message('toastr_msg', 'Koleksi berhasil disimpan ke Daftar Peminjaman');
					set_message('toastr_type', 'success');
				} catch (\Exception $e) {
					exit($e->getMessage());
					set_message('toastr_msg', 'Koleksi gagal disimpan ke Daftar Peminjaman');
					set_message('toastr_type', 'error');
				}
			}

			return redirect()->to($prefix . '/peminjaman-mandiri?member_no=' . $member_no);
		}

		
	}
	public function checkBarcode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        $json = $this->request->getJSON();
        $barcode = $json->barcode ?? '';

        if (empty($barcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor barcode tidak boleh kosong'
            ]);
        }

        // Cari koleksi berdasarkan barcode dan status
        $collection = $this->collectionModel
            ->where('NomorBarcode', $barcode)
            ->where('Status_id', 1)
            ->first();

        if (!$collection) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Koleksi tidak ditemukan atau tidak tersedia'
            ]);
        }

        // Dapatkan data catalog terkait
        $catalogModel = model('CatalogModel');
        $catalog = $catalogModel->find($collection->Catalog_id);

        if (!$catalog) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data katalog tidak ditemukan'
            ]);
        }

        // Cek apakah item sudah ada di cart
        $currentCart = $this->session->get('cart') ?? [];
        foreach ($currentCart as $item) {
            if ($item['collection']->NomorBarcode === $barcode) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Item sudah ada di dalam troli'
                ]);
            }
        }

        // Generate unique cart item ID
        $cartId = uniqid();

        // Buat item cart baru
        $cartItem = [
            'id' => $cartId,
            'collection' => $collection,
            'options' => [
                'collection' => $collection,
                'catalog' => $catalog
            ]
        ];

        // Tambahkan ke session cart
        $currentCart[$cartId] = $cartItem;
        $this->session->set('cart', $currentCart);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Koleksi berhasil ditambahkan ke troli'
        ]);
    }
	public function cart_insert($prefix='')
	{
		

		$member_no = $this->request->getGet('member_no');
		
		$member = get_ref_single('members', 'MemberNo="' . $member_no . '"', 'data');
		
		$IDs = $this->request->getGet('ID');
		
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

		return redirect()->to(base_url('dispusip-sumut/peminjaman-mandiri') . '?member_no=1287316153891008');

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

	public function check_barcode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        $barcode = $this->request->getJSON()->barcode;

        if (empty($barcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode cannot be empty'
            ]);
        }

        // Query to check barcode and get title from catalog
        $query = $this->db->table('collections')
            ->select('collections.NomorBarcode,collections.Location_Library_id,collections.ID, catalogs.Title, catalogs.Publisher')
            ->join('catalogs', 'collections.Catalog_id = catalogs.ID')
            ->where('collections.NomorBarcode', $barcode)
            ->get();

        $result = $query->getRow();

        if (!$result) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode tidak ditemukan'
            ]);
        }

        // Get current cart from session
        $cart = $this->session->get('cart');

        // Check if item is already in cart
        foreach ($cart as $item) {
            if ($item['NomorBarcode'] === $barcode) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Buku sudah ada dalam troli'
                ]);
            }
        }

        // Add new item to cart
        $cart[] = [
            'NomorBarcode' => $result->NomorBarcode,
            'Title' => $result->Title,
            'Publisher' => $result->Publisher,
			'Location_Library_id'=>$result->Location_Library_id,
			'ID'=>$result->ID
        ];

        // Update cart in session
        $this->session->set('cart', $cart);
		

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Buku berhasil ditambahkan ke troli',
            'data' => $result
        ]);
    }

    // Method to clear cart
    public function clear_cart()
    {
        $this->session->set('cart', []);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Troli berhasil dikosongkan'
        ]);
    }

    // Method to remove single item from cart
    public function remove_item()
    {
		$barcode = $this->request->getJSON()->barcode;
		

        if (empty($barcode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode cannot be empty'
            ]);
        }
        $cart = $this->session->get('cart');
        
        $cart = array_filter($cart, function($item) use ($barcode) {
            return $item['NomorBarcode'] !== $barcode;
        });
        
        $this->session->set('cart', array_values($cart));
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Item berhasil dihapus dari troli'
        ]);
    }
}
