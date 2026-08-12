# INLISLite

## Apa itu INLISLite?

Kenali **INLISLite**, satu aplikasi dengan berbagai kemudahan dalam genggaman Anda. Mulai dari pencarian koleksi, peminjaman, keanggotaan, hingga akses literatur digital. Dirancang untuk membantu seluruh perpustakaan di Indonesia demi memenuhi keperluan pemustaka di berbagai daerah. Temukan inspirasi, referensi, dan wawasan baru dalam satu aplikasi ini.

**INLISLite, buat dunia literasi jadi lebih dekat dan menyenangkan! #SalamLiterasi**

Aplikasi ini dirancang langsung oleh **Divisi Pusat Data dan Informasi**, Perpustakaan Nasional Republik Indonesia, untuk memberikan pengalaman terbaik dalam mengelola perpustakaan berbasis digital demi mewujudkan modernisasi di lingkup perpustakaan.

Aplikasi ini dibangun di atas framework **CodeIgniter 4**, sebuah framework PHP full-stack yang ringan, cepat, fleksibel, dan aman.

---

## Persyaratan Server

- PHP versi 7.3 atau lebih tinggi (disarankan PHP 8.x)
- Ekstensi PHP yang wajib aktif:
  - `intl`
  - `mbstring`
  - `mysqlnd`
  - `json` (aktif secara default, jangan dimatikan)
  - `xml` (aktif secara default, jangan dimatikan)
  - `curl` (jika menggunakan library `HTTP\CURLRequest`)
- MySQL / MariaDB
- Composer
- Web server (Apache/Nginx) atau bisa menggunakan server bawaan PHP untuk pengembangan lokal

---

## Cara Menjalankan Aplikasi

### 1. Clone / Extract Project

Pastikan seluruh source code INLISLite sudah berada di folder project Anda, misalnya:

```
D:\laragon\www\inlislite
```

### 2. Install Dependency via Composer

Masuk ke folder project, lalu jalankan:

```bash
composer install
```

Jika sebelumnya sudah pernah di-install dan ingin memperbarui:

```bash
composer update
```

### 3. Konfigurasi File Environment (.env)

Salin file `env` menjadi `.env`:

```bash
cp env .env
```

Buka `.env`, lalu sesuaikan minimal bagian berikut:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = inlislite_v33
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

Sesuaikan `hostname`, `username`, `password`, dan `database` dengan konfigurasi MySQL di komputer/server Anda.

### 4. Buat Database & Import File SQL

Buat database baru terlebih dahulu (misalnya melalui phpMyAdmin, HeidiSQL, atau command line):

```sql
CREATE DATABASE inlislite_v33 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Kemudian import file database yang ada di folder download, `inlislite_v33.sql`, ke database tersebut.

**Via command line:**

```bash
mysql -u root -p inlislite_v33 < path/to/inlislite_v33.sql
```

**Via phpMyAdmin:**
1. Pilih database `inlislite_v33` yang baru dibuat.
2. Buka tab **Import**.
3. Pilih file `inlislite_v33.sql`.
4. Klik **Go / Kirim** dan tunggu proses import selesai.

### 5. Jalankan Aplikasi

**Penting:** Sejak CodeIgniter 4, file `index.php` **tidak lagi berada di root project**, melainkan di dalam folder `public/`. Web server harus diarahkan ke folder `public`, bukan ke root project.

**Opsi A — Menggunakan built-in server PHP (untuk development/testing cepat):**

```bash
php spark serve
```

Secara default aplikasi akan berjalan di `http://localhost:8080`.

**Opsi B — Menggunakan Laragon / Apache / Nginx:**

Arahkan document root virtual host ke folder:

```
inlislite/public
```

Contoh virtual host Nginx:

```nginx
server {
    listen 80;
    server_name inlislite.test;
    root /path/to/inlislite/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Jangan mengarahkan web server ke folder root project lalu mengakses `public/...` — ini praktik yang tidak disarankan karena akan membuka logic dan file framework yang seharusnya tersembunyi.

### 6. Verifikasi

Buka browser dan akses baseURL yang telah dikonfigurasi (misalnya `http://localhost:8080` atau `http://inlislite.test`). Jika koneksi database dan konfigurasi `.env` sudah benar, halaman utama INLISLite akan tampil.

---

## Troubleshooting Singkat

| Masalah | Kemungkinan Penyebab |
|---|---|
| Halaman blank / error 500 | Cek permission folder `writable/`, cek log di `writable/logs/` |
| Error koneksi database | Cek kredensial di `.env`, pastikan database sudah dibuat & di-import |
| CSS/JS tidak muncul | Cek `app.baseURL` di `.env` sudah sesuai dengan URL akses |
| `index.php` tidak ditemukan | Pastikan document root web server mengarah ke folder `public/`, bukan root project |

---

## Sumber & Dukungan

- Dokumentasi resmi CodeIgniter 4: https://codeigniter4.github.io/userguide/
- Repositori pengembangan CodeIgniter 4: https://github.com/codeigniter4/CodeIgniter4
- Dikembangkan oleh: Divisi Pusat Data dan Informasi, Perpustakaan Nasional Republik Indonesia

#SalamLiterasi
