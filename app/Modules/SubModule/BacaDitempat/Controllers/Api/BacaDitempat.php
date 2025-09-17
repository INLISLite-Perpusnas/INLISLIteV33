<?php

namespace BacaDitempat\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class BacaDitempat extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $bacaditempatModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->bacaditempatModel = new \BacaDitempat\Models\BacaDitempatModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/survei-pemustaka/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('bacaditempat');
		helper('reference');
	}

	public function datatable()
	{
		$db = db_connect();
		$branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
		$builder = $db->table('bacaditempat as a')
			->select('a.ID as id, a.ID as action')
			->select('a.CreateDate as VisitDate')
			->select('a.Member_id, members.Fullname as Member_name, members.MemberNo as Member_no')
			->select('a.Location_Id as Location_id , locations.Name as Location_name')
			->select('location_library.Name as LocationLibrary_name')
			->select('a.collection_id as Collection_id, "" as Barcode_no')
			->join('members', 'members.ID = a.Member_id', 'left')
			->join('locations', 'locations.ID = a.Location_id', 'left')
			->join('location_library', 'location_library.ID = locations.LocationLibrary_id', 'left')
			->groupBy('a.ID','desc');
		
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('VisitDate', function ($row) {
				$html   = '<b>' . $row->VisitDate . '</b>';
				return $html;
			})
			->edit('Member_name', function ($row) {
				$html   = '<b>' . $row->Member_name . '</b>';
				return $html;
			})
			->edit('Member_no', function ($row) {
				$html   =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-id-card fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . $row->Member_no . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('Barcode_no', function ($row) {
				$data = get_ref_single('collections', 'ID="' . $row->Collection_id . '"', 'data');
				$html   =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . $data->NomorBarcode ?? '' . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('action', function ($row) {
				$html   = '<a href="javascript:void(0);" data-href="' . base_url('bacaditempat/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}
}
