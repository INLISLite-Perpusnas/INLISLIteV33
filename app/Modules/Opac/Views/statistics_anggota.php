<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
    :root {
        --op-primary: #1e3a8a;
        --op-primary-light: #3b5bdb;
        --op-primary-soft: #eef2ff;
        --op-ink: #111827;
        --op-muted: #6b7280;
        --op-border: #e5e7eb;
        --op-bg: #f6f7fb;
        --op-radius-lg: 20px;
        --op-radius-md: 14px;
        --op-radius-sm: 10px;
        --op-shadow-sm: 0 1px 3px rgba(17, 24, 39, 0.06), 0 1px 2px rgba(17, 24, 39, 0.04);
        --op-shadow-md: 0 10px 30px -12px rgba(30, 58, 138, 0.18);
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1600px; margin-left: auto; margin-right: auto; }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2.5rem; padding-right: 2.5rem; }
    }

    /* Page header */
    .op-page-header { text-align: center; margin-bottom: 2.75rem; }
    .op-page-header .op-icon-badge {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem;
        box-shadow: var(--op-shadow-md);
        margin-bottom: 1rem;
    }
    .op-page-header h1 { font-weight: 800; letter-spacing: -0.02em; color: var(--op-ink); font-size: 2.15rem; }
    .op-page-header p { color: var(--op-muted); font-size: 1.02rem; }

    .op-section-heading {
        font-weight: 800;
        color: var(--op-ink);
        font-size: 1.3rem;
        display: flex; align-items: center; gap: .6rem;
        margin-bottom: 1.25rem;
    }
    .op-section-heading .op-icon-badge-sm {
        width: 38px; height: 38px; border-radius: 11px;
        background: var(--op-primary-soft); color: var(--op-primary);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }

    /* Overview stat cards */
    .op-stat-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .op-stat-card:hover { transform: translateY(-3px); box-shadow: var(--op-shadow-md); }
    .op-stat-icon {
        flex: 0 0 auto;
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        color: #fff;
    }
    .op-stat-icon.i-primary { background: linear-gradient(135deg, #1e3a8a, #3b5bdb); }
    .op-stat-icon.i-success { background: linear-gradient(135deg, #059669, #34d399); }
    .op-stat-icon.i-info    { background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .op-stat-icon.i-warning { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .op-stat-value { font-weight: 800; font-size: 1.55rem; color: var(--op-ink); line-height: 1.1; }
    .op-stat-label { color: var(--op-muted); font-size: .85rem; font-weight: 500; }
    .op-stat-sub { color: #9ca3af; font-size: .75rem; font-weight: 500; }

    /* Card shells */
    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        height: 100%;
    }
    .op-card-header {
        padding: 1.1rem 1.4rem;
        display: flex; align-items: center; gap: .6rem;
        border-bottom: 1px solid var(--op-border);
        font-weight: 700;
        color: var(--op-ink);
        font-size: 1rem;
    }
    .op-card-header .op-header-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: .85rem;
        flex: 0 0 auto;
    }
    .op-card-body { padding: 1.4rem; }

    /* Gender mini cards */
    .op-gender-mini {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        padding: 1rem;
        text-align: center;
        background: #fafbfd;
        height: 100%;
    }
    .op-gender-mini h4 { font-weight: 800; margin-bottom: .15rem; }

    /* Progress */
    .op-progress { height: 10px; border-radius: 999px; background: #f1f2f6; overflow: hidden; }
    .op-progress .op-progress-bar { height: 100%; border-radius: 999px; transition: width .5s ease; }
    .op-progress.lg { height: 25px; border-radius: 999px; }
    .op-progress.lg .op-progress-bar { display: flex; align-items: center; justify-content: flex-end; padding-right: .6rem; color: #fff; font-size: .78rem; font-weight: 700; }
    .op-progress.thin { height: 8px; }

    .op-grad-primary { background: linear-gradient(90deg,#1e3a8a,#3b5bdb) !important; }
    .op-grad-success { background: linear-gradient(90deg,#059669,#34d399) !important; }
    .op-grad-info    { background: linear-gradient(90deg,#0891b2,#38bdf8) !important; }
    .op-grad-warning { background: linear-gradient(90deg,#d97706,#fbbf24) !important; }
    .op-grad-secondary { background: linear-gradient(90deg,#4b5563,#9ca3af) !important; }
    .op-grad-violet { background: linear-gradient(90deg,#667eea,#764ba2) !important; }

    .op-badge-soft { border-radius: 999px; padding: .3rem .7rem; font-weight: 700; font-size: .78rem; display: inline-flex; align-items: center; gap: .3rem; }
    .op-badge-primary { background: var(--op-primary-soft); color: var(--op-primary); }
    .op-badge-info { background: #eff8ff; color: #175cd3; }
    .op-badge-warning { background: #fffaeb; color: #b54708; }
    .op-badge-muted { background: #f4f4f5; color: #3f3f46; }

    /* Table */
    .op-table-wrap { border: 1px solid var(--op-border); border-radius: var(--op-radius-md); overflow: hidden; }
    .op-table-wrap table { margin-bottom: 0; }
    .op-table-wrap thead th {
        background: #f8f9fc; color: var(--op-ink); font-size: .74rem;
        text-transform: uppercase; letter-spacing: .04em; font-weight: 700;
        border: none; padding: .75rem 1rem;
    }
    .op-table-wrap tbody td { padding: .65rem 1rem; vertical-align: middle; font-size: .875rem; border-color: #f0f1f5; }
    .op-table-wrap tbody tr:hover { background: #fafbff; }

    /* Trend mini stat boxes */
    .op-trend-box {
        text-align: center;
        padding: 1.1rem;
        background: #fafbfd;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        height: 100%;
    }
    .op-trend-box h4 { font-weight: 800; margin-bottom: .2rem; }

    /* Insights */
    .op-insight-list { list-style: none; padding: 0; margin: 0; }
    .op-insight-list li { display: flex; align-items: flex-start; gap: .6rem; padding: .55rem 0; font-size: .9rem; color: var(--op-ink); }
    .op-insight-title { font-weight: 700; font-size: .95rem; display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem; }

    /* Export buttons */
    .op-export-btn {
        border-radius: var(--op-radius-sm);
        font-weight: 600; font-size: .875rem; padding: .75rem 1rem;
        border: 1px solid var(--op-border); background: #fff; color: var(--op-ink);
        display: flex; align-items: center; justify-content: center; gap: .6rem;
        width: 100%; transition: all .2s ease;
    }
    .op-export-btn:hover { border-color: #c7d2fe; background: #f7f8ff; color: var(--op-primary); }

    .op-empty-state { text-align: center; color: var(--op-muted); padding: 2.5rem 1rem; }
    .op-empty-state i { color: #d1d5db; }

    canvas { max-height: 340px; }

    @media print {
        .btn, .op-export-btn, .op-card-header { display: none !important; }
        .op-card { border: 1px solid #dee2e6 !important; break-inside: avoid; page-break-inside: avoid; }
        .op-container { max-width: 100% !important; }
    }
</style>

<div class="container-fluid op-container py-5" style="padding-top: 100px !important; padding-bottom: 40px !important;">

    <!-- Page Header -->
    <div class="op-page-header">
        <div class="op-icon-badge"><i class="fas fa-users"></i></div>
        <h1>Statistik Keanggotaan</h1>
        <p class="mb-0">Analisis dan statistik data anggota perpustakaan</p>
    </div>

    <!-- Overview Statistics -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="op-stat-card">
                <div class="op-stat-icon i-primary"><i class="fas fa-users"></i></div>
                <div>
                    <div class="op-stat-value"><?= number_format($total_members ?? 0) ?></div>
                    <div class="op-stat-label">Total Anggota</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="op-stat-card">
                <div class="op-stat-icon i-success"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="op-stat-value"><?= number_format($active_members ?? 0) ?></div>
                    <div class="op-stat-label">Anggota Aktif</div>
                    <div class="op-stat-sub"><?= $total_members > 0 ? number_format(($active_members / $total_members) * 100, 1) : 0 ?>% dari total</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="op-stat-card">
                <div class="op-stat-icon i-info"><i class="fas fa-calendar-plus"></i></div>
                <div>
                    <div class="op-stat-value"><?= number_format($new_members_this_month ?? 0) ?></div>
                    <div class="op-stat-label">Anggota Baru</div>
                    <div class="op-stat-sub">Bulan <?= date('F') ?></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="op-stat-card">
                <div class="op-stat-icon i-warning"><i class="fas fa-user-plus"></i></div>
                <div>
                    <div class="op-stat-value"><?= number_format($today_registrations ?? 0) ?></div>
                    <div class="op-stat-label">Pendaftaran Hari Ini</div>
                    <div class="op-stat-sub"><?= date('d M Y') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demographics Section -->
    <div class="op-section-heading">
        <span class="op-icon-badge-sm"><i class="fas fa-chart-pie"></i></span>
        Demografi Anggota
    </div>

    <div class="row g-4 mb-4">
        <!-- Gender Distribution -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#1e3a8a,#3b5bdb);"><i class="fas fa-venus-mars"></i></span>
                    Distribusi Berdasarkan Jenis Kelamin
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_gender)): ?>
                        <div class="row g-2 mb-3">
                            <?php
                            $genderColors = ['text-primary', 'text-danger', 'text-secondary'];
                            foreach ($by_gender as $index => $gender):
                                $percentage = ($gender->total / $total_members) * 100;
                                $gColor = $genderColors[$index % count($genderColors)];
                            ?>
                                <div class="col">
                                    <div class="op-gender-mini">
                                        <h4 class="<?= $gColor ?>"><?= number_format($gender->total) ?></h4>
                                        <p class="mb-0 text-muted small"><?= esc($gender->gender) ?></p>
                                        <small class="text-muted"><?= number_format($percentage, 1) ?>%</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <canvas id="genderChart" height="100"></canvas>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-venus-mars fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data jenis kelamin tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Age Distribution -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#059669,#34d399);"><i class="fas fa-birthday-cake"></i></span>
                    Distribusi Berdasarkan Rentang Usia
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_age_range)): ?>
                        <div class="mb-3 text-center">
                            <span class="op-badge-soft op-badge-primary" style="font-size:.85rem;">
                                Usia Rata-rata: <?= number_format($avg_age ?? 0, 1) ?> tahun
                            </span>
                        </div>

                        <?php
                        $maxCount = max(array_column($by_age_range, 'total'));
                        foreach ($by_age_range as $age):
                            $percentage = ($age->total / $total_members) * 100;
                            $barWidth = ($age->total / $maxCount) * 100;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold"><?= esc($age->age_range) ?></span>
                                    <span class="op-badge-soft op-badge-primary"><?= number_format($age->total) ?> (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div class="op-progress lg" title="<?= $age->total ?> anggota">
                                    <div class="op-progress-bar op-grad-success" style="width: <?= $barWidth ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data usia tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Education & Occupation -->
    <div class="row g-4 mb-4">
        <!-- Education Level -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#0891b2,#38bdf8);"><i class="fas fa-graduation-cap"></i></span>
                    Distribusi Berdasarkan Pendidikan
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_education)): ?>
                        <div class="op-table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Jenjang Pendidikan</th>
                                            <th class="text-end">Jumlah</th>
                                            <th class="text-end">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($by_education as $edu):
                                            $percentage = ($edu->total / $total_members) * 100;
                                        ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-book-reader text-info me-2"></i>
                                                    <?= esc($edu->education_level ?? 'Tidak Diketahui') ?>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($edu->total) ?></strong></td>
                                                <td class="text-end"><span class="op-badge-soft op-badge-info"><?= number_format($percentage, 1) ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data pendidikan tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Job Distribution -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#d97706,#fbbf24);"><i class="fas fa-briefcase"></i></span>
                    Distribusi Berdasarkan Pekerjaan
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_job)): ?>
                        <div class="op-table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Jenis Pekerjaan</th>
                                            <th class="text-end">Jumlah</th>
                                            <th class="text-end">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($by_job as $job):
                                            $percentage = ($total_members > 0) ? ($job->total / $total_members) * 100 : 0;
                                        ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-user-tie text-warning me-2"></i>
                                                    <?= esc($job->job_name ?? 'Tidak Diisi/Lainnya') ?>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($job->total) ?></strong></td>
                                                <td class="text-end"><span class="op-badge-soft op-badge-warning"><?= number_format($percentage, 1) ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-briefcase fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data pekerjaan tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#111827,#374151);"><i class="fas fa-chart-line"></i></span>
                    Tren Pendaftaran Anggota (12 Bulan Terakhir)
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_month)): ?>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="op-trend-box">
                                    <h4 class="text-primary"><?= number_format($new_members_this_month) ?></h4>
                                    <small class="text-muted">Pendaftaran Bulan Ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="op-trend-box">
                                    <h4 class="<?= $growth_rate >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $growth_rate >= 0 ? '+' : '' ?><?= number_format($growth_rate, 1) ?>%
                                    </h4>
                                    <small class="text-muted">Pertumbuhan Tahun Ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="op-trend-box">
                                    <h4 class="text-info">
                                        <?= !empty($by_month) ? number_format(array_sum(array_column($by_month, 'total')) / count($by_month), 0) : 0 ?>
                                    </h4>
                                    <small class="text-muted">Rata-rata per Bulan</small>
                                </div>
                            </div>
                        </div>

                        <canvas id="registrationChart" height="100"></canvas>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data pendaftaran tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Distribution -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="op-card">
                <div class="op-card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); border-bottom: none;">
                    <span class="op-header-icon" style="background: rgba(255,255,255,.22);"><i class="fas fa-map-marked-alt"></i></span>
                    <span class="text-white">Distribusi Geografis (Top 10 Provinsi)</span>
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_province)): ?>
                        <div class="op-table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Provinsi</th>
                                            <th class="text-end">Jumlah</th>
                                            <th style="width:30%;">Visualisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $maxProvince = $by_province[0]->total ?? 1;
                                        foreach ($by_province as $index => $province):
                                            $percentage = ($province->total / $maxProvince) * 100;
                                        ?>
                                            <tr>
                                                <td><span class="op-badge-soft op-badge-muted">#<?= $index + 1 ?></span></td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                    <strong><?= esc($province->province) ?></strong>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($province->total) ?></strong></td>
                                                <td>
                                                    <div class="op-progress lg" style="width: 100%;">
                                                        <div class="op-progress-bar op-grad-violet" style="width: <?= $percentage ?>%">
                                                            <?= number_format(($province->total / $total_members) * 100, 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data geografis tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4 h-100">
                <!-- Marital Status -->
                <div class="op-card">
                    <div class="op-card-header">
                        <span class="op-header-icon" style="background: linear-gradient(135deg,#4b5563,#9ca3af);"><i class="fas fa-ring"></i></span>
                        Status Perkawinan
                    </div>
                    <div class="op-card-body">
                        <?php if (!empty($by_marital_status)): ?>
                            <?php foreach ($by_marital_status as $marital):
                                $percentage = ($marital->total / $total_members) * 100;
                            ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small"><?= esc($marital->marital_status) ?></span>
                                        <strong class="small"><?= number_format($marital->total) ?></strong>
                                    </div>
                                    <div class="op-progress thin">
                                        <div class="op-progress-bar op-grad-secondary" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">Tidak ada data</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Identity Type -->
                <div class="op-card">
                    <div class="op-card-header">
                        <span class="op-header-icon" style="background: linear-gradient(135deg,#1e3a8a,#3b5bdb);"><i class="fas fa-id-card"></i></span>
                        Jenis Identitas
                    </div>
                    <div class="op-card-body">
                        <?php if (!empty($by_identity_type)): ?>
                            <?php foreach ($by_identity_type as $identity):
                                $percentage = ($identity->total / $total_members) * 100;
                            ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small"><?= esc($identity->identity_type ?? 'Tidak Diketahui') ?></span>
                                        <strong class="small"><?= number_format($identity->total) ?></strong>
                                    </div>
                                    <div class="op-progress thin">
                                        <div class="op-progress-bar op-grad-primary" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">Tidak ada data</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights & Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="op-card">
                <div class="op-card-header" style="background: linear-gradient(45deg, #f093fb, #f5576c); border-bottom: none;">
                    <span class="op-header-icon" style="background: rgba(255,255,255,.22);"><i class="fas fa-lightbulb"></i></span>
                    <span class="text-white">Insights & Rekomendasi</span>
                </div>
                <div class="op-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="op-insight-title text-primary"><i class="fas fa-chart-line"></i>Analisis Data</div>
                            <ul class="op-insight-list">
                                <?php if (!empty($by_gender) && isset($by_gender[0])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        Mayoritas anggota berjenis kelamin <strong><?= $by_gender[0]->gender ?></strong>
                                        (<?= number_format(($by_gender[0]->total / $total_members) * 100, 1) ?>%)
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($by_age_range)):
                                    $maxAgeGroup = array_reduce($by_age_range, function($carry, $item) {
                                        return (!$carry || $item->total > $carry->total) ? $item : $carry;
                                    });
                                ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        Rentang usia terbanyak: <strong><?= $maxAgeGroup->age_range ?></strong>
                                        (<?= number_format($maxAgeGroup->total) ?> anggota)
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <i class="fas fa-check-circle text-success"></i>
                                    Pertumbuhan anggota tahun ini:
                                    <strong class="<?= $growth_rate >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $growth_rate >= 0 ? '+' : '' ?><?= number_format($growth_rate, 1) ?>%
                                    </strong>
                                </li>

                                <?php if (!empty($by_province) && isset($by_province[0])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        Provinsi dengan anggota terbanyak:
                                        <strong><?= $by_province[0]->province ?></strong>
                                        (<?= number_format($by_province[0]->total) ?> anggota)
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <div class="op-insight-title text-warning"><i class="fas fa-star"></i>Rekomendasi</div>
                            <ul class="op-insight-list">
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Fokus kampanye pada segmen usia produktif (18-35 tahun)
                                </li>
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Tingkatkan koleksi sesuai preferensi pendidikan mayoritas
                                </li>
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Program khusus untuk meningkatkan kedisiplinan pengembalian
                                </li>
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Ekspansi layanan ke provinsi dengan anggota masih sedikit
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row">
        <div class="col-12">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#111827,#374151);"><i class="fas fa-download"></i></span>
                    Export Statistik
                </div>
                <div class="op-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button class="op-export-btn" onclick="exportStatistics('excel')">
                                <i class="fas fa-file-excel text-success"></i>
                                Export ke Excel
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="op-export-btn" onclick="exportStatistics('csv')">
                                <i class="fas fa-file-csv text-info"></i>
                                Export ke CSV
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="op-export-btn" onclick="exportStatistics('pdf')">
                                <i class="fas fa-file-pdf text-warning"></i>
                                Export ke PDF
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="op-export-btn" onclick="window.print()">
                                <i class="fas fa-print text-primary"></i>
                                Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" integrity="sha384-9MhbyIRcBVQiiC7FSd7T38oJNj2Zh+EfxS7/vjhBi4OOT78NlHSnzM31EZRWR1LZ" crossorigin="anonymous"></script>

<script>
// Chart colors
const chartColors = {
    primary: '#0d6efd',
    success: '#198754',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#0dcaf0',
    secondary: '#6c757d'
};

// Gender Distribution Chart
<?php if (!empty($by_gender)): ?>
const genderCtx = document.getElementById('genderChart');
if (genderCtx) {
    const genderData = <?= json_encode($by_gender) ?>;
    const genderLabels = genderData.map(item => item.gender);
    const genderValues = genderData.map(item => item.total);
    
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderValues,
                backgroundColor: [
                    chartColors.primary,
                    chartColors.danger,
                    chartColors.secondary
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}
<?php endif; ?>

// Job Distribution Chart
<?php if (!empty($by_job)): ?>
const jobCtx = document.getElementById('jobChart');

if (jobCtx) {
    const jobData = <?= json_encode($by_job) ?>;
    const jobLabels = jobData.map(item => {
        const name = item.job_name || 'Tidak Diketahui';
        return name.length > 15 ? name.substring(0, 15) + '...' : name;
    });
    const jobValues = jobData.map(item => item.total);
   
    
    new Chart(jobCtx, {
        type: 'bar',
        data: {
            labels: jobLabels,
            datasets: [{
                label: 'Jumlah Anggota',
                data: jobValues,
                backgroundColor: chartColors.warning,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
<?php endif; ?>

// Registration Trend Chart
<?php if (!empty($by_month)): ?>
const registrationCtx = document.getElementById('registrationChart');
if (registrationCtx) {
    const monthData = <?= json_encode($by_month) ?>;
    const monthLabels = monthData.map(item => item.month_name).reverse();
    const monthValues = monthData.map(item => item.total).reverse();
    
    new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Pendaftaran Anggota',
                data: monthValues,
                borderColor: chartColors.primary,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
<?php endif; ?>

// Export functionality
function exportStatistics(format) {
    const params = new URLSearchParams({
        'export': 'members_statistics',
        'format': format
    });
    
    const url = `<?= base_url('opac/export') ?>?${params.toString()}`;
    window.open(url, '_blank');
    
    showToast(`Export statistik anggota ${format.toUpperCase()} dimulai...`, 'info');
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; border-radius: 12px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.25); border:none;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.btn, .alert').forEach(el => el.style.display = 'none');
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.btn, .alert').forEach(el => el.style.display = '');
    document.body.classList.remove('printing');
});

// Auto-refresh (optional - every 5 minutes)
setInterval(function() {
    console.log('Auto-refresh statistics...');
    // Uncomment to enable auto-refresh
    // location.reload();
}, 300000);
</script>
<?= $this->endSection() ?>