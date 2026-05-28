<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --blue-900: #0f2356;
        --blue-800: #1B3878;
        --blue-500: #2d65d4;
        --blue-200: #bdd0f5;
        --blue-100: #dde8fb;
        --blue-50:  #eef3fd;
        --success:  #1fba74;
        --danger:   #e8394d;
        --warn-bg:  #fffbe6;
        --warn-border: #f0c040;
        --gray-800: #1e2433;
        --gray-500: #6b7489;
        --gray-300: #d1d7e4;
        --gray-100: #f4f6fb;
        --radius-xl: 20px;
        --radius-lg: 14px;
        --radius-md: 10px;
        --shadow-card: 0 4px 24px rgba(27,56,120,0.10), 0 1px 4px rgba(27,56,120,0.06);
        --shadow-btn:  0 4px 14px rgba(27,56,120,0.30);
    }

    body { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; padding-top: 110px; }

    .sp-hero { text-align: center; margin-bottom: 24px; }
    .sp-hero-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #0b9e5f, var(--success));
        border-radius: 50%; box-shadow: 0 8px 28px rgba(31,186,116,0.35);
        margin-bottom: 14px; animation: bounceIn .7s ease-out;
    }
    .sp-hero-icon i { font-size: 32px; color: white; }
    .sp-hero h2 { font-size: 22px; font-weight: 800; color: var(--blue-900); letter-spacing: -.4px; margin-bottom: 4px; }
    .sp-hero p  { color: var(--gray-500); font-size: 14px; margin: 0; }

    @keyframes bounceIn {
        0%   { transform: scale(0.3); opacity: 0; }
        50%  { transform: scale(1.08); }
        75%  { transform: scale(0.95); }
        100% { transform: scale(1); opacity: 1; }
    }

    .sp-inner-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-card); overflow: hidden; margin-bottom: 18px; }
    .sp-inner-header { background: linear-gradient(135deg, var(--blue-900), var(--blue-800)); padding: 16px 24px; display: flex; align-items: center; gap: 10px; }
    .sp-inner-header .h-icon { width: 36px; height: 36px; background: rgba(255,255,255,.15); border-radius: 9px; display: flex; align-items: center; justify-content: center; }
    .sp-inner-header .h-icon i { color: white; font-size: 16px; }
    .sp-inner-header h5 { color: white; font-size: 16px; font-weight: 700; margin: 0; }
    .sp-inner-body { padding: 26px 24px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .info-box { background: var(--gray-100); border-radius: var(--radius-lg); padding: 16px 18px; }
    .info-box h6 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--blue-500); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .info-row p { margin: 0 0 6px; font-size: 13px; color: var(--gray-500); display: flex; gap: 6px; }
    .info-row p:last-child { margin: 0; }
    .info-row p strong { color: var(--gray-800); font-weight: 600; min-width: 118px; flex-shrink: 0; }

    .denda-alert { background: var(--warn-bg); border: 1.5px solid var(--warn-border); border-radius: var(--radius-md); padding: 13px 16px; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600; color: #7a5700; }
    .denda-alert i { font-size: 17px; color: var(--warn-border); flex-shrink: 0; }
    .denda-alert small { font-weight: 400; color: #9a7010; display: block; margin-top: 2px; font-size: 12px; }

    .books-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .books-header h6 { font-size: 15px; font-weight: 700; color: var(--blue-900); margin: 0; }
    .count-badge { background: var(--blue-800); color: white; font-size: 11px; font-weight: 700; padding: 3px 11px; border-radius: 50px; }

    .book-item { background: var(--gray-100); border-radius: var(--radius-md); padding: 13px 15px; margin-bottom: 9px; border-left: 4px solid var(--success); display: flex; align-items: center; gap: 13px; }
    .book-item.overdue { border-left-color: var(--danger); }
    .book-num { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg, var(--blue-800), var(--blue-500)); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: white; flex-shrink: 0; }
    .book-info { flex: 1; }
    .book-title { font-size: 14px; font-weight: 700; color: var(--blue-900); margin-bottom: 4px; }
    .book-meta  { display: flex; flex-wrap: wrap; gap: 12px; }
    .book-meta span { font-size: 12px; color: var(--gray-500); }
    .book-meta span strong { color: var(--gray-800); }
    .late-badge { background: #fef1f2; color: var(--danger); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; white-space: nowrap; flex-shrink: 0; }
    .ok-badge   { background: #f0fdf4; color: var(--success); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; white-space: nowrap; flex-shrink: 0; }

    .receipt-box { border: 2px dashed var(--gray-300); border-radius: var(--radius-lg); padding: 20px 22px; text-align: center; margin-top: 20px; }
    .receipt-label { font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gray-500); margin-bottom: 14px; }
    .receipt-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 12px; }
    .rstat-label { font-size: 11px; color: var(--gray-500); margin-bottom: 3px; }
    .rstat-val   { font-size: 15px; font-weight: 800; color: var(--blue-900); }
    .receipt-note { font-size: 12px; color: var(--gray-500); border-top: 1px solid var(--gray-300); padding-top: 12px; }

    .sp-btn { display: inline-flex; align-items: center; gap: 7px; padding: 12px 24px; border-radius: 50px; font-size: 14px; font-weight: 700; font-family: inherit; cursor: pointer; border: none; transition: all .2s; text-decoration: none; }
    .sp-btn-success { background: linear-gradient(135deg, #0b9e5f, var(--success)); color: white; box-shadow: 0 4px 14px rgba(31,186,116,.35); }
    .sp-btn-success:hover { transform: translateY(-2px); color: white; }
    .sp-btn-primary { background: linear-gradient(135deg, var(--blue-800), var(--blue-500)); color: white; box-shadow: var(--shadow-btn); }
    .sp-btn-primary:hover { transform: translateY(-2px); color: white; }
    .sp-btn-ghost { background: white; color: var(--gray-500); border: 2px solid var(--gray-300); }
    .sp-btn-ghost:hover { border-color: var(--blue-200); color: var(--blue-800); background: var(--blue-50); }

    @media (max-width: 640px) {
        .sp-inner-body { padding: 18px 14px; }
        .info-grid     { grid-template-columns: 1fr; }
        .receipt-stats { grid-template-columns: 1fr; gap: 10px; }
    }

    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .sp-inner-card { box-shadow: none !important; }
        .sidebar-argon, .argon-header, .mobile-menu-toggle, .app-page-title { display: none !important; }
        .app-main__outer { margin-left: 0 !important; padding: 0 !important; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">

    <div class="app-page-title no-print">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon"><i class="pe-7s-check icon-gradient bg-strong-bliss"></i></div>
                <div>Pengembalian Berhasil
                    <div class="page-title-subheading">Transaksi pengembalian telah berhasil diproses</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('sirkulasi-pengembalian') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sirkulasi-pengembalian/create') ?>">Pengembalian</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Berhasil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- Hero -->
            <div class="sp-hero no-print">
                <div class="sp-hero-icon"><i class="fa fa-check"></i></div>
                <h2>Pengembalian Berhasil!</h2>
                <p>Transaksi pengembalian telah berhasil diproses</p>
            </div>

            <!-- Main Card -->
            <div class="sp-inner-card">
                <div class="sp-inner-header">
                    <div class="h-icon"><i class="fa fa-file-text"></i></div>
                    <h5>Struk Pengembalian</h5>
                </div>
                <div class="sp-inner-body">

                    <!-- Info Grid -->
                    <div class="info-grid">
                        <div class="info-box">
                            <h6><i class="fa fa-user"></i> Data Anggota</h6>
                            <div class="info-row">
                                <p><strong>Nomor Anggota</strong> <?= esc($member->MemberNo ?? '-') ?></p>
                                <p><strong>Nama</strong> <?= esc($member->Fullname ?? '-') ?></p>
                                <p><strong>Email</strong> <?= esc($member->Email ?: '-') ?></p>
                                <p><strong>Telepon</strong> <?= esc($member->Phone ?: '-') ?></p>
                            </div>
                        </div>
                        <div class="info-box">
                            <h6><i class="fa fa-rotate-left"></i> Data Pengembalian</h6>
                            <div class="info-row">
                                <p><strong>Tanggal Kembali</strong> <?= date('d/m/Y H:i', strtotime($return_date)) ?></p>
                                <p><strong>Jumlah Buku</strong> <?= count($items) ?> buku</p>
                                <p><strong>Pelanggaran</strong> <?= $violation_count ?> item</p>
                                <?php if ($total_denda > 0): ?>
                                <p><strong>Total Denda</strong> Rp <?= number_format($total_denda, 0, ',', '.') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Denda Alert -->
                    <?php if ($violation_count > 0): ?>
                    <div class="denda-alert">
                        <i class="fa fa-exclamation-triangle"></i>
                        <div>
                            <?= $violation_count ?> buku dikembalikan terlambat
                            <?php if ($total_denda > 0): ?>
                            — Total denda: <strong>Rp <?= number_format($total_denda, 0, ',', '.') ?></strong>
                            <?php endif; ?>
                            <small>Silakan selesaikan pembayaran denda di loket perpustakaan</small>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Books List -->
                    <?php if (!empty($items)): ?>
                    <div class="books-header">
                        <h6><i class="fa fa-list me-2"></i>Daftar Buku yang Dikembalikan</h6>
                        <span class="count-badge"><?= count($items) ?> Buku</span>
                    </div>

                    <?php foreach($items as $index => $item): ?>
                    <?php $isLate = ($item->LateDays ?? 0) > 0; ?>
                    <div class="book-item <?= $isLate ? 'overdue' : '' ?>">
                        <div class="book-num"><?= $index + 1 ?></div>
                        <div class="book-info">
                            <div class="book-title"><?= esc($item->Title) ?></div>
                            <div class="book-meta">
                                <span><strong>Barcode:</strong> <?= esc($item->NomorBarcode) ?></span>
                                <span><strong>No. Panggil:</strong> <?= esc($item->CallNumber ?: '-') ?></span>
                                <span><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($item->DueDate)) ?></span>
                                <?php if ($isLate): ?>
                                <span><strong>Terlambat:</strong> <?= $item->LateDays ?> hari</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($isLate): ?>
                            <div class="late-badge"><i class="fa fa-clock-o me-1"></i><?= $item->LateDays ?> hari terlambat</div>
                        <?php else: ?>
                            <div class="ok-badge"><i class="fa fa-check me-1"></i>Tepat Waktu</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Receipt -->
                    <div class="receipt-box">
                        <div class="receipt-label">Bukti Pengembalian</div>
                        <div class="receipt-stats">
                            <div>
                                <div class="rstat-label">Jumlah Buku</div>
                                <div class="rstat-val"><?= count($items) ?></div>
                            </div>
                            <div>
                                <div class="rstat-label">Tanggal</div>
                                <div class="rstat-val"><?= date('d/m/Y', strtotime($return_date)) ?></div>
                            </div>
                            <div>
                                <div class="rstat-label">Total Denda</div>
                                <div class="rstat-val"><?= $total_denda > 0 ? 'Rp ' . number_format($total_denda, 0, ',', '.') : '-' ?></div>
                            </div>
                        </div>
                        <div class="receipt-note">
                            Simpan struk ini sebagai bukti pengembalian &nbsp;&bull;&nbsp;
                            Dicetak pada: <?= date('d/m/Y H:i:s') ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-4 no-print" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap">
                        <button type="button" class="sp-btn sp-btn-success" onclick="window.print()">
                            <i class="fa fa-print"></i> Cetak Struk
                        </button>
                        <button type="button" class="sp-btn sp-btn-primary" id="btn-send-email"
                                onclick="kirimStrukEmail()">
                            <i class="fa fa-envelope"></i> Kirim ke Email
                        </button>
                        <a href="<?= base_url('sirkulasi-pengembalian/create') ?>" class="sp-btn sp-btn-primary">
                            <i class="fa fa-rotate-left"></i> Pengembalian Baru
                        </a>
                        <a href="<?= base_url() ?>" class="sp-btn sp-btn-ghost">
                            <i class="fa fa-home"></i> Kembali ke Beranda
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    window.addEventListener('beforeprint', function() {
        document.title = 'Struk Pengembalian - <?= esc($member->MemberNo ?? '') ?>';
    });

    var itemIds     = <?= json_encode(array_column($items, 'ID')) ?>;
    var memberId    = <?= (int)($member->ID ?? 0) ?>;
    var memberEmail = '<?= esc($member->Email ?? '') ?>';

    function kirimStrukEmail() {
        if (!memberEmail) {
            Swal.fire('Gagal', 'Email anggota tidak tersedia.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Kirim Struk?',
            html: 'Struk akan dikirim ke <strong>' + memberEmail + '</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            const btn = document.getElementById('btn-send-email');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';
            fetch('<?= base_url('sirkulasi-pengembalian/send-struk') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>',
                },
                body: 'item_ids=' + encodeURIComponent(JSON.stringify(itemIds)) + '&member_id=' + memberId,
            })
            .then(r => r.json())
            .then(data => {
                Swal.fire(data.success ? 'Berhasil' : 'Gagal', data.message, data.success ? 'success' : 'error');
            })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-envelope"></i> Kirim ke Email';
            });
        });
    }
</script>
<?= $this->endSection('script'); ?>
