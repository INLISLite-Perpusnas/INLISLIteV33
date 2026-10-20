<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<style>
    .suggestion-page {
        padding: 110px 0 80px;
        background: linear-gradient(160deg, #eff6ff 0%, #fff 54%, #f1f5f9 100%);
        min-height: 85vh;
    }
    .suggestion-heading {
        max-width: 760px;
        margin-bottom: 32px;
    }
    .suggestion-heading h2 {
        color: #12345b;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 750;
    }
    .suggestion-heading p { color: #52647b; }
    .suggestion-form {
        background: #fff;
        border: 1px solid #dce7f2;
        border-radius: 20px;
        box-shadow: 0 18px 50px rgba(26, 57, 95, .08);
        padding: clamp(24px, 4vw, 44px);
    }
    .suggestion-form label { font-weight: 600; color: #233b54; margin-bottom: 7px; }
    .suggestion-form .form-control { border-color: #cbd8e6; border-radius: 10px; padding: 11px 14px; }
    .suggestion-form .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
    .suggestion-form .btn-submit { background: #164e83; border: 0; border-radius: 10px; padding: 12px 24px; }
    .suggestion-form .btn-submit:hover { background: #123f6c; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<section class="suggestion-page">
    <div class="container">
        <div class="suggestion-heading">
            <span class="badge rounded-pill bg-primary mb-3">Layanan Perpustakaan</span>
            <h2>Usulan Koleksi</h2>
            <p>Belum menemukan koleksi yang dicari? Kirim usulan agar dapat dipertimbangkan perpustakaan.</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Periksa kembali isian:</strong>
                <ul class="mb-0">
                    <?php foreach ($errors as $message): ?>
                        <li><?= esc($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="suggestion-form" action="<?= site_url('home/usulan-koleksi') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12">
                    <label for="suggestion-title" class="form-label">Judul koleksi <span class="text-danger">*</span></label>
                    <input id="suggestion-title" name="Title" class="form-control" type="text" maxlength="255" required value="<?= esc(old('Title')) ?>" placeholder="Judul buku atau koleksi yang diusulkan">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-type" class="form-label">Jenis koleksi</label>
                    <input id="suggestion-type" name="Type" class="form-control" type="text" maxlength="50" value="<?= esc(old('Type')) ?>" placeholder="Contoh: Buku, majalah">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-author" class="form-label">Penulis</label>
                    <input id="suggestion-author" name="Author" class="form-control" type="text" maxlength="255" value="<?= esc(old('Author')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-subject" class="form-label">Subjek</label>
                    <input id="suggestion-subject" name="Subject" class="form-control" type="text" maxlength="255" value="<?= esc(old('Subject')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-publisher" class="form-label">Penerbit</label>
                    <input id="suggestion-publisher" name="Publisher" class="form-control" type="text" maxlength="50" value="<?= esc(old('Publisher')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-location" class="form-label">Tempat terbit</label>
                    <input id="suggestion-location" name="PublishLocation" class="form-control" type="text" maxlength="255" value="<?= esc(old('PublishLocation')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="suggestion-year" class="form-label">Tahun terbit</label>
                    <input id="suggestion-year" name="PublishYear" class="form-control" type="text" maxlength="50" value="<?= esc(old('PublishYear')) ?>">
                </div>
                <div class="col-12">
                    <label for="suggestion-comments" class="form-label">Keterangan tambahan</label>
                    <textarea id="suggestion-comments" name="Comments" class="form-control" rows="4" maxlength="2000" placeholder="Informasi lain yang membantu pencarian koleksi"><?= esc(old('Comments')) ?></textarea>
                </div>
                <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <span class="text-muted small">Kolom bertanda * wajib diisi.</span>
                    <button type="submit" class="btn btn-primary btn-submit"><i class="fa-solid fa-paper-plane me-2"></i>Kirim Usulan</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?= $this->endSection(); ?>