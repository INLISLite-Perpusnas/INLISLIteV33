<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

  <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #059669;
            --gradient-bg: linear-gradient(135deg,rgb(105, 162, 202) 0%,rgba(48, 76, 113, 0.69) 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        /* Hero Banner */
        .hero-banner {
            background: var(--gradient-bg);
            position: relative;
            overflow: hidden;
            min-height: 600px;
            display: flex;
            align-items: center;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.4rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero-cta {
            background: var(--accent-color);
            border: none;
            padding: 15px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .hero-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        /* Search Bar */
        .search-section {
            margin-top: -50px;
            position: relative;
            z-index: 3;
        }

        .search-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .search-input {
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            padding: 15px 25px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-btn {
            border-radius: 50px;
            padding: 15px 30px;
            background: var(--primary-color);
            border: none;
        }

        /* Statistics */
        .stats-section {
            background: #f8fafc;
            padding: 4rem 0;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary-color);
            font-weight: 500;
        }

        /* Modules Section */
        .modules-section {
            padding: 5rem 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #1e293b;
        }

        .module-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }

        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }

        .module-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            padding: 20px;
            border-radius: 50%;
            display: inline-block;
        }

        .module-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .module-description {
            color: var(--secondary-color);
            font-size: 0.95rem;
        }

        /* Collections Section */
        .collections-section {
            background: #f8fafc;
            padding: 5rem 0;
        }

        .book-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .book-image {
            height: 200px;
            background: var(--gradient-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .book-content {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            height: 2.8rem;
            overflow: hidden;
        }

        .book-author {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .book-publisher {
            color: var(--secondary-color);
            font-size: 0.85rem;
        }

        /* News Section */
        .news-section {
            padding: 5rem 0;
        }

        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .news-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .news-content {
            padding: 1.5rem;
        }

        .news-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .news-excerpt {
            color: var(--secondary-color);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--secondary-color);
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            margin-bottom: 2rem;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-text {
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }

        /* Color variants for modules */
        .bg-primary { background: rgba(37, 99, 235, 0.1); color: var(--primary-color); }
        .bg-success { background: rgba(5, 150, 105, 0.1); color: var(--success-color); }
        .bg-info { background: rgba(6, 182, 212, 0.1); color: #0891b2; }
        .bg-warning { background: rgba(245, 158, 11, 0.1); color: var(--accent-color); }
        .bg-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .bg-secondary { background: rgba(100, 116, 139, 0.1); color: var(--secondary-color); }
    </style>
</head>
<body>
    <!-- Hero Banner Section -->
    <section class="hero-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-right">
                        <h1 class="hero-title"><?= $banner['title'] ?></h1>
                        <p class="hero-subtitle"><?= $banner['subtitle'] ?></p>
                        <p class="mb-4"><?= $banner['description'] ?></p>
                        <a href="<?= $banner['cta_link'] ?>" class="btn btn-warning hero-cta">
                            <i class="fas fa-search me-2"></i>
                            <?= $banner['cta_text'] ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="text-center">
                        <i class="fas fa-book-open" style="font-size: 15rem; opacity: 0.3; color: white;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="search-card" data-aos="fade-up">
                        <form id="searchForm">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" id="searchInput" 
                                       placeholder="Cari buku, penulis, atau penerbit...">
                                <button class="btn btn-primary search-btn" type="submit">
                                    <i class="fas fa-search me-2"></i>
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($statistics['total_books']) ?></div>
                        <div class="stat-label">Total Koleksi Buku</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($statistics['total_members']) ?></div>
                        <div class="stat-label">Anggota Aktif</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($statistics['books_borrowed']) ?></div>
                        <div class="stat-label">Buku Dipinjam</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($statistics['visitors_today']) ?></div>
                        <div class="stat-label">Pengunjung Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="modules-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Layanan Perpustakaan</h2>
            <div class="row">
                <?php foreach ($modules as $index => $module): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <a href="<?= $module['link'] ?>" class="module-card d-block">
                        <div>
                            <img src="<?= $module['img'] ?>" alt="<?= $module['name'] ?>" style="width: 250px;height: 250px">
                        </div>
                        <h4 class="module-title"><?= $module['name'] ?></h4>
                        <p class="module-description"><?= $module['description'] ?></p>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Collections Section -->
    <section class="collections-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Koleksi Terbaru</h2>
            <div class="row">
                <?php if (!empty($featured_books)): ?>
                    <?php foreach ($featured_books as $index => $book): ?>
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <?php 
                     $coverPath = base_url('uploads/katalog/' . ($book->CoverURL ?: 'default-cover.jpg'));
                     $defaultCover = base_url('assets/img/default-cover.png');
                     ?>  
                    <div class="book-card">
                            <div class="book-image">
                             <img style="max-width: 200px; max-height: 200px; width: 100%; object-fit: cover;" src="<?= $coverPath ?>" alt="<?= esc($book->Title) ?>" onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                            </div>
                            <div class="book-content">
                                <h5 class="book-title"><a href="<?= base_url('opac/detail/' . $book->ID) ?>"><?= esc($book->Title) ?></a></h5>
                                <p class="book-author"><?= esc($book->Author ?: 'Penulis tidak diketahui') ?></p>
                                <p class="book-publisher"><?= esc($book->Publisher ?: 'Penerbit tidak diketahui') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Koleksi buku sedang dimuat...</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="<?= base_url('opac') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>
                    Lihat Semua Koleksi
                </a>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Berita & Pengumuman</h2>
            <div class="row">
                <?php foreach ($news as $index => $article): ?>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <div class="news-card">
                        <div class="news-image" style="background-image: url('<?= $article['image'] ?>')"></div>
                        <div class="news-content">
                            <h5 class="news-title"><?= esc($article['title']) ?></h5>
                            <p class="news-excerpt"><?= esc($article['excerpt']) ?></p>
                            <div class="news-meta">
                                <span><i class="fas fa-user me-1"></i><?= esc($article['author']) ?></span>
                                <span><i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($article['date'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="<?= base_url('news') ?>" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-newspaper me-2"></i>
                    Lihat Semua Berita
                </a>
            </div>
        </div>
    </section>

  

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize AOS (Animate On Scroll)
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                mirror: false
            });

            // Search functionality
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                const query = $('#searchInput').val().trim();
                
                if (query) {
                    // Redirect to search page with query
                    window.location.href = '<?= base_url('opac/search') ?>?q=' + encodeURIComponent(query);
                } else {
                    // Show alert if empty
                    alert('Silakan masukkan kata kunci pencarian');
                    $('#searchInput').focus();
                }
            });

            // Auto-complete search (optional)
            let searchTimeout;
            $('#searchInput').on('input', function() {
                const query = $(this).val().trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                if (query.length >= 3) {
                    // Delay search request
                    searchTimeout = setTimeout(function() {
                        performAutoComplete(query);
                    }, 300);
                }
            });

            function performAutoComplete(query) {
                $.ajax({
                    url: '<?= base_url('opac/searchBooks') ?>',
                    method: 'GET',
                    data: { q: query },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showSearchSuggestions(response.data);
                        }
                    },
                    error: function() {
                        console.log('Error fetching search suggestions');
                    }
                });
            }

            function showSearchSuggestions(books) {
                // Remove existing suggestions
                $('.search-suggestions').remove();
                
                if (books.length > 0) {
                    let suggestionsHtml = '<div class="search-suggestions position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1" style="z-index: 1000;">';
                    
                    books.forEach(function(book) {
                        suggestionsHtml += `
                            <div class="suggestion-item p-3 border-bottom" style="cursor: pointer;">
                                <div class="fw-semibold">${book.Title}</div>
                                <small class="text-muted">${book.Author || 'Penulis tidak diketahui'}</small>
                            </div>
                        `;
                    });
                    
                    suggestionsHtml += '</div>';
                    
                    // Append suggestions
                    $('.search-card .input-group').after(suggestionsHtml);
                    
                    // Handle suggestion click
                    $('.suggestion-item').on('click', function() {
                        const title = $(this).find('.fw-semibold').text();
                        $('#searchInput').val(title);
                        $('.search-suggestions').remove();
                        $('#searchForm').submit();
                    });
                }
            }

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-card').length) {
                    $('.search-suggestions').remove();
                }
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 70
                    }, 800);
                }
            });

            // Add hover effect to cards
            $('.module-card, .book-card, .news-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            // Lazy loading for images (if needed)
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Counter animation for statistics
            function animateCounters() {
                $('.stat-number').each(function() {
                    const $this = $(this);
                    const target = parseInt($this.text().replace(/,/g, ''));
                    const increment = target / 100;
                    let current = 0;
                    
                    const timer = setInterval(function() {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        $this.text(Math.floor(current).toLocaleString());
                    }, 20);
                });
            }

            // Trigger counter animation when stats section is visible
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            });

            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                statsObserver.observe(statsSection);
            }
        });

        // Service Worker registration (for PWA if needed)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>
<?= $this->endSection() ?>