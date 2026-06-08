<?php

namespace Dashboard\Controllers;

class Dashboard extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;

	function __construct()
	{
		helper('app');
	}

	public function index()
	{
		
		$page = 'index';
		if (user()->category == 'admin') {
			$page = 'index';
		} elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
			$page = 'sa_prov';
		} elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
			$page = 'sa_prov_umum';
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
			$page = 'sa_kabkot';
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id !== null) {
			$page = 'sa_kabkot_umum';
		} else {
			$page = 'branch';
		}

		$this->data['title'] = 'Dashboard';

		echo view('Dashboard\Views\\' . $page, $this->data);
	}
}
