<?php

namespace PengaturanDetailKatalog\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class PengaturanDetailKatalog extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $scdModel;

	function __construct()
	{
		$this->scdModel = new \PengaturanDetailKatalog\Models\SCDModel();
	}

	public function scd_create($id)
	{
		$save_data = array(
			'Field_id' => $id,
			'CreateBy' => user_id(),
			'CreateTerminal' => getClientIpAddress(),
		);
		$insertID = $this->scdModel->insert($save_data);
		if ($insertID) {
			$response = [
				'status' => 200,
				'insertID' => $insertID,
				'error' => null,
				'messages' => [
					'success' => 'Data berhasil disimpan'
				]
			];
			return $this->respond($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan'.' ID:' . $id);
		}
	}
	public function scd_delete($id = null)
	{
		$data = $this->scdModel->find($id);
		if ($data) {
			$this->scdModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Data berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound('Data tidak ditemukan'.' ID:' . $id);
		}
	}
}
