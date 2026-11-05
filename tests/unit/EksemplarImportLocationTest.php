<?php

use CodeIgniter\Database\BaseConnection;
use Eksemplar\Controllers\EksemplarImportController;
use PHPUnit\Framework\TestCase;

final class EksemplarImportLocationTest extends TestCase
{
    private BaseConnection $db;
    private EksemplarImportController $controller;

    protected function setUp(): void
    {
        $this->db = \Config\Database::connect([
            'DBDriver' => 'SQLite3',
            'database' => ':memory:',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug' => true,
        ], false);
        $this->db->query('CREATE TABLE location_library (ID INTEGER PRIMARY KEY, Code TEXT)');
        $this->db->query('CREATE TABLE locations (ID INTEGER PRIMARY KEY, Code TEXT, LocationLibrary_id INTEGER)');
        $this->db->query("INSERT INTO location_library VALUES (1, 'PUSAT'), (5, 'CABANG')");
        $this->db->query("INSERT INTO locations VALUES (466, '0101', 1), (468, '0102', 1), (500, '0101', 5)");

        $this->controller = (new ReflectionClass(EksemplarImportController::class))->newInstanceWithoutConstructor();
        $this->controller->db = $this->db;
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }

    public function testResolvesExistingCodesWithinSelectedLibrary(): void
    {
        $libraryId = (new ReflectionMethod($this->controller, 'parseLocationLibraryId'))->invoke($this->controller, 'PUSAT');
        $resolveRoom = new ReflectionMethod($this->controller, 'parseLocationId');

        $this->assertSame(1, $libraryId);
        $this->assertSame(468, $resolveRoom->invoke($this->controller, '0102', $libraryId));
        $this->assertSame(500, $resolveRoom->invoke($this->controller, '0101', 5));
    }

    public function testRejectsRoomCodeMissingFromSelectedLibrary(): void
    {
        $this->expectExceptionMessage('Kode lokasi ruang 0106 tidak ditemukan');

        (new ReflectionMethod($this->controller, 'parseLocationId'))->invoke($this->controller, '0106', 1);
    }

    public function testRejectsUnknownLibraryCode(): void
    {
        $this->expectExceptionMessage('Kode lokasi perpustakaan TIDAK_ADA tidak ditemukan');

        (new ReflectionMethod($this->controller, 'parseLocationLibraryId'))->invoke($this->controller, 'TIDAK_ADA');
    }
}
