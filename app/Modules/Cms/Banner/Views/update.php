<?php
$request = service('request');
$errors = $errors ?? [];
$err = static fn(string $field) => $errors[$field] ?? null;
$asset = static fn(string $path) => base_url($path) . '?v=' . filemtime(FCPATH . $path);
$permalink = permalink('banner/' . slugify($banner->category) . '/' . $banner->slug);
$currentCover = !empty($banner->file_cover) ? base_url('uploads/banner/' . $banner->file_cover) : '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= $asset('assets/css/ina-form.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div>Ubah Banner
                    <div class="page-title-subheading">Perbarui data banner berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cms/banner') ?>">Banner</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?= get_message('message'); ?>

    <form id="frm" class="ina-form-card" method="post" enctype="multipart/form-data" action="" novalidate>
        <div class="ina-form-card-header">
            <h2>Data Banner</h2>
            <p>Kolom bertanda <span class="ina-required" aria-hidden="true">*</span> wajib diisi.</p>
        </div>

        <div class="ina-form-body">
            <!-- Text Field: Judul -->
            <div class="ina-field ina-col-12<?= $err('title') ? ' has-error' : '' ?>">
                <label class="ina-label" for="title">Judul Banner<span class="ina-required" aria-hidden="true">*</span></label>
                <input type="text" class="ina-input" id="title" name="title" placeholder="Contoh: Pekan Literasi 2026"
                    value="<?= esc(set_value('title', $banner->title)) ?>" required aria-required="true"
                    aria-describedby="title_error title_help"<?= $err('title') ? ' aria-invalid="true"' : '' ?> />
                <p class="ina-error" id="title_error" <?= $err('title') ? '' : 'hidden' ?>><span><?= esc($err('title') ?? '') ?></span></p>
                <p class="ina-helper" id="title_help">Permalink: <a href="<?= esc($permalink, 'attr') ?>" target="_blank" rel="noopener"><?= esc($permalink) ?></a></p>
            </div>

            <!-- Pilihan: Kategori -->
            <div class="ina-field ina-col-6">
                <label class="ina-label" for="category">Kategori<span class="ina-required" aria-hidden="true">*</span></label>
                <select class="ina-select" name="category" id="category" required aria-required="true">
                    <?php foreach (get_ref('ref-banner', 'slug') as $row) : ?>
                        <option value="<?= esc($row->name) ?>" <?= set_select('category', $row->name, $row->name == $banner->category) ?>><?= esc($row->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Text Field: Urutan (angka) -->
            <div class="ina-field ina-col-6<?= $err('sort') ? ' has-error' : '' ?>">
                <label class="ina-label" for="sort">Urutan<span class="ina-required" aria-hidden="true">*</span></label>
                <input type="number" class="ina-input" name="sort" id="sort" min="1" step="1" inputmode="numeric" placeholder="Contoh: 1"
                    value="<?= esc(set_value('sort', $banner->sort)) ?>" required aria-required="true"
                    aria-describedby="sort_error sort_help"<?= $err('sort') ? ' aria-invalid="true"' : '' ?> />
                <p class="ina-error" id="sort_error" <?= $err('sort') ? '' : 'hidden' ?>><span><?= esc($err('sort') ?? '') ?></span></p>
                <p class="ina-helper" id="sort_help">Angka 1 tampil paling awal.</p>
            </div>

            <!-- Text Area: Keterangan -->
            <div class="ina-field ina-col-12">
                <label class="ina-label" for="description">Keterangan<span class="ina-optional">(opsional)</span></label>
                <textarea class="ina-textarea" id="description" name="description" placeholder="Tulis keterangan singkat tentang banner ini" aria-describedby="description_help"><?= esc(set_value('description', $banner->description)) ?></textarea>
                <p class="ina-helper" id="description_help">Keterangan membantu pengelola mengenali isi banner.</p>
            </div>

            <!-- File Upload: varian Button di dalam form; file saat ini tampil sebagai upload item -->
            <div class="ina-field ina-col-12<?= $err('file_cover') ? ' has-error' : '' ?>">
                <span class="ina-label" id="file_cover_label">Gambar Banner<span class="ina-optional">(kosongkan jika tidak diganti)</span></span>
                <div class="ina-upload" data-accept="jpg,jpeg,png" data-max-mb="2" data-label="Gambar banner"
                    data-current-url="<?= esc($currentCover, 'attr') ?>" data-current-name="<?= esc($banner->file_cover ?? '', 'attr') ?>"
                    data-error="<?= esc($err('file_cover') ?? '', 'attr') ?>">
                    <div class="ina-upload-base">
                        <button type="button" class="ina-upload-btn" aria-labelledby="file_cover_label file_cover_btn" aria-describedby="file_cover_desc file_cover_error">
                            <i class="fas fa-upload" aria-hidden="true"></i> <span id="file_cover_btn">Pilih File</span>
                        </button>
                        <p class="ina-upload-desc" id="file_cover_desc">
                            Seret gambar ke sini atau pilih file.<br>
                            <strong>Format:</strong> JPG, JPEG, PNG · <strong>Ukuran maks.:</strong> 2 MB
                        </p>
                        <input type="file" class="ina-upload-input" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" tabindex="-1" aria-hidden="true">
                    </div>
                    <div class="ina-upload-list" aria-live="polite"></div>
                    <p class="ina-error" id="file_cover_error" hidden><span></span></p>
                </div>
            </div>
        </div>

        <div class="ina-form-footer">
            <a href="<?= base_url('cms/banner') ?>" class="ina-btn ina-btn-secondary">Batal</a>
            <button type="submit" class="ina-btn ina-btn-primary-solid" name="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= $asset('assets/js/ina-upload.js') ?>"></script>
<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000,
                icon:'error'
            });
        <?php endif; ?>

        // Validasi field wajib di browser; pesan tampil di bawah field terkait
        var form = document.getElementById('frm');
        var rules = [
            { el: document.getElementById('title'), check: function(v) { return v.trim() === '' ? 'Judul banner wajib diisi.' : ''; } },
            { el: document.getElementById('sort'), check: function(v) { return !/^[1-9]\d*$/.test(v.trim()) ? 'Urutan harus berupa angka bulat mulai dari 1.' : ''; } }
        ];
        var setError = function(el, message) {
            var box = document.getElementById(el.id + '_error');
            el.closest('.ina-field').classList.toggle('has-error', !!message);
            box.hidden = !message;
            box.querySelector('span').textContent = message || '';
            if (message) el.setAttribute('aria-invalid', 'true'); else el.removeAttribute('aria-invalid');
        };
        form.addEventListener('submit', function(e) {
            var first = null;
            rules.forEach(function(rule) {
                var message = rule.check(rule.el.value);
                setError(rule.el, message);
                if (message && !first) first = rule.el;
            });
            if (first) {
                e.preventDefault();
                first.focus();
            }
        });
        rules.forEach(function(rule) {
            rule.el.addEventListener('input', function() {
                if (!rule.check(rule.el.value)) setError(rule.el, '');
            });
        });
    });
</script>
<?= $this->endSection('script'); ?>
