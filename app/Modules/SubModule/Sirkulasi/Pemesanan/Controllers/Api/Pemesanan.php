<?php

namespace Pemesanan\Controllers\Api;

use App\Libraries\DataTable;

class Pemesanan extends \Base\Controllers\BaseResourceController
{
	/** Status pesanan yang bisa difilter dari halaman daftar. */
	public const STATUSES = ['Menunggu', 'Kedaluwarsa', 'Selesai', 'Dibatalkan'];

	/**
	 * Status pesanan:
	 *  - Dibatalkan  : dibatalkan petugas (active = 0)
	 *  - Menunggu    : belum lewat batas waktu dan koleksi masih ditahan untuk pemesan
	 *  - Selesai     : koleksi sudah dipinjam pemesan dalam rentang pesanan, atau sudah
	 *                  tidak ditahan lagi sebelum batas waktu
	 *  - Kedaluwarsa : lewat batas waktu tanpa diambil
	 */
	public static function status(object $row, string $now): string
	{
		if ((string) $row->active === '0') {
			return 'Dibatalkan';
		}
		if ((int) ($row->Diambil ?? 0) === 1) {
			return 'Selesai';
		}
		// Pesanan hanya dibuat saat koleksi tersedia (Status_id 1). Kalau koleksi sudah
		// dipinjam, pesanan dianggap selesai walau tanda pesanan di collections belum dilepas.
		$held = $row->BookingMemberID === $row->memberId && $row->BookingExpiredDate === $row->bookingExpired
			&& (int) $row->CollectionStatus === 1;
		if (!empty($row->bookingExpired) && $row->bookingExpired > $now) {
			return $held ? 'Menunggu' : 'Selesai';
		}
		return empty($row->bookingExpired) ? 'Selesai' : 'Kedaluwarsa';
	}

	public function datatable()
	{
		$now = date('Y-m-d H:i:s');
		$db = db_connect();
		$builder = $db->table('bookinglogs b')
			->select('b.bookingDate, b.memberId, b.bookingExpired, b.collectionId, b.active, b.collectionId as action, b.collectionId as status')
			->select('m.Fullname, col.NomorBarcode, col.BookingMemberID, col.BookingExpiredDate, col.Status_id as CollectionStatus')
			->select('a.Title, a.Publisher, loc.Name as LocationName')
			->join('members m', 'm.MemberNo = b.memberId', 'left')
			->join('collections col', 'col.ID = b.collectionId', 'left')
			->join('catalogs a', 'a.ID = col.Catalog_id', 'left')
			->join('locations loc', 'loc.ID = col.Location_id', 'left')
			->orderBy('b.bookingDate', 'DESC');

		// DataTable membaca daftar kolom dari SELECT ... FROM. Subquery di bawah ditambahkan
		// setelahnya supaya kata FROM di dalam subquery tidak mengacaukan pembacaan itu.
		$table = DataTable::of($builder);

		// Pesanan dianggap diambil kalau pemesan meminjam koleksi itu antara waktu pesan dan batas ambil.
		$taken = 'EXISTS (SELECT 1 FROM collectionloanitems li WHERE li.Collection_id = b.collectionId'
			. ' AND li.member_id = m.ID AND li.LoanDate >= b.bookingDate'
			. ' AND (b.bookingExpired IS NULL OR li.LoanDate <= b.bookingExpired))';
		$builder->select("($taken) as Diambil", false);

		$held = 'COALESCE(col.BookingMemberID = b.memberId AND col.BookingExpiredDate = b.bookingExpired AND col.Status_id = 1, 0)';
		switch ((string) $this->request->getGet('status')) {
			case 'Menunggu':
				$builder->where('COALESCE(b.active, 1) <>', 0)->where("NOT $taken", null, false)
					->where('b.bookingExpired >', $now)->where($held, null, false);
				break;
			case 'Selesai':
				$builder->where('COALESCE(b.active, 1) <>', 0)->groupStart()
					->where($taken, null, false)
					->orWhere('b.bookingExpired IS NULL', null, false)
					->orGroupStart()->where('b.bookingExpired >', $now)->where("NOT $held", null, false)->groupEnd()
					->groupEnd();
				break;
			case 'Kedaluwarsa':
				$builder->where('COALESCE(b.active, 1) <>', 0)->where("NOT $taken", null, false)
					->where('b.bookingExpired <=', $now);
				break;
			case 'Dibatalkan':
				$builder->where('b.active', 0);
				break;
		}

		$canCancel = is_allowed('sirkulasi-pemesanan/delete', null, false);

		return $table
			->addNumbering('no')
			->edit('bookingDate', function ($row) {
				return date('d-m-Y H:i:s', strtotime($row->bookingDate));
			})
			->edit('bookingExpired', function ($row) {
				return $row->bookingExpired ? date('d-m-Y H:i:s', strtotime($row->bookingExpired)) : '-';
			})
			->edit('memberId', function ($row) {
				return esc($row->memberId);
			})
			->edit('Fullname', function ($row) {
				return esc($row->Fullname ?? '-');
			})
			->edit('NomorBarcode', function ($row) {
				return esc($row->NomorBarcode ?? '-');
			})
			->edit('Title', function ($row) {
				return esc($row->Title ?? '-');
			})
			->edit('Publisher', function ($row) {
				return esc($row->Publisher ?? '-');
			})
			->edit('LocationName', function ($row) {
				return esc($row->LocationName ?? '-');
			})
			->edit('status', function ($row) use ($now) {
				$status = self::status($row, $now);
				$class = ['Menunggu' => 'warning', 'Selesai' => 'success', 'Kedaluwarsa' => 'secondary', 'Dibatalkan' => 'danger'][$status];
				return '<span class="badge badge-pill badge-' . $class . '">' . $status . '</span>';
			})
			->edit('action', function ($row) use ($now, $canCancel) {
				if (!$canCancel || self::status($row, $now) !== 'Menunggu') {
					return '';
				}
				return '<form method="post" action="' . site_url('sirkulasi-pemesanan/delete') . '" class="m-0">'
					. csrf_field()
					. '<input type="hidden" name="memberId" value="' . esc($row->memberId, 'attr') . '">'
					. '<input type="hidden" name="collectionId" value="' . (int) $row->collectionId . '">'
					. '<input type="hidden" name="bookingDate" value="' . esc($row->bookingDate, 'attr') . '">'
					. '<button type="submit" class="btn btn-danger btn-sm btn-batal-pesan" data-barcode="' . esc($row->NomorBarcode ?? '', 'attr') . '">'
					. '<i class="fa fa-times"></i> Batalkan</button></form>';
			})
			->toJson();
	}
}
