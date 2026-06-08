<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
.color-preview {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    border: 1px solid #ddd;
    vertical-align: middle;
    margin-right: 5px;
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-folder icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Master Kelas Besar
                    <div class="page-title-subheading">Daftar semua Master Kelas Besar</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('master-kelas-besar') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Master Kelas Besar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Master Kelas Besar
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('master-kelas-besar/create')) : ?>
                    <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Kelas Besar</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="100">Kode Kelas</th>
                        <th class="text-center">Nama Kelas</th>
                        <th class="text-center" width="100">Warna</th>
                        <th class="text-center" width="80">Status</th>
                        <th class="text-center" width="180">Aksi</th>
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
<?= $this->include('MasterKelasBesar\Views\add_modal'); ?>
<?= $this->include('MasterKelasBesar\Views\update_modal'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('master-kelas-besar/datatable/' . $slug) ?>',
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
            "columns": [
                {
                    data: 'no',
                    className: 'text-center'
                },
                {
                    data: 'kdKelas',
                    className: 'text-left'
                },
                {
                    data: 'namakelas'
                },
                {
                    data: 'warna',
                    className: 'text-center'
                },
                {
                    data: 'active',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center'
                }
            ],
            "columnDefs": [
                {
                    targets: [0, 3, 4, 5],
                    searchable: false
                },
                {
                    targets: [0, 5],
                    orderable: false
                }
            ],
            "order": [
                [1, "asc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();
                $('[data-toggle="tooltip"]').tooltip();
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        if (this.value.length == 0) {
                            t.search('').draw();
                        }

                        if (this.value.length >= 3) {
                            t.search(this.value).draw();
                        }
                    }
                });
            }
        });
    });

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        });
        return false;
    });
</script>

<script>
    // Tambahkan script ini di bagian script view Anda
$("body").on("click", ".toggle-status", function(e) {
    e.preventDefault();
    
    var button = $(this);
    var url = button.attr('data-href');
    var currentStatus = button.attr('data-status');
    var newStatus = currentStatus == '1' ? '0' : '1';
    var statusText = newStatus == '1' ? 'mengaktifkan' : 'menonaktifkan';
    var statusAction = newStatus == '1' ? 'diaktifkan' : 'dinonaktifkan';
    
    Swal.fire({
        title: 'Konfirmasi Perubahan Status',
        text: `Apakah Anda yakin ingin ${statusText} data ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // AJAX request
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload DataTable
                            t.ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memproses data.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem: ' + error
                    });
                }
            });
        }
    });
});

// Untuk tombol hapus yang sudah ada, update juga
$("body").on("click", ".remove-data", function(e) {
    e.preventDefault();
    var url = $(this).attr('data-href');
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus data...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirect ke URL hapus
            window.location.href = url;
        }
    });
});

// Flash message handling (jika menggunakan session flashdata)
<?php if (session()->getFlashdata('success')) : ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= session()->getFlashdata('success') ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '<?= session()->getFlashdata('error') ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php endif; ?>
</script>

<?= $this->endSection('script'); ?>