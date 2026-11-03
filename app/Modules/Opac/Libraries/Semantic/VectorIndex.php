<?php

namespace Opac\Libraries\Semantic;

use Codewithkyrian\Transformers\Tensor\Tensor;
use Interop\Polite\Math\Matrix\NDArray;

/**
 * Indeks vektor di memori service semantic:serve: seluruh catalog_embeddings dimuat sebagai satu matriks float32
 * [n, 384], sehingga satu pencarian = satu perkalian matriks (BLAS) lalu ambil skor tertinggi.
 * Ukuran ±1,5 KB per katalog (100 ribu katalog ≈ 150 MB; pencarian ±50 ms di CPU).
 * Objek tidak berubah setelah dibuat; pembaruan dilakukan dengan memuat ulang dari database.
 */
final class VectorIndex
{
    /** @var int[] catalogs.ID per baris matriks */
    private array $ids;
    private ?Tensor $matrix;
    /** Penanda isi tabel saat dimuat (jumlah baris + UpdatedAt terbesar) untuk mendeteksi perubahan. */
    public readonly string $signature;

    private function __construct(array $ids, string $vectors, string $signature)
    {
        $this->ids       = $ids;
        $this->matrix    = $ids ? Tensor::fromString($vectors, NDArray::float32, [count($ids), EmbeddingModel::DIMENSIONS]) : null;
        $this->signature = $signature;
    }

    public static function signature($db): string
    {
        $row = $db->query('SELECT COUNT(*) AS total, MAX(UpdatedAt) AS updated FROM catalog_embeddings')->getRow();
        return ($row->total ?? 0) . '|' . ($row->updated ?? '');
    }

    public static function loadFromDatabase($db): self
    {
        $signature = self::signature($db);
        $bytes     = EmbeddingModel::DIMENSIONS * 4;
        $ids       = [];
        $vectors   = [];
        $lastId    = 0;
        // Per blok ID agar hasil query tidak menumpuk di memori klien MySQL sekaligus.
        do {
            $rows = $db->query('SELECT Catalog_id, Embedding FROM catalog_embeddings WHERE Catalog_id > ? ORDER BY Catalog_id LIMIT 5000', [$lastId])->getResultArray();
            foreach ($rows as $row) {
                $lastId = (int) $row['Catalog_id'];
                if (strlen($row['Embedding']) !== $bytes) {
                    continue;
                }
                $ids[]     = $lastId;
                $vectors[] = $row['Embedding'];
            }
        } while (count($rows) === 5000);

        return new self($ids, implode('', $vectors), $signature);
    }

    public function size(): int
    {
        return count($this->ids);
    }

    /** ID katalog paling mirip, urut kemiripan menurun. $query harus ternormalisasi (EmbeddingModel::query). */
    public function search(array $query, int $k): array
    {
        if ($this->matrix === null || $k <= 0) {
            return [];
        }
        $vector = Tensor::fromString(EmbeddingModel::pack($query), NDArray::float32, [EmbeddingModel::DIMENSIONS, 1]);
        $scores = array_values(unpack('g*', $this->matrix->matmul($vector)->toString()));
        arsort($scores);

        $result = [];
        foreach (array_slice($scores, 0, $k, true) as $row => $score) {
            $result[] = ['id' => $this->ids[$row], 'score' => round($score, 4)];
        }
        return $result;
    }
}
