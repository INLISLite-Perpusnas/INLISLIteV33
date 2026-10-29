<?php

namespace Home\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Home extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $katalogModel;
    public $visitorModel;
    public $eksemplarModel;
    public $beritaModel;
    public $settingModel;
    public $db;
    public $bannerModel;

    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->bannerModel = new \Banner\Models\BannerModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->beritaModel = new \Berita\Models\BeritaModel();
        $this->db = \Config\Database::connect('data');
        helper('reference');
    }

    public function index()
    {
        $this->data['title'] = 'Beranda - Perpustakaan Digital';
        $this->data['meta_description'] = 'Cari koleksi perpustakaan, lihat buku populer dan terbaru, serta baca berita dan informasi layanan perpustakaan.';
        $this->data['is_home_page'] = true;

        $useCache = setting_enabled('is_home_cache');
        $cacheKey = 'home_index_data';

        if ($useCache && $cached = cache($cacheKey)) {
            $this->data = array_merge($this->data, $cached);
        } else {
            $pageData = [
                'featured_books'       => $this->getFeaturedBooks(),
                'popular_books'        => $this->getPopularBooks(),
                'sering_dipinjam_books' => $this->getSeringDipinjamBooks(),
                'sering_dibaca_books'   => $this->getSeringDibacaBooks(),
                'statistics'           => $this->getLibraryStatistics(),
                'news'                 => $this->getNews(),
                'banners'              => $this->getBannersData(),
                'modules'              => $this->getLibraryModules(),
            ];

            if ($useCache) {
                cache()->save($cacheKey, $pageData, 300);
            }

            $this->data = array_merge($this->data, $pageData);
        }

        $this->data['surveys'] = (new \SurveiPemustaka\Models\SurveyFormService())
            ->activeSurveys($this->surveyMemberNo() !== null);

        return view('Home\Views\index', $this->data);
    }

    /**
     * Get featured books from catalog
     */
    private function getFeaturedBooks()
    {
        try {
            return $this->db->table('catalogs')
                ->orderBy('CreateDate', 'DESC')
                ->limit(8)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get popular books from catalog (fitur toggle lewat settingparameters is_populer)
     */
    private function getPopularBooks()
    {
        if (!setting_enabled('is_populer')) {
            return [];
        }

        try {
            $popularBooks = $this->db->table('catalogs')
                ->where('IsOPAC', 1)
                ->where('ISPopuler', 1)
                ->orderBy('ID', 'DESC')
                ->get()
                ->getResult();
              

            // Section hanya ditampilkan jika koleksi populer aktif minimal 5
            if (count($popularBooks) < 5) {
                return [];
            }

            return $popularBooks;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get 6 buku yang paling sering DIPINJAM, dihitung dari riwayat peminjaman
     * (collectionloanitems -> collections -> catalogs), bukan dari flag manual.
     * Fitur toggle lewat settingparameters is_sering_dipinjam (default 0 / nonaktif).
     * Lihat juga: Peminjaman\Controllers\Peminjaman untuk struktur tabel yang sama.
     */
    private function getSeringDipinjamBooks($limit = 6)
    {
        if (!setting_enabled('is_sering_dipinjam')) {
            return [];
        }

        try {
            return $this->db->table('collectionloanitems cli')
                ->select('cat.ID, cat.Title, cat.Author, cat.CoverURL, COUNT(cli.ID) as LoanCount')
                ->join('collections col', 'col.ID = cli.Collection_id')
                ->join('catalogs cat', 'cat.ID = col.Catalog_id')
                ->where('cat.IsOPAC', 1)
                ->groupBy('cat.ID, cat.Title, cat.Author, cat.CoverURL')
                ->orderBy('LoanCount', 'DESC')
                ->limit($limit)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get 6 buku yang paling sering DIBACA DI TEMPAT, dihitung dari tabel
     * bacaditempat (BacaDitempat\Models\BacaDitempatModel) -> collections -> catalogs.
     * Fitur toggle lewat settingparameters is_sering_dibaca (default 0 / nonaktif).
     */
    private function getSeringDibacaBooks($limit = 6)
    {
        if (!setting_enabled('is_sering_dibaca')) {
            return [];
        }

        try {
            if (!$this->db->tableExists('bacaditempat')) {
                return [];
            }

            return $this->db->table('bacaditempat bt')
                ->select('cat.ID, cat.Title, cat.Author, cat.CoverURL, COUNT(bt.ID) as ReadCount')
                ->join('collections col', 'col.ID = bt.collection_id')
                ->join('catalogs cat', 'cat.ID = col.Catalog_id')
                ->where('cat.IsOPAC', 1)
                ->where('bt.collection_id IS NOT NULL')
                ->groupBy('cat.ID, cat.Title, cat.Author, cat.CoverURL')
                ->orderBy('ReadCount', 'DESC')
                ->limit($limit)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get library statistics
     */
    private function getLibraryStatistics()
    {
        try {
            $totalBooks = $this->eksemplarModel->countAllResults();

            // You can add more statistics from other tables
            return [
                'total_books' => $totalBooks,
                'total_members' => $this->getTotalMembers(),
                'books_borrowed' => $this->getBorrowedBooks(),
                'visitors_today' => $this->getTodayVisitors()
            ];
        } catch (\Exception $e) {
            return [
                'total_books' => 0,
                'total_members' => 0,
                'books_borrowed' => 0,
                'visitors_today' => 0
            ];
        }
    }

    /**
     * Get total members (if members table exists)
     */
    private function getTotalMembers()
    {
        try {
            if ($this->db->tableExists('members')) {
                return $this->db->table('members')->countAllResults();
            }
            return 1250; // Dummy data
        } catch (\Exception $e) {
            return 1250; // Dummy data
        }
    }

    /**
     * Get borrowed books count
     */
    private function getBorrowedBooks()
    {
        try {
            if ($this->db->tableExists('collectionloanitems')) {
                return $this->db->table('collectionloanitems')
                    ->where('LoanStatus', 'Loan')
                    ->countAllResults();
            }
            return 0; // Dummy data
        } catch (\Exception $e) {
            return 0; // Dummy data
        }
    }

    /**
     * Get today's visitors count
     */
    private function getTodayVisitors()
    {
        try {
        $today = date('Y-m-d');
        $builderAnggota = $this->db->table('memberguesses');
        $builderAnggota->where('DATE(CreateDate)', $today);
		 $totalAnggota = $builderAnggota->countAllResults();
		$builderRombongan = $this->db->table('groupguesses');
        $builderRombongan->selectSum('CountPersonel', 'total_personel');
        $builderRombongan->where('DATE(CreateDate)', $today);
		$resultRombongan = $builderRombongan->get()->getRow();
        $totalRombongan = (int)($resultRombongan->total_personel ?? 0);
		$totalKunjungan = $totalAnggota + $totalRombongan;
            return $totalKunjungan;
        } catch (\Exception $e) {
            return 23; // Dummy data
        }
    }

    /**
     * Get news/announcements (dummy data)
     */
  private function getNews()
{
    try {
        return $this->beritaModel
            ->asArray()
            ->select('t_berita.*, users.username')         
            ->join('users', 'users.id = t_berita.created_by', 'left')          
            ->where('t_berita.active', 1)
            ->orderBy('t_berita.created_at', 'DESC')
            ->findAll(4);
            
    } catch (\Exception $e) {
        return []; 
    }
}

    /**
     * Get banner data (dummy data)
     */


    private function getBannersData()
    {
        try {
            return $this->bannerModel->where('active', 1)->where('category', 'Beranda')->orderBy('sort', 'ASC')->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get library modules
     */
    private function getLibraryModules()
    {
        return [
            [
                'name' => 'OPAC',
                'description' => 'Cari dan jelajahi koleksi perpustakaan',
                'icon' => 'fas fa-search',
                'img' => base_url('assets/img/opac1.png'),
                'link' => base_url('opac'),
                'color' => 'primary'
            ],
            [
                'name' => 'Buku Tamu',
                'description' => 'Registrasi kunjungan perpustakaan',
                'img' => base_url('assets/img/bukutamu.png'),
                'icon' => 'fas fa-book-open',
                'link' => base_url('buku-tamu'),
                'color' => 'success'
            ],
            [
                'name' => 'Baca di Tempat',
                'description' => 'Layanan membaca di perpustakaan',
                'img' => base_url('assets/img/bacaditempat.png'),
                'icon' => 'fas fa-chair',
                'link' => base_url('baca-ditempat'),
                'color' => 'info'
            ],
            ...(setting_enabled('is_keanggotaan_online') ? [[
                'name' => 'Keanggotaan Online',
                'description' => 'Daftar menjadi anggota perpustakaan',
                'img' => base_url('assets/img/anggota.png'),
                'icon' => 'fas fa-user-plus',
                'link' => base_url('home/pendaftaran_online'),
                'color' => 'warning'
            ]] : []),
            ...(setting_enabled('is_peminjaman_mandiri', true) ? [[
                'name' => 'Peminjaman Mandiri',
                'description' => 'Pinjam buku secara mandiri',
                'img' => base_url('assets/img/peminjaman.png'),
                'icon' => 'fas fa-hand-holding-heart',
                'link' => base_url('peminjaman-mandiri'),
                'color' => 'danger'
            ]] : []),
            ...(setting_enabled('is_pengembalian_mandiri', true) ? [[
                'name' => 'Pengembalian Mandiri',
                'description' => 'Kembalikan buku secara mandiri',
                'img' => base_url('assets/img/pengembalian.png'),
                'icon' => 'fas fa-undo',
                'link' => base_url('pengembalian-mandiri'),
                'color' => 'secondary'
            ]] : [])
        ];
    }

    /**
     * Search books (for AJAX)
     */
    public function searchBooks()
    {
        $keyword = $this->request->getGet('q');

        if (!$keyword) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Keyword tidak boleh kosong'
            ]);
        }

        try {
            $books = $this->katalogModel
                ->select('ID, Title, Author, Publisher, ControlNumber')
                ->groupStart()
                ->like('Title', $keyword)
                ->orLike('Author', $keyword)
                ->orLike('Publisher', $keyword)
                ->groupEnd()
                ->limit(10)
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $books
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencari buku'
            ]);
        }
    }

    private function surveyMemberNo(): ?string
    {
        if (!logged_in()) {
            return null;
        }
        $username = (string) (user()->username ?? '');
        if ($username === '') {
            return null;
        }
        $member = $this->db->table('members')->select('MemberNo')
            ->where('MemberNo', $username)->get()->getRowArray();
        return $member['MemberNo'] ?? null;
    }

    public function survey()
    {
        return view('Home\Views\survey', [
            'title' => 'Survei Pemustaka',
            'meta_description' => 'Isi survei aktif untuk membantu meningkatkan layanan perpustakaan.',
            'surveys' => (new \SurveiPemustaka\Models\SurveyFormService())
                ->activeSurveys($this->surveyMemberNo() !== null),
        ]);
    }

    public function simpanSurvey(int $id)
    {
        $session = session();
        $sessionId = $session->get('public_survey_session');
        if (!is_string($sessionId) || $sessionId === '') {
            $sessionId = bin2hex(random_bytes(16));
            $session->set('public_survey_session', $sessionId);
        }
        if ($session->get('survey_done_' . $id)) {
            return redirect()->to('home/survey#survei')->with('survey_error', 'Survei ini sudah Anda isi.');
        }
        try {
            $error = (new \SurveiPemustaka\Models\SurveyFormService())->submit(
                $id, $this->request->getPost('jawaban'), $sessionId, $this->surveyMemberNo()
            );
        } catch (\Throwable $exception) {
            log_message('error', 'Gagal menyimpan survei: ' . $exception->getMessage());
            $error = 'Jawaban belum tersimpan. Silakan coba lagi.';
        }
        if ($error !== null) {
            return redirect()->to('home/survey#survei')->withInput()->with('survey_error', $error);
        }
        $session->set('survey_done_' . $id, true);
        return redirect()->to('home/survey#survei')->with('survey_success', 'Terima kasih, jawaban survei berhasil dikirim.');
    }

    public function usulanKoleksi()
    {
        $this->assertUsulanKoleksiActive();

        return view('Home\Views\usulan-koleksi', [
            'title' => 'Usulan Koleksi',
            'meta_description' => 'Usulkan koleksi baru untuk perpustakaan.',
        ]);
    }

    public function simpanUsulanKoleksi()
    {
        $this->assertUsulanKoleksiActive();

        $this->validation->setRules([
            'Title' => 'required|max_length[255]',
            'Type' => 'permit_empty|max_length[50]',
            'Subject' => 'permit_empty|max_length[255]',
            'Author' => 'permit_empty|max_length[255]',
            'PublishLocation' => 'permit_empty|max_length[255]',
            'PublishYear' => 'permit_empty|max_length[50]',
            'Publisher' => 'permit_empty|max_length[50]',
            'Comments' => 'permit_empty|max_length[2000]',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->to('home/usulan-koleksi')->withInput()
                ->with('errors', $this->validation->getErrors());
        }

        $fields = ['Title', 'Type', 'Subject', 'Author', 'PublishLocation', 'PublishYear', 'Publisher', 'Comments'];
        $payload = [];
        foreach ($fields as $field) {
            $value = trim((string) $this->request->getPost($field));
            $payload[$field] = $value === '' ? null : $value;
        }

        if ($payload['Title'] === null) {
            return redirect()->to('home/usulan-koleksi')->withInput()
                ->with('errors', ['Title' => 'Judul koleksi wajib diisi.']);
        }

        $payload['Status'] = 'Usulan';
        $payload['DateRequest'] = date('Y-m-d H:i:s');
        $payload['CreateDate'] = $payload['DateRequest'];

        try {
            $branchValue = $this->settingModel->where('Name', 'Branch_id')->first()->Value ?? null;
            $payload['Branch_id'] = ctype_digit((string) $branchValue) ? (int) $branchValue : null;
            if (!$this->db->table('requestcatalog')->insert($payload)) {
                throw new \RuntimeException('Insert requestcatalog failed');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Gagal menyimpan usulan koleksi: ' . $exception->getMessage());
            return redirect()->to('home/usulan-koleksi')->withInput()
                ->with('error', 'Usulan belum tersimpan. Silakan coba lagi.');
        }

        return redirect()->to('home/usulan-koleksi')->with('success', 'Usulan koleksi berhasil dikirim.');
    }

    private function assertUsulanKoleksiActive(): void
    {
        $setting = $this->settingModel->where('Name', 'UsulanKoleksiLKD')->first();
        if (strtoupper(trim((string) ($setting->Value ?? ''))) !== 'TRUE') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    public function pendaftaran_online()
    {
        if (!setting_enabled('is_keanggotaan_online')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->data['title'] = 'Pendaftaran Online';
        return view('Home\Views\pendaftaran-online', $this->data);
    }
}
