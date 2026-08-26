<?php

namespace Artikel\Controllers;

/**
 * Artikel (publik, gaya OPAC)
 *
 * Halaman publik untuk menelusuri artikel terbitan berkala dan membaca
 * konten digitalnya (PDF) langsung, tanpa login dan tanpa dicatat ke
 * collectionloans — berbeda dari alur "Baca Buku Digital" di Opac::bacaDigital(),
 * yang memang untuk buku (bukan artikel) dan sengaja pakai sistem peminjaman.
 *
 * Pembacaan PDF-nya sendiri memakai ulang KatalogFileController::view_decrypted_article()
 * / get_decrypted_content_article() yang sudah ada (dipakai juga oleh panel admin) —
 * kedua endpoint itu memang tidak pernah menyentuh collectionloans atau butuh login,
 * hanya kebetulan sebelumnya berada di bawah grup route 'katalog' yang tertutup filter
 * sesi. Supaya publik bisa mengaksesnya, keduanya ditambahkan ke pengecualian filter
 * di app/Config/Filters.php.
 */
class Artikel extends \Base\Controllers\BaseController
{
    public $db;
    public $articleModel;

    public function __construct()
    {
        $this->db           = \Config\Database::connect('data');
        $this->articleModel = new \Katalog\Models\ArtikelModel();

        helper(['sanitize']);
    }

    /**
     * Daftar & pencarian artikel. Hanya menampilkan artikel yang ditandai
     * "Tampilkan di OPAC" (ISOPAC = 1) saat ditambahkan/diedit di panel admin.
     */
    public function index()
    {
        $search = sanitizeSearch($this->request->getGet('search') ?? '');

        $this->articleModel
            ->select('serial_articles.id, serial_articles.Title, serial_articles.Creator, serial_articles.Contributor,
                      serial_articles.Subject, serial_articles.EDISISERIAL, serial_articles.TANGGAL_TERBIT_EDISI_SERIAL,
                      serial_articles.Catalog_id, catalogs.Title as CatalogTitle, catalogs.CoverURL,
                      f.ID as FileId')
            ->join('catalogs', 'catalogs.ID = serial_articles.Catalog_id', 'left')
            ->join('serial_articlefiles f', 'f.Articles_id = serial_articles.id', 'left')
            ->where('serial_articles.ISOPAC', 1);

        if ($search !== '') {
            $this->articleModel->groupStart()
                ->like('serial_articles.Title', $search)
                ->orLike('serial_articles.Creator', $search)
                ->orLike('serial_articles.Subject', $search)
                ->orLike('catalogs.Title', $search)
                ->groupEnd();
        }

        $this->articleModel->orderBy('serial_articles.id', 'DESC');

        $perPage     = 12;
        $currentPage = (int) ($this->request->getVar('page_artikel') ?? 1);

        $this->data['title']    = 'Artikel';
        $this->data['articles'] = $this->articleModel->paginate($perPage, 'artikel', $currentPage);
        $this->data['pager']    = $this->articleModel->pager;
        $this->data['search']   = $search;

        return view('Artikel\Views\index', $this->data);
    }

    /**
     * Detail satu artikel + tautan baca PDF (kalau konten digitalnya ada).
     */
    public function detail($id)
    {
        $article = $this->db->table('serial_articles a')
            ->select('a.*, catalogs.Title as CatalogTitle, catalogs.Author as CatalogAuthor,
                      catalogs.Publisher, catalogs.PublishYear, catalogs.CoverURL, catalogs.ID as CatalogId,
                      f.ID as FileId')
            ->join('catalogs', 'catalogs.ID = a.Catalog_id', 'left')
            ->join('serial_articlefiles f', 'f.Articles_id = a.id', 'left')
            ->where('a.id', $id)
            ->where('a.ISOPAC', 1)
            ->get()
            ->getRow();

        if (!$article) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Artikel tidak ditemukan');
        }

        $this->data['title']   = 'Artikel - ' . $article->Title;
        $this->data['article'] = $article;

        return view('Artikel\Views\detail', $this->data);
    }
}
