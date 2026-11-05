<?php

namespace Base\Controllers;

use App\Libraries\TempUpload;
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
		$file = $this->request->getFile('file');

		if (!$file || !$file->isValid() || $file->hasMoved()) {
			return $this->uploadError(400, 'File tidak valid atau gagal diunggah.');
		}

		$extension = TempUpload::safeExtension($file->getTempName(), $file->getClientName());
		if ($extension === null) {
			return $this->uploadError(415, 'File ditolak. Gunakan JPG, PNG, GIF, WEBP, PDF, DOC, atau DOCX dengan ukuran maksimal 12 MB.');
		}

		$newName = TempUpload::randomName($extension);
		$file->move(TempUpload::directory(), $newName);

		return $this->response->setJSON([
			'success' => true,
			'data' => [
				'name' => $newName,
				'type' => $file->getClientMimeType(),
			],
			'msg' => 'File berhasil diunggah.',
		]);
	}

	/** Dropzone membaca kunci error, modal upload katalog membaca messages.error. */
	private function uploadError(int $status, string $message)
	{
		return $this->response->setStatusCode($status)->setJSON([
			'success' => false,
			'data' => '',
			'msg' => $message,
			'error' => $message,
			'messages' => ['error' => $message],
		]);
	}

	/**
	 * Menghapus file sementara milik do_upload. Path dari klien tidak dipakai.
	 */
	public function do_delete()
	{
		$path = strtoupper($this->request->getMethod()) === 'POST'
			? TempUpload::deletablePath((string) $this->request->getPost('name'))
			: null;

		if ($path === null || !unlink($path)) {
			return $this->response->setStatusCode(400)->setJSON([
				'success' => false,
				'data' => '',
				'msg' => 'File gagal dihapus.',
			]);
		}

		return $this->response->setJSON([
			'success' => true,
			'data' => '',
			'msg' => 'File berhasil dihapus.',
		]);
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
