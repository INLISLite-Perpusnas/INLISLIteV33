<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class MergeDatabases extends BaseCommand
{
    protected $group       = 'db';
    protected $name        = 'db:merge';
    protected $description = 'Fresh-merge: hapus isi destination, gabungkan semua tabel+data dari source1 dan source2 (tanpa foreign key).';
    protected $usage       = 'db:merge [--tables=tbl1,tbl2] [--no-ignore] [--keep-fk]';
    public function run(array $params)
    {
        $tablesOpt = null;
        $useIgnore = !in_array('--no-ignore', $params, true); // default: INSERT IGNORE
        $keepFK    = in_array('--keep-fk', $params, true);    // default: strip FK
        foreach ($params as $p) { if (strpos($p, '--tables=') === 0) $tablesOpt = trim(substr($p, 9)); }
        try {
            $src1 = Database::connect('source1');
            $src2 = Database::connect('source2');
            $dst  = Database::connect('destination');
        } catch (\Throwable $e) {
            CLI::error('Gagal buka koneksi DB: '.$e->getMessage());
            return;
        }

        // 0) Matikan FK checks di destination
        $dst->query('SET FOREIGN_KEY_CHECKS=0');

        // 1) Bersihkan destination (drop semua tabel)
        $this->dropAllTables($dst);

        // 2) Kumpulkan daftar tabel (dan urutkan: source1 duluan)
        $tables1 = $src1->listTables() ?? [];
        $tables2 = $src2->listTables() ?? [];
        if ($tablesOpt) {
            $filter  = array_map('trim', explode(',', $tablesOpt));
            $tables1 = array_values(array_intersect($tables1, $filter));
            $tables2 = array_values(array_intersect($tables2, $filter));
        }
        $allTables = array_values(array_unique(array_merge($tables1, $tables2)));

        CLI::write('Tables total   : '.count($allTables));
        CLI::write('Insert mode    : '.($useIgnore ? 'INSERT IGNORE' : 'INSERT (error jika duplikat)'));
        CLI::write('Strip FK       : '.($keepFK ? 'TIDAK (keep foreign key)' : 'YA (hapus foreign key)'));
        CLI::newLine();

        // 3) Buat tabel di destination (tanpa FK)
        foreach ($allTables as $t) {
            $defSrc = in_array($t, $tables1, true) ? $src1 : $src2;
            $create = $this->getCreateTableSQL($defSrc, $t, $keepFK);
            if (!$create) {
                CLI::error("Gagal ambil CREATE TABLE untuk {$t}, skip.");
                continue;
            }
            try {
                $dst->query($create);
                if (!$this->tableExists($dst, $t)) { throw new \RuntimeException("Tabel {$t} gagal terCREATE."); }
                CLI::write("[CREATE] {$t}");
            } catch (\Throwable $e) {
                CLI::error("CREATE {$t} gagal: " . $e->getMessage());
                CLI::write("---- BEGIN CREATE {$t} SQL ----", 'red');
                CLI::write($create);
                CLI::write("----  END  CREATE {$t} SQL ----", 'red');
                continue;
            }
            $this->normalizeKeyTypes($defSrc, $dst, $t);
        }

        // 4) Copy data dari source1 lalu source2 (tanpa batching)
        foreach ([['db'=>$src1,'label'=>'source1'], ['db'=>$src2,'label'=>'source2']] as $pack) {
            $sdb    = $pack['db'];
            $label  = $pack['label'];
            $tables = $label === 'source1' ? $tables1 : $tables2;
            CLI::write("== Copy dari {$label} ==", 'cyan');
            foreach ($tables as $t) {
                if (!$this->tableExists($dst, $t)) {
                    CLI::write(">> {$t}: belum ada di destination (mungkin CREATE gagal), skip.", 'red');
                    continue;
                }
                try { $rows = $sdb->table($t)->get()->getResultArray(); } catch (\Throwable $e) {
                    CLI::error("SELECT * {$t} gagal: ".$e->getMessage());
                    continue;
                }

                $n = count($rows);
                CLI::write(">> {$t}: {$n} baris", 'yellow');
                if ($n === 0) continue;

                $cols = $this->intersectColumns($sdb, $dst, $t);
                if (empty($cols)) {
                    CLI::write("   Kolom tidak kompatibel, skip.", 'red');
                    continue;
                }
                $trimmed = [];
                foreach ($rows as $r) {
                    $tmp = [];
                    foreach ($cols as $c) $tmp[$c] = $r[$c] ?? null;
                    $trimmed[] = $tmp;
                }
                try {
                    $builder = $dst->table($t);
                    if ($useIgnore && method_exists($builder, 'ignore')) {
                        $builder->ignore(true)->insertBatch($trimmed);
                    } else if ($useIgnore) {
                        $this->insertIgnoreBatchRaw($dst, $t, $cols, $trimmed);
                    } else {
                        $builder->insertBatch($trimmed);
                    }
                    $this->syncAutoIncrement($dst, $t);
                    CLI::write("   OK");
                } catch (\Throwable $e) { CLI::error("   INSERT {$t} gagal: ".$e->getMessage()); }
            }
        }

        // 5) Aktifkan lagi FK checks
        $dst->query('SET FOREIGN_KEY_CHECKS=1');
        CLI::write('Selesai.', 'green');
    }

    protected function dropAllTables($db): void
    {
        $tables = $db->listTables() ?? [];
        if (empty($tables)) {
            CLI::write('Destination kosong (tidak ada tabel).', 'green');
            return;
        }
        foreach ($tables as $t) {
            try {
                $db->query("DROP TABLE IF EXISTS `{$t}`");
                CLI::write("[DROP] {$t}");
            } catch (\Throwable $e) {
                CLI::error("DROP {$t} gagal: ".$e->getMessage());
            }
        }
    }

    protected function getCreateTableSQL($src, string $table, bool $keepFK): ?string
    {
        try {
            $row = $src->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if (!$row || !isset($row['Create Table'])) return null;
            $sql = $row['Create Table'];
            $sql = preg_replace('/^CREATE TABLE `/i', 'CREATE TABLE IF NOT EXISTS `', $sql);
            if ($keepFK) return $sql;
            $lines = preg_split("/\r\n|\n|\r/", $sql);
            $filtered = [];
            foreach ($lines as $ln) {
                $trim = trim($ln);
                if (stripos($trim, 'FOREIGN KEY') !== false) continue;
                if (stripos($trim, 'CONSTRAINT') !== false && stripos($trim, 'FOREIGN KEY') !== false) continue;
                $filtered[] = $ln;
            }
            $sql = implode("\n", $filtered);
            $sql = preg_replace("/,\s*\)/", "\n)", $sql);
            return $sql;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function intersectColumns($src, $dst, string $table): array
    {
        try {
            $srcCols = array_map(fn($f) => $f->name, $src->getFieldData($table) ?? []);
            $dstCols = array_map(fn($f) => $f->name, $dst->getFieldData($table) ?? []);
            $common  = array_values(array_intersect($dstCols, $srcCols)); // urutan mengikuti dst
            return $common;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function insertIgnoreBatchRaw($db, string $table, array $cols, array $rows): void
    {
        if (empty($rows)) return;
        $colList = '`' . implode('`,`', $cols) . '`';
        $chunks  = [$rows];
        foreach ($chunks as $chunk) {
            $values = [];
            foreach ($chunk as $r) {
                $vals = [];
                foreach ($cols as $c) $vals[] = $db->escape($r[$c] ?? null);
                $values[] = '(' . implode(',', $vals) . ')';
            }
            $sql = "INSERT IGNORE INTO `{$table}` ({$colList}) VALUES " . implode(',', $values);
            $db->query($sql);
        }
    }

    protected function syncAutoIncrement($dst, string $table): void
    {
        try {
            $pk = null;
            foreach ($dst->getFieldData($table) ?? [] as $f) {
                if (!empty($f->primary_key) && stripos($f->type ?? '', 'int') !== false) {
                    $pk = $f->name; break;
                }
            }
            if (!$pk) return;
            $row = $dst->query("SELECT MAX(`{$pk}`) mx FROM `{$table}`")->getRow();
            $next = (int) ($row->mx ?? 0) + 1;
            if ($next < 1) $next = 1;
            $dst->query("ALTER TABLE `{$table}` AUTO_INCREMENT={$next}");
        } catch (\Throwable $e) { /* nothing to do */ }
    }

    protected function tableExists($db, string $table): bool
    {
        try {
            $row = $db->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1", [$table])->getRowArray();
            return !empty($row);
        } catch (\Throwable $e) {
            return false;
        }
    }
    protected function normalizeKeyTypes($src, $dst, string $table): void
    {
        $cols = $src->query("SHOW COLUMNS FROM `{$table}`")->getResultArray();
        $idx  = $src->query("SHOW INDEX FROM `{$table}`")->getResultArray();

        $pkCols = [];
        foreach ($cols as $c) {
            if (strcasecmp($c['Key'] ?? '', 'PRI') === 0) {
                $pkCols[] = $c['Field'];
            }
        }
        $indexedCols = [];
        foreach ($idx as $i) {
            if (!empty($i['Column_name'])) {
                $indexedCols[$i['Column_name']] = true;
            }
        }

        foreach ($cols as $c) {
            $name = $c['Field'];
            $type = strtolower($c['Type'] ?? '');
            $null = (strcasecmp($c['Null'] ?? '', 'YES') === 0) ? 'NULL' : 'NOT NULL';
            $isPK = in_array($name, $pkCols, true);
            $looksFK = (substr($name, -3) === '_id') || isset($indexedCols[$name]);

            // kandidat yang perlu diganti: double/float/decimal di PK/FK
            if (($isPK || $looksFK) && (str_starts_with($type, 'double') || str_starts_with($type, 'float') || str_starts_with($type, 'decimal'))) {
                try {
                    if ($isPK) {
                        // PK: INT UNSIGNED AUTO_INCREMENT
                        $dst->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$name}` INT UNSIGNED NOT NULL");
                        // pastikan jadi AUTO_INCREMENT bila masuk akal
                        // (kalau sebelumnya PK numeric, aman)
                        // MySQL butuh PK tunggal untuk AUTO_INCREMENT
                        if (count($pkCols) === 1) {
                            $dst->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$name}` INT UNSIGNED NOT NULL AUTO_INCREMENT");
                        }
                    } else {
                        // FK/calon FK: INT UNSIGNED (ikut nullability)
                        $dst->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$name}` INT UNSIGNED {$null}");
                    }
                } catch (\Throwable $e) {
                    CLI::error("Normalisasi tipe {$table}.{$name} gagal: ".$e->getMessage());
                }
            }
        }
    }

    protected function str_starts_with($haystack, $needle)
    {
        if (function_exists('str_starts_with')) return \str_starts_with($haystack, $needle);
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}