<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #1B3878;
        --primary-dark: #142a5c;
        --primary-light: #dbe4f3;
        --secondary-color: #1B3878;
        --success-color: #1B3878;
        --danger-color: #dc3545;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        padding-top: 90px;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }

    .btn-primary,
    .btn-success {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        font-size: 18px;
        text-align: center;
        letter-spacing: 1px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(27, 56, 120, 0.25);
    }

    .scanner-input {
        font-family: 'Courier New', monospace;
        font-size: 20px;
        font-weight: bold;
    }

    .member-card {
        background: linear-gradient(135deg, var(--primary-light), #c9d6ee);
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
        border-left: 5px solid var(--primary-color);
    }

    .book-item {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin: 10px 0;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .step-indicator {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .current-step {
        color: var(--primary-color);
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Override Bootstrap biar full biru */
    .text-primary,
    .text-success {
        color: var(--primary-color) !important;
    }

    .bg-primary,
    .bg-success {
        background-color: var(--primary-color) !important;
    }

    .badge.bg-primary {
        background-color: var(--primary-color) !important;
    }

    .alert-success {
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-left: 5px solid var(--primary-color);
    }

    .alert-info {
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-left: 5px solid var(--primary-color);
    }

    .text-warning {
        color: var(--primary-color) !important;
    }

    .text-success {
        color: var(--primary-color) !important;
    }

    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .form-control {
            font-size: 16px;
        }
    }
</style>

<div class="container py-4">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="display-4 text-primary mb-2">
                <i class="fas fa-book-open me-3"></i>Peminjaman Mandiri
            </h1>
            <p class="lead text-muted">Sistem Peminjaman Buku Otomatis</p>
            <?php if(isset($locationData) && $locationData): ?>
            <div class="alert alert-info">
                <i class="fas fa-map-marker-alt me-2"></i>
                Lokasi: <strong><?= esc($locationData->Name) ?></strong> - <?= esc($locationData->Branch_name) ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Alerts -->
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= esc($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= esc($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Step Indicator -->
        <div class="step-indicator text-center">
            <div class="current-step">
                <?php if(!$memberData): ?>
                    <i class="fas fa-user-check me-2"></i>Langkah 1: Masukkan Nomor Anggota
                <?php elseif(empty($selectedBooks)): ?>
                    <i class="fas fa-barcode me-2"></i>Langkah 2: Scan Barcode Buku
                <?php else: ?>
                    <i class="fas fa-check-circle me-2"></i>Langkah 3: Konfirmasi Peminjaman
                <?php endif; ?>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if(!$memberData): ?>
                    <!-- Step 1: Member Input -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Validasi Anggota
                            </h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-id-card text-primary mb-3" style="font-size: 3rem;"></i>
                                <p class="lead">Scan atau ketik nomor anggota Anda</p>
                            </div>
                            
                            <form action="<?= base_url('peminjaman-mandiri') ?>" method="GET">
                                <div class="mb-4">
                                    <input type="text" 
                                           class="form-control form-control-lg scanner-input" 
                                           name="MemberNo" 
                                           placeholder="Nomor Anggota"
                                           value="<?= esc($memberNo ?? '') ?>"
                                           autofocus
                                           autocomplete="off"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>
                                    Validasi Anggota
                                </button>
                            </form>

                            <div class="mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Gunakan scanner barcode atau ketik manual nomor anggota
                                </small>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Step 2: Member Info & Book Input -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-barcode me-2"></i>
                                Scan Barcode Buku
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Member Info -->
                            <div class="member-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="text-success mb-3">
                                            <i class="fas fa-user-check me-2"></i>
                                            Anggota Valid
                                        </h5>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p><strong>Nomor:</strong> <?= esc($memberData['member_no']) ?></p>
                                                <p><strong>Nama:</strong> <?= esc($memberData['fullname']) ?></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p><strong>Sedang Dipinjam:</strong> <?= $memberData['current_loans'] ?> buku</p>
                                                <p><strong>Sisa Kuota:</strong> 
                                                    <span class="<?= $memberData['remaining_loans'] > 0 ? 'text-success' : 'text-danger' ?>">
                                                        <?= $memberData['remaining_loans'] ?>
                                                    </span> buku
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-success fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($memberData['remaining_loans'] > 0): ?>
                                <!-- Book Scanner -->
                                <div class="text-center mb-4">
                                    <i class="fas fa-barcode text-primary mb-3" style="font-size: 3rem;"></i>
                                    <p class="lead">Scan barcode buku yang ingin dipinjam</p>
                                </div>
                                
                                <form action="<?= base_url('peminjaman-mandiri') ?>" method="GET">
                                    <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                                    <div class="mb-4">
                                        <input type="text" 
                                               class="form-control form-control-lg scanner-input" 
                                               name="NomorBarcode" 
                                               placeholder="Barcode Buku"
                                               value=""
                                               autocomplete="off"
                                               autofocus
                                               required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-plus me-2"></i>
                                            Tambah Buku
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>

                            <!-- Books List -->
                            <?php if(!empty($selectedBooks)): ?>
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Daftar Buku yang Akan Dipinjam</h5>
                                    <span class="badge bg-primary"><?= count($selectedBooks) ?> Buku</span>
                                </div>
                                
                                <?php foreach($selectedBooks as $index => $book): ?>
                                    <div class="book-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <h6 class="mb-2 text-primary"><?= esc($book['title']) ?></h6>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><small><strong>Pengarang:</strong> <?= esc($book['author']) ?></small></p>
                                                        <p class="mb-1"><small><strong>Penerbit:</strong> <?= esc($book['publisher']) ?> (<?= esc($book['publish_year']) ?>)</small></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><small><strong>Barcode:</strong> <?= esc($book['barcode']) ?></small></p>
                                                        <p class="mb-1"><small><strong>No. Panggil:</strong> <?= esc($book['call_number']) ?></small></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <a href="<?= base_url('peminjaman-mandiri/remove-book?MemberNo=' . urlencode($memberData['member_no']) . '&index=' . $index) ?>"
                                                   class="btn btn-outline-danger btn-sm btn-hapus-buku">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="text-center mt-4">
                                    <form action="<?= base_url('peminjaman-mandiri/process-loan') ?>" method="POST" id="form-proses-peminjaman">
                                        <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check me-2"></i>
                                            Proses Peminjaman (<?= count($selectedBooks) ?> Buku)
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <!-- New Member Button -->
                            <div class="text-center mt-4">
                                <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Anggota Baru
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Petunjuk Penggunaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-id-card text-primary fa-2x mb-3"></i>
                                    <h6>Langkah 1</h6>
                                    <p class="small">Scan atau ketik nomor anggota perpustakaan Anda</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-barcode text-primary fa-2x mb-3"></i>
                                    <h6>Langkah 2</h6>
                                    <p class="small">Scan barcode pada setiap buku yang ingin dipinjam</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-primary fa-2x mb-3"></i>
                                    <h6>Langkah 3</h6>
                                    <p class="small">Konfirmasi peminjaman dan cetak struk sebagai bukti</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Tips Penggunaan:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Pastikan barcode dalam kondisi baik dan tidak rusak</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pegang scanner tegak lurus dengan barcode</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Tunggu bunyi "beep" setelah scan berhasil</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Perhatian:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Simpan struk peminjaman dengan baik</li>
                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Kembalikan buku sebelum tanggal jatuh tempo</li>
                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Hubungi petugas jika ada kendala</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
        // Auto-focus on input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[autofocus]');
            if (inputs.length > 0) {
                inputs[0].focus();
                inputs[0].select();
            }
        });

        // Handle Enter key for form submission
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                if (target.tagName === 'INPUT' && target.type === 'text') {
                    const form = target.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            }
        });

        // Auto-clear barcode input after successful addition
        <?php if(!empty($successMessage) && $memberData): ?>
            setTimeout(function() {
                const barcodeInput = document.querySelector('input[name="NomorBarcode"]');
                if (barcodeInput) {
                    barcodeInput.focus();
                }
            }, 100);
        <?php endif; ?>

        // Format barcode input (uppercase and remove spaces)
        document.addEventListener('input', function(e) {
            if (e.target.name === 'NomorBarcode' || e.target.name === 'MemberNo') {
                e.target.value = e.target.value.toUpperCase().replace(/\s/g, '');
            }
        });

        // SweetAlert2 — konfirmasi hapus buku
        document.querySelectorAll('.btn-hapus-buku').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                Swal.fire({
                    title: 'Hapus Buku?',
                    text: 'Buku ini akan dihapus dari daftar peminjaman.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });

        // SweetAlert2 — konfirmasi proses peminjaman
        const formProses = document.getElementById('form-proses-peminjaman');
        if (formProses) {
            formProses.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Proses Peminjaman?',
                    text: 'Yakin ingin memproses peminjaman ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Proses',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // Show loading state on form submission
        document.addEventListener('submit', function(e) {
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                
                // Re-enable after 5 seconds in case of network issues
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || submitBtn.innerHTML.replace(/Memproses.../, '');
                }, 5000);
            }
        });
    </script>
<?= $this->endSection('script') ?>