<?php

use PHPUnit\Framework\TestCase;
use SurveiPemustaka\Models\SurveyFormService;

final class SurveyFormServiceTest extends TestCase
{
    private function database()
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:',
            'DBPrefix' => '', 'pConnect' => false, 'DBDebug' => true,
        ], false);
        $db->query('CREATE TABLE survey (ID INTEGER PRIMARY KEY, NamaSurvey TEXT, TanggalMulai TEXT, TanggalSelesai TEXT, IsActive INTEGER, active INTEGER, NomorUrut INTEGER, TargetSurvey INTEGER, Branch_id INTEGER, RedaksiAwal TEXT, RedaksiAkhir TEXT)');
        $db->query('CREATE TABLE survey_pertanyaan (ID INTEGER PRIMARY KEY, Survey_id INTEGER, Pertanyaan TEXT, JenisPertanyaan TEXT, IsMandatory INTEGER, IsCanMultipleAnswer INTEGER, NoUrut INTEGER, Orientation TEXT, active INTEGER)');
        $db->query('CREATE TABLE survey_pilihan (ID INTEGER PRIMARY KEY, Survey_Pertanyaan_id INTEGER, Pilihan TEXT, ChoosenCount INTEGER, active INTEGER)');
        $db->query('CREATE TABLE survey_isian (ID INTEGER PRIMARY KEY AUTOINCREMENT, Survey_Pertanyaan_id INTEGER, Sesi TEXT, MemberNo TEXT, Isian TEXT, Branch_id INTEGER)');
        $db->query('CREATE TABLE survey_pilihan_sesi (ID INTEGER PRIMARY KEY AUTOINCREMENT, Survey_Pilihan_id INTEGER, MemberNo TEXT, Sesi TEXT, Branch_id INTEGER)');
        $db->table('survey')->insert([
            'ID' => 1, 'NamaSurvey' => 'Evaluasi layanan', 'TanggalMulai' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'TanggalSelesai' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'IsActive' => 1, 'active' => 1, 'TargetSurvey' => 0, 'Branch_id' => 42,
        ]);
        $db->table('survey')->insert([
            'ID' => 2, 'NamaSurvey' => 'Anggota', 'TanggalMulai' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'TanggalSelesai' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'IsActive' => 1, 'active' => 1, 'TargetSurvey' => 1, 'Branch_id' => 42,
        ]);
        $db->table('survey_pertanyaan')->insertBatch([
            ['ID' => 1, 'Survey_id' => 1, 'Pertanyaan' => 'Penilaian', 'JenisPertanyaan' => 'Pilihan', 'IsMandatory' => 1, 'IsCanMultipleAnswer' => 0, 'NoUrut' => 1, 'Orientation' => 'Vertikal', 'active' => 1],
            ['ID' => 2, 'Survey_id' => 1, 'Pertanyaan' => 'Komentar', 'JenisPertanyaan' => 'Isian', 'IsMandatory' => 0, 'IsCanMultipleAnswer' => 0, 'NoUrut' => 2, 'Orientation' => 'Vertikal', 'active' => 1],
        ]);
        $db->table('survey_pilihan')->insertBatch([
            ['ID' => 1, 'Survey_Pertanyaan_id' => 1, 'Pilihan' => 'Bagus', 'ChoosenCount' => 0, 'active' => 1],
            ['ID' => 2, 'Survey_Pertanyaan_id' => 1, 'Pilihan' => 'Buruk', 'ChoosenCount' => 0, 'active' => 1],
            ['ID' => 3, 'Survey_Pertanyaan_id' => 2, 'Pilihan' => 'Tidak valid', 'ChoosenCount' => 0, 'active' => 1],
        ]);
        return $db;
    }

    public function testHomeLoadsOnlyAvailableSurveysAndTheirQuestions(): void
    {
        $db = $this->database();
        $service = new SurveyFormService($db);
        $public = $service->activeSurveys(false);
        $this->assertCount(1, $public);
        $this->assertSame('Penilaian', $public[0]['questions'][0]['Pertanyaan']);
        $this->assertCount(2, $public[0]['questions'][0]['options']);
        $this->assertCount(2, $service->activeSurveys(true));
        $db->close();
    }

    /** @runInSeparateProcess @preserveGlobalState disabled */
    public function testPublicFormRendersChoicesAndFreeText(): void
    {
        $db = $this->database();
        $html = view('SurveiPemustaka\Views\public_form', [
            'surveys' => (new SurveyFormService($db))->activeSurveys(false),
        ]);
        $this->assertStringContainsString('Evaluasi layanan', $html);
        $this->assertStringContainsString('name="jawaban[1]"', $html);
        $this->assertStringContainsString('name="jawaban[2]"', $html);
        $this->assertStringContainsString('Bagus', $html);
        $db->close();
    }

    public function testChoiceTextAndCountStoredAtomicallyWithoutDuplicate(): void
    {
        $db = $this->database();
        $service = new SurveyFormService($db);
        $this->assertNull($service->submit(1, [1 => '1', 2 => 'Terima kasih'], 'session-a', null));
        $this->assertSame(1, (int) $db->table('survey_pilihan')->where('ID', 1)->get()->getRow('ChoosenCount'));
        $this->assertSame(1, $db->table('survey_isian')->countAllResults());
        $this->assertSame(1, $db->table('survey_pilihan_sesi')->countAllResults());
        $this->assertNotNull($service->submit(1, [1 => '1'], 'session-a', null));
        $this->assertSame(1, (int) $db->table('survey_pilihan')->where('ID', 1)->get()->getRow('ChoosenCount'));
        $db->close();
    }

    public function testRejectsInvalidAnswersAndMemberOnlySurvey(): void
    {
        $db = $this->database();
        $service = new SurveyFormService($db);
        $this->assertNotNull($service->submit(1, [1 => '3'], 'session-a', null));
        $this->assertNotNull($service->submit(1, [1 => ['1', '2']], 'session-a', null));
        $this->assertNotNull($service->submit(1, [2 => 'opsional'], 'session-a', null));
        $this->assertNotNull($service->submit(2, [1 => '1'], 'session-a', null));
        $this->assertSame(0, $db->table('survey_pilihan_sesi')->countAllResults());
        $db->close();
    }
    public function testExpiredTodayAndDisabledSurveyDoNotAppear(): void
    {
        $db = $this->database();
        $db->table('survey')->where('ID', 1)->update(['TanggalSelesai' => date('Y-m-d H:i:s', strtotime('-1 hour'))]);
        $service = new SurveyFormService($db);
        $this->assertSame([], $service->activeSurveys(false));
        $this->assertNotNull($service->submit(1, [1 => '1'], 'expired-session', null));
        $db->table('survey')->where('ID', 1)->update([
            'TanggalSelesai' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'IsActive' => 0,
        ]);
        $this->assertSame([], $service->activeSurveys(false));
        $db->close();
    }

    public function testMultipleChoicesPersistPerSession(): void
    {
        $db = $this->database();
        $db->table('survey_pertanyaan')->where('ID', 1)->update(['IsCanMultipleAnswer' => 1]);
        $service = new SurveyFormService($db);
        $this->assertNull($service->submit(1, [1 => ['1', '2']], 'multi-session', null));
        $this->assertSame(2, $db->table('survey_pilihan_sesi')->countAllResults());
        $this->assertSame(1, (int) $db->table('survey_pilihan')->where('ID', 1)->get()->getRow('ChoosenCount'));
        $this->assertSame(1, (int) $db->table('survey_pilihan')->where('ID', 2)->get()->getRow('ChoosenCount'));
        $db->close();
    }
}