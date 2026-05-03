<?php

use Base\Models\BaseModel;
use Base\Models\DataModel;
use Katalog\Models\KatalogModel;
use Katalog\Models\KatalogRuasModel;

if (!function_exists('parse_catalog_ruas')) {
	function parse_catalog_ruas($row, $tag = null, $is_form = false)
	{
		$form = [];
		$catalog = [];
		$matches = [];
		preg_match_all('/\$[a-z]+\s+(.+?)(?=\s+\$|$)/i', $row, $matches);
		$space = ' ';
		$comma = ', ';
		$slash = ' / ';
		$colon = ' : ';
		$semicolon = ' ;';

		switch ($tag) {
			case '245':
				$judulUtama = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][0] ?? "", ",;:\/#$"));
				$anakJudul = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][1] ?? "", ",;:\/#$"));
				$penanggungJawab = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][2] ?? "", ",;:\/#$"));
				$catalog['Title'] = $judulUtama . $colon . $anakJudul . $slash . $penanggungJawab;
				$form['JudulUtama'] = $judulUtama;
				$form['AnakJudul'] = $anakJudul;
				$form['PenanggungJawab'] = $penanggungJawab;
				break;
			case '260':
				$publishLocation = preg_replace('/[,;:#\/\s]+$/', '', rtrim($matches[1][0] ?? "", ",;:\/#$"));
				$publisher = preg_replace('/[,;:#\/\s]+$/', '', rtrim($matches[1][1] ?? "", ",;:\/#$"));
				$publishYear = preg_replace('/[,;:#\/\s]+$/', '', rtrim($matches[1][2] ?? "", ",;:\/#$"));

				$catalog['PublishLocation'] = $publishLocation . $colon;
				$catalog['Publisher'] = $publisher . $comma;
				$catalog['PublishYear'] = $publishYear;
				$catalog['Publikasi'] = $catalog['PublishLocation'] . $catalog['Publisher'] . $catalog['PublishYear'];
				$form['PublishLocation'] = $publishLocation;
				$form['Publisher'] = $publisher;
				$form['PublishYear'] = $publishYear;
				break;
			case '300':
				$halaman = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][0] ?? "", ",;:\/#$"));
				$ilus = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][1] ?? "", ",;:\/#$"));
				$dimensi = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][2] ?? "", ",;:\/#$"));
				$bahanSertaan = preg_replace('/[\/;:#$]\b/', '', rtrim($matches[1][3] ?? "", ",;:\/#$"));
				$catalog['PhysicalDescription'] = $halaman . $space . $ilus . $space . $dimensi . $space . $bahanSertaan;
				$form['halaman'] = $halaman;
				$form['ilus'] = $ilus;
				$form['dimensi'] = $dimensi;
				$form['bahanSertaan'] = $bahanSertaan;
				break;
			case '100':
				$catalog['Author'] = $matches[1][0] ?? "";
				break;
		}

		return ($is_form) ? $form : $catalog;
	}
}

if (!function_exists('get_catalog_ruas_tag')) {
	function get_catalog_ruas_tag($catalog_id = null, $tag = null)
	{
		$builder = new DataModel('catalog_ruas');
		$query = $builder->where('CatalogId', $catalog_id)->where('Tag', $tag)->orderBy('ID', 'ASC');
		$data = $query->get()->getResult();
		return $data;
	}
}

if (!function_exists('get_catalog_ruas_tags')) {
	function get_catalog_ruas_tags($catalog_id = null, $tags = [])
	{
		$builder = new DataModel('catalog_ruas');
		$query = $builder->where('CatalogId', $catalog_id)->whereIn('Tag', $tags)->orderBy('ID', 'ASC');
		$data = $query->get()->getResult();
		return $data;
	}
}



