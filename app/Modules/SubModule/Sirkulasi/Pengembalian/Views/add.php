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
<link rel="stylesheet" href="<?= base_url('assets/css/ina-pelanggaran-modal.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-pelanggaran-modal.css') ?>">
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
<!-- Modal Pelanggaran (INA Modal varian Task) -->
<div class="modal fade" id="modalPelanggaran" tabindex="-1" role="dialog" aria-labelledby="vpTitle" aria-describedby="vpDesc" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable vp-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="vpTitle">Catat Pelanggaran</h5>
                    <p class="vp-desc" id="vpDesc">Pilih buku yang melanggar, lalu isi jenis pelanggaran dan dendanya.</p>
                </div>
                <button type="button" class="vp-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Tutup">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="vpForm" novalidate>
                    <fieldset class="vp-field">
                        <legend class="vp-label">Buku yang melanggar <span class="vp-required" aria-hidden="true">*</span></legend>
                        <ul class="vp-books" id="vpBookList"></ul>
                        <small class="vp-error" id="vpBooksError" hidden>Pilih minimal satu buku.</small>
                    </fieldset>

                    <hr class="vp-divider">

                    <div class="vp-field">
                        <label class="vp-label" for="jenisPelanggaranSelect">Jenis Pelanggaran <span class="vp-required" aria-hidden="true">*</span></label>
                        <select id="jenisPelanggaranSelect" class="form-select vp-control" required aria-describedby="vpPelanggaranError">
                            <option value="">Pilih jenis pelanggaran</option>
                        </select>
                        <small class="vp-error" id="vpPelanggaranError" hidden>Pilih jenis pelanggaran.</small>
                    </div>

                    <div class="vp-field">
                        <label class="vp-label" for="jenisDendaSelect">Jenis Denda <span class="vp-required" aria-hidden="true">*</span></label>
                        <select id="jenisDendaSelect" class="form-select vp-control" required aria-describedby="vpDendaError">
                            <option value="">Pilih jenis denda</option>
                        </select>
                        <small class="vp-error" id="vpDendaError" hidden>Pilih jenis denda.</small>
                    </div>

                    <div class="vp-field">
                        <label class="vp-label" for="jumlahDendaInput">Jumlah Denda <span class="vp-optional">(opsional)</span></label>
                        <div class="vp-affix">
                            <span class="vp-affix__text" aria-hidden="true">Rp</span>
                            <input type="number" id="jumlahDendaInput" class="vp-control" min="0" step="500" inputmode="numeric" placeholder="0" aria-describedby="vpDendaHint vpJumlahError">
                        </div>
                        <div class="vp-check">
                            <input type="checkbox" id="perHariCheck">
                            <label for="perHariCheck">Kalikan dengan jumlah hari terlambat</label>
                        </div>
                        <small class="vp-hint" id="vpDendaHint">Isi 0 atau kosongkan bila tidak ada denda uang.</small>
                        <small class="vp-error" id="vpJumlahError" hidden></small>
                    </div>

                    <div class="vp-field">
                        <label class="vp-label" for="jumlahSuspendInput">Lama Suspend <span class="vp-optional">(opsional)</span></label>
                        <div class="vp-affix">
                            <input type="number" id="jumlahSuspendInput" class="vp-control" min="0" max="365" step="1" inputmode="numeric" placeholder="0" aria-describedby="vpSuspendHint vpSuspendError">
                            <span class="vp-affix__text" aria-hidden="true">hari</span>
                        </div>
                        <small class="vp-hint" id="vpSuspendHint">Selama masa suspend, anggota tidak bisa meminjam buku.</small>
                        <small class="vp-error" id="vpSuspendError" hidden></small>
                    </div>

                    <div class="vp-summary" id="vpSummary" role="status" aria-live="polite" hidden></div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="skipViolationBtn" class="btn btn-outline-secondary">Lewati Pelanggaran</button>
                <button type="button" id="confirmViolationBtn" class="btn btn-primary">Simpan &amp; Kembalikan Buku</button>
            </div>

        </div>
    </div>
