<?php

namespace Referensi\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Referensi extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $refModel;
	protected $refItemModel;

	function __construct()
	{
		$this->refModel = new \Referensi\Models\RefModel();
		$this->refItemModel = new \Referensi\Models\RefItemModel();
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('refferences as a')
			->select('a.ID as id, a.ID as action, a.Name as name, a.Format_id, a.UpdateDate as update_date')
			->select('f.Name as format')
			->join('formats as f', 'f.ID = a.Format_id');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('name', function ($row) {
				$html  =  '<b>' . $row->name . '</b>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('master-referensi/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-referensi/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function item_data_delete($id = null)
	{
		$data = $this->refItemModel->find($id);
		if ($data) {
			$this->refItemModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Data berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan' . ' ID:' . $id);
		}
	}
}
