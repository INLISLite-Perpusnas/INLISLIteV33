<?php

namespace Anggota\Controllers;

use Base\Models\BaseModel;
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
        $templateModel = new BaseModel('t_template');
        $template = $templateModel->where('active', 1)->first();

        if (empty($template)) {
            echo "Tidak ada template untuk di cetak";
            exit;
        }

        $bg = file_get_contents(ROOTPATH . 'public/uploads/master-template/' . $template->file_image);
        $this->data['bg_base64'] = 'data:image/png;base64,' . base64_encode($bg);

        $db = db_connect();

        $anggota = $db->table('members m')->select('m.*')->where('m.id', $id)->get()->getRow();
        if (!$anggota) {
            throw new \Exception('Data anggota tidak ditemukan');
        }
        $this->data['anggota'] = $anggota;

        $this->data['perpus_name'] = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64  = '';
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

        $options  = new QROptions(['scale' => 7, 'imageBase64' => true]);
        $qrcode   = new QRCode($options);
        $this->data['qr_image'] = $qrcode->render($anggota->MemberNo);

        $this->data['end_date'] = date('d F Y', strtotime($anggota->EndDate));

        $jenis_anggota = $this->jenisanggotaModel->find($anggota->JenisAnggota_id);
        $this->data['jenis_anggota_nama'] = $jenis_anggota ? $jenis_anggota->jenisanggota : 'UMUM';

        $background_image_filename = $db->table('settingparameters')
            ->where('Name', 'FileKartuAnggota')->get()->getRow()->Value ?? null;

        $backgroundStyle = '';
        if (!empty($background_image_filename)) {
            $bgPath = ROOTPATH . 'public/uploads/master-template/' . $background_image_filename;
            if (file_exists($bgPath)) {
                $ext   = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                $mime  = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;
                $bgB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($bgPath));
                $backgroundStyle = "background: url('{$bgB64}') no-repeat center center / cover;";
            }
        }
        $this->data['backgroundStyle'] = $backgroundStyle;

        echo view('Anggota\Views\pdf\pdf1', $this->data);
    }

    // ----------------------------------------------------------------
    // CETAK KARTU BELAKANG
    // ----------------------------------------------------------------

    public function printkartubelakang($id = null)
    {
        $db = db_connect();

        $this->data['perpus_name'] = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $this->data['lokasi_perpustakaan'] = $db->table('settingparameters')
            ->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64  = '';
        if ($logo_setting && $logo_setting->Value) {
            $logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
            if (file_exists($logo_path)) {
                $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
            }
        }
        $this->data['logo_base64'] = $logo_base64 ?: 'https://placehold.co/80x80/cccccc/666666?text=LOGO';

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

        $db = db_connect();

        $perpus_name = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64  = '';
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
            if (!$anggota) continue;

            $photo_base64 = '';
            if ($anggota->PhotoUrl) {
                $photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
                if (file_exists($photo_path)) {
                    $photo_base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photo_path));
                }
            }
            $photo_src = $photo_base64 ?: 'https://placehold.co/250x280/cccccc/666666?text=FOTO';

            $options = new QROptions(['scale' => 7, 'imageBase64' => true]);
            $qrcode  = new QRCode($options);
            $qr_image = $qrcode->render($anggota->MemberNo);

            $jenis_anggota_data = $db->table('jenis_anggota')
                ->select('jenisanggota')->where('id', $anggota->JenisAnggota_id)->get()->getRow();

            $members_data[] = [
                'anggota'          => $anggota,
                'photo_base64'     => $photo_src,
                'qr_image'         => $qr_image,
                'end_date'         => date('d F Y', strtotime($anggota->EndDate)),
                'jenis_anggota_nama' => $jenis_anggota_data ? $jenis_anggota_data->jenisanggota : 'UMUM',
            ];
        }

        $this->data['members_data'] = $members_data;
        $this->data['perpus_name']  = $perpus_name;
        $this->data['logo_base64']  = $logo_base64;
        $this->data['title']        = 'Cetak Kartu Anggota - Multiple';

        return view('Anggota\Views\pdf\multiple-pdf1', $this->data);
    }

    // ----------------------------------------------------------------
    // BEBAS PUSTAKA
    // ----------------------------------------------------------------

    public function bebaspustaka(int $id = null)
    {
        $db      = db_connect();
        $anggota = $this->anggotaModel->find($id);

        $jenis_perpustakaan = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? 'UMUM';
        $nama_perpustakaan  = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan';

        $kelas_nama    = null;
        $fakultas_nama = null;
        $jurusan_nama  = null;

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

        $this->data['title']              = 'Bebas Pustaka';
        $this->data['anggota']            = $anggota;
        $this->data['jenis_perpustakaan'] = strtoupper($jenis_perpustakaan);
        $this->data['nama_perpustakaan']  = $nama_perpustakaan;
        $this->data['kelas_nama']         = $kelas_nama;
        $this->data['fakultas_nama']      = $fakultas_nama;
        $this->data['jurusan_nama']       = $jurusan_nama;

        $dompdf = new Dompdf();
        $html   = view('Anggota\Views\bebas-pustaka', $this->data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream();
    }

    // ----------------------------------------------------------------
    // UPLOAD BACKGROUND KARTU
    // ----------------------------------------------------------------

    public function uploadBackground()
    {
        $validationRule = [
            'bgImage' => [
                'label' => 'Background Image',
                'rules' => [
                    'uploaded[bgImage]',
                    'is_image[bgImage]',
                    'mime_in[bgImage,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[bgImage,2048]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $img = $this->request->getFile('bgImage');

        if ($img->isValid() && !$img->hasMoved()) {
            $newName    = $img->getRandomName();
            $uploadPath = ROOTPATH . 'public/uploads/master-template/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            try {
                $img->move($uploadPath, $newName);

                $db      = db_connect();
                $setting = $db->table('settingparameters')->where('Name', 'FileKartuAnggota')->get()->getRow();

                if ($setting) {
                    if ($setting->Value && file_exists($uploadPath . $setting->Value)) {
                        unlink($uploadPath . $setting->Value);
                    }
                    $db->table('settingparameters')->where('ID', $setting->ID)->update(['Value' => $newName]);
                } else {
                    $db->table('settingparameters')->insert(['Name' => 'FileKartuAnggota', 'Value' => $newName]);
                }

                return $this->response->setJSON([
                    'success'  => true,
                    'message'  => 'Background kartu berhasil diupdate.',
                    'file_url' => base_url('uploads/master-template/' . $newName),
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file yang diunggah.']);
    }
}
