<?php

namespace HariLibur\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
use DateTime;
use DateInterval;
//use Hermawan\DataTables\DataTable;

class HariLibur extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $hariliburModel;
	protected $valIDation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		$this->hariliburModel = new \HariLibur\Models\HariLiburModel();
		$this->valIDation = \Config\Services::valIDation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/harilibur/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		helper('reference');
		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('holidays as a')
			->select('a.ID, a.ID as action,a.Dates,a.Names, a.UpdateDate')
			->select('a.active')
			->where('a.branch_id', 0);

		if (is_profiling()) {
			$builder->orWhere('a.Branch_ID', user()->branch_id);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Names', function ($row) {
				$html  =  '<b>' . $row->Names . '</b>';
				return $html;
			})
			->edit('Dates', function ($row) {
				$html  =  '<b>' . (substr(($row->Dates), 0, 10)) . '</b>';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:voID(0);" data-href="' . base_url('api/hari-libur/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-hari-libur/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-ID="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-hari-libur/apply_status/' . $row->ID . '?field=active&value=0') . '" data-ID="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:voID(0);" data-href="' . base_url('master-hari-libur/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->hariliburModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($ID = null)
	{
		$data = $this->hariliburModel->find($ID);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with ID ' . $ID);
		}
	}

	public function create()
	{
		$save_data = [
			'Names'    => $this->request->getPost('Names'),
			'Dates'    => $this->request->getPost('Dates'),
			'Branch_ID' => user()->branch_id ?? 0
		];

		$save_data_ID = $this->hariliburModel->insert($save_data);
		if ($save_data_ID) {
			$this->session->setFlashdata('toastr_msg', 'Hari Libur berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Hari Libur berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Hari Libur gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function createliburpanjang()
	{
		$startDateString = $this->request->getPost('StartDates');
		$endDateString = $this->request->getPost('EndDates');
		$startDate = new DateTime($startDateString);
		$endDate = new DateTime($endDateString);
		$Names = $this->request->getPost('Names');


		if ($endDate >= $startDate) {
			$save_data = [];
			for ($currentDate = clone $startDate; $currentDate <= $endDate; $currentDate->modify('+1 day')) {
				$save_data[] = [
					'Names' => $Names,
					'Dates' => $currentDate->format('Y-m-d'),
					'Branch_id' => user()->branch_id ?? 0,
					'CreateBy' => user()->id,
					'UpdateBy' => user()->id
				];
			}

			$save_data_ID = $this->hariliburModel->insertBatch($save_data);
			if ($save_data_ID) {
				$this->session->setFlashdata('toastr_msg', 'Hari Libur berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'error' => false,
					'message' => 'Hari Libur berhasil disimpan',
				];
			} else {
				$response = [
					'error' => true,
					'message' => 'Hari Libur gagal disimpan. Silakan coba lagi',
				];
			}

			return $this->simpleResponse($response);
		}
	}



	public function edit($ID = null)
	{
		$update_data = array(
			'Dates' => $this->request->getPost('Dates'),
			'Names' => $this->request->getPost('Names'),
			'Branch_id' => user()->branch_id ?? 0,
			'UpdateBy' => user()->id
		);

		$update_data_ID = $this->hariliburModel->update($ID, $update_data);
		if ($update_data_ID) {
			$this->session->setFlashdata('toastr_msg', 'Hari Libur berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Hari Libur berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Hari Libur gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($ID = null)
	{
		$data = $this->hariliburModel->find($ID);
		if ($data) {
			$this->hariliburModel->delete($ID);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Hari Libur berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('HariLibur.info.not_found') . ' ID:' . $ID);
		}
	}
}
