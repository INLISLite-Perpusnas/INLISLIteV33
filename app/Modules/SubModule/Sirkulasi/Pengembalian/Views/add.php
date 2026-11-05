<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>

<style>
    /* Warna dan ukuran mengikuti token INA Digital dari layout utama */
    :root {
        --blue-900: var(--ina-text);
        --blue-800: var(--ina-primary);
        --blue-500: var(--ina-primary);
        --blue-200: var(--ina-border);
        --blue-100: var(--ina-primary-subtle);
        --blue-50: var(--ina-primary-subtle);
        --success: var(--ina-success);
        --danger: var(--ina-danger);
        --warn-bg: #fff4e8;
        --warn-col: #9a4a06;
        --gray-800: var(--ina-text);
        --gray-500: var(--ina-text-muted);
        --gray-300: #d1d5db;
        --gray-100: var(--ina-page);
        --white: var(--ina-surface);
        --radius-xl: var(--ina-radius);
        --radius-lg: 8px;
        --radius-md: 8px;
        --shadow-card: var(--ina-shadow);
    }

    /* ── Inner card ── */
    .pm-inner-card {
        background: var(--white);
        border: 1px solid var(--ina-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: 18px;
    }

    .pm-inner-header {
        background: var(--white);
        border-bottom: 1px solid var(--ina-border);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pm-inner-header .h-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        background: var(--ina-primary-subtle);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pm-inner-header .h-icon i {
        color: var(--ina-primary);
        font-size: 16px;
    }

    .pm-inner-header h5 {
        color: var(--ina-text);
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .pm-inner-body {
        padding: 24px;
    }

    /* ── Scan center ── */
    .scan-center {
        text-align: center;
        padding: 4px 0 20px;
    }

    .scan-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--ina-primary-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
    }

    .scan-circle i {
        font-size: 26px;
        color: var(--ina-primary);
    }

    .scan-center h6 {
        font-size: 16px;
        font-weight: 600;
        color: var(--ina-text);
        margin-bottom: 4px;
    }

    .scan-center p {
        color: var(--gray-500);
        font-size: 14px;
        margin-bottom: 0;
    }

    /* ── Input ── */
    .pm-field {
        margin-bottom: 18px;
    }

    .pm-field-label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--ina-text);
    }

    .pm-field-hint {
        display: block;
        margin-top: 6px;
        font-size: 13px;
        color: var(--gray-500);
    }

    .pm-input-wrap {
        position: relative;
    }

    .pm-input-wrap .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ina-icon);
        font-size: 16px;
        pointer-events: none;
    }

    .pm-input {
        width: 100%;
        height: 44px;
        padding: 0 14px 0 42px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-lg);
        font-family: inherit;
        font-size: 15px;
        font-weight: 400;
        color: var(--ina-text);
        background: var(--white);
        transition: border-color .15s ease, box-shadow .15s ease;
        outline: none;
    }

    .pm-input::placeholder {
        color: #9ca3af;
    }

    .pm-input:hover {
        border-color: #9ca3af;
    }

    .pm-input:focus {
        border-color: var(--ina-primary);
        box-shadow: 0 0 0 3px rgba(29, 95, 209, .15);
    }

    .pm-input-plain {
        padding-left: 14px;
    }

    #modalPelanggaran .form-select {
        display: block;
        width: 100%;
        height: 44px;
        padding: 0 14px;
        font-family: inherit;
        font-size: 15px;
        color: var(--ina-text);
        background-color: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-lg);
        outline: none;
    }

    #modalPelanggaran .form-select:focus {
        border-color: var(--ina-primary);
        box-shadow: 0 0 0 3px rgba(29, 95, 209, .15);
    }

    /* ── Buttons ── */
    .pm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        text-decoration: none;
    }

    .pm-btn:focus-visible,
    .pm-btn-outline-sm:focus-visible {
        outline: 2px solid var(--ina-primary);
        outline-offset: 2px;
    }

    .pm-btn-primary {
        background: var(--ina-primary);
        color: #fff;
    }

    .pm-btn-primary:hover {
        background: var(--ina-primary-hover);
        color: #fff;
    }

    .pm-btn-success {
        background: var(--ina-primary);
        color: #fff;
    }

    .pm-btn-success:hover {
        background: var(--ina-primary-hover);
        color: #fff;
    }

    .pm-btn-danger {
        background: var(--ina-danger);
        color: #fff;
    }

    .pm-btn-danger:hover {
        background: #a32020;
        color: #fff;
    }

    .pm-btn-ghost {
        background: var(--white);
        color: var(--ina-primary);
        border-color: var(--ina-primary);
    }

    .pm-btn-ghost:hover {
        background: var(--ina-primary-subtle);
        color: var(--ina-primary);
    }

    .pm-btn-outline-sm {
        background: var(--white);
        color: var(--ina-primary);
        border: 1px solid var(--ina-primary);
        padding: 7px 14px;
        font-size: 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .pm-btn-outline-sm:hover {
        background: var(--ina-primary-subtle);
        color: var(--ina-primary);
    }

    /* ── Messages ── */
    .pm-alert {
        border-radius: var(--radius-md);
        padding: 12px 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 14px;
        border: 1px solid transparent;
    }

    .pm-alert i {
        font-size: 15px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .pm-alert-danger {
        background: var(--ina-danger-subtle);
        color: var(--ina-danger);
        border-color: #f5c2c2;
    }

    .pm-alert-success {
        background: #e9f6ec;
        color: var(--ina-success);
        border-color: #bfe3c8;
    }

    /* ── Book info box ── */
    .book-info-box {
        background: var(--white);
        border: 1px solid var(--ina-border);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-top: 16px;
        margin-bottom: 16px;
    }

    .book-info-box h6 {
        font-size: 15px;
        font-weight: 600;
        color: var(--ina-text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .book-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 8px 0;
        border-bottom: 1px solid var(--ina-border);
        font-size: 14px;
    }

    .book-detail:last-child {
        border-bottom: none;
    }

    .book-detail .bd-label {
        color: var(--gray-500);
        font-weight: 500;
    }

    .book-detail .bd-val {
        color: var(--gray-800);
        font-weight: 600;
        text-align: right;
    }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-available {
        background: #e9f6ec;
        color: var(--ina-success);
    }

    .status-loaned {
        background: var(--warn-bg);
        color: var(--warn-col);
    }

    .status-overdue {
        background: var(--ina-danger-subtle);
        color: var(--ina-danger);
    }

    /* ── Loading ── */
    .pm-loading {
        text-align: center;
        padding: 24px;
    }

    .pm-loading i {
        font-size: 28px;
        color: var(--ina-primary);
        animation: spin 1s linear infinite;
    }

    .pm-loading p {
        color: var(--gray-500);
        font-size: 14px;
        margin-top: 10px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* ── Divider ── */
    .pm-divider {
        border: none;
        border-top: 1px solid var(--ina-border);
        margin: 22px 0;
    }

    /* ── History items ── */
    .history-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }

    .history-header h6 {
        font-size: 15px;
        font-weight: 600;
        color: var(--ina-text);
        margin: 0;
    }

    .history-item {
        background: var(--white);
        border: 1px solid var(--ina-border);
        border-radius: var(--radius-md);
        padding: 14px 16px;
        margin-bottom: 8px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .history-item .hi-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--ina-text);
        margin-bottom: 3px;
    }

    .history-item .hi-author {
        font-size: 13px;
        color: var(--gray-500);
        margin-bottom: 3px;
    }

    .history-item .hi-barcode {
        font-size: 13px;
        color: var(--gray-500);
    }

    .history-item .hi-date {
        font-size: 13px;
        color: var(--gray-500);
        white-space: nowrap;
        text-align: right;
    }

    .history-empty {
        text-align: center;
        padding: 28px;
        color: var(--gray-500);
        font-size: 14px;
    }

    /* ── Tambahan untuk Divider Antar Buku ── */
    .pm-divider-top {
        border-top: 1px solid var(--ina-border);
        margin-top: 15px;
        padding-top: 15px;
    }

    .history-empty i {
        font-size: 28px;
        margin-bottom: 10px;
        display: block;
    }

    /* ── Modal pelanggaran ── */
    #modalPelanggaran .modal-dialog {
        box-shadow: none;
    }

    #modalPelanggaran .modal-content {
        border: 1px solid var(--ina-border);
        border-radius: var(--radius-xl);
        overflow: hidden;
    }

    #modalPelanggaran .modal-header {
        align-items: center;
        padding: 16px 24px;
        background: var(--white);
        border-bottom: 1px solid var(--ina-border);
    }

    #modalPelanggaran .modal-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--ina-text);
        margin: 0;
    }

    #modalPelanggaran .pm-modal-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        color: var(--ina-danger);
        background: var(--ina-danger-subtle);
        border-radius: 8px;
    }

    #modalPelanggaran .modal-footer {
        padding: 16px 24px;
        gap: 10px;
        border-top: 1px solid var(--ina-border);
    }

    /* ── fade-in ── */
    .fade-in {
        animation: fadeIn .3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .fade-in {
            animation: none;
        }
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">

    <!-- Page Title -->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Tambah Pengembalian
                    <div class="page-title-subheading">Sistem Pengembalian Buku </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('sirkulasi-pengembalian/create') ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Pengembalian Mandiri</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- Main Card: Scanner -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon"><i class="fa fa-undo"></i></div>
                    <h5>Scan Barcode Buku</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="scan-center">
                        <div class="scan-circle"><i class="fa fa-barcode"></i></div>
                        <h6>Scan atau Masukkan Barcode Buku</h6>
                        <p>Arahkan scanner ke barcode buku yang ingin dikembalikan</p>
                    </div>

                    <div class="pm-field">
                        <label class="pm-field-label" for="barcodeInput">Barcode Buku</label>
                        <div class="pm-input-wrap">
                        <i class="fa fa-barcode input-icon"></i>
                        <input type="text" id="barcodeInput" class="pm-input"
                            placeholder="Masukkan atau scan barcode buku..."
                            autocomplete="off" autofocus
                            style="padding-right:45px;">
                        <i class="fa fa-paste"
                            id="btn-paste-barcode"
                            title="Paste dari clipboard"
                            style="
                                            position:absolute;
                                            right:15px;
                                            top:50%;
                                            transform:translateY(-50%);
                                            cursor:pointer;
                                            opacity:.5;
                                            transition:all .15s ease;
                                        "
                            onmouseover="this.style.opacity='1';this.style.transform='translateY(-50%) scale(1.1)'"
                            onmouseout="this.style.opacity='.5';this.style.transform='translateY(-50%) scale(1)'">
                        </i>
                        </div>
                    </div>

                    <div class="pm-field" style="margin-bottom: 22px;">
                        <label class="pm-field-label" for="returnDateInput">Tanggal Pengembalian</label>
                        <div class="pm-input-wrap">
                            <i class="fa fa-calendar input-icon"></i>
                            <input type="date" id="returnDateInput" class="pm-input"
                                value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" title="Tanggal Pengembalian">
                        </div>
                        <small class="pm-field-hint">Hanya bisa hari ini atau tanggal mundur (backdate), tidak bisa tanggal yang akan datang.</small>
                    </div>

                    <div class="text-center" style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap">
                        <button type="button" id="checkBookBtn" class="pm-btn pm-btn-ghost">
                            <i class="fa fa-search"></i> Cek Buku
                        </button>
                        <button type="button" id="returnBookBtn" class="pm-btn pm-btn-success">
                            <i class="fa fa-undo"></i> Kembalikan Buku
                        </button>
                    </div>

                    <!-- Loading -->
                    <div id="loadingSection" class="pm-loading d-none">
                        <i class="fa fa-spinner"></i>
                        <p>Memproses...</p>
                    </div>

                    <!-- Message -->
                    <div id="messageSection"></div>

                    <!-- Book Info -->
                    <div id="bookInfoSection" class="d-none">
                        <div class="book-info-box fade-in">
                            <h6><i class="fa fa-book"></i> Informasi Buku</h6>
                            <div id="bookDetails"></div>
                        </div>
                    </div>



                </div>
            </div>

            <!-- History Card -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon"><i class="fa fa-history"></i></div>
                    <h5>Riwayat Pengembalian Terbaru</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="history-header">
                        <h6>5 Pengembalian Terakhir</h6>
                        <button type="button" id="refreshHistoryBtn" class="pm-btn-outline-sm">
                            <i class="fa fa-sync"></i> Refresh
                        </button>
                    </div>

                    <div id="historyList">
                        <div class="history-empty">
                            <i class="fa fa-clock"></i>
                            Klik Refresh untuk melihat riwayat pengembalian
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!-- Modal Pelanggaran -->
<div class="modal fade" id="modalPelanggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="pm-modal-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>
                    <h5 class="modal-title">Proses Pelanggaran Keterlambatan</h5>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body" style="padding: 24px;">

                <!-- Info terlambat -->
                <div class="pm-alert pm-alert-danger mb-3" id="overdueInfoBox">
                    <i class="fa fa-clock"></i>
                    <span id="overdueInfoText">Terdapat buku yang melewati tanggal jatuh tempo.</span>
                </div>

                <!-- Daftar buku terlambat -->
                <div id="overdueBookList" style="margin-bottom: 18px;"></div>

                <hr class="pm-divider" style="margin: 16px 0;">

                <!-- Form Pelanggaran -->
                <div id="violationFormSection">
                    <p style="font-size:14px;color:var(--gray-500);margin-bottom:14px;">
                        Apakah ingin mencatat pelanggaran untuk buku yang terlambat?
                    </p>

                    <div class="mb-3">
                        <label class="pm-field-label">
                            <i class="fa fa-list-ul"></i> Jenis Pelanggaran
                        </label>
                        <select id="jenisPelanggaranSelect" class="form-select">
                            <option value="">-- Pilih Jenis Pelanggaran --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="pm-field-label">
                            <i class="fa fa-tag"></i> Jenis Denda
                        </label>
                        <select id="jenisDendaSelect" class="form-select">
                            <option value="">-- Pilih Jenis Denda --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="pm-field-label">
                            <i class="fa fa-money-bill"></i> Jumlah Denda
                        </label>
                        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                            <input type="number" id="jumlahDendaInput" class="pm-input pm-input-plain" min="0" step="500" placeholder="Contoh: 1000"
                                style="flex:1;min-width:160px;">
                            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gray-500);">
                                <input type="checkbox" id="perHariCheck" style="width:16px;height:16px;accent-color:var(--blue-500);" checked>
                                <label for="perHariCheck" style="cursor:pointer;font-weight:600;">Per hari</label>
                            </div>
                        </div>
                        <small id="dendaPreview" style="color:var(--blue-800);font-size:12px;margin-top:6px;display:block;"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="skipViolationBtn" class="pm-btn pm-btn-ghost" style="flex:1;">
                    <i class="fa fa-forward"></i> Lewati Pelanggaran
                </button>
                <button type="button" id="confirmViolationBtn" class="pm-btn pm-btn-danger" style="flex:1;">
                    <i class="fa fa-gavel"></i> Proses + Catat Pelanggaran
                </button>
            </div>

        </div>
    </div>
    <script>
        class SelfReturnApp {
            constructor() {
                this.currentMemberId = null;
                this.currentMemberName = null;
                this.booksToReturn = [];
                this.jenisPelanggaran = [];
                this.jenisDenda = [];
                window.selfReturnApp = this;
                this.init();
            }

            init() {
                this.bindEvents();
                this.focusInput();
                this.setupAutoScan();
                this.loadMasterData(); // Muat dropdown sekali saja
            }

            bindEvents() {
                document.getElementById('checkBookBtn').addEventListener('click', () => this.checkBook());
                document.getElementById('returnBookBtn').addEventListener('click', () => this.returnBook());
                document.getElementById('refreshHistoryBtn').addEventListener('click', () => this.loadHistory());
                document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.checkBook();
                });
                document.getElementById('btn-paste-barcode').addEventListener('click', async () => {
                    try {
                        const text = await navigator.clipboard.readText();
                        const barcode = text.trim();

                        if (barcode) {
                            document.getElementById('barcodeInput').value = barcode;
                            this.checkBook();
                        }
                    } catch (e) {
                        console.error('Gagal membaca clipboard:', e);
                    }
                });

                // Tombol di dalam modal
                document.getElementById('skipViolationBtn').addEventListener('click', () => {
                    bootstrap.Modal.getInstance(document.getElementById('modalPelanggaran')).hide();
                    this.submitReturn(false, null);
                });
                document.getElementById('confirmViolationBtn').addEventListener('click', () => {
                    this.handleConfirmViolation();
                });

                // Live preview kalkulasi denda
                document.getElementById('jumlahDendaInput').addEventListener('input', () => this.updateDendaPreview());
                document.getElementById('perHariCheck').addEventListener('change', () => this.updateDendaPreview());
            }

            setupAutoScan() {
                let scanTimeout;
                const input = document.getElementById('barcodeInput');
                input.addEventListener('input', () => {
                    clearTimeout(scanTimeout);
                    scanTimeout = setTimeout(() => {
                        if (input.value.length >= 8) this.checkBook();
                    }, 500);
                });
            }

            async loadMasterData() {
                try {
                    const [resP, resD] = await Promise.all([
                        fetch('/sirkulasi-pengembalian/get-jenis-pelanggaran').then(r => r.json()),
                        fetch('/sirkulasi-pengembalian/get-jenis-denda').then(r => r.json())
                    ]);
                    if (resP.status === 'success') {
                        this.jenisPelanggaran = resP.data;
                        const sel = document.getElementById('jenisPelanggaranSelect');
                        resP.data.forEach(p => {
                            sel.innerHTML += `<option value="${p.ID}">${p.JenisPelanggaran}</option>`;
                        });
                    }
                    if (resD.status === 'success') {
                        this.jenisDenda = resD.data;
                        const sel = document.getElementById('jenisDendaSelect');
                        resD.data.forEach(d => {
                            sel.innerHTML += `<option value="${d.ID}">${d.Name}</option>`;
                        });
                    }
                } catch (e) {
                    console.error('Gagal memuat master data:', e);
                }
            }

            focusInput() {
                document.getElementById('barcodeInput').focus();
            }
            showLoading() {
                document.getElementById('loadingSection').classList.remove('d-none');
                this.hideMessage();
                this.hideBookInfo();
            }
            hideLoading() {
                document.getElementById('loadingSection').classList.add('d-none');
            }
            hideBookInfo() {
                document.getElementById('bookInfoSection').classList.add('d-none');
            }
            hideMessage() {
                document.getElementById('messageSection').innerHTML = '';
            }

            showMessage(message, type = 'success') {
                const sec = document.getElementById('messageSection');
                const cls = type === 'success' ? 'pm-alert-success' : 'pm-alert-danger';
                const icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
                sec.innerHTML = `<div class="pm-alert ${cls} fade-in mt-3"><i class="${icon}"></i><span>${message}</span></div>`;
                if (type === 'success') setTimeout(() => {
                    sec.innerHTML = '';
                }, 5000);
            }

            renderBookList() {
                const sec = document.getElementById('bookInfoSection');
                const details = document.getElementById('bookDetails');
                const titleSec = sec.querySelector('h6');

                if (this.booksToReturn.length === 0) {
                    this.hideBookInfo();
                    return;
                }

                titleSec.innerHTML = `
            <div style="display:flex;flex-direction:column;gap:5px;">
                <span style="color:var(--blue-900);font-size:16px;"><i class="fa fa-user-circle"></i> ${this.currentMemberName || '-'}</span>
                <small style="color:var(--gray-500);font-size:13px;font-weight:normal;">
                    <i class="fa fa-book"></i> ${this.booksToReturn.length} buku siap dikembalikan
                </small>
            </div>`;

                let itemsHtml = '';
                this.booksToReturn.forEach((book, index) => {
                    const statusBadge = book.is_overdue ?
                        `<span class="status-badge status-overdue"><i class="fa fa-exclamation-circle"></i> Terlambat ${book.days_overdue} hari</span>` :
                        `<span class="status-badge status-available"><i class="fa fa-clock"></i> Sedang Dipinjam</span>`;

                    itemsHtml += `
                <div class="${index > 0 ? 'pm-divider-top' : ''} position-relative fade-in" style="padding-right:45px;">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute"
                            style="right:0;top:10px;border-radius:8px;width:35px;height:35px;"
                            onclick="selfReturnApp.removeBookItem('${book.id}')">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="book-detail">
                        <span class="bd-label">Judul</span>
                        <span class="bd-val" style="text-align:right;max-width:75%;">${book.title || '-'}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Barcode</span>
                        <span class="bd-val">${book.barcode}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Jatuh Tempo</span>
                        <span class="bd-val">${this.formatDate(book.due_date)}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Status</span>
                        <span class="bd-val">${statusBadge}</span>
                    </div>
                </div>`;
                });

                details.innerHTML = itemsHtml;
                sec.classList.remove('d-none');
            }

            removeBookItem(id) {
                this.booksToReturn = this.booksToReturn.filter(b => String(b.id) !== String(id));
                this.renderBookList();
            }

            async checkBook() {
                const barcode = document.getElementById('barcodeInput').value.trim();
                const returnDate = document.getElementById('returnDateInput').value;
                if (!barcode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan masukkan barcode buku',
                        confirmButtonColor: '#1B3878'
                    });
                    this.focusInput();
                    return;
                }
                this.showLoading();
                try {
                    const res = await fetch('/sirkulasi-pengembalian/check-book', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `nomorBarcode=${encodeURIComponent(barcode)}&returnDate=${encodeURIComponent(returnDate)}`
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.currentMemberId = data.data.member_id;
                        this.currentMemberName = data.data.member_name;
                        this.booksToReturn = data.data.items;
                        this.renderBookList();
                        this.hideMessage();
                        this.loadHistory();
                        this.clearInput();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message,
                            confirmButtonColor: '#e8394d'
                        });
                        this.hideBookInfo();
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengecek buku',
                        confirmButtonColor: '#e8394d'
                    });
                    this.hideBookInfo();
                } finally {
                    this.hideLoading();
                }
            }

            returnBook() {
                if (this.booksToReturn.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tidak ada buku yang dipilih. Silakan scan buku terlebih dahulu.',
                        confirmButtonColor: '#1B3878'
                    });
                    this.focusInput();
                    return;
                }

                // Cek apakah ada buku yang terlambat
                const overdueBooks = this.booksToReturn.filter(b => b.is_overdue);

                if (overdueBooks.length > 0) {
                    this.showViolationModal(overdueBooks);
                } else {
                    // Jika tidak ada keterlambatan, tanyakan konfirmasi kerusakan/hilang
                Swal.fire({
                        title: 'Konfirmasi Kondisi Pengembalian',
                        text: 'Apakah terdapat pelanggaran pada buku yang dikembalikan, seperti buku hilang, rusak, atau kondisi lainnya?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#e8394d',
                        cancelButtonColor: '#1B3878',
                        confirmButtonText: 'Ya, Ada Pelanggaran',
                        cancelButtonText: 'Tidak, Kondisi Baik'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.showViolationModal(this.booksToReturn);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Tidak ada pelanggaran, langsung proses pengembalian
                            this.submitReturn(false, null);
                        }
                    });
                }
            }

            showViolationModal(overdueBooks) {
                // Render daftar buku di modal
                const listEl = document.getElementById('overdueBookList');
                listEl.innerHTML = overdueBooks.map(b => `
            <div style="background:var(--gray-100);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:8px;border-left:4px solid var(--danger);">
                <div style="font-size:14px;font-weight:700;color:var(--gray-800);">${b.title || '-'}</div>
                <div style="font-size:12px;color:var(--gray-500);">
                    <i class="fa fa-barcode"></i> ${b.barcode} ${b.is_overdue ? `&nbsp;|&nbsp; <span style="color:var(--danger);font-weight:700;"><i class="fa fa-exclamation-circle"></i> Terlambat ${b.days_overdue} hari</span>` : ''}
                </div>
            </div>`).join('');

                const hasOverdue = overdueBooks.some(b => b.is_overdue);
                if (hasOverdue) {
                    const overdueCount = overdueBooks.filter(b => b.is_overdue).length;
                    document.getElementById('overdueInfoText').textContent =
                        `${overdueCount} dari ${this.booksToReturn.length} buku melewati tanggal jatuh tempo.`;
                } else {
                    document.getElementById('overdueInfoText').textContent =
                        `Proses pencatatan pelanggaran (kerusakan/kehilangan) untuk ${this.booksToReturn.length} buku.`;
                }

                // Reset form
                document.getElementById('jumlahDendaInput').value = '';
                document.getElementById('perHariCheck').checked = hasOverdue;
                document.getElementById('dendaPreview').textContent = '';

                // Simpan referensi overdueBooks untuk kalkulasi preview
                this._overdueBooks = overdueBooks;

                new bootstrap.Modal(document.getElementById('modalPelanggaran')).show();
            }

            updateDendaPreview() {
                const nominal = parseFloat(document.getElementById('jumlahDendaInput').value) || 0;
                const perHari = document.getElementById('perHariCheck').checked;
                const overdue = this._overdueBooks || [];
                const preview = document.getElementById('dendaPreview');

                if (!nominal || overdue.length === 0) {
                    preview.textContent = '';
                    return;
                }

                if (perHari) {
                    const totalDays = overdue.reduce((s, b) => s + (b.days_overdue || 0), 0);
                    const total = totalDays * nominal;
                    preview.innerHTML = `<i class="fa fa-info-circle"></i> Estimasi total denda: <b>Rp ${total.toLocaleString('id-ID')}</b> (${totalDays} hari × Rp ${nominal.toLocaleString('id-ID')})`;
                } else {
                    const total = overdue.length * nominal;
                    preview.innerHTML = `<i class="fa fa-info-circle"></i> Estimasi total denda: <b>Rp ${total.toLocaleString('id-ID')}</b> (${overdue.length} buku × Rp ${nominal.toLocaleString('id-ID')})`;
                }
            }

            handleConfirmViolation() {
                const jenisPelanggaranId = document.getElementById('jenisPelanggaranSelect').value;
                const jenisDendaId = document.getElementById('jenisDendaSelect').value;
                const jumlahDenda = document.getElementById('jumlahDendaInput').value;
                const perHari = document.getElementById('perHariCheck').checked;

                if (!jenisPelanggaranId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih jenis pelanggaran',
                        confirmButtonColor: '#1B3878'
                    });
                    return;
                }
                if (!jenisDendaId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih jenis denda',
                        confirmButtonColor: '#1B3878'
                    });
                    return;
                }
                if (!jumlahDenda || parseFloat(jumlahDenda) < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan masukkan jumlah denda yang valid',
                        confirmButtonColor: '#1B3878'
                    });
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('modalPelanggaran')).hide();

                this.submitReturn(true, {
                    jenis_pelanggaran_id: jenisPelanggaranId,
                    jenis_denda_id: jenisDendaId,
                    jumlah_denda: parseFloat(jumlahDenda),
                    per_hari: perHari
                });
            }

            async submitReturn(processViolation, violationData) {
                this.showLoading();

                const itemIds = this.booksToReturn.map(b => b.id);
                const returnDate = document.getElementById('returnDateInput').value;

                const body = [
                    `item_ids=${encodeURIComponent(JSON.stringify(itemIds))}`,
                    `process_violation=${processViolation ? '1' : '0'}`,
                    `violation_data=${encodeURIComponent(JSON.stringify(violationData || {}))}`,
                    `returnDate=${encodeURIComponent(returnDate)}`
                ].join('&');

                try {
                    const res = await fetch('/sirkulasi-pengembalian/process-return', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body
                    });
                    const data = await res.json();

                    this.hideLoading();

                    if (data.status === 'success') {
                        window.location.href = data.struk_url;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message,
                            confirmButtonColor: '#e8394d'
                        });
                    }
                } catch (e) {
                    this.hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Sistem',
                        text: 'Terjadi kesalahan saat memproses pengembalian',
                        confirmButtonColor: '#e8394d'
                    });
                }
            }

            async loadHistory() {
                try {
                    let url = '/sirkulasi-pengembalian/getReturnHistory?limit=5';
                    if (this.currentMemberId) url += '&member_id=' + this.currentMemberId;
                    const res = await fetch(url);
                    const data = await res.json();
                    if (data.status === 'success') this.displayHistory(data.data);
                } catch (e) {
                    console.error('Error loading history:', e);
                }
            }

            displayHistory(items) {
                const list = document.getElementById('historyList');
                if (!items.length) {
                    list.innerHTML = `<div class="history-empty"><i class="fa fa-inbox"></i> Belum ada riwayat pengembalian</div>`;
                    return;
                }
                list.innerHTML = items.map(item => `
            <div class="history-item fade-in">
                <div>
                    <div class="hi-title">${item.Title || 'Judul tidak tersedia'}</div>
                    <div class="hi-author">${item.Author || 'Pengarang tidak tersedia'}</div>
                    <div class="hi-barcode"><i class="fa fa-barcode"></i> ${item.NomorBarcode}</div>
                </div>
                <div class="hi-date">
                    ${this.formatDate(item.ActualReturn)}
                    ${item.LateDays > 0 ? `<br><span class="status-badge status-overdue" style="font-size:10px;margin-top:5px;">Terlambat ${item.LateDays} hari</span>` : ''}
                </div>
            </div>`).join('');
            }

            formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            clearInput() {
                document.getElementById('barcodeInput').value = '';
                this.focusInput();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new SelfReturnApp();
        });
    </script>
    <?= $this->endSection('script'); ?>