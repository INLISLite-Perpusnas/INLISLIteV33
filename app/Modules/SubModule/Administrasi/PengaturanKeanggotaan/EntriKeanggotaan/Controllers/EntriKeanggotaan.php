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
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Form Entri Keanggotaan berhasil disimpan');
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_text', 'Form Entri Keanggotaan gagal disimpan');
        }

        return redirect()->to('/master-entri-keanggotaan');
    } else {
        $this->session->setFlashdata('swal_icon', 'error');
        $this->session->setFlashdata('swal_title', 'Validasi Gagal');
        $this->session->setFlashdata('swal_text', 'Harap lengkapi form dengan benar');
        return redirect()->back()->withInput();
    }
}

}
