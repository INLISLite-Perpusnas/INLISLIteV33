<?php
/**
 * Header + tab navigasi halaman statistik OPAC.
 * Data: $active (katalog|sirkulasi|anggota|kunjungan), $subtitle (opsional).
 */
$tabs = [
    'katalog'   => ['url' => base_url('opac/statistics'), 'icon' => 'fa-book-open', 'label' => 'Katalog', 'title' => 'Statistik Katalog', 'subtitle' => 'Analisis dan statistik koleksi perpustakaan.'],
    'sirkulasi' => ['url' => base_url('opac/statistics_sirkulasi'), 'icon' => 'fa-right-left', 'label' => 'Sirkulasi', 'title' => 'Statistik Sirkulasi', 'subtitle' => 'Ringkasan peminjaman dan pengembalian koleksi perpustakaan.'],
    'anggota'   => ['url' => base_url('opac/statistics_anggota'), 'icon' => 'fa-users', 'label' => 'Anggota', 'title' => 'Statistik Keanggotaan', 'subtitle' => 'Analisis dan statistik data anggota perpustakaan.'],
    'kunjungan' => ['url' => base_url('opac/statistics_kunjungan'), 'icon' => 'fa-shoe-prints', 'label' => 'Kunjungan', 'title' => 'Statistik Kunjungan', 'subtitle' => 'Kenali pengunjung dan perkembangan kunjungan perpustakaan.'],
];
$current = $tabs[$active] ?? $tabs['katalog'];
?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-opac-stats.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-opac-stats.css') ?>">

<div class="st-head">
    <a href="<?= base_url('home') ?>" class="st-back" title="Kembali ke beranda" aria-label="Kembali ke beranda"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="st-badge"><i class="fa-solid <?= $current['icon'] ?>"></i></span>
    <div>
        <h1><?= esc($current['title']) ?></h1>
        <p><?= esc($subtitle ?? $current['subtitle']) ?></p>
    </div>
</div>

<nav class="st-tabs" aria-label="Jenis statistik">
    <?php foreach ($tabs as $key => $tab): ?>
        <a href="<?= $tab['url'] ?>" class="st-tab<?= $key === $active ? ' active' : '' ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>>
            <i class="fa-solid <?= $tab['icon'] ?>"></i><?= esc($tab['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>
