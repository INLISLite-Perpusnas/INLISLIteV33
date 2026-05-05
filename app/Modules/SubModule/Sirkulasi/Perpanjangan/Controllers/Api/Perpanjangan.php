<?php

namespace Perpanjangan\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Perpanjangan extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $perpanjanganModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->perpanjanganModel = new \Perpanjangan\Models\PerpanjanganModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/perpanjangan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('peminjaman');
		helper('member');
		helper('perpanjangan');
	}

	public function datatable($slug = null)
{
    $db = db_connect();
    $builder = $db->table('collectionloanextends cle')
        ->select('cli.ID, cli.ID as action')
        ->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays, cli.LoanStatus')
        ->select('cle.ID as CountExtend, cle.DateExtend, cle.DueDateExtend, cle.UpdateDate, cle.CollectionLoanItem_id')
        ->select('col.NomorBarcode')
        ->select('a.Title, a.PublishLocation, a.Publisher, a.PublishYear')
        ->select('m.Fullname, m.MemberNo')
        ->select('loc.Name as LocationLibrary')
        ->join('collectionloanitems cli', 'cli.ID = cle.CollectionLoanItem_id')
        ->join('collectionloans cl',      'cl.ID = cli.CollectionLoan_id')
        ->join('collections col',         'col.ID = cle.Collection_id')
        ->join('catalogs a',              'a.ID = col.Catalog_id')
        ->join('members m',               'm.ID = cle.Member_id')
        ->join('location_library loc',    'loc.ID = col.Location_Library_id', 'left')
        ->orderBy('cle.DateExtend', 'DESC'); // ✅ Descending

    $dataTable = DataTable::of($builder)
        ->addNumbering('no')
        ->edit('CollectionLoan_id', function ($row) {
            return '
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left mr-3">
                            <i class="far fa-id-card fa-3x text-secondary"></i>
                        </div>
                        <div class="widget-content-left text-secondary">
                            <dl class="dl-horizontal mb-0">
                                <dt class="font-weight-bold mb-0"><i class="fa fa-user text-secondary"></i> No. Anggota</dt>
                                <dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->MemberNo . ' <span class="text-secondary">(' . $row->Fullname . ')</span></a></dd>
                                <dt class="font-weight-bold mb-0"><i class="fa fa-hashtag text-secondary"></i> No. Transaksi</dt>
                                <dd class="font-weight-bold mb-0 mr-1">&nbsp;: <a href="#">' . $row->CollectionLoan_id . '</a></dd>
                            </dl>
                        </div>
                    </div>
                </div>';
        })
        ->edit('NomorBarcode', function ($row) {
            return '
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left mr-3">
                            <i class="far fa-qrcode fa-2x text-info"></i>
                        </div>
                        <div class="widget-content-left">
                            <div class="widget-heading">' . $row->NomorBarcode . '</div>
                        </div>
                    </div>
                </div>';
        })
        ->edit('Title', function ($row) {
            return '
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left mr-3">
                            <i class="far fa-book fa-2x text-info"></i>
                        </div>
                        <div class="widget-content-left">
                            <div class="widget-heading text-primary">' . $row->Publisher . '</div>
                            <div class="widget-heading">' . $row->Title . '</div>
                        </div>
                    </div>
                </div>';
        })
        ->edit('LoanDate', function ($row) {
            return '
                <span class="badge badge-primary badge-pill">
                    <i class="fa fa-calendar"></i> ' . $row->LoanDate . '
                </span><br>
                <span class="badge badge-warning badge-pill mt-1">
                    <i class="fa fa-clock-o"></i> ' . $row->DueDateExtend . '
                </span>';
        })
        ->edit('CountExtend', function ($row) {
            $count = count_extend($row->CollectionLoanItem_id);
            return '<span class="badge badge-pill badge-secondary">Ke-' . $count . '</span>';
        })
        ->edit('DateExtend', function ($row) {
            return '
                <span class="badge badge-success badge-pill">
                    <i class="fa fa-refresh"></i> ' . $row->DateExtend . '
                </span>';
        })
        ->edit('LoanStatus', function ($row) {
            if ($row->LoanStatus === 'Loan') {
                return '
                    <span class="badge badge-pill" style="background:#dde8fb;color:#1B3878;font-size:12px;padding:6px 12px;">
                        <i class="fa fa-book"></i> Dipinjam
                    </span>';
            } elseif ($row->LoanStatus === 'Return') {
                return '
                    <span class="badge badge-pill" style="background:#edfaf4;color:#0a6e43;font-size:12px;padding:6px 12px;">
                        <i class="fa fa-check-circle"></i> Dikembalikan
                    </span>';
            }
            return '<span class="badge badge-secondary badge-pill">' . $row->LoanStatus . '</span>';
        })
       
        ->toJson();

    return $dataTable;
}

	public function loan_datatable()
	{
		$db = db_connect();
		$builder = $db->table('collections col')
			->select('cli.ID, cli.ID as action')
			->select('cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LateDays')
			->select('col.NomorBarcode, col.UpdateDate')
			->select('cat.Title, cat.PublishLocation, cat.Publisher, cat.PublishYear')
			->select('m.Fullname, m.MemberNo')
			->join('catalogs cat', 'cat.ID = col.Catalog_id')
			->join('collectionloanitems cli', 'cli.Collection_id = col.ID')
			->join('members m', 'm.ID = cli.member_id')
			->where('cli.LoanStatus', 'Loan');

		

		$cart_cli_arr = array();
		$carts = get_cart_extend();
		if (!empty($carts)) {
			foreach ($carts as $row) {
				$cart_cli_arr[] = $row->options->collection->ID;
			}
			if (!empty($cart_cli_arr)) {
				$builder->whereNotIn('col.ID', $cart_cli_arr);
			}
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('ID', function ($row) {
				$html = '';
				if ($row->DueDate > date('Y-m-d')) {
					$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->ID . '">';
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
							<div class="widget-heading">' . $row->NomorBarcode . '</div>
							<div class="widget-subheading">' . $row->MemberNo . '</div>
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
							<div class="widget-heading text-primary">' . $row->Publisher . '</div>
							<div class="widget-heading">' . $row->Title . '</div>
						</div>
					</div>
				</div>';
				return $html;
			})
			->edit('LoanDate', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . $row->LoanDate . '</badge>';
				$html .=  '<badge class="badge badge-warning badge-pill">' . $row->DueDate . '</badge>';
				return $html;
			})
			->edit('LateDays', function ($row) {
				if ($row->DueDate > date('Y-m-d')) {
					$periods = \Carbon\CarbonPeriod::create(date('Y-m-d'), $row->DueDate);
					$dates = [];
					foreach ($periods as $period) {
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '-' . count($dates);
					if (count($dates) <= 3) {
						if (count($dates) == 0) {
							$diff_class = 'info';
							$diff = '+' . count($dates);
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
						if (in_array($period->format('N'), ['6', '7'])) continue;
						$dates[] = $period->format('Y-m-d');
					}

					$diff = '+' . count($dates);
					$diff_class = 'danger';
				}

				$html  =  '<badge class="badge badge-' . $diff_class . ' badge-pill">' . $diff . ' hari</badge>';
				return $html;
			})
			->edit('UpdateDate', function ($row) {
				$html  =  '<badge class="badge badge-info badge-pill">' . $row->UpdateDate . '</badge>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="javascript:void(0);" data-href="' . base_url('api/sirkulasi-perpanjangan/detail/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('perpanjangan/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $delete;
			})
			->toJson();
		return $dataTable;
	}

	public function index()
	{
		$data = $this->perpanjanganModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		$data = $this->perpanjanganModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$save_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$save_data_id = $this->perpanjanganModel->insert($save_data);
		if ($save_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Perpanjangan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Perpanjangan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Perpanjangan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function edit($id = null)
	{
		$update_data = array(
			'LoanDate' => $this->request->getPost('LoanDate'),
			'DueDate' => $this->request->getPost('DueDate'),
			'LateDays' => $this->request->getPost('LateDays'),
			'ActualReturn' => $this->request->getPost('ActualReturn'),
		);

		$update_data_id = $this->perpanjanganModel->update($id, $update_data);
		if ($update_data_id) {
			$this->session->setFlashdata('toastr_msg', 'Perpanjangan berhasil disimpan');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'error' => false,
				'message' => 'Perpanjangan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Perpanjangan gagal disimpan. Silakan coba lagi',
			];
		}

		return $this->simpleResponse($response);
	}

	public function delete($id = null)
	{
		$data = $this->perpanjanganModel->find($id);
		if ($data) {
			$this->perpanjanganModel->delete($id);
			$response = [
				'error' => false,
				'message' => 'Perpanjangan berhasil dihapus',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Perpanjangan gagal dihapus. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}

	public function switch($id = null)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');

		$update_data_id = $this->perpanjanganModel->update($id, array($field => ($value == 'true') ? 1 : 0));

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Upload Dokumen Keanggotaan berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Upload Dokumen Keanggotaan gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
	}
}