if (!function_exists('get_array_tag')) {
	/**
	 * Mengambil data dari get_catalog_ruas_tag dan mengubahnya 
	 * menjadi associative array berdasarkan tag ($a, $b, dll).
	 */
	function get_array_tag($catalog_id = null, $tag = null)
	{
		// 1. Ambil data string asli Anda (baris ini TIDAK dibuang)
		$catalog_ruas = get_catalog_ruas_tag($catalog_id, $tag);

		// Asumsi data string yang akan diproses ada di sini
		$string_to_parse = $catalog_ruas[0]->Value ?? "";

		$result = [];

		// 2. Terapkan logika parsing pada string yang didapat dari fungsi Anda
		$pattern = '/\/?\$([a-zA-Z])\s*(.*?)(?=\s*\/?\$|$)/s';

		if (preg_match_all($pattern, $string_to_parse, $matches)) {
			$keys = $matches[1];
			$values = $matches[2];

			for ($i = 0; $i < count($keys); $i++) {
				$result[$keys[$i]] = trim($values[$i]);
			}
		}

		return $result;
	}
}

// Contoh cara memanggil fungsi Anda nanti:
// $data_katalog = get_array_tag_assoc(12345, 'some_tag');
// print_r($data_katalog);



if (!function_exists('rtrim_chars')) {
	function rtrim_chars($line)
	{
		$charsToRtrim = " :;/";
		return rtrim(trim($line), $charsToRtrim);
	}
}

if (!function_exists('ltrim_words')) {
	function ltrim_words($line)
	{
		$wordsToLtrim = ['sang', 'yang'];
		foreach ($wordsToLtrim as $word) {
			$line = preg_replace("/^(?i)$word\s*/", '', $line);
		}
		return $line;
	}
}



if (!function_exists('get_catalog_ruas')) {
	function get_catalog_ruas($catalog_id = null)
	{
		$catalogRuasModel = new KatalogRuasModel();
		$catalogRuas = $catalogRuasModel->where('Catalogid', $catalog_id)->get()->getResult();

		$catalog = [];

		foreach ($catalogRuas as $ruas) {
			$catalog = array_merge($catalog, parse_catalog_ruas($ruas->Value, $ruas->Tag));
		}

		return $catalog;
	}
}

if (!function_exists('form_catalog_ruas')) {
	function form_catalog_ruas($catalog_id = null, $tag = null)
	{
		$catalogRuasModel = new KatalogRuasModel();
		$ruas = $catalogRuasModel->where('Catalogid', $catalog_id)->where('Tag', $tag)->first();

		try {
			$form = parse_catalog_ruas($ruas->Value, $ruas->Tag, true);
			return $form;
		} catch (\Throwable $th) {
			return false;
		}
	}
}

if (!function_exists('stringify_catalog_ruas')) {
	function stringify_catalog_ruas($inputArr = [])
	{
		$inputStr = '';
		foreach ($inputArr as $key => $value) {
			$inputStr .= '$' . $key . ' ' . $value . ' ';
		}
		$inputStr = rtrim($inputStr);

		return $inputStr;
	}
}

if (!function_exists('get_all_tags')) {
	function get_all_tags($worksheet_id = '1')
	{
		$db = db_connect();
		$session = service('session');
		if (!$session->has('worksheet_fields')) {
			$builder = $db->table('worksheetfields as wsf')
				->select('f.ID, f.Tag, f.Name, f.Mandatory, f.Fixed, f.Repeatable')
				->join('fields as f', 'f.ID = wsf.Field_id')
				->where('wsf.Worksheet_id', $worksheet_id)
				 ->orderBy('f.Tag', 'ASC');
			$worksheet_fields = $builder->get()->getResultArray();
			$session->set('worksheet_fields', $worksheet_fields);
		}
		$session_tags = $session->get('worksheet_fields');
		$builder2 = $db->table('fields as f')->select('f.ID, f.Tag, f.Name, f.Fixed, f.Repeatable')->orderBy('f.Tag', 'ASC');
		$fields = $builder2->get()->getResultArray();
		$filtered_tags = array_filter($fields, function ($field) use ($session_tags) {
			if ($field['Repeatable'] == 0) {
				foreach ($session_tags as $worksheetField) {
					if (($field['ID'] === $worksheetField['ID']) && ($field['Repeatable'] == 0)) {
						return false;
					}
				}
			}
			return true;
		});

		$data = array(
			'session_tags' => $session_tags,
			'filtered_tags' => $filtered_tags,
		);

		return (object) $data;
	}
}

