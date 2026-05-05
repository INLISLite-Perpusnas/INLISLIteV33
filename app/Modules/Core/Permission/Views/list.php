<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-shield icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Permission
                    <div class="page-title-subheading">Daftar Semua Permission</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Permission</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Permission
            <div class="btn-actions-pane-right actions-icon-btn">
              <a data-bs-toggle="modal" data-bs-target="#modal_create" data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah Permission">
    <i class="fa fa-plus"></i> Tambah Permission
</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_permissions" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Route</th>
                        <th>Permission</th>
                        <th>Kategori Permission</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $row) : ?>
                        <tr>
                            <?php static $no = 1; ?>
                            <td width="35" class="text-center"><?= $no++; ?></td>
                            <td><?= _spec($row->menu); ?></td>
                            <td><?= _spec($row->route); ?></td>
                            <td><?= _spec($row->name); ?></td>
                            <td><?= _spec($row->category); ?></td>
                            <td><?= _spec($row->description); ?></td>
                            <td style="min-width: 150px;" class="text-center">
                                <?php if (is_allowed('permission/edit') ||  is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('api/permission/detail/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Edit Permission" class="btn btn-xs btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif; ?>
                                <?php if (is_allowed('permission/delete') ||  is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('permission/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Permission" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>


<?= $this->section('script'); ?>
<?= $this->include('Permission\Views\add_modal'); ?>
<?= $this->include('Permission\Views\update_modal'); ?>
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
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#tbl_permissions').DataTable({
        "scrollX": true,
        "scrollCollapse": true,
        "dom": "<'row mb-2'<'col-md-6 text-left'l><'col-md-6 text-right'f>>" +
               "<'row'<'col-md-12'tr>>" +
               "<'row mt-2'<'col-md-5 text-left'i><'col-md-7 d-flex justify-content-end'p>>",
        "pagingType": "full_numbers"
    });

    // Handle Hapus Permission
    $('#tbl_permissions').on('click', '.remove-data', function(e) {
        e.preventDefault();
        var url = $(this).attr('data-href');
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
    });

    // Handle Submit Tambah Permission
    $('#frm_create').submit(function(event) {
        event.preventDefault();
        var data_post = $(this).serializeArray();

        $('.loading').show();

        $.ajax({
            url: '<?= base_url('api/permission/create') ?>',
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            console.log(res);
            if (res.status === 200) {
                Swal.fire({
                    title: 'Success',
                    text: 'Permission berhasil disimpan',
                    type: 'success',
                    showConfirmButton: false,
                    timer: 3000
                });

                setTimeout(function() {
                    window.location.href = '<?= base_url('permission') ?>';
                }, 2000);
            } else {
                $('#frm_create_message').html(res.messages.error);
            }
        })
        .fail(function(res) {
            console.log(res);
            if(res.responseJSON && res.responseJSON.messages) {
                $('#frm_create_message').html(res.responseJSON.messages.error);
            } else {
                $('#frm_create_message').html('<div class="alert alert-danger">Terjadi kesalahan pada server.</div>');
            }
        })
        .always(function() {
            $('.loading').hide();
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 2000);
        });
    });

    // Handle Reset Modal saat ditutup
    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_create_message').html('');
    });

    // Handle Input Nama Method (Tanpa Spasi & Huruf Kecil)
    $('#name').on('keyup', function(e) {
        $(this).val($(this).val().toLowerCase().replace(/\s/g, ''));
        if (e.which == 32) {
            return false;
        }
    });
});
</script>
<?= $this->endSection('script'); ?>