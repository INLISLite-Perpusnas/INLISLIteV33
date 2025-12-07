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

    // 1. Set Validasi
    $this->validation->setRule('title', 'Judul Banner', 'required');
    
    // Validasi File Upload
    $this->validation->setRule('file_cover', 'File Banner', [
        'uploaded[file_cover]', // Wajib upload
        'is_image[file_cover]',
        'mime_in[file_cover,image/jpg,image/jpeg,image/png]',
        'max_size[file_cover,2048]', // Max 2MB
    ]);

    if (
        $this->request->getPost() &&
        $this->validation->withRequest($this->request)->run()
    ) {
        $db = \Config\Database::connect();
        $db->transStart();

        // Geser sort
        $this->bannerModel
            ->where('sort >=', 1)
            ->set('sort', 'sort + 1', false)
            ->update();

        $title_slug = url_title($this->request->getPost('title'), '-', true);
        
        $save_data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $title_slug,
            'category'    => $this->request->getPost('category'),
            'sort'        => 1,
            'description' => $this->request->getPost('description'),
            'created_by'  => user_id(),
        ];

        // --- Logic Upload Baru (Direct Upload) ---
        try {
            $file = $this->request->getFile('file_cover');

            if ($file->isValid() && !$file->hasMoved()) {
                // Generate nama file baru
                $newFileName = date('Ymd') . '_' . $file->getRandomName();
                
                // Pindahkan file ke folder tujuan
                $file->move($this->modulePath, $newFileName);

                // Buat Thumbnail
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);

                // Simpan nama file ke array data
                $save_data['file_cover'] = $newFileName;
            }

            // Logic Meta Data (Jika Ada)
            if (is_member('admin')) {
                $index_arr = $this->request->getPost('index');
                if (!empty($index_arr)) {
                    $meta = array();
                    foreach ($index_arr as $index => $value) {
                        // Pastikan key dan value ada
                        $k = $this->request->getPost('key')[$value] ?? '';
                        $v = $this->request->getPost('value')[$value] ?? '';
                        if ($k != '') {
                            $meta[] = ['key' => $k, 'value' => $v];
                        }
                    }
                    if (!empty($meta)) {
                        $save_data['meta'] = json_encode($meta);
                    }
                }
            }

            // Insert Database
            $this->bannerModel->insert($save_data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Rollback & Error Handling
                $dbError = $db->error();
                if (!empty($dbError['message'])) {
                    log_message('error', 'DB Error: ' . $dbError['message']);
                    set_message('message', $dbError['message']);
                }
                set_message('toastr_type', 'warning');
                return redirect()->back()->withInput();
            } else {
                set_message('toastr_msg', 'Banner berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/banner');
            }

        } catch (\Exception $e) {
            $db->transRollback();
            set_message('message', 'Upload Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

    } else {
        $this->data['redirect'] = base_url('banner/create');
        set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
        echo view('Banner\Views\add', $this->data);
    }
}

	public function edit(int $id = null)
{
    // 1. Cek Data
    $banner = $this->bannerModel->find($id);
    if (!$banner) {
        set_message('toastr_msg', 'Banner tidak ditemukan');
        set_message('toastr_type', 'error');
        return redirect()->to('/cms/banner');
    }

    $this->data['title'] = 'Ubah Banner';
    $this->data['banner'] = $banner;

    // 2. Set Validasi
    $this->validation->setRule('title', 'Judul Banner', 'required');
    $this->validation->setRule('sort', 'Urutan', 'required|is_natural_no_zero');
    
    // Validasi File (Permit Empty artinya opsional saat edit)
    $this->validation->setRule('file_cover', 'File Banner', [
        'permit_empty',
        'is_image[file_cover]',
        'mime_in[file_cover,image/jpg,image/jpeg,image/png]',
        'max_size[file_cover,2048]',
    ]);

    if ($this->request->getPost()) {
        if ($this->validation->withRequest($this->request)->run()) {
            
            $db = \Config\Database::connect();
            $db->transStart();

            $title_slug = url_title($this->request->getPost('title'), '-', TRUE);
            
            $update_data = [
                'title'       => $this->request->getPost('title'),
                'slug'        => $this->request->getPost('slug') ?? $title_slug,
                'category'    => $this->request->getPost('category'),
                'sort'        => $this->request->getPost('sort'),
                'description' => $this->request->getPost('description'),
                'updated_by'  => user_id(),
            ];

            // A. Logic Sorting
            $oldSort = $banner->sort;
            $newSort = (int)$this->request->getPost('sort');
            if ($newSort != $oldSort) {
                if ($newSort < $oldSort) {
                    $this->bannerModel
                        ->where('sort >=', $newSort)
                        ->where('sort <', $oldSort)
                        ->set('sort', 'sort + 1', false)
                        ->update();
                } elseif ($newSort > $oldSort) {
                    $this->bannerModel
                        ->where('sort <=', $newSort)
                        ->where('sort >', $oldSort)
                        ->set('sort', 'sort - 1', false)
                        ->update();
                }
            }

            // B. Logic Upload File Baru
            try {
                $file = $this->request->getFile('file_cover');

                // Jika user mengupload file baru
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newFileName = date('Ymd') . '_' . $file->getRandomName();
                    
                    // Pindahkan file baru
                    $file->move($this->modulePath, $newFileName);
                    create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);

                    // Update data DB
                    $update_data['file_cover'] = $newFileName;

                    // HAPUS File Lama (Cleanup)
                    if (!empty($banner->file_cover) && file_exists($this->modulePath . $banner->file_cover)) {
                        unlink($this->modulePath . $banner->file_cover);
                    }
                    if (!empty($banner->file_cover) && file_exists($this->modulePath . 'thumb_' . $banner->file_cover)) {
                        unlink($this->modulePath . 'thumb_' . $banner->file_cover);
                    }
                } else {
                    // Jika tidak upload baru, biarkan data lama (tidak perlu di-update di DB)
                    // $update_data['file_cover'] = $banner->file_cover; // Optional, tidak di-set juga aman
                }

                // C. Meta Data Logic
                if (is_member('admin')) {
                    $index_arr = $this->request->getPost('index');
                    if (!empty($index_arr)) {
                        $meta = array();
                        foreach ($index_arr as $index => $value) {
                            $k = $this->request->getPost('key')[$value] ?? '';
                            $v = $this->request->getPost('value')[$value] ?? '';
                            if ($k != '') {
                                $meta[] = ['key' => $k, 'value' => $v];
                            }
                        }
                        if (!empty($meta)) {
                            $update_data['meta'] = json_encode($meta);
                        }
                    }
                }

                $this->bannerModel->update($id, $update_data);
                $db->transComplete();

                if ($db->transStatus() === false) {
                    set_message('toastr_msg', 'Banner gagal diubah (DB Error)');
                    set_message('toastr_type', 'warning');
                    return redirect()->back()->withInput();
                } else {
                    set_message('toastr_msg', 'Banner berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/banner');
                }

            } catch (\Exception $e) {
                $db->transRollback();
                set_message('toastr_msg', 'Error: ' . $e->getMessage());
                set_message('toastr_type', 'error');
                return redirect()->back()->withInput();
            }
        }
    }

    $this->data['redirect'] = base_url('cms/banner/edit/' . $id);
    // Tambahan: tampilkan error validasi jika submit gagal
    $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
    
    echo view('Banner\Views\update', $this->data);
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
