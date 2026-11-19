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
        $db=db_connect();

        $logo=$db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value?:"Perpustakaan Mitra";
        $data['logo']=$logo;
        
        // Tambahkan hCaptcha site key
        $data['hcaptcha_site_key'] = getenv('HCAPTCHA_SITE_KEY');
        $data['is_auth_login'] = true;

		$data['title'] = 'Login INLISLite';
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

		// Gunakan cURL (bukan file_get_contents) karena verifikasi SSL-nya
		// mengikuti setting curl.cainfo di php.ini, yang di environment ini
		// sudah dikonfigurasi dengan benar (berbeda dari openssl.cafile yang
		// dipakai stream wrapper file_get_contents dan sering belum diset).
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query($data),
			CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_TIMEOUT        => 15,
		]);

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);

		if ($result === false) {
			log_message('error', 'hCaptcha verify request failed: ' . $curlError);
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
				return redirect()->back()->withInput()->with('error', 'Verifikasi hCaptcha gagal. Silakan coba lagi.');
			}

			// Gunakan service authentication yang sudah ada
			if (!service('authentication')->attempt([
				'username' => $username,
				'password' => $password
			])) {
				return redirect()->back()->withInput()->with('error', 'Maaf, username atau password anda salah.');
			}

			// Jika autentikasi berhasil, ambil data user dari database
			$db = db_connect();
			$user = $db->table('users')->where('username', $username)->get()->getRowObject();

			if (is_null($user)) {
				return redirect()->back()->withInput()->with('error', 'Maaf, username atau password anda salah.');
			}

			// Bersihkan error lama yang mungkin masih tersimpan di session
			session()->remove('error');
			
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
			$userData->location_ids = $user->location_ids ?? '';

			// Set user data ke session sebagai objek
			session()->set('user', $userData);
			
			// Set payload token
			$payload = JWT::decode($token, (new Key(getenv('security.TOKEN_SECRET'), getenv('security.TOKEN_ALG'))));
			session()->set('payload', $payload);
			
			return redirect()->to('/dashboard');
		} catch (\Exception $e) {
			return redirect()->back()->withInput()->with('error', $e->getMessage());
		}
	}

	/** Lama link reset kata sandi berlaku (menit). */
	private const RESET_TTL_MINUTES = 60;

	/** Jeda minimal sebelum link reset untuk akun yang sama boleh dikirim ulang (detik). */
	private const RESET_RESEND_SECONDS = 120;

	/**
	 * Halaman lupa kata sandi: pengguna mengisi username atau email akunnya.
	 */
	public function forgotPassword()
	{
		if (logged_in()) {
			return redirect()->to('/dashboard');
		}

		return view('Auth\Views\forgot', ['logo' => $this->loginLogo()]);
	}

	/**
	 * Membuat token reset lalu mengirim link reset kata sandi ke email akun.
	 */
	public function attemptForgot()
	{
		if (logged_in()) {
			return redirect()->to('/dashboard');
		}

		// Batasi 5 permintaan per menit per IP untuk mencegah spam email
		if (!service('throttler')->check('forgot-' . md5($this->request->getIPAddress()), 5, MINUTE)) {
			return redirect()->back()->withInput()->with('error', 'Terlalu banyak permintaan. Silakan coba lagi dalam 1 menit.');
		}

		$identity = trim((string) $this->request->getPost('identity'));
		if ($identity === '' || mb_strlen($identity) > 255) {
			return redirect()->back()->withInput()->with('error', 'Isi username atau email akun Anda.');
		}

		// Pesan sama untuk akun ada/tidak ada supaya daftar akun tidak bisa ditebak
		$genericMessage = 'Jika akun ditemukan dan memiliki email, link reset kata sandi sudah dikirim ke email tersebut. '
			. 'Link berlaku ' . self::RESET_TTL_MINUTES . ' menit. Periksa juga folder spam.';

		$db = db_connect();
		$user = $db->table('users')
			->groupStart()->where('username', $identity)->orWhere('email', $identity)->groupEnd()
			->where('deleted_at', null)
			->get(1)->getRowObject();

		if (!$user || !filter_var((string) $user->email, FILTER_VALIDATE_EMAIL)) {
			return redirect()->back()->with('message', $genericMessage);
		}

		// Link sebelumnya masih baru: jangan kirim ulang supaya inbox pengguna tidak dibanjiri
		if (!empty($user->reset_at) && !empty($user->reset_hash)
			&& strtotime($user->reset_at) > time() - self::RESET_RESEND_SECONDS) {
			return redirect()->back()->with('message', $genericMessage);
		}

		// Yang disimpan hanya hash token; token aslinya hanya ada di link email
		$token = bin2hex(random_bytes(32));
		$db->table('users')->where('id', $user->id)->update([
			'reset_hash' => hash('sha256', $token),
			'reset_at' => date('Y-m-d H:i:s'),
			'reset_expires' => date('Y-m-d H:i:s', time() + self::RESET_TTL_MINUTES * 60),
		]);

		if (!$this->sendResetEmail($user, $token)) {
			// Token dibatalkan supaya pengguna bisa langsung meminta ulang
			$db->table('users')->where('id', $user->id)->update(['reset_hash' => null, 'reset_at' => null, 'reset_expires' => null]);
			return redirect()->back()->withInput()->with('error', 'Email reset kata sandi gagal dikirim. Silakan coba lagi atau hubungi administrator.');
		}

		return redirect()->back()->with('message', $genericMessage);
	}

	/**
	 * Halaman isi kata sandi baru dari link email.
	 */
	public function resetPassword()
	{
		$token = (string) $this->request->getGet('token');
		if (!$this->userByResetToken($token)) {
			return redirect()->to(base_url('login/forgot'))
				->with('error', 'Link reset kata sandi tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
		}

		return view('Auth\Views\reset', ['logo' => $this->loginLogo(), 'token' => $token]);
	}

	/**
	 * Menyimpan kata sandi baru lalu menghapus token reset.
	 */
	public function attemptReset()
	{
		$token = (string) $this->request->getPost('token');
		$user = $this->userByResetToken($token);
		if (!$user) {
			return redirect()->to(base_url('login/forgot'))
				->with('error', 'Link reset kata sandi tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
		}

		$rules = [
			'password' => [
				'label' => 'Kata sandi baru',
				'rules' => 'required|min_length[8]|max_length[72]',
			],
			'pass_confirm' => [
				'label' => 'Konfirmasi kata sandi',
				'rules' => 'required|matches[password]',
			],
		];
		if (!$this->validate($rules)) {
			return redirect()->to(base_url('login/reset-password?token=' . $token))
				->with('errors', $this->validator->getErrors());
		}

		db_connect()->table('users')->where('id', $user->id)->update([
			'password_hash' => \Myth\Auth\Password::hash((string) $this->request->getPost('password')),
			'reset_hash' => null,
			'reset_at' => date('Y-m-d H:i:s'),
			'reset_expires' => null,
			'force_pass_reset' => 0,
		]);

		return redirect()->to(base_url('login'))->with('message', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
	}

	/**
	 * User pemilik token reset yang masih berlaku, atau null.
	 */
	private function userByResetToken(string $token): ?object
	{
		if (!preg_match('/^[0-9a-f]{64}$/D', $token)) {
			return null;
		}

		return db_connect()->table('users')
			->where('reset_hash', hash('sha256', $token))
			->where('reset_expires >', date('Y-m-d H:i:s'))
			->where('deleted_at', null)
			->get(1)->getRowObject();
	}

	/**
	 * Mengirim email berisi link reset kata sandi lewat SMTP di .env (email.*).
	 */
	private function sendResetEmail(object $user, string $token): bool
	{
		$email = \Config\Services::email();
		$email->initialize([
			'protocol' => 'smtp',
			'SMTPHost' => getenv('email.SMTPHost') ?: 'smtp.gmail.com',
			'SMTPUser' => getenv('email.SMTPUser') ?: '',
			'SMTPPass' => getenv('email.SMTPPass') ?: '',
			'SMTPPort' => (int) (getenv('email.SMTPPort') ?: 587),
			'SMTPCrypto' => getenv('email.SMTPCrypto') ?: 'tls',
			'mailType' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n",
		]);

		helper('app');
		$siteName = (string) get_parameter('site-name', 'INLISLite');
		$logo = $this->loginLogo();
		$body = view('Auth\Views\email\forgot', [
			'action_url' => esc(base_url('login/reset-password?token=' . $token), 'attr'),
			'login_url' => esc(base_url('login'), 'attr'),
			'logo_url' => esc($logo ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'), 'attr'),
			'site_name' => esc($siteName),
			'site_description' => esc((string) get_parameter('site-description', 'Sistem Manajemen Perpustakaan')),
			'username' => esc((string) $user->username),
			'expires_minutes' => self::RESET_TTL_MINUTES,
		]);

		$email->setFrom(getenv('email.fromEmail') ?: getenv('email.SMTPUser'), getenv('email.fromName') ?: $siteName);
		$email->setTo($user->email);
		$email->setSubject('Reset Kata Sandi - ' . $siteName);
		$email->setMessage($body);

		try {
			if ($email->send(false)) {
				return true;
			}
			log_message('error', 'Gagal kirim email reset kata sandi user #' . $user->id . ': ' . $email->printDebugger(['headers']));
		} catch (\Throwable $e) {
			log_message('error', 'Gagal kirim email reset kata sandi user #' . $user->id . ': ' . $e->getMessage());
		}
		return false;
	}

	/**
	 * Nama file logo perpustakaan (settingparameters.Logo), sama dengan halaman login.
	 */
	private function loginLogo(): string
	{
		$row = db_connect()->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
		return (string) ($row->Value ?? '');
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
