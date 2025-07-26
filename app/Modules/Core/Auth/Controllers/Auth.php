<?php

namespace Auth\Controllers;

use Myth\Auth\Controllers\AuthController as MythAuthController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends MythAuthController
{
public function login()
	{
		
		if (logged_in()) {
			return redirect()->to('/dashboard');
		}
        $db=db_connect('data');

        $logo=$db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value?:"Perpustakaan Mitra";
        $data['logo']=$logo;
        
        // Tambahkan hCaptcha site key
        $data['hcaptcha_site_key'] = getenv('HCAPTCHA_SITE_KEY');

		$data['title'] = 'Login Form | Profiling';
		echo view('Auth\Views\authlogin', $data);
	}

	/**
	 * Verify hCaptcha
	 */
	private function verifyHcaptcha($hcaptchaResponse)
	{
		$secretKey = getenv('HCAPTCHA_SECRET_KEY');
		
		if (empty($hcaptchaResponse)) {
			return false;
		}
		
		$url = 'https://hcaptcha.com/siteverify';
		$data = [
			'secret' => $secretKey,
			'response' => $hcaptchaResponse,
			'remoteip' => $this->request->getIPAddress()
		];
		
		$options = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			]
		];
		
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		
		if ($result === false) {
			return false;
		}
		
		$response = json_decode($result, true);
		
		return isset($response['success']) && $response['success'] === true;
	}

	public function attemptLogin()
	{
		try {
			$username = $this->request->getPost('login');
			$password = $this->request->getPost('password');
			$hcaptchaResponse = $this->request->getPost('h-captcha-response');
			
			// Verifikasi hCaptcha terlebih dahulu
			if (!$this->verifyHcaptcha($hcaptchaResponse)) {
				session()->set('error', "Verifikasi hCaptcha gagal. Silakan coba lagi.");
				return redirect()->back()->withInput();
			}
			
			// Gunakan service authentication yang sudah ada
			if (!service('authentication')->attempt([
				'username' => $username,
				'password' => $password
			])) {
				session()->set('error', "Maaf, username atau password anda salah.");
				return redirect()->back()->withInput();
			}
			
			// Jika autentikasi berhasil, ambil data user dari database
			$db = db_connect();
			$user = $db->table('users')->where('username', $username)->get()->getRowObject();
			
			if (is_null($user)) {
				session()->set('error', "Maaf, username atau password anda salah.");
				return redirect()->back()->withInput();
			}
			
			// Ambil permission data seperti di API
			$auth_permissions = $db->table('auth_permissions')->get()->getResultObject();
			$result_auth_permissions = [];

			$auth_user_permissions = $db->table('auth_users_permissions')->where('user_id', $user->id)->get()->getResultObject();
			$result_auth_user_permissions = [];
			foreach ($auth_user_permissions as $row) {
				$result_auth_user_permissions[$row->permission_id] = $row->user_id;
			}

			if (!empty($result_auth_user_permissions)) { 
				// Cek dulu jika ternyata spesial user punya permission khusus maka gunakan itu
				foreach ($auth_permissions as $row) {
					if (isset($result_auth_user_permissions[$row->id])) {
						$result_auth_permissions[$row->name] = [$user->id];
					}
				}
			} else { 
				// Else gunakan dari groupnya
				$auth_groups_users = $db->table('auth_groups_users')->get()->getResultObject();
				$result_auth_groups_users = [];
				foreach ($auth_groups_users as $row) {
					$result_auth_groups_users[$row->group_id][] = $row->user_id;
				}

				$auth_groups = $db->table('auth_groups')->get()->getResultObject();
				$result_auth_groups = [];
				foreach ($auth_groups as $row) {
					$result_auth_groups[$row->id] = $row;
				}

				$auth_groups_permissions = $db->table('auth_groups_permissions')->get()->getResultObject();
				$result_auth_groups_permissions = [];
				foreach ($auth_groups_permissions as $row) {
					$result_auth_groups_permissions[$row->permission_id][] = $row->group_id;
				}

				foreach ($auth_permissions as $row) {
					if (isset($result_auth_groups_permissions[$row->id])) {
						$groups_permissions = $result_auth_groups_permissions[$row->id];
						$user_id = [];
						foreach ($groups_permissions as $list_group_id) {
							if (isset($result_auth_groups_users[$list_group_id])) {
								$user_id = array_merge($user_id, $result_auth_groups_users[$list_group_id]);
							}
						}
						$result_auth_permissions[$row->name] = $user_id;
					}
				}
			}
			
			// Generate token seperti di API
			$exp = time() + getenv('security.TOKEN_EXP');
			$token = JWT::encode([
				"iat" => getenv('security.TOKEN_IAT'),
				"exp" => $exp,
				"user" => $user->id,
			], getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'));
			
			// Set session data
			session()->set('username', $username);
			session()->set('password', $password);
			session()->set('token', $token);
			session()->set('logged_in', $user->id);
			session()->set('branch_id', $user->branch_id ?? 0);
			session()->set('member_id', $user->member_id ?? 0);
			session()->set('auth_permissions', $result_auth_permissions);
			
			// Buat objek userData
			$userData = new \stdClass();
			$userData->id = $user->id;
			$userData->username = $user->username;
			$userData->first_name = $user->first_name ?? 'Admin';
			$userData->last_name = $user->last_name ?? 'Pusat';
			$userData->email = $user->email ?? 'admin@admin.com';
			$userData->phone = $user->phone ?? '08118055777';
			$userData->address = $user->address ?? 'Jl. Medan Merdeka Selatan No.11, Gambir, Jakarta Pusat';
			$userData->avatar = $user->avatar ?? '1683480754_669684b97dfb4cf28593.png';
			$userData->category = $user->category ?? 'admin';
			$userData->branch_id = $user->branch_id ?? 0;
			$userData->npp_provinsi_id = $user->npp_provinsi_id ?? 0;
			$userData->npp_kabkota_id = $user->npp_kabkota_id ?? 0;
			$userData->npp_kecamatan_id = $user->npp_kecamatan_id ?? 0;
			$userData->npp_kelurahan_id = $user->npp_kelurahan_id ?? 0;
			$userData->member_id = $user->member_id ?? 0;
			
			// Set user data ke session sebagai objek
			session()->set('user', $userData);
			
			// Set payload token
			$payload = JWT::decode($token, (new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'))));
			session()->set('payload', $payload);
			
			return redirect()->to('/dashboard');
		} catch (\Exception $e) {
			session()->set('error', $e->getMessage());
			return redirect()->back()->withInput();
		}
	}

	private function validateUserFromDatabase($username, $password)
	{
		// Menggunakan model untuk akses database
		$userModel = new \Auth\Models\UserModel(); // Sesuaikan dengan nama model Anda
		
		// Cari user berdasarkan username
		$user = $userModel->where('username', $username)->first();
		
		// Jika user ditemukan dan password cocok
		if ($user && password_verify($password, $user->password_hash)) {
			// Set session data
			session()->set('username', $username);
			session()->set('logged_in', $user->id);
			session()->set('branch_id', $user->branch_id ?? null);
			session()->set('member_id', $user->member_id ?? null);
			
			// Load permissions jika ada
			if (isset($user['role_id'])) {
				$permissionModel = new \Permission\Models\PermissionModel(); // Sesuaikan dengan model permission Anda
				$permissions = $permissionModel->getPermissionsByRoleId($user['role_id']);
				session()->set('auth_permissions', $permissions);
			}
			
			// Set user data ke session
			session()->set('user', $user);
			
			return true;
		}
		
		return false;
	}
}