<?php

namespace OpacLogs\Controllers;

class OpacLogs extends \Base\Controllers\BaseController
{
    public function index()
    {
        $this->data['title'] = 'Log Pencarian OPAC';

        if ($this->request->getMethod() === 'POST') {
            if (!is_allowed('master-opac-logs/edit', null, false)) {
                set_message('swal_icon', 'error');
                set_message('swal_title', 'Akses Ditolak');
                set_message('swal_text', lang('App.permission.not.have'));
                return redirect()->to('/master-opac-logs');
            }

            $this->validation->setRules([
                'opac_log' => ['label' => 'Pencatatan pencarian OPAC', 'rules' => 'required|in_list[TRUE,FALSE]'],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                if ($this->request->getPost('opac_log') === 'TRUE'
                    && !\Config\Database::connect('data')->tableExists('opaclogs')) {
                    set_message('toastr_msg', 'Jalankan migrasi database sebelum mengaktifkan log pencarian OPAC.');
                    set_message('toastr_type', 'error');
                    return redirect()->to('/master-opac-logs');
                }
                set_setting_parameter('opac_log', $this->request->getPost('opac_log'));
                set_message('toastr_msg', 'Pengaturan log pencarian OPAC berhasil disimpan');
                set_message('toastr_type', 'success');
            } else {
                set_message('toastr_msg', implode('<br>', $this->validation->getErrors()));
                set_message('toastr_type', 'error');
            }
            return redirect()->to('/master-opac-logs');
        }

        $this->data['opac_log'] = get_setting_parameter('opac_log');
        $this->data['log_table_exists'] = \Config\Database::connect('data')->tableExists('opaclogs');
        return view('OpacLogs\Views\index', $this->data);
    }
}
