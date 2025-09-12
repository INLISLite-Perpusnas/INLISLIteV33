<?php

namespace Katalog\Controllers;

use Base\Models\BaseModel;
use Base\Models\DataModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Scriptotek\Marc\Collection;

class Katalog extends \Base\Controllers\BaseController
{
  public $auth;
  public $authorize;
  public $fileModel;
  public $katalogModel;
  public $katalogRuasModel;
  public $artikelModel;
  public $worksheetModel;
  public $articleModel;
  public $serialArticleFilesModel;
  public $uploadPath;
  public $modulePath;
  public $validation;
  public $db;
  public $eksemplarModel;

  function __construct()
  {
    $this->fileModel = new \Katalog\Models\FileModel();
    $this->katalogModel = new \Katalog\Models\KatalogModel();
    $this->artikelModel = new \Katalog\Models\ArtikelModel();
    $this->edisiSerialModel = new \Katalog\Models\EdisiSerialModel();
    $this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();
    $this->worksheetModel = new \Katalog\Models\WorksheetModel();
    $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
    $this->articleModel = new \Katalog\Models\ArtikelModel();
    $this->serialArticleFilesModel = new \Katalog\Models\SerialArticleFilesModel();
    $this->uploadPath = ROOTPATH . 'public/uploads/';
    $this->modulePath = ROOTPATH . 'public/uploads/katalog/';
    $this->validation = \Config\Services::validation();
    $this->db = \Config\Database::connect('data');

    if (!file_exists($this->uploadPath)) {
      mkdir($this->uploadPath);
    }

    if (!file_exists($this->modulePath)) {
      mkdir($this->modulePath);
    }

    $this->auth = \Myth\Auth\Config\Services::authentication();
    $this->authorize = \Myth\Auth\Config\Services::authorization();

    helper('reference');
    helper('katalog');
    helper('region');
    helper('form');
    helper('app');
  }

  public function index()
  {
    $data['title'] = 'Daftar Katalog';
    echo view('Katalog\Views\list', $data);
  }

  public function karantina()
  {
    $data['title'] = 'Karantina Katalog';
    echo view('Katalog\Views\list_karantina', $data);
  }

  public function proses_karantina()
  {
    $IDs = $this->request->getvar('ID');
    $update_data = array();

    if (!empty($IDs)) {
      foreach ($IDs as $ID) {
        $update_data[] = array(
          'ID' => $ID,
          'IsQUARANTINE' => 1,
        );
      }

      if (!empty($update_data)) {
        $this->katalogModel->updateBatch($update_data, 'ID');

        set_message('toastr_msg', 'Berhasil ditambahkan ke Troli Karatina');
        set_message('toastr_type', 'success');
        set_message('message', 'Berhasil ditambahkan ke Troli Karatina');
      }
    } else {
      set_message('toastr_msg', 'Pilih katalog yang akan dikarantina terlebih dahulu');
      set_message('toastr_type', 'warning');
      set_message('message', 'Pilih katalog yang akan dikarantina terlebih dahulu');
    }

    return redirect()->back();
  }

  public function pulihkan_katalog()
  {
    $IDs = $this->request->getvar('ID');
    $update_data = array();

    if (!empty($IDs)) {
      foreach ($IDs as $ID) {
        $update_data[] = array(
          'ID' => $ID,
          'IsQUARANTINE' => null,
        );
      }

      if (!empty($update_data)) {
        $this->katalogModel->updateBatch($update_data, 'ID');

        set_message('toastr_msg', 'Berhasil dipulihkan ke daftar katalog');
        set_message('toastr_type', 'success');
        set_message('message', 'Berhasil dipulihkan ke daftar katalog');
      }
    } else {
      set_message('toastr_msg', 'Pilih katalog yang akan dipulihkan terlebih dahulu');
      set_message('toastr_type', 'warning');
      set_message('message', 'Pilih katalog yang akan dipulihkan terlebih dahulu');
    }

    return redirect()->back();
  }

