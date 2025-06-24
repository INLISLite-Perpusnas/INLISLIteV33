<?php

namespace Eksemplar\Controllers;

use Eksemplar\Models\EksemplarModel as AkuisisiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;

class Eksemplar extends \Base\Controllers\BaseController
{
	use ResponseTrait;
	protected $eksemplarModel;
	
	protected $katalogModel;
	protected $katalogRuasModel;
	protected $db;

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
		$this->katalogModel = new \Katalog\Models\KatalogModel();
		$this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();
		$this->db = \Config\Database::connect('data');
	
       

		helper('reference');
		helper('katalog');
		helper('eksemplar');
	}

	public function index()
	{
		$this->data['title'] = 'Eksemplar';
		echo view('Eksemplar\Views\list', $this->data);
	}

	public function karantina()
	{
		$data['title'] = 'Karantina Eksemplar';
		echo view('Eksemplar\Views\list_karantina', $data);
	}

	public function create()
	{
		if (!is_allowed('eksemplar/create')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('eksemplar');
		}

		$this->data['title'] = 'Tambah Eksemplar';
		$slug = $this->request->getGet('slug');

		$this->validation->setRule('Catalog_id', 'Judul Katalog', 'required');
		$this->validation->setRule('Branch_id', 'Branch ID', 'required');
		$this->validation->setRule('Location_Library_id', 'Lokasi Perpustakaan', 'required');
		$this->validation->setRule('Location_id', 'Lokasi Ruang', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			helper('form');
			$post = $this->request->getPost();
			$redirect = $post['redirect'];

			$collections = [];
			$total = $post['JumlahEksemplar'];

			for ($i = 1; $i <= $total; $i++) {
				$save = [
					'Catalog_id' => $post['Catalog_id'],
					'Branch_id' => $post['Branch_id'],
					'ISDRM' => $post['ISDRM'],
					'Location_Library_id' => $post['Location_Library_id'],
					'Location_id' => $post['Location_id'],
					'NomorBarcode' => $post['NomorBarcode'][$i],
					'NoInduk' => $post['NoInduk'][$i],
					'RFID' => $post['RFID'][$i],
					'CallNumber' => $post['CallNumber'],
					'IsQUARANTINE' => '0',
				];

				if (!empty($post['TanggalPengadaan'])) {
					$save['TanggalPengadaan'] = $post['TanggalPengadaan'];
				}
				if (!empty($post['Rule_id'])) {
					$save['Rule_id'] = $post['Rule_id'];
				}
				if (!empty($post['Category_id'])) {
					$save['Category_id'] = $post['Category_id'];
				}
				if (!empty($post['Currency_id'])) {
					$save['Currency'] = $post['Currency_id'];
				}
				if (!empty($post['Media_id'])) {
					$save['Media_id'] = $post['Media_id'];
				}
				if (!empty($post['Source_id'])) {
					$save['Source_id'] = $post['Source_id'];
				}
				if (!empty($post['Status_id'])) {
					$save['Status_id'] = $post['Status_id'];
				}
				if (!empty($post['Partner_id'])) {
					$save['Partner_id'] = $post['Partner_id'];
				}
				if (!empty($post['Price'])) {
					$save['Price'] = $post['Price'];
				}
				if (!empty($post['PriceType'])) {
					$save['PriceType'] = $post['PriceType'];
				}

				array_push($collections, $save);
			}
			// dd($collections);

			if (!empty($collections)) {
				try {
					$this->eksemplarModel->insertBatch($collections);
					set_message('toastr_msg', 'Eksemplar berhasil ditambah');
					set_message('toastr_type', 'success');
				} catch (\Throwable $e) {
					set_message('toastr_msg', 'Eksemplar gagal ditambah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Eksemplar gagal ditambah');
				}

				$IsRedirect = $this->request->getPost('IsRedirect');
				if ($IsRedirect == 1) {
					if (!empty($redirect)) {
						return redirect()->to($redirect);
					} else {
						return redirect()->to('eksemplar');
					}
				} else {
					return redirect()->back()->withInput();
				}
			}
		} else {
			$this->data['redirect'] = base_url('eksemplar/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Eksemplar\Views\add', $this->data);
		}
	}

	public function edit($id)
	{
		if (!is_allowed('eksemplar/edit')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('eksemplar');
		}

		$this->data['title'] = 'Ubah Eksemplar';
		$this->data['is_allowed'] = !is_member('admin') && !is_member('sa_prov') && !is_member('sa_kabkota');

		$slug = $this->request->getGet('slug');
		$eksemplar = $this->eksemplarModel->find($id);
		$this->data['eksemplar'] = $eksemplar;
		if (!empty($eksemplar)) {
			$katalog = $this->katalogModel->find($eksemplar->Catalog_id);
			$this->data['katalog'] = $katalog;
		} else {
			return redirect()->to('/eksemplar');
		}

		$this->validation->setRule('Catalog_id', 'Judul Katalog', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			helper('form');
			$post = $this->request->getPost();
			$redirect = $post['redirect'];

			$update = [
				'Location_Library_id' => $post['Location_Library_id'],
				'Location_id' => $post['Location_id'],
				'NomorBarcode' => $post['NomorBarcode0'],
				'NoInduk' => $post['NoInduk0'],
				'RFID' => $post['RFID0'],
			];

			if (!empty($post['TanggalPengadaan'])) {
				$update['TanggalPengadaan'] = $post['TanggalPengadaan'];
			}
			if (!empty($post['Rule_id'])) {
				$update['Rule_id'] = $post['Rule_id'];
			}
			if (!empty($post['Category_id'])) {
				$update['Category_id'] = $post['Category_id'];
			}
			if (!empty($post['Currency_id'])) {
				$update['Currency'] = $post['Currency_id'];
			}
			if (!empty($post['Media_id'])) {
				$update['Media_id'] = $post['Media_id'];
			}
			if (!empty($post['Source_id'])) {
				$update['Source_id'] = $post['Source_id'];
			}
			if (!empty($post['Status_id'])) {
				$update['Status_id'] = $post['Status_id'];
			}
			if (!empty($post['Partner_id'])) {
				$update['Partner_id'] = $post['Partner_id'];
			}
			if (!empty($post['Price'])) {
				$update['Price'] = $post['Price'];
			}
			if (!empty($post['PriceType'])) {
				$update['PriceType'] = $post['PriceType'];
			}

			try {
				$this->eksemplarModel->update($id, $update);
				set_message('toastr_msg', 'Eksemplar berhasil diubah');
				set_message('toastr_type', 'success');
			} catch (\Throwable $e) {
				set_message('toastr_msg', 'Eksemplar gagal diubah');
				set_message('toastr_type', 'warning');
				set_message('message', 'Eksemplar gagal diubah');
			}

			$IsRedirect = $this->request->getPost('IsRedirect');
			if ($IsRedirect == 1) {
				if (!empty($redirect)) {
					return redirect()->to($redirect);
				} else {
					return redirect()->to('eksemplar');
				}
			} else {
				return redirect()->back()->withInput();
			}
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Eksemplar\Views\update', $this->data);
		}
	}

	public function create_action()
	{
		// echo '<pre>';
		helper('form');
		$model = new AkuisisiModel;
		$post = $this->request->getPost();
		$branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
		$BarcodeNumber = (int)preg_replace('/[^0-9]/', '', BarcodeNumber_helper());
		$NoInduk = (int)substr(NoInduk_helper(), strpos(NoInduk_helper(), "-") + 1);
		$RFID = (int)preg_replace('/[^0-9]/', '', RFID_helper());
		$validation = \Config\Services::validation();
		if ($validation->run($post, 'input_koleksi') == FALSE) {
			// session()->setFlashdata('inputs', $post);			
			session()->setFlashdata('errors', ['Harap Pilih Judul.']);
			return redirect()->to(base_url('/backend/collections/create'));
		}
		// print_r($post);
		// die;

		for ($i = 0; $i < (int)$post['jml_eksemplar']; $i++) {
			// echo RFID_helper() . "<br />";		
			// echo str_pad((int)$BarcodeNumber + $i, 11, '0', STR_PAD_LEFT) . "<br />";
			$save = [
				'NomorBarcode' => 'BRCD' . str_pad((int)$BarcodeNumber + $i, 11, '0', STR_PAD_LEFT),
				'NoInduk' => date("Y") . "-" . str_pad((int)$NoInduk + $i, 5, '0', STR_PAD_LEFT),
				'Currency' => $post['Currency'],
				'RFID' => 'RFID' . str_pad((int)$RFID + $i, 11, '0', STR_PAD_LEFT),
				'Price' => $post['Price'],
				'PriceType' => $post['PriceType'],
				'TanggalPengadaan' => $post['TANGGAL_PENGADAAN'],
				'CallNumber' => $post['CallNumber'],
				'Branch_id' => $branch_id,
				'Catalog_id' => $post['Catalog_id'],
				'Partner_id' => $post['Partner_id'],
				'Location_id' => $post['Location_id'],
				'Rule_id' => $post['Rule_id'],
				'Category_id' => $post['Category_id'],
				'Media_id' => $post['Media_id'],
				'Source_id' => $post['Source_id'],
				'Status_id' => $post['Status_id'],
				'Location_Library_id' => $post['Location_Library_id'],
				'Keterangan_Sumber' => null,
				'CreateBy' => 2,
				'CreateDate' => date("Y-m-d H:i:s"),
				'CreateTerminal' => null,
				'UpdateBy' => 2,
				'UpdateDate' => date("Y-m-d H:i:s"),
				'UpdateTerminal' => null,
				'IsVerified' => '',
				'IsQUARANTINE' => null,
				'QUARANTINEDBY' => null,
				'QUARANTINEDDATE' => null,
				'QUARANTINEDTERMINAL' => null,
				'ISREFERENSI' => null,
				'EDISISERIAL' => $post['EDISISERIAL'],
				// 'NOJILID' => $post['NOJILID'],
				'TANGGAL_TERBIT_EDISI_SERIAL' => $post['TANGGAL_TERBIT_EDISI_SERIAL'],
				'BAHAN_SERTAAN' => $post['BAHAN_SERTAAN'],
				'KETERANGAN_LAIN' => $post['KETERANGAN_LAIN'],
				'ISOPAC' => $post['IsOPAC'],
			];
			$model->save($save);
		}
		return redirect()->to(base_url() . '/backend/collections');
	}

	public function update($id)
	{
		helper('form');
		$model = new AkuisisiModel();
		$collections = $model->get_by_id($id);

		$data = [
			'agama' => $collections,
			'action' => base_url() . '/agama/update_action/' . $id,
			'title' => 'Ubah Agama',
			'button' => 'Ubah',
			'breadcrumb' => [
				'Agama',
				'Ubah Agama',
			],
		];
		echo view('templates/meta', $data);
		echo view('templates/header', $data);
		echo view('backend/akuisisi/collections_read', $data);
		echo view('templates/footer', $data);
		echo view('templates/rightsidebar', $data);
		echo view('templates/script', $data);
	}

	public function update_action($id)
	{
		// $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
		$ID = $this->request->getPost('ID');
		$NomorBarcode = $this->request->getPost('NomorBarcode');
		$NoInduk = $this->request->getPost('NoInduk');
		$Currency = $this->request->getPost('Currency');
		$RFID = $this->request->getPost('RFID');
		$Price = $this->request->getPost('Price');
		$PriceType = $this->request->getPost('PriceType');
		$TanggalPengadaan = $this->request->getPost('TanggalPengadaan');
		$CallNumber = $this->request->getPost('CallNumber');
		$Branch_id = $this->request->getPost('Branch_id');
		$Catalog_id = $this->request->getPost('Catalog_id');
		$Partner_id = $this->request->getPost('Partner_id');
		$Location_id = $this->request->getPost('Location_id');
		$Rule_id = $this->request->getPost('Rule_id');
		$Category_id = $this->request->getPost('Category_id');
		$Media_id = $this->request->getPost('Media_id');
		$Source_id = $this->request->getPost('Source_id');
		$Status_id = $this->request->getPost('Status_id');
		$Location_Library_id = $this->request->getPost('Location_Library_id');
		$Keterangan_Sumber = $this->request->getPost('Keterangan_Sumber');
		$CreateBy = $this->request->getPost('CreateBy');
		$CreateDate = $this->request->getPost('CreateDate');
		$CreateTerminal = $this->request->getPost('CreateTerminal');
		$UpdateBy = $this->request->getPost('UpdateBy');
		$UpdateDate = $this->request->getPost('UpdateDate');
		$UpdateTerminal = $this->request->getPost('UpdateTerminal');
		$IsVerified = $this->request->getPost('IsVerified');
		$QUARANTINEDBY = $this->request->getPost('QUARANTINEDBY');
		$QUARANTINEDDATE = $this->request->getPost('QUARANTINEDDATE');
		$QUARANTINEDTERMINAL = $this->request->getPost('QUARANTINEDTERMINAL');
		$ISREFERENSI = $this->request->getPost('ISREFERENSI');
		$EDISISERIAL = $this->request->getPost('EDISISERIAL');
		$NOJILID = $this->request->getPost('NOJILID');
		$TANGGAL_TERBIT_EDISI_SERIAL = $this->request->getPost('TANGGAL_TERBIT_EDISI_SERIAL');
		$BAHAN_SERTAAN = $this->request->getPost('BAHAN_SERTAAN');
		$KETERANGAN_LAIN = $this->request->getPost('KETERANGAN_LAIN');
		$TGLENTRYJILID = $this->request->getPost('TGLENTRYJILID');
		$IDJILID = $this->request->getPost('IDJILID');
		$NOMORPANGGILJILID = $this->request->getPost('NOMORPANGGILJILID');
		$ISOPAC = $this->request->getPost('ISOPAC');
		$JILIDCREATEBY = $this->request->getPost('JILIDCREATEBY');
		$KIILastUploadDate = $this->request->getPost('KIILastUploadDate');
		$BookingMemberID = $this->request->getPost('BookingMemberID');
		$BookingExpiredDate = $this->request->getPost('BookingExpiredDate');
		$IsDeposit = $this->request->getPost('IsDeposit');
		$NomorDeposit = $this->request->getPost('NomorDeposit');
		$ThnTerbitDeposit = $this->request->getPost('ThnTerbitDeposit');
		$deposit_ws_ID = $this->request->getPost('deposit_ws_ID');
		$deposit_kode_wilayah_ID = $this->request->getPost('deposit_kode_wilayah_ID');
		$Nomor_Regis = $this->request->getPost('Nomor_Regis');

		helper('form');

		$model = new AkuisisiModel();
		// $val = $this->_rules();		
		// if ($val->withRequest($this->request)->run() == false) {
		// 	$data = [
		// 		'agama' => [],
		// 		'action' => base_url() . '/agama/update_action/' . $id,
		// 		'title' => 'Daftar Agama',
		// 		'button' => 'Ubah',
		// 		'breadcrumb' => [
		// 			'Agama',
		// 			'Ubah Agama',
		// 		],
		// 		'validation' => $val
		// 	];
		// 	echo view('templates/meta', $data);
		// 	echo view('templates/header', $data);
		// 	echo view('backend/akuisisi/collections_read', $data);
		// 	echo view('templates/footer', $data);
		// 	echo view('templates/rightsidebar', $data);
		// 	echo view('templates/script', $data);
		// } else {
		// 	$update = [
		// 		'ID' => $ID,
		// 		'NomorBarcode' => $NomorBarcode,
		// 		'NoInduk' => $NoInduk,
		// 		'Currency' => $Currency,
		// 		'RFID' => $RFID,
		// 		'Price' => $Price,
		// 		'PriceType' => $PriceType,
		// 		'TanggalPengadaan' => $TanggalPengadaan,
		// 		'CallNumber' => $CallNumber,
		// 		'Branch_id' => $Branch_id,
		// 		'Catalog_id' => $Catalog_id,
		// 		'Partner_id' => $Partner_id,
		// 		'Location_id' => $Location_id,
		// 		'Rule_id' => $Rule_id,
		// 		'Category_id' => $Category_id,
		// 		'Media_id' => $Media_id,
		// 		'Source_id' => $Source_id,
		// 		'Status_id' => $Status_id,
		// 		'Location_Library_id' => $Location_Library_id,
		// 		'Keterangan_Sumber' => $Keterangan_Sumber,
		// 		'CreateBy' => $CreateBy,
		// 		'CreateDate' => $CreateDate,
		// 		'CreateTerminal' => $CreateTerminal,
		// 		'UpdateBy' => $UpdateBy,
		// 		'UpdateDate' => $UpdateDate,
		// 		'UpdateTerminal' => $UpdateTerminal,
		// 		'IsVerified' => $IsVerified,
		// 		'QUARANTINEDBY' => $QUARANTINEDBY,
		// 		'QUARANTINEDDATE' => $QUARANTINEDDATE,
		// 		'QUARANTINEDTERMINAL' => $QUARANTINEDTERMINAL,
		// 		'ISREFERENSI' => $ISREFERENSI,
		// 		'EDISISERIAL' => $EDISISERIAL,
		// 		'NOJILID' => $NOJILID,
		// 		'TANGGAL_TERBIT_EDISI_SERIAL' => $TANGGAL_TERBIT_EDISI_SERIAL,
		// 		'BAHAN_SERTAAN' => $BAHAN_SERTAAN,
		// 		'KETERANGAN_LAIN' => $KETERANGAN_LAIN,
		// 		'TGLENTRYJILID' => $TGLENTRYJILID,
		// 		'IDJILID' => $IDJILID,
		// 		'NOMORPANGGILJILID' => $NOMORPANGGILJILID,
		// 		'ISOPAC' => $ISOPAC,
		// 		'JILIDCREATEBY' => $JILIDCREATEBY,
		// 		'KIILastUploadDate' => $KIILastUploadDate,
		// 		'BookingMemberID' => $BookingMemberID,
		// 		'BookingExpiredDate' => $BookingExpiredDate,
		// 		'IsDeposit' => $IsDeposit,
		// 		'NomorDeposit' => $NomorDeposit,
		// 		'ThnTerbitDeposit' => $ThnTerbitDeposit,
		// 		'deposit_ws_ID' => $deposit_ws_ID,
		// 		'deposit_kode_wilayah_ID' => $deposit_kode_wilayah_ID,
		// 		'Nomor_Regis' => $Nomor_Regis,
		// 	];
		// 	$model->update_action($id, $update);
		// 	return redirect()->route('collections');
		// }
	}

	public function delete($id)
	{
		if (!is_allowed('eksemplar/delete')) {
			set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
			set_message('toastr_type', 'error');
			return redirect()->to('eksemplar');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('eksemplar');
		}
		$EksemplarDelete = $this->eksemplarModel->delete($id);
		if ($EksemplarDelete) {
			set_message('toastr_msg', 'Eksemplar berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('eksemplar');
		} else {
			set_message('toastr_msg', 'Eksemplar gagal dihapus');
			set_message('toastr_type', 'warning');
			set_message('message', 'Eksemplar gagal dihapus');
			return redirect()->to('eksemplar');
		}
	}

	
	public function print_label()
	{
		helper('thumbnail');
		$this->data['title'] = 'Cetak Label Eksemplar';

		$this->validation->setRule('eksemplar_ids', 'Eksemplar', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			helper('form');
			$post = $this->request->getPost();

			$template = $post['eksemplar_tpl'];
			$eksemplar_ids = $post['eksemplar_ids'];
			$eksemplar_ids_arr = explode(',', $eksemplar_ids);
		

			$db = db_connect('data');
			$builder = $db->table('collections as a')
				->select('a.ID, a.ID as action')
				->select('a.NomorBarcode')
				->select('b.Title, b.CallNumber')
				->join('catalogs b', 'b.ID=a.Catalog_id')
				->whereIn('a.ID', $eksemplar_ids_arr);

			$eksemplar_data = $builder->get()->getResultObject();

			$LabelData = [];
			foreach ($eksemplar_data as $row) {
				array_push($LabelData, array(
					'Title' => character_limiter($row->Title, 20),
					'Barcode' => $row->NomorBarcode,
					'CallNumber' => $row->CallNumber,
					'NamaPerpustakaan' => 'Perpusnas RI',
					'Warna1' => '#FFFF66',
					'BarcodePNG' => get_barcode_png($row->NomorBarcode),
				));
			}

			// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			// $pdf->SetPrintHeader(false);
			// $pdf->AddPage();
			view('Eksemplar\Views\template\\' . $template, array('LabelData' => $LabelData));
			// $pdf->writeHTML($html, true, false, false, false, '');
			// $pdf->Output('example_006.pdf', 'D');
			// die;
		} else {
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
		}
	}

	public function report()
	{
		helper('reference');

		$db = db_connect('data');
		$builder = $db->table('collections as a')
			->select('a.ID, a.ID as action, a.ID as Collection_id')
			->select('a.NomorBarcode, a.TanggalPengadaan, a.NoInduk, a.Catalog_id, a.IsOPAC')
			->select('a.Branch_id, a.Location_id')
			->join('branchs b', 'b.ID = a.Branch_id', 'inner')
			->where('a.IsQUARANTINE', 0);

		if (user()->category == 'admin') {
		} elseif (user()->category == 'sa_prov' && user()->branch_id === null) {
			$npp_provinsi_id = preg_replace('/\./', '', user()->npp_provinsi_id);
			$builder->where('b.NPP_Provinsi_id', $npp_provinsi_id);
		} elseif (user()->category == 'sa_prov' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id === null) {
			$npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
			$builder->where('b.NPP_KabKota_id', $npp_kabkota_id);
		} elseif (user()->category == 'sa_kabkot' && user()->branch_id !== null) {
			$builder->where('a.Branch_id', branch_id());
		} else {
			$builder->where('a.Branch_id', branch_id());
		}

		$results = $builder->get()->getResult();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:E1');
		$sheet->setCellValue("A1", "Laporan Eksemplar");
		$sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "Nomor Barcode");
		$sheet->setCellValue("B2", "Tanggal Pengadaan");
		$sheet->setCellValue("C2", "Nomor Induk");
		$sheet->setCellValue("D2", "Data Bibliografis");
		$sheet->setCellValue("E2", "OPAC");

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(100);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);

		$col = 3;
		$no = 1;
		$i = 1;
		foreach ($results as $row) {
			$catalog = get_ref_single('catalogs', 'ID=' . $row->Catalog_id, 'data');
			$bibliography  = ($catalog->Title ?? "") . '\n' . ($catalog->Publikasi ?? "");

			$sheet->setCellValue("A" . $col, $row->NomorBarcode);
			$sheet->setCellValue("B" . $col, $row->TanggalPengadaan);
			$sheet->setCellValue("C" . $col, $row->NoInduk);
			$sheet->setCellValue("D" . $col, $bibliography);
			$sheet->setCellValue("E" . $col, $row->IsOPAC);

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Laporan Eksemplar';
		$filename = ucwords($subject) . '-' . date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}


	public function proses_karantina()
	{
		// Ambil data dari POST
		$eksemplar_ids = $this->request->getPost('eksemplar_ids');
	
		// Jika eksemplar_ids berupa string "3,4", ubah menjadi array
		if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
			$eksemplar_ids = array_map('trim', explode(',', $eksemplar_ids));
		}
	
		// Validasi: Pastikan eksemplar_ids berisi angka
		if (empty($eksemplar_ids) || !is_array($eksemplar_ids) || !array_filter($eksemplar_ids, 'is_numeric')) {
			$this->session->setFlashdata('error', 'Tidak ada eksemplar yang dipilih atau data tidak valid');
			return redirect()->back();
		}
	
		// Koneksi ke database
		$db = db_connect('data');
		try {
			// Perbarui status IsQUARANTINE menjadi 1
			$builder = $db->table('collections');
			$builder->whereIn('ID', $eksemplar_ids);
			$builder->update(['IsQUARANTINE' => 1]);
	
			// Periksa apakah ada baris yang diperbarui
			if ($db->affectedRows() > 0) {
				
				set_message('toastr_msg', 'Eksemplar berhasil dikarantina');
			set_message('toastr_type', 'success');
			} else {
				$this->session->setFlashdata('info', "Tidak ada perubahan data, mungkin eksemplar sudah dikarantina");
			}
	
			return redirect()->back();
		} catch (\Exception $e) {
			set_message('toastr_msg', 'plih item terlebih dahulu');
			set_message('toastr_type', 'error');
			return redirect()->back();
		}
	}

	public function pulihkan_eksemplar()
	{
		$eksemplar_ids = $this->request->getVar('ID');
    
		// We need to modify the JavaScript to properly pass the IDs
		$form = $this->request->getVar();
		
		// If we have IDs as checkboxes (ID[]), handle that format
		if (isset($form['ID']) && is_array($form['ID'])) {
			$eksemplar_ids = $form['ID'];
		}
		
		// If no IDs, redirect with error
		if (empty($eksemplar_ids)) {
			session()->setFlashdata('error', 'Tidak ada eksemplar yang dipilih');
			return redirect()->back();
		}
		
		try {
			// Update IsQUARANTINE value to 0 for selected items
			$builder = $this->db->table('collections');
			$builder->whereIn('ID', $eksemplar_ids);
			$builder->update(['IsQUARANTINE' => 0]);
			
			// Get count of affected rows
			$affectedRows = $this->db->affectedRows();
			
			set_message('toastr_msg', 'Eksemplar berhasil dipulihkan');
			set_message('toastr_type', 'success');
			return redirect()->to(base_url('eksemplar'));
			
		} catch (\Exception $e) {
			set_message('toastr_msg', 'plih item terlebih dahulu');
			set_message('toastr_type', 'error');
			return redirect()->back();
		}
	}
	
    
    /**
     * Process OPAC display for selected collection items
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function proses_opac()
    {
        // Get the eksemplar_ids from the POST data
        $eksemplar_ids = $this->request->getPost('eksemplar_ids');
        
        // If eksemplar_ids is a string like "3,4", convert it to an array
        if (!is_array($eksemplar_ids) && is_string($eksemplar_ids)) {
            $eksemplar_ids = explode(',', $eksemplar_ids);
            $eksemplar_ids = array_map('trim', $eksemplar_ids);
        }
        
        // If still empty or not an array, redirect with error
        if (empty($eksemplar_ids) || !is_array($eksemplar_ids)) {
            $this->session->setFlashdata('error', 'Tidak ada eksemplar yang dipilih');
            return redirect()->back();
        }
        
        try {
            // Update IsOPAC value to 1 for selected items
            $builder = $this->db->table('collections');
            $builder->whereIn('ID', $eksemplar_ids);
            $builder->update(['IsOPAC' => 1]);
            
            // Get count of affected rows
            $affectedRows = $this->db->affectedRows();
            
			set_message('toastr_msg', 'Eksemplar berhasil dikarantina');
			set_message('toastr_type', 'success');
            return redirect()->back();
            
        } catch (\Exception $e) {
			set_message('toastr_msg', 'plih item terlebih dahulu');
			set_message('toastr_type', 'error');
            return redirect()->back();
        }
    }
    
    // Other controller methods would go here...
// bagian import eksemplar
	public function importviews()
    {
        $this->data['title'] = 'Import eksemplar Excel';
        return view('Eksemplar\Views\import', $this->data);
    }
    
    public function uploadexcel()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('katalog/import'));
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'excel_file' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,10240]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }
        
        $file = $this->request->getFile('excel_file');
        
        if (!$file->isValid()) {
            return $this->fail(['excel_file' => 'File tidak valid']);
        }
        
        try {
            // Pastikan direktori upload ada
            if (!is_dir(WRITEPATH . 'uploads/temp')) {
                mkdir(WRITEPATH . 'uploads/temp', 0755, true);
            }
            
            // Move file to temp directory
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $fileName);
            $filePath = WRITEPATH . 'uploads/temp/' . $fileName;
            
            // Load spreadsheet menggunakan PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            $header = array_shift($rows);
            
            // Process import
            $result = $this->processImport($rows, $header);
            
            // Delete temp file
            unlink($filePath);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Import berhasil',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            // Delete temp file if exists
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            
            return $this->fail([
                'message' => 'Error saat import: ' . $e->getMessage()
            ]);
        }
    }
    
    private function processImport($rows, $header)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        $this->db->transBegin();
        
        try {
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows
                
                $rowNumber = $rowIndex + 2; // +2 karena array dimulai dari 0 dan ada header
                
                try {
				
                    // Parse row data
                    $catalogData = $this->parseCatalogData($row, $header);
				
                    $collectionsData = $this->parseCollectionsData($row, $header);
					dd($collectionsData);
                    $marcFields = $this->parseMarcFields($row, $header);
					dd($marcFields);
                    
                    // Insert catalog
                    $catalogId = $this->insertCatalog($catalogData);
                    
                    // Insert MARC fields
                    if (!empty($marcFields)) {
                        $this->insertMarcFields($catalogId, $marcFields);
                    }
                    
                    // Insert collections
                    if (!empty($collectionsData)) {
                        $this->insertCollections($catalogId, $collectionsData);
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }
            
            if ($errorCount > 0 && $successCount == 0) {
                $this->db->transRollback();
                throw new \Exception("Semua data gagal diimport. Errors: " . implode('; ', array_slice($errors, 0, 5)));
            }
            
            $this->db->transCommit();
            
            return [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => array_slice($errors, 0, 10) // Batasi error yang ditampilkan
            ];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
	private function getValue($row, $headerMap, $columnName, $default = '')
{
    // Cek apakah column name ada di header mapping
    if (isset($headerMap[$columnName])) {
        // Ambil index column dari header mapping
        $columnIndex = $headerMap[$columnName];
        
        // Ambil value dari row berdasarkan index
        $value = $row[$columnIndex] ?? $default;
        
        // Trim whitespace jika value adalah string
        return is_string($value) ? trim($value) : $value;
    }
    
    // Return default jika column tidak ditemukan
    return $default;
}
    
    private function parseCatalogData($row, $header)
    {
       $headerMap = array_flip($header);
        
        // Generate ControlNumber unik dengan format INLIS000000000004123
        $controlNumber = $this->generateUniqueControlNumber();
        
        // Parse judul lengkap
        $judulUtama = $this->getValue($row, $headerMap, 'JUDUL_UTAMA');
        $anakJudul = $this->getValue($row, $headerMap, 'ANAK_JUDUL');
        $title = $judulUtama . ($anakJudul ? ' : ' . $anakJudul : '');
        
        // Parse pengarang
        $author = $this->getValue($row, $headerMap, 'TAJUK_PENGARANG');
        
        // Parse penerbit info
        $publisher = $this->getValue($row, $headerMap, 'PENERBIT');
        $publishLocation = $this->getValue($row, $headerMap, 'KOTA_TERBIT');
        $publishYear = $this->getValue($row, $headerMap, 'TAHUN_TERBIT');
        
        // Parse physical description
        $jumlahHalaman = $this->getValue($row, $headerMap, 'JUMLAH_HALAMAN');
        $dimensi = $this->getValue($row, $headerMap, 'DIMENSI');
        $physicalDescription = $jumlahHalaman . ($dimensi ? ' ; ' . $dimensi : '');
        
        $data = [
            'ControlNumber' => $controlNumber,
            'BIBID' => $this->generateBibId($controlNumber), // Generate BIBID based on ControlNumber
            'Title' => $title,
            'Author' => $author,
            'Edition' => $this->getValue($row, $headerMap, 'EDISI'),
            'Publisher' => $publisher,
            'PublishLocation' => $publishLocation,
            'PublishYear' => $publishYear,
            'Subject' => $this->getValue($row, $headerMap, 'SUBJEK_TOPIK'),
            'PhysicalDescription' => $physicalDescription,
            'ISBN' => $this->getValue($row, $headerMap, 'ISBN'),
            'CallNumber' => $this->getValue($row, $headerMap, 'NOMOR_PANGGIL_KATALOG'),
            'Note' => $this->getValue($row, $headerMap, 'ABSTRAK'),
            'Languages' => $this->getValue($row, $headerMap, 'BAHASA'),
            'DeweyNo' => $this->getValue($row, $headerMap, 'NO_DDC'),
            'CreateBy' => user()->id ?? 1,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateTerminal' => $this->request->getIPAddress(),
            'Branch_id' => user()->branch_id ?? 1,
            'Location_id' => 1,
            'IsOPAC' => 1,
            'IsBNI' => 1,
            'IsKIN' => 1,
            'IsRDA' => 1,
            'active' => 1
        ];
        
        // Validasi required fields
        if (empty($data['Title'])) {
            throw new \Exception('Judul utama tidak boleh kosong');
        }
        
        // Log untuk debugging
        log_message('debug', 'Generated ControlNumber: ' . $controlNumber . ' for title: ' . $title);
        
        return $data;
    }

	 // Method untuk generate BIBID berdasarkan ControlNumber
    private function generateBibId($controlNumber)
    {
        // Extract number part dari ControlNumber
        $numberPart = substr($controlNumber, 5); // Remove 'INLIS' prefix
        
        // Format BIBID: 0010-092 + last 7 digits
        $lastSevenDigits = substr($numberPart, -7);
        $bibId = '0010-092' . $lastSevenDigits;
        
        return $bibId;
    }
    
    // Method untuk generate sequence yang aman untuk concurrent access
    private function getNextSequenceNumber()
    {
        // Gunakan database untuk generate sequence yang thread-safe
        $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(ControlNumber, 6) AS UNSIGNED)), 0) + 1 as next_num 
                FROM catalog 
                WHERE ControlNumber LIKE 'INLIS%'";
        
        $result = $this->db->query($sql)->getRow();
        
        return $result ? $result->next_num : 1;
    }
    
    // Alternative method dengan database sequence (lebih robust)
    private function generateControlNumberWithSequence()
    {
        try {
            // Start transaction untuk ensure atomicity
            $this->db->transBegin();
            
            // Get next sequence number
            $nextNumber = $this->getNextSequenceNumber();
            
            // Format ControlNumber
            $controlNumber = 'INLIS' . str_pad($nextNumber, 14, '0', STR_PAD_LEFT);
            
            // Commit transaction
            $this->db->transCommit();
            
            return $controlNumber;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw new \Exception('Failed to generate ControlNumber: ' . $e->getMessage());
        }
	}
    private function parseCollectionsData($row, $header)
    {
        $collections = [];
        $headerMap = array_flip($header);
        
        if (isset($headerMap['Collections'])) {
            $collectionsText = $row[$headerMap['Collections']] ?? '';
            
            if (!empty($collectionsText)) {
                // Parse collections data (format: barcode1|noinduk1|price1;barcode2|noinduk2|price2)
                $collectionLines = explode(';', $collectionsText);
                
                foreach ($collectionLines as $line) {
                    if (empty(trim($line))) continue;
                    
                    $parts = explode('|', $line);
                    if (count($parts) >= 2) {
                        $collections[] = [
                            'NomorBarcode' => trim($parts[0]),
                            'NoInduk' => trim($parts[1]),
                            'Price' => isset($parts[2]) ? (int)$parts[2] : 0,
                            'Currency' => 'IDR',
                            'RFID' => trim($parts[0]), // Same as barcode
                            'PriceType' => 'Per eksemplar',
                            'TanggalPengadaan' => date('Y-m-d H:i:s'),
                            'Branch_id' => user()->branch_id ?? 1,
                            'Partner_id' => 1,
                            'Location_id' => 466,
                            'Rule_id' => 1,
                            'Category_id' => 7,
                            'Media_id' => 2,
                            'Source_id' => 1,
                            'Status_id' => 1,
                            'Location_Library_id' => 1,
                            'CreateBy' => user()->id ?? 1,
                            'CreateDate' => date('Y-m-d H:i:s'),
                            'CreateTerminal' => $this->request->getIPAddress()
                        ];
                    }
                }
            }
        }
        
        return $collections;
    }
    
    private function parseMarcFields($row, $header)
    {
        $marcFields = [];
        $headerMap = array_flip($header);
        
        if (isset($headerMap['MARC_Fields'])) {
            $marcText = $row[$headerMap['MARC_Fields']] ?? '';
            
            if (!empty($marcText)) {
                // Parse MARC fields (format: tag1:indicator1:indicator2:value1;tag2:indicator1:indicator2:value2)
                $marcLines = explode(';', $marcText);
                $sequence = 1;
                
                foreach ($marcLines as $line) {
                    if (empty(trim($line))) continue;
                    
                    $parts = explode(':', $line, 4);
                    if (count($parts) >= 4) {
                        $marcFields[] = [
                            'Tag' => trim($parts[0]),
                            'Indicator1' => trim($parts[1]) ?: '#',
                            'Indicator2' => trim($parts[2]) ?: '#',
                            'Value' => trim($parts[3]),
                            'Sequence' => $sequence++,
                            'CreateBy' => user()->id ?? 1,
                            'CreateDate' => date('Y-m-d H:i:s'),
                            'CreateTerminal' => $this->request->getIPAddress(),
                            'Branch_id' => user()->branch_id ?? 1,
                            'active' => 1
                        ];
                    }
                }
            }
        }
        
        return $marcFields;
    }
    
    private function insertCatalog($data)
    {
        // Check if ControlNumber already exists
        $existing = $this->katalogModel->where('ControlNumber', $data['ControlNumber'])->first();
        if ($existing) {
            throw new \Exception("ControlNumber {$data['ControlNumber']} sudah ada");
        }
        
        if (!$this->katalogModel->insert($data)) {
            $errors = $this->katalogModel->errors();
            throw new \Exception("Gagal insert catalog: " . implode(', ', $errors));
        }
        
        return $this->katalogModel->getInsertID();
    }
    
    private function insertMarcFields($catalogId, $marcFields)
    {
        foreach ($marcFields as $field) {
            $field['CatalogId'] = $catalogId;
            
            if (!$this->katalogRuasModel->insert($field)) {
                $errors = $this->katalogRuasModel->errors();
                throw new \Exception("Gagal insert MARC field: " . implode(', ', $errors));
            }
        }
    }
    
    private function insertCollections($catalogId, $collections)
    {
        foreach ($collections as $collection) {
            $collection['Catalog_id'] = $catalogId;
            
            // Check if barcode already exists
            $existing = $this->eksemplarModel->where('NomorBarcode', $collection['NomorBarcode'])->first();
            if ($existing) {
                throw new \Exception("Barcode {$collection['NomorBarcode']} sudah ada");
            }
            
            if (!$this->eksemplarModel->insert($collection)) {
                $errors = $this->eksemplarModel->errors();
                throw new \Exception("Gagal insert collection: " . implode(', ', $errors));
            }
        }
    }
    
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers sesuai format yang diminta
        $headers = [
            'NO', 'TGL_PENGADAAN', 'NO_INDUK', 'NO_BARCODE', 'NO_RFID', 'JENIS_SUMBER', 'NAMA_SUMBER',
            'MATA_UANG', 'HARGA', 'KODE_LOKASI_PERPUSTAKAAN', 'KODE_LOKASI_RUANG', 'AKSES', 'KATEGORI',
            'MEDIA', 'KETERSEDIAAN', 'NOMOR_PANGGIL_EKSEMPLAR', 'JENIS_BAHAN', 'JUDUL_UTAMA', 'ANAK_JUDUL',
            'PERNYATAAN_TANGGUNGJAWAB', 'TAJUK_PENGARANG', 'TAJUK_PENGARANG_BADAN_KOOPERASI',
            'PENGARANG_TAMBAHAN_NAMA_ORANG', 'PENGARANG_TAMBAHAN_NAMA_BADAN', 'EDISI', 'KOTA_TERBIT',
            'PENERBIT', 'TAHUN_TERBIT', 'JUMLAH_HALAMAN', 'DIMENSI', 'ISBN', 'ISSN', 'ISMN', 'NO_DDC',
            'NOMOR_PANGGIL_KATALOG', 'ABSTRAK', 'BAHASA', 'SUBJEK_TOPIK', 'EDISI_SERIAL', 'TGL_TERBIT_EDISI_SERIAL',
            'BAHAN_SERTAAN_SERIAL', 'KETERANGAN_LAIN_SERIAL'
        ];
        
        $sheet->fromArray([$headers], null, 'A1');
        
        // Style header
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);
        
        // Add sample data (5 rows)
        $sampleData = [
            [
                1, '14-02-2015', 'X0022/2016', 'X0022/2016', 'X0022/2016', 'Hadiah/Hibah', '---Belum ditentukan---',
                'IDR', 0, 'Pusat', '0101', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '123 PRA m',
                'Monograf', 'Mahligai Biru', '', 'Mamik Pradana', 'Pradana, Mamik', '', '', '', '', 'Jakarta',
                'Grafika', '2015', '120 hlm.', '25 cm.', '978-222-666-444', '', '', '123', '123 PRA m', '',
                'ind', 'Rumah Tangga', '', '', '', ''
            ],
            [
                2, '15-02-2015', 'X0023/2016', 'X0023/2016', 'X0023/2016', 'Pembelian', '---Belum ditentukan---',
                'IDR', 0, 'Pusat', '0101', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '201 SAM k',
                'Monograf', 'Kancil dan Kerbau', '', 'Deni Saman', 'Saman, Deni', '', '', '', '', 'Jakarta',
                'Prabu', '2015', '68 hlm.', '21 cm.', '856-225-456-78', '', '', '201', '201 SAM k', '',
                'ind', 'Fiksi', '', '', '', ''
            ],
            [
                3, '16-03-2015', 'X0024/2016', 'X0024/2016', 'X0024/2016', 'Pembelian', 'Toko Buku Mandiri',
                'IDR', 75000, 'Pusat', '0102', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '004.678 BUD p',
                'Monograf', 'Pemrograman Web dengan PHP', 'Panduan Lengkap untuk Pemula', 'Budi Raharjo', 'Raharjo, Budi', '', '', '', 'Edisi 2', 'Bandung',
                'Informatika', '2015', '350 hlm.', '24 cm.', '978-602-1234-567-8', '', '', '004.678', '004.678 BUD p', 'Buku panduan pemrograman web menggunakan PHP',
                'ind', 'Teknologi Informasi; Pemrograman', '', '', '', ''
            ],
            [
                4, '20-03-2015', 'X0025/2016', 'X0025/2016', 'X0025/2016', 'Hadiah/Hibah', 'Dinas Pendidikan',
                'IDR', 0, 'Pusat', '0103', 'Dapat dipinjam', 'Koleksi Umum', 'Buku', 'Tersedia', '899.221 DEW s',
                'Monograf', 'Sastra Indonesia Kontemporer', 'Analisis dan Apresiasi', 'Dewi Lestari', 'Lestari, Dewi', '', 'Pusat Bahasa', '', 'Edisi 3', 'Jakarta',
                'Gramedia Pustaka Utama', '2015', '320 hlm.', '20 cm.', '978-602-0307-456-7', '', '', '899.221', '899.221 DEW s', 'Kumpulan analisis sastra Indonesia modern',
                'ind', 'Sastra Indonesia; Literatur', '', '', '', ''
            ],
            [
                5, '25-03-2015', 'X0026/2016', 'X0026/2016', 'X0026/2016', 'Pembelian', 'CV. Pustaka Ilmu',
                'IDR', 85000, 'Pusat', '0104', 'Dapat dipinjam', 'Koleksi Referensi', 'Buku', 'Tersedia', '904.598 BAM s',
                'Monograf', 'Sejarah Perkembangan Teknologi Digital di Indonesia', '', 'Prof. Dr. Bambang Sutrisno; Dr. Maya Sari', 'Sutrisno, Bambang', '', '', 'Sari, Maya', 'Edisi 1', 'Jakarta',
                'Erlangga', '2015', '500 hlm.', '24 cm.', '978-602-2989-345-6', '', '', '904.598', '904.598 BAM s', 'Dokumentasi lengkap perkembangan teknologi digital di Indonesia',
                'ind', 'Sejarah; Teknologi; Indonesia', '', '', '', ''
            ]
        ];
        
        $sheet->fromArray($sampleData, null, 'A2');
        
        // Auto-size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set border untuk semua data
        $dataRange = 'A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . (count($sampleData) + 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);
        
        // Set response headers
        $filename = 'template_import_katalog_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

	// generate controlnumber
	private function generateControlNumber()
    {
        // Format: INLIS000000000004123
        $prefix = 'INLIS';
        $totalLength = 19; // Total length termasuk prefix
        $numberLength = $totalLength - strlen($prefix); // 14 digits untuk angka
        
        try {
            // Ambil ControlNumber tertinggi yang ada
            $lastRecord = $this->katalogModel
                ->select('ControlNumber')
                ->where('ControlNumber LIKE', $prefix . '%')
                ->orderBy('ControlNumber', 'DESC')
                ->first();
            
            if ($lastRecord && !empty($lastRecord->ControlNumber)) {
                // Extract angka dari ControlNumber terakhir
                $lastNumber = substr($lastRecord->ControlNumber, strlen($prefix));
                
                // Convert ke integer dan tambah 1
                $nextNumber = (int)$lastNumber + 1;
            } else {
                // Jika belum ada data, mulai dari 1
                $nextNumber = 1;
            }
            
            // Format dengan padding zero
            $formattedNumber = str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);
            
            // Gabungkan prefix dengan number
            $controlNumber = $prefix . $formattedNumber;
            
            // Validasi panjang hasil
            if (strlen($controlNumber) !== $totalLength) {
                throw new \Exception("Generated ControlNumber length mismatch: " . strlen($controlNumber));
            }
            
            return $controlNumber;
            
        } catch (\Exception $e) {
            // Fallback jika ada error
            log_message('error', 'Error generating ControlNumber: ' . $e->getMessage());
            
            // Generate dengan timestamp sebagai fallback
            $fallbackNumber = time() % 99999999999999; // 14 digits max
            return $prefix . str_pad($fallbackNumber, $numberLength, '0', STR_PAD_LEFT);
        }
    }
    
    // Method tambahan untuk validasi ControlNumber format
    private function validateControlNumberFormat($controlNumber)
    {
        $pattern = '/^INLIS\d{14}$/'; // INLIS + 14 digits
        return preg_match($pattern, $controlNumber);
    }
    
    // Method untuk mengecek apakah ControlNumber sudah digunakan
    private function isControlNumberExists($controlNumber)
    {
        $existing = $this->katalogModel
            ->where('ControlNumber', $controlNumber)
            ->countAllResults();
            
        return $existing > 0;
    }
    
    // Method yang lebih robust dengan retry mechanism
    private function generateUniqueControlNumber($maxRetries = 5)
    {
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            $controlNumber = $this->generateControlNumber();
            
            // Cek apakah sudah digunakan
            if (!$this->isControlNumberExists($controlNumber)) {
                return $controlNumber;
            }
            
            $attempt++;
            
            // Jika sudah digunakan, tunggu sebentar untuk menghindari collision
            usleep(100000); // 0.1 second
        }
        
        // Jika masih gagal setelah retry, gunakan timestamp
        $timestamp = microtime(true);
        $uniqueNumber = str_replace('.', '', $timestamp);
        $uniqueNumber = substr($uniqueNumber, -14); // Ambil 14 digit terakhir
        
        return 'INLIS' . str_pad($uniqueNumber, 14, '0', STR_PAD_LEFT);
    }

}