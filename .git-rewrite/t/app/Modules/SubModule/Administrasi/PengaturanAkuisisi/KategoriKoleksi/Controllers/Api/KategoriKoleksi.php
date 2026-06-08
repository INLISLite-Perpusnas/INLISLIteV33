<?php

namespace KategoriKoleksi\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class KategoriKoleksi extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $kategorikoleksiModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['reference']);
		$this->kategorikoleksiModel = new \KategoriKoleksi\Models\KategoriKoleksiModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/kategorikoleksi/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{ 
		$db = db_connect();
		$builder = $db->table('collectioncategorys as a')
			->select('a.ID, a.ID as action, a.Name as Nama, a.UpdateDate, a.active, a.Branch_id')
			->select('0 as JumlahKoleksi');

		if (user()->category == 'admin') {
			$builder;
        } elseif (user()->category == 'sa_umum' ) {
            $builder->where('a.Branch_id', 0)->orWhere('a.Branch_id', branch_id());
        }  else {
            $builder->where('a.Branch_id', branch_id());
        }

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Nama', function ($row) {
				$html = '<b>' . $row->Nama . '</b>';
				return $html;
			})
			->edit('JumlahKoleksi', function ($row) {
				$db = db_connect();
				$builder = $db->table('collections')->where('Category_id', $row->ID);
				return $builder->countAllResults();
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html = '<badge class="badge badge-info">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-kategori-koleksi/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-kategori-koleksi/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-kategori-koleksi/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-kategori-koleksi/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				$html = $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;

				return $html;
			})
			->toJson();

		return $dataTable;
	}

	public function index()
	{ 
		$data = $this->kategorikoleksiModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->kategorikoleksiModel->find($id);
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
			'Name' => $this->request->getPost('Name'),
			'Branch_id' => branch_id()
		);

		$save_data_id = $this->kategorikoleksiModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Kategori Koleksi berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Kategori Koleksi berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Kategori Koleksi gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Name' => $this->request->getPost('Name'),
			'Name' => $this->request->getPost('Name'),
			'Branch_id' => branch_id()
		);

		$update_data_id = $this->kategorikoleksiModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Kategori Koleksi berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Kategori Koleksi berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Kategori Koleksi gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->kategorikoleksiModel->find($id);
		if ($data) {
			$this->kategorikoleksiModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Kategori Koleksi berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('KategoriKoleksi.info.not_found') . ' ID:' . $id);
		}
	}
}
