<?php

namespace Anggota\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Anggota extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $anggotaModel;
	public $validation;
	public $session;
	public $modulePath;
	public $uploadPath;
	public $regionModel;

	function __construct()
	{
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		$this->regionModel = new \Region\Models\RegionModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/anggota/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('thumbnail');
	}

	public function index()
	{
		$data = $this->anggotaModel->findAll();
		return $this->respond($data, 200);
	}

	/**
	 * Show an array of resource (paginated).
	 *
	 * @return mixed
	 */
	public function datatable($isKeranjang = 0)
	{
		/**
		 * Query builder to get data from database
		 * @var \CodeIgniter\Database\BaseBuilder $builder
		 */
		$db = db_connect('data');
		$builder = $db->table('members as a');
		$builder->select([
			// Kolom dari tabel members (a)
			'a.ID',
			'a.ID as action',
			'a.ID as cid',
			'a.IsKeranjang',
			'a.FullName',
			'a.Phone',
			'a.Email',
			'a.PhotoUrl',
			'a.MemberNo',
			'a.RegisterDate',
			'a.EndDate',
			'a.JenisAnggota_id',
			'a.StatusAnggota_id',
			'a.CreateDate',
			
			// Kolom dari tabel join
			'jenis_anggota.jenisanggota as JenisAnggota',
			'status_anggota.Nama as StatusAnggota'
		]);
		$builder->join('jenis_anggota', 'jenis_anggota.id = a.JenisAnggota_id', 'left'); // Menggunakan LEFT JOIN
		$builder->join('status_anggota', 'status_anggota.id = a.StatusAnggota_id', 'left'); // Menggunakan LEFT JOIN
		
		if($isKeranjang == 1){
			$builder->where('a.IsKeranjang', $isKeranjang);
		}


		/**
		 * DataTables
		 * @var \Hermawan\DataTables\DataTable $dataTable
		 */
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('cid', function ($row) {
				$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->cid . '">';
				return $html;
			})
			->edit('FullName', function ($row) {
				$default = base_url('uploads/default/nophoto.jpg');
				$image = (!empty($row->PhotoUrl)) ? base_url('uploads/anggota/' . $row->PhotoUrl) : $default;

				$html =
					'<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<a href="' . $image . '" class="image-link"><img width="100" class="rounded" src="' . $image . '" id="lazy' . $row->ID . '" class="lazy" data-src="' . $image . '" onerror="this.onerror=null;this.src=' . $default . ';" alt=""></a>
								</div>
						
								<div class="widget-content-left">
									<div class="widget-heading">' . $row->FullName . '</div>
									<div class="widget-subheading"><i class="fa fa-envelope"></i> ' . $row->Email . '</div>
									<div class="widget-subheading"><i class="fa fa-user"></i> ' . $row->JenisAnggota . '</div>
									<a href="#" width="100px" type="button" 
										class="btn btn-secondary btn-sm mt-2 upload-data" 
										data-toggle="modal" 
										data-target="#modal_upload" 
										data-id="' . $row->ID . '" 
										data-format=".pdf,.jpg,.png" 
										data-format-title="Format (JPG|PNG). Max 1 Files @ 2MB" 
										data-field="PhotoUrl" 
										data-title="Upload Foto"
									><i class="fa fa-upload"></i> Upload Foto</a>

									<a href="#" width="100px" type="button" 
										class="btn btn-secondary btn-sm mt-2 upload-data" 
										data-toggle="modal" 
										data-target="#modal_camera" 
										data-id="' . $row->ID . '" 
										data-format=".pdf,.jpg,.png" 
										data-format-title="Format (JPG|PNG). Max 1 Files @ 2MB" 
										data-field="PhotoUrl" 
										data-title="Upload Foto"
									><i class="fa fa-camera"></i> Ambil Foto</a>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('MemberNo', function ($row) {
				$html =
					'<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<i class="far fa-id-card fa-2x text-info"></i>
								</div>
								<div class="widget-content-left">
									<div class="widget-heading">' . $row->MemberNo . '</div>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('RegisterDate', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . substr($row->RegisterDate, 0, 10) . '</badge><br>';
				return $html;
			})
			->edit('EndDate', function ($row) {
				$html  =  '<badge class="badge badge-warning badge-pill">' . substr($row->EndDate, 0, 10) . '</badge>';
				return $html;
			})
			->edit('StatusAnggota', function ($row) {
				$html = '<select class="form-control apply-select" name="status_anggota" style="width:100%" data-href="' . base_url('api/anggota/switch/' . $row->ID) . '" data-field="StatusAnggota_id">';
				foreach (get_ref_table('status_anggota', 'id, Nama', 'id IS NOT NULL', 'data') as $row2) {
					$selected = $row2->id == $row->StatusAnggota_id ? 'selected' : '';
					$html .= '<option value="' . $row2->id . '" ' . $selected . '>' . $row2->Nama . '</option>';
				}
				return $html;
			})
		
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('anggota/edit/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah " class="btn btn-primary"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('anggota/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				if ($row->IsKeranjang == 1) {
					$edit = '<a href="' . base_url('anggota/pulihkan_keranjang?ID[]=' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Pulihkan " class="btn btn-warning"><i class="fa fa-undo font-weight-bold"> </i></a>';
				}
				return $edit . ' ' . $delete;
			})
			->toJson();

		return $dataTable;
}
	public function detail($id = null)
	{
		$data = $this->anggotaModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newAnggotaId = $this->anggotaModel->insert($save_data);
			if ($newAnggotaId) {
				$this->session->setFlashdata('toastr_msg', lang('Anggota.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Anggota.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Anggota.info.failed_saved')
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$anggotaUpdate = $this->anggotaModel->update($id, $update_data);
			if ($anggotaUpdate) {
				$this->session->setFlashdata('toastr_msg', lang('Anggota.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Anggota.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Anggota.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->anggotaModel->find($id);
		if ($data) {
			$this->anggotaModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Anggota.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Anggota.info.not_found') . ' ID:' . $id);
		}
	}

	public function hapusall()
	{

		$anggota_id = $this->request->getGet('anggota_id');

		$jmldata = count($anggota_id);

		// dd('stop');
		for ($i = 0; $i < $jmldata; $i++) {
			//check
			$cekdata = $this->anggotaModel->find($anggota_id[$i]);
			// dd($cekdata);

			$fotolama = $cekdata->Fullname;
			// dd($fotolama);
			// if ($fotolama != 'default.png') {
			//     unlink_file($this->modulePath, $anggota->PhotoUrl);
			// }
			$this->anggotaModel->delete($anggota_id[$i]);
		}

		$msg = [
			'sukses' => "$jmldata Data berhasil dihapus"
		];
		return redirect()->to('/anggota');
	}
	public function cities($code = '11')
	{
		$propinsi_id = $this->request->getGet('propinsi_id');
		if (!empty($propinsi_id)) {
			$data = get_dropdown('m_kota', 'propinsi_id = ' . $propinsi_id);
		} else {
			$data = get_dropdown('m_kota');
		}
		// $data= $this->regionModel
		// ->select('code,name')
		// ->where('level', 2)
		// ->like('code', $code)
		// ->findAll();

		return $this->respond($data, 200);
	}

	public function get_date()
	{
		$date = date('Y-m-d');
		return $this->respond($date, 200);
	}

	public function upload_file()
	{
		$upload_id = $this->request->getPost('upload_id');
		$upload_field = $this->request->getPost('upload_field');
		$upload_title = $this->request->getPost('upload_title');

		$update_data = [];
		$files = (array) $this->request->getPost('file_pendukung');
		if (count($files)) {
			$listed_file = array();
			foreach ($files as $uuid => $name) {
				if (file_exists($this->uploadPath . $name)) {
					$file = new File($this->uploadPath . $name);
					$newFileName = $file->getRandomName();
					$file->move($this->modulePath, $newFileName);
					$listed_file[] = $newFileName;
				}
			}
			$update_data['PhotoUrl'] = implode(',', $listed_file);
		}

		$anggota = $this->anggotaModel->find($upload_id);
		$anggotaUpdate = $this->anggotaModel->update($upload_id, $update_data);
		if ($anggotaUpdate) {
			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}

	public function capture_file()
	{
		$capture_id = $this->request->getPost('capture_id');

		$base64_string = $this->request->getPost('camera_image');
		if (!empty($base64_string)) {
			$file = new File($this->uploadPath);
			$newFileName = $file->getRandomName() . '.jpg';
			base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
			$update_data['PhotoUrl'] =  $newFileName;
		}

		$anggota = $this->anggotaModel->find($capture_id);
		$anggotaUpdate = $this->anggotaModel->update($capture_id, $update_data);
		if ($anggotaUpdate) {
			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}

	public function switch($id = null)
{
    // Change from getGet to getPost to match how the data is sent in ajax_post function
    $field = $this->request->getPost('field');
    $value = $this->request->getPost('value');

    $update_data_id = $this->anggotaModel->update($id, array($field => $value));

    if ($update_data_id) {
        $response = [
            'error' => false,
            'message' => 'Field ' . $field . ' berhasil disimpan',
        ];
    } else {
        $response = [
            'error' => true,
            'message' => 'Field ' . $field . ' gagal disimpan. Silakan coba lagi',
        ];
    }
    return $this->simpleResponse($response);
}
}