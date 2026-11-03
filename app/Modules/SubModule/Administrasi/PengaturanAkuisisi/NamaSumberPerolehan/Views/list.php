<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    #tbl_data thead tr.filter-row th { padding: 6px; }
    #tbl_data thead tr.filter-row input { width: 100%; }
    #tbl_data .ina-actions { display: inline-flex; gap: 6px; }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= esc($title) ?>
                    <div class="page-title-subheading">Daftar rekanan, penerbit, atau donatur asal perolehan koleksi</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Akuisisi</li>
                        <li class="breadcrumb-item"><?= esc($title) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card col-md-12">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel <?= esc($title) ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('master-nama-sumber-perolehan/create', null, false)) : ?>
                    <a href="javascript:void(0);" id="btn_create" class="btn btn-catalog-add" title="Tambah"><i class="fa fa-plus"></i> Nama Sumber Perolehan</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">#</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th class="text-right" width="110">Eksemplar</th>
                        <th class="text-center" width="110">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th><input type="text" class="form-control form-control-sm column-filter" data-column="1" aria-label="Filter nama"></th>
                        <th><input type="text" class="form-control form-control-sm column-filter" data-column="2" aria-label="Filter alamat"></th>
                        <th><input type="text" class="form-control form-control-sm column-filter" data-column="3" aria-label="Filter no. telepon"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!-- Modal di luar .app-main agar tidak tertutup backdrop (sama seperti modul master lain) -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_form_title"></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="frm_partner">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frm_Name">Nama <span class="text-danger">*</span></label>
                        <input required maxlength="255" type="text" class="form-control" id="frm_Name" name="Name">
                    </div>
                    <div class="form-group">
                        <label for="frm_Address">Alamat</label>
                        <textarea maxlength="255" class="form-control" id="frm_Address" name="Address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="frm_Phone">No. Telepon</label>
                        <input maxlength="100" type="text" class="form-control" id="frm_Phone" name="Phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn_save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Token CSRF diperbarui dari tiap respons karena token di-regenerate setelah dipakai
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';
    var saveUrl = '';
    var t;

    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "orderCellsTop": true,
            "ajax": { "url": '<?= site_url('api/master-nama-sumber-perolehan/datatable') ?>' },
            "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-2 align-items-center'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "sInfoEmpty": "Tidak ada data",
                "sInfoFiltered": "(disaring dari _MAX_ data)",
                "sZeroRecords": "Data tidak ditemukan",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "Akhir",
                    "sFirst": "Awal",
                }
            },
            "columns": [
                { data: 'no', className: 'text-center', orderable: false, searchable: false },
                { data: 'Nama' },
                { data: 'Alamat' },
                { data: 'Telepon' },
                { data: 'Eksemplar', className: 'text-right', orderable: false, searchable: false },
                { data: 'action', className: 'text-center', orderable: false, searchable: false },
            ],
            "order": [],
        });

        var filterTimer;
        $('.column-filter').on('keyup change', function() {
            var input = this;
            clearTimeout(filterTimer);
            filterTimer = setTimeout(function() {
                var column = t.column($(input).data('column'));
                if (column.search() !== input.value) column.search(input.value).draw();
            }, 400);
        });
    });

    function openForm(title, url, data) {
        saveUrl = url;
        $('#modal_form_title').text(title);
        $('#frm_Name').val(data.Name || '');
        $('#frm_Address').val(data.Address || '');
        $('#frm_Phone').val(data.Phone || '');
        $('#modal_form').modal('show');
    }

    $('#btn_create').on('click', function() {
        openForm('Tambah Nama Sumber Perolehan', '<?= base_url('api/master-nama-sumber-perolehan/create') ?>', {});
    });

    $('body').on('click', '.show-data', function() {
        $.getJSON($(this).data('href'), function(res) {
            openForm('Koreksi Nama Sumber Perolehan', '<?= base_url('api/master-nama-sumber-perolehan/edit') ?>/' + res.ID, res);
        }).fail(function() {
            Swal.fire({ title: 'Oups', text: 'Data gagal dimuat.', type: 'error' });
        });
    });

    $('#frm_partner').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({ name: csrfName, value: csrfHash });
        $('#btn_save').attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mohon menunggu...');

        $.ajax({ url: saveUrl, type: 'POST', data: data, dataType: 'json' })
            .always(function(res, status, xhr) {
                var body = res && res.responseJSON ? res.responseJSON : res;
                if (body && body.csrf) csrfHash = body.csrf;
                $('#btn_save').attr('disabled', false).html('Simpan');

                if (body && body.error === false) {
                    $('#modal_form').modal('hide');
                    t.ajax.reload(null, false);
                    Swal.fire({ title: 'Berhasil', html: body.message, type: 'success', showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire({ title: 'Oups', html: (body && body.message) || 'Maaf, terjadi kesalahan. Muat ulang halaman lalu coba lagi.', type: 'error' });
                }
            });
    });

    $('body').on('click', '.remove-data', function() {
        var url = $(this).data('href');
        var name = $(this).data('name');
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: 'Hapus "' + name + '"? <?= lang('App.swal.can_not_be_restored') ?>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then(function(result) {
            if (result.value) {
                var form = $('<form method="post"></form>').attr('action', url);
                form.append($('<input type="hidden">').attr('name', csrfName).val(csrfHash));
                $('body').append(form);
                form.submit();
            }
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>
