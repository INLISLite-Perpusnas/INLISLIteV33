<?php

namespace SelfLoan\Controllers;

class SelfLoan extends \App\Controllers\BaseController
{
    protected $db;
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
                         ja.jenisanggota as JenisAnggota_name, ja.MaxLoanDays, ja.MaxLoanDays,
                         sa.Nama as StatusAnggota_name')
                ->join('jenis_anggota as ja', 'ja.ID = m.JenisAnggota_id', 'left')
                ->join('status_anggota as sa', 'sa.ID = m.StatusAnggota_id', 'left')
                ->where('m.MemberNo', $memberNo)
                ->where('m.active', 1)
                ->get()
                ->getRow();
            
            if (!$member) {
                return ['success' => false, 'message' => 'Anggota tidak ditemukan atau tidak aktif'];
            }
            
            // Check member status
            if ($member->StatusAnggota_id != 3) { // Assuming 1 = active status
                return ['success' => false, 'message' => 'Status anggota tidak aktif'];
            }
            
            // Check membership expiry
            if ($member->EndDate && strtotime($member->EndDate) < time()) {
                return ['success' => false, 'message' => 'Keanggotaan sudah berakhir'];
            }
            
            // Get current loans count
            $currentLoans = $this->db->table('collectionloanitems')
                ->where('member_id', $member->ID)
                ->where('LoanStatus', 'Loan')
                ->where('active', 1)
                ->countAllResults();
				// dd($member);
            $maxLoans = $member->MaxLoanDays ?: 5;
		
			
            $remainingLoans = $maxLoans - $currentLoans;
		
            
            if ($remainingLoans <= 0) {
                return ['success' => false, 'message' => 'Kuota peminjaman sudah penuh'];
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
                    'loan_days' => $member->MaxLoanDays ?: 7,
                    'branch_id' => $member->Branch_id
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
            
            // Check loan limit
            if (count($selectedBooks) >= $memberData['remaining_loans']) {
                return ['success' => false, 'message' => 'Maksimal peminjaman ' . $memberData['max_loans'] . ' buku'];
            }
            
            // Query collection data
			$collection = $this->db->table('collections as col')
            ->select('col.*, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, 
                     loc.Name as Location_name, stat.Name as Status_name')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id', 'left')
            ->join('locations as loc', 'loc.ID = col.Location_id', 'left')
            ->join('collectionstatus as stat', 'stat.ID = col.Status_id', 'left')
            ->join('collectionrules as rule', 'rule.ID = col.Rule_id', 'left')
            ->where('col.NomorBarcode', $barcode)
            ->where('col.active', 1)
            ->get()
            ->getRow();
            
            if (!$collection) {
                return ['success' => false, 'message' => 'Koleksi dengan barcode "' . $barcode . '" tidak ditemukan'];
            }
            
            // Check if collection can be loaned
            if ($collection->Rule_id!=1) {
                return ['success' => false, 'message' => 'Koleksi ini tidak dapat dipinjam'];
            }
        
            // Check collection status (assuming status_id 2 = available)
            if ($collection->Status_id != 1) {
                return ['success' => false, 'message' => 'Koleksi sedang tidak tersedia untuk dipinjam'];
            }
          
            // Check if already loaned
            $existingLoan = $this->db->table('collectionloanitems')
                ->where('Collection_id', $collection->ID)
                ->where('LoanStatus', 'Loan')
                ->where('active', 1)
                ->get()
                ->getRow();
			
            
            if ($existingLoan) {
                return ['success' => false, 'message' => 'Koleksi sedang dipinjam oleh anggota lain'];
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
                    'location' => $collection->Location_name
                ]
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Book validation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
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
        
        $this->db->transStart();
        
        try {
            // Generate loan ID
       
            $loanDate = date('Y-m-d H:i:s');
            $dueDate = date('Y-m-d H:i:s', strtotime("+{$memberData['loan_days']} days"));
            $locationId = $this->request->getCookie('Location_id') ?: 1;
            
            // Create collection loan record
            $loanData = [
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
                'active' => 1
            ];
            
            $this->db->table('collectionloans')->insert($loanData);
			$newLoanId = $this->db->insertID();
		 
            
            // Process each collection
            foreach ($selectedBooks as $book) {
                // Create loan item
                $loanItemData = [
                    'CollectionLoan_id' => $newLoanId,
                    'LoanDate' => $loanDate,
                    'DueDate' => $dueDate,
                    'LoanStatus' => 'Loan',
                    'Collection_id' => $book['id'],
                    'member_id' => $memberData['id'],
                    'CreateBy' => 1,
                    'CreateDate' => $loanDate,
                    'CreateTerminal' => $this->request->getIPAddress(),
                    'Branch_id' => $memberData['branch_id'],
                    'active' => 1
                ];
                
                $this->db->table('collectionloanitems')->insert($loanItemData);
                
                // Update collection status to "Dipinjam"
                $this->db->table('collections')
                    ->where('ID', $book['id'])
                    ->update([
                        'Status_id' => 5, // Dipinjam status
                        'UpdateBy' => 1,
                        'UpdateDate' => $loanDate,
                        'UpdateTerminal' => $this->request->getIPAddress()
                    ]);
            }
            
            $this->db->transCommit();
            
            // Clear session
            $this->session->remove($sessionKey);
            
            // Redirect to success page
            return redirect()->to('/peminjaman-mandiri/success?loan_id=' . $newLoanId);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Loan processing error: ' . $e->getMessage());
            return redirect()->to('/peminjaman-mandiri?MemberNo=' . urlencode($memberNo))
                   ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
    
    public function success()
    {
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
            ->where('cli.active', 1)
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