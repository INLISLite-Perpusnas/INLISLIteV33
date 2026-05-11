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

        // Get featured books/collections (latest 8 books)
        $this->data['featured_books'] = $this->getFeaturedBooks();

        // Get library statistics
        $this->data['statistics'] = $this->getLibraryStatistics();

        // Get news/announcements (dummy data)
        $this->data['news'] = $this->getNews();

        // Get banner data
        $this->data['banners'] = $this->getBannersData();

        // Library modules
        $this->data['modules'] = $this->getLibraryModules();

        return view('Home\Views\index', $this->data);
    }

    /**
     * Get featured books from catalog
     */
    private function getFeaturedBooks()
    {
        try {
            return $this->db->table('catalogs')->limit(8)->get()->getResult();
        } catch (\Exception $e) {
            // Return empty array if error
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
            return $this->visitorModel
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults();
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
            [
                'name' => 'Keanggotaan Online',
                'description' => 'Daftar menjadi anggota perpustakaan',
                'img' => base_url('assets/img/anggota.png'),
                'icon' => 'fas fa-user-plus',
                'link' => base_url('home/pendaftaran_online'),
                'color' => 'warning'
            ],
            [
                'name' => 'Peminjaman Mandiri',
                'description' => 'Pinjam buku secara mandiri',
                'img' => base_url('assets/img/peminjaman.png'),
                'icon' => 'fas fa-hand-holding-heart',
                'link' => base_url('peminjaman-mandiri'),
                'color' => 'danger'
            ],
            [
                'name' => 'Pengembalian Mandiri',
                'description' => 'Kembalikan buku secara mandiri',
                'img' => base_url('assets/img/pengembalian.png'),
                'icon' => 'fas fa-undo',
                'link' => base_url('pengembalian-mandiri'),
                'color' => 'secondary'
            ]
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

    public function pendaftaran_online()
    {
        $this->data['title'] = 'Pendaftaran Online';
        return view('Home\Views\pendaftaran-online', $this->data);
    }
}
