<?php
$request = service('request');
$errors = $errors ?? [];
$err = static fn(string $field) => $errors[$field] ?? null;
$asset = static fn(string $path) => base_url($path) . '?v=' . filemtime(FCPATH . $path);
$permalink = permalink('berita/' . $berita->slug);
$currentCover = !empty($berita->file_cover) ? base_url('uploads/berita/' . $berita->file_cover) : '';
$gallery = array_map(static fn($img) => [
    'name' => $img['name'],
    'url'  => base_url('uploads/berita/' . $img['name']),
], $old_file_image_data ?? []);
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= $asset('assets/css/ina-form.css') ?>">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div>Ubah Berita
                    <div class="page-title-subheading">Perbarui data berita berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cms/berita') ?>">Berita</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?= get_message('message') ?>

    <form id="frm" class="ina-form-card" method="post" enctype="multipart/form-data" action="" novalidate>
        <div class="ina-form-card-header">
            <h2>Data Berita</h2>
            <p>Kolom bertanda <span class="ina-required" aria-hidden="true">*</span> wajib diisi.</p>
        </div>

        <div class="ina-form-body">
            <!-- Text Field: Judul -->
            <div class="ina-field ina-col-8<?= $err('title') ? ' has-error' : '' ?>">
                <label class="ina-label" for="title">Judul Berita<span class="ina-required" aria-hidden="true">*</span></label>
                <input type="text" class="ina-input" id="title" name="title" placeholder="Contoh: Perpustakaan Gelar Lomba Bercerita"
                    value="<?= esc(set_value('title', $berita->title)) ?>" required aria-required="true"
                    aria-describedby="title_error title_help"<?= $err('title') ? ' aria-invalid="true"' : '' ?> />
                <p class="ina-error" id="title_error" <?= $err('title') ? '' : 'hidden' ?>><span><?= esc($err('title') ?? '') ?></span></p>
                <p class="ina-helper" id="title_help">Permalink: <a href="<?= esc($permalink, 'attr') ?>" target="_blank" rel="noopener"><?= esc($permalink) ?></a></p>
            </div>

            <!-- Text Field: Urutan (angka) -->
            <div class="ina-field ina-col-4<?= $err('sort') ? ' has-error' : '' ?>">
                <label class="ina-label" for="sort">Urutan<span class="ina-required" aria-hidden="true">*</span></label>
                <input type="number" class="ina-input" name="sort" id="sort" min="1" step="1" inputmode="numeric" placeholder="Contoh: 1"
                    value="<?= esc(set_value('sort', $berita->sort)) ?>" required aria-required="true"
                    aria-describedby="sort_error sort_help"<?= $err('sort') ? ' aria-invalid="true"' : '' ?> />
                <p class="ina-error" id="sort_error" <?= $err('sort') ? '' : 'hidden' ?>><span><?= esc($err('sort') ?? '') ?></span></p>
                <p class="ina-helper" id="sort_help">Angka 1 tampil paling awal.</p>
            </div>

            <!-- Text Area (editor): Uraian -->
            <div class="ina-field ina-col-12">
                <label class="ina-label" for="content">Uraian<span class="ina-optional">(opsional)</span></label>
                <textarea id="content" name="content" aria-describedby="content_help"><?= set_value('content', $berita->content) ?></textarea>
                <p class="ina-helper" id="content_help">Isi lengkap berita. Gunakan toolbar untuk format teks, tautan, dan gambar.</p>
            </div>

            <!-- Text Area: Keterangan -->
            <div class="ina-field ina-col-12">
                <label class="ina-label" for="description">Keterangan<span class="ina-optional">(opsional)</span></label>
                <textarea class="ina-textarea" id="description" name="description" placeholder="Tulis ringkasan singkat berita" aria-describedby="description_help"><?= esc(set_value('description', $berita->description)) ?></textarea>
                <p class="ina-helper" id="description_help">Ringkasan tampil di daftar berita.</p>
            </div>

            <!-- File Upload (satu file): Cover; cover tersimpan tampil sebagai upload item -->
            <div class="ina-field ina-col-6<?= $err('file_cover') ? ' has-error' : '' ?>">
                <span class="ina-label" id="file_cover_label">Cover<span class="ina-optional">(kosongkan jika tidak diganti)</span></span>
                <div class="ina-upload" data-accept="jpg,jpeg,png" data-max-mb="2" data-label="Cover"
                    data-current-url="<?= esc($currentCover, 'attr') ?>" data-current-name="<?= esc($berita->file_cover ?? '', 'attr') ?>"
                    data-error="<?= esc($err('file_cover') ?? '', 'attr') ?>">
                    <div class="ina-upload-base">
                        <button type="button" class="ina-upload-btn" aria-labelledby="file_cover_label file_cover_btn" aria-describedby="file_cover_desc file_cover_error">
                            <i class="fas fa-upload" aria-hidden="true"></i> <span id="file_cover_btn">Pilih File</span>
                        </button>
                        <p class="ina-upload-desc" id="file_cover_desc">
                            Seret gambar ke sini atau pilih file.<br>
                            <strong>Format:</strong> JPG, JPEG, PNG · <strong>Maks.:</strong> 2 MB
                        </p>
                        <input type="file" class="ina-upload-input" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" tabindex="-1" aria-hidden="true">
                    </div>
                    <div class="ina-upload-list" aria-live="polite"></div>
                    <p class="ina-error" id="file_cover_error" hidden><span></span></p>
                </div>
            </div>

            <!-- File Upload (banyak file): Galeri; foto tersimpan bisa ditandai hapus -->
            <div class="ina-field ina-col-6<?= $err('file_image') ? ' has-error' : '' ?>">
                <span class="ina-label" id="file_image_label">Galeri Foto<span class="ina-optional">(opsional)</span></span>
                <div class="ina-upload" data-accept="jpg,jpeg,png" data-max-mb="2" data-multiple="1" data-label="Foto galeri"
                    data-existing="<?= esc(json_encode($gallery), 'attr') ?>" data-remove-name="remove_gallery[]"
                    data-error="<?= esc($err('file_image') ?? '', 'attr') ?>">
                    <div class="ina-upload-base">
                        <button type="button" class="ina-upload-btn" aria-labelledby="file_image_label file_image_btn" aria-describedby="file_image_desc file_image_error">
                            <i class="fas fa-upload" aria-hidden="true"></i> <span id="file_image_btn">Pilih File</span>
                        </button>
                        <p class="ina-upload-desc" id="file_image_desc">
                            Tambah beberapa foto sekaligus. Klik ikon × untuk menghapus foto.<br>
                            <strong>Format:</strong> JPG, JPEG, PNG · <strong>Maks.:</strong> 2 MB per foto
                        </p>
                        <input type="file" class="ina-upload-input" name="file_image[]" id="file_image" multiple accept=".jpg,.jpeg,.png" tabindex="-1" aria-hidden="true">
                    </div>
                    <div class="ina-upload-list" aria-live="polite"></div>
                    <p class="ina-error" id="file_image_error" hidden><span></span></p>
                </div>
            </div>
        </div>

        <div class="ina-form-footer">
            <a href="<?= base_url('cms/berita') ?>" class="ina-btn ina-btn-secondary">Batal</a>
            <button type="submit" class="ina-btn ina-btn-primary-solid" name="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script src="<?= $asset('assets/js/ina-upload.js') ?>"></script>
<?= $this->include('Berita\Views\form_script') ?>
<?= $this->endSection('script') ?>
