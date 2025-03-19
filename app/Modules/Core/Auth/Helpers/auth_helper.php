<?php
if (!function_exists('assign_group')) {
	function assign_group($user_id, $user_group = 'reseller')
    {
		helper('text');

		$groupModel = new \Myth\Auth\Authorization\GroupModel();
		$db = db_connect();
		$group = $db->table('auth_groups')->where('name', $user_group)->get()->getFirstRow();
		$groupModel->addUserToGroup($user_id, $group->id);

		if($user_group == 'reseller'){
			$user_profile = get_profile($user_id);
			$resellerModel = new \Reseller\Models\ResellerModel();
			$save_data = array(
				'user_id' => $user_id,
				'name' => 'Reseller Name',
				'code' => strtoupper(random_string('alnum', 8)), 
				'active' => 1,
			);
			$resellerModel->insert($save_data);
		}
		
		return true;
    }
}

if (!function_exists('reseller_profile')) {
	function reseller_profile($user_id)
    {
		$resellerModel = new \Reseller\Models\ResellerModel();
		$query = $userModel
			->select('id, name, code, description, thumbnail, website, active')
			->where('user_id', $user_id);

		$data = $query->first();		
		return $data;
    }
}

if (!function_exists('buyer_profile')) {
	function buyer_profile($user_id)
    {
		// $resellerModel = new \Reseller\Models\ResellerModel();
		// $query = $userModel
		// 	->select('id, name, code, description, thumbnail, website, active')
		// 	->where('user_id', $user_id);

		// $data = $query->first();		
		return null;
    }
}

if (!function_exists('get_profile')) {
	function get_profile($id)
    {
		$userModel = new \Auth\Models\UserModel();
		$query = $userModel
			->select('id, email, username, first_name, last_name, phone, avatar, cover, address, website, gender, birth_date')
			->where('id', $id);

		$data = $query->first();
		if(!empty($data)){
			$data->prefix_url = base_url('uploads/user/');
			$data->bearer_token = "Bearer ".jwt_encode($data->id, $data->email);

			// if(is_member('reseller')){
			// 	$data->role = 'reseller';
			// 	// $data->reseller_profile = reseller_profile($id);
			// }

			// if(is_member('buyer')){
			// 	$data->role = 'buyer';
			// 	// $data->buyer_profile = buyer_profile($id);
			// }
		} else {
			$data = array();
		}
		
		return $data;
    }
}

if (!function_exists('get_profile_oauth')) {
	function get_profile_oauth($email)
    {
		$userModel = new \Auth\Models\UserModel();
		$query = $userModel
			->select('id, email, username, first_name, last_name, phone, avatar, cover, address, website, gender, birth_date')
			->where('email', $email);

		$data = $query->first();
		if(!empty($data)){
			$data->prefix_url = base_url('uploads/user/');
			$data->bearer_token = "Bearer ".jwt_encode($data->id, $data->email);
		} else {
			$data = array();
		}
		
		return $data;
    }
}

if (!function_exists('generate_token')) {
	function generate_token()
    {
		//Generate a random string.
		$token = openssl_random_pseudo_bytes(12); 
		
		//Convert the binary data into hexadecimal representation.
		$token = bin2hex($token);
		
		//Print it out for example purposes.
		return $token;
    }
}

if (!function_exists('jwt_encode')) {
	function jwt_encode($id, $username)
	{
		$key = getenv('security.token.secret');
		$alg = getenv('security.token.alg');
		$payload = array(
			"iat" => getenv('security.token.iat'),
			"nbf" => getenv('security.token.nbf'),
			"uid" => $id,
			"username" => $username
		);
	
		$fbJwt = new Firebase\JWT\JWT();
		$fbKey = new Firebase\JWT\Key($key, $alg);
		$encoded = $fbJwt->encode($payload, $key, $alg);
		return $encoded;
	}
}

if (!function_exists('jwt_decode')) {
	function jwt_decode($encoded)
	{
		$key = getenv('security.token.secret');
		$alg = getenv('security.token.alg');
	
		$fbJwt = new Firebase\JWT\JWT();
		$fbKey = new Firebase\JWT\Key($key, $alg);
		$decoded = $fbJwt->decode($encoded, $fbKey);
	
		return $decoded;
	}
}

if (!function_exists('unlink_file')) {
    function unlink_file($path, $file = null)
    {
		$result = false;
		if(!empty($file)){
			if(file_exists($path.'/'.$file)){
				unlink($path.'/'.$file);
				$result = true;
			} 
		}

		return false;
    }
}

if (!function_exists('calculate_score')) {
    function calculate_score($user_profile)
    {
		$score = 25;
		if(!empty($user_profile->fullname)){
			$score = $score + 25;
		}

		if(!empty($user_profile->identity)){
			$score = $score + 25;
		}

		if(!empty($user_profile->bank)){
			$score = $score + 25;
		}

		$user_profile->score = $score;

		return $user_profile;
    }
}




