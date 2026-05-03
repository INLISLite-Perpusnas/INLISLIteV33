<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<div class="container py-5" style="padding-top: 100px !important; padding-bottom: 40px !important;">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-users me-3"></i>
                    Statistik Keanggotaan
                </h1>
                <p class="lead text-muted">
                    Analisis dan statistik data anggota perpustakaan
                </p>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center border-0 shadow h-100">
                <div class="card-body bg-primary text-white rounded">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($total_members ?? 0) ?></h3>
                    <p class="mb-0">Total Anggota</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center border-0 shadow h-100">
                <div class="card-body bg-success text-white rounded">
                    <i class="fas fa-user-check fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($active_members ?? 0) ?></h3>
                    <p class="mb-0">Anggota Aktif</p>
                    <small class="opacity-75"><?= $total_members > 0 ? number_format(($active_members / $total_members) * 100, 1) : 0 ?>% dari total</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center border-0 shadow h-100">
                <div class="card-body bg-info text-white rounded">
                    <i class="fas fa-calendar-plus fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($new_members_this_month ?? 0) ?></h3>
                    <p class="mb-0">Anggota Baru</p>
                    <small class="opacity-75">Bulan <?= date('F') ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center border-0 shadow h-100">
                <div class="card-body bg-warning text-white rounded">
                    <i class="fas fa-user-plus fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($today_registrations ?? 0) ?></h3>
                    <p class="mb-0">Pendaftaran Hari Ini</p>
                    <small class="opacity-75"><?= date('d M Y') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Demographics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-chart-pie me-2 text-primary"></i>
                Demografi Anggota
            </h3>
        </div>
    </div>

    <div class="row">
        <!-- Gender Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-venus-mars me-2"></i>
                        Distribusi Berdasarkan Jenis Kelamin
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_gender)): ?>
                        <div class="row text-center mb-3">
                            <?php 
                            $colors = ['primary', 'danger', 'secondary'];
                            foreach ($by_gender as $index => $gender): 
                                $percentage = ($gender->total / $total_members) * 100;
                                $color = $colors[$index % count($colors)];
                            ?>
                                <div class="col">
                                    <div class="p-3 border rounded">
                                        <h4 class="text-<?= $color ?> fw-bold"><?= number_format($gender->total) ?></h4>
                                        <p class="mb-0 text-muted"><?= esc($gender->gender) ?></p>
                                        <small class="text-muted"><?= number_format($percentage, 1) ?>%</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <canvas id="genderChart" height="100"></canvas>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-venus-mars fa-3x mb-3"></i>
                            <p>Tidak ada data jenis kelamin tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Age Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-birthday-cake me-2"></i>
                        Distribusi Berdasarkan Rentang Usia
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_age_range)): ?>
                        <div class="mb-3 text-center">
                            <h4 class="text-success">Usia Rata-rata: <?= number_format($avg_age ?? 0, 1) ?> tahun</h4>
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
                                    <span class="badge bg-success"><?= number_format($age->total) ?> (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= $barWidth ?>%"
                                         title="<?= $age->total ?> anggota">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                            <p>Tidak ada data usia tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Education & Occupation -->
    <div class="row">
        <!-- Education Level -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Distribusi Berdasarkan Pendidikan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_education)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                            <td class="text-end">
                                                <strong><?= number_format($edu->total) ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-info"><?= number_format($percentage, 1) ?>%</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                            <p>Tidak ada data pendidikan tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Job Distribution -->
        <div class="col-lg-6 mb-4">
    <div class="card shadow border-0 h-100">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-briefcase me-2"></i>
                Distribusi Berdasarkan Pekerjaan
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($by_job)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    <td class="text-end">
                                        <strong><?= number_format($job->total) ?></strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning text-dark"><?= number_format($percentage, 1) ?>%</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-briefcase fa-3x mb-3"></i>
                    <p>Tidak ada data pekerjaan tersedia</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    </div>

    <!-- Registration Trends -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Tren Pendaftaran Anggota (12 Bulan Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_month)): ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="text-primary mb-0"><?= number_format($new_members_this_month) ?></h4>
                                    <small class="text-muted">Pendaftaran Bulan Ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="text-success mb-0">
                                        <?= $growth_rate >= 0 ? '+' : '' ?><?= number_format($growth_rate, 1) ?>%
                                    </h4>
                                    <small class="text-muted">Pertumbuhan Tahun Ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="text-info mb-0">
                                        <?= !empty($by_month) ? number_format(array_sum(array_column($by_month, 'total')) / count($by_month), 0) : 0 ?>
                                    </h4>
                                    <small class="text-muted">Rata-rata per Bulan</small>
                                </div>
                            </div>
                        </div>
                        
                        <canvas id="registrationChart" height="100"></canvas>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>Tidak ada data pendaftaran tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Distribution -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Distribusi Geografis (Top 10 Provinsi)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_province)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Provinsi</th>
                                        <th class="text-end">Jumlah</th>
                                        <th>Visualisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $maxProvince = $by_province[0]->total ?? 1;
                                    foreach ($by_province as $index => $province): 
                                        $percentage = ($province->total / $maxProvince) * 100;
                                    ?>
                                        <tr>
                                            <td><span class="badge bg-secondary">#<?= $index + 1 ?></span></td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <strong><?= esc($province->province) ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <strong><?= number_format($province->total) ?></strong>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px; width: 200px;">
                                                    <div class="progress-bar" 
                                                         style="width: <?= $percentage ?>%; background: linear-gradient(135deg, #667eea, #764ba2);">
                                                        <?= number_format(($province->total / $total_members) * 100, 1) ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <p>Tidak ada data geografis tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="col-lg-4 mb-4">
            <!-- Marital Status -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-ring me-2"></i>
                        Status Perkawinan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_marital_status)): ?>
                        <?php foreach ($by_marital_status as $marital): 
                            $percentage = ($marital->total / $total_members) * 100;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= esc($marital->marital_status) ?></span>
                                    <strong><?= number_format($marital->total) ?></strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-secondary" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Tidak ada data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Identity Type -->
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Jenis Identitas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_identity_type)): ?>
                        <?php foreach ($by_identity_type as $identity): 
                            $percentage = ($identity->total / $total_members) * 100;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= esc($identity->identity_type ?? 'Tidak Diketahui') ?></span>
                                    <strong><?= number_format($identity->total) ?></strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Tidak ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights & Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #f093fb, #f5576c);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        Insights & Rekomendasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-line me-2"></i>Analisis Data:
                            </h6>
                            <ul class="list-unstyled">
                                <?php if (!empty($by_gender) && isset($by_gender[0])): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Mayoritas anggota berjenis kelamin <strong><?= $by_gender[0]->gender ?></strong> 
                                        (<?= number_format(($by_gender[0]->total / $total_members) * 100, 1) ?>%)
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (!empty($by_age_range)): 
                                    $maxAgeGroup = array_reduce($by_age_range, function($carry, $item) {
                                        return (!$carry || $item->total > $carry->total) ? $item : $carry;
                                    });
                                ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Rentang usia terbanyak: <strong><?= $maxAgeGroup->age_range ?></strong> 
                                        (<?= number_format($maxAgeGroup->total) ?> anggota)
                                    </li>
                                <?php endif; ?>
                                
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Pertumbuhan anggota tahun ini: 
                                    <strong class="<?= $growth_rate >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $growth_rate >= 0 ? '+' : '' ?><?= number_format($growth_rate, 1) ?>%
                                    </strong>
                                </li>
                                
                                <?php if (!empty($by_province) && isset($by_province[0])): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Provinsi dengan anggota terbanyak: 
                                        <strong><?= $by_province[0]->province ?></strong> 
                                        (<?= number_format($by_province[0]->total) ?> anggota)
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-warning mb-3">
                                <i class="fas fa-star me-2"></i>Rekomendasi:
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Fokus kampanye pada segmen usia produktif (18-35 tahun)
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Tingkatkan koleksi sesuai preferensi pendidikan mayoritas
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Program khusus untuk meningkatkan kedisiplinan pengembalian
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
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
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>
                        Export Statistik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-success w-100" onclick="exportStatistics('excel')">
                                <i class="fas fa-file-excel me-2"></i>
                                Export ke Excel
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-info w-100" onclick="exportStatistics('csv')">
                                <i class="fas fa-file-csv me-2"></i>
                                Export ke CSV
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-warning w-100" onclick="exportStatistics('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>
                                Export ke PDF
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-primary w-100" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

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
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
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

<style>
@media print {
    .btn, .alert, .card-header {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .container {
        max-width: 100% !important;
    }
    
    .col-lg-3, .col-lg-4, .col-lg-6, .col-lg-8, .col-lg-12 {
        width: 100% !important;
    }
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    background-color: #e9ecef;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

canvas {
    max-height: 400px;
}
</style>
<?= $this->endSection() ?>