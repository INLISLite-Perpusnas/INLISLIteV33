<?php

namespace Banner\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Banner extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $bannerModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->bannerModel = new \Banner\Models\BannerModel();
		helper(['reference']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect('default');
		$builder = $db->table('t_banner as a')
			->select('a.id, a.id as action, a.title, a.slug, a.file_cover, a.description,a.sort,  a.active')
			->select('a.category');

		if (!empty($slug)) {
			$builder = $builder->where('a.category', unslugify($slug));
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function ($row) {
				// Kolom entitas: thumbnail cover + judul
				$default = base_url('uploads/default/no_cover.jpg');
				$image   = !empty($row->file_cover) ? base_url('uploads/banner/' . $row->file_cover) : $default;
				return '<div class="ina-cell-entity">'
					. '<a href="' . esc($image, 'attr') . '" class="image-link ina-thumb" aria-label="Lihat cover ' . esc($row->title, 'attr') . '">'
					. '<img src="' . esc($image, 'attr') . '" onerror="this.onerror=null;this.src=\'' . $default . '\';" alt="" width="72" height="48" loading="lazy">'
					. '</a><span class="ina-cell-title">' . esc($row->title) . '</span></div>';
			})
			->edit('category', function ($row) {
				return '<span class="ina-badge ina-badge-info">' . esc($row->category) . '</span>';
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
				$edit = '<a href="' . base_url('cms/banner/edit/' . $row->id) . '" class="ina-action" title="Ubah" aria-label="Ubah banner"><i class="fas fa-pen" aria-hidden="true"></i></a>';
				$toggle = '<a href="' . base_url('cms/banner/apply_status/' . $row->id . '?field=active&value=' . ($isActive ? 0 : 1)) . '" data-id="' . $row->id . '" class="ina-action ' . ($isActive ? 'draft-data' : 'active-data') . '" title="' . ($isActive ? 'Nonaktifkan' : 'Aktifkan') . '" aria-label="' . ($isActive ? 'Nonaktifkan banner' : 'Aktifkan banner') . '"><i class="fas ' . ($isActive ? 'fa-eye-slash' : 'fa-eye') . '" aria-hidden="true"></i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('cms/banner/delete/' . $row->id) . '" class="ina-action ina-action-danger remove-data" title="Hapus" aria-label="Hapus banner"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
				return '<div class="ina-actions">' . $edit . $toggle . $delete . '</div>';
			})
			->toJson();
		return $dataTable;
	}

	public function detail($slug)
	{
		try {
			$data = $this->bannerModel->where('slug', $slug)->first();
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
}
