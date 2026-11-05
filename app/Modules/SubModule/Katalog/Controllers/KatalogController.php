<?php

namespace Katalog\Controllers;

use Base\Models\DataModel;

/**
 * KatalogController
 * * Menangani: tampilan daftar, karantina, status OPAC,
 * hapus permanen, dan toggle status.
 */
class KatalogController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // INDEX & LIST
    // ----------------------------------------------------------------

    public function index()
    {
        $data['title']                = 'Daftar Katalog';
        $data['meta_description']     = 'Daftar dan pengelolaan katalog perpustakaan.';
        $data['is_catalog_list_page'] = true;
        $data['worksheets']           = $this->worksheetModel
            ->select('ID, Name')
            ->orderBy('NoUrut')
            ->findAll();
        echo view('Katalog\Views\list', $data);
    }

    /** Daftar katalog yang dikunci pada jenis bahan Artikel. */
    public function daftarArtikel()
    {
        $worksheet = $this->worksheetModel->select('ID, Name')->where('Name', 'Artikel')->first();
        if (!$worksheet) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Jenis bahan tidak ditemukan');
            $this->session->setFlashdata('swal_text', 'Jenis bahan Artikel belum tersedia.');
            return redirect()->to(base_url('katalog'));
        }

        $data['title']                = 'Daftar Artikel';
        $data['meta_description']     = 'Daftar katalog dengan jenis bahan Artikel.';
        $data['is_catalog_list_page'] = true;
        $data['locked_worksheet']     = $worksheet;
        echo view('Katalog\Views\list', $data);
    }

    /** Daftar katalog yang ada di keranjang (catalogs.IsKeranjang = 1). */
    public function keranjang()
    {
        $data['title']                = 'Keranjang Katalog';
        $data['meta_description']     = 'Daftar katalog yang ada di keranjang.';
        $data['is_catalog_list_page'] = true;
        $data['is_keranjang']         = true;
        $data['worksheets']           = $this->worksheetModel
            ->select('ID, Name')
            ->orderBy('NoUrut')
            ->findAll();
        echo view('Katalog\Views\list', $data);
    }

    public function masukkanKeranjang()
    {
        return $this->setKeranjang(1, 'Katalog berhasil dimasukkan ke keranjang');
    }

    public function kembalikanKeranjang()
    {
        return $this->setKeranjang(0, 'Katalog berhasil dikembalikan ke daftar katalog');
    }

    private function setKeranjang(int $value, string $message)
    {
        $IDs = array_values(array_filter(array_map('intval', (array) $this->request->getPost('ID'))));

        if ($IDs) {
            db_connect()->table('catalogs')->whereIn('ID', $IDs)->update(['IsKeranjang' => $value]);

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', $message);
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog terlebih dahulu');
        }

        return redirect()->back();
    }

    public function karantina()
    {
        $data['title'] = 'Karantina Katalog';
        echo view('Katalog\Views\list_karantina', $data);
    }

    // ----------------------------------------------------------------
    // EXPORT DATA TAG (setara pengkatalogan/katalog-export-data-tag INLISLite 3)
    // ----------------------------------------------------------------

    /**
     * Unduh ruas MARC (catalog_ruas) katalog sebagai tabel HTML berekstensi .xls.
     * ID[] = katalog terpilih; tanpa ID[] = semua katalog pada daftar (karantina=1 untuk karantina, keranjang=1 untuk keranjang).
     */
    public function export_data_tag()
    {
        if (!is_allowed('katalog/export_data_tag', null, false)) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->back();
        }

        $db  = db_connect();
        $ids = array_values(array_filter(array_map('intval', (array) $this->request->getGet('ID'))));
        $karantina = $this->request->getGet('karantina') ? 1 : 0;
        $worksheetId = (int) $this->request->getGet('worksheet_id');

        $catalogs = $db->table('catalogs')->select('ID, Title')->orderBy('ID');
        if ($ids) {
            $catalogs->whereIn('ID', $ids);
        } else {
            $catalogs->where('IsQUARANTINE', $karantina);
            if (!$karantina) {
                $catalogs->where('IsKeranjang', $this->request->getGet('keranjang') ? 1 : 0);
            }
            if ($worksheetId > 0) {
                $catalogs->where('Worksheet_id', $worksheetId);
            }
        }
        $catalogs = $catalogs->get()->getResultArray();

        // Ditulis ke file sementara per potongan agar ekspor semua katalog tidak menghabiskan memori
        $path = tempnam(sys_get_temp_dir(), 'tag');
        $fh   = fopen($path, 'w');
        fwrite($fh, '<html><head><meta charset="UTF-8"><style>.num{mso-number-format:General;}.text{mso-number-format:"\@";}</style></head><body>');
        fwrite($fh, '<table border="1" align="center"><tr><th>No</th><th>Judul</th><th>Katalog ID</th><th>Tag</th><th>Indicator 1</th><th>Indicator 2</th><th>Value</th></tr>');

        $no = 1;
        foreach (array_chunk($catalogs, 500) as $chunk) {
            $ruas = [];
            $rows = $db->table('catalog_ruas')
                ->select('CatalogId, Tag, Indicator1, Indicator2, Value')
                ->whereIn('CatalogId', array_column($chunk, 'ID'))
                ->orderBy('CatalogId')->orderBy('ID')
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $ruas[$row['CatalogId']][] = $row;
            }

            foreach ($chunk as $catalog) {
                if (empty($ruas[$catalog['ID']])) {
                    continue;
                }
                $html = '';
                foreach ($ruas[$catalog['ID']] as $i => $row) {
                    $html .= '<tr>'
                        . '<td class="text">' . ($i === 0 ? $no : '') . '</td>'
                        . '<td class="text">' . ($i === 0 ? esc($catalog['Title']) : '') . '</td>'
                        . '<td class="text">' . (int) $row['CatalogId'] . '</td>'
                        . '<td class="text">' . esc($row['Tag']) . '</td>'
                        . '<td class="num">' . esc($row['Indicator1']) . '</td>'
                        . '<td class="num">' . esc($row['Indicator2']) . '</td>'
                        . '<td class="text">' . esc($row['Value']) . '</td>'
                        . '</tr>';
                }
                fwrite($fh, $html . '<tr></tr>');
                $no++;
            }
        }

        fwrite($fh, '</table></body></html>');
        fclose($fh);

        return $this->response->download($path, null)
            ->setFileName('SemuaDataKatalog.xls')
            ->setContentType('application/vnd.ms-excel');
    }

    // ----------------------------------------------------------------
    // KARANTINA
    // ----------------------------------------------------------------

    public function proses_karantina()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsQUARANTINE' => 1];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil ditambahkan ke Troli Karantina');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dikarantina terlebih dahulu');
        }

        return redirect()->back();
    }

    public function pulihkan_katalog()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsQUARANTINE' => 0];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil dipulihkan ke daftar katalog');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dipulihkan terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // OPAC
    // ----------------------------------------------------------------

    public function proses_opac()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsOPAC' => 1];
            }

            $this->katalogModel->updateBatch($update_data, 'ID');
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil ditampilkan ke OPAC');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan ditampilkan ke OPAC terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id = 0)
    {
        // ← cek apakah bulk delete dari query string
        $ids = $this->request->getGet('check_data');

        // ← single delete dari URL parameter
        if ($id) {
            $ids = [$id];
        }

        if (empty($ids)) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Tidak ada katalog yang dipilih');
            return redirect()->to('katalog');
        }

        // Cek apakah ada katalog yang masih memiliki eksemplar
        $katalog_with_eksemplar = [];
        foreach ($ids as $catalog_id) {
            $catalog_id = (int) $catalog_id;
            if ($catalog_id > 0) {
                $jumlah = $this->eksemplarModel->where('Catalog_id', $catalog_id)->countAllResults();
                if ($jumlah > 0) {
                    $katalog = $this->katalogModel->find($catalog_id);
                    $katalog_with_eksemplar[] = ($katalog->BIBID ?? $catalog_id) . ' - ' . ($katalog->Title ?? '') . ' <b>(' . $jumlah . ' eksemplar)</b>';
                }
            }
        }

        if (!empty($katalog_with_eksemplar)) {
            $list = implode('<br>', $katalog_with_eksemplar);
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Tidak Dapat Dihapus');
            $this->session->setFlashdata('swal_html', 'Katalog berikut masih memiliki eksemplar, hapus eksemplarnya terlebih dahulu:<br><br>' . $list);
            return redirect()->back();
        }

        $this->db->transStart();

        foreach ($ids as $catalog_id) {
            $catalog_id = (int) $catalog_id;
            if ($catalog_id > 0) {
                $this->katalogModel->delete($catalog_id);
                $this->katalogRuasModel->where('CatalogId', $catalog_id)->delete();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === true) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Katalog berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Katalog gagal dihapus');
        }

        return redirect()->back();
    }

    public function hapus_permanen()
    {
        // 1. Ambil ID dari request (get atau post)
        $IDs = $this->request->getVar('ID');
        $ids_to_delete = [];

        if (!empty($IDs) && is_array($IDs)) {
            // 2. Loop untuk memastikan data bersih dan masuk ke array penampung
            foreach ($IDs as $ID) {
                $ids_to_delete[] = $ID;
            }

            // 3. Cek apakah ada katalog yang masih memiliki eksemplar
            $katalog_with_eksemplar = [];
            foreach ($ids_to_delete as $catalog_id) {
                $jumlah = $this->eksemplarModel->where('Catalog_id', $catalog_id)->countAllResults();
                if ($jumlah > 0) {
                    $katalog = $this->katalogModel->find($catalog_id);
                    $katalog_with_eksemplar[] = ($katalog->BIBID ?? $catalog_id) . ' - ' . ($katalog->Title ?? '') . ' <b>(' . $jumlah . ' eksemplar)</b>';
                }
            }

            if (!empty($katalog_with_eksemplar)) {
                $list = implode('<br>', $katalog_with_eksemplar);
                $this->session->setFlashdata('swal_icon', 'warning');
                $this->session->setFlashdata('swal_title', 'Tidak Dapat Dihapus');
                $this->session->setFlashdata('swal_html', 'Katalog berikut masih memiliki eksemplar, hapus eksemplarnya terlebih dahulu:<br><br>' . $list);
                return redirect()->back();
            }

            // 4. Eksekusi penghapusan
            // Hapus detailnya dulu (Ruas) agar tidak ada data yatim (orphaned data)
            $this->katalogRuasModel->whereIn('CatalogId', $ids_to_delete)->delete();

            // Hapus data utama (Katalog)
            // Memberikan array ID langsung ke fungsi delete() adalah cara batch delete di CI4
            $this->katalogModel->delete($ids_to_delete);

            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Berhasil dihapus permanen');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih katalog yang akan dihapus permanen terlebih dahulu');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // STATUS
    // ----------------------------------------------------------------

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $updated = $this->katalogModel->update($id, [$field => $value]);

        if ($updated) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Katalog berhasil disimpan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Katalog gagal disimpan');
        }

        return redirect()->to('katalog');
    }

    // ----------------------------------------------------------------
    // EDISI SERIAL
    // ----------------------------------------------------------------

    public function deleteEdisiSerial(int $id, int $catalog_id)
    {
        $data = $this->edisiSerialModel->find($id);

        if ($data) {
            $this->edisiSerialModel->delete($id);
            
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Edisi serial berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data edisi serial tidak ditemukan (ID: ' . $id . ')');
        }

        return redirect()->to(base_url('katalog/edit/' . $catalog_id . '?slug=edisi_serial'));
    }
}
