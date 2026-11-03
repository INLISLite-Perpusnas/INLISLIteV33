<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Koleksi Sering Baca di Tempat
                    <div class="page-title-subheading">Pengaturan koleksi sering dibaca di tempat pada halaman beranda.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Opac</li>
                        <li class="breadcrumb-item">Koleksi Sering Baca di Tempat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?= get_message('message') ?>
    <div class="row">
        <div class="col-md-4">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('master-opac-baca-ditempat/index') ?>">
                        <?= csrf_field() ?>
                        <div class="position-relative form-group">
                            <label>Tampilkan Koleksi Sering Baca di Tempat *</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="show_sering_dibaca" id="show_true" value="1" <?= $setting['show_sering_dibaca'] === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_true">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="show_sering_dibaca" id="show_false" value="0" <?= $setting['show_sering_dibaca'] !== '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_false">Tidak</label>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label for="sering_dibaca_show">Maksimal Jumlah Koleksi *</label>
                            <input type="number" min="1" class="form-control" name="sering_dibaca_show" id="sering_dibaca_show" value="<?= esc($setting['sering_dibaca_show']) ?>" required>
                        </div>
                        <div class="form-group">
                            <?php if (is_allowed('master-opac-baca-ditempat/edit')) : ?>
                                <span class="has-tip" data-tip="Simpan pengaturan section Koleksi Sering Baca di Tempat di beranda. Cache beranda langsung dibersihkan."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Koleksi Sering Dibaca di Tempat</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" width="35">No</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th class="text-center">Jumlah Dibaca</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $number => $book): ?>
                                    <tr>
                                        <td class="text-center"><?= $number + 1 ?></td>
                                        <td><?= esc($book->Title) ?></td>
                                        <td><?= esc($book->Author) ?></td>
                                        <td class="text-center"><?= number_format((int) $book->ReadCount) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($books)): ?>
                                    <tr><td colspan="4" class="text-center">Belum ada data baca di tempat.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page') ?>
