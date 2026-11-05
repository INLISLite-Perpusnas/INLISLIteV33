<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Permissions implements FilterInterface
{
  /** Modul yang dipakai semua pengguna login atau halaman anggota/kiosk, tidak dijaga per izin. */
  private const AJAX_OPEN_MODULES = [
    'dashboard', 'opac', 'member', 'profil_anggota', 'notification', 'region', 'home', 'news',
    'peminjaman-mandiri', 'pengembalian-mandiri', 'sirkulasi-selfloan',
  ];

  /** Rute yang namanya berbeda dari nama izinnya. */
  private const AJAX_MODULE_ALIASES = [
    'kartuanggota'      => 'anggota',
    'pengaturananggota' => 'anggota',
    'keranjanganggota'  => 'keranjang-anggota',
    'pemesanankoleksi'  => 'sirkulasi-pemesanan',
  ];

  /** Modul yang saling membaca data: pemegang izin salah satunya boleh membaca yang lain. */
  private const AJAX_MODULE_FAMILIES = [
    [
      'katalog', 'karantina-katalog', 'eksemplar', 'karantina-eksemplar', 'export-eksemplar',
      'kardeks-terbitan-berkala', 'stockopname', 'laporan-katalog', 'laporan-eksemplar',
      'keranjang-katalog', 'keranjang-eksemplar',
    ],
    [
      'sirkulasi', 'sirkulasi-peminjaman', 'sirkulasi-pengembalian', 'sirkulasi-perpanjangan',
      'sirkulasi-pelanggaran', 'sirkulasi-pemesanan', 'anggota', 'keanggotaan', 'keranjang-anggota',
    ],
  ];

  /**
   * Do whatever processing this filter needs to do.
   * By default it should not return anything during
   * normal execution. However, when an abnormal state
   * is found, it should return an instance of
   * CodeIgniter\HTTP\Response. If it does, script
   * execution will end and that Response will be
   * sent back to the client, allowing for error pages,
   * redirects, etc.
   *
   * @param RequestInterface $request
   * @param array|null       $arguments
   *
   * @return mixed
   */
  public function before(RequestInterface $request, $arguments = null)
  {
    $permission = str_replace(base_url(), '', current_url());
    if (strpos($permission, 'report/') === 0) {
      return;
    }

    // Header X-Requested-With bisa dipalsukan: pengecualian AJAX hanya berlaku bagi yang sudah login
    $is_allowed = is_allowed($permission, session()->get('logged_in'), logged_in());
    if (!$is_allowed) {
      // Tujuan penolakan adalah dashboard, jadi yang tidak punya izin dashboard diarahkan ke OPAC agar tidak berputar
      if (preg_match('#^(index\.php/)?dashboard(/|$)#', ltrim($permission, '/'))) {
        return redirect()->to('/opac');
      }
      session()->setFlashdata('swal_permission', [
        'icon'  => 'error',
        'title' => 'Akses Ditolak',
        'text'  => lang('App.permission.not.have'),
      ]);
      return redirect()->to('/dashboard/index');
    }

    if (is_ajax() && logged_in() && !$this->isAjaxAllowed($permission)) {
      log_message('warning', 'Permissions: AJAX ditolak {uri} user {user}', ['uri' => $permission, 'user' => login_id()]);
      return service('response')
        ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
        ->setJSON(['error' => true, 'message' => lang('App.permission.not.have')]);
    }
  }

  /**
   * is_allowed() meloloskan semua AJAX, jadi izin request AJAX pengguna login diperiksa di sini:
   * - aksi tulis (API atau yang izinnya terdaftar) wajib punya izin aksi itu atau izin create/edit/delete modulnya
   * - selain itu cukup punya izin pada modul tersebut atau keluarga modulnya
   */
  private function isAjaxAllowed(string $uri): bool
  {
    $segments = array_values(array_filter(explode('/', strtolower(str_replace('index.php/', '', $uri))), 'strlen'));
    $isApi = ($segments[0] ?? '') === 'api';
    if ($isApi) {
      array_shift($segments);
    }
    $module = $segments[0] ?? '';
    $action = $segments[1] ?? '';
    $module = self::AJAX_MODULE_ALIASES[$module] ?? $module;

    if (in_array($module, self::AJAX_OPEN_MODULES, true) || ($module === 'user' && $action === 'profile') || is_member('admin')) {
      return true;
    }

    // Ubah akun sendiri: kepemilikan upload_file diperiksa di controller karena ID-nya ada di body
    if ($isApi && $module === 'user') {
      if ($action === 'upload_file' || ($action === 'edit' && ($segments[2] ?? '') === (string) login_id())) {
        return true;
      }
    }

    // Akun anggota tidak memakai API internal. Izinnya (mis. katalog/view_decrypted, anggota/online)
    // tidak boleh terhitung sebagai akses baca modul, karena modul itu berisi data semua anggota.
    if (is_member('anggota_online')) {
      return false;
    }

    $held = [];
    $registered = [];
    foreach ((array) session()->get('auth_permissions') as $name => $users) {
      $name = strtolower(preg_replace('#^api/#', '', (string) $name));
      $registered[$name] = true;
      if (in_array(login_id(), (array) $users)) {
        $held[$name] = true;
      }
    }

    $moduleRegistered = false;
    foreach (array_keys($registered) as $name) {
      if ($name === $module || str_starts_with($name, $module . '/')) {
        $moduleRegistered = true;
        break;
      }
    }

    // Upload sementara: rute bisa bertingkat (cms/banner/do_upload), jadi modulnya semua segmen kecuali yang terakhir
    $last = end($segments);
    if ($last === 'do_upload' || $last === 'do_delete') {
      $base = implode('/', array_slice($segments, 0, -1));
      $base = self::AJAX_MODULE_ALIASES[$base] ?? $base;
      // user/do_upload dipakai bersama oleh modal upload modul lain, isinya hanya file sementara tervalidasi
      if ($base === 'user') {
        return true;
      }

      $writes = ['create', 'edit', 'delete', $last];
      $writeRegistered = false;
      foreach ($writes as $write) {
        if (isset($held[$base . '/' . $write])) {
          return true;
        }
        $writeRegistered = $writeRegistered || isset($registered[$base . '/' . $write]);
      }
      if ($writeRegistered) {
        return false;
      }
    }

    $required = $this->requiredWriteActions($action);
    $target = $module . '/' . $action;
    if ($required && ($isApi || !$moduleRegistered || isset($registered[$target]))) {
      if (isset($held[$target])) {
        return true;
      }
      foreach ($required as $write) {
        if (isset($held[$module . '/' . $write])) {
          return true;
        }
      }
      return false;
    }

    $modules = [$module];
    foreach (self::AJAX_MODULE_FAMILIES as $family) {
      if (in_array($module, $family, true)) {
        $modules = $family;
      }
    }
    foreach (array_keys($held) as $name) {
      if (in_array(explode('/', $name)[0], $modules, true)) {
        return true;
      }
    }

    // Modul tanpa izin terdaftar tetap bisa dibaca staf, tapi tidak oleh akun tanpa izin apa pun
    return !$moduleRegistered && $held !== [];
  }

  /** Izin modul yang diterima untuk aksi tulis API; kosong berarti aksi baca. */
  private function requiredWriteActions(string $action): array
  {
    if (preg_match('/^(add|remove)_(to|from)_session/', $action)) {
      return ['create', 'edit'];
    }
    if (preg_match('/^(delete|hapus|remove)/', $action)) {
      return ['delete'];
    }
    if (preg_match('/^(edit|update|switch)/', $action)) {
      return ['edit'];
    }
    if (preg_match('/^(create|add|upload|save|import|proses|process|pulihkan)/', $action)) {
      return ['create', 'edit'];
    }
    return [];
  }

  /**
   * Allows After filters to inspect and modify the response
   * object as needed. This method does not allow any way
   * to stop execution of other after filters, short of
   * throwing an Exception or Error.
   *
   * @param RequestInterface  $request
   * @param ResponseInterface $response
   * @param array|null        $arguments
   *
   * @return mixed
   */
  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    //
  }
}
