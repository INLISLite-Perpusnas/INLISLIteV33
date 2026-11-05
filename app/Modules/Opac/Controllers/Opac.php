<?php

namespace Opac\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Opac\Libraries\Semantic\SemanticSearch;

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
    $this->data['meta_description'] = 'Telusuri judul, pengarang, subjek, penerbit, dan koleksi perpustakaan melalui katalog daring.';
    $this->data['is_opac_index'] = true;

    if (setting_enabled('is_opac_cache')) {
        $this->loadRegularCatalogscache();
    } else {
        $this->loadRegularCatalogs();
    }

    $this->data['show_collection_tabs'] = !array_diff_key((array) $this->request->getGet(), ['page' => true]);
    $this->data['collection_tabs'] = $this->data['show_collection_tabs']
        ? ['latest' => [
            'label' => 'Koleksi Terbaru',
            'books' => $this->data['catalogs'] ?? [],
            'count' => $this->data['total_records'] ?? 0,
        ]] + $this->getCollectionTabs()
        : [];
    $this->data['opac_banners'] = $this->getOpacBanners();
    $this->data['worksheets']   = $this->getWorksheets();
    $this->logOpacSearch();
    $this->data['search_history'] = $this->getSearchHistory();
    $this->data['worksheet_id'] = $this->request->getVar('Worksheet_id') ?? '';
    $this->data['semantic_enabled'] = SemanticSearch::enabled();
    $endTime = microtime(true);
    $this->data['execution_time'] = $endTime - $startTime;
    return view('Opac\Views\index', $this->data);
}

private function logOpacSearch(): void
{
    $params = (array) $this->request->getGet();
    $fields = ['search', 'search2', 'Author', 'Publisher', 'Subject', 'PublishYear', 'PublishLocation', 'Worksheet_id'];
    if (isset($params['page']) || !array_intersect($fields, array_keys($params))
        || strtoupper((string) get_setting_parameter('opac_log')) !== 'TRUE') {
        return;
    }

    try {
        if (!$this->db->tableExists('opaclogs')) {
            return;
        }

        $terms = [];
        foreach (['search' => 'Kata kunci', 'search2' => 'Kata kunci kedua', 'Author' => 'Pengarang', 'Publisher' => 'Penerbit', 'Subject' => 'Subjek', 'PublishYear' => 'Tahun', 'PublishLocation' => 'Kota terbit'] as $key => $label) {
            if (isset($params[$key]) && is_scalar($params[$key]) && trim((string) $params[$key]) !== '') {
                $terms[] = $label . ': ' . trim((string) $params[$key]);
            }
        }

        $worksheetId = $params['Worksheet_id'] ?? '';
        $worksheetName = '';
        if (is_scalar($worksheetId) && $worksheetId !== '') {
            $worksheetName = (string) $worksheetId;
            foreach ($this->data['worksheets'] as $worksheet) {
                if ((string) $worksheet->ID === $worksheetName) {
                    $worksheetName = $worksheet->Name;
                    break;
                }
            }
            $terms[] = 'Jenis bahan: ' . $worksheetName;
        }

        $searchBy = $params['search_by'] ?? '';
        $this->db->table('opaclogs')->insert([
            'waktu' => date('Y-m-d H:i:s'),
            'User_id' => session()->get('username') ?: null,
            'ip' => $this->request->getIPAddress(),
            'jenis_pencarian' => is_scalar($searchBy) && $searchBy !== '' ? (string) $searchBy : 'Semua Kriteria',
            'keyword' => mb_substr(implode('; ', $terms) ?: '(tanpa kata kunci)', 0, 2000),
            'jenis_bahan' => mb_substr($worksheetName, 0, 150),
            'url' => mb_substr((string) $this->request->getUri(), 0, 2048),
        ]);

        // Pengunjung tanpa login dikenali dari session, bukan IP yang bisa dipakai bersama
        $ids = (array) session()->get('opac_search_log_ids');
        $ids[] = (int) $this->db->insertID();
        session()->set('opac_search_log_ids', array_slice($ids, -20));
    } catch (\Throwable $e) {
        log_message('error', 'Gagal menyimpan log pencarian OPAC.');
    }
}

/**
 * Riwayat pencarian milik pengunjung ini dari opaclogs, hanya saat log pencarian diaktifkan
 * di /master-opac-logs. Anggota melihat riwayat akunnya, pengunjung lain riwayat session-nya.
 */
private function getSearchHistory(): array
{
    if (strtoupper((string) get_setting_parameter('opac_log')) !== 'TRUE') {
        return [];
    }

    try {
        if (!$this->db->tableExists('opaclogs')) {
            return [];
        }

        $builder = $this->db->table('opaclogs')
            ->select('keyword, MAX(url) AS url, MAX(ID) AS lastId', false)
            ->where('keyword !=', '(tanpa kata kunci)');

        $username = logged_in() ? session()->get('username') : null;
        if ($username) {
            $builder->where('User_id', $username);
        } else {
            $ids = array_map('intval', (array) session()->get('opac_search_log_ids'));
            if (!$ids) {
                return [];
            }
            $builder->whereIn('ID', $ids);
        }

        $rows = $builder->groupBy('keyword')->orderBy('lastId', 'DESC')->limit(5)->get()->getResult();
    } catch (\Throwable $e) {
        log_message('error', 'Gagal memuat riwayat pencarian OPAC.');
        return [];
    }

    $history = [];
    foreach ($rows as $row) {
        $query = (string) parse_url((string) $row->url, PHP_URL_QUERY);
        $history[] = [
            'label' => str_replace('Kata kunci: ', '', $row->keyword),
            'url'   => base_url('opac') . ($query !== '' ? '?' . $query : '') . '#hasil-pencarian',
        ];
    }

    return $history;
}
private function getCollectionTabs(): array
{
    $tabSettings = [
        'featured' => ['label' => 'Koleksi Unggulan', 'show' => strtoupper((string) get_setting_parameter('ShowKoleksiUnggulan')) === 'TRUE', 'limit' => (int) get_setting_parameter('KoleksiUnggulanShow'), 'default' => 8],
        'borrowed' => ['label' => 'Koleksi Sering Dipinjam', 'show' => strtoupper((string) get_setting_parameter('ShowKoleksiSeringDipinjam')) === 'TRUE', 'limit' => (int) get_setting_parameter('KoleksiSeringDipinjamShow'), 'default' => 8],
        // Sama seperti Sering Dipinjam: flag tampil + jumlah maksimal, diatur di master-opac-baca-ditempat
        // (show_sering_dibaca disimpan 1/0, TRUE/FALSE juga diterima)
        'read' => ['label' => 'Koleksi Sering Baca di Tempat', 'show' => in_array(strtoupper(trim((string) get_setting_parameter('show_sering_dibaca'))), ['1', 'TRUE'], true), 'limit' => (int) get_setting_parameter('sering_dibaca_show'), 'default' => 6],
    ];

    $tabs = [];
    foreach ($tabSettings as $type => $settings) {
        if (!$settings['show']) {
            continue;
        }
        $limit = $settings['limit'] > 0 ? $settings['limit'] : $settings['default'];
        $books = $this->getCollectionTabBooks($type, $limit);
        if ($books) {
            $tabs[$type] = ['label' => $settings['label'], 'books' => $books];
        }
    }

    return $tabs;
}

