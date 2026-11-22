<?php

namespace Anggota\Controllers;

use chillerlan\QRCode\{QRCode, QROptions};
use Dompdf\Dompdf;

/**
 * AnggotaPrintController
 *
 * Menangani: cetak kartu anggota, kartu belakang, multiple print,
 * bebas pustaka, dan upload background kartu.
 */
class AnggotaPrintController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    // ----------------------------------------------------------------
    // CETAK KARTU ANGGOTA (SINGLE)
    // ----------------------------------------------------------------

    public function printanggota($id = null)
    {
        $db = db_connect();

        $anggota = $db->table('members m')->select('m.*')->where('m.id', $id)->get()->getRow();
        if (!$anggota) {
            throw new \Exception('Data anggota tidak ditemukan');
        }
        $this->data['anggota'] = $anggota;

        // Orientasi kartu: dari URL bila dikirim, jika tidak pakai orientasi tersimpan (KartuDepanWarna)
        $orientation = strtolower((string) ($this->request->getGet('orientation') ?: ($this->cardFrontColors()['orientation'] ?? 'landscape')));
        $this->data['orientation'] = $orientation === 'portrait' ? 'portrait' : 'landscape';
        // Opsi tampilan: dari URL bila dikirim, jika tidak pakai pilihan tersimpan (KartuDepanWarna)
        $this->data = array_merge($this->data, $this->cardDisplayOptions(fn ($key) => $this->request->getGet($key)));
        $npp = $db->table('settingparameters')->where('Name', 'NPPPerpustakaan')->get()->getRow();
        $this->data['npp'] = $npp->Value ?? '';
        $this->data['perpus_name'] = $db
            ->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Nasional';

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64 = '';
        if ($logo_setting && $logo_setting->Value) {
            $logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
            if (file_exists($logo_path)) {
                $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
            }
        }
        $this->data['logo_base64'] = $logo_base64 ?: 'https://placehold.co/80x80/cccccc/666666?text=LOGO';

        $photo_base64 = '';
        if ($anggota->PhotoUrl) {
            $photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
            if (file_exists($photo_path)) {
                $photo_base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photo_path));
            }
        }
        $this->data['photo_base64'] = $photo_base64;

        $options = new QROptions(['scale' => 7, 'imageBase64' => true]);
        $qrcode = new QRCode($options);
        $this->data['qr_image'] = $qrcode->render($anggota->MemberNo);

        $this->data['end_date'] = $this->formatTanggalIndo($anggota->EndDate);

        $jenis_anggota = $this->jenisanggotaModel->find($anggota->JenisAnggota_id);
        $this->data['jenis_anggota_nama'] = $jenis_anggota ? $jenis_anggota->jenisanggota : 'UMUM';

        $background_image_filename = $db
            ->table('settingparameters')
            ->where('Name', 'FileKartuAnggota')
            ->get()
            ->getRow()
            ->Value ?? null;

        $backgroundStyle = '';
        if (!empty($background_image_filename)) {
            $bgPath = ROOTPATH . 'public/uploads/master-template/' . $background_image_filename;
            if (file_exists($bgPath)) {
                $ext = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                $mime = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;
                $bgB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($bgPath));
                $backgroundStyle = "background: url('{$bgB64}') no-repeat center center / cover;";
            }
        }
        $this->data['backgroundStyle'] = $backgroundStyle;
        $this->data['can_send_email'] = is_allowed('anggota/kirim_kartu_digital', null, false);
        $this->data['card_layout'] = $this->cardLayout();
        $this->data['front_colors'] = $this->cardFrontColors();
        $this->data['can_edit_layout'] = is_allowed('anggota/uploadbackground', null, false);

        echo view('Anggota\Views\pdf\pdf1', $this->data);
    }

    /**
     * Kirim kartu anggota ke email sebagai lampiran PDF.
     * PDF dibuat di halaman preview (html2canvas + jsPDF) supaya hasilnya sama persis
     * dengan tampilan yang sedang diatur petugas, lalu dikirim ke sini sebagai base64.
     */
    public function kirimkartu($id = null)
    {
        if (!is_allowed('anggota/kirim_kartu_digital', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengirim kartu ke email.']);
        }

        $anggota = db_connect()->table('members')->select('ID, MemberNo, Fullname, Email')->where('ID', (int) $id)->get()->getRow();
        if (!$anggota) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data anggota tidak ditemukan.']);
        }

        $input = $this->request->getJSON(true) ?? [];
        $email = trim((string) ($input['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Alamat email tidak valid.']);
        }

        // Batasi ukuran (±6 MB PDF) dan pastikan isinya benar-benar PDF
        $encoded = (string) ($input['pdf'] ?? '');
        $encoded = preg_replace('#^data:application/pdf;[^,]*,#', '', $encoded);
        $pdf = strlen($encoded) <= 8 * 1024 * 1024 ? base64_decode($encoded, true) : false;
        if ($pdf === false || strncmp($pdf, '%PDF-', 5) !== 0) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'File kartu tidak valid atau terlalu besar.']);
        }

        $perpusName = db_connect()->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? 'Perpustakaan';
        $result = (new \App\Libraries\EmailNotificationLibrary())->sendMemberCardPdf($anggota, $email, $pdf, $perpusName);

        return $this->response->setStatusCode($result['success'] ? 200 : 500)->setJSON($result);
    }

    private function formatTanggalIndo($tanggal)
    {
        if (empty($tanggal) || strtotime($tanggal) === false) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $ts = strtotime($tanggal);

        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }

    // ----------------------------------------------------------------
    // CETAK KARTU BELAKANG
    // ----------------------------------------------------------------

    public function printkartubelakang($id = null)
    {
        $db = db_connect();

        // Orientasi kartu: dari URL bila dikirim, jika tidak pakai orientasi tersimpan (KartuBelakangWarna)
        $orientation = strtolower((string) ($this->request->getGet('orientation') ?: ($this->cardBackColors()['orientation'] ?? 'landscape')));
        $this->data['orientation'] = $orientation === 'portrait' ? 'portrait' : 'landscape';

        $this->data['perpus_name'] = $db
            ->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Nasional';

        $this->data['lokasi_perpustakaan'] = $db
            ->table('settingparameters')
            ->where('Name', 'NamaLokasiPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Nasional';

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64 = '';
        if ($logo_setting && $logo_setting->Value) {
            $logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
            if (file_exists($logo_path)) {
                $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
            }
        }
        $this->data['logo_base64'] = $logo_base64 ?: 'https://placehold.co/80x80/cccccc/666666?text=LOGO';

        $this->data['back_colors'] = $this->cardBackColors();
        $this->data['back_bg_b64'] = $this->backBackgroundBase64();
        $this->data['can_edit_layout'] = is_allowed('anggota/uploadbackground', null, false);

        echo view('Anggota\Views\pdf\cetak-kartubelakang', $this->data);
    }

    // ----------------------------------------------------------------
    // CETAK KARTU MULTIPLE
    // ----------------------------------------------------------------

    public function multipleprint()
    {
        $member_ids = $this->request->getPost('member_ids');

        if (!$member_ids || !is_array($member_ids)) {
            throw new \Exception('ID anggota tidak valid');
        }

        // Orientasi kartu: 'landscape' (default) atau 'portrait'
        $orientation = strtolower((string) $this->request->getPost('orientation'));
        $this->data['orientation'] = $orientation === 'portrait' ? 'portrait' : 'landscape';
        // Opsi tampilan: dari POST bila dikirim, jika tidak pakai pilihan tersimpan (KartuDepanWarna)
        $this->data = array_merge($this->data, $this->cardDisplayOptions(fn ($key) => $this->request->getPost($key)));
        $this->data['show_expiry'] = $this->request->getPost('show_expiry') !== '0';
        $db = db_connect();

        $perpus_name = $db
            ->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Nasional';
        $npp = $db->table('settingparameters')->where('Name', 'NPPPerpustakaan')->get()->getRow();
        $this->data['npp'] = $npp->Value ?? '';
        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64 = '';
        if ($logo_setting && $logo_setting->Value) {
            $logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
            if (file_exists($logo_path)) {
                $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
            }
        }
        $logo_base64 = $logo_base64 ?: 'https://placehold.co/80x80/cccccc/666666?text=LOGO';

        $members_data = [];

        foreach ($member_ids as $member_id) {
            $anggota = $db->table('members m')->select('m.*')->where('m.id', $member_id)->get()->getRow();
            if (!$anggota)
                continue;

            $photo_base64 = '';
            if ($anggota->PhotoUrl) {
                $photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
                if (file_exists($photo_path)) {
                    $photo_base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photo_path));
                }
            }
            $photo_src = $photo_base64 ?: 'https://placehold.co/250x280/cccccc/666666?text=FOTO';

            $options = new QROptions(['scale' => 7, 'imageBase64' => true]);
            $qrcode = new QRCode($options);
            $qr_image = $qrcode->render($anggota->MemberNo);

            $jenis_anggota_data = $db
                ->table('jenis_anggota')
                ->select('jenisanggota')
                ->where('id', $anggota->JenisAnggota_id)
                ->get()
                ->getRow();

            $members_data[] = [
                'anggota' => $anggota,
                'photo_base64' => $photo_src,
                'qr_image' => $qr_image,
                'end_date' => $this->formatTanggalIndo($anggota->EndDate),
                'jenis_anggota_nama' => $jenis_anggota_data ? $jenis_anggota_data->jenisanggota : 'UMUM',
            ];
        }

        $bg_b64 = '';
        $bg_setting = $db->table('settingparameters')->where('Name', 'FileKartuAnggota')->get()->getRow();
        if ($bg_setting && $bg_setting->Value) {
            $bgPath = ROOTPATH . 'public/uploads/master-template/' . $bg_setting->Value;
            if (file_exists($bgPath)) {
                $ext = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                $mime = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;
                $bg_b64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($bgPath));
            }
        }

        $this->data['members_data'] = $members_data;
        $this->data['perpus_name'] = $perpus_name;
        $this->data['logo_base64'] = $logo_base64;
        $this->data['bg_b64'] = $bg_b64;
        $this->data['title'] = 'Cetak Kartu Anggota - Multiple';
        $this->data['can_send_email'] = is_allowed('anggota/kirim_kartu_digital', null, false);
        $this->data['card_layout'] = $this->cardLayout();
        $this->data['front_colors'] = $this->cardFrontColors();
        $this->data['can_edit_layout'] = is_allowed('anggota/uploadbackground', null, false);
        $this->data['back_colors'] = $this->cardBackColors();
        $this->data['back_bg_b64'] = $this->backBackgroundBase64();

        return view('Anggota\Views\pdf\multiple-pdf1', $this->data);
    }

    // ----------------------------------------------------------------
    // BEBAS PUSTAKA
    // ----------------------------------------------------------------

    public function bebaspustaka(int $id = null)
    {
        $db = db_connect();
        $anggota = $this->anggotaModel->find($id);

        $jenis_perpustakaan = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? 'UMUM';
        $nama_perpustakaan = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan';

        $kelas_nama = null;
        $fakultas_nama = null;
        $jurusan_nama = null;

        if (strtoupper($jenis_perpustakaan) === 'SEKOLAH') {
            if (!empty($anggota->Kelas_id)) {
                $kelas = $db->table('kelas_siswa')->where('id', $anggota->Kelas_id)->get()->getRow();
                $kelas_nama = $kelas->namakelassiswa ?? null;
            }
        } elseif (strtoupper($jenis_perpustakaan) === 'PERGURUAN TINGGI') {
            if (!empty($anggota->Fakultas_id)) {
                $fakultas = $db->table('master_fakultas')->where('id', $anggota->Fakultas_id)->get()->getRow();
                $fakultas_nama = $fakultas->Nama ?? null;
            }
            if (!empty($anggota->Jurusan_id)) {
                $jurusan = $db->table('master_jurusan')->where('id', $anggota->Jurusan_id)->get()->getRow();
                $jurusan_nama = $jurusan->Nama ?? null;
            }
        }

        $this->data['title'] = 'Bebas Pustaka';
        $this->data['anggota'] = $anggota;
        $this->data['jenis_perpustakaan'] = strtoupper($jenis_perpustakaan);
        $this->data['nama_perpustakaan'] = $nama_perpustakaan;
        $this->data['kelas_nama'] = $kelas_nama;
        $this->data['fakultas_nama'] = $fakultas_nama;
        $this->data['jurusan_nama'] = $jurusan_nama;

        $dompdf = new Dompdf();
        $html = view('Anggota\Views\bebas-pustaka', $this->data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream();
    }

    // ----------------------------------------------------------------
    // UPLOAD BACKGROUND KARTU
    // ----------------------------------------------------------------

    // ----------------------------------------------------------------
    // TATA LETAK KARTU (drag & drop, disimpan di settingparameters)
    // ----------------------------------------------------------------

    /** Nama parameter penyimpan posisi elemen kartu (JSON). */
    private const CARD_LAYOUT_SETTING = 'KartuAnggotaLayout';

    /** Elemen kartu depan yang boleh digeser. */
    private const CARD_LAYOUT_ITEMS = ['logo', 'libname', 'npp', 'fullname', 'code', 'type', 'memberno', 'expiry', 'photo'];

    /**
     * Posisi elemen kartu tersimpan: ['landscape-1' => ['logo' => [x, y], ...], ...].
     * x/y = pergeseran dari posisi bawaan dalam piksel desain kartu
     * (landscape lebar 1004, portrait lebar 620); elemen ke-3 opsional = skala ukuran.
     */
    private function cardLayout(): array
    {
        $row = db_connect()->table('settingparameters')->where('Name', self::CARD_LAYOUT_SETTING)->get()->getRow();
        $layout = $row ? json_decode((string) $row->Value, true) : null;
        return is_array($layout) ? $layout : [];
    }

    /**
     * Simpan posisi elemen untuk satu kombinasi orientasi & template.
     * Body JSON: {"key": "landscape-1", "positions": {"logo": [12, -4, 1.2], ...}}; positions kosong = reset.
     */
    public function saveCardLayout()
    {
        if (!is_allowed('anggota/uploadbackground', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengubah desain kartu.']);
        }

        $input = $this->request->getJSON(true) ?? [];
        $key = (string) ($input['key'] ?? '');
        if (!preg_match('/^(landscape|portrait)-(1|2)$/D', $key)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Orientasi/template tidak valid.']);
        }

        $positions = [];
        foreach ((array) ($input['positions'] ?? []) as $item => $point) {
            // [x, y] atau [x, y, skala]; skala 0.3-3 (bawaan 1)
            if (!in_array($item, self::CARD_LAYOUT_ITEMS, true) || !is_array($point) || !in_array(count($point), [2, 3], true)
                || !is_numeric($point[0] ?? null) || !is_numeric($point[1] ?? null)
                || (isset($point[2]) && !is_numeric($point[2]))) {
                continue;
            }
            [$x, $y] = [round((float) $point[0], 1), round((float) $point[1], 1)];
            $scale = isset($point[2]) ? round((float) $point[2], 2) : 1.0;
            if (abs($x) > 2000 || abs($y) > 2000 || $scale < 0.3 || $scale > 3) {
                continue;
            }
            if ($scale != 1) {
                $positions[$item] = [$x, $y, $scale];
            } elseif ($x != 0 || $y != 0) {
                $positions[$item] = [$x, $y];
            }
        }

        $layout = $this->cardLayout();
        if ($positions) {
            $layout[$key] = $positions;
        } else {
            unset($layout[$key]);
        }

        $db = db_connect();
        $value = json_encode($layout, JSON_UNESCAPED_SLASHES);
        $audit = ['UpdateBy' => session()->get('logged_in') ?: null, 'UpdateDate' => date('Y-m-d H:i:s'), 'UpdateTerminal' => $this->request->getIPAddress()];
        $row = $db->table('settingparameters')->where('Name', self::CARD_LAYOUT_SETTING)->get()->getRow();
        if ($row) {
            $db->table('settingparameters')->where('ID', $row->ID)->update(['Value' => $value] + $audit);
        } else {
            $db->table('settingparameters')->insert([
                'Name' => self::CARD_LAYOUT_SETTING, 'Value' => $value,
                'CreateBy' => $audit['UpdateBy'], 'CreateDate' => $audit['UpdateDate'], 'CreateTerminal' => $audit['UpdateTerminal'],
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $positions ? 'Posisi elemen kartu berhasil disimpan.' : 'Posisi elemen kartu dikembalikan ke bawaan.',
            'layout' => $layout,
        ]);
    }

    // ----------------------------------------------------------------
    // WARNA KARTU BELAKANG (disimpan di settingparameters)
    // ----------------------------------------------------------------

    private const CARD_BACK_COLORS_SETTING = 'KartuBelakangWarna';

    /**
     * Warna kartu belakang tersimpan, atau [] bila belum pernah diatur.
     * ['background' => '#rrggbb' | 'linear-gradient(...)', 'overlayColor' => '#rrggbb',
     *  'overlayOpacity' => 0-100, 'textColor' => '#rrggbb']
     */
    private function cardBackColors(): array
    {
        $row = db_connect()->table('settingparameters')->where('Name', self::CARD_BACK_COLORS_SETTING)->get()->getRow();
        $colors = $row ? json_decode((string) $row->Value, true) : null;
        return is_array($colors) ? $colors : [];
    }

    public function saveCardBackColors()
    {
        if (!is_allowed('anggota/uploadbackground', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengubah desain kartu.']);
        }

        $input = $this->request->getJSON(true) ?? [];
        $hex = '/^#[0-9a-f]{6}$/iD';
        // Background: warna polos atau gradient preset (hanya warna hex & persen, tanpa url())
        $gradient = '/^linear-gradient\(\d{1,3}deg(, #[0-9a-f]{6}( \d{1,3}%)?){2,5}\)$/iD';
        $background = (string) ($input['background'] ?? '');
        $opacity = $input['overlayOpacity'] ?? null;

        if ((!preg_match($hex, $background) && !preg_match($gradient, $background))
            || !preg_match($hex, (string) ($input['overlayColor'] ?? ''))
            || !preg_match($hex, (string) ($input['textColor'] ?? ''))
            || !is_numeric($opacity) || $opacity < 0 || $opacity > 100
            || !in_array($input['orientation'] ?? 'landscape', ['landscape', 'portrait'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Data warna tidak valid.']);
        }

        $value = json_encode([
            'background' => $background,
            'overlayColor' => strtolower($input['overlayColor']),
            'overlayOpacity' => (int) $opacity,
            'textColor' => strtolower($input['textColor']),
            'orientation' => $input['orientation'] ?? 'landscape',
        ], JSON_UNESCAPED_SLASHES);

        $db = db_connect();
        $audit = ['UpdateBy' => session()->get('logged_in') ?: null, 'UpdateDate' => date('Y-m-d H:i:s'), 'UpdateTerminal' => $this->request->getIPAddress()];
        $row = $db->table('settingparameters')->where('Name', self::CARD_BACK_COLORS_SETTING)->get()->getRow();
        if ($row) {
            $db->table('settingparameters')->where('ID', $row->ID)->update(['Value' => $value] + $audit);
        } else {
            $db->table('settingparameters')->insert([
                'Name' => self::CARD_BACK_COLORS_SETTING, 'Value' => $value,
                'CreateBy' => $audit['UpdateBy'], 'CreateDate' => $audit['UpdateDate'], 'CreateTerminal' => $audit['UpdateTerminal'],
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Pengaturan kartu belakang tersimpan.']);
    }

    // ----------------------------------------------------------------
    // GAMBAR BACKGROUND KARTU BELAKANG (file di uploads/master-template,
    // nama file di settingparameters.FileKartuAnggotaBelakang)
    // ----------------------------------------------------------------

    private const CARD_BACK_BG_SETTING = 'FileKartuAnggotaBelakang';

    private function masterTemplatePath(): string
    {
        return rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'master-template' . DIRECTORY_SEPARATOR;
    }

    /** Gambar background kartu belakang tersimpan sebagai data URI, atau '' bila tidak ada. */
    private function backBackgroundBase64(): string
    {
        $row = db_connect()->table('settingparameters')->where('Name', self::CARD_BACK_BG_SETTING)->get()->getRow();
        if (!$row || !$row->Value) {
            return '';
        }
        $path = $this->masterTemplatePath() . basename($row->Value);
        if (!is_file($path)) {
            return '';
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = in_array($ext, ['jpg', 'jpeg'], true) ? 'image/jpeg' : 'image/' . $ext;
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    /** Ganti nama file di settingparameters dan hapus file lama ($newName null = hapus). */
    private function replaceBackBackground(?string $newName): void
    {
        $db = db_connect();
        $audit = ['UpdateBy' => session()->get('logged_in') ?: null, 'UpdateDate' => date('Y-m-d H:i:s'), 'UpdateTerminal' => $this->request->getIPAddress()];
        $row = $db->table('settingparameters')->where('Name', self::CARD_BACK_BG_SETTING)->get()->getRow();
        if ($row) {
            if ($row->Value && is_file($this->masterTemplatePath() . basename($row->Value))) {
                unlink($this->masterTemplatePath() . basename($row->Value));
            }
            $db->table('settingparameters')->where('ID', $row->ID)->update(['Value' => $newName] + $audit);
        } elseif ($newName !== null) {
            $db->table('settingparameters')->insert([
                'Name' => self::CARD_BACK_BG_SETTING, 'Value' => $newName,
                'CreateBy' => $audit['UpdateBy'], 'CreateDate' => $audit['UpdateDate'], 'CreateTerminal' => $audit['UpdateTerminal'],
            ]);
        }
    }

    public function uploadBackBackground()
    {
        if (!is_allowed('anggota/uploadbackground', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengubah desain kartu.']);
        }

        if (!$this->validate(['bgImage' => [
            'label' => 'Background Image',
            'rules' => [
                'uploaded[bgImage]',
                'is_image[bgImage]',
                'mime_in[bgImage,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
                'max_size[bgImage,10240]',
            ],
        ]])) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => implode(' ', $this->validator->getErrors())]);
        }

        $img = $this->request->getFile('bgImage');
        if (!$img || !$img->isValid() || $img->hasMoved()) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal memproses file yang diunggah.']);
        }

        try {
            if (!is_dir($this->masterTemplatePath())) {
                mkdir($this->masterTemplatePath(), 0755, true);
            }
            $newName = 'belakang_' . $img->getRandomName();
            $img->move($this->masterTemplatePath(), $newName);
            $this->replaceBackBackground($newName);
        } catch (\Throwable $e) {
            log_message('error', 'Upload background kartu belakang gagal: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menyimpan gambar background.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Gambar background kartu belakang tersimpan.']);
    }

    public function removeBackBackground()
    {
        if (!is_allowed('anggota/uploadbackground', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengubah desain kartu.']);
        }
        $this->replaceBackBackground(null);
        return $this->response->setJSON(['success' => true, 'message' => 'Gambar background kartu belakang dihapus.']);
    }

    // ----------------------------------------------------------------
    // WARNA KARTU DEPAN (disimpan di settingparameters.KartuDepanWarna)
    // ----------------------------------------------------------------

    private const CARD_FRONT_COLORS_SETTING = 'KartuDepanWarna';

    /**
     * ['source' => 'color'|'image', 'background' => '#hex'|'linear-gradient(...)',
     *  'overlayColor', 'overlayOpacity', 'panelOn', 'panelColor', 'panelOpacity', 'panelBlur']
     * source 'image' = pakai gambar FileKartuAnggota; 'color' = pakai background warna.
     */
    private function cardFrontColors(): array
    {
        $row = db_connect()->table('settingparameters')->where('Name', self::CARD_FRONT_COLORS_SETTING)->get()->getRow();
        $colors = $row ? json_decode((string) $row->Value, true) : null;
        return is_array($colors) ? $colors : [];
    }

    public function saveCardFrontColors()
    {
        if (!is_allowed('anggota/uploadbackground', null, false)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki izin mengubah desain kartu.']);
        }

        $in = $this->request->getJSON(true) ?? [];
        $hex = '/^#[0-9a-f]{6}$/iD';
        $gradient = '/^linear-gradient\(\d{1,3}deg(, #[0-9a-f]{6}( \d{1,3}%)?){2,5}\)$/iD';
        $percent = static fn ($v, $max = 100) => is_numeric($v) && $v >= 0 && $v <= $max;
        $background = (string) ($in['background'] ?? '');

        if (!in_array($in['source'] ?? '', ['color', 'image'], true)
            || (!preg_match($hex, $background) && !preg_match($gradient, $background))
            || !preg_match($hex, (string) ($in['overlayColor'] ?? '')) || !$percent($in['overlayOpacity'] ?? null)
            || !preg_match($hex, (string) ($in['panelColor'] ?? '')) || !$percent($in['panelOpacity'] ?? null)
            || !$percent($in['panelBlur'] ?? null, 20)
            || !in_array((string) ($in['template'] ?? '1'), ['1', '2'], true)
            || !in_array($in['codeType'] ?? 'qr', ['qr', 'barcode'], true)
            || !in_array($in['orientation'] ?? 'landscape', ['landscape', 'portrait'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Data warna tidak valid.']);
        }

        $this->saveSetting(self::CARD_FRONT_COLORS_SETTING, json_encode([
            'source' => $in['source'],
            'background' => $background,
            'overlayColor' => strtolower($in['overlayColor']),
            'overlayOpacity' => (int) $in['overlayOpacity'],
            'panelOn' => !empty($in['panelOn']),
            'panelColor' => strtolower($in['panelColor']),
            'panelOpacity' => (int) $in['panelOpacity'],
            'panelBlur' => (int) $in['panelBlur'],
            // Opsi tampilan
            'template' => (string) ($in['template'] ?? '1'),
            'showLogo' => !array_key_exists('showLogo', $in) || !empty($in['showLogo']),
            'showName' => !array_key_exists('showName', $in) || !empty($in['showName']),
            'showNpp' => !array_key_exists('showNpp', $in) || !empty($in['showNpp']),
            'codeType' => $in['codeType'] ?? 'qr',
            'orientation' => $in['orientation'] ?? 'landscape',
        ], JSON_UNESCAPED_SLASHES));

        return $this->response->setJSON(['success' => true, 'message' => 'Pengaturan kartu depan tersimpan.']);
    }

    /**
     * Opsi tampilan kartu depan. Nilai dari request ($input) dipakai bila dikirim,
     * jika tidak memakai pilihan tersimpan di KartuDepanWarna, lalu bawaan.
     */
    private function cardDisplayOptions(callable $input): array
    {
        $saved = $this->cardFrontColors();
        $flag = static function (string $key, string $savedKey) use ($input, $saved): bool {
            $value = $input($key);
            return $value !== null ? $value !== '0' : (bool) ($saved[$savedKey] ?? true);
        };
        $template = (string) ($input('template') ?? ($saved['template'] ?? '1'));
        $codeType = strtolower((string) ($input('code_type') ?? ($saved['codeType'] ?? 'qr')));

        return [
            'template' => $template === '2' ? '2' : '1',
            'code_type' => $codeType === 'barcode' ? 'barcode' : 'qr',
            'show_logo' => $flag('show_logo', 'showLogo'),
            'show_name' => $flag('show_name', 'showName'),
            'show_npp' => $flag('show_npp', 'showNpp'),
        ];
    }

    /** Insert/update satu baris settingparameters berdasarkan Name, dengan kolom audit. */
    private function saveSetting(string $name, ?string $value): void
    {
        $db = db_connect();
        $audit = ['UpdateBy' => session()->get('logged_in') ?: null, 'UpdateDate' => date('Y-m-d H:i:s'), 'UpdateTerminal' => $this->request->getIPAddress()];
        $row = $db->table('settingparameters')->where('Name', $name)->get()->getRow();
        if ($row) {
            $db->table('settingparameters')->where('ID', $row->ID)->update(['Value' => $value] + $audit);
        } else {
            $db->table('settingparameters')->insert([
                'Name' => $name, 'Value' => $value,
                'CreateBy' => $audit['UpdateBy'], 'CreateDate' => $audit['UpdateDate'], 'CreateTerminal' => $audit['UpdateTerminal'],
            ]);
        }
    }

    public function uploadBackground()
    {
        if (empty(session()->get('logged_in'))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi login tidak valid.'])->setStatusCode(401);
        }

        $validationRule = [
            'bgImage' => [
                'label' => 'Background Image',
                'rules' => [
                    'uploaded[bgImage]',
                    'is_image[bgImage]',
                    'mime_in[bgImage,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[bgImage,10240]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $img = $this->request->getFile('bgImage');
        if (!$img || !$img->isValid() || $img->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file yang diunggah.']);
        }

        $newName = $img->getRandomName();
        $uploadPath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'master-template' . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        try {
            $img->move($uploadPath, $newName);

            $db = db_connect();
            $setting = $db->table('settingparameters')->where('Name', 'FileKartuAnggota')->get()->getRow();

            if ($setting) {
                if ($setting->Value && is_file($uploadPath . $setting->Value)) {
                    unlink($uploadPath . $setting->Value);
                }
                $db->table('settingparameters')->where('ID', $setting->ID)->update(['Value' => $newName]);
            } else {
                $db->table('settingparameters')->insert(['Name' => 'FileKartuAnggota', 'Value' => $newName]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Background kartu berhasil diupdate.',
                'file_url' => base_url('uploads/master-template/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
