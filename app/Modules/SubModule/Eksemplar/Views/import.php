<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-guestbook-import.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-guestbook-import.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-eksemplar-import.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-eksemplar-import.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner ei-page">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-cloud-upload icon-gradient bg-strong-bliss" aria-hidden="true"></i>
                </div>
                <div>Import eksemplar Excel
                    <div class="page-title-subheading">Tambahkan data katalog dan eksemplar sekaligus dari file Excel</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home" aria-hidden="true"></i> Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('eksemplar') ?>">Eksemplar</a></li>
                        <li class="breadcrumb-item" aria-current="page">Import Excel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            Unggah file Excel
            <div class="gb-import-actions">
                <a href="<?= base_url('eksemplar/download-template') ?>" class="btn btn-outline-primary gb-import-btn">
                    <i class="fa fa-download" aria-hidden="true"></i> Unduh template
                </a>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>

            <div class="ei-notice" role="note">
                <i class="fa fa-info-circle ei-notice-icon" aria-hidden="true"></i>
                <div>
                    <p class="ei-notice-title">Disarankan maksimal 1.000 baris per file</p>
                    <p>Bagi data yang lebih banyak menjadi beberapa file, karena proses bisa terputus jika berjalan terlalu lama.</p>
                    <p>Jika terputus, baris yang sudah diproses tetap tersimpan. Saat file diunggah ulang, baris tersebut ditolak karena barcode-nya sudah ada, dan baris lainnya tetap masuk. Isi <code>NO_BARCODE</code> atau <code>NO_INDUK</code> di setiap baris agar tidak ada data yang masuk dua kali.</p>
                </div>
            </div>

            <form id="form_import" enctype="multipart/form-data">
                <div class="gb-upload-field" id="upload-area">
                    <i class="fa fa-cloud-upload-alt gb-upload-icon" aria-hidden="true"></i>
                    <p class="gb-upload-title">Seret file Excel ke sini atau</p>
                    <button type="button" class="btn btn-outline-primary gb-upload-pick" id="pick-file">Pilih file</button>
                    <p class="gb-upload-hint">Format .xlsx atau .xls, satu file, maksimal 10 MB.</p>
                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" hidden>
                </div>

                <div class="gb-upload-item" id="upload-item" data-state="idle" hidden>
                    <i class="fa fa-file-excel gb-upload-item-icon" aria-hidden="true"></i>
                    <div class="gb-upload-item-body">
                        <div class="gb-upload-item-name" id="file-name"></div>
                        <div class="gb-upload-item-desc" id="file-desc" role="status" aria-live="polite"></div>
                        <div class="gb-upload-progress" id="file-progress" hidden><span id="file-bar"></span></div>
                    </div>
                    <button type="button" class="gb-upload-remove" id="file-remove" aria-label="Hapus file">
                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
                    </button>
                </div>

                <ul class="gb-import-errors" id="import-errors" tabindex="0" aria-label="Daftar baris yang perlu diperbaiki" hidden></ul>

                <div class="ei-form-actions">
                    <a class="btn btn-outline-primary gb-import-btn-lg" id="view-catalog" href="<?= base_url('katalog') ?>" hidden>Lihat data katalog</a>
                    <button type="submit" class="btn btn-primary gb-import-btn-lg" id="submit-import" disabled>Import</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">Panduan pengisian</div>
        <div class="card-body">
            <p class="ei-text">Gunakan template yang tersedia atau template INLISLite 3. Kolom bertanda <span class="ei-required">*</span> wajib diisi.</p>

            <div class="row">
                <div class="col-md-6">
                    <h3 class="ei-heading">Data katalog</h3>
                    <dl class="ei-columns">
                        <dt>JENIS_BAHAN</dt><dd>Sesuai master. Jika kosong: Monograf</dd>
                        <dt>JUDUL_UTAMA <span class="ei-required">*</span></dt><dd>Judul utama</dd>
                        <dt>ANAK_JUDUL</dt><dd>Anak judul</dd>
                        <dt>PERNYATAAN_TANGGUNGJAWAB</dt><dd>Penanggung jawab</dd>
                        <dt>TAJUK_PENGARANG</dt><dd>Pengarang utama</dd>
                        <dt>TAJUK_PENGARANG_BADAN_KOOPERASI</dt><dd>Badan pengarang</dd>
                        <dt>PENGARANG_TAMBAHAN_NAMA_ORANG</dt><dd>Pisahkan dengan titik koma</dd>
                        <dt>PENGARANG_TAMBAHAN_NAMA_BADAN</dt><dd>Pisahkan dengan titik koma</dd>
                        <dt>EDISI</dt><dd>Edisi</dd>
                        <dt>KOTA_TERBIT</dt><dd>Tempat terbit</dd>
                        <dt>PENERBIT</dt><dd>Nama penerbit</dd>
                        <dt>TAHUN_TERBIT</dt><dd>Tahun terbit</dd>
                        <dt>JUMLAH_HALAMAN</dt><dd>Contoh: 120 hlm.</dd>
                        <dt>DIMENSI</dt><dd>Contoh: 25 cm.</dd>
                        <dt>ISBN, ISSN, ISMN</dt><dd>Pisahkan dengan titik koma</dd>
                        <dt>NO_DDC</dt><dd>Nomor klasifikasi DDC</dd>
                        <dt>NOMOR_PANGGIL_KATALOG</dt><dd>Pisahkan dengan titik koma</dd>
                        <dt>ABSTRAK</dt><dd>Pisahkan dengan titik koma</dd>
                        <dt>BAHASA</dt><dd>Kode bahasa, contoh: ind</dd>
                        <dt>SUBJEK_TOPIK</dt><dd>Pisahkan dengan titik koma</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h3 class="ei-heading">Data eksemplar</h3>
                    <dl class="ei-columns">
                        <dt>TGL_PENGADAAN <span class="ei-required">*</span></dt><dd>Format dd-mm-yyyy, contoh: 14-02-2015</dd>
                        <dt>NO_INDUK</dt><dd>Nomor induk</dd>
                        <dt>NO_BARCODE</dt><dd>Harus unik. Jika kosong: dibuat otomatis</dd>
                        <dt>NO_RFID</dt><dd>Jika kosong: dibuat otomatis</dd>
                        <dt>JENIS_SUMBER</dt><dd>Sesuai master. Jika kosong: Pembelian</dd>
                        <dt>NAMA_SUMBER <span class="ei-required">*</span></dt><dd>Sesuai master Nama Sumber Perolehan</dd>
                        <dt>MATA_UANG</dt><dd>Kode mata uang. Jika kosong: IDR</dd>
                        <dt>HARGA</dt><dd>Angka tanpa titik atau koma. Jika kosong: 0</dd>
                        <dt>KODE_LOKASI_PERPUSTAKAAN <span class="ei-required">*</span></dt><dd>Kode lokasi perpustakaan</dd>
                        <dt>KODE_LOKASI_RUANG <span class="ei-required">*</span></dt><dd>Kode ruang di perpustakaan tersebut</dd>
                        <dt>AKSES</dt><dd>Sesuai master. Jika kosong: Dapat dipinjam</dd>
                        <dt>KATEGORI</dt><dd>Sesuai master. Jika kosong: Koleksi Umum</dd>
                        <dt>MEDIA</dt><dd>Sesuai master. Jika kosong: Buku</dd>
                        <dt>KETERSEDIAAN</dt><dd>Sesuai master. Jika kosong: Tersedia</dd>
                        <dt>NOMOR_PANGGIL_EKSEMPLAR</dt><dd>Nomor panggil eksemplar</dd>
                        <dt>EDISI_SERIAL</dt><dd>Khusus terbitan berkala</dd>
                        <dt>TGL_TERBIT_EDISI_SERIAL</dt><dd>Format dd-mm-yyyy</dd>
                        <dt>BAHAN_SERTAAN_SERIAL</dt><dd>Khusus terbitan berkala</dd>
                        <dt>KETERANGAN_LAIN_SERIAL</dt><dd>Khusus terbitan berkala</dd>
                    </dl>
                </div>
            </div>

            <h3 class="ei-heading">Cara data diproses</h3>
            <ul class="ei-list">
                <li>Nama pada kolom bertuliskan "Sesuai master" harus sama dengan nama di menu master, tanpa membedakan huruf besar-kecil.</li>
                <li>Baris yang salah dilewati dan ditampilkan di hasil import. Baris lain tetap masuk.</li>
                <li>Buku dengan jenis bahan, judul, pengarang, edisi, kota, penerbit, dan tahun terbit yang sama masuk ke satu katalog, dengan satu eksemplar per baris.</li>
                <li>Nomor barcode tidak boleh sama dengan eksemplar yang sudah ada.</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        const MAX_SIZE = 10 * 1024 * 1024;
        const TIMEOUT_MESSAGE = 'Proses terlalu lama dan terputus. Baris yang sudah diproses tetap tersimpan. Bagi file menjadi maksimal 1.000 baris per file, lalu unggah ulang.';
        const form = $('#form_import');
        const field = $('#upload-area');
        const input = $('#excel_file');
        const pick = $('#pick-file');
        const item = $('#upload-item');
        const desc = $('#file-desc');
        const progress = $('#file-progress');
        const bar = $('#file-bar');
        const remove = $('#file-remove');
        const errorList = $('#import-errors');
        const submit = $('#submit-import');
        const viewCatalog = $('#view-catalog');
        let selected = null;
        let finished = false;
        let uploading = false;

        function formatSize(bytes) {
            return bytes < 1024 * 1024
                ? Math.max(1, Math.round(bytes / 1024)) + ' KB'
                : (bytes / 1024 / 1024).toFixed(1) + ' MB';
        }

        function setFieldDisabled(disabled) {
            field.toggleClass('is-disabled', disabled);
            pick.prop('disabled', disabled);
        }

        function setItem(state, text) {
            item.prop('hidden', false).attr('data-state', state);
            desc.text(text);
            progress.prop('hidden', state !== 'uploading');
            remove.prop('hidden', state === 'uploading' || finished);
        }

        function showErrors(errors) {
            errorList.empty();
            (errors || []).forEach(function(message) {
                errorList.append($('<li>').text(message));
            });
            errorList.prop('hidden', errorList.children().length === 0);
        }

        function reset() {
            selected = null;
            finished = false;
            input.val('');
            item.prop('hidden', true).attr('data-state', 'idle');
            bar.css('width', '0');
            showErrors([]);
            setFieldDisabled(false);
            viewCatalog.prop('hidden', true);
            submit.text('Import').prop('disabled', true);
        }

        function chooseFile(file) {
            if (!file || field.hasClass('is-disabled')) {
                return;
            }

            reset();
            $('#file-name').text(file.name);
            setFieldDisabled(true);

            if (!/\.(xlsx|xls)$/i.test(file.name)) {
                setItem('error', 'File harus berformat Excel (.xlsx atau .xls). Hapus file ini, lalu pilih file lain.');
                return;
            }
            if (file.size > MAX_SIZE) {
                setItem('error', 'Ukuran file ' + formatSize(file.size) + ', maksimal 10 MB. Bagi data menjadi beberapa file.');
                return;
            }

            selected = file;
            setItem('selected', formatSize(file.size));
            submit.prop('disabled', false);
        }

        function failed(message, errors) {
            uploading = false;
            selected = null;
            setItem('error', message);
            showErrors(errors);
            submit.text('Import').prop('disabled', true);
        }

        pick.on('click', function() {
            input.trigger('click');
        });
        input.on('change', function() {
            chooseFile(this.files[0]);
        });
        remove.on('click', function() {
            reset();
            pick.trigger('focus');
        });
        field.on('dragenter dragover', function(e) {
            e.preventDefault();
            if (!field.hasClass('is-disabled')) {
                field.addClass('is-dragover');
            }
        });
        field.on('dragleave drop', function(e) {
            e.preventDefault();
            field.removeClass('is-dragover');
        });
        field.on('drop', function(e) {
            chooseFile(e.originalEvent.dataTransfer.files[0]);
        });

        form.on('submit', function(e) {
            e.preventDefault();

            if (finished) {
                reset();
                pick.trigger('focus');
                return;
            }
            if (!selected || uploading) {
                return;
            }

            const body = new FormData();
            body.append('excel_file', selected);

            uploading = true;
            setItem('uploading', 'Mengunggah file...');
            showErrors([]);
            submit.text('Memproses...').prop('disabled', true);

            $.ajax({
                url: '<?= base_url('eksemplar/uploadexcel') ?>',
                type: 'POST',
                data: body,
                processData: false,
                contentType: false,
                dataType: 'json',
                xhr: function() {
                    const xhr = $.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                bar.css('width', Math.round(event.loaded / event.total * 100) + '%');
                                if (event.loaded === event.total) {
                                    desc.text('Memeriksa dan menyimpan data. Jangan tutup halaman ini.');
                                }
                            }
                        });
                    }
                    return xhr;
                },
                success: function(response) {
                    const data = response.data;
                    uploading = false;
                    selected = null;
                    finished = true;
                    setItem(data.error_count > 0 ? 'warning' : 'success',
                        data.success_count + ' baris berhasil diimport' + (data.error_count > 0 ? ', ' + data.error_count + ' baris dilewati.' : '.'));
                    showErrors(data.errors);
                    viewCatalog.prop('hidden', false);
                    submit.text('Import file lain').prop('disabled', false);
                },
                error: function(xhr) {
                    const json = xhr.responseJSON;
                    let message = 'Terjadi kendala di server. Muat ulang halaman, lalu coba lagi.';
                    if (xhr.status === 504 || xhr.status === 502 || xhr.status === 524) {
                        message = TIMEOUT_MESSAGE;
                    } else if (xhr.status === 0) {
                        message = 'Koneksi terputus. Baris yang sudah diproses tetap tersimpan. Periksa jaringan, lalu unggah ulang.';
                    } else if (json && json.message) {
                        message = json.message;
                    } else if (json && json.messages) {
                        message = Object.values(json.messages).join(' ');
                    }
                    failed(message, json && json.errors);
                }
            });
        });
    });
</script>
<?= $this->endSection('script'); ?>
