<?php

namespace BukuTamu\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class BukuTamu extends \Base\Controllers\BaseResourceController

{
	use ResponseTrait;
	protected $memberguestModel;
	protected $groupguestModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->groupguestModel = new \BukuTamu\Models\GroupGuestModel();
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
		$branch_id = branch_id();
		$builder = $db->table('memberguesses as a')
			->select('a.id, a.id as action')
			->select('a.CreateDate as VisitDate')
			->select('a.Nama as Member_name, a.NoAnggota as Member_no')
			->select('a.Location_id, locations.Name as Location_name')
			->select('location_library.Name as LocationLibrary_name')
			->join('locations', 'locations.ID = a.Location_id', 'left')
			->join('location_library', 'location_library.ID = locations.LocationLibrary_id', 'left')
			->where('a.NoAnggota IS NOT NULL');

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
			->edit('action', function ($row) {
				$html   = '<a href="javascript:void(0);" data-href="' . base_url('bukutamu/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}

public function non_anggota_datatable()
{
    $db = db_connect();
    $builder = $db->table('memberguesses as a')
        ->select('a.id, a.id as action')
        ->select('a.CreateDate as VisitDate')
        ->select('a.Nama as Visitor_name')
        ->select('a.Profesi_id, a.PendidikanTerakhir_id, a.Jeniskelamin_id')
        ->select('l.Name as Location_name')
        ->select('ll.Name as LocationLibrary_name')
        ->select('mp.Pekerjaan as Profesi_name')
        ->select('mpd.Nama as PendidikanTerakhir_name')
        ->select('jk.Name as JenisKelamin_name')
        ->join('locations l', 'l.ID = a.Location_id', 'left')
        ->join('location_library ll', 'll.ID = l.LocationLibrary_id', 'left')
        ->join('master_pekerjaan mp', 'mp.ID = a.Profesi_id', 'left')
        ->join('master_pendidikan mpd', 'mpd.ID = a.PendidikanTerakhir_id', 'left')
        ->join('jenis_kelamin jk', 'jk.ID = a.Jeniskelamin_id', 'left')
        ->where('a.NoAnggota IS NULL');
    
    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->edit('VisitDate', function ($row) {
            $html   = '<b>' . $row->VisitDate . '</b>';
            return $html;
        })
        ->edit('Visitor_name', function ($row) {
            $html   = '<b>' . $row->Visitor_name . '</b>';
            return $html;
        })
        ->edit('action', function ($row) {
            $html   = '<a href="javascript:void(0);" data-href="' . base_url('bukutamu/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
            return $html;
        })
        ->toJson();
    return $dataTable;
}
	public function rombongan_datatable()
	{
		$db = db_connect();
		$builder = $db->table('groupguesses as a')
			->select('a.ID, a.ID as id, a.ID as action')
			->select('a.CreateDate as VisitDate')
			->select('a.NamaKetua as Group_chief, a.AsalInstansi as Group_name, a.CountPersonel')
			->select('a.Location_id, locations.Name as Location_name')
			->select('location_library.Name as LocationLibrary_name')
			->join('locations', 'locations.ID = a.Location_id', 'left')
			->join('location_library', 'location_library.ID = locations.LocationLibrary_id', 'left');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('VisitDate', function ($row) {
				$html   = '<b>' . $row->VisitDate . '</b>';
				return $html;
			})
			->edit('Group_chief', function ($row) {
				$html   = '<b>' . $row->Group_chief . '</b>';
				return $html;
			})
			->edit('action', function ($row) {
				$html   = '<a href="javascript:void(0);" data-href="' . base_url('bukutamu/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}
}