<?php

namespace PeraturanPeminjamanTanggal\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class PeraturanPeminjamanTanggal extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $peraturanpeminjamantanggalModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['reference']);
		// $this->peraturanpeminjamantanggalModel = new \PeraturanPeminjamanTanggal\Models\PeraturanPeminjamanTanggalModel();
		$this->peraturanpeminjamantanggalModel = new \PeraturanPeminjamanTanggal\Models\PeraturanPeminjamanTanggalModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/peraturanpeminjamantanggal/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('peraturan_peminjaman_tanggal as a')
			->select('a.ID, a.ID as action, a.TanggalAwal as TanggalAwal,a.TanggalAkhir as TanggalAkhir, a.MaxPinjamKoleksi,a.MaxLoanDays,a.DendaTenorJumlah,a.DaySuspend,a.DayPerpanjang,a.CountPerpanjang, a.active');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')

			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a data-href="' . base_url('api/peraturan-peminjaman-tanggal/detail/' . $row->ID) . '" data-toggle="modal" data-target="#modal_update" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('master-peraturan-peminjaman-tanggal/apply_status/' . $row->ID . '?field=active&value=1') . '"  data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('master-peraturan-peminjaman-tanggal/apply_status/' . $row->ID . '?field=active&value=0') . '" data-id="' . $row->ID . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a data-href="' . base_url('master-peraturan-peminjaman-tanggal/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->peraturanpeminjamantanggalModel->findAll();
		return $this->respond($data, 200);
	}

public function detail($id = null)
{
    if (empty($id)) {
        return $this->failNotFound('ID parameter is required');
    }

    $db = db_connect();
    
    // Get main data from peraturan_peminjaman_tanggal
    $mainData = $db->table('peraturan_peminjaman_tanggal')
        ->where('ID', $id)
        ->get()
        ->getRowArray();
    
    if (!$mainData) {
        return $this->failNotFound('No Data Found with id ' . $id);
    }
    
    // Get related categories
    $categories = $db->table('collectioncategorysloantanggal')
        ->select('Category_id')
        ->where('peminjaman_tanggal_id', $id)
        ->get()
        ->getResultArray();
    
    // Extract category IDs into a simple array
    $categoryIds = array_column($categories, 'Category_id');
    
    // Add categories to main data
    $mainData['Category_id'] = $categoryIds;
    
    return $this->respond($mainData);
}

	public function create()
	{
		$save_data = [
			'TanggalAwal' => $this->request->getPost('TanggalAwal'),
			'TanggalAkhir' => $this->request->getPost('TanggalAkhir'),
			'CreateTerminal' => $this->request->getPost('CreateTerminal'),
			'UpdateTerminal' => $this->request->getPost('UpdateTerminal'),
			'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
			'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
			'DendaType' => $this->request->getPost('DendaType'),
			'DendaTenorJumlah' => $this->request->getPost('DendaTenorJumlah'),
			'DendaTenorSatuan' => $this->request->getPost('DendaTenorSatuan'),
			'DendaPerTenor' => $this->request->getPost('DendaPerTenor'),
			'DendaTenorMultiply' => $this->request->getPost('DendaTenorMultiply'),
			'SuspendMember' => $this->request->getPost('SuspendMember'),
			'WarningLoanDueDay' => $this->request->getPost('WarningLoanDueDay'),
			'SuspendType' => $this->request->getPost('SuspendType'),
			'SuspendTenorJumlah' => $this->request->getPost('SuspendTenorJumlah'),
			'SuspendTenorSatuan' => $this->request->getPost('SuspendTenorSatuan'),
			'DaySuspend' => $this->request->getPost('DaySuspend'),
			'SuspendTenorMultiply' => $this->request->getPost('SuspendTenorMultiply'),
			'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
			'CountPerpanjang' => $this->request->getPost('CountPerpanjang'),
		];

		if ($save_data) {
			$save_data_id = $this->peraturanpeminjamantanggalModel->insert($save_data);
			
			$this->session->setFlashdata('toastr_msg', 'Peraturan Peminjaman Tanggal berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Peraturan Peminjaman Tanggal berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Peraturan Peminjaman Tanggal gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($ID = null)
{
    if (empty($ID)) {
        $response = [
            'error' => true,
            'message' => 'ID parameter is required',
        ];
        return $this->simpleResponse($response);
    }

    // Check if record exists
    $existingData = $this->peraturanpeminjamantanggalModel->find($ID);
    if (!$existingData) {
        $response = [
            'error' => true,
            'message' => 'Data tidak ditemukan dengan ID: ' . $ID,
        ];
        return $this->simpleResponse($response);
    }

    // Prepare update data - include ALL fields from the form
    $update_data = [
        'TanggalAwal' => $this->request->getPost('TanggalAwal'),
        'TanggalAkhir' => $this->request->getPost('TanggalAkhir'),
        'MaxPinjamKoleksi' => $this->request->getPost('MaxPinjamKoleksi'),
        'MaxLoanDays' => $this->request->getPost('MaxLoanDays'),
        'WarningLoanDueDay' => $this->request->getPost('WarningLoanDueDay'),
        'DayPerpanjang' => $this->request->getPost('DayPerpanjang'),
        'CountPerpanjang' => $this->request->getPost('CountPerpanjang'),
        'DendaType' => $this->request->getPost('DendaType'),
        'DendaPerTenor' => $this->request->getPost('DendaPerTenor'),
        'DendaTenorJumlah' => $this->request->getPost('DendaTenorJumlah'),
        'DendaTenorSatuan' => $this->request->getPost('DendaTenorSatuan'),
        'DendaTenorMultiply' => $this->request->getPost('DendaTenorMultiply'),
        'SuspendType' => $this->request->getPost('SuspendType'),
        'DaySuspend' => $this->request->getPost('DaySuspend'),
        'SuspendTenorJumlah' => $this->request->getPost('SuspendTenorJumlah'),
        'SuspendTenorSatuan' => $this->request->getPost('SuspendTenorSatuan'),
        'SuspendTenorMultiply' => $this->request->getPost('SuspendTenorMultiply'),
    ];

    // Remove null values to avoid updating with empty strings
    $update_data = array_filter($update_data, function($value) {
        return $value !== null && $value !== '';
    });

    try {
        // Update main data
        $update_result = $this->peraturanpeminjamantanggalModel->update($ID, $update_data);
        
        if ($update_result) {
            
            $this->session->setFlashdata('toastr_msg', 'Data berhasil disimpan');
            $this->session->setFlashdata('toastr_type', 'success');
            
            $response = [
                'error' => false,
                'message' => 'Data berhasil disimpan',
                'data' => $update_data
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'Data gagal disimpan. Tidak ada perubahan atau terjadi kesalahan',
            ];
        }
    } catch (\Exception $e) {
        log_message('error', 'Edit PeraturanPeminjamanTanggal error: ' . $e->getMessage());
        
        $response = [
            'error' => true,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
        ];
    }

    return $this->simpleResponse($response);
}

	public function delete($ID = null)
	{
		$id=$ID;
		$data = $this->peraturanpeminjamantanggalModel->find($id);
		if ($data) {
			$this->peraturanpeminjamantanggalModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Peraturan Peminjaman Tanggal berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('PeraturanPeminjamanTanggal.info.not_found') . ' ID:' . $id);
		}
	}
}
