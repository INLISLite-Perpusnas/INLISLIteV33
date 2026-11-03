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
				$edit = '<a href="' . base_url('master-referensi/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="ina-action"><i class="fas fa-pen" aria-hidden="true"></i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-referensi/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus" aria-label="Hapus" class="ina-action ina-action-danger remove-data"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
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
