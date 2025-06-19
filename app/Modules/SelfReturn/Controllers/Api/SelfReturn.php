<?php

namespace SelfReturn\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;

class SelfReturn extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $pengembalianModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();

		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/pengembalian/';
		$this->uploadPath = WRITEPATH . 'uploads/';
		$this->cart = new \App\Libraries\Cart();

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		helper('reference');
		helper('pengembalian');
	}

	public function datatable($slug = null)
	{
		$db = db_connect('data');
		$builder = $db->table('collectionloans cl')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('cl.UpdateDate')
			->select('col.NomorBarcode')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->select('loc.Name as LocationLibrary')
			->join('collectionloanitems cli','cli.CollectionLoan_id = cl.ID')
			->join('collections col','col.ID = cli.Collection_id')
			->join('catalogs cat','cat.ID = col.Catalog_id')
			->join('members m','m.ID = cli.member_id')
			->join('location_library loc','loc.ID = col.Location_Library_id')
			->where('cli.LoanStatus', 'Return');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('CollectionLoan_id', function ($row) {
                $html =
                '<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-id-card fa-3x text-secondary"></i>
						</div>
						<div class="widget-content-left text-secondary">
							<dl class="dl-horizontal mb-0">
								<dt class="font-weight-bold mb-0"><i class="fa fa-user text-secondary"></i> No. Anggota</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">'.$row->MemberNo.'  <span class="text-secondary">('.$row->Fullname.')</span></a></dd>
								<dt class="font-weight-bold mb-0"><i class="fa fa-hashtag text-secondary"></i> No. Transaksi</dt>
								<dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">'.$row->CollectionLoan_id.'</a></dd>
							</dl>
						</div>
					</div>
				</div>';
                return $html;
            })
			->edit('NomorBarcode', function ($row) {
                $html =
                '<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' .$row->NomorBarcode .'</div>
						</div>
					</div>
				</div>';
                return $html;
            })
			->edit('Title', function ($row) {
                $html =
                '<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-book fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading text-primary">' .$row->Publisher.'</div>
							<div class="widget-heading">' .$row->Title.'</div>
						</div>
					</div>
				</div>';
                return $html;
            })
			->edit('LoanDate', function($row){
				$html  =  '<badge class="badge badge-primary badge-pill">'.$row->LoanDate.'</badge>';
				$html .=  '<badge class="badge badge-warning badge-pill">'.$row->DueDate.'</badge>';
				return $html;
			})
			->edit('ActualReturn', function($row){
				$html  =  '<badge class="badge badge-info badge-pill">'.$row->ActualReturn.'</badge>';
				return $html;
			})
			->edit('LateDays', function($row){
				$periods = \Carbon\CarbonPeriod::create($row->DueDate, $row->ActualReturn);
				$dates = [];
				foreach ($periods as $period) {
					if (in_array($period->format('N'), ['6','7'])) continue;
					$dates[] = $period->format('Y-m-d');
				}

				$diff = '+'.count($dates);
				if(count($dates) <= 3){
					if(count($dates) == 0){
						$diff_class = 'info';
						$diff = count($dates);
					} else {
						$diff_class = 'warning';
					}
				} else {
					$diff_class = 'danger';
				}

				$html  =  '<badge class="badge badge-'.$diff_class.' badge-pill">'.$diff.' hari</badge>';
				return $html;
			})
			->edit('UpdateDate', function($row){
				$html  =  '<badge class="badge badge-info badge-pill">'.$row->UpdateDate.'</badge>';
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="javascript:void(0);" data-href="'.base_url('api/pengembalian/detail/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('pengembalian/delete/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function loan_datatable()
	{
		$db = db_connect('data');
		$builder = $db->table('collections col')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('col.NomorBarcode, col.UpdateDate')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->join('catalogs cat','cat.ID = col.Catalog_id')
			->join('collectionloanitems cli','cli.Collection_id = col.ID')
			->join('members m','m.ID = cli.member_id')
			->where('cli.LoanStatus', 'Loan');

		$cart_cli_arr = array();
		$carts = get_cart_return();
		if(!empty($carts)){
			foreach($carts as $row){
				$cart_cli_arr[] = $row->options->collection->ID;
			}
			if(!empty($cart_cli_arr)){
				$builder->whereNotIn('col.ID',$cart_cli_arr);
			}
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function($row){
				$html = '';
				if($row->DueDate > date('Y-m-d')){
					$html = '<input type="checkbox" class="check" name="ID[]" value="'.$row->ID.'">';
				}
				return $html;
			})
			->edit('NomorBarcode', function ($row) {
                $html =
                '<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-qrcode fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading">' .$row->NomorBarcode .'</div>
							<div class="widget-subheading">' .$row->MemberNo .'</div>
						</div>
					</div>
				</div>';
                return $html;
            })
			->edit('Title', function ($row) {
                $html =
                '<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="far fa-book fa-2x text-info"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading text-primary">' .$row->Publisher.'</div>
							<div class="widget-heading">' .$row->Title.'</div>
						</div>
					</div>
				</div>';
                return $html;
            })
			->edit('LoanDate', function($row){
				$html  =  '<badge class="badge badge-primary badge-pill">'.$row->LoanDate.'</badge>';
				$html .=  '<badge class="badge badge-warning badge-pill">'.$row->DueDate.'</badge>';
				return $html;
			})
			->edit('LateDays', function($row){
				if($row->DueDate > date('Y-m-d')){
					$periods = \Carbon\CarbonPeriod::create(date('Y-m-d'), $row->DueDate);
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6','7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '-'.count($dates);
					if(count($dates) <= 3){
						if(count($dates) == 0){
							$diff_class = 'info';
							$diff = '+'.count($dates);
						} else {
							$diff_class = 'warning';
						}
					} else {
						$diff_class = 'secondary';
					}
					
				} else {
					$periods = \Carbon\CarbonPeriod::create($row->DueDate, date('Y-m-d'));
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6','7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '+'.count($dates);
					$diff_class = 'danger';
				}

				$html  =  '<badge class="badge badge-'.$diff_class.' badge-pill">'.$diff.' hari</badge>';
				return $html;
			})
			->edit('UpdateDate', function($row){
				$html  =  '<badge class="badge badge-info badge-pill">'.$row->UpdateDate.'</badge>';
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="javascript:void(0);" data-href="'.base_url('api/pengembalian/detail/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('pengembalian/delete/'.$row->ID).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}
}
