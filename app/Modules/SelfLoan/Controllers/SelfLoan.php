<?php

namespace SelfLoan\Controllers;

class SelfLoan extends \App\Controllers\BaseController
{
    protected $db;
    protected $language;
    protected $data = [];
    protected $session;
    
    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');
        
        $this->db = \Config\Database::connect('data');
        $this->session = service('session');
        
        helper('reference');
        helper('peminjaman');
        helper('member');
        helper('selfloan');
    }

    public function index()
    {
        $this->data['title'] = 'Peminjaman Mandiri';
        // Get parameters from URL
        $memberNo = $this->request->getGet('MemberNo');
        $nomorBarcode = $this->request->getGet('NomorBarcode');

        
        // Initialize data
        $this->data['memberNo'] = $memberNo;
        $this->data['nomorBarcode'] = $nomorBarcode;
        $this->data['memberData'] = null;
        $this->data['bookData'] = null;
        $this->data['selectedBooks'] = [];
        $this->data['errorMessage'] = '';
        $this->data['successMessage'] = '';
        
        // Get location data from cookie
        $locationId = $this->request->getCookie('Location_id');
        if (!$locationId) {
            return redirect()->to('buku-tamu/lokasi');
        }
        
        // Process member validation
        if ($memberNo) {
          
            $memberResult = $this->validateMember($memberNo);
          
            if ($memberResult['success']) {
                $this->data['memberData'] = $memberResult['data'];
                
                // Get current selected books from session
                $sessionKey = 'self_loan_books_' . $memberResult['data']['id'];
                $this->data['selectedBooks'] = $this->session->get($sessionKey) ?? [];
                
                // Process book validation if barcode provided
                if ($nomorBarcode) {
                    $bookResult = $this->validateBook($nomorBarcode, $memberResult['data']);
                    if ($bookResult['success']) {
                        // Add book to session
                        $this->addBookToSession($bookResult['data'], $sessionKey);
                        $this->data['selectedBooks'] = $this->session->get($sessionKey);
                        $this->data['successMessage'] = 'Buku "' . $bookResult['data']['title'] . '" berhasil ditambahkan';
                        
                        // Redirect to remove barcode from URL
                        return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo));
                    } else {
                        $this->data['errorMessage'] = $bookResult['message'];
                    }
                }
            } else {
                $this->data['errorMessage'] = $memberResult['message'];
            }
        }
        
        return view('SelfLoan\Views\simple_view', $this->data);
    }
    
   private function validateMember($memberNo)
    {
    try {
        // Clean and validate member number
        $memberNo = trim($memberNo);
        if (empty($memberNo)) {
            return ['success' => false, 'message' => 'Nomor anggota harus diisi'];
        }
        // Query member data
        $member = $this->db->table('members as m')
            ->select('m.ID, m.MemberNo, m.Fullname, m.Email, m.Phone, 
                     m.StatusAnggota_id, m.EndDate, m.JenisAnggota_id, m.Branch_id,
                     ja.jenisanggota as JenisAnggota_name, ja.MaxLoanDays, ja.MaxPinjamKoleksi,
                     sa.Nama as StatusAnggota_name')
            ->join('jenis_anggota as ja', 'ja.ID = m.JenisAnggota_id', 'left')
            ->join('status_anggota as sa', 'sa.ID = m.StatusAnggota_id', 'left')
            ->where('m.MemberNo', $memberNo)
            ->get()
            ->getRow();
        if (!$member) {
            return ['success' => false, 'message' => 'Anggota tidak ditemukan atau tidak aktif'];
        }
        
        // Check member status
        if ($member->StatusAnggota_id != 3) { // Assuming 3 = active status
            return ['success' => false, 'message' => 'Status anggota tidak aktif'];
        }
        
        // Check membership expiry
        if ($member->EndDate && strtotime($member->EndDate) < time()) {
            return ['success' => false, 'message' => 'Keanggotaan sudah berakhir'];
        }
        
        // Get current location from cookie
        $locationId = $this->request->getCookie('Location_id');
        
        // Check member location authorization
        $member_loc = $this->db->table('memberloanauthorizelocation')
            ->select('LocationLoan_id')
            ->where('Member_id', $member->ID)
            ->get()
            ->getResult();
        
        $member_loc_arr = [];
        foreach ($member_loc as $loc) {
            $member_loc_arr[] = $loc->LocationLoan_id;
        }
        if (!empty($member_loc_arr)) {
            // Get location library ID from current location
            $current_location = $this->db->table('locations')
                ->select('LocationLibrary_id')
                ->where('ID', $locationId)
                ->get()
                ->getRow();
            
            if ($current_location && !in_array($current_location->LocationLibrary_id, $member_loc_arr)) {
                return ['success' => false, 'message' => 'Anda tidak memiliki akses peminjaman di lokasi ini, silahkan hubungi petugas'];
            }
        }
        
    
        // Get current loans count
        $currentLoans = $this->db->table('collectionloanitems')
            ->where('member_id', $member->ID)
            ->where('LoanStatus', 'Loan')
            ->countAllResults();
          
          
        
        // Get loan configuration based on priority
        $loan_config = $this->getLoanConfiguration($member, []);
        $maxLoans = $loan_config['max_pinjam_koleksi'];
        $loanDays = $loan_config['max_loan_days'];
        $configSource = $loan_config['source'];
        
        $remainingLoans = $maxLoans - $currentLoans;
        
        if ($remainingLoans <= 0) {
            return ['success' => false, 'message' => 'Kuota peminjaman sudah penuh (' . $maxLoans . ' dari ' . $configSource . ')'];
        }
        
        return [
            'success' => true,
            'data' => [
                'id' => $member->ID,
                'member_no' => $member->MemberNo,
                'fullname' => $member->Fullname,
                'email' => $member->Email,
                'phone' => $member->Phone,
                'current_loans' => $currentLoans,
                'max_loans' => $maxLoans,
                'remaining_loans' => $remainingLoans,
                'loan_days' => $loanDays,
                'branch_id' => $member->Branch_id,
                'config_source' => $configSource,
                'member_object' => $member,
                'authorized_locations' => $member_loc_arr,
                'jenis_anggota_id' => $member->JenisAnggota_id,
                'jenis_anggota_name' => $member->JenisAnggota_name,
                'status_anggota_id' => $member->StatusAnggota_id,
                'status_anggota_name' => $member->StatusAnggota_name,
                'end_date' => $member->EndDate
            ]
        ];
        
    } catch (\Exception $e) {
        log_message('error', 'Member validation error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
    }
    }
    
    private function validateBook($barcode, $memberData)
    {
        try {
            // Clean barcode
            $barcode = trim($barcode);
            if (empty($barcode)) {
                return ['success' => false, 'message' => 'Barcode harus diisi'];
            }
            
            // Check if book already in session
            $sessionKey = 'self_loan_books_' . $memberData['id'];
            $selectedBooks = $this->session->get($sessionKey) ?? [];
            
            foreach ($selectedBooks as $book) {
                if ($book['barcode'] === $barcode) {
                    return ['success' => false, 'message' => 'Buku sudah ditambahkan ke daftar'];
                }
            }
            
            // Query collection data
            $collection = $this->db->table('collections as col')
                ->select('col.*, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Worksheet_id,
                         loc.Name as Location_name, stat.Name as Status_name')
                ->join('catalogs as cat', 'cat.ID = col.Catalog_id', 'left')
                ->join('locations as loc', 'loc.ID = col.Location_id', 'left')
                ->join('collectionstatus as stat', 'stat.ID = col.Status_id', 'left')
                ->join('collectionrules as rule', 'rule.ID = col.Rule_id', 'left')
                ->where('col.NomorBarcode', $barcode)
                ->get()
                ->getRow();
            if (!$collection) {
                return ['success' => false, 'message' => 'Koleksi dengan barcode "' . $barcode . '" tidak ditemukan'];
            }
            
            // Create simulated cart for loan configuration check
            $simulatedCart = $selectedBooks;
            $simulatedCart[] = (object)[
                'options' => (object)[
                    'collection' => $collection,
                    'catalog' => (object)['Worksheet_id' => $collection->Worksheet_id],
                    'member' => $memberData['member_object']
                ]
            ];
            
            // Get updated loan configuration with new book
            $loan_config = $this->getLoanConfiguration($memberData['member_object'], $simulatedCart);
            $maxLoans = $loan_config['max_pinjam_koleksi'];
            $configSource = $loan_config['source'];
            
            // Check loan limit with new configuration
            $totalAfterAdd = count($selectedBooks) + 1 + $memberData['current_loans'];
            if ($totalAfterAdd > $maxLoans) {
                $available_slots = $maxLoans - ($memberData['current_loans'] + count($selectedBooks));
                $available_slots = max(0, $available_slots);
                return ['success' => false, 'message' => 'Melebihi limit peminjaman (' . $maxLoans . ' dari ' . $configSource . '). Tersisa ' . $available_slots . ' slot'];
            }
            
            // Check if collection can be loaned
            if ($collection->Rule_id != 1) {
                return ['success' => false, 'message' => 'Koleksi ini tidak dapat dipinjam'];
            }
        
            // Check collection status (assuming status_id 1 = available)
            if ($collection->Status_id != 1) {
                return ['success' => false, 'message' => 'Koleksi sedang tidak tersedia untuk dipinjam'];
            }
          
            // Check if already loaned
            $existingLoan = $this->db->table('collectionloanitems')
                ->where('Collection_id', $collection->ID)
                ->where('LoanStatus', 'Loan')
                ->get()
                ->getRow();
            
            if ($existingLoan) {
                return ['success' => false, 'message' => 'Koleksi sedang dipinjam oleh anggota lain'];
            }

            // Check member category authorization
            // Get member category authorization from database
            $member_cat_check = $this->db->table('memberloanauthorizecategory')
                ->where('Member_id', $memberData['id'])
                ->where('CategoryLoan_id', $collection->Category_id)
                ->get()
                ->getRow();
            
            // If member has category authorization records, check if this category is allowed
            $has_category_auth = $this->db->table('memberloanauthorizecategory')
                ->where('Member_id', $memberData['id'])
                ->countAllResults();
            
            if ($has_category_auth > 0 && !$member_cat_check) {
                return ['success' => false, 'message' => 'Anda tidak memiliki akses peminjaman pada jenis bahan pustaka ini'];
            }
            
            return [
                'success' => true,
                'data' => [
                    'id' => $collection->ID,
                    'barcode' => $collection->NomorBarcode,
                    'call_number' => $collection->CallNumber,
                    'title' => $collection->Title,
                    'author' => $collection->Author ?: 'Tidak diketahui',
                    'publisher' => $collection->Publisher ?: 'Tidak diketahui',
                    'publish_year' => $collection->PublishYear ?: '',
                    'location' => $collection->Location_name,
                    'worksheet_id' => $collection->Worksheet_id
                ]
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Book validation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
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
            $current_loan_count = $this->db->table('collectionloanitems')
                ->where('member_id', $member->ID)
                ->where('LoanStatus', 'Loan')
                ->where('active', 1)
                ->countAllResults();
            
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
        $jenis_anggota = $this->db->table('jenis_anggota')
            ->where('id', $member->JenisAnggota_id)
            ->get()
            ->getRow();
        
        return [
            'max_loan_days' => $jenis_anggota->MaxLoanDays ?? 3,
            'max_pinjam_koleksi' => $jenis_anggota->MaxPinjamKoleksi ?? 1000,
            'source' => 'Jenis Anggota (' . ($jenis_anggota->jenisanggota ?? 'Unknown') . ')'
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
        
        $catalog_ids = [];
        foreach ($carts as $cart) {
            if (isset($cart->options->collection->Catalog_id)) {
                $catalog_ids[] = $cart->options->collection->Catalog_id;
            }
        }
        
        if (empty($catalog_ids)) {
            return null;
        }
        
        // Get worksheet IDs from catalogs table
        $catalogs = $this->db->table('catalogs')
            ->select('Worksheet_id')
            ->whereIn('ID', array_unique($catalog_ids))
            ->where('Worksheet_id IS NOT NULL')
            ->get()
            ->getResult();
        
        if (empty($catalogs)) {
            return null;
        }
        
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
                'max_pinjam_koleksi' => $worksheet->MaxPinjamKoleksi,
                'worksheet_name' => $worksheet->Name ?? 'Unknown'
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
    
    private function addBookToSession($bookData, $sessionKey)
    {
        $selectedBooks = $this->session->get($sessionKey) ?? [];
        $selectedBooks[] = $bookData;
        $this->session->set($sessionKey, $selectedBooks);
    }
    
    public function removeBook()
    {
        $memberNo = $this->request->getGet('MemberNo');
        $bookIndex = $this->request->getGet('index');
        
        if (!$memberNo || !is_numeric($bookIndex)) {
            return redirect()->to('/peminjaman-mandiri');
        }
        
        // Get member data to create session key
        $memberResult = $this->validateMember($memberNo);
        if (!$memberResult['success']) {
            return redirect()->to('/peminjaman-mandiri');
        }
        
        $sessionKey = 'self_loan_books_' . $memberResult['data']['id'];
        $selectedBooks = $this->session->get($sessionKey) ?? [];
        
        if (isset($selectedBooks[$bookIndex])) {
            unset($selectedBooks[$bookIndex]);
            $selectedBooks = array_values($selectedBooks); // Re-index array
            $this->session->set($sessionKey, $selectedBooks);
        }
        
        return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo));
    }
    
   public function processLoan()
    {
        $memberNo = $this->request->getPost('MemberNo');
        
        if (!$memberNo) {
            return redirect()->to('/peminjaman-mandiri')->with('error', 'Data tidak valid');
        }
        
        // Validate member
        $memberResult = $this->validateMember($memberNo);
        if (!$memberResult['success']) {
            return redirect()->to('/peminjaman-mandiri')->with('error', $memberResult['message']);
        }
        
        $memberData = $memberResult['data'];
        $sessionKey = 'self_loan_books_' . $memberData['id'];
        $selectedBooks = $this->session->get($sessionKey) ?? [];
        
        if (empty($selectedBooks)) {
            return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo))
                   ->with('error', 'Tidak ada buku yang dipilih');
        }
        
        // Final loan configuration check
        $simulatedCart = [];
        foreach ($selectedBooks as $book) {
            $simulatedCart[] = (object)[
                'options' => (object)[
                    'collection' => (object)['Catalog_id' => 1], // Dummy for structure
                    'catalog' => (object)['Worksheet_id' => $book['worksheet_id'] ?? null],
                    'member' => $memberData['member_object']
                ]
            ];
        }
        
        $loan_config = $this->getLoanConfiguration($memberData['member_object'], $simulatedCart);
        $loanDays = $loan_config['max_loan_days'];
        $maxLoans = $loan_config['max_pinjam_koleksi'];
        
        // Final validation
        $totalLoan = count($selectedBooks) + $memberData['current_loans'];
        if ($totalLoan > $maxLoans) {
            return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo))
                   ->with('error', 'Melebihi limit peminjaman (' . $maxLoans . ' dari ' . $loan_config['source'] . ')');
        }
        
        // --- PERBAIKAN TRANSAKSI DIMULAI DI SINI ---
        // Gunakan transBegin() untuk transaksi manual
        $this->db->transBegin();
        
        try {
            $collection_loan = get_ref_single('collectionloans', 'ID IS NOT NULL','data');

            $lastNumber = $collection_loan ? (int) substr($collection_loan->ID, -5) : 0;
            $increment = $lastNumber + 1;

            $collection_loan_id = get_pad_number($increment, date('ymd'), 5);
            $loanDate = date('Y-m-d H:i:s');
            $dueDate = date('Y-m-d H:i:s', strtotime("+{$loanDays} days"));
            $locationId = $this->request->getCookie('Location_id') ?: 1;
            
            // Create collection loan record
            $loanData = [
                'ID' => $collection_loan_id,
                'CollectionCount' => count($selectedBooks),
                'LateCount' => 0,
                'ExtendCount' => 0,
                'LoanCount' => count($selectedBooks),
                'ReturnCount' => 0,
                'Member_id' => $memberData['id'],
                'Branch_id' => $memberData['branch_id'],
                'CreateBy' => 1, // System user
                'CreateDate' => $loanDate,
                'CreateTerminal' => $this->request->getIPAddress(),
                'LocationLibrary_id' => $locationId,
            ];
            
            // Cek apakah insert master berhasil
            if (!$this->db->table('collectionloans')->insert($loanData)) {
                $dbError = $this->db->error();
                throw new \Exception('Gagal insert collectionloans: ' . $dbError['message']);
            }
            
            
            // Process each collection
            foreach ($selectedBooks as $book) {
                // Create loan item
                $loanItemData = [
                    'CollectionLoan_id' => $collection_loan_id,
                    'LoanDate' => $loanDate,
                    'DueDate' => $dueDate,
                    'LoanStatus' => 'Loan',
                    'Collection_id' => $book['id'],
                    'member_id' => $memberData['id'], // Pastikan nama kolom di DB Anda memang huruf kecil 'member_id'
                    'CreateBy' => 1,
                    'CreateDate' => $loanDate,
                    'CreateTerminal' => $this->request->getIPAddress(),
                    'Branch_id' => $memberData['branch_id'],
                ];
             
                // Cek apakah insert item berhasil
                if (!$this->db->table('collectionloanitems')->insert($loanItemData)) {
                    $dbError = $this->db->error();
                    throw new \Exception('Gagal insert collectionloanitems (Buku ID: '.$book['id'].'): ' . $dbError['message']);
                }
                
                // Update collection status to "Dipinjam"
                if (!$this->db->table('collections')
                    ->where('ID', $book['id'])
                    ->update([
                        'Status_id' => 5, // Dipinjam status
                        'UpdateBy' => 1,
                        'UpdateDate' => $loanDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ])) {
                    $dbError = $this->db->error();
                    throw new \Exception('Gagal update collections: ' . $dbError['message']);
                }
            }
            
            // Cek status akhir transaksi sebelum di-commit
            if ($this->db->transStatus() === false) {
                throw new \Exception('Status transaksi false, dibatalkan oleh sistem.');
            }
            
            // Commit semua perubahan jika sukses
            $this->db->transCommit();
            
            // Clear session
            $this->session->remove($sessionKey);
            
            // Redirect to success page
            return redirect()->to('/peminjaman-mandiri/success?loan_id=' . $newLoanId);
            
        } catch (\Exception $e) {
            // Rollback jika terjadi kegagalan di titik manapun
            $this->db->transRollback();
            
            // Catat error sebenarnya ke file log
            log_message('error', 'Loan processing error: ' . $e->getMessage());
            
            // Menampilkan error database langsung ke user (Bagus untuk tahap development/debugging)
            return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo))
                   ->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
    
    public function success()
    {
        $this->data['title'] = 'Peminjaman Berhasil';
        $loanId = $this->request->getGet('loan_id');
        
        if (!$loanId) {
            return redirect()->to('/peminjaman-mandiri');
        }
        
        // Get loan details
        $loan = $this->db->table('collectionloans as cl')
            ->select('cl.*, m.MemberNo, m.Fullname, m.Email, m.Phone')
            ->join('members as m', 'm.ID = cl.Member_id')
            ->where('cl.ID', $loanId)
            ->get()
            ->getRow();
       
        if (!$loan) {
            return redirect()->to('/peminjaman-mandiri');
        }
        
        // Get loan items
        $loanItems = $this->db->table('collectionloanitems as cli')
            ->select('cli.*, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
            ->join('collections as col', 'col.ID = cli.Collection_id')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
            ->where('cli.CollectionLoan_id', $loanId)
            ->get()
            ->getResult();
        
        $this->data['loan'] = $loan;
        $this->data['loanItems'] = $loanItems;
        
        return view('SelfLoan\Views\success', $this->data);
    }
    
    private function generateLoanId()
    {
        $prefix = 'LN';
        $timestamp = date('YmdHis');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }
    
    private function getLocationData($locationId)
    {
        return $this->db->table('locations as a')
            ->select('a.ID, a.Code, a.Name, b.Name as LocationLibrary_name, 
                     b.Code as LocationLibrary_code, a.Branch_id, c.Name as Branch_name')
            ->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
            ->join('branchs as c', 'c.ID = a.Branch_id', 'left')
            ->where('a.ID', $locationId)
            ->get()
            ->getRow();
    }
}