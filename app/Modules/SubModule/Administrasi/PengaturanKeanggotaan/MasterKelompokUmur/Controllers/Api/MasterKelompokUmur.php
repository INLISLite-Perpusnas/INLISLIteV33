<?php

namespace MasterKelompokUmur\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class MasterKelompokUmur extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $masterKelompokUmurModel;
	protected $validation;
	protected $session;

	function __construct()
	{
		  $this->masterKelompokUmurModel = new \MasterKelompokUmur\Models\MasterKelompokUmurModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		helper('reference');
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$branchId=user()->branch_id;
		$builder = $db->table('master_range_umur as a')
		  ->select('a.id, a.id as action, a.umur1 as Umur1, a.umur2 as Umur2, a.keterangan as Keterangan, a.NoUrut as NomorUrut, a.CreateDate as CreateDate, a.UpdateDate as UpdateDate, a.active as active')
			->select('a.active');
			// ->whereIn('a.Branch_id', [$branchId, 0]);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Nama', function ($row) {
				$html  =  '<b>' . $row->Nama . '</b>';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				 $html = \Carbon\Carbon::parse($row->UpdateDate)->format('m-d-Y');
				return $html;
			})
			->edit('action', function ($row) use ($branchId) {
			
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-kelompok-umur/detail/' . $row->id) . '" data-toggle="modal" data-target="#modal_update" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-kelompok-umur/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-kelompok-umur/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-kelompok-umur/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			
		})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->masterKelompokUmurModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->masterKelompokUmurModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'umur1' => $this->request->getPost('umur1'),
			'umur2' => $this->request->getPost('umur2'),
			'keterangan' => $this->request->getPost('keterangan'),
			// 'Branch_id' => branch_id()
		);

		$save_data_id = $this->masterKelompokUmurModel->insert($save_data);
		if ($save_data_id) {
			$response = [
				'error' => false,
				'message' => 'Master Kelompok Umur berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Master Kelompok Umur gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'umur1	' => $this->request->getPost('umur1'),
			'umur2' => $this->request->getPost('umur2'),
			'Keterangan' => $this->request->getPost('Keterangan')
		);

		$update_data_id = $this->masterKelompokUmurModel->update($id, $update_data);
		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Master Kelompok Umur berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Master Kelompok Umur gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->masterKelompokUmurModel->find($id);
		if ($data) {
			$this->masterKelompokUmurModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Master Kelompok Umur berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('MasterFakultas.info.not_found') . ' ID:' . $id);
		}
	}
}
