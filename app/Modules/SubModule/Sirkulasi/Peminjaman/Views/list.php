<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    tr.group,
    tr.group:hover {
        background-color: #F0F3F5 !important;
    }

    dl {
        display: grid;
        grid-template-columns: max-content auto;
    }

    dt {
        grid-column-start: 1;
        width: 100px;
        font-weight: normal;
    }

    dd {
        grid-column-start: 2;
    }

    /* ===== NOTIFIKASI BUTTON ===== */
    .btn-notify {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        font-size: 11px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
        transition: all .2s;
        font-weight: 600;
        white-space: nowrap;
    }

    .btn-notify:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, .2);
    }

    .btn-notify-single {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
    }

    .btn-notify-single:hover {
        background: linear-gradient(135deg, #ea580c, #c2410c);
        color: #fff;
    }

    .btn-notify-single.sending {
        opacity: .7;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* ===== SEND ALL BUTTON ===== */
    #btn-send-all-notification {
        background: linear-gradient(135deg, #dc2626, #991b1b);
        border: none;
        transition: all .2s;
    }

    #btn-send-all-notification:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, .4);
    }

    /* ===== MODAL OVERDUE SUMMARY ===== */
    #modal-send-all .overdue-member-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 10px;
        overflow: hidden;
    }

    #modal-send-all .overdue-member-card .member-header {
        background: #f8fafc;
        padding: 8px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }

    #modal-send-all .overdue-member-card .member-body {
        padding: 8px 14px;
        font-size: 12px;
    }

    #modal-send-all .overdue-member-card .book-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px dashed #f1f5f9;
    }

    #modal-send-all .overdue-member-card .book-item:last-child {
        border-bottom: none;
    }

    #modal-send-all #overdue-list-container {
        max-height: 380px;
        overflow-y: auto;
    }

    /* Summary stats */
    .send-all-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .send-all-stats .stat-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }

    .send-all-stats .stat-box .stat-number {
        font-size: 26px;
        font-weight: 800;
        line-height: 1;
    }

    .send-all-stats .stat-box .stat-label {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
    }

    .no-email-badge {
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 20px;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .has-email-badge {
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 20px;
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #86efac;
    }

    /* Sending progress */
    #send-all-progress {
        display: none;
    }

    #send-all-progress .progress {
        height: 8px;
        border-radius: 4px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Peminjaman
                    <div class="page-title-subheading">Daftar semua Peminjaman</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('peminjaman') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Peminjaman</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Peminjaman
            <div class="btn-actions-pane-right actions-icon-btn d-flex gap-2 align-items-center">

                <!-- ===== TOMBOL SEND ALL NOTIFICATION (BARU) ===== -->
                <button id="btn-send-all-notification"
                    class="btn btn-danger btn-sm"
                    title="Kirim email notifikasi ke semua anggota yang terlambat"
                    onclick="openSendAllModal()">
                    <i class="fa fa-envelope-open-text"></i>
                    <span class="ml-1 d-none d-md-inline">Notifikasi Semua Terlambat</span>
                </button>

                <?php if (is_allowed('sirkulasi-peminjaman/create')) : ?>
                    <a href="<?= base_url('sirkulasi-peminjaman/create') ?>" class="btn btn-success btn-sm" title="Tambah">
                        <i class="fa fa-plus"></i> Peminjaman
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
                <thead class="bg-night-sky text-light">
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="100">No. Barcode</th>
                        <th class="text-center">Penerbit / Judul</th>
                        <th class="text-center" width="100">Tgl. Pinjam / Jatuh Tempo</th>
                        <th class="text-center">Hari Terlambat</th>
                        <th class="text-center" width="120">Lokasi Perpustakaan</th>
                        <th class="text-center" width="100">Updated Date</th>
                        <!-- Kolom action untuk tombol notifikasi (tersembunyi dari header, muncul lewat grouping) -->
                        <th class="text-center" width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- MODAL: Send All Notification                               -->
