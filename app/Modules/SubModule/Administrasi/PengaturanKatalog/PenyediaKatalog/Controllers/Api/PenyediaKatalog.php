<?php

namespace PenyediaKatalog\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class PenyediaKatalog extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $libModel;
	protected $libItemModel;

	function __construct()
	{
		$this->libModel = new \PenyediaKatalog\Models\LibModel();
		$this->libItemModel = new \PenyediaKatalog\Models\LibItemModel();
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('library as a')
			->select('a.ID as id, a.ID as action, a.NAME as alias, a.FULLNAME as name, a.UpdateDate as update_date')
			->select('a.ID as database, a.URL as endpoint, a.PORT as port, a.DATABASENAME as db, a.RECORDSYNTAX as sintax, a.PROTOCOL as protocol');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('name', function ($row) {
				$html  =  '<b>' . $row->alias . '</b><br>' . $row->name;
				return $html;
			})
			->edit('database', function ($row) {
				$html  =  'Database: ' . $row->db . '<br>Port: ' . $row->port;
				return $html;
			})
			->edit('protocol', function ($row) {
				$html  =  'Protocol: ' . $row->protocol . '<br>Sintaks: ' . $row->sintax;
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('master-penyedia-katalog/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-penyedia-katalog/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function item_data_delete($id = null)
	{
		$data = $this->libItemModel->find($id);
		if ($data) {
			$this->libItemModel->delete($id);
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
