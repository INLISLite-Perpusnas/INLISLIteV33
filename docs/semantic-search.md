# Pencarian Semantik OPAC

Pencarian berdasarkan makna (mis. "buku tentang bercocok tanam" menemukan "Teknik Budidaya Padi") memakai model
`multilingual-e5-small` (ONNX quantized, 384 dimensi) yang berjalan lokal di CPU — tanpa GPU dan tanpa layanan luar.
Model dan cara pembuatan teks dokumen sama dengan INLIS Enterprise (Java), sehingga hasil kedua aplikasi konsisten.

## Komponen

| Bagian | Lokasi |
|---|---|
| Konfigurasi (`semantic.*` di `.env`) | `app/Config/Semantic.php` |
| Tabel vektor `catalog_embeddings` | `app/Database/Migrations/2026-11-01-000001_CreateCatalogEmbeddings.php` |
| Indexer | `php spark semantic:index` |
| Service (model + indeks vektor di memori) | `php spark semantic:serve` |
| Integrasi OPAC (opsi "Pencarian semantik") | `Opac::applySemanticSearch()` |

Request web tidak memuat model dan tidak butuh FFI: OPAC memanggil service di `127.0.0.1:8765`. Bila service mati,
OPAC otomatis memakai pencarian FULLTEXT dan menampilkan pemberitahuan "Pencarian semantik sedang tidak tersedia".

Alur data:

1. `semantic:index` membaca katalog OPAC, membuat vektor, dan menyimpannya ke tabel `catalog_embeddings`.
   Hanya katalog baru/berubah yang di-embed; katalog yang tidak lagi tampil di OPAC dihapus dari tabel.
2. `semantic:serve` memuat model dan seluruh vektor ke memori, lalu melayani pencarian. Tiap 60 detik service
   memeriksa tabel dan memuat ulang indeks bila ada perubahan (tidak perlu restart).
3. Katalog yang ditambah/diedit baru masuk pencarian semantik setelah `semantic:index` berikutnya (dijadwalkan
   tiap pukul 00:00). Sebelum itu katalog tetap bisa ditemukan lewat pencarian kata kunci biasa.

## Kebutuhan

- PHP 8.4 dengan ekstensi FFI untuk CLI (`php -m` menampilkan `FFI`). Web server tidak membutuhkan FFI.
- Pustaka native ONNX Runtime di `vendor/codewithkyrian/transformers/shared/<os>/lib/`, diunduh oleh
  `composer install` sesuai OS. Folder `vendor` hasil salinan dari Windows tidak berisi pustaka Linux, dan sebaliknya.
- File model di `writable/semantic/models/Xenova/multilingual-e5-small/` (±130 MB). Folder ini tidak masuk git;
  diunduh otomatis dari Hugging Face saat indexing pertama, atau disalin manual untuk server offline
  (`config.json`, `tokenizer.json`, `tokenizer_config.json`, `onnx/model_quantized.onnx`).
- RAM ±300 MB untuk service + 1,5 KB per katalog.

## Pemasangan di Ubuntu

Contoh memakai folder `/var/www/html/INLISLIteV33` dan user web `www-data`; sesuaikan bila berbeda.
Bila `php -m` sudah menampilkan `FFI`, jalankan perintah tanpa `-d extension=ffi` (bila ditambahkan, PHP hanya
memberi peringatan `Module "FFI" is already loaded`). Bila FFI belum ada, pasang paket `php8.4-ffi`.

1. Pastikan pustaka ONNX Runtime untuk Linux tersedia:

   ```bash
   ls /var/www/html/INLISLIteV33/vendor/codewithkyrian/transformers/shared/linux-x86_64/lib/
   ```

   Bila kosong atau folder tidak ada, jalankan `composer install --no-dev` di folder aplikasi.

2. Buat tabel dan atur izin folder:

   ```bash
   cd /var/www/html/INLISLIteV33
   php spark migrate
   chown -R www-data:www-data writable
   ```

   Gunakan `php spark migrate` biasa, **bukan** `--all`.

3. Indexing pertama (±140 katalog/detik; mis. 60 ribu katalog ±7 menit):

   ```bash
   sudo -u www-data php spark semantic:index
   ```

   Tunggu sampai muncul `Selesai: ...`. Bila indexing terlanjur dijalankan sebagai root, ulangi `chown` di langkah 2.

4. Aktifkan di `.env`:

   ```
   semantic.enabled = true
   ```

5. Pasang service systemd:

   ```bash
   cat > /etc/systemd/system/inlis-semantic.service <<'EOF'
   [Unit]
   Description=INLIS OPAC semantic search
   After=network.target mysql.service

   [Service]
   User=www-data
   WorkingDirectory=/var/www/html/INLISLIteV33
   ExecStart=/usr/bin/php spark semantic:serve
   Restart=always
   RestartSec=5

   [Install]
   WantedBy=multi-user.target
   EOF

   systemctl daemon-reload
   systemctl enable --now inlis-semantic
   ```

   Service menyala sendiri saat server boot dan restart sendiri bila berhenti.

