<?php

namespace Auth\Controllers\Api;

class Auth extends \Base\Controllers\BaseResourceController
{
	protected $config;
    protected $userModel;
	protected $uploadPath;
    protected $modulePath;
	protected $session;

    public function __construct()
    {
		$this->config = config('Auth');
		$this->userModel = new \User\Models\UserModel();
		$this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->password = new \Myth\Auth\Password();
		$this->groupModel = new \Myth\Auth\Authorization\GroupModel();

        parent::__construct();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/user/';
        
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

	public function index()
    {
        return $this->simpleResponse('Auth API');
    }

	public function register($user_group = 'reseller')
    {
		try {
			$db = db_connect("default");
			$db->transBegin();

			#region
			$sign_type = 1;
			$email = $this->request->getPost('email');
			$first_name = $this->request->getPost('first_name');
			$last_name = $this->request->getPost('last_name');
			$phone = $this->request->getPost('phone');
			$password = $this->request->getPost('password');
			$username = strtoupper(random_string('alnum', 8));
			$user = $this->userModel->where('email', $email)->first();
			if(!empty($user)){
				$data = array(
					"error" => true,
					"message" => "Maaf, email anda sudah terdaftar. Silakan menggunakan email lain."
				);
			} else {
				$activate_hash = bin2hex(random_bytes(16));
				$register_data = array(
					'username' => $username,
					'email' => $email,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'phone' => $phone,
					'password_hash' => $this->password->hash($password),
					'active' => 0,
					'activate_hash' => $activate_hash,
				);
		
				$user_id = $this->userModel->insert($register_data);
				if($user_id > 0){
					//Assign Group Data
					assign_group($user_id, $user_group);
					if($user_group == 'reseller'){
						assign_group($user_id, 'buyer');
					}
	
					//Send Email
					$login_url = getenv('view.loginUrl');
					$activate_url = getenv('view.activateUrl');
					$action_url = $activate_url.'/'.$activate_hash;
					$logo_url = base_url('uploads/logo.png');
					$site_name = get_parameter('site-name');
					$site_description = get_parameter('site-description');
	
					$body = view('Auth\Views\email\activation', 
						array(
							'email'=> $email, 
							'login_url'=> $login_url,
							'action_url'=> $action_url,
							'logo_url'=> $logo_url,
							'site_name'=> $site_name,
							'site_description'=> $site_description,
						)
					);
	
					$mailer_data = array(
						'email' => $email,
						'subject' => $email,
						'body' => $body,
					);
	
					$mailer = new \App\Libraries\Mailer();
					$sent = $mailer->send($mailer_data);
					if($sent){
						$data = array(
							"error" => false,
							"message" => "Terima kasih, kode verifikasi email berhasil dikirim. Silakan cek email anda untuk verifikasi",
							"data" => array(
								"user_id" => $user_id,
								"activate_hash" => $activate_hash,
							)
						);
					} else {
						$data = array(
							"error" => true,
							"message" => "Maaf, kode verifikasi email gagal dikirim. Silakan coba lagi."
						);
					}
				} else {
					$data = array(
						"error" => true,
						"message" => "Maaf, kode verifikasi email gagal dikirim. Silakan coba lagi.."
					);
				}
			}
			#endregion

			$db->transCommit();
		} catch (\ReflectionException | \Exception $e) {
			$db->transRollback();
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}

        return $this->simpleResponse($data);
    }

	public function resend_email()
    {
		try {
			$db = db_connect("default");
			$db->transBegin();

			#region
			$email = $this->request->getPost('email');
			$user = $this->userModel->where('email', $email)->first();
			if(empty($user)){
				$data = array(
					"error" => true,
					"message" => "Maaf, email anda belum terdaftar. Silakan daftar terlebih dahulu."
				);
			} else {
				if($user->active == 1){
					$data = array(
						"error" => true,
						"message" => "Maaf, email anda sudah terverifikasi. Silakan login atau reset password jika anda lupa."
					);
				} else {
					$activate_hash = bin2hex(random_bytes(16));
					$update_data = array(
						'activate_hash' => $activate_hash,
					);
			
					$update = $this->userModel->update($user->id, $update_data);
					if($update){
						//Send Email
						$login_url = getenv('view.loginUrl');
						$activate_url = getenv('view.activateUrl');
						$action_url = $activate_url.'/'.$activate_hash;
						$logo_url = base_url('uploads/logo.png');
						$site_name = get_parameter('site-name');
						$site_description = get_parameter('site-description');
	
						$body = view('Auth\Views\email\activation', 
							array(
								'email'=> $email, 
								'login_url'=> $login_url,
								'action_url'=> $action_url,
								'logo_url'=> $logo_url,
								'site_name'=> $site_name,
								'site_description'=> $site_description,
							)
						);
	
						$mailer_data = array(
							'email' => $email,
							'subject' => $email,
							'body' => $body,
						);
	
						$mailer = new \App\Libraries\Mailer();
						$sent = $mailer->send($mailer_data);
						if($sent){
							$data = array(
								"error" => false,
								"message" => "Terima kasih, kode verifikasi email berhasil dikirim. Silakan cek email anda untuk verifikasi",
								"data" => array(
									"user_id" => $user->id,
									"activate_hash" => $activate_hash,
								)
							);
						} else {
							$data = array(
								"error" => true,
								"message" => "Maaf, kode verifikasi email gagal dikirim. Silakan coba lagi."
							);
						}
					} else {
						$data = array(
							"error" => true,
							"message" => "Maaf, kode verifikasi email gagal dikirim. Silakan coba lagi.."
						);
					}
				}
			}
			#endregion

			$db->transCommit();
		} catch (\ReflectionException | \Exception $e) {
			$db->transRollback();
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}

        return $this->simpleResponse($data);
    }

	public function activate($code)
    {
		$user = $this->userModel->where('active', 0)->where('activate_hash', $code)->first();
		if(!empty($user)){
			$update_user = $this->userModel->update($user->id, array('active' => 1, 'activate_hash' => null));
			if($update_user){
				$data = array(
					"error" => false,
					"message" => "Aktivasi akun berhasil",
				);
			} else {
				$data = array(
					"error" => true,
					"message" => "Aktivasi akun gagal. Silakan coba lagi."
				);    
			}
		} else {
			$data = array(
				"error" => true,
				"message" => "Kode Aktivasi anda tidak sesuai. Silakan coba lagi."
			);
		}

		$redirect_url = getenv('view.loginUrl');
		return redirect()->to($redirect_url);
    }

	public function verify($code)
    {
		$user = $this->userModel->where('active', 0)->where('activate_hash', $code)->first();
		if(!empty($user)){
			$update_user = $this->userModel->update($user->id, array('active' => 1, 'activate_hash' => null));
			if($update_user){
				$data = array(
					"error" => false,
					"message" => "Verifikasi email berhasil",
				);
			} else {
				$data = array(
					"error" => true,
					"message" => "Verifikasi email gagal. Silakan coba lagi."
				);    
			}
		} else {
			$data = array(
				"error" => true,
				"message" => "Kode Verifikasi anda tidak sesuai. Silakan coba lagi."
			);
		}

		return $this->simpleResponse($data);
    }

	public function login()
    {
		$email = $this->request->getPost('email');
		$password = $this->request->getPost('password');
		$logged_in = $this->auth->attempt(['email' => $email, 'password' => $password], true);

		if($logged_in){
			$user = get_profile_oauth($email);
			$data = array(
				"error" => false,
				"message" => "Login berhasil",
				"data" => $user,
			);
		} else {
			$data = array(
				"error" => true,
				"message" => "Maaf, email atau password anda salah. Silakan coba lagi."
			);
		}
        return $this->simpleResponse($data);
    }

	public function fast_login($id)
    {
		$user = $this->userModel->select('id, email, username, first_name, last_name, phone, avatar, address')->find($id);
		$user->bearer_token = "Bearer ".jwt_encode($user->id, $user->email);
		$data = array(
			"error" => false,
			"message" => "Login berhasil",
			"data" => $user,
		);
        return $this->simpleResponse($data);
    }

	public function profile()
    {
		try{
			$header = $this->request->getHeader('decoded');
			if(!empty($header)){
				$decoded = $header->getValue();
				$user_profile = get_profile($decoded->uid);
				$data = array(
					"error" => false,
					"message" => "Get profile success",
					"data" => $user_profile,
				);
			}
		} catch (\ReflectionException | \Exception $e) {
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}
		return $this->simpleResponse($data);
    }

	public function update_profile()
    {
		try{
			$header = $this->request->getHeader('decoded');
			if(!empty($header)){
				$decoded = $header->getValue();
				$data_update = array(
					'first_name' => $this->request->getPost('first_name')??'',
					'last_name' => $this->request->getPost('last_name')??'',
					'phone' => $this->request->getPost('phone')??'',
					'birth_date' => $this->request->getPost('birth_date'),
					'gender' => $this->request->getPost('gender')??0,
					'address' => $this->request->getPost('address')??'',
				);
		
				$update = $this->userModel->update($decoded->uid, $data_update);
				if($update){
					$user_profile = get_profile($decoded->uid);
					$data = array(
						"error" => false,
						"message" => "Update profile success",
						"data" => $user_profile,
					);
				} else {
					$data = array(
						"error" => true,
						"message" => "Maaf, profil anda gagal diperbarui. Silakan coba lagi."
					);    
				}
			}
		} catch (\ReflectionException | \Exception $e) {
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}
		return $this->simpleResponse($data);
    }

	public function change_password()
    {
		try{
			$header = $this->request->getHeader('decoded');
			if(!empty($header)){
				$decoded = $header->getValue();
				$user = $this->userModel->select('password_hash')->find($decoded->uid);
				$new_password = $this->request->getPost('new_password');
				$confirm_password = $this->request->getPost('confirm_password');
				$verified_pass = true; //$this->password->verify($old_password, $user->password_hash);
				if(!$verified_pass){
					$data = array(
						"error" =>  true,
						"message" => "Maaf, password lama anda salah. Silakan coba lagi."
					);
				} else {
					if($new_password != $confirm_password){		
						$data = array(
							"error" =>  true,
							"message" => "Maaf, password baru anda tidak sama dengan konfirmasi password baru. Silakan coba lagi."
						);
					} else {
						$change_password = $this->userModel->update($decoded->uid, array('password_hash' => $this->password->hash($new_password)));
						if($change_password){
							$data = array(
								"error" => false,
								"message" => "Change password success",
							);
						} else {
							$data = array(
								"error" =>  true,
								"message" => "Maaf, Change password gagal. Silakan coba lagi."
							);
						}
					}
				}
			}
		} catch (\ReflectionException | \Exception $e) {
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}
        return $this->simpleResponse($data);
    }

	public function upload_avatar()
    {
		try{
			$header = $this->request->getHeader('decoded');
			if(!empty($header)){
				$decoded = $header->getValue();
				if ($this->request->getFile('file')) {
					$file = $this->request->getFile('file');
					$fileName =  "avatar_".$file->getRandomName();
					$success = $this->userModel->update($decoded->uid, array('avatar' => $fileName));
		
					if (!$success){
						$data = array(
							"error" => true,
							"message" => "Upload avatar fail",
						);
					} else{
						$file->move($this->modulePath, $fileName);
						$user = $this->userModel->find($decoded->uid);
						unlink_file($this->modulePath, $user->avatar);
					}
		
					$user_profile = get_profile($decoded->uid);
					$data = array(
						"error" => false,
						"message" => "Upload avatar success",
						"data" => $user_profile,
					);
				} else {
					$data = array(
						"error" => true,
						"message" => "File avatar is required",
					);
				}
			}
		} catch (\ReflectionException | \Exception $e) {
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}
		return $this->simpleResponse($data);
    }

	public function upload_cover()
    {
		try{
			$header = $this->request->getHeader('decoded');
			if(!empty($header)){
				$decoded = $header->getValue();
				if ($this->request->getFile('file')) {
					$file = $this->request->getFile('file');
					$fileName =  "cover_".$file->getRandomName();
					$user_update = $this->userModel->update($decoded->uid, array('cover' => $fileName));

					if (!$user_update){
						$data = array(
							"error" => true,
							"message" => "Cover fail to upload",
						);
					} else{
						$file->move($this->modulePath, $fileName);
						$user = $this->userModel->find($decoded->uid);
						unlink_file($this->modulePath, $user->cover);
					}

					$user_profile = get_profile($decoded->uid);
					$data = array(
						"error" => false,
						"message" => "Upload cover success",
						"data" => $user_profile,
					);
				} else {
					$data = array(
						"error" => true,
						"message" => "File cover is required",
					);
				}
			}
		} catch (\ReflectionException | \Exception $e) {
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}

		

        return $this->simpleResponse($data);
    }

	public function forget_password()
    {
		$email = $this->request->getPost('email');

		$user = $this->userModel->where('email',$email)->where('active',1)->first();
		if(!empty($user)){
			$reset_hash = bin2hex(random_bytes(16));
			$reset_expires = date('Y-m-d H:i:s', time() + 3600);

			$data_update = array(
				'force_pass_reset' => 0,
				'reset_hash' => $reset_hash,
				'reset_at' => date('Y-m-d H:i:s'),
				'reset_expires' => $reset_expires,
			);

			$update = $this->userModel->update($user->id, $data_update);

			if($update){
				//Send Email
				$login_url = getenv('view.loginUrl');
				$reset_url = getenv('view.resetUrl');
				$action_url = $reset_url.'/'.$reset_hash;
				$logo_url = base_url('uploads/logo.png');
				$site_name = get_parameter('site-name');
				$site_description = get_parameter('site-description');

				$body = view('Auth\Views\email\forgot', 
					array(
						'email'=> $email, 
						'login_url'=> $login_url,
						'action_url'=> $action_url,
						'logo_url'=> $logo_url,
						'site_name'=> $site_name,
						'site_description'=> $site_description,
					)
				);

				$mailer_data = array(
					'email' => $email,
					'subject' => $email,
					'body' => $body,
				);

				$mailer = new \App\Libraries\Mailer();
				$sent = $mailer->send($mailer_data);
				if($sent){
					$data = array(
						"error" => false,
						"message" => "Terima kasih, kode reset sudah dikirim ke email anda. Silakan cek email anda",
						"data" => array(
							"user_id" => $user->id,
							"reset_hash" => $reset_hash,
						)
					);
				} else {
					$data = array(
						"error" => true,
						"message" => "Maaf, kode reset tidak dapat dikirim ke email anda. Silakan coba lagi."
					);
				}
			} else {
				$data = array(
					"error" => true,
					"message" => "Maaf, kode reset tidak dapat dikirim ke email anda. Silakan coba lagi."
				);
			}
		} else {
			$data = array(
				"error" => true,
				"message" => "Maaf, email anda belum terdaftar atau belum dikonfirmasi."
			);
		}

        return $this->simpleResponse($data);
    }

	public function reset_password($reset_hash)
    {
		$user = $this->userModel->where('active', 1)->where('reset_hash', $reset_hash)->first();
		if(!empty($user)){
			$new_password = $this->request->getPost('new_password');
			$data_update = array(
				'force_pass_reset' => 0,
				'reset_hash' => null,
				'reset_at' => null,
				'reset_expires' => null,
				'password_hash' => $this->password->hash($new_password),
			);

			$reset_password = $this->userModel->update($user->id, $data_update);
			if($reset_password){
				$data = array(
					"error" => false,
					"message" => "Reset password success",
				);
			} else {
				$data = array(
					"error" =>  true,
					"message" => "Maaf, Reset password gagal. Silakan coba lagi."
				);
			}
		} else {
			$data = array(
				"error" =>  true,
				"message" => "Maaf, Kode Reset password tidak valid. Silakan coba lagi."
			);
		}
		return $this->simpleResponse($data);
    }

	public function login_oauth($user_group = 'reseller')
    {
		try {
			$db = db_connect("default");
			$db->transBegin();

			#region login oauth
			$sign_type = $this->request->getPost('sign_type')??2; //2:Google, 3:Facebook
			$email = $this->request->getPost('email');
			$first_name = $this->request->getPost('first_name');
			$last_name = $this->request->getPost('last_name');
			$password = 'password';
			$username = strtoupper(random_string('alnum', 8));
			$user = $this->userModel->where('email', $email)->where('sign_type', $sign_type)->first();
			if(!empty($user)){
				$user_profile = get_profile_oauth($email);
				$data = array(
					"error" => false,
					"message" => "Login oauth berhasil",
					"data" => $user_profile,
				);
			} else {
				#region register
				$register_data = array(
					'email' => $email,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'password_hash' => $this->password->hash($password),
					'active' => 1,
					'username' => $username,
					'sign_type' => $sign_type,
				);
		
				$user_id = $this->userModel->insert($register_data);
				if($user_id > 0){
					//Add to Group
					$this->db = db_connect();
					$group = $this->db->table('auth_groups')->where('name', $user_group)->get()->getFirstRow();
					$this->groupModel->addUserToGroup($user_id, $group->id);

					$user_profile = get_profile_oauth($email);
					$data = array(
						"error" => false,
						"message" => "Register oauth berhasil",
						"data" => $user_profile,
					);
				} else {
					$data = array(
						"error" => true,
						"message" => "Maaf, Register oauth gagal. Silakan coba lagi."
					);
				}
				#endregion
			}
			#endregion

			$db->transCommit();
		} catch (\ReflectionException | \Exception $e) {
			$db->transRollback();
			$data = array(
				"error" => true,
				"message" => "Error: ".$e->getMessage(),
			);   
		}

        return $this->simpleResponse($data);
    }
}
