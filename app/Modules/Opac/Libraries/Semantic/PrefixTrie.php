<?php

namespace Opac\Libraries\Semantic;

use Codewithkyrian\Transformers\DataStructures\CharTrie;
use Codewithkyrian\Transformers\DataStructures\CharTrieNode;
use Codewithkyrian\Transformers\Tokenizers\UnigramModel;

/**
 * Pengganti trie vocab tokenizer Unigram (XLM-R) milik TransformersPHP 0.6.
 *
 * CharTrie::commonPrefixSearch() bawaan memakai getChild() yang MEMBUAT node untuk setiap karakter yang belum ada,
 * sehingga trie membengkak ±0,6 MB per teks unik (indexing ribuan katalog kehabisan memori) dan setiap pencarian
 * menyusuri sisa kalimat sampai habis. Versi ini hanya membaca dan berhenti di karakter pertama yang tidak cocok;
 * hasil tokenisasi sama persis (node buatan versi lama tidak pernah bertanda leaf).
 */
final class PrefixTrie extends CharTrie
{
    public function __construct(private CharTrieNode $start)
    {
        // Sengaja tidak memanggil parent::__construct(): node akar diambil dari trie yang sudah dibangun.
    }

    /** Pasang ke tokenizer bila modelnya Unigram; aman dipanggil untuk tokenizer lain (tidak berbuat apa-apa). */
    public static function install($tokenizer): void
    {
        $model = $tokenizer->model ?? null;
        if (!$model instanceof UnigramModel) {
            return;
        }
        $trieProperty = new \ReflectionProperty(UnigramModel::class, 'trie');
        $trie         = $trieProperty->getValue($model);
        if ($trie instanceof self) {
            return;
        }
        $root = (new \ReflectionProperty(CharTrie::class, 'root'))->getValue($trie);
        $trieProperty->setValue($model, new self($root));
    }

    public function commonPrefixSearch(string $text): \Generator
    {
        $node   = $this->start;
        $prefix = '';
        for ($i = 0; ($ch = mb_substr($text, $i, 1)) !== ''; $i++) {
            $node = $node->children[$ch] ?? null;
            if ($node === null) {
                return;
            }
            $prefix .= $ch;
            if ($node->isLeaf) {
                yield $prefix;
            }
        }
    }
}
