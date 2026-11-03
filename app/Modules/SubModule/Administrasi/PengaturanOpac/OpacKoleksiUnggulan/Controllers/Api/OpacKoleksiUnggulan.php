<?php

namespace OpacKoleksiUnggulan\Controllers\Api;

use App\Libraries\DataTable;

class OpacKoleksiUnggulan extends \Base\Controllers\BaseResourceController
{
	public function datatable()
	{
		// allow_ajax=false: request DataTables selalu AJAX, cek permission sebenarnya
		$canDelete = is_allowed('master-opac-koleksi-unggulan/delete', null, false);
		$builder = db_connect()->table('catalogs as a')
			->select('a.ID, a.ID as action, a.Title, a.Author, a.PublishYear, w.Name as worksheet')
			->join('worksheets w', 'w.ID = a.Worksheet_id', 'left')
			->where('a.IsPopuler', 1);

		return DataTable::of($builder)
			->addNumbering('no')
			->edit('Title', fn($row) => '<b>' . esc($row->Title) . '</b>')
			->edit('Author', fn($row) => esc($row->Author))
			->edit('PublishYear', fn($row) => esc($row->PublishYear))
			->edit('worksheet', fn($row) => esc($row->worksheet))
			->edit('action', function ($row) use ($canDelete) {
				if (!$canDelete) {
					return '';
				}
				return '<span class="has-tip" data-tip="Hapus dari Koleksi Unggulan (katalog tidak ikut terhapus)"><a href="javascript:void(0);" data-href="' . base_url('master-opac-koleksi-unggulan/delete') . '" data-id="' . $row->ID . '" class="ina-action ina-action-danger remove-data"><i class="fas fa-trash-alt" aria-hidden="true"></i></a></span>';
			})
			->toJson();
	}

	public function catalog()
	{
		$builder = db_connect()->table('catalogs as a')
			->select('a.ID, a.ID as action, a.BIBID, a.Title, a.Author, a.PublishYear, w.Name as worksheet')
			->join('worksheets w', 'w.ID = a.Worksheet_id', 'left')
			->groupStart()->where('a.IsQUARANTINE', 0)->orWhere('a.IsQUARANTINE', null)->groupEnd()
			->groupStart()->where('a.IsPopuler !=', 1)->orWhere('a.IsPopuler', null)->groupEnd();

		return DataTable::of($builder)
			->addNumbering('no')
			->edit('BIBID', fn($row) => esc($row->BIBID))
			->edit('Title', fn($row) => esc($row->Title))
			->edit('Author', fn($row) => esc($row->Author))
			->edit('PublishYear', fn($row) => esc($row->PublishYear))
			->edit('worksheet', fn($row) => esc($row->worksheet))
			->edit('action', function ($row) {
				return '<span class="has-tip" data-tip="Jadikan judul ini Koleksi Unggulan"><a href="javascript:void(0);" data-href="' . base_url('master-opac-koleksi-unggulan/create') . '" data-id="' . $row->ID . '" class="btn btn-sm btn-success pick-data"><i class="fa fa-check"></i> Pilih</a></span>';
			})
			->toJson();
	}
}