6. Jadwalkan indexing tiap pukul 00:00 (`crontab -u www-data -e`):

   ```
   0 0 * * * cd /var/www/html/INLISLIteV33 && /usr/bin/php spark semantic:index >> writable/logs/semantic-index.log 2>&1
   ```

   Jam mengikuti zona waktu server; periksa dengan `timedatectl`, ubah bila perlu dengan
   `timedatectl set-timezone Asia/Jakarta`.

7. Periksa:

   ```bash
   systemctl status inlis-semantic --no-pager
   curl http://127.0.0.1:8765/health          # {"ok":true,"vectors":...}
   ```

   Lalu buka OPAC dan aktifkan "Pencarian semantik" di form pencarian.

Perintah harian:

| Keperluan | Perintah |
|---|---|
| Status service | `systemctl status inlis-semantic` |
| Log service | `journalctl -u inlis-semantic -f` |
| Restart service | `systemctl restart inlis-semantic` |
| Indexing manual | `sudo -u www-data php spark semantic:index` |
| Log indexing terjadwal | `tail -f writable/logs/semantic-index.log` |

## Pemasangan di Windows (Laragon)

1. `composer install` dengan PHP CLI yang ekstensi FFI-nya bisa dimuat
   (`php -d extension=ffi composer.phar install`).
2. `php spark migrate` (bukan `--all`).
3. `php -d extension=ffi spark semantic:index`.
4. `.env`: `semantic.enabled = true`.
5. Jalankan service dengan [NSSM](https://nssm.cc) dari CMD Administrator:

   ```bat
   nssm install InlisSemantic "C:\laragon\bin\php\php-8.4.21-Win32-vs17-x64\php.exe" "-d extension=ffi spark semantic:serve"
   nssm set InlisSemantic AppDirectory "C:\laragon\www\inlislitev33"
   nssm set InlisSemantic Start SERVICE_AUTO_START
   nssm start InlisSemantic
   ```

6. Jadwalkan indexing tiap pukul 00:00:

   ```bat
   schtasks /create /tn InlisSemanticIndex /sc daily /st 00:00 /tr "cmd /c cd /d C:\laragon\www\inlislitev33 && C:\laragon\bin\php\php-8.4.21-Win32-vs17-x64\php.exe -d extension=ffi spark semantic:index"
   ```

Menyalin folder aplikasi beserta Laragon ke komputer Windows lain sudah membawa pustaka, model, tabel vektor,
dan `.env`; cukup jalankan/daftarkan ulang service (langkah 5).

## Keamanan

Service tidak memiliki autentikasi. Biarkan listen di `127.0.0.1` (bawaan) dan jangan buka port 8765 di firewall.

## Pemecahan masalah

| Gejala | Penyebab / tindakan |
|---|---|
| OPAC menampilkan "Pencarian semantik sedang tidak tersedia" | Service mati. Periksa `systemctl status inlis-semantic` dan `journalctl -u inlis-semantic -n 50`. Setelah service hidup, peringatan hilang dalam ≤30 detik. |
| Toggle "Pencarian semantik" tidak muncul | `semantic.enabled` belum `true` di `.env`. |
| `Ekstensi PHP FFI belum aktif` | Pasang/aktifkan FFI untuk PHP CLI. |
| `Tabel catalog_embeddings belum ada` | Jalankan `php spark migrate`. |
| Model gagal diunduh | Server offline; salin `writable/semantic/models/` dari komputer lain. |
| Katalog baru tidak muncul di pencarian semantik | Normal sampai indexing pukul 00:00; jalankan `semantic:index` manual bila perlu segera. |

## Kinerja

- Muat model: ±2 detik saat service start.
- Satu pencarian: ±5 ms embed kata kunci + ±10–30 ms perkalian matriks (22 ribu katalog).
- Indexing: ±140–160 katalog/detik; berikutnya hanya katalog baru/berubah.
- Dengan filter (Jenis Bahan, Pengarang, dll.) hasil semantik bisa sampai ±1000 katalog, tetap urut kemiripan.

## Catatan pustaka TransformersPHP 0.6

- `Tensor::normalize()` tidak menghasilkan vektor satuan, dan tokenizer mem-padding sampai `maxLength`;
  padding, mean pooling, dan normalisasi L2 dikerjakan sendiri di `EmbeddingModel`.
- `CharTrie::commonPrefixSearch()` membuat node baru untuk setiap teks (bocor memori, lambat); diganti
  `PrefixTrie` lewat reflection. Periksa kembali kedua hal ini bila pustaka di-upgrade.