private function getCollectionTabBooks(string $type, int $limit): array
{
    try {
        if ($type === 'read' && !$this->db->tableExists('bacaditempat')) {
            return [];
        }

        if ($type === 'featured') {
            $builder = $this->db->table('catalogs cat')
                ->select('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Subject, cat.CoverURL')
                ->where('cat.IsOPAC', 1)
                ->where('cat.IsPopuler', 1)
                ->orderBy('cat.ID', 'DESC');
        } elseif ($type === 'borrowed') {
            $builder = $this->db->table('collectionloanitems cli')
                ->select('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Subject, cat.CoverURL, COUNT(cli.ID) AS LoanCount')
                ->join('collections col', 'col.ID = cli.Collection_id')
                ->join('catalogs cat', 'cat.ID = col.Catalog_id')
                ->where('cat.IsOPAC', 1)
                ->groupBy('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Subject, cat.CoverURL')
                ->orderBy('LoanCount', 'DESC');
        } else {
            $builder = $this->db->table('bacaditempat bt')
                ->select('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Subject, cat.CoverURL, COUNT(bt.ID) AS ReadCount')
                ->join('collections col', 'col.ID = bt.collection_id')
                ->join('catalogs cat', 'cat.ID = col.Catalog_id')
                ->where('cat.IsOPAC', 1)
                ->groupBy('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.Subject, cat.CoverURL')
                ->orderBy('ReadCount', 'DESC');
        }

        return $builder->limit($limit)->get()->getResult();
    } catch (\Exception $e) {
        return [];
    }
}

private function getOpacBanners()
{
    try {
        return $this->bannerModel->where('active', 1)->where('category', 'Opac')->orderBy('sort', 'ASC')->findAll();
    } catch (\Exception $e) {
        return [];
    }
}

// Daftar Jenis Bahan untuk filter OPAC, diambil dari tabel worksheets
private function getWorksheets()
{
    try {
        return $this->db->table('worksheets')->select('ID, Name')->orderBy('NoUrut', 'ASC')->get()->getResult();
    } catch (\Exception $e) {
        return [];
    }
}

