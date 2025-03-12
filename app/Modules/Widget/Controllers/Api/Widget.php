<?php

namespace Widget\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Widget extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/widget/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('converter');
	}
}
