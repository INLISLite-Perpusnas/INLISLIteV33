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
    public $memberModel;
    public $collectionLoanModel;
    public $eksemplarModel;
    public $data = [];
    public $db;

    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->db = \Config\Database::connect('data');
        $this->fileModel = new \Katalog\Models\FileModel();
        
        // Add new models for recommendation
        $this->memberModel = new \Member\Models\MemberModel();
        $this->collectionLoanModel = new \CollectionLoan\Models\CollectionLoanModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
    }

    // ... existing methods (index, detail, search, browse, export, etc.) ...

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

        $memberId = $member['ID'];

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

    // ... rest of existing methods (statistics, api, etc.) ...
}