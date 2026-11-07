<?php

namespace PerpanjanganAnggota\Controllers;

use \CodeIgniter\Files\File;

class PerpanjanganAnggota extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    // public $anggotaModel;
    public $anggotaModel;
    public $perpanjanganModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->perpanjanganModel = new \PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
        $this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->uploadPath = WRITEPATH . 'uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/perpanjangan-anggota/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
        helper('adminigniter');
        helper('reference');
        helper('anggota');
        helper('tgl_indo');
        helper('url');
        helper('thumbnail');
    }
    public function index()
    {
        $query = $this->perpanjanganModel
            ->select('member_perpanjangan.*, members.Fullname as nama, members.MemberNo as MembersNo')
            ->join('members', 'members.ID = member_perpanjangan.Member_id', 'left')
            ->orderBy('member_perpanjangan.ID', 'DESC'); // This line sorts the results by ID in descending order

        // Eksekusi query
        $perpanjangans = $query->findAll();

        // Data untuk view
        $this->data['title'] = 'Daftar Perpanjangan Anggota';
        $this->data['perpanjangans'] = $perpanjangans;

        return view('PerpanjanganAnggota\Views\list', $this->data);
    }

    public function searchMembers()
    {
        if (!logged_in() || !is_allowed('perpanjangan-anggota/create', login_id(), false)) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak']);
        }

        $requestedPage = $this->request->getGet('page');
        $page = max(1, min(1000, is_scalar($requestedPage) ? (int) $requestedPage : 1));
        $limit = 50;
        $query = $this->request->getGet('q');
        $search = mb_substr(is_string($query) ? trim($query) : '', 0, 100);
        $builder = db_connect('data')->table('members')
            ->select('members.ID, members.MemberNo, members.Fullname, members.NoHp, members.Email, members.Address, members.EndDate, members.JenisAnggota_id, members.Fakultas_id, members.Jurusan_id, members.Kelas_id, members.Job_id')
            ->select('jenis_anggota.jenisanggota AS JenisAnggota_label, master_fakultas.Nama AS Fakultas_label, master_jurusan.Nama AS Jurusan_label')
            ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
            ->join('master_fakultas', 'master_fakultas.id = members.Fakultas_id', 'left')
            ->join('master_jurusan', 'master_jurusan.id = members.Jurusan_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('members.MemberNo', $search, 'after')
                ->orLike('members.Fullname', $search, 'after')
                ->groupEnd();
        }

        foreach (['fakultas' => 'Fakultas_id', 'jurusan' => 'Jurusan_id', 'kelas' => 'Kelas_id', 'pekerjaan' => 'Job_id'] as $param => $column) {
            $value = $this->request->getGet($param);
            if ($value !== null && $value !== '') {
                if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value < 1) {
                    return $this->response->setStatusCode(400)->setJSON(['message' => 'Filter tidak valid']);
                }
                $builder->where('members.' . $column, (int) $value);
            }
        }

        $education = $this->request->getGet('pendidikan');
        if ($education !== null && $education !== '') {
            if (!is_scalar($education) || !ctype_digit((string) $education) || (int) $education < 1) {
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Filter tidak valid']);
            }
            $builder->groupStart()
                ->where('members.JenjangPendidikan_id', (int) $education)
                ->orGroupStart()
                    ->where('members.JenjangPendidikan_id', null)
                    ->where('members.EducationLevel_id', (int) $education)
                ->groupEnd()
            ->groupEnd();
        }

        if ($this->request->getGet('expired') === '1') {
            $builder->where('members.EndDate <', date('Y-m-d'));
        }

        $members = $builder->orderBy('members.ID', 'DESC')->get($limit + 1, ($page - 1) * $limit)->getResult();
        $more = count($members) > $limit && $page < 1000;
        $results = [];
        foreach (array_slice($members, 0, $limit) as $member) {
            $results[] = [
                'id' => (string) $member->ID,
                'text' => $member->MemberNo . ' - ' . $member->Fullname,
                'no_anggota' => $member->MemberNo,
                'name' => $member->Fullname,
                'email' => $member->Email,
                'address' => $member->Address,
                'date' => $member->EndDate,
                'nomor' => $member->NoHp,
                'jenis' => $member->JenisAnggota_id,
                'jenis_label' => $member->JenisAnggota_label,
                'fakultas_label' => $member->Fakultas_label,
                'jurusan_label' => $member->Jurusan_label,
            ];
        }

        return $this->response->setJSON(['results' => $results, 'pagination' => ['more' => $more]]);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah PerpanjanganAnggota';

        $this->validation->setRules([
            'Member_id' => ['label' => 'Anggota', 'rules' => 'required'],
            'EndDate' => ['label' => 'Tanggal Berakhir', 'rules' => 'required'],
            'Jenisanggota_id' => ['label' => 'Jenis Anggota', 'rules' => 'required'],
        ]);
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $member_ids = (array) $this->request->getPost('Member_id');
            $biaya      = $this->request->getPost('biaya');
            $keterangan = $this->request->getPost('Keterangan');
            $endDate    = $this->request->getPost('EndDate');
            $jenisId    = $this->request->getPost('Jenisanggota_id');
            $fakultasId = $this->request->getPost('Fakultas_id');
            $jurusanId  = $this->request->getPost('Jurusan_id');
            $isLunas    = $this->request->getPost('is_lunas') ? 1 : 0;

            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($member_ids as $id_anggota) {
                $save_data = [
                    'Member_id'       => $id_anggota,
                    'Tanggal'         => $endDate,
                    'biaya'           => $biaya,
                    'Keterangan'      => $keterangan,
                    'IsLunas'         => $isLunas,
                    'Branch_id'       => branch_id(),
                    'UpdateBy'        => user_id(),
                ];

                $newPerpanjanganId = $this->perpanjanganModel->insert($save_data, true); // true = return insert ID

                if ($newPerpanjanganId !== false && $newPerpanjanganId > 0) {
                    $data = [
                        'EndDate' => $endDate,
                        'JenisAnggota_id' => $jenisId
                    ];

                    if ($jenisId == '12') {
                        $data['Fakultas_id'] = $fakultasId;
                        $data['Jurusan_id'] = $jurusanId;
                    }

                    $this->anggotaModel->update($id_anggota, $data);
                }
            }

            $db->transComplete();

            if ($db->transStatus() !== false) {
                session()->setFlashdata('swal_icon', 'success');
                session()->setFlashdata('swal_title', 'Berhasil');
                session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil disimpan');
                return redirect()->to('/perpanjangan-anggota');
            } else {
                session()->setFlashdata('swal_icon', 'error');
                session()->setFlashdata('swal_title', 'Gagal');
                session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal disimpan');
                echo view('PerpanjanganAnggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('perpanjangan-anggota/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('PerpanjanganAnggota\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah PerpanjanganAnggota';
        $perpanjangananggota = $this->anggotaModel->find($id);
        $this->data['perpanjangananggota'] = $perpanjangananggota;

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $slug,
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user_id(),
                ];

                $perpanjanganUpdate = $this->anggotaModel->update($id, $update_data);

                if ($perpanjanganUpdate) {
                    session()->setFlashdata('swal_icon', 'success');
                    session()->setFlashdata('swal_title', 'Berhasil');
                    session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil diubah');
                    return redirect()->to('/perpanjangan-anggota');
                } else {
                    session()->setFlashdata('swal_icon', 'error');
                    session()->setFlashdata('swal_title', 'Gagal');
                    session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal diubah');
                    return redirect()->to('/perpanjangan-anggota/edit/' . $id);
                }
            }
        }


        $this->data['redirect'] = base_url('perpanjangan-anggota/edit/' . $id);
        echo view('PerpanjanganAnggota\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'ID tidak ditemukan');
            return redirect()->to('/perpanjangan-anggota');
        }
        $perpanjanganDelete = $this->perpanjanganModel->delete($id);
        if ($perpanjanganDelete) {
            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Berhasil');
            session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil dihapus');
        } else {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal dihapus');
        }
        return redirect()->to('/perpanjangan-anggota');
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $perpanjanganUpdate = $this->anggotaModel->update($id, array($field => $value));

        if ($perpanjanganUpdate) {
            set_message('toastr_msg', ' PerpanjanganAnggota berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' PerpanjanganAnggota gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/perpanjangan-anggota');
    }
}