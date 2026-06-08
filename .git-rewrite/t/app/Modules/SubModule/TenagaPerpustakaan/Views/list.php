<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Tenaga Perpustakaan
                    <div class="page-title-subheading">Manajemen Data Tenaga Perpustakaan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tenaga Perpustakaan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-users icon-gradient bg-plum-plate"></i>Data Tenaga Perpustakaan
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('tenaga-perpustakaan/create') ?>" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Tambah Data
                </a>
            </div>
        </div>
        <div class="card-body">
        <div class="card-body">
            <?php if (session()->getFlashdata('message')): ?>
                <?php $message = session()->getFlashdata('message'); ?>
                <?php if (is_array($message)): ?>
                    <div class="alert alert-<?= $message['type'] == 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show" role="alert">
                        <i class="fa <?= $message['type'] == 'success' ? 'fa-check-circle' : ($message['type'] == 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle') ?> mr-2"></i>
                        <strong><?= ucfirst($message['type'] == 'error' ? 'Error' : $message['type']) ?>!</strong> <?= esc($message['text']) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fa fa-info-circle mr-2"></i>
                        <?= esc($message) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> <?= $error_message ?>
                </div>
            <?php endif; ?>

            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Jenis Kelamin</th>
                        <th>Pendidikan</th>
                        <th>Jabatan</th>
                        <th class="text-center" width="120">Aksi</th>
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#tbl_data').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('tenaga-perpustakaan/datatable') ?>',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'nama' },
            { data: 'nip' },
            { data: 'no_hp' },
            { data: 'email' },
            { 
                data: 'jenis_kelamin',
                render: function(data, type, row) {
                    if (data === 'Laki-laki') return 'Laki-laki';
                    if (data === 'Perempuan') return 'Perempuan';
                    return '-';
                }
            },
            { data: 'pendidikan' },
            { data: 'jabatan' },
            { 
                data: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        language: {
            processing: "Sedang memproses...",
            lengthMenu: "Tampilkan _MENU_ entri",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(disaring dari _MAX_ total entri)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, 'asc']]
    });
});
</script>
<?= $this->endSection('script'); ?>