<?php

namespace OpacBacaDitempat\Controllers;

class OpacBacaDitempat extends \Base\Controllers\BaseController
{
    public function index()
    {
        $this->data['title'] = 'Koleksi Sering Baca di Tempat';

        if ($this->request->getMethod() === 'POST') {
            if (!is_allowed('master-opac-baca-ditempat/edit', null, false)) {
                set_message('swal_icon', 'error');
                set_message('swal_title', 'Akses Ditolak');
                set_message('swal_text', lang('App.permission.not.have'));
                return redirect()->to('/master-opac-baca-ditempat');
            }

            $this->validation->setRules([
                'show_sering_dibaca' => ['label' => 'Tampilkan Koleksi Sering Baca di Tempat', 'rules' => 'required|in_list[1,0]'],
                'sering_dibaca_show' => ['label' => 'Maksimal Jumlah Koleksi', 'rules' => 'required|is_natural_no_zero'],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $show = $this->request->getPost('show_sering_dibaca');
                set_setting_parameter('show_sering_dibaca', $show);
                set_setting_parameter('is_sering_dibaca', $show);
                set_setting_parameter('sering_dibaca_show', $this->request->getPost('sering_dibaca_show'));
                cache()->delete('home_index_data');
                set_message('toastr_msg', 'Pengaturan Koleksi Sering Baca di Tempat berhasil disimpan');
                set_message('toastr_type', 'success');
            } else {
                set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
                set_message('toastr_type', 'error');
            }
            return redirect()->to('/master-opac-baca-ditempat');
        }

        $show = get_setting_parameter('show_sering_dibaca');
        $limit = (int) get_setting_parameter('sering_dibaca_show');
        $currentShow = $show === null ? get_setting_parameter('is_sering_dibaca') : $show;
        $this->data['setting'] = [
            'show_sering_dibaca' => in_array(strtoupper(trim((string) $currentShow)), ['1', 'TRUE'], true) ? '1' : '0',
            'sering_dibaca_show' => $limit > 0 ? $limit : 6,
        ];
        $this->data['books'] = $this->getMostReadBooks();

        return view('OpacBacaDitempat\Views\index', $this->data);
    }

    private function getMostReadBooks(): array
    {
        try {
            $db = \Config\Database::connect('data');
            if (!$db->tableExists('bacaditempat')) {
                return [];
            }

            return $db->table('bacaditempat bt')
                ->select('cat.Title, cat.Author, COUNT(bt.ID) as ReadCount')
                ->join('collections col', 'col.ID = bt.collection_id')
                ->join('catalogs cat', 'cat.ID = col.Catalog_id')
                ->where('cat.IsOPAC', 1)
                ->where('bt.collection_id IS NOT NULL')
                ->groupBy('cat.ID, cat.Title, cat.Author')
                ->orderBy('ReadCount', 'DESC')
                ->limit(10)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }
}
