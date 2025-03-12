<?php

namespace SurveiPemustaka\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class SurveiPemustaka extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $surveiModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->surveiModel = new \SurveiPemustaka\Models\SurveiModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/surveipemustaka/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('survey as a')
			->select('a.ID as id, a.ID as action')
			->select('a.NomorUrut as sort, a.IsActive as active')
			->select('a.NamaSurvey as Name, a.TanggalMulai as StartDate, a.TanggalSelesai as EndDate, a.TargetSurvey, 0 as Respondent');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Name', function ($row) {
				$html  =  '<b>' . $row->Name . '</b>';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('TargetSurvey', function ($row) {
				$status = $row->TargetSurvey == 1 ? 'Anggota' : 'Semua';
				$class = $row->TargetSurvey == 1 ? 'success' : 'info';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('StartDate', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . substr($row->StartDate, 0, 10) . '</badge>';
				return $html;
			})
			->edit('EndDate', function ($row) {
				$html  =  '<badge class="badge badge-warning badge-pill">' . substr($row->EndDate, 0, 10) . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$html   = '<a href="' . base_url('surveipemustaka/question/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Pertanyaan" class="btn btn-info"><i class="pe-7s-menu font-weight-bold"> </i> </a> ';
				$html  .= '<a href="javascript:void(0);" data-href="' . base_url('api/surveipemustaka/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a> ';
				$html  .= '<a href="' . base_url('surveipemustaka/apply_status/' . $row->id . '?field=IsActive&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a> ';
				$html  .= '<a href="' . base_url('surveipemustaka/apply_status/' . $row->id . '?field=IsActive&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a> ';
				$html  .= '<a href="javascript:void(0);" data-href="' . base_url('surveipemustaka/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}

	public function question_datatable($Survey_id = null)
	{
		$db = db_connect('data');
		$builder = $db->table('survey_pertanyaan as a')
			->select('a.ID as id, a.ID as action')
			->select('a.NoUrut as sort')
			->select('a.Pertanyaan as Name, a.JenisPertanyaan as Type, a.Orientation, a.IsMandatory, IsCanMultipleAnswer, 0 as Items')
			->where('a.Survey_id', $Survey_id)
			->orderBy('a.NoUrut');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Name', function ($row) {
				$html  =  '<b>' . $row->Name . '</b>';
				return $html;
			})
			->edit('IsMandatory', function ($row) {
				$status = $row->IsMandatory == 1 ? 'Ya' : 'Tidak';
				$class = $row->IsMandatory == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('IsCanMultipleAnswer', function ($row) {
				$status = $row->IsCanMultipleAnswer == 1 ? 'Ya' : 'Tidak';
				$class = $row->IsCanMultipleAnswer == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$html 	= '';
				if ($row->IsCanMultipleAnswer == 1) {
					$html  .= '<a href="' . base_url('surveipemustaka/items/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Isian" class="btn btn-info"><i class="pe-7s-menu font-weight-bold"> </i> </a> ';
				} else {
					$html  .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
				}
				$html  .= '<a href="javascript:void(0);" data-href="' . base_url('api/surveipemustaka/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a> ';
				$html  .= '<a href="javascript:void(0);" data-href="' . base_url('surveipemustaka/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}

	public function items_datatable($Survey_Pertanyaan_id = null)
	{
		$db = db_connect('data');
		$builder = $db->table('survey_pilihan as a')
			->select('a.ID as id, a.ID as action')
			->select('a.Pilihan as Name, a.ChoosenCount as Respondent')
			->where('a.Survey_Pertanyaan_id', $Survey_Pertanyaan_id)
			->orderBy('a.ID');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Name', function ($row) {
				$html  =  '<b>' . $row->Name . '</b>';
				return $html;
			})
			->edit('action', function ($row) {
				$html   = '<a href="javascript:void(0);" data-href="' . base_url('api/surveipemustaka/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a> ';
				$html  .= '<a href="javascript:void(0);" data-href="' . base_url('surveipemustaka/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a> ';
				return $html;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->surveiModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->surveiModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'Name' => $this->request->getPost('Name'),
		);

		$save_data_id = $this->surveiModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Survei berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Survei berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Survei gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Name' => $this->request->getPost('Name'),
		);

		$update_data_id = $this->surveiModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Survei berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Survei berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Survei gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->surveiModel->find($id);
		if ($data) {
			$this->surveiModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Survei berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Survei.info.not_found') . ' ID:' . $id);
		}
	}
}
