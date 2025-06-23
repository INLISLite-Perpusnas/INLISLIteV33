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

    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->db=\Config\Database::connect('data');
        $this->fileModel = new \Katalog\Models\FileModel();
    }

    public function index()
    {
        $this->data['title'] = 'OPAC - Online Public Access Catalog';
        
        // Ambil data katalog dengan pagination
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
        
        return view('Opac\Views\index', $this->data);
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
}