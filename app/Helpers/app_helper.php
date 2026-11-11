<?php
use Myth\Auth\Config\Services as MythServices;

if (!function_exists('get_username')) {
    /**
     * Get username by user_id
     *
     * @param int|null $user_id
     * @return string
     */
    function get_username($user_id = null)
    {
        if (empty($user_id)) {
            return '-'; // atau bisa return 'Unknown'
        }

        $db = \Config\Database::connect(); // CI4 way
        $builder = $db->table('users');
        $builder->select('username');
        $builder->where('id', $user_id);
        $query = $builder->get()->getRow();

        if ($query) {
            return $query->username;
        }

        return '-'; // default kalau tidak ditemukan
    }
}

if (!function_exists('get_session')) {
    function get_session($name)
    {
        $session = \Config\Services::session();
        return $session->get($name);
    }
}

if (!function_exists('update_viewers')) {
    function update_viewers($table, $id)
    {
        $db = db_connect();
        $builder = $db->table($table);
        $builder->set('viewers', 'viewers+1', false);
        $builder->where('id', $id);
        $builder->update();
    }
}

if (!function_exists('get_youtube_id')) {
    function get_youtube_id($url)
    {
        $youtube_id = '';
        $url_regex_short = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $url_regex_long =
            '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

        if (preg_match($url_regex_long, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($url_regex_short, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        return $youtube_id;
    }
}

if (!function_exists('get_group')) {
    function get_group($user_id = null)
    {
        helper('auth');
        if (empty($user_id)) {
            $user_id = user_id();
        }
        $db = db_connect();
        $group_user = $db->table('auth_groups_users')->where('user_id', $user_id)->get()->getFirstRow();
        if (!empty($group_user)) {
            $group = $db->table('auth_groups')->where('id', $group_user->group_id)->get()->getFirstRow();
            return $group;
        }

        return false;
    }
}

if (!function_exists('permalink')) {
    function permalink($slug)
    {
        $site_url = getenv('app.siteURL');

        $url = $site_url . '/' . $slug;
        $url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);

        return $url;
    }
}

if (!function_exists('slugify')) {
    function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = trim(strtolower($text));
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if (!function_exists('unslugify')) {
    function unslugify($text, string $divider = '-')
    {
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        $text = str_replace($divider, ' ', $text);

        if (empty($text)) {
            return '';
        }

        return $text;
    }
}

if (!function_exists('get_unique_id')) {
    function get_unique_id()
    {
        return rand(100000000, 999999999) . uniqid();
    }
}

if (!function_exists('get_string_after')) {
    function get_string_after($string, $start)
    {
        $string_arr = explode($start, $string);
        return $string_arr[1];
    }
}

if (!function_exists('get_string_between')) {
    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}

if (!function_exists('image_url')) {
    function image_url($path = null)
    {
        return 'https://www.perpusnas.go.id/' . $path;
    }
}

if (!function_exists('get_parameter')) {
    function get_parameter($param = null, $default = null)
    {
        $app = new \App\Libraries\App();
        return $app->getParameter($param, $default);
    }
}

if (!function_exists('setting_enabled')) {
    function setting_enabled(string $name, bool $default = false): bool
    {
        static $values = [];

        if (!array_key_exists($name, $values)) {
            $row = \Config\Database::connect('data')->table('settingparameters')
                ->select('Value')
                ->where('Name', $name)
                ->get()
                ->getRow();
            if (!$row && $name === 'is_keanggotaan_online') {
                $row = \Config\Database::connect('data')->table('settingparameters')
                    ->select('Value')
                    ->where('Name', 'Is_keanggotaan_online')
                    ->get()
                    ->getRow();
            }
            $values[$name] = $row ? (string) $row->Value : null;
        }

        return $values[$name] === null ? $default : $values[$name] === '1';
    }
}

if (!function_exists('setting_cache_ttl')) {
    /**
     * Lama cache dalam DETIK dari settingparameters (nilai disimpan dalam menit,
     * diatur di master-pengaturan-frontend). Fallback ke $defaultMinutes bila kosong/tidak valid.
     */
    function setting_cache_ttl(string $name, int $defaultMinutes): int
    {
        static $values = [];

        if (!array_key_exists($name, $values)) {
            $row = \Config\Database::connect('data')->table('settingparameters')
                ->select('Value')
                ->where('Name', $name)
                ->get()
                ->getRow();
            $values[$name] = $row ? (int) $row->Value : 0;
        }

        $minutes = $values[$name] > 0 ? $values[$name] : $defaultMinutes;
        return $minutes * 60;
    }
}

if (!function_exists('add_parameter')) {
    function add_parameter($param = null, $value = null)
    {
        $app = new \App\Libraries\App();
        return $app->addParameter($param, $value);
    }
}

if (!function_exists('set_parameter')) {
    function set_parameter($param = null, $value = null)
    {
        $app = new \App\Libraries\App();
        return $app->setParameter($param, $value);
    }
}

if (!function_exists('delete_parameter')) {
    function delete_parameter($param = null)
    {
        $app = new \App\Libraries\App();
        return $app->deleteParam($param);
    }
}

if (!function_exists('parameter_exists')) {
    function parameter_exists($param = null)
    {
        $app = new \App\Libraries\App();
        return $app->parameterExists($param);
    }
}

if (!function_exists('create_log')) {
    function create_log(
        $message,
        $controller = null,
        $operation = null,
        $ref_table = null,
        $ref_id = null
    ) {
        $app = new \App\Libraries\App();
        return $app->addLog(
            $message,
            $controller,
            $operation,
            $ref_table,
            $ref_id
        );
    }
}

if (!function_exists('get_menu')) {
    function get_menu($is_admin = false)
    {
        $menuModel = new \Menu\Models\MenuModel();
        if ($is_admin) {
            return $menuModel
                ->where('parent', '0')
                ->where('is_admin', '1')
                ->where('active', '1')
                ->orderBy('sort', 'asc')
                ->findAll();
        } else {
            return $menuModel
                ->where('parent', '0')
                ->where('is_admin', '0')
                ->where('active', '1')
                ->orderBy('sort', 'asc')
                ->findAll();
        }
    }
}

if (!function_exists('get_sub_menu')) {
    function get_sub_menu($parent_id)
    {
        $menuModel = new \Menu\Models\MenuModel();
        return $menuModel
            ->where('parent', $parent_id)
            ->where('active', '1')
            ->orderBy('sort', 'asc')
            ->findAll();
    }
}

if (!function_exists('display_menu_dropdown')) {
    function display_menu_dropdown($id, $category_slug)
    {
        $action = '';
        $action .= '<div class="pull-right">';
        $action .=
            '<a href="' .
            base_url('menu?slug=' . $category_slug . '&menu_id=' . $id) .
            '" data-toggle="tooltip" data-placement="top" title="Ubah Menu/Sub" aria-label="Ubah menu" class="ina-action"><i class="fas fa-pen" aria-hidden="true"></i></a>';
        $action .=
            '<a href="javascript:void(0)" data-href="' .
            base_url('menu/delete/' . $id . '?slug=' . $category_slug) .
            '" data-toggle="tooltip" data-placement="top" title="Hapus Menu/Sub" aria-label="Hapus menu" class="ina-action ina-action-danger remove-data"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
        $action .=
            '<a href="' .
            base_url(
                'menu?slug=' . $category_slug . '&parent_id=' . $id
            ) .
            '" data-toggle="tooltip" data-placement="top" title="Tambah Sub Menu" aria-label="Tambah sub menu" class="ina-action"><i class="fas fa-plus" aria-hidden="true"></i></a>';
        $action .= '</div>';

        return $action;
    }
}

if (!function_exists('display_menu_module')) {
    function display_menu_module($category_id, $parent, $level)
    {
        $baseModel = new \Base\Models\BaseModel();
        $query = $baseModel->query(
            "SELECT m.id, m.name as label, m.type, m.active, m.controller, m.controller as link, c.slug as category_slug, deriv.count 
            FROM `c_menus` m 
            JOIN `c_categories` c ON m.category_id = c.id 
            LEFT OUTER JOIN (
                SELECT parent, COUNT(*) AS count 
                    FROM `c_menus` GROUP BY parent
                ) deriv ON m.id = deriv.parent WHERE  m.parent= " .
                $parent .
                ' and m.category_id = ' .
                $category_id .
                "
            ORDER BY m.sort ASC"
        );
        $result = $query->getResult();

        $ret = '';

        if ($result) {
            $ret .= '<ol class="dd-list">';
            foreach ($result as $row) {
                if ($row->count > 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= display_menu_dropdown(
                        $row->id,
                        $row->category_slug
                    );

                    $ret .= '</div>';
                    $ret .= display_menu_module(
                        $category_id,
                        $row->id,
                        $level + 1
                    );
                    $ret .= '</li>';
                } elseif ($row->count == 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content  "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= display_menu_dropdown(
                        $row->id,
                        $row->category_slug
                    );

                    $ret .= '</div></li>';
                }
            }
            $ret .= '</ol>';
        }

        return $ret;
    }
}

if (!function_exists('display_menu_option')) {
    function display_menu_option(
        $category_id,
        $parent,
        $level = 0,
        $selected = null,
        $disabled = false
    ) {
        $baseModel = new \Base\Models\BaseModel();

        $query = $baseModel->query(
            "SELECT a.id, a.icon, a.name, a.controller, a.type, deriv.childs 
            FROM `c_menus` a LEFT OUTER JOIN (
                SELECT parent, COUNT(*) AS childs 
                    FROM `c_menus` GROUP BY parent
                ) deriv ON a.id = deriv.parent WHERE  a.parent= " .
                $parent .
                ' and a.active = 1 and a.category_id = ' .
                $category_id .
                " 
            ORDER BY `sort` ASC"
        );
        $menus = $query->getResult();

        $result = '';
        if ($menus) {
            foreach ($menus as $row) {
                $attribute_disabled =
                    $disabled && $row->childs > 0 ? 'disabled' : '';
                $attribute_selected = $row->id == $selected ? 'selected' : '';

                $label =
                    str_repeat('&nbsp;&nbsp;&nbsp;', $level) .
                    '&#9507; ' .
                    $row->name;
                $result .=
                    '<option value="' .
                    $row->id .
                    '" data-name="' .
                    htmlspecialchars($row->name) .
                    '" ' .
                    $attribute_selected .
                    ' ' .
                    $attribute_disabled .
                    '>' .
                    $label .
                    '</option>';
                if ($row->childs > 0) {
                    $result .= display_menu_option(
                        $category_id,
                        $row->id,
                        $level + 1,
                        $selected
                    );
                }
            }
        }
        return $result;
    }
}

if (!function_exists('display_action_menu_reference')) {
    function display_action_menu_reference($row)
    {
        $action = '';
        $action .= '<div class="pull-right">';
        $action .=
            '<button data-toggle="tooltip" data-placement="top" title="slug: ' .
            $row->slug .
            '" class="btn btn-xs btn-info"><i class="pe-7s-info font-weight-bold"></i></button> ';
        $action .=
            '<a href="' .
            base_url('reference?slug=' . $row->slug) .
            '" data-toggle="tooltip" data-placement="top" title="Lihat Referensi" class="btn btn-xs btn-primary"><i class="lnr-list font-weight-bold"></i></a>';
        $action .= '</div>';
        return $action;
    }
}

if (!function_exists('display_menu_reference')) {
    function display_menu_reference($category_id, $parent, $level)
    {
        $baseModel = new \Base\Models\BaseModel();
        $query = $baseModel->query(
            "SELECT a.id, a.name as label, a.type, a.active, a.controller, a.controller as link, a.slug, deriv.count 
            FROM `c_menus` a LEFT OUTER JOIN (
                SELECT parent, COUNT(*) AS count 
                    FROM `c_menus` GROUP BY parent
                ) deriv ON a.id = deriv.parent WHERE  a.active = 1 and a.parent= " .
                $parent .
                ' and a.category_id = ' .
                $category_id .
                "
            ORDER BY `sort` ASC"
        );
        $result = $query->getResult();

        $ret = '';

        if ($result) {
            $ret .= '<ol class="dd-list">';
            foreach ($result as $row) {
                if ($row->count > 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= '</div>';
                    $ret .= display_menu_reference(
                        $category_id,
                        $row->id,
                        $level + 1
                    );
                    $ret .= '</li>';
                } elseif ($row->count == 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content  "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= display_action_menu_reference($row);

                    $ret .= '</div></li>';
                }
            }
            $ret .= '</ol>';
        }

        return $ret;
    }
}

/**
 * --------------Limit Helper-------------
 */

if (!function_exists('json_encode_beautify')) {
    function json_encode_beautify($data)
    {
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        return '<pre>' . $json_data . '</pre>';
    }
}

if (!function_exists('get_config')) {
    function get_config($param, $file = 'Core')
    {
        $config = config($file);
        return $config->{$param};
    }
}

if (!function_exists('nvl')) {
    function nvl($var)
    {
        if (empty($var)) {
            return '';
        } else {
            return $var;
        }
    }
}

if (!function_exists('set_alert')) {
    function set_alert($message = null, $type = 'success')
    {
        $session = session();
        $response =
            '<div class="alert alert-' .
            $type .
            ' fade show" role="alert">' .
            $message .
            '</div>';
        $session->setFlashdata('message', $response);
    }
}

if (!function_exists('set_message')) {
    function set_message($name, $value)
    {
        $session = \Config\Services::session();
        $session->setFlashdata($name, $value);
    }
}

if (!function_exists('get_message')) {
    function get_message($name)
    {
        $session = \Config\Services::session();
        return $session->getFlashdata($name);
    }
}

if (!function_exists('show_message')) {
    function show_message($message = 'message')
    {
        $response = '';
        if (!empty(get_message($message))) {
            $response =
                '<div class="alert alert-danger fade show" role="alert">' .
                get_message($message) .
                '</div>';
        }
        return $response;
    }
}

if (!function_exists('get_mysql_version')) {
    function get_mysql_version()
    {
        $mysql_info = explode(' ', mysqli_get_client_info());
        $mysql_version = isset($mysql_info[1]) ? $mysql_info[1] : false;
        $mysql_version_number = explode('-', $mysql_version)[0];

        if ($mysql_version_number) {
            return $mysql_version_number;
        } elseif (isset($mysql_info[0])) {
            return (int) substr($mysql_info[0], 0, 3);
        }

        return 5;
    }
}

if (!function_exists('get_lang')) {
    function get_lang()
    {
        $session = session();
        return $session->lang;
    }
}

if (!function_exists('get_ip_address')) {
    function get_ip_address()
    {
        $request = service('request');


        return $request->getIPAddress();
    }
}

if (!function_exists('get_ip_info')) {
    function get_ip_info($ip_address)
    {
        return json_decode(
            file_get_contents("http://ip-api.com/php/{$ip_address}")
        );
    }
}

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
            $visitorModel = new \Report\Models\VisitorModel();
            $ip_address = get_ip_address();
            if ($ip_address != false) {
                $timestamp = date('Y-m-d');
                $visitor = $visitorModel
                    ->where('ip_address', $ip_address)
                    ->where('timestamp', $timestamp)
                    ->first();

                if (!empty($visitor)) {
                    $visitorModel->update($visitor->id, [
                        'hits' => $visitor->hits + 1,
                    ]);
                } else {
                    $data_save = [
                        'ip_address' => $ip_address,
                        'timestamp' => $timestamp,
                        'hits' => 1,
                    ];

                    $ip_info = get_ip_info($ip_address);
                    if (!empty($ip_info)) {
                        $data_save['ip_country'] = $ip_info->country;
                        $data_save['ip_city'] = $ip_info->city;
                        $data_save['ip_lat'] = $ip_info->lat;
                        $data_save['ip_lon'] = $ip_info->lon;
                    }
                    $visitorModel->insert($data_save);
                }
            }

            $query = $visitorModel->selectSum('hits');
            $count_visitor = $query->get()->getRow()->hits;
            return $count_visitor;
        }
    }
}

