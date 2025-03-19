<?php

use Base\Models\BaseModel;
use Base\Models\DataModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function reloadPermission()
{
	delete_cache('display_menu', true);
	delete_cache('list_menu', true);
	// callAuthAPI(session()->get('username'), session()->get('password'));
}

function read_cache($file)
{
	if (@is_dir(@dirname($file = (WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $file))) && ($c = @unserialize(@file_get_contents($file))) && (isset($c->t) && isset($c->c)) && ($c->t == 0 || microtime(true) <= $c->t)) {
		return $c->c;
	}
	return false;
}
function write_cache($file, $content, $time = 0)
{
	if (!@is_dir($d = @dirname($file = (WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $file)))) @mkdir($d, 0777, true);
	if (($fp = @fopen($file, 'w'))) {
		$c = new stdClass;
		$c->t = ($time > 0) ? (microtime(true) + $time) : 0;
		$c->c = $content;
		flock($fp, LOCK_EX);
		fwrite($fp, serialize($c));
		flock($fp, LOCK_UN);
		fclose($fp);
		@chmod($file, 0777);
		return true;
	} else {
		return false;
	}
}
function delete_cache($file = null, $recursive = false)
{
	if ($file == null) {
		$r = function ($d, $x = true) use (&$r) {
			if (@is_dir($d) && ($o = @scandir($d))) {
				foreach ($o as $f) {
					if ($f != '.' && $f != '..') {
						if (@is_dir($n = ($d . DIRECTORY_SEPARATOR . $f)) && !@is_link($d . '/' . $f)) $r($n);
						else @unlink($n);
					}
				}
				if ($x) @rmdir($d);
			}
		};
		$r(WRITEPATH . 'cache', false);
		return true;
	} else if (@is_dir(@dirname($file = (WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $file)))) return ($recursive ? @array_map('unlink', glob($file . '*')) : @unlink($file));
	else return true;
}
function encData($data)
{
	$encrypter = service('encrypter');
	return urlsafeB64encode($encrypter->encrypt($data));
}
function decData($data)
{
	try {
		$encrypter = service('encrypter');
		return $encrypter->decrypt(urlsafeB64decode($data));
	} catch (\Exception $e) {
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	}
}
function urlsafeB64encode($string)
{
	$data = base64_encode($string);
	$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
	return $data;
}
function urlsafeB64decode($string)
{
	$data = str_replace(array('-', '_'), array('+', '/'), $string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}
function callGetAPI($uri_api)
{
	try {
		$result = json_decode(service('curlrequest', ['verify' => false])->request("get", $uri_api, [
			"headers" => [
				"Accept"        => "application/json",
				"Authorization" => 'Bearer ' . session()->get('token')
			]
		])->getBody());
		return $result;
	} catch (\Exception $e) {
		if ($e->getMessage() == 'Access expired') {
			try {
				$username = session()->get('username');
				$password = decData(session()->get('password'));
				$result   = json_decode(service('curlrequest', ['verify' => false])->request("post", env('API_AUTH'), [
					'form_params' => [
						'username' => $username,
						'password' => $password,
					]
				])->getBody());
				try {
					$payload = JWT::decode($result->token, (new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'))));
					session()->set('username', $username);
					session()->set('password', encData($password));
					session()->set('token', $result->token);
					session()->set('logged_in', $result->user->id);
					session()->set('branch_id', $result->user->branch_id);

					return callGetAPI($uri_api);
				} catch (\Exception $ex) {
					dd($ex->getMessage());
					throw $ex;
				}
			} catch (\Exception $e) {
				dd($e->getMessage());
				throw $e;
			}
		} else {
			dd($e->getMessage());
			throw $e;
		}
	}
}
function callPostAPI($uri_api, $form_params)
{
	try {
		$result = json_decode(service('curlrequest', ['verify' => false])->request("post", $uri_api, [
			'form_params' => $form_params,
			"headers"     => [
				"Accept"        => "application/json",
				"Authorization" => 'Bearer ' . session()->get('token')
			]
		])->getBody());
		return $result;
	} catch (\Exception $e) {
		if ($e->getMessage() == 'Access expired') {
			try {
				$username = session()->get('username');
				$password = decData(session()->get('password'));
				$result   = json_decode(service('curlrequest', ['verify' => false])->request("post", env('API_AUTH'), [
					'form_params' => [
						'username' => $username,
						'password' => $password,
					]
				])->getBody());

				try {
					$payload = JWT::decode($result->token, (new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'))));
					session()->set('username', $username);
					session()->set('password', encData($password));
					session()->set('token', $result->token);
					session()->set('logged_in', $result->user->id);
					session()->set('branch_id', $result->user->branch_id);

					return callPostAPI($uri_api, $form_params);
				} catch (\Exception $ex) {
					dd($ex->getMessage());
					throw $ex;
				}
			} catch (\Exception $e) {
				dd($e->getMessage());
				throw $e;
			}
		} else {
			dd($e->getMessage());
			throw $e;
		}
	}
}
function callAuthAPI($username, $password)
{
	
	try {
		
		$result = json_decode(service('curlrequest', ['verify' => false])->request("post", env('API_AUTH'), [
			'form_params' => [
				'username' => $username,
				'password' => $password,
			]
		])->getBody());
		
		try {
			$payload = JWT::decode($result->token, (new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'))));
			session()->set('username', $username);
			session()->set('password', $password);
			session()->set('token', $result->token);
			session()->set('logged_in', $result->user->id);
			session()->set('branch_id', $result->user->branch_id);
			session()->set('member_id', $result->user->member_id);
			session()->set('auth_permissions', $result->auth_permissions);
			session()->set('user', $result->user);
			session()->set('payload', $payload);

			return true;
		} catch (\Exception $ex) {
			dd($ex->getMessage());
			return false;
		}
	} catch (\Exception $e) {
		dd($e->getMessage());
		return false;
	}
}
function get_parameter($paramName, $defaultValue = null)
{
	$parameter = get_single('c_parameters', 'id, name, value', 'name="' . $paramName . '"');
	return $parameter->value ?? $defaultValue;
}
function is_profiling()
{
	return get_parameter('branch') == 1;
}
function is_member($group_name, $user_id = null)
{
	$user_id = $user_id ?? login_id();
	$groupModel = new BaseModel('auth_groups');
	$groupModel->where('name', $group_name);
	$group = $groupModel->first();

	if (!empty($group)) {
		$groupUserModel = new BaseModel('auth_groups_users');
		$groupUserModel->where('group_id', $group->id);
		$groupUserModel->where('user_id', $user_id);
		return $groupUserModel->countAllResults() > 0;
	}

	return false;
}
function is_ajax()
{
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
function is_allowed($uri, $user_id = null)
{
	if (getenv('app.checkURL') == 'false') {
		return true;
	}

	if (is_ajax()) {
		return true;
	}
	$uri     = str_replace('index.php/', '', $uri); // Ternyata uri bawa index.php, jadi harus di remove terlebih dahulu
	$user_id = $user_id ?? login_id();

	if (is_profiling()) {
		$auth_permissions = (array) session()->get('auth_permissions');
		$terpilih		  = [];
		if (!isset($auth_permissions[$uri])) { // ngak nemu uri terhadap permissionnya
			if (str_ends_with($uri, '/')) { // jika uri end with /
				if (isset($auth_permissions[$uri . 'index'])) { // tambahkan urinya/index
					$terpilih = $auth_permissions[$uri . 'index']; // ada permission terpilih berdasarkan uri
				}
			} else {
				if (isset($auth_permissions[$uri . '/index'])) { // tambahkan urinya/index
					$terpilih = $auth_permissions[$uri . '/index']; // ada permission terpilih berdasarkan uri
				}
			}
		} else {
			$terpilih = $auth_permissions[$uri]; // ada permission terpilih berdasarkan uri
		}
		if (in_array($user_id, $terpilih)) { // Jika terpilih ada, dan user_id ada di terpilih berarti di allowed
			return true;
		} else {
			foreach ($auth_permissions as $key => $auth_permission) { // loop semua permission
				if (in_array($user_id, $auth_permission)) { // check yang mengandung permissionnya terhadap idnya
					if (str_starts_with($uri, $key)) { // baru cek urinya start ngak dengan yang dari permission
						return true;
					}
				}
			}
		}
	} else {
		$auth = \Myth\Auth\Config\Services::authentication();
		$authorize = \Myth\Auth\Config\Services::authorization();

		if ($auth->check()) {
			if (is_member('admin') || is_member('sa_pus')) {
				return true;
			} else {
				return $authorize->hasPermission($uri, $user_id);
			}
		}
	}
	return false;
}
function user()
{
	if (is_profiling()) {
		return session()->get('user');
	} else {
		return user();
	}
}
function groups()
{
	$model = new BaseModel('auth_groups');
	return $model->findAll();
}
function groups_users($user_id = null)
{
	$user_id = $user_id ?? login_id();
	$model = new BaseModel('auth_groups_users');
	$groups_users = $model->where('user_id', $user_id)->findAll();
	$data = [];
	foreach ($groups_users as $group_user) {
		$data[] = $group_user->group_id;
	}
	return $data;
}

function permissions_users($user_id = null, $group_id = null)
{
	$model = new BaseModel('auth_groups_permissions');
	if (!empty($group_id)) {
		$model = $model->where('group_id', $group_id);
	} else {
		$model = $model->whereIn('group_id', groups_users($user_id));
	}

	$permissions_users = $model->findAll();
	$data = [];
	foreach ($permissions_users as $permission_user) {
		$data[] = $permission_user->permission_id;
	}
	return $data;
}

function branch_id()
{
	return user()->branch_id ?? 0;
}

function npp_kabkota_id()
{
	return user()->npp_kabkota_id ?? 0;
}

function npp_kecamatan_id()
{
	return user()->npp_kecamatan_id ?? 0;
}

function npp_kelurahan_id()
{
	return user()->npp_kelurahan_id ?? 0;
}

function login_id()
{
	return session()->get('logged_in') ?? 0;
}
function logged_in()
{
	return session()->has('logged_in');
}

function get_menu_item($title, $url = '#', $active = false, $icon = null, $heading = false)
{
	return [
		"title" => $title,
		"url" => $url,
		"active" => $active ?? false,
		"icon" => (!empty($icon)) ? $icon : "check-circle2",
		"heading" => $heading,
	];
}
function set_message($name, $value)
{
	$session = service('session');
	$session->setFlashdata($name, $value);
}
function get_message($name)
{
	$session = service('session');
	return $session->getFlashdata($name);
}
function getClientIpAddress()
{
	return ('127.0.0.1');
}
function dd($object)
{
	echo '<pre>';
	print_r($object);
	echo '</pre>';
	exit();
}
function ddd($object)
{
	print_r($object);
	exit();
}

/*
try {
    // Call auth
    if(callAuthAPI($userma, $pass)) {
        $user_id = session()->get('logged_in');
    }
    $result_get  = callGetAPI($api_provisi);
    $result_post = callPostAPI($api_provisi,['param1'=>'value1', 'param2'=>'value2']);
} catch (\Exception $e) { throw $e; }
*/
function get_single($table, $fields = 'id', $where = '1=1', $dbGroup = 'default')
{
	$builder = ($dbGroup == 'default') ? new BaseModel($table) : new DataModel($table);
	$query = $builder->select($fields);

	if (!empty($where)) {
		$query->where($where);
	}

	return $query->get()->getRow();
}
function get_table($table, $fields = 'id', $where = '1=1', $dbGroup = 'default')
{
	$builder = ($dbGroup == 'data') ? new DataModel($table) : new BaseModel($table);
	$query = $builder->select($fields);
	$query->distinct();


	if (!empty($where)) {
		$query->where($where);
	}

	return $query->get()->getResult();
}
function count_all($table, $where = '1=1', $dbGroup = 'default')
{
	$builder = ($dbGroup == 'data') ? new DataModel($table) : new BaseModel($table);
	$query = $builder;

	if (!empty($where)) {
		$query->where($where);
	}

	return $query->countAllResults();
}
function is_exist($table, $where = '1=1', $dbGroup = 'default')
{
	return count_all($table, $where, $dbGroup) > 0;
}
function get_object_array($objects, $field)
{
	$result = [];
	foreach ($objects as $row) {
		$result[] = strtoupper($row->$field);
	}

	return $result;
}
function get_setting_parameter($paramName, $branch = false)
{
	$response = '';
	$tableName = $branch ? 'settingparameters_branchs' : 'settingparameters';
	$builder = new DataModel($tableName);
	$query = $builder->where('Name', $paramName);

	if ($branch) {
		$query->where('Branch_id', user()->branch_id);
	}

	$param = $query->get()->getRow();

	if ($param) {
		$response = $param->Value;
	} else {
		$defaultTableName = 'settingparameters';
		$defaultBuilder = new DataModel($defaultTableName);
		$defaultQuery = $defaultBuilder->where('Name', $paramName);
		$defaultParam = $defaultQuery->get()->getRow();

		$response = $defaultParam ? $defaultParam->Value : null;
	}
	return $response;
}
function set_setting_parameter($paramName, $paramValue, $branch = false)
{
	$tableName = $branch ? 'settingparameters_branchs' : 'settingparameters';
	$builder = new DataModel($tableName);
	$query = $builder->where('Name', $paramName);

	if ($branch) {
		$query->where('Branch_id', user()->branch_id);
	}

	$param = $query->get()->getRow();

	if (empty($param)) {
		$save_data = array('Name' => $paramName, 'Value' => $paramValue);
		if ($branch) {
			$save_data['Branch_id'] = user()->branch_id;
		}
		$builder->insert($save_data);

		return true;
	} else {
		$update_data = array('Name' => $paramName, 'Value' => $paramValue);
		if ($branch) {
			$update_data['Branch_id'] = user()->branch_id;
		}
		$builder->update($param->ID, $update_data);
		return true;
	}
}
