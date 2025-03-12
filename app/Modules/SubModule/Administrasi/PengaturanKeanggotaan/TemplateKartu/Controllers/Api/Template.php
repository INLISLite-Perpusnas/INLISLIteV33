<?php

namespace TemplateKartu\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class TemplateKartu extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $templateModel;
    protected $validation;
    protected $session;
    protected $modulePath;
    protected $uploadPath;

    function __construct()
    {
        $this->templateModel = new \TemplateKartu\Models\TemplateModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->modulePath = ROOTPATH . 'public/uploads/master-template/';
        $this->uploadPath = WRITEPATH . 'uploads/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('reference');
    }

    public function datatable($slug = null)
    {
        $db = db_connect('backend');
        $builder = $db
            ->table('t_template as a')
            ->select('a.id, a.id as action, a.title, a.slug, a.content, a.description, a.sort, a.file_image, a.file_size, a.active, 0 as selected')
            ->select('a.category, a.category_sub');

        if (!empty($slug)) {
            $builder = $builder->where('a.category', unslugify($slug));
        }

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('title', function ($row) {
                $html  = '<b>' . $row->title . '</b>';
                return $html;
            })
            ->edit('category', function ($row) {
                $html = '<span class="badge badge-primary badge-pill">' . $row->category . '</span> ';
                if (!empty($row->category_sub)) {
                    $html .= '<br><span class="badge badge-secondary badge-pill">' . $row->category_sub . '</span>';
                }
                return $html;
            })
            ->edit('active', function ($row) {
                $checked = $row->active == 1 ? 'checked' : '';
                $html = '<input type="checkbox" class="apply-status" data-href="' . base_url('api/master-template-kartu/switch/' . $row->id) . '" data-checked="' . $checked . '" data-field="active" ' . $checked . ' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
                return $html;
            })
            ->edit('description', function ($row) {
                return character_limiter($row->description, 100);
            })
            ->edit('file_image', function ($row) {
                $default = base_url('uploads/default/no_cover.jpg');
                $image = (!empty($row->file_image)) ? base_url('uploads/master-template/' . $row->file_image) : $default;

                $html = '<a href="' . $image . '" class="image-link"><img width="100" class="rounded" src="' . $default . '" id="lazy' . $row->id . '" class="lazy" data-src="' . $image . '" onerror="this.onerror=null;this.src=' . $default . ';" alt=""></a>';
                return $html;
            })
            ->edit('file_size', function ($row) {
                $html  = $row->file_size ?? 0 . ' KB';
                return $html;
            })
            ->edit('action', function ($row) {
                $upload = '<a href="javascript:void(0);" 
					data-parent=""
					data-id="' . $row->id . '" 
					data-field="file_image"
					data-url="' . base_url('api/master-template-kartu/upload_file') . '"  
					data-dz_url="' . base_url('master-template-kartu/do_upload') . '" 
					data-controller="template" 
					data-title="Upload File"
					data-sub_title="Format (.docx). Max 10MB"
					data-redirect="' . base_url('master-template-kartu') . '" 
					data-format=".docx" 
					data-count="1" 
					data-max="10" 
					data-toggle="tooltip" data-placement="top" title="Upload File" class="btn btn-primary upload-data">
					<i class="fa fa-upload"> </i> </a>';
                $edit = '<a href="' . base_url('master-template-kartu/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
                $delete = '<a href="javascript:void(0);" data-href="' . base_url('master-template-kartu/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
                return $upload . ' ' . $edit . ' ' . $delete;
            })
            ->toJson();
        return $dataTable;
    }

    public function detail($slug)
    {
        try {
            $data = $this->templateModel->where('slug', $slug)->first();
            if ($data) {
                $response = [
                    'error' => false,
                    'message' => 'Show data successfully',
                    'data' => $data,
                ];
                return $this->simpleResponse($response);
            } else {
                return $this->failNotFound('No Data Found with slug ' . $slug);
            }
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function upload_file()
    {
        $upload_id = $this->request->getPost('upload_id');
        $upload_field = $this->request->getPost('upload_field');
        $upload_title = $this->request->getPost('upload_title');

        $update_data = [];
        $files = (array) $this->request->getPost('upload_data_file');
        if (count($files)) {
            $listed_file = array();
            $file_name = '';
            $file_size = '';
            foreach ($files as $uuid => $name) {
                if (file_exists($this->uploadPath . $name)) {
                    $file = new File($this->uploadPath . $name);
                    $file_name = $file->getRandomName();
                    $file_size = $file->getSizeByUnit('kb');
                    $file->move($this->modulePath, $file_name);
                }
            }
            $update_data[$upload_field] = $file_name;
            $update_data['file_size'] = $file_size;
        }
        $template = $this->templateModel->find($upload_id);
        $templateUpdate = $this->templateModel->update($upload_id, $update_data);
        if ($templateUpdate) {
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
        $slug = $this->request->getGet('slug') ?? 'depan';
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $update_data_id = $this->templateModel->update($id, array($field => ($value == 'true') ? 1 : 0));

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
}
