<?php

namespace Anggota\Controllers;

/**
 * Trait AnggotaBase
 *
 * Shared properties dan inisialisasi yang digunakan oleh semua
 * controller Anggota. Include trait ini di setiap sub-controller.
 */
trait AnggotaBase
{
    public $auth;
    public $authorize;
    public $anggotaModel;
    public $uploadPath;
    public $lokasiperpustakaanModel;
    public $jenisanggotaModel;
    public $anggotahakaksesModel;
    public $AksesKoleksiModel;
    public $modulePath;
    public $pengaturananggotaModel;
    public $templateKartuModel;
    public $kartuanggotaModel;
    public $regionModel;
    public $settingModel;
    public $password;

    protected function initAnggotaBase(): void
    {
        $this->anggotaModel              = new \Anggota\Models\AnggotaModel();
        $this->lokasiperpustakaanModel   = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
        $this->anggotahakaksesModel      = new \Anggota\Models\Anggotahakakses();
        $this->AksesKoleksiModel         = new \Anggota\Models\Hak_akses_koleksi();
        $this->regionModel               = new \Region\Models\RegionModel();
        $this->jenisanggotaModel         = new \JenisAnggota\Models\JenisAnggotaModel();
        $this->templateKartuModel        = new \KartuAnggota\Models\KartuAnggotaModel();
        $this->kartuanggotaModel         = new \KartuAnggota\Models\KartuAnggotaModel();
        $this->settingModel              = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->password                  = new \Myth\Auth\Password();

        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/anggota/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }
        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth      = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();

        helper('adminigniter');
        helper('reference');
        helper('region');
        helper('anggota');
        helper('date_id_helper');
        helper('url');
        helper('thumbnail');
        helper('sirkulasi');
    }
}
