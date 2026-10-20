<?php

use App\Libraries\LaporanFrekuensiExport;
use PHPUnit\Framework\TestCase;

final class LaporanFrekuensiExportTest extends TestCase
{
    public function testGroupsMatchExistingColumnChoices(): void
    {
        foreach (['Anggota', 'BacaDitempat', 'BukuTamu', 'Eksemplar', 'Katalog'] as $report) {
            $source = file_get_contents(APPPATH . 'Modules/Laporan/Laporan' . $report . '/Controllers/Laporan' . $report . '.php');
            preg_match('/\$columns = \[(.*?)\];/s', $source, $match);
            preg_match_all("/^\\s*'([^']+)'\\s*=>/m", $match[1], $columns);
            $this->assertSame($columns[1], array_keys(LaporanFrekuensiExport::groups($report)), $report);
        }
        $view = file_get_contents(APPPATH . 'Modules/Laporan/LaporanSirkulasi/Views/index.php');
        preg_match_all('/class="form-check-input column-checkbox"[^>]*value="([^"]+)"/', $view, $columns);
        $this->assertSame($columns[1], array_keys(LaporanFrekuensiExport::groups('Sirkulasi')));
    }
    public function testRejectsUnknownCategoriesAndKeepsSelectedOrder(): void
    {
        $selected = LaporanFrekuensiExport::selectedGroups('Katalog', ['Subject', 'invalid', 'Publisher', 'Subject']);

        $this->assertSame(['Subject', 'Publisher'], array_keys($selected));
        $this->assertSame([], LaporanFrekuensiExport::selectedGroups('Katalog', ['invalid']));
    }

    public function testCriteriaTakePriorityOverCheckedColumnsAndCombineGroups(): void
    {
        $mapping = ['author' => 'Author', 'publisher' => 'Publisher'];
        $selected = LaporanFrekuensiExport::criteriaGroups('Katalog', ['Title'], ['author', 'publisher'], $mapping);
        $this->assertSame(['Author', 'Publisher'], array_keys($selected));
        $this->assertSame(['Title'], array_keys(LaporanFrekuensiExport::criteriaGroups('Katalog', ['Title'], [], $mapping)));
    }

