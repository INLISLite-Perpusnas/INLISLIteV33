<?php

namespace Pelanggaran\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Pelanggaran extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $pelanggaranModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->pelanggaranModel = new \Pelanggaran\Models\PelanggaranModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/pelanggaran/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('pelanggaran');
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('pelanggaran p')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('p.UpdateDate, p.JumlahDenda, p.JumlahSuspend, p.Paid')
			->select('jp.JenisPelanggaran')
			->select('jd.Name as JenisDenda')
			->select('col.NomorBarcode')
			->select('a.Title, a.PublishLocation, a.Publisher, a.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->select('loc.Name as LocationLibrary')
			->join('jenis_pelanggaran jp', 'jp.ID = p.JenisPelanggaran_id')
			->join('jenis_denda jd', 'jd.ID = p.JenisDenda_id')
			->join('collectionloans cl', 'cl.ID = p.CollectionLoan_id')
			->join('collectionloanitems cli', 'cli.CollectionLoan_id = cl.ID')
			->join('collections col', 'col.ID = cli.Collection_id')
			->join('catalogs a', 'a.ID = col.Catalog_id')
			->join('branchs b', 'b.ID = a.Branch_id', 'inner')
			->join('members m', 'm.ID = cli.member_id')
			->join('location_library loc', 'loc.ID = col.Location_Library_id');

		if (user()->category == 'admin') {
		} elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
			$npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
			$builder->where('b.NPP_Provinsi_id', $npp_provinsi_id);
		} elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
			$npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
			$builder->where('b.NPP_KabKota_id', $npp_kabkota_id);
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} else {
			$builder->where('a.Branch_id', branch_id());
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('CollectionLoan_id', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-id-card fa-3x text-secondary"></i>
						</div>
						<div class="widget-content-left text-secondary">
							<dl class="dl-horizontal mb-0">
								<dt class="font-weight-bold mb-0"><i class="fa fa-user text-secondary"></i> No. Anggota</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->MemberNo . '  <span class="text-secondary">(' . $row->Fullname . ')</span></a></dd>
								<dt class="font-weight-bold mb-0"><i class="fa fa-hashtag text-secondary"></i> No. Transaksi</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->CollectionLoan_id . '</a></dd>
							</dl>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('NomorBarcode', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . $row->NomorBarcode . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('Title', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-book fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading text-primary">' . $row->Publisher . '</div>
							<div class="widget-heading">' . $row->Title . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('JenisPelanggaran', function ($row) {
				$html  =  '<badge class="badge badge-warning badge-pill">' . $row->JenisPelanggaran . '</badge><br>';
				$html .=  '<b>' . $row->JenisDenda . '</b>';
				return $html;
			})
			->edit('JumlahDenda', function ($row) {
				$html  = formatRupiah($row->JumlahDenda);
				return $html;
			})
			->edit('JumlahSuspend', function ($row) {
				$html  =  formatRupiah($row->JumlahSuspend, '') . ' hari';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/sirkulasi-pelanggaran/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('pelanggaran/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->pelanggaranModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->pelanggaranModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$save_data_id = $this->pelanggaranModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Pelanggaran berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Pelanggaran berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Pelanggaran gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$update_data_id = $this->pelanggaranModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Pelanggaran berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Pelanggaran berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Pelanggaran gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->pelanggaranModel->find($id);
		if ($data) {
			$this->pelanggaranModel->delete($id);
			$response = [
				'error' => false,
				'message' => 'Pelanggaran berhasil dihapus',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Pelanggaran gagal dihapus. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function switch($id = null)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$update_data_id = $this->pelanggaranModel->update($id, array($field => ($value == 'true') ? 1 : 0));

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Upload Dokumen Keanggotaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Upload Dokumen Keanggotaan gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}
}