  public function proses_opac()
  {
    $IDs = $this->request->getvar('ID');
    $update_data = array();

    if (!empty($IDs)) {
      foreach ($IDs as $ID) {
        $update_data[] = array(
          'ID' => $ID,
          'IsOPAC' => 1,
        );
      }

      if (!empty($update_data)) {
        $this->katalogModel->updateBatch($update_data, 'ID');

        set_message('toastr_msg', 'Berhasil ditampilkan ke OPAC');
        set_message('toastr_type', 'success');
        set_message('message', 'Berhasil ditampilkan ke OPAC');
      }
    } else {
      set_message('toastr_msg', 'Pilih katalog yang akan ditampilkan ke OPAC terlebih dahulu');
      set_message('toastr_type', 'warning');
      set_message('message', 'Pilih katalog yang akan ditampilkan ke OPAC terlebih dahulu');
    }

    return redirect()->back();
  }
  public function ekspor_marc()
  {
    $IDs = $this->request->getVar('ID');
    $format = $this->request->getVar('format') ?? 'mrc';

    if (!$IDs || empty($format)) {
      return redirect()->back()->with('error', 'Format dan data katalog harus dipilih.');
    }

    $collection = [];

    foreach ($IDs as $id) {
      $catalog = $this->katalogModel->asArray()->find($id);
      if (!$catalog) continue;

      // Ambil ruas MARC dari model
      $record = $this->katalogRuasModel
        ->select('*')
        ->where('CatalogId', $id)
        ->orderBy('Sequence', 'ASC')
        ->findAll();
      $collection[] = $record;
    }

    if (count($collection) === 0) {
      return redirect()->back()->with('error', 'Tidak ada data MARC yang ditemukan.');
    }

    switch ($format) {
      case 'txt':
        $content = '';
        foreach ($collection as $record) {
          $content .= "MARC-" . $record[0]->ID . "\n";
          $content .= "=LDR  00000nam  2200000   4500\n";

          foreach ($record as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
            $ind1 = $field->Indicator1 ?: ' ';
            $ind2 = $field->Indicator2 ?: ' ';
            $value = $field->Value;

            if (intval($tag) < 10) {
              // Control fields
              $content .= "={$tag}  {$value}\n";
            } else {
              // Data fields
              $content .= "={$tag}  {$ind1}{$ind2}\${$value}\n";
            }
          }
          $content .= "\n"; // Tambahkan baris baru antar record
        }
        return $this->response
          ->setHeader('Content-Type', 'text/plain')
          ->setHeader('Content-Disposition', 'attachment; filename="export_marc.txt"')
          ->setBody($content);

      case 'xlsx':
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        // $headers = ['Id Katalog', 'Tag', 'Ind1', 'Ind2', 'Nilai', 'Urutan'];
        $headers = ['Id Katalog', 'Tag', 'Ind1', 'Ind2', 'Nilai'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Format Header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->applyFromArray([
          'fillType' => Fill::FILL_SOLID,
          'startColor' => ['argb' => 'FFDDDDDD'],
        ]);

        $row = 2; // Mulai dari baris kedua setelah header
        $lastKatalogId = null;

        foreach ($collection as $record) {
          $idKatalog = $record[0]->ID ?? '';

          // Urutkan record berdasarkan aturan custom
          usort($record, function ($a, $b) {
            // Pastikan 'a' tetap di awal (jika itu yang dimaksud)
            if ($a->Tag === 'a') return -1;
            if ($b->Tag === 'a') return 1;

            $aIsNum = is_numeric($a->Tag);
            $bIsNum = is_numeric($b->Tag);

            if ($aIsNum && $bIsNum) {
              return (int)$a->Tag <=> (int)$b->Tag;
            }
            if ($aIsNum) return -1;
            if ($bIsNum) return 1;

            // Sama-sama non-numeric
            return strcmp($a->Tag, $b->Tag);
          });

          // Reset urutan jika ID katalog berubah
          if ($idKatalog !== $lastKatalogId) {
            $urutan = 1;
            $lastKatalogId = $idKatalog;
          }

          foreach ($record as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
            $ind1 = $field->Indicator1 ?: ' ';
            $ind2 = $field->Indicator2 ?: ' ';
            $value = $field->Value;

            if (intval($tag) < 10) {
              // Control fields
              $sheet->setCellValue('A' . $row, ($urutan == 1) ? $idKatalog : '');
              $sheet->setCellValue('B' . $row, $tag);
              $sheet->setCellValue('E' . $row, $value);
            } else {
              // Data fields
              $sheet->setCellValue('A' . $row, ($urutan == 1) ? $idKatalog : '');
              $sheet->setCellValue('B' . $row, $tag);
              $sheet->setCellValue('C' . $row, $ind1);
              $sheet->setCellValue('D' . $row, $ind2);
              $sheet->setCellValue('E' . $row, $value);
            }

            $row++;
            $urutan++;
          }
        }

        // Tambahkan border ke seluruh area data
        $lastRow = $row - 1;
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray([
          'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
          ],
        ]);

        // Auto-size kolom
        foreach (range('A', 'E') as $col) {
          $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Rata kiri dan tengah vertikal
        $sheet->getStyle('A1:E' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


        $writer = new Xlsx($spreadsheet);
        $filename = 'export_marc.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;

      default:
        return redirect()->back()->with('error', 'Format tidak dikenali.');
    }
  }
  public function create_marc()
  {
    if (!is_allowed('katalog/create')) {
      set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    $data['title'] = 'Tambah Katalog Form MARC';
    $this->validation->setRule('Worksheet_id', 'Jenis Bahan', 'required');
    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
      $post = $this->request->getPost();

      $catalogsModel = new DataModel('catalogs', null, 'ID');
      $save_data = array(
        'Worksheet_id' => $this->request->getPost('Worksheet_id'),
        'ControlNumber' => get_control_number(),
        'BIBID' => random_string('numeric', 13),
        'Branch_id' => user()->branch_id,
      );
      $CatalogId = $catalogsModel->insert($save_data);

      $Indexes = $this->request->getPost('Index');
      $Indicator1s = $this->request->getPost('Indicator1');
      $Indicator2s = $this->request->getPost('Indicator2');
      $Values = $this->request->getPost('Value');

      $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
      $catalogRuasData = [];
      foreach ($Values as $key => $value) {
        $items = [];
        if (array_key_exists($key, $Values)) {
          $items['Value'] = is_array($Values[$key]) ? $Values[$key] : [$Values[$key]];
        }
        if (array_key_exists($key, $Indicator1s)) {
          $items['Indicator1'] = is_array($Indicator1s[$key]) ? $Indicator1s[$key] : [$Indicator1s[$key]];
        }
        if (array_key_exists($key, $Indicator2s)) {
          $items['Indicator2'] = is_array($Indicator2s[$key]) ? $Indicator2s[$key] : [$Indicator2s[$key]];
        }
        $catalogRuasData[$key] = $items;
      }

      if (!empty($catalogRuasData)) {
        $catalog_ruas_data = [];
        foreach ($catalogRuasData as $key => $items) {
          foreach ($items['Value'] as $index => $row) {
            $Value = isset($items['Value'][$index]) ? $items['Value'][$index] : '';
            $Indicator1 = isset($items['Indicator1'][$index]) ? $items['Indicator1'][$index] : '';
            $Indicator2 = isset($items['Indicator2'][$index]) ? $items['Indicator2'][$index] : '';
            $item = array(
              'CatalogId' => $CatalogId,
              'Tag' => $key,
              'Value' => $Value,
              'Indicator1' => $Indicator1,
              'Indicator2' => $Indicator2,
            );
            array_push($catalog_ruas_data, $item);
          }
        }
        // ... Kode Anda untuk mem-filter data yang Value-nya hanya '$a'
        $catalog_ruas_data = array_filter($catalog_ruas_data, function ($item) {
          return trim($item['Value']) !== '$a';
        });
        $catalog_ruas_data = array_values($catalog_ruas_data);


        // --- TAMBAHKAN KODE BARU DI SINI ---

        // 1. Siapkan data untuk Tag 001
        $tag_001 = [
          'CatalogId'  => $CatalogId,
          'Tag'        => '001',
          'Value'      => $save_data['ControlNumber'], // Mengambil dari ControlNumber yang sudah dibuat
          'Indicator1' => '',
          'Indicator2' => '',
        ];

        // 2. Tambahkan Tag 001 ke bagian paling AWAL dari array
        array_unshift($catalog_ruas_data, $tag_001);

        $katalogRuasModel->insertBatch($catalog_ruas_data);

        $update_data = convert_catalog_ruas($CatalogId);

        $catalogsModel->update($CatalogId, $update_data);
      }

      return redirect()->to('katalog');
    } else {

      $session = service('session');
      $worksheetModel = new DataModel('worksheets', null, 'ID');
      $worksheets = $worksheetModel->orderBy('NoUrut')->findAll();
      $data['worksheets'] = $worksheets;

      $worksheet_id = $this->request->getvar('worksheet_id') ?? 1;
      if (!$session->has('worksheet_id')) {
        $session->set('worksheet_id', $worksheet_id);
      } else {
        $session_worksheet_id = $session->get('worksheet_id');
        if ($worksheet_id != $session_worksheet_id) {
          $session->remove('worksheet_id');
          $session->remove('worksheet_fields');
          $session->set('worksheet_id', $worksheet_id);
        }
      }
      $all_tags = get_all_tags($worksheet_id);
      $data['session_tags'] = $all_tags->session_tags;
      $data['filtered_tags'] =  $all_tags->filtered_tags;

      return view('Katalog\Views\add_marc', $data);
    }
  }

  // Tambahkan method ini di dalam class controller Katalog Anda

  public function edit_marc($id = null)
  {


    if (!$id) {
      set_message('toastr_msg', 'ID Katalog tidak ditemukan');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    $catalogsModel = new DataModel('catalogs', null, 'ID');
    $catalog = $catalogsModel->find($id);

    if (!$catalog) {
      set_message('toastr_msg', 'Data katalog tidak ditemukan');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    $data['title'] = 'Edit Katalog Form MARC';
    $data['catalog'] = $catalog;

    $this->validation->setRule('Worksheet_id', 'Jenis Bahan', 'required');

    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
      $post = $this->request->getPost();

      // Update catalog basic info
      $update_catalog = array(
        'Worksheet_id' => $this->request->getPost('Worksheet_id'),
      );
      $catalogsModel->update($id, $update_catalog);

      // Delete existing catalog_ruas data
      $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
      $katalogRuasModel->where('CatalogId', $id)->delete();

      $Indexes = $this->request->getPost('Index');
      $Indicator1s = $this->request->getPost('Indicator1');
      $Indicator2s = $this->request->getPost('Indicator2');
      $Values = $this->request->getPost('Value');

      $catalogRuasData = [];
      foreach ($Values as $key => $value) {
        $items = [];
        if (array_key_exists($key, $Values)) {
          $items['Value'] = is_array($Values[$key]) ? $Values[$key] : [$Values[$key]];
        }
        if (array_key_exists($key, $Indicator1s)) {
          $items['Indicator1'] = is_array($Indicator1s[$key]) ? $Indicator1s[$key] : [$Indicator1s[$key]];
        }
        if (array_key_exists($key, $Indicator2s)) {
          $items['Indicator2'] = is_array($Indicator2s[$key]) ? $Indicator2s[$key] : [$Indicator2s[$key]];
        }
        $catalogRuasData[$key] = $items;
      }

      if (!empty($catalogRuasData)) {
        $catalog_ruas_data = [];
        foreach ($catalogRuasData as $key => $items) {
          foreach ($items['Value'] as $index => $row) {
            $Value = isset($items['Value'][$index]) ? $items['Value'][$index] : '';
            $Indicator1 = isset($items['Indicator1'][$index]) ? $items['Indicator1'][$index] : '';
            $Indicator2 = isset($items['Indicator2'][$index]) ? $items['Indicator2'][$index] : '';
            $item = array(
              'CatalogId' => $id,
              'Tag' => $key,
              'Value' => $Value,
              'Indicator1' => $Indicator1,
              'Indicator2' => $Indicator2,
            );
            array_push($catalog_ruas_data, $item);
          }
        }

        // Filter data yang Value-nya hanya '$a'
        $catalog_ruas_data = array_filter($catalog_ruas_data, function ($item) {
          return trim($item['Value']) !== '$a';
        });
        $catalog_ruas_data = array_values($catalog_ruas_data);

        // Tambahkan Tag 001 jika belum ada
        $has_001 = false;
        foreach ($catalog_ruas_data as $item) {
          if ($item['Tag'] === '001') {
            $has_001 = true;
            break;
          }
        }

        if (!$has_001) {
          $tag_001 = [
            'CatalogId'  => $id,
            'Tag'        => '001',
            'Value'      => $catalog->ControlNumber,
            'Indicator1' => '',
            'Indicator2' => '',
          ];
          array_unshift($catalog_ruas_data, $tag_001);
        }

        $katalogRuasModel->insertBatch($catalog_ruas_data);

        $update_data = convert_catalog_ruas($id);
        $catalogsModel->update($id, $update_data);
      }

      set_message('toastr_msg', 'Data katalog berhasil diupdate');
      set_message('toastr_type', 'success');
      return redirect()->to('katalog');
    } else {
      $session = service('session');
      $worksheetModel = new DataModel('worksheets', null, 'ID');
      $worksheets = $worksheetModel->orderBy('NoUrut')->findAll();
      $data['worksheets'] = $worksheets;

      $worksheet_id = $this->request->getvar('worksheet_id') ?? $catalog->Worksheet_id;

      // Clear session untuk edit
      $session->remove('worksheet_id');
      $session->remove('worksheet_fields');
      $session->set('worksheet_id', $worksheet_id);

      // Get existing catalog_ruas data
      $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
      $existing_ruas = $katalogRuasModel->where('CatalogId', $id)->findAll();

      $all_tags = get_all_tags($worksheet_id);

      // Prepare existing data untuk ditampilkan di form
      $existing_data = [];
      foreach ($existing_ruas as $ruas) {
        $existing_data[$ruas->Tag][] = [
          'Value' => $ruas->Value,
          'Indicator1' => $ruas->Indicator1,
          'Indicator2' => $ruas->Indicator2
        ];
      }

      $data['existing_data'] = $existing_data;
      $data['session_tags'] = $all_tags->session_tags;
      $data['filtered_tags'] = $all_tags->filtered_tags;

      return view('Katalog\Views\edit_marc', $data);
    }
  }




  public function hapus_permanen()
  {
    $IDs = $this->request->getvar('ID');
    $update_data = array();

    if (!empty($IDs)) {
      $this->katalogModel->delete($IDs);

      set_message('toastr_msg', 'Katalog Berhasil dihapus permanen');
      set_message('toastr_type', 'success');
      set_message('message', 'Katalog Berhasil dihapus permanen');
    } else {
      set_message('toastr_msg', 'Pilih katalog yang akan dihapus permanen terlebih dahulu');
      set_message('toastr_type', 'warning');
      set_message('message', 'Pilih katalog yang akan dihapus permanen terlebih dahulu');
    }

    return redirect()->back();
  }

  public function create()
  {
    if (!is_allowed('katalog/create')) {
      set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    $data['title'] = 'Tambah Katalog Form Sederhana';

    $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');

    $this->validation->setRule('judul[a]', 'Judul Utama', 'trim');
    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
      $post = $this->request->getPost();
      $ControlNumber = get_control_number();
      $BIBID = get_bib_id();
      $Branch_id = $branch_id;
      $CoverURL = '';
      $CreateBy = user_id();
      $CreateDate = date("Y-m-d H:i:s");
      $UpdateBy = user_id();
      $UpdateDate = date("Y-m-d H:i:s");

      $save_data = [
        'ControlNumber' => $ControlNumber,
        'BIBID' => $BIBID,
        'Branch_id' => $Branch_id,
        'CoverURL' => $CoverURL,
        'CreateBy' => $CreateBy,
        'CreateDate' => $CreateDate,
        'UpdateBy' => $UpdateBy,
        'UpdateDate' => $UpdateDate
      ];

      if (!empty($this->request->getPost('judul'))) {
        $Title = implode_data($this->request->getPost('judul'), ' ');
        $save_data['Title'] = $Title;
      }

      if (!empty($this->request->getPost('pengarangUtama'))) {
        $authorUtama = multi_array($this->request->getPost('pengarangUtama'));
        $authorTambahan = multi_array($this->request->getPost('pengarangTambahan'));
        $author = implode_data([$authorUtama, $authorTambahan]);
        $save_data['Author'] = $author;
      }

      if (!empty($this->request->getPost('penerbit')['b'])) {
        $Publisher = $this->request->getPost('penerbit')['b'];
        $save_data['Publisher'] = $Publisher;
      }

      if (!empty($this->request->getPost('penerbit')['a'])) {
        $PublishLocation = $this->request->getPost('penerbit')['a'];
        $save_data['PublishLocation'] = $PublishLocation;
      }

      if (!empty($this->request->getPost('penerbit')['c'])) {
        $PublishYear = $this->request->getPost('penerbit')['c'];
        $save_data['PublishYear'] = $PublishYear;
      }

      if (!empty($this->request->getPost('penerbit'))) {
        $Publikasi = implode_data($this->request->getPost('penerbit'), ' ');
        $save_data['Publikasi'] = $Publikasi;
      }

      if (!empty($this->request->getPost('PhysicalDescription'))) {
        $PhysicalDescription = implode_data($this->request->getPost('PhysicalDescription'), ' ');
        $save_data['PhysicalDescription'] = $PhysicalDescription;
      }

      if (!empty($this->request->getPost('Edition'))) {
        $Edition = $this->request->getPost('Edition');
        $save_data['Edition'] = $Edition;
      }

      if (!empty($this->request->getPost('Subject'))) {
        $Subject = multi_array($this->request->getPost('Subject'), ';');
        $save_data['Subject'] = $Subject;
      }

      if (!empty($this->request->getPost('DeweyNo'))) {
        $DeweyNo = implode_data($this->request->getPost('DeweyNo'), ' ');
        $save_data['DeweyNo'] = $DeweyNo;
      }

      if (!empty($this->request->getPost('ISBN'))) {
        $ISBN = implode_data($this->request->getPost('ISBN'), ' ; ');
        $save_data['ISBN'] = $ISBN;
      }

      if (!empty($this->request->getPost('CallNumber'))) {
        $CallNumber = implode_data($this->request->getPost('CallNumber'), ';');
        $save_data['CallNumber'] = $CallNumber;
      }

      if (!empty($this->request->getPost('catatan'))) {
        $Note = multi_array($this->request->getPost('catatan'), ';');
        $save_data['Note'] = $Note;
      }

      if (!empty($this->request->getPost('Languages')['lang'])) {
        $Languages = implode_data($this->request->getPost('Languages')['lang'], ' ');
        $save_data['Languages'] = $Languages;
      }

      if (!empty($this->request->getPost('Worksheet_id'))) {
        $Worksheet_id = $this->request->getPost('Worksheet_id');
        $save_data['Worksheet_id'] = $Worksheet_id;
      }

      if (!empty($this->request->getPost('IsOPAC'))) {
        $IsOPAC = $this->request->getPost('IsOPAC');
        $save_data['IsOPAC'] = $IsOPAC ? 1 : 0;
      }

      if (!empty($this->request->getPost('IsRDA'))) {
        $IsRDA = $this->request->getPost('IsRDA');
        $save_data['IsRDA'] = $IsRDA ? 1 : 0;
      }

      $IsRedirect = $this->request->getPost('IsRedirect');

      $db = db_connect('data');
      $db->transBegin();
      try {
        $catalog_id = $this->katalogModel->insert($save_data);
        $post = array_merge(
          array(
            'ControlNumber' => $ControlNumber,
            'tag005' => date("YmdHis"),
            'BIBID' => $BIBID
          ),
          $post,
          array(
            'language' => str_pad(date("ymd"), 22, "#") . str_pad($post['Languages']['ks'], 11, "#") . str_pad($post['Languages']['bkt'], 2, "#") . str_pad($post['Languages']['lang'], 5, "#"),
            'cat_id' => $catalog_id
          )
        );

        $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();

        $catalog_ruas = data_catalog_ruas($post, $catalog_id);
        $this->katalogRuasModel->insert_catalog_ruas($catalog_ruas);
        $db->transCommit();

        set_message('toastr_msg', 'Katalog berhasil disimpan');
        set_message('toastr_type', 'success');
      } catch (\Throwable $th) {
        $db->transRollback();

        set_message('toastr_msg', 'Katalog gagal disimpan');
        set_message('toastr_type', 'warning');

        return redirect()->back()->withInput();
      }

      if ($IsRedirect == 1) {
        return redirect()->to('katalog');
      } else {
        return redirect()->to('katalog/edit/' . $catalog_id);
      }
    } else {
      $data['redirect'] = base_url('katalog/create');

      $session = service('session');
      $worksheetModel = new DataModel('worksheets', null, 'ID');
      $worksheets = $worksheetModel->orderBy('NoUrut')->findAll();
      $data['worksheets'] = $worksheets;

      $worksheet_id = $this->request->getvar('worksheet_id') ?? 1;
      if (!$session->has('worksheet_id')) {
        $session->set('worksheet_id', $worksheet_id);
      } else {
        $session_worksheet_id = $session->get('worksheet_id');
        if ($worksheet_id != $session_worksheet_id) {
          $session->remove('worksheet_id');
          $session->remove('worksheet_fields');
          $session->set('worksheet_id', $worksheet_id);
        }
      }

      set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
      echo view('Katalog\Views\add', $data);
    }
  }

  public function edit(int $catalog_id = null)
  {
    if (!is_allowed('katalog/edit')) {
      set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    $data['title'] = 'Edit Katalog Form Sederhana';
    $data['is_allowed'] = !is_member('admin') && !is_member('sa_prov') && !is_member('sa_kabkota');

    $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');
    $slug = $this->request->getGet('slug');

    $catalog = $this->katalogModel->find($catalog_id);
    if (empty($catalog)) {
      set_message('toastr_msg', 'Katalog tidak ditemukan');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }
    $CreateBy = get_username($catalog->CreateBy ?? 0);
    $UpdateBy = get_username($catalog->UpdateBy ?? 0);
    $data['CreateBy'] = $CreateBy;
    $data['UpdateBy'] = $UpdateBy;

    $data['catalog'] = $catalog;

    $worksheet = $this->worksheetModel->find($catalog->Worksheet_id);
    $data['worksheet'] = $worksheet;

    $data['str_245'] = get_array_tag($catalog_id, '245');
    $data['str_260'] = get_array_tag($catalog_id, '260');
    $data['str_300'] = get_array_tag($catalog_id, '300');
    $data['str_240'] = get_array_tag($catalog_id, '240');
    $data['str_247'] = get_array_tag($catalog_id, '247');
    $data['str_310'] = get_array_tag($catalog_id, '310');
    $data['str_336'] = get_array_tag($catalog_id, '336');
    $data['str_337'] = get_array_tag($catalog_id, '337');
    $data['str_338'] = get_array_tag($catalog_id, '338');
    $data['str_082'] = get_array_tag($catalog_id, '082');

    $cr_008 = get_catalog_ruas_tag($catalog_id, '008');
    $cr_008_ks = substr($cr_008[0]->Value ?? "", 25, 1);
    $data['cr_008_ks'] = $cr_008_ks;
    $cr_008_bkt = substr($cr_008[0]->Value ?? "", 36, 1);
    $data['cr_008_bkt'] = $cr_008_bkt;
    $cr_008_lang = substr($cr_008[0]->Value ?? "", 38, 3);
    $data['cr_008_lang'] = $cr_008_lang;

    $files = $this->fileModel->where('Catalog_id', $catalog_id)->orderBy('UpdateDate', 'desc')->findAll();
    $data['files'] = $files;

    $this->validation->setRule('judul[a]', 'Judul Utama', 'trim');
    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

      if ($slug == 'konten_digital') {
        // save to catalog_konten_digital
      }

      $post = $this->request->getPost();
      $ControlNumber = $catalog->ControlNumber;
      $BIBID = $catalog->BIBID;
      $CoverURL = '';
      $UpdateBy = user_id();
      $UpdateDate = date("Y-m-d H:i:s");

      $update_data = [
        'ControlNumber' => $ControlNumber,
        'BIBID' => $BIBID,
        'CoverURL' => $CoverURL,
        'UpdateBy' => $UpdateBy,
        'UpdateDate' => $UpdateDate
      ];

      if (!empty($branch_id)) {
        $update_data['Branch_id'] = $branch_id;
      }

      if (!empty($this->request->getPost('judul'))) {
        $Title = implode_data($this->request->getPost('judul'), ' ');
        $update_data['Title'] = $Title;
      }

      if (!empty($this->request->getPost('pengarangUtama'))) {
        $authorUtama = multi_array($this->request->getPost('pengarangUtama'));
        $authorTambahan = multi_array($this->request->getPost('pengarangTambahan'));
        $author = implode_data([$authorUtama, $authorTambahan]);
        $update_data['Author'] = $author;
      }

      if (!empty($this->request->getPost('penerbit')['b'])) {
        $Publisher = $this->request->getPost('penerbit')['b'];
        $update_data['Publisher'] = $Publisher;
      }

      if (!empty($this->request->getPost('penerbit')['a'])) {
        $PublishLocation = $this->request->getPost('penerbit')['a'];
        $update_data['PublishLocation'] = $PublishLocation;
      }

      if (!empty($this->request->getPost('penerbit')['c'])) {
        $PublishYear = $this->request->getPost('penerbit')['c'];
        $update_data['PublishYear'] = $PublishYear;
      }

      if (!empty($this->request->getPost('penerbit'))) {
        $Publikasi = implode_data($this->request->getPost('penerbit'), ' ');
        $update_data['Publikasi'] = $Publikasi;
      }

      if (!empty($this->request->getPost('PhysicalDescription'))) {
        $PhysicalDescription = implode_data($this->request->getPost('PhysicalDescription'), ' ');
        $update_data['PhysicalDescription'] = $PhysicalDescription;
      }

      if (!empty($this->request->getPost('Edition'))) {
        $Edition = $this->request->getPost('Edition');
        $update_data['Edition'] = $Edition;
      }

      if (!empty($this->request->getPost('Subject'))) {
        $Subject = multi_array($this->request->getPost('Subject'), ';');
        $update_data['Subject'] = $Subject;
      }

      if (!empty($this->request->getPost('DeweyNo'))) {
        $DeweyNo = implode_data($this->request->getPost('DeweyNo'), ' ');
        $update_data['DeweyNo'] = $DeweyNo;
      }

      if (!empty($this->request->getPost('ISBN'))) {
        $ISBN = implode_data($this->request->getPost('ISBN'), ' ; ');
        $update_data['ISBN'] = $ISBN;
      }

      if (!empty($this->request->getPost('CallNumber'))) {
        $CallNumber = implode_data($this->request->getPost('CallNumber'), ';');
        $update_data['CallNumber'] = $CallNumber;
      }

      if (!empty($this->request->getPost('catatan'))) {
        $Note = multi_array($this->request->getPost('catatan'), ';');
        $update_data['Note'] = $Note;
      }

      if (!empty($this->request->getPost('Languages')['lang'])) {
        $Languages = implode_data($this->request->getPost('Languages')['lang'], ' ');
        $update_data['Languages'] = $Languages;
      }

      if (!empty($this->request->getPost('Worksheet_id'))) {
        $Worksheet_id = $this->request->getPost('Worksheet_id');
        $update_data['Worksheet_id'] = $Worksheet_id;
      }

      if (!empty($this->request->getPost('IsRDA'))) {
        $IsRDA = $this->request->getPost('IsRDA');
        $update_data['IsRDA'] = $IsRDA ? 1 : 0;
      }

      $IsOPAC = $this->request->getPost('IsOPAC');
      $update_data['IsOPAC'] = $IsOPAC ? 1 : 0;

      $IsRedirect = $this->request->getPost('IsRedirect');

      // Start a transaction
      $db = db_connect('data');
      $db->transBegin();
      try {
        $this->katalogModel->update($catalog_id, $update_data);
        $post = array_merge(
          array(
            'ControlNumber' => $ControlNumber,
            'tag005' => date("YmdHis"),
            'BIBID' => $BIBID
          ),
          $post,
          array(
            'language' => str_pad(date("ymd"), 22, "#") . str_pad($post['Languages']['ks'], 11, "#") . str_pad($post['Languages']['bkt'], 2, "#") . str_pad($post['Languages']['lang'], 5, "#"),
            'cat_id' => $catalog_id
          )
        );

        $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();

        $catalog_ruas = data_catalog_ruas($post, $catalog_id);
        $this->katalogRuasModel->insert_catalog_ruas($catalog_ruas);
        $db->transCommit();

        set_message('toastr_msg', 'Katalog berhasil disimpan');
        set_message('toastr_type', 'success');
      } catch (\Throwable $th) {
        $db->transRollback();

        set_message('toastr_msg', 'Katalog gagal disimpan');
        set_message('toastr_type', 'warning');

        return redirect()->back()->withInput();
      }

      if ($IsRedirect == 1) {
        return redirect()->to('katalog');
      } else {
        return redirect()->to('katalog/edit/' . $catalog_id);
      }
    } else {
      $data['redirect'] = base_url('katalog/edit/' . $catalog_id);

      $session = service('session');
      $worksheetModel = new DataModel('worksheets', null, 'ID');
      $worksheets = $worksheetModel->orderBy('NoUrut')->findAll();
      $data['worksheets'] = $worksheets;

      $worksheet_id = $this->request->getvar('worksheet_id') ?? 1;
      if (!$session->has('worksheet_id')) {
        $session->set('worksheet_id', $worksheet_id);
      } else {
        $session_worksheet_id = $session->get('worksheet_id');
        if ($worksheet_id != $session_worksheet_id) {
          $session->remove('worksheet_id');
          $session->remove('worksheet_fields');
          $session->set('worksheet_id', $worksheet_id);
        }
      }

      set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
      echo view('Katalog\Views\update', $data);
    }
  }

  public function edit2(int $id = null)
  {
    $data['title'] = 'Ubah Katalog';
    $katalog = $this->katalogModel->find($id);
    $branch_id = user()->branch_id ?? $this->request->getGet('branch_id');

    $data = array_merge(form_catalog_ruas($id, '245'), $data);
    $data = array_merge(form_catalog_ruas($id, '300'), $data);
    $data = array_merge(form_catalog_ruas($id, '260'), $data);

    $author = $katalog->Author;
    $authorarray = explode(";", $author);
    $TajukPengarangUtama = $authorarray[0];
    $TajukPengarangTambahan1 = $authorarray[1] ?? "";

    $data['TajukPengarangUtama'] = $TajukPengarangUtama;
    $data['TajukPengarangTambahan1'] = $TajukPengarangTambahan1;

    $data['katalog'] = $katalog;
    $data['LanguagesKs'] = '';
    $data['LanguagesBkt'] = '';
    $katalogRuas = $this->katalogRuasModel->where('CatalogId', $katalog->ID)->where('Tag', '008')->first();
    if ($katalogRuas) {
      preg_match_all('/[a-zA-Z]+/', $katalogRuas->Value, $Languages);

      // if ($Languages) {
      // 	$data['LanguagesKs'] = $Languages[0][1];
      // 	$data['LanguagesBkt'] = $Languages[0][2];
      // }
    }

    $this->validation->setRule('judul[a]', 'Judul Utama', 'trim');
    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
      $post = $this->request->getPost();
      $ControlNumber = get_control_number();
      $BIBID = get_bib_id();
      $Branch_id = $branch_id;

      $inputStr = stringify_catalog_ruas($post['judul']);
      $input = parse_catalog_ruas($inputStr, '245');
      $Title = $input['Title'];

      $Author = [];
      $Author[] = multi_array($post['Author']);
      $Author[] = multi_array($post['pengarang_tambahan']);
      $Author = implode_data([
        multi_array($post['Author']),
        multi_array($post['pengarang_tambahan'])
      ]);
      $Edition = $post['Edition'];

      $inputStr = stringify_catalog_ruas($post['penerbit']);
      $input = parse_catalog_ruas($inputStr, '260');
      $PublishLocation = $input['PublishLocation'];
      $Publisher = $input['Publisher'];
      $PublishYear = $input['PublishYear'];
      $Publikasi = $input['Publikasi'];

      $inputStr = stringify_catalog_ruas($post['PhysicalDescription']);
      $input = parse_catalog_ruas($inputStr, '300');
      $PhysicalDescription = $input['PhysicalDescription'];

      $Subject = multi_array($post['Subject']);
      $ISBN = implode_data($post['ISBN']);
      $CallNumber = implode_data(array_merge(array($post['CallNumber']), isset($post['repeat-CallNumber']) ? $post['repeat-CallNumber'] : array()));
      $Note = multi_array($post['catatan']);
      $Languages = implode_data($post['Languages']['lang']);

      $DeweyNo = implode_data($post['DeweyNo']);
      $IsOPAC = $this->request->getPost('IsOPAC');
      $Worksheet_id = $post['Worksheet_id'] ?? 1;
      $CreateBy = user_id();
      $CreateDate = date("Y-m-d H:i:s");
      $UpdateBy = user_id();
      $UpdateDate = date("Y-m-d H:i:s");
      $IsRedirect = $this->request->getPost('IsRedirect');

      $update_data = [
        'ControlNumber' => $ControlNumber,
        'BIBID' => $BIBID,
        'Title' => $Title,
        'Author' => $Author,
        'Edition' => $Edition,
        'PublishLocation' => $PublishLocation,
        'Publisher' => $Publisher,
        'PublishYear' => $PublishYear,
        'Publikasi' => $Publikasi,
        'Branch_id' => $Branch_id,
        'Subject' => $Subject,
        'PhysicalDescription' => $PhysicalDescription,
        'ISBN' => $ISBN,
        'CallNumber' => $CallNumber,
        'Note' => $Note,
        'Languages' => $Languages,
        'DeweyNo' => $DeweyNo,
        'IsOPAC' => $IsOPAC ? 1 : 0,
        'Worksheet_id' => $Worksheet_id,
        'CreateBy' => $CreateBy,
        'CreateDate' => $CreateDate,
        'UpdateBy' => $UpdateBy,
        'UpdateDate' => $UpdateDate,
      ];

      $catalog_id = $this->katalogModel->update($id, $update_data);
      $post = array_merge(
        array(
          'ControlNumber' => $ControlNumber,
          'tag005' => date("YmdHis"),
          'BIBID' => $BIBID
        ),
        $post,
        array(
          'language' => str_pad(date("ymd"), 22, "#") . str_pad($post['Languages']['ks'], 11, "#") . str_pad($post['Languages']['bkt'], 2, "#") . str_pad($post['Languages']['lang'], 5, "#"),
          'cat_id' => $id
        )
      );

      $this->session->setFlashdata($post);

      $data = $this->session->getFlashdata();
      $DataCatalogsRuas = data_catalog_ruas($this->session->getFlashdata());

      $this->katalogRuasModel->Update_catalog_ruas($DataCatalogsRuas);

      // (array_key_exists("update", $data)) ? $this->katalogRuasModel->Update_catalog_ruas($DataCatalogsRuas) : $this->katalogRuasModel->Insert_catalog_ruas($DataCatalogsRuas);
      set_message('toastr_msg', 'Katalog berhasil disimpan');
      set_message('toastr_type', 'success');

      if ($IsRedirect == 1) {
        return redirect()->to('katalog');
      } else {
        return redirect()->to('katalog/edit/' . $id);
      }
    } else {
      $worksheetModel = new DataModel('worksheets', null, 'ID');
      $worksheets = $worksheetModel->orderBy('NoUrut')->findAll();
      $data['worksheets'] = $worksheets;
      set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
      echo view('Katalog\Views\update', $data);
    }
  }
  public function delete(int $id = 0)
  {
    if (!is_allowed('katalog/delete')) {
      set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }

    if (!$id) {
      set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
      set_message('toastr_type', 'error');
      return redirect()->to('katalog');
    }
    $KatalogDelete = $this->katalogModel->delete($id);
    if ($KatalogDelete) {
      set_message('toastr_msg', 'Katalog berhasil dihapus');
      set_message('toastr_type', 'success');
      return redirect()->to('katalog');
    } else {
      set_message('toastr_msg', 'Katalog gagal dihapus');
      set_message('toastr_type', 'warning');
      set_message('message', 'Katalog gagal dihapus');
      return redirect()->to('katalog');
    }
  }
  public function apply_status($id)
  {
    $field = $this->request->getGet('field');
    $value = $this->request->getGet('value');

    $KatalogUpdate = $this->katalogModel->update($id, array($field => $value));

    if ($KatalogUpdate) {
      set_message('toastr_msg', 'Katalog berhasil disimpan');
      set_message('toastr_type', 'success');
    } else {
      set_message('toastr_msg', 'Katalog gagal disimpan');
      set_message('toastr_type', 'warning');
    }
    return redirect()->to('katalog');
  }

  public function report()
  {
    $db = db_connect('data');
    $builder = $db->table('catalogs as a')
      ->select('a.ID, a.BIBID, a.Title,  a.Edition, a. Publisher, a.PhysicalDescription, a.ControlNumber, a.IsOPAC, a.IsRDA')
      ->select('a.ID as action, 0 as Eksemplar')
      ->select('b.ID as Branch_id, b.Name as Perpustakaan, b.Name, b.Code, b.NPP_Provinsi_id, b.NPP_KabKota_id, b.NPP_Kecamatan_id, b.NPP_Kelurahan_id, b.NPP_id')
      ->join('branchs b', 'b.ID = a.Branch_id', 'inner')
      ->where('a.IsQUARANTINE', 0);



    $results = $builder->get()->getResult();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->mergeCells('A1:M1');
    $sheet->setCellValue("A1", "Laporan Katalog");
    $sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(12);

    $sheet->setCellValue("A2", "Branch ID");
    $sheet->setCellValue("B2", "NPP");
    $sheet->setCellValue("C2", "Mitra Perpustakaan");
    $sheet->setCellValue("D2", "Catalog ID");
    $sheet->setCellValue("E2", "BIBID");
    $sheet->setCellValue("F2", "Judul");
    $sheet->setCellValue("G2", "Edisi");
    $sheet->setCellValue("H2", "Publisher");
    $sheet->setCellValue("I2", "Deskripsi Fisik");
    $sheet->setCellValue("J2", "No. Panggil");
    $sheet->setCellValue("K2", "Eksemplar");
    $sheet->setCellValue("L2", "OPAC");
    $sheet->setCellValue("M2", "Pedoman Katalog");

    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(10);
    $sheet->getColumnDimension('C')->setWidth(75);
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(50);
    $sheet->getColumnDimension('G')->setWidth(10);
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->getColumnDimension('J')->setWidth(20);
    $sheet->getColumnDimension('K')->setWidth(10);
    $sheet->getColumnDimension('L')->setWidth(10);
    $sheet->getColumnDimension('M')->setWidth(20);

    $sheet->getStyle('A2:M2')->getFont()->setBold(true)->setSize(12);

    $col = 3;
    $no = 1;
    $i = 1;
    foreach ($results as $row) {
      $collections = count_all('collections', 'Catalog_id = ' . $row->ID, 'data');
      $sheet->setCellValue("A" . $col, $row->Branch_id);
      $sheet->setCellValue("B" . $col, $row->Code);
      $sheet->setCellValue("C" . $col, $row->Perpustakaan);
      $sheet->setCellValue("D" . $col, $row->ID);
      $sheet->setCellValue("E" . $col, $row->BIBID);
      $sheet->setCellValue("F" . $col, $row->Title);
      $sheet->setCellValue("G" . $col, $row->Edition);
      $sheet->setCellValue("H" . $col, $row->Publisher);
      $sheet->setCellValue("I" . $col, $row->PhysicalDescription);
      $sheet->setCellValue("J" . $col, $row->ControlNumber);
      $sheet->setCellValue("K" . $col, $collections ?? 0);
      $sheet->setCellValue("L" . $col, $row->IsOPAC);
      $sheet->setCellValue("M" . $col, $row->IsRDA);

      $col++;
      $no++;
      $i++;
    }

    $writer = new Xlsx($spreadsheet);
    $subject = 'Laporan Katalog';
    $filename = ucwords($subject) . '-' . date('Y-m-d');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
  }

  public function view_decrypted($ID)
  {
    // Load the file model


    // Get the file record
    $file = $this->fileModel->find($ID);
    if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
      return $this->response->setStatusCode(404)->setBody('File not found');
    }

    // Instead of serving the file directly, we'll render a view with our custom PDF viewer
    return view('Katalog\Views\slug\pdf_viewer', ['fileId' => $ID, 'fileName' => $file->FileURL]);
  }

  public function view_decrypted_article($ID)
	{
		// Load the file model


		// Get the file record
		$file = $this->serialArticleFilesModel->find($ID);
		if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
			return $this->response->setStatusCode(404)->setBody('File not found');
		}

		// Instead of serving the file directly, we'll render a view with our custom PDF viewer
		return view('Katalog\Views\slug\pdf_viewer_article', ['fileId' => $ID, 'fileName' => $file->FileURL]);
	}