    public function testAllAuthorsAreGroupedIntoCatalogCounts(): void
    {
        $selected = LaporanFrekuensiExport::criteriaGroups('Katalog', [], ['author'], ['author' => 'Author']);
        $this->assertSame(['Author'], array_keys($selected));
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '',
            'pConnect' => false, 'DBDebug' => true,
        ], false);
        $db->query('CREATE TABLE catalogs (ID INTEGER PRIMARY KEY, Author TEXT)');
        $db->query("INSERT INTO catalogs VALUES (1, 'Ilyas'), (2, 'Ilyas'), (3, 'Dani')");
        $rows = LaporanFrekuensiExport::groupedRows($db->table('catalogs'), $selected, 'COUNT(DISTINCT catalogs.ID)');
        $this->assertSame('Ilyas', $rows[0]['group_0']);
        $this->assertSame(2, (int) $rows[0]['jumlah_data']);
        $this->assertSame('Dani', $rows[1]['group_0']);
        $this->assertSame(1, (int) $rows[1]['jumlah_data']);
        $html = LaporanFrekuensiExport::previewHtml($selected, $rows);
        $this->assertStringContainsString('<th>Pengarang</th><th>Jumlah Data</th>', $html);
        $this->assertStringContainsString('<th>3</th>', $html);
        $this->assertStringNotContainsString('Jumlah Eksemplar', $html);
        $db->close();
    }

    public function testAggregatesDistinctTitlesAndCopiesPerCategory(): void
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3',
            'database' => ':memory:',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug' => true,
        ], false);
        $db->query('CREATE TABLE catalogs (ID INTEGER PRIMARY KEY, Subject TEXT, Publisher TEXT)');
        $db->query('CREATE TABLE collections (ID INTEGER PRIMARY KEY, Catalog_id INTEGER)');
        $db->query("INSERT INTO catalogs VALUES (1, 'Fiksi', 'Penerbit A'), (2, 'Fiksi', 'Penerbit A'), (3, 'Sains', 'Penerbit B')");
        $db->query('INSERT INTO collections VALUES (1, 1), (2, 1), (3, 2), (4, 3)');

        $selected = LaporanFrekuensiExport::selectedGroups('Katalog', ['Subject', 'Publisher']);
        $builder = $db->table('catalogs')
            ->join('collections freq_collections', 'freq_collections.Catalog_id = catalogs.ID', 'left');
        $rows = LaporanFrekuensiExport::groupedRows(
            $builder, $selected, 'COUNT(DISTINCT catalogs.ID)', 'COUNT(DISTINCT freq_collections.ID)'
        );

        $this->assertCount(2, $rows);
        $this->assertSame('Fiksi', $rows[0]['group_0']);
        $this->assertSame('Penerbit A', $rows[0]['group_1']);
        $this->assertSame(2, (int) $rows[0]['jumlah_data']);
        $this->assertSame(3, (int) $rows[0]['jumlah_eksemplar']);
        $this->assertSame(1, (int) $rows[1]['jumlah_data']);
        $this->assertSame(1, (int) $rows[1]['jumlah_eksemplar']);
        $db->close();
    }

    public function testFrequencyPreviewShowsGroupsAndTotals(): void
    {
        $selected = LaporanFrekuensiExport::selectedGroups('Katalog', ['Subject', 'Publisher']);
        $rows = [
            ['group_0' => 'Fiksi', 'group_1' => '<A>', 'jumlah_data' => 2, 'jumlah_eksemplar' => 3],
            ['group_0' => 'Sains', 'group_1' => 'B', 'jumlah_data' => 1, 'jumlah_eksemplar' => 2],
        ];
        $html = LaporanFrekuensiExport::previewHtml($selected, $rows, true);

        $this->assertStringContainsString('Jumlah Judul', $html);
        $this->assertStringContainsString('Jumlah Eksemplar', $html);
        $this->assertStringContainsString('&lt;A&gt;', $html);
        $this->assertStringContainsString('<th>3</th><th>5</th>', $html);
    }
    public function testGroupsByEverySelectedCategoryAndCountsTitlesAndCopies(): void
    {
        $query = new FrequencyQueryStub();
        $selected = LaporanFrekuensiExport::selectedGroups('Katalog', ['Subject', 'Publisher']);
        $rows = LaporanFrekuensiExport::groupedRows(
            $query,
            $selected,
            'COUNT(DISTINCT catalogs.ID)',
            'COUNT(DISTINCT freq_collections.ID)'
        );

        $this->assertSame(['catalogs.Subject', 'catalogs.Publisher'], $query->groups);
        $this->assertSame([
            'catalogs.Subject AS group_0',
            'catalogs.Publisher AS group_1',
            'COUNT(DISTINCT catalogs.ID) AS jumlah_data',
            'COUNT(DISTINCT freq_collections.ID) AS jumlah_eksemplar',
        ], $query->selects);
        $this->assertSame('jumlah_data', $query->order);
        $this->assertSame([['group_0' => 'Fiksi', 'group_1' => 'Penerbit A', 'jumlah_data' => 2, 'jumlah_eksemplar' => 3]], $rows);
    }
}

final class FrequencyQueryStub
{
    public array $selects = [];
    public array $groups = [];
    public string $order = '';

    public function select(string $expression, bool $escape): self
    {
        $this->selects[] = $expression;
        return $this;
    }

    public function groupBy(array $expressions, bool $escape): self
    {
        $this->groups = $expressions;
        return $this;
    }

    public function orderBy(string $column, string $direction): self
    {
        $this->order = $column;
        return $this;
    }

    public function get(): self
    {
        return $this;
    }

    public function getResultArray(): array
    {
        return [['group_0' => 'Fiksi', 'group_1' => 'Penerbit A', 'jumlah_data' => 2, 'jumlah_eksemplar' => 3]];
    }
}