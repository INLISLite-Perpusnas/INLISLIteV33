<?php

namespace ReadOnspot\Controllers;

class ReadOnspot extends \App\Controllers\BaseController
{
    public $bacaditempatModel;
    public $uploadPath;
    public $modulePath;
    public $collectionModel;

    public $data = [];
    public $language;

    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->bacaditempatModel = new \ReadOnSpot\Models\ReadOnSpotModel();
        $this->collectionModel   = new \Peminjaman\Models\CollectionModel();

        helper('reference');
        helper('peminjaman');
        helper('pengembalian');
        helper('member');
        helper('lokasiruang');
        helper('home');
    }

    /**
     * Main index page
     */
    public function index()
    {
        // Get location id from cookie
        $locationId = $this->request->getCookie('Location_id');
        
        // Check if location id is available
        if (!$locationId) {
            return redirect()->to('buku-tamu/lokasi');
        }
        
        $this->data['title'] = 'Baca Ditempat';
        echo view('ReadOnSpot\Views\index', $this->data);
    }

    /**
     * Search member by member number
     */
    public function searchMember()
    {
        $memberNo = $this->request->getVar('member_no');
        
        if (!$memberNo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor anggota tidak boleh kosong'
            ]);
        }
        
        try {
            // Search member in database
            $member = $this->getMemberByNumber($memberNo);
		
            
            if ($member) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'id' => $member->ID,
                        'member_number' => $member->MemberNo,
                        'name' => $member->Fullname,
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anggota dengan nomor tersebut tidak ditemukan'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error searching member: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari anggota'
            ]);
        }
    }

    /**
     * Search book by barcode
     */
    public function searchBook()
    {
        $barcode = $this->request->getVar('barcode');
        
        if (!$barcode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Barcode tidak boleh kosong'
            ]);
        }
        
        try {
            // Search book in collections and catalogs
            $book = $this->getBookByBarcode($barcode);
			
            
            if ($book) {
                // Check if book is available (not borrowed)
                if ($book->Status_id != 1) { // 9 = Available
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Buku sedang tidak tersedia (Status tidak tersedia '
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'collection_id' => $book->collection_id,
                        'catalog_id' => $book->catalog_id,
                        'barcode' => $book->NomorBarcode,
                        'title' => $book->Title,
                        'author' => $book->Author,
                        'publisher' => $book->Publisher,
                        'call_number' => $book->CallNumber,
                        'location' =>   $locationId = $this->request->getCookie('Location_id') ?? 'N/A'
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Buku dengan barcode tersebut tidak ditemukan'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error searching book: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari buku'
            ]);
        }
    }

    /**
     * Store read onspot data (Auto-save when both member and book are valid)
     */
    public function store()
    {
        $locationId = $this->request->getCookie('Location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location ID tidak ditemukan. Silakan set lokasi terlebih dahulu.'
            ]);
        }
        
        $validation = \Config\Services::validation();
        
        // Set validation rules
        $validation->setRules([
            'member_id' => 'required|numeric',
            'collection_id' => 'required|numeric'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $validation->getErrors())
            ]);
        }
        
        try {
            $memberId = $this->request->getPost('member_id');
            $collectionId = $this->request->getPost('collection_id');
            
            // Check if member exists
            $member = $this->getMemberById($memberId);
            if (!$member) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ]);
            }
            
            // Check if collection exists and available
            $collection = $this->getCollectionById($collectionId);
            if (!$collection) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Koleksi buku tidak ditemukan'
                ]);
            }
            
            if ($collection->Status_id != 9) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Buku sedang tidak tersedia'
                ]);
            }
            
            // Generate new ID and visitor number
            $newId = $this->generateNewId();
            $noPengunjung = $this->generateVisitorNumber($locationId);
            
            // Prepare data for insertion
            $data = [
                'ID' => $newId,
                'NoPengunjung' => $noPengunjung,
                'collection_id' => $collectionId,
                'Member_id' => $memberId,
                'Location_Id' => (int)$locationId,
                'Is_return' => '0',
                'CreateBy' => session('user_id') ?? 1,
                'CreateDate' => date('Y-m-d H:i:s'),
                'CreateTerminal' => $this->request->getIPAddress()
            ];
            
            // Insert data
            $result = $this->bacaditempatModel->insert($data);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data baca ditempat berhasil disimpan otomatis',
                    'data' => [
                        'id' => $newId,
                        'no_pengunjung' => $noPengunjung,
                        'member_name' => $member->Fullname,
                        'book_title' => $collection->Title
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data baca ditempat'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error storing read onspot data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ]);
        }
    }

    /**
     * Auto-save when both member and book data are complete
     */
    public function autoSave()
    {
        $memberNo = $this->request->getPost('member_no');
        $barcode = $this->request->getPost('barcode');
        
        if (!$memberNo || !$barcode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data member dan barcode harus lengkap'
            ]);
        }
        
        try {
            // Get member data
            $member = $this->getMemberByNumber($memberNo);
			
            if (!$member) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ]);
            }
            
            // Get book data
            $book = $this->getBookByBarcode($barcode);
            if (!$book) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan'
                ]);
            }
            
            if ($book->Status_id != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Buku sedang tidak tersedia'
                ]);
            }
            // dd($member->ID);
            // Auto-save the data
            $this->request->setGlobal('post', [
                'member_id' => $member->ID,
                'collection_id' => $book->collection_id
            ]);
            
            return $this->store();
            
        } catch (\Exception $e) {
            log_message('error', 'Error in auto-save: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan otomatis'
            ]);
        }
    }

    /**
     * Get recent read onspot data (today)
     */
    public function recentData()
    {
        $locationId = $this->request->getCookie('Location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location ID tidak ditemukan'
            ]);
        }
        
        try {
            $today = date('Y-m-d');
            
            $query = "
                SELECT 
                    b.ID,
                    b.NoPengunjung,
                    b.CreateDate as create_date,
                    b.Is_return,
                    m.NomorAnggota as member_number,
                    m.Fullname as member_name,
                    c.NomorBarcode as barcode,
                    cat.Title as book_title,
                    cat.Author as book_author
                FROM bacaditempat b
                LEFT JOIN members m ON b.Member_id = m.ID
                LEFT JOIN collections c ON b.collection_id = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                WHERE DATE(b.CreateDate) = ? 
                AND b.Location_Id = ?
                ORDER BY b.CreateDate DESC
                LIMIT 20
            ";
            
            $db = \Config\Database::connect('data');
            $results = $db->query($query, [$today, $locationId])->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting recent data: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }

    /**
     * Set book return status
     */
    public function setReturn()
    {
        $readId = $this->request->getPost('read_id');
        
        if (!$readId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID baca ditempat tidak ditemukan'
            ]);
        }
        
        try {
            $data = [
                'Is_return' => '1',
                'UpdateBy' => session('user_id') ?? 1,
                'UpdateDate' => date('Y-m-d H:i:s'),
                'UpdateTerminal' => $this->request->getIPAddress()
            ];
            
            $result = $this->bacaditempatModel->update($readId, $data);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status buku berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate status buku'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error setting return status: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate status'
            ]);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $locationId = $this->request->getCookie('Location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location ID tidak ditemukan'
            ]);
        }
        
        try {
            $db = \Config\Database::connect('data');
            $today = date('Y-m-d');
            $thisMonth = date('Y-m');
            
            // Today's count
            $todayQuery = "
                SELECT COUNT(*) as count 
                FROM bacaditempat 
                WHERE DATE(CreateDate) = ? 
                AND Location_Id = ?
            ";
            $todayCount = $db->query($todayQuery, [$today, $locationId])->getRow()->count;
            
            // This month's count
            $monthQuery = "
                SELECT COUNT(*) as count 
                FROM bacaditempat 
                WHERE DATE_FORMAT(CreateDate, '%Y-%m') = ? 
                AND Location_Id = ?
            ";
            $monthCount = $db->query($monthQuery, [$thisMonth, $locationId])->getRow()->count;
            
            // Unique members today
            $uniqueMembersQuery = "
                SELECT COUNT(DISTINCT Member_id) as count 
                FROM bacaditempat 
                WHERE DATE(CreateDate) = ? 
                AND Location_Id = ?
            ";
            $uniqueMembers = $db->query($uniqueMembersQuery, [$today, $locationId])->getRow()->count;
            
            // Popular books today
            $popularBooksQuery = "
                SELECT 
                    cat.Title,
                    COUNT(*) as read_count
                FROM bacaditempat b
                LEFT JOIN collections c ON b.collection_id = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                WHERE DATE(b.CreateDate) = ?
                AND b.Location_Id = ?
                GROUP BY cat.ID, cat.Title
                ORDER BY read_count DESC
                LIMIT 5
            ";
            $popularBooks = $db->query($popularBooksQuery, [$today, $locationId])->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'today_count' => $todayCount,
                    'month_count' => $monthCount,
                    'unique_members' => $uniqueMembers,
                    'popular_books' => $popularBooks
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting statistics: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik'
            ]);
        }
    }

    /**
     * Get active readers (books not returned)
     */
    public function getActiveReaders()
    {
        $locationId = $this->request->getCookie('Location_id');
        
        if (!$locationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location ID tidak ditemukan'
            ]);
        }
        
        try {
            $query = "
                SELECT 
                    b.ID,
                    b.NoPengunjung,
                    b.CreateDate,
                    m.NomorAnggota as member_number,
                    m.Fullname as member_name,
                    c.NomorBarcode as barcode,
                    cat.Title as book_title,
                    cat.Author as book_author
                FROM bacaditempat b
                LEFT JOIN members m ON b.Member_id = m.ID
                LEFT JOIN collections c ON b.collection_id = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                WHERE b.Location_Id = ?
                AND b.Is_return = '0'
                ORDER BY b.CreateDate ASC
            ";
            
            $db = \Config\Database::connect();
            $results = $db->query($query, [$locationId])->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting active readers: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pembaca aktif'
            ]);
        }
    }

    /**
     * Get popular books
     */
    public function getPopularBooks()
    {
        $locationId = $this->request->getCookie('Location_id');
        $period = $this->request->getVar('period') ?? 'today'; // today, week, month
        
        if (!$locationId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location ID tidak ditemukan'
            ]);
        }
        
        try {
            $dateCondition = '';
            switch ($period) {
                case 'week':
                    $dateCondition = "AND DATE(b.CreateDate) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $dateCondition = "AND DATE_FORMAT(b.CreateDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
                    break;
                case 'today':
                default:
                    $dateCondition = "AND DATE(b.CreateDate) = CURDATE()";
                    break;
            }
            
            $query = "
                SELECT 
                    cat.Title,
                    cat.Author,
                    COUNT(*) as read_count,
                    c.CallNumber
                FROM bacaditempat b
                LEFT JOIN collections c ON b.collection_id = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                WHERE b.Location_Id = ?
                {$dateCondition}
                GROUP BY cat.ID, cat.Title, cat.Author, c.CallNumber
                ORDER BY read_count DESC
                LIMIT 10
            ";
            
            $db = \Config\Database::connect();
            $results = $db->query($query, [$locationId])->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting popular books: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data buku populer'
            ]);
        }
    }

    /**
     * Export data for reports
     */
    public function exportData()
    {
        $locationId = $this->request->getCookie('Location_id');
        $startDate = $this->request->getVar('start_date') ?? date('Y-m-d');
        $endDate = $this->request->getVar('end_date') ?? date('Y-m-d');
        $format = $this->request->getVar('format') ?? 'excel';
        
        if (!$locationId) {
            return redirect()->back()->with('error', 'Location ID tidak ditemukan');
        }
        
        try {
            $query = "
                SELECT 
                    b.ID,
                    b.NoPengunjung as 'No Pengunjung',
                    b.CreateDate as 'Tanggal Baca',
                    CASE WHEN b.Is_return = '1' THEN 'Dikembalikan' ELSE 'Sedang Baca' END as 'Status',
                    m.NomorAnggota as 'No Anggota',
                    m.Fullname as 'Nama Anggota',
                    c.NomorBarcode as 'Barcode Buku',
                    cat.Title as 'Judul Buku',
                    cat.Author as 'Pengarang',
                    cat.CallNumber as 'No Panggil'
                FROM bacaditempat b
                LEFT JOIN members m ON b.Member_id = m.ID
                LEFT JOIN collections c ON b.collection_id = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                WHERE b.Location_Id = ?
                AND DATE(b.CreateDate) >= ?
                AND DATE(b.CreateDate) <= ?
                ORDER BY b.CreateDate DESC
            ";
            
            $db = \Config\Database::connect();
            $results = $db->query($query, [$locationId, $startDate, $endDate])->getResultArray();
            
            if ($format === 'excel') {
                return $this->exportToExcel($results, $startDate, $endDate);
            } else {
                return $this->exportToCSV($results, $startDate, $endDate);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export data');
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Get member by member number
     */
    private function getMemberByNumber($memberNo)
    {
        $db = \Config\Database::connect('data');
        
        $query = "
            SELECT ID, MemberNo, Fullname,active
            FROM members 
            WHERE MemberNo = ? 
			AND active = 1
            LIMIT 1
        ";
        
        return $db->query($query, [$memberNo])->getRow();
    }

    /**
     * Get member by ID
     */
    private function getMemberById($memberId)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT ID, NomorAnggota, Fullname, active
            FROM members 
            WHERE ID = ? 
            AND active = 1
            LIMIT 1
        ";
        
        return $db->query($query, [$memberId])->getRow();
    }

    /**
     * Get book by barcode
     */
   private function getBookByBarcode($barcode)
{
    $db = \Config\Database::connect('data');
    
    $query = "
        SELECT 
            c.ID as collection_id,
            c.NomorBarcode,
            c.Status_id,
            c.CallNumber,
            cat.ID as catalog_id,
            cat.Title,
            cat.Author,
            cat.Publisher
        FROM collections c
        LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
        WHERE c.NomorBarcode = ?
        AND cat.IsOPAC = 1
        LIMIT 1
    ";
    
    return $db->query($query, [$barcode])->getRow();
}


    /**
     * Get collection by ID
     */
    private function getCollectionById($collectionId)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                c.ID,
                c.NomorBarcode,
                c.Status_id,
                cat.Title,
                cat.Author
            FROM collections c
            LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
            WHERE c.ID = ?
            LIMIT 1
        ";
        
        return $db->query($query, [$collectionId])->getRow();
    }

    /**
     * Generate visitor number with pattern: DDMMYYYY0001
     */
    private function generateVisitorNumber($locationId)
    {
        $db = \Config\Database::connect();
        $today = date('dmY'); // Format: ddmmyyyy
        
        // Get today's count for this location
        $query = "
            SELECT COUNT(*) as count 
            FROM bacaditempat 
            WHERE DATE(CreateDate) = CURDATE() 
            AND Location_Id = ?
        ";
        
        $result = $db->query($query, [$locationId])->getRow();
        $todayCount = ($result->count ?? 0) + 1;
        
        // Format: DDMMYYYY + 4-digit sequential number
        return $today . str_pad($todayCount, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate new ID for bacaditempat table
     */
    private function generateNewId()
    {
        $db = \Config\Database::connect();
        
        $query = "SELECT MAX(ID) as max_id FROM bacaditempat";
        $result = $db->query($query)->getRow();
        
        return ($result->max_id ?? 0) + 1;
    }

    /**
     * Get status name by status ID
     */
  

    /**
     * Export to Excel
     */
    private function exportToExcel($data, $startDate, $endDate)
    {
        // Implement Excel export using PhpSpreadsheet or similar library
        // This is a basic implementation - you may need to install PhpSpreadsheet
        
        $filename = "baca_ditempat_{$startDate}_to_{$endDate}.xlsx";
        
        // Set headers for download
        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Basic CSV output (replace with proper Excel library)
        return $this->exportToCSV($data, $startDate, $endDate);
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($data, $startDate, $endDate)
    {
        $filename = "baca_ditempat_{$startDate}_to_{$endDate}.csv";
        
        // Set headers for download
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            // Add data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * Legacy method - keeping for compatibility
     */
    public function store_data()
    {
        return $this->store();
    }
}