  public function get_decrypted_content($ID)
  {

    $file = $this->fileModel->find($ID);

    if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
      return $this->response->setStatusCode(404)->setBody('File not found');
    }

    $tempDecryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');
    $encryption = new \App\Libraries\Encryption();
    $encryption->decryptFile($this->modulePath . $file->FileURL, $tempDecryptedFile);

    $this->response->setContentType('application/pdf');
    $this->response->setHeader('Content-Disposition', 'inline; filename="' . $file->FileURL . '"');
    $this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');
    $this->response->setHeader('Content-Security-Policy', "default-src 'self'; object-src 'self'");

    $content = file_get_contents($tempDecryptedFile);
    unlink($tempDecryptedFile);

    return $this->response->setBody($content);
  }

  public function get_decrypted_content_article($ID)
	{

		$file = $this->serialArticleFilesModel->find($ID);

		if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
			return $this->response->setStatusCode(404)->setBody('File not found');
		}

		$tempDecryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');
		$encryption = new \App\Libraries\Encryption();
		$encryption->decryptFile($this->modulePath . $file->FileURL, $tempDecryptedFile);

		$this->response->setContentType('application/pdf');
		$this->response->setHeader('Content-Disposition', 'inline; filename="' . $file->FileURL . '"');
		$this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');
		$this->response->setHeader('Content-Security-Policy', "default-src 'self'; object-src 'self'");

		$content = file_get_contents($tempDecryptedFile);
		unlink($tempDecryptedFile);

		return $this->response->setBody($content);
	}

  // Fungsi untuk menampilkan form
  public function showCreateForm()
  {
    echo view('Katalog\Views\marc_import_form');
  }
  public function createFromMarcFile()
  {
    if ($this->request->getMethod() !== 'post') {
      return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan']);
    }

    $file = $this->request->getFile('marc_file');

    $this->db->transStart();

    try {
      // Baca file MARC menggunakan File_MARC langsung
      $marcContent = file_get_contents($file->getTempName());
      $marc = new \File_MARC($marcContent, \File_MARC::SOURCE_STRING);

      $record = $marc->next(); // Ambil record pertama

      if (!$record) {
        throw new \Exception("Tidak ada record MARC yang valid ditemukan");
      }

      $catalogData = [];

      // Mapping data MARC menggunakan File_MARC
      $field001 = $record->getField('001');
      $catalogData['ControlNumber'] = $field001 ? $field001->getData() : null;

      $field035 = $record->getField('035');
      $catalogData['BIBID'] = ($field035 && $field035->getSubfield('a')) ? $field035->getSubfield('a')->getData() : null;

      $field100 = $record->getField('100');
      $catalogData['Author'] = ($field100 && $field100->getSubfield('a')) ? $field100->getSubfield('a')->getData() : null;

      // Title (245)
      $field245 = $record->getField('245');
      $title_a = ($field245 && $field245->getSubfield('a')) ? $field245->getSubfield('a')->getData() : '';
      $subtitle = ($field245 && $field245->getSubfield('b')) ? $field245->getSubfield('b')->getData() : '';
      $authorResp = ($field245 && $field245->getSubfield('c')) ? $field245->getSubfield('c')->getData() : '';
      $catalogData['Title'] = trim(rtrim(trim("{$title_a} {$subtitle}"), ':') . " / {$authorResp}");

      // Edition (250)
      $field250 = $record->getField('250');
      $catalogData['Edition'] = ($field250 && $field250->getSubfield('a')) ? $field250->getSubfield('a')->getData() : null;

      // Publication (260)
      $field260 = $record->getField('260');

      $pub_a = ($field260 && $field260->getSubfield('a')) ? $field260->getSubfield('a')->getData() : '';

      $pub_b = ($field260 && $field260->getSubfield('b')) ? $field260->getSubfield('b')->getData() : '';
      $pub_c = ($field260 && $field260->getSubfield('c')) ? $field260->getSubfield('c')->getData() : '';

      $catalogData['PublishLocation'] = $pub_a;
      $catalogData['Publisher'] = $pub_b;
      $catalogData['PublishYear'] = $pub_c;
      $catalogData['Publikasi'] = trim("{$catalogData['PublishLocation']}: {$catalogData['Publisher']}, {$catalogData['PublishYear']}");


      // Physical Description (300)
      $field300 = $record->getField('300');
      $catalogData['PhysicalDescription'] = ($field300 && $field300->getSubfield('a')) ? $field300->getSubfield('a')->getData() : null;

      // Subject (650)
      $field650 = $record->getField('650');
      $catalogData['Subject'] = ($field650 && $field650->getSubfield('a')) ? $field650->getSubfield('a')->getData() : null;

      // ISBN (020)
      $field020 = $record->getField('020');
      $catalogData['ISBN'] = ($field020 && $field020->getSubfield('a')) ? $field020->getSubfield('a')->getData() : null;

      // Dewey (082)
      $field082 = $record->getField('082');
      $catalogData['DeweyNo'] = ($field082 && $field082->getSubfield('a')) ? $field082->getSubfield('a')->getData() : null;

      // Call Number (084)
      $field084 = $record->getField('084');
      $catalogData['CallNumber'] = ($field084 && $field084->getSubfield('a')) ? $field084->getSubfield('a')->getData() : null;

      // Default values
      $catalogData['IsOPAC'] = 1;
      $catalogData['Worksheet_id'] = 1;
      $catalogData['Branch_id'] = 2522;
      $catalogData['CreateBy'] = 33;
      $catalogData['CreateDate'] = date('Y-m-d H:i:s');
      $catalogData['CreateTerminal'] = $this->request->getIPAddress();
      $catalogData['active'] = 1;

      // Filter data sesuai field tabel
      $tableFields = $this->db->getFieldNames('catalogs');
      $filteredData = [];
      foreach ($catalogData as $key => $value) {
        if (in_array($key, $tableFields)) {
          $filteredData[$key] = $value;
        }
      }

      // Insert ke catalogs
      $insertResult = $this->db->table('catalogs')->insert($filteredData);

      if (!$insertResult) {
        throw new \Exception("Gagal menyimpan data ke tabel catalogs: " . $this->db->error()['message']);
      }

      $newCatalogId = $this->db->insertID();

      if (!$newCatalogId) {
        throw new \Exception("Gagal mendapatkan ID catalog yang baru diinsert");
      }

      // Insert ke catalog_ruas dengan parsing yang benar
      $ruasBatchData = [];
      $sequence = 1;

      // Ambil semua fields menggunakan File_MARC
      $fields = $record->getFields();
      foreach ($fields as $field) {
        $tag = $field->getTag();

        // Jika control field (001, 003, 005, 006, 007, 008, 009)
        if ($tag < '010') {
          $ruasBatchData[] = [
            'CatalogId' => $newCatalogId,
            'Tag' => $tag,
            'Indicator1' => null,
            'Indicator2' => null,
            'Value' => $field->getData(),
            'Sequence' => $sequence++,
          ];
        } else {
          // Data field dengan indicator dan subfield
          $indicator1 = $field->getIndicator(1);
          $indicator2 = $field->getIndicator(2);

          // Convert space atau null ke #
          $indicator1 = ($indicator1 === ' ' || $indicator1 === null) ? '#' : $indicator1;
          $indicator2 = ($indicator2 === ' ' || $indicator2 === null) ? '#' : $indicator2;

          // Ambil semua subfield
          $subfields = $field->getSubfields();
          if (!empty($subfields)) {
            foreach ($subfields as $subfield) {
              $ruasBatchData[] = [
                'CatalogId' => $newCatalogId,
                'Tag' => $tag,
                'Indicator1' => $indicator1,
                'Indicator2' => $indicator2,
                'Value' => '$' . $subfield->getCode() . ' ' . $subfield->getData(),
                'Sequence' => $sequence++,
              ];
            }
          } else {
            // Jika tidak ada subfield, simpan sebagai field kosong
            $ruasBatchData[] = [
              'CatalogId' => $newCatalogId,
              'Tag' => $tag,
              'Indicator1' => $indicator1,
              'Indicator2' => $indicator2,
              'Value' => '',
              'Sequence' => $sequence++,
            ];
          }
        }
      }

      if (!empty($ruasBatchData)) {
        $this->db->table('catalog_ruas')->insertBatch($ruasBatchData);
      }

      $this->db->transCommit();

      return $this->response->setJSON([
        'success' => true,
        'catalog_id' => $newCatalogId,
        'title' => $catalogData['Title']
      ]);
    } catch (\Exception $e) {
      $this->db->transRollback();
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
    }
  }
  public function createFromMarcFileold()
  {
    if ($this->request->getMethod() !== 'post') {
      return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan']);
    }

    $file = $this->request->getFile('marc_file');

    $this->db->transStart();

    try {
      // Baca file MARC menggunakan File_MARC langsung
      $marcContent = file_get_contents($file->getTempName());
      $marc = new \File_MARC($marcContent, \File_MARC::SOURCE_STRING);

      $record = $marc->next(); // Ambil record pertama

      if (!$record) {
        throw new \Exception("Tidak ada record MARC yang valid ditemukan");
      }

      $catalogData = [];

      // Mapping data MARC menggunakan File_MARC
      $field001 = $record->getField('001');
      $catalogData['ControlNumber'] = $field001 ? $field001->getData() : null;

      $field035 = $record->getField('035');
      $catalogData['BIBID'] = ($field035 && $field035->getSubfield('a')) ? $field035->getSubfield('a')->getData() : null;

      $field100 = $record->getField('100');
      $catalogData['Author'] = ($field100 && $field100->getSubfield('a')) ? $field100->getSubfield('a')->getData() : null;

      // Title (245)
      $field245 = $record->getField('245');
      $title_a = ($field245 && $field245->getSubfield('a')) ? $field245->getSubfield('a')->getData() : '';
      $subtitle = ($field245 && $field245->getSubfield('b')) ? $field245->getSubfield('b')->getData() : '';
      $authorResp = ($field245 && $field245->getSubfield('c')) ? $field245->getSubfield('c')->getData() : '';
      $catalogData['Title'] = trim(rtrim(trim("{$title_a} {$subtitle}"), ':') . " / {$authorResp}");

      // Edition (250)
      $field250 = $record->getField('250');
      $catalogData['Edition'] = ($field250 && $field250->getSubfield('a')) ? $field250->getSubfield('a')->getData() : null;

      // Publication (260)
      $field260 = $record->getField('264');
      dd($field260);
      $pub_a = ($field260 && $field260->getSubfield('a')) ? $field260->getSubfield('a')->getData() : '';
      $pub_b = ($field260 && $field260->getSubfield('b')) ? $field260->getSubfield('b')->getData() : '';
      $pub_c = ($field260 && $field260->getSubfield('c')) ? $field260->getSubfield('c')->getData() : '';

      $catalogData['PublishLocation'] = rtrim(trim($pub_a), ':');
      $catalogData['Publisher'] = rtrim(trim($pub_b), ',');
      $catalogData['PublishYear'] = $pub_c;
      $catalogData['Publikasi'] = trim("{$catalogData['PublishLocation']}: {$catalogData['Publisher']}, {$catalogData['PublishYear']}");

      // Physical Description (300)
      $field300 = $record->getField('300');
      $catalogData['PhysicalDescription'] = ($field300 && $field300->getSubfield('a')) ? $field300->getSubfield('a')->getData() : null;

      // Subject (650)
      $field650 = $record->getField('650');
      $catalogData['Subject'] = ($field650 && $field650->getSubfield('a')) ? $field650->getSubfield('a')->getData() : null;

      // ISBN (020)
      $field020 = $record->getField('020');
      $catalogData['ISBN'] = ($field020 && $field020->getSubfield('a')) ? $field020->getSubfield('a')->getData() : null;

      // Dewey (082)
      $field082 = $record->getField('082');
      $catalogData['DeweyNo'] = ($field082 && $field082->getSubfield('a')) ? $field082->getSubfield('a')->getData() : null;

      // Call Number (084)
      $field084 = $record->getField('084');
      $catalogData['CallNumber'] = ($field084 && $field084->getSubfield('a')) ? $field084->getSubfield('a')->getData() : null;

      // Default values
      $catalogData['IsOPAC'] = 1;
      $catalogData['Worksheet_id'] = 1;
      $catalogData['Branch_id'] = 2522;
      $catalogData['CreateBy'] = 33;
      $catalogData['CreateDate'] = date('Y-m-d H:i:s');
      $catalogData['CreateTerminal'] = $this->request->getIPAddress();
      $catalogData['active'] = 1;

      // Filter data sesuai field tabel
      $tableFields = $this->db->getFieldNames('catalogs');
      $filteredData = [];
      foreach ($catalogData as $key => $value) {
        if (in_array($key, $tableFields)) {
          $filteredData[$key] = $value;
        }
      }

      // Insert ke catalogs
      $insertResult = $this->db->table('catalogs')->insert($filteredData);

      if (!$insertResult) {
        throw new \Exception("Gagal menyimpan data ke tabel catalogs: " . $this->db->error()['message']);
      }

      $newCatalogId = $this->db->insertID();

      if (!$newCatalogId) {
        throw new \Exception("Gagal mendapatkan ID catalog yang baru diinsert");
      }

      // Insert ke catalog_ruas dengan parsing yang benar
      $ruasBatchData = [];
      $sequence = 1;

      // Ambil semua fields menggunakan File_MARC
      $fields = $record->getFields();
      foreach ($fields as $field) {
        $tag = $field->getTag();

        // Jika control field (001, 003, 005, 006, 007, 008, 009)
        if ($tag < '010') {
          $ruasBatchData[] = [
            'CatalogId' => $newCatalogId,
            'Tag' => $tag,
            'Indicator1' => null,
            'Indicator2' => null,
            'Value' => $field->getData(),
            'Sequence' => $sequence++,
          ];
        } else {
          // Data field dengan indicator dan subfield
          $indicator1 = $field->getIndicator(1);
          $indicator2 = $field->getIndicator(2);

          // Ambil semua subfield
          $subfields = $field->getSubfields();
          if (!empty($subfields)) {
            foreach ($subfields as $subfield) {
              $ruasBatchData[] = [
                'CatalogId' => $newCatalogId,
                'Tag' => $tag,
                'Indicator1' => $indicator1 === ' ' ? null : $indicator1,
                'Indicator2' => $indicator2 === ' ' ? null : $indicator2,
                'Value' => $subfield->getCode() . $subfield->getData(),
                'Sequence' => $sequence++,
              ];
            }
          } else {
            // Jika tidak ada subfield, simpan sebagai field kosong
            $ruasBatchData[] = [
              'CatalogId' => $newCatalogId,
              'Tag' => $tag,
              'Indicator1' => $indicator1 === ' ' ? null : $indicator1,
              'Indicator2' => $indicator2 === ' ' ? null : $indicator2,
              'Value' => '',
              'Sequence' => $sequence++,
            ];
          }
        }
      }

      if (!empty($ruasBatchData)) {
        $this->db->table('catalog_ruas')->insertBatch($ruasBatchData);
      }

      $this->db->transCommit();

      return $this->response->setJSON([
        'success' => true,
        'catalog_id' => $newCatalogId,
        'title' => $catalogData['Title']
      ]);
    } catch (\Exception $e) {
      $this->db->transRollback();
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
    }
  }
  public function deleteEdisiSerial(int $id, int $catalog_id)
	{
		$data = $this->edisiSerialModel->find($id);
		if ($data) {
			$this->edisiSerialModel->delete($id);
			return redirect()->to('/katalog/edit/' . $catalog_id . '?slug=edisi_serial');
		} else {
			return $this->failNotFound('Data edisi serial not found (' . $id . ")");
		}
	}

  	public function create_artikel()
	{
		$validation = \Config\Services::validation();

		$catalog_id     = $this->request->getPost('catalog_id');
		$title          = $this->request->getPost('title');
		$creator        = $this->request->getPost('creator_final');
		$contributor    = $this->request->getPost('contributor_final');
		$start_page     = $this->request->getPost('start_page');
		$pages          = $this->request->getPost('pages');
		$subject        = $this->request->getPost('subject_final');
		$edisi_serial   = $this->request->getPost('edisi_serial');
		$tanggal_terbit = $this->request->getPost('tanggal_terbit');
		$isopac         = $this->request->getPost('isopac') ? 1 : 0;

		$validation->setRules([
			'catalog_id' => 'required|integer',
			'title'      => 'required|string',
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->response->setJSON([
				'status' => 400,
				'messages' => [
					'error' => $validation->getErrors()
				]
			]);
		}

		$data = [
			'Catalog_id'         => $catalog_id,
			'Title'             => $title,
			'Creator'           => $creator,
			'Contributor'       => $contributor,
			'StartPage'         => $start_page,
			'Pages'             => $pages,
			'Subject'           => $subject,
			'EDISISERIAL'       => $edisi_serial,
			'TANGGAL_TERBIT_EDISI_SERIAL' => $tanggal_terbit,
			'ISOPAC'            => $isopac,
		];

		$db = db_connect('data');
		$builder = $db->table('serial_articles');

		try {
			$builder->insert($data);
		} catch (\Exception $e) {
			$response = [
				'error' => true,
				'message' => 'Artikel gagal disimpan',
			];
		}

		$catalog = $db->table('catalogs')->where('ID', $catalog_id)->get()->getRow();

		$data = [
			'catalog' => $catalog,
		];

		set_message('toastr_msg', 'Artikel berhasil disimpan');
		set_message('toastr_type', 'success');

		set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
		return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=artikel'));
	}

	public function edit_artikel($id = null)
	{
		if (!$id) {
			return $this->response->setJSON([
				'error' => true,
				'message' => 'ID is required'
			])->setStatusCode(400);
		}

		$validation = \Config\Services::validation();

		$catalog_id     = $this->request->getPost('catalog_id');
		$title          = $this->request->getPost('title');
		$creator        = $this->request->getPost('creator_final');
		$contributor    = $this->request->getPost('contributor_final');
		$start_page     = $this->request->getPost('start_page');
		$pages          = $this->request->getPost('pages');
		$subject        = $this->request->getPost('subject_final');
		$edisi_serial   = $this->request->getPost('edisi_serial');
		$tanggal_terbit = $this->request->getPost('tanggal_terbit');
		$isopac         = $this->request->getPost('isopac') ? 1 : 0;

		$validation->setRules([
			'catalog_id' => 'required|integer',
			'title'      => 'required|string',
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->response->setJSON([
				'error' => true,
				'message' => $validation->getErrors()
			])->setStatusCode(400);
		}

		$db = db_connect('data');
		$builder = $db->table('serial_articles');

		$existing = $builder->where('id', $id)->get()->getRow();
		if (!$existing) {
			return $this->response->setJSON([
				'error' => true,
				'message' => 'Artikel tidak ditemukan'
			])->setStatusCode(404);
		}

		$data = [
			'Catalog_id'         => $catalog_id,
			'Title'              => $title,
			'Creator'            => $creator,
			'Contributor'        => $contributor,
			'StartPage'          => $start_page,
			'Pages'              => $pages,
			'Subject'            => $subject,
			'EDISISERIAL'        => $edisi_serial,
			'TANGGAL_TERBIT_EDISI_SERIAL' => $tanggal_terbit,
			'ISOPAC'             => $isopac,
		];

		try {
			$builder->where('id', $id)->update($data);
		} catch (\Exception $e) {
			$response = [
				'error' => true,
				'message' => 'Artikel gagal disimpan',
			];
		}

		$response = [
			'error' => false,
			'message' => 'Artikel berhasil disimpan',
		];

		set_message('toastr_msg', 'Artikel berhasil diperbarui');
		set_message('toastr_type', 'success');

		set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
		return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=artikel'));
	}

  	public function delete_artikel($id = null)
	{
		if (!$id) {
			return $this->response->setJSON([
				'error' => true,
				'message' => 'ID is required'
			])->setStatusCode(400);
		}

		$db = db_connect('data');
		$builder = $db->table('serial_articles');

		$artikel = $builder->where('id', $id)->get()->getRow();

		if (!$artikel) {
			return $this->response->setJSON([
				'error' => true,
				'message' => 'Artikel tidak ditemukan'
			])->setStatusCode(404);
		}

		try {
			$builder->where('id', $id)->delete();

			return $this->response->setJSON([
				'error' => false,
				'message' => 'Artikel berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'error' => true,
				'message' => 'Gagal menghapus artikel: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

  public function get_artikel($id = null)
	{
		if (!$id) {
			return $this->response->setJSON(['error' => true, 'message' => 'ID is required'])->setStatusCode(400);
		}

		$artikel = $this->artikelModel->get_by_id($id);

		if (!$artikel) {
			return $this->response->setJSON(['error' => true, 'message' => 'Article not found'])->setStatusCode(404);
		}

		return $this->response->setJSON(['error' => false, 'data' => $artikel]);
	}
}
