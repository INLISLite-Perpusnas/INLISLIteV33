<?php

namespace SettingBukuTamu\Controllers;

class SettingBukuTamu extends \Base\Controllers\BaseController
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
		$this->data['title'] = 'Setting Buku Tamu';
		$this->validation->setRule('parameter', 'Setting Buku Tamu Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$paramName = 'SettingBukuTamu';
			$paramValue = $this->request->getPost('parameter');
			$updateParam = set_setting_parameter($paramName, $paramValue, is_profiling());

			if ($updateParam) {
				set_message('toastr_msg', 'Setting Buku Tamu Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Setting Buku Tamu Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-setting-buku-tamu');
		}

		echo view('SettingBukuTamu\Views\update', $this->data);
	}
}
