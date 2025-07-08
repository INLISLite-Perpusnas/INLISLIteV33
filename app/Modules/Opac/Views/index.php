<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
    <style>
    /* Style dasar pagination */
    .pagination li a,
    .pagination li span {
        color: #007bff; /* Biru Bootstrap */
        border: 1px solid #007bff;
        padding: 5px 10px;
        margin: 2px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
    }

    /* Hover effect */
    .pagination li a:hover {
        background-color: #007bff;
        color: white;
    }

    /* Aktif / current page */
    .pagination li.active span,
    .pagination li.active a {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>

    <style>
        /* Improved Layout Styling */
        .main-container {
            max-width: 1400px;
            /* Increased container width */
            margin: 0 auto;
        }

        .catalog-section {
            padding-left: 0;
            padding-right: 15px;
        }

        .sidebar-section {
            padding-left: 15px;
            padding-right: 0;
        }

        /* Sidebar Styling */
        .sidebar-container {
            position: sticky;
            top: 20px;
            width: 100%;
        }

        .sidebar-card {
            border: none;
            transition: transform 0.2s ease;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-card:hover {
            transform: translateY(-2px);
        }

        .sidebar-card .card-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }

        .sidebar-card .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-card .card-body {
            padding: 20px;
        }

        /* Statistics Items */
        .stat-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 50%;
            margin-right: 15px;
        }

        .stat-icon i {
            font-size: 20px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .stat-section {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .stat-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .stat-section h6 {
            margin-bottom: 12px;
            font-weight: 600;
        }

        .stat-list {
            max-height: 220px;
            overflow-y: auto;
        }

        .stat-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .stat-list-item:last-child {
            border-bottom: none;
        }

        .stat-name {
            flex: 1;
            font-size: 0.9rem;
            color: #555;
            text-decoration: none;
            margin-right: 10px;
        }

        .stat-name:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .stat-list-item .badge {
            font-size: 0.75rem;
            min-width: 30px;
            text-align: center;
        }

        /* Catalog Card Styling */
        .catalog-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .catalog-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .catalog-card .card-header {
            background-color: #28a745;
            color: white;
            padding: 12px 15px;
        }

        .catalog-card .card-body {
            padding: 15px;
        }

        .book-cover-container {
            position: relative;
            margin-bottom: 10px;
        }

        .book-cover {
            max-width: 100px;
            max-height: 150px;
            width: 100%;
            object-fit: cover;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }

        .book-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 4px;
        }

        .book-cover-container:hover .book-overlay {
            opacity: 1;
            cursor: pointer;
        }

        /* Color variations for different sidebar sections */
        .bg-gradient-primary {
            background: linear-gradient(135deg,rgb(105, 162, 202) 0%,rgba(48, 76, 113, 0.69) 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #1e7e34) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #117a8b) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800) !important;
            color: #000 !important;
        }

        /* Hero Section */
        .hero-section {
            background:  linear-gradient(135deg,rgb(105, 162, 202) 0%,rgba(48, 76, 113, 0.69) 100%);
            color: white;
            padding: 60px 0;
        }

        .hero-section h1 {
            color: white !important;
        }

        .hero-section .lead {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Search Box Styling */
        .search-box .form-control,
        .search-box .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }

        .search-box .btn {
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
        }

        .recommendation-search .card {
            border-radius: 10px;
            overflow: hidden;
        }

        /* Content Section */
        .content-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .main-container {
                max-width: 100%;
                padding: 0 15px;
            }
        }

        @media (max-width: 991px) {
            .sidebar-container {
                position: static;
                margin-top: 30px;
            }

            .catalog-section,
            .sidebar-section {
                padding-left: 15px;
                padding-right: 15px;
            }
        }

        @media (max-width: 768px) {
            .book-cover-container {
                text-align: center;
                margin-bottom: 15px;
            }

            .catalog-card .row {
                flex-direction: column;
            }

            .catalog-card .col-4,
            .catalog-card .col-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .book-cover {
                max-width: 120px !important;
                max-height: 180px !important;
            }

            .stat-list {
                max-height: 150px;
            }
        }

        /* Scrollbar styling */
        .stat-list::-webkit-scrollbar {
            width: 6px;
        }

        .stat-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .stat-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .stat-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Animation for loading state */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .sidebar-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .sidebar-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .sidebar-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .sidebar-card:nth-child(4) {
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-book-reader me-3"></i>
                            Selamat Datang di OPAC
                        </h1>
                        <p class="lead">Online Public Access Catalog - Temukan koleksi perpustakaan dengan mudah</p>
                    </div>
  <!-- Recommendation Search Box -->
                <div class="recommendation-search mb-4">
                    <form method="GET" action="<?= base_url('opac') ?>" id="recommendationForm">
                        <?=csrf_field()?>
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-magic me-2"></i>Rekomendasi Personal
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="member_no" 
                                               placeholder="Masukkan nomor anggota untuk rekomendasi personal..." 
                                               value="<?= $member_no ?? '' ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-magic me-2"></i>Dapatkan Rekomendasi
                                        </button>
                                    </div>
                                </div>
                                <?php if (!empty($member_no)): ?>
                                <div class="mt-2">
                                    <a href="<?= base_url('opac') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Kembali ke Katalog Umum
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
                    <!-- Regular Search Box -->
                    <div class="search-box">
                        <form method="GET" action="#">
                            <?=csrf_field()?>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" name="search_by">
                                        <option value="">Semua Field</option>
                                        <option value="Title">Judul</option>
                                        <option value="Author">Pengarang</option>
                                        <option value="Subject">Subjek</option>
                                        <option value="Publisher">Penerbit</option>
                                        <option value="ISBN">ISBN</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-lg" name="search"
                                        placeholder="Masukkan kata kunci pencarian..." value="">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recommendations Section -->
    <?php if (!empty($member_no) && isset($recommendations)): ?>
        <section class="py-5">
            <div class="container">
                <!-- Metrics Display -->
                <?php if (isset($metrics) && !$is_cold_start): ?>
                    <div class="card metrics-card shadow-lg mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4 text-white">
                                <i class="fas fa-chart-line me-2"></i>Metrik Evaluasi Sistem Rekomendasi
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value text-white"><?= number_format($metrics['precision'] * 100, 1) ?>%</div>
                                        <div class="metric-label text-white-50">Precision</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value text-white"><?= number_format($metrics['recall'] * 100, 1) ?>%</div>
                                        <div class="metric-label text-white-50">Recall</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value text-white"><?= number_format($metrics['accuracy'] * 100, 1) ?>%</div>
                                        <div class="metric-label text-white-50">Accuracy</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value text-white"><?= number_format($metrics['ndcg'] * 100, 1) ?>%</div>
                                        <div class="metric-label text-white-50">NDCG</div>
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

                <div class="row">
                    <div class="col-12 mb-3">
                        <h3 style="color: #028548;">
                            <i class="fas fa-magic me-2"></i>
                            <?= $is_cold_start ? 'Buku Populer' : 'Rekomendasi untuk Anggota: ' . esc($member_no) ?>
                            <span class="badge bg-warning text-dark"><?= count($recommendations) ?> buku</span>
                        </h3>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>


    <!-- Main Content Section -->
    <section class="content-section">
        <div class="main-container">
            <div class="row">
                <!-- Main Content Area (Left Side) - Adjusted width -->
                <div class="col-lg-9 catalog-section">
                    <div class="text-center mb-5">
                        <h3><i class="fas fa-star me-2"></i>Koleksi Terbaru</h3>
                        <p class="text-muted">Temukan buku-buku terbaru dalam koleksi kami</p>
                    </div>

                    <!-- Catalog Cards -->
                    <div class="row">
                        <?php
                        // Use recommendations if available, otherwise use regular catalogs
                        $displayCatalogs = [];
                        if (!empty($member_no) && isset($recommendations)) {
                            $displayCatalogs = $recommendations;
                        } else {
                            $displayCatalogs = $catalogs ?? [];
                        }
                        ?>

                        <?php if (!empty($displayCatalogs)): ?>
                            <?php foreach ($displayCatalogs as $catalog): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card catalog-card h-100">
                                        <div class="card-header <?= !empty($member_no) ? 'bg-success text-white' : 'bg-success text-white' ?>">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-book me-2"></i>
                                                <?php if (!empty($member_no)): ?>
                                                    <?= $is_cold_start ? 'Populer' : 'Rekomendasi' ?>
                                                    <?php if ($is_cold_start && isset($catalog['LoanCount'])): ?>
                                                        <span class="badge bg-light text-dark ms-2"><?= $catalog['LoanCount'] ?> peminjaman</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Book Cover Column -->
                                                <div class="col-4">
                                                    <div class="book-cover-container position-relative">
                                                        <?php
                                                        $coverURL = '';
                                                        $title = '';
                                                        if (!empty($member_no)) {
                                                            $coverURL = $catalog['CoverURL'] ?? '';
                                                            $title = $catalog['Title'] ?? 'Book';
                                                        } else {
                                                            $coverURL = $catalog->CoverURL ?? '';
                                                            $title = $catalog->Title ?? 'Book';
                                                        }

                                                        $coverPath = base_url('uploads/katalog/' . ($coverURL ?: 'default-cover.jpg'));
                                                        $defaultCover = base_url('assets/img/default-cover.png');
                                                        ?>
                                                        <img src="<?= $coverPath ?>"
                                                            style="max-width: 100px; max-height: 150px; width: 100%; object-fit: cover;"
                                                            alt="Cover <?= esc($title) ?>"
                                                            class="book-cover img-fluid rounded shadow-sm"
                                                            onerror="this.src='<?= $defaultCover ?>'">
                                                        <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded"
                                                            style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s ease;">
                                                            <i class="fas fa-eye text-white fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Book Information Column -->
                                                <div class="col-8">
                                                    <?php
                                                    if (!empty($member_no)) {
                                                        $bookTitle = $catalog['Title'] ?? 'Tanpa Judul';
                                                        $bookPublisher = $catalog['Publisher'] ?? 'N/A';
                                                        $bookAuthor = $catalog['Author'] ?? 'N/A';
                                                        $bookYear = $catalog['PublishYear'] ?? 'N/A';
                                                        $bookISBN = $catalog['ISBN'] ?? '';
                                                        $bookSubject = $catalog['Subject'] ?? '';
                                                        $bookID = $catalog['ID'] ?? 0;
                                                    } else {
                                                        $bookTitle = $catalog->Title ?? 'Tanpa Judul';
                                                        $bookAuthor = $catalog->Author ?? 'N/A';
                                                        $bookPublisher = $catalog->Publisher ?? 'N/A';
                                                        $bookYear = $catalog->PublishYear ?? 'N/A';
                                                        $bookSubject = $catalog->Subject ?? '';
                                                        $bookISBN = $catalog->ISBN ?? '';
                                                        $bookID = $catalog->ID ?? 0;
                                                    }
                                                    ?>

                                                    <h5 class="card-title text-primary mb-2" style="font-size: 1rem; line-height: 1.3;">
                                                        <?= esc(substr($bookTitle, 0, 50)) ?>
                                                        <?= strlen($bookTitle) > 50 ? '...' : '' ?>
                                                    </h5>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                            <i class="fas fa-user me-1"></i>
                                                            <strong>Pengarang:</strong><br>
                                                            <?= esc(substr($bookAuthor, 0, 35)) ?>
                                                            <?= strlen($bookAuthor) > 35 ? '...' : '' ?>
                                                        </small>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                            <i class="fas fa-building me-1"></i>
                                                            <strong>Penerbit:</strong><br>
                                                            <?= esc(substr($bookPublisher, 0, 30)) ?>
                                                            <?= strlen($bookPublisher) > 30 ? '...' : '' ?>
                                                        </small>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <strong>Tahun:</strong> <?= esc($bookYear) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Subject and ISBN Information -->
                                            <div class="mt-3">
                                                <?php if (!empty($bookSubject)): ?>
                                                    <div class="mb-2">
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                            <i class="fas fa-tag me-1"></i>
                                                            <?= esc(substr($bookSubject, 0, 25)) ?>
                                                            <?= strlen($bookSubject) > 25 ? '...' : '' ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (empty($member_no) && !empty($bookISBN)): ?>
                                                    <div class="mb-2">
                                                        <small class="text-info" style="font-size: 0.7rem;">
                                                            <i class="fas fa-barcode me-1"></i>
                                                            ISBN: <?= esc($bookISBN) ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url('opac/detail/' . $bookID) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                                <div class="btn-group">
                                                    <?php if (!empty($member_no)): ?>
                                                        <span class="badge <?= $is_cold_start ? 'bg-warning text-dark' : 'bg-success' ?>">
                                                            <i class="fas fa-magic me-1"></i><?= $is_cold_start ? 'Populer' : 'Rekomendasi' ?>
                                                        </span>
                                                    <?php elseif (($catalog->IsOPAC ?? false)): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Tersedia Online
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">
                                        <?php if (!empty($member_no)): ?>
                                            Tidak ada rekomendasi yang ditemukan untuk anggota ini
                                        <?php elseif (isset($search) && $search): ?>
                                            Tidak ada hasil yang ditemukan
                                        <?php else: ?>
                                            Gunakan form pencarian untuk menemukan koleksi
                                        <?php endif; ?>
                                    </h4>
                                    <?php if (!empty($member_no)): ?>
                                        <p class="text-muted mb-3">
                                            Anggota mungkin belum terdaftar atau belum memiliki riwayat peminjaman
                                        </p>
                                    <?php elseif (isset($search) && $search): ?>
                                        <p class="text-muted mb-3">
                                            Coba gunakan kata kunci yang berbeda atau
                                            <a href="<?= base_url('opac/search') ?>">pencarian lanjutan</a>
                                        </p>
                                    <?php endif; ?>
                                    <a href="<?= base_url('opac') ?>" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar (Right Side) - Adjusted width and positioning -->
                <div class="col-lg-3 sidebar-section">
                    <div class="sidebar-container">
                        <!-- Collection Statistics -->
                        <div class="card sidebar-card">
                            <div class="card-header bg-gradient-primary">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Lebih Lengkap
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Total Books -->
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-books text-primary"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h6 class="stat-value"><?= isset($total_records) ? number_format($total_records) : '0' ?></h6>
                                        <small class="text-muted">Total Koleksi</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Publishers -->
                        <?php if (isset($publisher_counts) && !empty($publisher_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header bg-gradient-success">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-building me-2"></i>Penerbit Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($publisher_counts);
                                        $top_publishers = array_slice($publisher_counts, 0, 5, true);
                                        foreach ($top_publishers as $publisher => $count):
                                            if (!empty(trim($publisher))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Publisher', $publisher)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($publisher, 0, 25)) ?><?= strlen($publisher) > 25 ? '...' : '' ?>
                                                    </a>
                                                    <span class="badge bg-info text-white"><?= $count ?></span>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Authors -->
                        <?php if (isset($author_counts) && !empty($author_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header bg-gradient-info">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-edit me-2"></i>Pengarang Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($author_counts);
                                        $top_authors = array_slice($author_counts, 0, 5, true);
                                        foreach ($top_authors as $author => $count):
                                            if (!empty(trim($author))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Author', $author)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($author, 0, 25)) ?><?= strlen($author) > 25 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Subjects -->
                        <?php if (isset($subject_counts) && !empty($subject_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header bg-gradient-warning">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tags me-2"></i>Subjek Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($subject_counts);
                                        $top_subjects = array_slice($subject_counts, 0, 5, true);
                                        foreach ($top_subjects as $subject => $count):
                                            if (!empty(trim($subject))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Subject', $subject)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($subject, 0, 25)) ?><?= strlen($subject) > 25 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Publication Years -->
                        <?php if (isset($year_counts) && !empty($year_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header" style="background: linear-gradient(45deg, #fd7e14, #e85d04); color: white;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Tahun Terbit
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($year_counts);
                                        $top_years = array_slice($year_counts, 0, 5, true);
                                        foreach ($top_years as $year => $count):
                                            if (!empty(trim($year)) && $year != '1970'):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('PublishYear', $year)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc($year) ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Publication Locatons -->
                        <?php if (isset($publish_location_counts) && !empty($publish_location_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header" style="background: linear-gradient(45deg, #fd7e14, #e85d04); color: white;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>KatalogRuasModel Terbit
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($publish_location_counts);
                                        $top_locations = array_slice($publish_location_counts, 0, 8, true);
                                        foreach ($top_locations as $location => $count):
                                            if (!empty(trim($location))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('PublishLocation', $location)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($location, 0, 20)) ?><?= strlen($location) > 20 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if (empty($member_no) && isset($pager) && $pager->getPageCount() > 1): ?>
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="Pagination">
                            <?= $pager->links() ?>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }

            // Add click handlers for stat items
            document.querySelectorAll('.stat-list-item').forEach(item => {
                item.addEventListener('click', function() {
                    const statName = this.querySelector('.stat-name').textContent.trim();
                    console.log('Clicked on:', statName);
                });
            });
        });

        // Search suggestions
        document.querySelector('input[name="search"]').addEventListener('input', function(e) {
            const query = e.target.value;
            if (query.length > 2) {
                console.log('Search suggestion for:', query);
            }
        });
    </script>
<?= $this->endSection() ?>