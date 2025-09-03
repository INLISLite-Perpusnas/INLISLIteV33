<?php

namespace Tag\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Tag extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $fieldModel;
	protected $fieldDataModel;
	protected $fieldIndicator1Model;
	protected $fieldIndicator2Model;

	function __construct()
	{
		$this->fieldModel = new \Tag\Models\FieldModel();
		$this->fieldDataModel = new \Tag\Models\FieldDataModel();
		$this->fieldIndicator1Model = new \Tag\Models\FieldIndicator1Model();
		$this->fieldIndicator2Model = new \Tag\Models\FieldIndicator2Model();
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('fields as a')
			->select('a.ID as id, a.ID as action, a.Tag as code, a.Name as name')
			->select('a.Length as length, a.Fixed as fixed, a.Enabled as enabled, a.Repeatable as repeatable, a.Mandatory as mandatory, a.IsCustomable as customable')
			->select('f.Name as format, g.Name as group')
			->join('formats as f', 'f.ID = a.Format_id')
			->join('fieldgroups as g', 'g.ID = a.Group_id');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('master-tag', function ($row) {
				$html  =  '<b>' . $row->tag . '</b>';
				return $html;
			})
			->edit('fixed', function ($row) {
				$checked = $row->fixed == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-tag/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="fixed" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('enabled', function ($row) {
				$checked = $row->enabled == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-tag/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="enabled" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('repeatable', function ($row) {
				$checked = $row->repeatable == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-tag/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="repeatable" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('mandatory', function ($row) {
				$checked = $row->mandatory == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-tag/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="mandatory" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('customable', function ($row) {
				$checked = $row->customable == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-tag/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="iscustomable" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('master-tag/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('master-tag/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function switch($id = null)
	{
		
		$field = $this->request->getPost('field');
		$value = $this->request->getPost('value');
		
		

		$update_data_id = $this->fieldModel->update($id, array($field => ($value == 'true') ? 1 : 0));
	

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field ' . ucfirst($field) . ' berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field ' . ucfirst($field) . ' gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function field_data_delete($id = null)
	{
		$data = $this->fieldDataModel->find($id);
		if ($data) {
			$this->fieldDataModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Data berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan' . ' ID:' . $id);
		}
	}

	public function field_indicator1_delete($id = null)
	{
		$data = $this->fieldIndicator1Model->find($id);
		if ($data) {
			$this->fieldIndicator1Model->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Data berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan' . ' ID:' . $id);
		}
	}

	public function field_indicator2_delete($id = null)
	{
		$data = $this->fieldIndicator2Model->find($id);
		if ($data) {
			$this->fieldIndicator2Model->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Data berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan' . ' ID:' . $id);
		}
	}

	public function get_all_tags()
	{
		$db = db_connect('data');
		$query = $db->table('fields')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}
}
