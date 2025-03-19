<?php

namespace FormatKartu\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class FormatKartu extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $formatkartuModel;
	protected $validation;

	function __construct()
	{
		$this->formatkartuModel = new \FormatKartu\Models\FormatKartuModel();
		$this->validation = \Config\Services::validation();
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('cardformats as a')
			->select('a.ID as id, a.ID as action, a.Name as name, a.Width as width, a.Height as height, a.FontName as font_name, a.FontSize as font_size, a.UpdateDate as update_date')
			->select('a.ID as dimension, a.ID as font');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('dimension', function ($row) {
				$html = $row->width . ' x ' . $row->height;
				return $html;
			})
			->edit('font', function ($row) {
				$html = $row->font_name . ' : ' . $row->font_size;
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/master-format-kartu/show/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah FormatKartu" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-format-kartu/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus  Profil" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . " " . $delete;
			})
			->toJson();

		return $dataTable;
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Format Kartu', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$save_data = array(
					'Name' => $this->request->getPost('name'),
					'Width' => $this->request->getPost('width'),
					'Height' => $this->request->getPost('height'),
					'FontName' => $this->request->getPost('font_name'),
					'FontSize' => $this->request->getPost('font_size'),
					'FormatTeks' => $this->request->getPost('format_text'),
					'FormatTeksNoAuthor' => $this->request->getPost('format_text_no_author'),
				);

				$saveData = $this->formatkartuModel->insert($save_data);
				if ($saveData) {
					$response = [
						'error'    => false,
						'messages' => 'Format Kartu berhasil disimpan'
					];
					return $this->respond($response);
				} else {
					$response = [
						'error'    => true,
						'messages' => 'Format Kartu gagal disimpan'
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
		$data = $this->formatkartuModel->find($id);
		if ($data) {
			return $this->respond($data, 200);
		} else {
			return $this->failNotFound('Not found ID: ' . $id);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('name', 'Nama Format Kartu', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'Name' => $this->request->getPost('name'),
				'Width' => $this->request->getPost('width'),
				'Height' => $this->request->getPost('height'),
				'FontName' => $this->request->getPost('font_name'),
				'FontSize' => $this->request->getPost('font_size'),
				'FormatTeks' => $this->request->getPost('format_text'),
				'FormatTeksNoAuthor' => $this->request->getPost('format_text_no_author'),
			);

			$updateDate = $this->formatkartuModel->update($id, $update_data);
			if ($updateDate) {
				$response = [
					'error'    => false,
					'messages' => 'Format Kartu berhasil disimpan'
				];
				return $this->respond($response);
			} else {
				$response = [
					'error'    => true,
					'messages' => 'Format Kartu gagal disimpan'
				];
				return $this->respond($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}
}
