<?php

namespace PerpanjanganKeanggotaanMandiri\Controllers;

class PerpanjanganKeanggotaanMandiri extends \Base\Controllers\BaseController
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
		$this->data['title'] = 'Form Master keanggotaan Mandiri';
		$this->validation->setRule('parameter', 'Form Entri Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$paramName = 'PerpanjanganKenggotaanMandiri';
			$paramValue = $this->request->getPost('parameter');
			$updateParam = set_setting_parameter($paramName, $paramValue, is_profiling());

			if ($updateParam) {
				set_message('toastr_msg', 'Form Entri Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Form Entri Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-perpanjangan-keanggotaan-mandiri');
		} 

		echo view('PerpanjanganKeanggotaanMandiri\Views\update', $this->data);
	}
}
