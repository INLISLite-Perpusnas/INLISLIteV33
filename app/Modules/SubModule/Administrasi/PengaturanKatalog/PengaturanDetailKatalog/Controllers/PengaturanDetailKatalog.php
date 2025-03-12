<?php

namespace PengaturanDetailKatalog\Controllers;

class PengaturanDetailKatalog extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $scdModel;
	public $wsfModel;
	public $wsfItemModel;

	function __construct()
	{
		$this->scdModel = new \PengaturanDetailKatalog\Models\SCDModel();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}

	public function index()
	{
		$this->data['title'] = 'Pengaturan Detail Katalog';
		$this->validation->setRule('name', 'Nama Pengaturan Detail Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = [
				'CODE' => $this->request->getPost('code'),
				'Name' => $this->request->getPost('name'),
				'Format_id' => $this->request->getPost('format'),
				'KETERANGAN' => $this->request->getPost('description'),
				'NoUrut' => $this->request->getPost('sort'),
				'ISSERIAL' => $this->request->getPost('serial'),
				'ISKARTOGRAFI' => $this->request->getPost('kartografi'),
				'ISMUSIK' => $this->request->getPost('music'),
				'UpdateBy' => user_id(),
				'UpdateTerminal' => getClientIpAddress(),
			];

			$updatePengaturanDetailKatalog = $this->scdModel->update($id, $update_data);
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				$update_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Worksheet_id' => $id,
						'Field_id' => $value,
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}

				if (!empty($save_data)) {
					$this->wsfModel->insertBatch($save_data);
				}
			}

			if ($updatePengaturanDetailKatalog) {
				set_message('toastr_msg', 'Pengaturan Detail Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-pengaturan-detail-katalog');
			} else {
				set_message('message', 'Pengaturan Detail Katalog gagal disimpan');
				echo view('PengaturanDetailKatalog\Views\update', $this->data);
			}
		} else {
			$db = db_connect('data');
			$builder = $db->table('settingcatalogdetail as scd')
				->select('scd.ID, scd.Field_id')
				->select('f.Tag, f.Name')
				->join('fields as f', 'f.ID = scd.Field_id')
				->orderBy('f.Tag', 'asc');
			$scds = $builder->get()->getResult();
			$this->data['scds'] = $scds;

			$excludes = get_object_array($scds, 'Tag');
			$builder = $db->table('fields as f')
				->select('f.ID, f.Tag, f.Name')
				->whereNotIn('f.Tag', $excludes)
				->orderBy('f.Tag', 'asc');
			$fields = $builder->get()->getResult();
			$this->data['fields'] = $fields;

			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('PengaturanDetailKatalog\Views\update', $this->data);
		}
	}
}
