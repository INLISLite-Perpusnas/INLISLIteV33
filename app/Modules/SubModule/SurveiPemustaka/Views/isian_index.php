<?php $backUrl = site_url('surveipemustaka/pertanyaan/' . $pertanyaan['Survey_id']); ?>
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => 'Survey Isian',
        'subtitle' => $pertanyaan['Pertanyaan'],
        'crumbs' => [
            'Survey' => site_url('surveipemustaka'),
            'Survey Pertanyaan' => $backUrl,
            'Survey Isian' => null,
        ],
    ]) ?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?></div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover table-striped table-bordered table-sm survey-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">#</th>
                            <th>Isian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= nl2br(esc($row['Isian'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="<?= site_url('surveipemustaka/isian/' . $pertanyaan['ID']) ?>" class="btn btn-secondary"><i class="fa fa-redo"></i> Reset List</a>
            <a href="<?= $backUrl ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= view('SurveiPemustaka\Views\_datatable_script') ?>
<?= $this->endSection('script'); ?>
