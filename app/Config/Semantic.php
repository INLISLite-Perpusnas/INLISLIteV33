<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Pencarian semantik OPAC (model multilingual-e5-small, berjalan lokal di CPU).
 *
 * Model dijalankan oleh service CLI `php -d extension=ffi spark semantic:serve` yang menyimpan model dan indeks
 * vektor di memori; OPAC memanggilnya lewat HTTP ke host:port di bawah. Bila service tidak aktif, OPAC otomatis
 * memakai pencarian FULLTEXT biasa. Semua nilai bisa ditimpa dari .env, mis. `semantic.enabled = true`.
 */
class Semantic extends BaseConfig
{
    /**
     * Tampilkan opsi pencarian semantik di OPAC.
     *
     * @var bool
     */
    public $enabled = false;

    /**
     * Alamat service semantic:serve. Biarkan 127.0.0.1: service tidak memakai autentikasi.
     *
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * @var int
     */
    public $port = 8765;

    /**
     * Batas waktu (detik) OPAC menunggu service sebelum kembali ke FULLTEXT.
     *
     * @var float
     */
    public $timeout = 2.0;

    /**
     * Folder model (struktur cache Hugging Face: <modelDir>/<modelId>/onnx/model_quantized.onnx).
     * Kosong = WRITEPATH . 'semantic/models'.
     *
     * @var string
     */
    public $modelDir = '';

    /**
     * @var string
     */
    public $modelId = 'Xenova/multilingual-e5-small';

    /**
     * Jumlah hasil semantik maksimum yang ditampilkan (tanpa filter tambahan).
     *
     * @var int
     */
    public $maxResults = 48;

    /**
     * Jumlah kandidat bila ada filter tambahan (Jenis Bahan, Pengarang, dst.), karena sebagian besar bisa gugur.
     *
     * @var int
     */
    public $filteredCandidates = 1000;

    public function modelDirectory(): string
    {
        return $this->modelDir !== '' ? rtrim($this->modelDir, '/\\') : WRITEPATH . 'semantic' . DIRECTORY_SEPARATOR . 'models';
    }
}
