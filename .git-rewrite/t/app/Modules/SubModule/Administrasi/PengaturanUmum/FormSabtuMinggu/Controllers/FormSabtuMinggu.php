<?php

namespace FormSabtuMinggu\Controllers;

class FormSabtuMinggu extends \Base\Controllers\BaseController
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
            'IsSaturdayHoliday',
            'IsSundayHoliday'
        ])
        ->asArray()
        ->findAll();

    // Ubah ke key-value
    foreach ($settings as $setting) {
        $this->data[$setting['Name']] = $setting['Value'];
    }



		echo view('FormSabtuMinggu\Views\update', $this->data);
	}

		public function update_data()
{
    $this->validation->setRule('IsSaturdayHoliday', 'Form Entri Hari Sabtu', 'required');
	$this->validation->setRule('IsSundayHoliday', 'Form Entri Hari Minggu', 'required');

    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

        $dataToUpdate = [
          'IsSaturdayHoliday' => $this->request->getPost('IsSaturdayHoliday'),
		  'IsSundayHoliday' => $this->request->getPost('IsSundayHoliday'),
        ];

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
            set_message('toastr_msg', 'Form Entri Sabtu Minggu berhasil disimpan');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Form Entri Sabtu Minggu gagal disimpan');
            set_message('toastr_type', 'error');
        }

        return redirect()->to('/master-form-sabtuminggu');
    } else {
        set_message('toastr_msg', 'Validasi gagal');
        set_message('toastr_type', 'error');
        return redirect()->back()->withInput();
    }
}
}
