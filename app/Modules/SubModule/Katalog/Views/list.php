<?php
$request = service('request');

?>

<?= $this->extend('App\Views\layout\main'); ?>


<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Katalog
                    <div class="page-title-subheading">Daftar semua Katalog</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Katalog</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    <div class="main-card mb-3 card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Tabel Daftar Katalog</div>

            <div class="d-flex align-items-center flex-wrap">
                <?php if (is_allowed('katalog/create')) : ?>
                    <?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
                        <a href="<?= base_url('katalog/create?rda=1') ?>" class="btn btn-primary btn-sm mr-2">
                            <i class="fa fa-plus"></i> Tambah Katalog RDA
                        </a>
                        <a href="<?= base_url('katalog/create?rda=0') ?>" class="btn btn-success btn-sm mr-2">
                            <i class="fa fa-plus"></i> Tambah Katalog AACR
                        </a>
                    <?php else : ?>
                        <a href="<?= base_url('katalog/create_marc') ?>" class="btn btn-success btn-sm mr-2">
                            <i class="fa fa-plus"></i> Tambah Katalog
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

               
            </div>
        </div>
        
        <div class="card-body">
            <div class="d-block mb-3 pb-3 border-bottom">
                <button type="button" id="proses_karantina" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-shopping-cart"></i> Pindahkan ke Karantina</button>
                <button type="button" id="proses_opac" class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-check-square"></i> Tampilkan di OPAC</button>
            </div>

            <div class="table-responsive">
                <form name="form_items" id="form_items">
                    <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
								<th class="text-center" width="35">No</th>
                                <th class="text-center" width="35">
                                    <input type="checkbox" class="check_data" title="Pilih Semua">
                                </th>

                                <th class="text-center">BIBID</th>
                                <th class="text-center" style="min-width: 300px;">Judul</th>
                                <th class="text-center">Edisi</th>
                                <th class="text-center">Publisher</th>
                                <th class="text-center">Deskripsi Fisik</th>
                                <th class="text-center">No. Panggil</th>
                                <th class="text-center">Eksemplar</th>
                                <th class="text-center">OPAC</th>
                                <th class="text-center">Pedoman Katalog</th>
                                <th class="text-center" >Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000,
                icon:'success'
            });
        <?php endif; ?>
    });
</script>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('api/katalog/datatable/0') ?>",
                "type": "POST",

            },

            "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                   "<'row'<'col-md-12'tr>>" +
                   "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
                   
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
           "columns": [
                {
                    data: 'no',
                    className: 'text-center',
                    searchable: false, // Wajib false
                    orderable: false
                }, 
                { 
                    data: 'ID', // Untuk checkbox
                    className: 'text-center',
                    searchable: false, // Wajib false agar tidak dicari
                    orderable: false
                },
                {
                    data: 'BIBID',
                    className: 'text-left'
                },
                {
                    data: 'Title'
                },
                {
                    data: 'Edition',
                    className: 'text-center'
                },
                {
                    data: 'Publisher',
                    className: 'text-center'
                },
                {
                    data: 'PhysicalDescription',
                    className: 'text-center'
                },
                {
                    data: 'CallNumber',
                    className: 'text-center'
                },
                {
                    data: 'Eksemplar',
                    className: 'text-center',
                    searchable: false, // INI KUNCI FIX ERROR 500-NYA
                    orderable: false
                },
                {
                    data: 'IsOPAC',
                    className: 'text-center',
                    searchable: false, // Wajib false
                    orderable: false
                },
                {
                    data: 'IsRDA',
                    className: 'text-center',
                    searchable: false // Wajib false
                },
                {
                    data: 'action',
                    className: 'text-center',
                    searchable: false, // Wajib false
                    orderable: false,
                    
                }
            ],
            "order": [
                [1, "asc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();
                $('[data-toggle="tooltip"]').tooltip();
                $('.apply-status').bootstrapToggle();
                $(".apply-status").on('change', function() {
                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).is(':checked');
                    var data_post = 'field=' + field + '&value=' + value;

                    $.ajax({
                            url: url,
                            type: 'POST',
                            data: data_post,
                        })
                        .done(function(res) {
                            console.log(res)

                            if (res.error == false) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    html: res.message,
                                    type: 'success',
                                    showConfirmButton: false,
                                    timer: 5000,
                                }).then(() => {});
                            } else {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: res.message,
                                    type: 'error',
                                    showConfirmButton: false,
                                    timer: 5000
                                }).then(() => {});
                            }
                        })
                        .fail(function(res) {
                            console.log(res);

                            Swal.fire({
                                title: 'Oups',
                                text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                                type: 'error',
                                showConfirmButton: false,
                                timer: 5000
                            }).then(() => {});
                        });
                });
            },
        });
    });

    $(".check_data").click(function() {
        $('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
    });

    $('#proses_karantina').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('katalog/proses_karantina') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua katalog yang terpilih akan ditambah <br>ke Karantina Katalog",
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

    $('#proses_opac').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('katalog/proses_opac') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua katalog yang terpilih akan ditampilkan di OPAC",
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