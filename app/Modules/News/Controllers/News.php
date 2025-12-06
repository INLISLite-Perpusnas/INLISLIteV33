<?php

namespace News\Controllers;

use Berita\Models\BeritaModel;

class News extends \App\Controllers\BaseController
{
    protected $beritaModel;

    public function __construct()
    {
        $this->beritaModel = new BeritaModel();
    }

    public function index()
    {
        // Ambil 10 berita terbaru yang aktif, urut sort ASC
        $data['news'] = $this->beritaModel
              ->asArray()
            ->select('t_berita.*, users.username')         
            ->join('users', 'users.id = t_berita.created_by', 'left')          
            ->where('t_berita.active', 1)
            ->orderBy('t_berita.created_at', 'DESC')
            ->findAll();

        $data['title'] = 'Berita Terbaru';
        return view('News\Views\index', $data);
    }

   public function detail($id, $slug)
{
    $news = $this->beritaModel
        ->select('t_berita.*, users.username') 
        ->join('users', 'users.id = t_berita.created_by', 'left')
        ->find($id);
    if (!$news) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $data['news'] = $news;
    $data['title'] = $news->title; 
    return view('News\Views\detail', $data);
}
}
