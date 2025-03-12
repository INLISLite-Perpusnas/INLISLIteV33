<?php

namespace Kontak\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Kontak extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $kontakModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->kontakModel = new \Kontak\Models\KontakModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/kontak/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['reference', 'thumbnail']);
	}

	public function datatable()
	{
		$db = db_connect('backend');
		$builder = $db->table('t_kontak as a')
			->select('a.id, a.id as action, a.name, a.email, a.phone, a.subject, a.message, a.description, a.active')
			->select('a.created_at,  a.updated_at');

		$dataTable = DataTable::of($builder)
		->addNumbering('no')
			->edit('name', function($row){
				$html = '<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<i class="far fa-user-alt fa-2x text-info"></i>
								</div>
								<div class="widget-content-left">
									<div class="widget-heading">'.$row->name.'</div>
									<div class="widget-subheading"><i class="fa fa-envelope"></i> '.$row->email.'</div>
									<div class="widget-subheading"><i class="fa fa-phone"></i> '.$row->phone.'</div>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('message', function($row){
				$html = '<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<i class="far fa-comments fa-2x text-info"></i>
								</div>
								<div class="widget-content-left">
									<b>'.$row->subject.'</b><br>
									<i>'.$row->message.'</i>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('created_at', function($row){
				// $html = '<span class="badge badge-info">'.$row->created_at.'</span>';
				$html = $row->created_at;
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/kontak/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/kontak/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $delete;
			})
			->toJson();

		return $dataTable;
	}

	public function index()
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'desc';
		$params['category'] = $params['category'] ?? '';
		$params['language'] = $params['language'] ?? 'id';
		$params['keyword'] = $params['keyword'] ?? '';
		$params['channel'] = $params['channel'] ?? '';

		$query = $this->kontakModel
			->where('active', 1);

		if(!empty($params['category'])){
			$query->where('category',$params['category']);
		}

		if(!empty($params['language'])){
			$query->where('language',$params['language']);
		}

		if(!empty($params['keyword'])){
			$query->like('title',$params['keyword']);
		}

		if(!empty($params['channel'])){
			$query->where('channel',$params['channel']);
		}

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

	public function detail($slug = null)
    {
		try {
            $data = $this->kontakModel->where('slug', $slug)->first();
			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with slug ' . $slug);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function show($id = null)
    {
		try {
            $data = $this->kontakModel->find($id);
			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with id ' . $id);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function create()
	{
		$save_data = array(
			'name' => $this->request->getPost('name'),
			'email' => $this->request->getPost('email'),
			'phone' => $this->request->getPost('phone'),
			'subject' => $this->request->getPost('subject'),
			'message' => $this->request->getPost('message'),
		);

		$id = $this->kontakModel->insert($save_data);
		if ($id) {
			$data = $this->kontakModel->find($id);
			$response = [
				'error'    => false,
				'message' => 'Data added successfully',
				'data'	=> $data,
			];
			return $this->simpleResponse($response);
		} else {
			$response = [
				'error'    => true,
				'message' => 'Data failed to add'
			];
			return $this->fail($response);
		}
	}

	public function update($id = null)
	{
		$this->validation->setRule('title', 'Title', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$slug = url_title($title, '-', TRUE);
			$update_data = array(
				'title' => $title,
				'slug' => $slug,
				'content' => $this->request->getPost('content')??'',
			);

			$kontakUpdate = $this->kontakModel->update($id, $update_data);
			if ($kontakUpdate) {
				$data = $this->kontakModel->find($id);
				$response = [
					'error'    => false,
					'data'	=> $data,
					'message' => 'Data updated successfully'
				];
				return $this->simpleResponse($response);
			} else {
				$response = [
					'error'    => true,
					'message' => 'Data failed to updated'
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->kontakModel->find($id);
		if ($data) {
			$delete = $this->kontakModel->delete($id);
			$response = [
				'error'    => false,
				'message' => 'Data deleted successfully'
			];
			return $this->simpleResponse($response);
		} else {
			return $this->failNotFound('Could not find data for specified ID' . $id);
		}
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
                    $newFileName = date('Ymd').'_'.$file->getRandomName();
                    $file->move($this->modulePath, $newFileName);
                    $listed_file[] = $newFileName;

					if($upload_field == 'file_image'){
						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
                }
            }
            $update_data[$upload_field] = implode(',', $listed_file);
        }

        $kontak= $this->kontakModel->find($upload_id);
        $kontakUpdate = $this->kontakModel->update($upload_id,$update_data);
        if ($kontakUpdate) {
			if($upload_field == 'file_image'){
				unlink_file($this->modulePath, $kontak->file_image);
				unlink_file($this->modulePath, 'thumb_'.$kontak->file_image);
			} else {
				unlink_file($this->modulePath, $kontak->file_pdf);
			}	

            $this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
            $this->session->setFlashdata('toastr_type', 'success');
            $response = [
                'status'   => 201,
                'error'    => false,
                'messages' => [
                    'success' => 'Upload file berhasil'
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status'   => 400,
                'error'    => false,
                'messages' => [
                    'error' => 'Upload file gagal'
                ]
            ];
            return $this->fail($response);
        }
	}
}
