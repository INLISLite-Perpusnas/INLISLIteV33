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
				set_message('toastr_msg', 'Form Entri Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Form Entri Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-form-entri');
		}

		echo view('FormEntri\Views\update', $this->data);
	}
}
