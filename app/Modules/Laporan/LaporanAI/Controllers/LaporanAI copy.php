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
    }

    public function index()
    {
        $this->data['title'] = 'Laporan AI dengan Gemini';
        return view('LaporanAI\Views\index', $this->data);
    }

    /**
     * Process AI query using Gemini API
     */
    public function processQuery()
    {
        // Set proper JSON content type at the beginning
        $this->response->setContentType('application/json');
        
        try {
            $request = service('request');
            $userQuery = $request->getPost('query');
            
            if (empty($userQuery)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Query tidak boleh kosong'
                ]);
            }

            // Validate Gemini API key
            if (empty($this->geminiApiKey)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gemini API key tidak dikonfigurasi'
                ]);
            }

            // 1. Get database schema context
            $schemaContext = $this->getDatabaseSchema();
            
            // 2. Create prompt for Gemini
            $prompt = $this->createPrompt($userQuery, $schemaContext);
            
            // 3. Send to Gemini API
            $geminiResponse = $this->callGeminiAPI($prompt);
            
            // Log response for debugging
            log_message('debug', 'Gemini Response: ' . $geminiResponse);
            
            // 4. Parse SQL from Gemini response
            $sqlQuery = $this->extractSQLFromResponse($geminiResponse);
            
            if (empty($sqlQuery)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menghasilkan query SQL yang valid',
                    'debug_response' => $geminiResponse // For debugging
                ]);
            }

            // 5. Execute SQL query
            $result = $this->executeQuery($sqlQuery);
            
            // 6. Generate chart configuration
            $chartConfig = $this->generateChartConfig($result['data'], $userQuery);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result['data'],
                'columns' => $result['columns'],
                'sql' => $sqlQuery,
                'chart' => $chartConfig,
                'summary' => $this->generateSummary($result['data'], $userQuery)
                // Remove gemini_response from production response for security
            ]);

        } catch (\Exception $e) {
            log_message('error', 'LaporanAI Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ]);
        }
    }

    /**
     * Get database schema for context
     */
    private function getDatabaseSchema()
    {
        $tables = [
            'catalogs' => 'Tabel katalog buku/koleksi perpustakaan',
            'collections' => 'Tabel eksemplar fisik koleksi',
            'members' => 'Tabel anggota perpustakaan', 
            'collectionloanitems' => 'Tabel detail peminjaman koleksi',
            'collectionloans' => 'Tabel header peminjaman',
            'collectioncategorys' => 'Tabel kategori koleksi',
            'collectionmedias' => 'Tabel bentuk fisik media',
            'collectionsources' => 'Tabel sumber pengadaan',
            'collectionrules' => 'Tabel aturan peminjaman',
            'collectionlocations' => 'Tabel lokasi koleksi',
            'bacaditempat' => 'Tabel kunjungan baca di tempat',
            'memberguesses' => 'Tabel pengunjung non-anggota',
            'groupguesses' => 'Tabel kunjungan rombongan',
            'jenis_anggota' => 'Tabel jenis keanggotaan'
        ];

        $schema = "DATABASE SCHEMA PERPUSTAKAAN:\n\n";
        
        foreach ($tables as $table => $description) {
            $schema .= "Tabel: {$table} - {$description}\n";
            
            // Get column information with error handling
            try {
                $columns = $this->db->getFieldData($table);
                if (!empty($columns)) {
                    foreach ($columns as $column) {
                        $schema .= "  - {$column->name} ({$column->type})\n";
                    }
                } else {
                    $schema .= "  - (No columns found)\n";
                }
            } catch (\Exception $e) {
                log_message('warning', "Cannot get field data for table {$table}: " . $e->getMessage());
                $schema .= "  - (Unable to retrieve column information)\n";
            }
            $schema .= "\n";
        }

        return $schema;
    }

    /**
     * Create prompt for Gemini API
     */
    private function createPrompt($userQuery, $schemaContext)
    {
        return "
Anda adalah AI assistant untuk sistem perpustakaan. 
Berdasarkan pertanyaan user dan schema database berikut, buatkan query SQL yang tepat.

{$schemaContext}

ATURAN:
1. Hanya gunakan tabel dan kolom yang ada di schema
2. Gunakan JOIN yang tepat untuk menghubungkan tabel
3. Berikan query SQL yang aman (hindari DROP, DELETE, UPDATE)
4. Fokus pada SELECT statement
5. Gunakan alias yang jelas untuk kolom
6. Tambahkan GROUP BY dan aggregate functions jika diperlukan
7. Jika diperlukan LIMIT, gunakan maksimal 100 rows
8. Pastikan nama kolom dan tabel sesuai dengan schema

PERTANYAAN USER: {$userQuery}

Berikan response dalam format:
```sql
[SQL QUERY HERE]
```

PENJELASAN: [Penjelasan singkat tentang query]
";
    }

    /**
     * Call Gemini API with improved error handling
     */
    private function callGeminiAPI($prompt)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=" . $this->geminiApiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'topK' => 1,
                'topP' => 1,
                'maxOutputTokens' => 2048
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Increased timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception("cURL Error: " . $curlError);
        }

        if ($httpCode !== 200) {
            log_message('error', "Gemini API HTTP {$httpCode}. Response: " . $response);
            throw new \Exception("Gemini API error: HTTP {$httpCode}");
        }

        if (empty($response)) {
            throw new \Exception("Empty response from Gemini API");
        }

        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', "Invalid JSON from Gemini: " . $response);
            throw new \Exception("Invalid JSON response from Gemini API: " . json_last_error_msg());
        }

        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            log_message('error', "Invalid Gemini response structure: " . json_encode($result));
            throw new \Exception("Invalid response structure from Gemini API");
        }

        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * Extract SQL query from Gemini response
     */
    private function extractSQLFromResponse($response)
    {
        // Extract SQL from ```sql code blocks
        if (preg_match('/```sql\s*(.*?)\s*```/s', $response, $matches)) {
            return trim($matches[1]);
        }
        
        // Fallback: look for SELECT statements
        if (preg_match('/SELECT\s+.*?(?=\n\n|\nPENJELASAN|$)/si', $response, $matches)) {
            return trim($matches[0]);
        }

        // Last fallback: look for any SQL-like pattern
        if (preg_match('/SELECT\s+.*/i', $response, $matches)) {
            return trim($matches[0]);
        }

        return '';
    }

    /**
     * Execute SQL query safely with improved error handling
     */
    private function executeQuery($sql)
    {
        // Security check: only allow SELECT statements
        if (!preg_match('/^\s*SELECT\s+/i', $sql)) {
            throw new \Exception("Hanya SELECT query yang diizinkan");
        }

        // Additional security checks - use word boundaries to avoid false positives
        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'ALTER', 'CREATE', 'TRUNCATE', 'EXEC', 'EXECUTE'];
        foreach ($dangerousKeywords as $keyword) {
            // Use word boundaries (\b) to match whole words only
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $sql)) {
                throw new \Exception("Query mengandung keyword berbahaya: {$keyword}");
            }
        }

        try {
            // Test database connection
            if (!$this->db->connID) {
                throw new \Exception("Database connection failed");
            }

            $query = $this->db->query($sql);
            
            if (!$query) {
                $error = $this->db->error();
                throw new \Exception("SQL Error: " . $error['message']);
            }

            $result = $query->getResultArray();
            
            $columns = [];
            if (!empty($result)) {
                $columns = array_keys($result[0]);
            }

            return [
                'data' => $result,
                'columns' => $columns
            ];

        } catch (\Exception $e) {
            log_message('error', 'SQL Execution Error: ' . $e->getMessage());
            log_message('error', 'SQL Query: ' . $sql);
            throw new \Exception("Error executing query: " . $e->getMessage());
        }
    }

    /**
     * Generate chart configuration based on data
     */
    private function generateChartConfig($data, $userQuery)
    {
        if (empty($data)) {
            return null;
        }

        $columns = array_keys($data[0]);
        
        // Detect if suitable for chart
        if (count($columns) < 2) {
            return null;
        }

        // Find label column (first text column)
        $labelColumn = $columns[0];
        
        // Find value columns (numeric columns)
        $valueColumns = [];
        foreach ($columns as $col) {
            if ($col !== $labelColumn && is_numeric($data[0][$col])) {
                $valueColumns[] = $col;
            }
        }

        if (empty($valueColumns)) {
            return null;
        }

        // Determine chart type based on query
        $chartType = 'bar';
        if (stripos($userQuery, 'trend') !== false || stripos($userQuery, 'waktu') !== false) {
            $chartType = 'line';
        } elseif (stripos($userQuery, 'distribusi') !== false || stripos($userQuery, 'proporsi') !== false) {
            $chartType = 'pie';
        }

        return [
            'type' => $chartType,
            'labels' => array_column($data, $labelColumn),
            'datasets' => array_map(function($col) use ($data) {
                return [
                    'label' => $col,
                    'data' => array_column($data, $col),
                    'backgroundColor' => $this->generateColors(count($data))
                ];
            }, $valueColumns)
        ];
    }

    /**
     * Generate colors for chart
     */
    private function generateColors($count)
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];
        
        return array_slice($colors, 0, $count);
    }

    /**
     * Generate summary text
     */
    private function generateSummary($data, $userQuery)
    {
        if (empty($data)) {
            return "Tidak ada data ditemukan untuk query: " . $userQuery;
        }

        $totalRows = count($data);
        $columns = array_keys($data[0]);
        
        return "Ditemukan {$totalRows} baris data dengan kolom: " . implode(', ', $columns);
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $request = service('request');
        $data = json_decode($request->getPost('data'), true);
        $columns = json_decode($request->getPost('columns'), true);
        
        if (empty($data) || empty($columns)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $col = 1;
        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($col, 1, $column);
            $col++;
        }

        // Set data
        $row = 2;
        foreach ($data as $rowData) {
            $col = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($col, $row, $rowData[$column] ?? '');
                $col++;
            }
            $row++;
        }

        // Style headers
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->getStyle('1:1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFCCCCCC');

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'laporan_ai_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        $request = service('request');
        $data = json_decode($request->getPost('data'), true);
        $columns = json_decode($request->getPost('columns'), true);
        $query = $request->getPost('query') ?? 'Laporan Data';
        
        if (empty($data) || empty($columns)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor');
        }

        // Create HTML table
        $html = "
        <html>
        <head>
            <style>
                table { width: 100%; border-collapse: collapse; font-size: 12px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                h1 { text-align: center; color: #333; }
                .summary { margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <h1>Laporan AI - Perpustakaan</h1>
            <div class='summary'>
                <strong>Query:</strong> {$query}<br>
                <strong>Tanggal:</strong> " . date('d/m/Y H:i:s') . "<br>
                <strong>Total Data:</strong> " . count($data) . " baris
            </div>
            <table>
                <thead>
                    <tr>";
        
        foreach ($columns as $column) {
            $html .= "<th>{$column}</th>";
        }
        
        $html .= "</tr></thead><tbody>";
        
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($columns as $column) {
                $html .= "<td>" . htmlspecialchars($row[$column] ?? '') . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</tbody></table></body></html>";

        // Generate PDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan_ai_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, array('Attachment' => true));
    }
}