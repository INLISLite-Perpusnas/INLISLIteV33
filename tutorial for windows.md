# Tutorial Menjalankan INLISLite v3.3 di Windows

---

## Prasyarat — Spesifikasi Minimum PC / Laptop

Pastikan perangkat Anda memenuhi spesifikasi berikut sebelum melanjutkan:

| Komponen | Minimum | Disarankan |
|----------|---------|------------|
| **Sistem Operasi** | Windows 10 (64-bit) | Windows 10 / 11 (64-bit) |
| **Prosesor** | Intel Core i3 / AMD Ryzen 3 | Intel Core i5 / AMD Ryzen 5 |
| **RAM** | 4 GB | 8 GB atau lebih |
| **Ruang Disk** | 5 GB kosong | 10 GB atau lebih |
| **Browser** | Chrome / Firefox / Edge versi terbaru | Chrome / Edge versi terbaru |

> **Catatan:** Aplikasi ini **tidak bisa berjalan di Windows 32-bit**. Pastikan sistem operasi Anda adalah versi **64-bit**.

---

## Langkah 1 — Download File Aplikasi

1. Buka browser dan kunjungi: **https://inlislite.perpusnas.go.id**
2. Cari dan klik tombol **Download** untuk INLISLite v3.3
3. Tunggu hingga file ZIP selesai didownload
4. File akan tersimpan di folder **Downloads**

---

## Langkah 2 — Ekstrak File ZIP

1. Buka folder **Downloads** di File Explorer
2. Klik kanan file ZIP yang sudah didownload → pilih **Extract All...**
3. Ubah lokasi ekstrak menjadi **`C:\`** (root drive C)
4. Klik **Extract** dan tunggu hingga selesai

   Hasil ekstrak harus menghasilkan folder:
   ```
   C:\laragon\
   ```

---

## Langkah 3 — Buka Folder Laragon

1. Buka **File Explorer**
2. Navigasi ke drive **`C:\`**
3. Buka folder **`laragon`**
4. Cari file **`laragon.exe`**

---

## Langkah 4 — Jalankan Laragon

1. Double-klik file **`laragon.exe`**
2. Jika muncul **pop-up lisensi**, klik tombol **X** di pojok kanan atas pop-up tersebut untuk menutupnya

   ![Tutup popup dengan klik X di pojok kanan atas]

3. Laragon akan terbuka dan menampilkan panel kontrol

---

## Langkah 5 — Start All

1. Di panel Laragon, klik tombol **Start All**
2. Tunggu beberapa detik hingga indikator **Nginx** dan **MySQL** berubah menjadi **hijau**

---

## Langkah 6 — Buka Aplikasi di Browser

1. Klik tombol **Web** di panel Laragon
2. Browser akan otomatis terbuka dan menampilkan halaman **INLISLite v3.3**

---

## Langkah 7 — Login

Gunakan akun berikut untuk masuk:

| Field | Value |
|-------|-------|
| **Username** | `admin` |
| **Password** | `P@ssw0rd` |

1. Masukkan username dan password di halaman login
2. Klik tombol **Login**
3. Aplikasi INLISLite siap digunakan

---

## Troubleshooting

**Apache/MySQL tidak mau hijau saat Start All**
- Pastikan tidak ada aplikasi lain yang menggunakan port 80 (Apache) atau port 3306 (MySQL)
- Coba klik **Stop All** terlebih dahulu, tunggu beberapa detik, lalu **Start All** lagi

**Browser tidak terbuka otomatis saat klik Web**
- Buka browser secara manual
- Ketik di address bar: `http://localhost` lalu tekan Enter

**Halaman error / tidak tampil**
- Pastikan status Apache dan MySQL di Laragon sudah hijau sebelum membuka browser
- Coba refresh browser (F5)
