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
    public $bannerModel;


    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->bannerModel = new \Banner\Models\BannerModel();
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
    $startTime = microtime(true);
    $this->data['title'] = 'OPAC - Online Public Access Catalog';

    // ✅ Validasi member_no
    $memberNo = $this->request->getVar('member_no') ?? '';
    if (!empty($memberNo) && !preg_match('/^[a-zA-Z0-9\-]{0,50}$/', $memberNo)) {
        $memberNo = '';
    }
    $memberNo = esc($memberNo);

    if ($memberNo) {
        try {
            $result = $this->calculateRecommendations($memberNo);

            $this->data['member_no']      = $memberNo;
            $this->data['recommendations'] = $result['recommendations'];
            $this->data['is_cold_start']   = $result['is_cold_start'];
            $this->data['catalogs']        = [];
            $this->data['pager']           = null;
            $this->data['search']          = null;
            $this->data['search_by']       = null;
            $this->data['total_records']   = 0;

        } catch (\Exception $e) {
            $this->data['member_no']          = $memberNo;
            $this->data['recommendations']    = [];
            $this->data['metrics']            = null;
            $this->data['is_cold_start']      = true;
            $this->data['recommendation_error'] = esc($e->getMessage()); // ✅ escape error message
            $this->loadRegularCatalogs();
        }
    } else {
        $is_opac_cache = env('is_opac_cache', 0);
        if ($is_opac_cache == 1) {
            $this->loadRegularCatalogscache();
        } else {
            $this->loadRegularCatalogs();
        }
    }

    $this->data['opac_banners'] = $this->getOpacBanners();
    $endTime = microtime(true);
    $this->data['execution_time'] = $endTime - $startTime;
    return view('Opac\Views\index', $this->data);
}

private function getOpacBanners()
{
    try {
        return $this->bannerModel->where('active', 1)->where('category', 'Opac')->orderBy('sort', 'ASC')->findAll();
    } catch (\Exception $e) {
        return [];
    }
}

private function loadRegularCatalogs()
{
    $perPage     = 12;
    $currentPage = $this->request->getVar('page') ?? 1;

    $builder = $this->katalogModel->select('catalogs.*')->orderBy("ID", "DESC");

    $search = sanitizeSearch($this->request->getVar('search'));

    if ($search) {
        $rawSearch = $this->request->getVar('search');
        $searchBy  = sanitizeSearch($this->request->getVar('search_by') ?? 'Title');
        $this->applyFulltextSearch($builder, $search, $searchBy, $rawSearch);
    }

    $additionalFilters = ['Publisher', 'Author', 'PublishLocation', 'Subject', 'PublishYear'];
    foreach ($additionalFilters as $filter) {
        $value = $this->request->getVar($filter);
        if (!empty($value)) {
            if ($filter === 'Author') {
                $authors = array_filter(array_map('trim', preg_split('/[;,]+/', $value)));
                foreach ($authors as $author) {
                    $builder->orLike('Author', sanitizeSearch($author));
                }
            } else {
                $builder->like($filter, sanitizeSearch($value));
            }
        }
    }

    $catalogs = $builder->paginate($perPage, 'default', $currentPage);
    $pager    = $this->katalogModel->pager;

    $this->data['pager']         = $pager;
    $this->data['catalogs']      = $catalogs;
    $this->data['total_records'] = $pager->getTotal();

    $this->data['publisher_counts']       = array_count_values(array_map(fn($p) => rtrim(trim($p), ','), array_column($catalogs, 'Publisher')));
    $this->data['author_counts']          = array_count_values(array_map(fn($a) => rtrim(trim($a), ','), array_column($catalogs, 'Author')));
    $this->data['publish_location_counts'] = array_count_values(array_map(fn($l) => rtrim(trim($l), ','), array_column($catalogs, 'PublishLocation')));
    $this->data['subject_counts']         = array_count_values(array_map(fn($s) => rtrim(trim($s), ','), array_column($catalogs, 'Subject')));
    $this->data['year_counts']            = array_count_values(array_map(fn($d) => date('Y', strtotime($d)), array_column($catalogs, 'EndDate')));

    $this->data['search']    = esc($this->request->getVar('search') ?? '');
    $allowedSearchBy         = ['Title', 'Author', 'Subject', 'Publisher', 'ISBN'];
    $searchBy                = $this->request->getVar('search_by') ?? 'Title';
    $this->data['search_by'] = in_array($searchBy, $allowedSearchBy) ? $searchBy : 'Title';
}

