<?php

namespace Eksemplar\Controllers;

use Eksemplar\Models\EksemplarModel as AkuisisiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Eksemplar extends \Base\Controllers\BaseController
{
	protected $eksemplarModel;
	protected $katalogModel;
	protected $db;

	function __construct()
	{
		$this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
		$this->katalogModel = new \Katalog\Models\KatalogModel();
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
			return redirect()->to(base_url('eksemplar/karantina'));
			
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

}
