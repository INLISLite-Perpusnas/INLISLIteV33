<?php

namespace Media\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Media extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $mediaModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->mediaModel = new \Media\Models\MediaModel();
		helper(['reference']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect('backend');
		$builder = $db->table('t_page as a')
			->select('a.id, a.id as action, a.title, a.slug, a.content, a.file_image, a.file_cover, a.viewers, a.description,a.sort,  a.active')
			->select('a.category, a.category_sub, a.category_id')
			->select('a.created_at,  a.updated_at')
			->where('a.category', 'Media');

		if(!empty($slug)){
			$builder = $builder->where('a.category_sub', unslugify($slug));
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function($row){
				$html  =  '<b>'.$row->title.'</b><br>';
				return $html;
			})
			->edit('file_image', function($row){
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file_image)) ? base_url('uploads/media/' . $row->file_image) : $default;

				$html = '<a href="'.$image.'" class="image-link"><img width="100" class="rounded" src="'.$default.'" id="lazy'.$row->id.'" class="lazy" data-src="'.$image.'" onerror="this.onerror=null;this.src='.$default.';" alt=""></a>';
				return $html;
			})
			->edit('category', function($row){
				$html = '<span class="badge badge-primary badge-pill">'.$row->category.'</span> ';
				if(!empty($row->category_sub)){
					$html .= '<br><span class="badge badge-secondary badge-pill">'.$row->category_sub.'</span>';
				}
				return $html;
			})
			->edit('active', function($row){
				$status = $row->active == 1 ? 'Active' : 'Inactive';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-'.$class.'  badge-pill">'.$status.'</span>';
				return $html;
			})
			->edit('description', function($row){
				return character_limiter($row->description,100);
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/media/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/media/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/media/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/media/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function detail($slug)
	{
		try {
            $data = $this->mediaModel->where('slug', $slug)->first();
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
