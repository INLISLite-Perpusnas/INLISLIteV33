<?php

namespace PenomoranKoleksi\Controllers;

class PenomoranKoleksi extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;

	function __construct()
	{
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}

	public function index()
	{
		$this->data['title'] = 'Penomoran Koleksi';
		$this->validation->setRule('parameter', 'Penomoran Koleksi Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$paramName = 'PenomoranKoleksiKatalog';
			$paramValue = $this->request->getPost('parameter');
			$updateParam = set_setting_parameter($paramName, $paramValue, is_profiling());

			if ($updateParam) {
				set_message('toastr_msg', 'Penomoran Koleksi Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Penomoran Koleksi Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-penomoran-koleksi');
		}

		echo view('PenomoranKoleksi\Views\update', $this->data);
	}
}
