<?php

namespace Pengaturananggota\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pengaturananggota extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $bannerModel;
	public $uploadPath;
	public $modulePath;
	public $db;

	function __construct()
	{
		$this->bannerModel = new \Pengaturananggota\Models\PengaturananggotaModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/pengaturananggota/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
		helper('auth');
	}

	public function index()
	{
		$this->data['title'] = ' Pengaturananggota';
		echo view('Pengaturananggota\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$pengaturananggota = $this->bannerModel->find($id);
		$this->data['title'] = 'Pengaturananggota - Detail';
		$this->data['pengaturananggota'] = $pengaturananggota;
		echo view('Pengaturananggota\Views\view', $this->data);
	}

	public function create()
	{
		$branch = user()->branch_id;
		$this->data['title'] = 'Tambah Pengaturananggota';
		$slug = $this->request->getGet('slug');

		$this->validation->setRule('title', 'Judul Pengaturananggota', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title_slug = url_title($this->request->getPost('title'), '-', TRUE);
			$save_data = [
				'title' => $this->request->getPost('title'),
				'slug' => $title_slug,
				'branch_id' => $branch,
				'category' => $this->request->getPost('category'),
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'created_by' => user_id(),
			];

			// Logic Upload
			$files = (array) $this->request->getPost('file_cover');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = date('Ymd') . '_' . $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;

						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
				}
				$save_data['file_cover'] = implode(',', $listed_file);
			}

			$files = (array) $this->request->getPost('file_image');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = date('Ymd') . '_' . $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;
					}
				}
				$save_data['file_image'] = implode(',', $listed_file);
			}

			if (is_member('admin')) {
				$index_arr = $this->request->getPost('index');
				if (!empty($index_arr)) {
					$meta = array();
					foreach ($index_arr as $index => $value) {
						$meta[] = [
							'key' => $this->request->getPost('key')[$value],
							'value' => $this->request->getPost('value')[$value],
						];
					}
					if (!empty($meta)) {
						$save_data['meta'] = json_encode($meta);
					}
				}
			}

			$newPageId = $this->bannerModel->insert($save_data);
			// dd($newPageId);
			if ($newPageId) {
				set_message('toastr_msg', 'Pengaturananggota berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('pengaturananggota');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
				echo view('Pengaturananggota\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('pengaturananggota/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Pengaturananggota\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Pengaturananggota';
		$pengaturananggota = $this->bannerModel->find($id);
		$this->data['pengaturananggota'] = $pengaturananggota;

		$this->validation->setRule('title', 'Judul Pengaturananggota', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$title_slug = url_title($this->request->getPost('title'), '-', TRUE);
				$update_data = [
					'title' => $this->request->getPost('title'),
					'slug' => $this->request->getPost('slug') ?? $title_slug,
					'category' => $this->request->getPost('category'),
					'sort' => $this->request->getPost('sort'),
					'description' => $this->request->getPost('description'),
					'content' => $this->request->getPost('content'),
					'updated_by' => user_id(),
				];

				// Logic Upload
				$files = (array) $this->request->getPost('file_cover');
				if (count($files)) {
					$listed_file = array();
					foreach ($files as $uuid => $name) {
						if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = date('Ymd') . '_' . $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_file[] = $newFileName;

							create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
						}
					}
					$update_data['file_cover'] = implode(',', $listed_file);
				}

				$files = (array) $this->request->getPost('file_image');
				if (count($files)) {
					$listed_file = array();
					foreach ($files as $uuid => $name) {
						if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = date('Ymd') . '_' . $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_file[] = $newFileName;
						}
					}
					$update_data['file_image'] = implode(',', $listed_file);
				}

				if (is_member('admin')) {
					$index_arr = $this->request->getPost('index');
					if (!empty($index_arr)) {
						$meta = array();
						foreach ($index_arr as $index => $value) {
							$meta[] = [
								'key' => $this->request->getPost('key')[$value],
								'value' => $this->request->getPost('value')[$value],
							];
						}
						if (!empty($meta)) {
							$update_data['meta'] = json_encode($meta);
						}
					}
				}

				$pageUpdate = $this->bannerModel->update($id, $update_data);

				if ($pageUpdate) {
					set_message('toastr_msg', 'Pengaturananggota berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('pengaturananggota');
				} else {
					set_message('toastr_msg', 'Pengaturananggota gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Pengaturananggota gagal diubah');
					return redirect()->to('pengaturananggota');
				}
			}
		}


		$this->data['redirect'] = base_url('cms/pengaturananggota/edit/' . $id);
		echo view('Pengaturananggota\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$pengaturananggota = $this->bannerModel->find($id);
		$bannerDelete = $this->bannerModel->delete($id);
		if ($bannerDelete) {
			unlink_file($this->modulePath, $pengaturananggota->file_image);
			unlink_file($this->modulePath, 'thumb_' . $pengaturananggota->file_image);
			unlink_file($this->modulePath, $pengaturananggota->file_pdf);

			set_message('toastr_msg', ' Pengaturananggota berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/pengaturananggota');
		} else {
			set_message('toastr_msg', ' Pengaturananggota gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/pengaturananggota');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');
		$pengaturananggota = $this->bannerModel->find($id);

		$bannerUpdate = $this->bannerModel->update($id, array($field => $value));

		if ($bannerUpdate) {
			set_message('toastr_msg', ' Pengaturananggota berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Pengaturananggota gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('pengaturananggota');
	}

	public function export()
	{
		$query = $this->bannerModel
			->select('t_banner.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_banner.created_by', 'left')
			->join('users updated', 'updated.id = t_banner.updated_by', 'left');

		$results = $query->findAll();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "Pengaturananggota");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul Artikel");
		$sheet->setCellValue("C2", "Pengarang/Penulis");
		$sheet->setCellValue("D2", "Aktif");
		$sheet->setCellValue("E2", "Created By");
		$sheet->setCellValue("F2", "Updated By");
		$sheet->setCellValue("G2", "Foto Cover");
		$sheet->setCellValue("H2", "Konten Digital");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(10);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);

		$sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12);

		$col = 3;
		$no = 1;
		$i = 1;
		foreach ($results as $row) {
			$sheet->setCellValue("A" . $col, $no);
			$sheet->setCellValue("B" . $col, $row->title);
			$sheet->setCellValue("C" . $col, $row->author);
			$sheet->setCellValue("D" . $col, $row->active);
			$sheet->setCellValue("E" . $col, $row->created_at . ' | ' . strtoupper($row->created_name));
			$sheet->setCellValue("F" . $col, $row->updated_at . ' | ' . strtoupper($row->updated_name));
			$sheet->setCellValue("G" . $col, base_url('uploads/pengaturananggota/' . $row->file_image));
			$sheet->setCellValue("H" . $col, base_url('uploads/pengaturananggota/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Pengaturananggota';
		$filename = ucwords($subject) . '-' . date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function thumb()
	{
		$from = $this->request->getGet('from');
		$to = $this->request->getGet('to');

		for ($i = $from; $i <= $to; $i++) {
			$pengaturananggota = $this->bannerModel->find($i);
			$newFileName = $pengaturananggota->file_image;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