if (!function_exists('add_to_session')) {
	function add_to_session($field_id = null)
	{
		$db = db_connect();
		$session = service('session');

		$builder = $db->table('fields as f')
			->select('f.ID, f.Tag, f.Name, f.Mandatory, f.Fixed, f.Repeatable')
			->where('f.ID', $field_id);
		$worksheet_field = $builder->get()->getRowArray();
		$worksheet_fields = $builder->get()->getResultArray();

		$session_tags = array();
		if ($worksheet_field) {
			if (!$session->has('worksheet_fields')) {
				$session->set('worksheet_fields', $worksheet_fields);
			} else {
				$session_tags = $session->get('worksheet_fields');
				array_push($session_tags, $worksheet_field);
				$session->set('worksheet_fields', $session_tags);
			}
		}

		$builder2 = $db->table('fields as f')->select('f.ID, f.Tag, f.Name, f.Fixed, f.Repeatable');
		$fields = $builder2->get()->getResultArray();
		$filtered_tags = array_filter($fields, function ($field) use ($session_tags) {
			foreach ($session_tags as $worksheetField) {
				if ($field['ID'] === $worksheetField['ID']) {
					return false;
				}
			}
			return true;
		});

		$data = array(
			'session_tags' => $session_tags,
			'filtered_tags' => $filtered_tags,
			'worksheet_field' => $worksheet_field,
		);

		return (object) $data;
	}
}

if (!function_exists('remove_from_session')) {
	function remove_from_session($field_id = null)
	{
		$db = db_connect();
		$session = service('session');

		$builder = $db->table('fields as f')
			->select('f.ID, f.Tag, f.Name, f.Mandatory, f.Fixed, f.Repeatable')
			->where('f.ID', $field_id);
		$worksheet_field = $builder->get()->getRowArray();

		$session_tags = [];
		$filtered_tags = [];
		if ($session->has('worksheet_fields')) {
			$session_tags = $session->get('worksheet_fields');
			$filtered_tags = array_filter($session_tags, static function ($field) use ($field_id) {
				if ($field['ID'] === $field_id) {
					return false;
				}
				return true;
			});
			$session->set('worksheet_fields', $filtered_tags);
		}

		$data = array(
			'session_tags' => $session_tags,
			'filtered_tags' => $filtered_tags,
			'worksheet_field' => $worksheet_field,
		);

		return (object) $data;
	}
}

if (!function_exists('get_field_indicator1')) {
	function get_field_indicator1($field_id = null)
	{
		$db = db_connect();
		$builder = $db->table('fieldindicator1s as fi')
			->select('fi.Field_id as ID, fi.Code, fi.Name')
			->where('fi.Field_id', $field_id);
		$data = $builder->get()->getResult();

		return (object) $data;
	}
}

if (!function_exists('get_field_indicator2')) {
	function get_field_indicator2($field_id = null)
	{
		$db = db_connect();
		$builder = $db->table('fieldindicator2s as fi')
			->select('fi.Code, fi.Name')
			->where('fi.Field_id', $field_id);
		$data = $builder->get()->getResultArray();

		return (object) $data;
	}
}

if (!function_exists('select_two')) {
	function select_two()
	{
		$db = db_connect();
		$builder = $db->table('refferenceitems');

		$query = $builder->where('Refference_id', 5)
			->select('Code,CONCAT(Code, " - ",Name) as Name')->get();
		$data['lang'] = $query->getResult();

		$query = $builder->where('Refference_id', 17)
			->select('Code,CONCAT(Code, " - ",Name) as Name')->get();
		$data['karya-tulis'] = $query->getResult();

		$query = $builder->where('Refference_id', 2)
			->select('Code,CONCAT(Code, " - ",Name) as Name')->get();
		$data['kelompok-sasaran'] = $query->getResult();

		$query = $db->table('worksheets')
			->select('worksheets.ID as ID, worksheets.Name as Name, worksheets.Keterangan as Keterangan')->orderBy('ID')->get();

		$data['worksheets'] = $query->getResult();
		return $data;
	}
}

if (!function_exists('get_catalog')) {
	function get_catalog($catalog_id = null)
	{
		$model = new KatalogModel();

		$data = $model->where('ID', $catalog_id)->get()->getRow();
		return $data;
	}
}

