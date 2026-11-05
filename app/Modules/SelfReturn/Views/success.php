<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/ina-selfservice.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-selfservice.css') ?>">

    <div class="ss-ina">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-4 no-print">
            <i class="fas fa-check-circle ss-success-icon" aria-hidden="true"></i>
            <h1 class="ss-page-title">Pengembalian Berhasil</h1>
            <p class="ss-page-subtitle">Transaksi pengembalian telah berhasil diproses</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Success Card -->
                <div class="card success-animation">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Transaksi berhasil
                        </h4>
                    </div>
                    <div class="card-body">

                        <!-- Return Summary -->
                        <div class="return-summary">
                            <h4 class="mb-4 text-center">
                                <i class="fas fa-receipt text-primary me-2"></i>
                                Struk pengembalian
                            </h4>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Data anggota:</h6>
                                    <p class="mb-1"><strong>Nomor anggota:</strong> <?= esc($member->MemberNo ?? '-') ?></p>
                                    <p class="mb-1"><strong>Nama:</strong> <?= esc($member->Fullname ?? '-') ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= esc($member->Email ?: '-') ?></p>
                                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($member->Phone ?: '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Data pengembalian:</h6>
                                    <p class="mb-1"><strong>Tanggal kembali:</strong> <?= date('d/m/Y H:i', strtotime($return_date)) ?></p>
                                    <p class="mb-1"><strong>Jumlah buku:</strong> <?= count($items) ?> buku</p>
                                    <p class="mb-1"><strong>Buku terlambat:</strong> <?= $late_count ?> buku</p>
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
                            <h5 class="mb-3">
                                <i class="fas fa-book text-primary me-2"></i>
                                Daftar buku yang dikembalikan:
                            </h5>

                            <?php foreach ($items as $index => $item): ?>
                            <?php $isLate = ($item->LateDays ?? 0) > 0; ?>
                            <div class="book-item <?= $isLate ? 'overdue' : '' ?>">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <span class="badge <?= $isLate ? 'bg-danger' : 'bg-primary' ?>"><?= $index + 1 ?></span>
                                    </div>
                                    <div class="col-md-11">
                                        <h6 class="mb-2"><?= esc($item->Title) ?></h6>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p class="mb-1"><small><strong>Pengarang:</strong> <?= esc($item->Author ?: 'Tidak diketahui') ?></small></p>
                                                <p class="mb-1"><small><strong>Barcode:</strong> <?= esc($item->NomorBarcode) ?></small></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="mb-1"><small><strong>No. panggil:</strong> <?= esc($item->CallNumber ?: '-') ?></small></p>
                                                <p class="mb-1"><small><strong>Jatuh tempo:</strong> <?= date('d/m/Y', strtotime($item->DueDate)) ?></small></p>
                                            </div>
                                        </div>
                                        <?php if ($isLate): ?>
                                        <span class="badge bg-danger mt-1"><i class="fas fa-clock me-1"></i>Terlambat <?= $item->LateDays ?> hari</span>
                                        <?php else: ?>
                                        <span class="badge bg-primary mt-1"><i class="fas fa-check me-1"></i>Tepat waktu</span>
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
                                <h6 class="text-muted mb-3">Bukti pengembalian</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah buku</small><br>
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
                                Informasi penting:
                            </h6>
                            <ul class="mb-0">
                                <li>Terdapat buku yang dikembalikan melewati batas waktu peminjaman</li>
                                <li>Silakan hubungi petugas perpustakaan untuk penyelesaian denda keterlambatan</li>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="ss-actions mt-4 no-print">
                            <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                Cetak struk
                            </button>
                            <a href="<?= base_url('pengembalian-mandiri') ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-undo me-2"></i>
                                Pengembalian baru
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
                                <h6>Peminjaman mandiri</h6>
                                <p class="small text-muted">Pinjam buku secara mandiri</p>
                                <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ke peminjaman
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-search text-primary fa-2x mb-3"></i>
                                <h6>Katalog online</h6>
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
                        Kembali ke beranda
                    </a>
                </div>
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
