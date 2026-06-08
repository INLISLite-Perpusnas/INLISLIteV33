<?php

namespace EntriKeanggotaan\Controllers;

class EntriKeanggotaan extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $settingModel;

	function __construct()
	{
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();


	}

public function index()
{
    $this->data['title'] = 'Form Entri';

    $settings = $this->settingModel
        ->select('Name, Value')
        ->whereIn('Name', [
            'TipeNomorAnggota',
            'TipePenomoranAnggota',
            'IsCetakSlipPerpanjangan',
            'IsCetakSlipPelanggaran',
            'IsCetakSlipPendaftaran'
        ])
        ->asArray()
        ->findAll();

    // Ubah ke key-value
    foreach ($settings as $setting) {
        $this->data[$setting['Name']] = $setting['Value'];
    }

    echo view('EntriKeanggotaan\Views\update', $this->data);
}


	public function update_data()
{
    $this->validation->setRule('TipeNomorAnggota', 'Form Entri Nomor Anggota', 'required');

    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

        $dataToUpdate = [
            'TipeNomorAnggota' => $this->request->getPost('TipeNomorAnggota'),
        ];

        if ($this->request->getPost('TipeNomorAnggota') == 'Otomatis') {
            $dataForOtomatis = [
                'TipePenomoranAnggota' => $this->request->getPost('TipePenomoranAnggota'),
                'IsCetakSlipPerpanjangan' => $this->request->getPost('IsCetakSlipPerpanjangan'),
                'IsCetakSlipPelanggaran' => $this->request->getPost('IsCetakSlipPelanggaran'),
                'IsCetakSlipPendaftaran' => $this->request->getPost('IsCetakSlipPendaftaran'),
            ];
        } else {
            $dataForOtomatis = [
                'TipePenomoranAnggota' => NULL,
                'IsCetakSlipPerpanjangan' => NULL,
                'IsCetakSlipPelanggaran' => NULL,
                'IsCetakSlipPendaftaran' => NULL,
            ];
        }
        $dataToUpdate = array_merge($dataToUpdate, $dataForOtomatis);

        $success = true;

        foreach ($dataToUpdate as $name => $value) {
            $row = $this->settingModel->where('Name', $name)->first();
            if ($row) {
                $update = $this->settingModel->update($row->ID, ['Value' => $value]);
                if (!$update) {
                    $success = false;
                }
            } else {
                // Kalau belum ada, insert baru
                $insert = $this->settingModel->insert([
                    'Name' => $name,
                    'Value' => $value,
                ]);
                if (!$insert) {
                    $success = false;
                }
            }
        }

        if ($success) {
            set_message('toastr_msg', 'Form Entri Katalog berhasil disimpan');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Form Entri Katalog gagal disimpan');
            set_message('toastr_type', 'error');
        }

        return redirect()->to('/master-entri-keanggotaan');
    } else {
        set_message('toastr_msg', 'Validasi gagal');
        set_message('toastr_type', 'error');
        return redirect()->back()->withInput();
    }
}

}
