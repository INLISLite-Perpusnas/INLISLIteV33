<?php

namespace MasterJurusan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class MasterJurusan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jurusanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;
	protected $db;


	function __construct()
	{
		$this->jurusanModel = new \MasterJurusan\Models\MasterJurusanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->db=db_connect();
		$this->modulePath = ROOTPATH . 'public/uploads/jurusan/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		helper('reference');
		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('master_jurusan as a')
			->select('a.id, a.id as action,a.Nama, a.UpdateDate')
			->select('a.active, b.Nama as Fakultas')
			->join('master_fakultas as b', 'b.id = a.id_fakultas', 'left');

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
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/jurusan/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-jurusan/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-jurusan/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jurusan/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jurusanModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jurusanModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'Nama' => $this->request->getPost('Nama'),
			'id_fakultas' => $this->request->getPost('id_fakultas'),
			'Branch_id' => branch_id()
		);

		$save_data_id = $this->jurusanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Master Jurusan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Master Jurusan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Master Jurusan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}
    public function getjurusan($fakultas_id = null)
    {
        try {
            if (empty($fakultas_id)) {
                return $this->fail('Fakultas ID is required', 400);
            }

            // Validasi fakultas_id exists
            $fakultasExists = $this->db->table('master_fakultas')
                ->where('id', $fakultas_id)
                ->countAllResults();

            if ($fakultasExists == 0) {
                return $this->failNotFound('Fakultas tidak ditemukan');
            }

            $jurusan = $this->db->table('master_jurusan')
                ->select('id, id_fakultas, Nama')
                ->where('id_fakultas', $fakultas_id)
                ->orderBy('Nama', 'ASC')
                ->get()
                ->getResult();

            return $this->respond([
                'success' => true,
                'message' => 'Data jurusan berhasil diambil',
                'data' => $jurusan,
                'fakultas_id' => $fakultas_id
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Gagal mengambil data jurusan: ' . $e->getMessage());
        }
    }
	public function edit($id = null)
	{
		$update_data = [
			'Nama' => $this->request->getPost('Nama'),
			'id_fakultas' => $this->request->getPost('id_fakultas'),
			'Branch_id' => branch_id()
		];

		$update_data_id = $this->jurusanModel->update($id, $update_data);
		$response = [
			'error' => $update_data_id ? false : true,
			'message' => $update_data_id ? 'Master Jurusan berhasil disimpan' : 'Master Jurusan gagal disimpan. Silakan coba lagi'
		];

		$this->session->setFlashdata('toastr_msg', $response['message']);
		$this->session->setFlashdata('toastr_type', $response['error'] ? 'error' : 'success');

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jurusanModel->find($id);
		if ($data) {
			$this->jurusanModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Master Jurusan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('MasterJurusan.info.not_found') . ' ID:' . $id);
		}
	}
}
