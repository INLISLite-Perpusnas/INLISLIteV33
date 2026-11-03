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
				// Tanda baca penghubung subruas (" :", " /", ",") tidak ikut tampil di form
				$result[$keys[$i]] = marc_clean_tanda_baca($tag, $values[$i]);
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

if (!function_exists('marc_field_columns')) {
	/**
	 * Kolom fields untuk form MARC. Kolom bit(1) di-cast agar bernilai 0/1 biasa.
	 * Enabled = isian boleh diketik, IsCustomable = ruas tetap diisi lewat form per posisi.
	 */
	function marc_field_columns()
	{
		return 'f.ID, f.Tag, f.Name, f.Length, CAST(f.Mandatory AS UNSIGNED) AS Mandatory, CAST(f.Fixed AS UNSIGNED) AS Fixed, '
			. 'CAST(f.Repeatable AS UNSIGNED) AS Repeatable, CAST(f.Enabled AS UNSIGNED) AS Enabled, CAST(f.IsCustomable AS UNSIGNED) AS IsCustomable';
	}
}

if (!function_exists('marc_default_value')) {
	/**
	 * Isian awal satu ruas di form MARC (setara actionCreateTaglistClean INLISLite 3):
	 * ruas tetap kosong kecuali 007 "ta" (jenis bahan 1) dan 008 dari nilai bawaan unsurnya;
	 * ruas biasa "$a " (+ "$2 rda..." untuk 336/337/338).
	 */
	function marc_default_value(array $field, $worksheet_id)
	{
		if ((int) ($field['Fixed'] ?? 0) === 1) {
			if ($field['Tag'] === '007' && (int) $worksheet_id === 1) {
				return 'ta';
			}
			if ($field['Tag'] === '008') {
				$items = db_connect()->table('worksheetfielditems wfi')
					->select('wfi.StartPosition, wfi.Length, wfi.DefaultValue')
					->join('worksheetfields wf', 'wf.ID = wfi.WorksheetField_id')
					->where('wf.Worksheet_id', (int) $worksheet_id)
					->where('wf.Field_id', (int) $field['ID'])
					->orderBy('wfi.StartPosition', 'ASC')
					->get()->getResult();
				$value = '';
				foreach ($items as $item) {
					$part = (int) $item->StartPosition === 0 ? date('ymd') : (string) $item->DefaultValue;
					$value .= str_pad(substr($part, 0, (int) $item->Length), (int) $item->Length, '#');
				}
				return $value;
			}
			return '';
		}

		$extra = ['336' => '$2 rdacontent', '337' => '$2 rdamedia', '338' => '$2 rdacarrier'];
		return '$a ' . ($extra[$field['Tag']] ?? '');
	}
}