</div>
    <script>
        // Teks dari server/DB selalu di-escape sebelum masuk innerHTML
        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

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

                // Ringkasan denda & suspend diperbarui setiap isian berubah
                ['jumlahDendaInput', 'jumlahSuspendInput'].forEach(id =>
                    document.getElementById(id).addEventListener('input', () => this.updateDendaPreview()));
                document.getElementById('perHariCheck').addEventListener('change', () => this.updateDendaPreview());
                document.getElementById('vpBookList').addEventListener('change', (e) => {
                    const li = e.target.closest('.vp-book');
                    if (li) li.classList.toggle('is-selected', e.target.checked);
                    this.updateDendaPreview();
                });
                ['jenisPelanggaranSelect', 'jenisDendaSelect'].forEach(id =>
                    document.getElementById(id).addEventListener('change', (e) => this.setFieldError(e.target, null)));
                document.getElementById('vpForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleConfirmViolation();
                });
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
                        resP.data.forEach(p => sel.add(new Option(p.JenisPelanggaran, p.ID)));
                    }
                    if (resD.status === 'success') {
                        this.jenisDenda = resD.data;
                        const sel = document.getElementById('jenisDendaSelect');
                        resD.data.forEach(d => sel.add(new Option(d.Name, d.ID)));
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
                <span style="color:var(--blue-900);font-size:16px;"><i class="fa fa-user-circle"></i> ${escapeHtml(this.currentMemberName || '-')}</span>
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
                            onclick="selfReturnApp.removeBookItem(${Number(book.id)})" aria-label="Hapus dari daftar">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="book-detail">
                        <span class="bd-label">Judul</span>
                        <span class="bd-val" style="text-align:right;max-width:75%;">${escapeHtml(book.title || '-')}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Barcode</span>
                        <span class="bd-val">${escapeHtml(book.barcode)}</span>
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
                    this.showViolationModal();
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
                            this.showViolationModal();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Tidak ada pelanggaran, langsung proses pengembalian
                            this.submitReturn(false, null);
                        }
                    });
                }
            }

            showViolationModal() {
                const books = this.booksToReturn;
                const overdueCount = books.filter(b => b.is_overdue).length;

                // Semua buku transaksi ditampilkan; yang terlambat langsung dipilih,
                // buku lain bisa dicentang bila rusak/hilang.
                document.getElementById('vpBookList').innerHTML = books.map(b => `
                    <li>
                        <label class="vp-book${b.is_overdue ? ' is-selected' : ''}">
                            <input type="checkbox" name="vp_book" value="${Number(b.id)}" ${b.is_overdue ? 'checked' : ''}>
                            <span class="vp-book__body">
                                <span class="vp-book__title">${escapeHtml(b.title || '-')}</span>
                                <span class="vp-book__meta">
                                    <span><i class="fa fa-barcode" aria-hidden="true"></i> ${escapeHtml(b.barcode)}</span>
                                    ${b.is_overdue
                                        ? `<span class="vp-badge vp-badge--danger">Terlambat ${Number(b.days_overdue)} hari</span>`
                                        : '<span class="vp-badge vp-badge--neutral">Tepat waktu</span>'}
                                </span>
                            </span>
                        </label>
                    </li>`).join('');

                document.getElementById('vpDesc').textContent = overdueCount > 0
                    ? `${overdueCount} dari ${books.length} buku melewati tanggal jatuh tempo. Catat pelanggaran atau lewati.`
                    : 'Pilih buku yang rusak atau hilang, lalu isi jenis pelanggaran dan dendanya.';

                // Form kembali kosong setiap kali dibuka
                document.getElementById('vpForm').reset();
                const perHari = document.getElementById('perHariCheck');
                perHari.disabled = overdueCount === 0;
                perHari.checked = overdueCount > 0;
                ['jenisPelanggaranSelect', 'jenisDendaSelect', 'jumlahDendaInput', 'jumlahSuspendInput']
                    .forEach(id => this.setFieldError(document.getElementById(id), null));
                document.getElementById('vpBooksError').hidden = true;
                this.updateDendaPreview();

                const modalEl = document.getElementById('modalPelanggaran');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                modalEl.addEventListener('shown.bs.modal', () => document.getElementById('jenisPelanggaranSelect').focus(), { once: true });
            }

            selectedViolationBooks() {
                const ids = Array.from(document.querySelectorAll('#vpBookList input[name="vp_book"]:checked')).map(i => String(i.value));
                return this.booksToReturn.filter(b => ids.includes(String(b.id)));
            }

            setFieldError(field, message) {
                const errorEl = document.getElementById(field.getAttribute('aria-describedby').split(' ').pop());
                if (message) {
                    field.setAttribute('aria-invalid', 'true');
                    errorEl.textContent = message;
                    errorEl.hidden = false;
                } else {
                    field.removeAttribute('aria-invalid');
                    errorEl.hidden = true;
                }
            }

            formatShortDate(date) {
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }

            updateDendaPreview() {
                const books = this.selectedViolationBooks();
                const nominal = Math.max(0, parseInt(document.getElementById('jumlahDendaInput').value, 10) || 0);
                const suspend = Math.max(0, parseInt(document.getElementById('jumlahSuspendInput').value, 10) || 0);
                const perHari = document.getElementById('perHariCheck').checked;
                const summary = document.getElementById('vpSummary');
                const lines = [];

                if (books.length && nominal > 0) {
                    if (perHari) {
                        const totalDays = books.reduce((s, b) => s + (Number(b.days_overdue) || 0), 0);
                        lines.push(`Total denda: <strong>Rp ${(totalDays * nominal).toLocaleString('id-ID')}</strong> (${totalDays} hari terlambat × Rp ${nominal.toLocaleString('id-ID')})`);
                    } else {
                        lines.push(`Total denda: <strong>Rp ${(books.length * nominal).toLocaleString('id-ID')}</strong> (${books.length} buku × Rp ${nominal.toLocaleString('id-ID')})`);
                    }
                }
                if (books.length && suspend > 0) {
                    // Masa suspend dihitung dari tanggal pengembalian (sama dengan aturan di Entri Peminjaman)
                    const base = document.getElementById('returnDateInput').value;
                    const start = base ? new Date(base + 'T00:00:00') : new Date();
                    start.setDate(start.getDate() + suspend);
                    lines.push(`Anggota baru boleh meminjam lagi pada <strong>${this.formatShortDate(start)}</strong>.`);
                }

                summary.innerHTML = lines.join('<br>');
                summary.hidden = lines.length === 0;
            }

            handleConfirmViolation() {
                const pelanggaranEl = document.getElementById('jenisPelanggaranSelect');
                const dendaEl = document.getElementById('jenisDendaSelect');
                const jumlahEl = document.getElementById('jumlahDendaInput');
                const suspendEl = document.getElementById('jumlahSuspendInput');
                const books = this.selectedViolationBooks();
                let firstInvalid = null;
                const fail = (el, msg) => { this.setFieldError(el, msg); firstInvalid = firstInvalid || el; };

                document.getElementById('vpBooksError').hidden = books.length > 0;
                if (!books.length) firstInvalid = document.querySelector('#vpBookList input');

                pelanggaranEl.value ? this.setFieldError(pelanggaranEl, null) : fail(pelanggaranEl, 'Pilih jenis pelanggaran.');
                dendaEl.value ? this.setFieldError(dendaEl, null) : fail(dendaEl, 'Pilih jenis denda.');

                const jumlahRaw = jumlahEl.value.trim();
                const jumlah = jumlahRaw === '' ? 0 : Number(jumlahRaw);
                if (!Number.isInteger(jumlah) || jumlah < 0) {
                    fail(jumlahEl, 'Jumlah denda harus angka bulat 0 atau lebih.');
                } else if (jumlah > 100000000) {
                    fail(jumlahEl, 'Jumlah denda maksimal Rp 100.000.000.');
                } else {
                    this.setFieldError(jumlahEl, null);
                }

                const suspendRaw = suspendEl.value.trim();
                const suspend = suspendRaw === '' ? 0 : Number(suspendRaw);
                if (!Number.isInteger(suspend) || suspend < 0 || suspend > 365) {
                    fail(suspendEl, 'Lama suspend harus angka bulat 0 sampai 365 hari.');
                } else {
                    this.setFieldError(suspendEl, null);
                }

                if (firstInvalid) {
                    firstInvalid.focus();
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('modalPelanggaran')).hide();

                this.submitReturn(true, {
                    item_ids: books.map(b => Number(b.id)),
                    jenis_pelanggaran_id: Number(pelanggaranEl.value),
                    jenis_denda_id: Number(dendaEl.value),
                    jumlah_denda: jumlah,
                    jumlah_suspend: suspend,
                    per_hari: document.getElementById('perHariCheck').checked && !document.getElementById('perHariCheck').disabled
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
                    <div class="hi-title">${escapeHtml(item.Title || 'Judul tidak tersedia')}</div>
                    <div class="hi-author">${escapeHtml(item.Author || 'Pengarang tidak tersedia')}</div>
                    <div class="hi-barcode"><i class="fa fa-barcode"></i> ${escapeHtml(item.NomorBarcode)}</div>
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