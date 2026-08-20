<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

    <style>
    :root {
        --primary-color: #1B3878;
        --primary-dark: #142a5c;
        --primary-light: #dbe4f3;
        --danger-color: #dc3545;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        padding-top: 90px;
    }

    /* Card */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* Animation */
    .success-animation {
        animation: bounceIn 0.8s ease-in-out;
    }

    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Return Summary */
    .return-summary {
        background: linear-gradient(135deg, #dbe4f3, #c9d6ee);
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
        border-left: 5px solid var(--primary-color);
    }

    /* Book Item */
    .book-item {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin: 10px 0;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .book-item.overdue {
        border-left-color: var(--danger-color);
    }

    /* Receipt */
    .receipt-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        border: 2px dashed #dee2e6;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
    }

    /* Override Bootstrap Colors */
    .text-success {
        color: var(--primary-color) !important;
    }

    .bg-success {
        background-color: var(--primary-color) !important;
    }

    .border-success {
        border-color: var(--primary-color) !important;
    }

    .alert-success {
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-left: 5px solid var(--primary-color);
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-left: 5px solid #ffc107;
    }

    /* Badge */
    .badge.bg-primary {
        background-color: var(--primary-color) !important;
    }

    .badge.bg-danger {
        background-color: var(--danger-color) !important;
    }

    /* Link hover */
    a {
        color: var(--primary-color);
    }

    a:hover {
        color: var(--primary-dark);
    }

    /* Print */
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; }
    }

    /* Mobile */
    @media (max-width: 768px) {
        .container { padding: 10px; }
    }
</style>

    <div class="container py-4">
        <!-- Header -->
        <div class="text-center mb-4 no-print">
            <h1 class="display-4 text-success mb-2">
                <i class="fas fa-check-circle me-3"></i>Pengembalian Berhasil!
            </h1>
            <p class="lead text-muted">Transaksi pengembalian telah berhasil diproses</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Success Card -->
                <div class="card border-success success-animation">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Transaksi Berhasil
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Success Icon -->
                        <div class="text-center mb-4 no-print">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>

                        <!-- Return Summary -->
                        <div class="return-summary">
                            <h4 class="text-success mb-4 text-center">
                                <i class="fas fa-receipt me-2"></i>
                                Struk Pengembalian
                            </h4>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Data Anggota:</h6>
                                    <p class="mb-1"><strong>Nomor Anggota:</strong> <?= esc($member->MemberNo ?? '-') ?></p>
                                    <p class="mb-1"><strong>Nama:</strong> <?= esc($member->Fullname ?? '-') ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= esc($member->Email ?: '-') ?></p>
                                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($member->Phone ?: '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Data Pengembalian:</h6>
                                    <p class="mb-1"><strong>Tanggal Kembali:</strong> <?= date('d/m/Y H:i', strtotime($return_date)) ?></p>
                                    <p class="mb-1"><strong>Jumlah Buku:</strong> <?= count($items) ?> buku</p>
                                    <p class="mb-1"><strong>Buku Terlambat:</strong> <?= $late_count ?> buku</p>
                                </div>
                            </div>

                            <?php if ($late_count > 0): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong><?= $late_count ?> buku dikembalikan terlambat</strong><br>
                                <small>Silakan hubungi petugas perpustakaan untuk informasi denda keterlambatan</small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Books List -->
                        <?php if (!empty($items)): ?>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-books me-2"></i>
                                Daftar Buku yang Dikembalikan:
                            </h5>

                            <?php foreach ($items as $index => $item): ?>
                            <?php $isLate = ($item->LateDays ?? 0) > 0; ?>
                            <div class="book-item <?= $isLate ? 'overdue' : '' ?>">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <span class="badge <?= $isLate ? 'bg-danger' : 'bg-primary' ?>"><?= $index + 1 ?></span>
                                    </div>
                                    <div class="col-md-11">
                                        <h6 class="mb-2 text-primary"><?= esc($item->Title) ?></h6>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p class="mb-1"><small><strong>Pengarang:</strong> <?= esc($item->Author ?: 'Tidak diketahui') ?></small></p>
                                                <p class="mb-1"><small><strong>Barcode:</strong> <?= esc($item->NomorBarcode) ?></small></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="mb-1"><small><strong>No. Panggil:</strong> <?= esc($item->CallNumber ?: '-') ?></small></p>
                                                <p class="mb-1"><small><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($item->DueDate)) ?></small></p>
                                            </div>
                                        </div>
                                        <?php if ($isLate): ?>
                                        <span class="badge bg-danger mt-1"><i class="fas fa-clock me-1"></i>Terlambat <?= $item->LateDays ?> hari</span>
                                        <?php else: ?>
                                        <span class="badge bg-primary mt-1"><i class="fas fa-check me-1"></i>Tepat Waktu</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Receipt Section -->
                        <div class="receipt-section">
                            <div class="text-center">
                                <h6 class="text-muted mb-3">BUKTI PENGEMBALIAN</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah Buku</small><br>
                                        <strong><?= count($items) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Tanggal</small><br>
                                        <strong><?= date('d/m/Y', strtotime($return_date)) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Terlambat</small><br>
                                        <strong><?= $late_count ?> buku</strong>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <small class="text-muted">
                                    Simpan struk ini sebagai bukti pengembalian<br>
                                    Dicetak pada: <?= date('d/m/Y H:i:s') ?>
                                </small>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <?php if ($late_count > 0): ?>
                        <div class="alert alert-warning mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Penting:
                            </h6>
                            <ul class="mb-0">
                                <li>Terdapat buku yang dikembalikan melewati batas waktu peminjaman</li>
                                <li>Silakan hubungi petugas perpustakaan untuk penyelesaian denda keterlambatan</li>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4 no-print">
                            <button type="button" class="btn btn-success btn-lg me-3" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                Cetak Struk
                            </button>
                            <a href="<?= base_url('pengembalian-mandiri') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-undo me-2"></i>
                                Pengembalian Baru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4 no-print">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-book-open text-primary fa-2x mb-3"></i>
                                <h6>Peminjaman Mandiri</h6>
                                <p class="small text-muted">Pinjam buku secara mandiri</p>
                                <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ke Peminjaman
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-search text-primary fa-2x mb-3"></i>
                                <h6>Katalog Online</h6>
                                <p class="small text-muted">Cari koleksi perpustakaan</p>
                                <a href="<?= base_url('opac') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ke OPAC
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-4 no-print">
                    <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Print styling
        window.addEventListener('beforeprint', function() {
            document.title = 'Struk Pengembalian - <?= esc($member->MemberNo ?? '') ?>';
        });

        // Countdown for auto-redirect
        let countdown = 300; // 5 minutes
        const countdownInterval = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?= base_url('pengembalian-mandiri') ?>';
            }
        }, 1000);

        // Clear countdown if user interacts
        document.addEventListener('click', function() {
            clearInterval(countdownInterval);
        });
    </script>


<?= $this->endSection('content') ?>
