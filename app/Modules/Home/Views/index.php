<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Hero Section (Murni Gambar) */
    .hero-section {
        margin-top: 76px; /* Jarak untuk menghindari navbar yang fixed */
        height: 450px; /* Atur tinggi banner sesuai kebutuhan (misal 400px - 500px) */
        position: relative;
        overflow: hidden;
        background-color: #e2e8f0; /* Warna skeleton saat gambar dimuat */
    }
    .hero-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    /* Overlapping Search Box */
    .search-overlap {
        margin-top: -40px; /* Menarik kotak pencarian ke atas agar menimpa gambar banner */
        position: relative;
        z-index: 10;
    }
    .search-input-group i { 
        position: absolute; 
        left: 20px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: var(--slate-500); 
        z-index: 5;
    }
    .search-input-group input { 
        padding-left: 55px; 
        border-radius: 1rem !important; 
        background-color: var(--slate-50); 
        border: 1px solid var(--slate-100); 
        font-size: 1rem;
    }
    .search-input-group input:focus { 
        background-color: white; 
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25); 
        border-color: var(--brand-500);
    }

    /* Cards & Hover Effects */
    .hover-card { 
        transition: all 0.3s ease; 
        border: 1px solid var(--slate-100); 
    }
    .hover-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1); 
        border-color: var(--brand-100); 
    }
    
    /* Image Utils */
    .book-cover { height: 240px; object-fit: cover; width: 100%; background-color: #e2e8f0; }
    .news-cover { height: 100px; object-fit: cover; width: 100%; border-radius: 0.75rem;}
    .news-main-cover { height: 380px; object-fit: cover; width: 100%; }

    /* Custom Title Color */
    .section-title {
        color: #1b3878 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <section class="hero-section">
        <img src="<?= isset($banner['image']) ? base_url('uploads/banner/' . $banner['image']) : base_url('assets/img/default-banner.jpg') ?>" 
             alt="Banner Perpustakaan" 
             class="hero-bg">
    </section>

    <div class="container search-overlap">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card rounded-2xl shadow-soft border-0 p-2 bg-white">
                    <div class="card-body p-3">
                        <form id="searchForm" action="<?= base_url('opac') ?>" method="GET" class="row g-2">
                            <div class="col-md-9 position-relative search-input-group">
                                <i class="fa-solid fa-search"></i>
                                <input type="text" id="searchInput" name="search" class="form-control form-control-lg py-3" placeholder="Cari judul buku, penulis, atau penerbit..." autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-brand w-100 h-100 rounded-xl fw-bold" style="font-size: 1.05rem;">
                                    Cari Buku
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <section class="py-5 mt-3">
        <div class="container">
            <div class="row text-center g-4 justify-content-center">
                
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-book-bookmark fa-2x mb-3" style="color: #1b3878;"></i>
                            <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['total_books'] ?? 0 ?></h2>
                            <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Koleksi Buku</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-users fa-2x mb-3" style="color: #1b3878;"></i>
                            <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['total_members'] ?? 0 ?></h2>
                            <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Anggota Aktif</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-hand-holding-hand fa-2x mb-3" style="color: #1b3878;"></i>
                            <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['books_borrowed'] ?? 0 ?></h2>
                            <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Buku Dipinjam</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-person-walking fa-2x mb-3" style="color: #1b3878;"></i>
                            <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['visitors_today'] ?? 0 ?></h2>
                            <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Pengunjung Hari Ini</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <section class="py-5 bg-light" id="koleksi">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold mb-1 section-title">Koleksi Terbaru</h3>
                    <p class="text-secondary mb-0">Buku-buku yang baru saja ditambahkan.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php if (!empty($featured_books)): ?>
                    <?php foreach (array_slice($featured_books, 0, 5) as $book): ?>
                        <?php
                        $coverPath = base_url('uploads/katalog/' . ($book->CoverURL ?: 'default-cover.jpg'));
                        $defaultCover = base_url('assets/img/default-cover.png');
                        ?>
                        <div class="col-6 col-md-4 col-lg-2-4" style="width: 20%; min-width: 160px;"> <div class="card h-100 border-0 shadow-sm hover-card rounded-xl overflow-hidden bg-white">
                                <img src="<?= $coverPath ?>" class="card-img-top book-cover" alt="<?= esc($book->Title) ?>" onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title fw-bold fs-6 mb-1 line-clamp-2" title="<?= esc($book->Title) ?>"><?= esc($book->Title) ?></h6>
                                    <p class="card-text text-secondary small mb-3 text-truncate" title="<?= esc($book->Author) ?>">
                                        <?= esc($book->Author ?: 'Anonim') ?>
                                    </p>
                                    <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="btn btn-outline-primary btn-sm mt-auto fw-semibold w-100 rounded-lg">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-book-open fa-3x text-secondary opacity-50 mb-3"></i>
                        <p class="text-secondary">Koleksi buku sedang dimuat...</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5">
                <a href="<?= base_url('opac') ?>" class="btn btn-brand rounded-pill px-5 py-2 fw-semibold shadow-sm">
                    Lihat Semua Koleksi <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="py-5" id="berita">
        <div class="container my-4">
            <h3 class="fw-bold mb-4 d-flex align-items-center section-title">
                <div class="bg-brand rounded-pill me-3" style="width: 5px; height: 30px;"></div>
                Berita & Pengumuman
            </h3>

            <?php if (!empty($news)): ?>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <?php 
                        $highlight = $news[0]; 
                        $highlightImg = base_url('uploads/berita/' . ($highlight['file_cover'] ?: 'default.jpg'));
                        ?>
                        <a href="<?= base_url('news/detail/' . $highlight['id'] . '/' . $highlight['slug']) ?>" class="card border-0 rounded-2xl overflow-hidden text-decoration-none shadow-sm hover-card position-relative text-white h-100">
                            <img src="<?= $highlightImg ?>" onerror="this.src='https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?auto=format&fit=crop&w=1350&q=80'" class="news-main-cover">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.4) 50%, transparent 100%);"></div>
                            
                            <div class="position-absolute bottom-0 start-0 p-4 w-100">
                                <span class="badge bg-brand mb-2 py-1 px-3 rounded-pill"><?= date('d M Y', strtotime($highlight['created_at'])) ?></span>
                                <h4 class="fw-bold mb-2 line-clamp-2"><?= esc($highlight['title']) ?></h4>
                                <p class="small text-light text-opacity-75 mb-0 line-clamp-2"><?= strip_tags($highlight['content']) ?></p>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-6">
                        <div class="d-flex flex-column h-100 justify-content-between">
                            <?php for ($i = 1; $i < count($news) && $i <= 3; $i++): 
                                $article = $news[$i];
                                $imgFile = base_url('uploads/berita/' . ($article['file_cover'] ?: 'default.jpg'));
                            ?>
                            <a href="<?= base_url('news/detail/' . $article['id'] . '/' . $article['slug']) ?>" class="d-flex gap-3 text-decoration-none text-dark hover-card p-3 rounded-xl border border-light mb-3 bg-white shadow-sm h-100 align-items-center">
                                <div class="flex-shrink-0" style="width: 120px;">
                                    <img src="<?= $imgFile ?>" onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=500&q=60'" class="news-cover">
                                </div>
                                <div>
                                    <small class="text-secondary fw-semibold d-block mb-1"><i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($article['created_at'])) ?></small>
                                    <h6 class="fw-bold mb-1 line-clamp-2 text-dark" style="line-height: 1.4;"><?= esc($article['title']) ?></h6>
                                    <span class="text-brand small fw-bold mt-2 d-inline-block">Baca Selengkapnya</span>
                                </div>
                            </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center text-secondary py-5 bg-white rounded-2xl border border-light shadow-sm">
                    <i class="fa-regular fa-newspaper fa-3x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">Belum ada berita yang dipublikasikan.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function() {
        
        // Counter Animation for Statistics
        function animateCounters() {
            $('.stat-number').each(function() {
                const $this = $(this);
                const textVal = $this.text().replace(/[,.]/g, '');
                const target = parseInt(textVal);
                
                if (isNaN(target) || target === 0) return;

                const increment = Math.ceil(target / 40); 
                let current = 0;

                const timer = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    $this.text(current.toLocaleString('id-ID'));
                }, 40);
            });
        }

        // Trigger animasi angka jika box statistik masuk ke layar viewport
        if ('IntersectionObserver' in window) {
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            });

            const statElement = document.querySelector('.stat-number');
            if (statElement) {
                statsObserver.observe(statElement);
            }
        } else {
            animateCounters();
        }

        // --- Live Search Auto Suggestion ---
        let searchTimeout;
        $('#searchInput').on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(searchTimeout);

            if (query.length >= 3) {
                searchTimeout = setTimeout(function() {
                    performAutoComplete(query);
                }, 300);
            } else {
                $('.search-suggestions').remove();
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
                }
            });
        }

        function showSearchSuggestions(books) {
            $('.search-suggestions').remove();
            if (books.length > 0) {
                let suggestionsHtml = '<div class="search-suggestions position-absolute w-100 bg-white border rounded shadow-lg z-3 mt-1" style="max-height: 250px; overflow-y: auto; top: 100%; border-radius: 1rem;">';
                
                books.forEach(function(book) {
                    suggestionsHtml += `
                        <div class="suggestion-item p-3 border-bottom" style="cursor: pointer; transition: background 0.2s;">
                            <div class="fw-bold small text-dark">${book.Title}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${book.Author || 'Penulis tidak diketahui'}</div>
                        </div>
                    `;
                });
                suggestionsHtml += '</div>';

                // Append di container search input
                $('.search-input-group').append(suggestionsHtml);

                $('.suggestion-item').hover(function(){
                    $(this).addClass('bg-light');
                }, function(){
                    $(this).removeClass('bg-light');
                }).on('click', function() {
                    $('#searchInput').val($(this).find('.fw-bold').text());
                    $('.search-suggestions').remove();
                    $('#searchForm').submit();
                });
            }
        }

        // Menutup autocomplete jika klik di luar area input
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-input-group').length) {
                $('.search-suggestions').remove();
            }
        });
    });
</script>
<?= $this->endSection() ?>