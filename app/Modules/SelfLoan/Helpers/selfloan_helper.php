<?php

// File: app/Helpers/selfloan_helper.php

if (!function_exists('generate_loan_number')) {
    /**
     * Generate unique loan number
     */
    function generate_loan_number($prefix = 'LN') {
        $db = \Config\Database::connect('data');
        
        do {
            $timestamp = date('YmdHis');
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $loanNumber = $prefix . $timestamp . $random;
            
            // Check if number already exists
            $exists = $db->table('collectionloans')
                        ->where('ID', $loanNumber)
                        ->countAllResults() > 0;
        } while ($exists);
        
        return $loanNumber;
    }
}

if (!function_exists('check_member_loan_eligibility')) {
    /**
     * Check if member is eligible for loan
     */
    function check_member_loan_eligibility($memberId) {
        $db = \Config\Database::connect('data');
        
        // Get member data with loan rules
        $member = $db->table('members as m')
                    ->select('m.*, ja.MaxPinjamKoleksi, ja.MaxLoanDays, ja.SuspendMember')
                    ->join('jenis_anggota as ja', 'ja.id = m.JenisAnggota_id', 'left')
                    ->where('m.ID', $memberId)
                    ->where('m.active', 1)
                    ->get()
                    ->getRow();
        
        if (!$member) {
            return [
                'eligible' => false,
                'message' => 'Anggota tidak ditemukan'
            ];
        }
        
        // Check membership status
        if ($member->StatusAnggota_id !=3) {
            return [
                'eligible' => false,
                'message' => 'Status anggota tidak aktif'
            ];
        }
        
        // Check membership expiry
        if ($member->EndDate && strtotime($member->EndDate) < time()) {
            return [
                'eligible' => false,
                'message' => 'Keanggotaan sudah berakhir'
            ];
        }
        
        // Check current loans
        $currentLoans = $db->table('collectionloanitems')
                          ->where('member_id', $memberId)
                          ->where('LoanStatus', 'Dipinjam')
                          ->where('active', 1)
                          ->countAllResults();
        
        $maxLoans = $member->MaxPinjamKoleksi ?: 5;
        
        if ($currentLoans >= $maxLoans) {
            return [
                'eligible' => false,
                'message' => "Sudah mencapai batas maksimal peminjaman ($maxLoans buku)"
            ];
        }
        
        // Check overdue books
        $overdueBooks = $db->table('collectionloanitems')
                          ->where('member_id', $memberId)
                          ->where('LoanStatus', 'Dipinjam')
                          ->where('DueDate <', date('Y-m-d H:i:s'))
                          ->where('active', 1)
                          ->countAllResults();
        
        if ($overdueBooks > 0) {
            return [
                'eligible' => false,
                'message' => "Terdapat $overdueBooks buku yang terlambat dikembalikan"
            ];
        }
        
        return [
            'eligible' => true,
            'current_loans' => $currentLoans,
            'max_loans' => $maxLoans,
            'remaining_loans' => $maxLoans - $currentLoans,
            'loan_days' => $member->MaxLoanDays ?: 7
        ];
    }
}

if (!function_exists('check_collection_availability')) {
    /**
     * Check if collection is available for loan
     */
    function check_collection_availability($collectionId, $memberId = null) {
        $db = \Config\Database::connect('data');
        
        // Get collection data
        $collection = $db->table('collections as col')
                        ->select('col.*, cat.Title, stat.Name as Status_name')
                        ->join('catalogs as cat', 'cat.ID = col.Catalog_id', 'left')
                        ->join('collectionstatus as stat', 'stat.ID = col.Status_id', 'left')
                        ->join('collectionrules as rule', 'rule.ID = col.Rule_id', 'left')
                        ->where('col.ID', $collectionId)
                        ->where('col.active', 1)
                        ->get()
                        ->getRow();
        
        if (!$collection) {
            return [
                'available' => false,
                'message' => 'Koleksi tidak ditemukan'
            ];
        }
        
        // Check collection status (assuming 9 = available)
        if ($collection->Status_id != 1) {
            return [
                'available' => false,
                'message' => "Koleksi tidak tersedia. Status: {$collection->Status_name}"
            ];
        }
        
        // Check if already on loan
        $onLoan = $db->table('collectionloanitems')
                    ->where('Collection_id', $collectionId)
                    ->where('LoanStatus', 'Loan')
                    ->where('active', 1)
                    ->get()
                    ->getRow();
        
        if ($onLoan) {
            return [
                'available' => false,
                'message' => 'Koleksi sedang dipinjam'
            ];
        }
        
        // Check if member already borrowed this collection
        if ($memberId) {
            $memberLoan = $db->table('collectionloanitems')
                            ->where('Collection_id', $collectionId)
                            ->where('member_id', $memberId)
                            ->where('LoanStatus', 'Dipinjam')
                            ->where('active', 1)
                            ->get()
                            ->getRow();
            
            if ($memberLoan) {
                return [
                    'available' => false,
                    'message' => 'Anda sudah meminjam koleksi ini'
                ];
            }
        }
        
        // Check if collection is reference only
        if (isset($collection->ISREFERENSI) && $collection->ISREFERENSI) {
            return [
                'available' => false,
                'message' => 'Koleksi hanya untuk dibaca di tempat'
            ];
        }
        
        return [
            'available' => true,
            'collection' => $collection
        ];
    }
}

