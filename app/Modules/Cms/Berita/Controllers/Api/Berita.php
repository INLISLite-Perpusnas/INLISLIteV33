<?php

namespace Berita\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Berita extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $beritaModel;
    protected $validation;
    protected $session;
    protected $modulePath;
    protected $uploadPath;

    function __construct()
    {
        $this->beritaModel = new \Berita\Models\BeritaModel();
        helper(['reference']);
    }

    public function index()
    {
        // Contoh: ambil semua berita
        $data = $this->beritaModel->findAll();
        return $this->respond($data);
    }
    
    public function datatable($slug = null)
    {
        $db = db_connect('default');
        $builder = $db
            ->table('t_berita as a')
            ->select(
                'a.id, a.id as action, a.title, a.slug, a.content, a.file_image, a.viewers, a.description,a.sort,  a.active'
            )
            ->select('a.category, a.category_sub')
            ->select('a.file_image as cover');

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
                $html = '<span class="badge badge-primary badge-pill">' .$row->category . '</span> ';
                if (!empty($row->category_sub)) {
                    $html .='<br><span class="badge badge-secondary badge-pill">' .$row->category_sub .'</span>';
                }
                return $html;
            })
            ->edit('active', function ($row) {
                $status = $row->active == 1 ? 'Active' : 'Inactive';
                $class = $row->active == 1 ? 'success' : 'danger';
                $html ='<span class="badge badge-' . $class . '  badge-pill">' .$status .'</span>';
                return $html;
            })
            ->edit('description', function ($row) {
                return character_limiter($row->description, 100);
            })
            ->edit('action', function ($row) {
                $edit =
                    '<a href="' .
                    base_url('cms/berita/edit/' . $row->id) .
                    '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
                $active =
                    '<a href="' .
                    base_url(
                        'cms/berita/apply_status/' .
                            $row->id .
                            '?field=active&value=1'
                    ) .
                    '"  data-id="' .
                    $row->id .
                    '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
                $inactive =
                    '<a href="' .
                    base_url(
                        'cms/berita/apply_status/' .
                            $row->id .
                            '?field=active&value=0'
                    ) .
                    '" data-id="' .
                    $row->id .
                    '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
                $delete =
                    '<a href="javascript:void(0);" data-href="' .
                    base_url('cms/berita/delete/' . $row->id) .
                    '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
                return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
            })
            ->toJson();
        return $dataTable;
    }

    public function detail($slug)
    {
        try {
            $data = $this->beritaModel->where('slug', $slug)->first();
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
}
