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
        --warn-col: #856404;
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
    .scan-center { text-align: center; padding: 4px 0 20px; }
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
    .pm-btn-outline-sm {
        background: transparent; color: var(--blue-800);
        border: 1.5px solid var(--blue-200);
        padding: 7px 14px; font-size: 13px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 5px; font-weight: 600;
    }
    .pm-btn-outline-sm:hover { background: var(--blue-50); color: var(--blue-800); }

    /* ── Messages ── */
    .pm-alert {
        border-radius: var(--radius-md); padding: 13px 16px;
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 14px; font-weight: 500; margin-bottom: 14px; border: none;
    }
    .pm-alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
    .pm-alert-danger  { background: #fef1f2; color: var(--danger); }
    .pm-alert-success { background: var(--blue-50); color: var(--blue-800); border: 1px solid var(--blue-200); }

    /* ── Book info box ── */
    .book-info-box {
        background: var(--gray-100); border-radius: var(--radius-lg);
        padding: 20px; border-left: 4px solid var(--blue-500);
        margin-bottom: 16px;
    }
    .book-info-box h6 {
        font-size: 14px; font-weight: 700; color: var(--blue-900);
        margin-bottom: 12px; display: flex; align-items: center; gap: 7px;
    }
    .book-detail {
        display: flex; justify-content: space-between; align-items: center;
        padding: 7px 0; border-bottom: 1px solid var(--gray-300);
        font-size: 13px;
    }
    .book-detail:last-child { border-bottom: none; }
    .book-detail .bd-label { color: var(--gray-500); font-weight: 500; }
    .book-detail .bd-val   { color: var(--gray-800); font-weight: 600; text-align: right; }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 50px;
        font-size: 12px; font-weight: 700;
    }
    .status-available { background: #edfaf4; color: #0a6e43; }
    .status-loaned    { background: var(--warn-bg); color: var(--warn-col); }
    .status-overdue   { background: #fef1f2; color: var(--danger); }

    /* ── Loading ── */
    .pm-loading {
        text-align: center; padding: 24px;
    }
    .pm-loading i { font-size: 28px; color: var(--blue-500); animation: spin 1s linear infinite; }
    .pm-loading p  { color: var(--gray-500); font-size: 14px; margin-top: 10px; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* ── Divider ── */
    .pm-divider { border: none; border-top: 2px dashed var(--gray-300); margin: 22px 0; }

    /* ── History items ── */
    .history-header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
    }
    .history-header h6 { font-size: 15px; font-weight: 700; color: var(--blue-900); margin: 0; }

    .history-item {
        background: var(--gray-100); border-radius: var(--radius-md);
        padding: 14px 16px; margin-bottom: 9px;
        border-left: 4px solid var(--blue-500);
        display: flex; align-items: flex-start; justify-content: space-between; gap: 14px;
    }
    .history-item .hi-title  { font-size: 14px; font-weight: 700; color: var(--blue-900); margin-bottom: 3px; }
    .history-item .hi-author { font-size: 12px; color: var(--gray-500); margin-bottom: 3px; }
    .history-item .hi-barcode{ font-size: 12px; color: var(--gray-500); }
    .history-item .hi-date   { font-size: 12px; color: var(--gray-500); white-space: nowrap; text-align: right; }

    .history-empty {
        text-align: center; padding: 28px;
        color: var(--gray-500); font-size: 14px;
    }

	/* ── Tambahan untuk Divider Antar Buku ── */
    .pm-divider-top {
        border-top: 1px dashed var(--gray-300);
        margin-top: 15px;
        padding-top: 15px;
    }
    .history-empty i { font-size: 28px; margin-bottom: 10px; display: block; }

    /* ── fade-in ── */
    .fade-in { animation: fadeIn .3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
</style>
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
                        <li class="active breadcrumb-item" aria-current="page">Pengembalian Mandiri</li>
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

                    <div class="pm-input-wrap">
                        <i class="fa fa-barcode input-icon"></i>
                        <input type="text" id="barcodeInput" class="pm-input"
                               placeholder="Masukkan atau scan barcode buku..."
                               autocomplete="off" autofocus>
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
                            <i class="fa fa-clock-o"></i>
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
<script>
class SelfReturnApp {
    constructor() {
        this.currentMemberId = null; 
        this.currentMemberName = null; 
        this.booksToReturn = []; // Menyimpan state list buku yang akan dikembalikan
        window.selfReturnApp = this; // Expose global untuk fungsi onclick hapus buku
        this.init();
    }

    init() {
        this.bindEvents();
        this.focusInput();
        this.setupAutoScan();
    }

    bindEvents() {
        document.getElementById('checkBookBtn').addEventListener('click', () => this.checkBook());
        document.getElementById('returnBookBtn').addEventListener('click', () => this.returnBook());
        document.getElementById('refreshHistoryBtn').addEventListener('click', () => this.loadHistory());
        document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.checkBook();
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

    showMessage(message, type = 'success') {
        const sec = document.getElementById('messageSection');
        const cls  = type === 'success' ? 'pm-alert-success' : 'pm-alert-danger';
        const icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
        sec.innerHTML = `
            <div class="pm-alert ${cls} fade-in mt-3">
                <i class="${icon}"></i>
                <span>${message}</span>
            </div>`;
        if (type === 'success') {
            setTimeout(() => { sec.innerHTML = ''; }, 5000);
        }
    }

    hideMessage() {
        document.getElementById('messageSection').innerHTML = '';
    }

    // Fungsi baru untuk me-render buku dari array
    renderBookList() {
        const sec = document.getElementById('bookInfoSection');
        const details = document.getElementById('bookDetails');
        const titleSec = sec.querySelector('h6');

        if (this.booksToReturn.length === 0) {
            this.hideBookInfo();
            return;
        }

        // Tampilkan Header dengan Nama Anggota
        titleSec.innerHTML = `
            <div style="display:flex; flex-direction:column; gap:5px;">
                <span style="color:var(--blue-900); font-size:16px;">
                    <i class="fa fa-user-circle"></i> ${this.currentMemberName || '-'}
                </span>
                <small style="color:var(--gray-500); font-size:13px; font-weight:normal;">
                    <i class="fa fa-book"></i> ${this.booksToReturn.length} buku siap dikembalikan
                </small>
            </div>
        `;

        let itemsHtml = '';
        
        this.booksToReturn.forEach((book, index) => {
            let statusBadge = book.is_overdue
                ? `<span class="status-badge status-overdue"><i class="fa fa-exclamation-circle"></i> Terlambat ${book.days_overdue} hari</span>`
                : `<span class="status-badge status-available"><i class="fa fa-clock-o"></i> Sedang Dipinjam</span>`;

            let dividerClass = index > 0 ? 'pm-divider-top' : '';

            itemsHtml += `
                <div class="${dividerClass} position-relative fade-in" style="padding-right: 45px;">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute" 
                            style="right: 0; top: 10px; border-radius: 8px; width:35px; height:35px;"
                            onclick="selfReturnApp.removeBookItem(${book.id})" title="Keluarkan buku ini dari daftar pengembalian">
                        <i class="fa fa-trash"></i>
                    </button>

                    <div class="book-detail">
                        <span class="bd-label">Judul</span>
                        <span class="bd-val" style="text-align:right; max-width:75%;">${book.title || '-'}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Barcode</span>
                        <span class="bd-val">${book.barcode}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Status</span>
                        <span class="bd-val">${statusBadge}</span>
                    </div>
                </div>
            `;
        });

        details.innerHTML = itemsHtml;
        sec.classList.remove('d-none');
    }

    // Fungsi menghapus item dari list keranjang pengembalian
    removeBookItem(id) {
        this.booksToReturn = this.booksToReturn.filter(b => b.id !== id);
        this.renderBookList();
    }

    hideBookInfo() {
        document.getElementById('bookInfoSection').classList.add('d-none');
    }

    async checkBook() {
        const barcode = document.getElementById('barcodeInput').value.trim();
        if (!barcode) { 
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan masukkan barcode buku', confirmButtonColor: '#1B3878' });
            this.focusInput(); return; 
        }

        this.showLoading();
        try {
            const res  = await fetch('/sirkulasi-pengembalian/check-book', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nomorBarcode=${encodeURIComponent(barcode)}`
            });
            const data = await res.json();
            
            if (data.status === 'success') { 
                // Simpan data ke dalam variabel global class
                this.currentMemberId = data.data.member_id;
                this.currentMemberName = data.data.member_name;
                this.booksToReturn = data.data.items; 
                
                this.renderBookList(); 
                this.hideMessage(); 
                this.loadHistory();
                this.clearInput(); // Clear input agar siap untuk scan transaksi orang lain
            } else { 
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
                this.hideBookInfo(); 
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengecek buku', confirmButtonColor: '#e8394d' });
            this.hideBookInfo();
        } finally { this.hideLoading(); }
    }

    async returnBook() {
        // Cek jika tidak ada buku di dalam keranjang pengembalian
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

        this.showLoading();
        
        // Ambil array ID item yang ada di keranjang
        const itemIds = this.booksToReturn.map(book => book.id);

        try {
            const res  = await fetch('/sirkulasi-pengembalian/process-return', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_ids=${encodeURIComponent(JSON.stringify(itemIds))}` // Kirim sebagai JSON string
            });
            const data = await res.json();
            
            this.hideLoading();
            
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: data.message,
                    confirmButtonColor: '#1B3878',
                    confirmButtonText: 'Tutup',
                    timer: 3500,
                    timerProgressBar: true
                });
                
                // Kosongkan keranjang setelah berhasil
                this.booksToReturn = []; 
                this.currentMemberId = null;
                this.currentMemberName = null;
                
                this.hideBookInfo();
                this.loadHistory();
                this.focusInput();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
            }
        } catch (e) {
            this.hideLoading();
            Swal.fire({ icon: 'error', title: 'Error Sistem', text: 'Terjadi kesalahan saat memproses pengembalian', confirmButtonColor: '#e8394d' });
        }
    }

    // Fungsi History tetap sama seperti sebelumnya...
    async loadHistory() {
        try {
            let url = '/sirkulasi-pengembalian/getReturnHistory?limit=5';
            if (this.currentMemberId) {
                url += '&member_id=' + this.currentMemberId;
            }
            const res  = await fetch(url);
            const data = await res.json();
            if (data.status === 'success') this.displayHistory(data.data);
        } catch (e) { console.error('Error loading history:', e); }
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
                    ${item.LateDays > 0 ? `<br><span class="status-badge status-overdue" style="font-size:10px; margin-top:5px;">Terlambat ${item.LateDays} hari</span>` : ''}
                </div>
            </div>
        `).join('');
    }

    formatDate(dateString) {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    clearInput() {
        document.getElementById('barcodeInput').value = '';
        this.focusInput();
    }
}

document.addEventListener('DOMContentLoaded', () => { new SelfReturnApp(); });
</script>
<?= $this->endSection('script'); ?>