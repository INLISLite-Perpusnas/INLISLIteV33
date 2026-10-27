<?php

namespace SurveiPemustaka\Models;

use CodeIgniter\Database\BaseConnection;

class SurveyFormService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect('data');
    }

    private function availableSurveys()
    {
        return $this->db->table('survey')->where('active', 1)->where('IsActive', 1)
            ->where('TanggalMulai <=', date('Y-m-d H:i:s'))
            ->where('TanggalSelesai >=', date('Y-m-d H:i:s'));
    }

    public function activeSurveys(bool $isMember): array
    {
        $query = $this->availableSurveys();
        if (!$isMember) {
            $query->groupStart()->where('TargetSurvey', 0)
                ->orWhere('TargetSurvey IS NULL', null, false)->groupEnd();
        }
        $surveys = $query->orderBy('NomorUrut')->orderBy('ID')->get()->getResultArray();
        if (!$surveys) return [];

        $questions = $this->db->table('survey_pertanyaan')
            ->whereIn('Survey_id', array_column($surveys, 'ID'))->where('active', 1)
            ->orderBy('NoUrut')->orderBy('ID')->get()->getResultArray();
        $options = $questions ? $this->db->table('survey_pilihan')
            ->whereIn('Survey_Pertanyaan_id', array_column($questions, 'ID'))->where('active', 1)
            ->orderBy('ID')->get()->getResultArray() : [];
        $byQuestion = [];
        foreach ($options as $option) $byQuestion[$option['Survey_Pertanyaan_id']][] = $option;
        $bySurvey = [];
        foreach ($questions as $question) {
            $question['options'] = $byQuestion[$question['ID']] ?? [];
            $bySurvey[$question['Survey_id']][] = $question;
        }
        foreach ($surveys as &$survey) $survey['questions'] = $bySurvey[$survey['ID']] ?? [];
        unset($survey);
        return $surveys;
    }

    private static function enabled($value): bool
    {
        return $value === 1 || $value === '1' || $value === "\x01" || $value === true;
    }

    public function submit(int $surveyId, $answers, string $sessionId, ?string $memberNo): ?string
    {
        $survey = $this->availableSurveys()->where('ID', $surveyId)->get()->getRowArray();
        if (!$survey || ((int) $survey['TargetSurvey'] === 1 && $memberNo === null)) {
            return 'Survei tidak tersedia.';
        }
        if (!is_array($answers) || count($answers) > 100 || strlen($sessionId) > 255) {
            return 'Jawaban tidak valid.';
        }
        $questions = $this->db->table('survey_pertanyaan')
            ->where('Survey_id', $surveyId)->where('active', 1)->get()->getResultArray();
        if (!$questions) return 'Survei belum memiliki pertanyaan.';
        $byId = array_column($questions, null, 'ID');
        foreach (array_keys($answers) as $id) {
            if (!ctype_digit((string) $id) || !isset($byId[$id])) return 'Pertanyaan tidak valid.';
        }
        $options = $this->db->table('survey_pilihan')
            ->whereIn('Survey_Pertanyaan_id', array_keys($byId))->where('active', 1)->get()->getResultArray();
        $allowed = [];
        foreach ($options as $option) $allowed[$option['Survey_Pertanyaan_id']][$option['ID']] = true;

        $choices = $texts = [];
        foreach ($questions as $question) {
            $id = $question['ID'];
            $answer = $answers[$id] ?? null;
            if (strcasecmp((string) $question['JenisPertanyaan'], 'Pilihan') === 0) {
                $values = $answer === null || $answer === '' ? [] : (array) $answer;
                if (count($values) !== count(array_unique($values, SORT_REGULAR))
                    || (!self::enabled($question['IsCanMultipleAnswer']) && count($values) > 1)) {
                    return 'Pilihan jawaban tidak valid.';
                }
                foreach ($values as $value) {
                    if ((!is_string($value) && !is_int($value)) || !ctype_digit((string) $value)
                        || !isset($allowed[$id][(int) $value])) return 'Pilihan jawaban tidak valid.';
                    $choices[] = (int) $value;
                }
                $filled = (bool) $values;
            } else {
                if ($answer !== null && !is_string($answer)) return 'Isian tidak valid.';
                $value = trim((string) $answer);
                if (mb_strlen($value) > 2000) return 'Isian terlalu panjang (maksimal 2000 karakter).';
                $filled = $value !== '';
                if ($filled) $texts[] = ['Survey_Pertanyaan_id' => $id, 'Isian' => $value];
            }
            if (self::enabled($question['IsMandatory']) && !$filled) {
                return 'Semua pertanyaan wajib harus dijawab.';
            }
        }
        if (!$choices && !$texts) return 'Isi minimal satu jawaban.';

        $this->db->transBegin();
        $textAnswers = $this->db->table('survey_isian i')
            ->join('survey_pertanyaan q', 'q.ID = i.Survey_Pertanyaan_id')
            ->where('q.Survey_id', $surveyId)->groupStart()->where('i.Sesi', $sessionId);
        if ($memberNo !== null) $textAnswers->orWhere('i.MemberNo', $memberNo);
        $answered = $textAnswers->groupEnd()->countAllResults() > 0;
        $choiceAnswers = $this->db->table('survey_pilihan_sesi ps')
            ->join('survey_pilihan p', 'p.ID = ps.Survey_Pilihan_id')
            ->join('survey_pertanyaan q', 'q.ID = p.Survey_Pertanyaan_id')
            ->where('q.Survey_id', $surveyId)->groupStart()->where('ps.Sesi', $sessionId);
        if ($memberNo !== null) $choiceAnswers->orWhere('ps.MemberNo', $memberNo);
        $answered = $answered || $choiceAnswers->groupEnd()->countAllResults() > 0;
        if ($answered) {
            $this->db->transRollback();
            return 'Survei ini sudah Anda isi.';
        }
        $identity = ['Sesi' => $sessionId, 'MemberNo' => $memberNo, 'Branch_id' => $survey['Branch_id']];
        foreach ($texts as $text) $this->db->table('survey_isian')->insert($identity + $text);
        foreach ($choices as $optionId) {
            $this->db->table('survey_pilihan_sesi')->insert($identity + ['Survey_Pilihan_id' => $optionId]);
            $this->db->table('survey_pilihan')->where('ID', $optionId)
                ->set('ChoosenCount', 'COALESCE(ChoosenCount, 0) + 1', false)->update();
        }
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return 'Jawaban belum tersimpan. Silakan coba lagi.';
        }
        $this->db->transCommit();
        return null;
    }
}