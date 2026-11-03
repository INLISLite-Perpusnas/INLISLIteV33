<?php

namespace OpacKoleksiSeringDipinjam\Controllers;

class OpacKoleksiSeringDipinjam extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Koleksi Sering Dipinjam';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-koleksi-sering-dipinjam/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-koleksi-sering-dipinjam');
			}

			$this->validation->setRules([
				'ShowKoleksiSeringDipinjam' => ['label' => 'Tampilkan Koleksi Sering Dipinjam', 'rules' => 'required|in_list[TRUE,FALSE]'],
				'KoleksiSeringDipinjamShow' => ['label' => 'Maksimal Jumlah Koleksi', 'rules' => 'required|is_natural_no_zero'],
			]);

			if ($this->validation->withRequest($this->request)->run()) {
				set_setting_parameter('ShowKoleksiSeringDipinjam', $this->request->getPost('ShowKoleksiSeringDipinjam'));
				set_setting_parameter('KoleksiSeringDipinjamShow', $this->request->getPost('KoleksiSeringDipinjamShow'));
				// Beranda menyimpan cache data (is_home_cache); buang agar perubahan langsung tampil
				cache()->delete('home_index_data');
				set_message('toastr_msg', 'Pengaturan Koleksi Sering Dipinjam berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-koleksi-sering-dipinjam');
		}

		$this->data['setting'] = [
			'ShowKoleksiSeringDipinjam' => get_setting_parameter('ShowKoleksiSeringDipinjam'),
			'KoleksiSeringDipinjamShow' => get_setting_parameter('KoleksiSeringDipinjamShow'),
		];
		echo view('OpacKoleksiSeringDipinjam\Views\index', $this->data);
	}
}
