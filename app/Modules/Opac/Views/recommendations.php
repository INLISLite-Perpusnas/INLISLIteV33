<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .book-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .book-cover {
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        .metrics-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .metric-item {
            text-align: center;
            padding: 1rem;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .cold-start-badge {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 text-center mb-4">
                    <i class="fas fa-magic me-3"></i>Rekomendasi Buku1
                </h1>
                
                <!-- Search Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('opac/recommendations') ?>">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <label for="member_no" class="form-label">Nomor Anggota</label>
                                    <input type="text" class="form-control" id="member_no" name="member_no" 
                                           value="<?= esc($member_no) ?>" placeholder="Masukkan nomor anggota">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-2"></i>Cari Rekomendasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (!empty($recommendations)): ?>
                    <!-- Metrics Display -->
                    <?php if ($metrics && !$is_cold_start): ?>
                    <div class="card metrics-card shadow-lg mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">
                                <i class="fas fa-chart-line me-2"></i>Metrik Evaluasi Sistem Rekomendasi
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <div class="metric-value"><?= number_format($metrics['precision'] * 100, 1) ?>%</div>
                                        <div class="metric-label">Precision</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <div class="metric-value"><?= number_format($metrics['recall'] * 100, 1) ?>%</div>
                                        <div class="metric-label">Recall</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <div class="metric-value"><?= number_format($metrics['accuracy'] * 100, 1) ?>%</div>
                                        <div class="metric-label">Accuracy</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <div class="metric-value"><?= number_format($metrics['ndcg'] * 100, 1) ?>%</div>
                                        <div class="metric-label">NDCG</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Cold Start Badge -->
                    <?php if ($is_cold_start): ?>
                    <div class="alert cold-start-badge text-center mb-4">
                        <h5><i class="fas fa-star me-2"></i>Rekomendasi Buku Populer</h5>
                        <p class="mb-0">Karena Anda belum memiliki riwayat peminjaman, berikut adalah buku-buku populer yang mungkin menarik untuk Anda.</p>
                    </div>
                    <?php endif; ?>

                    <!-- Recommendations Grid -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h3>
                                <i class="fas fa-books me-2"></i>
                                <?= $is_cold_start ? 'Buku Populer' : 'Rekomendasi untuk Anda' ?>
                                <span class="badge bg-primary"><?= count($recommendations) ?> buku</span>
                            </h3>
                        </div>
                        
                        <?php foreach ($recommendations as $book): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card book-card shadow-sm h-100">
                                <div class="position-relative">
                                    <?php if (!empty($book['CoverURL'])): ?>
                                        <img src="<?= esc($book['CoverURL']) ?>" class="card-img-top book-cover" alt="<?= esc($book['Title']) ?>">
                                    <?php else: ?>
                                        <div class="book-cover d-flex align-items-center justify-content-center bg-light">
                                            <i class="fas fa-book fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($is_cold_start && isset($book['LoanCount'])): ?>
                                    <span class="position-absolute top-0 end-0 badge bg-warning m-2">
                                        <i class="fas fa-fire me-1"></i><?= $book['LoanCount'] ?> peminjaman
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-primary">
                                        <a href="<?= base_url('opac/detail/' . $book['ID']) ?>" class="text-decoration-none">
                                            <?= esc($book['Title']) ?>
                                        </a>
                                    </h6>
                                    
                                    <?php if (!empty($book['Author'])): ?>
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i><?= esc($book['Author']) ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($book['Subject'])): ?>
                                    <p class="card-text small mb-2">
                                        <i class="fas fa-tag me-1"></i>
                                        <span class="badge bg-light text-dark"><?= esc($book['Subject']) ?></span>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <a href="<?= base_url('opac/detail/' . $book['ID']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- API Example -->
                    <div class="card mt-5">
                        <div class="card-header">
                            <h5><i class="fas fa-code me-2"></i>API Endpoint</h5>
                        </div>
                        <div class="card-body">
                            <p>Anda juga dapat mengakses rekomendasi melalui API:</p>
                            <code>GET <?= base_url('opac/getRecommendations?member_no=' . $member_no) ?></code>
                            <br><br>
                            <button class="btn btn-sm btn-secondary" onclick="testAPI()">
                                <i class="fas fa-play me-1"></i>Test API
                            </button>
                            <div id="api-result" class="mt-3" style="display:none;">
                                <h6>API Response:</h6>
                                <pre id="api-response" class="bg-light p-3 rounded"></pre>
                            </div>
                        </div>
                    </div>

                <?php elseif ($member_no): ?>
                    <div class="alert alert-warning text-center">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Tidak Ada Rekomendasi</h5>
                        <p class="mb-0">Nomor anggota tidak ditemukan atau terjadi kesalahan dalam menghasilkan rekomendasi.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h5><i class="fas fa-info-circle me-2"></i>Mulai Pencarian</h5>
                        <p class="mb-0">Masukkan nomor anggota di atas untuk mendapatkan rekomendasi buku yang dipersonalisasi.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function testAPI() {
            const memberNo = '<?= esc($member_no) ?>';
            if (!memberNo) {
                alert('Masukkan nomor anggota terlebih dahulu');
                return;
            }

            fetch(`<?= base_url('opac/getRecommendations') ?>?member_no=${memberNo}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('api-result').style.display = 'block';
                    document.getElementById('api-response').textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    document.getElementById('api-result').style.display = 'block';
                    document.getElementById('api-response').textContent = 'Error: ' + error.message;
                });
        }
    </script>
</body>
</html>