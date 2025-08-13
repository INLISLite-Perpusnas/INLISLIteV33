<?= $this->extend('App\Views\layout\opac\layout'); ?>
<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --accent-color: #f59e0b;
        --success-color: #059669;
        --gradient-bg: linear-gradient(135deg, rgb(105, 162, 202) 0%, rgba(62, 99, 146, 0.69) 100%);
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
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
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
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        max-height: 2.8em;
        min-height: 2.8em;
    }

    .news-excerpt {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-height: 6.4em;
        min-height: 6.4em;
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
    .bg-primary {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-color);
    }

    .bg-success {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success-color);
    }

    .bg-info {
        background: rgba(6, 182, 212, 0.1);
        color: #0891b2;
    }

    .bg-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--accent-color);
    }

    .bg-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .bg-secondary {
        background: rgba(100, 116, 139, 0.1);
        color: var(--secondary-color);
    }
</style>
</head>

<body>
    <div class="container py-4">
        <!-- Breadcrumb -->
        <div class="col-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active">News</li>
                </ol>
            </nav>
        </div>
        <h2><?= esc($title) ?></h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <input type="text" id="search-box" class="form-control" placeholder="Cari berita...">
            </div>
        </div>
        <div id="news-list" class="row"></div>
        <nav>
            <ul class="pagination justify-content-center" id="news-pagination"></ul>
        </nav>
    </div>

    <script>
        // Data berita dari PHP ke JS
        const newsData = <?= json_encode($news) ?>;
        const perPage = 12;
        let currentPage = 1;
        const BASE_URL = '<?= base_url() ?>';
        const UPLOAD_PATH = 'uploads/berita/';
        const DEFAULT_COVER = 'default-cover.png';
        let filteredNewsData = [...newsData]; // Salinan data untuk pencarian


        // Fungsi untuk render berita
        function renderNews(page, dataToRender = filteredNewsData) {
            const start = (page - 1) * perPage;
            const end = start + perPage;
            const items = dataToRender.slice(start, end);

            let html = '';
            if (items.length === 0) {
                html = '<div class="col-12"><p class="text-center">Tidak ada berita yang ditemukan.</p></div>';
            } else {
                items.forEach(function(article, index) {
                    const detailUrl = `news/detail/${article.id}/${article.slug}`;
                    const coverFileName = article.file_cover || DEFAULT_COVER;
                    const coverPath = `${BASE_URL}${UPLOAD_PATH}${coverFileName}`;
                    html += `
                        <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="${(index + 1) * 100}">
                            <div class="news-card" style="cursor:pointer" onclick="window.location.href='${detailUrl}'">
                                <div class="news-image" style="background-image: url('${coverPath}')"></div>
                                <div class="news-content">
                                    <h5 class="news-title">${article.title}</h5>
                                    <p class="news-excerpt">${stripTags(article.content)}</p>
                                    <div class="news-meta">
                                        <span><i class="fas fa-user me-1"></i>${article.description}</span>
                                        <span><i class="fas fa-calendar me-1"></i>${formatDate(article.created_at)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            document.getElementById('news-list').innerHTML = html;
            renderPagination(dataToRender.length);
        }

        function renderPagination() {
            const totalPages = Math.ceil(newsData.length / perPage);
            let html = '';

            html += `<li class="page-item${currentPage === 1 ? ' disabled' : ''}">
                        <a class="page-link" href="#" onclick="gotoPage(${currentPage - 1});return false;">Prev</a>
                    </li>`;
            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item${i === currentPage ? ' active' : ''}">
                            <a class="page-link" href="#" onclick="gotoPage(${i});return false;">${i}</a>
                        </li>`;
            }
            html += `<li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
                        <a class="page-link" href="#" onclick="gotoPage(${currentPage + 1});return false;">Next</a>
                    </li>`;

            document.getElementById('news-pagination').innerHTML = html;
        }

        // Event listener untuk search box
        document.getElementById('search-box').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filteredNewsData = newsData.filter(article =>
                article.title.toLowerCase().includes(searchTerm) ||
                article.content.toLowerCase().includes(searchTerm)
            );
            currentPage = 1; // Reset ke halaman pertama setelah pencarian
            renderNews(currentPage);
        });

        function gotoPage(page) {
            const totalPages = Math.ceil(newsData.length / perPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderNews(currentPage);
            renderPagination();
        }

        // Helper functions
        function stripTags(html) {
            let div = document.createElement("div");
            div.innerHTML = html;
            return div.textContent || div.innerText || "";
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Inisialisasi
        gotoPage(1);
    </script>
</body>
<?= $this->endSection('content') ?>