if (!function_exists('get_control_number')) {
	function get_control_number()
	{
		$db = db_connect();
		//get last control number
		if (!empty($id)) {
			$query2 = $db->query('SELECT catalog_ruas.`Value` AS MaxControlNumber FROM catalog_ruas WHERE catalog_ruas.`CatalogId` = "' . $id . '" AND catalog_ruas.`Tag` ="001" ');
			$row = $query2->getRow()->MaxControlNumber;

			$newControlNumber =  substr($row, 3);
		} else {
			$query2 = $db->query('SELECT MAX(ControlNumber) AS MaxControlNumber FROM catalogs WHERE ControlNumber LIKE "INLIS0%"');
			$row = $query2->getRow()->MaxControlNumber;

			if ($row >= 0) {
				$controlNumber = (int)preg_replace('/[^0-9]/', '', $row);
			}
			$newControlNumber =  'INLIS' . str_pad((int)$controlNumber + 1, 15, '0', STR_PAD_LEFT);
		}
		return $newControlNumber;
	}
}

if (!function_exists('get_bib_id')) {
	function get_bib_id()
	{
		$db = db_connect();
		if (!empty($id)) {
			$query2 = $db->query('SELECT catalog_ruas.`Value` AS MaxBibId FROM catalog_ruas WHERE catalog_ruas.`CatalogId` = "' . $id . '" AND catalog_ruas.`Tag` ="035" ');
			$row = $query2->getRow()->MaxBibId;

			$newId =  substr($row, 3);
		} else {
			$yearMonth =  date('my');
			$query2 = $db->query('SELECT SUBSTR(MAX(BIBID),"0010-' . $yearMonth . '") AS MaxBibId FROM catalogs WHERE BIBID LIKE "0010-' . $yearMonth . '%" AND LENGTH(BIBID)=15');
			$row = $query2->getRow()->MaxBibId;

			$maxId =  (int)$row + 1;
			$newId =  '0010-' . $yearMonth . str_pad($maxId, 6, '0', STR_PAD_LEFT);
		}

		return $newId;
	}
}

function merge_key_value($value)
{
	$arr_map = array_map(
		function ($v, $k) {
			if (is_array($v)) {
				return $k . '[]=' . implode('&' . $k . '[]=', $v);
			} else {
				return '$' . $k . ' ' . $v;
			}
		},
		$value,
		array_keys($value)
	);

	return $arr_map;
}

function implode_data($post, $separator = ";")
{
	return is_array($post) ? implode($separator, $post) : $post;
}

function multi_array($post)
{
	$fix = array_map(static function ($value) {
		return implode_data($value);
	}, $post);

	return implode_data($fix);
}


function tag_indicator($key, $isRDA = false)
{
	$tag = 'unset';
	$ind1 = '#';
	$ind2 = '#';
	switch ($key) {
		case 'judul':
			$tag = '245';
			break;
		case 'variasiBentukJudul':
			$tag = '246';
			break;
		case 'judulAsli':
			$tag = '740';
			break;
		case 'judulSeragam':
			$tag = '240';
			break;
		case 'judulSebelumnya':
			$tag = '247';
			break;
		case 'pengarangUtama':
			$tag = '100';
			break;
		case 'pengarangTambahan':
			$tag = '700';
			break;
		case 'penerbit':
			$tag = $isRDA ? '264' : '260'; // ← berubah sesuai isRDA
			break;
		case 'frekuensi':
			$tag = '310';
			break;
		case 'frekuensiSebelumnya':
			$tag = '321';
			break;
		case 'PhysicalDescription':
			$tag = '300';
			break;
		case 'jenisIsi':
			$tag = '336';
			break;
		case 'jenisMedia':
			$tag = '337';
			break;
		case 'jenisWadah':
			$tag = '338';
			break;
		case 'ISBN':
			$tag = '020';
			break;
		case 'LocCollDaring':
			$tag = '856';
			break;
		case 'ControlNumber':
			$ind1 = null;
			$ind2 = null;
			$tag = '001';
			break;
		case 'tag005':
			$ind1 = null;
			$ind2 = null;
			$tag = '005';
			break;
		case 'BIBID':
			$ind1 = '#';
			$ind2 = '#';
			$tag = '035';
			break;
		case 'CallNumber':
			$tag = '084';
			break;
		case 'DeweyNo':
			$tag = '082';
			break;
		case 'language':
			$ind1 = null;
			$ind2 = null;
			$tag = '008';
			break;
		default:
			$tag = $key;
	}

	return array(
		'tag' => $tag,
		'ind1' => $ind1,
		'ind2' => $ind2
	);
}

