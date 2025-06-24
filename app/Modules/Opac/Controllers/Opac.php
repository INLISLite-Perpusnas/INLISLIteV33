<?php

namespace Opac\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Opac extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $visitorModel;
    public $katalogModel;
    public $fileModel;
    public $data = [];
    public $db;
    public $memberModel;
    public $collectionLoanModel;
    public $eksemplarModel;
    

    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->db=\Config\Database::connect('data');
        $this->fileModel = new \Katalog\Models\FileModel();
          $this->memberModel = new \Anggota\Models\AnggotaModel();
        $this->collectionLoanModel =new \Peminjaman\Models\CollectionLoanModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
    }

 public function index()
{
    $this->data['title'] = 'OPAC - Online Public Access Catalog';
    
    // Check for member number (for recommendations)
    $memberNo = $this->request->getVar('member_no');
    
    if ($memberNo) {
        // Handle recommendations
        try {
            $result = $this->calculateRecommendations($memberNo);
            
            $this->data['member_no'] = $memberNo;
            $this->data['recommendations'] = $result['recommendations'];
            $this->data['metrics'] = [
                'precision' => $result['precision'],
                'recall' => $result['recall'],
                'ndcg' => $result['ndcg'],
                'accuracy' => $result['accuracy']
            ];
            $this->data['is_cold_start'] = $result['is_cold_start'];
            
            // Don't show regular catalogs when showing recommendations
            $this->data['catalogs'] = [];
            $this->data['pager'] = null;
            $this->data['search'] = null;
            $this->data['search_by'] = null;
            $this->data['total_records'] = 0;
            
        } catch (\Exception $e) {
            // If recommendation fails, fall back to regular catalog display
            $this->data['member_no'] = $memberNo;
            $this->data['recommendations'] = [];
            $this->data['metrics'] = null;
            $this->data['is_cold_start'] = true;
            $this->data['recommendation_error'] = $e->getMessage();
            
            // Show regular catalogs as fallback
            $this->loadRegularCatalogs();
        }
    } else {
        // Regular catalog display (original functionality)
        $this->loadRegularCatalogs();
        
        // Clear recommendation data
        $this->data['member_no'] = null;
        $this->data['recommendations'] = null;
        $this->data['metrics'] = null;
        $this->data['is_cold_start'] = false;
    }
    
    return view('Opac\Views\index', $this->data);
}

