<?php

use NamaPerpustakaan\Controllers\BackupService;
use PHPUnit\Framework\TestCase;

require_once APPPATH . 'Modules/SubModule/Administrasi/PengaturanUmum/NamaPerpustakaan/Controllers/BackupService.php';

final class BackupServiceTest extends TestCase
{
    public function testUploadsArchivePreservesFoldersAndFiles(): void
    {
        if (!class_exists(ZipArchive::class)) {
            $this->markTestSkipped('Ekstensi zip tidak tersedia.');
        }

        $source = sys_get_temp_dir() . '/backup-test-' . bin2hex(random_bytes(8));
        $destination = $source . '.zip';
        mkdir($source);
        mkdir($source . '/nested');
        file_put_contents($source . '/nested/example.txt', 'sample');

        try {
            (new BackupService())->uploads($source, $destination);
            $archive = new ZipArchive();
            $this->assertTrue($archive->open($destination));
            try {
                $this->assertNotFalse($archive->locateName('uploads/'));
                $this->assertNotFalse($archive->locateName('uploads/nested/'));
                $this->assertSame('sample', $archive->getFromName('uploads/nested/example.txt'));
            } finally {
                $archive->close();
            }
        } finally {
            @unlink($destination);
            unlink($source . '/nested/example.txt');
            rmdir($source . '/nested');
            rmdir($source);
        }
    }
}