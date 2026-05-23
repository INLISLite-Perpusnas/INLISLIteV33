<?php

namespace FormEntri\Controllers;

class FormEntri extends \Base\Controllers\BaseController
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
		$this->data['title'] = 'Form Entri';
		$this->validation->setRule('parameter', 'Form Entri Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$paramName = 'FormEntriKatalog';
			$paramValue = $this->request->getPost('parameter');
			$updateParam = set_setting_parameter($paramName, $paramValue, is_profiling());

			if ($updateParam) {
				set_message('swal_icon', 'success');
				set_message('swal_title', 'Berhasil');
				set_message('swal_text', 'Form Entri Katalog berhasil disimpan');
			} else {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Gagal');
				set_message('swal_text', 'Form Entri Katalog gagal disimpan');
			}
			return redirect()->to('/master-form-entri');
		}

		echo view('FormEntri\Views\update', $this->data);
	}
}
