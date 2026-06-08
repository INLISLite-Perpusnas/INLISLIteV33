<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Berhasil - Perpustakaan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #039550;
            --success-color: #28a745;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .success-animation {
            animation: bounceIn 0.8s ease-in-out;
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }

        .loan-summary {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            border-left: 5px solid var(--success-color);
        }

        .book-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .receipt-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            border: 2px dashed #dee2e6;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #027a42);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #1e7e34);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .card { box-shadow: none !important; }
        }

        @media (max-width: 768px) {
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="text-center mb-4 no-print">
            <h1 class="display-4 text-success mb-2">
                <i class="fas fa-check-circle me-3"></i>Peminjaman Berhasil!
            </h1>
            <p class="lead text-muted">Transaksi peminjaman telah berhasil diproses</p>
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
                        
                        <!-- Loan Summary -->
                        <div class="loan-summary">
                            <h4 class="text-success mb-4 text-center">
                                <i class="fas fa-receipt me-2"></i>
                                Struk Peminjaman
                            </h4>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Data Anggota:</h6>
                                    <p class="mb-1"><strong>Nomor Anggota:</strong> <?= esc($loan->MemberNo) ?></p>
                                    <p class="mb-1"><strong>Nama:</strong> <?= esc($loan->Fullname) ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= esc($loan->Email ?: '-') ?></p>
                                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($loan->Phone ?: '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Data Peminjaman:</h6>
                                    <p class="mb-1"><strong>ID Peminjaman:</strong> <?= esc($loan->ID) ?></p>
                                    <p class="mb-1"><strong>Tanggal Pinjam:</strong> <?= date('d/m/Y H:i', strtotime($loan->CreateDate)) ?></p>
                                    <p class="mb-1"><strong>Jumlah Buku:</strong> <?= $loan->CollectionCount ?> buku</p>
                                    <p class="mb-1"><strong>Masa Peminjaman:</strong> 7 hari</p>
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
                                <i class="fas fa-calendar-exclamation me-2"></i>
                                <strong>Tanggal Kembali: <?= date('d/m/Y', strtotime($firstDueDate)) ?></strong><br>
                                <small>Harap kembalikan buku sebelum tanggal jatuh tempo untuk menghindari denda</small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Books List -->
                        <?php if (!empty($loanItems)): ?>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-books me-2"></i>
                                Daftar Buku yang Dipinjam:
                            </h5>
                            
                            <?php foreach($loanItems as $index => $item): ?>
                            <div class="book-item">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <span class="badge bg-primary"><?= $index + 1 ?></span>
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
                                                <p class="mb-1"><small><strong>Tanggal Kembali:</strong> <span class="text-danger"><?= date('d/m/Y', strtotime($item->DueDate)) ?></span></small></p>
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
                                <h6 class="text-muted mb-3">BUKTI PEMINJAMAN</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">ID Transaksi</small><br>
                                        <strong><?= esc($loan->ID) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Tanggal</small><br>
                                        <strong><?= date('d/m/Y', strtotime($loan->CreateDate)) ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah</small><br>
                                        <strong><?= $loan->CollectionCount ?> Buku</strong>
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
                                Informasi Penting:
                            </h6>
                            <ul class="mb-0">
                                <li>Simpan struk ini dengan baik sebagai bukti peminjaman</li>
                                <li>Buku harus dikembalikan sebelum tanggal jatuh tempo</li>
                                <li>Keterlambatan pengembalian akan dikenakan denda</li>
                                <li>Hubungi petugas perpustakaan jika ada kendala</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4 no-print">
                            <button type="button" class="btn btn-success btn-lg me-3" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                Cetak Struk
                            </button>
                            <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Peminjaman Baru
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
                                <h6>Pengembalian Mandiri</h6>
                                <p class="small text-muted">Kembalikan buku secara mandiri</p>
                                <a href="<?= base_url('pengembalian-mandiri') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ke Pengembalian
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

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
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
</body>
</html>