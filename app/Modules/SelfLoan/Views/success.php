<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>


<link rel="stylesheet" href="<?= base_url('assets/css/ina-selfservice.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-selfservice.css') ?>">

    <div class="ss-ina">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-4 no-print">
            <i class="fas fa-check-circle ss-success-icon" aria-hidden="true"></i>
            <h1 class="ss-page-title">Peminjaman Berhasil</h1>
            <p class="ss-page-subtitle">Transaksi peminjaman telah berhasil diproses</p>
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
                        
                        <!-- Loan Summary -->
                        <div class="loan-summary">
                            <h4 class="mb-4 text-center">
                                <i class="fas fa-receipt text-primary me-2"></i>
                                Struk peminjaman
                            </h4>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Data anggota:</h6>
                                    <p class="mb-1"><strong>Nomor anggota:</strong> <?= esc($loan->MemberNo) ?></p>
                                    <p class="mb-1"><strong>Nama:</strong> <?= esc($loan->Fullname) ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= esc($loan->Email ?: '-') ?></p>
                                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($loan->Phone ?: '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Data peminjaman:</h6>
                                    <p class="mb-1"><strong>ID peminjaman:</strong> <?= esc($loan->ID) ?></p>
                                    <p class="mb-1"><strong>Tanggal pinjam:</strong> <?= date('d/m/Y H:i', strtotime($loan->CreateDate)) ?></p>
                                    <p class="mb-1"><strong>Jumlah buku:</strong> <?= $loan->CollectionCount ?> buku</p>
                                    <p class="mb-1"><strong>Masa peminjaman:</strong> 7 hari</p>
                                </div>
                            </div>

                            <!-- Due Date Warning -->
                            <?php 
                            $firstDueDate = '';
                            if (!empty($loanItems)) {
                                $firstDueDate = $loanItems[0]->DueDate;
                            }
                            ?>
                            <?php if($firstDueDate): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-calendar-day me-2"></i>
                                <strong>Tanggal kembali: <?= date('d/m/Y', strtotime($firstDueDate)) ?></strong><br>
                                <small>Harap kembalikan buku sebelum tanggal jatuh tempo untuk menghindari denda</small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Books List -->
                        <?php if (!empty($loanItems)): ?>
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-book text-primary me-2"></i>
                                Daftar buku yang dipinjam:
                            </h5>
                            
                            <?php foreach($loanItems as $index => $item): ?>
                            <div class="book-item">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <span class="badge bg-primary"><?= $index + 1 ?></span>
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
                                                <p class="mb-1"><small><strong>Tanggal kembali:</strong> <span class="text-danger"><?= date('d/m/Y', strtotime($item->DueDate)) ?></span></small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Receipt Section -->
                        <div class="receipt-section">
                            <div class="text-center">
                                <h6 class="text-muted mb-3">Bukti peminjaman</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">ID transaksi</small><br>
                                        <strong><?= esc($loan->ID) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Tanggal</small><br>
                                        <strong><?= date('d/m/Y', strtotime($loan->CreateDate)) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah</small><br>
                                        <strong><?= $loan->CollectionCount ?> buku</strong>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <small class="text-muted">
                                    Simpan struk ini sebagai bukti peminjaman<br>
                                    Dicetak pada: <?= date('d/m/Y H:i:s') ?>
                                </small>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi penting:
                            </h6>
                            <ul class="mb-0">
                                <li>Simpan struk ini dengan baik sebagai bukti peminjaman</li>
                                <li>Buku harus dikembalikan sebelum tanggal jatuh tempo</li>
                                <li>Keterlambatan pengembalian akan dikenakan denda</li>
                                <li>Hubungi petugas perpustakaan jika ada kendala</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="ss-actions mt-4 no-print">
                            <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                Cetak struk
                            </button>
                            <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Peminjaman baru
                            </a>
                        </div>

                        <!-- QR Code for Mobile -->
                        <div class="text-center mt-4 no-print">
                            <small class="text-muted">
                                <i class="fas fa-qrcode me-1"></i>
                                Scan QR code untuk melihat detail peminjaman di ponsel
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4 no-print">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-undo text-primary fa-2x mb-3"></i>
                                <h6>Pengembalian mandiri</h6>
                                <p class="small text-muted">Kembalikan buku secara mandiri</p>
                                <a href="<?= base_url('pengembalian-mandiri') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ke pengembalian
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
        // Auto-print after 3 seconds (optional)
        // setTimeout(function() {
        //     if (confirm('Cetak struk sekarang?')) {
        //         window.print();
        //     }
        // }, 3000);

        // Print styling
        window.addEventListener('beforeprint', function() {
            document.title = 'Struk Peminjaman - <?= esc($loan->ID) ?>';
        });

        // Countdown for auto-redirect (optional)
        let countdown = 300; // 5 minutes
        const countdownInterval = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?= base_url('peminjaman-mandiri') ?>';
            }
        }, 1000);

        // Clear countdown if user interacts
        document.addEventListener('click', function() {
            clearInterval(countdownInterval);
        });

        // Success animation delay
        setTimeout(function() {
            const successCard = document.querySelector('.success-animation');
            if (successCard) {
                successCard.style.transform = 'scale(1)';
            }
        }, 100);
    </script>


<?= $this->endSection('content') ?>