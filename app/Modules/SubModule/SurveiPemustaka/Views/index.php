<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => 'Survey',
        'subtitle' => 'Daftar semua survey',
        'crumbs' => ['Survey' => null],
    ]) ?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= site_url('surveipemustaka/create') ?>" class="btn btn-catalog-add"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover table-striped table-bordered table-sm survey-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">#</th>
                            <th>Nama Survey</th>
                            <th class="text-center" width="90">Tgl.Mulai</th>
                            <th class="text-center" width="90">Tanggal Selesai</th>
                            <th class="text-center" width="60">Status</th>
                            <th class="text-center" width="60">Nomor Urut</th>
                            <th class="text-center" width="80">Target Survey</th>
                            <th class="text-center" width="80">Jumlah Responden</th>
                            <th class="text-center" width="150"></th>
                            <th class="text-center" width="90"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surveys as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= esc($row['NamaSurvey']) ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['TanggalMulai'])) ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['TanggalSelesai'])) ?></td>
                                <td class="text-center">
                                    <?= $flag($row['IsActive'])
                                        ? '<i class="fa fa-check text-success" title="Aktif"></i>'
                                        : '<i class="fa fa-times text-danger" title="Tidak Aktif"></i>' ?>
                                </td>
                                <td class="text-center"><?= esc($row['NomorUrut']) ?></td>
                                <td class="text-center"><?= esc($targetSurvey[(int) $row['TargetSurvey']] ?? 'Rejected') ?></td>
                                <td class="text-center"><?= (int) ($responden[$row['ID']] ?? 0) ?></td>
                                <td>
                                    <div class="btn-group-vertical w-100">
                                        <a href="<?= site_url('surveipemustaka/pertanyaan/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Daftar Pertanyaan">
                                            <i class="fa fa-th-list"></i> Daftar Pertanyaan
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-ina-outline btn-sm btn-hasil" data-id="<?= (int) $row['ID'] ?>" title="Hasil">
                                            <i class="fa fa-chart-bar"></i> Hasil
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-vertical w-100">
                                        <a href="<?= site_url('surveipemustaka/edit/' . $row['ID']) ?>" class="btn btn-ina-outline btn-sm" title="Edit">
                                            <i class="fa fa-pencil-alt"></i> Edit
                                        </a>
                                        <form method="post" action="<?= site_url('surveipemustaka/delete/' . $row['ID']) ?>" class="m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-ina-outline-danger btn-sm w-100 btn-delete" title="Delete"
                                                data-warning="Seluruh pertanyaan, pilihan, dan jawaban responden survey ini ikut terhapus.">
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
        <div class="card-footer">
            <a href="<?= site_url('surveipemustaka') ?>" class="btn btn-secondary"><i class="fa fa-redo"></i> Reset List</a>
        </div>
    </div>
</div>

<div class="modal fade" id="result-modal" tabindex="-1" role="dialog" aria-labelledby="result-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="result-modal-title">Hasil Survey</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="modalResults" style="width:100%"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('assets/vendors/chartjs/Chart.min.js') ?>"></script>
<?= view('SurveiPemustaka\Views\_chart_script') ?>
<?= view('SurveiPemustaka\Views\_datatable_script') ?>
<script>
    $('body').on('click', '.btn-hasil', function() {
        var id = $(this).data('id');
        $('#modalResults').html('<center>Loading...</center>');
        $('#result-modal').appendTo('body').modal('show');
        $.ajax({
            type: 'GET',
            cache: false,
            url: '<?= site_url('surveipemustaka/hasil') ?>/' + id,
            success: function(response) {
                $('#modalResults').html(response);
                $('#modalResults canvas[data-chart]').each(function() {
                    surveyChart(this, 'pie');
                });
            },
            error: function(xhr) {
                $('#modalResults').html(xhr.responseText || '<center><h3>Gagal memuat hasil survey</h3></center>');
            }
        });
    });
</script>
<?= $this->endSection('script'); ?>
