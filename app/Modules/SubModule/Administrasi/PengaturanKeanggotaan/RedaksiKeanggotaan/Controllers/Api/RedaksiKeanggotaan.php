<?php

namespace RedaksiKeanggotaan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class RedaksiKeanggotaan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $RedaksiKeanggotaanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->RedaksiKeanggotaanModel = new \RedaksiKeanggotaan\Models\RedaksiKeanggotaanModel();

		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/redaksikeanggotaan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['url', 'text']);
		helper('reference');
		helper('RedaksiKeanggotaan');
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('memberrules as a')
			->select('a.ID as id, a.ID as action')
			->select('a.NameCategory as NameCategory, a.SortNum as SortNum');
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('master-redaksi-keanggotaan/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-redaksi-keanggotaan/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->fieldModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->fieldModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function delete($id = null)
	{
		$data = $this->fieldModel->find($id);
		if ($data) {
			$this->fieldModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Redaksi Keanggotaan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('RedaksiKeanggotaan.info.not_found') . ' ID:' . $id);
		}
	}
}
