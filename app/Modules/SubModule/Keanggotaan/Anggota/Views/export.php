<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-anggota-export.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-anggota-export.css') ?>">
<?= $this->endSection('style'); ?>
<?= $this->section('page'); ?>
<div class="app-main__inner anggota-export">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon"><i class="fas fa-file-export" aria-hidden="true"></i></div>
                <div>
                    <h1 class="anggota-export__title">Export Anggota</h1>
                    <p class="anggota-export__intro">Unduh data anggota sesuai kolom dan urutan template import.</p>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('anggota') ?>">Anggota</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Export Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card anggota-export__card">
        <div class="card-header">Atur data yang diunduh</div>
        <div class="card-body">
            <p class="anggota-export__description">Pilih pembuat dan periode pencatatan. Kosongkan kedua tanggal untuk mengunduh seluruh data anggota yang tampil di daftar.</p>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-warning" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <form action="<?= base_url('anggota/export') ?>" method="get">
                <div class="anggota-export__fields">
                    <div class="form-group">
                        <label for="create_by">Dibuat oleh</label>
                        <select class="form-control" name="create_by" id="create_by">
                            <option value="">Semua pembuat</option>
                            <?php foreach ($creators as $id => $username): ?>
                                <option value="<?= esc((string) $id, 'attr') ?>"><?= esc($username) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="anggota-export__hint">Berdasarkan kolom CreateBy pada data anggota.</small>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Tanggal mulai</label>
                        <input class="form-control" type="date" name="start_date" id="start_date">
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal akhir</label>
                        <input class="form-control" type="date" name="end_date" id="end_date">
                    </div>
                </div>
                <p class="anggota-export__hint anggota-export__period-note">Periode memakai tanggal data dibuat (CreateDate), termasuk tanggal mulai dan akhir.</p>
                <div class="anggota-export__actions">
                    <button type="submit" class="btn btn-primary anggota-export__download"><i class="fas fa-download" aria-hidden="true"></i> Unduh Excel</button>
                    <a class="anggota-export__template" href="<?= base_url('uploads/master-template/template_anggota.xlsx') ?>">Lihat template import</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>
<?= $this->section('script'); ?>
<script>
    const exportForm = document.querySelector('.anggota-export form');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function validatePeriod() {
        startDate.setCustomValidity(endDate.value && !startDate.value ? 'Isi tanggal mulai.' : '');
        endDate.setCustomValidity(startDate.value && !endDate.value
            ? 'Isi tanggal akhir.'
            : startDate.value && endDate.value && startDate.value > endDate.value
                ? 'Tanggal akhir harus setelah tanggal mulai.' : '');
    }

    startDate.addEventListener('change', validatePeriod);
    endDate.addEventListener('change', validatePeriod);
    exportForm.addEventListener('submit', function (event) {
        validatePeriod();
        if (!exportForm.reportValidity()) {
            event.preventDefault();
        }
    });
</script>
<?= $this->endSection('script'); ?>