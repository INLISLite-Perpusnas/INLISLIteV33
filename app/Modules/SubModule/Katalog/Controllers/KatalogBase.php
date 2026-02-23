<?php

namespace Katalog\Controllers;

/**
 * Trait KatalogBase
 * 
 * Shared properties dan inisialisasi yang digunakan oleh semua
 * controller Katalog. Include trait ini di setiap sub-controller.
 */
trait KatalogBase
{
    public $auth;
    public $authorize;
    public $fileModel;
    public $katalogModel;
    public $katalogRuasModel;
    public $artikelModel;
    public $worksheetModel;
    public $articleModel;
    public $serialArticleFilesModel;
    public $uploadPath;
    public $modulePath;
    public $validation;
    public $db;
    public $eksemplarModel;
    public $edisiSerialModel;

    protected function initKatalogBase(): void
    {
        $this->fileModel               = new \Katalog\Models\FileModel();
        $this->katalogModel            = new \Katalog\Models\KatalogModel();
        $this->artikelModel            = new \Katalog\Models\ArtikelModel();
        $this->edisiSerialModel        = new \Katalog\Models\EdisiSerialModel();
        $this->katalogRuasModel        = new \Katalog\Models\KatalogRuasModel();
        $this->worksheetModel          = new \Katalog\Models\WorksheetModel();
        $this->eksemplarModel          = new \Eksemplar\Models\EksemplarModel();
        $this->articleModel            = new \Katalog\Models\ArtikelModel();
        $this->serialArticleFilesModel = new \Katalog\Models\SerialArticleFilesModel();
        $this->uploadPath              = ROOTPATH . 'public/uploads/';
        $this->modulePath              = ROOTPATH . 'public/uploads/katalog/';
        $this->validation              = \Config\Services::validation();
        $this->db                      = \Config\Database::connect('data');

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth      = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();

        helper(['reference', 'katalog', 'region', 'form', 'app']);
    }
}