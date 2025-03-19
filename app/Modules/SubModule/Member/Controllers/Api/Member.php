<?php

namespace Member\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use Myth\Auth\Models\UserModel;

class Member extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $client;
    protected $memberModel;
    protected $validation;
    protected $session;
    protected $modulePath;
    protected $uploadPath;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();

        $this->memberModel = new \Member\Models\MemberModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->modulePath = ROOTPATH . 'public/uploads/anggota/';
        $this->uploadPath = WRITEPATH . 'uploads/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper(['app', 'url', 'text', 'reference', 'thumbnail']);
    }

    public function register()
    {
		helper('member');

        $email = $this->request->getPost('Email');
        $username = $this->request->getPost('IdentityNo');
		$password = get_parameter('password-default','inlislite=');
		$activate_hash = bin2hex(random_bytes(16));
		$form_data = $this->request->getPost();
		// dd($form_data);

		$response = member_register($email, $username, $password, $activate_hash, $form_data);
        return $this->simpleResponse($response);
    }

	public function login()
    {
		helper('auth');

		$auth = \Myth\Auth\Config\Services::authentication();
		$memberModel = new \Member\Models\MemberModel();
		$userModel = new \Auth\Models\UserModel();

		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');
		$logged_in = $auth->attempt(['username' => $username, 'password' => $password], true);
		if($logged_in){
			$anggota = $memberModel
				->where('IdentityNo', $username)
				->first();

			$user = $userModel
				->where('username', $username)
				->first();

			$data = array(
				"error" => false,
				"message" => "Login berhasil",
				"data" => array(
					"anggota" => $anggota,
					"user" => $user,
				),
			);
		} else {
			$data = array(
				"error" => true,
				"message" => "Username atau password anda salah. <br>Silakan coba lagi."
			);
		}
        return $this->simpleResponse($data);
    }

    public function resend_email()
    {
        helper('member');
		$email = $this->request->getPost('email');
		$password = get_parameter('password-default','inlislite=');
		
		$userModel = new \Member\Models\UserModel();
		$data = $userModel
			->where('email', $email)
			->first();

		if (!empty($data)) {
			if($data->active == 1){
				$response = [
					'error' => true,
					'message' => 'Maaf, nomor anggota anda sudah aktif. <br>Silakan coba lagi',
				];
			} else {
				$hash = bin2hex(random_bytes(16));
				$userModel->update($data->id, ['activate_hash' => $hash]);
				$response = member_notify($email, $password, $hash, $data);
			}
		} else {
            $response = [
                'error' => true,
                'message' => 'Maaf, email tidak ditemukan. <br>Silakan coba lagi',
            ];
		}

        return $this->simpleResponse($response);
    }

	public function reset_email()
    {
        helper('member');
		$email = $this->request->getPost('email');
		$password = get_parameter('password-default','inlislite=');
		
		$users = model(UserModel::class);
		$user = $users->where('email', $email)->first();

		if (is_null($user))
		{
			$response = [
                'error' => true,
                'message' => 'Maaf, email tidak ditemukan. <br>Silakan coba lagi',
            ];
		} 

		// Save the reset hash 
		$user->generateResetHash();
		$users->save($user);

		$response = member_notify($email, $password, $user->reset_hash, $user, false);

        return $this->simpleResponse($response);
    }

	public function check()
    {
        helper('member');
		$email = $this->request->getPost('email');
		$username = $this->request->getPost('username');

		$response = member_check($email, $username);

        return $this->simpleResponse($response);
    }
}
