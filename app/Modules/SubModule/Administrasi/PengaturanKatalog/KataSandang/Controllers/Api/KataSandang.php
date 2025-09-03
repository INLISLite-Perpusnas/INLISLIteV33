<?php

namespace KataSandang\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class KataSandang extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $katasandangModel;
	protected $validation;

	function __construct()
	{
		$this->katasandangModel = new \KataSandang\Models\KataSandangModel();
		$this->validation = \Config\Services::validation();
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('kata_sandang as a')
			->select('a.ID as id, a.ID as action, a.Tag as tag, a.Name as name, a.JumlahKarakter as length, a.UpdateDate as update_date');

		if (!empty($slug)) {
			$builder->where('Tag', $slug);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('action', function ($row) {
			$edit = '<a data-toggle="modal" 
						data-target="#modal_edit" 
						href="javascript:void(0);"  
						data-href="' . base_url('api/master-kata-sandang/show/' . $row->id) . '"  
						data-id="' . $row->id . '" 
						class="btn btn-warning show-data" 
						title="Ubah Kata Sandang">
						<i class="pe-7s-note font-weight-bold"></i>
					</a>';
			$delete = '<a href="javascript:void(0);" 
						data-href="' . base_url('master-kata-sandang/delete/' . $row->id) . '" 
						class="btn btn-danger remove-data" 
						title="Hapus Profil">
						<i class="pe-7s-trash font-weight-bold"></i>
					</a>';
			return $edit . " " . $delete;
		})
			->toJson();

		return $dataTable;
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Kata Sandang', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$save_data = array(
					'Tag' => $this->request->getPost('tag'),
					'Name' => $this->request->getPost('name'),
					'JumlahKarakter' => $this->request->getPost('length'),
				);

				$saveData = $this->katasandangModel->insert($save_data);
				if ($saveData) {
					$response = [
						'error'    => false,
						'messages' => 'Kata Sandang berhasil disimpan'
					];
					return $this->respond($response);
				} else {
					$response = [
						'error'    => true,
						'messages' => 'Kata Sandang gagal disimpan'
					];
					return $this->respond($response);
				}
			} else {
				$message = $this->validation->listErrors();
				return $this->fail($message, 400);
			}
		}
	}

	public function show($id = null)
	{
		$data = $this->katasandangModel->find($id);
		if ($data) {
			return $this->respond($data, 200);
		} else {
			return $this->failNotFound('Not found ID: ' . $id);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('name', 'Nama Kata Sandang', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'Tag' => $this->request->getPost('tag'),
				'Name' => $this->request->getPost('name'),
				'JumlahKarakter' => $this->request->getPost('length'),
			);

			$updateDate = $this->katasandangModel->update($id, $update_data);
			if ($updateDate) {
				$response = [
					'error'    => false,
					'messages' => 'Kata Sandang berhasil disimpan'
				];
				return $this->respond($response);
			} else {
				$response = [
					'error'    => true,
					'messages' => 'Kata Sandang gagal disimpan'
				];
				return $this->respond($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}
}
