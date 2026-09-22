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
    .vk-row { display: flex; align-items: center; gap: .8rem; margin-bottom: 1rem; }
    .vk-row:last-child { margin-bottom: 0; }
    .vk-row-label { width: 90px; color: var(--vk-ink); font-size: .85rem; font-weight: 600; }
    .vk-bar-track { flex: 1; height: 12px; background: #eef0f5; border-radius: 999px; overflow: hidden; }
    .vk-bar { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--vk-primary), var(--vk-primary-light)); }
    .vk-row-value { width: 55px; text-align: right; color: var(--vk-muted); font-size: .82rem; }
    .vk-table { margin: 0; }
    .vk-table th { background: #f8f9fc; color: var(--vk-ink); font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; border: 0; }
    .vk-table td { font-size: .875rem; vertical-align: middle; border-color: #f0f1f5; }
    .vk-empty { color: var(--vk-muted); text-align: center; padding: 2rem 1rem; }
    .vk-filter { background: #fff; border: 1px solid var(--vk-border); border-radius: var(--vk-radius); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .vk-filter-label { color: var(--vk-ink); font-weight: 700; }
    .vk-filter select { min-width: 220px; border: 1px solid var(--vk-border); border-radius: 10px; padding: .65rem 2.5rem .65rem .85rem; color: var(--vk-ink); background-color: #fff; }
    @media (max-width: 576px) { .vk-filter { align-items: stretch; flex-direction: column; } .vk-filter select { width: 100%; } }
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
        <div class="vk-icon"><i class="fas fa-chart-line"></i></div>
        <h1>Statistik Kunjungan</h1>
        <p class="mb-0">Ringkasan kunjungan anggota dan rombongan perpustakaan</p>
    </div>

    <form class="vk-filter" method="get" action="<?= base_url('opac/statistics_kunjungan') ?>">
        <label class="vk-filter-label mb-0" for="visit-period"><i class="fas fa-filter me-2 text-primary"></i>Periode Statistik</label>
        <select id="visit-period" name="period" onchange="this.form.submit()" aria-label="Pilih periode statistik kunjungan">
            <?php foreach ($periods as $periodKey => $period): ?>
                <option value="<?= esc($periodKey) ?>" <?= $selected_period === $periodKey ? 'selected' : '' ?>><?= esc($period['label']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="row g-4 mb-4">
        <?php $stats = [
            ['fas fa-users', '#1e3a8a', $total_visitors ?? 0, 'Total Pengunjung', $selected_period_label],
            ['fas fa-clipboard-list', '#059669', $total_visits ?? 0, 'Total Kunjungan', $selected_period_label],
            ['fas fa-calendar-day', '#0891b2', $today_visitors ?? 0, 'Pengunjung Hari Ini', date('d M Y')],
            ['fas fa-calendar-alt', '#d97706', $month_visitors ?? 0, 'Pengunjung Bulan Ini', date('F Y')],
        ]; foreach ($stats as $stat): ?>
            <div class="col-lg-3 col-md-6">
                <div class="vk-card"><div class="vk-stat">
                    <div class="vk-stat-icon" style="background:<?= $stat[1] ?>"><i class="<?= $stat[0] ?>"></i></div>
                    <div><div class="vk-stat-value"><?= number_format($stat[2]) ?></div><div class="vk-stat-label"><?= $stat[3] ?></div><div class="vk-stat-sub"><?= $stat[4] ?></div></div>
                </div></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    $heatmapHours = range(7, 20);
    $heatmapDays = [1 => 'Minggu', 2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu', 5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu'];
    $heatmap = [];
    $heatmapMax = 0;
    foreach ($hourly_visits ?? [] as $visitRow) {
        $dayNumber = (int) $visitRow->day_number;
        $hour = (int) $visitRow->visit_hour;
        $visitors = (int) $visitRow->visitors;
        $heatmap[$dayNumber][$hour] = $visitors;
        $heatmapMax = max($heatmapMax, $visitors);
    }
    ?>
    <div class="vk-card mb-4">
        <div class="vk-card-header"><i class="fas fa-clock me-2 text-primary"></i>Pola Jam Kunjungan</div>
        <div class="vk-card-body vk-heatmap-wrap">
            <table class="vk-heatmap" aria-label="Pola jam kunjungan">
                <thead><tr><th></th><?php foreach ($heatmapHours as $hour): ?><th><?= $hour ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($heatmapDays as $dayNumber => $dayName): ?>
                        <tr><th class="vk-day-label"><?= $dayName ?></th>
                            <?php foreach ($heatmapHours as $hour):
                                $value = $heatmap[$dayNumber][$hour] ?? 0;
                                $level = $heatmapMax > 0 ? $value / $heatmapMax : 0;
                            ?><td style="--heat-level: <?= $level ?>" title="<?= esc($dayName) ?> pukul <?= $hour ?>:00 - <?= number_format($value) ?> pengunjung"><?= $value > 0 ? number_format($value) : '' ?></td><?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7"><div class="vk-card">
            <div class="vk-card-header"><i class="fas fa-chart-bar me-2 text-primary"></i>Tren Pengunjung - <?= esc($selected_period_label) ?></div>
            <div class="vk-card-body">
                <?php
                $monthlyVisitorValues = array_map(static fn($row) => (int) $row->visitors, $monthly_visits ?? []);
                $maxVisitors = max($monthlyVisitorValues ?: [1]);
                ?>
                <?php if (!empty($monthly_visits)): foreach ($monthly_visits as $row): ?>
                    <div class="vk-row"><div class="vk-row-label"><?= esc($row->month_name) ?></div><div class="vk-bar-track"><div class="vk-bar" style="width:<?= ((int) $row->visitors / $maxVisitors) * 100 ?>%"></div></div><div class="vk-row-value"><?= number_format($row->visitors) ?></div></div>
                <?php endforeach; else: ?><div class="vk-empty">Belum ada data kunjungan.</div><?php endif; ?>
            </div>
        </div></div>
        <div class="col-lg-5"><div class="vk-card">
            <div class="vk-card-header"><i class="fas fa-user-friends me-2 text-success"></i>Jenis Kunjungan</div>
            <div class="table-responsive"><table class="table vk-table"><thead><tr><th>Jenis</th><th>Catatan</th><th>Pengunjung</th></tr></thead><tbody>
                <?php if (!empty($visit_types)): foreach ($visit_types as $row): ?><tr><td><?= esc($row->visit_type) ?></td><td><?= number_format($row->visits) ?></td><td><strong><?= number_format($row->visitors) ?></strong></td></tr><?php endforeach; else: ?><tr><td colspan="3" class="vk-empty">Belum ada data.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>