<?php

namespace Tag\Controllers;

class Tag extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $fieldModel;
	public $fieldDataModel;
	public $fieldIndicator1Model;
	public $fieldIndicator2Model;

	function __construct()
	{
		$this->fieldModel = new \Tag\Models\FieldModel();
		$this->fieldDataModel = new \Tag\Models\FieldDataModel();
		$this->fieldIndicator1Model = new \Tag\Models\FieldIndicator1Model();
		$this->fieldIndicator2Model = new \Tag\Models\FieldIndicator2Model();

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
	}
	public function index()
	{
		$this->data['title'] = 'Tag';
		echo view('Tag\Views\list', $this->data);
	}

	public function create()
	{
		$this->data['title'] = 'Tambah Tag';
		$this->validation->setRule('code', 'Tag', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$fixed = $this->request->getPost('fixed');
			$enabled = $this->request->getPost('enabled');
			$mandatory = $this->request->getPost('mandatory');
			$repeatable = $this->request->getPost('repeatable');
			$customable = $this->request->getPost('customable');

			// Field
			$save_data = [
				'Name' => $this->request->getPost('name'),
				'Tag' => $this->request->getPost('code'),
				'Length' => $this->request->getPost('length'),
				'Group_id' => $this->request->getPost('group'),
				'Format_id' => $this->request->getPost('format'),
				'Fixed' => $fixed ? 1 : 0,
				'Enabled' => $enabled ? 1 : 0,
				'Mandatory' => $mandatory ? 1 : 0,
				'Repeatable' => $repeatable ? 1 : 0,
				'IsCustomable' => $customable ? 1 : 0,
				'CreateBy' => user_id(),
				'CreateTerminal' => getClientIpAddress(),
			];

			$newTagId = $this->fieldModel->insert($save_data);
			$field_id = $this->fieldModel->getInsertID();

			// Field Data
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Field_id' => $field_id,
						'Tag' => $this->request->getPost('code'),
						'Code' => $this->request->getPost('code0')[$value],
						'Name' => $this->request->getPost('name0')[$value],
						'Delimiter' => $this->request->getPost('delimiter0')[$value],
						'SortNo' => $this->request->getPost('sortno0')[$value],
						'isShow' => $this->request->getPost('isshow0')[$value],
						'Repeatable' => $this->request->getPost('repeatable0')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal'	=> $created_terminal,
					];
				}

				if (!empty($save_data)) {
					$this->fieldDataModel->insertBatch($save_data);
				}
			}

			// Field Indicator 1
			$index_arr = $this->request->getPost('index1');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Field_id' => $field_id,
						'Code' => $this->request->getPost('code1')[$value],
						'Name' => $this->request->getPost('name1')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}
				if (!empty($save_data)) {
					$this->fieldIndicator1Model->insertBatch($save_data);
				}
			}

			// Field Indicator 2
			$index_arr = $this->request->getPost('index2');
			if (!empty($index_arr)) {
				$save_data = array();
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Field_id' => $field_id,
						'Code' => $this->request->getPost('code2')[$value],
						'Name' => $this->request->getPost('name2')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}
				if (!empty($save_data)) {
					$this->fieldIndicator2Model->insertBatch($save_data);
				}
			}

			if ($newTagId) {
				set_message('toastr_msg', 'Tag berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-tag');
			} else {
				set_message('message', 'Tag gagal disimpan');
				echo view('Tag\Views\add', $this->data);
			}
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Tag\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Tag';
		$this->validation->setRule('code', 'Tag', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$fixed = $this->request->getPost('fixed');
			$enabled = $this->request->getPost('enabled');
			$mandatory = $this->request->getPost('mandatory');
			$repeatable = $this->request->getPost('repeatable');
			$customable = $this->request->getPost('customable');

			// Field
			$update_data = [
				'Name' => $this->request->getPost('name'),
				'Tag' => $this->request->getPost('code'),
				'Length' => $this->request->getPost('length'),
				'Group_id' => $this->request->getPost('group'),
				'Format_id' => $this->request->getPost('format'),
				'Fixed' => $fixed ? 1 : 0,
				'Enabled' => $enabled ? 1 : 0,
				'Mandatory' => $mandatory ? 1 : 0,
				'Repeatable' => $repeatable ? 1 : 0,
				'IsCustomable' => $customable ? 1 : 0,
				'UpdateBy' => user_id(),
				'UpdateTerminal' => getClientIpAddress(),
			];

			$updateTag = $this->fieldModel->update($id, $update_data);

			// Field Data
			$this->fieldDataModel->where('Field_id', $id)->delete();
			$index_arr = $this->request->getPost('index0');
			if (!empty($index_arr)) {
				$save_data = [];
				foreach ($index_arr as $index => $value) {
					$repeatable0 = $this->request->getPost('repeatable0')[$value];
					$isshow0 = $this->request->getPost('isshow0')[$value];
					$save_data[] = [
						'Field_id' => $id,
						'Code' => $this->request->getPost('code0')[$value],
						'Name' => $this->request->getPost('name0')[$value],
						'Delimiter' => $this->request->getPost('delimiter0')[$value],
						'SortNo' => $this->request->getPost('sortno0')[$value],
						'IsShow' => $isshow0 ? 1 : 0,
						'Repeatable' => $repeatable0 ? 1 : 0,
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}
				if (!empty($save_data)) {
					$this->fieldDataModel->insertBatch($save_data);
				}
			}

			// Field Indicator 1
			$this->fieldIndicator1Model->where('Field_id', $id)->delete();
			$index_arr = $this->request->getPost('index1');
			if (!empty($index_arr)) {
				$save_data = [];
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Field_id' => $id,
						'Code' => $this->request->getPost('code1')[$value],
						'Name' => $this->request->getPost('name1')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}
				if (!empty($save_data)) {
					$this->fieldIndicator1Model->insertBatch($save_data);
				}
			}

			// Field Indicator 2
			$this->fieldIndicator2Model->where('Field_id', $id)->delete();
			$index_arr = $this->request->getPost('index2');
			if (!empty($index_arr)) {
				$save_data = [];
				foreach ($index_arr as $index => $value) {
					$save_data[] = [
						'Field_id' => $id,
						'Code' => $this->request->getPost('code2')[$value],
						'Name' => $this->request->getPost('name2')[$value],
						'CreateBy' => user_id(),
						'CreateTerminal' => getClientIpAddress(),
					];
				}
				if (!empty($save_data)) {
					$this->fieldIndicator2Model->insertBatch($save_data);
				}
			}

			if ($updateTag) {
				set_message('toastr_msg', 'Tag berhasil diubah');
				set_message('toastr_type', 'success');
				return redirect()->to('/master-tag');
			} else {
				set_message('message', 'Tag gagal disimpan');
				echo view('Tag\Views\update', $this->data);
			}
		} else {
			$tag = $this->fieldModel->find($id);
			$indicator1s = get_table('fieldindicator1s', 'Field_id, Code, Name', 'Field_id=' . $tag->ID, 'data');
			$indicator2s = get_table('fieldindicator2s', 'Field_id, Code, Name', 'Field_id=' . $tag->ID, 'data');
			$fielddatas = get_table('fielddatas', 'Field_id, Code, Name, Delimiter, SortNo, IsShow, Repeatable', 'Field_id=' . $tag->ID, 'data');
			$this->data['tag'] = $tag;
			$this->data['indicator1s'] = $indicator1s;
			$this->data['indicator2s'] = $indicator2s;
			$this->data['fielddatas'] = $fielddatas;
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Tag\Views\update', $this->data);
		}
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('master-tag');
		}
		$tagDelete = $this->fieldModel->delete($id);
		if ($tagDelete) {
			$this->fieldDataModel->where('Field_id', $id)->delete();
			$this->fieldIndicator1Model->where('Field_id', $id)->delete();
			$this->fieldIndicator2Model->where('Field_id', $id)->delete();
			set_message('toastr_msg', 'Tag berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('master-tag');
		} else {
			set_message('toastr_msg', 'Tag gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Tag gagal dihapus');
			return redirect()->to('master-tag');
		}
	}
}
