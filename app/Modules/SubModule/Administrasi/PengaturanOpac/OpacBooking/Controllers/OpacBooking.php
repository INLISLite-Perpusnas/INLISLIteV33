<?php

namespace OpacBooking\Controllers;

class OpacBooking extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Pemesanan Koleksi';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-opac-booking/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-opac-booking');
			}

			$this->validation->setRules([
				'IsBookingActivated'    => ['label' => 'Aktifkan Booking', 'rules' => 'permit_empty|in_list[0,1]'],
				'JumlahBookingMaksimal' => ['label' => 'Jumlah Pemesanan Maksimal', 'rules' => 'required|is_natural_no_zero'],
				'BookingExpired'        => ['label' => 'Lama Pemesanan Sebelum Kadaluarsa', 'rules' => 'required|regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]'],
			]);

			if ($this->validation->withRequest($this->request)->run()) {
				$expired = $this->request->getPost('BookingExpired');
				if (strlen($expired) === 5) {
					$expired .= ':00';
				}
				set_setting_parameter('IsBookingActivated', $this->request->getPost('IsBookingActivated') ? '1' : '0');
				set_setting_parameter('JumlahBookingMaksimal', $this->request->getPost('JumlahBookingMaksimal'));
				set_setting_parameter('BookingExpired', $expired);

				set_message('toastr_msg', 'Pengaturan Pemesanan Koleksi berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-opac-booking');
		}

		$this->data['setting'] = [
			'IsBookingActivated'    => get_setting_parameter('IsBookingActivated'),
			'JumlahBookingMaksimal' => get_setting_parameter('JumlahBookingMaksimal'),
			'BookingExpired'        => get_setting_parameter('BookingExpired'),
		];
		echo view('OpacBooking\Views\index', $this->data);
	}
}
