# Pencarian Semantik OPAC

Pencarian berdasarkan makna (mis. "buku tentang bercocok tanam" menemukan "Teknik Budidaya Padi") memakai model
`multilingual-e5-small` (ONNX quantized, 384 dimensi) yang berjalan lokal di CPU — tanpa GPU dan tanpa layanan luar.
Model dan cara pembuatan teks dokumen sama dengan INLIS Enterprise (Java), sehingga hasil kedua aplikasi konsisten.

## Komponen

| Bagian | Lokasi |
|---|---|
| Konfigurasi (`semantic.*` di `.env`) | `app/Config/Semantic.php` |
| Tabel vektor `catalog_embeddings` | `app/Database/Migrations/2026-11-01-000001_CreateCatalogEmbeddings.php` |
| Indexer | `php -d extension=ffi spark semantic:index` |
| Service (model + indeks vektor di memori) | `php -d extension=ffi spark semantic:serve` |
| Integrasi OPAC (opsi "Pencarian semantik") | `Opac::applySemanticSearch()` |

Request web tidak memuat model dan tidak butuh FFI: OPAC memanggil service di `127.0.0.1:8765`. Bila service mati,
OPAC otomatis memakai pencarian FULLTEXT dan menampilkan pemberitahuan.

## Pemasangan

1. `composer install` dengan PHP CLI yang ekstensi FFI-nya bisa dimuat
   (`php -d extension=ffi composer.phar install`). Plugin `codewithkyrian/platform-package-installer` mengunduh
   pustaka native ONNX Runtime sesuai OS.
2. `php spark migrate` (bukan `--all`) untuk membuat tabel `catalog_embeddings`.
3. Model: dijalankan pertama kali, indexer mengunduh `Xenova/multilingual-e5-small` dari Hugging Face ke
   `writable/semantic/models`. Untuk server offline, salin folder itu dari server lain
   (`writable/semantic/models/Xenova/multilingual-e5-small/{config.json,tokenizer.json,tokenizer_config.json,onnx/model_quantized.onnx}`).
4. `php -d extension=ffi spark semantic:index` — ±2 menit untuk 22 ribu katalog. Jadwalkan tiap malam
   (Task Scheduler / cron); hanya katalog baru/berubah yang di-embed.
5. Jalankan `php -d extension=ffi spark semantic:serve` sebagai service yang hidup terus (NSSM di Windows,
   systemd di Linux). Perubahan dari indexer dimuat otomatis tiap 60 detik.
6. `.env`: `semantic.enabled = true`.

`ffi.enable` cukup bawaan (`preload`): FFI hanya dipakai di CLI. Jangan bind service ke selain `127.0.0.1`,
karena tidak ada autentikasi.

## Kinerja (CPU 16 thread)

- Muat model: ±2 detik saat service start; RAM service ±300 MB + 1,5 KB per katalog.
- Satu pencarian: ±5 ms embed kata kunci + ±10–30 ms perkalian matriks (22 ribu katalog).
- Indexing: ±150 dokumen/detik.

## Catatan pustaka TransformersPHP 0.6

- `Tensor::normalize()` tidak menghasilkan vektor satuan, dan tokenizer mem-padding sampai `maxLength`;
  padding, mean pooling, dan normalisasi L2 dikerjakan sendiri di `EmbeddingModel`.
- `CharTrie::commonPrefixSearch()` membuat node baru untuk setiap teks (bocor memori, lambat); diganti
  `PrefixTrie` lewat reflection. Periksa kembali kedua hal ini bila pustaka di-upgrade.
