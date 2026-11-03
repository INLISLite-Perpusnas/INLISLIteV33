<?php

namespace OpacHistoriPencarian\Controllers\Api;

use App\Libraries\DataTable;

class OpacHistoriPencarian extends \Base\Controllers\BaseResourceController
{
	public function datatable()
	{
		$db = db_connect();
		if (!$db->tableExists('opaclogs')) {
			return $this->response->setJSON(['recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
		}

		$builder = $db->table('opaclogs as a')
			->select('a.ID, a.waktu, a.User_id, a.ip, a.jenis_pencarian, a.keyword, a.jenis_bahan, a.url');

		return DataTable::of($builder)
			->addNumbering('no')
			->edit('User_id', fn($row) => esc($row->User_id))
			->edit('ip', fn($row) => esc($row->ip))
			->edit('jenis_pencarian', fn($row) => esc($row->jenis_pencarian))
			->edit('jenis_bahan', fn($row) => esc($row->jenis_bahan))
			->edit('keyword', function ($row) {
				$keyword = esc($row->keyword);
				if (empty($row->url) || !preg_match('#^(https?:)?/#i', $row->url)) {
					return $keyword;
				}
				return '<a href="' . esc($row->url, 'attr') . '" target="_blank" rel="noopener">' . $keyword . '</a>';
			})
			->toJson();
	}
}
