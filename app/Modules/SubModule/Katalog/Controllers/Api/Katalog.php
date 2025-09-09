<?php

namespace Katalog\Controllers\Api;

use App\Libraries\App;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
// use Hermawan\DataTables\DataTable;

class Katalog extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $fileModel;
	public $katalogModel;
	public $validation;
	public $session;
	public $modulePath;
	public $uploadPath;

	function __construct()
	{
		$this->fileModel = new \Katalog\Models\FileModel();
		$this->katalogModel = new \Katalog\Models\KatalogModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/katalog/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
	}

public function datatable($IsQUARANTINE = 0)
{
    $db = db_connect('data');

    $builder = $db->table('catalogs as a')
        ->select('a.ID, a.ID as action')
        ->select('a.ControlNumber, a.BIBID, a.Title, a.Author, a.Edition, a.Publisher, a.PublishLocation, a.PublishYear, a.Publikasi, a.Subject, a.PhysicalDescription, a.ISBN, a.CallNumber, a.Note, a.Languages, a.DeweyNo, a.ApproveDateOPAC, a.IsOPAC, a.IsBNI, a.IsKIN, a.IsRDA, a.CoverURL, a.Worksheet_id, a.CreateBy, a.CreateDate, a.CreateTerminal, a.UpdateBy, a.UpdateDate, a.UpdateTerminal, a.MARC_LOC, a.PRESERVASI_ID, a.QUARANTINEDBY, a.QUARANTINEDDATE, a.QUARANTINEDTERMINAL, a.Member_id, a.KIILastUploadDate')
        ->select('a.Branch_id, a.Location_id')
        ->select('0 as Eksemplar')
        ->where('a.IsQUARANTINE', $IsQUARANTINE)
        ->orderBy('a.ID', 'DESC');

    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->edit('ID', function ($row) {
            return '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
        })
        ->edit('BIBID', function ($row)  {
            helper('reference');
            $html  = $row->BIBID . '<br>';


            return $html;
        })
        ->edit('Eksemplar', function ($row) {
            helper('eksemplar');
            return count_collections($row->ID);
        })
        ->edit('IsRDA', function ($row) {
            $checked = $row->IsRDA == 1 ? 'checked' : '';
            return '<input type="checkbox" class="apply-status" data-href="' . base_url('api/katalog/switch/' . $row->ID) . '" data-checked="' . $checked . '" data-field="IsRDA" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="RDA" data-off="AACR" data-size="mini">';
        })
        ->edit('IsOPAC', function ($row) {
            $checked = $row->IsOPAC == 1 ? 'checked' : '';
            return '<input type="checkbox" class="apply-status" data-href="' . base_url('api/katalog/switch/' . $row->ID) . '" data-checked="' . $checked . '" data-field="IsOPAC" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
        })
        ->edit('action', function ($row) use ($IsQUARANTINE) {
            if ($row->IsRDA == 0) {
                $edit = '<a href="' . base_url('katalog/edit/' . $row->ID . '?rda=0') . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
            } else {
                $edit = '<a href="' . base_url('katalog/edit/' . $row->ID . '?rda=1') . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
            }

            $delete = '';
            // Only show delete button if IsQUARANTINE = 1
            if ($IsQUARANTINE == 1) {
                $delete = '<a href="' . base_url('katalog/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
            }

            return $edit . ($delete ? ' ' . $delete : '');
        })
        ->toJson();

    return $dataTable;
}

	public function katalog($IsQUARANTINE = 0)
	{
		$branch_id = $this->request->getGet('branch_id');
		$db = db_connect('data');

		$builder = $db->table('catalogs as a')
			->select('a.ID, a.ID as action')
			->select('a.ControlNumber, a.BIBID, a.Title, a.Author, a.Edition, a.Publisher, a.PublishLocation, a.PublishYear, a.Publikasi, a.Subject, a.PhysicalDescription, a.ISBN, a.CallNumber, a.Note, a.Languages, a.DeweyNo, a.ApproveDateOPAC, a.IsOPAC, a.IsBNI, a.IsKIN, a.IsRDA, a.CoverURL, a.Worksheet_id, a.CreateBy, a.CreateDate, a.CreateTerminal, a.UpdateBy, a.UpdateDate, a.UpdateTerminal, a.MARC_LOC, a.PRESERVASI_ID, a.QUARANTINEDBY, a.QUARANTINEDDATE, a.QUARANTINEDTERMINAL, a.Member_id, a.KIILastUploadDate')
			->select('a.Branch_id, a.Location_id')
			->select('0 as Eksemplar')
			->where('a.IsQUARANTINE', $IsQUARANTINE)
			->orderBy('a.ID', 'DESC');

		if (is_member('admin') || is_member('sa_prov') || is_member('sa_kabkot') || is_member('sa_psm')) {
			if (!empty($branch_id)) {
				$builder->where('a.Branch_id', $branch_id);
			} else {
				$builder->where('a.ID', 0);
			}
		}

		if (is_member('pustakawan')) {
			$builder->where('a.Branch_id', user()->branch_id);
			if (!empty(user()->location_ids)) {
				$locations = explode(',', user()->location_ids);
				if (!empty($locations)) {
					$builder->whereIn('a.Location_id', $locations);
				}
			}
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function ($row) {
				$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
				return $html;
			})
			->edit('BIBID', function ($row) {
				helper('reference');
				$html  = $row->BIBID . '<br>';

				if (!empty($branch_id)) {
					$html .= '<span class="badge badge-primary">' . get_ref_single('branchs', 'ID=' . $row->Branch_id, 'data')->Code . '</span><br>';
					$html .= '<span class="badge badge-info">' . get_ref_single('locations', 'ID=' . $row->Location_id, 'data')->Name . '</span> ';
				}

				return $html;
			})
			->edit('Eksemplar', function ($row) {
				helper('eksemplar');

				$html  = count_collections($row->ID);

				return $html;
			})
			->edit('IsRDA', function ($row) {
				$checked = $row->IsRDA == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/katalog/switch/' . $row->ID) . '" data-checked="' . $checked . '" data-field="IsRDA" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="RDA" data-off="AACR" data-size="mini">';
				return $html;
			})
			->edit('IsOPAC', function ($row) {
				$checked = $row->IsOPAC == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/katalog/switch/' . $row->ID) . '" data-checked="' . $checked . '" data-field="IsOPAC" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('katalog/edit/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah1" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';

				$delete = '<a href="javascript:void(0);" data-href="' . base_url('katalog/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		// Retrieve parameters from the request
		$fields = $this->request->getVar('fields') ?? []; // Fields to retrieve
		$search = $this->request->getVar('search') ?? ''; // Search search
		$limit = (int)($this->request->getVar('limit') ?? 10); // Number of records per page
		$page = (int)($this->request->getVar('page') ?? 1); // Current page number
		$offset = ($page - 1) * $limit; // Calculate offset for pagination
		$order = $this->request->getVar('order') ?? 'ID'; // Field to order by
		$direction = $this->request->getVar('direction') ?? 'desc'; // Sorting direction

		// Ensure fields is an array and contains valid field names
		if (is_string($fields)) {
			$fields = json_decode($fields, true);
		}

		// Clone the query builder to use for counting total records
		$countBuilder = clone $this->katalogModel;

		// Apply search filter to each specified field
		if (!empty($search)) {
			foreach ($fields as $field) {
				$countBuilder->orLike($field, $search);
			}
		}

		// Get the total record count after applying the search filter
		$total_record = $countBuilder->countAllResults(false);

		// Use the original query builder to get the paginated data
		$builder = $this->katalogModel;

		if (!empty($search)) {
			foreach ($fields as $field) {
				$builder->orLike($field, $search);
			}
		}

		if (!empty($order)) {
			$builder->orderBy($order, $direction);
		}

		$data = $builder->findAll($limit, $offset);

		$total_page = ceil($total_record / $limit);

		$response = [
			"total_record" => $total_record,
			"per_page" => $limit,
			"total_page" => $total_page,
			"current_page" => $page,
			"result" => [
				"error" => false,
				"param" => [
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
		$data = $this->katalogModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$slug = url_title($this->request->getPost('name') ?? '', '-', TRUE);
		$save_data = array(
			'code' => $this->request->getPost('code'),
			'name' => $this->request->getPost('name'),
			'slug' => $slug,
		);

		$save_data_id = $this->katalogModel->insert($save_data);
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
		$slug = url_title($this->request->getPost('name') ?? '', '-', TRUE);
		$update_data = array(
			'code' => $this->request->getPost('code'),
			'name' => $this->request->getPost('name'),
			'slug' => $slug,
		);

		$update_data_id = $this->katalogModel->update($id, $update_data);
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
		$data = $this->katalogModel->find($id);
		if ($data) {
			$this->katalogModel->delete($id);
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

		$update_data_id = $this->katalogModel->update($id, array($field => ($value == 'true') ? 1 : 0));

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

	public function upload_cover()
	{
		try {
			$upload_id = $this->request->getPost('upload_id');
			$upload_field = $this->request->getPost('upload_field');

			$files = (array) $this->request->getPost('upload_file');
			if (count($files)) {
				$data = [];
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = $file->getRandomName();

						$file->move($this->modulePath, $newFileName);
						$data = [
							$upload_field => $newFileName,
							'UpdateDate' => date('Y-m-d H:i:s'),
						];
					}
				}

				$updateData = $this->katalogModel->update($upload_id, $data);
				if ($updateData) {
					set_message('toastr_msg', 'File Cover berhasil diupload');
					set_message('toastr_type', 'success');

					return $this->respondCreated([
						'status'   => 201,
						'error'    => false,
						'messages' => ['success' => 'File Cover berhasil diupload']
					]);
				} else {
					set_message('toastr_msg', 'File Cover gagal diupload');
					set_message('toastr_type', 'warning');

					return $this->respond([
						'status'   => 400,
						'error'    => true,
						'messages' => ['error' => 'File Cover gagal diupload']
					]);
				}
			}
		} catch (\Exception $e) {
			set_message('toastr_msg', 'Terjadi kesalahan: ' . $e->getMessage());
			set_message('toastr_type', 'error');

			return $this->respond([
				'status'   => 500,
				'error'    => true,
				'messages' => ['error' => 'Terjadi kesalahan: ' . $e->getMessage()]
			]);
		}
	}
	public function upload_file()
{
    helper('auth');
    try {
        $upload_id = $this->request->getPost('upload_id');
        $upload_ref_id = $this->request->getPost('upload_ref_id');
        $upload_field = $this->request->getPost('upload_field');

        $files = (array) $this->request->getPost('upload_file');

        if (count($files)) {
            $insertData = [];
            $updateData = [];
            foreach ($files as $uuid => $name) {
                if (file_exists($this->uploadPath . $name)) {
                    $file = new File($this->uploadPath . $name);

					 // === Tambahan cek ukuran file ===
					$maxSize = 2 * 1024 * 1024; // 2MB
					if ($file->getSize() > $maxSize) {
						throw new \RuntimeException("Ukuran file '{$name}' melebihi batas 2MB.");
					}
					// === Akhir tambahan ===

                    $newFileName = $file->getRandomName();

                    // Move the file to the module path
                    $file->move($this->modulePath, $newFileName);

                    // Check if the file is a PDF
                    if (strtolower($file->getExtension()) === 'pdf') {
                        // Create an instance of the Encryption class
                        $encryption = new \App\Libraries\Encryption();

                        // Path to the moved file
                        $filePath = $this->modulePath . $newFileName;

                        // Path for the encrypted file
                        $encryptedFilePath = $this->modulePath . 'encrypted_' . $newFileName;

                        // Encrypt the file
                        $encryption->encryptFile($filePath, $encryptedFilePath);

                        // Delete the original unencrypted file
                        unlink($filePath);

                        // Update the filename to the encrypted version
                        $newFileName = 'encrypted_' . $newFileName;
                    }

                    if (!empty($upload_id)) {
                        $updateData = [
                            $upload_field => $newFileName,
                            'Catalog_id' => $upload_ref_id,
                            'UpdateDate' => date('Y-m-d H:i:s'),
                        ];
                    } else {
                        $data = [
                            $upload_field => $newFileName,
                            'Catalog_id' => $upload_ref_id,
                            'UpdateDate' => date('Y-m-d H:i:s'),
                        ];
                        $insertData[] = $data;
                    }
                }
            }

            if (!empty($updateData)) {
                $upsertData = $this->fileModel->update($upload_id, $updateData);
            }

            if (!empty($insertData)) {
                $upsertData = $this->fileModel->insertBatch($insertData);
            }

            if ($upsertData) {
                set_message('toastr_msg', 'File Konten Digital berhasil diupload');
                set_message('toastr_type', 'success');

                return $this->respondCreated([
                    'status'   => 201,
                    'error'    => false,
                    'messages' => ['success' => 'File Konten Digital berhasil diupload']
                ]);
            } else {
                set_message('toastr_msg', 'File Konten Digital gagal diupload');
                set_message('toastr_type', 'warning');

                return $this->respond([
                    'status'   => 400,
                    'error'    => true,
                    'messages' => ['error' => 'File Konten Digital gagal diupload']
                ]);
            }
        }
    } catch (\Exception $e) {
        set_message('toastr_msg', 'Terjadi kesalahan: ' . $e->getMessage());
        set_message('toastr_type', 'error');

        return $this->respond([
            'status'   => 500,
            'error'    => true,
            'messages' => ['error' => 'Terjadi kesalahan: ' . $e->getMessage()]
        ]);
    }
}

	

	public function view_decrypted($ID)
    {
        // Load the file model
  

        // Get the file record
        $file = $this->fileModel->find($ID);
		if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        // Instead of serving the file directly, we'll render a view with our custom PDF viewer
        return view('Katalog\Views\slug\pdf_viewer', ['fileId' => $ID, 'fileName' => $file->FileURL]);
    }

    public function get_decrypted_content($ID)
    {
       
        $file = $this->fileModel->find($ID);

        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $tempDecryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');
        $encryption = new \App\Libraries\Encryption();
        $encryption->decryptFile($this->modulePath . $file->FileURL, $tempDecryptedFile);

        $this->response->setContentType('application/pdf');
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $file->FileURL . '"');
        $this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->response->setHeader('Content-Security-Policy', "default-src 'self'; object-src 'self'");

        $content = file_get_contents($tempDecryptedFile);
        unlink($tempDecryptedFile);

        return $this->response->setBody($content);
    }
	public function get_all_tags($worksheet_id)
	{
		if ($worksheet_id === null) {
			return $this->fail('Field Worksheet ID tidak ditemukan.');
		}

		helper('katalog');
		$data = get_all_tags($worksheet_id);
		$filtered_tags = $data->filtered_tags;

		$response = array();
		foreach ($filtered_tags as $row) {
			array_push(
				$response,
				array('code' => $row['ID'], 'tag' => $row['Tag'], 'name' => $row['Name'])
			);
		}

		return $this->simpleResponse($response);
	}

	public function add_to_session($field_id = null)
	{
		if ($field_id === null) {
			return $this->fail('Field ID tidak ditemukan.');
		}

		helper('katalog');
		$data = add_to_session($field_id);
		$response = $data->worksheet_field;

		return $this->simpleResponse($response);
	}

	public function remove_from_session($field_id = null)
	{
		if ($field_id === null) {
			return $this->fail('Field ID tidak ditemukan.');
		}

		helper('katalog');
		$data = remove_from_session($field_id);
		$response = $data->worksheet_field;

		return $this->simpleResponse($response);
	}

	public function get_field_indicator1($field_id = null)
	{
		if ($field_id === null) {
			return $this->fail('Field ID tidak ditemukan.');
		}

		$db = db_connect('data');
		$builder = $db->table('fieldindicator1s as fi')
			->select('fi.Field_id as action, fi.Field_id, fi.Code, fi.Name')
			->where('fi.Field_id', $field_id);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('action', function ($row) {
				$choose = '<a href="javascript:void(0)" data-href="' . base_url('katalog/edit/' . $row->action) . '" class="btn btn-warning choose-data1" data-field="indicator1_' . $row->Field_id . '" data-value="' . $row->Code . '"><i class="pe-7s-note font-weight-bold"> </i> Pilih</a>';
				return $choose;
			})
			->toJson();

		return $dataTable;
	}

	public function get_field_indicator2($field_id = null)
	{
		if ($field_id === null) {
			return $this->fail('Field ID tidak ditemukan.');
		}

		$db = db_connect('data');
		$builder = $db->table('fieldindicator2s as fi')
			->select('fi.Field_id as action, fi.Field_id, fi.Code, fi.Name')
			->where('fi.Field_id', $field_id);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('action', function ($row) {
				$choose = '<a href="javascript:void(0)" data-href="' . base_url('katalog/edit/' . $row->action) . '" class="btn btn-warning choose-data2" data-field="indicator2_' . $row->Field_id . '" data-value="' . $row->Code . '"><i class="pe-7s-note font-weight-bold"> </i> Pilih</a>';
				return $choose;
			})
			->toJson();

		return $dataTable;
	}

	public function get_field_content($field_id = null)
	{
		if ($field_id === null) {
			return $this->fail('Field ID tidak ditemukan.');
		}

		$db = db_connect('data');
		$builder = $db->table('fielddatas as fi')
			->select('fi.Field_id as action, fi.Field_id, fi.Code, fi.Name, fi.Field_id as Input')
			->where('fi.Field_id', $field_id);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('Input', function ($row) {
				$html = '<input type="text" class="form-control input-content" code="' . $row->Code . '" field="' . $row->Field_id . '" value="">';
				return $html;
			})
			->edit('action', function ($row) {
				$choose = '<a href="javascript:void(0)" data-href="' . base_url('katalog/edit/' . $row->action) . '" class="btn btn-warning choose-data2" data-field="content_' . $row->Field_id . '" data-value="' . $row->Code . '"><i class="pe-7s-note font-weight-bold"> </i> Pilih</a>';
				return $choose;
			})
			->toJson();

		return $dataTable;
	}

	public function delete_file($ID)
    {
        $file = $this->fileModel->find($ID);
		if ($file) {
			$content = $this->modulePath . $file->FileURL;
			unlink($content);
			$this->fileModel->delete($file->ID);
			set_message('toastr_msg', 'File Konten Digital berhasil dihapus');
			set_message('toastr_type', 'success');
			set_message('message', 'File Konten Digital berhasil dihapus');
		} else {
			set_message('toastr_msg', 'File Konten Digital gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'File Konten Digital gagal dihapus');
		}
		return redirect()->back();
	}
}
