<?php

namespace Opac\Libraries\Semantic;

use Codewithkyrian\Transformers\Models\Auto\AutoModel;
use Codewithkyrian\Transformers\PreTrainedTokenizers\AutoTokenizer;
use Codewithkyrian\Transformers\Tensor\Tensor;
use Codewithkyrian\Transformers\Transformers;
use Interop\Polite\Math\Matrix\NDArray;

/**
 * Embedding teks lokal dengan model multilingual-e5-small (ONNX quantized, 384 dimensi) lewat TransformersPHP.
 * Sama dengan EmbeddingModel di INLIS Enterprise (Java): awalan "query: " untuk kata kunci dan "passage: " untuk
 * dokumen, maksimal 128 token, mean pooling dengan attention mask, lalu normalisasi L2 (kosinus = hasil kali titik).
 *
 * Hanya untuk CLI (butuh ekstensi FFI): dipakai oleh `spark semantic:index` dan `spark semantic:serve`.
 * Padding dan pooling dikerjakan sendiri: tokenizer pustaka selalu mem-padding sampai maxLength dan
 * Tensor::normalize() tidak menghasilkan vektor satuan. Trie tokenizer diganti {@see PrefixTrie} (bocor memori).
 */
final class EmbeddingModel
{
    public const DIMENSIONS = 384;
    public const MAX_TOKENS = 128;

    private $tokenizer;
    private $model;

    private function __construct(string $modelDir, string $modelId)
    {
        if (!extension_loaded('ffi')) {
            throw new \RuntimeException('Ekstensi PHP FFI belum aktif. Jalankan dengan: php -d extension=ffi spark ...');
        }
        Transformers::setup()->setCacheDir($modelDir)->apply();
        $this->tokenizer = AutoTokenizer::fromPretrained($modelId);
        PrefixTrie::install($this->tokenizer);
        $this->model     = AutoModel::fromPretrained($modelId, quantized: true);
    }

    public static function open(string $modelDir, string $modelId): self
    {
        return new self($modelDir, $modelId);
    }

    /** Vektor untuk kata kunci pencarian. */
    public function query(string $text): array
    {
        return $this->embed(['query: ' . $text])[0];
    }

    /** Vektor untuk dokumen (katalog). */
    public function passages(array $texts): array
    {
        return $this->embed(array_map(static fn ($text) => 'passage: ' . $text, array_values($texts)));
    }

    /** Vektor (array float) menjadi 384 float32 little-endian untuk kolom catalog_embeddings.Embedding. */
    public static function pack(array $vector): string
    {
        return pack('g*', ...$vector);
    }

    private function embed(array $texts): array
    {
        $encoded = ($this->tokenizer)($texts, truncation: true, maxLength: self::MAX_TOKENS, returnTensor: false);
        $batch   = count($texts);
        $length  = max(array_map('count', $encoded['input_ids']));

        // Sisa baris berisi 0 (padding, attention 0) sehingga tidak ikut di mean pooling.
        $ids = $mask = $types = [];
        for ($row = 0; $row < $batch; $row++) {
            $padding      = array_fill(0, $length - count($encoded['input_ids'][$row]), 0);
            $ids[$row]    = array_merge($encoded['input_ids'][$row], $padding);
            $mask[$row]   = array_merge($encoded['attention_mask'][$row], $padding);
            $types[$row]  = array_fill(0, $length, 0);
        }
        $outputs = ($this->model)([
            'input_ids'      => new Tensor($ids, NDArray::int64, [$batch, $length]),
            'attention_mask' => new Tensor($mask, NDArray::int64, [$batch, $length]),
            'token_type_ids' => new Tensor($types, NDArray::int64, [$batch, $length]),
        ]);
        $hidden = unpack('g*', $outputs['last_hidden_state']->toString());

        $vectors = [];
        for ($row = 0; $row < $batch; $row++) {
            $vector = array_fill(0, self::DIMENSIONS, 0.0);
            $count  = 0;
            for ($token = 0; $token < $length; $token++) {
                if ($mask[$row][$token] == 0) {
                    continue;
                }
                $offset = (($row * $length) + $token) * self::DIMENSIONS + 1; // unpack() mulai dari indeks 1
                for ($dim = 0; $dim < self::DIMENSIONS; $dim++) {
                    $vector[$dim] += $hidden[$offset + $dim];
                }
                $count++;
            }
            $norm = 0.0;
            for ($dim = 0; $dim < self::DIMENSIONS; $dim++) {
                $vector[$dim] /= max(1, $count);
                $norm += $vector[$dim] * $vector[$dim];
            }
            $norm = sqrt($norm);
            if ($norm > 0) {
                for ($dim = 0; $dim < self::DIMENSIONS; $dim++) {
                    $vector[$dim] /= $norm;
                }
            }
            $vectors[] = $vector;
        }
        return $vectors;
    }
}
