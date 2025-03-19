<?= $this->extend('layout/backend/main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-bell icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Notifikasi
                    <div class="page-title-subheading">Daftar Semua Notifikasi</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Notifikasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Notifikasi
            <!-- <div class="btn-actions-pane-right actions-icon-btn">
                <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah City</a>
            </div> -->
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_notif" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Code</th>
                        <th>URL</th>
                        <th>Updated at</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($notifications as $row) : ?>
                        <tr>
                            <td width="35"><?= $no++ ?></td>
                            <?php $user = get_user($row->from); ?>
                            <td><?= _spec($user->first_name . ' ' . $user->last_name); ?></td>
                            <?php if ($row->status == 0) {
                                $status_strs  = status_waiting_label;
                                $color_status = 'secondary';
                            } else if ($row->status == 1) {
                                $status_strs  = status_approve_label;
                                $color_status = 'success';
                            } else if ($row->status == 2) {
                                $status_strs  = status_reject_label;
                                $color_status = 'danger';
                            } else {
                                $status_strs  = status_revision_label;
                                $color_status = 'warning';
                            } ?>
                            <td width="80"><span class="badge badge-<?= $color_status ?>"><?= $status_strs ?></span></td>
                            <td width="300"><?= _spec($row->message); ?></td>
                            <td><?= _spec($row->code); ?></td>
                            <td><a href="<?= base_url($row->ref_url) ?>"><?= $row->ref_url ?></a></td>
                            <td>
                                <?php if ($row->updated_at) : ?>
                                    <br>
                                    <div class="mb-2 mr-2 badge badge-warning"><?= $row->updated_at ?></div>
                                <?php endif; ?>
                            </td>
                            <!-- <td width="80">
                                <a href="javascript:void(0);" data-href="<?= base_url('city/delete/' . $row->id); ?>" title="Delete Notif" class="btn btn-xs btn-danger remove-data"><i class="fa fa-trash"> </i></a>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script>
    Dropzone.autoDiscover = false;

    setDataTable('#tbl_notif', disableOrderCols = [0, 6], defaultOrderCols = [5, 'desc'], autoNumber = true);

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        Swal.fire({
            title: 'Are you sure?',
            text: "Data to be deleted can not be restored!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Yes!',
            cancelButtonText: 'No!'
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>