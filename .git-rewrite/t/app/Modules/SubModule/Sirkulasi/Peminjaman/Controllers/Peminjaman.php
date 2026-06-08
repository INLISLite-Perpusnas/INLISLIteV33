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
	public $db;

	function __construct()
	{
		$this->peminjamanModel = new \Peminjaman\Models\PeminjamanModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->cart = new \App\Libraries\Cart();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->db=db_connect();
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

	public function createold()
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

        // Get loan configuration based on priority
        $loan_config = $this->getLoanConfiguration($member, $carts);
		
        
        $max_loan_days = $loan_config['max_loan_days'];
        $max_pinjam_koleksi = $loan_config['max_pinjam_koleksi'];
        $config_source = $loan_config['source'];
        
        $loan_count = get_loan_count($member->ID);

        $collection_loan_id = $this->request->getPost('collection_loan_id');
        $loan = $this->request->getPost('loan_date');
        $loan_date = new \DateTime($loan);
        $due = new \DateTime($loan);
        $due_date = $due->add(new \DateInterval('P' . $max_loan_days . 'D'));

        $this->data['member'] = $member;
        $this->data['jenis_anggota'] = $jenis_anggota;
        $this->data['collection_loan_id'] = $collection_loan_id;
        $this->data['loan_count'] = $loan_count;
        $this->data['loan_limit'] = $max_pinjam_koleksi;
        $this->data['config_source'] = $config_source;
    }

    $this->validation->setRule('member_no', 'Nomor Anggota', 'required');
    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
        
        // Check loan limit
        if (($loan_cart + $loan_count) > $max_pinjam_koleksi) {
            set_message('toastr_msg', 'Koleksi gagal disimpan, melebihi Limit Jumlah Peminjaman (' . $max_pinjam_koleksi . ' dari ' . $config_source . ')');
            set_message('toastr_type', 'error');
            return redirect()->back();
        }

        $i = 0;
        $save_collection_loans = array();
        $save_collection_loan_items = array();
        $collection_ids_to_update = array();

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

                    if (!empty($collection_ids_to_update)) {
                        $this->collectionModel->whereIn('ID', $collection_ids_to_update)
                                              ->set(['Status_id' => 5])
                                              ->update();
                    }
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

/**
 * Get loan configuration based on priority:
 * 1. Peminjaman hari (Day-based rules)
 * 2. Peminjaman tanggal (Date range rules) 
 * 3. Jenis bahan pustaka (Material type - worksheets)
 * 4. Jenis anggota (Member type)
 */
private function getLoanConfiguration($member, $carts)
{
    $today = new \DateTime();
    $day_index = $today->format('N'); // 1=Monday, 7=Sunday
    
    // 1. Check peminjaman_hari (Day-based rules) - Highest Priority
    $day_rule = $this->db->table('peraturan_peminjaman_hari')
        ->where('DayIndex', $day_index)
        ->where('active', 1)
        ->get()
        ->getRow();
        
    if ($day_rule) {
        return [
            'max_loan_days' => $day_rule->MaxLoanDays,
            'max_pinjam_koleksi' => $day_rule->MaxPinjamKoleksi,
            'source' => 'Peraturan Hari (' . $this->getDayName($day_index) . ')'
        ];
    }
    
    // 2. Check peminjaman_tanggal (Date range rules)
    $today_formatted = $today->format('Y-m-d H:i:s');
    $date_rule = $this->db->table('peraturan_peminjaman_tanggal')
        ->where('TanggalAwal <=', $today_formatted)
        ->where('TanggalAkhir >=', $today_formatted)
        ->where('active', 1)
        ->get()
        ->getRow();
        
    if ($date_rule) {
        return [
            'max_loan_days' => $date_rule->MaxLoanDays,
            'max_pinjam_koleksi' => $date_rule->MaxPinjamKoleksi,
            'source' => 'Peraturan Tanggal'
        ];
    }
    
    // 3. Check jenis bahan pustaka (Material type - worksheets)
    $worksheet_limits = $this->getWorksheetLimits($carts);
    if ($worksheet_limits) {
        // Check if current loan exceeds worksheet limit
        $current_loan_count = get_loan_count($member->ID);
        $total_requested = count($carts) + $current_loan_count;
        
        if ($total_requested > $worksheet_limits['max_pinjam_koleksi']) {
            // Return worksheet limit for validation
            return [
                'max_loan_days' => $worksheet_limits['max_loan_days'],
                'max_pinjam_koleksi' => $worksheet_limits['max_pinjam_koleksi'],
                'source' => 'Jenis Bahan Pustaka (' . ($worksheet_limits['worksheet_name'] ?? 'Unknown') . ')'
            ];
        }
    }
    
    // 4. Default to jenis_anggota (Member type) - Lowest Priority
    $jenis_anggota = get_ref_single('jenis_anggota', 'id="' . $member->JenisAnggota_id . '"', 'data');
    
    return [
        'max_loan_days' => $jenis_anggota->MaxLoanDays ?? 3,
        'max_pinjam_koleksi' => $jenis_anggota->MaxPinjamKoleksi ?? 1000,
        'source' => 'Jenis Anggota (' . $jenis_anggota->jenisanggota . ')'
    ];
}

