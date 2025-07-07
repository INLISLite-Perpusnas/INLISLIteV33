<?php

use Eksemplar\Models\EksemplarModel;

if (!function_exists('count_collections')) {
	function count_collections($catalog_id = null)
	{
		$model = new EksemplarModel();

		$data = $model->where('Catalog_id', $catalog_id)->countAllResults();
		return $data;
	}
}

if (!function_exists('data_catalog')) {
	function data_catalog($param)
	{
		$db      = \Config\Database::connect();

		$query = $db->table('catalog_ruas')->where('CatalogId', $param)->where('Tag', 245)
			->select('CatalogId AS ID,Value AS Value')->get();
		$queryx = $db->table('catalog_ruas')->where('CatalogId', $param)
			->select('CatalogId AS ID,Value AS Value')->get();
		$data = $query->getRow();
		$datax = $queryx->getResult();

		$catalog_json = array();
		foreach (array_filter(explode('$', $data->Value)) as $value) {
			$catalog_json[substr($value, 0, 1)] = substr($value, 2);
		}

		return json_encode($catalog_json);
	}
}

function get_branch_id($location_library_id)
{
	$db = db_connect('data');
	$query = $db->table('location_library')->select('Branch_id as no')->where('ID', $location_library_id)->get();
	$no = $query->getRow()->no;
	return $no;
}
function get_branch($branch_id)
{
	$db = db_connect('data');
	$query = $db->table('branchs')->where('ID', $branch_id)->get();
	$row = $query->getRow();
	return $row;
}

function gen_no_barcode($i = 1)
{
	$db = db_connect('data');
	$query = $db->table('collections')->select('MAX(RIGHT(NomorBarcode,5)) as no')->get();
	$no = $query->getRow()->no;
	$formatQuery = $db->table('settingparameters')
                     ->select('Value')
                     ->where('Name', 'FormatNomorInduk')
                     ->get();
    
    $formatResult = $formatQuery->getRow();
	$format = get_parameter('nomor-barcode', '{yyyy}/PN/{99999}');
	$format2 = str_replace('{yyyy}', date('Y'), $format);
	$format3 = str_replace('{99999}', '', $format2);

	if (empty($no)) {
		$no = 1;
	} else {
		$no = intval($no) + $i;
		$no = str_pad($no, 5, "0", STR_PAD_LEFT);
	}

	$number = $format3 . $no;
	return $number;
}

function gen_no_rfid($i = 1)
{
	$db = db_connect('data');
	$query = $db->table('collections')->select('MAX(RIGHT(RFID,5)) as no')->get();
	$no = $query->getRow()->no;
	$format = get_parameter('nomor-rfid', '{yyyy}/PN/{99999}');
	$format2 = str_replace('{yyyy}', date('Y'), $format);
	$format3 = str_replace('{99999}', '', $format2);

	if (empty($no)) {
		$no = $i;
	} else {
		$no = intval($no) + $i;
		$no = str_pad($no, 5, "0", STR_PAD_LEFT);
	}

	$number = $format3 . $no;
	return $number;
}

function gen_no_induk($i = 1)
{
	$db = db_connect('data');
	$query = $db->table('collections')->select('MAX(RIGHT(NoInduk,5)) as no')->get();
	$no = $query->getRow()->no;
	$format = get_parameter('nomor-induk', '{yyyy}/PN/{99999}');
	$format2 = str_replace('{yyyy}', date('Y'), $format);
	$format3 = str_replace('{99999}', '', $format2);

	if (empty($no)) {
		$no = 1;
	} else {
		$no = intval($no) + $i;
		$no = str_pad($no, 5, "0", STR_PAD_LEFT);
	}

	$number = $format3 . $no;
	return $number;
}
