<?php

namespace MitraPerpustakaan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class MitraPerpustakaan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $mitraperpustakaanModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->mitraperpustakaanModel = new \MitraPerpustakaan\Models\MitraPerpustakaanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/master-mitra-perpustakaan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		helper('region');
	}

	public function datatable($slug = null)
	{
		$NPP_Jenis = $this->request->getGet('NPP_Jenis') ?? '';
		$NPP_Provinsi_id = $this->request->getGet('NPP_Provinsi_id') ?? '';
		$NPP_KabKota_id = $this->request->getGet('NPP_KabKota_id') ?? '';
		$NPP_Kecamatan_id = $this->request->getGet('NPP_Kecamatan_id') ?? '';

		$db = db_connect('data');
		$builder = $db->table('branchs as a')
			->select('a.ID, a.Code, a.Name, a.Alias, "" as Alamat, "" as Jenis')
			->select('a.NPP_id, a.NPP_Jenis, a.NPP_Provinsi_id, a.NPP_KabKota_id, a.NPP_Kecamatan_id, a.NPP_Kelurahan_id')
			->select('a.sort, a.active, a.ID as action');

		if (!empty($NPP_Jenis)) {
			$builder->where('NPP_Jenis', $NPP_Jenis);
		}
		if (!empty($NPP_Provinsi_id)) {
			$NPP_Provinsi_id =  str_replace('.', '', $NPP_Provinsi_id);
			$builder->where('NPP_Provinsi_id', $NPP_Provinsi_id);
		}
		if (!empty($NPP_KabKota_id)) {
			$NPP_KabKota_id =  str_replace('.', '', $NPP_KabKota_id);
			$builder->where('NPP_KabKota_id', $NPP_KabKota_id);
		}
		if (!empty($NPP_Kecamatan_id)) {
			$NPP_Kecamatan_id =  str_replace('.', '', $NPP_Kecamatan_id);
			$builder->where('NPP_Kecamatan_id', $NPP_Kecamatan_id);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Alias', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->Alias . '</badge>';
				return $html;
			})
			->edit('Name', function ($row) {
				$html  =  'NPP: <b>' . $row->Code . '</b><br>';
				$html .=  '<b>' . $row->Name . '</b><br>';
				return $html;
			})
			->edit('Jenis', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . $row->NPP_Jenis . '</badge>';
				return $html;
			})
			->edit('Alamat', function ($row) {
				$html = '';
				if (!empty(npp_region($row->NPP_Provinsi_id, 1))) {
					$html .=  'Prov: <b>' . npp_region($row->NPP_Provinsi_id, 1)->name . '</b><br>';
				}
				if (!empty(npp_region($row->NPP_KabKota_id, 2))) {
					$html .=  'Kota/Kab: <b>' . npp_region($row->NPP_KabKota_id, 2)->name . '</b><br>';
				}
				if (!empty(npp_region($row->NPP_Kecamatan_id, 2))) {
					$html .=  'Kec: <b>' . npp_region($row->NPP_Kecamatan_id, 3)->name . '</b><br>';
				}
				return $html;
			})

			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api-mitra-perpustakaan/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('mitra-perpustakaan/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('mitra-perpustakaan/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('mitra-perpustakaan/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->mitraperpustakaanModel->findAll();
		return $this->respond($data, 200);
	}

	public function select2()
	{
		$search = $this->request->getGet('search') ?? '';
		$page = $this->request->getGet('page') ?? '1';

		$db = db_connect('data');
		$builder_count = $db->table('branchs as a')
			->select('count(*) as total')
			->like('a.Name', $search);
		$total = $builder_count->get()->getRow()->total ?? 0;

		$per_page = 10;
		$offset = $page * $per_page;
		$limit = $offset - $per_page;

		$db = db_connect('data');
		$builder = $db->table('branchs as a')
			->select('a.ID as id, a.Name as text')
			->like('a.Name', $search)
			->limit($per_page, $limit);

		$items = $builder->get()->getResult();
		$response = array(
			'items' => $items,
			'total' => $total,
		);

		return $this->simpleResponse($response);
	}

	public function location_library($branch_id = '')
	{
		$response = $this->mitraperpustakaanModel
			->select('Code,Name')
			->where('branch_id', $branch_id)
			->findAll();
		return $this->simpleResponse($response);
	}


	public function detail($id = null)
	{
		$data = $this->mitraperpustakaanModel->find($id);
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
			'Address' => $this->request->getPost('Address'),
		);

		$save_data_id = $this->mitraperpustakaanModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Mitra Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Mitra Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Mitra Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'Code' => $this->request->getPost('Code'),
			'Name' => $this->request->getPost('Name'),
			'Address' => $this->request->getPost('Address'),
		);

		$update_data_id = $this->mitraperpustakaanModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Mitra Perpustakaan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Mitra Perpustakaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Mitra Perpustakaan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->mitraperpustakaanModel->find($id);
		if ($data) {
			$this->mitraperpustakaanModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Mitra Perpustakaan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('MitraPerpustakaan.info.not_found') . ' ID:' . $id);
		}
	}

	//custom
	public function get_branchs($value = '32', $field = 'NPP_Provinsi_id')
	{
		$response = $this->mitraperpustakaanModel
			->select('ID as code, concat(Code, " - ", Name) as name')
			->where($field, $value)
			->where('Code <>', '')
			->orderBy('Name', 'ASC')
			->findAll();
		return $this->simpleResponse($response);
	}

	public function check($code = null)
	{
		$db = db_connect('data');
		$builder = $db->table('branchs as a')
			->select('a.ID, a.Code, a.Name')
			->where('a.Alias', $code);

		$data = $builder->get()->getRow();

		return $this->simpleResponse($data);
	}
}
