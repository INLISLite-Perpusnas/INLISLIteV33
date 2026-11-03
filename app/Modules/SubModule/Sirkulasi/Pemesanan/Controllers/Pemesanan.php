<?php

namespace Pemesanan\Controllers;

/**
 * Daftar koleksi yang dipesan anggota lewat OPAC (tabel bookinglogs)
 * dan pembatalan pesanan oleh petugas sirkulasi.
 */
class Pemesanan extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Daftar Koleksi Yang Dipesan';
		return view('Pemesanan\Views\list', $this->data);
	}

	/**
	 * Batalkan pesanan: lepaskan BookingMemberID/BookingExpiredDate di collections
	 * dan tandai baris bookinglogs nonaktif (active = 0).
	 * bookinglogs tidak punya primary key, jadi baris dikenali dari anggota + koleksi + tanggal pesan.
	 */
	public function delete()
	{
		if (!is_allowed('sirkulasi-pemesanan/delete', null, false)) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('sirkulasi-pemesanan');
		}

		$memberNo = (string) $this->request->getPost('memberId');
		$collectionId = (int) $this->request->getPost('collectionId');
		$bookingDate = (string) $this->request->getPost('bookingDate');

		$db = db_connect();
		$log = $db->table('bookinglogs')
			->where('memberId', $memberNo)->where('collectionId', $collectionId)->where('bookingDate', $bookingDate)
			->get()->getRow();
		if (!$log) {
			set_message('toastr_msg', 'Data pesanan tidak ditemukan');
			set_message('toastr_type', 'error');
			return redirect()->to('sirkulasi-pemesanan');
		}

		$db->transStart();
		// Hanya lepaskan koleksi jika masih ditahan untuk pesanan ini
		$db->table('collections')
			->where('ID', $collectionId)->where('BookingMemberID', $memberNo)->where('BookingExpiredDate', $log->bookingExpired)
			->update(['BookingMemberID' => null, 'BookingExpiredDate' => null]);
		$db->table('bookinglogs')
			->where('memberId', $memberNo)->where('collectionId', $collectionId)->where('bookingDate', $bookingDate)
			->update(['active' => 0]);
		$db->transComplete();

		if ($db->transStatus()) {
			set_message('toastr_msg', 'Pesanan koleksi berhasil dibatalkan');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Pesanan koleksi gagal dibatalkan');
			set_message('toastr_type', 'error');
		}
		return redirect()->to('sirkulasi-pemesanan');
	}
}
