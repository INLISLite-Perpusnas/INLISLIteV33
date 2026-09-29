<?php

namespace Profil\Controllers;

use Profil\Models\ProfilModel;
use Profil\Models\LayananModel;

class Profil extends \Base\Controllers\BaseController
{
    protected $profilModel;
    protected $layananModel;

    public function __construct()
    {
        $this->profilModel  = new ProfilModel();
        $this->layananModel = new LayananModel();
    }

    public function index()
    {
        $this->data['title']             = 'Profil & Layanan Perpustakaan';
        $this->data['nama_perpustakaan'] = get_setting_parameter('NamaPerpustakaan') ?? '';
        $this->data['profil']            = $this->profilModel->getProfil();
        $this->data['layanan']           = $this->layananModel->getLayananWithLocation();

        return view('Profil\Views\index', $this->data);
    }
}
