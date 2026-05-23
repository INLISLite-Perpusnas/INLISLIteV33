<?php

namespace Anggota\Controllers;

use Base\Models\BaseModel;
use chillerlan\QRCode\{QRCode, QROptions};

/**
 * AnggotaKartuController
 *
 * Menangani cetak kartu anggota: depan, belakang, multiple,
 * upload background, bebas pustaka, dan kirim kartu digital via email.
 */
class AnggotaKartuController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    public function printanggota($id = null)
    {
        $templateModel = new BaseModel('t_template');
        $template      = $templateModel->where('active', 1)->first();

        if (empty($template)) {
            echo "Tidak ada template untuk di cetak";
            exit;
        }

        $bg        = file_get_contents(ROOTPATH . 'public/uploads/master-template/' . $template->file_image);
        $bg_base64 = 'data:image/png;base64,' . base64_encode($bg);
        $this->data['bg_base64'] = $bg_base64;

        $db      = db_connect();
        $anggota = $db->table('members m')->select('m.*')->where('m.id', $id)->get()->getRow();

        if (!$anggota) {
            throw new \Exception('Data anggota tidak ditemukan');
        }

        $this->data['anggota']    = $anggota;
        $this->data['perpus_name'] = $this->getPerpusName($db);
        $this->data['logo_base64'] = $this->getLogoBase64($db);

        $photo_base64 = '';
        if ($anggota->PhotoUrl) {
            $photo_path = ROOTPATH . 'public/uploads/anggota/' . $anggota->PhotoUrl;
            if (file_exists($photo_path)) {
                $photo_base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photo_path));
            }
        }
        $this->data['photo_base64'] = $photo_base64;

        $options  = new QROptions(['scale' => 7, 'imageBase64' => true]);
        $this->data['qr_image'] = (new QRCode($options))->render($anggota->MemberNo);

        $this->data['end_date'] = date('d F Y', strtotime($anggota->EndDate));

        $jenis_anggota = $this->jenisanggotaModel->find($anggota->JenisAnggota_id);
        $this->data['jenis_anggota_nama'] = $jenis_anggota ? $jenis_anggota->jenisanggota : 'UMUM';

        $background_image_filename = $db->table('settingparameters')->where('Name', 'KartuAnggota1')->get()->getRow()->Value ?? null;
        $this->data['backgroundStyle'] = '';
        if (!empty($background_image_filename)) {
            $imageUrl = base_url('uploads/card_backgrounds/' . $background_image_filename);
            $this->data['backgroundStyle'] = "background: url('{$imageUrl}') no-repeat center center / cover;";
        }

        echo view('Anggota\Views\pdf\pdf1', $this->data);
    }

    public function printkartubelakang($id = null)
    {
        $db = db_connect();

        $this->data['perpus_name']        = $this->getPerpusName($db);
        $this->data['lokasi_perpustakaan'] = $db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value ?? 'Perpustakaan Nasional';
        $this->data['logo_base64']         = $this->getLogoBase64($db);

        echo view('Anggota\Views\pdf\cetak-kartubelakang', $this->data);
    }

    public function multipleprint()
    {
        $member_ids = $this->request->getPost('member_ids');

        if (!$member_ids || !is_array($member_ids)) {
            throw new \Exception('ID anggota tidak valid');
        }

        $db          = db_connect();
        $perpus_name = $this->getPerpusName($db);
        $logo_base64 = $this->getLogoBase64($db);
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

            $options  = new QROptions(['scale' => 7, 'imageBase64' => true]);
            $qr_image = (new QRCode($options))->render($anggota->MemberNo);

            $jenis_row        = $db->table('jenis_anggota')->select('jenisanggota')->where('id', $anggota->JenisAnggota_id)->get()->getRow();
            $members_data[]   = [
                'anggota'           => $anggota,
                'photo_base64'      => $photo_base64 ?: 'https://placehold.co/250x280/cccccc/666666?text=FOTO',
                'qr_image'          => $qr_image,
                'end_date'          => date('d F Y', strtotime($anggota->EndDate)),
                'jenis_anggota_nama'=> $jenis_row ? $jenis_row->jenisanggota : 'UMUM',
            ];
        }

        $this->data['members_data'] = $members_data;
        $this->data['perpus_name']  = $perpus_name;
        $this->data['logo_base64']  = $logo_base64;
        $this->data['title']        = 'Cetak Kartu Anggota - Multiple';

        return view('Anggota\Views\pdf\multiple-pdf1', $this->data);
    }

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

        $img         = $this->request->getFile('bgImage');
        $settingName = 'KartuAnggota1';
        $setting     = $this->settingModel->where('Name', $settingName)->first();
        $oldFileName = $setting->Value ?? null;
        $newName     = $img->getRandomName();
        $uploadPath  = FCPATH . 'uploads/card_backgrounds/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $img->move($uploadPath, $newName);

        try {
            if ($oldFileName && file_exists($uploadPath . $oldFileName)) {
                unlink($uploadPath . $oldFileName);
            }

            if ($setting) {
                $this->settingModel->update($setting->ID, ['Value' => $newName]);
            } else {
                $obj        = new \stdClass();
                $obj->Name  = $settingName;
                $obj->Value = $newName;
                $this->settingModel->insert($obj);
            }

            return $this->response->setJSON([
                'success'  => true,
                'message'  => '✅ Background kartu berhasil diupdate.',
                'file_url' => base_url('uploads/card_backgrounds/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => '❌ Database error: ' . $e->getMessage()]);
        }
    }

    public function kirim_kartu_digital()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['error' => true, 'message' => 'Method tidak diizinkan']);
        }

        $memberIds = $this->request->getPost('member_ids');
        if (empty($memberIds) || !is_array($memberIds)) {
            return $this->response->setJSON(['error' => true, 'message' => 'Tidak ada anggota yang dipilih']);
        }

        $db          = db_connect();
        $perpusName  = $this->getPerpusName($db);
        $logoBase64  = $this->getLogoBase64($db);

        $members = $this->anggotaModel->whereIn('ID', $memberIds)->findAll();
        if (empty($members)) {
            return $this->response->setJSON(['error' => true, 'message' => 'Data anggota tidak ditemukan']);
        }

        $emailLib  = new \App\Libraries\EmailNotificationLibrary();
        $sentCount = 0;
        $failCount = 0;
        $skipCount = 0;
        $errors    = [];

        foreach ($members as $member) {
            if (empty(trim($member->Email ?? ''))) {
                $skipCount++;
                continue;
            }

            $photoSrc = 'https://placehold.co/250x280/cccccc/666666?text=FOTO';
            if (!empty($member->PhotoUrl)) {
                $photoPath = ROOTPATH . 'public/uploads/anggota/' . $member->PhotoUrl;
                if (file_exists($photoPath)) {
                    $photoSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoPath));
                }
            }

            $options  = new QROptions(['scale' => 7, 'imageBase64' => true]);
            $qrImage  = (new QRCode($options))->render($member->MemberNo);

            $jenisRow    = $db->table('jenis_anggota')->select('jenisanggota')->where('id', $member->JenisAnggota_id)->get()->getRow();
            $jenisAnggota = $jenisRow ? $jenisRow->jenisanggota : 'UMUM';

            $result = $emailLib->sendMemberCard($member, $qrImage, $photoSrc, $perpusName, $logoBase64, $jenisAnggota);

            if ($result['success']) {
                $sentCount++;
            } else {
                $failCount++;
                $errors[] = esc($member->Fullname) . ': ' . $result['message'];
            }
        }

        $message  = "<div class='text-left'><p><strong>Hasil Pengiriman Kartu Digital:</strong></p><ul>";
        $message .= "<li>✓ Terkirim: <strong>{$sentCount}</strong> anggota</li>";
        if ($failCount > 0) $message .= "<li>✗ Gagal: <strong>{$failCount}</strong> anggota</li>";
        if ($skipCount > 0) $message .= "<li>— Dilewati (tidak ada email): <strong>{$skipCount}</strong> anggota</li>";
        $message .= "</ul>";
        if (!empty($errors)) {
            $message .= "<hr><p><strong>Detail Error:</strong></p><ul>";
            foreach ($errors as $err) $message .= "<li class='text-danger small'>{$err}</li>";
            $message .= "</ul>";
        }
        $message .= "</div>";

        return $this->response->setJSON([
            'error'   => false,
            'message' => $message,
            'data'    => ['sent' => $sentCount, 'failed' => $failCount, 'skipped' => $skipCount],
        ]);
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPERS
    // ----------------------------------------------------------------

    private function getPerpusName($db): string
    {
        return $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? 'Perpustakaan Nasional';
    }

    private function getLogoBase64($db): string
    {
        $setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        if ($setting && $setting->Value) {
            $path = ROOTPATH . 'public/uploads/branch/' . $setting->Value;
            if (file_exists($path)) {
                return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            }
        }
        return 'https://placehold.co/80x80/cccccc/666666?text=LOGO';
    }
}
