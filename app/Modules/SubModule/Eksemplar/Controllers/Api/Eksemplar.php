<?php

namespace Eksemplar\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
// use Hermawan\DataTables\DataTable;

class Eksemplar extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $eksemplarModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/eksemplar/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		helper('eksemplar');
	}

	public function katalog($IsQUARANTINE = 0)
	{
		$db = db_connect();
		$builder = $db->table('catalogs as a')
			->select('a.ID, a.ID as action')
			->select('a.ControlNumber, a.BIBID, a.Title, a.Author, a.Edition, a.Publisher, a.PublishLocation, a.PublishYear, a.Publikasi, a.Subject, a.PhysicalDescription, a.ISBN, a.CallNumber, a.Note, a.Languages, a.DeweyNo, a.ApproveDateOPAC, a.IsOPAC, a.IsBNI, a.IsKIN, a.IsRDA, a.CoverURL, a.Worksheet_id, a.CreateBy, a.CreateDate, a.CreateTerminal, a.UpdateBy, a.UpdateDate, a.UpdateTerminal, a.MARC_LOC, a.PRESERVASI_ID, a.QUARANTINEDBY, a.QUARANTINEDDATE, a.QUARANTINEDTERMINAL, a.Member_id, a.KIILastUploadDate')
			->select('a.Location_id')
			->select('0 as Eksemplar')
			->where('a.IsQUARANTINE', $IsQUARANTINE);

	

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function ($row) {
				$html = '<a href="javascript:void(0)" class="btn btn-success btn-sm btnAddtoForm" data-id="' . $row->ID . '" data-callnumber="' . $row->CallNumber . '" data-title="' . $row->Title . '"><i class="fa fa-check-square"></i> Pilih</a>';
				return $html;
			})
			->edit('ISBN', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . $row->ISBN . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('Title', function ($row) {
				$html =
					'<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-book fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' . ($row->Title ?? "") . '</div>
							<div class="widget-heading text-primary">' . ($row->Publisher ?? "") . '</div>
							<div class="widget-heading text-secondary">' . ($row->CallNumber ?? "") . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('Eksemplar', function ($row) {
				helper('eksemplar');
				$html  = count_collections($row->ID);
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/katalog/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('katalog/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function datatable($IsQUARANTINE = 0, $catalog_id = '', $Iskeranjang = 0)
{
    $flag = 1;
    $db = db_connect();
    $builder = $db->table('collections as a')
        ->select('a.ID, a.ID as action, a.ID as Collection_id')
        ->select('a.NomorBarcode, a.TanggalPengadaan, a.NoInduk, a.Catalog_id, a.IsOPAC, a.ISDRM, a.IsQUARANTINE, a.Status_id')
        ->select('cs.Name as StatusName')
		->select('Loc.Name as LocationLibraryName')
        ->join('collectionstatus as cs', 'a.Status_id = cs.ID', 'left')
        ->join('location_library as Loc', 'a.Location_Library_id = Loc.ID', 'left')
        ->where('a.IsQUARANTINE', $IsQUARANTINE)
		->groupBy('a.ID', 'DESC');

    if (!empty($catalog_id)) {
        $builder->where('a.Catalog_id', $catalog_id);
    }

    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->edit('ID', function ($row) {
            $html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
            return $html;
        })
        ->edit('NomorBarcode', function ($row) {
            $html = '<b>' . $row->NomorBarcode . '</b>';
            return $html;
        })
        ->edit('Catalog_id', function ($row) {
            helper('reference');
            $catalog = get_ref_single('catalogs', 'ID=' . $row->Catalog_id, 'data');
            $html = ($catalog->Title ?? "") . '<br>';
            $html .= '<b class="text-primary">' . ($catalog->Publikasi ?? "") . '</b><br>';
            return $html;
        })
        ->edit('IsOPAC', function ($row) {
            $checked = $row->IsOPAC == 1 ? 'checked' : '';
            return '<input type="checkbox" class="apply-status" data-href="' . base_url('api/eksemplar/switch/' . $row->ID) . '" data-checked="' . $checked . '" data-field="IsOPAC" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
        })
        ->edit('ISDRM', function ($row) {
            $color = $row->ISDRM == 1 ? 'success' : 'warning';
            $label = $row->ISDRM == 1 ? 'Ya' : 'Tdk';
            $html = '<span class="badge badge-' . $color . '" style="min-width: 40px">' . $label . '</span>';
            return $html;
        })
        ->edit('IsQUARANTINE', function ($row) {
            $color = $row->IsQUARANTINE == 1 ? 'success' : 'warning';
            $label = $row->IsQUARANTINE == 1 ? 'Ya' : 'Tdk';
            $html = '<span class="badge badge-' . $color . '" style="min-width: 40px">' . $label . '</span>';
            return $html;
        })
        ->edit('StatusName', function ($row) {
            // Tampilkan nama status dengan styling yang menarik
            $statusName = $row->StatusName ?? 'Tidak Diketahui';
            
            // Anda bisa menambahkan warna berbeda berdasarkan status
            $badgeClass = 'badge-info'; // default
            
            // Contoh: customize warna berdasarkan status tertentu
            switch (strtolower($statusName)) {
                case 'tersedia':
                case 'available':
                    $badgeClass = 'badge-success';
                    break;
                case 'dipinjam':
                case 'borrowed':
                    $badgeClass = 'badge-danger';
                    break;
                case 'rusak':
                case 'damaged':
                    $badgeClass = 'badge-warning';
                    break;
                case 'hilang':
                case 'lost':
                    $badgeClass = 'badge-dark';
                    break;
                default:
                    $badgeClass = 'badge-info';
                    break;
            }
            
            $html = '<span class="badge ' . $badgeClass . '" style="min-width: 60px">' . $statusName . '</span>';
            return $html;
        })
        ->edit('action', function ($row) use ($catalog_id) {
            // Tombol Edit selalu ditampilkan
            // Siapkan URL dasar terlebih dahulu
            $baseUrl = base_url('eksemplar/edit/' . $row->ID);

            // Gunakan ternary operator untuk menambahkan parameter HANYA jika $catalog_id tidak kosong
            $finalUrl = !empty($catalog_id) ? $baseUrl . '?catalog_id=' . $catalog_id : $baseUrl;

            // Gabungkan menjadi HTML akhir
            $edit = '<a href="' . $finalUrl . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
            
            // Siapkan variabel delete sebagai string kosong
            $delete = ""; 

            // Gabungkan tombol edit dan delete (jika ada)
            return $edit . ' ' . $delete;
        })
        ->toJson();
    return $dataTable;
}

	public function index($catalog_id = null)
	{
		// Retrieve parameters from the request
		$fields = $this->request->getVar('fields') ?? []; // Fields to retrieve
		$search = $this->request->getVar('search') ?? ''; // Search term
		$limit = (int)($this->request->getVar('limit') ?? 10); // Number of records per page
		$page = (int)($this->request->getVar('page') ?? 1); // Current page number
		$offset = ($page - 1) * $limit; // Calculate offset for pagination
		$order = $this->request->getVar('order') ?? 'ID'; // Field to order by
		$direction = $this->request->getVar('direction') ?? 'desc'; // Sorting direction

		// Ensure fields is an array and contains valid field names
		if (is_string($fields)) {
			$fields = json_decode($fields, true);
		}

		// Query builder for counting total records
		$countBuilder = $this->eksemplarModel;

		// Define the where clause
		if (!empty($catalog_id)) {
			$countBuilder->where('Catalog_id', $catalog_id);
		}

		// Apply search filter to each specified field
		if (!empty($search) && !empty($fields)) {
			$countBuilder->groupStart();
			$first = true;
			foreach ($fields as $field) {
				if ($first) {
					$countBuilder->like($field, $search);
					$first = false;
				} else {
					$countBuilder->orLike($field, $search);
				}
			}
			$countBuilder->groupEnd();
		}

		// Get the total record count after applying filters
		$total_record = $countBuilder->countAllResults(false);

		// Query builder for retrieving paginated data
		$builder = $this->eksemplarModel->where('Status_id', 1);

		// Define the where clause
		if (!empty($catalog_id)) {
			$builder->where('Catalog_id', $catalog_id);
		}

		// Apply search filter to each specified field
		if (!empty($search) && !empty($fields)) {
			$builder->groupStart();
			$first = true;
			foreach ($fields as $field) {
				if ($first) {
					$builder->like($field, $search);
					$first = false;
				} else {
					$builder->orLike($field, $search);
				}
			}
			$builder->groupEnd();
		}

		// Apply ordering
		if (!empty($order)) {
			$builder->orderBy($order, $direction);
		}

		// Retrieve paginated data
		$data = $builder->findAll($limit, $offset);

		foreach ($data as $key => $row) {
			$data[$key]->DRM_KEY = '0123456789abcdef0123456789abcdef';
			$data[$key]->DRM_IV = 'abcdef0123456789';
			$data[$key]->DRM_PDF = base_url('example.pdf');
			$data[$key]->DRM_PDF_ENC = base_url('example_encrypted.pdf');
			$data[$key]->DRM_PDF_DEC = base_url('example_decrypted.pdf');
		}

		// Calculate total pages
		$total_page = ceil($total_record / $limit);

		// Prepare response
		$response = [
			"total_record" => $total_record,
			"per_page" => $limit,
			"total_page" => $total_page,
			"current_page" => $page,
			"result" => [
				"error" => false,
				"param" => [
					"catalog_id" => $catalog_id,
					"limit" => $limit,
					"offset" => $offset,
					"page" => $page,
					"fields" => $fields,
					"search" => $search,
					"order" => $order,
					"direction" => $direction,
				],
				"data" => $data
			]
		];

		return $this->respond($response, 200);
	}

	public function detail($id = null)
	{
		$data = $this->eksemplarModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$slug = url_title($this->request->getPost('name'), '-', TRUE);
		$save_data = array(
			'code' => $this->request->getPost('code'),
			'name' => $this->request->getPost('name'),
			'slug' => $slug,
		);

		$save_data_id = $this->eksemplarModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Katalog berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Katalog berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Katalog gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$slug = url_title($this->request->getPost('name'), '-', TRUE);
		$update_data = array(
			'code' => $this->request->getPost('code'),
			'name' => $this->request->getPost('name'),
			'slug' => $slug,
		);

		$update_data_id = $this->eksemplarModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Katalog berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Katalog berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Katalog gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->eksemplarModel->find($id);
		if ($data) {
			$this->eksemplarModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Katalog berhasil dihapus'
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Katalog.info.not_found') . ' ID:' . $id);
		}
	}

	public function switch($id = null)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$update_data_id = $this->eksemplarModel->update($id, array($field => ($value == 'true') ? 1 : 0));

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Katalog berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Katalog gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function get_collectionsources()
	{
		$db = db_connect();
		$query = $db->table('collectionsources')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectionpartners()
	{
		$db = db_connect();
		$query = $db->table('partners')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectionrules()
	{
		$db = db_connect();
		$query = $db->table('collectionrules')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectionmedias()
	{
		$db = db_connect();
		$query = $db->table('collectionmedias')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectioncategory()
	{
		$db = db_connect();
		$query = $db->table('collectioncategorys')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectionstatus()
	{
		$db = db_connect();
		$query = $db->table('collectionstatus')->select('ID as code, Name as name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectioncurrency()
	{
		$db = db_connect();
		$query = $db->table('currency')->select('Currency as code, Description as name')->orderBy('Description')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function get_collectionpricetype()
	{
		$json = '[{"code": "Per eksemplar","name": "Per eksemplar"}, {"code": "Per jilid","name": "Per jilid"}]';
		$data  = json_decode($json);
		return $this->simpleResponse($data);
	}

	public function get_locationlibrary()
	{
		$db = db_connect();
		$query = $db->table('location_library')->select('ID as code, Name as name')->orderBy('Name')->get();
		return $this->simpleResponse($query->getResult());
	}

	public function select2_locationlibrary()
	{
		$search = $this->request->getGet('search') ?? '';
		$page = $this->request->getGet('page') ?? '1';

		$db = db_connect();
		$builder_count = $db->table('location_library as a')
			->select('count(*) as total')
			->like('a.Name', $search);
		$total = $builder_count->get()->getRow()->total ?? 0;

		$per_page = 10;
		$offset = $page * $per_page;
		$limit = $offset - $per_page;

		$db = db_connect();
		$builder = $db->table('location_library as a')
			->select('a.ID as id, a.Name as text')
			->where('a.Branch_id', user()->branch_id)
			->like('a.Name', $search)
			->limit($per_page, $limit);

		$items = $builder->get()->getResult();
		$response = array(
			'items' => $items,
			'total' => $total,
		);

		return $this->simpleResponse($response);
	}

	public function get_locations($LocationLibrary_id = 0)
	{
		$db = db_connect();
		$query = $db->table('locations')
			->select('ID as code, Name as name')
			->where('LocationLibrary_id', $LocationLibrary_id)->orderBy('Name')->get();

		return $this->simpleResponse($query->getResult());
	}

	public function get_eksemplar_number($count = 1)
	{
		$eksemplar_number = [];
		for ($i = 1; $i <= $count; $i++) {
			$data = [
				'NomorBarcode' => gen_no_barcode($i),
				'NoInduk' => gen_no_induk($i),
				'RFID' => gen_no_rfid($i),
			];
			array_push($eksemplar_number, $data);
		}

		$response = array(
			'data' => $eksemplar_number,
		);

		return $this->simpleResponse($response);
	}

	public function add_partner()
    {
        // Set validation rules
        $this->validation->setRules([
            'Name' => 'required|min_length[2]',
        ]);

        // Run validation
        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->respond([
                'success' => false,
                'message' => $this->validation->getErrors()
            ], 400);
        }

        // Prepare data
        $data = [
            'Name' => $this->request->getPost('Name'),
            'Address' => $this->request->getPost('Address'),
            'Phone' => $this->request->getPost('Phone'),
            'Fax' => $this->request->getPost('Fax'),
        ];

        // Insert to database
        $db = db_connect();
        $builder = $db->table('partners');
        $inserted = $builder->insert($data);
        
        if ($inserted) {
            // Get ID of inserted row
            $insertID = $db->insertID();
            
            // Get complete data
            $result = $db->table('partners')
                ->where('ID', $insertID)
                ->get()
                ->getRow();
                
            return $this->respond([
                'success' => true,
                'message' => 'Partner berhasil ditambahkan',
                'data' => $result
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal menambahkan partner'
            ], 500);
        }
    }

    public function get_partner($id = null)
    {
        if (!$id) {
            return $this->respond([
                'success' => false,
                'message' => 'ID partner tidak ditemukan'
            ], 400);
        }

        $db = db_connect();
        $partner = $db->table('partners')
            ->where('ID', $id)
            ->get()
            ->getRow();

        if (!$partner) {
            return $this->respond([
                'success' => false,
                'message' => 'Partner tidak ditemukan'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'data' => $partner
        ]);
    }

    public function update_partner($id = null)
    {
        if (!$id) {
            return $this->respond([
                'success' => false,
                'message' => 'ID partner tidak ditemukan'
            ], 400);
        }

        // Set validation rules
        $this->validation->setRules([
            'Name' => 'required|min_length[2]',
        ]);

        // Run validation
        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->respond([
                'success' => false,
                'message' => $this->validation->getErrors()
            ], 400);
        }

        // Check if record exists
        $db = db_connect();
        $partner = $db->table('partners')
            ->where('ID', $id)
            ->get()
            ->getRow();

        if (!$partner) {
            return $this->respond([
                'success' => false,
                'message' => 'Partner tidak ditemukan'
            ], 404);
        }

        // Only admin or users from the same branch can edit
        if (user()->category != 'admin' && $partner->Branch_id != branch_id()) {
            return $this->respond([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah data ini'
            ], 403);
        }

        // Prepare data
        $data = [
            'Name' => $this->request->getPost('Name'),
            'Address' => $this->request->getPost('Address'),
            'Phone' => $this->request->getPost('Phone'),
            'Fax' => $this->request->getPost('Fax'),
            'UpdateBy' => user()->id,
            'UpdateDate' => date('Y-m-d H:i:s')
        ];

        // Update record
        $builder = $db->table('partners');
        $updated = $builder->where('ID', $id)->update($data);

        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'Partner berhasil diperbarui'
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal memperbarui partner'
            ], 500);
        }
    }
}