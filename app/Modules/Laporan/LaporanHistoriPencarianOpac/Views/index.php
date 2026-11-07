<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon"><i class="pe-7s-graph2 icon-gradient bg-strong-bliss"></i></div>
                <div>Laporan Histori Pencarian OPAC
                    <div class="page-title-subheading">Pilih kolom dan periode untuk meninjau atau mengekspor histori pencarian.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item">Laporan</li>
                        <li class="breadcrumb-item active" aria-current="page">Histori Pencarian OPAC</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
    <?php endif; ?>
    <?php if (!$tableExists): ?>
        <div class="alert alert-warning" role="alert">Tabel <code>opaclogs</code> belum tersedia. Jalankan migrasi database sebelum membuat laporan.</div>
    <?php else: ?>
        <div class="main-card mb-3 card">
            <div class="card-header"><i class="header-icon lnr-funnel icon-gradient bg-plum-plate"></i> Filter Laporan</div>
            <div class="card-body">
                <form method="post" action="<?= base_url('laporan-opac-histori-pencarian/index') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0 font-weight-bold">Pilih Kolom</label>
                            <label class="form-check-label" for="select_all_columns"><input type="checkbox" id="select_all_columns" checked> Pilih Semua</label>
                        </div>
                        <div class="row">
                            <?php foreach ($columns as $key => $label): ?>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label" for="column_<?= esc($key, 'attr') ?>">
                                        <input type="checkbox" class="column-checkbox" id="column_<?= esc($key, 'attr') ?>" name="columns[]" value="<?= esc($key, 'attr') ?>" <?= in_array($key, $options['columns'], true) ? 'checked' : '' ?>>
                                        <?= esc($label) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input class="form-control" type="date" id="start_date" name="start_date" value="<?= esc($options['start_date'], 'attr') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input class="form-control" type="date" id="end_date" name="end_date" value="<?= esc($options['end_date'], 'attr') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="keyword">Kata Kunci</label>
                            <input class="form-control" type="search" id="keyword" name="keyword" maxlength="200" value="<?= esc($options['keyword'], 'attr') ?>" placeholder="Semua kata kunci">
                        </div>
                    </div>
                    <p class="text-muted">Periode berdasarkan waktu pencarian. Kosongkan kedua tanggal untuk seluruh histori. PDF maksimal 2.000 baris; gunakan Excel untuk data lebih banyak.</p>
                    <div class="d-flex flex-wrap" style="gap: 8px">
                        <button type="submit" class="btn btn-ina-outline">Tampilkan Pratinjau</button>
                        <button type="submit" class="btn btn-catalog-add" formaction="<?= base_url('laporan-opac-histori-pencarian/export') ?>"><i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel</button>
                        <button type="submit" class="btn btn-ina-outline" formaction="<?= base_url('laporan-opac-histori-pencarian/export_pdf') ?>"><i class="fas fa-file-pdf" aria-hidden="true"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if ($rows): ?>
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Pratinjau (maksimal 20 baris)</div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead><tr>
                            <?php foreach ($options['columns'] as $key): ?>
                                <th><?= esc($columns[$key]) ?></th>
                            <?php endforeach; ?>
                        </tr></thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr><?php foreach ($options['columns'] as $key): ?><td><?= esc((string) ($row[$key] ?? '')) ?></td><?php endforeach; ?></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif (service('request')->getMethod() === 'POST' && !$error): ?>
            <div class="alert alert-info">Tidak ada histori pencarian untuk filter yang dipilih.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    const checkAll = document.getElementById('select_all_columns');
    if (checkAll) {
        const columns = document.querySelectorAll('.column-checkbox');
        const sync = () => {
            checkAll.checked = Array.from(columns).every((column) => column.checked);
        };
        checkAll.addEventListener('change', () => columns.forEach((column) => { column.checked = checkAll.checked; }));
        columns.forEach((column) => column.addEventListener('change', sync));
        sync();
    }
</script>
<?= $this->endSection('script'); ?>
