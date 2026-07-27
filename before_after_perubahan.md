# Dokumentasi Perubahan Kode (Before & After)

Berikut adalah rincian modifikasi baris kode yang dilakukan untuk memperbaiki dua masalah:
1. Kesalahan generasi/pembuatan Nomor Panggil otomatis (huruf kapital/pemotongan string) di `add.php` dan `update.php`
2. Kesalahan render cetak PDF/Word untuk Label Roll dan Call Number menjadi 3 baris.

---

## 1. Modifikasi Katalog - Nomor Panggil Otomatis (add.php & update.php)

Perubahan ini dilakukan pada file `app/Modules/SubModule/Katalog/Views/add.php` dan `app/Modules/SubModule/Katalog/Views/update.php`.

### **Before (Sebelum Diubah)**
Di dalam fungsi *event listener* `nomorInput`, kode memotong nomor DDC maksimal 3 karakter, dan memberikan huruf kapital untuk huruf pertama judul:
```javascript
    const concatenatedValue =
        ddcValue.substring(0, 3).toUpperCase() + " " +
        pengarangValue.substring(0, 3).toUpperCase() + " " +
        judulValue.substring(0, 1).toUpperCase();
```

### **After (Setelah Diubah)**
Kode diubah agar mengambil **nilai DDC utuh** dan menggunakan huruf kecil (*lowercase*) untuk huruf pertama judul. Khusus untuk `update.php`, blok kode ini tidak ada sebelumnya sehingga disisipkan secara utuh.
```javascript
    const concatenatedValue =
        ddcValue + " " +
        pengarangValue.substring(0, 3).toUpperCase() + " " +
        judulValue.substring(0, 1).toLowerCase();
```

---

## 2. Modifikasi Cetak Eksemplar - Ekspor Dokumen Word 

Masalah terjadi karena terdapat perintah `return;` di dalam struktur perulangan (looping) *foreach*, yang mengakibatkan *template* hanya memproses 1 label dan langsung menghentikan proses ekspor Word. Berlaku pada seluruh *template* label (Contoh: `cetak-label-lr1.php`).

### **Before (Sebelum Diubah)**
```php
foreach ($LabelData as $row) {
    // ...
    if (isset($outputFormat) && $outputFormat == 'word') {
        echo $html;
        return; // Menghentikan loop pada putaran pertama!
    }
    $pdf->writeHTML($html, true, false, false, false, '');
}

$pdf->Output('label.pdf', 'D');
die;
```

### **After (Setelah Diubah)**
Perintah `return` dipindahkan keluar perulangan (diletakkan tepat sebelum PDF Output) dan di dalam *loop* menggunakan `continue`.
```php
foreach ($LabelData as $row) {
    // ...
    if (isset($outputFormat) && $outputFormat == 'word') {
        echo $html . '<br>';
        continue; // Lanjut ke label berikutnya
    }
    $pdf->writeHTML($html, true, false, false, false, '');
}

if (isset($outputFormat) && $outputFormat == 'word') {
    return; // Berhenti dengan lancar setelah semua diecho
}

$pdf->Output('label.pdf', 'D');
die;
```

---

## 3. Modifikasi Cetak Eksemplar - Posisi Center & Pola 3 Baris No. Panggil

Masalah terjadi karena pemotongan (*parsing*) hanya mendeteksi spasi biasa `\s+`. Selain itu, label tidak sejajar di tengah (*middle align*) pada format PDF. Berlaku pada seluruh *template* label (Contoh: `cetak-label-lr1.php` & `cetak-label-a4-1.php`).

### **Before (Sebelum Diubah)**
Mendeteksi pemisah hanya berdasarkan spasi tunggal, lalu dibungkus `div` yang merusak format *vertical-align*. Dan pada baris tabel (`<td>`) belum terdapat CSS `vertical-align: middle;`.
```php
// Pemotongan hanya berdasarkan karakter spasi
$callNumber = str_replace(' ', '<br>', htmlspecialchars($row['CallNumber']));

// ...
<td style="text-align: center;">
```

### **After (Setelah Diubah)**
Mendeteksi karakter spasi *maupun* karakter garis miring (`/`) menggunakan pola RegEx `[\s\/]+`. Lalu kita memasukkan parameter `vertical-align: middle;` untuk perataan vertikal ke tengah secara presisi.
```php
// Pemotongan berdasarkan spasi ganda maupun garis miring (/)
$parts = preg_split('/[\s\/]+/', trim($row['CallNumber']));
$callNumber = implode('<br>', array_map('htmlspecialchars', $parts));

// ...
<td style="text-align: center; vertical-align: middle;">
```
