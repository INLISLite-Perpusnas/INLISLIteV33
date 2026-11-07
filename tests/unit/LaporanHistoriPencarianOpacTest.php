<?php

use LaporanHistoriPencarianOpac\Controllers\LaporanHistoriPencarianOpac;
use PHPUnit\Framework\TestCase;

final class LaporanHistoriPencarianOpacTest extends TestCase
{
    public function testDateFilterIncludesBothDaysAndEscapesKeyword(): void
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '',
            'pConnect' => false, 'DBDebug' => true,
        ], false);
        try {
            $db->query('CREATE TABLE opaclogs (ID INTEGER PRIMARY KEY, waktu TEXT, keyword TEXT)');
            $db->query("INSERT INTO opaclogs VALUES
                (1, '2026-11-01 00:00:00', 'katalog'),
                (2, '2026-11-07 23:59:59', 'katalog'),
                (3, '2026-11-08 00:00:00', 'katalog'),
                (4, '2026-11-02 12:00:00', 'buku'),
                (5, '2026-11-02 12:00:00', 'katalog%' )");
            $controller = (new ReflectionClass(LaporanHistoriPencarianOpac::class))->newInstanceWithoutConstructor();
            $filters = new ReflectionMethod($controller, 'applyFilters');
            $query = $db->table('opaclogs')->select('ID');
            $filters->invoke($controller, $query, [
                'start_date' => '2026-11-01', 'end_date' => '2026-11-07', 'keyword' => 'katalog',
            ]);
            $this->assertSame([1, 2, 5], array_map('intval', array_column($query->orderBy('ID')->get()->getResultArray(), 'ID')));

            $literal = $db->table('opaclogs')->select('ID');
            $filters->invoke($controller, $literal, ['start_date' => '', 'end_date' => '', 'keyword' => '%']);
            $this->assertSame([5], array_map('intval', array_column($literal->get()->getResultArray(), 'ID')));
        } finally {
            $db->close();
        }
    }

    public function testRejectsInvalidCalendarDates(): void
    {
        $controller = (new ReflectionClass(LaporanHistoriPencarianOpac::class))->newInstanceWithoutConstructor();
        $date = new ReflectionMethod($controller, 'validDate');
        $this->assertTrue($date->invoke($controller, '2024-02-29'));
        foreach (['2025-02-29', '2026-13-01', '2026-11-7', '2026-11-07 extra'] as $input) {
            $this->assertFalse($date->invoke($controller, $input));
        }
    }

    public function testReportColumnsMatchOpacHistoryFields(): void
    {
        $columns = (new ReflectionClass(LaporanHistoriPencarianOpac::class))->getReflectionConstant('COLUMNS')->getValue();
        $this->assertSame(['waktu', 'User_id', 'ip', 'jenis_pencarian', 'keyword', 'jenis_bahan', 'url'], array_keys($columns));
        $this->assertSame('No. Anggota', $columns['User_id']);
    }
}
