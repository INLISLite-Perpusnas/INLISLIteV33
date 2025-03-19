<?php

namespace Referensi\Controllers;

class Referensi extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $refModel;
	public $refItemModel;

	function __construct()
	{
		$this->refModel = new \Referensi\Models\RefModel();
		$this->refItemModel = new \Referensi\Models\RefItemModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}
	public function index()
	{
		$this->data['title'] = 'Referensi';
		echo view('Referensi\Views\list', $this->data);
	}

	public function create()
	{
		$this->data['title'] = 'Tambah Referensi';
		$this->validation->setRule('name', 'Nama Referensi', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// Ref
			$save_data = [
				'Name' => $this->request->getPost('name'),
				'Format_id' => $this->request->getPost('format'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => getClientIpAddress(),
			];

			$newReferensiId = $this->refModel->insert($save_data);

			// Item
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Refference_id' => $newReferensiId,
						'Code' => $this->request->getPost('code0')[$value],
						'Name' => $this->request->getPost('name0')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}

				if (!empty($save_data)) {
					$this->refItemModel->insertBatch($save_data);
				}
			}

			if ($newReferensiId) {
				set_message('toastr_msg', 'Referensi berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-referensi');
			} else {
				set_message('message', 'Referensi gagal disimpan');
				echo view('Referensi\Views\add', $this->data);
			}
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Referensi\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Referensi';
		$this->validation->setRule('name', 'Nama Referensi', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$terminal = getClientIpAddress();
			// Ref
			$update_data = [
				'Name' => $this->request->getPost('name'),
				'Format_id' => $this->request->getPost('format'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => $terminal,
			];

			$updateReferensi = $this->refModel->update($id, $update_data);

			// Item
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				$update_data = array();
				foreach ($index_arr as $index => $value) {
					$existing = $this->refItemModel->find($value);
					if (!empty($existing)) {
						$update_data[] = [
							'ID' => $value,
							'Refference_id' => $id,
							'Code' => $this->request->getPost('code0')[$value],
							'Name' => $this->request->getPost('name0')[$value],
							'UpdateBy' => user_id(),
							'UpdateTerminal' => $terminal,
						];
					} else {
						$save_data[] = [
							'Refference_id' => $id,
							'Code' => $this->request->getPost('code0')[$value],
							'Name' => $this->request->getPost('name0')[$value],
							'CreateBy' => user_id(),
							'CreateTerminal' => $terminal,
						];
					}
				}

				if (!empty($save_data)) {
					$this->refItemModel->insertBatch($save_data);
				}

				if (!empty($update_data)) {
					$this->refItemModel->updateBatch($update_data, 'ID');
				}
			}

			if ($updateReferensi) {
				set_message('toastr_msg', 'Referensi berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-referensi');
			} else {
				set_message('message', 'Referensi gagal disimpan');
				echo view('Referensi\Views\update', $this->data);
			}
		} else {
			$ref = $this->refModel->find($id);
			$refItems = get_table('refferenceitems', 'ID, Refference_id, Code, Name', 'Refference_id=' . $ref->ID, 'data');
			$this->data['ref'] = $ref;
			$this->data['refItems'] = $refItems;
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Referensi\Views\update', $this->data);
		}
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('referensi');
		}
		$refDelete = $this->refModel->delete($id);
		if ($refDelete) {
			$this->refItemModel->where('Refference_item', $id)->delete();
			set_message('toastr_msg', 'Referensi berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('referensi');
		} else {
			set_message('toastr_msg', 'Referensi gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Referensi gagal dihapus');
			return redirect()->to('referensi');
		}
	}
}
