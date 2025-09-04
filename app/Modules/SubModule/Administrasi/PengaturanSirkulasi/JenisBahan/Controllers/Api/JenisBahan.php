<?php

namespace JenisBahan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisBahan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenisbahanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->jenisbahanModel = new \JenisBahan\Models\JenisBahanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenisbahan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('worksheets as a')
			->select('a.ID, a.ID as action, a.MaxPinjamKoleksi, a.Name, a.MaxLoanDays, a.DendaTenorJumlah, a.DaySuspend, a.DayPerpanjang, a.CountPerpanjang, a.active, a.Branch_id')
			->where('a.Branch_id', 0);

		if (is_profiling()) {
			$builder->orWhere('a.Branch_id', user()->branch_id);
		}
		// ✅ Tangkap parameter pencarian
		$request = service('request');
		$search = $request->getPost('search')['value'] ?? '';

		if (!empty($search)) {
			$builder->groupStart()
				->like('a.Name', $search)
				->orLike('a.MaxPinjamKoleksi', $search)
				->orLike('a.MaxLoanDays', $search)
				->orLike('a.DendaTenorJumlah', $search)
				->orLike('a.DaySuspend', $search)
				->orLike('a.DayPerpanjang', $search)
				->orLike('a.CountPerpanjang', $search)
				->groupEnd();
		}
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-jenis-bahan/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-jenis-bahan/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-jenis-bahan/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jenis-bahan/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				$html = '';
				$html .= $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
				return $html;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenisbahanModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenisbahanModel->find($id);
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
			'Branch_id' => branch_id()
		);

		$save_data_id = $this->jenisbahanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Bahan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Bahan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Bahan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($ID = null)
	{
		$update_data = array(
			'Name' => $this->request->getPost('Name'),
			'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
			'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
			'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
			'CountPerpanjang' => $this->request->getPost('CountPerpanjang'),
			'DendaType' => $this->request->getPost('DendaType'),
			'DaySuspend' => $this->request->getPost('DaySuspend'),
			'DendaPerTenor' => $this->request->getPost('DendaPerTenor'),
			'SuspendType' => $this->request->getPost('SuspendType'),
			'DaySuspend' => $this->request->getPost('DaySuspend')
		);

		$update_data_id = $this->jenisbahanModel->update($ID, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Bahan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Bahan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Bahan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($ID = null)
	{
		$data = $this->jenisbahanModel->find($id);
		if ($data) {
			$this->jenisbahanModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Bahan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('JenisBahan.info.not_found') . ' ID:' . $id);
		}
	}
}
