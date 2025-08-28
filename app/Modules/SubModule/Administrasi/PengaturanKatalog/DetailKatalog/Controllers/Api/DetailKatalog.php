<?php

namespace DetailKatalog\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class DetailKatalog extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $detailKatalogModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['reference']);
		$this->detailKatalogModel = new \DetailKatalog\Models\DetailKatalogModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/kategorikoleksi/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable()
	{
		$db = db_connect('data');
		$builder = $db->table('settingcatalogdetail as a')
			->select('a.ID, a.ID as action, a.active, a.Branch_id')
			->select('b.Tag, b.Name as Name')
			->join('fields as b', 'b.ID = a.Field_id');

		

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Nama', function ($row) {
				$html = '<b>' . $row->Nama . '</b>';
				return $html;
			})
			->edit('JumlahKoleksi', function ($row) {
				$db = db_connect('data');
				$builder = $db->table('collections')->where('Media_id', $row->ID);
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
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-detail-katalog/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				$html = '' . $delete . '';
				return $html;
			})
			->toJson();

		return $dataTable;
	}

	public function index()
	{
		$data = $this->detailKatalogModel->findAll();
		return $this->respond($data, 200);
	}

	public function create()
	{
		$save_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('Name'),
			'Branch_id' => branch_id()
		);

		$save_data_id = $this->detailKatalogModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Detail Katalog berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Detail Katalog berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Detail Katalog gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->detailKatalogModel->find($id);
		if ($data) {
			$this->detailKatalogModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Detail Katalog berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('DetailKatalog.info.not_found') . ' ID:' . $id);
		}
	}
}
