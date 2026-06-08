<?php

namespace EntriKeanggotaan\Controllers;

class EntriKeanggotaan extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;

	function __construct()
	{
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();


	}

	public function index()
	{
		$this->data['title'] = 'Form Entri';
		$this->validation->setRule('TipeNomorAnggota', 'Form Entri Nomor Anggota', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
		

			$TipeNomorAnggota= $this->request->getPost('TipeNomorAnggota');
			// dd($TipeNomorAnggota);
			set_setting_parameter('TipeNomorAngota', $TipeNomorAnggota, is_profiling());
             $TipePenomoranAnggota= $this->request->getPost('TipePenomoranAngota');
			set_setting_parameter('TipePenomoranAngota', $TipePenomoranAnggota, is_profiling());
            
            $IsCetakSlipPerpanjangan= $this->request->getPost('IsCetakSlipPerpanjangan')? 1 : 0;
			set_setting_parameter('IsCetakSlipPerpanjangan', $IsCetakSlipPerpanjangan, is_profiling());

			$IsCetakSlipPelanggaran= $this->request->getPost('IsCetakSlipPelanggaran')? 1 : 0;
			set_setting_parameter('IsCetakSlipPelanggaran', $IsCetakSlipPelanggaran, is_profiling());

			$IsCetekSlipPendaftaran= $this->request->getPost('IsCetakSlipPendaftaran')? 1 : 0;
			set_setting_parameter('IsCetakSlipPendaftaran', $IsCetekSlipPendaftaran, is_profiling());
            


			
		

			if ($TipeNomorAnggota) {
				set_message('toastr_msg', 'Form Entri Katalog berhasil disimpan');
				set_message('toastr_type', 'success');
			} else {
				set_message('toastr_msg', 'Form Entri Katalog gagal disimpan');
				set_message('toastr_type', 'error');
			}
			return redirect()->to('/master-entri-keanggotaan');
		} 

		echo view('EntriKeanggotaan\Views\update', $this->data);
	}
}
