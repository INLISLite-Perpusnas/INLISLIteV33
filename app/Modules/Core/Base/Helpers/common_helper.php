<?php

use Base\Models\BaseModel;

if (!function_exists('get_param')) {
	function get_param($key = '')
	{
		$request = service('request');
		$value = $request->getGet($key) ?? '';

		return $value;
	}
}

if (!function_exists('get_single')) {
	function get_single($table, $fields = 'id', $where = '1=1')
	{
		$builder = new BaseModel($table);
		$query = $builder->select($fields);

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->get()->getRow();
	}
}

if (!function_exists('get_table')) {
	function get_table($table, $fields = 'id', $where = '1=1')
	{
		$builder = new BaseModel($table);
		$query = $builder->select($fields);
		$query->distinct();

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->get()->getResult();
	}
}

if (!function_exists('count_all')) {
	function count_all($table, $where = '1=1')
	{
		$builder = new BaseModel($table);
		$query = $builder;

		if (!empty($where)) {
			$query->where($where);
		}

		return $query->countAllResults();
	}
}

if (!function_exists('is_exist')) {
	function is_exist($table, $where = '1=1')
	{
		return count_all($table, $where) > 0;
	}
}

if (!function_exists('set_message')) {
	function set_message($name, $value)
	{
		$session = service('session');
		$session->setFlashdata($name, $value);
	}
}

if (!function_exists('get_message')) {
	function get_message($name)
	{
		$session = service('session');
		return $session->getFlashdata($name);
	}
}

if (!function_exists('get_object_array')) {
	function get_object_array($objects, $field)
	{
		$result = [];
		foreach ($objects as $row) {
			$result[] = $row->$field;
		}

		return $result;
	}
}

if (!function_exists('formatIDR')) {
	function formatIDR($angka = null, $prefix = 'Rp. ')
	{
		$hasil = $prefix . number_format($angka, 0, ',', '.');
		return $hasil;
	}
}

if (!function_exists('unformatIDR')) {
	function unformatIDR($amount = null)
	{
		$result = str_replace("Rp ", "", $amount);
		$result = str_replace(".00", "", $result);

		return $result;
	}
}
