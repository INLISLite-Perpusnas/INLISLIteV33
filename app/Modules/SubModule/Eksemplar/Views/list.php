<?php
$request = service('request');
$slug = $request->getGet('slug');
$branch_id = $request->getGet('branch_id');

/**
 * Daftar aksi massal.
 * Aksi cetak-label memunculkan panel tambahan (jenis kertas + model).
 */
$actions = [
    'cetak-label'         => 'Cetak Label',
    'karantina-eksemplar' => 'Karantina Eksemplar',
];

/**
 * Konfigurasi lengkap semua jenis kertas beserta model-model label-nya.
 *
 * Format:
 *   'paper_size_key' => [
 *       'label'  => 'Nama tampilan di dropdown',
 *       'group'  => 'Nama group optgroup',
 *       'models' => [
 *           'template_key' => 'Label model',
 *       ]
 *   ]
 *
 * template_key = nama file view tanpa prefix path dan tanpa .php
 * Contoh: 'cetak-label-a4-1' → Views/template/cetak-label-a4-1.php
 */
$paper_size_config = [
    // ── Kertas A4 ──────────────────────────────────────────────────────────
    'a4' => [
        'label'  => 'Kertas A4',
        'group'  => 'Kertas A4',
        'models' => [
            'cetak-label-a4-1'       => 'Model A4-1 (No. Panggil + Barcode)',
            'cetak-label-a4-2'       => 'Model A4-2 (No. Panggil + Barcode)',
            'cetak-label-a4-3'       => 'Model A4-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-4'       => 'Model A4-4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-5'       => 'Model A4-5 (No. Panggil + Barcode)',
            'cetak-label-a4-6'       => 'Model A4-6 (No. Panggil + Barcode)',
            'cetak-label-a4-7'       => 'Model A4-7 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-8'       => 'Model A4-8 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-9'       => 'Model A4-9 (No. Panggil + Barcode + 5 Warna Kelas)',
            'cetak-label-a4-10'      => 'Model A4-10 (No. Panggil + 2x Barcode + Cabang)',
            'cetak-label-a4-11'      => 'Model A4-11 (No. Panggil + 3 Digit Warna Kelas, Tanpa Barcode)',
            'cetak-label-a4-12'      => 'Model A4-12 (Judul + Barcode, 24 Label/Halaman)',
            // QR Code – fitur baru yang dipertahankan
            'cetak-label-a4-4-qrcode' => 'Model A4-QR (QR Code + No. Panggil)',
        ],
    ],
    // ── Kertas Label Roll ──────────────────────────────────────────────────
    'label-roll' => [
        'label'  => 'Kertas Label Roll',
        'group'  => 'Kertas Label Roll',
        'models' => [
            'cetak-label-lr1' => 'Model LR1 (No. Panggil + Barcode)',
            'cetak-label-lr2' => 'Model LR2 (No. Panggil + Barcode)',
            'cetak-label-lr3' => 'Model LR3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr4' => 'Model LR4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr5' => 'Model LR5 (No. Panggil Tanpa Barcode)',
            'cetak-label-lr6' => 'Model LR6 (No. Panggil Tanpa Barcode + 1 Warna)',
        ],
    ],
    // ── Kertas Barcode Roll ────────────────────────────────────────────────
    'barcode-roll' => [
        'label'  => 'Kertas Barcode Roll',
        'group'  => 'Kertas Label Roll',
        'models' => [
            'cetak-label-br1' => 'Model BR1 (Barcode)',
            'cetak-label-br2' => 'Model BR2 (Barcode + Judul)',
        ],
    ],
    // ── Kertas Label Tom & Jerry 107 ───────────────────────────────────────
    'label-tj107' => [
        'label'  => 'Kertas Label Tom & Jerry 107',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-tj107-1' => 'Model TJ107-1 (Hanya Barcode)',
        ],
    ],
    // ── Kertas Label Tom & Jerry 121 ───────────────────────────────────────
    'label-tj121' => [
        'label'  => 'Kertas Label Tom & Jerry 121',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-tj121-1' => 'Model TJ121-1 (No. Panggil + Barcode)',
            'cetak-label-tj121-2' => 'Model TJ121-2 (No. Panggil + Barcode)',
        ],
    ],
    // ── Kertas Label Golden Cock 121 ───────────────────────────────────────
    'label-gc121' => [
        'label'  => 'Kertas Label Golden Cock 121',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-gc121-1' => 'Model GC121-1 (No. Panggil + Barcode)',
            'cetak-label-gc121-2' => 'Model GC121-2 (No. Panggil + Barcode)',
            'cetak-label-gc121-3' => 'Model GC121-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-gc121-4' => 'Model GC121-4 (No. Panggil + Barcode + 1 Warna)',
        ],
    ],
];

