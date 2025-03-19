<?php

namespace Menu\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Menu extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $menuModel;
	public $menuCategoryModel;
	public $validation;
	public $session;
	public $auth;
	public $authorize;
	public $sort;
	public $menus;
	function __construct()
	{
		$this->menuModel = new \Menu\Models\MenuModel();
		$this->menuCategoryModel = new \Menu\Models\MenuCategoryModel();
		$this->session = session();
		$this->validation = service('validation');
		$this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
		
		helper(['text','app','reference','menu']);
	}

	public function index()
	{
		$data = $this->menuModel->findAll();
		return $this->respond($data, 200);
	}

	public function delete($id = null)
	{
		$data = $this->menuModel->find($id);
		if ($data) {
			$this->menuModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Menu berhasil dihapus'
				]
			];
			reloadPermission();
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan dengan ID: ' . $id);
		}
	}

	public function set_status()
	{
		$id = $this->request->getPost('id');
		$status = $this->request->getPost('status');
		$menu = $this->menuModel->find($id);
		$activation_status = ($status == 1) ? 'Aktif': 'Nonaktif';
		$message = 'Menu '.$menu->name. ' '.$activation_status;

		$moduleUpdate = $this->menuModel->update($id, array('active' => $status));
		if ($moduleUpdate) {
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => $message
				]
			];
			reloadPermission();
			return $this->respond($response);
		} else {
			return $this->fail('<div class="alert alert-danger fade show" role="alert">Menu gagal diubah statusnya</div>', 400);
		}
	}

	private function _parse_menu($menus, $parent = '0')
	{
		foreach ($menus as $menu) {
			$this->sort++;
			$this->menus[] = [
				'id' => $menu['id'],
				'sort' => $this->sort,
				'parent' => $parent
			];
			if (isset($menu['children'])) {
				$this->_parse_menu($menu['children'], $menu['id']);
			}
		}
	}

	public function save_ordering()
	{
		$this->menus = [];
		$this->sort = 0;
		$this->_parse_menu($this->request->getPost('menu'));

		$moduleUpdate = $this->menuModel->updateBatch($this->menus, 'id');
		if ($moduleUpdate) {
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Menu berhasil diubah'
				]
			];
			return $this->respond($response);
		} else {
			return $this->fail('<div class="alert alert-danger fade show" role="alert">Menu gagal diubah</div>', 400);
		}
	}

	public function category_create()
	{
		$this->validation->setRule('name', 'Nama Menu', 'required');		
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$form_slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $form_slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newCategoryMenuId = $this->menuCategoryModel->insert($save_data);
			if ($newCategoryMenuId) {
				$this->session->setFlashdata('toastr_msg', 'Kategori Menu berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Kategori Menu berhasil disimpan'
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' => 'Kategori Menu gagal disimpan'
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function category_detail($id = null)
	{
		$data = $this->menuCategoryModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('Data tidak ditemukan dengan ID: ' . $id);
		}
	}

	public function category_edit($id = null)
	{
		$this->validation->setRule('name', 'Kategori Menu', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			if(!empty($this->request->getPost('slug'))){
				$update_data['slug'] = $this->request->getPost('slug');
			}

			$menuCategoryUpdate = $this->menuCategoryModel->update($id, $update_data);
			if ($menuCategoryUpdate) {
				$this->session->setFlashdata('toastr_msg', 'Kategori Menu berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Kategori Menu berhasil disimpan'
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Kategori Menu gagal disimpan</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama Menu', 'required');		
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$form_slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = array(
				'name' => $this->request->getPost('name'),
                'slug' => 'ref-'.$form_slug,
				'controller' => 'ref-'.$form_slug,
				'permission' => 'access',
                'parent' => $this->request->getPost('parent') ?? 0,
                'category_id' => 3,
			);

			$menuId = $this->menuModel->insert($save_data);
			if ($menuId) {
				$this->session->setFlashdata('toastr_msg', 'Kategori Referensi berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Kategori Referensi berhasil disimpan'
					],
					'data'    => $menuId,
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' => 'Kategori Referensi gagal disimpan'
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function config_menu()
	{
		try {
			$data = config_menu_frontend(0, 1);

			// child menu
			$model = new \Organisasi\Models\OrganisasiModel();
			$childs = $model->orderBy('sort','asc')->findAll();
			$channel = [];
			foreach($childs as $row){
				$data[] = array(
					"themes" => (int) $row->sort,
					"key" =>  "beranda"."_".$row->sort,
					"is_public" => true,
					"label" =>  "Beranda",
					"url" =>  "/",
					"submenu" =>  []
				);

				$data[] = array(
					"themes" => (int) $row->sort,
					"key" =>  "berita"."_".$row->sort,
					"is_public" => true,
					"label" =>  "Berita",
					"url" =>  "/berita",
					"submenu" =>  []
				);

				$data[] = array(
					"themes" => (int) $row->sort,
					"key" =>  "agenda"."_".$row->sort,
					"is_public" => true,
					"label" =>  "Agenda",
					"url" =>  "/agenda",
					"submenu" =>  []
				);
			}
	
			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with id ' . $pk);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
	}
}
