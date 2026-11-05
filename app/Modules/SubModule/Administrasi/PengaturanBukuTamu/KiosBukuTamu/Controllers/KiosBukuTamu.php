<?php

namespace KiosBukuTamu\Controllers;

use KiosBukuTamu\Libraries\KioskAuth;

class KiosBukuTamu extends \Base\Controllers\BaseController
{
	private const ROUTE = '/master-kios-buku-tamu';

	public function index()
	{
		$kioskAuth = new KioskAuth();
		$this->data['title'] = 'Aktivasi Kios';
		$this->data['locations'] = $kioskAuth->activeLocations();
		$this->data['kiosks'] = $kioskAuth->all();
		$this->data['new_code'] = session()->getFlashdata('kiosk_code');

		echo view('KiosBukuTamu\Views\index', $this->data);
	}

	public function create()
	{
		if (!is_allowed('master-kios-buku-tamu/create', null, false)) {
			return $this->denied();
		}

		$kioskAuth = new KioskAuth();
		$locationId = $this->request->getPost('Location_id');
		$location = null;
		foreach ($kioskAuth->activeLocations() as $row) {
			if (is_string($locationId) && (string) $row->ID === $locationId) {
				$location = $row;
				break;
			}
		}
		if (!$location) {
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Gagal');
			set_message('swal_text', 'Pilih lokasi ruang yang aktif.');
			return redirect()->to(self::ROUTE);
		}

		try {
			$code = $kioskAuth->createCode((int) $location->ID, login_id(), $this->request->getIPAddress());
		} catch (\RuntimeException $e) {
			log_message('error', 'Gagal membuat kode kios: ' . $e->getMessage());
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Gagal');
			set_message('swal_text', 'Kode tidak dapat dibuat. Periksa pengaturan encryption.key di server.');
			return redirect()->to(self::ROUTE);
		}

		session()->setFlashdata('kiosk_code', [
			'code' => $code,
			'location' => $location->Code . ' - ' . $location->Name,
			'expires' => date('H:i', time() + KioskAuth::CODE_TTL),
		]);
		return redirect()->to(self::ROUTE);
	}

	public function delete()
	{
		if (!is_allowed('master-kios-buku-tamu/delete', null, false)) {
			return $this->denied();
		}

		$id = $this->request->getPost('id');
		$revoked = is_string($id) && ctype_digit($id) && (new KioskAuth())->revoke((int) $id, login_id());

		set_message('swal_icon', $revoked ? 'success' : 'error');
		set_message('swal_title', $revoked ? 'Berhasil' : 'Gagal');
		set_message('swal_text', $revoked ? 'Akses kios berhasil dicabut.' : 'Kios tidak ditemukan atau sudah dicabut.');
		return redirect()->to(self::ROUTE);
	}

	private function denied()
	{
		set_message('swal_icon', 'error');
		set_message('swal_title', 'Akses Ditolak');
		set_message('swal_text', lang('App.permission.not.have'));
		return redirect()->to(self::ROUTE);
	}
}
