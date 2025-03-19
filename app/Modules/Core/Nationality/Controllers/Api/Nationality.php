<?php

namespace Nationality\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Nationality extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $nationalityModel;

	function __construct()
	{
		$this->nationalityModel = new \Nationality\Models\NationalityModel();
	}

	public function get_countries(){
		$response = $this->nationalityModel
			->distinct()
			->select('iso2 as code,country as name')
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_cities($code = 'ID'){
		$response = $this->nationalityModel
			->select('city_ascii as code,city_ascii as name')
			->where('iso2', $code)
			->findAll();
		return $this->simpleResponse($response);
	}
}
