<?php

namespace Widget\Controllers;

use \CodeIgniter\Files\File;
use \Dompdf\Options;
use \Dompdf\Dompdf;

class Widget extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $uploadPath;
	public $modulePath;

	function __construct()
	{
		$this->modulePath = ROOTPATH . 'public/uploads/widget/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('converter');
	}

	public function index()
	{
		echo "Widget Controller";
	}

	public function convert($module = 'psp')
	{
		$upload_table = $this->request->getGet('upload_table');
		$upload_id = $this->request->getGet('upload_id');
		$upload_field = $this->request->getGet('upload_field');

		$db = db_connect('default');
		$builder = $db->table($upload_table);
		$builder->where('id', $upload_id);
		$data = $builder->get()->getRow();

		$this->modulePath = ROOTPATH . 'public/uploads/' . $module . '/';
		$output_doc = $this->modulePath . '/' . $data->{$upload_field};
		$filename = date('Ymd_His');
		$output_pdf = $this->modulePath . '/' . $filename . '.pdf';
		$message = convert_doc_to_pdf($output_doc, $output_pdf);

		if (empty($message)) {
			$builder->set($upload_field . '_pdf', $filename . '.pdf');
			$builder->update();
			set_message('toastr_msg', 'Generate PDF berhasil');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Generate PDF gagal');
			set_message('toastr_type', 'warning');
		}
		return redirect()->back();
	}
}
