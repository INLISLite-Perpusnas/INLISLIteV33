<?php

namespace Anggota\Controllers;

/**
 * AnggotaOnlineController
 *
 * Menangani: halaman online anggota, perpanjangan masa berlaku,
 * dan aktivasi akun online (bulk).
 */
class AnggotaOnlineController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    // ----------------------------------------------------------------
    // HALAMAN ONLINE ANGGOTA
    // ----------------------------------------------------------------

    public function online()
    {
        $slug = $this->request->getGet('slug');
        if (empty($slug)) {
            $slug = 'profile';
        }
        $this->data['slug'] = $slug;

        $db = db_connect();

        $jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";
        $member_no = user()->username;
        $member    = get_member($member_no);

        $this->data['member_no'] = $member_no;
        $this->data['member']    = $member;

        if ($jenisperpustakaan == "UMUM") {
            $this->data['jenis_perpustakaan_id'] = 1;
        } elseif ($jenisperpustakaan == "KHUSUS") {
            $this->data['jenis_perpustakaan_id'] = 2;
        } elseif ($jenisperpustakaan == "PERGURUAN TINGGI") {
            $this->data['jenis_perpustakaan_id'] = 3;
        } else {
            $this->data['jenis_perpustakaan_id'] = 4;
        }

        $hak_akses_koleksi = $this->AksesKoleksiModel->where('member_id', $member->ID)->findAll();
        $arr_hak_akses_koleksi = [];
        foreach ($hak_akses_koleksi as $row) {
            $arr_hak_akses_koleksi[] = $row->CategoryLoan_id;
        }
        $this->data['arr_hak_akses_koleksi'] = $arr_hak_akses_koleksi;

        $hak_akses_lokasi = $this->anggotahakaksesModel->where('Member_id', $member->ID)->findAll();
        $arr_hak_akses_lokasi = [];
        foreach ($hak_akses_lokasi as $row) {
            $arr_hak_akses_lokasi[] = $row->LocationLoan_id;
        }
        $this->data['arr_hak_akses_lokasi'] = $arr_hak_akses_lokasi;

        $this->data['peminjaman']  = get_peminjaman($member->ID);
        $this->data['pelanggaran'] = get_pelanggaran($member->ID);
        $this->data['CreateBy']    = get_username($member->CreateBy ?? 0);
        $this->data['UpdateBy']    = get_username($member->UpdateBy ?? 0);

        return view('Anggota\Views\online\index', $this->data);
    }

    // ----------------------------------------------------------------
    // PERPANJANGAN MASA BERLAKU
    // ----------------------------------------------------------------

    public function extend($member_no = null)
    {
        if (empty($member_no)) {
            $member_no = user()->username;
        }

        $member      = get_member($member_no);
        $jenis_anggota = db_get_single('m_jenis_anggota', 'id = ' . $member->ref_jenisanggota);
        $start_date  = $member->EndDate;
        $end_date    = date('Y-m-d', strtotime($start_date . ' + ' . $jenis_anggota->expiry_days . ' days'));

        $updateAnggota = $this->anggotaModel->protect(false)->update($member->id, ['EndDate' => $end_date]);

        if ($updateAnggota) {
            set_message('toastr_msg', 'Perpanjangan Masa Berlaku Anggota berhasil');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Perpanjangan Masa Berlaku Anggota gagal');
            set_message('toastr_type', 'error');
        }

        return redirect()->back();
    }

    // ----------------------------------------------------------------
    // AKTIVASI AKUN ONLINE (BULK)
    // ----------------------------------------------------------------

    public function aktifkan_online()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Method tidak diizinkan',
            ]);
        }

        $memberIds = $this->request->getPost('member_ids');

        if (empty($memberIds) || !is_array($memberIds)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Tidak ada anggota yang dipilih',
            ]);
        }

        $db          = db_connect('default');
        $memberModel = $this->anggotaModel;

        $successCount  = $failCount = 0;
        $errors        = [];
        $activatedList = [];

        $members = $memberModel->whereIn('ID', $memberIds)->findAll();

        if (empty($members)) {
            return $this->response->setJSON([
                'error'   => true,
                'message' => 'Data anggota tidak ditemukan',
            ]);
        }

        foreach ($members as $member) {
            if (empty($member->MemberNo)) {
                $failCount++;
                $errors[] = "No anggota " . esc($member->Fullname) . " kosong";
                continue;
            }

            $existingUser = $db->table('users')->where('username', $member->MemberNo)->get()->getRow();

            if ($existingUser) {
                $userId = $existingUser->id;
                $db->table('users')->where('id', $userId)->update(['active' => 1]);

                $alreadyInGroup = $db->table('auth_groups_users')
                    ->where('group_id', 8)->where('user_id', $userId)->get()->getRow();
                if (!$alreadyInGroup) {
                    $db->table('auth_groups_users')->insert(['group_id' => 8, 'user_id' => $userId]);
                }

                $memberModel->update($member->ID, ['IsOnlineActive' => 1]);
                $successCount++;
                $activatedList[] = ['name' => $member->Fullname, 'username' => $member->MemberNo];
                continue;
            }

            $userData = [
                'username'      => $member->MemberNo,
                'email'         => $member->Email ?? '',
                'password_hash' => $this->password->hash($member->MemberNo),
                'anggota'       => $member->ID,
                'activate_hash' => bin2hex(random_bytes(16)),
                'active'        => 1,
            ];

            if ($db->table('users')->insert($userData)) {
                $userId = $db->insertID();
                $db->table('auth_groups_users')->insert(['group_id' => 8, 'user_id' => $userId]);
                $memberModel->update($member->ID, ['IsOnlineActive' => 1]);
                $successCount++;
                $activatedList[] = ['name' => $member->Fullname, 'username' => $member->MemberNo];
            } else {
                $failCount++;
                $errors[] = "Gagal aktivasi akun untuk " . esc($member->Fullname);
            }
        }

        $message = "<div class='text-left'>
            <p><strong>Hasil Aktivasi Online:</strong></p>
            <ul>
                <li>✓ Berhasil: <strong>{$successCount}</strong> anggota</li>";
        if ($failCount > 0) {
            $message .= "<li>✗ Gagal: <strong>{$failCount}</strong> anggota</li>";
        }
        $message .= "</ul>";

        if (!empty($activatedList)) {
            $message .= "<hr><p><strong>Info Akun (Username & Password = No Anggota):</strong></p><ul>";
            foreach ($activatedList as $acc) {
                $message .= "<li><strong>" . esc($acc['name']) . "</strong> — Username: <code>" . esc($acc['username']) . "</code>, Password: <code>" . esc($acc['username']) . "</code></li>";
            }
            $message .= "</ul>";
        }

        if (!empty($errors)) {
            $message .= "<hr><p><strong>Detail Error:</strong></p><ul>";
            foreach ($errors as $err) {
                $message .= "<li class='text-danger small'>" . esc($err) . "</li>";
            }
            $message .= "</ul>";
        }

        $message .= "</div>";

        return $this->response->setJSON([
            'error'   => false,
            'message' => $message,
            'data'    => ['success' => $successCount, 'failed' => $failCount],
        ]);
    }
}
