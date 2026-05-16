<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure E-Reader</title>
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            background-color: #f4f6f9; /* Background abu-abu lembut agar fokus ke dokumen */
        }
        
        /* Modern Toolbar Styling */
        #toolbar { 
            height: 65px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        
        .toolbar-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 4px;
            border: 1px solid #e9ecef;
        }

        #page-num, #page-count { font-weight: 600; }
        
        /* Viewer Container & Paper Effect */
        .viewer-container {
            margin-top: 65px;
            padding: 30px 15px;
            display: flex;
            justify-content: center;
            min-height: calc(100vh - 65px);
            overflow: auto;
        }

        canvas { 
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15); /* Efek bayangan kertas */
            border-radius: 4px;
            background-color: #fff;
        }

        #search-result {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
        }
    </style>
</head>
<body>
   <div id="protection-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #f8f9fa; z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
        <i class="bi bi-shield-lock-fill text-danger" style="font-size: 5rem;"></i>
        <h3 class="text-dark mt-3 fw-bold">Tindakan Terdeteksi</h3>
        <p class="text-muted text-center">Sistem mendeteksi aktivitas sistem (Screenshot / Pindah Tab / Mouse Keluar).<br>Tampilan disembunyikan untuk melindungi dokumen.</p>
        <button id="resume-reading" class="btn btn-primary btn-lg mt-4 px-4 shadow-sm">
            <i class="bi bi-eye me-2"></i> Lanjutkan Membaca
        </button>
    </div>
    <!-- Navbar / Toolbar -->
    <nav id="toolbar" class="navbar fixed-top navbar-expand-lg bg-white border-bottom">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center flex-nowrap">
            
            <!-- Branding -->
            <div class="d-flex align-items-center me-4">
                <i class="bi bi-book-half fs-4 text-primary me-2"></i>
                <span class="fw-bold text-dark fs-5 d-none d-md-inline">E-Reader</span>
            </div>

            <!-- Pagination Controls -->
            <div class="d-flex align-items-center toolbar-group mx-auto">
                <button id="prev" class="btn btn-light btn-sm" title="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>
                
                <span class="mx-3 text-muted" style="font-size: 0.9rem;">
                    Hal <span id="page-num" class="text-dark">0</span> / <span id="page-count" class="text-dark">0</span>
                </span>
                
                <button id="next" class="btn btn-light btn-sm me-2" title="Halaman Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <div class="input-group input-group-sm" style="width: 130px;">
                    <input type="number" id="page-input" class="form-control text-center" min="1" placeholder="Go to...">
                    <button id="go-to-page" class="btn btn-primary"><i class="bi bi-arrow-right-short"></i></button>
                </div>
            </div>

            <!-- Search Controls -->
            <div class="d-flex align-items-center ms-4">
                <span id="search-result" class="me-2 d-none d-lg-inline"></span>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search-input" class="form-control border-start-0" placeholder="Cari teks...">
                    <button id="search" class="btn btn-outline-secondary">Cari</button>
                </div>
            </div>

        </div>
    </nav>

    <!-- PDF Viewer Area -->
    <div class="viewer-container">
        <canvas id="pdf-viewer"></canvas>
    </div>

   <script>
    // ==========================================
    // 1. SISTEM KEAMANAN & ANTI-SCREENSHOT
    // ==========================================

    // Disable right-click
    document.addEventListener('contextmenu', event => event.preventDefault());

    // Disable keyboard shortcuts (Save, Print, Copy, dll)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'c' || e.key === 'u')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        if (e.altKey) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        if (e.key.match(/^F\d+$/) || e.keyCode >= 112 && e.keyCode <= 123) {
            e.preventDefault();
            e.stopPropagation();
            if (document.activeElement) document.activeElement.blur();
            return false;
        }
        if (e.key === 'Tab') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // Disable copy, cut, dan drag
    document.addEventListener('copy', e => { e.preventDefault(); return false; });
    document.addEventListener('cut', e => { e.preventDefault(); return false; });
    document.addEventListener('dragstart', e => e.preventDefault());

    // Prevent leaving the page accidentally
    window.onbeforeunload = function() {
        return "Apakah Anda yakin ingin meninggalkan halaman ini?";
    };

    // --- LOGIKA OVERLAY ANTI-SCREENSHOT ---
    const overlay = document.getElementById('protection-overlay');
    const viewerContainer = document.querySelector('.viewer-container');

    // Fungsi untuk menyembunyikan konten secara instan
    function hideContent() {
        if(viewerContainer) viewerContainer.style.opacity = '0';
        if(overlay) overlay.style.display = 'flex';
    }

    // Fungsi untuk menampilkan kembali konten melalui klik tombol
    document.getElementById('resume-reading').addEventListener('click', function() {
        if(viewerContainer) viewerContainer.style.opacity = '1';
        if(overlay) overlay.style.display = 'none';
    });

    // Sembunyikan saat window kehilangan fokus (Snipping Tool / pindah aplikasi)
    window.addEventListener('blur', hideContent);

    // Deteksi kombinasi tombol Mac (Cmd + Shift) atau Print Screen Windows
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey && e.shiftKey) || e.key === 'PrintScreen') {
            hideContent(); 
            e.preventDefault();
            return false;
        }
    }, true);

    // Sembunyikan saat mouse keluar dari area browser (Cegah klik menu bar Apple)
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 0 || e.clientX <= 0 || (e.clientX >= window.innerWidth || e.clientY >= window.innerHeight)) {
            hideContent();
        }
    });


    // ==========================================
    // 2. SISTEM PDF.js (RENDER, WATERMARK, SEARCH)
    // ==========================================

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js';

    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.5,
        canvas = document.getElementById('pdf-viewer'),
        ctx = canvas.getContext('2d');

    // Render Halaman PDF
    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: scale});
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            let renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            let renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }

                // Highlight hasil pencarian (jika ada)
                highlightSearchResults(page);

                // Tambahkan watermark setelah halaman selesai dirender
                addWatermark();
            });
        });

        document.getElementById('page-num').textContent = num;
        document.getElementById('page-input').value = num;
    }

    // Fungsi Watermark
    function addWatermark() {
        const text = "Konten dilindungi undang-undang";
        ctx.save();
        ctx.globalAlpha = 0.08;
        ctx.font = "bold 24px 'Segoe UI', Arial";
        ctx.fillStyle = "#0d6efd"; 
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        const spacingX = 350;
        const spacingY = 200;
        for (let x = spacingX / 2; x < canvas.width; x += spacingX) {
            for (let y = spacingY / 2; y < canvas.height; y += spacingY) {
                ctx.save();
                ctx.translate(x, y);
                ctx.rotate(-Math.PI / 6);
                ctx.fillText(text, 0, 0);
                ctx.restore();
            }
        }
        ctx.restore();
    }

    // --- LOGIKA PAGINASI ---
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    function onPrevPage() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    }

    function onNextPage() {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    }

   function goToPage() {
        let pageInput = document.getElementById('page-input');
        let pageRequested = parseInt(pageInput.value);

        // Jika angka di kotak sama dengan halaman saat ini, fungsikan sebagai tombol "Next"
        if (pageRequested === pageNum) {
            if (pageNum < pdfDoc.numPages) {
                pageNum++;
                queueRenderPage(pageNum);
            }
            return;
        }

        // Jika pengguna mengetik angka baru, lompat ke halaman tersebut
        if (pageRequested >= 1 && pageRequested <= pdfDoc.numPages) {
            pageNum = pageRequested;
            queueRenderPage(pageNum);
        } else {
            alert('Halaman tidak valid. Masukkan angka antara 1 dan ' + pdfDoc.numPages);
            pageInput.value = pageNum; // Kembalikan teks ke angka semula
        }
    }

    document.getElementById('prev').addEventListener('click', onPrevPage);
    document.getElementById('next').addEventListener('click', onNextPage);
    document.getElementById('go-to-page').addEventListener('click', goToPage);
    document.getElementById('page-input').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') goToPage();
    });


    // --- LOGIKA PENCARIAN ---
    let searchText = '';
    let searchResults = [];
    let currentSearchIndex = -1;

    function performSearch() {
        let inputVal = document.getElementById('search-input').value.trim();
        const resultEl = document.getElementById('search-result');

        if (inputVal === '') {
            resultEl.textContent = '';
            searchResults = [];
            currentSearchIndex = -1;
            searchText = '';
            queueRenderPage(pageNum);
            return;
        }

        // PERBAIKAN: Jika kata kuncinya masih sama, cukup pindah ke hasil/halaman berikutnya
        if (inputVal === searchText && searchResults.length > 0) {
            updateSearchResult();
            return;
        }

        // Jika kata kunci baru, mulai pencarian dari awal
        searchText = inputVal;
        resultEl.innerHTML = '<span class="text-primary spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
        currentSearchIndex = -1;
        searchResults = [];

        if (!pdfDoc) return;

        let countPromises = [];
        for (let i = 1; i <= pdfDoc.numPages; i++) {
            countPromises.push(pdfDoc.getPage(i).then(function(page) {
                return page.getTextContent().then(function(textContent) {
                    return { pageNum: i, textContent: textContent };
                });
            }));
        }

        Promise.all(countPromises).then(function(pages) {
            pages.forEach(content => {
                let items = content.textContent.items;
                let str = items.map(item => item.str).join(' ');

                if (str.toLowerCase().includes(searchText.toLowerCase())) {
                    searchResults.push({ pageNum: content.pageNum, items: items });
                }
            });
            
            if (searchResults.length === 0) {
                resultEl.innerHTML = '<span class="text-danger">Tidak ditemukan.</span>';
            } else {
                updateSearchResult();
            }
        });
    }

    function updateSearchResult() {
        const resultEl = document.getElementById('search-result');
        if (searchResults.length > 0) {
            // Maju ke indeks berikutnya, jika sudah di akhir, kembali ke 0
            currentSearchIndex = (currentSearchIndex + 1) % searchResults.length;
            let currentResult = searchResults[currentSearchIndex];
            
            resultEl.innerHTML = `Hasil <b>${currentSearchIndex + 1}</b> dari <b>${searchResults.length}</b> (Hal ${currentResult.pageNum})`;
            pageNum = currentResult.pageNum;
            queueRenderPage(pageNum);
        }
    }

    function highlightSearchResults(page) {
        if (searchResults.length === 0 || currentSearchIndex < 0) return;

        let currentResult = searchResults[currentSearchIndex];
        if (currentResult.pageNum !== page.pageNumber) return;

        let items = currentResult.items;
        let viewport = page.getViewport({ scale: scale });

        ctx.save();
        ctx.fillStyle = 'rgba(255, 225, 0, 0.4)'; // Warna stabilo
        
        items.forEach(item => {
            // Cek apakah item teks mengandung kata yang dicari
            if (item.str.toLowerCase().includes(searchText.toLowerCase())) {
                let tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                let x = tx[4];
                let y = tx[5];
                let width = item.width * scale;
                let height = item.height * scale;
                ctx.fillRect(x, y - height - 2, width, height + 4); 
            }
        });
        ctx.restore();
    }

    document.getElementById('search').addEventListener('click', performSearch);
    document.getElementById('search-input').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') performSearch();
    });

    // --- LOAD DOKUMEN PDF ---
    let pdfUrl = '<?= base_url('katalog/get_decrypted_content/' . encData($fileId)) ?>';
    
    let loadingTask = pdfjsLib.getDocument(pdfUrl);
    loadingTask.promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('page-count').textContent = pdfDoc.numPages;
        renderPage(pageNum);
    }).catch(function(error) {
        console.error("Error loading PDF: ", error);
        alert("Gagal memuat dokumen PDF.");
    });
</script>
</body>
</html>