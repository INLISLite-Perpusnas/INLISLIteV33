<?php

namespace UsulanKoleksi\Controllers;

class UsulanKoleksi extends \Base\Controllers\BaseController
{
    public function index()
    {
        if (!is_member('admin')) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }

        $this->data['title'] = 'Daftar Usulan Koleksi';
        return view('UsulanKoleksi\Views\list', $this->data);
    }
}