<?php

namespace PeraturanPeminjamanHari\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class PeraturanPeminjamanHari extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $peraturanpeminjamanhariModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['reference']);
		// $this->peraturanpeminjamanhariModel = new \PeraturanPeminjamanHari\Models\PeraturanPeminjamanHariModel();
		$this->peraturanpeminjamanhariModel = new \PeraturanPeminjamanHari\Models\PeraturanPeminjamanHariModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/peraturanpeminjamanhari/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('peraturan_peminjaman_hari as a')
			->select('a.ID, a.ID as action, a.DayIndex as Hari, a.MaxPinjamKoleksi,a.MaxLoanDays,a.DendaTenorJumlah,a.DaySuspend,a.DayPerpanjang,a.CountPerpanjang, a.active');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')

			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/peraturan-peminjaman-hari/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-peraturan-peminjaman-hari/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-peraturan-peminjaman-hari/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('peraturan-peminjaman-hari/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->peraturanpeminjamanhariModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		// $data = $this->peraturanpeminjamanhariModel->find($id);
		$peraturanTable = 'peraturan_peminjaman_hari';
		$categoryTable = 'collectioncategorysloanhari';

		// Perform the join operation
		$db = db_connect('data');
		$query = $db->table($peraturanTable)
			->select("$peraturanTable.ID, $peraturanTable.Dayindex, $peraturanTable.MaxLoanDays, $categoryTable.DataID as category_id")
			->join($categoryTable, "$peraturanTable.ID = $categoryTable.peminjaman_hari_id", 'left');

		// Get the result
		$result = $query->get()->getResult();
		dd($result);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = [
			'DayIndex' => $this->request->getPost('DayIndex'),
			'CreateTerminal' => $this->request->getPost('CreateTerminal'),
			'UpdateTerminal' => $this->request->getPost('UpdateTerminal'),
			'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
			'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
			'DendaType' => $this->request->getPost('DendaType'),
			'DendaTenorJumlah' => $this->request->getPost('DendaTenorJumlah'),
			'DendaTenorSatuan' => $this->request->getPost('DendaTenorSatuan'),
			'DendaPerTenor' => $this->request->getPost('DendaPerTenor'),
			'DendaTenorMultiply' => $this->request->getPost('DendaTenorMultiply'),
			'SuspendMember' => $this->request->getPost('SuspendMember'),
			'WarningLoanDueDay' => $this->request->getPost('WarningLoanDueDay'),
			'SuspendType' => $this->request->getPost('SuspendType'),
			'SuspendTenorJumlah' => $this->request->getPost('SuspendTenorJumlah'),
			'SuspendTenorSatuan' => $this->request->getPost('SuspendTenorSatuan'),
			'DaySuspend' => $this->request->getPost('DaySuspend'),
			'SuspendTenorMultiply' => $this->request->getPost('SuspendTenorMultiply'),
			'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
			'CountPerpanjang' => $this->request->getPost('CountPerpanjang'),
			'Branch_id' => branch_id()
		];

		if ($save_data) {
			$save_data_id = $this->peraturanpeminjamanhariModel->insert($save_data);
			$selectedCategories = $this->request->getPost('Category_id');
			$dataToInsert = [];

			foreach ($selectedCategories as $categoryId) {
				$dataToInsert[] = [
					'Category_id' => $categoryId,
					'peminjaman_hari_id' => $save_data_id,
					'Branch_id' => branch_id()
				];
			}
			$db = db_connect('data');
			// Insert the data as a batch
			$db->table('collectioncategorysloanhari')->insertBatch($dataToInsert);
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
		// $slug = url_title($this->request->getPost('name'), '-', TRUE);
		$update_data = array(
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

		$update_data_id = $this->peraturanpeminjamanhariModel->update($ID, $update_data);
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
		$data = $this->peraturanpeminjamanhariModel->find($id);
		if ($data) {
			$this->peraturanpeminjamanhariModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Jenis Bahan berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('PeraturanPeminjamanHari.info.not_found') . ' ID:' . $id);
		}
	}
}
