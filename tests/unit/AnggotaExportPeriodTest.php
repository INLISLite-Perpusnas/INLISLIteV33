<?php

use Anggota\Controllers\AnggotaExportController;
use PHPUnit\Framework\TestCase;

final class AnggotaExportPeriodTest extends TestCase
{
    public function testAcceptsValidCalendarDates(): void
    {
        $controller = (new ReflectionClass(AnggotaExportController::class))->newInstanceWithoutConstructor();
        $validDate = new ReflectionMethod($controller, 'validDate');

        $this->assertTrue($validDate->invoke($controller, '2024-02-29'));
        $this->assertTrue($validDate->invoke($controller, '2026-11-07'));
    }

    public function testUsesSameColumnCountAndOrderAsImportTemplate(): void
    {
        $columns = (new ReflectionClass(AnggotaExportController::class))->getReflectionConstant('COLUMNS')->getValue();
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(FCPATH . 'uploads/master-template/template_anggota.xlsx');
        $headers = $spreadsheet->getActiveSheet()->rangeToArray('A1:AW1')[0];

        $this->assertCount(49, $headers);
        $this->assertCount(count($headers) - 1, $columns);
        $this->assertSame('NO ANGGOTA', $headers[1]);
        $this->assertSame('MemberNo', $columns[0]);
        $this->assertSame('PHOTO URL', $headers[48]);
        $this->assertSame('PhotoUrl', $columns[47]);
        $this->assertSame('NamaDarurat', $columns[array_search('NAMA (KEADAAN DARURAT)', $headers, true) - 1]);
        $this->assertSame('AlamatDarurat', $columns[array_search('ALAMAT (KEADAAN DARURAT)', $headers, true) - 1]);
        $this->assertSame('TelpDarurat', $columns[array_search('NO TELP (KEADAAN DARURAT)', $headers, true) - 1]);
        $this->assertSame('StatusHubunganDarurat', $columns[array_search('STATUS HUBUNGAN (DARURAT)', $headers, true) - 1]);

        $spreadsheet->disconnectWorksheets();
    }
    public function testFiltersListedMembersCreatorAndInclusiveDateRange(): void
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '',
            'pConnect' => false, 'DBDebug' => true,
        ], false);
        try {
            $db->query('CREATE TABLE members (ID INTEGER PRIMARY KEY, Branch_id INTEGER, IsKeranjang INTEGER, CreateBy INTEGER, CreateDate TEXT)');
            $db->query("INSERT INTO members VALUES
                (1, NULL, 0, 2, '2026-10-01 00:00:00'),
                (2, 10, 0, 2, '2026-10-31 23:59:59'),
                (3, 10, 0, 2, '2026-11-01 00:00:00'),
                (4, 10, 0, 3, '2026-10-15 12:00:00'),
                (5, 11, 1, 2, '2026-10-15 12:00:00')");

            $controller = (new ReflectionClass(AnggotaExportController::class))->newInstanceWithoutConstructor();
            $query = $db->table('members')->select('ID');
            (new ReflectionMethod($controller, 'applyFilters'))->invoke($controller, $query, '2', '2026-10-01', '2026-10-31');

            $this->assertSame([1, 2], array_map('intval', array_column($query->orderBy('ID')->get()->getResultArray(), 'ID')));

            $allQuery = $db->table('members')->select('ID');
            (new ReflectionMethod($controller, 'applyFilters'))->invoke($controller, $allQuery, '', '', '');
            $this->assertSame([1, 2, 3, 4], array_map('intval', array_column($allQuery->orderBy('ID')->get()->getResultArray(), 'ID')));
        } finally {
            $db->close();
        }
    }

    public function testRejectsInvalidOrAmbiguousDates(): void
    {
        $controller = (new ReflectionClass(AnggotaExportController::class))->newInstanceWithoutConstructor();
        $validDate = new ReflectionMethod($controller, 'validDate');

        foreach (['2025-02-29', '2026-13-01', '2026-1-7', '07-11-2026', '2026-11-07 extra', "2026-11-07" . chr(0)] as $input) {
            $this->assertFalse($validDate->invoke($controller, $input), $input);
        }
    }
}