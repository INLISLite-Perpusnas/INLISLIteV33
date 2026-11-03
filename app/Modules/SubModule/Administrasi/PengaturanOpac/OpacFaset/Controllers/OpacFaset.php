<?php

namespace OpacFaset\Controllers;

class OpacFaset extends \Base\Controllers\BaseController
{
	// Prefix parameter => judul grup; tiap grup punya {prefix}Max dan {prefix}Min di settingparameters
	public const FASET = [
		'FacedAuthor'          => 'Faset Pengarang',
		'FacedPublisher'       => 'Faset Penerbit',
		'FacedPublishLocation' => 'Faset Lokasi Penerbitan',
		'FacedPublishYear'     => 'Faset Tahun Terbit',
		'FacedSubject'         => 'Faset Subyek',
		'FacedBahasa'          => 'Faset Bahasa',
	];

	public function index()
	{
		$this->data['title'] = 'Pengaturan Faset';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-faset/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-faset');
			}

			$rules = [];
			foreach (self::FASET as $prefix => $label) {
				$rules[$prefix . 'Max'] = ['label' => 'Maksimal Jumlah Baris ' . $label, 'rules' => 'required|is_natural_no_zero'];
				$rules[$prefix . 'Min'] = ['label' => 'Minimal Jumlah Baris ' . $label, 'rules' => 'required|is_natural_no_zero'];
			}
			$this->validation->setRules($rules);

			if ($this->validation->withRequest($this->request)->run()) {
				foreach (array_keys($rules) as $name) {
					set_setting_parameter($name, $this->request->getPost($name));
				}
				set_message('toastr_msg', 'Pengaturan Faset berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-faset');
		}

		$setting = [];
		foreach (self::FASET as $prefix => $label) {
			$setting[$prefix . 'Max'] = get_setting_parameter($prefix . 'Max');
			$setting[$prefix . 'Min'] = get_setting_parameter($prefix . 'Min');
		}
		$this->data['faset'] = self::FASET;
		$this->data['setting'] = $setting;
		echo view('OpacFaset\Views\index', $this->data);
	}
}
