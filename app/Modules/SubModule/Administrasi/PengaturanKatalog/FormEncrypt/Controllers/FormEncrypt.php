<?php

namespace FormEncrypt\Controllers;

class FormEncrypt extends \Base\Controllers\BaseController
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
		$this->data['title'] = 'Form Encrypt';
		$this->validation->setRule('parameter', 'Form Encrypt', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$paramName1 = 'FormAlgorithm';
			$paramName2 = 'FormKey';
			$paramName3 = 'FormIV';
			$paramValue1 = $this->request->getPost('parameter1');
			$paramValue2 = $this->request->getPost('parameter2');
			$paramValue3 = $this->request->getPost('parameter3');
			
			$updateParam = set_setting_parameter($paramName1, $paramValue1, is_profiling());
			$updateParam = set_setting_parameter($paramName2, $paramValue2, is_profiling());
			$updateParam = set_setting_parameter($paramName3, $paramValue3, is_profiling());

			if ($updateParam) {
				set_message('toastr_msg', 'Form Entri Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Form Entri Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-form-encrypt');
		}

		echo view('FormEncrypt\Views\update', $this->data);
	}
}