if (!function_exists('site_name')) {
    function site_name()
    {
        return get_parameter('site_name');
    }
}

// Generic
if (!function_exists('is_image')) {
    function is_image($filename = '')
    {
        $array = explode('.', $filename);
        $extension = strtolower(end($array));
        $list_image_ext = ['', 'png', 'jpg', 'jpeg', 'gif'];

        if (array_search($extension, $list_image_ext)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('clean_snake_case')) {
    function clean_snake_case($text = '')
    {
        $text = preg_replace('/_/', ' ', $text);

        return $text;
    }
}

if (!function_exists('smart_wordwrap')) {
    function smart_wordwrap($string, $width = 35, $break = '<br>')
    {
        $pattern = sprintf('/([^ ]{%d,})/', $width);
        $output = '';
        $words = preg_split(
            $pattern,
            $string,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        foreach ($words as $word) {
            if (false !== strpos($word, ' ')) {
                $output .= $word;
            } else {
                $wrapped = explode($break, wordwrap($output, $width, $break));
                $count = $width - (strlen(end($wrapped)) % $width);

                $output .= substr($word, 0, $count) . $break;

                $output .= wordwrap(
                    substr($word, $count),
                    $width,
                    $break,
                    true
                );
            }
        }

        return wordwrap($output, $width, $break);
    }
}

if (!function_exists('date_indo')) {
    function date_indo($tgl)
    {
        $ubah = gmdate($tgl, time() + 60 * 60 * 8);
        $pecah = explode('-', $ubah);
        $tanggal = $pecah[2];
        $bulan = bulan($pecah[1]);
        $tahun = $pecah[0];
        return $tanggal . ' ' . $bulan . ' ' . $tahun;
    }
}

if (!function_exists('bulan')) {
    function bulan($bln)
    {
        switch ($bln) {
            case 1:
                return 'Januari';
                break;
            case 2:
                return 'Februari';
                break;
            case 3:
                return 'Maret';
                break;
            case 4:
                return 'April';
                break;
            case 5:
                return 'Mei';
                break;
            case 6:
                return 'Juni';
                break;
            case 7:
                return 'Juli';
                break;
            case 8:
                return 'Agustus';
                break;
            case 9:
                return 'September';
                break;
            case 10:
                return 'Oktober';
                break;
            case 11:
                return 'November';
                break;
            case 12:
                return 'Desember';
                break;
        }
    }
}

if (!function_exists('shortdate_indo')) {
    function shortdate_indo($tgl)
    {
        $ubah = gmdate($tgl, time() + 60 * 60 * 8);
        $pecah = explode('-', $ubah);
        $tanggal = $pecah[2];
        $bulan = short_bulan($pecah[1]);
        $tahun = $pecah[0];
        return $tanggal . '/' . $bulan . '/' . $tahun;
    }
}

if (!function_exists('short_bulan')) {
    function short_bulan($bln)
    {
        switch ($bln) {
            case 1:
                return '01';
                break;
            case 2:
                return '02';
                break;
            case 3:
                return '03';
                break;
            case 4:
                return '04';
                break;
            case 5:
                return '05';
                break;
            case 6:
                return '06';
                break;
            case 7:
                return '07';
                break;
            case 8:
                return '08';
                break;
            case 9:
                return '09';
                break;
            case 10:
                return '10';
                break;
            case 11:
                return '11';
                break;
            case 12:
                return '12';
                break;
        }
    }
}

if (!function_exists('mediumdate_indo')) {
    function mediumdate_indo($tgl)
    {
        $ubah = gmdate($tgl, time() + 60 * 60 * 8);
        $pecah = explode('-', $ubah);
        $tanggal = $pecah[2];
        $bulan = medium_bulan($pecah[1]);
        $tahun = $pecah[0];
        return $tanggal . '-' . $bulan . '-' . $tahun;
    }
}

if (!function_exists('medium_bulan')) {
    function medium_bulan($bln)
    {
        switch ($bln) {
            case 1:
                return 'Jan';
                break;
            case 2:
                return 'Feb';
                break;
            case 3:
                return 'Mar';
                break;
            case 4:
                return 'Apr';
                break;
            case 5:
                return 'Mei';
                break;
            case 6:
                return 'Jun';
                break;
            case 7:
                return 'Jul';
                break;
            case 8:
                return 'Ags';
                break;
            case 9:
                return 'Sep';
                break;
            case 10:
                return 'Okt';
                break;
            case 11:
                return 'Nov';
                break;
            case 12:
                return 'Des';
                break;
        }
    }
}

if (!function_exists('longdate_indo')) {
    function longdate_indo($tanggal)
    {
        $ubah = gmdate($tanggal, time() + 60 * 60 * 8);
        $pecah = explode('-', $ubah);
        $tgl = $pecah[2];
        $bln = $pecah[1];
        $thn = $pecah[0];
        $bulan = bulan($pecah[1]);

        $nama = date('l', mktime(0, 0, 0, $bln, $tgl, $thn));
        $nama_hari = '';
        if ($nama == 'Sunday') {
            $nama_hari = 'Minggu';
        } elseif ($nama == 'Monday') {
            $nama_hari = 'Senin';
        } elseif ($nama == 'Tuesday') {
            $nama_hari = 'Selasa';
        } elseif ($nama == 'Wednesday') {
            $nama_hari = 'Rabu';
        } elseif ($nama == 'Thursday') {
            $nama_hari = 'Kamis';
        } elseif ($nama == 'Friday') {
            $nama_hari = 'Jumat';
        } elseif ($nama == 'Saturday') {
            $nama_hari = 'Sabtu';
        }
        return $nama_hari . ',' . $tgl . ' ' . $bulan . ' ' . $thn;
    }
}

if (!function_exists('debug')) {
    function debug($vars = null)
    {
        return get_instance()->console->debug($vars);
    }
}

if (!function_exists('now')) {
    function now()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('now_utc')) {
    function now_utc()
    {
        date_default_timezone_set('UTC');

        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('is_dir_empty')) {
    function is_dir_empty($dir)
    {
        if (!is_readable($dir)) {
            return true;
        }
        return count(scandir($dir)) == 2;
    }
}

if (!function_exists('generate_key')) {
    function generate_key($length = 40, $type = 'alnum')
    {
        // $salt = base_convert(bin2hex(random_string($type, 64)), 16, 36);
        $salt = random_string($type, 64);
        if ($salt === false) {
            $salt = hash('sha256', time() . mt_rand());
        }

        $new_key = substr($salt, 0, $length);

        return $new_key;
    }
}

if (!function_exists('is_true')) {
    function is_true($val, $return_null = false)
    {
        $boolval = is_string($val)
            ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : (bool) $val;
        return $boolval === null && !$return_null ? false : $boolval;
    }
}

if (!function_exists('check_is_image_ext')) {
    function check_is_image_ext($file_name = '')
    {
        $extension_list = [
            'jpg' => ['jpg', 'jpeg'],
            'png' => ['png'],
        ];

        $file_name_arr = explode('.', $file_name);
        if (is_array($file_name_arr)) {
            foreach ($extension_list as $ext => $list_ext) {
                if (in_array(end($file_name_arr), $list_ext)) {
                    return $file_name;
                }
            }
        }

        return get_icon_file($file_name);
    }
}

if (!function_exists('_ent')) {
    function _ent($string = null)
    {
        return htmlentities($string);
    }
}

if (!function_exists('_spec')) {
    function _spec($string = null)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    function dd($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka = null, $prefix = 'Rp. ')
    {
        $hasil = $prefix . number_format($angka, 0, ',', '.');
        return $hasil;
    }
}

if (!function_exists('unformatRupiah')) {
    function unformatRupiah($amount = null)
    {
        $result = str_replace("Rp ", "", $amount);
        $result = str_replace(".", "", $result);

        return $result;
    }
}

if (!function_exists('checkAuthorization')) {
    function checkAuthorization(string $uri, $user_id): bool
    {
        // Profiling Mode → Permission menggunakan session auth_permissions
        if (function_exists('is_profiling') && is_profiling()) {
            
            $auth_permissions = (array) session()->get('auth_permissions');
            $terpilih = [];

            // Cari exact match dulu
            if (!isset($auth_permissions[$uri])) {
                // Coba dengan tambahan "/index"
                $uri_variants = [
                    $uri . (str_ends_with($uri, '/') ? 'index' : '/index')
                ];

                foreach ($uri_variants as $variant) {
                    if (isset($auth_permissions[$variant])) {
                        $terpilih = $auth_permissions[$variant];
                        break;
                    }
                }
            } else {
                $terpilih = $auth_permissions[$uri];
            }

            // Cek apakah user punya akses
            if (in_array($user_id, $terpilih)) {
                return true;
            }

            // Fallback → URI prefix match
            foreach ($auth_permissions as $key => $auth_permission) {
                if (in_array($user_id, $auth_permission) && str_starts_with($uri, $key)) {
                    return true;
                }
            }
        }

        // Non-profiling Mode → Gunakan Myth\Auth Permission System
        $auth = MythServices::authentication();
        $authorize = MythServices::authorization();

        if ($auth->check()) {
            if (function_exists('is_member') && is_member('admin')) {
                return true;
            }
            return $authorize->hasPermission($uri, $user_id);
        }

        return false;
    }
}
if (!function_exists('sirkulasi_hari_libur')) {
    /**
     * Hari libur sirkulasi: hari pekan yang libur (N: 6=Sabtu, 7=Minggu, dari setting
     * IsSaturdayHoliday/IsSundayHoliday) dan tanggal di tabel holidays. Di-cache per request.
     */
    function sirkulasi_hari_libur(): array
    {
        static $libur = null;
        if ($libur === null) {
            $db = db_connect();
            $settings = array_column($db->table('settingparameters')
                ->select('Name, Value')
                ->whereIn('Name', ['IsSaturdayHoliday', 'IsSundayHoliday'])
                ->get()->getResultArray(), 'Value', 'Name');

            $weekend = [];
            // Default dianggap libur jika setting tidak ada
            if (($settings['IsSaturdayHoliday'] ?? 'True') !== 'False') $weekend[] = '6';
            if (($settings['IsSundayHoliday'] ?? 'True') !== 'False') $weekend[] = '7';

            $tanggal = [];
            foreach ($db->table('holidays')->select('Dates')->where('active', 1)->get()->getResult() as $row) {
                $tanggal[date('Y-m-d', strtotime($row->Dates))] = true;
            }
            $libur = ['weekend' => $weekend, 'tanggal' => $tanggal];
        }
        return $libur;
    }
}

if (!function_exists('hari_kerja_antara')) {
    /**
     * Jumlah hari kerja setelah tanggal $from sampai dengan tanggal $to (jam diabaikan),
     * melewati hari libur sirkulasi. 0 jika $to tidak setelah $from.
     */
    function hari_kerja_antara($from, $to): int
    {
        $start = new \DateTime(date('Y-m-d', strtotime((string) $from)));
        $end   = new \DateTime(date('Y-m-d', strtotime((string) $to)));
        if ($end <= $start) {
            return 0;
        }

        $libur = sirkulasi_hari_libur();
        $count = 0;
        for ($day = $start->modify('+1 day'); $day <= $end; $day->modify('+1 day')) {
            if (in_array($day->format('N'), $libur['weekend'], true) || isset($libur['tanggal'][$day->format('Y-m-d')])) {
                continue;
            }
            $count++;
        }
        return $count;
    }
}

if (!function_exists('hari_terlambat')) {
    /**
     * Hari terlambat pengembalian: hari kerja sesudah tanggal jatuh tempo sampai tanggal
     * kembali (atau hari ini jika belum kembali). Dipakai semua menu sirkulasi agar angkanya sama.
     */
    function hari_terlambat($dueDate, $returnDate = null): int
    {
        if (empty($dueDate)) {
            return 0;
        }
        return hari_kerja_antara($dueDate, $returnDate ?: date('Y-m-d'));
    }
}

if (!function_exists('status_jatuh_tempo')) {
    /**
     * Label sisa hari / hari terlambat untuk pinjaman yang belum kembali.
     * Hasil: ['text' => '-3' | '+0' | '+2', 'class' => secondary|warning|info|danger, 'late' => int].
     */
    function status_jatuh_tempo($dueDate): array
    {
        $today = date('Y-m-d');
        if (date('Y-m-d', strtotime((string) $dueDate)) > $today) {
            $sisa = hari_kerja_antara($today, $dueDate);
            if ($sisa === 0) {
                return ['text' => '+0', 'class' => 'info', 'late' => 0];
            }
            return ['text' => '-' . $sisa, 'class' => $sisa <= 3 ? 'warning' : 'secondary', 'late' => 0];
        }

        $late = hari_terlambat($dueDate, $today);
        return ['text' => '+' . $late, 'class' => $late > 0 ? 'danger' : 'info', 'late' => $late];
    }
}

if (!function_exists('sirkulasi_suspend_sampai')) {
    /**
     * Masa suspend (skorsing) anggota akibat pelanggaran, sama dengan aplikasi lama
     * (SirkulasiHelpers::validatePelanggaran): tanggal boleh pinjam = tanggal pelanggaran
     * dicatat + JumlahSuspend hari kalender, diambil yang paling akhir.
     * Pelanggaran dicocokkan lewat Member_id, atau lewat item pinjamannya bila Member_id kosong.
     *
     * @return string|null Tanggal (Y-m-d) anggota baru boleh meminjam lagi,
     *                     atau null bila tidak sedang dalam masa suspend.
     */
    function sirkulasi_suspend_sampai($db, int $memberId): ?string
    {
        if ($memberId <= 0) {
            return null;
        }

        $row = $db->query(
            'SELECT MAX(DATE_ADD(DATE(p.CreateDate), INTERVAL p.JumlahSuspend DAY)) AS BolehPinjam
               FROM pelanggaran p
          LEFT JOIN collectionloanitems cli ON cli.ID = p.CollectionLoanItem_id
              WHERE p.JumlahSuspend > 0
                AND p.CreateDate IS NOT NULL
                AND COALESCE(p.active, 1) = 1
                AND (p.Member_id = ? OR (p.Member_id IS NULL AND cli.member_id = ?))',
            [$memberId, $memberId]
        )->getRow();

        $bolehPinjam = $row->BolehPinjam ?? null;

        return ($bolehPinjam && $bolehPinjam > date('Y-m-d')) ? $bolehPinjam : null;
    }
}
