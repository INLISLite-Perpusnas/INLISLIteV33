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
    }

    /* Increase the size of the checkbox */
    .form-check-input[type="checkbox"] {
        width: 25px;
        height: 25px;
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
        // Event Listener untuk Dropdown
        $('.dropdown-item').click(function (e) {
            e.preventDefault(); 

            // Ambil data ID Lokasi
            var locationID = $(this).data('location-id');
            // FIX: Ambil data ID Anggota (sesuaikan dengan ID di HTML input hidden)
            var jenisAnggota = $('#jenis-anggota').val();

            // Validasi Input Client Side
            if (!locationID || !jenisAnggota) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'ID Lokasi atau ID Jenis Anggota tidak ditemukan.'
                });
                return;
            }

            // Eksekusi AJAX
            $.ajax({
                type: 'POST',
                url: '<?= base_url('master-jenis-anggota/save') ?>',
                dataType: 'json', // Memastikan response dibaca sebagai JSON
                data: {
                    Location_Library_id: locationID,
                    JenisAnggota_id: jenisAnggota
                },
                success: function (response) {
                    // FIX: Cek flag success dari controller
                    if (response.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || "Data berhasil disimpan"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Jika server merespon tapi logic gagal (misal data duplikat)
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal menyimpan data.'
                        });
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Terjadi kesalahan saat menghubungi server.',
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
                            'X-Requested-With': 'XMLHttpRequest' // Header standar AJAX
                        },
                    })
                    .then(response => {
                        // Cek apakah response OK dan valid JSON
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Parsing JSON
                    })
                    .then(data => {
                        if (data.success || data.status === 'success') { // Sesuaikan dengan return controller Anda
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
                                data.message || 'Gagal menghapus data.',
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat memproses permintaan.',
                            'error'
                        );
                    });
            }
        });
    }
</script>
<?= $this->endSection('script'); ?>