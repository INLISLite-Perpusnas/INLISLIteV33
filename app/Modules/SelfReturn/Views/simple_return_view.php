<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-selfservice.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-selfservice.css') ?>">

<div class="ss-ina">
<div class="main-container">
        <div class="text-center mb-4">
            <h1 class="ss-page-title">Pengembalian Mandiri</h1>
            <p class="ss-page-subtitle">Sistem pengembalian buku mandiri</p>
        </div>

        <div class="return-card">
            <div class="card-body">
                <!-- Scanner Section -->
                <div class="scanner-section">
                    <div class="ss-field">
                        <label for="barcodeInput">Barcode buku</label>
                        <div class="barcode-input">
                            <input type="text" id="barcodeInput" class="form-control"
                                   placeholder="Scan atau ketik barcode"
                                   autocomplete="off" autofocus>
                            <i class="fas fa-barcode scan-icon" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="ss-actions">
                        <button type="button" id="checkBookBtn" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search"></i> Cek buku
                        </button>
                        <button type="button" id="returnBookBtn" class="btn btn-primary btn-lg">
                            <i class="fas fa-undo"></i> Kembalikan buku
                        </button>
                    </div>
                </div>

                <!-- Loading Section -->
                <div id="loadingSection" class="loading d-none">
                    <i class="fas fa-spinner"></i>
                    <p class="mt-2">Memproses...</p>
                </div>

                <!-- Message Section -->
                <div id="messageSection"></div>

                <!-- Book Info Section: buku dalam satu transaksi peminjaman yang siap dikembalikan -->
                <div id="bookInfoSection" class="d-none">
                    <div class="book-info fade-in">
                        <h5><i class="fas fa-book text-primary"></i> Informasi buku</h5>
                        <div id="bookDetails"></div>
                    </div>
                </div>

                <!-- Return History Section -->
                <div class="history-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Riwayat pengembalian terbaru</h5>
                        <button type="button" id="refreshHistoryBtn" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                    <div id="historyList">
                        <div class="text-center text-muted">
                            <i class="fas fa-clock"></i>
                            <p>Klik refresh untuk melihat riwayat pengembalian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


  <!-- Bootstrap JS -->
  <script>
        class SelfReturnApp {
            constructor() {
                this.currentMemberId = null;
                this.currentMemberName = null;
                this.booksToReturn = [];
                window.selfReturnApp = this;
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

                // Enter key handling — scan barcode lalu tampilkan info transaksinya
                document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.checkBook();
                    }
                });
            }

            setupAutoScan() {
                let scanTimeout;
                const input = document.getElementById('barcodeInput');

                input.addEventListener('input', () => {
                    clearTimeout(scanTimeout);
                    scanTimeout = setTimeout(() => {
                        if (input.value.length >= 8) { // Minimum barcode length
                            this.checkBook();
                        }
                    }, 500);
                });
            }

            focusInput() {
                document.getElementById('barcodeInput').focus();
            }

            showLoading() {
                document.getElementById('loadingSection').classList.remove('d-none');
                this.hideMessage();
            }

            hideLoading() {
                document.getElementById('loadingSection').classList.add('d-none');
            }

            showMessage(message, type = 'success') {
                const messageSection = document.getElementById('messageSection');
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

                messageSection.innerHTML = `
                    <div class="alert ${alertClass} fade-in">
                        <i class="${icon}"></i> ${message}
                    </div>
                `;

                // Auto hide success messages
                if (type === 'success') {
                    setTimeout(() => {
                        messageSection.innerHTML = '';
                    }, 5000);
                }
            }

            hideMessage() {
                document.getElementById('messageSection').innerHTML = '';
            }

            hideBookInfo() {
                document.getElementById('bookInfoSection').classList.add('d-none');
            }

            // Render seluruh buku dalam transaksi peminjaman yang sedang discan,
            // masing-masing dengan tombol hapus sebelum dikonfirmasi.
            renderBookList() {
                const section = document.getElementById('bookInfoSection');
                const details = document.getElementById('bookDetails');
                const titleSection = section.querySelector('h5');

                if (this.booksToReturn.length === 0) {
                    this.hideBookInfo();
                    return;
                }

                titleSection.innerHTML = `
                    <i class="fas fa-user"></i> ${this.currentMemberName || '-'}
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-book"></i> ${this.booksToReturn.length} buku siap dikembalikan
                    </small>
                `;

                details.innerHTML = this.booksToReturn.map(book => {
                    const statusBadge = book.is_overdue
                        ? `<span class="status-badge status-danger">Terlambat ${book.days_overdue} hari</span>`
                        : `<span class="status-badge status-warning">Sedang dipinjam</span>`;

                    return `
                        <div class="book-item-block fade-in">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-item" aria-label="Hapus buku"
                                    onclick="selfReturnApp.removeBookItem('${book.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            <div class="book-detail">
                                <span><strong>Judul:</strong></span>
                                <span>${book.title || 'Tidak tersedia'}</span>
                            </div>
                            <div class="book-detail">
                                <span><strong>Pengarang:</strong></span>
                                <span>${book.author || 'Tidak tersedia'}</span>
                            </div>
                            <div class="book-detail">
                                <span><strong>Barcode:</strong></span>
                                <span>${book.barcode}</span>
                            </div>
                            <div class="book-detail">
                                <span><strong>Jatuh tempo:</strong></span>
                                <span>${this.formatDate(book.due_date)}</span>
                            </div>
                            <div class="book-detail">
                                <span><strong>Status:</strong></span>
                                <span>${statusBadge}</span>
                            </div>
                        </div>
                    `;
                }).join('');

                section.classList.remove('d-none');
            }

            removeBookItem(id) {
                this.booksToReturn = this.booksToReturn.filter(b => String(b.id) !== String(id));
                this.renderBookList();
            }

            async checkBook() {
                const barcode = document.getElementById('barcodeInput').value.trim();

                if (!barcode) {
                    this.showMessage('Silakan masukkan barcode buku', 'error');
                    this.focusInput();
                    return;
                }

                this.showLoading();

                try {
                    const response = await fetch('/pengembalian-mandiri/check-book', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `nomorBarcode=${encodeURIComponent(barcode)}`
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        this.currentMemberId = data.data.member_id;
                        this.currentMemberName = data.data.member_name;
                        this.booksToReturn = data.data.items;
                        this.renderBookList();
                        this.hideMessage();
                        this.clearInput();
                    } else {
                        this.showMessage(data.message, 'error');
                        this.hideBookInfo();
                    }
                } catch (error) {
                    this.showMessage('Terjadi kesalahan saat mengecek buku', 'error');
                    this.hideBookInfo();
                } finally {
                    this.hideLoading();
                }
            }

            returnBook() {
                if (this.booksToReturn.length === 0) {
                    this.showMessage('Tidak ada buku yang dipilih. Silakan scan buku terlebih dahulu.', 'error');
                    this.focusInput();
                    return;
                }

                Swal.fire({
                    title: 'Proses pengembalian?',
                    text: `${this.booksToReturn.length} buku akan dikembalikan.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2d659e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, proses',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        this.submitReturn();
                    }
                });
            }

            async submitReturn() {
                this.showLoading();

                const itemIds = this.booksToReturn.map(b => b.id);

                try {
                    const response = await fetch('/pengembalian-mandiri/process-return', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `item_ids=${encodeURIComponent(JSON.stringify(itemIds))}`
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        window.location.href = data.struk_url;
                    } else {
                        this.showMessage(data.message, 'error');
                        this.hideLoading();
                    }
                } catch (error) {
                    this.showMessage('Terjadi kesalahan saat memproses pengembalian', 'error');
                    this.hideLoading();
                }
            }

            async loadHistory() {
                try {
                    const response = await fetch('/pengembalian-mandiri/history?limit=5');
                    const data = await response.json();

                    if (data.status === 'success') {
                        this.displayHistory(data.data);
                    }
                } catch (error) {
                    console.error('Error loading history:', error);
                }
            }

            displayHistory(historyData) {
                const historyList = document.getElementById('historyList');

                if (historyData.length === 0) {
                    historyList.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada riwayat pengembalian</p>
                        </div>
                    `;
                    return;
                }

                historyList.innerHTML = historyData.map(item => `
                    <div class="history-item fade-in">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${item.Title || 'Judul tidak tersedia'}</h6>
                                <p class="mb-1 text-muted">${item.Author || 'Pengarang tidak tersedia'}</p>
                                <small class="text-muted">Barcode: ${item.NomorBarcode}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">${this.formatDate(item.ActualReturn)}</small>
                                ${item.LateDays > 0 ? `<br><span class="status-badge status-danger">Terlambat ${item.LateDays} hari</span>` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            formatDate(dateString) {
                if (!dateString) return '-';

                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
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

        // Initialize app when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new SelfReturnApp();
        });
    </script>


<?= $this->endsection() ?>
