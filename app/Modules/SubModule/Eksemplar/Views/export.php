<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .export-progress {
        display: none;
        margin-top: 20px;
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-cloud-download icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Export Eksemplar Excel
                    <div class="page-title-subheading">Export data eksemplar ke file Excel dengan format template import</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Koleksi</li>
                        <li class="breadcrumb-item" aria-current="page">Export Excel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-calendar-full icon-gradient bg-plum-plate"> </i>Periode Export
        </div>
        <div class="card-body">
            <form id="form_export" autocomplete="off">
                <div class="form-row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="dasar_tanggal">Berdasarkan
                                <span class="has-tip" data-tip="Kolom tanggal eksemplar yang dipakai untuk menyaring periode."><i class="fa fa-info-circle text-muted"></i></span>
                            </label>
                            <select class="form-control" id="dasar_tanggal" name="dasar_tanggal">
                                <?php foreach ($date_fields as $key => [, $label]) : ?>
                                    <option value="<?= esc($key, 'attr') ?>"><?= esc($label) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="mode">Jenis Periode</label>
                            <select class="form-control" id="mode" name="mode">
                                <option value="harian">Harian (per tanggal)</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="periode">Periode custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 periode-input" data-mode="harian">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 periode-input" data-mode="bulanan" style="display:none;">
                        <div class="form-group">
                            <label for="bulan">Bulan</label>
                            <input type="month" class="form-control" id="bulan" name="bulan" value="<?= date('Y-m') ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 periode-input" data-mode="periode" style="display:none;">
                        <div class="form-group">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="<?= date('Y-m-01') ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 periode-input" data-mode="periode" style="display:none;">
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <?php if (is_allowed('export-eksemplar/export')) : ?>
                    <span class="has-tip" data-tip="Export eksemplar pada periode terpilih ke Excel dengan format template import. Data diproses per <?= (int) $batch_size ?> baris agar tidak timeout.">
                        <button type="submit" class="btn btn-success" id="btn_export"><i class="fa fa-file-excel"></i> Export Excel</button>
                    </span>
                <?php endif; ?>
                <span class="has-tip" data-tip="Unduh template kosong berisi contoh data untuk import.">
                    <a href="<?= base_url('eksemplar/download-template') ?>" class="btn btn-info"><i class="fa fa-download"></i> Download Template</a>
                </span>
            </form>

            <div class="export-progress" id="export_progress">
                <h6 id="export_status">Menyiapkan export...</h6>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="export_bar" role="progressbar" style="width: 0%">0%</div>
                </div>
                <small class="text-muted" id="export_note">Jangan tutup halaman ini sampai proses selesai.</small>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-question-circle icon-gradient bg-plum-plate"> </i>Keterangan
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Kolom dan format file sama dengan <strong>Download Template</strong> pada menu Import Excel, sehingga hasil export dapat diimport kembali.</li>
                <li>Eksemplar yang sedang dikarantina tidak ikut diexport.</li>
                <li>Data besar diproses bertahap per <?= (int) $batch_size ?> baris. File hasil export tersimpan sementara selama 1 hari.</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(function() {
        var csrfName = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';
        var baseUrl = '<?= base_url('export-eksemplar') ?>/';

        $('#mode').on('change', function() {
            var mode = $(this).val();
            $('.periode-input').hide().filter('[data-mode="' + mode + '"]').show();
        });

        function post(action, data) {
            data = data || {};
            data[csrfName] = csrfHash;
            return $.ajax({
                url: baseUrl + action,
                type: 'POST',
                data: data,
                dataType: 'json'
            }).always(function(res, status, xhr) {
                var json = res && res.responseJSON ? res.responseJSON : res;
                if (json && json.csrf) {
                    csrfHash = json.csrf;
                }
            });
        }

        function progress(done, total, text) {
            var pct = total > 0 ? Math.floor(done / total * 100) : 0;
            $('#export_bar').css('width', pct + '%').text(pct + '%');
            $('#export_status').text(text);
        }

        function fail(xhr) {
            var json = xhr && xhr.responseJSON ? xhr.responseJSON : {};
            reset();
            Swal.fire('Gagal', json.message || 'Terjadi kesalahan saat export.', 'error');
        }

        function reset() {
            $('#btn_export').prop('disabled', false);
            $('#export_progress').hide();
        }

        function runBatch(job, total) {
            post('process', { job: job }).done(function(res) {
                progress(res.done, res.total, 'Memproses ' + res.done.toLocaleString('id-ID') + ' dari ' + res.total.toLocaleString('id-ID') + ' eksemplar...');
                if (res.finish) {
                    $('#export_status').text('Membuat file Excel...');
                    post('finish', { job: job }).done(function(fin) {
                        progress(1, 1, 'Selesai. ' + res.total.toLocaleString('id-ID') + ' eksemplar diexport.');
                        $('#export_bar').removeClass('progress-bar-striped progress-bar-animated');
                        $('#export_note').html('<i class="fa fa-check-circle text-success"></i> File sedang diunduh. Bila unduhan tidak berjalan, <a href="' + fin.url + '">klik di sini</a>.');
                        $('#btn_export').prop('disabled', false);
                        window.location.href = fin.url;
                    }).fail(fail);
                } else {
                    runBatch(job, total);
                }
            }).fail(fail);
        }

        $('#form_export').on('submit', function(e) {
            e.preventDefault();
            var data = {};
            $.each($(this).serializeArray(), function(_, field) {
                data[field.name] = field.value;
            });

            $('#btn_export').prop('disabled', true);
            $('#export_progress').show();
            $('#export_bar').addClass('progress-bar-striped progress-bar-animated');
            $('#export_note').text('Jangan tutup halaman ini sampai proses selesai.');
            progress(0, 1, 'Menghitung jumlah data...');

            post('start', data).done(function(res) {
                if (res.status === 'empty') {
                    reset();
                    Swal.fire('Tidak Ada Data', res.message, 'info');
                    return;
                }
                progress(0, res.total, 'Memproses 0 dari ' + res.total.toLocaleString('id-ID') + ' eksemplar...');
                runBatch(res.job, res.total);
            }).fail(fail);
        });
    });
</script>
<?= $this->endSection('script'); ?>
