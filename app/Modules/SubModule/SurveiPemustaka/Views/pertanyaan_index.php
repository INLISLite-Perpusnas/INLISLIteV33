<?php
$yaTidak = static function ($value) use ($flag) {
    return $flag($value) ? '<i class="fa fa-check text-success" title="Ya"></i>' : '<i class="fa fa-times text-danger" title="Tidak"></i>';
};
?>
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => 'Survey Pertanyaan',
        'subtitle' => $survey['NamaSurvey'],
        'crumbs' => ['Survey' => site_url('surveipemustaka'), 'Survey Pertanyaan' => null],
    ]) ?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= site_url('surveipemustaka/pertanyaan/create/' . $survey['ID']) ?>" class="btn btn-catalog-add"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover table-striped table-bordered table-sm survey-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">#</th>
                            <th class="text-center" width="50">No Urut</th>
                            <th>Pertanyaan</th>
                            <th class="text-center" width="90">Jenis Pertanyaan</th>
                            <th class="text-center" width="80">Orientation</th>
                            <th class="text-center" width="60">Wajib Diisi</th>
                            <th class="text-center" width="90">Jawaban bisa lebih dari satu</th>
                            <th class="text-center" width="70">Jumlah Pilihan</th>
                            <th class="text-center" width="80">Jumlah Responden</th>
                            <th class="text-center" width="100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $index => $row): ?>
                            <?php $isPilihan = $row['JenisPertanyaan'] === 'Pilihan'; ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="text-center"><?= esc($row['NoUrut']) ?></td>
                                <td><?= nl2br(esc($row['Pertanyaan'])) ?></td>
                                <td class="text-center"><?= esc($row['JenisPertanyaan']) ?></td>
                                <td class="text-center"><?= esc($row['Orientation']) ?></td>
                                <td class="text-center"><?= $yaTidak($row['IsMandatory']) ?></td>
                                <td class="text-center"><?= $yaTidak($row['IsCanMultipleAnswer']) ?></td>
                                <td class="text-center"><?= (int) ($jumlahPilihan[$row['ID']] ?? 0) ?></td>
                                <td class="text-center"><?= (int) ($jumlahResponden[$isPilihan ? 'Pilihan' : 'Isian'][$row['ID']] ?? 0) ?></td>
                                <td>
                                    <div class="btn-group-vertical w-100">
                                        <a href="<?= site_url('surveipemustaka/pertanyaan/edit/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Edit">
                                            <i class="fa fa-pencil-alt"></i> Update
                                        </a>
                                        <form method="post" action="<?= site_url('surveipemustaka/pertanyaan/delete/' . $row['ID']) ?>" class="m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-ina-outline-danger btn-sm w-100 btn-delete" title="Delete"
                                                data-warning="Pilihan dan jawaban responden pertanyaan ini ikut terhapus.">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        <?php if ($isPilihan): ?>
                                            <a href="<?= site_url('surveipemustaka/pilihan/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Daftar Pilihan">
                                                <i class="fa fa-th-list"></i> Pilihan
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('surveipemustaka/isian/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Daftar Isian">
                                                <i class="fa fa-list-alt"></i> Isian
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="<?= site_url('surveipemustaka/pertanyaan/' . $survey['ID']) ?>" class="btn btn-secondary"><i class="fa fa-redo"></i> Reset List</a>
            <a href="<?= site_url('surveipemustaka') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= view('SurveiPemustaka\Views\_datatable_script') ?>
<?= $this->endSection('script'); ?>
