<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
        body {
            background: white;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .return-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            text-align: center;
            border: none;
        }
        
        .card-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 2rem;
        }
        
        .card-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .scanner-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .barcode-input {
            position: relative;
            margin-bottom: 20px;
        }
        
        .barcode-input input {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 15px 50px 15px 20px;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .barcode-input input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .barcode-input .scan-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #45a049, #4CAF50);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .btn-secondary {
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .book-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            border-left: 5px solid #4CAF50;
        }
        
        .book-info h5 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .book-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .book-detail strong {
            color: #34495e;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .history-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .history-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #4CAF50;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .loading i {
            font-size: 2rem;
            color: #4CAF50;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: #721c24;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

<div class="main-container">
        <div class="return-card">
            <div class="card-header">
                <h2><i class="fas fa-book-open"></i> Self Return</h2>
                <p>Sistem Pengembalian Buku Mandiri</p>
            </div>
            
            <div class="card-body">
                <!-- Scanner Section -->
                <div class="scanner-section">
                    <h4 class="mb-4">Scan atau Masukkan Barcode Buku</h4>
                    
                    <div class="barcode-input">
                        <input type="text" id="barcodeInput" class="form-control" 
                               placeholder="Masukkan atau scan barcode buku..." 
                               autocomplete="off" autofocus>
                        <i class="fas fa-barcode scan-icon"></i>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-center">
                        <button type="button" id="checkBookBtn" class="btn btn-secondary">
                            <i class="fas fa-search"></i> Cek Buku
                        </button>
                        <button type="button" id="returnBookBtn" class="btn btn-primary">
                            <i class="fas fa-undo"></i> Kembalikan Buku
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
                
                <!-- Book Info Section -->
                <div id="bookInfoSection" class="d-none">
                    <div class="book-info">
                        <h5><i class="fas fa-book"></i> Informasi Buku</h5>
                        <div id="bookDetails"></div>
                    </div>
                </div>
                
                <!-- Return History Section -->
                <div class="history-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><i class="fas fa-history"></i> Riwayat Pengembalian Terbaru</h5>
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


  <!-- Bootstrap JS -->
  <script>
        class SelfReturnApp {
            constructor() {
                this.init();
            }
            
            init() {
                this.bindEvents();
                this.focusInput();
                
                // Auto-check book when barcode is entered (for scanner)
                this.setupAutoScan();
            }
            
            bindEvents() {
                document.getElementById('checkBookBtn').addEventListener('click', () => this.checkBook());
                document.getElementById('returnBookBtn').addEventListener('click', () => this.returnBook());
                document.getElementById('refreshHistoryBtn').addEventListener('click', () => this.loadHistory());
                
                // Enter key handling
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
                this.hideBookInfo();
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
            
            showBookInfo(bookData) {
                const bookInfoSection = document.getElementById('bookInfoSection');
                const bookDetails = document.getElementById('bookDetails');
                
                let statusBadge = '';
                let statusClass = '';
                
                if (bookData.is_on_loan) {
                    if (bookData.is_overdue) {
                        statusBadge = `<span class="status-badge status-danger">Terlambat ${bookData.days_overdue} hari</span>`;
                    } else {
                        statusBadge = '<span class="status-badge status-warning">Sedang Dipinjam</span>';
                    }
                } else {
                    statusBadge = '<span class="status-badge status-success">Tersedia</span>';
                }
                
                bookDetails.innerHTML = `
                    <div class="book-detail">
                        <span><strong>Judul:</strong></span>
                        <span>${bookData.title || 'Tidak tersedia'}</span>
                    </div>
                    <div class="book-detail">
                        <span><strong>Pengarang:</strong></span>
                        <span>${bookData.author || 'Tidak tersedia'}</span>
                    </div>
                    <div class="book-detail">
                        <span><strong>Barcode:</strong></span>
                        <span>${bookData.barcode}</span>
                    </div>
                    <div class="book-detail">
                        <span><strong>Status:</strong></span>
                        <span>${statusBadge}</span>
                    </div>
                    ${bookData.is_on_loan ? `
                        <div class="book-detail">
                            <span><strong>Tanggal Pinjam:</strong></span>
                            <span>${this.formatDate(bookData.loan_date)}</span>
                        </div>
                        <div class="book-detail">
                            <span><strong>Tanggal Kembali:</strong></span>
                            <span>${this.formatDate(bookData.due_date)}</span>
                        </div>
                    ` : ''}
                `;
                
                bookInfoSection.classList.remove('d-none');
                bookInfoSection.classList.add('fade-in');
            }
            
            hideBookInfo() {
                document.getElementById('bookInfoSection').classList.add('d-none');
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
                        this.showBookInfo(data.data);
                        this.hideMessage();
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
            
            async returnBook() {
                const barcode = document.getElementById('barcodeInput').value.trim();
                
                if (!barcode) {
                    this.showMessage('Silakan masukkan barcode buku', 'error');
                    this.focusInput();
                    return;
                }
                
                this.showLoading();
                
                try {
                    const response = await fetch('/pengembalian-mandiri/process-return', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `nomorBarcode=${encodeURIComponent(barcode)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        let message = `Buku "${data.data.title}" berhasil dikembalikan!`;
                        if (data.data.is_late) {
                            message += ` (Terlambat ${data.data.late_days} hari)`;
                        }
                        
                        this.showMessage(message, 'success');
                        this.hideBookInfo();
                        this.clearInput();
                        this.loadHistory(); // Refresh history
                    } else {
                        this.showMessage(data.message, 'error');
                    }
                } catch (error) {
                    this.showMessage('Terjadi kesalahan saat memproses pengembalian', 'error');
                } finally {
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