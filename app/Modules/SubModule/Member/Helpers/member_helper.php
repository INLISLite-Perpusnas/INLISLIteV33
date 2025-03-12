<?php
if (!function_exists('get_member_data')) {
	function get_member_data($member_no)
	{
		$db = db_connect('data');
		$builder = $db->table('members m')
			->select('m.*')
			->where('m.MemberNo', $member_no);
		
		return $builder->get()->getRow();
	}
}

if (!function_exists('get_object_array')) {
    function get_object_array($objects, $field)
    {       
        $result = [];
        foreach($objects as $row){
            $result[] = strtoupper($row->$field);
        }

        return $result;
    }
}

if (!function_exists('get_member_location')) {
	function get_member_location($member_id)
	{
		$db = db_connect('data');
		$builder = $db->table('memberloanauthorizelocation mae')
			->select('mae.LocationLoan_id')
			->where('mae.Member_id', $member_id);
		
		return $builder->get()->getResult();
	}
}

if (!function_exists('get_member_category')) {
	function get_member_category($member_id)
	{
		$db = db_connect('data');
		$builder = $db->table('memberloanauthorizecategory mac')
			->select('mac.CategoryLoan_id')
			->where('mac.Member_id', $member_id);
		
		return $builder->get()->getResult();
	}
}

if (!function_exists('member_register')) {
    function member_register($email, $username, $password, $activate_hash = '', $form_data)
    {
        helper('parameter');
		$memberModel = new \Member\Models\MemberModel();

		$existing = $memberModel
                ->where('Email', $email)
                ->orWhere('IdentityNo', $username)
                ->first();

		if (!empty($existing)) {
			$response = [
				'error' => true,
				'message' => 'Email atau Nomor Identitas sudah terdaftar',
			];
		} else {	
			$users = model(Myth\Auth\Models\UserModel::class);
			$data_user = [
				'username' => $username,
				'password' => $password,
				'email' => $email,
				'activate_hash' => $activate_hash,
				'active' => 0
			];
			$user = new Myth\Auth\Entities\User($data_user);
			$users->withGroup('anggota');

			try {
				$users->save($user);
				$form_data['MemberNo'] = $username;
				$form_data['RegisterDate'] = date('Y-m-d');
				$form_data['StatusAnggota_id'] = 3;

				$member_id = $memberModel->insert($form_data);
				$response = member_notify($email, $password, $activate_hash, $form_data);
			} catch (\Exception $e) {
				// rollback user here
				$response = [
					'error' => true,
					'message' => 'Error, data anggota gagal disimpan. Silakan coba lagi',
					'description' => $e->getMessage(),
				];
				exit($e->getMessage());
			}
		}

        return $response;
    }
}

if (!function_exists('member_notify')) {
    function member_notify($email, $password = null, $hash = '', $data = [], $activation = true)
    {
        //Send Email
        $login_url = getenv('view.loginUrl');
        $activate_url = getenv('view.activateUrl');
        $reset_url = getenv('view.resetUrl');
        $logo_url = base_url('uploads/logo.png');
        $site_name = get_parameter('site-name');
        $site_description = get_parameter('site-description');
		$subject = 'Pendaftaran - Keanggotan Online';
		$succeed = 'Terima kasih, email verifikasi berhasil dikirim. <br>Silakan cek email Anda';
		$failed = 'Maaf, email verifikasi gagal dikirim. Silakan coba lagi';

		$body = view('Member\Views\email\register', [
			'login_url' => $login_url,
			'action_url' => $activate_url . '/' . $hash,
			'logo_url' => $logo_url,
			'site_name' => $site_name,
			'site_description' => $site_description,
			'data' => $data,
			'username' => $data->username,
			'password' => $password,
		]);

		if($activation == false){
			$body = view('Member\Views\email\reset', [
				'login_url' => $login_url,
				'action_url' => $reset_url . '/' . $hash.'?username='.$data->username,
				'logo_url' => $logo_url,
				'site_name' => $site_name,
				'site_description' => $site_description,
				'data' => $data,
				'username' => $data->username,
				'password' => $password,
			]);

			$subject = 'Lupa Password - Keanggotan Online';
		}

        $mailer = new \App\Libraries\Mailer();
        $mailer_data = [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
        ];

        $sent = $mailer->send($mailer_data);
        if ($sent) {
            $response = [
                'error' => false,
                'message' => $succeed,
                'data' => $data,
            ];
        } else {
            $response = [
                'error' => true,
                'message' => $failed,
            ];
        }
        return $response;
    }
}

if (!function_exists('member_activate')) {
    function member_activate($activate_hash = '')
    {
        $response = false;
		$memberModel = new \Member\Models\MemberModel();
		$userModel = new \User\Models\UserModel();
        if (!empty($activate_hash)) {
			$user = $userModel
				->where('activate_hash', $activate_hash)
				->first();

			if(!empty($user)){
				$userModel
					->set('active', 1)
					->where('activate_hash', $activate_hash)
					->update();

				$response = $memberModel
					->where('Email', $user->email)
					->orWhere('IdentityNo', $user->username)
					->first();
			} 
		}

        return $response;
    }
}

if (!function_exists('member_reset')) {
    function member_reset($username, $reset_hash = '')
    {
        $response = false;
		$password = get_parameter('password-default','inlislite=');
		$users = model(Myth\Auth\Models\UserModel::class);

		if (is_null($reset_hash))
		{
			$response = [
				'error' => true,
				'message' => 'Maaf, kode token lupa pasword tidak valid. <br>Silakan coba beberapa saat lagi atau hubungi Admin!',
			];

			return (object) $response;
		} 

		$user = $users
			->where('username', $username)
			->where('reset_hash', $reset_hash)
			->first();

		if (is_null($user))
		{
			$response = [
				'error' => true,
				'message' => 'Maaf, kode token lupa pasword tidak valid. <br>Silakan coba beberapa saat lagi atau hubungi Admin!',
			];

			return (object) $response;
		} 

		// Reset token still valid?
		if (! empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp())
		{
			$response = [
				'error' => true,
				'message' => 'Maaf, kode token lupa pasword sudah expired. <br>Silakan coba beberapa saat lagi atau hubungi Admin!',
			];

			return (object) $response;
		}

		// Success! Save the new password, and cleanup the reset hash.
		$user->password 		= $password;
		$user->reset_hash 		= null;
		$user->reset_at 		= date('Y-m-d H:i:s');
		$user->reset_expires    = null;
		$user->force_pass_reset = false;
		$users->save($user);

		$response = [
			'error' => false,
			'message' => 'Password anda berhasil direset. <br>Silakan login menggunakan password baru anda!',
			'data' => $user,
		];

		return (object) $response;
    }
}

if (!function_exists('member_check')) {
    function member_check($email, $username)
    {
        helper('parameter');
		$memberModel = new \Member\Models\MemberModel();

		$existing = $memberModel
                ->where('Email', $email)
                ->orWhere('IdentityNo', $username)
                ->first();

		if (!empty($existing)) {
			$response = [
				'error' => true,
				'message' => 'Email atau Nomor Identitas sudah terdaftar',
			];
		} else {	
			$response = [
				'error' => false,
				'message' => 'Email atau Nomor Identitas belum terdaftar',
			];
		}

        return $response;
    }
}

