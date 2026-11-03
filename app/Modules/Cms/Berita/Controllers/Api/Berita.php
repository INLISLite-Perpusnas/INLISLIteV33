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
            ->select('a.file_image as cover')
            ->orderBy('a.created_at', 'DESC');

        if (!empty($slug)) {
            $builder = $builder->where('a.category', unslugify($slug));
        }

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('title', function ($row) {
                return '<span class="ina-cell-title">' . esc($row->title) . '</span>';
            })
            ->edit('category', function ($row) {
                $html = '<span class="badge badge-primary badge-pill">' .$row->category . '</span> ';
                if (!empty($row->category_sub)) {
                    $html .='<br><span class="badge badge-secondary badge-pill">' .$row->category_sub .'</span>';
                }
                return $html;
            })
            ->edit('active', function ($row) {
                return $row->active == 1
                    ? '<span class="ina-badge ina-badge-success">Aktif</span>'
                    : '<span class="ina-badge ina-badge-neutral">Nonaktif</span>';
            })
            ->edit('description', function ($row) {
                // Isi sel dibatasi maksimal 3 baris lewat CSS (ina-cell-clamp)
                return '<span class="ina-cell-clamp">' . esc(character_limiter(strip_tags((string) $row->description), 200)) . '</span>';
            })
            ->edit('action', function ($row) {
                // Ikon aksi konsisten: ubah, aktif/nonaktifkan (sesuai status), hapus
                $isActive = $row->active == 1;
                $edit = '<a href="' . base_url('cms/berita/edit/' . $row->id) . '" class="ina-action" title="Ubah" aria-label="Ubah berita"><i class="fas fa-pen" aria-hidden="true"></i></a>';
                $toggle = '<a href="' . base_url('cms/berita/apply_status/' . $row->id . '?field=active&value=' . ($isActive ? 0 : 1)) . '" data-id="' . $row->id . '" class="ina-action ' . ($isActive ? 'draft-data' : 'active-data') . '" title="' . ($isActive ? 'Nonaktifkan' : 'Aktifkan') . '" aria-label="' . ($isActive ? 'Nonaktifkan berita' : 'Aktifkan berita') . '"><i class="fas ' . ($isActive ? 'fa-eye-slash' : 'fa-eye') . '" aria-hidden="true"></i></a>';
                $delete = '<a href="javascript:void(0);" data-href="' . base_url('cms/berita/delete/' . $row->id) . '" class="ina-action ina-action-danger remove-data" title="Hapus" aria-label="Hapus berita"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
                return '<div class="ina-actions">' . $edit . $toggle . $delete . '</div>';
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