private function loadRegularCatalogs()
{
    $perPage     = 14;
    $currentPage = $this->request->getVar('page') ?? 1;

    $builder = $this->katalogModel->select('catalogs.*')->where('IsOPAC', 1);

    $semantic = $this->applyKeywords($builder);
    if (!$semantic) {
        $builder->orderBy("ID", "DESC");
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

    // Filter Jenis Bahan (Worksheet_id) - exact match ke tabel worksheets, bukan LIKE
    $worksheetId = $this->request->getVar('Worksheet_id');
    if (!empty($worksheetId)) {
        $builder->where('Worksheet_id', (int) $worksheetId);
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
    $this->data['search_by'] = $this->opacSearchBy('search_by');
    $this->data['search2']   = $this->secondKeyword();
    $this->data['search_by2'] = $this->opacSearchBy('search_by2');
    $this->data['search_op2'] = $this->secondKeywordOperator();
}

private function loadRegularCatalogscache()
{
    $cacheTTL    = setting_cache_ttl('opac_cache_ttl', 60); // default 1 jam
    $perPage     = 14;
    $currentPage = $this->request->getVar('page') ?? 1;

    $requestParams = $this->request->getGet();
    ksort($requestParams);
    $cacheKey = 'catalog_data_p' . $perPage . '_' . md5(http_build_query($requestParams));

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
        $builder = $this->katalogModel->select('catalogs.*')->where('IsOPAC', 1);

        $semantic = $this->applyKeywords($builder);
        if (!$semantic) {
            $builder->orderBy("ID", "DESC");
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

        // Filter Jenis Bahan (Worksheet_id) - exact match ke tabel worksheets, bukan LIKE
        $worksheetId = $this->request->getVar('Worksheet_id');
        if (!empty($worksheetId)) {
            $builder->where('Worksheet_id', (int) $worksheetId);
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

        // Hasil cadangan FULLTEXT saat service semantik mati tidak di-cache, agar tidak bertahan setelah service pulih.
        if (empty($this->data['semantic_unavailable'])) {
            cache()->save($cacheKey, $dataToCache, $cacheTTL);
        }
        $this->data = array_merge($this->data, $dataToCache);
    }

    $this->data['search']    = esc($this->request->getVar('search') ?? '');
    $this->data['search_by'] = $this->opacSearchBy('search_by');
    $this->data['search2']   = $this->secondKeyword();
    $this->data['search_by2'] = $this->opacSearchBy('search_by2');
    $this->data['search_op2'] = $this->secondKeywordOperator();
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
            ->select('collections.ID as CollectionID, collections.Status_id, collections.Rule_id, collections.BookingMemberID, collections.BookingExpiredDate')
            ->select('collections.NomorBarcode, collections.CallNumber, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName, collections.EDISISERIAL, collections.TANGGAL_TERBIT_EDISI_SERIAL')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
            ->where('collections.ISDRM', 0)
            ->findAll();


        $drmEnabled = $this->drmEnabled();
        $roweksemplar_public = !$drmEnabled && (int) $catalog['IsOPAC'] === 1 ? $file : null;

        // Get digital books (DRM)
        $roweksemplar_drm = [];
        if ($drmEnabled) {
            $roweksemplar_drm = $EksemplarModel
                ->select('collections.ID as CollectionID, collections.NomorBarcode, collections.CallNumber, collections.Status_id, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
                ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
                ->join('locations', 'locations.id = collections.Location_id', 'left')
                ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
                ->where('collections.catalog_id', $id)
                ->where('collections.ISDRM', 1)
                ->orderBy('collections.ID', 'ASC')
                ->findAll();
        }

        // Kumpulkan ID koleksi DRM yang sedang dipinjam oleh anggota yang login
        $member_active_loan_collections = [];
        if ($drmEnabled) {
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
        $this->data['roweksemplar_public'] = $roweksemplar_public;
        $this->data['drm_enabled'] = $drmEnabled;
        $this->data['member_active_loan_collections'] = $member_active_loan_collections;
        $this->data['hcaptcha_site_key'] = getenv('HCAPTCHA_SITE_KEY');
        $this->data['booking_enabled'] = $this->bookingEnabled();
        $this->data['booking_member_no'] = $this->currentMemberNo();

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

        $this->data = array_merge($this->data, $this->statisticsCache('katalog', function () {
            $d = [];

            // Total katalog
            $d['total_catalogs'] = $this->katalogModel->countAll();

            // Katalog per tahun
            $d['by_year'] = $this->katalogModel
            ->select('PublishYear, COUNT(*) as total')
            ->where("PublishYear REGEXP '^[0-9]{4}$'")
            ->groupBy('PublishYear')
            ->orderBy('PublishYear', 'DESC')
            ->findAll();


            // Katalog per bahasa
            $d['by_language'] = $this->katalogModel
                ->select('Languages, COUNT(*) as total')
                ->where('Languages IS NOT NULL')
                ->where('Languages !=', '')
                ->groupBy('Languages')
                ->orderBy('total', 'DESC')
                ->findAll();

            // Katalog per penerbit (top 10)
            $builder = $this->db->table('catalogs');
            $d['by_publisher'] = $builder
                ->select('Publisher, COUNT(*) as total')
                ->where('Publisher IS NOT NULL')
                ->where('Publisher !=', '')
                ->groupBy('Publisher')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get()
                ->getResult(); // atau getResultArray();


            return $d;
        }));

        return view('Opac\Views\statistics', $this->data);
    }

    public function statistics_anggota()
    {
        $this->data['title'] = 'Statistik Anggota';

        $this->data = array_merge($this->data, $this->statisticsCache('anggota', function () {
            $d = [];

            // 1. Total anggota
            $d['total_members'] = $this->memberModel
                ->where('active', 1)
                ->countAllResults();

            // 2. Anggota aktif (IsOnlineActive = 1)
            $d['active_members'] = $this->memberModel
                ->where('active', 1)
                ->where('IsOnlineActive', 1)
                ->countAllResults();

            // 3. Anggota baru bulan ini
            $d['new_members_this_month'] = $this->memberModel
                ->where('active', 1)
                ->where('MONTH(RegisterDate)', date('m'))
                ->where('YEAR(RegisterDate)', date('Y'))
                ->countAllResults();

            // 4. Distribusi berdasarkan jenis kelamin
            $d['by_gender'] = $this->db->query("
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
            $d['by_education'] = $this->db->query("
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
            $d['by_job'] = $this->db->query("
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
            $d['by_month'] = $this->db->query("
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
            $d['by_year'] = $this->db->query("
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
            $d['by_age_range'] = $this->db->query("
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
            $d['by_province'] = $this->db->query("
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
            $d['by_marital_status'] = $this->db->query("
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
            $d['late_return_stats'] = $this->db->query("
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
            $d['by_identity_type'] = $this->db->query("
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
            $d['avg_age'] = $this->db->query("
                SELECT 
                    ROUND(AVG(TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE())), 1) as avg_age
                FROM members
                WHERE active = 1 
                AND DateOfBirth IS NOT NULL
            ")->getRow()->avg_age ?? 0;

            // 15. Anggota terdaftar hari ini
            $d['today_registrations'] = $this->memberModel
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

            $d['growth_rate'] = $lastYearMembers > 0 
                ? (($currentYearMembers - $lastYearMembers) / $lastYearMembers) * 100 
                : 0;

            return $d;
        }));

        return view('Opac\Views\statistics_anggota', $this->data);
    }  

    public function statistics_kunjungan()
    {
        $this->data['title'] = 'Statistik Kunjungan';

        [$startDate, $endDate] = $this->resolveStatisticsPeriod();
        $periodCondition = $this->statisticsPeriodCondition('visits.CreateDate', $startDate, $endDate);

        $this->data = array_merge($this->data, $this->statisticsCache('kunjungan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd'), function () use ($startDate, $endDate, $periodCondition) {
            $d = [];

            $visitTables = "
                SELECT CreateDate,
                    CASE
                        WHEN NoAnggota IS NULL OR TRIM(NoAnggota) = '' THEN 'Non Anggota'
                        ELSE 'Anggota'
                    END AS visit_type,
                    1 AS visitor_count
                FROM memberguesses
                WHERE CreateDate IS NOT NULL
                UNION ALL
                SELECT CreateDate, 'Rombongan' AS visit_type,
                    GREATEST(COALESCE(CountPersonel, 0), 1) AS visitor_count
                FROM groupguesses
                WHERE CreateDate IS NOT NULL
            ";

            $d['total_visits'] = $this->db->query("SELECT COUNT(*) AS total FROM ({$visitTables}) visits WHERE {$periodCondition}")->getRow()->total ?? 0;
            $d['total_visitors'] = $this->db->query("SELECT COALESCE(SUM(visitor_count), 0) AS total FROM ({$visitTables}) visits WHERE {$periodCondition}")->getRow()->total ?? 0;
            $d['today_visits'] = $this->db->query("SELECT COUNT(*) AS total FROM ({$visitTables}) visits WHERE {$periodCondition} AND DATE(visits.CreateDate) = CURDATE()")->getRow()->total ?? 0;
            $d['today_visitors'] = $this->db->query("SELECT COALESCE(SUM(visitor_count), 0) AS total FROM ({$visitTables}) visits WHERE {$periodCondition} AND DATE(visits.CreateDate) = CURDATE()")->getRow()->total ?? 0;
            $d['month_visitors'] = $this->db->query("SELECT COALESCE(SUM(visitor_count), 0) AS total FROM ({$visitTables}) visits WHERE {$periodCondition} AND YEAR(visits.CreateDate) = YEAR(CURDATE()) AND MONTH(visits.CreateDate) = MONTH(CURDATE())")->getRow()->total ?? 0;

            $d['visit_types'] = $this->db->query("SELECT visit_type, COUNT(*) AS visits, COALESCE(SUM(visitor_count), 0) AS visitors FROM ({$visitTables}) visits WHERE {$periodCondition} GROUP BY visit_type ORDER BY visitors DESC")->getResult();
            $axis = $this->statisticsTrendAxis($startDate, $endDate);
            $trendRows = $this->db->query("SELECT DATE_FORMAT(visits.CreateDate, '{$axis['sql_format']}') AS bucket, COUNT(*) AS visits, COALESCE(SUM(visitor_count), 0) AS visitors FROM ({$visitTables}) visits WHERE {$periodCondition} GROUP BY bucket ORDER BY bucket ASC")->getResult();
            $d['trend'] = [
                'labels' => $axis['labels'],
                'visitors' => $this->statisticsTrendSeries($axis['keys'], $trendRows, 'visitors'),
                'visits' => $this->statisticsTrendSeries($axis['keys'], $trendRows, 'visits'),
            ];
            $d['trend_granularity'] = $axis['granularity'];
            $d['hourly_visits'] = $this->db->query("SELECT DAYOFWEEK(visits.CreateDate) AS day_number, HOUR(visits.CreateDate) AS visit_hour, COALESCE(SUM(visitor_count), 0) AS visitors FROM ({$visitTables}) visits WHERE {$periodCondition} AND HOUR(visits.CreateDate) BETWEEN 7 AND 20 GROUP BY day_number, visit_hour ORDER BY day_number, visit_hour")->getResult();

            return $d;
        }));

        $export = $this->request->getPost('export');
        if (in_array($export, ['excel', 'pdf'], true)) {
            $data = $this->data;
            return \App\Libraries\StatistikExport::download([
                'title' => 'Statistik Kunjungan',
                'subtitle' => 'Ringkasan kunjungan anggota dan rombongan perpustakaan',
                'period_label' => $data['selected_period_label'],
                'stats' => [
                    ['Total Pengunjung', $data['total_visitors'], $data['selected_period_label'], '1E3A8A'],
                    ['Total Kunjungan', $data['total_visits'], $data['selected_period_label'], '059669'],
                    ['Pengunjung Hari Ini', $data['today_visitors'], date('d M Y'), '0891B2'],
                    ['Pengunjung Bulan Ini', $data['month_visitors'], date('F Y'), 'D97706'],
                ],
                'heatmap' => $this->statisticsHeatmap('Pola Jam Kunjungan', $data['hourly_visits'], 'visit_hour', 'visitors'),
                'chart' => [
                    'title' => 'Tren Pengunjung (' . $data['trend_granularity'] . ') - ' . $data['selected_period_label'],
                    'image' => \App\Libraries\StatistikExport::decodeChartImage($this->request->getPost('chart_image')),
                    'labels' => $data['trend']['labels'],
                    'series' => ['Pengunjung' => $data['trend']['visitors'], 'Kunjungan' => $data['trend']['visits']],
                ],
                'tables' => [
                    [
                        'title' => 'Jenis Kunjungan',
                        'headers' => ['Jenis', 'Catatan', 'Pengunjung'],
                        'rows' => array_map(static fn($row) => [(string) $row->visit_type, (int) $row->visits, (int) $row->visitors], $data['visit_types']),
                    ],
                ],
            ], $export, $this->response);
        }

        return view('Opac\Views\statistics_kunjungan', $this->data);
    }

    public function statistics_sirkulasi()
    {
        $this->data['title'] = 'Statistik Sirkulasi';

        [$startDate, $endDate] = $this->resolveStatisticsPeriod();
        $loanCondition = $this->statisticsPeriodCondition('cli.LoanDate', $startDate, $endDate);
        $returnCondition = $this->statisticsPeriodCondition('cli.ActualReturn', $startDate, $endDate);

        $this->data = array_merge($this->data, $this->statisticsCache('sirkulasi_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd'), function () use ($startDate, $endDate, $loanCondition, $returnCondition) {
            $d = [];

            $d['total_loans'] = $this->db->query("SELECT COUNT(*) AS total FROM collectionloanitems cli WHERE {$loanCondition}")->getRow()->total ?? 0;
            $d['total_returns'] = $this->db->query("SELECT COUNT(*) AS total FROM collectionloanitems cli WHERE {$returnCondition}")->getRow()->total ?? 0;
            $d['total_borrowers'] = $this->db->query("SELECT COUNT(DISTINCT cli.member_id) AS total FROM collectionloanitems cli WHERE {$loanCondition}")->getRow()->total ?? 0;
            $onLoan = $this->db->query("SELECT COUNT(*) AS total, COALESCE(SUM(DATE(cli.DueDate) < CURDATE()), 0) AS overdue FROM collectionloanitems cli WHERE cli.LoanStatus = 'Loan'")->getRow();
            $d['on_loan'] = $onLoan->total ?? 0;
            $d['on_loan_overdue'] = $onLoan->overdue ?? 0;

            $d['return_status'] = $this->db->query("SELECT CASE WHEN cli.DueDate IS NOT NULL AND DATE(cli.ActualReturn) > DATE(cli.DueDate) THEN 'Terlambat' ELSE 'Tepat Waktu' END AS return_status, COUNT(*) AS total FROM collectionloanitems cli WHERE {$returnCondition} GROUP BY return_status ORDER BY total DESC")->getResult();
            $d['top_collections'] = $this->db->query("SELECT cat.Title AS title, cat.Author AS author, COUNT(*) AS total FROM collectionloanitems cli INNER JOIN collections col ON col.ID = cli.Collection_id INNER JOIN catalogs cat ON cat.ID = col.Catalog_id WHERE {$loanCondition} GROUP BY cat.ID, cat.Title, cat.Author ORDER BY total DESC LIMIT 10")->getResult();
            $d['hourly_loans'] = $this->db->query("SELECT DAYOFWEEK(cli.LoanDate) AS day_number, HOUR(cli.LoanDate) AS loan_hour, COUNT(*) AS total FROM collectionloanitems cli WHERE {$loanCondition} AND HOUR(cli.LoanDate) BETWEEN 7 AND 20 GROUP BY day_number, loan_hour ORDER BY day_number, loan_hour")->getResult();

            $axis = $this->statisticsTrendAxis($startDate, $endDate);
            $loanRows = $this->db->query("SELECT DATE_FORMAT(cli.LoanDate, '{$axis['sql_format']}') AS bucket, COUNT(*) AS total FROM collectionloanitems cli WHERE {$loanCondition} GROUP BY bucket")->getResult();
            $returnRows = $this->db->query("SELECT DATE_FORMAT(cli.ActualReturn, '{$axis['sql_format']}') AS bucket, COUNT(*) AS total FROM collectionloanitems cli WHERE {$returnCondition} GROUP BY bucket")->getResult();
            $d['trend'] = [
                'labels' => $axis['labels'],
                'loans' => $this->statisticsTrendSeries($axis['keys'], $loanRows, 'total'),
                'returns' => $this->statisticsTrendSeries($axis['keys'], $returnRows, 'total'),
            ];
            $d['trend_granularity'] = $axis['granularity'];

            return $d;
        }));

        $export = $this->request->getPost('export');
        if (in_array($export, ['excel', 'pdf'], true)) {
            $data = $this->data;
            $returnTotal = array_sum(array_map(static fn($row) => (int) $row->total, $data['return_status']));
            return \App\Libraries\StatistikExport::download([
                'title' => 'Statistik Sirkulasi',
                'subtitle' => 'Ringkasan peminjaman dan pengembalian koleksi perpustakaan',
                'period_label' => $data['selected_period_label'],
                'stats' => [
                    ['Total Peminjaman', $data['total_loans'], $data['selected_period_label'], '1E3A8A'],
                    ['Total Pengembalian', $data['total_returns'], $data['selected_period_label'], '059669'],
                    ['Anggota Peminjam', $data['total_borrowers'], $data['selected_period_label'], '0891B2'],
                    ['Sedang Dipinjam', $data['on_loan'], number_format($data['on_loan_overdue']) . ' melewati jatuh tempo', 'D97706'],
                ],
                'heatmap' => $this->statisticsHeatmap('Pola Jam Peminjaman', $data['hourly_loans'], 'loan_hour', 'total'),
                'chart' => [
                    'title' => 'Tren Sirkulasi (' . $data['trend_granularity'] . ') - ' . $data['selected_period_label'],
                    'image' => \App\Libraries\StatistikExport::decodeChartImage($this->request->getPost('chart_image')),
                    'labels' => $data['trend']['labels'],
                    'series' => ['Peminjaman' => $data['trend']['loans'], 'Pengembalian' => $data['trend']['returns']],
                ],
                'tables' => [
                    [
                        'title' => 'Ketepatan Pengembalian',
                        'headers' => ['Status', 'Jumlah', '%'],
                        'rows' => array_map(static fn($row) => [$row->return_status, (int) $row->total, $returnTotal > 0 ? number_format(($row->total / $returnTotal) * 100, 1) . '%' : '0%'], $data['return_status']),
                    ],
                    [
                        'title' => '10 Koleksi Paling Sering Dipinjam - ' . $data['selected_period_label'],
                        'headers' => ['#', 'Judul', 'Pengarang', 'Dipinjam'],
                        'widths' => [1, 7, 4, 2],
                        'rows' => array_map(static fn($row, $i) => [$i + 1, (string) $row->title, (string) ($row->author ?: '-'), (int) $row->total], $data['top_collections'], array_keys($data['top_collections'])),
                    ],
                ],
            ], $export, $this->response);
        }

        return view('Opac\Views\statistics_sirkulasi', $this->data);
    }

    /**
     * Cache data statistik OPAC (diatur di master-pengaturan-frontend: is_statistik_cache &
     * statistik_cache_ttl). Kunci memuat tanggal hari ini agar angka "hari ini" tidak terbawa ke hari berikutnya.
     */
    private function statisticsCache(string $key, callable $build): array
    {
        if (!setting_enabled('is_statistik_cache')) {
            return $build();
        }

        $cacheKey = 'opac_stat_' . $key . '_' . date('Ymd');
        $cached = cache($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $data = $build();
        cache()->save($cacheKey, $data, setting_cache_ttl('statistik_cache_ttl', 60));
        return $data;
    }

    /**
     * Matriks heatmap hari x jam (07-20) untuk export, sama dengan yang dihitung di view.
     */
    private function statisticsHeatmap(string $title, array $rows, string $hourField, string $valueField): array
    {
        $matrix = [];
        foreach ($rows as $row) {
            $matrix[(int) $row->day_number][(int) $row->{$hourField}] = (int) $row->{$valueField};
        }

        return [
            'title' => $title,
            'days' => [1 => 'Minggu', 2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu', 5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu'],
            'hours' => range(7, 20),
            'matrix' => $matrix,
        ];
    }

    /**
     * Baca filter periode (?period=30|90|180|6m|12m|custom&start_date&end_date) untuk halaman statistik.
     * Mengisi data view periode dan mengembalikan [tanggal mulai, tanggal akhir].
     */
    private function resolveStatisticsPeriod(): array
    {
        $periods = [
            '30' => ['label' => '30 hari terakhir', 'modify' => '-30 days'],
            '90' => ['label' => '90 hari terakhir', 'modify' => '-90 days'],
            '180' => ['label' => '180 hari terakhir', 'modify' => '-180 days'],
            '6m' => ['label' => '6 bulan terakhir', 'modify' => '-6 months'],
            '12m' => ['label' => '12 bulan terakhir', 'modify' => '-12 months'],
            'custom' => ['label' => 'Periode kustom', 'modify' => null],
        ];
        $selectedPeriod = (string) ($this->request->getGet('period') ?? '30');
        if (!isset($periods[$selectedPeriod])) {
            $selectedPeriod = '30';
        }

        $today = new \DateTimeImmutable('today');
        $startDate = null;
        $endDate = $today;
        if ($selectedPeriod === 'custom') {
            $parseDate = static function ($value) {
                $date = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);
                return ($date && $date->format('Y-m-d') === (string) $value) ? $date : null;
            };
            $startDate = $parseDate($this->request->getGet('start_date'));
            $endDate = $parseDate($this->request->getGet('end_date'));
            if (!$startDate || !$endDate || $startDate > $endDate) {
                // Tanggal kustom tidak valid: kembali ke periode bawaan.
                $selectedPeriod = '30';
                $startDate = null;
                $endDate = $today;
            }
        }
        if ($startDate === null) {
            $startDate = $today->modify($periods[$selectedPeriod]['modify']);
        }

        $this->data['periods'] = $periods;
        $this->data['selected_period'] = $selectedPeriod;
        $this->data['start_date'] = $startDate->format('Y-m-d');
        $this->data['end_date'] = $endDate->format('Y-m-d');
        $this->data['selected_period_label'] = $selectedPeriod === 'custom'
            ? $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
            : $periods[$selectedPeriod]['label'];

        return [$startDate, $endDate];
    }

    private function statisticsPeriodCondition(string $column, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): string
    {
        return "{$column} >= " . $this->db->escape($startDate->format('Y-m-d'))
            . " AND {$column} < " . $this->db->escape($endDate->modify('+1 day')->format('Y-m-d'));
    }

    /**
     * Sumbu tren: harian untuk rentang <= 92 hari, selain itu bulanan.
     */
    private function statisticsTrendAxis(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $isDaily = $startDate->diff($endDate)->days <= 92;
        $axis = [
            'granularity' => $isDaily ? 'Harian' : 'Bulanan',
            'sql_format' => $isDaily ? '%Y-%m-%d' : '%Y-%m',
            'keys' => [],
            'labels' => [],
        ];
        $cursor = $isDaily ? $startDate : $startDate->modify('first day of this month');
        while ($cursor <= $endDate) {
            $axis['keys'][] = $cursor->format($isDaily ? 'Y-m-d' : 'Y-m');
            $axis['labels'][] = $cursor->format($isDaily ? 'd M' : 'M Y');
            $cursor = $cursor->modify($isDaily ? '+1 day' : '+1 month');
        }

        return $axis;
    }

    /**
     * Petakan hasil query (kolom bucket) ke sumbu tren; titik kosong diisi 0 agar garis tidak loncat.
     */
    private function statisticsTrendSeries(array $keys, array $rows, string $field): array
    {
        $byBucket = array_column($rows, null, 'bucket');

        return array_map(static fn($key) => (int) ($byBucket[$key]->{$field} ?? 0), $keys);
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
     * Pencarian semantik (opsi semantic=1 di form OPAC): ID katalog termirip dari service `spark semantic:serve`,
     * urutan kemiripan dipertahankan; filter tambahan tetap berlaku. Mengembalikan false bila tidak diminta, kriteria
     * ISBN, atau service tidak tersedia, sehingga pemanggil memakai FULLTEXT.
     */
    private function applySemanticSearch($builder, string $search, string $searchBy): bool
    {
        if ($this->request->getVar('semantic') !== '1' || $searchBy === 'ISBN' || !SemanticSearch::enabled()) {
            return false;
        }

        $hasFilters = false;
        foreach (['search2', 'Publisher', 'Author', 'PublishLocation', 'Subject', 'PublishYear', 'Worksheet_id'] as $filter) {
            $hasFilters = $hasFilters || !empty($this->request->getVar($filter));
        }
        $config = config('Semantic');
        $ids    = SemanticSearch::search($search, $hasFilters ? $config->filteredCandidates : $config->maxResults);
        if ($ids === null) {
            $this->data['semantic_unavailable'] = true;
            return false;
        }

        $ids = $ids ?: [0];
        $builder->whereIn('catalogs.ID', $ids)
                ->orderBy('FIELD(catalogs.ID, ' . implode(',', $ids) . ')', '', false);
        return true;
    }

    /** Kriteria pencarian dari form; bawaan Judul, sedangkan "Semua Kriteria" dikirim sebagai nilai kosong. */
    private function opacSearchBy(string $param): string
    {
        $searchBy = $this->request->getVar($param) ?? 'Title';
        return in_array($searchBy, ['Title', 'Author', 'Subject', 'Publisher', 'ISBN'], true) ? $searchBy : '';
    }

    private function secondKeyword(): string
    {
        $keyword = $this->request->getVar('search2');
        return is_string($keyword) ? trim($keyword) : '';
    }

    /** Hubungan kedua kata kunci: bawaan OR (cocok salah satu), AND berarti harus cocok keduanya. */
    private function secondKeywordOperator(): string
    {
        return $this->request->getVar('search_op2') === 'AND' ? 'AND' : 'OR';
    }

    /**
     * Terapkan kata kunci pertama dan kedua. Mengembalikan true bila kata kunci pertama
     * memakai pencarian semantik, sehingga urutan hasil sudah ditentukan.
     */
    private function applyKeywords($builder): bool
    {
        $rawSearch  = $this->request->getVar('search');
        $search     = sanitizeSearch($rawSearch);
        $rawSecond  = $this->secondKeyword();
        $second     = sanitizeSearch($rawSecond);
        // Kondisi OR dikelompokkan agar filter IsOPAC dan filter lain tetap berlaku untuk keduanya
        $matchEither = $search && $second && $this->secondKeywordOperator() === 'OR';
        $semantic    = false;

        if ($matchEither) {
            $builder->groupStart();
        }
        if ($search) {
            $searchBy = $this->opacSearchBy('search_by');
            $semantic = $this->applySemanticSearch($builder, $search, $searchBy);
            if (!$semantic) {
                $this->applyFulltextSearch($builder, $search, $searchBy, $rawSearch);
            }
        }
        if ($second) {
            $this->applyFulltextSearch($builder, $second, $this->opacSearchBy('search_by2'), $rawSecond, $matchEither);
        }
        if ($matchEither) {
            $builder->groupEnd();
        }

        return $semantic;
    }

    /**
     * Apply FULLTEXT search to query builder, fallback to LIKE when term is too short.
     * Requires FULLTEXT indexes on catalogs table — see app/Database/opac_fulltext_indexes.sql
     */
    private function applyFulltextSearch($builder, string $search, string $searchBy, ?string $rawSearch = null, bool $orGroup = false): void
    {
        $rawSearch = $rawSearch ?? $search;

        $orGroup ? $builder->orGroupStart() : $builder->groupStart();
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

    /** Stopword bawaan InnoDB yang panjangnya 3 huruf atau lebih (information_schema.innodb_ft_default_stopword). */
    private const FT_STOPWORDS = ['about', 'are', 'com', 'for', 'from', 'how', 'that', 'the', 'this', 'was', 'what', 'when', 'where', 'who', 'will', 'with', 'und', 'www'];

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
            // Kata di bawah innodb_ft_min_token_size (bawaan 3) dan stopword InnoDB tidak terindeks; bila diwajibkan, hasilnya selalu kosong
            if (mb_strlen($word) < 3 || in_array(mb_strtolower($word), self::FT_STOPWORDS, true)) continue;
            $terms[] = '+' . $word . '*';
        }
        return implode(' ', $terms);
    }

    private function drmEnabled(): bool
    {
        $setting = $this->db->table('settingparameters')->select('Value')->where('Name', 'isdrm')->get()->getRow();
        return (string) ($setting->Value ?? '1') !== '0';
    }

    public function kontenDigitalPublik(int $catalog_id)
    {
        if ($this->drmEnabled()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $catalog = $this->katalogModel->asArray()->find($catalog_id);
        $file = $this->fileModel->where('Catalog_id', $catalog_id)->first();

        if (!$catalog || (int) $catalog['IsOPAC'] !== 1 || !$file) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $fileName = (string) $file->FileURL;
        $directory = realpath(ROOTPATH . 'public/uploads/katalog');
        $filePath = realpath(ROOTPATH . 'public/uploads/katalog/' . $fileName);
        if (!$directory || !$filePath || dirname($filePath) !== $directory || !is_file($filePath) || !is_readable($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $temporary = null;
        try {
            if (str_starts_with($fileName, 'encrypted_')) {
                $temporary = tempnam(sys_get_temp_dir(), 'opac_pdf_');
                if ($temporary === false) {
                    throw new \RuntimeException('Temporary PDF file unavailable');
                }
                (new \App\Libraries\Encryption())->decryptFile($filePath, $temporary);
                $content = file_get_contents($temporary);
            } else {
                $content = file_get_contents($filePath);
            }
        } finally {
            if ($temporary !== null && is_file($temporary)) {
                unlink($temporary);
            }
        }

        if (!is_string($content) || !str_starts_with($content, '%PDF-')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $download = $this->request->getGet('download') === '1';
        if ($download) {
            $branch = $this->db->table('settingparameters')->select('Value')->where('Name', 'Branch_id')->get()->getRow();
            $branchId = (string) ($branch->Value ?? '');
            $ip = $this->request->getIPAddress();
            $logged = $this->db->table('logsdownload')->insert([
                'User_id' => logged_in() ? (string) login_id() : null,
                'ip' => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : null,
                'catalogfilesID' => (int) $file->ID,
                'isLKD' => 0,
                'waktu' => date('Y-m-d H:i:s'),
                'Branch_id' => ctype_digit($branchId) ? (int) $branchId : null,
                'active' => 1,
            ]);
            if (!$logged) {
                throw new \RuntimeException('Gagal mencatat unduhan konten digital.');
            }
        }

        $disposition = $download ? 'attachment' : 'inline';
        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="katalog-' . $catalog_id . '.pdf"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($content);
    }

    public function bacaDigital($catalog_id){
        if (!$this->drmEnabled()) {
            return redirect()->to(base_url('opac/konten-digital/' . $catalog_id));
        }

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

    /**
     * Login khusus anggota via modal di halaman detail OPAC (AJAX).
     * Dipakai supaya anggota tidak perlu diarahkan ke /login saat membaca buku digital.
     */
    public function memberLogin()
    {
        $username         = trim((string) $this->request->getPost('username'));
        $password         = (string) $this->request->getPost('password');
        $hcaptchaResponse = $this->request->getPost('h-captcha-response');

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Nomor anggota dan password wajib diisi.',
            ]);
        }

        if (!$this->verifyHcaptcha($hcaptchaResponse)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Verifikasi hCaptcha gagal. Silakan coba lagi.',
            ]);
        }

        if (!service('authentication')->attempt(['username' => $username, 'password' => $password])) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Maaf, nomor anggota atau password anda salah.',
            ]);
        }

        $db   = db_connect();
        $user = $db->table('users')->where('username', $username)->get()->getRowObject();

        if (is_null($user)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Maaf, nomor anggota atau password anda salah.',
            ]);
        }

        // Modal ini khusus anggota perpustakaan, bukan untuk akun staf/admin
        $member = $this->memberModel->where('MemberNo', $username)->first();
        if (!$member) {
            service('authentication')->logout();

            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Login ini khusus untuk anggota perpustakaan. Silakan gunakan halaman login staf.',
            ]);
        }

        session()->remove('error');

        // Ambil permission data seperti pada login utama
        $auth_permissions            = $db->table('auth_permissions')->get()->getResultObject();
        $result_auth_permissions     = [];
        $auth_user_permissions       = $db->table('auth_users_permissions')->where('user_id', $user->id)->get()->getResultObject();
        $result_auth_user_permissions = [];
        foreach ($auth_user_permissions as $row) {
            $result_auth_user_permissions[$row->permission_id] = $row->user_id;
        }

        if (!empty($result_auth_user_permissions)) {
            foreach ($auth_permissions as $row) {
                if (isset($result_auth_user_permissions[$row->id])) {
                    $result_auth_permissions[$row->name] = [$user->id];
                }
            }
        } else {
            $auth_groups_users        = $db->table('auth_groups_users')->get()->getResultObject();
            $result_auth_groups_users = [];
            foreach ($auth_groups_users as $row) {
                $result_auth_groups_users[$row->group_id][] = $row->user_id;
            }

            $auth_groups        = $db->table('auth_groups')->get()->getResultObject();
            $result_auth_groups = [];
            foreach ($auth_groups as $row) {
                $result_auth_groups[$row->id] = $row;
            }

            $auth_groups_permissions        = $db->table('auth_groups_permissions')->get()->getResultObject();
            $result_auth_groups_permissions = [];
            foreach ($auth_groups_permissions as $row) {
                $result_auth_groups_permissions[$row->permission_id][] = $row->group_id;
            }

            foreach ($auth_permissions as $row) {
                if (isset($result_auth_groups_permissions[$row->id])) {
                    $groups_permissions = $result_auth_groups_permissions[$row->id];
                    $user_id            = [];
                    foreach ($groups_permissions as $list_group_id) {
                        if (isset($result_auth_groups_users[$list_group_id])) {
                            $user_id = array_merge($user_id, $result_auth_groups_users[$list_group_id]);
                        }
                    }
                    $result_auth_permissions[$row->name] = $user_id;
                }
            }
        }

        $exp   = time() + getenv('security.TOKEN_EXP');
        $token = JWT::encode([
            'iat'  => getenv('security.TOKEN_IAT'),
            'exp'  => $exp,
            'user' => $user->id,
        ], getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'));

        session()->set('username', $username);
        session()->set('token', $token);
        session()->set('logged_in', $user->id);
        session()->set('branch_id', $user->branch_id ?? 0);
        session()->set('member_id', $user->member_id ?? 0);
        session()->set('auth_permissions', $result_auth_permissions);

        $userData                    = new \stdClass();
        $userData->id                = $user->id;
        $userData->username          = $user->username;
        $userData->first_name        = $user->first_name ?? 'Anggota';
        $userData->last_name         = $user->last_name ?? '';
        $userData->email             = $user->email ?? '';
        $userData->phone             = $user->phone ?? '';
        $userData->address           = $user->address ?? '';
        $userData->avatar            = $user->avatar ?? '1683480754_669684b97dfb4cf28593.png';
        $userData->category          = $user->category ?? 'member';
        $userData->branch_id         = $user->branch_id ?? 0;
        $userData->npp_provinsi_id   = $user->npp_provinsi_id ?? 0;
        $userData->npp_kabkota_id    = $user->npp_kabkota_id ?? 0;
        $userData->npp_kecamatan_id  = $user->npp_kecamatan_id ?? 0;
        $userData->npp_kelurahan_id  = $user->npp_kelurahan_id ?? 0;
        $userData->member_id         = $user->member_id ?? 0;

        session()->set('user', $userData);

        $payload = JWT::decode($token, new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG')));
        session()->set('payload', $payload);

        return $this->response->setJSON([
            'error'   => false,
            'message' => 'Login berhasil.',
        ]);
    }

    // ----------------------------------------------------------------
    // PEMESANAN (BOOKING) KOLEKSI
    // Aturan mengikuti OPAC INLISLite 3 (DetailOpacController + OpacHelpers::cekBooking).
    // ----------------------------------------------------------------

    /** Status koleksi "Tersedia" dan aturan akses "Dapat dipinjam". */
    private const BOOKING_STATUS_TERSEDIA = 1;
    private const BOOKING_RULE_DAPAT_DIPINJAM = 1;
    private const STATUS_ANGGOTA_AKTIF = 3;

    private function bookingEnabled(): bool
    {
        return (string) get_setting_parameter('IsBookingActivated') === '1';
    }

    /** Nomor anggota dari akun yang sedang login, null jika tamu atau akun staf. */
    private function currentMemberNo(): ?string
    {
        if (!logged_in()) {
            return null;
        }
        try {
            $username = (string) (user()->username ?? '');
        } catch (\Throwable $e) {
            return null;
        }
        if ($username === '') {
            return null;
        }
        return $this->memberModel->where('MemberNo', $username)->countAllResults() > 0 ? $username : null;
    }

    public function pesan(int $collectionId = 0)
    {
        $reply = function (bool $error, string $message, array $extra = []) {
            return $this->response->setJSON(['error' => $error, 'message' => $message, 'csrf' => csrf_hash()] + $extra);
        };

        if (!$this->bookingEnabled()) {
            return $reply(true, 'Fitur pemesanan koleksi sedang tidak aktif.');
        }
        if (!logged_in()) {
            return $reply(true, 'Silakan login sebagai anggota untuk memesan koleksi.', ['login_required' => true]);
        }
        $memberNo = $this->currentMemberNo();
        if ($memberNo === null) {
            return $reply(true, 'Pemesanan koleksi khusus untuk anggota perpustakaan.');
        }

        $collection = $this->db->table('collections')
            ->select('ID, Catalog_id, Status_id, Rule_id, ISDRM, Category_id, Location_Library_id, NomorBarcode, BookingMemberID, BookingExpiredDate')
            ->where('ID', $collectionId)->get()->getRow();
        if (!$collection || (int) $collection->ISDRM !== 0) {
            return $reply(true, 'Koleksi tidak ditemukan.');
        }

        $now = new \DateTimeImmutable('now');
        $nowText = $now->format('Y-m-d H:i:s');
        if (!empty($collection->BookingMemberID) && (string) $collection->BookingExpiredDate > $nowText) {
            return $reply(true, $collection->BookingMemberID === $memberNo
                ? 'Koleksi ini sudah Anda pesan sampai ' . $collection->BookingExpiredDate . '.'
                : 'Koleksi ini sudah dipesan anggota lain sampai ' . $collection->BookingExpiredDate . '.');
        }
        if ((int) $collection->Status_id !== self::BOOKING_STATUS_TERSEDIA
            || (int) $collection->Rule_id !== self::BOOKING_RULE_DAPAT_DIPINJAM) {
            return $reply(true, 'Koleksi ini tidak dapat dipesan.');
        }

        $errors = $this->bookingErrors($memberNo, $collection, $nowText);
        if ($errors) {
            return $reply(true, 'Gagal memesan: ' . implode(', ', $errors) . '.');
        }

        $expired = $this->bookingExpiredAt($now, (string) get_setting_parameter('BookingExpired'))->format('Y-m-d H:i:s');

        $this->db->transStart();
        // Update bersyarat agar dua anggota tidak bisa memesan eksemplar yang sama bersamaan
        $this->db->table('collections')
            ->where('ID', $collection->ID)
            ->where('Status_id', self::BOOKING_STATUS_TERSEDIA)
            ->groupStart()
                ->where('BookingMemberID', null)
                ->orWhere('BookingMemberID', '')
                ->orWhere('BookingExpiredDate', null)
                ->orWhere('BookingExpiredDate <=', $nowText)
            ->groupEnd()
            ->update(['BookingMemberID' => $memberNo, 'BookingExpiredDate' => $expired]);
        $booked = $this->db->affectedRows() > 0;
        if ($booked) {
            $this->db->table('bookinglogs')->insert([
                'memberId' => $memberNo,
                'collectionId' => $collection->ID,
                'bookingDate' => $nowText,
                'bookingExpired' => $expired,
                'Branch_id' => branch_id() ?: null,
                'active' => 1,
            ]);
        }
        $this->db->transComplete();

        if (!$booked) {
            return $reply(true, 'Koleksi ini baru saja dipesan anggota lain.');
        }
        if (!$this->db->transStatus()) {
            return $reply(true, 'Pemesanan gagal disimpan. Silakan coba lagi.');
        }

        return $reply(false, 'Koleksi ' . $collection->NomorBarcode . ' berhasil dipesan. Silakan ambil di perpustakaan sebelum ' . $expired . '.', [
            'expired' => $expired,
        ]);
    }

    // ----------------------------------------------------------------
    // KERANJANG PESAN ANGGOTA (setara opac/BookingController INLISLite 3)
    // ----------------------------------------------------------------

    /** Pesanan aktif anggota: koleksi yang masih ditahan dan belum lewat batas waktu. */
    private function memberActiveBookings(string $memberNo): array
    {
        return $this->db->table('collections col')
            ->select('col.ID, col.NomorBarcode, col.BookingExpiredDate, cat.ID as CatalogID, cat.Title')
            ->join('catalogs cat', 'cat.ID = col.Catalog_id', 'left')
            ->where('col.BookingMemberID', $memberNo)
            ->where('col.BookingExpiredDate >', date('Y-m-d H:i:s'))
            ->orderBy('col.BookingExpiredDate')
            ->get()->getResult();
    }

    /** Isi modal "Keranjang Pesan" (action=showBookingDetail di versi lama). */
    public function bookingList()
    {
        $memberNo = $this->currentMemberNo();
        return view('Opac\Views\booking\list', [
            'memberNo' => $memberNo,
            'booking' => $memberNo ? $this->memberActiveBookings($memberNo) : [],
        ]);
    }

    /** Batalkan pesanan milik anggota yang login (action=cancelBooking di versi lama). */
    public function bookingCancel(int $collectionId = 0)
    {
        $memberNo = $this->currentMemberNo();
        if ($memberNo === null) {
            return $this->response->setJSON(['error' => true, 'message' => 'Silakan login sebagai anggota.', 'csrf' => csrf_hash()]);
        }

        $collection = $this->db->table('collections')->select('BookingExpiredDate')
            ->where('ID', $collectionId)->where('BookingMemberID', $memberNo)->get()->getRow();
        if (!$collection) {
            return $this->response->setJSON(['error' => true, 'message' => 'Pesanan tidak ditemukan.', 'csrf' => csrf_hash()]);
        }

        $this->db->transStart();
        // Hanya koleksi yang dipesan anggota ini yang boleh dilepas
        $this->db->table('collections')
            ->where('ID', $collectionId)->where('BookingMemberID', $memberNo)
            ->update(['BookingMemberID' => null, 'BookingExpiredDate' => null]);
        $this->db->table('bookinglogs')
            ->where('memberId', $memberNo)->where('collectionId', $collectionId)
            ->where('bookingExpired', $collection->BookingExpiredDate)
            ->update(['active' => 0]);
        $this->db->transComplete();

        return $this->response->setJSON([
            'error' => !$this->db->transStatus(),
            'message' => $this->db->transStatus() ? 'Item telah dihapus dari keranjang pesan.' : 'Pesanan gagal dibatalkan.',
            'csrf' => csrf_hash(),
        ]);
    }

    /** Tanda terima pemesanan koleksi untuk dicetak (actionCetak di versi lama). */
    public function bookingCetak()
    {
        $memberNo = $this->currentMemberNo();
        if ($memberNo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('Opac\Views\booking\cetak', [
            'anggota' => $this->db->table('members')->select('MemberNo, Fullname')->where('MemberNo', $memberNo)->get()->getRow(),
            'booking' => $this->memberActiveBookings($memberNo),
            'namaPerpustakaan' => get_setting_parameter('NamaPerpustakaan'),
        ]);
    }

    /** Validasi anggota terhadap koleksi (setara OpacHelpers::cekBooking INLISLite 3). */
    private function bookingErrors(string $memberNo, object $collection, string $nowText): array
    {
        $errors = [];
        $member = $this->db->table('members')->select('ID, StatusAnggota_id, EndDate')
            ->where('MemberNo', $memberNo)->get()->getRow();

        $max = (int) get_setting_parameter('JumlahBookingMaksimal');
        $active = $this->db->table('collections')
            ->where('BookingMemberID', $memberNo)->where('BookingExpiredDate >', $nowText)->countAllResults();
        if ($max > 0 && $active >= $max) {
            $errors[] = 'jumlah pesanan aktif sudah mencapai batas ' . $max . ' eksemplar';
        }
        if ((int) $member->StatusAnggota_id !== self::STATUS_ANGGOTA_AKTIF) {
            $errors[] = 'status keanggotaan belum aktif';
        }
        if (empty($member->EndDate) || $member->EndDate < $nowText) {
            $errors[] = 'masa berlaku keanggotaan sudah habis';
        }

        $locations = array_column($this->db->table('memberloanauthorizelocation')->select('LocationLoan_id')
            ->where('Member_id', $member->ID)->get()->getResultArray(), 'LocationLoan_id');
        if (!in_array((string) $collection->Location_Library_id, array_map('strval', $locations), true)) {
            $errors[] = 'Anda tidak mempunyai akses peminjaman di lokasi ini';
        }

        $categories = array_column($this->db->table('memberloanauthorizecategory')->select('CategoryLoan_id')
            ->where('Member_id', $member->ID)->get()->getResultArray(), 'CategoryLoan_id');
        if (!in_array((string) $collection->Category_id, array_map('strval', $categories), true)) {
            $errors[] = 'Anda tidak mempunyai akses peminjaman kategori koleksi ini';
        }

        return $errors;
    }

    /**
     * Batas waktu pesanan: durasi BookingExpired (HH:MM:SS) yang hanya dihitung
     * pada jam buka perpustakaan (master_jam_buka), melewati hari tutup dan hari libur.
     */
    private function bookingExpiredAt(\DateTimeImmutable $now, string $duration): \DateTimeImmutable
    {
        $parts = array_map('intval', explode(':', $duration)) + [0, 0, 0];
        $remaining = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
        if ($remaining <= 0) {
            $remaining = 3 * 3600;
        }

        $schedule = [];
        foreach ($this->db->table('master_jam_buka')->get()->getResultArray() as $row) {
            $schedule[strtolower($row['hari'])] = $row;
        }
        $fallback = $now->modify('+' . $remaining . ' seconds');
        if (!$schedule) {
            return $fallback;
        }

        $holidays = [];
        foreach ($this->db->table('holidays')->select('Dates')->where('Dates >=', $now->format('Y-m-d'))
            ->get()->getResultArray() as $row) {
            $holidays[substr($row['Dates'], 0, 10)] = true;
        }

        $dayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $cursor = $now;
        for ($i = 0; $i < 60; $i++) {
            $date = $cursor->format('Y-m-d');
            $row = $schedule[$dayNames[(int) $cursor->format('w')]] ?? null;
            if ($row && !isset($holidays[$date])) {
                $open = new \DateTimeImmutable($date . ' ' . $row['jam_buka']);
                $close = new \DateTimeImmutable($date . ' ' . $row['jam_tutup']);
                if ($close > $open) {
                    if ($cursor < $open) {
                        $cursor = $open;
                    }
                    if ($cursor < $close) {
                        $available = $close->getTimestamp() - $cursor->getTimestamp();
                        if ($remaining <= $available) {
                            return $cursor->modify('+' . $remaining . ' seconds');
                        }
                        $remaining -= $available;
                    }
                }
            }
            $cursor = (new \DateTimeImmutable($date))->modify('+1 day');
        }

        return $fallback;
    }

    /**
     * Verify hCaptcha response token.
     */
    private function verifyHcaptcha($hcaptchaResponse)
    {
        $secretKey = getenv('HCAPTCHA_SECRET_KEY');

        if (empty($secretKey)) {
            // hCaptcha belum dikonfigurasi di server, lewati verifikasi
            return true;
        }

        if (empty($hcaptchaResponse)) {
            return false;
        }

        $url  = 'https://hcaptcha.com/siteverify';
        $data = [
            'secret'   => $secretKey,
            'response' => $hcaptchaResponse,
            'remoteip' => $this->request->getIPAddress(),
        ];

        // Gunakan cURL (bukan file_get_contents) karena verifikasi SSL-nya
        // mengikuti setting curl.cainfo di php.ini, yang di environment ini
        // sudah dikonfigurasi dengan benar (berbeda dari openssl.cafile yang
        // dipakai stream wrapper file_get_contents dan sering belum diset).
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $result    = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            log_message('error', 'hCaptcha verify request failed: ' . $curlError);
            return false;
        }

        $response = json_decode($result, true);

        return isset($response['success']) && $response['success'] === true;
    }

    private function _autoReturnDigitalLoan($loanItem, int $collection_id)
{
    // Hanya proses auto-return untuk koleksi digital (ISDRM = 1)
    $collection = $this->db->table('collections')
        ->select('ID, ISDRM, Status_id')
        ->where('ID', $collection_id)
        ->get()
        ->getRow();

    if (!$collection || (int) $collection->ISDRM !== 1) {
        return; // bukan koleksi digital, tidak perlu auto-return
    }

    $now = date('Y-m-d H:i:s');
    $ip  = $this->request->getIPAddress();

    $this->db->transBegin();

    $this->db->table('collectionloanitems')
        ->where('ID', $loanItem->ID)
        ->update([
            'LoanStatus'     => 'Return',
            'ActualReturn'   => $now,
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