<!-- ========================================================= -->
<div class="modal fade" id="modal-send-all" tabindex="-1" role="dialog" aria-labelledby="modalSendAllLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a5c,#2563eb);">
                <h5 class="modal-title text-white" id="modalSendAllLabel">
                    <i class="fa fa-envelope-open-text mr-2"></i>Kirim Notifikasi Keterlambatan Massal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Loading state -->
                <div id="send-all-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Memuat data keterlambatan...</p>
                </div>

                <!-- Content state -->
                <div id="send-all-content" style="display:none;">
                    <!-- Statistik ringkasan -->
                    <div class="send-all-stats">
                        <div class="stat-box">
                            <div class="stat-number text-danger" id="stat-total-items">0</div>
                            <div class="stat-label">Total Item Terlambat</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number text-warning" id="stat-total-members">0</div>
                            <div class="stat-label">Anggota Terlambat</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number text-success" id="stat-total-email">0</div>
                            <div class="stat-label">Anggota Punya Email</div>
                        </div>
                    </div>

                    <p class="text-muted small mb-2">
                        <i class="fa fa-info-circle text-info"></i>
                        Setiap anggota akan menerima <strong>satu email</strong> berisi semua buku yang terlambat.
                        Anggota tanpa email tidak akan mendapat notifikasi.
                    </p>

                    <!-- List anggota terlambat -->
                    <div id="overdue-list-container"></div>

                    <!-- Progress bar (tampil saat mengirim) -->
                    <div id="send-all-progress" class="mt-3">
                        <p class="text-muted small mb-1" id="progress-text">Mengirim email...</p>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                role="progressbar" style="width:100%"></div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div id="send-all-empty" style="display:none;" class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada pinjaman yang melewati jatuh tempo saat ini. 🎉</p>
                </div>

                <!-- Result state -->
                <div id="send-all-result" style="display:none;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Tutup
                </button>
                <button type="button" id="btn-confirm-send-all" class="btn btn-danger btn-sm" onclick="confirmSendAll()" style="display:none;">
                    <i class="fa fa-paper-plane mr-1"></i>Kirim Notifikasi ke Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: Konfirmasi single notifikasi                        -->
<!-- Konfirmasi single notifikasi ditangani oleh SweetAlert2 -->

