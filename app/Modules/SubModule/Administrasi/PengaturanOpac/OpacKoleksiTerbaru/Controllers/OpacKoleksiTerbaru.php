<?php

namespace OpacKoleksiTerbaru\Controllers;

class OpacKoleksiTerbaru extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Koleksi Terbaru';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-koleksi-terbaru/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-koleksi-terbaru');
			}

			$this->validation->setRules([
				'ShowKoleksiTerbaru' => ['label' => 'Tampilkan Koleksi Terbaru', 'rules' => 'required|in_list[TRUE,FALSE]'],
				'KoleksiTerbaruShow' => ['label' => 'Maksimal Jumlah Koleksi', 'rules' => 'required|is_natural_no_zero'],
			]);

			if ($this->validation->withRequest($this->request)->run()) {
				set_setting_parameter('ShowKoleksiTerbaru', $this->request->getPost('ShowKoleksiTerbaru'));
				set_setting_parameter('KoleksiTerbaruShow', $this->request->getPost('KoleksiTerbaruShow'));
				// Beranda menyimpan cache data (is_home_cache); buang agar perubahan langsung tampil
				cache()->delete('home_index_data');
				set_message('toastr_msg', 'Pengaturan Koleksi Terbaru berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-koleksi-terbaru');
		}

		$this->data['setting'] = [
			'ShowKoleksiTerbaru' => get_setting_parameter('ShowKoleksiTerbaru'),
			'KoleksiTerbaruShow' => get_setting_parameter('KoleksiTerbaruShow'),
		];
		echo view('OpacKoleksiTerbaru\Views\index', $this->data);
	}
}
