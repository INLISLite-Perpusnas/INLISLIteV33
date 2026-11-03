<?php

namespace NamaSumberPerolehan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;

class NamaSumberPerolehan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $namaSumberPerolehanModel;

	function __construct()
	{
		$this->namaSumberPerolehanModel = new \NamaSumberPerolehan\Models\NamaSumberPerolehanModel();
	}

	/** Header X-Requested-With tidak dipercaya untuk izin (lihat is_allowed). */
	private function allowed(string $action): bool
	{
		return is_allowed('master-nama-sumber-perolehan/' . $action, null, false);
	}

	private function denied()
	{
		return $this->respond(['error' => true, 'message' => 'Maaf, Anda tidak memiliki akses', 'csrf' => csrf_hash()], 403);
	}

	public function datatable()
	{
		if (!$this->allowed('index')) return $this->denied();

		$builder = db_connect()->table('partners a')
			->select('a.ID, a.ID as action, a.Name as Nama, a.Address as Alamat, a.Phone as Telepon, a.ID as Eksemplar')
			->orderBy('a.Name', 'ASC');

		$canEdit   = $this->allowed('edit');
		$canDelete = $this->allowed('delete');

		return DataTable::of($builder)
			->addNumbering('no')
			->edit('Nama', fn($row) => esc($row->Nama))
			->edit('Alamat', fn($row) => esc($row->Alamat))
			->edit('Telepon', fn($row) => esc($row->Telepon))
			// Jumlah eksemplar per halaman saja (maks. sebanyak baris yang tampil), bukan subquery
			->edit('Eksemplar', fn($row) => number_format(
				db_connect()->table('collections')->where('Partner_id', $row->ID)->countAllResults(), 0, ',', '.'
			))
			// Kolom aksi: tombol ikon Ubah & Hapus (gaya tabel INA), tampil sesuai hak akses
			->edit('action', function ($row) use ($canEdit, $canDelete) {
				$html = '';
				if ($canEdit) {
					$html .= '<a href="javascript:void(0);" data-href="' . base_url('api/master-nama-sumber-perolehan/detail/' . $row->ID) . '" class="ina-action show-data" title="Ubah" aria-label="Ubah ' . esc($row->Nama, 'attr') . '"><i class="fas fa-pen" aria-hidden="true"></i></a>';
				}
				if ($canDelete) {
					$html .= '<a href="javascript:void(0);" data-href="' . base_url('master-nama-sumber-perolehan/delete/' . $row->ID) . '" data-name="' . esc($row->Nama, 'attr') . '" class="ina-action ina-action-danger remove-data" title="Hapus" aria-label="Hapus ' . esc($row->Nama, 'attr') . '"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
				}
				return $html !== '' ? '<div class="ina-actions">' . $html . '</div>' : '<span class="text-muted">-</span>';
			})
			->toJson();
	}

	public function detail($id = null)
	{
		if (!$this->allowed('edit')) return $this->denied();
		$data = $this->namaSumberPerolehanModel->find((int) $id);
		return $data ? $this->respond($data) : $this->failNotFound('Data tidak ditemukan');
	}

	public function create()
	{
		if (!$this->allowed('create')) return $this->denied();
		return $this->save(null);
	}

	public function edit($id = null)
	{
		if (!$this->allowed('edit')) return $this->denied();
		if (!$this->namaSumberPerolehanModel->find((int) $id)) {
			return $this->respond(['error' => true, 'message' => 'Data tidak ditemukan', 'csrf' => csrf_hash()], 404);
		}
		return $this->save((int) $id);
	}

	private function save(?int $id)
	{
		$data = [
			'Name'    => trim((string) $this->request->getPost('Name')),
			'Address' => trim((string) $this->request->getPost('Address')),
			'Phone'   => trim((string) $this->request->getPost('Phone')),
		];

		$validation = \Config\Services::validation(null, false);
		$validation->setRules([
			'Name'    => ['label' => 'Nama', 'rules' => 'required|max_length[255]', 'errors' => ['required' => 'Nama wajib diisi.', 'max_length' => 'Nama maksimal 255 karakter.']],
			'Address' => ['label' => 'Alamat', 'rules' => 'permit_empty|max_length[255]', 'errors' => ['max_length' => 'Alamat maksimal 255 karakter.']],
			'Phone'   => ['label' => 'No. Telepon', 'rules' => 'permit_empty|max_length[100]', 'errors' => ['max_length' => 'No. Telepon maksimal 100 karakter.']],
		]);
		if (!$validation->run($data)) {
			return $this->respond(['error' => true, 'message' => implode('<br>', array_map('esc', $validation->getErrors())), 'csrf' => csrf_hash()], 400);
		}

		$terminal = $this->request->getIPAddress();
		if ($id === null) {
			$ok = $this->namaSumberPerolehanModel->insert($data + [
				'Branch_id' => branch_id(), 'CreateBy' => login_id(), 'CreateTerminal' => $terminal,
				'UpdateBy' => login_id(), 'UpdateTerminal' => $terminal,
			]);
		} else {
			$ok = $this->namaSumberPerolehanModel->update($id, $data + ['UpdateBy' => login_id(), 'UpdateTerminal' => $terminal]);
		}

		if (!$ok) {
			return $this->respond(['error' => true, 'message' => 'Nama Sumber Perolehan gagal disimpan. Silakan coba lagi', 'csrf' => csrf_hash()], 500);
		}
		return $this->respond(['error' => false, 'message' => 'Nama Sumber Perolehan berhasil disimpan', 'csrf' => csrf_hash()]);
	}
}
