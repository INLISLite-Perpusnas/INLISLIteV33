<?php

namespace FormSabtuMinggu\Controllers;

class FormSabtuMinggu extends \Base\Controllers\BaseController
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
		$this->validation->setRule('IsSaturdayHoliday', 'Form Sabtu Minggu', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$Saturdayholiday = $this->request->getPost('IsSaturdayHoliday');
			$updateParam = set_setting_parameter('IsSaturdayHoliday', $Saturdayholiday, is_profiling());

			$Sundayholiday = $this->request->getPost('IsSundayHoliday');
			set_setting_parameter('IsSundayHoliday', $Sundayholiday, is_profiling());

			if ($updateParam) {
				set_message('toastr_msg', 'Form Layanan Sabtu Minggu berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-form-sabtuminggu');
			} else {
				set_message('toastr_msg', 'Form Layanan Sabtu Minggu gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-form-entri');
		}

		echo view('FormSabtuMinggu\Views\update', $this->data);
	}
}
