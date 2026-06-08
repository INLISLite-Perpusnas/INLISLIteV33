<?php

namespace JenisBahanPustaka\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisBahanPustaka extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $wsModel;
	protected $wsfModel;
	protected $wsfItemModel;

	function __construct()
	{
		$this->wsModel = new \JenisBahanPustaka\Models\WsModel();
		$this->wsfModel = new \JenisBahanPustaka\Models\WsfModel();
		$this->wsfItemModel = new \JenisBahanPustaka\Models\WsfItemModel();
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('worksheets as a')
			->select('a.ID as id, a.ID as action, a.Format_id as format_id, a.Name as name, a.UpdateDate as update_date')
			->select('f.Name as format')
			->join('formats as f', 'f.ID = a.Format_id');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('name', function ($row) {
				$html  =  '<b>' . $row->name . '</b>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('master-jenis-bahan-pustaka/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jenis-bahan-pustaka/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function wsf_delete($id = null)
	{
		$data = $this->wsfModel->find($id);
		if ($data) {
			$this->wsfModel->delete($id);
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
