<?php

namespace OpacUsulanKoleksi\Controllers;

class OpacUsulanKoleksi extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Usulan Koleksi';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-usulan-koleksi/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-usulan-koleksi');
			}

			$this->validation->setRules([
				'UsulanKoleksi' => ['label' => 'Tampilkan Usulan Koleksi', 'rules' => 'required|in_list[TRUE,FALSE]'],
			]);

			if ($this->validation->withRequest($this->request)->run()) {
				set_setting_parameter('UsulanKoleksi', $this->request->getPost('UsulanKoleksi'));
				set_message('toastr_msg', 'Pengaturan Usulan Koleksi berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-usulan-koleksi');
		}

		$this->data['setting'] = [
			'UsulanKoleksi' => get_setting_parameter('UsulanKoleksi'),
		];
		echo view('OpacUsulanKoleksi\Views\index', $this->data);
	}
}
