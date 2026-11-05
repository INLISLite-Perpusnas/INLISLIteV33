<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-selfservice.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-selfservice.css') ?>">

<div class="ss-ina">
<div class="container">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="ss-page-title">Peminjaman Mandiri</h1>
            <p class="ss-page-subtitle mb-3">Sistem peminjaman buku otomatis</p>
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
                    <i class="fas fa-user-check me-2"></i>Langkah 1: Masukkan nomor anggota
                <?php elseif(empty($selectedBooks)): ?>
                    <i class="fas fa-barcode me-2"></i>Langkah 2: Scan barcode buku
                <?php else: ?>
                    <i class="fas fa-check-circle me-2"></i>Langkah 3: Konfirmasi peminjaman
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
                                Validasi anggota
                            </h4>
                        </div>
                        <div class="card-body">
                            
                            <form action="<?= base_url('peminjaman-mandiri') ?>" method="GET">
                                <div class="ss-field">
                                    <label for="MemberNo">Nomor anggota</label>
                                    <input type="text"
                                           class="form-control form-control-lg scanner-input"
                                           id="MemberNo"
                                           name="MemberNo"
                                           aria-describedby="MemberNoHelp"
                                           placeholder="Scan atau ketik nomor anggota"
                                           value="<?= esc($memberNo ?? '') ?>"
                                           autofocus
                                           autocomplete="off"
                                           required>
                                    <small class="ss-help" id="MemberNoHelp">Gunakan scanner barcode atau ketik manual nomor anggota</small>
                                </div>
                                <div class="ss-actions">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>
                                        Validasi anggota
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Step 2: Member Info & Book Input -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-barcode me-2"></i>
                                Scan barcode buku
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Member Info -->
                            <div class="member-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="text-success mb-3">
                                            <i class="fas fa-user-check me-2"></i>
                                            Anggota valid
                                        </h5>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p><strong>Nomor:</strong> <?= esc($memberData['member_no']) ?></p>
                                                <p><strong>Nama:</strong> <?= esc($memberData['fullname']) ?></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p><strong>Sedang dipinjam:</strong> <?= $memberData['current_loans'] ?> buku</p>
                                                <p><strong>Sisa kuota:</strong> 
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
                                
                                <form action="<?= base_url('peminjaman-mandiri') ?>" method="GET">
                                    <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                                    <div class="ss-field">
                                        <label for="NomorBarcode">Barcode buku</label>
                                        <input type="text"
                                               class="form-control form-control-lg scanner-input"
                                               id="NomorBarcode"
                                               name="NomorBarcode"
                                               placeholder="Scan barcode buku"
                                               value=""
                                               autocomplete="off"
                                               autofocus
                                               required>
                                    </div>
                                    <div class="ss-actions">
                                        <?php // Satu aksi utama per layar: setelah ada buku, aksi utama pindah ke "Proses peminjaman" ?>
                                        <button type="submit" class="btn <?= empty($selectedBooks) ? 'btn-primary' : 'btn-outline-primary' ?> btn-lg">
                                            <i class="fas fa-plus me-2"></i>
                                            Tambah buku
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>

                            <!-- Books List -->
                            <?php if(!empty($selectedBooks)): ?>
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Daftar buku yang akan dipinjam</h5>
                                    <span class="badge bg-primary"><?= count($selectedBooks) ?> buku</span>
                                </div>
                                
                                <?php foreach($selectedBooks as $index => $book): ?>
                                    <div class="book-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <h6 class="mb-2"><?= esc($book['title']) ?></h6>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><small><strong>Pengarang:</strong> <?= esc($book['author']) ?></small></p>
                                                        <p class="mb-1"><small><strong>Penerbit:</strong> <?= esc($book['publisher']) ?> (<?= esc($book['publish_year']) ?>)</small></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><small><strong>Barcode:</strong> <?= esc($book['barcode']) ?></small></p>
                                                        <p class="mb-1"><small><strong>No. panggil:</strong> <?= esc($book['call_number']) ?></small></p>
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

                                <div class="ss-actions mt-4">
                                    <form action="<?= base_url('peminjaman-mandiri/process-loan') ?>" method="POST" id="form-proses-peminjaman">
                                        <input type="hidden" name="MemberNo" value="<?= esc($memberData['member_no']) ?>">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-check me-2"></i>
                                            Proses peminjaman (<?= count($selectedBooks) ?> buku)
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <!-- New Member Button -->
                            <div class="ss-actions mt-3">
                                <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Anggota baru
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Petunjuk penggunaan
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
                                <h6 class="mb-3">Tips penggunaan:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Pastikan barcode dalam kondisi baik dan tidak rusak</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pegang scanner tegak lurus dengan barcode</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Tunggu bunyi "beep" setelah scan berhasil</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Perhatian:</h6>
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
                    title: 'Hapus buku?',
                    text: 'Buku ini akan dihapus dari daftar peminjaman.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c62828',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.value) {
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
                    title: 'Proses peminjaman?',
                    text: 'Yakin ingin memproses peminjaman ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2d659e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, proses',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.value) {
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