<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Peraturan Peminjaman Tanggal
                    <div class="page-title-subheading">Daftar semua Peraturan Peminjaman Tanggal</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('jenisbahan') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Pengaturan Sirkulasi</li>
                        <li class="breadcrumb-item" aria-current="page">Peraturan Peminjaman Tanggal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Peraturan Peminjaman Tanggal
            <div class="btn-actions-pane-right actions-icon-btn">
                   <a data-bs-toggle="modal" data-bs-target="#modal_create" data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Peraturan Tanggal</a>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center">Tanggal Awal</th>
                        <th class="text-center">Tanggal Akhir</th>
                        <th class="text-center">Maximal Pinjam koleksi</th>
                        <th class="text-center">Jumlah Denda</th>
                        <th class="text-center">Lama Skorsing</th>
                        <th class="text-center">Maks.Lama Perpanjangan</th>
                        <th class="text-center">Maks.Lama Perpanjangan</th>
                        <th class="text-center">Maks.Banyaknya Perpanjangan</th>
                        <th class="text-center" width="80">Status</th>
                        <th class="text-center" style="min-width: 100px;">Aksi</th>
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
<?= $this->include('PeraturanPeminjamanTanggal\Views\add_modal'); ?>
<?= $this->include('PeraturanPeminjamanTanggal\Views\update_modal'); ?>
<script>
    var t;

    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "searching": true, // 1. AKTIFKAN SEARCHING
            "processing": true,
            "serverSide": true,
            "scrollX": true,   // 2. AKTIFKAN SCROLLBAR HORIZONTAL
            "scrollCollapse": true,
            "ajax": {
                "url": '<?php echo site_url('api/peraturan-peminjaman-tanggal/datatable/' . $slug) ?>',
            },
            // Update DOM agar search bar muncul (huruf 'f' adalah filter/search)
            "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",

            "pagingType": "full_numbers",
            "oLanguage": {
                // Shortcut ikon search
                "sSearch": "<i class='fa fa-search'></i> ",
                "sSearchPlaceholder": "Cari data...",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
            "columns": [
                { data: 'no', className: 'text-center', orderable: false },
                { data: 'TanggalAwal', className: 'text-center' },
                { data: 'TanggalAkhir', className: 'text-center' },
                { data: 'MaxPinjamKoleksi', className: 'text-center' },
                { data: 'MaxLoanDays', className: 'text-center' },
                { data: 'DendaTenorJumlah', className: 'text-center' },
                { data: 'DaySuspend', className: 'text-center' },
                { data: 'DayPerpanjang', className: 'text-center' },
                { data: 'CountPerpanjang', className: 'text-center' },
                { data: 'active', className: 'text-center' },
                { data: 'action', className: 'text-center', orderable: false },
            ],
            "order": [[1, "desc"]],
            "drawCallback": function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
                // Opsional: Menyesuaikan ukuran header setelah scroll aktif
                $(window).trigger('resize');
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                // Tambahkan class form-control agar tampilan konsisten dengan Bootstrap
                $searchInput.addClass('form-control form-control-sm').attr('placeholder', 'Tekan Enter...');
                
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) { // Hanya mencari saat tekan Enter
                        t.search(this.value).draw();
                    }
                });
            }
        });
    });

    // Script Hapus tetap sama...
</script>
<?= $this->endSection('script'); ?>