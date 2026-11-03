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

    /** Slug yang boleh dipakai sebagai nama view section halaman online. */
    private const ONLINE_SLUGS = ['profile', 'loan', 'violation', 'reserve', 'request', 'extend'];

    public function online()
    {
        $slug = $this->request->getGet('slug');
        if (empty($slug)) {
            $slug = 'profile';
        }
        if (!in_array($slug, self::ONLINE_SLUGS, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->data['slug'] = $slug;

        $db = db_connect();

        $jenisperpustakaan = $db->table('settingparameters')->where('Name', 'JenisPerpustakaan')->get()->getRow()->Value ?: "UMUM";
        $member_no = user()->username;
        $member = get_member($member_no);

        if (empty($member)) {
            set_message('toastr_msg', 'Akun ini tidak terhubung dengan data anggota');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard/index');
        }

        $this->data['member_no'] = $member_no;
        $this->data['member'] = $member;

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
        $this->data['jenis_kelamin'] = array_column(
            $this->jenisKelaminModel->select('ID, Name')->findAll(),
            'Name',
            'ID'
        );
        $this->data['peminjaman'] = get_peminjaman($member->ID);
        $this->data['pelanggaran'] = get_pelanggaran($member->ID);
        $this->data['CreateBy'] = get_username($member->CreateBy ?? 0);
        $this->data['UpdateBy'] = get_username($member->UpdateBy ?? 0);

        return view('Anggota\Views\online\index', $this->data);
    }

    // ----------------------------------------------------------------
    // PERPANJANGAN MASA BERLAKU
    // ----------------------------------------------------------------

    public function extend($member_no = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $own_member_no = user()->username;
        if (empty($member_no)) {
            $member_no = $own_member_no;
        }

        // Anggota hanya boleh memperpanjang dirinya sendiri; anggota lain butuh hak kelola anggota
        if ($member_no !== $own_member_no && !$this->canManageMembers()) {
            set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
            set_message('toastr_type', 'error');
            return redirect()->back();
        }

        $member = get_member($member_no);
        if (empty($member)) {
            set_message('toastr_msg', 'Data anggota tidak ditemukan');
            set_message('toastr_type', 'error');
            return redirect()->back();
        }

        $jenis_anggota = get_ref_single('jenis_anggota', ['id' => $member->JenisAnggota_id], 'data');
        $masa_berlaku = (int) ($jenis_anggota->MasaBerlakuAnggota ?? 0) ?: 365;

        // Perpanjang dari EndDate lama; jika kosong/tidak valid mulai dari hari ini
        $start_time = !empty($member->EndDate) ? strtotime($member->EndDate) : false;
        if ($start_time === false) {
            $start_time = strtotime(date('Y-m-d') . ' 23:59:59');
        }
        $end_date = date('Y-m-d H:i:s', strtotime('+' . $masa_berlaku . ' days', $start_time));

        $updateAnggota = $this->anggotaModel->update($member->ID, ['EndDate' => $end_date]);

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
                'error' => true,
                'message' => 'Method tidak diizinkan',
                'csrf' => csrf_hash(),
            ]);
        }

        if (!$this->canManageMembers()) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => true,
                'message' => 'Maaf, Anda tidak memiliki akses',
                'csrf' => csrf_hash(),
            ]);
        }

        $memberIds = $this->request->getPost('member_ids');
        if (is_array($memberIds)) {
            $memberIds = array_values(array_filter(array_map('intval', $memberIds)));
        }

        if (empty($memberIds) || !is_array($memberIds)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Tidak ada anggota yang dipilih',
                'csrf' => csrf_hash(),
            ]);
        }

        $db = db_connect('default');

        $successCount = $failCount = 0;
        $errors = [];
        $activatedList = [];

        $members = $db->table('members')->whereIn('ID', $memberIds)->get()->getResult();

        if (empty($members)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Data anggota tidak ditemukan',
                'csrf' => csrf_hash(),
            ]);
        }

        $groupId = $this->getMemberGroupId($db);
        if (empty($groupId)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Grup anggota tidak tersedia',
                'csrf' => csrf_hash(),
            ]);
        }

        foreach ($members as $member) {
            if (empty($member->MemberNo)) {
                $failCount++;
                $errors[] = "No anggota " . $member->Fullname . " kosong";
                continue;
            }

            $existingUser = $db->table('users')->where('username', $member->MemberNo)->get()->getRow();
            $password = null;

            $db->transStart();
            if ($existingUser) {
                // Akun lama hanya diaktifkan; password yang sudah ada tidak diubah
                $userId = $existingUser->id;
                $db->table('users')->where('id', $userId)->update(['active' => 1]);
            } else {
                $password = $this->generatePassword();
                $db->table('users')->insert([
                    'username' => $member->MemberNo,
                    'email' => $member->Email ?? '',
                    'category' => 'anggota',
                    'password_hash' => $this->password->hash($password),
                    'anggota' => $member->ID,
                    'activate_hash' => bin2hex(random_bytes(16)),
                    'active' => 1,
                ]);
                $userId = $db->insertID();
            }

            $alreadyInGroup = $db->table('auth_groups_users')
                ->where('group_id', $groupId)->where('user_id', $userId)->get()->getRow();
            if (!$alreadyInGroup) {
                $db->table('auth_groups_users')->insert(['group_id' => $groupId, 'user_id' => $userId]);
            }
            // Satu koneksi agar status anggota ikut transaksi akun
            $db->table('members')->where('ID', $member->ID)
                ->update(['IsOnlineActive' => 1, 'UpdateDate' => date('Y-m-d H:i:s')]);
            $db->transComplete();

            if (!$db->transStatus()) {
                $failCount++;
                $errors[] = "Gagal aktivasi akun untuk " . $member->Fullname;
                continue;
            }

            $successCount++;
            $activatedList[] = ['name' => $member->Fullname, 'username' => $member->MemberNo, 'password' => $password];
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
            $message .= "<hr><p><strong>Info Akun (password hanya ditampilkan sekali, catat sekarang):</strong></p><ul>";
            foreach ($activatedList as $acc) {
                $passwordInfo = $acc['password'] !== null
                    ? "Password: <code>" . esc($acc['password']) . "</code>"
                    : "<em>akun sudah ada, password tidak diubah</em>";
                $message .= "<li><strong>" . esc($acc['name']) . "</strong> — Username: <code>" . esc($acc['username']) . "</code>, " . $passwordInfo . "</li>";
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
            'error' => false,
            'message' => $message,
            'data' => ['success' => $successCount, 'failed' => $failCount],
            'csrf' => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    /** Hak kelola anggota; bypass header AJAX sengaja dimatikan. */
    private function canManageMembers(): bool
    {
        return is_allowed('anggota/edit', null, false);
    }

    /** ID grup 'anggota' di auth_groups; dibuat jika belum ada (sama seperti pendaftaran online). */
    private function getMemberGroupId($db)
    {
        $group = $db->table('auth_groups')->where('name', 'anggota')->get()->getFirstRow();
        if ($group) {
            return $group->id;
        }

        $db->table('auth_groups')->insert([
            'name' => 'anggota',
            'description' => 'Anggota perpustakaan',
        ]);

        return $db->insertID();
    }

    /** Password awal acak, tanpa karakter yang mirip (0/O, 1/l/I). */
    private function generatePassword(int $length = 10): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
