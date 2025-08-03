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
    public $katalogRuasModel;


    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->db = \Config\Database::connect('data');
        $this->fileModel = new \Katalog\Models\FileModel();
        $this->memberModel = new \Anggota\Models\AnggotaModel();
        $this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();

        helper('opac');
        helper('sanitize');
    }


    public function index()
    {
         // 🎯 1. Mulai timer
        $startTime = microtime(true);
        $this->data['title'] = 'OPAC - Online Public Access Catalog';

        // Check for member number (for recommendations)
        $memberNo = $this->request->getVar('member_no');

        if ($memberNo) {
            // Handle recommendations
            try {
                $result = $this->calculateRecommendations($memberNo);

                $this->data['member_no'] = $memberNo;
                $this->data['recommendations'] = $result['recommendations'];
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

        }
    $endTime = microtime(true);
        $this->data['execution_time'] = $endTime - $startTime;
        return view('Opac\Views\index', $this->data);
    }

    private function loadRegularCatalogs()
    {
        $perPage = 12;
        $currentPage = $this->request->getVar('page') ?? 1;

        // Pencarian utama
        $search = sanitizeSearch($this->request->getVar('search'));
        $searchBy = sanitizeSearch($this->request->getVar('search_by') ?? 'Title');



        $builder = $this->katalogModel->select('catalogs.*')->orderBy("ID", "Desc");

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

        // 🎯 Multiple search tambahan: cek jika ada di URL
        $additionalFilters = ['Publisher', 'Author', 'PublishLocation', 'Subject', 'PublishYear'];
        foreach ($additionalFilters as $filter) {
            $value = sanitizeSearch($this->request->getVar($filter));
            if (!empty($value)) {
                $builder->like($filter, $value);
            }
        }

        // --- Bagian yang sudah ada ---
        $this->data['catalogs'] = $builder->paginate($perPage);
        $catalogs = $this->data['catalogs'];

        $publishers = array_column($catalogs, 'Publisher');
        $cleaned_publishers = array_map(fn($p) => rtrim(trim($p), ','), $publishers);
        $publisher_counts = array_count_values($cleaned_publishers);

        $author = array_column($catalogs, 'Author');
        $cleaned_author = array_map(fn($a) => rtrim(trim($a), ','), $author);
        $author_counts = array_count_values($cleaned_author);

        $publish_location = array_column($catalogs, 'PublishLocation');
        $cleaned_publish_location = array_map(fn($l) => rtrim(trim($l), ','), $publish_location);
        $publish_location_counts = array_count_values($cleaned_publish_location);

        $subject = array_column($catalogs, 'Subject');
        $cleaned_subject = array_map(fn($s) => rtrim(trim($s), ','), $subject);
        $subject_counts = array_count_values($cleaned_subject);

        $created_dates = array_column($catalogs, 'CreateDate');
        $years = array_map(fn($d) => date('Y', strtotime($d)), $created_dates);
        $year_counts = array_count_values($years);

        $this->data['year_counts'] = $year_counts;
        $this->data['publish_location_counts'] = $publish_location_counts;
        $this->data['subject_counts'] = $subject_counts;
        $this->data['author_counts'] = $author_counts;
        $this->data['publisher_counts'] = $publisher_counts;

        $this->data['pager'] = $this->katalogModel->pager;
        $this->data['search'] = $search;
        $this->data['search_by'] = $searchBy;
        $this->data['total_records'] = $builder->countAllResults(false);
    }

 

    public function detail($id)
    {
        $file = $this->fileModel->where('Catalog_id', $id)->first();
        if ($file !== null) {
            $ID = $file->ID;
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

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->findAll();

        $this->data['marc'] = $marc;



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
            'ID',
            'Control Number',
            'BIBID',
            'Title',
            'Author',
            'Edition',
            'Publisher',
            'Publish Location',
            'Publish Year',
            'Subject',
            'Physical Description',
            'ISBN',
            'Call Number',
            'Languages'
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
 

    public function downloadMarcUtf8($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $content = "=LDR  00000nam  2200000   4500\n";

        foreach ($marc as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
            $ind1 = $field->Indicator1 ?: ' ';
            $ind2 = $field->Indicator2 ?: ' ';
            $value = $field->Value;

            if (intval($tag) < 10) {
                // Control fields
                $content .= "={$tag}  {$value}\n";
            } else {
                // Data fields
                $content .= "={$tag}  {$ind1}{$ind2}\${$value}\n";
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_UTF8.txt';

        return $this->response
            ->setContentType('text/plain; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($content);
    }

    /**
     * Download MARC in XML format
     */
    public function downloadMarcXml($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        // Root element
        $collection = $xml->createElement('collection');
        $collection->setAttribute('xmlns', 'http://www.loc.gov/MARC21/slim');
        $xml->appendChild($collection);

        // Record element
        $record = $xml->createElement('record');
        $collection->appendChild($record);

        // Leader
        $leader = $xml->createElement('leader', '00000nam  2200000   4500');
        $record->appendChild($leader);

        foreach ($marc as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);

            if (intval($tag) < 10) {
                // Control field
                $controlfield = $xml->createElement('controlfield', htmlspecialchars($field->Value));
                $controlfield->setAttribute('tag', $tag);
                $record->appendChild($controlfield);
            } else {
                // Data field
                $datafield = $xml->createElement('datafield');
                $datafield->setAttribute('tag', $tag);
                $datafield->setAttribute('ind1', $field->Indicator1 ?: ' ');
                $datafield->setAttribute('ind2', $field->Indicator2 ?: ' ');

                // Parse subfields
                $subfields = $this->parseSubfields($field->Value);
                foreach ($subfields as $code => $value) {
                    $subfield = $xml->createElement('subfield', htmlspecialchars($value));
                    $subfield->setAttribute('code', $code);
                    $datafield->appendChild($subfield);
                }

                $record->appendChild($datafield);
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_XML.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download in MODS format
     */
    public function downloadMarcMods($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $mods = $xml->createElement('mods');
        $mods->setAttribute('xmlns', 'http://www.loc.gov/mods/v3');
        $mods->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $mods->setAttribute('xsi:schemaLocation', 'http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-7.xsd');
        $xml->appendChild($mods);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $titleInfo = $xml->createElement('titleInfo');
                    $title = $xml->createElement('title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $titleInfo->appendChild($title);
                    $subtitle = $this->extractSubfield($value, 'b');
                    if ($subtitle) {
                        $subTitle = $xml->createElement('subTitle', htmlspecialchars($subtitle));
                        $titleInfo->appendChild($subTitle);
                    }
                    $mods->appendChild($titleInfo);
                    break;

                case '100': // Author
                    $name = $xml->createElement('name');
                    $name->setAttribute('type', 'personal');
                    $namePart = $xml->createElement('namePart', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $name->appendChild($namePart);
                    $role = $xml->createElement('role');
                    $roleTerm = $xml->createElement('roleTerm', 'author');
                    $roleTerm->setAttribute('type', 'text');
                    $role->appendChild($roleTerm);
                    $name->appendChild($role);
                    $mods->appendChild($name);
                    break;

                case '260': // Publication info
                    $originInfo = $xml->createElement('originInfo');
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('publisher', htmlspecialchars($publisher));
                        $originInfo->appendChild($pub);
                    }
                    $dateIssued = $this->extractSubfield($value, 'c');
                    if ($dateIssued) {
                        $date = $xml->createElement('dateIssued', htmlspecialchars($dateIssued));
                        $originInfo->appendChild($date);
                    }
                    $place = $this->extractSubfield($value, 'a');
                    if ($place) {
                        $placeTerm = $xml->createElement('placeTerm', htmlspecialchars($place));
                        $placeTerm->setAttribute('type', 'text');
                        $placeElement = $xml->createElement('place');
                        $placeElement->appendChild($placeTerm);
                        $originInfo->appendChild($placeElement);
                    }
                    $mods->appendChild($originInfo);
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('subject');
                    $topic = $xml->createElement('topic', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $subject->appendChild($topic);
                    $mods->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_MODS.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core RDF format
     */
    public function downloadMarcRdf($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $rdf = $xml->createElement('rdf:RDF');
        $rdf->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $rdf->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xml->appendChild($rdf);

        $description = $xml->createElement('rdf:Description');
        $description->setAttribute('rdf:about', 'http://example.com/catalog/' . $catalog['ID']);
        $rdf->appendChild($description);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($creator);
                    break;

                case '260': // Publisher
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $description->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $description->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_RDF.xml';

        return $this->response
            ->setContentType('application/rdf+xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core OAI format
     */
    public function downloadMarcOai($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $oai_dc = $xml->createElement('oai_dc:dc');
        $oai_dc->setAttribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
        $oai_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $oai_dc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $oai_dc->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
        $xml->appendChild($oai_dc);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($creator);
                    break;

                case '260': // Publisher and Date
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $oai_dc->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $oai_dc->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_OAI.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core SRW format
     */
    public function downloadMarcSrw($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $srw_dc = $xml->createElement('srw_dc:dc');
        $srw_dc->setAttribute('xmlns:srw_dc', 'info:srw/schema/1/dc-schema');
        $srw_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xml->appendChild($srw_dc);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($creator);
                    break;

                case '260': // Publisher and Date
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $srw_dc->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $srw_dc->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_SRW.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Helper method to parse subfields
     */
    private function parseSubfields($value)
    {
        $subfields = [];
        $parts = explode('$', $value);

        foreach ($parts as $part) {
            if (strlen($part) >= 2) {
                $code = substr($part, 0, 1);
                $text = substr($part, 1);
                $subfields[$code] = trim($text);
            }
        }

        return $subfields;
    }

    /**
     * Helper method to extract specific subfield
     */
    private function extractSubfield($value, $subfieldCode)
    {
        $subfields = $this->parseSubfields($value);
        return isset($subfields[$subfieldCode]) ? $subfields[$subfieldCode] : '';
    }
}
