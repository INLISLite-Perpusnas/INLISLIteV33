<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Opac\Libraries\Semantic\EmbeddingModel;
use Opac\Libraries\Semantic\SemanticSearch;

/**
 * Mengisi/menyegarkan catalog_embeddings untuk katalog OPAC:
 *   php -d extension=ffi spark semantic:index [--batch N] [--from ID] [--limit N] [--rebuild]
 *
 * Berjalan per blok ID naik dan bisa dilanjutkan (--from). Hanya katalog baru/berubah (hash teks berbeda) yang
 * di-embed; di akhir proses penuh, embedding katalog yang sudah tidak tampil di OPAC dihapus. Aman dijalankan
 * berulang (mis. tiap malam lewat Task Scheduler/cron). Service semantic:serve memuat perubahan otomatis.
 */
class SemanticIndex extends BaseCommand
{
    /** Dokumen per panggilan model; satu blok diurutkan menurut panjang agar padding token minimal. */
    private const CHUNK = 32;

    protected $group       = 'Opac';
    protected $name        = 'semantic:index';
    protected $description = 'Isi/segarkan embedding katalog untuk pencarian semantik OPAC (multilingual-e5-small).';
    protected $usage       = 'semantic:index [options]';
    protected $options     = [
        '--batch'   => 'Jumlah katalog dibaca per blok (default 256).',
        '--from'    => 'Mulai dari catalogs.ID setelah nilai ini (melanjutkan proses yang terhenti).',
        '--limit'   => 'Batas jumlah katalog yang diproses.',
        '--rebuild' => 'Embed ulang semua katalog walaupun teksnya tidak berubah (mis. setelah ganti model).',
    ];

    public function run(array $params)
    {
        ini_set('memory_limit', '2G');
        set_time_limit(0);

        $config  = config('Semantic');
        $batch   = max(1, min(4096, (int) (CLI::getOption('batch') ?: 256)));
        $cursor  = max(0, (int) CLI::getOption('from'));
        $limit   = CLI::getOption('limit') ? (int) CLI::getOption('limit') : PHP_INT_MAX;
        $rebuild = CLI::getOption('rebuild') !== null;
        $cleanup = $cursor === 0 && $limit === PHP_INT_MAX;

        $db = Database::connect('data');
        if (!$db->tableExists('catalog_embeddings')) {
            CLI::error('Tabel catalog_embeddings belum ada. Jalankan dulu: php spark migrate');
            return EXIT_ERROR;
        }

        CLI::write('Memuat model ' . $config->modelId . ' dari ' . $config->modelDirectory() . ' ...', 'yellow');
        try {
            $model = EmbeddingModel::open($config->modelDirectory(), $config->modelId);
        } catch (\Throwable $e) {
            CLI::error('Model gagal dimuat: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        $started  = microtime(true);
        $seen     = 0;
        $embedded = 0;
        while ($seen < $limit) {
            $rows = $db->query(
                'SELECT c.ID, c.Title, c.Author, c.Subject, c.Publisher, ce.ContentHash'
                . ' FROM catalogs c LEFT JOIN catalog_embeddings ce ON ce.Catalog_id = c.ID'
                . ' WHERE c.ID > ? AND c.IsOPAC = 1 ORDER BY c.ID LIMIT ?',
                [$cursor, (int) min($batch, $limit - $seen)]
            )->getResultArray();
            if (!$rows) {
                break;
            }
            $seen += count($rows);

            $changed = [];
            foreach ($rows as $row) {
                $cursor = (int) $row['ID'];
                $text   = SemanticSearch::documentText($row['Title'], $row['Author'], $row['Subject'], $row['Publisher']);
                if ($text === '') {
                    continue;
                }
                $hash = md5($text);
                if ($rebuild || $hash !== $row['ContentHash']) {
                    $changed[] = ['id' => $cursor, 'text' => $text, 'hash' => $hash];
                }
            }
            if ($changed) {
                $this->write($db, $changed, $this->embed($model, $changed));
                $embedded += count($changed);
            }

            $seconds = microtime(true) - $started;
            CLI::write(sprintf('id %d | dibaca %s | di-embed %s | %.1f dok/detik | memori %d MB', $cursor, number_format($seen), number_format($embedded), $embedded / max(1, $seconds), memory_get_usage() / 1048576));
        }

        if ($cleanup) {
            $db->query('DELETE ce FROM catalog_embeddings ce LEFT JOIN catalogs c ON c.ID = ce.Catalog_id AND c.IsOPAC = 1 WHERE c.ID IS NULL');
            CLI::write('Embedding katalog yang tidak lagi tampil di OPAC dihapus: ' . $db->affectedRows());
        }
        CLI::write(sprintf('Selesai: dibaca %s, di-embed %s, id terakhir %d, %.0f detik', number_format($seen), number_format($embedded), $cursor, microtime(true) - $started), 'green');
        return EXIT_SUCCESS;
    }

    /** Embedding per CHUNK setelah diurutkan menurut panjang teks; hasil dikembalikan sesuai urutan masukan. */
    private function embed(EmbeddingModel $model, array $docs): array
    {
        $order = array_keys($docs);
        usort($order, static fn ($a, $b) => strlen($docs[$a]['text']) <=> strlen($docs[$b]['text']));
        $vectors = [];
        foreach (array_chunk($order, self::CHUNK) as $chunk) {
            $result = $model->passages(array_map(static fn ($index) => $docs[$index]['text'], $chunk));
            foreach ($chunk as $position => $index) {
                $vectors[$index] = $result[$position];
            }
        }
        return $vectors;
    }

    /** Satu INSERT ... ON DUPLICATE KEY UPDATE per blok; vektor dikirim sebagai hex agar byte biner tidak berubah. */
    private function write($db, array $docs, array $vectors): void
    {
        $now    = date('Y-m-d H:i:s');
        $values = [];
        $binds  = [];
        foreach ($docs as $index => $doc) {
            $values[] = '(?, UNHEX(?), ?, ?)';
            array_push($binds, $doc['id'], bin2hex(EmbeddingModel::pack($vectors[$index])), $doc['hash'], $now);
        }
        $db->query(
            'INSERT INTO catalog_embeddings (Catalog_id, Embedding, ContentHash, UpdatedAt) VALUES ' . implode(', ', $values)
            . ' ON DUPLICATE KEY UPDATE Embedding = VALUES(Embedding), ContentHash = VALUES(ContentHash), UpdatedAt = VALUES(UpdatedAt)',
            $binds
        );
    }
}
