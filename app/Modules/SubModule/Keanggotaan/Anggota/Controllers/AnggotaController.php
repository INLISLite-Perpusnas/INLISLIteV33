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

    // ----------------------------------------------------------------
    // INDEX & LIST
    // ----------------------------------------------------------------

    public function index()
    {
        $this->data['title'] = ' Anggota';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Anggota\Views\list', $this->data);
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
                $save_data['Province'] = $region->name;
            }

            $city = $this->request->getPost('City');
            if (!empty($city)) {
                $region = $this->regionModel->where('code', $city)->first();
                $save_data['City'] = $region->name;
            }

            $kecamatan = $this->request->getPost('Kecamatan');
            if (!empty($kecamatan)) {
                $region = $this->regionModel->where('code', $kecamatan)->first();
                $save_data['Kecamatan'] = $region->name;
            }

            $kelurahan = $this->request->getPost('Kelurahan');
            if (!empty($kelurahan)) {
                $region = $this->regionModel->where('code', $kelurahan)->first();
                $save_data['Kelurahan'] = $region->name;
            }

            $provinceNow = $this->request->getPost('ProvinceNow');
            if (!empty($provinceNow)) {
                $region = $this->regionModel->where('code', $provinceNow)->first();
                $save_data['ProvinceNow'] = $region->name;
            }

            $cityNow = $this->request->getPost('CityNow');
            if (!empty($cityNow)) {
                $region = $this->regionModel->where('code', $cityNow)->first();
                $save_data['CityNow'] = $region->name;
            }

            $kecamatanNow = $this->request->getPost('KecamatanNow');
            if (!empty($kecamatanNow)) {
                $region = $this->regionModel->where('code', $kecamatanNow)->first();
                $save_data['KecamatanNow'] = $region->name;
            }

            $kelurahanNow = $this->request->getPost('KelurahanNow');
            if (!empty($kelurahanNow)) {
                $region = $this->regionModel->where('code', $kelurahanNow)->first();
                $save_data['KelurahanNow'] = $region->name;
            }

            $files = (array) $this->request->getPost('PhotoUrl');
            if (count($files)) {
                $listed_file = [];
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
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
                $file = new File($this->uploadPath);
                $newFileName = $file->getRandomName() . '.jpg';
                base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
                $save_data['PhotoUrl'] = $newFileName;
            }

            $newAnggotaId = $this->anggotaModel->protect(false)->insert($save_data);

            if ($newAnggotaId) {
                $Koleksi = $this->request->getPost('CategoryLoan_id');
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

                $Locations = $this->request->getPost('LocationLoan_id');
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
        $filename = 'pic_' . date('YmdHis') . '.jpeg';
        $url = '';
        if (move_uploaded_file($_FILES['file_image']['tmp_name'], 'upload/' . $filename)) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/upload/' . $filename;
        }
        echo $url;
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

        $db = db_connect();
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
                    $update_data['Province'] = $region->name;
                }

                $city = $this->request->getPost('City');
                if (!empty($city)) {
                    $region = $this->regionModel->where('code', $city)->first();
                    $update_data['City'] = $region->name;
                }

                $kecamatan = $this->request->getPost('Kecamatan');
                if (!empty($kecamatan)) {
                    $region = $this->regionModel->where('code', $kecamatan)->first();
                    $update_data['Kecamatan'] = $region->name;
                }

                $kelurahan = $this->request->getPost('Kelurahan');
                if (!empty($kelurahan)) {
                    $region = $this->regionModel->where('code', $kelurahan)->first();
                    $update_data['Kelurahan'] = $region->name;
                }

                $provinceNow = $this->request->getPost('ProvinceNow');
                if (!empty($provinceNow)) {
                    $region = $this->regionModel->where('code', $provinceNow)->first();
                    $update_data['ProvinceNow'] = $region->name;
                }

                $cityNow = $this->request->getPost('CityNow');
                if (!empty($cityNow)) {
                    $region = $this->regionModel->where('code', $cityNow)->first();
                    $update_data['CityNow'] = $region->name;
                }

                $kecamatanNow = $this->request->getPost('KecamatanNow');
                if (!empty($kecamatanNow)) {
                    $region = $this->regionModel->where('code', $kecamatanNow)->first();
                    $update_data['KecamatanNow'] = $region->name;
                }

                $kelurahanNow = $this->request->getPost('KelurahanNow');
                if (!empty($kelurahanNow)) {
                    $region = $this->regionModel->where('code', $kelurahanNow)->first();
                    $update_data['KelurahanNow'] = $region->name;
                }

                $is_camera = $this->request->getPost('is_camera');
                if ($is_camera) {
                    $base64_string = $this->request->getPost('camera_image');
                    if (!empty($base64_string)) {
                        $file = new File($this->uploadPath);
                        $newFileName = $file->getRandomName() . '.jpg';
                        base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
                        $update_data['PhotoUrl'] = $newFileName;
                    }
                } else {
                    $files = (array) $this->request->getPost('file_image');
                    if (count($files)) {
                        $listed_file = [];
                        foreach ($files as $uuid => $name) {
                            if (file_exists($this->modulePath . $name)) {
                                $listed_file[] = $name;
                            } elseif (file_exists($this->uploadPath . $name)) {
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
                    $Koleksi = $this->request->getPost('CategoryLoan_id');
                    $this->AksesKoleksiModel->where('member_id', $member_id)->delete();

                    $save_akses_koleksi = [];
                    for ($x = 0; $x < count($Koleksi); $x++) {
                        $save_akses_koleksi[] = [
                            'Member_id'       => $member_id,
                            'CategoryLoan_id' => $Koleksi[$x],
                        ];
                        if (!empty($save_akses_koleksi)) {
                            $this->AksesKoleksiModel->insertBatch($save_akses_koleksi);
                        }
                    }

                    $Locations = $this->request->getPost('LocationLoan_id');
                    $this->anggotahakaksesModel->where('member_id', $member_id)->delete();
                    $save_akses_lokasi = [];
                    for ($x = 0; $x < count($Locations); $x++) {
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

                    return $is_anggota ? redirect()->back() : redirect()->to('/anggota/edit/' . $ID);
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
        $IDs = $this->request->getvar('ID');

        if (!empty($IDs)) {
            $this->anggotaModel->delete($IDs);
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Anggota Berhasil dihapus permanen');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Pilih Anggota yang akan dihapus permanen terlebih dahulu');
        }

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