function data_catalog_ruas($post, $catalog_id)
{
	$result = [];
	$now = date("Y-m-d H:i:s");

	$isRDA = !empty($post['IsRDA']) && $post['IsRDA'] == 1;

	// ← ambil relator SEBELUM di-exclude
	$pengarangTambahanRelator = $post['pengarangTambahanRelator'] ?? [];

	$excludedKeys = [
		'pengarangTambahanRadio',
		'pengarangUtamaRadio',
		'catatanRadio',
		'pengarangTambahanRelator',
		'Languages',
		'CoverURL',
		'cat_id',
		'update',
		'worksheet_id',
		'Worksheet_id', // ← tambah ini (case sensitive)
		'catalog_type',
		'worksheet_name',
		'Edition',
		'IsOPAC',
		'IsRDA',
		'IsRedirect',
		'submit'
	];
	$post = array_diff_key($post, array_flip($excludedKeys));

	$mergeKey = ['judul', 'penerbit', 'PhysicalDescription'];
	foreach ($mergeKey as $key) {
		$tag_indicator = tag_indicator($key, $isRDA);
		$result[$tag_indicator['tag']][] = [
			'Tag'        => $tag_indicator['tag'],
			'Indicator1' => $tag_indicator['ind1'],
			'Indicator2' => $tag_indicator['ind2'],
			'CatalogId'  => $catalog_id,
			'value'      => implode_data(merge_key_value($post[$key]), " "),
			'CreateBy'   => user_id(),
			'CreateDate' => $now,
			'UpdateBy'   => user_id(),
			'UpdateDate' => $now,
		];
		unset($post[$key]);
	}

	$authorKey = ['pengarangUtama', 'pengarangTambahan', 'subject', 'catatan'];
	foreach ($authorKey as $key) {
		$value = $post[$key] ?? [];

		foreach ($value as $keys => $values) {
			$keys_arr = explode("-", $keys);
			$tag  = $keys_arr[0] ?? 'unset';
			$ind1 = $keys_arr[1] ?? '#';
			$ind2 = '#';

			if (is_array($values)) {
				// 1. Tambahkan $index pada foreach untuk menangkap urutan array-nya
				foreach ($values as $index => $val) {

					$fieldValue = '$a ' . trim($val ?? '');

					// ← ambil relator berdasarkan $index, bukan $ind1
					if ($key === 'pengarangTambahan') {

						// 2. Ganti (int) $ind1 menjadi $index
						$relatorKey = $index;

						$relator = !empty($pengarangTambahanRelator[$relatorKey])
							? trim($pengarangTambahanRelator[$relatorKey])
							: '';

						if (!empty($relator)) {
							$fieldValue .= ' $e ' . $relator;
						}
					}

					$result[$tag][] = [
						'Tag'        => $tag,
						'Indicator1' => $ind1,
						'Indicator2' => $ind2,
						'CatalogId'  => $catalog_id,
						'value'      => $fieldValue,
						'CreateBy'   => user_id(),
						'CreateDate' => $now,
						'UpdateBy'   => user_id(),
						'UpdateDate' => $now,
					];
				}
			}
		}
		unset($post[$key]);
	}

	foreach ($post as $key => $value) {
		$tag_indicator = tag_indicator($key, $isRDA);
		$tag  = $tag_indicator['tag'] ?? 'unset';
		$ind1 = $tag_indicator['ind1'] ?? '#';
		$ind2 = $tag_indicator['ind2'] ?? '#';

		if (is_array($value)) {
			foreach ($value as $keys => $values) {
				$result[$tag][] = [
					'Tag'        => $tag,
					'Indicator1' => $ind1,
					'Indicator2' => $ind2,
					'CatalogId'  => $catalog_id,
					'value'      => '$a ' . trim($values ?? ''),
					'CreateBy'   => user_id(),
					'CreateDate' => $now,
					'UpdateBy'   => user_id(),
					'UpdateDate' => $now,
				];
			}
		} else {
			$result[$tag][] = [
				'Tag'        => $tag,
				'Indicator1' => $ind1,
				'Indicator2' => $ind2,
				'CatalogId'  => $catalog_id,
				'value'      => '$a ' . trim($value ?? ''),
				'CreateBy'   => user_id(),
				'CreateDate' => $now,
				'UpdateBy'   => user_id(),
				'UpdateDate' => $now,
			];
		}
		unset($post[$key]);
	}

	return sort_array($result);
}
function sort_array($post)
{
	$sortedArray = [];

	foreach ($post as $value) {
		$sortedArray = array_merge($sortedArray, $value);
	}

	sort($sortedArray);

	return $sortedArray;
}