// Encode ke JSON agar bisa dikonsumsi JavaScript tanpa request AJAX tambahan
$flashIcon = session()->getFlashdata('swal_icon');
$flashTitle = session()->getFlashdata('swal_title');
$flashMessage = session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text');
?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .exemplar-list-page .page-title-heading h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .exemplar-list-page .page-title-subheading { margin: .25rem 0 0; }
    .exemplar-list-page .app-page-title .page-title-wrapper {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .exemplar-list-page .app-page-title .page-title-heading {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        min-width: 0;
    }
    .exemplar-list-page .app-page-title .page-title-icon {
        display: inline-flex;
        flex: 0 0 1.8rem;
        width: 1.8rem;
        height: 1.8rem;
        margin: 0;
        padding: 0;
        align-items: center;
        justify-content: center;
        color: inherit;
        background: transparent;
        border: 0;
        box-shadow: none;
    }
    .exemplar-list-page .app-page-title .page-title-icon i {
        display: block;
        font-size: 1.5rem;
        line-height: 1.2;
    }
    .exemplar-list-page .app-page-title .page-title-actions {
        margin-left: auto;
        text-align: right;
    }
    .exemplar-list-page .app-page-title .breadcrumb {
        justify-content: flex-end;
        margin: 0;
        padding: 0;
        background: transparent;
    }
    .exemplar-list-page .exemplar-filter-label {
        margin: 0; color: #fff; background: #495057; border: 1px solid #495057;
        padding: .5rem .75rem; border-radius: .25rem 0 0 .25rem; white-space: nowrap;
    }
    .exemplar-list-page .exemplar-switch {
        appearance: none; width: 2.75rem; height: 1.5rem; margin: 0;
        border: 2px solid #6c757d; border-radius: 999px; background: #fff;
        cursor: pointer; vertical-align: middle;
        transition: background-color .15s ease, border-color .15s ease;
    }
    .exemplar-list-page .exemplar-switch::before {
        content: ""; display: block; width: 1rem; height: 1rem; margin: .125rem;
        border-radius: 50%; background: #6c757d; transition: transform .15s ease;
    }
    .exemplar-list-page .exemplar-switch:checked { background: #087f5b; border-color: #087f5b; }
    .exemplar-list-page .exemplar-switch:checked::before { background: #fff; transform: translateX(1.25rem); }
    .exemplar-list-page .exemplar-switch:disabled { cursor: wait; opacity: .65; }
    .exemplar-list-page .exemplar-switch:focus-visible,
    .exemplar-list-page input[type="checkbox"]:focus-visible,
    .exemplar-list-page select:focus-visible,
    .exemplar-list-page button:focus-visible,
    .exemplar-list-page a:focus-visible { outline: 3px solid #f59f00; outline-offset: 2px; }
    .exemplar-list-page #tbl_data th { vertical-align: middle; }
    .exemplar-list-page #tbl_data .badge {
        display: inline-block;
        padding: .35em .65em;
        color: #fff !important;
        font-size: .75em;
        font-weight: 700;
        line-height: 1;
        border-radius: .25rem;
    }
    .exemplar-list-page #tbl_data .badge-secondary {
        color: #fff !important;
        background-color: #495057 !important;
    }
    .exemplar-list-page #tbl_data .badge-success {
        color: #fff !important;
        background-color: #087f5b !important;
    }
    .exemplar-list-page .exemplar-controls { gap: .75rem; }
    .exemplar-list-page .exemplar-create-actions { gap: .6rem; }
    .exemplar-list-page .exemplar-search-wrapper {
        margin-left: auto;
    }
    .exemplar-list-page .exemplar-search { min-width: 14rem; }
    .exemplar-list-page .exemplar-pagination {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem;
        background: #f8fafc;
        border: 1px solid #dbe3ec;
        border-radius: .75rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
    }
    .exemplar-list-page .exemplar-page-numbers {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .exemplar-list-page .exemplar-page-button {
        display: inline-flex;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 .7rem;
        gap: .35rem;
        align-items: center;
        justify-content: center;
        color: #334155;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        font-weight: 600;
        line-height: 1;
        transition: color .15s ease, background-color .15s ease, border-color .15s ease, transform .15s ease;
    }
    .exemplar-list-page .exemplar-page-button:hover:not(:disabled) {
        color: #fff;
        background: #3155a6;
        border-color: #3155a6;
        transform: translateY(-1px);
    }
    .exemplar-list-page .exemplar-page-button.is-active {
        color: #fff;
        background: #243f7e;
        border-color: #243f7e;
        box-shadow: 0 3px 8px rgba(36, 63, 126, .25);
    }
    .exemplar-list-page .exemplar-page-button:disabled {
        color: #94a3b8;
        background: #f1f5f9;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .exemplar-list-page .exemplar-page-ellipsis {
        min-width: 1.5rem;
        color: #64748b;
        text-align: center;
    }
    .exemplar-list-page #exemplar_info { color: #495057; }
    @media (prefers-reduced-motion: reduce) {
        .exemplar-list-page *, .exemplar-list-page *::before { transition: none !important; }
    }
    @media (max-width: 576px) {
        .exemplar-list-page .app-page-title .page-title-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }
        .exemplar-list-page .app-page-title .page-title-actions {
            align-self: flex-end;
        }
        .exemplar-list-page .exemplar-search-wrapper {
            width: 100%;
            margin-left: 0;
        }
        .exemplar-list-page .exemplar-search {
            width: 100%;
        }
        .exemplar-list-page .exemplar-pagination-label {
            display: none;
        }
        .exemplar-list-page .exemplar-page-button {
            min-width: 2rem;
            height: 2rem;
            padding: 0 .5rem;
        }
    }

    .exemplar-list-page .app-page-title { margin-bottom: 1.5rem; }
    .exemplar-list-page .card-header { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
    .exemplar-list-page .btn-actions-pane-right { margin-left: auto; }
    .exemplar-list-page .card-footer { display: block; }
    .exemplar-list-page .input-group { max-width: 100%; }
    .exemplar-list-page .btn-primary { background: #2456a6; border-color: #2456a6; }
    .exemplar-list-page .btn-success { background: #087f5b; border-color: #087f5b; }
    .exemplar-list-page .text-primary { color: #2456a6 !important; }
    .exemplar-list-page .text-muted { color: #545b62 !important; }
    .exemplar-list-page #tbl_data .badge-warning { color: #212529 !important; background: #ffc107; }
    .exemplar-list-page #tbl_data .badge-info { background: #126779; }
    .exemplar-list-page #tbl_data .badge-danger { background: #b42318; }
    .exemplar-list-page .barcode-copy,
    .exemplar-list-page .exemplar-sort { border: 0; background: transparent; color: inherit; font: inherit; cursor: pointer; }
    .exemplar-list-page .barcode-copy { display: inline-flex; gap: .5rem; align-items: center; padding: .4rem; font-weight: 600; }
    .exemplar-list-page .exemplar-sort { font-weight: 700; }
    .exemplar-list-page .exemplar-sort::after { content: ' \2195'; }
    .exemplar-list-page [aria-sort="ascending"] .exemplar-sort::after { content: ' \2191'; }
    .exemplar-list-page [aria-sort="descending"] .exemplar-sort::after { content: ' \2193'; }
    .exemplar-list-page .table-responsive { min-height: 24rem; }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<section class="app-main__inner" aria-labelledby="exemplar-title">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss" aria-hidden="true"></i>
                </div>
                <div><h1 id="exemplar-title">Eksemplar</h1>
                    <p class="page-title-subheading">Daftar semua Eksemplar</p>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('eksemplar') ?>">
                                <i class="fa fa-home" aria-hidden="true"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if ($flashIcon): ?>
        <div class="alert <?= in_array($flashIcon, ['success', 'info'], true) ? 'alert-success' : 'alert-warning' ?>" role="status">
            <strong><?= esc($flashTitle ?? '') ?></strong>
            <?= esc(strip_tags($flashMessage ?? '')) ?>
        </div>
    <?php endif; ?>
    <div class="main-card mb-3 card">

        <!-- ── Card Header ──────────────────────────────────────────────── -->
        <div class="card-header">
            <i class="header-icon fa fa-list icon-gradient bg-plum-plate" aria-hidden="true"></i>
            Daftar Eksemplar
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('eksemplar/create')) : ?>
                    <a href="<?= esc(base_url('eksemplar/create') . '?' . http_build_query(['slug' => $slug])) ?>" class="btn btn-success">
                        <i class="fa fa-plus" aria-hidden="true"></i> Tambah Eksemplar
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Card Footer: Panel Aksi Massal ──────────────────────────── -->
        <div class="card-footer">

            <!-- ── Baris 1: Pilih Aksi + Tombol Proses ── -->
            <div class="d-flex align-items-center flex-wrap" style="gap:6px; padding: 8px 0 4px 0;">
                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <label class="exemplar-filter-label" for="action">Pilih Aksi</label>
                    </div>
                    <select class="form-control" id="action" name="action">
                        <option value="">-- Pilih Aksi --</option>
                        <?php foreach ($actions as $key => $value) : ?>
                            <option value="<?= esc($key) ?>"><?= esc($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn btn-primary" id="btnProcess2" type="button" style="height:38px; padding: 0 16px; flex-shrink:0;">
                    <i class="fa fa-check" aria-hidden="true"></i> Proses
                </button>
            </div>

            <!-- ── Baris 2: Opsi Cetak (muncul jika aksi = Cetak Label) ── -->
            <div id="cetak_panel" style="display:none; padding: 8px 0 4px 0;">
                <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                    <!-- Jenis Kertas -->
                    <div class="input-group" style="width:280px; flex-shrink:0;">
                        <div class="input-group-prepend">
                            <label class="exemplar-filter-label" for="paper_size">Jenis Kertas</label>
                        </div>
                        <select class="form-control" id="paper_size" name="paper_size">
                            <option value="">-- Pilih Jenis Kertas --</option>
                            <optgroup label="Kertas A4">
                                <option value="a4">Kertas A4</option>
                            </optgroup>
                            <optgroup label="Kertas Label Roll">
                                <option value="label-roll">Kertas Label Roll</option>
                                <option value="barcode-roll">Kertas Barcode Roll</option>
                            </optgroup>
                            <optgroup label="Kertas Label Stiker">
                                <option value="label-tj107">Tom &amp; Jerry 107</option>
                                <option value="label-tj121">Tom &amp; Jerry 121</option>
                                <option value="label-gc121">Golden Cock 121</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Model Label (muncul setelah kertas dipilih) -->
                    <div class="input-group" id="label_model_wrapper" style="display:none; width:320px; flex-shrink:0;">
                        <div class="input-group-prepend">
                            <label class="exemplar-filter-label" for="label_model">Model Label</label>
                        </div>
                        <select class="form-control" id="label_model" name="label_model">
                            <option value="">-- Pilih Model --</option>
                        </select>
                    </div>

                    <!-- Format Output -->
                    <div class="input-group" style="width:200px; flex-shrink:0;">
                        <div class="input-group-prepend">
                            <label class="exemplar-filter-label" for="output_format">Format</label>
                        </div>
                        <select class="form-control" id="output_format" name="output_format">
                            <option value="pdf">PDF</option>
                            <option value="word">Word (DOCX)</option>
                        </select>
                    </div>

                </div>

                <!-- Info ringkas pilihan aktif -->
                <div id="model_info_row" style="display:none; margin-top:4px;">
                    <small class="text-muted">
                        <i class="fa fa-info-circle text-primary" aria-hidden="true"></i>
                        <span id="model_info_text"></span>
                    </small>
                </div>
            </div>
            <!-- end #cetak_panel -->

        </div>
        <!-- end card-footer -->

        <!-- ── Card Body: Tabel DataTable ─────────────────────────────── -->
        <div class="card-body">

            <!-- ── Filter: Lokasi Perpustakaan & Lokasi Ruang ── -->
            <div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <label class="exemplar-filter-label" for="filter_location_library"><i class="fa fa-map-marker-alt" aria-hidden="true"></i> Lokasi Perpustakaan</label>
                    </div>
                    <select class="form-control" id="filter_location_library">
                        <option value="">-- Semua Lokasi Perpustakaan --</option>
                    </select>
                </div>

                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <label class="exemplar-filter-label" for="filter_location_id"><i class="fa fa-door-open" aria-hidden="true"></i> Lokasi Ruang</label>
                    </div>
                    <select class="form-control" id="filter_location_id" disabled>
                        <option value="">-- Pilih Lokasi Perpustakaan Dahulu --</option>
                    </select>
                </div>

                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <label class="exemplar-filter-label" for="filter_media_id"><i class="fa fa-cube" aria-hidden="true"></i> Bentuk Fisik</label>
                    </div>
                    <select class="form-control" id="filter_media_id">
                        <option value="">-- Semua Bentuk Fisik --</option>
                    </select>
                </div>

                <button class="btn btn-outline-secondary" id="btnResetFilter" type="button" style="height:38px; flex-shrink:0;">
                    <i class="fa fa-undo" aria-hidden="true"></i> Reset Filter
                </button>
            </div>

            <p id="exemplar_filter_error" class="text-danger" role="status" hidden>Pilihan filter gagal dimuat. Silakan muat ulang halaman.</p>
            <div class="exemplar-controls d-flex align-items-end flex-wrap mb-3">
                <div>
                    <label for="exemplar_page_length" class="d-block mb-1">Data per halaman</label>
                    <select id="exemplar_page_length" class="form-control">
                        <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                    </select>
                </div>
                <div class="exemplar-search-wrapper">
                    <label for="exemplar_search" class="d-block mb-1">Cari eksemplar</label>
                    <input type="search" id="exemplar_search" class="form-control exemplar-search" placeholder="Barcode, judul, no. induk..." autocomplete="off">
                </div>
            </div>
            <div class="table-responsive" role="region" aria-label="Daftar eksemplar" tabindex="0">
            <form name="form_items" id="form_items">
                <table style="width:100%;" id="tbl_data"
                    class="table table-hover table-striped table-bordered">
                    <caption class="sr-only">Daftar eksemplar beserta status dan tindakan pengelolaannya.</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" width="35">No</th>
                            <th scope="col" class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua" aria-label="Pilih semua eksemplar pada halaman ini">
                            </th>
                            <th scope="col" class="text-center" width="100"><button type="button" class="exemplar-sort" data-sort="NomorBarcode">No. Barcode</button></th>
                            <th scope="col" class="text-center" width="100"><button type="button" class="exemplar-sort" data-sort="TanggalPengadaan">Tanggal Pengadaan</button></th>
                            <th scope="col" class="text-center" width="100"><button type="button" class="exemplar-sort" data-sort="NoInduk">No. Induk</button></th>
                            <th scope="col" class="text-center" style="min-width: 300px;"><button type="button" class="exemplar-sort" data-sort="Title">Data Bibliografis</button></th>
                            <th scope="col" class="text-center">DRM</th>
                            <th scope="col" class="text-center">Karantina</th>
                            <th scope="col" class="text-center">OPAC</th>
                            <th scope="col" class="text-center"><button type="button" class="exemplar-sort" data-sort="StatusName">Status</button></th>
                            <th scope="col" class="text-center" width="100"><button type="button" class="exemplar-sort" data-sort="LocationLibraryName">Lokasi</button></th>
                            <th scope="col" class="text-center" width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="exemplar_rows"><tr><td colspan="12" class="text-center">Memuat data eksemplar...</td></tr></tbody>
                </table>
            </form>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
                <p id="exemplar_info" class="mb-0" aria-live="polite">Memuat data eksemplar...</p>
                <button type="button" id="exemplar_retry" class="btn btn-outline-secondary" hidden>Coba lagi</button>
                <nav aria-label="Navigasi halaman eksemplar">
                    <div class="exemplar-pagination">
                        <button type="button" class="exemplar-page-button" id="exemplar_prev" disabled aria-label="Halaman sebelumnya">&#8249;</button>
                        <div class="exemplar-page-numbers" id="exemplar_page_numbers"></div>
                        <button type="button" class="exemplar-page-button" id="exemplar_next" disabled aria-label="Halaman berikutnya">&#8250;</button>
                    </div>
                </nav>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
window.exemplarListConfig = <?= json_encode([
    'apiUrl' => site_url('api/eksemplar/datatable'),
    'librariesUrl' => site_url('api/eksemplar/locationlibrary'),
    'roomsUrl' => site_url('api/eksemplar/locations'),
    'mediaUrl' => site_url('api/eksemplar/collectionmedias'),
    'paperSizes' => $paper_size_config,
    'csrfName' => csrf_token(),
    'csrfHash' => csrf_hash(),
    'actions' => [
        'cetak-label' => base_url('eksemplar/print_label'),
        'karantina-eksemplar' => base_url('eksemplar/proses_karantina'),
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script defer src="<?= base_url('assets/js/exemplar-list.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/exemplar-list.js') ?>"></script>
<?= $this->endSection('script'); ?>
