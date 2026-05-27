<?php

namespace Eksemplar\Controllers;

/**
 * Trait EksemplarBase
 *
 * Shared properties dan inisialisasi yang digunakan oleh semua
 * controller Eksemplar. Include trait ini di setiap sub-controller.
 */
trait EksemplarBase
{
    public $eksemplarModel;
    public $katalogModel;
    public $katalogRuasModel;
    public $db;

    protected function initEksemplarBase(): void
    {
        $this->eksemplarModel   = new \Eksemplar\Models\EksemplarModel();
        $this->katalogModel     = new \Katalog\Models\KatalogModel();
        $this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();
        $this->db               = \Config\Database::connect('data');

        helper(['reference', 'katalog', 'eksemplar']);
    }
}
