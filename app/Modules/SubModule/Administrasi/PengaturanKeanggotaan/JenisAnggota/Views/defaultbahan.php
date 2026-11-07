<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .tox.tox-tinymce.tox-fullscreen {
        z-index: 1050;
        top: 60px !important;
        left: 85px !important;
        width: calc(100% - 85px) !important;
    }

    /* Increase the font size of the label */
    .form-check-label {
        font-size: 18px;
        /* Adjust the desired font size */
    }

    /* Increase the size of the checkbox */
    .form-check-input[type="checkbox"] {
        width: 25px;
        /* Adjust the desired width */
        height: 25px;
        /* Adjust the desired height */
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.1/dist/sweetalert2.min.css" integrity="sha384-csqwGqQMzMfYK0Eo+IqUNGrlZujHH42RqILi8CiEaDRZHOW1upfFarO+ffrQhnFd" crossorigin="anonymous">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Default Jenis Bahan
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><?= lang('Page.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Default Jenis Bahan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Setting Default Jenis Bahan
            <div class="btn-actions-pane-right actions-icon-btn">
                <a data-bs-toggle="modal" data-bs-target="#modal_create" href="javascript:void(0);" class="btn btn-catalog-add" title="Tambah"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </div>

        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center" style="min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($joinedData as $row) : ?>
                        <tr>

                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row->Name ?></td>
                            <td class="text-center">
                                <a href="javascript:void(0);" data-record-id="<?= $row->ID ?>" onclick="deleteRecord(this)" title="Hapus" aria-label="Hapus" class="ina-action ina-action-danger"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Default Jenis Bahan
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frm_add_CollectionCategory_id">Jenis Bahan</label>
                        <select required class="form-control" id="frm_add_CollectionCategory_id" name="CollectionCategory_id">
                            <?php foreach ($CollectionCategorys as $jenisbahan) : ?>
                                <option value="<?= $jenisbahan->ID ?>"><?= $jenisbahan->Name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="JenisAnggota_id" value="<?= $JenisAnggota_id ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btnAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function () {
        $('#modal_create').appendTo('body');

        $('#frm_add').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('master-jenis-anggota/savejenisbahan') ?>',
                data: $(this).serialize(),
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Berhasil disimpan'
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah sukses
                    });
                },
                error: function (xhr, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data.',
                    });
                }
            });
        });
    });
</script>

<script>
    function deleteRecord(button) {
        const id = button.getAttribute('data-record-id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('master-jenis-anggota/deletedefaultbahan') ?>/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then((response) => {
                        if (response.ok) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                location.reload(); // Refresh setelah delete
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Gagal menghapus data.',
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan jaringan.',
                            'error'
                        );
                    });
            }
        });
    }
</script>
<?= $this->endSection('script'); ?>
