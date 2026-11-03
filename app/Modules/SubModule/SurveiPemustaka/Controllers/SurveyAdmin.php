<?php

namespace SurveiPemustaka\Controllers;

/**
 * SurveyAdmin
 *
 * Pengelolaan survei pemustaka di back office. Alur dan tampilan mengikuti
 * modul survey INLISLite 3 (backend/modules/survey):
 *  - Daftar Survei (tambah, edit, hapus, daftar pertanyaan, modal hasil)
 *  - Daftar Pertanyaan per survei (tambah, edit, hapus, pilihan / isian)
 *  - Daftar Pilihan per pertanyaan + grafik pie dan batang
 *  - Daftar Isian per pertanyaan (hanya baca)
 */
class SurveyAdmin extends \Base\Controllers\BaseController
{
    private const JENIS_PERTANYAAN = ['Pilihan' => 'Pilihan Ganda', 'Isian' => 'Isian Bebas'];
    private const ORIENTATION = ['Vertikal' => 'Vertikal', 'Horisontal' => 'Horisontal'];
    private const TARGET_SURVEY = [1 => 'Anggota', 0 => 'Semua'];
    private const HASIL_SURVEY_SHOW = [1 => 'Back Office dan Module Survey', 0 => 'Back Office'];

    private function db()
    {
        return db_connect();
    }

    /** Header X-Requested-With tidak dipercaya untuk izin (lihat is_allowed). */
    private function allowed(string $permission): bool
    {
        return is_allowed('surveipemustaka/' . $permission, null, false);
    }

