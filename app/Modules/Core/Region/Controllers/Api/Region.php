<?php

namespace Region\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Region extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $regionModel;

	function __construct()
	{
		$this->regionModel = new \Region\Models\RegionModel();
	}

	public function get_provinces()
	{
		
		$builder = $this->regionModel
			->select('npp,code,name')
			->where('level', 1);

		

		$response = $builder->findAll();
		return $this->simpleResponse($response);
	}

	public function get_cities($code = '11')
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 2)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_districts($code = '11.01')
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 3)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_sub_districts($code = '11.01.01')
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 4)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_kab_kota()
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 2)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_kelurahan($kab_kota_code = '73.73')
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 4)
			->like('code', $kab_kota_code)
			->findAll();
		return $this->simpleResponse($response);
	}

	// custom
	public function npp_provinces()
	{
		$response = $this->regionModel
			->select('npp as code,name')
			->where('level', 1)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function npp_cities($npp = '11')
	{
		$response = $this->regionModel
			->select('npp as code,name')
			->where('level', 2)
			->where('npp', $npp)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function npp_districts($npp = '1101')
	{
		$response = $this->regionModel
			->select('npp as code,name')
			->where('level', 3)
			->like('npp', $npp)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function npp_sub_districts($npp = '110101')
	{
		$response = $this->regionModel
			->select('npp as code,name')
			->where('level', 4)
			->like('npp', $npp)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_regions()
	{
		$response = $this->regionModel
			->select('npp,code,name')
			->where('level', 1)
			->findAll();
		return $this->simpleResponse($response);
	}
}
