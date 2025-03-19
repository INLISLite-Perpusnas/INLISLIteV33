<?php
if (!function_exists('get_imploded_array')) {
    function get_imploded_array($post, $param)
    {
        $fixdata = (is_array($post) ?  implode($param, $post) : $post);

        return $fixdata;
    }
}

if (!function_exists('get_due_date')) {
    function get_due_date($days = 0, $from_date = null)
    {        
		if(empty($from_date)){
			$from_date = date('Y-m-d');
		}

		$new_date = date('Y-m-d', strtotime($from_date. ' + '.$days.' days'));
		return $new_date;
    }
}

if (!function_exists('get_late_days')) {
    function get_late_days($loan_date, $return_date = null)
    {        
		$late_days = 0;
		if(empty($return_date)){
			$return_date = date('Y-m-d');
		}

		if(!empty($loan_date) and $return_date > $loan_date){
			$from = strtotime($loan_date);
			$to = strtotime($return_date);
			$datediff = $to - $from;
	
			$late_days = round($datediff / (60 * 60 * 24));
		}

		return $late_days;
    }
}

/**
 * ---------------
 * Auth Helper
 * ---------------
 */

if (!function_exists('getClientIpAddress')) {
    function getClientIpAddress()
    {
        
        return ('127.0.0.1');
    }
}
if (!function_exists('get_users')) {
    function get_users($group_id = null)
    {        
        if(!empty($group_id)){
            $users_groups = db_get_all("auth_groups_users","group_id = {$group_id}","user_id");
            $user_id_arr = array();
            foreach($users_groups as $row){
                array_push($user_id_arr, $row->user_id);
            }
            array_unique($user_id_arr);

            $user_id_str = implode(",", $user_id_arr);
            $users = db_get_all("users", "id in ({$user_id_str})");
            return $users;
        } else {
            $users = db_get_all("users",null,"id");
            return $users;
        }
    }
}

if (!function_exists('get_user_group_id')) {
    function get_user_group_id($user_id = null)
    {
        if(empty($user_id)){
            $user_id = user_id();
        }
        
        $group = db_get_single("auth_groups_users","user_id = {$user_id}");
        return $group->group_id;
    }
}

if (!function_exists('get_user_group_ids')) {
    function get_user_group_ids($user_id = null)
    {
        if(empty($user_id)){
            $user_id = user_id();
        }
        
        $groups = db_get_all("auth_groups_users","user_id = {$user_id}");

        return $groups;
    }
}

if (!function_exists('get_group_id')) {
    function get_group_id($group)
    {
        if (is_numeric($group))
        {
            return (int)$group;
        }

        $data = db_get_single("auth_groups","name = '{$group}'");
        return $data->id;
    }
}

if (!function_exists('get_group')) {
    function get_group($group_id = null)
    {
        if(empty($group_id)){
            $group_id = get_user_group_id();
        }

        $group = db_get_single("auth_groups","id = {$group_id}");
        return $group;
    }
}

if (!function_exists('get_user_id')) {
    function get_user_id()
    {
        $user_id = user_id();
        return $user_id;
    }
}

if (!function_exists('get_user')) {
    function get_user($user_id = null)
    {
        if(empty($user_id)){
            $user = user();
        } else {
            $user = db_get_single('users', 'id=' . $user_id);
        }

        return $user;
    }
}

if (!function_exists('logged_in')) {
    function logged_in()
    {
        $auth = \Myth\Auth\Config\Services::authentication();
        return $auth->check();
    }
}
/**
 * ---------------
 * DB Helper
 * ---------------
 */

function db_get_data(array $data)
{
    $db = \Config\Database::connect();
    $res = $db->table($data['table']);
    if (isset($data['distinct'])) {
        $res->distinct()
            ->select($data['distinct']['select']);
    }
    if (isset($data['select'])) {
        $res->select($data['select']);
    }
    if (isset($data['limit'])) {
        $res->limit($data['limit']['count'], $data['limit']['from']);
    }
    if (isset($data['like'])) {
        $res->groupStart();
        $res->like($data['like']);
        $res->groupEnd();
    }
    if (isset($data['orlike'])) {
        $res->groupStart();
        $res->orLike($data['orlike']);
        $res->groupEnd();
    }
    if (isset($data['where']) and count($data['where']) > 0) {
        $res->groupStart();
        if (count($data['where']) > 0) {
            foreach ($data['where'] as $where) {
                if (isset($where['value']))
                    $res->where($where['field'], $where['value']);
                else
                    $res->where($where['field'], null, false);
            }
        }
        if (isset($data['orwhere']) and count($data['orwhere']) > 0) {
            if (count($data['orwhere']) > 0) {
                foreach ($data['orwhere'] as $orwhere) {
                    if (isset($orwhere['value']))
                        $res->orWhere($orwhere['field'], $orwhere['value']);
                    else
                        $res->orWhere($orwhere['field'], null, false);
                }
            }
        }
        $res->groupEnd();
    }
    if (isset($data['orderBy'])) {
        if (isset($data['orderBy']['field']) and isset($data['orderBy']['sort']))
            $res->orderBy($data['orderBy']['field'], $data['orderBy']['sort']);
        else
            $res->orderBy($data['orderBy']['random']);
    }
    if (isset($data['join'])) {
        foreach ($data['join'] as $join) {
            $res->join($join['table'], $join['table'] . '.' . $join['child'] . '=' . $join['parent'], isset($join['type']) ? $join['type'] : '');
        }
    }
    if (isset($data['group'])) {
        $res->groupBy($data['group']);
    }
    return $res->get();
}

function db_insert_data(array $data, $table)
{
    $db = \Config\Database::connect();
    $isMultiarray = is_multi_array($data);
    if ($isMultiarray)
        $db->table($table)->insertBatch($data);
    else
        $db->table($table)->insert($data);
}

