<?php

namespace Group\Controllers;

use Base\Models\BaseModel;
use Group\Models\GroupPermissionModel;

class Group extends \Base\Controllers\BaseController
{
	/** Menu yang permission-nya ditampilkan bertingkat per submenu di halaman permission role. */
	private const TREE_MENU_NAMES = [
		'Koleksi', 'Perpustakaan Keliling', 'Keanggotaan', 'Sirkulasi', 'Laporan', 'Pengaturan Umum', 'Pengaturan Katalog', 'Pengaturan Eksemplar',
		'Pengaturan Keanggotaan', 'Pengaturan Sirkulasi', 'Pengaturan Buku Tamu', 'Pengaturan Opac',
		'Otorisasi', 'Setelan',
	];

	/** Judul kelompok untuk permission milik menu induk itu sendiri. */
	private const TREE_MENU_ACCESS_SECTION = 'Akses Menu';

	/** Permission yang submenunya tidak bisa ditentukan dari awalan route: nama atau route => controller submenu. */
	private const PERMISSION_ROUTE_ALIASES = [
		'katalog/create?rda=0'                         => 'katalog',
		'katalog/create?rda=1'                         => 'katalog',
		'eksemplar/create'                             => 'eksemplar',
		'katalog/pulihkan_katalog'                     => 'karantina-katalog',
		'katalog/hapus_permanen'                       => 'karantina-katalog',
		'katalog/preview-marc-file'                    => 'katalog/marc-import',
		'katalog/create-marc-from-file'                => 'katalog/marc-import',
		'eksemplar/pulihkan_eksemplar'                 => 'karantina-eksemplar',
		'eksemplar/hapus_permanen'                     => 'karantina-eksemplar',
		'eksemplar/import'                             => 'eksemplar/importviews',
		'eksemplar/importviews'                        => 'eksemplar/importviews',
		'eksemplar/download-template'                  => 'eksemplar/importviews',
		'anggota/import'                               => 'anggota/import_view',
		'anggota/import_anggota'                       => 'anggota/import_view',
		'sirkulasi-peminjaman/process-loan'            => 'sirkulasi-peminjaman/create',
		'sirkulasi-peminjaman/remove-book'             => 'sirkulasi-peminjaman/create',
		'sirkulasi-peminjaman/success'                 => 'sirkulasi-peminjaman/create',
		'sirkulasi-pengembalian/check-book'            => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/get-jenis-denda'       => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/get-jenis-pelanggaran' => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/getreturnhistory'      => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/process-return'        => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/send-struk'            => 'sirkulasi-pengembalian/create',
		'sirkulasi-pengembalian/success'               => 'sirkulasi-pengembalian/create',
		'sirkulasi-perpanjangan/send-struk'            => 'sirkulasi-perpanjangan/create',
		'sirkulasi-perpanjangan/success'               => 'sirkulasi-perpanjangan/create',
		'api/eksemplar/locationlibrary'                => 'master-lokasi-perpustakaan',
		'api/eksemplar/locations'                      => 'master-lokasi-ruang',
		'api/master-hari-libur/datatable'              => 'master-hari-libur',
		'mater-jenis-bahan'                            => 'master-jenis-bahan',
		'master-tujuan-buku-tamu'                      => 'master-tujuan-kunjungan',
	];

	public $auth;
	public $authorize;
	public $userModel;
	public $groupModel;
	public $permissionModel;

	function __construct()
	{
		$this->userModel = new \User\Models\UserModel();
		$this->groupModel = new \Group\Models\GroupModel();
		$this->permissionModel = new \Permission\Models\PermissionModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		helper('app');
	}

public function index()
{
    
    $groups = $this->groupModel->orderBy('id', 'asc')->findAll();
    
    $this->data['title'] = 'Role';
    $this->data['groups'] = $groups;
    
    return view('Group\Views\list', $this->data);
}

/**
 * Method terpisah untuk mengecek authorization
 */


