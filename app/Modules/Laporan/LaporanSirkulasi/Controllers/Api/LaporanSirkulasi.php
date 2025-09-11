<?php

namespace LaporanSirkulasi\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class LaporanSirkulasi extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $anggotaModel;

	function __construct()
	{
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		helper(['reference']);
	}

	public function index(){
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'desc';

		$query = $this->anggotaModel
			->where('active', 1);

		$total = $query->countAllResults(false);

		$data = $query
			->orderBy($params['order'], $params['direction'])
			->findAll($params['limit'], $params['offset']);

		$response = array(
			'error'    => false,
			'param' => $params,
			'data' => $data,
			'message' => 'Data retrieved successfully'
		);

		return $this->paginatedResponse($response, $total, $params['limit'], $params['offset']);
	}

	public function visitor_datatable($date_from = null, $date_to = null)
	{
		$db = db_connect('backend');
		$builder = $db->table('c_visitors as a')
			->select('a.timestamp, a.ip_address, a.ip_city, a.ip_country, a.hits')
			->select('a.created_at, a.updated_at');

		if (!empty($date_from)) {
			$builder->where('timestamp >=', $date_from);
		}

		if (!empty($date_to)) {
			$builder->where('timestamp <=', $date_to);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->toJson();
		return $dataTable;
	}

	public function member_datatable($isKeranjang = 0)
	{
		$db = db_connect('data');
		$builder = $db->table('members as a')
			->select('a.ID, a.ID as action, a.ID as cid')
			->select('a.IsKeranjang, a.FullName, a.Phone, a.Email, a.PhotoUrl, a.MemberNo, a.Email, a.RegisterDate, a.EndDate')
			->select('a.JenisAnggota_id, b.jenisanggota as JenisAnggota, a.StatusAnggota_id, c.Nama as StatusAnggota')
			->join('jenis_anggota as b','b.id=a.JenisAnggota_id')
			->join('status_anggota as c','c.id=a.StatusAnggota_id')
			->where('a.isKeranjang', $isKeranjang);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			
			->toJson();

		return $dataTable;
	}
}
