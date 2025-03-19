<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div> Kontak
                    <div class="page-title-subheading">Daftar Semua Kontak</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"> Publikasi</li>
                        <li class="active breadcrumb-item" aria-current="page"> Kontak</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Kontak
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('kontak/create')) : ?>
                    <!-- <a href="<?= base_url('cms/kontak/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah Kontak </a> -->
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_kontak" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No.</th>
                        <th class="text-center" width="300">Nama Kontak</th>
                        <th class="text-center" width="">Subjek dan Pesan</th>
                        <th class="text-center" width="">Tanggal</th>
                        <th class="text-center" width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbl_kontak_tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_kontak').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": 'http://localhost:9000/api/contact?apikey=INLISLITE',
            },
            "dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
            "columns": [{
                    data: 'no',
                    className: 'text-right',
                    orderable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'message'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'email',
                    visible: false
                },
                {
                    data: 'phone',
                    visible: false
                },
                {
                    data: 'subject',
                    visible: false
                },
            ],
            "order": [
                [3, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();
                $.each(data, function(i, row) {
                    $("#lazy" + row.id).Lazy();
                });

                $('.image-link').magnificPopup({
                    type: 'image'
                });
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        if (this.value.length == 0) {
                            t.search('').draw();
                        }

                        if (this.value.length > 3) {
                            t.search(this.value).draw();

                        }
                    }
                });
            }
        });
    });

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        console.log(url);
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: "<?= lang('App.swal.can_not_be_restored') ?>",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>