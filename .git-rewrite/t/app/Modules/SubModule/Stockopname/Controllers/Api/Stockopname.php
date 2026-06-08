<?php

namespace Stockopname\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Stockopname extends \Base\Controllers\BaseResourceController

{
	use ResponseTrait;
	protected $stockopnameModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->stockopnameModel = new \Stockopname\Models\StockopnameModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/survei-pemustaka/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('bukutamu');
	}

	public function datatable()
	{
	
		$db = db_connect();
		$builder = $db->table('stockopname as a')
			->select('a.ID, a.ID as action')
			->select('a.CreateDate as CreateDate, a.ProjectName as ProjectName, a.TglMulai as TglMulai, a.Koordinator, a.Tahun,a.Keterangan as Keterangan');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			
			->edit('action', function ($row) {
				$edit='<a href="javascript:void(0);: data-href="' . base_url('stockopname/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a> ';
				$delete   = '<a href="javascript:void(0);" data-href="' . base_url('stockopname/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

}