if (!function_exists('get_all_tags')) {
	function get_all_tags($worksheet_id = '1')
	{
		$db = db_connect();
		$session = service('session');
		// Data session versi lama belum memuat flag Enabled/IsCustomable: muat ulang
		$cached = $session->get('worksheet_fields');
		if (is_array($cached) && $cached && !array_key_exists('IsCustomable', reset($cached))) {
			$session->remove('worksheet_fields');
		}
		if (!$session->has('worksheet_fields')) {
			$builder = $db->table('worksheetfields as wsf')
				->select(marc_field_columns(), false)
				->join('fields as f', 'f.ID = wsf.Field_id')
				->where('wsf.Worksheet_id', $worksheet_id)
				 ->orderBy('f.Tag', 'ASC');
			$worksheet_fields = $builder->get()->getResultArray();
			$session->set('worksheet_fields', $worksheet_fields);
		}
		$session_tags = $session->get('worksheet_fields');
		$builder2 = $db->table('fields as f')->select(marc_field_columns(), false)->orderBy('f.Tag', 'ASC');
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
			->select(marc_field_columns(), false)
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

		$builder2 = $db->table('fields as f')->select(marc_field_columns(), false);
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
			->select(marc_field_columns(), false)
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
			// Ambil angka terbesar secara numerik, bukan MAX() string: nomor dengan jumlah nol
			// berbeda (mis. 'INLIS00000000145080' vs 'INLIS000000000145081') membuat MAX() string
			// selalu mengembalikan nilai yang sama sehingga nomor kendali baru jadi ganda.
			$query2 = $db->query("SELECT MAX(CAST(SUBSTRING(ControlNumber, 6) AS UNSIGNED)) AS MaxControlNumber FROM catalogs WHERE ControlNumber REGEXP '^INLIS[0-9]+$'");
			$controlNumber = (int) ($query2->getRow()->MaxControlNumber ?? 0);
			$newControlNumber =  'INLIS' . str_pad($controlNumber + 1, 15, '0', STR_PAD_LEFT);
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


/*
 * ------------------------------------------------------------------
 * PENYIMPANAN RUAS KATALOG
 * Mengikuti alur INLISLite 3 (pengkatalogan/KatalogController::actionSave,
 * actionSaveRuas, actionCreateTaglistSimple, actionCreateTaglistAdvance dan
 * CatalogHelpers::convertToCatalogFields). Hanya tabel catalogs & catalog_ruas.
 * ------------------------------------------------------------------
 */

if (!function_exists('katalog_system_tags')) {
	/**
	 * Tag yang selalu diisi sistem saat simpan, bukan dari input form:
	 * 001 (ControlNumber), 005 (waktu update), 035 (BIBID), 990 (No. Induk eksemplar).
	 */
	function katalog_system_tags()
	{
		return ['001', '005', '035', '990'];
	}
}

if (!function_exists('marc_tanda_baca')) {
	/**
	 * Tanda baca (fielddatas.Delimiter) yang mendahului tiap subruas suatu tag,
	 * mis. 245 => ['b' => ' : ', 'c' => ' /'].
	 */
	function marc_tanda_baca($tag)
	{
		static $cache = [];

		if (!isset($cache[$tag])) {
			$rows = db_connect()->table('fielddatas')
				->select('fielddatas.Code, fielddatas.Delimiter')
				->join('fields', 'fields.ID = fielddatas.Field_id')
				->where('fields.Tag', $tag)
				->get()->getResult();

			$cache[$tag] = [];
			foreach ($rows as $row) {
				if (trim((string) $row->Delimiter) !== '') {
					$cache[$tag][$row->Code] = (string) $row->Delimiter;
				}
			}
		}

		return $cache[$tag];
	}
}

if (!function_exists('marc_clean_tanda_baca')) {
	/**
	 * Buang tanda baca milik tag di akhir nilai subruas (setara getRegexPatternCleanTandaBaca).
	 */
	function marc_clean_tanda_baca($tag, $value)
	{
		$value = trim((string) $value);
		// Diulang sampai stabil agar tanda baca ganda (mis. "Jakarta : :") ikut bersih
		do {
			$before = $value;
			foreach (marc_tanda_baca($tag) as $delimiter) {
				$value = trim(preg_replace('/' . preg_quote(trim($delimiter), '/') . '\z/', '', $value));
			}
		} while ($value !== $before && $value !== '');
		return $value;
	}
}

if (!function_exists('marc_parse_subfields')) {
	/**
	 * Pecah nilai ruas "$a ... $b ..." menjadi [[kode, nilai], ...] sesuai urutan.
	 * Nilai tanpa "$" dianggap subruas $a.
	 */
	function marc_parse_subfields($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return [];
		}
		if (strpos($value, '$') === false) {
			return [['a', $value]];
		}

		$subfields = [];
		foreach (explode('$', $value) as $i => $part) {
			if ($i === 0) {
				if (trim($part) !== '') {
					$subfields[] = ['a', $part];
				}
				continue;
			}
			$code = substr($part, 0, 1);
			if ($code === '' || $code === false) {
				continue;
			}
			$subfields[] = [$code, (string) substr($part, 1)];
		}

		return $subfields;
	}
}

if (!function_exists('marc_build_value')) {
	/**
	 * Susun nilai ruas dari daftar subruas lengkap dengan tanda baca dari fielddatas,
	 * mis. 245 => "$a Judul : $b Anak judul /$c Penanggung jawab".
	 */
	function marc_build_value($tag, array $subfields)
	{
		$tandaBaca = marc_tanda_baca($tag);
		$output = '';

		foreach ($subfields as [$code, $value]) {
			$value = marc_clean_tanda_baca($tag, $value);
			if ($value === '') {
				continue;
			}
			if ($output !== '' && isset($tandaBaca[$code])) {
				$output .= $tandaBaca[$code];
			}
			$output .= '$' . $code . ' ' . $value;
		}

		return $output;
	}
}

if (!function_exists('marc_value_is_empty')) {
	/**
	 * Ruas dianggap kosong bila tidak ada isi selain kode subruas (mis. "$a").
	 */
	function marc_value_is_empty($value)
	{
		return trim(preg_replace('/\$\w/', '', (string) $value)) === '';
	}
}

if (!function_exists('build_tag_008')) {
	/**
	 * Susun tag 008 (40 karakter): 00-05 tanggal entri, 22 kelompok sasaran,
	 * 33 bentuk karya tulis, 35-37 bahasa. Bila sudah ada 008 lama, posisi lain dipertahankan.
	 */
	function build_tag_008(array $languages, $existing = null)
	{
		// Data yang sempat tersimpan dengan awalan "$a " dibersihkan dulu
		$existing = preg_replace('/^\$a\s*/', '', (string) $existing);
		$value = strlen($existing) >= 40 ? $existing : date('ymd') . str_repeat('#', 34);

		$ks   = trim((string) ($languages['ks'] ?? ''));
		$bkt  = trim((string) ($languages['bkt'] ?? ''));
		$lang = trim((string) ($languages['lang'] ?? ''));

		$value = substr_replace($value, $ks !== '' ? substr($ks, 0, 1) : '|', 22, 1);
		$value = substr_replace($value, $bkt !== '' ? substr($bkt, 0, 1) : '|', 33, 1);
		$value = substr_replace($value, $lang !== '' ? str_pad(substr($lang, 0, 3), 3, '#') : '|||', 35, 3);

		return $value;
	}
}

if (!function_exists('build_simple_ruas')) {
	/**
	 * Bangun daftar ruas dari POST form entri sederhana (setara actionCreateTaglistSimple).
	 * Saat edit, ruas yang tidak dikelola form sederhana (mis. 040, 041, 090, 542) dipertahankan.
	 *
	 * @param array $existing Baris catalog_ruas lama (object) untuk mode edit
	 * @return array [['Tag', 'Indicator1', 'Indicator2', 'Value'], ...]
	 */
	function build_simple_ruas(array $post, bool $isRDA, int $worksheetId, array $existing = [])
	{
		$rows = [];
		$add = function ($tag, $ind1, $ind2, array $subfields) use (&$rows) {
			$value = marc_build_value($tag, $subfields);
			if ($value !== '') {
				$rows[] = ['Tag' => $tag, 'Indicator1' => $ind1, 'Indicator2' => $ind2, 'Value' => $value];
			}
		};
		$list = function ($value) {
			return array_values(array_filter(array_map('trim', (array) ($value ?? [])), 'strlen'));
		};
		$ind = function ($value, $default = '#') {
			$value = trim((string) $value);
			return $value === '' ? $default : substr($value, 0, 1);
		};

		// Tajuk pengarang utama (1XX tidak berulang: ambil isian pertama)
		$mainAuthor = null;
		foreach ((array) ($post['pengarangUtama'] ?? []) as $key => $names) {
			$parts = explode('-', (string) $key);
			foreach ($list($names) as $name) {
				if ($mainAuthor === null) {
					$tag = in_array($parts[0], ['100', '110', '111'], true) ? $parts[0] : '100';
					$mainAuthor = [$tag, $ind($parts[1] ?? ''), $name];
				}
			}
		}

		// 245 Judul: ind1 = 1 bila ada pengarang utama
		$judul = (array) ($post['judul'] ?? []);
		$add('245', $mainAuthor ? '1' : '#', '#', [
			['a', $judul['a'] ?? ''],
			['b', $judul['b'] ?? ''],
			['c', $judul['c'] ?? ''],
		]);

		foreach ($list($post['variasiBentukJudul'] ?? []) as $v) {
			$add('246', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['judulSeragam'] ?? []) as $v) {
			$add('240', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['judulSebelumnya'] ?? []) as $v) {
			$add('247', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['judulAsli'] ?? []) as $v) {
			$add('740', '#', '#', [['a', $v]]);
		}

		if ($mainAuthor) {
			$add($mainAuthor[0], $mainAuthor[1], '#', [['a', $mainAuthor[2]]]);
		}

		// Tajuk pengarang tambahan: key "TAG-IND1-BARIS" (atau "TAG-IND1"), relator ($e) per baris
		$relators = (array) ($post['pengarangTambahanRelator'] ?? []);
		foreach ((array) ($post['pengarangTambahan'] ?? []) as $key => $names) {
			$parts = explode('-', (string) $key);
			$tag   = in_array($parts[0], ['700', '710', '711'], true) ? $parts[0] : '700';
			foreach ((array) $names as $index => $name) {
				$relator = isset($parts[2]) ? ($relators[$parts[2]] ?? '') : ($relators[$index] ?? '');
				$add($tag, $ind($parts[1] ?? ''), '#', [['a', $name], ['e', $relator]]);
			}
		}

		// Publikasi: 264 untuk RDA, 260 untuk AACR
		$penerbit = (array) ($post['penerbit'] ?? []);
		$add($isRDA ? '264' : '260', '#', '#', [
			['a', $penerbit['a'] ?? ''],
			['b', $penerbit['b'] ?? ''],
			['c', $penerbit['c'] ?? ''],
		]);

		foreach ($list($post['frekuensi'] ?? []) as $v) {
			$add('310', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['frekuensiSebelumnya'] ?? []) as $v) {
			$add('321', '#', '#', [['a', $v]]);
		}

		$fisik = (array) ($post['PhysicalDescription'] ?? []);
		$add('300', '#', '#', [
			['a', $fisik['a'] ?? ''],
			['b', $fisik['b'] ?? ''],
			['c', $fisik['c'] ?? ''],
			['e', $fisik['e'] ?? ''],
		]);

		foreach (['336' => ['jenisIsi', 'rdacontent'], '337' => ['jenisMedia', 'rdamedia'], '338' => ['jenisWadah', 'rdacarrier']] as $tag => [$field, $source]) {
			foreach ($list($post[$field] ?? []) as $v) {
				$add($tag, '#', '#', [['a', $v], ['2', $source]]);
			}
		}

		foreach ($list($post['Edition'] ?? []) as $v) {
			$add('250', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['DeweyNo'] ?? []) as $v) {
			$add('082', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['CallNumber'] ?? []) as $v) {
			$add('084', '#', '#', [['a', $v]]);
		}
		foreach ($list($post['ISBN'] ?? []) as $v) {
			$add('020', '#', '#', [['a', $v]]);
		}

		// Subjek: ind2 = 4 (sumber tidak disebutkan)
		foreach ((array) ($post['subject'] ?? []) as $key => $values) {
			$parts = explode('-', (string) $key);
			$tag   = in_array($parts[0], ['600', '650', '651'], true) ? $parts[0] : '650';
			foreach ($list($values) as $v) {
				$add($tag, $tag === '600' ? $ind($parts[1] ?? '') : '#', '4', [['a', $v]]);
			}
		}

		// Catatan
		foreach ((array) ($post['catatan'] ?? []) as $key => $values) {
			$parts = explode('-', (string) $key);
			$tag   = in_array($parts[0], ['500', '502', '504', '505', '520'], true) ? $parts[0] : '520';
			foreach ($list($values) as $v) {
				$add($tag, '#', '#', [['a', $v]]);
			}
		}

		foreach ($list($post['LocCollDaring'] ?? []) as $v) {
			$add('856', '#', '#', [['a', $v]]);
		}

		// Ruas lama di luar cakupan form sederhana tetap dibawa saat edit
		$managed = [
			'245', '246', '240', '247', '740', '100', '110', '111', '700', '710', '711',
			'260', '264', '310', '321', '300', '336', '337', '338', '250', '082', '084',
			'020', '600', '650', '651', '500', '502', '504', '505', '520', '856', '008',
		];
		$existing008 = null;
		$has006 = $has007 = false;
		foreach ($existing as $ruas) {
			if ($ruas->Tag === '008') {
				$existing008 = $existing008 ?? $ruas->Value;
			}
			if (in_array($ruas->Tag, $managed, true) || in_array($ruas->Tag, katalog_system_tags(), true)) {
				continue;
			}
			$has006 = $has006 || $ruas->Tag === '006';
			$has007 = $has007 || $ruas->Tag === '007';
			$rows[] = [
				'Tag'        => $ruas->Tag,
				'Indicator1' => $ruas->Indicator1,
				'Indicator2' => $ruas->Indicator2,
				'Value'      => $ruas->Value,
			];
		}

		// Nilai bawaan ruas tetap (006, 007, 008)
		if (!$has006) {
			$rows[] = ['Tag' => '006', 'Indicator1' => null, 'Indicator2' => null, 'Value' => 'a################'];
		}
		if (!$has007 && $worksheetId == 1) {
			$rows[] = ['Tag' => '007', 'Indicator1' => null, 'Indicator2' => null, 'Value' => 'ta'];
		}
		$rows[] = [
			'Tag'        => '008',
			'Indicator1' => null,
			'Indicator2' => null,
			'Value'      => build_tag_008((array) ($post['Languages'] ?? []), $existing008),
		];

		// Urutkan berdasarkan tag (stabil: urutan dalam tag yang sama tetap)
		usort($rows, function ($a, $b) {
			return strcmp($a['Tag'], $b['Tag']);
		});

		return $rows;
	}
}

if (!function_exists('build_marc_ruas')) {
	/**
	 * Bangun daftar ruas dari POST form MARC (setara actionCreateTaglistAdvance):
	 * ruas kosong dibuang, tanda baca subruas dirapikan, tag sistem diabaikan.
	 *
	 * @return array [['Tag', 'Indicator1', 'Indicator2', 'Value'], ...]
	 */
	function build_marc_ruas(array $values, array $indicator1s = [], array $indicator2s = [])
	{
		$rows = [];

		foreach ($values as $tag => $tagValues) {
			$tag = (string) $tag;
			if (in_array($tag, katalog_system_tags(), true)) {
				continue;
			}
			$isControl = ctype_digit($tag) && (int) $tag < 10;

			foreach ((array) $tagValues as $index => $value) {
				$value = trim((string) $value);
				if (marc_value_is_empty($value)) {
					continue;
				}

				if ($isControl) {
					$rows[] = ['Tag' => $tag, 'Indicator1' => null, 'Indicator2' => null, 'Value' => $value];
					continue;
				}

				$value = marc_build_value($tag, marc_parse_subfields($value));
				if ($value === '') {
					continue;
				}

				$ind1 = $indicator1s[$tag] ?? '';
				$ind2 = $indicator2s[$tag] ?? '';
				$ind1 = trim((string) (is_array($ind1) ? ($ind1[$index] ?? '') : $ind1));
				$ind2 = trim((string) (is_array($ind2) ? ($ind2[$index] ?? '') : $ind2));

				$rows[] = [
					'Tag'        => $tag,
					'Indicator1' => $ind1 === '' ? '#' : substr($ind1, 0, 1),
					'Indicator2' => $ind2 === '' ? '#' : substr($ind2, 0, 1),
					'Value'      => $value,
				];
			}
		}

		return $rows;
	}
}

if (!function_exists('save_katalog_ruas')) {
	/**
	 * Hapus lalu simpan ulang catalog_ruas (setara actionSaveRuas): 001, 005, 035 dari sistem,
	 * lalu ruas input, lalu 990 dari No. Induk eksemplar katalog. Sequence diisi berurutan.
	 * Panggil di dalam transaksi db_connect().
	 */
	function save_katalog_ruas($catalogId, $controlNumber, $bibid, array $rows)
	{
		$db       = db_connect();
		$now      = date('Y-m-d H:i:s');
		$userId   = user_id();
		$terminal = get_ip_address();
		$branchId = user()->branch_id ?? null;

		$noInduk = [];
		$collections = $db->table('collections')->select('NoInduk')
			->where('Catalog_id', $catalogId)->orderBy('ID', 'ASC')
			->get()->getResult();
		foreach ($collections as $coll) {
			if (trim((string) $coll->NoInduk) !== '') {
				$noInduk[] = ['Tag' => '990', 'Indicator1' => '#', 'Indicator2' => '#', 'Value' => '$a ' . trim($coll->NoInduk)];
			}
		}

		$all = array_merge(
			[
				['Tag' => '001', 'Indicator1' => null, 'Indicator2' => null, 'Value' => $controlNumber],
				['Tag' => '005', 'Indicator1' => null, 'Indicator2' => null, 'Value' => date('YmdHis')],
				['Tag' => '035', 'Indicator1' => '#', 'Indicator2' => '#', 'Value' => '$a ' . $bibid],
			],
			$rows,
			$noInduk
		);

		$batch = [];
		$seq = 1;
		foreach ($all as $row) {
			$batch[] = [
				'CatalogId'      => $catalogId,
				'Tag'            => $row['Tag'],
				'Indicator1'     => $row['Indicator1'],
				'Indicator2'     => $row['Indicator2'],
				'Value'          => $row['Value'],
				'Sequence'       => $seq++,
				'CreateBy'       => $userId,
				'CreateDate'     => $now,
				'CreateTerminal' => $terminal,
				'UpdateBy'       => $userId,
				'UpdateDate'     => $now,
				'UpdateTerminal' => $terminal,
				'Branch_id'      => $branchId,
			];
		}

		$db->table('catalog_ruas')->where('CatalogId', $catalogId)->delete();
		$db->table('catalog_ruas')->insertBatch($batch);

		return $all;
	}
}

if (!function_exists('ruas_to_catalog_fields')) {
	/**
	 * Petakan ruas ke kolom tabel catalogs (setara CatalogHelpers::convertToCatalogFields).
	 * Kolom yang tidak punya ruas dikosongkan agar tidak menyimpan nilai basi.
	 */
	function ruas_to_catalog_fields(array $rows)
	{
		$strip = function ($value) {
			return trim(preg_replace('/\s+/', ' ', preg_replace('/\$\w/', ' ', (string) $value)));
		};

		$title = null;
		$languages = null;
		$list = array_fill_keys([
			'Author', 'Edition', 'Publikasi', 'PublishLocation', 'Publisher', 'PublishYear',
			'Subject', 'PhysicalDescription', 'ISBN', 'CallNumber', 'Note', 'DeweyNo',
		], []);

		foreach ($rows as $row) {
			$tag   = $row['Tag'];
			$value = (string) $row['Value'];

			switch ($tag) {
				case '245':
					$title = $title ?? $strip($value);
					break;
				case '100':
				case '110':
				case '111':
				case '700':
				case '710':
				case '711':
					$a = marc_clean_tanda_baca($tag, parse_marc_value($value, 'a'));
					$e = marc_clean_tanda_baca($tag, parse_marc_value($value, 'e'));
					if ($a !== '') {
						$list['Author'][] = $a . ($e !== '' ? ' (' . $e . ')' : '');
					}
					break;
				case '250':
					$list['Edition'][] = $strip($value);
					break;
				case '260':
				case '264':
					foreach (marc_parse_subfields($value) as [$code, $sub]) {
						$sub = marc_clean_tanda_baca($tag, $sub);
						if ($sub === '') {
							continue;
						}
						if ($code === 'a') $list['PublishLocation'][] = $sub;
						if ($code === 'b') $list['Publisher'][] = $sub;
						if ($code === 'c') $list['PublishYear'][] = $sub;
					}
					$list['Publikasi'][] = $strip($value);
					break;
				case '600':
				case '650':
				case '651':
					$list['Subject'][] = $strip($value);
					break;
				case '300':
					$list['PhysicalDescription'][] = $strip($value);
					break;
				case '020':
				case '022':
				case '024':
					$list['ISBN'][] = $strip($value);
					break;
				case '084':
					$list['CallNumber'][] = $strip($value);
					break;
				case '500':
				case '502':
				case '504':
				case '505':
				case '520':
				case '542':
					$list['Note'][] = $strip($value);
					break;
				case '082':
					$list['DeweyNo'][] = $strip($value);
					break;
				case '008':
					if (strlen($value) >= 40) {
						$languages = substr($value, 35, 3);
					}
					break;
			}
		}

		$separators = [
			'Author' => ' ; ', 'Edition' => ' ; ', 'Publikasi' => ';', 'PublishLocation' => ';',
			'Publisher' => ';', 'PublishYear' => ';', 'Subject' => ' -- ', 'PhysicalDescription' => ' ',
			'ISBN' => ' ; ', 'CallNumber' => ' ; ', 'Note' => ' ; ', 'DeweyNo' => ' ; ',
		];
		$maxLength = [
			'Title' => 700, 'Author' => 700, 'Edition' => 255, 'Publikasi' => 700, 'PublishLocation' => 255,
			'Publisher' => 255, 'PublishYear' => 255, 'Subject' => 700, 'PhysicalDescription' => 255,
			'ISBN' => 700, 'CallNumber' => 700, 'Languages' => 255, 'DeweyNo' => 255,
		];

		$result = ['Title' => (string) $title];
		foreach ($list as $field => $values) {
			$values = array_filter($values, 'strlen');
			$result[$field] = $values ? implode($separators[$field], $values) : null;
		}
		$result['Languages'] = $languages;

		foreach ($maxLength as $field => $length) {
			if ($result[$field] !== null) {
				$result[$field] = mb_substr($result[$field], 0, $length);
			}
		}

		return $result;
	}
}