function parse_marc_value($value, $subfields, $separator = ' ')
{
	if (is_null($value)) {
		return '';
	}

	$subfields = is_array($subfields) ? $subfields : [$subfields];
	$result = [];
	$parts = explode('$', $value);

	foreach ($parts as $part) {
		if (empty($part)) continue;

		$subfield_char = substr($part, 0, 1);
		if (in_array($subfield_char, $subfields)) {
			$result[] = trim(substr($part, 1));
		}
	}

	return implode($separator, $result);
}

/**
 * Converts data from the catalog_ruas table into a flat array for the catalogs table.
 * @param int $CatalogId The ID of the catalog to convert.
 * @return array The data ready to be updated into the catalogs table.
 */
function convert_catalog_ruas($CatalogId)
{
	// Make sure you use the correct namespace for your DataModel
	$katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
	$ruas_data = $katalogRuasModel->where('CatalogId', $CatalogId)->findAll();

	if (empty($ruas_data)) {
		return [];
	}

	// Reorganize the data into an associative array with Tag as the key
	$marc = [];
	foreach ($ruas_data as $ruas) {
		// PERBAIKAN: Menggunakan sintaks object ($ruas->Tag) bukan array ($ruas['Tag'])
		$marc[$ruas->Tag] = $ruas->Value;
	}

	// Start mapping data to the 'catalogs' table columns
	$update_data = [];

	$update_data['ControlNumber'] = $marc['001'] ?? null;
	$update_data['BIBID'] = parse_marc_value($marc['035'] ?? null, 'a');

	// Combine subfields for the Title
	$title_a = parse_marc_value($marc['245'] ?? null, 'a');
	$title_b = parse_marc_value($marc['245'] ?? null, 'b');
	$title_c = parse_marc_value($marc['245'] ?? null, 'c');
	$update_data['Title'] = trim($title_a . ' ' . $title_b . ' ' . $title_c);

	$update_data['Author'] = parse_marc_value($marc['100'] ?? null, 'a');
	$update_data['Edition'] = parse_marc_value($marc['250'] ?? null, 'a');

	// Split and combine subfields for publication data from Tag 260
	$update_data['Publisher'] = parse_marc_value($marc['260'] ?? null, 'b');
	$update_data['PublishLocation'] = parse_marc_value($marc['260'] ?? null, 'a');
	$update_data['PublishYear'] = parse_marc_value($marc['260'] ?? null, 'c');
	$update_data['Publikasi'] = parse_marc_value($marc['260'] ?? null, ['a', 'b', 'c'], ' ');

	$update_data['Subject'] = parse_marc_value($marc['600'] ?? null, 'a');
	$update_data['PhysicalDescription'] = parse_marc_value($marc['300'] ?? null, 'a');
	$update_data['ISBN'] = parse_marc_value($marc['020'] ?? null, 'a');
	$update_data['CallNumber'] = parse_marc_value($marc['084'] ?? null, 'a');

	// Extract language code from Tag 008 (position 35, length 3)
	$tag_008_value = $marc['008'] ?? '';
	$update_data['Languages'] = (strlen($tag_008_value) >= 38) ? substr($tag_008_value, 35, 3) : null;

	$update_data['DeweyNo'] = parse_marc_value($marc['082'] ?? null, 'a');

	// Add any other default values needed for the 'catalogs' table
	$update_data['IsOPAC'] = 1;
	$update_data['CreateDate'] = date('Y-m-d H:i:s');
	$update_data['CreateBy'] = user_id();

	return $update_data;
}
