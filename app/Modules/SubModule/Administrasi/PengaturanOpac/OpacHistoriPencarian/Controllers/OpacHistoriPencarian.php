<?php

namespace OpacHistoriPencarian\Controllers;

class OpacHistoriPencarian extends \Base\Controllers\BaseController
{
	public function index()
	{
		$this->data['title'] = 'Histori Pencarian';
		// Tabel opaclogs warisan aplikasi lama; belum tentu ada di setiap database
		$this->data['tableExists'] = db_connect()->tableExists('opaclogs');
		echo view('OpacHistoriPencarian\Views\index', $this->data);
	}
}
