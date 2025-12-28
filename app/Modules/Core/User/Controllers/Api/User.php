<?php

namespace User\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
use PhpParser\Node\Stmt\TryCatch;

//use Hermawan\DataTables\DataTable;

class User extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $userModel;
	public $groupModel;
	public $validation;
	public $session;
	public $config;
	public $uploadPath;
	public $modulePath;
	public $password;
	public $request;
	public $settingModel;

	function __construct()
	{
		$this->session = session();
		$this->request = \Config\Services::request();
		$this->validation = service('validation');
		$this->config = config('Auth');
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->password = new \Myth\Auth\Password();

		$this->userModel = new \User\Models\UserModel();
		$this->groupModel = new \Group\Models\GroupModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		$this->modulePath = ROOTPATH . 'public/uploads/user/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('region');
		helper('eksemplar');
	}

	public function datatable()
	{
		$slug = $this->request->getGet('slug') ?? '';
	

		$db = db_connect('default');
		$builder = $db->table('users as a')
			->select('a.id, a.id as action, a.username,a.email, concat(a.first_name, " ", a.last_name) as full_name, a.first_name, a.last_name, a.active, a.updated_at, a.id as group_id')
			->like('a.category', $slug);
			// dd($builder->get()->getResult());

		if (empty($slug)) {
			$builder->where('id <', 0);
		}

	
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('first_name', function ($row) {
				$html = '<div class="widget-content p-0">
								<div class="widget-content-wrapper">
									<div class="widget-content-left mr-3">
										<i class="far fa-user-alt fa-2x text-secondary"></i>
									</div>
									<div class="widget-content-left">
										<div class="widget-heading">' . $row->full_name . '</div>
										<div class="widget-subheading">
											<b>' . $row->username . '</b><br>
											<i class="fa fa-envelope opacity-4"></i> ' . $row->email . '
										</div>
									</div>
								</div>
							</div>';

				return $html;
			})
		
			->edit('group_id', function ($row) {
				$groups = $this->userModel->getGroups($row->id);
				$html = '';
				foreach ($groups as $group) {
					$html .= '<span class="badge badge-pill badge-secondary">' . $group . '</span> ';
				}
				return $html;
			})
			->edit('branch_id', function ($row) {
				$npp_perpustakaan = $this->settingModel->where('Name', 'NPPPerpustakaan')->first()->Value ?? 'NPP Perpustakaan Mitra';
				$nama_perpustakaan = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';	
				$alamat_perpustakaan = $this->settingModel->where('Name', 'NamaLokasiPerpustakaan')->first()->Value ?? 'Alamat Perpustakaan Mitra';
				$html = '<dl>';
				if (!empty($npp_perpustakaan)) {
					$html .= '<dt>NPP</dt>';
					$html .= '<dd>' . ($npp_perpustakaan ?? '') . '</dd>';
				}

				if (!empty($nama_perpustakaan)) {
					$html .= '<dt>Nama</dt>';
					$html .= '<dd>' . strtoupper($nama_perpustakaan) . '</dd>';
				}

				if (!empty($alamat_perpustakaan)) {
					$html .= '<dt>Jenis</dt>';
					$html .= '<dd>' . strtoupper($alamat_perpustakaan ?? '-') . '</dd>';
				}

				$html .= '</dl>';

				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				// $active	= '<a href="' . base_url('user/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Aktifkan" class="btn btn-success publish-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				// $inactive = '<a href="' . base_url('user/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Nonaktifkan" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';

				// $html .=  $active . ' ' . $inactive;
				return $html;
			})
			->edit('updated_at', function ($row) {
				$html = '';
				$html .= '<span class="badge badge-pill badge-info">' . $row->updated_at . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$permission = '<a href="' . base_url('user/permissions/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Permission" class="btn btn-primary show-data"><i class="fa fa-cogs"> </i></a>';
				$detail = '<a href="' . base_url('user/detail/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Profil User" class="btn btn-warning show-data"><i class="pe-7s-user font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('user/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus User" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				$html 	= $permission . ' ' . $detail . ' ' . $delete;
				return $html;
			})
			->toJson();

		return $dataTable;
	}

	public function view($id = null)
	{
		$data = $this->userModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	function _alpha_dash_space($str_in = '')
	{
		if (!preg_match("/^([-a-z0-9_ ])+$/i", $str_in)) {
			$this->form_validation->set_message('_alpha_dash_space', 'The %s field may only contain alpha-numeric characters, spaces, underscores, and dashes.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function create()
	{
		// Validate here first, since some things,
		// like the password, can only be validated properly here.
		$rules = [
			'username'  	=> [
				'label' => 'Username',
				'rules' => 'required|alpha_dash|min_length[3]|is_unique[users.username]',
			],
			'first_name'	 	=> [
				'label' => 'Nama Depan',
				'rules' => 'required',
			],
			'email'			=> [
				'label' => 'Email',
				'rules' => 'required|valid_email|is_unique[users.email]',
			],
			'password'	 	=> [
				'label' => 'Password',
				'rules' => 'required',
			],
			'pass_confirm' 	=> [
				'label' => 'Konfirmasi Password',
				'rules' => 'required|matches[password]',
			]
		];

		if (!$this->validate($rules)) {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}

		// Save the user
		$users = model(\Myth\Auth\Models\UserModel::class);
		$group = $this->request->getPost('group') ?? $this->config->defaultUserGroup;
		$username = $this->request->getPost('username');
		$allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
		$save_data = $this->request->getPost($allowedPostFields);
		$user = new \Myth\Auth\Entities\User($save_data);
		$user->activate();
		$users = $users->withGroup($group);

		if (!$users->save($user)) {
			set_message('message', $this->session->getFlashdata('message'));
			return $this->fail('<div class="alert alert-danger fade show" role="alert">Tambah User gagal disimpan</div>', 400);
		} else {
			$update_data = $this->request->getPost($this->config->validFields);
			$first_name = $this->request->getPost('first_name');
			$last_name = $this->request->getPost('last_name');
			$is_branch = $this->request->getPost('is_branch') ?? 1;
			$provinsi_id = $this->request->getPost('provinsi_id') ?? user()->npp_provinsi_id;
			$branch_id = $this->settingModel->where('Name', 'Branch_id')->first()->Value ?? 'ID Perpustakaan Mitra';

			try {
				$kabkota_id = (null !== $this->request->getPost('kabkota_id')) ? $this->request->getPost('kabkota_id') : user()->kabkota_id;
				if (!empty($kabkota_id)) {
					$update_data['npp_kabkota_id'] = $kabkota_id;
				}
			} catch (\Throwable $th) {
			}

			if (!empty($provinsi_id)) {
				$update_data['npp_provinsi_id'] = $provinsi_id;
			}

			if (!empty($branch_id)) {
				$update_data['branch_id'] = $branch_id;
			}

			$update_data['first_name'] = $first_name;
			$update_data['last_name'] = $last_name;
			$update_data['category'] = $group;
			$update_data['is_branch'] = $is_branch;



			$db = db_connect('default');
			$builder = $db->table('users')->where('username', $username);
			$builder->update($update_data);

			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Tambah User berhasil disimpan'
				]
			];
			return $this->respond($response);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('username', 'Username', 'required');
		$this->validation->setRule('email', 'Email', 'required');
		if ($this->request->getPost('password')) {
			$this->validation->setRule('password', 'Password', 'required|min_length[' . $this->config->minimumPasswordLength . ']');
			$this->validation->setRule('pass_confirm', 'Konfirmasi Password', 'required|matches[password]');
		}

		if (is_member('admin')) {
			$this->validation->setRule('groups', 'Group', 'required');
		}

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'first_name' => $this->request->getPost('first_name'),
				'last_name' => $this->request->getPost('last_name'),
				'phone' => $this->request->getPost('phone'),
				'unit' => $this->request->getPost('unit'),
				'company' => $this->request->getPost('company'),
				'address' => $this->request->getPost('address'),
				'coordinate' => $this->request->getPost('coordinate'),
				'branch_id' => $this->settingModel->where('Name', 'Branch_id')->first()->Value ?? 'ID Perpustakaan Mitra'
			);

		

			if ($this->request->getPost('password')) {
				$update_data['password_hash'] = $this->password->hash($this->request->getPost('password'));
				$update_data['reset_hash'] = null;
				$update_data['reset_at'] = null;
				$update_data['reset_expires'] = null;
			}

			$userUpdate = $this->userModel->update($id, $update_data);
			if ($userUpdate) {
				if (is_member('admin')) {
					$groups = $this->authorize->groups();
					foreach ($groups as $group) {
						$this->authorize->removeUserFromGroup($id, $group->id);
					}

					$group_ids = $this->request->getPost('groups');
					foreach ($group_ids as $group_id) {
						$this->authorize->addUserToGroup($id, $group_id);
					}
				}
				


				$this->session->setFlashdata('toastr_msg', 'Profil User berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Profil User berhasil disimpan'
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Profil User gagal disimpan</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function upload_file()
{
    $upload_id = $this->request->getPost('upload_id');
    $upload_field = $this->request->getPost('upload_field');
    $upload_title = $this->request->getPost('upload_title');

    // Validasi input
    $validation = \Config\Services::validation();
    
    $validation->setRules([
        'upload_id' => [
            'label' => 'Upload ID',
            'rules' => 'required',
        ],
        'upload_field' => [
            'label' => 'Upload Field',
            'rules' => 'required',
        ],
        'file_pendukung' => [
            'label' => 'File',
            'rules' => [
                'uploaded[file_pendukung]',
                'max_size[file_pendukung,2048]', // Maksimal 2MB
                'is_image[file_pendukung]',
                'mime_in[file_pendukung,image/jpg,image/jpeg,image/png,image/gif]',
                'ext_in[file_pendukung,jpg,jpeg,png,gif]'
            ],
        ],
    ]);

    // Jalankan validasi
    if (!$validation->withRequest($this->request)->run()) {
        $response = [
            'status'   => 400,
            'error'    => true,
            'messages' => [
                'error' => implode('<br>', $validation->getErrors())
            ]
        ];
        return $this->fail($response);
    }

    $update_data = [];
    
    // Ambil file yang diupload
    $files = $this->request->getFiles();
    
    if (isset($files['file_pendukung'])) {
        $listed_file = array();
        
        foreach ($files['file_pendukung'] as $file) {
            // Cek apakah file valid
            if ($file->isValid() && !$file->hasMoved()) {
                
                // Validasi tambahan untuk ukuran gambar
                $imageInfo = getimagesize($file->getTempName());
                
                if ($imageInfo === false) {
                    $response = [
                        'status'   => 400,
                        'error'    => true,
                        'messages' => [
                            'error' => 'File ' . $file->getName() . ' bukan file gambar yang valid'
                        ]
                    ];
                    return $this->fail($response);
                }
                
                // Validasi ukuran file (2MB = 2097152 bytes)
                if ($file->getSize() > 2097152) {
                    $response = [
                        'status'   => 400,
                        'error'    => true,
                        'messages' => [
                            'error' => 'File ' . $file->getName() . ' melebihi ukuran maksimal 2MB'
                        ]
                    ];
                    return $this->fail($response);
                }
                
                // Validasi dimensi gambar (opsional - sesuaikan dengan kebutuhan)
                $maxWidth = 5000;  // Maksimal lebar 5000px
                $maxHeight = 5000; // Maksimal tinggi 5000px
                
                if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
                    $response = [
                        'status'   => 400,
                        'error'    => true,
                        'messages' => [
                            'error' => 'Dimensi gambar ' . $file->getName() . ' terlalu besar. Maksimal ' . $maxWidth . 'x' . $maxHeight . ' pixels'
                        ]
                    ];
                    return $this->fail($response);
                }
                
                // Validasi tipe MIME secara manual
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileMime = $file->getMimeType();
                
                if (!in_array($fileMime, $allowedMimes)) {
                    $response = [
                        'status'   => 400,
                        'error'    => true,
                        'messages' => [
                            'error' => 'Tipe file ' . $file->getName() . ' tidak diperbolehkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan'
                        ]
                    ];
                    return $this->fail($response);
                }
                
                // Generate nama file random
                $newFileName = $file->getRandomName();
                
                // Pindahkan file ke folder tujuan
                if ($file->move($this->modulePath, $newFileName)) {
                    $listed_file[] = $newFileName;
                } else {
                    $response = [
                        'status'   => 500,
                        'error'    => true,
                        'messages' => [
                            'error' => 'Gagal memindahkan file ' . $file->getName()
                        ]
                    ];
                    return $this->fail($response);
                }
            }
        }
        
        // Jika ada file yang berhasil diupload
        if (count($listed_file) > 0) {
            $update_data[$upload_field] = implode(',', $listed_file);
        } else {
            $response = [
                'status'   => 400,
                'error'    => true,
                'messages' => [
                    'error' => 'Tidak ada file yang valid untuk diupload'
                ]
            ];
            return $this->fail($response);
        }
    }

    // Update data ke database
    $userModel = new \user\Models\userModel();
    $updateuser = $userModel->update($upload_id, $update_data);
    
    if ($updateuser) {
        $this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
        $this->session->setFlashdata('toastr_type', 'success');
        
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Upload file berhasil. Total ' . count($listed_file) . ' file diupload.'
            ]
        ];
        return $this->respondCreated($response);
    } else {
        // Hapus file yang sudah diupload jika gagal update database
        foreach ($listed_file as $fileName) {
            if (file_exists($this->modulePath . $fileName)) {
                unlink($this->modulePath . $fileName);
            }
        }
        
        $response = [
            'status'   => 400,
            'error'    => true,
            'messages' => [
                'error' => 'Upload file gagal. Data tidak dapat disimpan ke database.'
            ]
        ];
        return $this->fail($response);
    }
}

	public function delete($id = null)
	{
		try {
			$data = $this->userModel->find($id);
			if ($data) {
				$delete = $this->userModel->delete($id);
				$response = array(
					'error'    => false,
					'message' => 'Data deleted successfully'
				);
			} else {
				$response = array(
					'error'    => true,
					'message' => 'Could not find data for specified ID' . $id,
				);
			}
		} catch (Exception $e) {
			$response = array(
				'error'    => true,
				'message' => $e->getMessage()
			);
		}
		return $this->simpleResponse($response);
	}

	public function select2_branch($slug = '')
	{
		$search = $this->request->getGet('search') ?? '';
		$page = $this->request->getGet('page') ?? '1';

		$db = db_connect();
		$builder_count = $db->table('branchs as a')
			->select('count(*) as total')
			->like('a.Code', $search);

		

		$total = $builder_count->get()->getRow()->total ?? 0;

		$per_page = 10;
		$offset = $page * $per_page;
		$limit = $offset - $per_page;

		$db = db_connect();
		$builder = $db->table('branchs as a')
			->select('a.ID as id, concat(a.Code," ","(",a.Name,")") as text')
			->like('a.Code', $search)
			->limit($per_page, $limit);

		
		$items = $builder->get()->getResult();
		$response = array(
			'items' => $items,
			'total' => $total,
		);
		if ($page * $per_page >= $total) {
			$response['pagination'] = array(
				'more' => false
			);
		} else {
			$response['pagination'] = array(
				'more' => true
			);
		}
		return $this->simpleResponse($response);
	}
}
