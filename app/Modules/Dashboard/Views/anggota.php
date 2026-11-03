<?= $this->extend('App\Views\layout\main');

?>
<?= $this->section('style') ?>
<style><?= file_get_contents(FCPATH . 'assets/css/dashboard.css') ?></style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<?php
$utama = [
    ['label' => 'Jumlah Peminjaman', 'value' => $total_peminjaman ?? 0, 'sub' => 'Riwayat peminjaman Anda', 'icon' => 'fas fa-book-reader', 'color' => 'blue'],
    ['label' => 'Jumlah Pelanggaran', 'value' => $total_pelanggaran ?? 0, 'sub' => 'Riwayat pelanggaran Anda', 'icon' => 'fas fa-exclamation-triangle', 'color' => 'orange'],
];
?>
<div class="app-main__inner">
    <div class="dashboard-container">
        <header class="dash-header">
            <div>
                <h1 id="dashboard-title">Dashboard</h1>
                <p class="dash-subtitle">Ringkasan aktivitas keanggotaan Anda.</p>
            </div>
        </header>

        <section class="dash-section" aria-labelledby="dash-utama">
            <h2 class="dash-section-title" id="dash-utama">Aktivitas Anggota</h2>
            <div class="metric-grid">
                <?php foreach ($utama as $m) : ?>
                    <?= view('Dashboard\Views\metric_card', ['m' => $m]) ?>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection('page') ?>
