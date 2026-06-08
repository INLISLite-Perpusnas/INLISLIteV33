<?php

namespace Base\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Config\Database as ConfigDatabase;
use Config\GroceryCrud as ConfigGroceryCrud;
use GroceryCrud\Core\GroceryCrud;

class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var CLIRequest|IncomingRequest
	 */
	public $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	public $helpers = [];

	public $data = [];
	public $response;
	public $session;
	public $validation;
	public $language;
	public $uploadPath;
	public $uploadFile;
	public $dataTable;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		// Preload any models, libraries, etc, here.
		helper(['url', 'text', 'form', 'html', 'file', 'cookie', 'auth', 'app', 'common']);

		// E.g.: $this->session = \Config\Services::session();
		$this->session = \Config\Services::session();
		$this->request = \Config\Services::request();
		$this->response = \Config\Services::response();
		$this->validation = \Config\Services::validation();
		$this->language = \Config\Services::language();
		$this->language->setLocale($this->session->lang);

		$this->uploadPath = WRITEPATH . 'uploads/';
		$this->uploadFile = new \CodeIgniter\Files\File($this->uploadPath);

		// dd(str_replace(base_url(), '', current_url()));
	}
	public function do_upload()
	{
		$response = [
			'success' => false,
			'data' => '',
			'msg' => "Image has not been uploaded successfully"
		];

		// $validated = $this->validation->validate([
		// 	'file' => [
		// 		'uploaded[file]',
		// 		// 'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
		// 		// 'max_size[file,10240]',
		// 	],
		// ]);

		$validated = true;

		if ($validated) {
			$file = $this->request->getFile('file');
			$file->move($this->uploadPath);

			$data = [
				'name' =>  $file->getClientName(),
				'type'  => $file->getClientMimeType(),
			];

			$response = [
				'success' => true,
				'data' => $data,
				'msg' => "Image has been uploaded successfully"
			];
		}

		return $this->response->setJSON($response);
	}

	public function do_delete()
	{
		$response = [
			'success' => false,
			'data' => '',
			'msg' => "Image has not been deleted successfully"
		];

		$name = $this->request->getPost('name');
		$path = $this->request->getPost('path');
		$file = $path  . $name;

		if (unlink($file)) {
			$response = [
				'success' => true,
				'data' => '',
				'msg' => "Image has been deleted successfully"
			];
		}

		return $this->response->setJSON($response);
	}

	public function flip($file)
	{
		$data['file'] = $file;
		echo view('layout/flip', $data);
	}

	public function _getGroceryCrudEnterprise($database = 'default')
	{
		$dbConfig = (new ConfigDatabase())->$database;
		$db = ['adapter' => [
			'driver' => 'Pdo_Mysql',
			'host'     => $dbConfig['hostname'],
			'database' => $dbConfig['database'],
			'username' => $dbConfig['username'],
			'password' => $dbConfig['password'],
			'charset' => 'utf8'
		]];

		$config = (new ConfigGroceryCrud())->getDefaultConfig();
		$groceryCrud = new GroceryCrud($config, $db);
		return $groceryCrud;
	}
}
