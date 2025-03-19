<?php
if (!function_exists('display_menu_reference')) {
	function display_menu_reference($category_id, $parent, $level)
	{
		$baseModel = new \Base\Models\BaseModel();
		$query = $baseModel->query("SELECT a.id, a.name as label, a.type, a.active, a.controller, a.slug, deriv.count
				FROM `c_menus` a LEFT OUTER JOIN (
					SELECT parent, COUNT(*) AS count
						FROM `c_menus` GROUP BY parent
					) deriv ON a.id = deriv.parent WHERE a.parent= " . $parent . " and a.category_id = " . $category_id . " and a.active = 1
				ORDER BY `sort` ASC");
		$result = $query->getResult();

		$ret = '';

		if ($result) {
			$ret .= '<ol class="dd-list" id="menu_references">';
			foreach ($result as $row) {
				$selected = ($row->id == get_var('menu_id')) ? 'bg-secondary text-white' : '';
				if ($row->count > 0) {
					$ret .= '<li class="dd-item dd3-item ' . ($row->active ? '' : 'menu-toggle-activate_inactive') . ' menu-toggle-activate" data-id="' . $row->id . '" data-status="' . $row->active . '">';

					if ($row->type != 'label') {
						$ret .= '<div class="dd-handle dd3-handle dd-handles"></div>';
						$ret .= '<div class="dd3-content">' . _ent($row->label);
					} else {
						$ret .= '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
						$ret .= '<div class="dd3-content"><b>' . _ent($row->label) . '</b>';
					}

					$ret .= '</div>';
					$ret .= display_menu_reference($category_id, $row->id, $level + 1);
					$ret .= "</li>";
				} elseif ($row->count == 0) {
					$ret .= '<li class="dd-item dd3-item ' . ($row->active ? '' : 'menu-toggle-activate_inactive') . ' menu-toggle-activate" data-id="' . $row->id . '" data-status="' . $row->active . '">';

					if ($row->type != 'label') {
						$ret .= '<div class="dd-handle dd3-handle dd-handles"></div>';
						$ret .= '<div class="dd3-content ' . $selected . '">' . _ent($row->label);
					} else {
						$ret .= '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
						$ret .= '<div class="dd3-content"><b>' . _ent($row->label) . '</b>';
					}

					$ret .= display_action_reference($row->id, $row->slug);

					$ret .= '</div></li>';
				}
			}
			$ret .= "</ol>";
		}

		return $ret;
	}
}

if (!function_exists('display_action_reference')) {
	function display_action_reference($id, $slug)
	{
		// $is_empty = count_by_menu_id($id) == 0;

		$action = '';
		$action .= '<div class="pull-right">';
		$action .= '<button data-toggle="tooltip" data-placement="top" title="Slug: ' . $slug . '" class="btn btn-xs btn-info"><i class="pe-7s-info font-weight-bold"></i></button> ';
		$action .= '<a href="' . base_url('reference?menu_id=' . $id) . '" data-toggle="tooltip" data-placement="top" title="Lihat Referensi" class="btn btn-xs btn-primary"><i class="lnr-list font-weight-bold"></i></a> ';
		// if($is_empty){
		//   $action .= '<a href="javascript:void(0);" data-href="'.base_url('reference/delete_category/'.$id).'" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-xs btn-danger remove-data"><i class="lnr-trash font-weight-bold"></i></a>';
		// }
		$action .= '</div>';
		return $action;
	}
}

if (!function_exists('count_by_menu_id')) {
	function count_by_menu_id($menu_id)
	{
		$model = new \Reference\Models\ReferenceModel();
		$count = $model->where('menu_id', $menu_id)->countAllResults();

		return $count;
	}
}

if (!function_exists('get_var')) {
	function get_var($name)
	{
		$request = service('request');

		$value = $request->getGet($name);
		return $value;
	}
}

if (!function_exists('get_path')) {
	function get_path()
	{
		$request = service('request');

		$path = preg_replace("/\//", "", $request->getPath());

		return stripslashes($path);
	}
}

if (!function_exists('get_ref_id')) {
	function get_ref_id($ref_value, $ref_field = 'name', $menu_value = 'ref_page', $menu_field = 'controller')
	{
		$baseModel = new \Base\Models\BaseModel();
		$baseModel->setTable('c_references');
		$query = $baseModel
			->select('c_references.*')
			->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner');

		$query->where('UPPER(c_references.' . $ref_field . ')', strtoupper($ref_value));
		if (!empty($menu_value)) {
			$query->where('UPPER(c_menus.' . $menu_field . ')', strtoupper($menu_value));
		}

		$data = $query->get()->getRow();

		return $data->id ?? 0;
	}
}

if (!function_exists('get_ref_value')) {
	function get_ref_value($ref_id, $menu_value = null, $menu_field = 'controller')
	{
		$baseModel = new \Base\Models\BaseModel();
		$baseModel->setTable('c_references');
		$query = $baseModel
			->select('c_references.*')
			->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner');

		$query->where('c_references.id', $ref_id);
		if (!empty($menu_value)) {
			$query->where('UPPER(c_menus.' . $menu_field . ')', strtoupper($menu_value));
		}

		$data = $query->get()->getRow();

		return $data->name ?? '';
	}
}

if (!function_exists('get_ref')) {
	function get_ref($menu_value, $menu_field = 'controller')
	{
		$baseModel = new \Base\Models\BaseModel();
		$baseModel->setTable('c_references');
		$query = $baseModel
			->select('c_references.*')
			->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner');

		$query->where('UPPER(c_menus.' . $menu_field . ')', strtoupper($menu_value));

		$data = $query->orderBy('c_references.sort', 'asc')->findAll();

		return $data;
	}
}

if (!function_exists('get_ref_dropdown')) {
	function get_ref_dropdown($menu_value, $menu_field = 'controller', $selected_id = 0)
	{
		$html = '<select class="form-control" name="ref_type_id" tabindex="-1" aria-hidden="true">';
		foreach (get_ref($menu_value, $menu_field) as $row) {
			$selected = ($row->id == $selected_id) ? 'selected' : '';
			$html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
		}
		$html .= '</select>';

		return $html;
	}
}

if (!function_exists('get_ref_table')) {
	function get_ref_table($table, $fields = 'id', $where = null, $DBGroup = 'default')
	{
		$db = db_connect($DBGroup);
		$baseModel = $db->table($table);
		$query = $baseModel->select($fields);
		$query->distinct();

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->get()->getResult();
	}
}

if (!function_exists('get_ref_single')) {
	function get_ref_single($table, $where = null, $DBGroup = 'data', $sort = 'ID', $direction = 'DESC')
	{
		$db = db_connect($DBGroup);
		$baseModel = $db->table($table);
		$query = $baseModel->where($where)->orderBy($sort, $direction);
		return $query->get()->getRow();
	}
}

if (!function_exists('get_pad_number')) {
	function get_pad_number($counter, $prefix = 'TRX-', $zero_length = 4)
	{
		$doc_number = strtoupper($prefix) . str_pad($counter, $zero_length, "0", STR_PAD_LEFT);

		return $doc_number;
	}
}

if (!function_exists('get_dropdown')) {
	function get_dropdown($table, $where = null, $code = 'id', $text = 'name')
	{
		$baseModel = new \Base\Models\BaseModel();
		$baseModel->setTable($table);

		$query = $baseModel;
		$query->select("$code as code");
		$query->select("$text as text");

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->orderBy($code)->get()->getResult();
	}
}

if (!function_exists('get_dropdown2')) {
	function get_dropdown2($table, $where = null, $code = 'id', $text = 'name', $expiry_days = 'expiry_days')
	{
		$baseModel = new \Base\Models\BaseModel();
		$baseModel->setTable($table);

		$query = $baseModel;
		$query->select("$code as code");
		$query->select("$text as text");
		$query->select("$expiry_days as expiry_days");

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->orderBy($code)->get()->getResult();
	}
}
