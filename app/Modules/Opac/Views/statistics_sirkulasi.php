<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
    :root {
        --vk-primary: #1e3a8a;
        --vk-primary-light: #3b5bdb;
        --vk-ink: #111827;
        --vk-muted: #6b7280;
        --vk-border: #e5e7eb;
        --vk-bg: #f6f7fb;
        --vk-radius: 16px;
    }
    body { background: var(--vk-bg); }
    .vk-container { max-width: 1500px; margin: 0 auto; }
    .vk-header { text-align: center; margin-bottom: 2.5rem; }
    .vk-icon { width: 64px; height: 64px; border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; background: linear-gradient(135deg, var(--vk-primary), var(--vk-primary-light)); margin-bottom: 1rem; }
    .vk-header h1 { color: var(--vk-ink); font-weight: 800; }
    .vk-header p { color: var(--vk-muted); }
    .vk-card { background: #fff; border: 1px solid var(--vk-border); border-radius: var(--vk-radius); box-shadow: 0 1px 3px rgba(17,24,39,.06); height: 100%; overflow: hidden; }
    .vk-card-header { padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--vk-border); color: var(--vk-ink); font-weight: 700; }
    .vk-card-body { padding: 1.4rem; }
    .vk-stat { display: flex; align-items: center; gap: 1rem; padding: 1.35rem; height: 100%; }
    .vk-stat-icon { width: 52px; height: 52px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 1.2rem; flex: 0 0 auto; }
    .vk-stat-value { color: var(--vk-ink); font-size: 1.55rem; font-weight: 800; line-height: 1.1; }
    .vk-stat-label { color: var(--vk-muted); font-size: .85rem; }
    .vk-stat-sub { color: #9ca3af; font-size: .75rem; }
    .vk-table { margin: 0; }
    .vk-table th { background: #f8f9fc; color: var(--vk-ink); font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; border: 0; }
    .vk-table td { font-size: .875rem; vertical-align: middle; border-color: #f0f1f5; }
    .vk-empty { color: var(--vk-muted); text-align: center; padding: 2rem 1rem; }
    .vk-filter { background: #fff; border: 1px solid var(--vk-border); border-radius: var(--vk-radius); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .vk-filter-label { color: var(--vk-ink); font-weight: 700; }
    .vk-filter select { min-width: 220px; border: 1px solid var(--vk-border); border-radius: 10px; padding: .65rem 2.5rem .65rem .85rem; color: var(--vk-ink); background-color: #fff; }
    .vk-filter-controls { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: .75rem; }
    .vk-custom-range { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; color: var(--vk-muted); }
    .vk-custom-range[hidden] { display: none; }
    .vk-custom-range input { border: 1px solid var(--vk-border); border-radius: 10px; padding: .55rem .75rem; color: var(--vk-ink); }
    .vk-custom-range .btn { border-radius: 10px; }
    .vk-chart-wrap { position: relative; height: 320px; }
    .vk-export { display: flex; gap: .5rem; }
    .vk-export .btn { border-radius: 10px; }
    @media (max-width: 576px) { .vk-filter { align-items: stretch; flex-direction: column; } .vk-filter-controls, .vk-custom-range, .vk-export { flex-direction: column; align-items: stretch; } .vk-filter select, .vk-custom-range input { width: 100%; } }
    .vk-heatmap-wrap { overflow-x: auto; }
    .vk-heatmap { min-width: 980px; border-collapse: separate; border-spacing: 4px; width: 100%; }
    .vk-heatmap th { color: var(--vk-muted); font-size: .78rem; font-weight: 700; text-align: center; padding: .35rem .25rem; white-space: nowrap; }
    .vk-heatmap th:first-child { text-align: left; width: 95px; }
    .vk-heatmap td { height: 42px; min-width: 58px; border-radius: 11px; text-align: center; color: #20558e; font-size: .86rem; background: #eef3f8; }
    .vk-heatmap td:not(:first-child) { background: hsl(211 52% calc(97% - (var(--heat-level) * 40%))); }
    .vk-heatmap .vk-day-label { background: transparent !important; color: var(--vk-muted); font-weight: 700; text-align: left; padding-left: .25rem; }
</style>

<div class="container-fluid vk-container py-5" style="padding-top: 100px !important;">
    <div class="vk-header">
        <div class="vk-icon"><i class="fas fa-exchange-alt"></i></div>
        <h1>Statistik Sirkulasi</h1>
        <p class="mb-0">Ringkasan peminjaman dan pengembalian koleksi perpustakaan</p>
    </div>

    <form class="vk-filter" method="get" action="<?= base_url('opac/statistics_sirkulasi') ?>">
        <label class="vk-filter-label mb-0" for="stat-period"><i class="fas fa-filter me-2 text-primary"></i>Periode Statistik</label>
        <div class="vk-filter-controls">
            <select id="stat-period" name="period" aria-label="Pilih periode statistik sirkulasi">
                <?php foreach ($periods as $periodKey => $period): ?>
                    <option value="<?= esc($periodKey) ?>" <?= $selected_period === (string) $periodKey ? 'selected' : '' ?>><?= esc($period['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <div id="stat-custom-range" class="vk-custom-range" <?= $selected_period === 'custom' ? '' : 'hidden' ?>>
                <input type="date" name="start_date" id="stat-start" value="<?= esc($start_date) ?>" max="<?= date('Y-m-d') ?>" aria-label="Tanggal mulai" <?= $selected_period === 'custom' ? 'required' : 'disabled' ?>>
                <span>s/d</span>
                <input type="date" name="end_date" id="stat-end" value="<?= esc($end_date) ?>" max="<?= date('Y-m-d') ?>" aria-label="Tanggal akhir" <?= $selected_period === 'custom' ? 'required' : 'disabled' ?>>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
            <div class="vk-export">
                <button type="button" class="btn btn-success" data-export="excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-danger" data-export="pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
            </div>
        </div>
    </form>

    <div class="row g-4 mb-4">
        <?php $stats = [
            ['fas fa-book-reader', '#1e3a8a', $total_loans ?? 0, 'Total Peminjaman', $selected_period_label],
            ['fas fa-undo-alt', '#059669', $total_returns ?? 0, 'Total Pengembalian', $selected_period_label],
            ['fas fa-user-check', '#0891b2', $total_borrowers ?? 0, 'Anggota Peminjam', $selected_period_label],
            ['fas fa-hourglass-half', '#d97706', $on_loan ?? 0, 'Sedang Dipinjam', number_format($on_loan_overdue ?? 0) . ' melewati jatuh tempo'],
        ]; foreach ($stats as $stat): ?>
            <div class="col-lg-3 col-md-6">
                <div class="vk-card"><div class="vk-stat">
                    <div class="vk-stat-icon" style="background:<?= $stat[1] ?>"><i class="<?= $stat[0] ?>"></i></div>
                    <div><div class="vk-stat-value"><?= number_format($stat[2]) ?></div><div class="vk-stat-label"><?= $stat[3] ?></div><div class="vk-stat-sub"><?= esc($stat[4]) ?></div></div>
                </div></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    $heatmapHours = range(7, 20);
    $heatmapDays = [1 => 'Minggu', 2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu', 5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu'];
    $heatmap = [];
    $heatmapMax = 0;
    foreach ($hourly_loans ?? [] as $loanRow) {
        $dayNumber = (int) $loanRow->day_number;
        $hour = (int) $loanRow->loan_hour;
        $total = (int) $loanRow->total;
        $heatmap[$dayNumber][$hour] = $total;
        $heatmapMax = max($heatmapMax, $total);
    }
    ?>
    <div class="vk-card mb-4">
        <div class="vk-card-header"><i class="fas fa-clock me-2 text-primary"></i>Pola Jam Peminjaman</div>
        <div class="vk-card-body vk-heatmap-wrap">
            <table class="vk-heatmap" aria-label="Pola jam peminjaman">
                <thead><tr><th></th><?php foreach ($heatmapHours as $hour): ?><th><?= $hour ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($heatmapDays as $dayNumber => $dayName): ?>
                        <tr><th class="vk-day-label"><?= $dayName ?></th>
                            <?php foreach ($heatmapHours as $hour):
                                $value = $heatmap[$dayNumber][$hour] ?? 0;
                                $level = $heatmapMax > 0 ? $value / $heatmapMax : 0;
                            ?><td style="--heat-level: <?= $level ?>" title="<?= esc($dayName) ?> pukul <?= $hour ?>:00 - <?= number_format($value) ?> peminjaman"><?= $value > 0 ? number_format($value) : '' ?></td><?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8"><div class="vk-card">
            <div class="vk-card-header"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Sirkulasi (<?= esc($trend_granularity) ?>) - <?= esc($selected_period_label) ?></div>
            <div class="vk-card-body">
                <?php if (array_sum($trend['loans']) + array_sum($trend['returns']) > 0): ?>
                    <div class="vk-chart-wrap"><canvas id="circulation-trend-chart" aria-label="Grafik tren sirkulasi"></canvas></div>
                <?php else: ?><div class="vk-empty">Belum ada data sirkulasi.</div><?php endif; ?>
            </div>
        </div></div>
        <div class="col-lg-4"><div class="vk-card">
            <div class="vk-card-header"><i class="fas fa-calendar-check me-2 text-success"></i>Ketepatan Pengembalian</div>
            <div class="table-responsive"><table class="table vk-table"><thead><tr><th>Status</th><th>Jumlah</th><th>%</th></tr></thead><tbody>
                <?php $returnTotal = array_sum(array_map(static fn($row) => (int) $row->total, $return_status ?? [])); ?>
                <?php if (!empty($return_status)): foreach ($return_status as $row): ?><tr><td><?= esc($row->return_status) ?></td><td><strong><?= number_format($row->total) ?></strong></td><td><?= $returnTotal > 0 ? number_format(($row->total / $returnTotal) * 100, 1) : 0 ?>%</td></tr><?php endforeach; else: ?><tr><td colspan="3" class="vk-empty">Belum ada data.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div></div>
    </div>

    <div class="vk-card mb-4">
        <div class="vk-card-header"><i class="fas fa-trophy me-2 text-warning"></i>10 Koleksi Paling Sering Dipinjam - <?= esc($selected_period_label) ?></div>
        <div class="table-responsive"><table class="table vk-table"><thead><tr><th>#</th><th>Judul</th><th>Pengarang</th><th>Dipinjam</th></tr></thead><tbody>
            <?php if (!empty($top_collections)): foreach ($top_collections as $i => $row): ?><tr><td><?= $i + 1 ?></td><td><?= esc($row->title) ?></td><td><?= esc($row->author ?: '-') ?></td><td><strong><?= number_format($row->total) ?></strong></td></tr><?php endforeach; else: ?><tr><td colspan="4" class="vk-empty">Belum ada data.</td></tr><?php endif; ?>
        </tbody></table></div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" integrity="sha384-9MhbyIRcBVQiiC7FSd7T38oJNj2Zh+EfxS7/vjhBi4OOT78NlHSnzM31EZRWR1LZ" crossorigin="anonymous"></script>
<script>
(function () {
    const periodSelect = document.getElementById('stat-period');
    const customRange = document.getElementById('stat-custom-range');
    const customInputs = customRange.querySelectorAll('input');
    periodSelect.addEventListener('change', function () {
        const isCustom = this.value === 'custom';
        customRange.hidden = !isCustom;
        customInputs.forEach(function (input) {
            input.disabled = !isCustom;
            input.required = isCustom;
        });
        if (!isCustom) {
            this.form.submit();
        }
    });
    document.getElementById('stat-start').addEventListener('change', function () {
        document.getElementById('stat-end').min = this.value;
    });

    // Export: kirim POST ke URL yang sama (filter periode ikut di query string) beserta gambar grafik.
    document.querySelectorAll('[data-export]').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = window.location.href;
            const fields = { export: this.dataset.export, chart_image: chartImage() };
            Object.keys(fields).forEach(function (name) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = fields[name];
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
            form.remove();
        });
    });
    function chartImage() {
        const source = document.getElementById('circulation-trend-chart');
        if (!source) {
            return '';
        }
        // Latar putih agar grafik tidak transparan di PDF/Excel.
        const output = document.createElement('canvas');
        output.width = source.width;
        output.height = source.height;
        const ctx = output.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, output.width, output.height);
        ctx.drawImage(source, 0, 0);
        return output.toDataURL('image/png');
    }

    const canvas = document.getElementById('circulation-trend-chart');
    if (!canvas || typeof Chart === 'undefined') {
        return;
    }
    const trend = <?= json_encode($trend) ?>;
    const pointRadius = trend.labels.length > 60 ? 0 : 3;
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [
                {
                    label: 'Peminjaman',
                    data: trend.loans,
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(59, 91, 219, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: pointRadius,
                    pointHoverRadius: 5
                },
                {
                    label: 'Pengembalian',
                    data: trend.returns,
                    borderColor: '#059669',
                    backgroundColor: 'transparent',
                    borderDash: [5, 4],
                    tension: 0.3,
                    pointRadius: pointRadius,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
})();
</script>
<?= $this->endSection() ?>