	public function detail(int $id)
	{
		$this->data['title'] = 'Detail Role';
		$user = $this->auth->user($id)->first();
		$currentGroups = $this->auth->getUsersGroups($id)->getResult();
		$this->data['user'] = $user;
		$this->data['currentGroups'] = $currentGroups;
		$this->data['auth'] = $this->auth;
		echo view('Group\Views\view', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter(id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$groupDelete = $this->authorize->deleteGroup($id);
		if ($groupDelete) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group berhasil dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/group');
		}
	}

	public function enable($id = null)
	{
		$groupUpdate = $this->groupModel->update($id, array('active' => 1));

		if ($groupUpdate) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil diaktifkan');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group gagal diaktifkan');
			set_message('toastr_type', 'warning');
			set_message('message', 'Group gagal diaktifkan');
			return redirect()->to('/group');
		}
	}

	public function disable($id = null)
	{
		$groupUpdate = $this->groupModel->update($id, array('active' => 0));

		if ($groupUpdate) {
			reloadPermission();
			set_message('toastr_msg', 'Group berhasil dinonaktifkan');
			set_message('toastr_type', 'success');
			return redirect()->to('/group');
		} else {
			set_message('toastr_msg', 'Group gagal dinonaktifkan');
			set_message('toastr_type', 'warning');
			set_message('message', 'Group gagal dinonaktifkan');
			return redirect()->to('/group');
		}
	}

	public function permissions($group_id = null)
	{
		$this->data['title'] = 'Permission - Role';
		
		$group = get_single('auth_groups', 'id, name', 'id =' . $group_id);
		$groupPermissionModel = new GroupPermissionModel();
		$permissionModel = new BaseModel('auth_permissions');

		$permissions = $permissionModel->orderby('name', 'asc')->findAll();
		
		$this->data['permissions'] = $permissions;
		$this->data['permission_tree'] = $this->buildPermissionTree($permissions);
		$this->data['permissions_users'] = permissions_users(null, $group_id);
		// dd($this->data['permissions_users']);
		$this->data['group'] = $group;
		$this->data['group_id'] = $group_id;

		$this->validation->setRule('permissions', 'Permission', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$groupPermissionModel->where('group_id', $group_id)->delete();
			$permissions = $this->request->getPost('permissions');
			$save_data = [];
			foreach ($permissions as $permission_id => $data_arr) {
				$save_data[] = [
					'group_id' => $group_id,
					'permission_id' => $permission_id,
				];
			}
			if (!empty($save_data)) {
				$upsertGroupPermission = $groupPermissionModel->insertBatch($save_data);
				if ($upsertGroupPermission) {
					reloadPermission();
					session()->setFlashdata('swal_icon',  'success');
					session()->setFlashdata('swal_title', 'Berhasil');
					session()->setFlashdata('swal_text',  'Permission berhasil disimpan');
				} else {
					session()->setFlashdata('swal_icon',  'warning');
					session()->setFlashdata('swal_title', 'Gagal');
					session()->setFlashdata('swal_text',  'Permission gagal disimpan');
				}
			}

			return redirect()->back();
		}
		echo view('Group\Views\permissions', $this->data);
	}

	/**
	 * Kelompokkan permission untuk halaman permission role.
	 * Menu pada TREE_MENU_NAMES menjadi menu > submenu > permission (berdasarkan route),
	 * permission lain tetap dikelompokkan menurut kolom menu.
	 */
	private function buildPermissionTree(array $permissions): array
	{
		$menus = (new BaseModel('c_menus'))->where('category_id', 1)->orderBy('sort', 'asc')->orderBy('id', 'asc')->findAll();

		$tree = [];
		$menuIdsByName = [];
		foreach ($menus as $menu) {
			if ((int) $menu->parent === 0 && in_array($menu->name, self::TREE_MENU_NAMES, true)) {
				$tree[$menu->id] = ['name' => $menu->name, 'sections' => [self::TREE_MENU_ACCESS_SECTION => []]];
				$menuIdsByName[$menu->name] = $menu->id;
			}
		}

		// Satu controller bisa muncul di beberapa menu: submenu pohon didahulukan
		$controllerMenus = [];
		foreach ($menus as $menu) {
			if (empty($menu->controller)) {
				continue;
			}
			if (isset($tree[$menu->parent])) {
				$tree[$menu->parent]['sections'][$menu->name] = [];
			}
			if (!isset($controllerMenus[$menu->controller]) || isset($tree[$menu->parent])) {
				$controllerMenus[$menu->controller] = $menu;
			}
		}

		$flat = [];
		foreach ($permissions as $permission) {
			$route = self::PERMISSION_ROUTE_ALIASES[$permission->name]
				?? self::PERMISSION_ROUTE_ALIASES[$permission->route]
				?? preg_replace('#^api/#', '', $permission->route);
			$menu = null;
			while ($route !== '' && $menu === null) {
				$menu = $controllerMenus[$route] ?? null;
				$slash = strrpos($route, '/');
				$route = $slash === false ? '' : substr($route, 0, $slash);
			}

			if ($menu !== null && isset($tree[$menu->parent])) {
				$tree[$menu->parent]['sections'][$menu->name][] = $permission;
			} elseif ($menu !== null && isset($tree[$menu->id])) {
				// Submenu yang berbagi route dengan induknya dibedakan lewat kolom menu
				$section = isset($tree[$menu->id]['sections'][$permission->menu]) ? $permission->menu : self::TREE_MENU_ACCESS_SECTION;
				$tree[$menu->id]['sections'][$section][] = $permission;
			} elseif (isset($menuIdsByName[$permission->menu])) {
				$tree[$menuIdsByName[$permission->menu]]['sections'][self::TREE_MENU_ACCESS_SECTION][] = $permission;
			} else {
				$flat[$permission->menu][] = $permission;
			}
		}

		// Urutan mengikuti sidebar: menu level atas menurut sort, lalu submenunya
		$childrenByParent = [];
		foreach ($menus as $menu) {
			$childrenByParent[(int) $menu->parent][] = $menu;
		}
		$positionById = [];
		$positionByName = [];
		$position = 0;
		foreach ($childrenByParent[0] ?? [] as $top) {
			foreach (array_merge([$top], $childrenByParent[(int) $top->id] ?? []) as $menu) {
				$positionById[$menu->id] = $position;
				$positionByName[$menu->name] ??= $position;
				$position++;
			}
		}

		$items = [];
		foreach ($tree as $id => $node) {
			$sections = array_filter($node['sections']);
			if ($sections) {
				$items[] = ['name' => $node['name'], 'sections' => $sections, 'position' => $positionById[$id] ?? PHP_INT_MAX];
			}
		}
		// Kartu yang tidak ada di sidebar diletakkan di akhir menurut abjad
		ksort($flat);
		foreach ($flat as $name => $rows) {
			$items[] = ['name' => (string) $name, 'rows' => $rows, 'position' => $positionByName[$name] ?? PHP_INT_MAX];
		}
		usort($items, fn ($a, $b) => $a['position'] <=> $b['position']);

		return $items;
	}
}
