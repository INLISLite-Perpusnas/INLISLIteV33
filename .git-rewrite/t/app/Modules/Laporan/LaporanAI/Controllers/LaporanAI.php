<?php 
namespace LaporanAI\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanAI extends \Base\Controllers\BaseController 
{
    public $auth;
    public $authorize;
    public $anggotaModel;
    private $geminiApiKey;
    private $db;

    function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY'); // Set di .env file
        $this->db = db_connect();
        
        // Handle MySQL ONLY_FULL_GROUP_BY mode
        $this->configureMySQLMode();
    }

    /**
     * Configure MySQL mode to handle ONLY_FULL_GROUP_BY
     */
    private function configureMySQLMode()
    {
        try {
            // Get current SQL mode
            $result = $this->db->query("SELECT @@sql_mode as sql_mode");
            $currentMode = $result->getRowArray()['sql_mode'];
            
            // Remove ONLY_FULL_GROUP_BY if present
            $newMode = str_replace('ONLY_FULL_GROUP_BY,', '', $currentMode);
            $newMode = str_replace(',ONLY_FULL_GROUP_BY', '', $newMode);
            $newMode = str_replace('ONLY_FULL_GROUP_BY', '', $newMode);
            
            // Set new mode for this session
            $this->db->query("SET sql_mode = '{$newMode}'");
            
        } catch (\Exception $e) {
            // If we can't change the mode, we'll handle it in individual queries
            log_message('warning', 'Could not modify SQL mode: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $this->data['title'] = 'Database Explorer - AI SQL Assistant';
        return view('LaporanAI\Views\index', $this->data);
    }

    /**
     * Get all database tables
     */
    public function getTables()
    {
        try {
            $query = $this->db->query("SHOW TABLES");
            $tables = [];
            
            foreach ($query->getResultArray() as $row) {
                $tableName = array_values($row)[0];
                
                // Get table info - Fixed query for ONLY_FULL_GROUP_BY mode
                try {
                    // Get row count
                    $countQuery = $this->db->query("SELECT COUNT(*) as row_count FROM `{$tableName}`");
                    $rowCount = $countQuery->getRowArray()['row_count'] ?? 0;
                    
                    // Get table size
                    $sizeQuery = $this->db->query("SELECT 
                        ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS size_mb
                        FROM information_schema.tables 
                        WHERE table_schema = DATABASE() 
                        AND table_name = ?", [$tableName]);
                    
                    $sizeInfo = $sizeQuery->getRowArray();
                    $sizeMb = $sizeInfo['size_mb'] ?? 0;
                    
                } catch (\Exception $e) {
                    // Fallback if queries fail
                    $rowCount = 0;
                    $sizeMb = 0;
                }
                
                $tables[] = [
                    'name' => $tableName,
                    'row_count' => $rowCount,
                    'size_mb' => $sizeMb
                ];
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $tables
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get table structure
     */
    public function getTableStructure($tableName)
    {
        try {
            $query = $this->db->query("DESCRIBE `{$tableName}`");
            $structure = $query->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $structure
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get table data with pagination
     */
    public function getTableData($tableName)
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $limit = $this->request->getGet('limit') ?? 50;
            $offset = ($page - 1) * $limit;
            
            // Get total count
            $countQuery = $this->db->query("SELECT COUNT(*) as total FROM `{$tableName}`");
            $total = $countQuery->getRowArray()['total'];
            
            // Get data
            $dataQuery = $this->db->query("SELECT * FROM `{$tableName}` LIMIT {$limit} OFFSET {$offset}");
            $data = $dataQuery->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_rows' => $total,
                    'limit' => $limit
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Execute custom SQL query
     */
    public function executeQuery()
    {
        try {
            $sql = $this->request->getJSON()->sql ?? '';
            
            if (empty($sql)) {
                throw new \Exception('SQL query is required');
            }
            
            // Basic security check - only allow SELECT statements
            $sql = trim($sql);
            if (!preg_match('/^SELECT\s+/i', $sql)) {
                throw new \Exception('Only SELECT statements are allowed');
            }
            
            $query = $this->db->query($sql);
            $data = $query->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'affected_rows' => $query->getNumRows()
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Convert natural language to SQL using Gemini AI
     */
    public function naturalToSQL()
    {
        try {
            $naturalQuery = $this->request->getJSON()->query ?? '';
            
            if (empty($naturalQuery)) {
                throw new \Exception('Natural language query is required');
            }
            
            // Get database schema
            $schema = $this->getDatabaseSchema();
            
            // Prepare prompt for Gemini
            $prompt = $this->buildPrompt($naturalQuery, $schema);
           
            
            // Call Gemini API
            $sqlQuery = $this->callGeminiAPI($prompt);
            
            return $this->response->setJSON([
                'status' => 'success',
                'sql' => $sqlQuery,
                'original_query' => $naturalQuery
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get all database tables - Alternative method
     */
    public function getTablesAlternative()
    {
        try {
            $query = $this->db->query("SHOW TABLES");
            $tables = [];
            
            foreach ($query->getResultArray() as $row) {
                $tableName = array_values($row)[0];
                
                // Simple approach - just get row count
                try {
                    $countQuery = $this->db->query("SELECT COUNT(*) as row_count FROM `{$tableName}`");
                    $rowCount = $countQuery->getRowArray()['row_count'] ?? 0;
                } catch (\Exception $e) {
                    $rowCount = 0;
                }
                
                $tables[] = [
                    'name' => $tableName,
                    'row_count' => $rowCount,
                    'size_mb' => 0 // We'll skip size calculation to avoid GROUP BY issues
                ];
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $tables
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get database schema for AI context - Improved version
     */
    private function getDatabaseSchema()
    {
        $schema = [];
        
        try {
            // Get all tables
            $tablesQuery = $this->db->query("SHOW TABLES");
            
            foreach ($tablesQuery->getResultArray() as $row) {
                $tableName = array_values($row)[0];
                
                try {
                    // Get columns for each table using SHOW COLUMNS (more reliable)
                    $columnsQuery = $this->db->query("SHOW COLUMNS FROM `{$tableName}`");
                    $columns = $columnsQuery->getResultArray();
                    
                    $schema[$tableName] = [];
                    foreach ($columns as $column) {
                        $schema[$tableName][] = [
                            'name' => $column['Field'],
                            'type' => $column['Type'],
                            'null' => $column['Null'],
                            'key' => $column['Key'],
                            'default' => $column['Default']
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip this table if there's an error
                    continue;
                }
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting database schema: ' . $e->getMessage());
        }
        
        return $schema;
    }

    /**
     * Build prompt for Gemini AI
     */
    private function buildPrompt($naturalQuery, $schema)
    {
        $schemaText = "Database Schema:\n";
        foreach ($schema as $tableName => $columns) {
            $schemaText .= "\nTable: {$tableName}\n";
            foreach ($columns as $column) {
                $schemaText .= "  - {$column['name']} ({$column['type']})";
                if ($column['key'] === 'PRI') $schemaText .= " [PRIMARY KEY]";
                $schemaText .= "\n";
            }
        }
        
        $prompt = "You are a SQL expert. Convert the following natural language query to SQL based on the provided database schema.

{$schemaText}

Natural Language Query: {$naturalQuery}

Rules:
1. Only generate SELECT statements
2. Use proper table and column names from the schema
3. Include appropriate WHERE, JOIN, ORDER BY, and GROUP BY clauses as needed
4. Return only the SQL query without explanation
5. Use MySQL syntax

SQL Query:";

        return $prompt;
    }

    /**
     * Call Gemini AI API
     */
    private function callGeminiAPI($prompt)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->geminiApiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        
        if ($httpCode !== 200) {
            throw new \Exception('Failed to call Gemini API');
        }
        
        $responseData = json_decode($response, true);
        
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response from Gemini API');
        }
        
        $sqlQuery = $responseData['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean up the response
        $sqlQuery = trim($sqlQuery);
        $sqlQuery = preg_replace('/^```sql\s*\n?/', '', $sqlQuery);
        $sqlQuery = preg_replace('/\n?```$/', '', $sqlQuery);
        
        return $sqlQuery;
    }

    /**
     * Export query results to Excel
     */
    public function exportToExcel()
    {
        try {
            $sql = $this->request->getJSON()->sql ?? '';
            
            if (empty($sql)) {
                throw new \Exception('SQL query is required');
            }
            
            $query = $this->db->query($sql);
            $data = $query->getResultArray();
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Add headers
            if (!empty($data)) {
                $headers = array_keys($data[0]);
                $sheet->fromArray($headers, null, 'A1');
                
                // Add data
                $sheet->fromArray($data, null, 'A2');
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'query_results_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filepath = WRITEPATH . 'uploads/' . $filename;
            
            $writer->save($filepath);
            
            return $this->response->download($filepath, null)->setFileName($filename);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}