<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --blue-900: #0f2356;
        --blue-800: #1B3878;
        --blue-700: #1e4494;
        --blue-500: #2d65d4;
        --blue-200: #bdd0f5;
        --blue-100: #dde8fb;
        --blue-50:  #eef3fd;
        --success:  #1fba74;
        --danger:   #e8394d;
        --gray-800: #1e2433;
        --gray-500: #6b7489;
        --gray-300: #d1d7e4;
        --gray-100: #f4f6fb;
        --white:    #ffffff;
        --radius-xl: 20px;
        --radius-lg: 14px;
        --radius-md: 10px;
        --shadow-card: 0 4px 24px rgba(27,56,120,0.10), 0 1px 4px rgba(27,56,120,0.06);
        --shadow-btn:  0 4px 14px rgba(27,56,120,0.30);
    }

    body { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; }

    /* ── Step progress ── */
    .pm-steps {
        display: flex; align-items: center; justify-content: center;
        gap: 0; margin-bottom: 22px;
    }
    .step-dot { display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .step-circle {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px;
        border: 2.5px solid var(--gray-300);
        color: var(--gray-500); background: white; transition: all .3s;
    }
    .step-circle.active {
        background: linear-gradient(135deg, var(--blue-800), var(--blue-500));
        border-color: transparent; color: white;
        box-shadow: 0 4px 14px rgba(27,56,120,.30);
    }
    .step-circle.done { background: var(--success); border-color: transparent; color: white; }
   .step-label { font-size: 11px; font-weight: 600; color: var(--white); white-space: nowrap; }
    .step-label.active { color: var(--blue-800); }
    .step-line {
        flex: 1; height: 2px; background: var(--gray-300);
        margin: 0 6px 20px; max-width: 80px;
    }
    .step-line.done { background: var(--success); }

    /* ── Alerts ── */
    .pm-alert {
        border-radius: var(--radius-md);
        padding: 13px 16px;
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 14px; font-weight: 500;
        margin-bottom: 14px; border: none;
    }
    .pm-alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
    .pm-alert .btn-close { margin-left: auto; background-size: 10px; }
    .pm-alert-danger  { background: #fef1f2; color: var(--danger); }
    .pm-alert-success { background: #edfaf4; color: #0f7a4a; }

    /* ── Location badge ── */
    .pm-location-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--blue-50); border: 1px solid var(--blue-200);
        color: var(--blue-800); border-radius: 50px;
        padding: 4px 14px; font-size: 13px; font-weight: 600;
    }

    /* ── Inner card ── */
    .pm-inner-card {
        background: white; border-radius: var(--radius-xl);
        box-shadow: var(--shadow-card); overflow: hidden; margin-bottom: 18px;
    }
    .pm-inner-header {
        background: linear-gradient(135deg, var(--blue-900), var(--blue-800));
        padding: 16px 24px; display: flex; align-items: center; gap: 10px;
    }
    .pm-inner-header .h-icon {
        width: 36px; height: 36px; background: rgba(255,255,255,.15);
        border-radius: 9px; display: flex; align-items: center; justify-content: center;
    }
    .pm-inner-header .h-icon i { color: white; font-size: 16px; }
    .pm-inner-header h5 { color: white; font-size: 16px; font-weight: 700; margin: 0; }
    .pm-inner-body { padding: 26px 24px; }

    /* ── Scan center ── */
    .scan-center { text-align: center; padding: 8px 0 20px; }
    .scan-circle {
        width: 70px; height: 70px; border-radius: 50%;
        background: linear-gradient(135deg, var(--blue-100), var(--blue-50));
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
    }
    .scan-circle i { font-size: 28px; color: var(--blue-800); }
    .scan-center h6 { font-size: 16px; font-weight: 700; color: var(--blue-900); margin-bottom: 5px; }
    .scan-center p  { color: var(--gray-500); font-size: 14px; }

    /* ── Input ── */
    .pm-input-wrap { position: relative; margin-bottom: 18px; }
    .pm-input-wrap .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--blue-500); font-size: 16px; pointer-events: none;
    }
    .pm-input {
        width: 100%; padding: 14px 18px 14px 46px;
        border: 2px solid var(--gray-300); border-radius: var(--radius-lg);
        font-size: 18px; font-weight: 700; letter-spacing: 2px;
        color: var(--blue-900); background: var(--gray-100);
        font-family: 'Plus Jakarta Sans', monospace;
        transition: all .25s; outline: none;
    }
    .pm-input::placeholder { letter-spacing: 0; font-weight: 400; color: var(--gray-500); font-size: 14px; }
    .pm-input:focus {
        border-color: var(--blue-500); background: white;
        box-shadow: 0 0 0 4px rgba(45,101,212,.12);
    }

    /* ── Buttons ── */
    .pm-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 12px 24px; border-radius: 50px;
        font-size: 14px; font-weight: 700; font-family: inherit;
        cursor: pointer; border: none; transition: all .2s; text-decoration: none;
    }
    .pm-btn-primary {
        background: linear-gradient(135deg, var(--blue-800), var(--blue-500));
        color: white; box-shadow: var(--shadow-btn);
    }
    .pm-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(27,56,120,.35); color: white; }
    .pm-btn-success {
        background: linear-gradient(135deg, #0b9e5f, var(--success));
        color: white; box-shadow: 0 4px 14px rgba(31,186,116,.35);
    }
    .pm-btn-success:hover { transform: translateY(-2px); color: white; }
    .pm-btn-ghost {
        background: var(--blue-50); color: var(--blue-800);
        border: 2px solid var(--blue-200);
    }
    .pm-btn-ghost:hover { background: var(--blue-100); color: var(--blue-800); }
    .pm-btn-danger-sm {
        background: transparent; color: var(--danger);
        border: 1.5px solid rgba(232,57,77,.35);
        padding: 6px 12px; font-size: 12px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 5px;
        text-decoration: none; font-weight: 600;
    }
    .pm-btn-danger-sm:hover { background: #fef1f2; color: var(--danger); }

    /* ── Member info ── */
    .member-info {
        background: linear-gradient(135deg, var(--blue-50), white);
        border: 1.5px solid var(--blue-200); border-radius: var(--radius-lg);
        padding: 18px 20px; margin-bottom: 22px;
        display: flex; align-items: center; gap: 16px;
    }
    .member-avatar {
        width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg, var(--blue-800), var(--blue-500));
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(27,56,120,.25);
    }
    .member-avatar i { color: white; font-size: 22px; }
    .member-details { flex: 1; }
    .member-valid-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #edfaf4; color: #0a6e43;
        border-radius: 50px; padding: 2px 10px;
        font-size: 11px; font-weight: 700; margin-bottom: 6px;
    }
    .member-name { font-size: 16px; font-weight: 800; color: var(--blue-900); }
    .member-no   { color: var(--gray-500); font-size: 12px; margin-top: 2px; }
    .member-stats { display: flex; gap: 18px; margin-top: 10px; flex-wrap: wrap; }
    .mstat-label { font-size: 10px; color: var(--gray-500); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .mstat-val   { font-size: 17px; font-weight: 800; color: var(--blue-900); }
    .mstat-val.ok  { color: var(--success); }
    .mstat-val.bad { color: var(--danger); }

    /* ── Books list ── */
    .books-header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
    }
    .books-header h6 { font-size: 15px; font-weight: 700; color: var(--blue-900); margin: 0; }
    .count-badge {
        background: var(--blue-800); color: white;
        font-size: 11px; font-weight: 700; padding: 3px 11px; border-radius: 50px;
    }
    .book-item {
        background: var(--gray-100); border-radius: var(--radius-md);
        padding: 14px 16px; margin-bottom: 9px;
        border-left: 4px solid var(--blue-500);
        display: flex; align-items: center; gap: 14px;
    }
    .book-num {
        width: 30px; height: 30px; border-radius: 8px;
        background: linear-gradient(135deg, var(--blue-800), var(--blue-500));
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: white; flex-shrink: 0;
    }
    .book-info { flex: 1; }
    .book-title { font-size: 14px; font-weight: 700; color: var(--blue-900); margin-bottom: 4px; }
    .book-meta  { display: flex; flex-wrap: wrap; gap: 12px; }
    .book-meta span { font-size: 12px; color: var(--gray-500); }
    .book-meta span strong { color: var(--gray-800); }

    /* ── Divider ── */
    .pm-divider { border: none; border-top: 2px dashed var(--gray-300); margin: 22px 0; }

    /* ── Confirm bar ── */
    .confirm-bar {
        background: linear-gradient(135deg, var(--blue-50), #e8f0fe);
        border: 1.5px solid var(--blue-200); border-radius: var(--radius-lg);
        padding: 18px 22px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px; flex-wrap: wrap;
    }
    .confirm-bar h6 { font-size: 14px; font-weight: 700; color: var(--blue-900); margin: 0 0 3px; }
    .confirm-bar p  { font-size: 12px; color: var(--gray-500); margin: 0; }

    /* ── Hint ── */
    .pm-hint {
        display: flex; align-items: center; gap: 6px;
        color: var(--gray-500); font-size: 12px;
        margin-top: 12px; justify-content: center;
    }

    /* ── How-to cards ── */
    .howto-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 16px; }
    .howto-card {
        background: var(--gray-100); border-radius: var(--radius-lg);
        padding: 18px 14px; text-align: center;
    }
    .hc-num { font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: var(--blue-500); margin-bottom: 8px; }
    .hc-icon {
        width: 46px; height: 46px; margin: 0 auto 10px;
        border-radius: 12px; background: white;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(27,56,120,.10);
    }
    .hc-icon i { font-size: 19px; color: var(--blue-800); }
    .howto-card h6 { font-size: 13px; font-weight: 700; color: var(--blue-900); margin-bottom: 4px; }
    .howto-card p  { font-size: 11px; color: var(--gray-500); line-height: 1.5; margin: 0; }

    .tips-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .tips-box  { background: var(--gray-100); border-radius: var(--radius-lg); padding: 16px 18px; }
    .tips-box h6 {
        font-size: 13px; font-weight: 700; color: var(--blue-900);
        margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
    }
    .tips-box ul { list-style: none; margin: 0; padding: 0; }
    .tips-box ul li {
        font-size: 12px; color: var(--gray-500); padding: 5px 0;
        border-bottom: 1px solid var(--gray-300);
        display: flex; align-items: flex-start; gap: 7px;
    }
    .tips-box ul li:last-child { border-bottom: none; }
    .tips-box ul li i { flex-shrink: 0; margin-top: 2px; font-size: 10px; }
    .tips-ok   { color: var(--success) !important; }
    .tips-warn { color: #e09800 !important; }

    @media (max-width: 640px) {
        .pm-inner-body { padding: 18px 14px; }
        .howto-grid    { grid-template-columns: 1fr; }
        .tips-grid     { grid-template-columns: 1fr; }
        .member-info   { flex-direction: column; align-items: flex-start; }
        .confirm-bar   { flex-direction: column; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">

    <!-- Page Title -->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-bookmarks icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Tambahkan Peminjaman 
                    <div class="page-title-subheading">Sistem Peminjaman Buku Otomatis</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('sirkulasi-peminjaman') ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Tambahkan Peminjaman </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Location Badge -->
    <?php if(isset($locationData) && $locationData): ?>
    <div class="mb-3">
        <span class="pm-location-badge">
            <i class="fa fa-map-marker"></i>
            <?= esc($locationData->Name) ?> &mdash; <?= esc($locationData->Branch_name) ?>
        </span>
    </div>
    <?php endif; ?>

    <!-- Alerts -->
    <?php if(session()->getFlashdata('error') || !empty($errorMessage)): ?>
    <div class="pm-alert pm-alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle"></i>
        <span><?= session()->getFlashdata('error') ?? esc($errorMessage) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if(!empty($successMessage)): ?>
    <div class="pm-alert pm-alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle"></i>
        <span><?= esc($successMessage) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Step Indicator -->
    <?php
    $step = 1;
    if ($memberData && empty($selectedBooks))  $step = 2;
    if ($memberData && !empty($selectedBooks)) $step = 3;
    ?>
    <div class="pm-steps">
        <div class="step-dot">
            <div class="step-circle <?= $step > 1 ? 'done' : ($step === 1 ? 'active' : '') ?>">
                <?= $step > 1 ? '<i class="fa fa-check" style="font-size:12px"></i>' : '1' ?>
            </div>
            <div class="step-label <?= $step === 1 ? 'active' : '' ?>">Anggota</div>
        </div>
        <div class="step-line <?= $step > 1 ? 'done' : '' ?>"></div>
        <div class="step-dot">
            <div class="step-circle <?= $step > 2 ? 'done' : ($step === 2 ? 'active' : '') ?>">
                <?= $step > 2 ? '<i class="fa fa-check" style="font-size:12px"></i>' : '2' ?>
            </div>
            <div class="step-label <?= $step === 2 ? 'active' : '' ?>">Scan Buku</div>
        </div>
        <div class="step-line <?= $step > 2 ? 'done' : '' ?>"></div>
        <div class="step-dot">
            <div class="step-circle <?= $step === 3 ? 'active' : '' ?>">3</div>
            <div class="step-label <?= $step === 3 ? 'active' : '' ?>">Konfirmasi</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- Main Card -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon">
                        <i class="fa <?= !$memberData ? 'fa-user-check' : 'fa-barcode' ?>"></i>
                    </div>
                    <h5>
                        <?php if(!$memberData): ?>
                            Validasi Anggota
                        <?php elseif(!empty($selectedBooks)): ?>
                            Konfirmasi Peminjaman
                        <?php else: ?>
                            Scan Barcode Buku
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="pm-inner-body">

                    <?php if(!$memberData): ?>
                    <!-- STEP 1 -->
                    <div class="scan-center">
                        <div class="scan-circle"><i class="fa fa-id-card"></i></div>
                        <h6>Masukkan Nomor Anggota</h6>
                        <p>Scan kartu atau ketik nomor anggota perpustakaan Anda</p>
                    </div>
                    <form action="<?= base_url('sirkulasi-peminjaman/create') ?>" method="GET">
                        <div class="pm-input-wrap">
                            <i class="fa fa-id-card input-icon"></i>
                            <input type="text" class="pm-input" name="MemberNo"
                                   placeholder="Nomor Anggota"
                                   value="<?= esc($memberNo ?? '') ?>"
                                   autofocus autocomplete="off" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="pm-btn pm-btn-primary">
                                <i class="fa fa-search"></i> Validasi Anggota
                            </button>
                        </div>
                    </form>
                    <div class="pm-hint">
                        <i class="fa fa-info-circle"></i>
                        Gunakan scanner barcode atau ketik manual nomor anggota
                    </div>

                    <?php else: ?>
                    <!-- STEP 2 & 3 -->

                    <!-- Member Info -->
                    <div class="member-info">
                        <div class="member-avatar"><i class="fa fa-user"></i></div>
                        <div class="member-details">
                            <div class="member-valid-badge">
                                <i class="fa fa-check-circle"></i> Anggota Terverifikasi
                            </div>
                            <div class="member-name"><?= esc($memberData['fullname']) ?></div>
                            <div class="member-no"><?= esc($memberData['member_no']) ?></div>
                            <div class="member-stats">
                                <div>
                                    <div class="mstat-label">Sedang Dipinjam</div>
                                    <div class="mstat-val"><?= $memberData['current_loans'] ?> buku</div>
                                </div>
                                <div>
                                    <div class="mstat-label">Sisa Kuota</div>
                                    <div class="mstat-val <?= $memberData['remaining_loans'] > 0 ? 'ok' : 'bad' ?>">
                                        <?= $memberData['remaining_loans'] ?> buku
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($memberData['remaining_loans'] > 0): ?>
                    <!-- Scan Buku -->
                    <div class="scan-center" style="padding-top:0">
                        <div class="scan-circle"><i class="fa fa-barcode"></i></div>
                        <h6>Scan Barcode Buku</h6>
                        <p>Arahkan scanner ke barcode buku yang ingin dipinjam</p>
                    </div>
                    <form action="<?= base_url('sirkulasi-peminjaman/create') ?>" method="GET">
                        <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                        <div class="pm-input-wrap">
                            <i class="fa fa-barcode input-icon"></i>
                            <input type="text" class="pm-input" name="NomorBarcode"
                                   placeholder="Barcode Buku"
                                   autocomplete="off" autofocus required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="pm-btn pm-btn-primary">
                                <i class="fa fa-plus"></i> Tambah Buku
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <!-- Books List -->
                    <?php if(!empty($selectedBooks)): ?>
                    <hr class="pm-divider">
                    <div class="books-header">
                        <h6>Daftar Buku yang Akan Dipinjam</h6>
                        <span class="count-badge"><?= count($selectedBooks) ?> Buku</span>
                    </div>

                    <?php foreach($selectedBooks as $index => $book): ?>
                    <div class="book-item">
                        <div class="book-num"><?= $index + 1 ?></div>
                        <div class="book-info">
                            <div class="book-title"><?= esc($book['title']) ?></div>
                            <div class="book-meta">
                                <span><strong>Pengarang:</strong> <?= esc($book['author']) ?></span>
                                <span><strong>Barcode:</strong> <?= esc($book['barcode']) ?></span>
                                <span><strong>No. Panggil:</strong> <?= esc($book['call_number']) ?></span>
                                <span><strong>Tahun:</strong> <?= esc($book['publish_year']) ?></span>
                            </div>
                        </div>
                        <a href="<?= base_url('sirkulasi-peminjaman/remove-book?MemberNo=' . urlencode($memberData['member_no']) . '&index=' . $index) ?>"
                           class="pm-btn-danger-sm"
                           onclick="return confirm('Hapus buku dari daftar?')">
                            <i class="fa fa-trash"></i> Hapus
                        </a>
                    </div>
                    <?php endforeach; ?>

                    <div class="confirm-bar mt-3">
                        <div>
                            <h6>Siap memproses peminjaman</h6>
                            <p><?= count($selectedBooks) ?> buku atas nama <?= esc($memberData['fullname']) ?></p>
                        </div>
                        <form action="<?= base_url('sirkulasi-peminjaman/process-loan') ?>" method="POST"
                              onsubmit="return confirm('Yakin ingin memproses peminjaman ini?')">
                            <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                            <button type="submit" class="pm-btn pm-btn-success">
                                <i class="fa fa-check"></i> Proses Peminjaman
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <a href="<?= base_url('sirkulasi-peminjaman') ?>" class="pm-btn pm-btn-ghost"
                           style="font-size:13px;padding:9px 20px">
                            <i class="fa fa-user-plus"></i> Ganti Anggota
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Petunjuk Penggunaan — pakai card INLISLite asli -->
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <i class="header-icon lnr-question-circle icon-gradient bg-plum-plate"></i>
                    Petunjuk Penggunaan
                </div>
                <div class="card-body">
                    <div class="howto-grid">
                        <div class="howto-card">
                            <div class="hc-num">Langkah 1</div>
                            <div class="hc-icon"><i class="fa fa-id-card"></i></div>
                            <h6>Validasi Anggota</h6>
                            <p>Scan atau ketik nomor anggota perpustakaan Anda</p>
                        </div>
                        <div class="howto-card">
                            <div class="hc-num">Langkah 2</div>
                            <div class="hc-icon"><i class="fa fa-barcode"></i></div>
                            <h6>Scan Barcode Buku</h6>
                            <p>Scan barcode pada setiap buku yang ingin dipinjam</p>
                        </div>
                        <div class="howto-card">
                            <div class="hc-num">Langkah 3</div>
                            <div class="hc-icon"><i class="fa fa-check-circle"></i></div>
                            <h6>Konfirmasi</h6>
                            <p>Konfirmasi peminjaman dan cetak struk sebagai bukti</p>
                        </div>
                    </div>
                    <div class="tips-grid">
                        <div class="tips-box">
                            <h6><i class="fa fa-lightbulb-o" style="color:var(--blue-500)"></i> Tips Penggunaan</h6>
                            <ul>
                                <li><i class="fa fa-circle tips-ok"></i> Pastikan barcode dalam kondisi baik dan tidak rusak</li>
                                <li><i class="fa fa-circle tips-ok"></i> Pegang scanner tegak lurus dengan barcode</li>
                                <li><i class="fa fa-circle tips-ok"></i> Tunggu bunyi "beep" setelah scan berhasil</li>
                            </ul>
                        </div>
                        <div class="tips-box">
                            <h6><i class="fa fa-exclamation-triangle" style="color:#e09800"></i> Perhatian</h6>
                            <ul>
                                <li><i class="fa fa-circle tips-warn"></i> Simpan struk peminjaman dengan baik</li>
                                <li><i class="fa fa-circle tips-warn"></i> Kembalikan buku sebelum tanggal jatuh tempo</li>
                                <li><i class="fa fa-circle tips-warn"></i> Hubungi petugas jika ada kendala</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const af = document.querySelector('input[autofocus]');
    if (af) { af.focus(); af.select(); }
});

document.addEventListener('input', function(e) {
    if (e.target.name === 'NomorBarcode' || e.target.name === 'MemberNo') {
        e.target.value = e.target.value.toUpperCase().replace(/\s/g, '');
    }
});

document.addEventListener('submit', function(e) {
    const btn = e.target.querySelector('button[type="submit"]');
    if (btn) {
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 5000);
    }
});

<?php if(!empty($successMessage) && $memberData): ?>
setTimeout(function() {
    const bc = document.querySelector('input[name="NomorBarcode"]');
    if (bc) bc.focus();
}, 100);
<?php endif; ?>
</script>
<?= $this->endSection('script'); ?>