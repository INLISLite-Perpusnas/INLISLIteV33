<?php

namespace SurveiPemustaka\Controllers;

class SurveyAdmin extends \Base\Controllers\BaseController
{
    private function db()
    {
        return \Config\Database::connect('data');
    }

    private function permitted(): bool
    {
        return !is_ajax() && is_allowed('survei/create');
    }

    public function index()
    {
        if (!$this->permitted()) return redirect()->to('dashboard/index')->with('error', 'Akses ditolak.');
        $db = $this->db();
        $surveys = $db->table('survey')->orderBy('NomorUrut')->orderBy('ID')->get()->getResultArray();
        $selectedId = (int) $this->request->getGet('survey');
        if (!$selectedId && $surveys && $this->request->getGet('new') !== '1') $selectedId = (int) $surveys[0]['ID'];
        $selected = null;
        foreach ($surveys as $survey) {
            if ((int) $survey['ID'] === $selectedId) $selected = $survey;
        }
        $questions = $selected ? $db->table('survey_pertanyaan')
            ->where('Survey_id', $selectedId)->orderBy('NoUrut')->orderBy('ID')->get()->getResultArray() : [];
        $options = $questions ? $db->table('survey_pilihan')
            ->whereIn('Survey_Pertanyaan_id', array_column($questions, 'ID'))
            ->orderBy('ID')->get()->getResultArray() : [];
        $byQuestion = [];
        foreach ($options as $option) $byQuestion[$option['Survey_Pertanyaan_id']][] = $option;
        foreach ($questions as &$question) $question['options'] = $byQuestion[$question['ID']] ?? [];
        unset($question);

        return view('SurveiPemustaka\Views\manage', [
            'title' => 'Survei Pemustaka', 'surveys' => $surveys,
            'selectedSurvey' => $selected, 'questions' => $questions,
            'canManage' => $this->permitted(),
        ]);
    }

    public function saveSurvey()
    {
        if (!$this->permitted()) return redirect()->to('surveipemustaka/manage')->with('error', 'Akses ditolak.');
        $name = trim((string) $this->request->getPost('NamaSurvey'));
        $start = (string) $this->request->getPost('TanggalMulai');
        $end = (string) $this->request->getPost('TanggalSelesai');
        $validDate = static function (string $value): bool {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
            return $date !== false && $date->format('Y-m-d\TH:i') === $value;
        };
        if ($name === '' || mb_strlen($name) > 200 || !$validDate($start) || !$validDate($end) || $end < $start) {
            return redirect()->back()->with('error', 'Nama dan rentang tanggal survei wajib valid.');
        }
        $db = $this->db();
        $id = (int) $this->request->getPost('ID');
        $payload = [
            'NamaSurvey' => $name,
            'TanggalMulai' => str_replace('T', ' ', $start) . ':00',
            'TanggalSelesai' => str_replace('T', ' ', $end) . ':00',
            'IsActive' => $this->request->getPost('IsActive') === '1' ? 1 : 0,
            'TargetSurvey' => $this->request->getPost('TargetSurvey') === '1' ? 1 : 0,
            'NomorUrut' => (int) $this->request->getPost('NomorUrut'),
            'RedaksiAwal' => trim((string) $this->request->getPost('RedaksiAwal')),
            'RedaksiAkhir' => trim((string) $this->request->getPost('RedaksiAkhir')),
        ];
        if ($id) {
            if (!$db->table('survey')->where('ID', $id)->countAllResults()) {
                return redirect()->to('surveipemustaka/manage')->with('error', 'Survei tidak ditemukan.');
            }
            $db->table('survey')->where('ID', $id)->update($payload);
        } else {
            $payload['active'] = 1;
            $db->table('survey')->insert($payload);
            $id = $db->insertID();
        }
        return redirect()->to('surveipemustaka/manage?survey=' . $id)->with('success', 'Survei tersimpan.');
    }

    public function saveQuestion()
    {
        if (!$this->permitted()) return redirect()->to('surveipemustaka/manage')->with('error', 'Akses ditolak.');
        $db = $this->db();
        $surveyId = (int) $this->request->getPost('Survey_id');
        $id = (int) $this->request->getPost('ID');
        $question = trim((string) $this->request->getPost('Pertanyaan'));
        $type = $this->request->getPost('JenisPertanyaan');
        if (!$db->table('survey')->where('ID', $surveyId)->countAllResults()
            || $question === '' || mb_strlen($question) > 2000 || !in_array($type, ['Pilihan', 'Isian'], true)) {
            return redirect()->back()->with('error', 'Pertanyaan tidak valid.');
        }
        $payload = [
            'Survey_id' => $surveyId, 'Pertanyaan' => $question, 'JenisPertanyaan' => $type,
            'Orientation' => $this->request->getPost('Orientation') === 'Horizontal' ? 'Horizontal' : 'Vertikal',
            'IsMandatory' => $this->request->getPost('IsMandatory') === '1' ? 1 : 0,
            'IsCanMultipleAnswer' => $type === 'Pilihan' && $this->request->getPost('IsCanMultipleAnswer') === '1' ? 1 : 0,
            'NoUrut' => (int) $this->request->getPost('NoUrut'),
            'active' => $this->request->getPost('active') === '0' ? 0 : 1,
        ];
        if ($id) {
            if (!$db->table('survey_pertanyaan')->where('ID', $id)->where('Survey_id', $surveyId)->countAllResults()) {
                return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.');
            }
            $db->table('survey_pertanyaan')->where('ID', $id)->update($payload);
        } else {
            $db->table('survey_pertanyaan')->insert($payload);
        }
        return redirect()->to('surveipemustaka/manage?survey=' . $surveyId)->with('success', 'Pertanyaan tersimpan.');
    }

    public function saveOption()
    {
        if (!$this->permitted()) return redirect()->to('surveipemustaka/manage')->with('error', 'Akses ditolak.');
        $db = $this->db();
        $questionId = (int) $this->request->getPost('Survey_Pertanyaan_id');
        $question = $db->table('survey_pertanyaan')->select('Survey_id')
            ->where('ID', $questionId)->get()->getRowArray();
        $option = trim((string) $this->request->getPost('Pilihan'));
        if (!$question || $option === '' || mb_strlen($option) > 2000) {
            return redirect()->back()->with('error', 'Pilihan tidak valid.');
        }
        $id = (int) $this->request->getPost('ID');
        $payload = ['Survey_Pertanyaan_id' => $questionId, 'Pilihan' => $option,
            'active' => $this->request->getPost('active') === '0' ? 0 : 1];
        if ($id) {
            if (!$db->table('survey_pilihan')->where('ID', $id)
                ->where('Survey_Pertanyaan_id', $questionId)->countAllResults()) {
                return redirect()->back()->with('error', 'Pilihan tidak ditemukan.');
            }
            $db->table('survey_pilihan')->where('ID', $id)->update($payload);
        } else {
            $payload['ChoosenCount'] = 0;
            $db->table('survey_pilihan')->insert($payload);
        }
        return redirect()->to('surveipemustaka/manage?survey=' . $question['Survey_id'])
            ->with('success', 'Pilihan tersimpan.');
    }
}