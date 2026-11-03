<?php

namespace OpacKoleksiUnggulan\Controllers;

class OpacKoleksiUnggulan extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Koleksi Unggulan';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-koleksi-unggulan/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-koleksi-unggulan');
			}

			$this->validation->setRules([
				'ShowKoleksiUnggulan' => ['label' => 'Tampilkan Koleksi Unggulan', 'rules' => 'required|in_list[TRUE,FALSE]'],
				'KoleksiUnggulanShow' => ['label' => 'Maksimal Jumlah Koleksi', 'rules' => 'required|is_natural_no_zero'],
			]);

			if ($this->validation->withRequest($this->request)->run()) {
				set_setting_parameter('ShowKoleksiUnggulan', $this->request->getPost('ShowKoleksiUnggulan'));
				set_setting_parameter('KoleksiUnggulanShow', $this->request->getPost('KoleksiUnggulanShow'));
				// Beranda menyimpan cache data (is_home_cache); buang agar perubahan langsung tampil
				cache()->delete('home_index_data');
				set_message('toastr_msg', 'Pengaturan Koleksi Unggulan berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-koleksi-unggulan');
		}

		$this->data['setting'] = [
			'ShowKoleksiUnggulan' => get_setting_parameter('ShowKoleksiUnggulan'),
			'KoleksiUnggulanShow' => get_setting_parameter('KoleksiUnggulanShow'),
		];
		echo view('OpacKoleksiUnggulan\Views\index', $this->data);
	}

	// Koleksi unggulan = catalogs.IsPopuler = 1
	public function create()
	{
		return $this->setPopuler('create', 1, 'ditambahkan ke');
	}

	public function delete()
	{
		return $this->setPopuler('delete', 0, 'dihapus dari');
	}

	private function setPopuler(string $permission, int $value, string $action)
	{
		if (!is_allowed('master-opac-koleksi-unggulan/' . $permission, null, false)) {
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Akses Ditolak');
			set_message('swal_text', lang('App.permission.not.have'));
			return redirect()->to('master-opac-koleksi-unggulan');
		}

		$catalogId = (int) $this->request->getPost('id');
		$builder = db_connect()->table('catalogs');
		if (!$builder->where('ID', $catalogId)->countAllResults()) {
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Gagal');
			set_message('swal_text', 'Katalog tidak ditemukan');
			return redirect()->to('master-opac-koleksi-unggulan');
		}

		$saved = $builder->where('ID', $catalogId)->update([
			'IsPopuler'      => $value,
			'UpdateBy'       => user_id(),
			'UpdateDate'     => date('Y-m-d H:i:s'),
			'UpdateTerminal' => $this->request->getIPAddress(),
		]);

		if ($saved) {
			cache()->delete('home_index_data');
			set_message('swal_icon', 'success');
			set_message('swal_title', 'Berhasil');
			set_message('swal_text', 'Katalog berhasil ' . $action . ' Koleksi Unggulan');
		} else {
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Gagal');
			set_message('swal_text', 'Katalog gagal ' . $action . ' Koleksi Unggulan');
		}
		return redirect()->to('master-opac-koleksi-unggulan');
	}
}
