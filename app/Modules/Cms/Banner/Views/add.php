<?php
$request = service('request');
$slug = $request->getGet('slug');
$errors = $errors ?? [];
$err = static fn(string $field) => $errors[$field] ?? null;
$asset = static fn(string $path) => base_url($path) . '?v=' . filemtime(FCPATH . $path);
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
                <div>Tambah Banner
                    <div class="page-title-subheading">Lengkapi data banner berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cms/banner') ?>">Banner</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?= get_message('message'); ?>

    <form id="frm_create" class="ina-form-card" method="post" enctype="multipart/form-data" action="<?= base_url('cms/banner/create?slug=' . $slug); ?>" novalidate>
        <div class="ina-form-card-header">
            <h2>Data Banner</h2>
            <p>Kolom bertanda <span class="ina-required" aria-hidden="true">*</span> wajib diisi.</p>
        </div>

        <div class="ina-form-body">
            <!-- Text Field: Judul -->
            <div class="ina-field ina-col-12<?= $err('title') ? ' has-error' : '' ?>">
                <label class="ina-label" for="title">Judul Banner<span class="ina-required" aria-hidden="true">*</span></label>
                <input type="text" class="ina-input" name="title" id="title" placeholder="Contoh: Pekan Literasi 2026"
                    value="<?= esc(set_value('title')) ?>" required aria-required="true"
                    aria-describedby="title_error"<?= $err('title') ? ' aria-invalid="true"' : '' ?> />
                <p class="ina-error" id="title_error" <?= $err('title') ? '' : 'hidden' ?>><span><?= esc($err('title') ?? '') ?></span></p>
            </div>

            <!-- Pilihan: Kategori -->
            <div class="ina-field ina-col-6">
                <label class="ina-label" for="category">Kategori<span class="ina-required" aria-hidden="true">*</span></label>
                <select class="ina-select" name="category" id="category" required aria-required="true">
                    <?php foreach (get_ref('ref-banner', 'slug') as $row) : ?>
                        <option value="<?= esc($row->name) ?>" <?= set_select('category', $row->name, slugify($row->name) == $slug) ?>><?= esc($row->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Text Area: Keterangan -->
            <div class="ina-field ina-col-12">
                <label class="ina-label" for="frm_create_description">Keterangan<span class="ina-optional">(opsional)</span></label>
                <textarea class="ina-textarea" id="frm_create_description" name="description" placeholder="Tulis keterangan singkat tentang banner ini" aria-describedby="description_help"><?= esc(set_value('description')) ?></textarea>
                <p class="ina-helper" id="description_help">Keterangan membantu pengelola mengenali isi banner.</p>
            </div>

            <!-- File Upload: varian Button di dalam form -->
            <div class="ina-field ina-col-12<?= $err('file_cover') ? ' has-error' : '' ?>">
                <span class="ina-label" id="file_cover_label">Gambar Banner<span class="ina-required" aria-hidden="true">*</span></span>
                <div class="ina-upload" data-accept="jpg,jpeg,png" data-max-mb="2" data-required="1" data-label="Gambar banner"
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
            <button type="submit" class="ina-btn ina-btn-primary-solid" name="submit">Simpan</button>
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

        // Judul wajib: tampilkan error di bawah field tanpa menunggu server
        var form = document.getElementById('frm_create');
        var title = document.getElementById('title');
        var titleError = document.getElementById('title_error');
        var setTitleError = function(message) {
            title.closest('.ina-field').classList.toggle('has-error', !!message);
            titleError.hidden = !message;
            titleError.querySelector('span').textContent = message || '';
            if (message) title.setAttribute('aria-invalid', 'true'); else title.removeAttribute('aria-invalid');
        };
        form.addEventListener('submit', function(e) {
            if (title.value.trim() === '') {
                e.preventDefault();
                setTitleError('Judul banner wajib diisi.');
                title.focus();
            }
        });
        title.addEventListener('input', function() {
            if (title.value.trim() !== '') setTitleError('');
        });
    });
</script>
<?= $this->endSection('script'); ?>
