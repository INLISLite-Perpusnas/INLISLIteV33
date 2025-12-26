<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>


 <link rel="stylesheet" href="<?= base_url('assets'); ?>/css/home.css">

</head>

<body>
    <!-- Hero Banner Section -->
 <section class="hero-banner position-relative">
    <img src="<?= base_url('uploads/banner/' . $banner['image']) ?>" 
         class="w-100 h-100 position-absolute"
         style="object-fit: cover; object-position: center; top:0; left:0;">
    
    <div class="container position-relative d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-10 text-center">
            <div class="hero-content" data-aos="fade-up">
            
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
                            <?= csrf_field() ?>
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
    <section class="news-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5" data-aos="fade-up">Berita & Pengumuman</h2>

        <div class="row g-4">
            <?php foreach ($news as $index => $article): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <?php
                        $imgFile = $article['file_cover'] ?: 'default.jpg';
                        $coverPath = base_url('uploads/berita/' . $imgFile);
                    ?>

                    <div class="news-card">
                        <div class="news-image" style="background-image: url('<?= $coverPath ?>');" title="<?= esc($article['title']) ?>"></div>

                        <div class="news-content">
                            <a href="<?= base_url('news/detail/' . $article['id'] . '/' . $article['slug']) ?>">
                                <h5 class="news-title"><?= esc($article['title']) ?></h5>
                            </a>

                            <div class="news-excerpt">
                                <?= esc(strip_tags($article['content'])) ?>
                            </div>

                            <div class="news-meta">
                                <span class="text-truncate" style="max-width: 50%;">
                                    <i class="fas fa-user me-1"></i> <?= esc($article['username'] ?: 'Admin') ?>
                                </span>
                                <span>
                                    <i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($article['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= base_url('news') ?>" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                <i class="fas fa-newspaper me-2"></i> Lihat Semua Berita
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
                    window.location.href = '<?= base_url('opac') ?>?search_by=&search=' + encodeURIComponent(query);
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
                    data: {
                        q: query
                    },
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