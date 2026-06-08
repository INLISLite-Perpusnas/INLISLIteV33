<?php

namespace Banner\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Banner extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $bannerModel;
	public $uploadPath;
	public $modulePath;
	public $db;

	function __construct()
	{
		$this->bannerModel = new \Banner\Models\BannerModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/banner/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
	}

	public function index()
	{
		$this->data['title'] = ' Banner';
		echo view('Banner\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$banner = $this->bannerModel->find($id);
		$this->data['title'] = 'Banner - Detail';
		$this->data['banner'] = $banner;
		echo view('Banner\Views\view', $this->data);
	}

	public function create()
	{
		$this->data['title'] = 'Tambah Banner';
		$slug = $this->request->getGet('slug');

		$this->validation->setRule('title', 'Judul Banner', 'required');
		if (
			$this->request->getPost() &&
			$this->validation->withRequest($this->request)->run()
		) {
			$db = \Config\Database::connect();
			$db->transStart();

			// Geser semua sort lama ke bawah
			$this->bannerModel
				->where('sort >=', 1)
				->set('sort', 'sort + 1', false)
				->update();

			$title_slug = url_title(
				$this->request->getPost('title'),
				'-',
				true
			);
			$save_data = [
				'title' => $this->request->getPost('title'),
				'slug' => $title_slug,
				'category' => $this->request->getPost('category'),
				'sort' => 1,
				'description' => $this->request->getPost('description'),
				'created_by' => user_id(),
			];

			// Logic Upload
			$files = (array) $this->request->getPost('file_cover');
			if (count($files)) {
				$listed_file = [];
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName =
							date('Ymd') . '_' . $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;

						create_thumbnail(
							$this->modulePath,
							$newFileName,
							'thumb_',
							250
						);
					}
				}
				$save_data['file_cover'] = implode(',', $listed_file);
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
			$db->transComplete();

			if ($db->transStatus() === false) {
				$dbError = $db->error(); // Check database error
				if (!empty($error['message'])) {
					log_message('error', 'DB Error: ' . $dbError['message']);
					set_message('message', $dbError['message']);
				}
				set_message('toastr_type', 'warning');
				echo view('Banner\Views\add', $this->data);
			} else {
				set_message('toastr_msg', 'Banner berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/banner');
			}
		} else {
			$this->data['redirect'] = base_url('banner/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Banner\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		$this->data['title'] = 'Ubah Banner';
		$banner = $this->bannerModel->find($id);
		// Dapatkan data file image yang lama, termasuk ukuran file
		$old_file_cover_data = [];
		if ($banner->file_cover) {
			$file_names = explode(',', $banner->file_cover);
			foreach ($file_names as $file_name) {
				$file_path = $this->modulePath . $file_name;
				if (file_exists($file_path)) {
					$old_file_cover_data[] = [
						'name' => $file_name,
						'size' => filesize($file_path),
					];
				}
			}
		}

		$this->data['banner'] = $banner;
		$this->data['old_file_cover_data'] = $old_file_cover_data;

		$this->validation->setRule('title', 'Judul Banner', 'required');
		$this->validation->setRule('sort', 'Urutan', 'required|is_natural_no_zero');

		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$title_slug = url_title($this->request->getPost('title'), '-', TRUE);
				$update_data = [
					'title' => $this->request->getPost('title'),
					'slug' => $this->request->getPost('slug') ?? $title_slug,
					'category' => $this->request->getPost('category'),
					'sort' => $this->request->getPost('sort'),
					'description' => $this->request->getPost('description'),
					'updated_by' => user_id(),
				];
				$oldSort = $banner->sort;
				$newSort = (int)$this->request->getPost('sort');
				if ($newSort != $oldSort) {
					if ($newSort < $oldSort) {
						// Geser ke atas: berita di range [newSort, oldSort-1] naik 1
						$this->bannerModel
							->where('sort >=', $newSort)
							->where('sort <', $oldSort)
							->set('sort', 'sort + 1', false)
							->update();
					} elseif ($newSort > $oldSort) {
						// Geser ke bawah: berita di range [oldSort+1, newSort] turun 1
						$this->bannerModel
							->where('sort <=', $newSort)
							->where('sort >', $oldSort)
							->set('sort', 'sort - 1', false)
							->update();
					}
				}
				// Logic Upload untuk file_cover
				$files_image = (array) $this->request->getPost('file_cover');
				if (count($files_image)) {
					$listed_image = [];
					foreach ($files_image as $name) {
						if (file_exists($this->modulePath . $name)) {
							$listed_image[] = $name;
						} else if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = date('Ymd') . '_' . $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_image[] = $newFileName;
						}
					}
					$update_data['file_cover'] = implode(',', $listed_image);
				} else {
					// Jika tidak ada file baru di-upload, pertahankan file cover yang lama
					$update_data['file_cover'] = $banner->file_cover;
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
					set_message('toastr_msg', 'Banner berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/banner');
				} else {
					set_message('toastr_msg', 'Banner gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Banner gagal diubah');
					return redirect()->to('/cms/banner');
				}
			}
		}


		$this->data['redirect'] = base_url('cms/banner/edit/' . $id);
		echo view('Banner\Views\update', $this->data);
	}
	public function do_upload()
	{
		// Pastikan request adalah AJAX dan ada file yang diunggah
		if ($this->request->isAJAX() && $this->request->getFile('file')) {
			$file = $this->request->getFile('file');

			if ($file->isValid() && !$file->hasMoved()) {
				$newName = $file->getRandomName();
				$file->move($this->uploadPath, $newName);

				// Mengembalikan nama file sebagai respons JSON
				return $this->response->setJSON(['filename' => $newName, 'size' => $file->getSize()]);
			}
		}

		// Mengembalikan respons error jika ada masalah
		return $this->response->setStatusCode(400)->setJSON(['error' => 'File upload failed.']);
	}

	public function delete(int $id = 0)
	{
		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$banner = $this->bannerModel->find($id);
		$bannerDelete = $this->bannerModel->delete($id);
		if ($bannerDelete) {
			unlink_file($this->modulePath, $banner->file_cover);
			unlink_file($this->modulePath, 'thumb_' . $banner->file_cover);
			unlink_file($this->modulePath, $banner->file_pdf);

			set_message('toastr_msg', ' Banner berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/banner');
		} else {
			set_message('toastr_msg', ' Banner gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/banner');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getGet('field');
		$value = $this->request->getGet('value');
		$banner = $this->bannerModel->find($id);

		$bannerUpdate = $this->bannerModel->update($id, array($field => $value));

		if ($bannerUpdate) {
			set_message('toastr_msg', ' Banner berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Banner gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/banner');
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
		$sheet->setCellValue("A1", "Banner");
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
			$sheet->setCellValue("G" . $col, base_url('uploads/banner/' . $row->file_cover));
			$sheet->setCellValue("H" . $col, base_url('uploads/banner/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Banner';
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
			$banner = $this->bannerModel->find($i);
			$newFileName = $banner->file_cover;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