private function loadRegularCatalogscache()
{
    $cacheTTL    = 3600;
    $perPage     = 12;
    $currentPage = $this->request->getVar('page') ?? 1;

    $requestParams = $this->request->getGet();
    ksort($requestParams);
    $cacheKey = 'catalog_data_' . md5(http_build_query($requestParams));

    if ($cachedData = cache($cacheKey)) {
        // ========== CACHE HIT ==========
        $this->data   = array_merge($this->data, $cachedData);
        $totalRecords = $cachedData['total_records'];

        // ✅ Buat pager HTML string dengan custom template
        $pager = service('pager');
        $pager->setPath('/opac', 'default');
        $this->data['pager'] = $pager->makeLinks($currentPage, $perPage, $totalRecords, 'opac_pagination');

    } else {
        // ========== CACHE MISS ==========
        $builder = $this->katalogModel->select('catalogs.*')->orderBy("ID", "DESC");

        $search = sanitizeSearch($this->request->getVar('search'));

        if ($search) {
            $rawSearch = $this->request->getVar('search');
            $searchBy  = sanitizeSearch($this->request->getVar('search_by') ?? 'Title');
            $this->applyFulltextSearch($builder, $search, $searchBy, $rawSearch);
        }

        $additionalFilters = ['Publisher', 'Author', 'PublishLocation', 'Subject', 'PublishYear'];
        foreach ($additionalFilters as $filter) {
            $value = $this->request->getVar($filter);
            if (!empty($value)) {
                if ($filter === 'Author') {
                    $authors = array_filter(array_map('trim', preg_split('/[;,]+/', $value)));
                    foreach ($authors as $author) {
                        $builder->orLike('Author', sanitizeSearch($author));
                    }
                } else {
                    $builder->like($filter, sanitizeSearch($value));
                }
            }
        }

        $catalogs     = $builder->paginate($perPage, 'default', $currentPage);
        $pager        = $this->katalogModel->pager;
        $totalRecords = $pager->getTotal();

        // ✅ Simpan pager sebagai OBJEK untuk cache miss
        $this->data['pager']         = $pager;
        $this->data['catalogs']      = $catalogs;
        $this->data['total_records'] = $totalRecords;

        $dataToCache = [
            'total_records'          => $totalRecords,
            'catalogs'               => $catalogs,
            'publisher_counts'       => array_count_values(array_map(fn($p) => rtrim(trim($p), ','), array_column($catalogs, 'Publisher'))),
            'author_counts'          => array_count_values(array_map(fn($a) => rtrim(trim($a), ','), array_column($catalogs, 'Author'))),
            'publish_location_counts' => array_count_values(array_map(fn($l) => rtrim(trim($l), ','), array_column($catalogs, 'PublishLocation'))),
            'subject_counts'         => array_count_values(array_map(fn($s) => rtrim(trim($s), ','), array_column($catalogs, 'Subject'))),
            'year_counts'            => array_count_values(array_map(fn($d) => date('Y', strtotime($d)), array_column($catalogs, 'EndDate'))),
        ];

        cache()->save($cacheKey, $dataToCache, $cacheTTL);
        $this->data = array_merge($this->data, $dataToCache);
    }

    $this->data['search']    = esc($this->request->getVar('search') ?? '');
    $allowedSearchBy         = ['Title', 'Author', 'Subject', 'Publisher', 'ISBN'];
    $searchBy                = $this->request->getVar('search_by') ?? 'Title';
    $this->data['search_by'] = in_array($searchBy, $allowedSearchBy) ? $searchBy : 'Title';
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
            ->where('collections.ISDRM', 0)
            ->findAll();


        // Get digital books (DRM)
        $roweksemplar_drm = $EksemplarModel
            ->select('collections.ID as CollectionID, collections.NomorBarcode, collections.CallNumber, collections.Status_id, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
            ->where('collections.ISDRM', 1)
            ->findAll();

        // Kumpulkan ID koleksi DRM yang sedang dipinjam oleh anggota yang login
        $member_active_loan_collections = [];
        try {
            $nomor_anggota = user()->username;
            $member        = $this->memberModel->where('MemberNo', $nomor_anggota)->first();
            if ($member && !empty($roweksemplar_drm)) {
                $collection_ids = array_column((array) $roweksemplar_drm, 'CollectionID');
                $active_loans   = $this->db->table('collectionloanitems')
                    ->whereIn('Collection_id', $collection_ids)
                    ->where('member_id', $member->ID)
                    ->where('LoanStatus', 'Loan')
                    ->where('DueDate >=', date('Y-m-d H:i:s'))
                    ->get()
                    ->getResult();
                $member_active_loan_collections = array_column($active_loans, 'Collection_id');
            }
        } catch (\Throwable $e) {
            // User tidak login atau bukan anggota – abaikan
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->findAll();

        $this->data['marc'] = $marc;

        $this->data['title']       = 'Detail Katalog - ' . $catalog['Title'];
        $this->data['catalog']     = $catalog;
        $this->data['roweksemplar']     = $roweksemplar;
        $this->data['roweksemplar_drm'] = $roweksemplar_drm;
        $this->data['member_active_loan_collections'] = $member_active_loan_collections;

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

        $results     = [];
        $total       = 0;
        $perPage     = 12;
        $currentPage = $this->request->getVar('page') ?? 1;

        if ($this->request->getMethod() === 'post' || $this->request->getVar('submit')) {
            $builder = $this->katalogModel->select('catalogs.*');

            if ($searchData['title']) {
                $term = $this->toFtTerm($searchData['title']);
                $term ? $builder->where("MATCH(Title) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Title', $searchData['title']);
            }
            if ($searchData['author']) {
                $term = $this->toFtTerm($searchData['author']);
                $term ? $builder->where("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Author', $searchData['author']);
            }
            if ($searchData['subject']) {
                $term = $this->toFtTerm($searchData['subject']);
                $term ? $builder->where("MATCH(Subject) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Subject', $searchData['subject']);
            }
            if ($searchData['publisher']) {
                $term = $this->toFtTerm($searchData['publisher']);
                $term ? $builder->where("MATCH(Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Publisher', $searchData['publisher']);
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

            $results = $builder->paginate($perPage, 'default', $currentPage);
            $total   = $this->katalogModel->pager->getTotal();
        }

        $this->data['pager']       = $this->katalogModel->pager ?? null;
        $this->data['search_data'] = $searchData;
        $this->data['results']     = $results;
        $this->data['total_found'] = $total;

        return view('Opac\Views\search', $this->data);
    }

public function browse()
{
    $this->data['title'] = 'Browse Katalog';

    $allowedTypes = ['author', 'title', 'subject'];
    $browseType   = $this->request->getVar('type') ?? 'author';
    $browseType   = in_array($browseType, $allowedTypes) ? $browseType : 'author';

    $letter = $this->request->getVar('letter') ?? 'A';
    $letter = preg_match('/^[A-Za-z0-9]$/', $letter) ? strtoupper($letter) : 'A';

    // Statistik
    $this->data['total_authors'] = $this->katalogModel
        ->where('Author !=', '')
        ->countAllResults(false);
    $this->katalogModel->resetQuery();

    $this->data['total_titles'] = $this->katalogModel
        ->countAllResults(false);
    $this->katalogModel->resetQuery();

    $this->data['total_subjects'] = $this->katalogModel
        ->where('Subject !=', '')
        ->countAllResults(false);
    $this->katalogModel->resetQuery();

    $this->data['total_languages'] = $this->katalogModel
        ->select('Languages')
        ->where('Languages !=', '')
        ->groupBy('Languages')
        ->countAllResults(false);
    $this->katalogModel->resetQuery();

    // Pagination
    $perPage     = 12;
    $currentPage = (int) ($this->request->getVar('page_browse') ?? 1);

    // ✅ Perbaiki: hapus '%' manual, cukup pakai parameter 'after'
    switch ($browseType) {
        case 'author':
            $this->katalogModel->like('Author', $letter, 'after')
                               ->orderBy('Author', 'ASC');
            break;
        case 'title':
            $this->katalogModel->like('Title', $letter, 'after')
                               ->orderBy('Title', 'ASC');
            break;
        case 'subject':
            $this->katalogModel->like('Subject', $letter, 'after')
                               ->orderBy('Subject', 'ASC');
            break;
    }

    $this->data['catalogs']    = $this->katalogModel->paginate($perPage, 'browse', $currentPage);
    $this->data['pager']       = $this->katalogModel->pager;
    $this->data['perPage']     = $perPage;
    $this->data['browse_type'] = esc($browseType);
    $this->data['letter']      = esc($letter);
    $this->data['alphabet']    = range('A', 'Z');

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
        ->where("PublishYear REGEXP '^[0-9]{4}$'")
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

    public function statistics_anggota()
    {
        $this->data['title'] = 'Statistik Anggota';

        // 1. Total anggota
        $this->data['total_members'] = $this->memberModel
            ->where('active', 1)
            ->countAllResults();

        // 2. Anggota aktif (IsOnlineActive = 1)
        $this->data['active_members'] = $this->memberModel
            ->where('active', 1)
            ->where('IsOnlineActive', 1)
            ->countAllResults();

        // 3. Anggota baru bulan ini
        $this->data['new_members_this_month'] = $this->memberModel
            ->where('active', 1)
            ->where('MONTH(RegisterDate)', date('m'))
            ->where('YEAR(RegisterDate)', date('Y'))
            ->countAllResults();

        // 4. Distribusi berdasarkan jenis kelamin
        $this->data['by_gender'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN Sex_id = 1 THEN 'Laki-laki'
                    WHEN Sex_id = 2 THEN 'Perempuan'
                    ELSE 'Tidak Diketahui'
                END as gender,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY Sex_id
            ORDER BY total DESC
        ")->getResult();

        // 5. Distribusi berdasarkan jenjang pendidikan
        $this->data['by_education'] = $this->db->query("
            SELECT 
                mp.Nama as education_level,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_pendidikan mp ON m.EducationLevel_id = mp.id
            WHERE m.active = 1
            GROUP BY m.EducationLevel_id, mp.Nama
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();

        // 6. Distribusi berdasarkan pekerjaan
        // Di Controller (Bagian 6)
        $this->data['by_job'] = $this->db->query("
            SELECT 
                COALESCE(mpk.Pekerjaan, 'Lainnya/Tidak Diisi') as job_name,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_pekerjaan mpk ON m.Job_id = mpk.id
            WHERE m.active = 1
            GROUP BY m.Job_id, mpk.Pekerjaan
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();
    
       

        // 7. Registrasi anggota per bulan (12 bulan terakhir)
        $this->data['by_month'] = $this->db->query("
            SELECT 
                DATE_FORMAT(RegisterDate, '%Y-%m') as month,
                DATE_FORMAT(RegisterDate, '%b %Y') as month_name,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND RegisterDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(RegisterDate, '%Y-%m'), DATE_FORMAT(RegisterDate, '%b %Y')
            ORDER BY month DESC
        ")->getResult();

        // 8. Distribusi berdasarkan tahun registrasi
        $this->data['by_year'] = $this->db->query("
            SELECT 
                YEAR(RegisterDate) as year,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND RegisterDate IS NOT NULL
            GROUP BY YEAR(RegisterDate)
            ORDER BY year DESC
            LIMIT 10
        ")->getResult();

        // 9. Distribusi berdasarkan rentang usia
        $this->data['by_age_range'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) < 13 THEN '< 13 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 18 AND 25 THEN '18-25 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 26 AND 35 THEN '26-35 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 36 AND 45 THEN '36-45 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 46 AND 55 THEN '46-55 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) > 55 THEN '> 55 tahun'
                    ELSE 'Tidak Diketahui'
                END as age_range,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND DateOfBirth IS NOT NULL
            GROUP BY age_range
            ORDER BY 
                CASE age_range
                    WHEN '< 13 tahun' THEN 1
                    WHEN '13-17 tahun' THEN 2
                    WHEN '18-25 tahun' THEN 3
                    WHEN '26-35 tahun' THEN 4
                    WHEN '36-45 tahun' THEN 5
                    WHEN '46-55 tahun' THEN 6
                    WHEN '> 55 tahun' THEN 7
                    ELSE 8
                END
        ")->getResult();

        // 10. Distribusi berdasarkan kota/provinsi (top 10)
        $this->data['by_province'] = $this->db->query("
            SELECT 
                COALESCE(Province, 'Tidak Diketahui') as province,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY Province
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();

        // 11. Distribusi berdasarkan status perkawinan
        $this->data['by_marital_status'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN MaritalStatus_id = 1 THEN 'Belum Menikah'
                    WHEN MaritalStatus_id = 2 THEN 'Menikah'
                    WHEN MaritalStatus_id = 3 THEN 'Cerai'
                    ELSE 'Tidak Diketahui'
                END as marital_status,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY MaritalStatus_id
            ORDER BY total DESC
        ")->getResult();

        // 12. Anggota dengan keterlambatan pengembalian
        $this->data['late_return_stats'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN LoanReturnLateCount = 0 THEN 'Tidak Pernah'
                    WHEN LoanReturnLateCount BETWEEN 1 AND 3 THEN '1-3 kali'
                    WHEN LoanReturnLateCount BETWEEN 4 AND 6 THEN '4-6 kali'
                    WHEN LoanReturnLateCount > 6 THEN '> 6 kali'
                END as late_category,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY late_category
            ORDER BY 
                CASE late_category
                    WHEN 'Tidak Pernah' THEN 1
                    WHEN '1-3 kali' THEN 2
                    WHEN '4-6 kali' THEN 3
                    WHEN '> 6 kali' THEN 4
                END
        ")->getResult();

        // 13. Distribusi berdasarkan jenis identitas
        $this->data['by_identity_type'] = $this->db->query("
            SELECT 
                mji.Nama as identity_type,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_jenis_identitas mji ON m.IdentityType_id = mji.id
            WHERE m.active = 1
            GROUP BY m.IdentityType_id, mji.Nama
            ORDER BY total DESC
        ")->getResult();

        // 14. Statistik tambahan
        $this->data['avg_age'] = $this->db->query("
            SELECT 
                ROUND(AVG(TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE())), 1) as avg_age
            FROM members
            WHERE active = 1 
            AND DateOfBirth IS NOT NULL
        ")->getRow()->avg_age ?? 0;

        // 15. Anggota terdaftar hari ini
        $this->data['today_registrations'] = $this->memberModel
            ->where('active', 1)
            ->where('DATE(RegisterDate)', date('Y-m-d'))
            ->countAllResults();

        // 16. Growth rate (perbandingan dengan tahun lalu)
        $currentYearMembers = $this->memberModel
            ->where('active', 1)
            ->where('YEAR(RegisterDate)', date('Y'))
            ->countAllResults();

        $lastYearMembers = $this->memberModel
            ->where('active', 1)
            ->where('YEAR(RegisterDate)', date('Y') - 1)
            ->countAllResults();

        $this->data['growth_rate'] = $lastYearMembers > 0 
            ? (($currentYearMembers - $lastYearMembers) / $lastYearMembers) * 100 
            : 0;

        return view('Opac\Views\statistics_anggota', $this->data);
    }  

        // Anggota peminjam per jenis kelamin


  
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

    /**
     * Apply FULLTEXT search to query builder, fallback to LIKE when term is too short.
     * Requires FULLTEXT indexes on catalogs table — see app/Database/opac_fulltext_indexes.sql
     */
    private function applyFulltextSearch($builder, string $search, string $searchBy, string $rawSearch = null): void
    {
        $rawSearch = $rawSearch ?? $search;

        $builder->groupStart();
        switch ($searchBy) {
            case 'Title':
                $term = $this->toFtTerm($search);
                $term ? $builder->where("MATCH(Title) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Title', $search);
                break;

            case 'Author':
                $authors = array_filter(array_map('trim', preg_split('/[;,]+/', $rawSearch)));
                $first   = true;
                foreach ($authors as $author) {
                    $term = $this->toFtTerm(sanitizeSearch($author));
                    if ($first) {
                        $term ? $builder->where("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                              : $builder->like('Author', sanitizeSearch($author));
                    } else {
                        $term ? $builder->orWhere("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                              : $builder->orLike('Author', sanitizeSearch($author));
                    }
                    $first = false;
                }
                break;

            case 'Subject':
                $term = $this->toFtTerm($search);
                $term ? $builder->where("MATCH(Subject) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Subject', $search);
                break;

            case 'ISBN':
                $builder->like('ISBN', $search);
                break;

            case 'Publisher':
                $term = $this->toFtTerm($search);
                $term ? $builder->where("MATCH(Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Publisher', $search);
                break;

            default: // semua kolom
                $term = $this->toFtTerm($search);
                if ($term) {
                    $builder->where("MATCH(Title, Author, Subject, Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                            ->orLike('ISBN', $search);
                } else {
                    $builder->orLike('Title', $search)
                            ->orLike('Author', $rawSearch)
                            ->orLike('Subject', $search)
                            ->orLike('ISBN', $search)
                            ->orLike('Publisher', $search);
                }
        }
        $builder->groupEnd();
    }

    /**
     * Convert a sanitized search string to MySQL FULLTEXT Boolean mode term.
     * Returns empty string when all words are too short (falls back to LIKE).
     * Input sudah melalui sanitizeSearch() sehingga karakter berbahaya sudah dihapus.
     */
    private function toFtTerm(string $search): string
    {
        // Hapus operator FULLTEXT Boolean dan single quote sebagai lapisan keamanan tambahan
        $search = preg_replace('/[+\-><()\~*"@\'\\\\]/', '', $search);
        $words  = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
        $terms  = [];
        foreach ($words as $word) {
            if (mb_strlen($word) < 2) continue;
            $terms[] = '+' . $word . '*';
        }
        return implode(' ', $terms);
    }

    public function bacaDigital($catalog_id)
    {
        helper('reference');

        $file = $this->fileModel->where('Catalog_id', $catalog_id)->first();
        if (!$file) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File digital tidak ditemukan');
        }

        $nomor_anggota = user()->username;
        $member        = $this->memberModel->where('MemberNo', $nomor_anggota)->first();
        $member_id     = $member ? (int) $member->ID : 0;

        if ($member_id > 0) {
            $collection = $this->db->table('collections')
                ->where('Catalog_id', $catalog_id)
                ->where('ISDRM', 1)
                ->get()
                ->getRow();

            if ($collection) {
                $loanItem = $this->db->table('collectionloanitems')
                    ->where('Collection_id', $collection->ID)
                    ->where('member_id', $member_id)
                    ->where('LoanStatus', 'Loan')
                    ->get()
                    ->getRow();

                if ($loanItem) {
                    if (strtotime($loanItem->DueDate) < time()) {
                        $this->_autoReturnDigitalLoan($loanItem, $collection->ID);
                        return redirect()
                            ->to(base_url('opac/detail/' . $catalog_id))
                            ->with('digital_error', 'Masa peminjaman digital telah berakhir. Koleksi dikembalikan otomatis. Silakan pinjam kembali.');
                    }
                    // Loan masih aktif – langsung izinkan baca
                } else {
                    // Tidak punya loan aktif – cek apakah koleksi masih tersedia
                    if ((int) $collection->Status_id !== 1) {
                        return redirect()
                            ->to(base_url('opac/detail/' . $catalog_id))
                            ->with('digital_info', 'Koleksi digital ini sedang dipinjam oleh anggota lain. Silakan coba lagi nanti.');
                    }
                    // Koleksi tersedia – catat peminjaman baru
                    try {
                        $this->_recordDigitalLoan((int) $catalog_id, $member_id);
                    } catch (\Exception $e) {
                        log_message('error', 'Digital loan recording error: ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->to(base_url('katalog/view_decrypted/' . encData($file->ID)));
    }

    private function _autoReturnDigitalLoan($loanItem, int $collection_id)
    {
        $now = date('Y-m-d H:i:s');
        $ip  = $this->request->getIPAddress();

        $this->db->transBegin();

        $this->db->table('collectionloanitems')
            ->where('ID', $loanItem->ID)
            ->update([
                'LoanStatus'     => 'Return',
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        $loan = $this->db->table('collectionloans')
            ->where('ID', $loanItem->CollectionLoan_id)
            ->get()
            ->getRow();

        if ($loan) {
            $this->db->table('collectionloans')
                ->where('ID', $loanItem->CollectionLoan_id)
                ->update([
                    'ReturnCount'    => (int) ($loan->ReturnCount ?? 0) + 1,
                    'UpdateDate'     => $now,
                    'UpdateTerminal' => $ip,
                ]);
        }

        $this->db->table('collections')
            ->where('ID', $collection_id)
            ->update([
                'Status_id'      => 1,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
        } else {
            $this->db->transCommit();
        }
    }

    private function _recordDigitalLoan(int $catalog_id, int $member_id)
    {
        $collection = $this->db->table('collections')
            ->where('Catalog_id', $catalog_id)
            ->where('ISDRM', 1)
            ->get()
            ->getRow();

        if (!$collection) {
            return;
        }

        $existing = $this->db->table('collectionloanitems')
            ->where('Collection_id', $collection->ID)
            ->where('member_id', $member_id)
            ->where('LoanStatus', 'Loan')
            ->get()
            ->getRow();

        if ($existing) {
            return;
        }

        $member = $this->db->table('members as m')
            ->select('m.ID, m.Branch_id, ja.MaxLoanDays')
            ->join('jenis_anggota as ja', 'ja.ID = m.JenisAnggota_id', 'left')
            ->where('m.ID', $member_id)
            ->get()
            ->getRow();

        if (!$member) {
            return;
        }

        $loanDays = (int) ($member->MaxLoanDays ?? 7);
        $last_loan = get_ref_single('collectionloans', 'ID IS NOT NULL', 'data');
        $lastNumber = $last_loan ? (int) substr($last_loan->ID, -5) : 0;
        $loan_id = get_pad_number($lastNumber + 1, date('ymd'), 5);

        $now       = date('Y-m-d H:i:s');
        $dueDate   = date('Y-m-d H:i:s', strtotime("+{$loanDays} days"));
        $createdBy = session()->get('logged_in') ?? 0;
        $ip        = $this->request->getIPAddress();

        $this->db->transBegin();

        $this->db->table('collectionloans')->insert([
            'ID'                => $loan_id,
            'CollectionCount'   => 1,
            'LateCount'         => 0,
            'ExtendCount'       => 0,
            'LoanCount'         => 1,
            'ReturnCount'       => 0,
            'Member_id'         => $member_id,
            'LocationLibrary_id'=> $collection->Location_Library_id ?? null,
            'Branch_id'         => $member->Branch_id,
            'CreateBy'          => $createdBy,
            'CreateDate'        => $now,
            'CreateTerminal'    => $ip,
        ]);

        $this->db->table('collectionloanitems')->insert([
            'CollectionLoan_id' => $loan_id,
            'LoanDate'          => $now,
            'DueDate'           => $dueDate,
            'LoanStatus'        => 'Loan',
            'Collection_id'     => $collection->ID,
            'member_id'         => $member_id,
            'CreateBy'          => $createdBy,
            'CreateDate'        => $now,
            'CreateTerminal'    => $ip,
            'Branch_id'         => $member->Branch_id,
        ]);

        $this->db->table('collections')
            ->where('ID', $collection->ID)
            ->update([
                'Status_id'      => 5,
                'UpdateBy'       => $createdBy,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new \Exception('Transaksi gagal saat mencatat peminjaman digital');
        }

        $this->db->transCommit();
    }
}
