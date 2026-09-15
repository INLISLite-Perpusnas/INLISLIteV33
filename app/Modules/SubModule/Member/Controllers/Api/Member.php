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

    $throttler = service('throttler');
    $throttleKey = hash('sha256', 'member-register:' . $this->request->getIPAddress());
    if (!$throttler->check($throttleKey, 5, MINUTE)) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Terlalu banyak percobaan pendaftaran. Silakan tunggu beberapa saat.',
      ]);
    }

    $inputFields = [
      'IdentityNo',
      'IdentityType_id',
      'Email',
      'Fullname',
      'Phone',
      'PlaceOfBirth',
      'DateOfBirth',
      'Sex_id',
      'Address',
      'Province',
      'City',
      'Kecamatan',
      'Kelurahan',
      'AddressNow',
      'ProvinceNow',
      'CityNow',
      'KecamatanNow',
      'KelurahanNow',
      'check_agree',
    ];

    $form_data = [];
    foreach ($inputFields as $field) {
      $value = $this->request->getPost($field);
      $form_data[$field] = is_string($value) ? trim($value) : $value;
    }

    $rules = [
      'IdentityNo'       => 'required|max_length[100]',
      'IdentityType_id'  => 'required|is_natural_no_zero',
      'Email'            => 'required|valid_email|max_length[254]',
      'Fullname'         => 'required|max_length[255]',
      'Phone'            => 'required|regex_match[/^[0-9]{8,20}$/]',
      'PlaceOfBirth'     => 'required|max_length[100]',
      'DateOfBirth'      => 'required|valid_date[Y-m-d]',
      'Sex_id'           => 'required|in_list[1,2]',
      'Address'          => 'required|max_length[1000]',
      'Province'         => 'required|max_length[20]',
      'City'             => 'required|max_length[20]',
      'Kecamatan'        => 'required|max_length[20]',
      'Kelurahan'        => 'required|max_length[20]',
      'AddressNow'       => 'required|max_length[1000]',
      'ProvinceNow'      => 'required|max_length[20]',
      'CityNow'          => 'required|max_length[20]',
      'KecamatanNow'     => 'required|max_length[20]',
      'KelurahanNow'     => 'required|max_length[20]',
      'check_agree'      => 'required|in_list[1]',
    ];

    if (!$this->validation->setRules($rules)->run($form_data)) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Data pendaftaran belum lengkap atau tidak valid.',
        'errors' => $this->validation->getErrors(),
      ]);
    }

    if ($form_data['DateOfBirth'] > date('Y-m-d')) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Tanggal lahir tidak boleh melebihi tanggal hari ini.',
      ]);
    }

    $dataDb = db_connect('data');
    $identityTypeExists = $dataDb->table('master_jenis_identitas')
      ->where('id', $form_data['IdentityType_id'])
      ->countAllResults() > 0;

    if (!$identityTypeExists) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Jenis identitas yang dipilih tidak valid.',
      ]);
    }

    $regions = [
      [$form_data['Province'], 1],
      [$form_data['City'], 2],
      [$form_data['Kecamatan'], 3],
      [$form_data['Kelurahan'], 4],
      [$form_data['ProvinceNow'], 1],
      [$form_data['CityNow'], 2],
      [$form_data['KecamatanNow'], 3],
      [$form_data['KelurahanNow'], 4],
    ];

    foreach ($regions as [$code, $level]) {
      $regionExists = $dataDb->table('t_region')
        ->where('code', $code)
        ->where('level', $level)
        ->countAllResults() > 0;

      if (!$regionExists) {
        return $this->simpleResponse([
          'error' => true,
          'message' => 'Data wilayah yang dipilih tidak valid.',
        ]);
      }
    }

    $validRegionHierarchy = strpos($form_data['City'], $form_data['Province'] . '.') === 0
      && strpos($form_data['Kecamatan'], $form_data['City'] . '.') === 0
      && strpos($form_data['Kelurahan'], $form_data['Kecamatan'] . '.') === 0
      && strpos($form_data['CityNow'], $form_data['ProvinceNow'] . '.') === 0
      && strpos($form_data['KecamatanNow'], $form_data['CityNow'] . '.') === 0
      && strpos($form_data['KelurahanNow'], $form_data['KecamatanNow'] . '.') === 0;

    if (!$validRegionHierarchy) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Susunan provinsi, kota, kecamatan, dan kelurahan tidak sesuai.',
      ]);
    }

    unset($form_data['check_agree']);
    $email = $form_data['Email'];
    $username = $form_data['IdentityNo'];
    $password = get_parameter('password-default', 'inlislite=');
    $activate_hash = bin2hex(random_bytes(16));

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
    if ($logged_in) {
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
    $password = get_parameter('password-default', 'inlislite=');

    $userModel = new \Member\Models\UserModel();
    $data = $userModel
      ->where('email', $email)
      ->first();

    if (!empty($data)) {
      if ($data->active == 1) {
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
    $password = get_parameter('password-default', 'inlislite=');

    $users = model(UserModel::class);
    $user = $users->where('email', $email)->first();

    if (is_null($user)) {
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

    $throttler = service('throttler');
    $throttleKey = hash('sha256', 'member-check:' . $this->request->getIPAddress());
    if (!$throttler->check($throttleKey, 20, MINUTE)) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Terlalu banyak permintaan verifikasi. Silakan tunggu beberapa saat.',
      ]);
    }

    $email = trim((string) $this->request->getPost('email'));
    $username = trim((string) $this->request->getPost('username'));

    if (!$this->validation->setRules([
      'email' => 'required|valid_email|max_length[254]',
      'username' => 'required|max_length[100]',
    ])->run(['email' => $email, 'username' => $username])) {
      return $this->simpleResponse([
        'error' => true,
        'message' => 'Nomor Identitas dan Email wajib diisi dengan format yang valid.',
        'errors' => $this->validation->getErrors(),
      ]);
    }

    $response = member_check($email, $username);

    return $this->simpleResponse($response);
  }
}
