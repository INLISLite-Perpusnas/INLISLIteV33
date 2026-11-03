<?php
$backUrl = site_url('surveipemustaka/pertanyaan/' . $pertanyaan['Survey_id']);
$chartData = [
    'title' => 'Survey Pilihan',
    'labels' => array_column($rows, 'Pilihan'),
    'data' => array_map('intval', array_column($rows, 'ChoosenCount')),
];
?>
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => 'Survey Pilihan',
        'subtitle' => $pertanyaan['Pertanyaan'],
        'crumbs' => [
            'Survey' => site_url('surveipemustaka'),
            'Survey Pertanyaan' => $backUrl,
            'Survey Pilihan' => null,
        ],
    ]) ?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= site_url('surveipemustaka/pilihan/create/' . $pertanyaan['ID']) ?>" class="btn btn-catalog-add"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover table-striped table-bordered table-sm survey-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">#</th>
                            <th>Pilihan</th>
                            <th class="text-center" width="100">Jumlah Responden</th>
                            <th class="text-center" width="90"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= nl2br(esc($row['Pilihan'])) ?></td>
                                <td class="text-center"><?= (int) $row['ChoosenCount'] ?></td>
                                <td>
                                    <div class="btn-group-vertical w-100">
                                        <a href="<?= site_url('surveipemustaka/pilihan/edit/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Edit">
                                            <i class="fa fa-pencil-alt"></i> Update
                                        </a>
                                        <form method="post" action="<?= site_url('surveipemustaka/pilihan/delete/' . $row['ID']) ?>" class="m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-ina-outline-danger btn-sm w-100 btn-delete" title="Delete"
                                                data-warning="Jawaban responden untuk pilihan ini ikut terhapus.">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="<?= site_url('surveipemustaka/pilihan/' . $pertanyaan['ID']) ?>" class="btn btn-secondary"><i class="fa fa-redo"></i> Reset List</a>
            <a href="<?= $backUrl ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <canvas id="chart-pie" height="120" data-chart="<?= esc(json_encode($chartData), 'attr') ?>"></canvas>
            <hr>
            <canvas id="chart-column" height="120" data-chart="<?= esc(json_encode($chartData + ['yLabel' => 'Diagram Batang Survey Pilihan']), 'attr') ?>"></canvas>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('assets/vendors/chartjs/Chart.min.js') ?>"></script>
<?= view('SurveiPemustaka\Views\_chart_script') ?>
<?= view('SurveiPemustaka\Views\_datatable_script') ?>
<script>
    $(function() {
        surveyChart(document.getElementById('chart-pie'), 'pie');
        surveyChart(document.getElementById('chart-column'), 'bar');
    });
</script>
<?= $this->endSection('script'); ?>
