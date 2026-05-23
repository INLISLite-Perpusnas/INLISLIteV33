<?php

namespace JenisAnggota\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class JenisAnggota extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $jenisanggotaModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->jenisanggotaModel = new \JenisAnggota\Models\JenisAnggotaModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/jenisanggota/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('jenis_anggota as a')
			->select('a.id, a.id as action, a.jenisanggota')
			->select('a.MasaBerlakuAnggota, a.BiayaPendaftaran, a.MaxLoanDays,a.MaxPinjamKoleksi, a.DayPerpanjang, a.BiayaPerpanjangan, a.UploadDokumenKeanggotaanOnline, a.UpdateDate')
			->select('a.active');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('jenisanggota', function ($row) {
				$html  =  '<b>' . $row->jenisanggota . '</b>';
				return $html;
			})
			->edit('MasaBerlakuAnggota', function ($row) {
				$html  =  formatRupiah($row->MasaBerlakuAnggota, '') . ' hari';
				return $html;
			})
			->edit('MaxLoanDays', function ($row) {
				$html  =  formatRupiah($row->MaxLoanDays, '') . ' hari';
				return $html;
			})
			->edit('BiayaPendaftaran', function ($row) {
				$html  =  formatRupiah($row->BiayaPendaftaran);
				return $html;
			})
			->edit('BiayaPerpanjangan', function ($row) {
				$html  =  formatRupiah($row->BiayaPerpanjangan);
				return $html;
			})
			->edit('UploadDokumenKeanggotaanOnline', function ($row) {
				$checked = $row->UploadDokumenKeanggotaanOnline == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-jenis-anggota/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="UploadDokumenKeanggotaanOnline" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('DefaultLokasi', function ($row) {
				$editlokasi = '<a href="' . base_url('master-jenis-anggota/defaultlokasi/' . $row->id) . '"  data-placement="top" title="Ubah" class="btn btn-primary show-data">Default Lokasi</a><br>';

				return $editlokasi;
			})
			->edit('DefaultBahan', function ($row) {

				$editbahan = '<a href="' . base_url('master-jenis-anggota/defaultbahan/' . $row->id) . '"  data-placement="top" title="Ubah" class="btn btn-primary show-data">Default Bahan</a>';
				return $editbahan;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-jenis-anggota/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-jenis-anggota/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-jenis-anggota/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-jenis-anggota/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->jenisanggotaModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->jenisanggotaModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'jenisanggota' => $this->request->getPost('jenisanggota'),
			'MasaBerlakuAnggota' => $this->request->getPost('MasaBerlakuAnggota'),
			'BiayaPendaftaran' => $this->request->getPost('BiayaPendaftaran'),
			'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
			'BiayaPerpanjangan' => $this->request->getPost('BiayaPerpanjangan'),
			'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
			'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
			'UploadDokumenKeanggotaanOnline' => $this->request->getPost('UploadDokumenKeanggotaanOnline'),
		);

		$save_data_id = $this->jenisanggotaModel->insert($save_data);
		if ($save_data_id) {
			
			$response = [
				'error' => false,
				'message' => 'Jenis Anggota berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Anggota gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'jenisanggota' => $this->request->getPost('jenisanggota'),
			'MasaBerlakuAnggota' => $this->request->getPost('MasaBerlakuAnggota'),
			'BiayaPendaftaran' => $this->request->getPost('BiayaPendaftaran'),
			'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
			'BiayaPerpanjangan' => $this->request->getPost('BiayaPerpanjangan'),
			'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
			'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
			'UploadDokumenKeanggotaanOnline' => $this->request->getPost('UploadDokumenKeanggotaanOnline'),
		);

		$update_data_id = $this->jenisanggotaModel->update($id, $update_data);
		if ($update_data_id) {
			
			$response = [
				'error' => false,
				'message' => 'Jenis Anggota berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Anggota gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->jenisanggotaModel->find($id);
		if ($data) {
			$this->jenisanggotaModel->delete($id);
			$response = [
				'error' => false,
				'message' => 'Jenis Anggota berhasil dihapus',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Jenis Anggota gagal dihapus. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function switch($id = null)
	{
		$field = $this->request->getPost('field');
		$value = $this->request->getPost('value');

		$update_data_id = $this->jenisanggotaModel->update($id, array($field => ($value == 'true') ? 1 : 0));

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
