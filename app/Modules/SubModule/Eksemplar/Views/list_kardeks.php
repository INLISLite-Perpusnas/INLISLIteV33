<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-news-paper icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= esc($title) ?>
                    <div class="page-title-subheading">Daftar terbitan berkala per edisi serial dan eksemplar</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Koleksi</li>
                        <li class="breadcrumb-item" aria-current="page"><?= esc($title) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?></div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" id="tbl_kardeks" class="table table-hover table-bordered table-sm">
                    <thead class="bg-night-sky text-light">
                        <tr>
                            <th class="text-center" width="30"></th>
                            <th class="text-center" width="35">No</th>
                            <th class="text-center" width="110">BIBID</th>
                            <th class="text-center">Judul</th>
                            <th class="text-center">Pengarang</th>
                            <th class="text-center">Tempat Terbit</th>
                            <th class="text-center">Penerbit</th>
                            <th class="text-center" width="70">Tahun Terbit</th>
                            <th class="text-center" width="80">Jumlah Edisi Serial</th>
                            <th class="text-center" width="80">Eksemplar</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= site_url('kardeks-terbitan-berkala') ?>" class="btn btn-info"><i class="fa fa-redo"></i> Reset List</a>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_kardeks').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": { "url": '<?= site_url('kardeks-terbitan-berkala/datatable') ?>' },
            "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-2 align-items-center'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sSearchPlaceholder": "Judul, pengarang, penerbit, BIBID, ISSN...",
                "sLengthMenu": "Tampilkan : _MENU_",
                "sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "sInfoEmpty": "Tidak ada data",
                "sInfoFiltered": "(disaring dari _MAX_ data)",
                "sEmptyTable": "Belum ada terbitan berkala",
                "sZeroRecords": "Data tidak ditemukan",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "Akhir",
                    "sFirst": "Awal",
                }
            },
            "order": [],
            "columns": [
                { data: 'expand', className: 'text-center', orderable: false, searchable: false },
                { data: 'no', className: 'text-center', orderable: false, searchable: false },
                { data: 'BIBID', className: 'text-nowrap' },
                { data: 'Title' },
                { data: 'Author' },
                { data: 'PublishLocation' },
                { data: 'Publisher' },
                { data: 'PublishYear', className: 'text-center' },
                { data: 'JumlahEdisiSerial', className: 'text-center', searchable: false },
                { data: 'Eksemplar', className: 'text-center', searchable: false },
                // Kolom tersembunyi agar ikut dicari
                { data: 'ISBN', visible: false },
                { data: 'CallNumber', visible: false },
                { data: 'ControlNumber', visible: false },
            ],
        });

        // Buka/tutup rincian Edisi Serial (setara ExpandRowColumn GridView INLISLite 3)
        $('#tbl_kardeks tbody').on('click', '.btn-kardeks-expand', function() {
            var btn = $(this);
            var row = t.row(btn.closest('tr'));
            var icon = btn.find('i');
            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('fa-minus-square').addClass('fa-plus-square');
                return;
            }
            row.child('<div class="p-2 text-muted">Memuat...</div>').show();
            icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            $.get('<?= site_url('kardeks-terbitan-berkala/edisi') ?>/' + btn.data('id'), function(html) {
                row.child('<div class="p-2 bg-light">' + html + '</div>').show();
            }).fail(function() {
                row.child('<div class="p-2 text-danger">Gagal memuat edisi serial.</div>').show();
            });
        });
    });
</script>
<?= $this->endSection('script'); ?>
