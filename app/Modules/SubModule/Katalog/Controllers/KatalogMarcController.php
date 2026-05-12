<?php

namespace Katalog\Controllers;

use Base\Models\DataModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * KatalogMarcController
 * * Menangani semua operasi terkait MARC:
 * - Form tambah/edit katalog berbasis MARC
 * - Ekspor data MARC (txt / xlsx)
 * - Import katalog dari file .mrc
 */
class KatalogMarcController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // CREATE MARC
    // ----------------------------------------------------------------

    public function create_marc()
    {
        $data['title'] = 'Tambah Katalog Form MARC';
        $this->validation->setRule('Worksheet_id', 'Jenis Bahan', 'required');

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $catalogsModel = new DataModel('catalogs', null, 'ID');
                $save_data     = [
                    'Worksheet_id'  => $this->request->getPost('Worksheet_id'),
                    'ControlNumber' => get_control_number(),
                    'BIBID'         => random_string('numeric', 13),
                    'Branch_id'     => user()->branch_id,
                ];

                if (!empty($this->request->getPost('IsRDA'))) {
                    $save_data['IsRDA'] = $this->request->getPost('IsRDA') ? 1 : 0;
                }

                $CatalogId  = $catalogsModel->insert($save_data);
                $catalog_ruas_data = $this->_buildRuasData($CatalogId);

                if (!empty($catalog_ruas_data)) {
                    // Sisipkan Tag 001 di paling awal
                    array_unshift($catalog_ruas_data, [
                        'CatalogId'  => $CatalogId,
                        'Tag'        => '001',
                        'Value'      => $save_data['ControlNumber'],
                        'Indicator1' => '',
                        'Indicator2' => '',
                    ]);

                    $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
                    $katalogRuasModel->insertBatch($catalog_ruas_data);

                    $catalogsModel->update($CatalogId, convert_catalog_ruas($CatalogId));
                }

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Katalog Form MARC berhasil ditambah');
                
                return redirect()->to('katalog');

            } else {
                // Tampilkan SweetAlert jika validasi gagal
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);
            }
        }

        $data = array_merge($data, $this->_getMarcFormData());
        return view('Katalog\Views\add_marc', $data);
    }

    // ----------------------------------------------------------------
    // EDIT MARC
    // ----------------------------------------------------------------

    public function edit_marc($id = null)
    {
        $catalogsModel = new DataModel('catalogs', null, 'ID');
        $catalog       = $catalogsModel->find($id);

        if (!$catalog) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data katalog tidak ditemukan');
            return redirect()->to('katalog');
        }

        $data['title']   = 'Edit Katalog Form MARC';
        $data['catalog'] = $catalog;

        $this->validation->setRule('Worksheet_id', 'Jenis Bahan', 'required');

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $catalogsModel->update($id, [
                    'Worksheet_id' => $this->request->getPost('Worksheet_id'),
                ]);

                $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
                $katalogRuasModel->where('CatalogId', $id)->delete();

                $catalog_ruas_data = $this->_buildRuasData($id);

                if (!empty($catalog_ruas_data)) {
                    // Pastikan Tag 001 ada
                    $has_001 = false;
                    foreach ($catalog_ruas_data as $item) {
                        if ($item['Tag'] === '001') { $has_001 = true; break; }
                    }
                    if (!$has_001) {
                        array_unshift($catalog_ruas_data, [
                            'CatalogId'  => $id,
                            'Tag'        => '001',
                            'Value'      => $catalog->ControlNumber,
                            'Indicator1' => '',
                            'Indicator2' => '',
                        ]);
                    }

                    $katalogRuasModel->insertBatch($catalog_ruas_data);
                    $catalogsModel->update($id, convert_catalog_ruas($id));
                }

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Data katalog berhasil diupdate');
                return redirect()->to('katalog');

            } else {
                // Tampilkan SweetAlert jika validasi gagal
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);
            }
        }

        // GET - tampilkan form dengan data existing
        $session      = service('session');
        $worksheet_id = $this->request->getvar('worksheet_id') ?? $catalog->Worksheet_id;
        $session->remove('worksheet_id');
        $session->remove('worksheet_fields');
        $session->set('worksheet_id', $worksheet_id);

        $katalogRuasModel = new DataModel('catalog_ruas', null, 'ID');
        $existing_ruas    = $katalogRuasModel->where('CatalogId', $id)->findAll();

        $existing_data = [];
        foreach ($existing_ruas as $ruas) {
            $existing_data[$ruas->Tag][] = [
                'Value'      => $ruas->Value,
                'Indicator1' => $ruas->Indicator1,
                'Indicator2' => $ruas->Indicator2,
            ];
        }

        $all_tags              = get_all_tags($worksheet_id);
        $data['existing_data'] = $existing_data;
        $data['session_tags']  = $all_tags->session_tags;
        $data['filtered_tags'] = $all_tags->filtered_tags;
        $data                  = array_merge($data, $this->_getWorksheetList());

        return view('Katalog\Views\edit_marc', $data);
    }

    // ----------------------------------------------------------------
    // EKSPOR MARC
    // ----------------------------------------------------------------

    public function ekspor_marc()
    {
        $IDs    = $this->request->getVar('ID');
        $format = $this->request->getVar('format') ?? 'mrc';

        if (!$IDs || empty($format)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Format dan data katalog harus dipilih.');
            return redirect()->back();
        }

        $collection = [];
        foreach ($IDs as $id) {
            $catalog = $this->katalogModel->asArray()->find($id);
            if (!$catalog) continue;

            $record = $this->katalogRuasModel
                ->select('*')
                ->where('CatalogId', $id)
                ->orderBy('Sequence', 'ASC')
                ->findAll();

            $collection[] = $record;
        }

        if (count($collection) === 0) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada data MARC yang ditemukan.');
            return redirect()->back();
        }

        switch ($format) {
            case 'txt':
                return $this->_eksporTxt($collection);

            case 'xlsx':
                return $this->_eksporXlsx($collection);

            default:
                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', 'Format tidak dikenali.');
                return redirect()->back();
        }
    }

    // ----------------------------------------------------------------
    // IMPORT DARI FILE MARC
    // ----------------------------------------------------------------

    public function showCreateForm()
    {
        echo view('Katalog\Views\marc_import_form');
    }

    public function createFromMarcFile()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan']);
        }

        $file        = $this->request->getFile('marc_file');
        $marcContent = file_get_contents($file->getTempName());

        // Normalisasi karakter INLISLite
        if (strpos($marcContent, '^') !== false && strpos($marcContent, '$') !== false) {
            $marcContent = str_replace(['^', '$'], [chr(30), chr(31)], $marcContent);
        } elseif (strpos($marcContent, '▲') !== false) {
            $marcContent = str_replace(['▲', '▼', '↔'], [chr(30), chr(31), chr(29)], $marcContent);
        }

        $this->db->transStart();

        try {
            $marc   = new \File_MARC($marcContent, \File_MARC::SOURCE_STRING);
            $record = $marc->next();

            if (!$record) {
                throw new \Exception("Record MARC tidak valid.");
            }

            $taglistData = $this->_parseFieldsToTaglist($record);
            $catalogData = $this->_mapTagsToCatalogData($taglistData);

            $catalogData['Worksheet_id']    = 1;
            $catalogData['Branch_id']       = 2522;
            $catalogData['CreateBy']        = user_id();
            $catalogData['CreateDate']      = date("Y-m-d H:i:s");
            $catalogData['UpdateBy']        = user_id();
            $catalogData['UpdateDate']      = date("Y-m-d H:i:s");
            $catalogData['CreateTerminal']  = $this->request->getIPAddress();
            $catalogData['active']          = 1;

            $tableFields  = $this->db->getFieldNames('catalogs');
            $filteredData = array_intersect_key($catalogData, array_flip($tableFields));

            $this->db->table('catalogs')->insert($filteredData);
            $newCatalogId = $this->db->insertID();

            if (!$newCatalogId) throw new \Exception("Gagal menyimpan data katalog utama.");

            // Simpan BIBID manual (Tag 035)
            if (!empty($catalogData['BIBID'])) {
                $this->db->table('catalog_ruas')->insert([
                    'CatalogId'  => $newCatalogId,
                    'Tag'        => '035',
                    'Indicator1' => '#',
                    'Indicator2' => '#',
                    'Value'      => '$a ' . $catalogData['BIBID'],
                    'Sequence'   => 0,
                ]);
            }

            // Simpan semua ruas
            $ruasBatch = [];
            $seq       = 1;
            foreach ($taglistData as $dataruas) {
                if ($dataruas['tag'] == '035' || strtolower($dataruas['tag']) == 'leader') continue;

                $ind1 = ($dataruas['ind1'] === ' ' || $dataruas['ind1'] === null) ? '#' : $dataruas['ind1'];
                $ind2 = ($dataruas['ind2'] === ' ' || $dataruas['ind2'] === null) ? '#' : $dataruas['ind2'];

                $ruasBatch[] = [
                    'CatalogId'  => $newCatalogId,
                    'Tag'        => $dataruas['tag'],
                    'Indicator1' => $ind1,
                    'Indicator2' => $ind2,
                    'Value'      => $dataruas['value'],
                    'Sequence'   => $seq++,
                ];
            }

            if (!empty($ruasBatch)) {
                $this->db->table('catalog_ruas')->insertBatch($ruasBatch);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Berhasil submit data pada judul: ' . ($catalogData['Title'] ?? 'Tanpa Judul'),
                'catalog_id' => $newCatalogId,
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine(),
            ]);
        }
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS - FORM
    // ----------------------------------------------------------------

    /**
     * Bangun array catalog_ruas dari data POST MARC.
     */
    private function _buildRuasData(int $CatalogId): array
    {
        $Indicator1s = $this->request->getPost('Indicator1');
        $Indicator2s = $this->request->getPost('Indicator2');
        $Values      = $this->request->getPost('Value');

        $catalogRuasData = [];
        foreach ($Values as $key => $value) {
            $items = [];
            $items['Value']      = is_array($Values[$key])      ? $Values[$key]      : [$Values[$key]];
            $items['Indicator1'] = is_array($Indicator1s[$key]) ? $Indicator1s[$key] : [$Indicator1s[$key]];
            $items['Indicator2'] = is_array($Indicator2s[$key]) ? $Indicator2s[$key] : [$Indicator2s[$key]];
            $catalogRuasData[$key] = $items;
        }

        $catalog_ruas_data = [];
        foreach ($catalogRuasData as $key => $items) {
            foreach ($items['Value'] as $index => $row) {
                $catalog_ruas_data[] = [
                    'CatalogId'  => $CatalogId,
                    'Tag'        => $key,
                    'Value'      => $items['Value'][$index] ?? '',
                    'Indicator1' => $items['Indicator1'][$index] ?? '',
                    'Indicator2' => $items['Indicator2'][$index] ?? '',
                ];
            }
        }

        // Filter baris yang hanya berisi '$a' tanpa nilai
        $catalog_ruas_data = array_values(array_filter($catalog_ruas_data, function ($item) {
            return trim($item['Value']) !== '$a';
        }));

        return $catalog_ruas_data;
    }

    /**
     * Ambil data worksheet untuk form MARC.
     */
    private function _getMarcFormData(): array
    {
        $session      = service('session');
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

        return [
            'session_tags'  => $all_tags->session_tags,
            'filtered_tags' => $all_tags->filtered_tags,
        ] + $this->_getWorksheetList();
    }

    private function _getWorksheetList(): array
    {
        $worksheetModel = new DataModel('worksheets', null, 'ID');
        return ['worksheets' => $worksheetModel->orderBy('NoUrut')->findAll()];
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS - EKSPOR
    // ----------------------------------------------------------------

    private function _eksporTxt(array $collection)
    {
        $content = '';
        foreach ($collection as $record) {
            $content .= "MARC-" . $record[0]->ID . "\n";
            $content .= "=LDR  00000nam  2200000   4500\n";

            foreach ($record as $field) {
                $tag  = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
                $ind1 = $field->Indicator1 ?: ' ';
                $ind2 = $field->Indicator2 ?: ' ';

                if (intval($tag) < 10) {
                    $content .= "={$tag}  {$field->Value}\n";
                } else {
                    $content .= "={$tag}  {$ind1}{$ind2}\${$field->Value}\n";
                }
            }
            $content .= "\n";
        }

        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setHeader('Content-Disposition', 'attachment; filename="export_marc.txt"')
            ->setBody($content);
    }

    private function _eksporXlsx(array $collection)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $headers = ['Id Katalog', 'Tag', 'Ind1', 'Ind2', 'Nilai'];
        $sheet->fromArray($headers, NULL, 'A1');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->applyFromArray([
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFDDDDDD'],
        ]);

        $row             = 2;
        $lastKatalogId   = null;

        foreach ($collection as $record) {
            $idKatalog = $record[0]->ID ?? '';

            usort($record, function ($a, $b) {
                if ($a->Tag === 'a') return -1;
                if ($b->Tag === 'a') return 1;
                $aIsNum = is_numeric($a->Tag);
                $bIsNum = is_numeric($b->Tag);
                if ($aIsNum && $bIsNum) return (int)$a->Tag <=> (int)$b->Tag;
                if ($aIsNum) return -1;
                if ($bIsNum) return 1;
                return strcmp($a->Tag, $b->Tag);
            });

            if ($idKatalog !== $lastKatalogId) {
                $urutan        = 1;
                $lastKatalogId = $idKatalog;
            }

            foreach ($record as $field) {
                $tag  = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
                $ind1 = $field->Indicator1 ?: ' ';
                $ind2 = $field->Indicator2 ?: ' ';

                $sheet->setCellValue('A' . $row, ($urutan == 1) ? $idKatalog : '');
                $sheet->setCellValue('B' . $row, $tag);
                if (intval($tag) >= 10) {
                    $sheet->setCellValue('C' . $row, $ind1);
                    $sheet->setCellValue('D' . $row, $ind2);
                }
                $sheet->setCellValue('E' . $row, $field->Value);
                $row++;
                $urutan++;
            }
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:E' . $lastRow)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer   = new Xlsx($spreadsheet);
        $filename = 'export_marc.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS - IMPORT / MAPPING
    // ----------------------------------------------------------------

    /**
     * Ubah object MARC menjadi array taglist standar.
     */
    private function _parseFieldsToTaglist($record): array
    {
        $taglistData = [];
        foreach ($record->getFields() as $field) {
            $tag = $field->getTag();
            $row = ['tag' => $tag, 'ind1' => ' ', 'ind2' => ' ', 'value' => ''];

            if ($tag < '010') {
                $row['value'] = $field->getData();
            } else {
                $row['ind1'] = $field->getIndicator(1);
                $row['ind2'] = $field->getIndicator(2);
                $tempVal = [];
                foreach ($field->getSubfields() as $subfield) {
                    $tempVal[] = '$' . $subfield->getCode() . ' ' . $subfield->getData();
                }
                $row['value'] = implode(' ', $tempVal);
            }

            $taglistData[] = $row;
        }
        return $taglistData;
    }

    /**
     * Petakan array taglist ke kolom tabel catalogs.
     */
    private function _mapTagsToCatalogData(array $taglist): array
    {
        $result    = [];
        $delimiter = ' ; ';

        foreach (['ControlNumber','Title','Author','Edition','Publisher','PublishLocation',
                  'PublishYear','Publikasi','Subject','PhysicalDescription','ISBN',
                  'CallNumber','Note','Languages','DeweyNo','BIBID','IsRDA'] as $k) {
            $result[$k] = null;
        }

        $cleanValue = function ($val) {
            return preg_replace('/\s+/', ' ', trim(preg_replace('/(\$\w)/', ' ', $val)));
        };

        foreach ($taglist as $tags) {
            $tagcode  = $tags['tag'];
            $tagvalue = $tags['value'];

            switch ($tagcode) {
                case '001':
                    $val = $cleanValue($tagvalue);
                    $result['ControlNumber'] = $result['ControlNumber']
                        ? $result['ControlNumber'] . $delimiter . $val : $val;
                    break;

                case '035':
                    if (preg_match('/\$a\s?(.*?)(?:\$|$)/', $tagvalue, $matches)) {
                        $result['BIBID'] = trim($matches[1]);
                    }
                    break;

                case '245':
                    $val = $cleanValue($tagvalue);
                    $result['Title'] = $result['Title'] ? $result['Title'] . $delimiter . $val : $val;
                    break;

                case '100': case '700': case '710': case '711':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['Author'] = $result['Author'] ? $result['Author'] . $delimiter . $val : $val;
                    }
                    break;

                case '250':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['Edition'] = $result['Edition'] ? $result['Edition'] . $delimiter . $val : $val;
                    }
                    break;

                case '260': case '264':
                    $result['IsRDA'] = ($tagcode == '264') ? 1 : 0;
                    $parts = explode('$', $tagvalue);
                    $locs = []; $pubs = []; $years = []; $fullPub = [];

                    foreach ($parts as $part) {
                        if (empty($part)) continue;
                        $code     = substr($part, 0, 1);
                        $val      = trim(substr($part, 1));
                        $valClean = trim($val, " :,;/");
                        if ($val != '') {
                            switch ($code) {
                                case 'a': $locs[]    = $valClean; break;
                                case 'b': $pubs[]    = $valClean; break;
                                case 'c': $years[]   = $valClean; break;
                            }
                            $fullPub[] = $val;
                        }
                    }

                    if (!empty($locs))    $result['PublishLocation'] = implode('; ', $locs);
                    if (!empty($pubs))    $result['Publisher']       = implode('; ', $pubs);
                    if (!empty($years))   $result['PublishYear']     = implode('; ', $years);
                    if (!empty($fullPub)) $result['Publikasi']       = implode(' ', $fullPub);
                    break;

                case '650': case '600': case '651':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['Subject'] = $result['Subject'] ? $result['Subject'] . ' -- ' . $val : $val;
                    }
                    break;

                case '300':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') $result['PhysicalDescription'] = $val;
                    break;

                case '020': case '022': case '024':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['ISBN'] = $result['ISBN'] ? $result['ISBN'] . $delimiter . $val : $val;
                    }
                    break;

                case '084':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['CallNumber'] = $result['CallNumber'] ? $result['CallNumber'] . $delimiter . $val : $val;
                    }
                    break;

                case '500': case '502': case '504': case '505': case '520': case '542':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['Note'] = $result['Note'] ? $result['Note'] . $delimiter . $val : $val;
                    }
                    break;

                case '082':
                    $val = $cleanValue($tagvalue);
                    if ($val != '') {
                        $result['DeweyNo'] = $result['DeweyNo'] ? $result['DeweyNo'] . $delimiter . $val : $val;
                    }
                    break;

                case '008':
                    if (strlen($tagvalue) >= 38) {
                        $lang = substr($tagvalue, 35, 3);
                        $result['Languages'] = $result['Languages'] ? $result['Languages'] . $delimiter . $lang : $lang;
                    }
                    break;
            }
        }

        return $result;
    }
}