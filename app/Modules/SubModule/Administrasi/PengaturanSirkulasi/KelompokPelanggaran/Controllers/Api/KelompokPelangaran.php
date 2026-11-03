<?php

namespace KelompokPelanggaran\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class KelompokPelanggaran extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $kelompokPelanggaranModel;
	protected $validation;
	protected $session;

	function __construct()
	{
		  $this->kelompokPelanggaranModel = new \KelompokPelanggaran\Models\KelompokPelanggaranModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		helper('reference');
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$branchId=user()->branch_id;
		$builder = $db->table('kelompok_pelanggaran as a')
		  ->select('a.id, a.id as action, a.Name as Name, a.Jumlah as Jumlah,  a.Warna as Warna,  a.Branch_id, a.CreateDate as CreateDate, a.UpdateDate as UpdateDate, a.active as active')
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
				$html  =  '<badge class="badge badge-info">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) use ($branchId) {
			
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-kelompok-umur/detail/' . $row->id) . '" data-toggle="modal" data-target="#modal_update" data-placement="top" title="Ubah" aria-label="Ubah" class="ina-action show-data"><i class="fas fa-pen" aria-hidden="true"></i></a>';
				$active = '<a href="' . base_url('master-kelompok-umur/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Aktifkan" aria-label="Aktifkan" class="ina-action active-data"><i class="fas fa-eye" aria-hidden="true"></i></a>';
				$inactive = '<a href="' . base_url('master-kelompok-umur/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Nonaktifkan" aria-label="Nonaktifkan" class="ina-action draft-data"><i class="fas fa-eye-slash" aria-hidden="true"></i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-kelompok-umur/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus" aria-label="Hapus" class="ina-action ina-action-danger remove-data"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			
		})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->kelompokPelanggaranModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->kelompokPelanggaranModel->find($id);
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

		$save_data_id = $this->kelompokPelanggaranModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Master Kelas berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Master Kelas berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Pendidikan gagal disimpan. Silakan coba lagi',
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

		$update_data_id = $this->KelompokPelanggaranModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Jenis Pendidikan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Jenis Master kelompok umur berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Master kelompok umur gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->KelompokPelanggaranModel>find($id);
		if ($data) {
			$this->KelompokPelanggaranModel->delete($id);
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