private function loadRegularCatalogs()
{
    // Original catalog loading logic
    $perPage = 12;
    $currentPage = $this->request->getVar('page') ?? 1;
    
    // Pencarian
    $search = $this->request->getVar('search');
    $searchBy = $this->request->getVar('search_by') ?? 'Title';
    
    $builder = $this->katalogModel->select('catalogs.*');
    
    if ($search) {
        switch ($searchBy) {
            case 'Title':
                $builder->like('Title', $search);
                break;
            case 'Author':
                $builder->like('Author', $search);
                break;
            case 'Subject':
                $builder->like('Subject', $search);
                break;
            case 'ISBN':
                $builder->like('ISBN', $search);
                break;
            case 'Publisher':
                $builder->like('Publisher', $search);
                break;
            default:
                $builder->groupStart()
                       ->like('Title', $search)
                       ->orLike('Author', $search)
                       ->orLike('Subject', $search)
                       ->groupEnd();
        }
    }
    
    $this->data['catalogs'] = $builder->paginate($perPage);
    $this->data['pager'] = $this->katalogModel->pager;
    $this->data['search'] = $search;
    $this->data['search_by'] = $searchBy;
    $this->data['total_records'] = $builder->countAllResults(false);
}

    public function detail($id)
    {
        $file = $this->fileModel->where('Catalog_id', $id)->first();  
        if($file!==null){
             $ID=$file->ID;
             $this->data['ID'] = $ID;
             
        }  
      
      
        
        $catalog = $this->katalogModel->asArray()->find($id);
        
        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }
        
        // Get eksemplar data
        $EksemplarModel = new \Eksemplar\Models\EksemplarModel();
        
        // Get physical books (non-DRM)
        $roweksemplar = $EksemplarModel
            ->select('collections.NomorBarcode, collections.CallNumber, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
           
            ->findAll();
           
        
        // Get digital books (DRM)
        $roweksemplar_drm = $EksemplarModel
            ->select('collections.NomorBarcode, collections.CallNumber, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
            ->where('collections.ISDRM', 1)
       
            ->findAll();
       
        $this->data['title'] = 'Detail Katalog - ' . $catalog['Title'];
        $this->data['catalog'] = $catalog;
        $this->data['roweksemplar'] = $roweksemplar;
        $this->data['roweksemplar_drm'] = $roweksemplar_drm;
        
        return view('Opac\Views\detail', $this->data);
    }

    public function search()
    {
        $this->data['title'] = 'Pencarian Katalog';
        
        // Advanced search form
        $searchData = [
            'title' => $this->request->getVar('title'),
            'author' => $this->request->getVar('author'),
            'subject' => $this->request->getVar('subject'),
            'publisher' => $this->request->getVar('publisher'),
            'isbn' => $this->request->getVar('isbn'),
            'year_from' => $this->request->getVar('year_from'),
            'year_to' => $this->request->getVar('year_to'),
            'language' => $this->request->getVar('language')
        ];
        
        $results = [];
        
        if ($this->request->getMethod() === 'post' || $this->request->getVar('submit')) {
            $builder = $this->katalogModel->select('catalogs.*');
            
            if ($searchData['title']) {
                $builder->like('Title', $searchData['title']);
            }
            if ($searchData['author']) {
                $builder->like('Author', $searchData['author']);
            }
            if ($searchData['subject']) {
                $builder->like('Subject', $searchData['subject']);
            }
            if ($searchData['publisher']) {
                $builder->like('Publisher', $searchData['publisher']);
            }
            if ($searchData['isbn']) {
                $builder->like('ISBN', $searchData['isbn']);
            }
            if ($searchData['year_from']) {
                $builder->where('PublishYear >=', $searchData['year_from']);
            }
            if ($searchData['year_to']) {
                $builder->where('PublishYear <=', $searchData['year_to']);
            }
            if ($searchData['language']) {
                $builder->like('Languages', $searchData['language']);
            }
            
            $results = $builder->findAll();
        }
        
        $this->data['search_data'] = $searchData;
        $this->data['results'] = $results;
        $this->data['total_found'] = count($results);
        
        return view('Opac\Views\search', $this->data);
    }

    public function browse()
    {
        $this->data['title'] = 'Browse Katalog';
        
        $browseType = $this->request->getVar('type') ?? 'author';
        $letter = $this->request->getVar('letter') ?? 'A';
        
        $builder = $this->katalogModel->select('catalogs.*');
        
        switch ($browseType) {
            case 'author':
                $builder->like('Author', $letter . '%', 'after');
                $builder->orderBy('Author', 'ASC');
                break;
            case 'title':
                $builder->like('Title', $letter . '%', 'after');
                $builder->orderBy('Title', 'ASC');
                break;
            case 'subject':
                $builder->like('Subject', $letter . '%', 'after');
                $builder->orderBy('Subject', 'ASC');
                break;
        }
        
        $this->data['catalogs'] = $builder->findAll();
        $this->data['browse_type'] = $browseType;
        $this->data['letter'] = $letter;
        $this->data['alphabet'] = range('A', 'Z');
        
        return view('Opac\Views\browse', $this->data);
    }

    public function export()
    {
        $format = $this->request->getVar('format') ?? 'excel';
        $search = $this->request->getVar('search');
        
        $builder = $this->katalogModel->select('catalogs.*');
        
        if ($search) {
            $builder->groupStart()
                   ->like('Title', $search)
                   ->orLike('Author', $search)
                   ->orLike('Subject', $search)
                   ->groupEnd();
        }
        
        $catalogs = $builder->findAll();
        
        if ($format === 'excel') {
            return $this->exportToExcel($catalogs);
        } else {
            return $this->exportToCSV($catalogs);
        }
    }

    private function exportToExcel($catalogs)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $headers = [
            'A1' => 'ID',
            'B1' => 'Control Number',
            'C1' => 'BIBID',
            'D1' => 'Title',
            'E1' => 'Author',
            'F1' => 'Edition',
            'G1' => 'Publisher',
            'H1' => 'Publish Location',
            'I1' => 'Publish Year',
            'J1' => 'Subject',
            'K1' => 'Physical Description',
            'L1' => 'ISBN',
            'M1' => 'Call Number',
            'N1' => 'Languages'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Data
        $row = 2;
        foreach ($catalogs as $catalog) {
            $sheet->setCellValue('A' . $row, $catalog['ID']);
            $sheet->setCellValue('B' . $row, $catalog['ControlNumber']);
            $sheet->setCellValue('C' . $row, $catalog['BIBID']);
            $sheet->setCellValue('D' . $row, $catalog['Title']);
            $sheet->setCellValue('E' . $row, $catalog['Author']);
            $sheet->setCellValue('F' . $row, $catalog['Edition']);
            $sheet->setCellValue('G' . $row, $catalog['Publisher']);
            $sheet->setCellValue('H' . $row, $catalog['PublishLocation']);
            $sheet->setCellValue('I' . $row, $catalog['PublishYear']);
            $sheet->setCellValue('J' . $row, $catalog['Subject']);
            $sheet->setCellValue('K' . $row, $catalog['PhysicalDescription']);
            $sheet->setCellValue('L' . $row, $catalog['ISBN']);
            $sheet->setCellValue('M' . $row, $catalog['CallNumber']);
            $sheet->setCellValue('N' . $row, $catalog['Languages']);
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'catalog_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function exportToCSV($catalogs)
    {
        $filename = 'catalog_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'ID', 'Control Number', 'BIBID', 'Title', 'Author', 'Edition',
            'Publisher', 'Publish Location', 'Publish Year', 'Subject',
            'Physical Description', 'ISBN', 'Call Number', 'Languages'
        ]);
        
        // Data
        foreach ($catalogs as $catalog) {
            fputcsv($output, [
                $catalog['ID'],
                $catalog['ControlNumber'],
                $catalog['BIBID'],
                $catalog['Title'],
                $catalog['Author'],
                $catalog['Edition'],
                $catalog['Publisher'],
                $catalog['PublishLocation'],
                $catalog['PublishYear'],
                $catalog['Subject'],
                $catalog['PhysicalDescription'],
                $catalog['ISBN'],
                $catalog['CallNumber'],
                $catalog['Languages']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function statistics()
    {
        $this->data['title'] = 'Statistik Katalog';
        
        // Total katalog
        $this->data['total_catalogs'] = $this->katalogModel->countAll();
        
        // Katalog per tahun
        $this->data['by_year'] = $this->katalogModel
            ->select('PublishYear, COUNT(*) as total')
            ->where('PublishYear IS NOT NULL')
            ->where('PublishYear !=', '')
            ->groupBy('PublishYear')
            ->orderBy('PublishYear', 'DESC')
            ->findAll();
        
        // Katalog per bahasa
        $this->data['by_language'] = $this->katalogModel
            ->select('Languages, COUNT(*) as total')
            ->where('Languages IS NOT NULL')
            ->where('Languages !=', '')
            ->groupBy('Languages')
            ->orderBy('total', 'DESC')
            ->findAll();
        
        // Katalog per penerbit (top 10)
        $builder = $this->db->table('catalogs');
$this->data['by_publisher'] = $builder
    ->select('Publisher, COUNT(*) as total')
    ->where('Publisher IS NOT NULL')
    ->where('Publisher !=', '')
    ->groupBy('Publisher')
    ->orderBy('total', 'DESC')
    ->limit(10)
    ->get()
    ->getResult(); // atau getResultArray();

        
        return view('Opac\Views\statistics', $this->data);
    }

    public function api($action = null)
    {
        $this->response->setContentType('application/json');
        
        switch ($action) {
            case 'search':
                return $this->apiSearch();
            case 'detail':
                return $this->apiDetail();
            default:
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid API endpoint'
                ]);
        }
    }

    private function apiSearch()
    {
        $query = $this->request->getVar('q');
        $limit = $this->request->getVar('limit') ?? 10;
        $offset = $this->request->getVar('offset') ?? 0;
        
        $builder = $this->katalogModel->select('ID, Title, Author, Publisher, PublishYear');
        
        if ($query) {
            $builder->groupStart()
                   ->like('Title', $query)
                   ->orLike('Author', $query)
                   ->orLike('Subject', $query)
                   ->groupEnd();
        }
        
        $results = $builder->limit($limit, $offset)->findAll();
        $total = $builder->countAllResults(false);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $results,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    private function apiDetail()
    {
        $id = $this->request->getVar('id');
        
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID parameter required'
            ]);
        }
        
        $catalog = $this->katalogModel->find($id);
        
        if (!$catalog) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Catalog not found'
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $catalog
        ]);
    }

        /**
     * Get book recommendations for a member
     */
    public function getRecommendations($memberNo = null)
    {
        if (!$memberNo) {
            $memberNo = $this->request->getVar('member_no');
        }

        if (!$memberNo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Member number is required'
            ]);
        }

        try {
            $result = $this->calculateRecommendations($memberNo);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'recommendations' => $result['recommendations'],
                    'metrics' => [
                        'precision' => $result['precision'],
                        'recall' => $result['recall'],
                        'ndcg' => $result['ndcg'],
                        'accuracy' => $result['accuracy']
                    ],
                    'is_cold_start' => $result['is_cold_start']
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate recommendations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate recommendations using collaborative filtering
     */
    private function calculateRecommendations($memberNo)
    {
        // Get loan data with catalog information
        $loanQuery = "
            SELECT cl.member_id, cl.Collection_id, c.Catalog_id, cat.Title, cat.Author, cat.Subject, cat.CoverURL
            FROM collectionloanitems cl
            JOIN collections c ON cl.Collection_id = c.ID
            JOIN catalogs cat ON c.Catalog_id = cat.ID
        ";
        
        $loanData = $this->db->query($loanQuery)->getResultArray();

        // Check if member exists
        $member = $this->memberModel->where('MemberNo', $memberNo)->first();
        if (!$member) {
            return [
                'recommendations' => [],
                'precision' => 0.0,
                'recall' => 0.0,
                'ndcg' => 0.0,
                'accuracy' => 0.0,
                'is_cold_start' => true
            ];
        }

        $memberId = $member->ID;

        // Get user's loan history
        $userLoans = array_filter($loanData, function($loan) use ($memberId) {
            return $loan['member_id'] == $memberId;
        });

        // Cold start: If user has no loan history, recommend popular books
        if (empty($userLoans)) {
            $popularBooks = $this->getPopularBooks();
            return [
                'recommendations' => $popularBooks,
                'precision' => 0.0,
                'recall' => 0.0,
                'ndcg' => 0.0,
                'accuracy' => 0.0,
                'is_cold_start' => true
            ];
        }

        // Create pivot table (member_id -> catalog_id -> loan_count)
        $pivotTable = $this->createPivotTable($loanData);

        // Calculate cosine similarity
        $similarityMatrix = $this->calculateCosineSimilarity($pivotTable);

        // Get similar members
        $similarMembers = $this->getSimilarMembers($memberId, $similarityMatrix, 10);

        // Get book recommendations
        $recommendations = $this->getBookRecommendations($loanData, $similarMembers, 10);

        // Calculate evaluation metrics
        $userBooks = array_column($userLoans, 'Catalog_id');
        $recommendedBooks = array_column($recommendations, 'ID');
        
        $metrics = $this->calculateMetrics($userBooks, $recommendedBooks);

        return [
            'recommendations' => $recommendations,
            'precision' => $metrics['precision'],
            'recall' => $metrics['recall'],
            'ndcg' => $metrics['ndcg'],
            'accuracy' => $metrics['accuracy'],
            'is_cold_start' => false
        ];
    }

    /**
     * Get popular books for cold start
     */
    private function getPopularBooks($limit = 10)
    {
        $query = "
            SELECT cat.ID, cat.ControlNumber, cat.BIBID, cat.Title, cat.Author, 
                   cat.Edition, cat.Publisher, cat.PublishLocation, cat.PublishYear, 
                   cat.Subject, cat.PhysicalDescription, cat.ISBN, cat.CallNumber, 
                   cat.Languages, cat.CoverURL, cat.IsOPAC, COUNT(*) AS LoanCount
            FROM collectionloanitems cl
            JOIN collections c ON cl.Collection_id = c.ID
            JOIN catalogs cat ON c.Catalog_id = cat.ID
            GROUP BY cat.ID, cat.ControlNumber, cat.BIBID, cat.Title, cat.Author, 
                     cat.Edition, cat.Publisher, cat.PublishLocation, cat.PublishYear, 
                     cat.Subject, cat.PhysicalDescription, cat.ISBN, cat.CallNumber, 
                     cat.Languages, cat.CoverURL, cat.IsOPAC
            ORDER BY LoanCount DESC
            LIMIT ?
        ";
        
        return $this->db->query($query, [$limit])->getResultArray();
    }

    /**
     * Create pivot table from loan data
     */
    private function createPivotTable($loanData)
    {
        $pivotTable = [];
        
        foreach ($loanData as $loan) {
            $memberId = $loan['member_id'];
            $catalogId = $loan['Catalog_id'];
            
            if (!isset($pivotTable[$memberId])) {
                $pivotTable[$memberId] = [];
            }
            
            if (!isset($pivotTable[$memberId][$catalogId])) {
                $pivotTable[$memberId][$catalogId] = 0;
            }
            
            $pivotTable[$memberId][$catalogId]++;
        }
        
        return $pivotTable;
    }

    /**
     * Calculate cosine similarity between members
     */
    private function calculateCosineSimilarity($pivotTable)
    {
        $memberIds = array_keys($pivotTable);
        $allCatalogIds = [];
        
        // Get all unique catalog IDs
        foreach ($pivotTable as $memberData) {
            $allCatalogIds = array_merge($allCatalogIds, array_keys($memberData));
        }
        $allCatalogIds = array_unique($allCatalogIds);
        
        $similarity = [];
        
        foreach ($memberIds as $memberId1) {
            $similarity[$memberId1] = [];
            
            foreach ($memberIds as $memberId2) {
                if ($memberId1 == $memberId2) {
                    $similarity[$memberId1][$memberId2] = 1.0;
                    continue;
                }
                
                // Create vectors for both members
                $vector1 = [];
                $vector2 = [];
                
                foreach ($allCatalogIds as $catalogId) {
                    $vector1[] = $pivotTable[$memberId1][$catalogId] ?? 0;
                    $vector2[] = $pivotTable[$memberId2][$catalogId] ?? 0;
                }
                
                // Calculate cosine similarity
                $similarity[$memberId1][$memberId2] = $this->cosineSimilarity($vector1, $vector2);
            }
        }
        
        return $similarity;
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    private function cosineSimilarity($vector1, $vector2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Get similar members sorted by similarity score
     */
    private function getSimilarMembers($memberId, $similarityMatrix, $limit)
    {
        if (!isset($similarityMatrix[$memberId])) {
            return [];
        }
        
        $similarities = $similarityMatrix[$memberId];
        unset($similarities[$memberId]); // Remove self
        
        arsort($similarities);
        
        return array_slice(array_keys($similarities), 0, $limit, true);
    }

    /**
     * Get book recommendations based on similar members
     */
    private function getBookRecommendations($loanData, $similarMembers, $limit)
    {
        // Count books borrowed by similar members
        $bookCounts = [];
        
        foreach ($loanData as $loan) {
            if (in_array($loan['member_id'], $similarMembers)) {
                $catalogId = $loan['Catalog_id'];
                if (!isset($bookCounts[$catalogId])) {
                    $bookCounts[$catalogId] = 0;
                }
                $bookCounts[$catalogId]++;
            }
        }
        
        // Sort by count
        arsort($bookCounts);
        
        // Get top books
        $topBookIds = array_slice(array_keys($bookCounts), 0, $limit);
        
        if (empty($topBookIds)) {
            return [];
        }
        
        // Get complete book details from catalogs table
        $placeholders = str_repeat('?,', count($topBookIds) - 1) . '?';
        $query = "
            SELECT ID, ControlNumber, BIBID, Title, Author, Edition, Publisher, 
                   PublishLocation, PublishYear, Subject, PhysicalDescription, 
                   ISBN, CallNumber, Languages, CoverURL, IsOPAC
            FROM catalogs 
            WHERE ID IN ($placeholders)
        ";
        
        $results = $this->db->query($query, $topBookIds)->getResultArray();
        
        // Maintain the order based on recommendation score
        $orderedResults = [];
        foreach ($topBookIds as $bookId) {
            foreach ($results as $book) {
                if ($book['ID'] == $bookId) {
                    $orderedResults[] = $book;
                    break;
                }
            }
        }
        
        return $orderedResults;
    }

    /**
     * Calculate evaluation metrics
     */
    private function calculateMetrics($userBooks, $recommendedBooks)
    {
        $userBooksSet = array_flip($userBooks);
        $recommendedBooksSet = array_flip($recommendedBooks);
        
        // Find intersection (relevant recommended books)
        $relevantRecommended = array_intersect_key($recommendedBooksSet, $userBooksSet);
        
        // Precision = relevant recommended / total recommended
        $precision = count($recommendedBooks) > 0 ? count($relevantRecommended) / count($recommendedBooks) : 0.0;
        
        // Recall = relevant recommended / total relevant
        $recall = count($userBooks) > 0 ? count($relevantRecommended) / count($userBooks) : 0.0;
        
        // Accuracy (same as recall in this context)
        $accuracy = $recall;
        
        // NDCG calculation
        $ndcg = $this->calculateNDCG($userBooks, $recommendedBooks);
        
        return [
            'precision' => round($precision, 4),
            'recall' => round($recall, 4),
            'accuracy' => round($accuracy, 4),
            'ndcg' => round($ndcg, 4)
        ];
    }

    /**
     * Calculate NDCG (Normalized Discounted Cumulative Gain)
     */
    private function calculateNDCG($userBooks, $recommendedBooks)
    {
        $userBooksSet = array_flip($userBooks);
        
        // Create relevance scores for recommended books
        $relevanceScores = [];
        foreach ($recommendedBooks as $bookId) {
            $relevanceScores[] = isset($userBooksSet[$bookId]) ? 1 : 0;
        }
        
        // Calculate DCG
        $dcg = 0;
        for ($i = 0; $i < count($relevanceScores); $i++) {
            $dcg += $relevanceScores[$i] / log(2 + $i, 2);
        }
        
        // Calculate IDCG (ideal DCG)
        $idealRelevanceScores = $relevanceScores;
        rsort($idealRelevanceScores);
        
        $idcg = 0;
        for ($i = 0; $i < count($idealRelevanceScores); $i++) {
            $idcg += $idealRelevanceScores[$i] / log(2 + $i, 2);
        }
        
        return $idcg > 0 ? $dcg / $idcg : 0.0;
    }

    /**
     * View recommendations page
     */
    public function recommendations($memberNo = null)
    {
        $this->data['title'] = 'Rekomendasi Buku';
        
        if ($memberNo) {
            $result = $this->calculateRecommendations($memberNo);
            $this->data['recommendations'] = $result['recommendations'];
            $this->data['metrics'] = [
                'precision' => $result['precision'],
                'recall' => $result['recall'],
                'ndcg' => $result['ndcg'],
                'accuracy' => $result['accuracy']
            ];
            $this->data['is_cold_start'] = $result['is_cold_start'];
            $this->data['member_no'] = $memberNo;
        } else {
            $this->data['recommendations'] = [];
            $this->data['metrics'] = null;
            $this->data['is_cold_start'] = false;
            $this->data['member_no'] = '';
        }
        
        return view('Opac\Views\recommendations', $this->data);
    }

}