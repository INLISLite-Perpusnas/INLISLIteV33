<?= $this->extend('App\Views\layout\main');

?>
<?= $this->section('style') ?>
<style><?= file_get_contents(FCPATH . 'assets/css/dashboard.css') ?></style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner" aria-labelledby="dashboard-title">
    <div class="dashboard-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">

                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="page-icon">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h1 id="dashboard-title">Dashboard</h1>
                        <p class="page-subtitle">Sistem Manajemen Perpustakaan Digital</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Statistics Cards -->
        <section class="stats-grid" aria-label="Statistik anggota">
            <div class="stat-card info" role="group" aria-label="Jumlah peminjaman: <?= number_format((int) ($total_peminjaman ?? 0)) ?>">
                <div class="stat-header">
                    <div class="stat-content">
                        <h2>Jumlah Peminjaman</h2>
                        <div class="stat-number"><?= number_format((int) ($total_peminjaman ?? 0)) ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card primary" role="group" aria-label="Jumlah pelanggaran: <?= number_format((int) ($total_pelanggaran ?? 0)) ?>">
                <div class="stat-header">
                    <div class="stat-content">
                        <h2>Jumlah Pelanggaran</h2>
                        <div class="stat-number"><?= number_format((int) ($total_pelanggaran ?? 0)) ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
    <?= $this->endSection('page') ?>
