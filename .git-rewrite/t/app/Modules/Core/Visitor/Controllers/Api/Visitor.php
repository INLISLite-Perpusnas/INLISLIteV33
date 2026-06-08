<?php

namespace Visitor\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Visitor extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $visitorModel;
    protected $validation;
    protected $session;
    protected $modulePath;
    protected $uploadPath;

    function __construct()
    {
        $this->visitorModel = new \Visitor\Models\VisitorModel();
        helper(['text', 'app', 'reference']);
    }

    public function datatable($slug = null)
    {
        $db = db_connect();
        $builder = $db
            ->table('c_visitors as a')
            ->select(
                'a.id, a.id as action, a.ip_address, a.created_at, a.updated_at, a.slug, a.hits, a.active'
            );

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('slug', function ($row) {
                return permalink($row->slug);
            })
            ->edit('action', function ($row) {
                $detail =
                    '<a href="' .
                    base_url('visitor/detail/' . $row->id) .
                    '" data-toggle="tooltip" data-placement="top" title="Detail" class="btn btn-primary show-data"><i class="fa fa-eye"> </i></a>';
                $delete =
                    '<a href="javascript:void(0);" data-href="' .
                    base_url('visitor/delete/' . $row->id) .
                    '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
                return $detail . ' ' . $delete;
            })
            ->toJson();
        return $dataTable;
    }

    public function index($slug = '/')
    {
        try {
            $ip_address = get_ip_address() ?? '127.0.0.1';

            $save_data = [
                'ip_address' => $ip_address,
                'slug' => $slug,
                'hits' => 1,
                'active' => 1,
            ];

            $visitorSave = $this->visitorModel->insert($save_data);
            if ($visitorSave) {
                $views = $this->visitorModel->selectSum('hits')->first();

                $response = [
                    'error' => false,
                    'messages' => 'Visitor berhasil disimpan',
                    'total' => $views->hits,
                ];
                return $this->respond($response);
            } else {
                $response = [
                    'error' => true,
                    'messages' => 'Visitor gagal disimpan',
                    'total' => $total,
                ];
                return $this->respond($response);
            }
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function detail($slug)
    {
        try {
            $data = $this->visitorModel->where('slug', $slug)->first();
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
