<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Usulan Koleksi
                    <div class="page-title-subheading">Daftar usulan koleksi yang dikirim melalui halaman publik.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i>Daftar Usulan Koleksi</div>
        <div class="card-body">
            <div id="status-feedback" class="alert" role="status" style="display: none;"></div>
            <div class="table-responsive">
                <table id="tbl_usulankoleksis" class="table table-hover table-striped table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul / Jenis</th>
                            <th>Penulis / Subjek</th>
                            <th>Terbit</th>
                            <th>Keterangan</th>
                            <th>Tanggal Usulan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(function () {
        var csrfName = <?= json_encode(csrf_token()) ?>;
        var csrfHash = <?= json_encode(csrf_hash()) ?>;
        var table = $('#tbl_usulankoleksis').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 350,
            pageLength: 25,
            ajax: '<?= site_url('api/usulankoleksi/datatable') ?>',
            columns: [
                { data: 'no', orderable: false, searchable: false, className: 'text-center' },
                { data: 'Title' },
                { data: 'Author' },
                { data: 'Publisher' },
                { data: 'Comments', orderable: false },
                { data: 'DateRequest', className: 'text-nowrap' },
                { data: 'Status', orderable: false }
            ],
            order: [[5, 'desc']],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ usulan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ usulan',
                infoEmpty: 'Belum ada usulan koleksi.',
                zeroRecords: 'Usulan tidak ditemukan.',
                processing: 'Memuat usulan...'
            }
        });

        $('#tbl_usulankoleksis').on('change', '.status-usulan', function () {
            var select = $(this);
            var previous = select.attr('data-previous');
            var payload = { status: select.val() };
            payload[csrfName] = csrfHash;
            select.prop('disabled', true);
            $('#status-feedback').hide();

            $.ajax({
                url: '<?= site_url('api/usulankoleksi/status') ?>/' + select.attr('data-id'),
                method: 'POST',
                dataType: 'json',
                data: payload
            }).done(function (response) {
                if (response.csrfHash) csrfHash = response.csrfHash;
                $('#status-feedback').removeClass('alert-danger').addClass('alert-success')
                    .text(response.message).show();
                table.ajax.reload(null, false);
            }).fail(function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.csrfHash) csrfHash = xhr.responseJSON.csrfHash;
                select.val(previous);
                $('#status-feedback').removeClass('alert-success').addClass('alert-danger')
                    .text(xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message : 'Status gagal diperbarui. Muat ulang halaman lalu coba lagi.').show();
            }).always(function () {
                select.prop('disabled', false);
            });
        });
    });
</script>
<?= $this->endSection('script'); ?>