if (!function_exists('calculate_due_date')) {
    /**
     * Calculate due date for loan
     */
    function calculate_due_date($memberId, $loanDate = null) {
        $db = \Config\Database::connect('data');
        
        if (!$loanDate) {
            $loanDate = date('Y-m-d H:i:s');
        }
        
        // Get member loan days
        $member = $db->table('members as m')
                    ->select('ja.MaxLoanDays')
                    ->join('jenis_anggota as ja', 'ja.id = m.JenisAnggota_id', 'left')
                    ->where('m.ID', $memberId)
                    ->get()
                    ->getRow();
        
        $loanDays = $member->MaxLoanDays ?? 7; // Default 7 days
        
        return date('Y-m-d H:i:s', strtotime($loanDate . " +{$loanDays} days"));
    }
}

if (!function_exists('log_loan_activity')) {
    /**
     * Log loan activity for audit trail
     */
    function log_loan_activity($type, $data, $userId = null, $terminal = null) {
        $db = \Config\Database::connect('data');
        
        // Check if loan_activities table exists, if not skip logging
        if (!$db->tableExists('loan_activities')) {
            return false;
        }
        
        $logData = [
            'activity_type' => $type, // 'loan', 'return', 'extend', etc.
            'activity_data' => json_encode($data),
            'user_id' => $userId ?: 1, // System user
            'terminal' => $terminal ?: request()->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            return $db->table('loan_activities')->insert($logData);
        } catch (Exception $e) {
            log_message('error', 'Failed to log loan activity: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('format_barcode')) {
    /**
     * Format and validate barcode
     */
    function format_barcode($barcode) {
        // Remove spaces and special characters
        $barcode = preg_replace('/[^A-Za-z0-9]/', '', $barcode);
        
        // Convert to uppercase
        $barcode = strtoupper($barcode);
        
        return $barcode;
    }
}

if (!function_exists('validate_member_number')) {
    /**
     * Validate member number format
     */
    function validate_member_number($memberNo) {
        // Remove spaces and special characters except allowed ones
        $memberNo = trim($memberNo);
        
        // Basic validation - adjust according to your member number format
        if (strlen($memberNo) < 3) {
            return [
                'valid' => false,
                'message' => 'Nomor anggota minimal 3 karakter'
            ];
        }
        
        if (strlen($memberNo) > 50) {
            return [
                'valid' => false,
                'message' => 'Nomor anggota maksimal 50 karakter'
            ];
        }
        
        return [
            'valid' => true,
            'formatted' => $memberNo
        ];
    }
}

if (!function_exists('get_loan_statistics')) {
    /**
     * Get loan statistics for dashboard
     */
    function get_loan_statistics($branchId = null, $dateFrom = null, $dateTo = null) {
        $db = \Config\Database::connect('data');
        
        $builder = $db->table('collectionloanitems as cli');
        
        if ($branchId) {
            $builder->where('cli.Branch_id', $branchId);
        }
        
        if ($dateFrom) {
            $builder->where('cli.LoanDate >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('cli.LoanDate <=', $dateTo);
        }
        
        $totalLoans = $builder->where('cli.active', 1)->countAllResults();
        
        $builder = $db->table('collectionloanitems as cli');
        if ($branchId) $builder->where('cli.Branch_id', $branchId);
        if ($dateFrom) $builder->where('cli.LoanDate >=', $dateFrom);
        if ($dateTo) $builder->where('cli.LoanDate <=', $dateTo);
        
        $activeLoans = $builder->where('cli.LoanStatus', 'Dipinjam')
                              ->where('cli.active', 1)
                              ->countAllResults();
        
        $builder = $db->table('collectionloanitems as cli');
        if ($branchId) $builder->where('cli.Branch_id', $branchId);
        if ($dateFrom) $builder->where('cli.LoanDate >=', $dateFrom);
        if ($dateTo) $builder->where('cli.LoanDate <=', $dateTo);
        
        $overdueLoans = $builder->where('cli.LoanStatus', 'Dipinjam')
                               ->where('cli.DueDate <', date('Y-m-d H:i:s'))
                               ->where('cli.active', 1)
                               ->countAllResults();
        
        $builder = $db->table('collectionloanitems as cli');
        if ($branchId) $builder->where('cli.Branch_id', $branchId);
        if ($dateFrom) $builder->where('cli.LoanDate >=', $dateFrom);
        if ($dateTo) $builder->where('cli.LoanDate <=', $dateTo);
        
        $returnedLoans = $builder->where('cli.LoanStatus', 'Dikembalikan')
                                ->where('cli.active', 1)
                                ->countAllResults();
        
        return [
            'total_loans' => $totalLoans,
            'active_loans' => $activeLoans,
            'overdue_loans' => $overdueLoans,
            'returned_loans' => $returnedLoans,
            'return_rate' => $totalLoans > 0 ? round(($returnedLoans / $totalLoans) * 100, 2) : 0
        ];
    }
}

if (!function_exists('send_loan_notification')) {
    /**
     * Send loan notification (email/SMS)
     */
    function send_loan_notification($type, $memberData, $loanData) {
        // This is a placeholder function
        // Implement according to your notification system
        
        switch ($type) {
            case 'loan_success':
                // Send loan confirmation
                break;
            case 'due_reminder':
                // Send due date reminder
                break;
            case 'overdue_notice':
                // Send overdue notice
                break;
        }
        
        return true;
    }
}