function is_multi_array($arr)
{
    rsort($arr);
    return isset($arr[0]) && is_array($arr[0]);
}

if (!function_exists('get_multi_array')) {
    function get_multi_array($post, $param)
    {
        foreach ($post as $value) {
            $fix[] = reset($value);
        }

        $combine = implode($fix, $param);

        return $combine;
    }
}

if (!function_exists('get_last')) {
    function get_last($ref_table, $where = null)
    {        
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($ref_table);
        $query = $baseModel->orderBy('id','desc');
        
        if(!empty($where)){
            $query->where($where);
        }

        $data = $query->limit(1)->row(); 

        return $data;
    }
}

if (!function_exists('get_first')) {
    function get_first($ref_table, $where = null)
    {        
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($ref_table);
        $query = $baseModel->orderBy('id','asc');
        
        if(!empty($where)){
            $query->where($where);
        }

        $data = $query->limit(1)->row(); 

        return $data;
    }
}

if (!function_exists('get_pad_number')) {
    function get_pad_number($counter, $prefix = 'TRX-', $zero_length = 4)
    {        
        $doc_number = strtoupper($prefix).str_pad($counter , $zero_length , "0" , STR_PAD_LEFT);

        return $doc_number;
    }
}

if (!function_exists('get_doc_number')) {
    function get_doc_number($table_name, $field_name = 'id', $prefix = 'TRX-', $zero_length = 4)
    {        
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($table_name);
        $data = $baseModel->selectMax($field_name)->row(); 

        $counter = (empty($data)) ? 1 : $data->{$field_name} + 1;
        $doc_number = strtoupper($prefix).str_pad($counter , $zero_length , "0" , STR_PAD_LEFT);

        return $doc_number;
    }
}

if (!function_exists('db_get_single')) {
    function db_get_single($table_name = null, $where = false)
    {
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($table_name);
        return $baseModel->get_single($where);
    }
}

if (!function_exists('db_get_all')) {
    function db_get_all($table_name = null, $where = null, $by = "id", $order = 'desc')
    {
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($table_name);
        return $baseModel->get_all($where, $by, $order);
    }
}

if (!function_exists('db_count_all')) {
    function db_count_all($table_name = null)
    {
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($table_name);
        return $baseModel->count_all();
    }
}

if (!function_exists('db_count')) {
    function db_count($table_name = null, $where = null)
    {
        $baseModel = new \Base\Models\BaseModel();
        $baseModel->setTable($table_name);
        return $baseModel->count($where);
    }
}

/**
 * ---------------
 * Common Helper
 * ---------------
 */
if (!function_exists('get_visitor')) {
    function get_visitor()
    {
        $site_visitor_mode = get_parameter('site-visitor-mode', 0);
        if ($site_visitor_mode == 0) {
            $visitor = get_parameter('site-visitor');
            $visitor++;
            set_parameter('site-visitor', $visitor);
            return get_parameter('site-visitor');
        } else {
            set_ip_info();
            return count_visitor();
        }
    }
}

if (!function_exists('count_visitor')) {
    function count_visitor()
    {
        $visitorModel = new \App\Models\VisitorModel();
        $visitors = $visitorModel->findAll();
        $sum = 0;
        foreach ($visitors as $row) {
            $sum += $row->hits;
        }
        return $sum;
    }
}

if (!function_exists('rest_url')) {
    function rest_url($uri = '')
    {        
		$base_url = base_url($uri);
		$rest_url = getenv('app.restURL');

		if(!empty($rest_url)){
			$base_url = $rest_url. '/'. $uri;
		}
		
        return $base_url;
    }
}

if (!function_exists('get_flash')) {
    function get_flash($name)
    {
        $session = \Config\Services::session(); 
        return $session->getFlashdata($name);
    }
}

if (!function_exists('show_flash')) {
    function show_flash($message = 'message', $type="danger")
    {
        $response = '';
        if (!empty(get_flash($message))) {
            $response = '
                <div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>' . get_flash($message) . '</strong>
                </div>';
        }
        return $response;
    }
}

if (!function_exists('pd')) {
    function pd($object)
    {
        print_r($object);
        die();
        return true;
    }
}

if (!function_exists('jd')) {
    function jd($object)
    {
        echo json_encode($object);
        die();
        return true;
    }
}

/**
 * ---------------
 * Global Helper
 * ---------------
 */
if (!function_exists('get_page')) {
    function get_page($slug = null)
    {
        $pageModel = new \Page\Models\PageModel();
        $page = $pageModel->where('slug', $slug)->row();

        return $page;
    }
}

if (!function_exists('get_option')) {
    function get_option($param_name = null)
    {
        return get_parameter($param_name);
    }
}

if (!function_exists('send_email')) {
    function send_email($to = '', $subject = '', $data = [])
    {
        $mail = new PHPMailer(true);
        $status = false;
		$messages = '';
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.googlemail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'balisaauction.dev@gmail.com'; // silahkan ganti dengan alamat email Anda
            $mail->Password   = 'password2021!!'; // silahkan ganti dengan password email Anda
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('balisaauction.dev@gmail.com', 'P4TO'); // silahkan ganti dengan alamat email Anda
            $mail->addAddress($to);
            $mail->addReplyTo('balisaauction.dev@gmail.com', 'P4TO'); // silahkan ganti dengan alamat email Anda
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = view('auth/notifEmail', $data);
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $status = $mail->send();
			$message = 'Email Sent';
        } catch (Exception $e) {
            $messages =  "Send Email failed. Error: " . $mail->ErrorInfo;
            $status = false;
        }

		$response = array(
			'status'=> $status,
			'message' => $message,
		);

		return $response;
    }
}
