<?php

namespace Anggota\Controllers;

use \CodeIgniter\Files\File;

/**
 * AnggotaController
 *
 * Menangani: index, keranjang, create, edit, detail, delete,
 * apply_status, proses_keranjang, pulihkan_keranjang, hapus_permanen,
 * camera, profile, getDefaults.
 */
class AnggotaController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    public function do_upload()
    {
        $file = $this->request->getFile('file');
        $allowedMimes = ['image/jpeg', 'image/png'];

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'msg' => 'File foto tidak valid.',
            ]);
        }

        if ($file->getSize() > 10 * 1024 * 1024 || !in_array($file->getMimeType(), $allowedMimes, true)) {
            return $this->response->setStatusCode(415)->setJSON([
                'success' => false,
                'msg' => 'Foto harus berupa JPG atau PNG dengan ukuran maksimal 10 MB.',
            ]);
        }

        $newFileName = $file->getRandomName();
        $file->move($this->uploadPath, $newFileName);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'name' => $newFileName,
                'type' => $file->getMimeType(),
            ],
            'msg' => 'Foto berhasil diunggah.',
        ]);
    }

    private function validReferenceIds($values, string $table, string $column): bool
    {
        if (!is_array($values) || $values === []) {
            return false;
        }

        $ids = array_values(array_unique(array_map('intval', $values)));
        if (in_array(0, $ids, true)) {
            return false;
        }

        return count($ids) === db_connect()->table($table)
            ->whereIn($column, $ids)
            ->countAllResults();
    }

    private function storeCameraImage(string $dataUri)
    {
        if (strlen($dataUri) > 14 * 1024 * 1024 || !preg_match('/^data:image\/(jpeg|jpg|png);base64,/i', $dataUri, $matches)) {
            return false;
        }

        $encoded = substr($dataUri, strpos($dataUri, ',') + 1);
        $binary = base64_decode($encoded, true);
        if ($binary === false || strlen($binary) > 10 * 1024 * 1024) {
            return false;
        }

        $imageInfo = @getimagesizefromstring($binary);
        if ($imageInfo === false || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png'], true)) {
            return false;
        }

        $extension = $imageInfo['mime'] === 'image/png' ? 'png' : 'jpg';
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        return file_put_contents($this->modulePath . $filename, $binary, LOCK_EX) === false ? false : $filename;
    }

    // ----------------------------------------------------------------
    // INDEX & LIST
    // ----------------------------------------------------------------

    public function index()
    {
        $this->data['title'] = ' Anggota';
        $this->data['is_member_list_page'] = true;
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');

        $jenisPerpustakaan = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? '';
        $jenisPerpustakaan = strtoupper(trim($jenisPerpustakaan));
        $this->data['is_sekolah'] = ($jenisPerpustakaan === 'SEKOLAH');
        $this->data['is_perguruan_tinggi'] = ($jenisPerpustakaan === 'PERGURUAN TINGGI');

        $db = db_connect();

        if ($this->data['is_sekolah']) {
            $this->data['kelas_list'] = $db->table('kelas_siswa')
                ->where('active', 1)
                ->orderBy('namakelassiswa', 'asc')
                ->get()
                ->getResult();
        } else {
            $this->data['kelas_list'] = [];
        }

        if ($this->data['is_perguruan_tinggi']) {
            $this->data['fakultas_list'] = $db->table('master_fakultas')
                ->where('active', 1)
                ->orderBy('Nama', 'asc')
                ->get()
                ->getResult();
            $this->data['jurusan_list'] = $db->table('master_jurusan')
                ->where('active', 1)
                ->orderBy('Nama', 'asc')
                ->get()
                ->getResult();
        } else {
            $this->data['fakultas_list'] = [];
            $this->data['jurusan_list'] = [];
        }

        echo view('Anggota\Views\list', $this->data);
    }

    // ----------------------------------------------------------------
    // UPDATE BATCH KELAS (khusus perpustakaan sekolah)
    // ----------------------------------------------------------------

    public function update_batch_kelas()
    {
        $memberIds = $this->request->getPost('member_ids');
        $kelasId   = $this->request->getPost('Kelas_id');

        if (empty($memberIds) || empty($kelasId)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Pilih anggota dan kelas tujuan terlebih dahulu',
            ]);
        }

        $memberIds = is_array($memberIds) ? $memberIds : explode(',', $memberIds);
        $memberIds = array_filter(array_map('trim', $memberIds));

        if (empty($memberIds)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Pilih anggota yang akan diperbarui kelasnya terlebih dahulu',
            ]);
        }

        $updateData = [];
        foreach ($memberIds as $id) {
            $updateData[] = [
                'ID'       => $id,
                'Kelas_id' => $kelasId,
                'UpdateBy' => login_id(),
            ];
        }

        $this->anggotaModel->updateBatch($updateData, 'ID');

        return $this->response->setJSON([
            'error'   => false,
            'message' => count($memberIds) . ' anggota berhasil diperbarui kelasnya',
        ]);
    }

    public function keranjang()
    {
        $this->data['title'] = 'Anggota - Keranjang';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Anggota\Views\list_keranjang', $this->data);
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create()
    {
        if (!is_allowed('anggota/create')) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->to('anggota');
        }

        $db = db_connect();
        $this->data['db'] = $db;
        $jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";

        if ($jenisperpustakaan == "UMUM") {
            $this->data['jenis_perpustakaan_id'] = 1;
        } elseif ($jenisperpustakaan == "KHUSUS") {
            $this->data['jenis_perpustakaan_id'] = 2;
        } elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
            $this->data['jenis_perpustakaan_id'] = 3;
        } else {
            $this->data['jenis_perpustakaan_id'] = 4;
        }

        $TipeNomorAnggota = $db->table('settingparameters')->where('Name', 'TipeNomorAnggota')->get()->getRow()->Value ?: "Manual";
        $identityNo = $this->request->getPost('IdentityNo');
        if ($TipeNomorAnggota == "Otomatis") {
            $this->data['TipeNomorAnggota'] = "Otomatis";
            $MemberNo = generateMemberNumber($identityNo);
        } else {
            $this->data['TipeNomorAnggota'] = "Manual";
            $MemberNo = $this->request->getPost('MemberNo');
        }

        $jenis_anggota = get_ref_single('jenis_anggota', 'UPPER(jenisanggota) = "UMUM"', 'data');
        $masa_berlaku = $jenis_anggota->MasaBerlakuAnggota ?? 365;
        $start = date('Y-m-d');
        $start_date = new \DateTime($start);
        $end = new \DateTime($start);
        $end_date = $end->add(new \DateInterval('P' . $masa_berlaku . 'D'));
        $this->data['date'] = date_format($start_date, "Y-m-d");

        $this->data['title'] = 'Tambah Anggota';

        $this->validation->setRules([
            'Fullname' => [
                'label'  => 'Fullname',
                'rules'  => 'required',
                'errors' => ['required' => 'Nama Tidak boleh kosong'],
            ],
            'Email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|is_unique[users.Email]',
                'errors' => [
                    'valid_email' => 'Masukan email yang benar',
                    'required'    => 'Email Tidak boleh Kosong',
                    'is_unique'   => 'Email ini sudah terdaftar.',
                ],
            ],
            'JenisAnggota_id' => [
                'label'  => 'Jenis Anggota',
                'rules'  => 'required',
                'errors' => ['required' => 'Jenis Anggota tidak boleh kosong'],
            ],
            'StatusAnggota_id' => [
                'label'  => 'Status Anggota',
                'rules'  => 'required',
                'errors' => ['required' => 'Status Anggota tidak boleh kosong'],
            ],
        ]);

        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $Koleksi = $this->request->getPost('CategoryLoan_id');
            $Locations = $this->request->getPost('LocationLoan_id');
            if (!$this->validReferenceIds($Koleksi, 'collectioncategorys', 'id')
                || !$this->validReferenceIds($Locations, 'location_library', 'ID')) {
                $this->session->setFlashdata('message', 'Koleksi dan lokasi perpustakaan wajib dipilih dengan benar.');
                echo view('Anggota\Views\add', $this->data);
                return;
            }

            $email = trim((string) $this->request->getPost('Email'));
            if ($db->table('members')->where('Email', $email)->countAllResults() > 0) {
                $this->session->setFlashdata('message', 'Email ini sudah terdaftar sebagai anggota.');
                echo view('Anggota\Views\add', $this->data);
                return;
            }

            $existingMember = $db->table('members')->where('MemberNo', $MemberNo)->get()->getRow();
            while ($existingMember) {
                $MemberNo = generateMemberNumber($identityNo);
                $existingMember = $db->table('members')->where('MemberNo', $MemberNo)->get()->getRow();
            }

            $save_data = [
                'Fullname'           => $this->request->getPost('Fullname'),
                'MemberNo'           => $MemberNo,
                'IdentityNo'         => $this->request->getPost('IdentityNo'),
                'PlaceOfBirth'       => $this->request->getPost('PlaceOfBirth'),
                'DateOfBirth'        => $this->request->getPost('DateOfBirth'),
                'Address'            => $this->request->getPost('Address'),
                'AddressNow'         => $this->request->getPost('AddressNow'),
                'Phone'              => $this->request->getPost('Phone'),
                'InstitutionName'    => $this->request->getPost('InstitutionName'),
                'InstitutionAddress' => $this->request->getPost('InstitutionAddress'),
                'InstitutionPhone'   => $this->request->getPost('InstitutionPhone'),
                'MotherMaidenName'   => $this->request->getPost('MotherMaidenName'),
                'Email'              => $this->request->getPost('Email'),
                'RT'                 => $this->request->getPost('RT'),
                'RTNow'              => $this->request->getPost('RTNow'),
                'RWNow'              => $this->request->getPost('RWNow'),
                'RW'                 => $this->request->getPost('RW'),
                'TahunAjaran'        => $this->request->getPost('TahunAjaran'),
                'IdentityType_id'    => $this->request->getPost('IdentityType_id'),
                'MaritalStatus_id'   => $this->request->getPost('MaritalStatus_id'),
                'Sex_id'             => $this->request->getPost('Sex_id'),
                'JenjangPendidikan_id' => $this->request->getPost('JenjangPendidikan_id'),
                'Job_id'             => $this->request->getPost('Job_id'),
                'JenisAnggota_id'    => $this->request->getPost('JenisAnggota_id'),
                'Agama_id'           => $this->request->getPost('Agama_id'),
                'UnitKerja_id'       => $this->request->getPost('UnitKerja_id'),
                'Fakultas_id'        => $this->request->getPost('Fakultas_id'),
                'Kelas_id'           => $this->request->getPost('Kelas_id'),
                'Jurusan_id'         => $this->request->getPost('Jurusan_id'),
                'IsKeranjang'        => 0,
                'StatusAnggota_id'   => $this->request->getPost('StatusAnggota_id'),
                'RegisterDate'       => date("Y-m-d H:i:s"),
                'EndDate'            => $this->request->getPost('EndDate'),
                'CreateBy'           => login_id(),
                'Branch_id'          => branch_id(),
            ];

            $province = $this->request->getPost('Province');
            if (!empty($province)) {
                $region = $this->regionModel->where('code', $province)->first();
                $save_data['Province'] = $region->name ?? null;
            }

            $city = $this->request->getPost('City');
            if (!empty($city)) {
                $region = $this->regionModel->where('code', $city)->first();
                $save_data['City'] = $region->name ?? null;
            }

            $kecamatan = $this->request->getPost('Kecamatan');
            if (!empty($kecamatan)) {
                $region = $this->regionModel->where('code', $kecamatan)->first();
                $save_data['Kecamatan'] = $region->name ?? null;
            }

            $kelurahan = $this->request->getPost('Kelurahan');
            if (!empty($kelurahan)) {
                $region = $this->regionModel->where('code', $kelurahan)->first();
                $save_data['Kelurahan'] = $region->name ?? null;
            }

            $provinceNow = $this->request->getPost('ProvinceNow');
            if (!empty($provinceNow)) {
                $region = $this->regionModel->where('code', $provinceNow)->first();
                $save_data['ProvinceNow'] = $region->name ?? null;
            }

            $cityNow = $this->request->getPost('CityNow');
            if (!empty($cityNow)) {
                $region = $this->regionModel->where('code', $cityNow)->first();
                $save_data['CityNow'] = $region->name ?? null;
            }

            $kecamatanNow = $this->request->getPost('KecamatanNow');
            if (!empty($kecamatanNow)) {
                $region = $this->regionModel->where('code', $kecamatanNow)->first();
                $save_data['KecamatanNow'] = $region->name ?? null;
            }

            $kelurahanNow = $this->request->getPost('KelurahanNow');
            if (!empty($kelurahanNow)) {
                $region = $this->regionModel->where('code', $kelurahanNow)->first();
                $save_data['KelurahanNow'] = $region->name ?? null;
            }

            $files = (array) $this->request->getPost('PhotoUrl');
            if (count($files)) {
                $listed_file = [];
                foreach ($files as $uuid => $name) {
                    if (is_string($name) && basename($name) === $name && is_file($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['PhotoUrl'] = implode(',', $listed_file);
            }

            $base64_string = $this->request->getPost('camera_image');
            if (!empty($base64_string)) {
                $newFileName = $this->storeCameraImage($base64_string);
                if ($newFileName === false) {
                    $this->session->setFlashdata('message', 'Foto kamera tidak valid. Gunakan JPG atau PNG maksimal 10 MB.');
                    echo view('Anggota\Views\add', $this->data);
                    return;
                }
                $save_data['PhotoUrl'] = $newFileName;
            }

            $membersTable = $db->table('members');
            $membersTable->insert($save_data);
            $newAnggotaId = $db->insertID();

            if ($newAnggotaId) {
                if (!empty($Koleksi)) {
                    $save_akses_koleksi = [];
                    for ($x = 0; $x < count($Koleksi); $x++) {
                        $save_akses_koleksi[] = [
                            'Member_id'       => $newAnggotaId,
                            'CategoryLoan_id' => $Koleksi[$x],
                        ];
                    }
                    if (!empty($save_akses_koleksi)) {
                        $this->AksesKoleksiModel->insertBatch($save_akses_koleksi);
                    }
                }

                $save_akses_lokasi = [];
                for ($x = 0; $x < count($Locations); $x++) {
                    $save_akses_lokasi[] = [
                        'Member_id'      => $newAnggotaId,
                        'LocationLoan_id' => $Locations[$x],
                    ];
                }
                if (!empty($save_akses_lokasi)) {
                    $this->anggotahakaksesModel->insertBatch($save_akses_lokasi);
                }

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Anggota berhasil disimpan');
                return redirect()->to('/anggota');
            } else {
                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Gagal');
                $this->session->setFlashdata('swal_text', 'Anggota gagal disimpan');
                echo view('Anggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('anggota/create');
            $this->session->setFlashdata('message', $this->validation->getErrors() ? $this->validation->listErrors() : '');
            echo view('Anggota\Views\add', $this->data);
        }
    }

    // ----------------------------------------------------------------
    // EDIT & PROFILE
    // ----------------------------------------------------------------

    public function camera()
    {
        $file = $this->request->getFile('file_image');
        if (!$file || !$file->isValid() || $file->hasMoved()
            || $file->getSize() > 10 * 1024 * 1024
            || !in_array($file->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            return $this->response->setStatusCode(415)->setBody('Foto tidak valid.');
        }

        $filename = $file->getRandomName();
        $file->move($this->modulePath, $filename);
        return $this->response->setBody(base_url('uploads/anggota/' . $filename));
    }

    public function profile()
    {
        $member_no = user()->username;
        $member = get_ref_single('members', 'memberNo="' . $member_no . '"', 'data');
        $this->edit($member->ID, true);
    }

    public function edit(int $ID = null, $is_anggota = false)
    {
    if (!is_allowed('anggota/edit')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Maaf, Anda tidak memiliki akses',
            ]);
        }
        set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
        set_message('toastr_type', 'error');
        return redirect()->to('anggota');
    }

        if (empty($ID)) {
            $encId = $this->request->getVar('ID');
            $ID = $encId ? (int) decData($encId) : null;
        }

        if (empty($ID)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
         $db = db_connect();
        $this->data['db'] = $db;
         $jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";

        if ($jenisperpustakaan == "UMUM") {
            $this->data['jenis_perpustakaan_id'] = 1;
        } elseif ($jenisperpustakaan == "KHUSUS") {
            $this->data['jenis_perpustakaan_id'] = 2;
        } elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
            $this->data['jenis_perpustakaan_id'] = 3;
        } else {
            $this->data['jenis_perpustakaan_id'] = 4;
        }
        $member_id = $ID;
        $MemberNo = $this->request->getPost('IdentityNo');

        $hak_akses_koleksi = $this->AksesKoleksiModel->where('member_id', $member_id)->findAll();
        $arr_hak_akses_koleksi = [];
        foreach ($hak_akses_koleksi as $row) {
            $arr_hak_akses_koleksi[] = $row->CategoryLoan_id;
        }

        $hak_akses_lokasi = $this->anggotahakaksesModel->where('Member_id', $member_id)->findAll();
        $arr_hak_akses_lokasi = [];
        foreach ($hak_akses_lokasi as $row) {
            $arr_hak_akses_lokasi[] = $row->LocationLoan_id;
        }

        $anggota = $this->anggotaModel->find($ID);
        if (!$anggota) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->data['title']               = 'Ubah Anggota';
        $this->data['anggota']             = $anggota;
        $this->data['CreateBy']            = get_username($anggota->CreateBy ?? 0);
        $this->data['UpdateBy']            = get_username($anggota->UpdateBy ?? 0);
        $this->data['hak_akses_koleksi']   = $hak_akses_koleksi;
        $this->data['arr_hak_akses_koleksi'] = $arr_hak_akses_koleksi;
        $this->data['hak_akses_lokasi']    = $hak_akses_lokasi;
        $this->data['arr_hak_akses_lokasi'] = $arr_hak_akses_lokasi;

        $this->validation->setRules([
            'Fullname' => [
                'label'  => 'Fullname',
                'rules'  => 'required',
                'errors' => ['required' => 'Nama Tidak boleh kosong'],
            ],
            'JenisAnggota_id' => [
                'label'  => 'Jenis Anggota',
                'rules'  => 'required',
                'errors' => ['required' => 'Jenis Anggota tidak boleh kosong'],
            ],
            'StatusAnggota_id' => [
                'label'  => 'Status Anggota',
                'rules'  => 'required',
                'errors' => ['required' => 'Status Anggota tidak boleh kosong'],
            ],
        ]);

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $Koleksi = $this->request->getPost('CategoryLoan_id');
                $Locations = $this->request->getPost('LocationLoan_id');
                if (!$this->validReferenceIds($Koleksi, 'collectioncategorys', 'id')
                    || !$this->validReferenceIds($Locations, 'location_library', 'ID')) {
                    return $this->request->isAJAX()
                        ? $this->response->setJSON(['success' => false, 'message' => 'Koleksi dan lokasi perpustakaan wajib dipilih dengan benar.'])
                        : redirect()->back()->with('message', 'Koleksi dan lokasi perpustakaan wajib dipilih dengan benar.');
                }

                $email = trim((string) $this->request->getPost('Email'));
                if ($email !== '' && $db->table('members')->where('Email', $email)->where('ID !=', $ID)->countAllResults() > 0) {
                    return $this->request->isAJAX()
                        ? $this->response->setJSON(['success' => false, 'message' => 'Email ini sudah terdaftar sebagai anggota lain.'])
                        : redirect()->back()->with('message', 'Email ini sudah terdaftar sebagai anggota lain.');
                }

                $update_data = [
                    'Fullname'           => $this->request->getPost('Fullname'),
                    'MemberNo'           => $MemberNo,
                    'IdentityNo'         => $this->request->getPost('IdentityNo'),
                    'PlaceOfBirth'       => $this->request->getPost('PlaceOfBirth'),
                    'DateOfBirth'        => $this->request->getPost('DateOfBirth'),
                    'Address'            => $this->request->getPost('Address'),
                    'AddressNow'         => $this->request->getPost('AddressNow'),
                    'Phone'              => $this->request->getPost('Phone'),
                    'InstitutionName'    => $this->request->getPost('InstitutionName'),
                    'InstitutionAddress' => $this->request->getPost('InstitutionAddress'),
                    'InstitutionPhone'   => $this->request->getPost('InstitutionPhone'),
                    'MotherMaidenName'   => $this->request->getPost('MotherMaidenName'),
                    'Email'              => $this->request->getPost('Email'),
                    'RT'                 => $this->request->getPost('RT'),
                    'RTNow'              => $this->request->getPost('RTNow'),
                    'RWNow'              => $this->request->getPost('RWNow'),
                    'RW'                 => $this->request->getPost('RW'),
                    'TahunAjaran'        => $this->request->getPost('TahunAjaran'),
                    'IdentityType_id'    => $this->request->getPost('IdentityType_id'),
                    'MaritalStatus_id'   => $this->request->getPost('MaritalStatus_id'),
                    'Sex_id'             => $this->request->getPost('Sex_id'),
                    'JenjangPendidikan_id' => $this->request->getPost('JenjangPendidikan_id'),
                    'Job_id'             => $this->request->getPost('Job_id'),
                    'JenisAnggota_id'    => $this->request->getPost('JenisAnggota_id'),
                    'Agama_id'           => $this->request->getPost('Agama_id'),
                    'UnitKerja_id'       => $this->request->getPost('UnitKerja_id'),
                    'Fakultas_id'        => $this->request->getPost('Fakultas_id'),
                    'Kelas_id'           => $this->request->getPost('Kelas_id'),
                    'Jurusan_id'         => $this->request->getPost('Jurusan_id'),
                    'StatusAnggota_id'   => $this->request->getPost('StatusAnggota_id'),
                    'UpdateBy'           => login_id(),
                ];

                $province = $this->request->getPost('Province');
                if (!empty($province)) {
                    $region = $this->regionModel->where('code', $province)->first();
                    $update_data['Province'] = $region->name ?? null;
                }

                $city = $this->request->getPost('City');
                if (!empty($city)) {
                    $region = $this->regionModel->where('code', $city)->first();
                    $update_data['City'] = $region->name ?? null;
                }

                $kecamatan = $this->request->getPost('Kecamatan');
                if (!empty($kecamatan)) {
                    $region = $this->regionModel->where('code', $kecamatan)->first();
                    $update_data['Kecamatan'] = $region->name ?? null;
                }

                $kelurahan = $this->request->getPost('Kelurahan');
                if (!empty($kelurahan)) {
                    $region = $this->regionModel->where('code', $kelurahan)->first();
                    $update_data['Kelurahan'] = $region->name ?? null;
                }

                $provinceNow = $this->request->getPost('ProvinceNow');
                if (!empty($provinceNow)) {
                    $region = $this->regionModel->where('code', $provinceNow)->first();
                    $update_data['ProvinceNow'] = $region->name ?? null;
                }

                $cityNow = $this->request->getPost('CityNow');
                if (!empty($cityNow)) {
                    $region = $this->regionModel->where('code', $cityNow)->first();
                    $update_data['CityNow'] = $region->name ?? null;
                }

                $kecamatanNow = $this->request->getPost('KecamatanNow');
                if (!empty($kecamatanNow)) {
                    $region = $this->regionModel->where('code', $kecamatanNow)->first();
                    $update_data['KecamatanNow'] = $region->name ?? null;
                }

                $kelurahanNow = $this->request->getPost('KelurahanNow');
                if (!empty($kelurahanNow)) {
                    $region = $this->regionModel->where('code', $kelurahanNow)->first();
                    $update_data['KelurahanNow'] = $region->name ?? null;
                }

                $is_camera = $this->request->getPost('is_camera');
                if ($is_camera) {
                    $base64_string = $this->request->getPost('camera_image');
                    if (!empty($base64_string)) {
                        $newFileName = $this->storeCameraImage($base64_string);
                        if ($newFileName === false) {
                            return $this->request->isAJAX()
                                ? $this->response->setJSON(['success' => false, 'message' => 'Foto kamera tidak valid. Gunakan JPG atau PNG maksimal 10 MB.'])
                                : redirect()->back()->with('message', 'Foto kamera tidak valid. Gunakan JPG atau PNG maksimal 10 MB.');
                        }
                        $update_data['PhotoUrl'] = $newFileName;
                    }
                } else {
                    $files = (array) $this->request->getPost('file_image');
                    if (count($files)) {
                        $listed_file = [];
                        foreach ($files as $uuid => $name) {
                            if (is_string($name) && basename($name) === $name && is_file($this->modulePath . $name)) {
                                $listed_file[] = $name;
                            } elseif (is_string($name) && basename($name) === $name && is_file($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName = $file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                        $update_data['PhotoUrl'] = implode(',', $listed_file);
                    }
                }

                $anggotaUpdate = $this->anggotaModel->update($ID, $update_data);
                if ($anggotaUpdate) {
                    $this->AksesKoleksiModel->where('member_id', $member_id)->delete();

                    $save_akses_koleksi = [];
                    for ($x = 0; $x < count((array) $Koleksi); $x++) {
                        $save_akses_koleksi[] = [
                            'Member_id'       => $member_id,
                            'CategoryLoan_id' => $Koleksi[$x],
                        ];
                        if (!empty($save_akses_koleksi)) {
                            $this->AksesKoleksiModel->insertBatch($save_akses_koleksi);
                        }
                    }

                    $this->anggotahakaksesModel->where('member_id', $member_id)->delete();
                    $save_akses_lokasi = [];
                    for ($x = 0; $x < count((array) $Locations); $x++) {
                        $save_akses_lokasi[] = [
                            'Member_id'       => $member_id,
                            'LocationLoan_id' => $Locations[$x],
                        ];
                    }
                    if (!empty($save_akses_lokasi)) {
                        $this->anggotahakaksesModel->insertBatch($save_akses_lokasi);
                    }

                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Data Anggota berhasil disimpan',
                        ]);
                    }

                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Data Anggota berhasil disimpan');

                    return $is_anggota ? redirect()->back() : redirect()->to('/anggota');
                } else {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Anggota gagal disimpan',
                        ]);
                    }

                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Gagal');
                    $this->session->setFlashdata('swal_text', 'Anggota gagal disimpan');

                    return $is_anggota ? redirect()->back() : redirect()->back();
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors'  => $this->validation->getErrors(),
                    ]);
                }
            }
        }

        $this->data['redirect']    = base_url('anggota/edit/' . $ID);
        $this->data['is_anggota']  = $is_anggota;
        echo view('Anggota\Views\update', $this->data);
    }

    // ----------------------------------------------------------------
    // DETAIL
    // ----------------------------------------------------------------

    public function detail(int $id = null)
    {
        $anggota = $this->anggotaModel->find($id);
        $this->data['redirect'] = base_url('anggota/detail/' . $id);
        $this->data['anggota']  = $anggota;
        echo view('Anggota\Views\detail', $this->data);
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id = 0)
{
    if (!is_allowed('anggota/delete')) {
        $this->session->setFlashdata('swal_icon', 'error');
        $this->session->setFlashdata('swal_title', 'Error');
        $this->session->setFlashdata('swal_text', 'Maaf, Anda tidak memiliki akses');
        return redirect()->to('anggota');
    }

    if (!$id) {
        $this->session->setFlashdata('swal_icon', 'error');
        $this->session->setFlashdata('swal_title', 'Error');
        $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
        return redirect()->to('/anggota');
    }

    // Cek apakah anggota masih memiliki pinjaman aktif (LoanStatus = 'Loan')
    $db = \Config\Database::connect();
    $masihPinjam = $db->table('collectionloanitems')
        ->where('member_id', $id)
        ->where('LoanStatus', 'Loan')
        ->countAllResults();

    if ($masihPinjam > 0) {
        $this->session->setFlashdata('swal_icon', 'error');
        $this->session->setFlashdata('swal_title', 'Gagal Dihapus');
        $this->session->setFlashdata('swal_text', 'Anggota ini masih memiliki ' . $masihPinjam . ' buku yang belum dikembalikan. Anggota tidak dapat dihapus sebelum pinjaman diselesaikan.');
        return redirect()->to('/anggota');
    }

    $anggotaDelete = $this->anggotaModel->delete($id);
    if ($anggotaDelete) {
        $this->session->setFlashdata('swal_icon', 'success');
        $this->session->setFlashdata('swal_title', 'Berhasil');
        $this->session->setFlashdata('swal_text', 'Data Anggota berhasil dihapus');
        return redirect()->to('/anggota');
    } else {
        $this->session->setFlashdata('swal_icon', 'warning');
        $this->session->setFlashdata('swal_title', 'Peringatan');
        $this->session->setFlashdata('swal_text', lang('Anggota.info.failed_deleted'));
        return redirect()->to('/anggota/delete/' . $id);
    }
}

    // ----------------------------------------------------------------
    // STATUS
    // ----------------------------------------------------------------

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $anggotaUpdate = $this->anggotaModel->update($id, [$field => $value]);
        if ($anggotaUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Anggota berhasil disimpan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Anggota gagal disimpan');
        }

        return redirect()->to('/anggota');
    }

    // ----------------------------------------------------------------
    // KERANJANG
    // ----------------------------------------------------------------

    public function proses_keranjang()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['id' => $ID, 'IsKeranjang' => 1];
            }
            if (!empty($update_data)) {
                $this->anggotaModel->updateBatch($update_data, 'id');
                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Berhasil dipindahkan ke keranjang');
            }
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih anggota yang akan dipindahkan ke keranjang terlebih dahulu');
        }

        return redirect()->back();
    }

    public function pulihkan_keranjang()
    {
        $IDs = $this->request->getvar('ID');
        $update_data = [];

        if (!empty($IDs)) {
            foreach ($IDs as $ID) {
                $update_data[] = ['ID' => $ID, 'IsKeranjang' => 0];
            }
            if (!empty($update_data)) {
                $this->anggotaModel->updateBatch($update_data, 'ID');
                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Berhasil dipulihkan dari keranjang anggota');
            }
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih anggota yang akan dipulihkan terlebih dahulu');
        }

        return redirect()->back();
    }

    public function hapus_permanen()
{
    $IDs = $this->request->getVar('ID');

    if (empty($IDs)) {
        $this->session->setFlashdata('swal_icon', 'warning');
        $this->session->setFlashdata('swal_title', 'Peringatan');
        $this->session->setFlashdata('swal_text', 'Pilih Anggota yang akan dihapus permanen terlebih dahulu');
        return redirect()->back();
    }

    // Normalisasi jadi array, karena $IDs bisa string tunggal atau comma-separated
    $memberIds = is_array($IDs) ? $IDs : explode(',', $IDs);
    $memberIds = array_filter(array_map('trim', $memberIds));

    $db = \Config\Database::connect();

    // Cek anggota yang masih punya pinjaman aktif (LoanStatus = 'Loan')
    $anggotaMasihPinjam = $db->table('collectionloanitems')
        ->select('collectionloanitems.member_id, member.FullName')
        ->join('member', 'member.ID = collectionloanitems.member_id', 'left') // sesuaikan nama tabel & kolom member
        ->whereIn('collectionloanitems.member_id', $memberIds)
        ->where('collectionloanitems.LoanStatus', 'Loan')
        ->groupBy('collectionloanitems.member_id')
        ->get()
        ->getResultArray();

    if (!empty($anggotaMasihPinjam)) {
        $namaAnggota = array_column($anggotaMasihPinjam, 'FullName');
        // Kalau join gagal / nama null, fallback ke ID saja
        $namaAnggota = array_filter($namaAnggota) ?: array_column($anggotaMasihPinjam, 'member_id');

        $this->session->setFlashdata('swal_icon', 'error');
        $this->session->setFlashdata('swal_title', 'Gagal Dihapus');
        $this->session->setFlashdata('swal_html', 'Anggota berikut masih memiliki pinjaman buku yang belum dikembalikan:<br><br>' . implode('<br>', $namaAnggota) . '<br><br>Anggota tidak dapat dihapus sebelum pinjaman diselesaikan.');
        return redirect()->back();
    }

    // Kalau semua ID aman (tidak ada pinjaman aktif), lanjut hapus
    $this->anggotaModel->delete($memberIds);

    $this->session->setFlashdata('swal_icon', 'success');
    $this->session->setFlashdata('swal_title', 'Berhasil');
    $this->session->setFlashdata('swal_text', 'Anggota Berhasil dihapus permanen');

    return redirect()->back();
}

    // ----------------------------------------------------------------
    // DEFAULTS (AJAX)
    // ----------------------------------------------------------------

    public function getDefaults($jenisAnggotaId)
    {
        $db = \Config\Database::connect();

        $collections = $db->table('collectioncategorysdefault')
            ->select('CollectionCategory_id')
            ->where('JenisAnggota_id', $jenisAnggotaId)
            ->get()
            ->getResultArray();
        $collectionIds = array_column($collections, 'CollectionCategory_id');

        $locations = $db->table('location_library_default')
            ->select('Location_Library_id')
            ->where('JenisAnggota_id', $jenisAnggotaId)
            ->get()
            ->getResultArray();
        $locationIds = array_column($locations, 'Location_Library_id');

        return $this->response->setJSON([
            'success'     => true,
            'collections' => $collectionIds,
            'locations'   => $locationIds,
        ]);
    }
}
