<?php

namespace PenyediaKatalog\Controllers;

class PenyediaKatalog extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $libModel;
	public $libItemModel;

	function __construct()
	{
		$this->libModel = new \PenyediaKatalog\Models\LibModel();
		$this->libItemModel = new \PenyediaKatalog\Models\LibItemModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}
	public function index()
	{
		$this->data['title'] = 'Penyedia Katalog';
		echo view('PenyediaKatalog\Views\list', $this->data);
	}

	public function create()
	{
		$this->data['title'] = 'Tambah Penyedia Katalog';
		$this->validation->setRule('name', 'Nama Penyedia Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// Ref
			$save_data = [
				'NAME' => $this->request->getPost('alias'),
				'FULLNAME' => $this->request->getPost('name'),
				'URL' => $this->request->getPost('link'),
				'PORT' => $this->request->getPost('port'),
				'DATABASENAME' => $this->request->getPost('database'),
				'RECORDSYNTAX' => $this->request->getPost('syntax'),
				'PROTOCOL' => $this->request->getPost('protocol'),
				'CreateBy' => user_id(),
				'CreateTerminal' => getClientIpAddress(),
			];

			$newPenyediaKatalogId = $this->libModel->insert($save_data);

			// Item
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'LIBRARYID' => $newPenyediaKatalogId,
						'CRITERIANAME' => $this->request->getPost('name0')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}

				if (!empty($save_data)) {
					$this->libItemModel->insertBatch($save_data);
				}
			}

			if ($newPenyediaKatalogId) {
				set_message('toastr_msg', 'Penyedia Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-penyedia-katalog');
			} else {
				set_message('message', 'Penyedia Katalog gagal disimpan');
				echo view('PenyediaKatalog\Views\add', $this->data);
			}
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('PenyediaKatalog\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Penyedia Katalog';
		$this->validation->setRule('name', 'Nama Penyedia Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$terminal = getClientIpAddress();
			// Ref
			$update_data = [
				'NAME' => $this->request->getPost('alias'),
				'FULLNAME' => $this->request->getPost('name'),
				'URL' => $this->request->getPost('link'),
				'PORT' => $this->request->getPost('port'),
				'DATABASENAME' => $this->request->getPost('database'),
				'RECORDSYNTAX' => $this->request->getPost('syntax'),
				'PROTOCOL' => $this->request->getPost('protocol'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => $terminal,
			];

			$updatePenyediaKatalog = $this->libModel->update($id, $update_data);

			// Item
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				$update_data = array();
				foreach ($index_arr as $index => $value) {
					$existing = $this->libItemModel->find($value);
					if (!empty($existing)) {
						$update_data[] = [
							'ID' => $value,
							'LIBRARYID' => $id,
							'CRITERIANAME' => $this->request->getPost('name0')[$value],
							'UpdateBy' => user_id(),
							'UpdateTerminal' => $terminal,
						];
					} else {
						$save_data[] = [
							'LIBRARYID' => $id,
							'CRITERIANAME' => $this->request->getPost('name0')[$value],
							'CreateBy' => user_id(),
							'CreateTerminal' => getClientIpAddress(),
						];
					}
				}

				if (!empty($save_data)) {
					$this->libItemModel->insertBatch($save_data);
				}

				if (!empty($update_data)) {
					$this->libItemModel->updateBatch($update_data, 'ID');
				}
			}

			if ($updatePenyediaKatalog) {
				set_message('toastr_msg', 'Penyedia Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-penyedia-katalog');
			} else {
				set_message('message', 'Penyedia Katalog gagal disimpan');
				echo view('PenyediaKatalog\Views\update', $this->data);
			}
		} else {
			$lib = $this->libModel->find($id);
			$libItems = get_table('librarysearchcriteria', 'ID, LIBRARYID, CRITERIANAME', 'LIBRARYID=' . $lib->ID, 'data');
			$this->data['lib'] = $lib;
			$this->data['libItems'] = $libItems;
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('PenyediaKatalog\Views\update', $this->data);
		}
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('master-penyedia-katalog');
		}
		$refDelete = $this->libModel->delete($id);
		if ($refDelete) {
			$this->libItemModel->where('LIBRARYID', $id)->delete();
			set_message('toastr_msg', 'Penyedia Katalog berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('master-penyedia-katalog');
		} else {
			set_message('toastr_msg', 'Penyedia Katalog gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Penyedia Katalog gagal dihapus');
			return redirect()->to('master-penyedia-katalog');
		}
	}
}
