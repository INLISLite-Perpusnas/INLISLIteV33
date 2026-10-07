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
    @import url('https://fonts.googleapis.com/css2?family=Mulish:wght@400;600;700;800&display=swap');

    .pusling-wrap {
        font-family: 'Mulish', sans-serif;
        padding-top: 100px;
        color: #1f2937;
        width: 100%;
        max-width: 1320px;
        margin-left: auto;
        margin-right: auto;
    }

    .pusling-wrap h1.pusling-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0;
    }

    .pusling-wrap .pusling-sub {
        font-size: .8rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    /* Search + filter */
    .pusling-search {
        position: relative;
        flex: 1;
    }

    .pusling-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .pusling-search input {
        width: 100%;
        height: 42px;
        padding: 0 14px 0 42px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #f9fafb;
        font-size: .85rem;
    }

    .pusling-search input:focus {
        outline: none;
        border-color: #1d4ed8;
        background: #fff;
    }

    .btn-filter {
        height: 42px;
        padding: 0 18px;
        border: 1.5px solid #1e40af;
        color: #1e40af;
        background: #fff;
        border-radius: 6px;
        font-weight: 600;
        font-size: .85rem;
        white-space: nowrap;
    }

    .btn-filter:hover,
    .btn-filter.active {
        background: #1e40af;
        color: #fff;
    }

    .btn-filter .dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ef4444;
        margin-left: 6px;
    }

    .filter-panel {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        padding: 14px;
        margin-top: 12px;
    }

    /* Tabs */
    .pusling-tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 16px;
    }

    .pusling-tabs a {
        flex: 1;
        text-align: center;
        padding: 10px 12px;
        font-size: .85rem;
        font-weight: 600;
        color: #6b7280 !important;
        text-decoration: none !important;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
    }

    .pusling-tabs a:hover {
        color: #ef4444 !important;
        background: #f9fafb;
    }

    .pusling-tabs a.active {
        color: #ef4444 !important;
        border-bottom-color: #ef4444;
        font-weight: 800;
    }

    .pusling-tabs a i {
        margin-right: 6px;
    }

    /* Pills */
    .pusling-pill {
        display: inline-block;
        padding: 5px 14px;
        margin-right: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: .78rem;
        color: #6b7280;
        background: #f9fafb;
        text-decoration: none !important;
    }

    .pusling-pill:hover {
        color: #ef4444;
    }

    .pusling-pill.active {
        border-color: #ef4444;
        color: #ef4444;
        background: #fef2f2;
    }

    /* Card */
    .pusling-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 192px;
        padding: 16px 16px 14px;
        border-radius: 8px;
        overflow: hidden;
        background: #12306b center/cover no-repeat;
        color: #fff !important;
        text-decoration: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        transition: transform .15s, box-shadow .15s;
    }

    .pusling-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .25);
    }

    .pusling-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(10, 30, 80, .70), rgba(10, 40, 110, .80));
    }

    .pusling-card>* {
        position: relative;
        z-index: 1;
    }

    .pusling-card .pc-date {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .pusling-card .pc-time {
        font-size: .72rem;
        font-weight: 600;
        margin: 2px 0 0;
    }

    .pusling-card .pc-name-wrap {
        display: flex;
        justify-content: center;
    }

    .pusling-card .pc-name {
        max-width: 100%;
        padding: 8px 14px;
        border-radius: 6px;
        text-align: center;
        background: rgba(255, 255, 255, .22);
        backdrop-filter: blur(3px);
        font-size: .82rem;
        font-weight: 800;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pusling-card .pc-addr {
        display: flex;
        align-items: center;
        font-size: .78rem;
        font-weight: 600;
        margin: 0;
    }

    .pusling-card .pc-addr i {
        margin-right: 8px;
        flex-shrink: 0;
    }

    .pusling-card .pc-addr span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pusling-card .pc-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        font-size: .65rem;
    }

    .pusling-card.batal::after {
        content: 'DIBATALKAN';
        position: absolute;
        top: 14px;
        right: -34px;
        transform: rotate(35deg);
        background: #dc2626;
        color: #fff;
        font-size: .62rem;
        font-weight: 800;
        padding: 3px 38px;
        z-index: 3;
    }

    /* Footer / pagination */
    .pusling-info {
        font-size: .8rem;
        color: #6b7280;
    }

    .pusling-pagination ul.pagination {
        margin: 0;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
    }

    .pusling-pagination .page-link {
        border: 0;
        border-right: 1px solid #e5e7eb;
        color: #374151;
        font-size: .8rem;
        min-width: 40px;
        text-align: center;
        padding: .55rem .6rem;
        border-radius: 0 !important;
    }

    .pusling-pagination .page-item:last-child .page-link {
        border-right: 0;
    }

    .pusling-pagination .page-item.active .page-link {
        background: #fff;
        color: #ef4444;
        font-weight: 800;
    }

    .pusling-pagination .page-item.disabled .page-link {
        color: #9ca3af;
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
                <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Cari nama lokasi atau alamat...">
            </div>
            <button type="button" class="btn-filter <?= $filterAktif ? 'active' : '' ?>" data-bs-toggle="modal"
                data-bs-target="#modalFilterTanggal">
                <i class="fa fa-filter mr-1"></i> Filter<?php if ($filterAktif): ?><span
                        class="dot"></span><?php endif; ?>
            </button>
        </div>

    </form>

    <!-- Modal filter tanggal (input terhubung ke #formPusling lewat atribut form) -->
    <div class="modal fade" id="modalFilterTanggal" tabindex="-1" aria-labelledby="modalFilterTanggalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 10px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalFilterTanggalLabel" style="font-size: 1rem;">
                        Filter Tanggal</h5>
                    <!-- Tombol Close Bootstrap 5 -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1" for="fDari">Dari Tanggal</label>
                        <input type="date" id="fDari" class="form-control" name="dari" form="formPusling"
                            value="<?= esc($dari) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold mb-1" for="fSampai">Sampai Tanggal</label>
                        <input type="date" id="fSampai" class="form-control" name="sampai" form="formPusling"
                            value="<?= esc($sampai) ?>">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="<?= site_url('perpustakaan-keliling?tab=' . urlencode($tab) . '&q=' . urlencode($q)) ?>"
                        class="btn btn-outline-secondary btn-sm">Reset Tanggal</a>
                    <button type="submit" form="formPusling" class="btn btn-primary btn-sm px-4">Terapkan</button>
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
        <div class="text-center py-5">
            <i class="fa fa-calendar-times fa-4x text-muted mb-3"></i>
            <h4><?= $tab === 'jadwal-layanan' ? 'Belum ada jadwal mendatang' : 'Tidak ada riwayat kunjungan' ?></h4>
            <p class="text-muted">Tidak ada jadwal yang cocok dengan kriteria pencarian Anda.</p>
            <?php if ($tab === 'jadwal-layanan'): ?>
                <a href="<?= site_url('perpustakaan-keliling?tab=riwayat') ?>" class="btn btn-secondary">Lihat Riwayat
                    Kunjungan</a>
            <?php else: ?>
                <a href="<?= site_url('perpustakaan-keliling?tab=jadwal-layanan') ?>" class="btn btn-primary">Lihat Jadwal
                    Mendatang</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection('content'); ?>