/**
 * Get worksheet limits for collections in cart
 */
private function getWorksheetLimits($carts)
{
    if (empty($carts)) {
        return null;
    }
    
	// Ambil Catalog_id dari collections
	$catalog_ids = [];
	foreach ($carts as $cart) {
		if (isset($cart->options->collection->Catalog_id)) {
			$catalog_ids[] = $cart->options->collection->Catalog_id;
		}
	}

	// Query ke tabel catalogs untuk mendapatkan Worksheet_id
	$catalogs = $this->db->table('catalogs')
		->select('Worksheet_id')
		->whereIn('ID', array_unique($catalog_ids))
		->where('Worksheet_id IS NOT NULL')
		->get()
		->getResult();

	// Ambil worksheet_ids dari hasil query catalogs
	$worksheet_ids = [];
	foreach ($catalogs as $catalog) {
		if ($catalog->Worksheet_id) {
			$worksheet_ids[] = $catalog->Worksheet_id;
		}
	}
    
    if (empty($worksheet_ids)) {
        return null;
    }
    
    // Get the most restrictive worksheet rule
    $worksheet = $this->db->table('worksheets')
        ->whereIn('ID', array_unique($worksheet_ids))
        ->where('active', 1)
        ->where('MaxPinjamKoleksi >', 0) // Only consider worksheets with limits
        ->orderBy('MaxPinjamKoleksi', 'ASC') // Most restrictive first
        ->get()
        ->getRow();
    
    if ($worksheet) {
        return [
            'max_loan_days' => $worksheet->MaxLoanDays,
            'max_pinjam_koleksi' => $worksheet->MaxPinjamKoleksi
        ];
    }
    
    return null;
}

/**
 * Get day name from day index
 */
private function getDayName($day_index)
{
    $days = [
        1 => 'Senin',
        2 => 'Selasa', 
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu'
    ];
    
    return $days[$day_index] ?? 'Unknown';
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
    
    if (!$member) {
        set_message('toastr_msg', 'Member tidak ditemukan');
        set_message('toastr_type', 'error');
        return redirect()->back();
    }
    
    $IDs = $this->request->getvar('ID');
    
    if (empty($IDs)) {
        set_message('toastr_msg', 'Tidak ada koleksi yang dipilih');
        set_message('toastr_type', 'error');
        return redirect()->back();
    }
    
    // Get current cart items
    $current_carts = get_cart_loan($member_no);
    $current_cart_count = count($current_carts);
    
    // Get current loan count for member
    $current_loan_count = get_loan_count($member->ID);
    
    // Prepare collections to be added
    $collections_to_add = [];
    foreach ($IDs as $ID) {
        $collection = get_ref_single('collections', 'ID="' . $ID . '"', 'data');
        if ($collection) {
            $catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');
            $collections_to_add[] = (object)[
                'options' => (object)[
                    'collection' => $collection,
                    'catalog' => $catalog,
                    'member' => $member
                ]
            ];
        }
    }
    
    // Simulate cart with new items for limit checking
    $simulated_cart = array_merge($current_carts, $collections_to_add);
    
    // Get loan configuration based on priority
    $loan_config = $this->getLoanConfiguration($member, $simulated_cart);
    $max_pinjam_koleksi = $loan_config['max_pinjam_koleksi'];
    $config_source = $loan_config['source'];
    
    // Check if adding these items would exceed the limit
    $total_items_after_add = $current_cart_count + count($IDs) + $current_loan_count;
    
    if ($total_items_after_add > $max_pinjam_koleksi) {
        $available_slots = $max_pinjam_koleksi - ($current_cart_count + $current_loan_count);
        $available_slots = max(0, $available_slots);
        
        set_message('toastr_msg', 'Gagal menambahkan koleksi. Melebihi limit peminjaman (' . $max_pinjam_koleksi . ' dari ' . $config_source . '). Tersisa ' . $available_slots . ' slot');
        set_message('toastr_type', 'error');
        return redirect()->back();
    }
    
    // If limit check passes, add items to cart
    $added_count = 0;
    foreach ($IDs as $ID) {
        $collection = get_ref_single('collections', 'ID="' . $ID . '"', 'data');
        
        if (!$collection) {
            continue;
        }
        
       
        
        $catalog = get_ref_single('catalogs', 'ID="' . $collection->Catalog_id . '"', 'data');
        
        // Check if item already in cart
      
        
        $this->cart->insert(array(
            'id'      => 'L' . $ID,
            'name'    => 'LOAN',
            'qty'     => 1,
            'price'   => 0,
            'options' => array(
                'collection' => $collection,
                'catalog'    => $catalog,
                'member'     => $member,
            ),
        ), 'L' . $ID);
        
        $added_count++;
    }
    
    if ($added_count > 0) {
        set_message('toastr_msg', $added_count . ' koleksi berhasil ditambahkan ke Troli Peminjaman');
        set_message('toastr_type', 'success');
        set_message('message', $added_count . ' koleksi berhasil ditambahkan ke Troli Peminjaman');
    } else {
        set_message('toastr_msg', 'Tidak ada koleksi yang dapat ditambahkan');
        set_message('toastr_type', 'warning');
    }
    
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