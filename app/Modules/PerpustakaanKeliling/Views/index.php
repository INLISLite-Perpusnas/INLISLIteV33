<?php
/**
 * View: Jadwal Pusling
 * Variabel: $tab, $q, $rentang, $dari, $sampai, $jadwals, $paginationLinks
 * Juga: $totalRows, $page, $perPage (dari controller)
 */
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglIndo = function ($tgl) use ($bulan) {
    $t = strtotime($tgl);
    return (int) date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
};
$filterAktif = !empty($dari) || !empty($sampai);
$jumlahItem = !empty($jadwals) ? count($jadwals) : 0;
$page = $page ?? 1;
$perPage = $perPage ?? 12;
$total = $totalRows ?? null;
$awal = $jumlahItem ? (($page - 1) * $perPage) + 1 : 0;
$akhir = $jumlahItem ? $awal + $jumlahItem - 1 : 0;
?>
<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<style>
    /* Jadwal Pusling - gaya INA Digital (Inter, skala 28/36, 18/26, 16/24, 14/20, 12/16; biru #2d659e) */
    :root {
        --pk-primary: #2d659e;
        --pk-primary-hover: #24527f;
        --pk-primary-soft: #eef4fa;
        --pk-ink: #1f2937;
        --pk-ink-subtle: #374151;
        --pk-muted: #6b7280;
        --pk-border: #e5e7eb;
        --pk-border-strong: #d1d5db;
        --pk-bg: #f7f7f8;
        --pk-radius: 12px;
        --pk-radius-sm: 8px;
        --pk-shadow: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .04);
        --pk-focus: 0 0 0 3px rgba(45, 101, 158, .2);
    }

    body { background: var(--pk-bg); }

    .pusling-wrap {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        padding-top: 110px;
        color: var(--pk-ink);
        width: 100%;
        max-width: 1600px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 1400px) {
        .pusling-wrap { padding-left: 2rem !important; padding-right: 2rem !important; }
    }

    /* Judul halaman: H4 */
    .pusling-wrap h1.pusling-title { margin: 0 0 4px; font-size: 28px; line-height: 36px; font-weight: 700; color: var(--pk-ink); }
    .pusling-wrap .pusling-sub { margin-bottom: 24px; font-size: 16px; line-height: 24px; color: var(--pk-muted); }

    /* Tab INA: garis bawah biru pada tab aktif */
    .pusling-tabs {
        display: flex;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--pk-border);
    }
    .pusling-tabs a {
        flex: 1;
        margin-bottom: -1px;
        padding: 12px 16px;
        text-align: center;
        font-size: 16px;
        line-height: 24px;
        font-weight: 500;
        color: var(--pk-muted) !important;
        text-decoration: none !important;
        border-bottom: 2px solid transparent;
    }
    .pusling-tabs a:hover { color: var(--pk-primary) !important; background: var(--pk-primary-soft); }
    .pusling-tabs a:focus-visible { outline: none; box-shadow: var(--pk-focus); }
    .pusling-tabs a.active { color: var(--pk-primary) !important; border-bottom-color: var(--pk-primary); font-weight: 600; }
    .pusling-tabs a i { margin-right: 8px; }

    /* Pencarian + tombol filter */
    .pusling-search { position: relative; flex: 1; min-width: 0; }
    .pusling-search i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--pk-muted); pointer-events: none;
    }
    .pusling-search input {
        width: 100%;
        height: 44px;
        padding: 0 14px 0 40px;
        font-size: 16px;
        line-height: 24px;
        color: var(--pk-ink);
        background: #fff;
        border: 1px solid var(--pk-border-strong);
        border-radius: var(--pk-radius-sm);
    }
    .pusling-search input:hover { border-color: #9ca3af; }
    .pusling-search input:focus { outline: none; border-color: var(--pk-primary); box-shadow: var(--pk-focus); }

    .btn-filter {
        display: inline-flex; align-items: center; gap: 8px;
        height: 44px;
        padding: 0 20px;
        font-size: 16px;
        line-height: 24px;
        font-weight: 600;
        white-space: nowrap;
        color: var(--pk-primary);
        background: #fff;
        border: 1px solid var(--pk-primary);
        border-radius: var(--pk-radius-sm);
    }
    .btn-filter:hover { background: var(--pk-primary-soft); }
    .btn-filter.active { background: var(--pk-primary); color: #fff; }
    .btn-filter:focus-visible { outline: none; box-shadow: var(--pk-focus); }
    .btn-filter .dot {
        display: inline-block;
        width: 8px; height: 8px;
        margin-left: 2px;
        border-radius: 50%;
        background: #fff;
    }

    /* Pill rentang: chip filter */
    .pusling-pill {
        display: inline-flex;
        align-items: center;
        margin: 0 8px 8px 0;
        padding: 6px 14px;
        font-size: 14px;
        line-height: 20px;
        font-weight: 500;
        color: var(--pk-ink-subtle);
        background: #fff;
        border: 1px solid var(--pk-border-strong);
        border-radius: 999px;
        text-decoration: none !important;
    }
    .pusling-pill:hover { color: var(--pk-primary); border-color: var(--pk-primary); }
    .pusling-pill.active { color: #fff; background: var(--pk-primary); border-color: var(--pk-primary); font-weight: 600; }

    /* Kartu jadwal: foto unit + overlay gelap netral */
    .pusling-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 200px;
        padding: 16px;
        border-radius: var(--pk-radius);
        overflow: hidden;
        background: #1f3b5c center/cover no-repeat;
        color: #fff !important;
        text-decoration: none !important;
        box-shadow: var(--pk-shadow);
        transition: box-shadow .15s ease;
    }
    .pusling-card:hover { box-shadow: 0 8px 24px rgba(16, 24, 40, .18); }
    .pusling-card:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(45, 101, 158, .45); }
    .pusling-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(17, 24, 39, .55) 0%, rgba(17, 24, 39, .35) 45%, rgba(17, 24, 39, .75) 100%);
    }
    .pusling-card > * { position: relative; z-index: 1; }
    .pusling-card .pc-date { margin: 0; font-size: 18px; line-height: 26px; font-weight: 700; }
    .pusling-card .pc-time { margin: 0; font-size: 14px; line-height: 20px; font-weight: 500; opacity: .9; }
    .pusling-card .pc-name-wrap { display: flex; justify-content: flex-start; }
    .pusling-card .pc-name {
        max-width: 100%;
        font-size: 16px;
        line-height: 24px;
        font-weight: 600;
        text-align: left;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pusling-card .pc-addr { display: flex; align-items: center; margin: 0; font-size: 14px; line-height: 20px; font-weight: 400; opacity: .9; }
    .pusling-card .pc-addr i { margin-right: 8px; flex-shrink: 0; }
    .pusling-card .pc-addr span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* Penanda dibatalkan: badge INA (bukan pita miring) */
    .pusling-card.batal::after {
        content: 'Dibatalkan';
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 3;
        padding: 2px 10px;
        font-size: 12px;
        line-height: 16px;
        font-weight: 600;
        color: #c62828;
        background: #fdecec;
        border-radius: 999px;
        transform: none;
    }

    /* Info + pagination */
    .pusling-info { font-size: 14px; line-height: 20px; color: var(--pk-muted); }
    .pusling-pagination ul.pagination { margin: 0; gap: 4px; border: 0; }
    .pusling-pagination .page-link {
        min-width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--pk-ink-subtle);
        background: #fff;
        border: 1px solid var(--pk-border-strong);
        border-radius: var(--pk-radius-sm) !important;
    }
    .pusling-pagination .page-link:hover { color: var(--pk-primary); border-color: var(--pk-primary); }
    .pusling-pagination .page-item.active .page-link { color: #fff; background: var(--pk-primary); border-color: var(--pk-primary); }
    .pusling-pagination .page-item.disabled .page-link { color: #9ca3af; background: #f9fafb; }

    /* Empty state */
    .pusling-empty {
        padding: 48px 24px;
        text-align: center;
        background: #fff;
        border: 1px solid var(--pk-border);
        border-radius: var(--pk-radius);
        box-shadow: var(--pk-shadow);
    }
    .pusling-empty i { font-size: 40px; color: #9ca3af; margin-bottom: 16px; }
    .pusling-empty h4 { margin-bottom: 4px; font-size: 18px; line-height: 26px; font-weight: 600; color: var(--pk-ink); }
    .pusling-empty p { margin-bottom: 20px; font-size: 14px; line-height: 20px; color: var(--pk-muted); }

    /* Tombol INA */
    .pk-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 40px;
        padding: 8px 16px;
        font-size: 14px;
        line-height: 20px;
        font-weight: 600;
        border-radius: var(--pk-radius-sm);
        text-decoration: none;
    }
    .pk-btn-primary { color: #fff; background: var(--pk-primary); border: 1px solid var(--pk-primary); }
    .pk-btn-primary:hover { color: #fff; background: var(--pk-primary-hover); border-color: var(--pk-primary-hover); }
    .pk-btn-outline { color: var(--pk-primary); background: #fff; border: 1px solid var(--pk-primary); }
    .pk-btn-outline:hover { color: var(--pk-primary-hover); background: var(--pk-primary-soft); }
    .pk-btn:focus-visible { outline: none; box-shadow: var(--pk-focus); }

    /* Modal filter tanggal */
    #modalFilterTanggal .modal-content { border: 0; border-radius: var(--pk-radius); }
    #modalFilterTanggal .modal-header { padding: 16px 24px; border-bottom: 1px solid var(--pk-border) !important; }
    #modalFilterTanggal .modal-title { font-size: 18px !important; line-height: 26px; font-weight: 600; color: var(--pk-ink); }
    #modalFilterTanggal .modal-body { padding: 20px 24px; }
    #modalFilterTanggal .modal-footer { padding: 16px 24px; gap: 8px; border-top: 1px solid var(--pk-border) !important; }
    #modalFilterTanggal label { font-size: 14px; line-height: 20px; font-weight: 600; color: var(--pk-ink-subtle); }
    #modalFilterTanggal .form-control {
        min-height: 44px;
        font-size: 16px;
        border: 1px solid var(--pk-border-strong);
        border-radius: var(--pk-radius-sm);
    }
    #modalFilterTanggal .form-control:focus { border-color: var(--pk-primary); box-shadow: var(--pk-focus); }
    #modalFilterTanggal .pk-help { font-size: 12px; line-height: 16px; color: var(--pk-muted); }

    @media (max-width: 576px) {
        .pusling-wrap h1.pusling-title { font-size: 24px; line-height: 32px; }
        .pusling-tabs a { font-size: 14px; line-height: 20px; padding: 10px 8px; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid px-4 pusling-wrap mb-5">

    <!-- Header -->
    <h1 class="pusling-title">Jadwal Pusling</h1>
    <p class="pusling-sub">Simak jadwal perpustakaan keliling sekitarmu</p>

    <!-- Nav Tab -->
    <div class="pusling-tabs">
        <a class="<?= $tab === 'jadwal-layanan' ? 'active' : '' ?>"
            href="<?= site_url('perpustakaan-keliling?tab=jadwal-layanan') ?>"><i class="fa fa-calendar-alt"></i>Jadwal
            Layanan</a>
        <a class="<?= $tab === 'riwayat' ? 'active' : '' ?>"
            href="<?= site_url('perpustakaan-keliling?tab=riwayat') ?>"><i class="fa fa-history"></i>Riwayat Layanan</a>
    </div>

    <!-- Search + Filter tanggal -->
    <form id="formPusling" method="GET" action="<?= site_url('perpustakaan-keliling') ?>">
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">
        <?php if ($tab === 'jadwal-layanan'): ?>
            <input type="hidden" name="rentang" value="<?= esc($rentang) ?>">
        <?php endif; ?>

        <div class="d-flex" style="gap: 12px;">
            <div class="pusling-search">
                <i class="fa fa-search"></i>
                <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Cari nama lokasi atau alamat..." aria-label="Cari lokasi">
            </div>
            <button type="button" class="btn-filter <?= $filterAktif ? 'active' : '' ?>" data-bs-toggle="modal"
                data-bs-target="#modalFilterTanggal">
                <i class="fa fa-filter" aria-hidden="true"></i> Filter<?php if ($filterAktif): ?><span
                        class="dot"></span><?php endif; ?>
            </button>
        </div>

    </form>

    <!-- Modal filter tanggal (input terhubung ke #formPusling lewat atribut form) -->
    <div class="modal fade" id="modalFilterTanggal" tabindex="-1" aria-labelledby="modalFilterTanggalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFilterTanggalLabel">
                        Filter Tanggal</h5>
                    <!-- Tombol Close Bootstrap 5 -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="mb-1" for="fDari">Dari Tanggal</label>
                        <input type="date" id="fDari" class="form-control" name="dari" form="formPusling"
                            value="<?= esc($dari) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="mb-1" for="fSampai">Sampai Tanggal</label>
                        <input type="date" id="fSampai" class="form-control" name="sampai" form="formPusling"
                            value="<?= esc($sampai) ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?= site_url('perpustakaan-keliling?tab=' . urlencode($tab) . '&q=' . urlencode($q)) ?>"
                        class="pk-btn pk-btn-outline">Reset Tanggal</a>
                    <button type="submit" form="formPusling" class="pk-btn pk-btn-primary">Terapkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pill rentang (hanya tab jadwal & tanggal custom kosong) -->
    <?php if ($tab === 'jadwal-layanan' && empty($dari) && empty($sampai)): ?>
        <div class="my-3">
            <?php foreach (['semua' => 'Semua', 'hari-ini' => 'Hari Ini', 'minggu-ini' => 'Pekan Ini', 'bulan-ini' => 'Bulan Ini'] as $key => $label): ?>
                <a href="<?= site_url('perpustakaan-keliling?tab=jadwal-layanan&rentang=' . $key . '&q=' . urlencode($q)) ?>"
                    class="pusling-pill <?= $rentang === $key ? 'active' : '' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="my-3"></div>
    <?php endif; ?>

    <!-- Grid kartu -->
    <?php if (!empty($jadwals)): ?>
        <div class="row">
            <?php foreach ($jadwals as $item): ?>
                <?php
                $bg = !empty($item->foto_unit) ? "background-image:url('" . esc($item->foto_unit, 'attr') . "');" : '';
                $batal = ($item->status === 'dibatalkan');
                ?>
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <a href="<?= site_url('perpustakaan-keliling/' . $item->slug) ?>"
                        class="pusling-card <?= $batal ? 'batal' : '' ?>" style="<?= $bg ?>"
                        title="<?= $batal && !empty($item->alasan_batal) ? 'Dibatalkan: ' . esc($item->alasan_batal, 'attr') : esc($item->nama_lokasi, 'attr') ?>">

                        <div>
                            <p class="pc-date"><?= $tglIndo($item->tanggal) ?></p>
                            <p class="pc-time"><?= date('H:i', strtotime($item->jam_mulai)) ?> -
                                <?= date('H:i', strtotime($item->jam_selesai)) ?> WIB
                            </p>
                        </div>

                        <div class="pc-name-wrap">
                            <div class="pc-name"><?= esc($item->nama_lokasi) ?></div>
                        </div>

                        <p class="pc-addr">
                            <i class="fa fa-map-marker-alt"></i>
                            <span><?= esc($item->alamat) ?></span>
                        </p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer: info + pagination -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-2" style="gap: 12px;">
            <div class="pusling-info">
                <?php if ($total !== null): ?>
                    Menampilkan <?= $awal ?> hingga <?= $akhir ?> dari <?= number_format($total, 0, ',', '') ?> hasil
                <?php endif; ?>
            </div>
            <div class="pusling-pagination"><?= $paginationLinks ?></div>
        </div>

    <?php else: ?>
        <div class="pusling-empty">
            <i class="fa fa-calendar-times" aria-hidden="true"></i>
            <h4><?= $tab === 'jadwal-layanan' ? 'Belum ada jadwal mendatang' : 'Tidak ada riwayat kunjungan' ?></h4>
            <p>Tidak ada jadwal yang cocok dengan kriteria pencarian Anda.</p>
            <?php if ($tab === 'jadwal-layanan'): ?>
                <a href="<?= site_url('perpustakaan-keliling?tab=riwayat') ?>" class="pk-btn pk-btn-outline">Lihat Riwayat
                    Kunjungan</a>
            <?php else: ?>
                <a href="<?= site_url('perpustakaan-keliling?tab=jadwal-layanan') ?>" class="pk-btn pk-btn-primary">Lihat Jadwal
                    Mendatang</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection('content'); ?>