    private function denied()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('<center><h3>Akses ditolak</h3></center>');
        }
        set_message('toastr_msg', lang('App.permission.not.have'));
        set_message('toastr_type', 'error');
        return redirect()->to('/dashboard/index');
    }

    private function notFound(string $message, string $to = 'surveipemustaka')
    {
        set_message('toastr_msg', $message);
        set_message('toastr_type', 'error');
        return redirect()->to($to);
    }

    private function flash(string $message, string $type = 'success'): void
    {
        set_message('toastr_msg', $message);
        set_message('toastr_type', $type);
    }

    private function audit(bool $isNew): array
    {
        $prefix = $isNew ? 'Create' : 'Update';
        $data = [
            $prefix . 'By' => login_id() ?: null,
            $prefix . 'Date' => date('Y-m-d H:i:s'),
            $prefix . 'Terminal' => getClientIpAddress(),
        ];
        if ($isNew) {
            $data['Branch_id'] = branch_id() ?: null;
            $data['active'] = 1;
        }
        return $data;
    }

    /** Kolom bit(1) dibaca sebagai "\x01" oleh MySQLi. */
    public static function flag($value): bool
    {
        return $value === 1 || $value === '1' || $value === "\x01" || $value === true;
    }

    private function findSurvey(int $id): ?array
    {
        return $this->db()->table('survey')->where('ID', $id)->get()->getRowArray();
    }

    private function findPertanyaan(int $id): ?array
    {
        return $this->db()->table('survey_pertanyaan')->where('ID', $id)->get()->getRowArray();
    }

    private function findPilihan(int $id): ?array
    {
        return $this->db()->table('survey_pilihan')->where('ID', $id)->get()->getRowArray();
    }

    private function render(string $view, array $data)
    {
        return view('SurveiPemustaka\Views\\' . $view, $data + [
            'flag' => [self::class, 'flag'],
            'jenisPertanyaan' => self::JENIS_PERTANYAAN,
            'orientation' => self::ORIENTATION,
            'targetSurvey' => self::TARGET_SURVEY,
            'hasilSurveyShow' => self::HASIL_SURVEY_SHOW,
        ]);
    }

    // ----------------------------------------------------------------
    // SURVEI
    // ----------------------------------------------------------------

    public function index()
    {
        if (!$this->allowed('index')) return $this->denied();

        $db = $this->db();
        $surveys = $db->table('survey')->orderBy('NomorUrut')->orderBy('ID')->get()->getResultArray();

        // Jumlah responden = jumlah sesi unik yang memilih jawaban (sama seperti INLISLite 3)
        $responden = [];
        $rows = $db->table('survey_pilihan_sesi ps')
            ->select('q.Survey_id, COUNT(DISTINCT ps.Sesi) AS total')
            ->join('survey_pilihan p', 'p.ID = ps.Survey_Pilihan_id')
            ->join('survey_pertanyaan q', 'q.ID = p.Survey_Pertanyaan_id')
            ->groupBy('q.Survey_id')->get()->getResultArray();
        foreach ($rows as $row) $responden[$row['Survey_id']] = (int) $row['total'];

        return $this->render('index', [
            'title' => 'Survey',
            'surveys' => $surveys,
            'responden' => $responden,
        ]);
    }

    public function create()
    {
        if (!$this->allowed('create')) return $this->denied();
        return $this->surveyForm(null);
    }

    public function edit(int $id = 0)
    {
        if (!$this->allowed('edit')) return $this->denied();
        $survey = $this->findSurvey($id);
        if (!$survey) return $this->notFound('Survey tidak ditemukan');
        return $this->surveyForm($survey);
    }

    private function surveyForm(?array $survey)
    {
        $errors = [];
        if ($this->request->is('post')) {
            $tglMulai = trim((string) $this->request->getPost('TglMulai'));
            $tglSelesai = trim((string) $this->request->getPost('TglSelesai'));
            $mulai = \DateTime::createFromFormat('!d-m-Y', $tglMulai);
            $selesai = \DateTime::createFromFormat('!d-m-Y', $tglSelesai);
            $nama = trim((string) $this->request->getPost('NamaSurvey'));
            $keterangan = trim((string) $this->request->getPost('Keterangan'));

            if ($nama === '') $errors['NamaSurvey'] = 'Nama Survey tidak boleh kosong.';
            elseif (mb_strlen($nama) > 200) $errors['NamaSurvey'] = 'Nama Survey maksimal 200 karakter.';
            if (!$mulai || $mulai->format('d-m-Y') !== $tglMulai) $errors['TglMulai'] = 'Tgl.Mulai tidak valid.';
            if (!$selesai || $selesai->format('d-m-Y') !== $tglSelesai) $errors['TglSelesai'] = 'Tanggal Selesai tidak valid.';
            if (!$errors && $mulai >= $selesai) {
                $errors['TglMulai'] = 'Tgl Mulai harus lebih kecil dari Tgl Selesai';
                $errors['TglSelesai'] = 'Tgl Selesai harus lebih besar dari Tgl Mulai';
            }
            if (mb_strlen($keterangan) > 255) $errors['Keterangan'] = 'Keterangan maksimal 255 karakter.';

            if (!$errors) {
                $payload = [
                    'NamaSurvey' => $nama,
                    'TanggalMulai' => $mulai->format('Y-m-d 00:00:00'),
                    // Akhir hari agar survei masih tampil di hari terakhir
                    'TanggalSelesai' => $selesai->format('Y-m-d 23:59:59'),
                    'IsActive' => $this->request->getPost('IsActive') === '1' ? 1 : 0,
                    'NomorUrut' => $this->request->getPost('NomorUrut') === '' ? null : (int) $this->request->getPost('NomorUrut'),
                    'TargetSurvey' => $this->request->getPost('TargetSurvey') === '1' ? 1 : 0,
                    'HasilSurveyShow' => $this->request->getPost('HasilSurveyShow') === '1' ? 1 : 0,
                    'RedaksiAwal' => (string) $this->request->getPost('RedaksiAwal'),
                    'RedaksiAkhir' => (string) $this->request->getPost('RedaksiAkhir'),
                    'Keterangan' => $keterangan,
                ];
                $db = $this->db();
                if ($survey) {
                    $db->table('survey')->where('ID', $survey['ID'])->update($payload + $this->audit(false));
                    $this->flash('Data berhasil diubah');
                } else {
                    $db->table('survey')->insert($payload + $this->audit(true));
                    $this->flash('Data berhasil disimpan');
                }
                return redirect()->to('surveipemustaka');
            }
        }

        return $this->render('form', [
            'title' => $survey ? 'Koreksi Survey' : 'Tambah Survey',
            'survey' => $survey,
            'errors' => $errors,
            'action' => $survey ? site_url('surveipemustaka/edit/' . $survey['ID']) : site_url('surveipemustaka/create'),
            'values' => $this->request->is('post') ? $this->request->getPost() : [
                'NamaSurvey' => $survey['NamaSurvey'] ?? '',
                'TglMulai' => isset($survey['TanggalMulai']) ? date('d-m-Y', strtotime($survey['TanggalMulai'])) : '',
                'TglSelesai' => isset($survey['TanggalSelesai']) ? date('d-m-Y', strtotime($survey['TanggalSelesai'])) : '',
                'IsActive' => self::flag($survey['IsActive'] ?? null) ? '1' : '0',
                'NomorUrut' => $survey['NomorUrut'] ?? '',
                'TargetSurvey' => (string) (int) ($survey['TargetSurvey'] ?? 1),
                'HasilSurveyShow' => (string) (int) ($survey['HasilSurveyShow'] ?? 1),
                'RedaksiAwal' => $survey['RedaksiAwal'] ?? '',
                'RedaksiAkhir' => $survey['RedaksiAkhir'] ?? '',
                'Keterangan' => $survey['Keterangan'] ?? '',
            ],
        ]);
    }

    public function delete(int $id = 0)
    {
        if (!$this->allowed('delete')) return $this->denied();
        if (!$this->findSurvey($id)) return $this->notFound('Survey tidak ditemukan');

        $db = $this->db();
        $pertanyaanIds = array_column($db->table('survey_pertanyaan')->select('ID')
            ->where('Survey_id', $id)->get()->getResultArray(), 'ID');

        $db->transStart();
        $this->deletePertanyaanChildren($pertanyaanIds);
        if ($pertanyaanIds) $db->table('survey_pertanyaan')->whereIn('ID', $pertanyaanIds)->delete();
        $db->table('survey')->where('ID', $id)->delete();
        $db->transComplete();

        $db->transStatus() ? $this->flash('Data berhasil dihapus') : $this->flash('Data gagal dihapus', 'error');
        return redirect()->to('surveipemustaka');
    }

    /** Isi modal "Hasil Survey": grafik pie per pertanyaan pilihan ganda. */
    public function hasil(int $id = 0)
    {
        if (!$this->allowed('hasil')) return $this->denied();

        $db = $this->db();
        $pertanyaan = $db->table('survey_pertanyaan')->where('Survey_id', $id)
            ->orderBy('NoUrut')->orderBy('ID')->get()->getResultArray();
        if (!$pertanyaan) {
            return $this->response->setBody('<center><h3>Data Kosong</h3></center>');
        }

        $pilihan = $db->table('survey_pilihan')->whereIn('Survey_Pertanyaan_id', array_column($pertanyaan, 'ID'))
            ->orderBy('ID')->get()->getResultArray();
        $byPertanyaan = [];
        foreach ($pilihan as $row) $byPertanyaan[$row['Survey_Pertanyaan_id']][] = $row;

        $charts = [];
        foreach ($pertanyaan as $row) {
            if ($row['JenisPertanyaan'] !== 'Pilihan') continue;
            $charts[] = [
                'id' => (int) $row['ID'],
                'title' => $row['Pertanyaan'],
                'labels' => array_column($byPertanyaan[$row['ID']] ?? [], 'Pilihan'),
                'data' => array_map('intval', array_column($byPertanyaan[$row['ID']] ?? [], 'ChoosenCount')),
            ];
        }

        return view('SurveiPemustaka\Views\hasil', ['charts' => $charts]);
    }

    // ----------------------------------------------------------------
    // PERTANYAAN
    // ----------------------------------------------------------------

    public function pertanyaan(int $surveyId = 0)
    {
        if (!$this->allowed('pertanyaan')) return $this->denied();
        $survey = $this->findSurvey($surveyId);
        if (!$survey) return $this->notFound('Survey tidak ditemukan');

        $db = $this->db();
        $rows = $db->table('survey_pertanyaan')->where('Survey_id', $surveyId)
            ->orderBy('NoUrut')->orderBy('ID')->get()->getResultArray();
        $ids = array_column($rows, 'ID');

        $jumlahPilihan = $jumlahResponden = [];
        if ($ids) {
            foreach ($db->table('survey_pilihan')->select('Survey_Pertanyaan_id, COUNT(*) AS total')
                ->whereIn('Survey_Pertanyaan_id', $ids)->groupBy('Survey_Pertanyaan_id')->get()->getResultArray() as $row) {
                $jumlahPilihan[$row['Survey_Pertanyaan_id']] = (int) $row['total'];
            }
            // Pilihan: sesi unik di survey_pilihan_sesi; Isian: sesi unik di survey_isian
            foreach ($db->table('survey_pilihan_sesi ps')->select('p.Survey_Pertanyaan_id, COUNT(DISTINCT ps.Sesi) AS total')
                ->join('survey_pilihan p', 'p.ID = ps.Survey_Pilihan_id')
                ->whereIn('p.Survey_Pertanyaan_id', $ids)->groupBy('p.Survey_Pertanyaan_id')->get()->getResultArray() as $row) {
                $jumlahResponden['Pilihan'][$row['Survey_Pertanyaan_id']] = (int) $row['total'];
            }
            foreach ($db->table('survey_isian')->select('Survey_Pertanyaan_id, COUNT(DISTINCT Sesi) AS total')
                ->whereIn('Survey_Pertanyaan_id', $ids)->groupBy('Survey_Pertanyaan_id')->get()->getResultArray() as $row) {
                $jumlahResponden['Isian'][$row['Survey_Pertanyaan_id']] = (int) $row['total'];
            }
        }

        return $this->render('pertanyaan_index', [
            'title' => 'Survey Pertanyaan',
            'survey' => $survey,
            'rows' => $rows,
            'jumlahPilihan' => $jumlahPilihan,
            'jumlahResponden' => $jumlahResponden,
        ]);
    }

    public function pertanyaanCreate(int $surveyId = 0)
    {
        if (!$this->allowed('pertanyaan')) return $this->denied();
        $survey = $this->findSurvey($surveyId);
        if (!$survey) return $this->notFound('Survey tidak ditemukan');
        return $this->pertanyaanForm($survey, null);
    }

    public function pertanyaanEdit(int $id = 0)
    {
        if (!$this->allowed('pertanyaan')) return $this->denied();
        $pertanyaan = $this->findPertanyaan($id);
        $survey = $pertanyaan ? $this->findSurvey((int) $pertanyaan['Survey_id']) : null;
        if (!$pertanyaan || !$survey) return $this->notFound('Pertanyaan tidak ditemukan');
        return $this->pertanyaanForm($survey, $pertanyaan);
    }

    private function pertanyaanForm(array $survey, ?array $pertanyaan)
    {
        $errors = [];
        $backUrl = site_url('surveipemustaka/pertanyaan/' . $survey['ID']);

        if ($this->request->is('post')) {
            $text = trim((string) $this->request->getPost('Pertanyaan'));
            $jenis = (string) $this->request->getPost('JenisPertanyaan');
            $orientasi = (string) $this->request->getPost('Orientation');
            if ($text === '') $errors['Pertanyaan'] = 'Pertanyaan tidak boleh kosong.';
            if (!isset(self::JENIS_PERTANYAAN[$jenis])) $errors['JenisPertanyaan'] = 'Jenis Pertanyaan tidak valid.';
            if (!isset(self::ORIENTATION[$orientasi])) $errors['Orientation'] = 'Orientation tidak valid.';

            if (!$errors) {
                $payload = [
                    'Survey_id' => $survey['ID'],
                    'Pertanyaan' => $text,
                    'IsMandatory' => $this->request->getPost('IsMandatory') === '1' ? 1 : 0,
                    'IsCanMultipleAnswer' => $this->request->getPost('IsCanMultipleAnswer') === '1' ? 1 : 0,
                    'JenisPertanyaan' => $jenis,
                    'Orientation' => $orientasi,
                    'NoUrut' => $this->request->getPost('NoUrut') === '' ? null : (int) $this->request->getPost('NoUrut'),
                ];
                $db = $this->db();
                if ($pertanyaan) {
                    $db->table('survey_pertanyaan')->where('ID', $pertanyaan['ID'])->update($payload + $this->audit(false));
                    $this->flash('Data berhasil diubah');
                } else {
                    $db->table('survey_pertanyaan')->insert($payload + $this->audit(true));
                    $this->flash('Data berhasil disimpan');
                }
                return redirect()->to($backUrl);
            }
        }

        return $this->render('pertanyaan_form', [
            'title' => $pertanyaan ? 'Koreksi Survey Pertanyaan' : 'Tambah Survey Pertanyaan',
            'survey' => $survey,
            'errors' => $errors,
            'backUrl' => $backUrl,
            'action' => $pertanyaan
                ? site_url('surveipemustaka/pertanyaan/edit/' . $pertanyaan['ID'])
                : site_url('surveipemustaka/pertanyaan/create/' . $survey['ID']),
            'values' => $this->request->is('post') ? $this->request->getPost() : [
                'Pertanyaan' => $pertanyaan['Pertanyaan'] ?? '',
                'IsMandatory' => self::flag($pertanyaan['IsMandatory'] ?? null) ? '1' : '0',
                'IsCanMultipleAnswer' => self::flag($pertanyaan['IsCanMultipleAnswer'] ?? null) ? '1' : '0',
                'JenisPertanyaan' => $pertanyaan['JenisPertanyaan'] ?? 'Pilihan',
                'Orientation' => ($pertanyaan['Orientation'] ?? '') === 'Horizontal' ? 'Horisontal' : ($pertanyaan['Orientation'] ?? 'Vertikal'),
                'NoUrut' => $pertanyaan['NoUrut'] ?? '',
            ],
        ]);
    }

    public function pertanyaanDelete(int $id = 0)
    {
        if (!$this->allowed('pertanyaan')) return $this->denied();
        $pertanyaan = $this->findPertanyaan($id);
        if (!$pertanyaan) return $this->notFound('Pertanyaan tidak ditemukan');

        $db = $this->db();
        $db->transStart();
        $this->deletePertanyaanChildren([$id]);
        $db->table('survey_pertanyaan')->where('ID', $id)->delete();
        $db->transComplete();

        $db->transStatus() ? $this->flash('Data berhasil dihapus') : $this->flash('Data gagal dihapus', 'error');
        return redirect()->to('surveipemustaka/pertanyaan/' . $pertanyaan['Survey_id']);
    }

    /** Hapus pilihan, sesi pilihan, dan isian milik pertanyaan (tabel tidak punya FK cascade). */
    private function deletePertanyaanChildren(array $pertanyaanIds): void
    {
        if (!$pertanyaanIds) return;
        $db = $this->db();
        $pilihanIds = array_column($db->table('survey_pilihan')->select('ID')
            ->whereIn('Survey_Pertanyaan_id', $pertanyaanIds)->get()->getResultArray(), 'ID');
        if ($pilihanIds) {
            $db->table('survey_pilihan_sesi')->whereIn('Survey_Pilihan_id', $pilihanIds)->delete();
            $db->table('survey_pilihan')->whereIn('ID', $pilihanIds)->delete();
        }
        $db->table('survey_isian')->whereIn('Survey_Pertanyaan_id', $pertanyaanIds)->delete();
    }

    // ----------------------------------------------------------------
    // PILIHAN
    // ----------------------------------------------------------------

    public function pilihan(int $pertanyaanId = 0)
    {
        if (!$this->allowed('pilihan')) return $this->denied();
        $pertanyaan = $this->findPertanyaan($pertanyaanId);
        if (!$pertanyaan) return $this->notFound('Pertanyaan tidak ditemukan');

        $rows = $this->db()->table('survey_pilihan')->where('Survey_Pertanyaan_id', $pertanyaanId)
            ->orderBy('ID')->get()->getResultArray();

        return $this->render('pilihan_index', [
            'title' => 'Survey Pilihan',
            'pertanyaan' => $pertanyaan,
            'survey' => $this->findSurvey((int) $pertanyaan['Survey_id']),
            'rows' => $rows,
        ]);
    }

    public function pilihanCreate(int $pertanyaanId = 0)
    {
        if (!$this->allowed('pilihan')) return $this->denied();
        $pertanyaan = $this->findPertanyaan($pertanyaanId);
        if (!$pertanyaan) return $this->notFound('Pertanyaan tidak ditemukan');
        return $this->pilihanForm($pertanyaan, null);
    }

    public function pilihanEdit(int $id = 0)
    {
        if (!$this->allowed('pilihan')) return $this->denied();
        $pilihan = $this->findPilihan($id);
        $pertanyaan = $pilihan ? $this->findPertanyaan((int) $pilihan['Survey_Pertanyaan_id']) : null;
        if (!$pilihan || !$pertanyaan) return $this->notFound('Pilihan tidak ditemukan');
        return $this->pilihanForm($pertanyaan, $pilihan);
    }

    private function pilihanForm(array $pertanyaan, ?array $pilihan)
    {
        $errors = [];
        $backUrl = site_url('surveipemustaka/pilihan/' . $pertanyaan['ID']);

        if ($this->request->is('post')) {
            $text = trim((string) $this->request->getPost('Pilihan'));
            if ($text === '') $errors['Pilihan'] = 'Pilihan tidak boleh kosong.';

            if (!$errors) {
                $db = $this->db();
                if ($pilihan) {
                    $db->table('survey_pilihan')->where('ID', $pilihan['ID'])
                        ->update(['Pilihan' => $text] + $this->audit(false));
                    $this->flash('Data berhasil diubah');
                } else {
                    $db->table('survey_pilihan')->insert([
                        'Survey_Pertanyaan_id' => $pertanyaan['ID'],
                        'Pilihan' => $text,
                        'ChoosenCount' => 0,
                    ] + $this->audit(true));
                    $this->flash('Data berhasil disimpan');
                }
                return redirect()->to($backUrl);
            }
        }

        return $this->render('pilihan_form', [
            'title' => $pilihan ? 'Koreksi Survey Pilihan' : 'Tambah Survey Pilihan',
            'pertanyaan' => $pertanyaan,
            'errors' => $errors,
            'backUrl' => $backUrl,
            'action' => $pilihan
                ? site_url('surveipemustaka/pilihan/edit/' . $pilihan['ID'])
                : site_url('surveipemustaka/pilihan/create/' . $pertanyaan['ID']),
            'values' => $this->request->is('post') ? $this->request->getPost() : ['Pilihan' => $pilihan['Pilihan'] ?? ''],
        ]);
    }

    public function pilihanDelete(int $id = 0)
    {
        if (!$this->allowed('pilihan')) return $this->denied();
        $pilihan = $this->findPilihan($id);
        if (!$pilihan) return $this->notFound('Pilihan tidak ditemukan');

        $db = $this->db();
        $db->transStart();
        $db->table('survey_pilihan_sesi')->where('Survey_Pilihan_id', $id)->delete();
        $db->table('survey_pilihan')->where('ID', $id)->delete();
        $db->transComplete();

        $db->transStatus() ? $this->flash('Data berhasil dihapus') : $this->flash('Data gagal dihapus', 'error');
        return redirect()->to('surveipemustaka/pilihan/' . $pilihan['Survey_Pertanyaan_id']);
    }

    // ----------------------------------------------------------------
    // ISIAN
    // ----------------------------------------------------------------

    public function isian(int $pertanyaanId = 0)
    {
        if (!$this->allowed('isian')) return $this->denied();
        $pertanyaan = $this->findPertanyaan($pertanyaanId);
        if (!$pertanyaan) return $this->notFound('Pertanyaan tidak ditemukan');

        $rows = $this->db()->table('survey_isian')->where('Survey_Pertanyaan_id', $pertanyaanId)
            ->orderBy('ID')->get()->getResultArray();

        return $this->render('isian_index', [
            'title' => 'Survey Isian',
            'pertanyaan' => $pertanyaan,
            'survey' => $this->findSurvey((int) $pertanyaan['Survey_id']),
            'rows' => $rows,
        ]);
    }
}