<?= $this->include('Peminjaman\Views\add_modal'); ?>
<?= $this->include('Peminjaman\Views\update_modal'); ?>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ===========================================================
    // KONFIGURASI
    // ===========================================================
    const API_BASE = '<?= site_url('sirkulasi-peminjaman') ?>';

    // ===========================================================
    // DATATABLE SETUP
    // ===========================================================
    var groupColumn = 7; // CollectionLoan_id ada di index 7
    var t;

    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollCollapse": true,
            "scrollX": true,
            "ajax": {
                "url": '<?= site_url('api/sirkulasi-peminjaman/datatable/' . $slug) ?>',
            },
            "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
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
                { data: 'no',               className: 'text-center', orderable: false }, // 0
                { data: 'NomorBarcode' },                                                  // 1
                { data: 'Title' },                                                         // 2
                { data: 'LoanDate',         className: 'text-center' },                   // 3
                { data: 'LateDays',         className: 'text-center' },                   // 4
                { data: 'LocationLibrary' },                                               // 5
                { data: 'UpdateDate',       className: 'text-center' },                   // 6
                { data: 'CollectionLoan_id', visible: false },                             // 7 (Grouping)
                { data: 'ID',               visible: false },                              // 8
                { data: 'Fullname',         visible: false },                              // 9
                { data: 'DueDate',          visible: false },                              // 10
                { data: 'Publisher',        visible: false },                              // 11
                // ===== KOLOM AKSI NOTIFIKASI (BARU) =====
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var today = new Date().toISOString().slice(0, 10);
                        // Hanya tampilkan tombol jika sudah melewati jatuh tempo
                        if (row.DueDate < today) {
                            // Pakai data-* attribute agar aman dari karakter kutip
                            return '<button'
                                + ' class="btn-notify btn-notify-single btn-send-notif"'
                                + ' data-id="'       + (row.ID || data) + '"'
                                + ' data-fullname="' + String(row.Fullname || '').replace(/"/g, '&quot;') + '"'
                                + ' data-title="'   + stripHtml(row.Title || '').replace(/"/g, '&quot;') + '"'
                                + ' data-publisher="' + stripHtml(row.Publisher || '').replace(/"/g, '&quot;') + '"'
                                + ' title="Kirim notifikasi keterlambatan ke anggota ini">'
                                + '<i class="fa fa-bell"></i> Kirim Notifikasi Keterlambatan'
                                + '</button>';
                        }
                        return '<span class="text-muted" style="font-size:11px;">\u2014</span>';
                    }
                }, // 12
            ],
            "columnDefs": [
                { targets: [0, 7, 8, 9, 10, 11], searchable: false },
                { targets: [0, 2, 3, 4, 7, 12],  orderable: false  }
            ],
            "order": [
                [7, "desc"]
            ],
            "drawCallback": function(settings) {
                var api  = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="group"><td colspan="9">' + group + '</td></tr>'
                            );
                            last = group;
                        }
                    });
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        t.search(this.value).draw();
                    }
                });
            }
        });
    });

    $('#tbl_data tbody').on('click', 'tr.group', function() {
        var currentOrder = t.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            t.order([groupColumn, 'desc']).draw();
        } else {
            t.order([groupColumn, 'asc']).draw();
        }
    });


    // ===========================================================
    // SINGLE NOTIFICATION - event delegation (aman dari re-render DataTable)
    // ===========================================================
    var _singleNotifyId = null;

    // Delegasikan event ke tbody agar tetap bekerja setelah DataTable re-draw
    $('#tbl_data tbody').on('click', '.btn-send-notif', function() {
        var id        = $(this).data('id');
        var fullname  = $(this).data('fullname');
        var title     = $(this).data('title');
        var publisher = $(this).data('publisher');
        openSingleNotifyModal(id, fullname, title, publisher);
    });

    function openSingleNotifyModal(id, fullname, title, publisher) {
        _singleNotifyId = id;

        Swal.fire({
            title: '<span style="font-size:16px;">Kirim Notifikasi Keterlambatan?</span>',
            html: '<div style="text-align:left;font-size:13px;">'
                + '<p style="margin:0 0 6px;color:#6b7280;">Email notifikasi akan dikirim ke anggota:</p>'
                + '<p style="margin:0 0 2px;font-weight:700;font-size:15px;color:#1e293b;">' + escapeHtml(fullname) + '</p>'
                + '<p style="margin:0 0 10px;color:#64748b;font-size:12px;">'
                + '<i class="fa fa-book mr-1 text-info"></i>'
                + '<strong>' + escapeHtml(title) + '</strong>'
                + (publisher ? ' <span style="color:#9ca3af;">— ' + escapeHtml(publisher) + '</span>' : '')
                + '</p>'
                + '<div style="background:#fef2f2;border-radius:6px;padding:8px 12px;font-size:12px;color:#b91c1c;">'
                + '<i class="fa fa-exclamation-triangle mr-1"></i>Buku telah melewati jatuh tempo pengembalian.'
                + '</div></div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fa fa-paper-plane mr-1"></i> Kirim Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '420px'
        }).then(function(result) {
            if (result.isConfirmed) {
                doSendSingle();
            } else {
                _singleNotifyId = null;
            }
        });
    }

    function doSendSingle() {
        if (!_singleNotifyId) return;

        // Tampilkan loading via SweetAlert
        Swal.fire({
            title: 'Mengirim Email...',
            html: '<span style="font-size:13px;color:#6b7280;">Harap tunggu sebentar.</span>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function() { Swal.showLoading(); }
        });

        $.ajax({
            url: API_BASE + '/send-notification/' + _singleNotifyId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Terkirim!',
                        html: '<span style="font-size:14px;">' + res.message + '</span>',
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'OK',
                        timer: 4000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Kirim',
                        html: '<span style="font-size:14px;">' + res.message + '</span>',
                        confirmButtonColor: '#f97316',
                        confirmButtonText: 'Tutup'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Server',
                    text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                    confirmButtonColor: '#dc2626'
                });
            },
            complete: function() {
                _singleNotifyId = null;
            }
        });
    }


    // ===========================================================
    // SEND ALL NOTIFICATION
    // ===========================================================
    function openSendAllModal() {
        // Reset tampilan modal
        $('#send-all-loading').show();
        $('#send-all-content').hide();
        $('#send-all-empty').hide();
        $('#send-all-result').hide().html('');
        $('#send-all-progress').hide();
        $('#btn-confirm-send-all').hide();
        $('#overdue-list-container').html('');

        $('#modal-send-all').modal('show');

        // Fetch data ringkasan
        $.ajax({
            url: API_BASE + '/overdue-summary',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#send-all-loading').hide();

                if (!res.success || res.total_items === 0) {
                    $('#send-all-empty').show();
                    return;
                }

                // Isi statistik
                var withEmail = res.data.filter(m => m.has_email).length;
                $('#stat-total-items').text(res.total_items);
                $('#stat-total-members').text(res.total_members);
                $('#stat-total-email').text(withEmail);

                // Render list anggota
                var html = '';
                res.data.forEach(function(member) {
                    var emailBadge = member.has_email
                        ? `<span class="has-email-badge"><i class="fa fa-check"></i> ${member.email}</span>`
                        : `<span class="no-email-badge"><i class="fa fa-times"></i> Tidak ada email</span>`;

                    var booksHtml = member.books.map(function(book) {
                        return `<div class="book-item">
                            <div>
                                <div style="font-weight:600;color:#374151;">${escapeHtml(book.title)}</div>
                                <div style="color:#9ca3af;font-size:11px;">${escapeHtml(book.barcode)} &bull; Jatuh tempo: ${book.due_date}</div>
                            </div>
                            <span class="badge badge-danger badge-pill ml-2">+${book.late_days} hari</span>
                        </div>`;
                    }).join('');

                    html += `
                        <div class="overdue-member-card">
                            <div class="member-header">
                                <div>
                                    <strong style="font-size:13px;">${escapeHtml(member.fullname)}</strong>
                                    <span class="text-muted ml-1" style="font-size:11px;">(${member.member_no})</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    ${emailBadge}
                                    <span class="badge badge-secondary" style="font-size:10px;">${member.books.length} buku</span>
                                </div>
                            </div>
                            <div class="member-body">${booksHtml}</div>
                        </div>`;
                });

                $('#overdue-list-container').html(html);

                if (withEmail > 0) {
                    $('#btn-confirm-send-all').show();
                }

                $('#send-all-content').show();
            },
            error: function() {
                $('#send-all-loading').hide();
                $('#send-all-result').show().html(
                    '<div class="alert alert-danger"><i class="fa fa-times-circle mr-1"></i>Gagal memuat data. Silakan coba lagi.</div>'
                );
            }
        });
    }

    function confirmSendAll() {
        var $btn = $('#btn-confirm-send-all');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Mengirim...');
        $('#send-all-progress').show();
        $('#progress-text').text('Sedang mengirim email ke semua anggota terlambat...');

        $.ajax({
            url: API_BASE + '/send-all-notification',
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                $('#send-all-progress').hide();
                $('#send-all-content').hide();

                var alertClass = res.success ? 'success' : 'warning';
                var icon       = res.success ? 'check-circle' : 'exclamation-triangle';
                var errHtml    = '';

                if (res.detail && res.detail.errors && res.detail.errors.length > 0) {
                    errHtml = '<ul class="mt-2 mb-0 small">' +
                        res.detail.errors.map(e => `<li>${escapeHtml(e)}</li>`).join('') +
                        '</ul>';
                }

                var detailHtml = '';
                if (res.detail) {
                    detailHtml = `
                        <div class="send-all-stats mt-3">
                            <div class="stat-box">
                                <div class="stat-number text-danger">${res.detail.total_item_terlambat}</div>
                                <div class="stat-label">Item Terlambat</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number text-success">${res.detail.email_terkirim}</div>
                                <div class="stat-label">Email Terkirim</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number text-secondary">${res.detail.email_gagal}</div>
                                <div class="stat-label">Email Gagal</div>
                            </div>
                        </div>`;
                }

                // Tutup modal lalu tampilkan SweetAlert hasil
                $('#modal-send-all').modal('hide');

                var swalIcon = res.success ? 'success' : 'warning';
                var swalHtml = '<p style="font-size:14px;margin-bottom:12px;">' + escapeHtml(res.message) + '</p>';
                if (res.detail) {
                    swalHtml += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;">'
                        + '<div style="background:#fef2f2;border-radius:8px;padding:10px;text-align:center;">'
                        + '<div style="font-size:24px;font-weight:800;color:#dc2626;">' + res.detail.total_item_terlambat + '</div>'
                        + '<div style="font-size:11px;color:#6b7280;">Item Terlambat</div></div>'
                        + '<div style="background:#f0fdf4;border-radius:8px;padding:10px;text-align:center;">'
                        + '<div style="font-size:24px;font-weight:800;color:#16a34a;">' + res.detail.email_terkirim + '</div>'
                        + '<div style="font-size:11px;color:#6b7280;">Email Terkirim</div></div>'
                        + '<div style="background:#f8fafc;border-radius:8px;padding:10px;text-align:center;">'
                        + '<div style="font-size:24px;font-weight:800;color:#64748b;">' + res.detail.email_gagal + '</div>'
                        + '<div style="font-size:11px;color:#6b7280;">Gagal</div></div>'
                        + '</div>';
                    if (res.detail.errors && res.detail.errors.length > 0) {
                        swalHtml += '<div style="margin-top:10px;text-align:left;background:#fef2f2;border-radius:6px;padding:8px 12px;">'
                            + '<p style="font-size:12px;font-weight:600;color:#dc2626;margin:0 0 4px;">Error Detail:</p>'
                            + '<ul style="font-size:12px;color:#7f1d1d;margin:0;padding-left:16px;">'
                            + res.detail.errors.map(function(e){ return '<li>' + escapeHtml(e) + '</li>'; }).join('')
                            + '</ul></div>';
                    }
                }

                Swal.fire({
                    icon: swalIcon,
                    title: res.success ? 'Notifikasi Terkirim!' : 'Proses Selesai',
                    html: swalHtml,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'OK',
                    width: '480px'
                });
            },
            error: function() {
                $('#send-all-progress').hide();
                $('#modal-send-all').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error Server',
                    text: 'Gagal mengirim notifikasi. Terjadi kesalahan pada server.',
                    confirmButtonColor: '#dc2626'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i>Kirim Notifikasi ke Semua');
                $btn.hide();
            }
        });
    }


    // ===========================================================
    // HELPER
    // ===========================================================
    // Strip tag HTML dari string (untuk data dari DataTable yang sudah di-render sebagai HTML)
    function stripHtml(html) {
        if (!html) return '';
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
<?= $this->endSection('script'); ?>