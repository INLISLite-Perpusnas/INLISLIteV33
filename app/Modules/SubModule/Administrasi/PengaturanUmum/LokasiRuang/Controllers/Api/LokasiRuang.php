<?php

namespace LokasiRuang\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class LokasiRuang extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $lokasiruangModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->lokasiruangModel = new \LokasiRuang\Models\LokasiRuangModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/lokasiruang/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		

		helper('reference');
	}

	public function datatable($slug = null)
{
    $db = db_connect('data');
    $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
    $builder = $db->table('locations as a')
        ->select('a.ID, a.ID as action, a.Code, a.Name');

    $builder->select('b.Name as location_library_name, b.Code as location_library_code')
        ->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left');

    $builder->select('(SELECT COUNT(*) FROM collections WHERE Location_id = a.ID) as exemplar');

    if ($branch_id) {
        $builder->where('a.Branch_id', $branch_id);
    }

    $dataTable = DataTable::of($builder)
        ->addNumbering('no') // This adds the numbering column
        ->edit('Code', function ($row) {
            return '<b>' . $row->Code . '</b>';
        })
        ->edit('location_library_name', function ($row) {
            return $row->location_library_name ?? '';
        })
        ->edit('exemplar', function ($row) {
            return $row->exemplar ?? 0;
        })
        ->edit('action', function ($row) {
            $edit = '<a href="javascript:void(0);" data-href="' . base_url('api-lokasi-ruang/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
            $active = '<a href="' . base_url('lokasi-ruang/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
            $inactive = '<a href="' . base_url('lokasi-ruang/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
            $delete = '<a href="javascript:void(0);" data-href="' . base_url('lokasi-ruang/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
            return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
        })
        ->toJson();
    
    return $dataTable;
}

	public function index()
	{
		$data = $this->lokasiruangModel->findAll();
		return $this->respond($data, 200);
	}


	public function location($LocationLibrary_id = '')
	{
		$response = $this->lokasiruangModel
			->select('Code,Name,ID')
			->where('LocationLibrary_id', $LocationLibrary_id)
			->findAll();
		return $this->simpleResponse($response);
	}


	public function detail($id = null)
	{
		$data = $this->lokasiruangModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('name'),
			'LocationLibrary_id' => $this->request->getPost('LocationLibrary_id'),
			'Branch_id' => branch_id(),
		);
		// dd($save_data);

		$save_data_id = $this->lokasiruangModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Lokasi Ruang berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Lokasi Ruang berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Lokasi Ruang gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('Name'),
			'LocationLibrary_id' => $this->request->getPost('LocationLibrary_id'),
		);

		$update_data_id = $this->lokasiruangModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Lokasi Ruang berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Lokasi Ruang berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Lokasi Ruang gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->lokasiruangModel->find($id);
		if ($data) {
			$this->lokasiruangModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Lokasi Ruang berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('LokasiRuang.info.not_found') . ' ID:' . $id);
		}
	}

	public function check($code = null)
{
    if (!$code) {
        return $this->failValidationError('Kode tidak boleh kosong');
    }

    $db = db_connect('data');

    $builder = $db->table('locations as a')
        ->select('a.ID, a.Code, a.Name')
        ->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
        ->select('a.Branch_id, c.Name as Branch_name')
        ->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
        ->join('branchs as c', 'c.ID = a.Branch_id', 'left')
        ->where('a.Code', $code);

    $data = $builder->get()->getRow();

    if (!$data) {
        return $this->failNotFound('Data tidak ditemukan untuk kode: ' . $code);
    }

    return $this->respond($data);
}

}
