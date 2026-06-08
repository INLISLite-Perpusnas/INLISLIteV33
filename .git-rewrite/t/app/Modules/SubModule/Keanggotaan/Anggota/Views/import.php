<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'keanggotaan';
$member_id = $request->getGet('member_id') ?? 0;
?>



<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Import <?= lang('Anggota.module') ?>
                    <div class="page-title-subheading"><?= lang('Anggota.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                <?= lang('Anggota.label.home') ?></a></li>
                        <li class="breadcrumb-item"><a
                                href="<?= base_url('anggota') ?>"><?= lang('Anggota.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Import <?= lang('Anggota.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-layers icon-gradient bg-plum-plate"> </i>
            Form Upload <?= lang('Anggota.module') ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('uploads/master-template/template_anggota.xlsx') ?>" data-toggle="tooltip"
                    data-placement="top" title="Lihat Template" target="_blank" class="btn btn-secondary"
                    style="min-width:35px"><i class="fa fa-file-excel"> </i> Download Template</a>
            </div>

        </div>
        <div class="card-body">
		<?php if (session()->getFlashdata('message')) : ?>
        <p><?= session()->getFlashdata('message') ?></p>
        <?php endif; ?>

            <form id="frm_create" enctype="multipart/form-data" method="post" action="<?= base_url('anggota/import'); ?>">

                <div class="form-row">
                    <div class="col-md-12">
                        <?= csrf_field() ?>
                        <label for="excel_file">Pilih file Excel:</label>
                        <input type="file" name="excel_file" id="excel_file" onchange="previewExcel(event)" required>
                        <div id="preview"></div>

                    </div>
                </div>



                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg mt-3"
                        name="submit"><?= lang('Anggota.action.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        function previewExcel(event) {
            const input = event.target;
            const reader = new FileReader();
            reader.onload = function() {
                const data = new Uint8Array(reader.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const json = XLSX.utils.sheet_to_json(worksheet, {header: 1});

                let table = '<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered" border="1"><thead><tr>';
                json[0].forEach(function(header) {
                    table += `<th>${header}</th>`;
                });
                table += '</tr></thead><tbody>';
                for (let i = 1; i < json.length; i++) {
                    table += '<tr>';
                    json[i].forEach(function(cell) {
                        table += `<td>${cell}</td>`;
                    });
                    table += '</tr>';
                }
                table += '</tbody></table>';
                document.getElementById('preview').innerHTML = table;
            };
            reader.readAsArrayBuffer(input.files[0]);
        }
    </script>
<?= $this->endSection('script'); ?>