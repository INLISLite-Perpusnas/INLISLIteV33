<?php

namespace JenisBahanPustaka\Controllers;

class JenisBahanPustaka extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $wsModel;
	public $wsfModel;
	public $wsfItemModel;

	function __construct()
	{
		$this->wsModel = new \JenisBahanPustaka\Models\WsModel();
		$this->wsfModel = new \JenisBahanPustaka\Models\WsfModel();
		$this->wsfItemModel = new \JenisBahanPustaka\Models\WsfItemModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}
	public function index()
	{
		$this->data['title'] = 'Jenis Bahan Pustaka';
		echo view('JenisBahanPustaka\Views\list', $this->data);
	}

	public function create()
	{
		$this->data['title'] = 'Tambah Jenis Bahan Pustaka';
		$this->validation->setRule('code', 'Kode Jenis Bahan Pustaka', 'required');
		$this->validation->setRule('name', 'Nama Jenis Bahan Pustaka', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$save_data = [
				'CODE' => $this->request->getPost('code'),
				'Name' => $this->request->getPost('name'),
				'Format_id' => $this->request->getPost('format'),
				'KETERANGAN' => $this->request->getPost('description'),
				'NoUrut' => $this->request->getPost('sort'),
				'ISSERIAL' => $this->request->getPost('serial'),
				'ISKARTOGRAFI' => $this->request->getPost('kartografi'),
				'ISMUSIK' => $this->request->getPost('music'),
				'CreateBy' => user_id(),
				'CreateTerminal' => getClientIpAddress(),
			];

			$newJenisBahanPustakaId = $this->wsModel->insert($save_data);
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Worksheet_id' => $newJenisBahanPustakaId,
						'Field_id' => $value,
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}

				if (!empty($save_data)) {
					$this->wsfModel->insertBatch($save_data);
				}
			}

			if ($newJenisBahanPustakaId) {
				set_message('swal_icon', 'success');
				set_message('swal_title', 'Berhasil');
				set_message('swal_text', 'Jenis Bahan Pustaka berhasil disimpan');
				return redirect()->to('/master-jenis-bahan-pustaka');
			} else {
				set_message('message', 'Jenis Bahan Pustaka gagal disimpan');
				echo view('JenisBahanPustaka\Views\add', $this->data);
			}
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('JenisBahanPustaka\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Jenis Bahan Pustaka';
		$this->validation->setRule('name', 'Nama Jenis Bahan Pustaka', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = [
				'CODE' => $this->request->getPost('code'),
				'Name' => $this->request->getPost('name'),
				'Format_id' => $this->request->getPost('format'),
				'KETERANGAN' => $this->request->getPost('description'),
				'NoUrut' => $this->request->getPost('sort'),
				'ISSERIAL' => $this->request->getPost('serial'),
				'ISKARTOGRAFI' => $this->request->getPost('kartografi'),
				'ISMUSIK' => $this->request->getPost('musik'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => getClientIpAddress(),
			];
			$updateJenisBahanPustaka = $this->wsModel->update($id, $update_data);
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				$update_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Worksheet_id' => $id,
						'Field_id' => $value,
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}

				if (!empty($save_data)) {
					$this->wsfModel->insertBatch($save_data);
				}
			}

			if ($updateJenisBahanPustaka) {
				set_message('swal_icon', 'success');
				set_message('swal_title', 'Berhasil');
				set_message('swal_text', 'Jenis Bahan Pustaka berhasil disimpan');
				return redirect()->to('/master-jenis-bahan-pustaka');
			} else {
				set_message('message', 'Jenis Bahan Pustaka gagal disimpan');
				echo view('JenisBahanPustaka\Views\update', $this->data);
			}
		} else {
			$ws = $this->wsModel->find($id);
			$this->data['ws'] = $ws;

			$db = db_connect();
			$builder = $db->table('worksheetfields as wsf')
				->select('wsf.ID, wsf.Field_id')
				->select('f.Tag, f.Name')
				->join('fields as f', 'f.ID = wsf.Field_id')
				->where('wsf.Worksheet_id', $id)
				->orderBy('f.Tag', 'asc');
			$wsfs = $builder->get()->getResult();
			$this->data['wsfs'] = $wsfs;

			$excludes = get_object_array($wsfs, 'Tag');
			$builder = $db->table('fields as f')
				->select('f.ID, f.Tag, f.Name')
				->whereNotIn('f.Tag', $excludes)
				->orderBy('f.Tag', 'asc');
			$fields = $builder->get()->getResult();
			$this->data['fields'] = $fields;

			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('JenisBahanPustaka\Views\update', $this->data);
		}
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('swal_icon', 'error');
			set_message('swal_title', 'Error');
			set_message('swal_text', 'Sorry you have to provide parameter (id)');
			return redirect()->to('master-jenis-bahan-pustaka');
		}
		$wsDelete = $this->wsModel->delete($id);
		if ($wsDelete) {
			$this->wsfItemModel->where('WorksheetField_id', $id)->delete();
			set_message('swal_icon', 'success');
			set_message('swal_title', 'Berhasil');
			set_message('swal_text', 'Jenis Bahan Pustaka berhasil dihapus');
			return redirect()->to('master-jenis-bahan-pustaka');
		} else {
			set_message('swal_icon', 'warning');
			set_message('swal_title', 'Gagal');
			set_message('swal_text', 'Jenis Bahan Pustaka gagal dihapus');
			return redirect()->to('master-jenis-bahan-pustaka');
		}
	}
}
