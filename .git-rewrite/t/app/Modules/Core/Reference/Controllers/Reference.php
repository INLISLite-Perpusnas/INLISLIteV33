<?php

namespace Reference\Controllers;

class Reference extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $referenceModel;
	public $menuModel;

	function __construct()
	{
		$this->referenceModel = new \Reference\Models\ReferenceModel();
		$this->menuModel = new \Menu\Models\MenuModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();

		helper('reference');
	}
	public function index()
	{
		$reference = $this->referenceModel->orderBy('id', 'desc')->first();
		$menu_id = $this->request->getGet('menu_id') ?? $reference->menu_id;


		$query = $this->referenceModel
			->select('c_references.*')
			->select('c_menus.name as category, c_menus.controller as code')
			->join('c_menus', 'c_menus.id = c_references.menu_id', 'left');

		if (!empty($menu_id)) {
			$query->where('menu_id', $menu_id);
		}
		$references = $query->findAll();

		$this->data['title'] = 'Referensi';
		$this->data['menu_id'] = $menu_id;
		$this->data['references'] = $references;
		echo view('Reference\Views\list', $this->data);
	}

	public function delete(int $id = 0)
	{


		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/reference');
		}
		$referenceDelete = $this->referenceModel->delete($id);
		if ($referenceDelete) {
			set_message('toastr_msg', 'Referensi berhasil dihapus');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Referensi gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', $this->auth->errors());
		}
		return redirect()->to('/reference?menu_id=' . $this->request->getGet('menu_id'));
	}

	public function delete_category(int $id = 0)
	{


		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/reference');
		}

		$menuDelete = $this->menuModel->delete($id);
		if ($menuDelete) {
			set_message('toastr_msg', 'Kategori Referensi berhasil dihapus');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Kategori Referensi gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', $this->auth->errors());
		}

		$reference = $this->referenceModel->orderBy('id', 'desc')->first();
		return redirect()->to('/reference?menu_id=' . $reference->menu_id);
	}

	public function enable($id = null)
	{


		$referenceUpdate = $this->referenceModel->update($id, array('active' => 1));

		if ($referenceUpdate) {
			set_message('toastr_msg', 'Referensi berhasil diaktifkan');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Referensi gagal diaktifkan');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/reference');
	}

	public function disable($id = null)
	{


		$referenceUpdate = $this->referenceModel->update($id, array('active' => 0));
		if ($referenceUpdate) {
			set_message('toastr_msg', 'Referensi berhasil dinonaktifkan');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Referensi gagal dinonaktifkan');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/reference');
	}
}
