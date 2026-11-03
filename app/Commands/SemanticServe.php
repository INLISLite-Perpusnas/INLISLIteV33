<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Opac\Libraries\Semantic\EmbeddingModel;
use Opac\Libraries\Semantic\VectorIndex;

/**
 * Service pencarian semantik OPAC (HTTP sederhana, satu proses, berjalan terus):
 *   php -d extension=ffi spark semantic:serve [--host 127.0.0.1] [--port 8765] [--refresh 60]
 *
 * Model e5 dan seluruh embedding katalog disimpan di memori, sehingga satu pencarian hanya butuh embed kata kunci
 * (±10 ms) dan satu perkalian matriks. Endpoint:
 *   GET /health                -> {"ok":true,"vectors":N}
 *   GET /search?q=...&k=48     -> {"ids":[...],"scores":[...],"took_ms":N}
 * Tidak ada autentikasi: biarkan host 127.0.0.1. Perubahan catalog_embeddings (dari semantic:index) dimuat ulang
 * otomatis tiap --refresh detik.
 */
class SemanticServe extends BaseCommand
{
    private const MAX_K = 2000;

    protected $group       = 'Opac';
    protected $name        = 'semantic:serve';
    protected $description = 'Jalankan service pencarian semantik OPAC (model dan indeks vektor di memori).';
    protected $usage       = 'semantic:serve [options]';
    protected $options     = [
        '--host'    => 'Alamat bind (default dari Config\Semantic, 127.0.0.1).',
        '--port'    => 'Port (default dari Config\Semantic, 8765).',
        '--refresh' => 'Interval cek perubahan embedding, detik (default 60).',
    ];

    private EmbeddingModel $model;
    private VectorIndex $index;

    public function run(array $params)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $config  = config('Semantic');
        $host    = CLI::getOption('host') ?: $config->host;
        $port    = (int) (CLI::getOption('port') ?: $config->port);
        $refresh = max(5, (int) (CLI::getOption('refresh') ?: 60));
        $db      = Database::connect('data');

        CLI::write('Memuat model ' . $config->modelId . ' dari ' . $config->modelDirectory() . ' ...', 'yellow');
        try {
            $this->model = EmbeddingModel::open($config->modelDirectory(), $config->modelId);
            $this->model->query('pemanasan pencarian semantik');
            $this->index = $this->loadIndex($db);
        } catch (\Throwable $e) {
            CLI::error('Service gagal dimulai: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        $server = @stream_socket_server("tcp://{$host}:{$port}", $errno, $error);
        if ($server === false) {
            CLI::error("Tidak bisa membuka {$host}:{$port}: {$error}");
            return EXIT_ERROR;
        }
        CLI::write("Pencarian semantik siap di http://{$host}:{$port} ({$this->index->size()} vektor). Ctrl+C untuk berhenti.", 'green');

        $nextCheck = time() + $refresh;
        while (true) {
            $read   = [$server];
            $write  = $except = null;
            $ready  = @stream_select($read, $write, $except, max(1, $nextCheck - time()));
            if ($ready > 0 && ($client = @stream_socket_accept($server, 0))) {
                $this->handle($client);
            }
            if (time() >= $nextCheck) {
                $this->refreshIndex($db);
                $nextCheck = time() + $refresh;
            }
        }
    }

    private function loadIndex($db): VectorIndex
    {
        $started = microtime(true);
        $index   = VectorIndex::loadFromDatabase($db);
        CLI::write(sprintf('Indeks vektor dimuat: %s vektor (%.1f s)', number_format($index->size()), microtime(true) - $started));
        return $index;
    }

    /** Muat ulang indeks bila isi catalog_embeddings berubah (jumlah baris atau UpdatedAt terbesar). */
    private function refreshIndex($db): void
    {
        try {
            try {
                $signature = VectorIndex::signature($db);
            } catch (\Throwable $e) {
                $db->reconnect(); // koneksi MySQL bisa diputus server setelah lama menganggur (wait_timeout)
                $signature = VectorIndex::signature($db);
            }
            if ($signature !== $this->index->signature) {
                $this->index = $this->loadIndex($db);
            }
        } catch (\Throwable $e) {
            CLI::error('Penyegaran indeks gagal: ' . $e->getMessage());
        }
    }

    private function handle($client): void
    {
        stream_set_timeout($client, 2);
        $request = '';
        while (!str_contains($request, "\r\n\r\n") && strlen($request) < 8192 && !feof($client)) {
            $chunk = fread($client, 2048);
            if ($chunk === false || $chunk === '') {
                break;
            }
            $request .= $chunk;
        }

        $status = 200;
        try {
            if (!preg_match('#^GET (\S+) HTTP/1\.[01]#', $request, $match)) {
                [$status, $body] = [400, ['error' => 'Permintaan tidak valid']];
            } else {
                $path = parse_url($match[1], PHP_URL_PATH);
                parse_str((string) parse_url($match[1], PHP_URL_QUERY), $query);
                if ($path === '/health') {
                    $body = ['ok' => true, 'vectors' => $this->index->size()];
                } elseif ($path === '/search') {
                    $body = $this->search(trim(mb_substr((string) ($query['q'] ?? ''), 0, 200)), (int) ($query['k'] ?? 48));
                } else {
                    [$status, $body] = [404, ['error' => 'Tidak ditemukan']];
                }
            }
        } catch (\Throwable $e) {
            CLI::error('Semantik: ' . $e->getMessage());
            [$status, $body] = [500, ['error' => 'Kesalahan internal']];
        }

        $json = json_encode($body);
        @fwrite($client, "HTTP/1.1 {$status} " . ($status === 200 ? 'OK' : 'Error') . "\r\nContent-Type: application/json\r\nContent-Length: "
            . strlen($json) . "\r\nConnection: close\r\n\r\n" . $json);
        fclose($client);
    }

    private function search(string $keyword, int $k): array
    {
        if ($keyword === '') {
            return ['ids' => [], 'scores' => [], 'took_ms' => 0];
        }
        $started = microtime(true);
        $vector  = $this->model->query($keyword);
        $embedMs = (microtime(true) - $started) * 1000;
        $hits    = $this->index->search($vector, max(1, min(self::MAX_K, $k)));
        $tookMs  = (microtime(true) - $started) * 1000;
        CLI::write(sprintf('Semantik: embed %.0f ms, cari %.0f ms, %d hasil', $embedMs, $tookMs - $embedMs, count($hits)));

        return ['ids' => array_column($hits, 'id'), 'scores' => array_column($hits, 'score'), 'took_ms' => (int) round($tookMs)];
    }
}
