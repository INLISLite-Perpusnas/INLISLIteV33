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
                    <?php foreach ($jenisbahans as $jenisbahan) : ?>
                        <a class="dropdown-item" href="#" data-jenisbahan-id="<?= $jenisbahan->ID ?>"><?= $jenisbahan->Name ?></a>
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
<!-- Include SweetAlert2 CSS -->


<!-- Include SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.1/dist/sweetalert2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.dropdown-item').click(function() {
            var jenisbahanID = $(this).data('jenisbahan-id');
            var isChecked = $(this).prop('checked');
            var action = 'save'
            var jenisAnggota = $('#jenis-anggota').val();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('master-jenis-anggota/savejenisbahan') ?>',
                data: {
                    CollectionCategory_id: jenisbahanID,
                    JenisAnggota_id: jenisAnggota
                },
                success: function(response) {
                    // Handle the response with SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: "berhasil disimpan",
                        onClose: function() {
                            // Redirect to the desired URL after the SweetAlert is closed
                            window.location.href = 'http://localhost:8080/index.php/master-jenis-anggota';
                        }

                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle errors with SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
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
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
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
                            // Show a success SweetAlert
                            Swal.fire(
                                'Deleted!',
                                'Your record has been deleted.',
                                'success'
                            ).then(() => {
                                // Reload the page or update the UI as needed
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            // Show an error SweetAlert
                            Swal.fire(
                                'Error!',
                                'Failed to delete the record.',
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            }
        });
    }
</script>




<?= $this->endSection('script'); ?>