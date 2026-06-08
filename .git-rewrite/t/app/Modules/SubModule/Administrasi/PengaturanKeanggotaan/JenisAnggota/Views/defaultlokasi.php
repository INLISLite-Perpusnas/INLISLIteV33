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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.1/dist/sweetalert2.min.css">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Default Lokasi
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><?= lang('Page.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Default Lokasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Setting Default Aplikasi
        </div>

        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <!-- your_view.php -->
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="locationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Tambah Lokasi
                </button>
                <div class="dropdown-menu" aria-labelledby="locationDropdown">
                    <?php foreach ($locations as $location) : ?>
                        <a class="dropdown-item" href="#" data-location-id="<?= $location->ID ?>"><?= $location->Name ?></a>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="jenis-anggota" value="<?= $JenisAnggota_id ?>">
            </div><br>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($joinedData as $row) : ?>
                        <tr>

                            <td><?= $no++ ?></td>
                            <td><?= $row->Name ?></td>
                            <td>
                                <button class="btn btn-danger" data-record-id="<?= $row->ID ?>" onclick="deleteRecord(this)">Delete</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.1/dist/sweetalert2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.dropdown-item').click(function (e) {
            e.preventDefault(); // Mencegah reload default <a href="#">

            var locationID = $(this).data('location-id');
            var jenisAnggota = $('#master-jenis-anggota').val();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('master-jenis-anggota/save') ?>',
                data: {
                    Location_Library_id: locationID,
                    JenisAnggota_id: jenisAnggota
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: "Berhasil disimpan"
                    }).then(() => {
                        location.reload();
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
                fetch(`<?= base_url('master-jenis-anggota/deletedefaultlokasi') ?>/${id}`, {
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
                                location.reload();
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
