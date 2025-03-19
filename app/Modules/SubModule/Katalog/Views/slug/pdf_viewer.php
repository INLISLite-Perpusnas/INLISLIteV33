<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure PDF Viewer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
        #pdf-viewer { width: 100%; height: 100%; margin-top: 70px; }
        #toolbar { position: fixed; top: 0; left: 0; right: 0; background: #333; color: white; padding: 10px; height: 50px; display: flex; align-items: center; z-index: 1000; }
        #page-num, #page-count { color: white; margin: 0 5px; }
        button { margin: 0 5px; }
        #search-result { color: #ffd700; margin-left: 10px; }
        canvas { position: relative; }
        .highlight { background-color: yellow; position: absolute; z-index: 10; pointer-events: none; opacity: 0.5; }
    </style>
</head>
<body>
    <div id="toolbar" class="d-flex align-items-center bg-dark text-white p-2">
        <button id="prev" class="btn btn-light btn-sm me-2">Previous</button>
        <button id="next" class="btn btn-light btn-sm me-2">Next</button>
        <span>Page: <span id="page-num"></span> / <span id="page-count"></span></span>
        <input type="number" id="page-input" class="form-control form-control-sm ms-3 me-2" min="1" placeholder="Go to page" style="width: 100px;">
        <button id="go-to-page" class="btn btn-light btn-sm me-3">Go</button>
        <input type="text" id="search-input" class="form-control form-control-sm me-2" placeholder="Search..." style="width: 200px;">
        <button id="search" class="btn btn-light btn-sm me-3">Search</button>
        <span id="search-result"></span>
    </div>
    <div class="container-fluid p-0">
        <canvas id="pdf-viewer"></canvas>
    </div>

    <script>
    // Disable right-click
    document.addEventListener('contextmenu', event => event.preventDefault());

    // Disable keyboard shortcuts
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
            if (document.activeElement) {
                document.activeElement.blur();
            }
            return false;
        }
        if (e.key === 'Tab') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // Disable copy and cut
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        return false;
    });
    document.addEventListener('cut', function(e) {
        e.preventDefault();
        return false;
    });

    // Prevent leaving the page
    window.onbeforeunload = function() {
        return "Are you sure you want to leave this page?";
    };

    // PDF.js viewer
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js';

    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.5,
        canvas = document.getElementById('pdf-viewer'),
        ctx = canvas.getContext('2d'),
        highlights = [];

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

            // Highlight any found search results on this page
            highlightSearchResults(page);

            // Add watermark after the page is rendered
            addWatermark();
        });
    });

    document.getElementById('page-num').textContent = num;
    document.getElementById('page-input').value = num;
}

function addWatermark() {
    const text = "Konten ini dilindungi undang-undang";
    ctx.save();
    ctx.globalAlpha = 0.5; // Transparansi watermark
        ctx.font = "bold 40px Arial";
        ctx.fillStyle = "blue"; // Warna biru untuk watermark
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(-Math.PI / 4);
    ctx.fillText(text, 0, 0);
    ctx.restore();
}


    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    function onPrevPage() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);
    }

    function onNextPage() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
    }

    function goToPage() {
        let pageInput = document.getElementById('page-input');
        let pageRequested = parseInt(pageInput.value);
        if (pageRequested >= 1 && pageRequested <= pdfDoc.numPages) {
            pageNum = pageRequested;
            queueRenderPage(pageNum);
        } else {
            alert('Invalid page number. Please enter a number between 1 and ' + pdfDoc.numPages);
        }
    }

    document.getElementById('prev').addEventListener('click', onPrevPage);
    document.getElementById('next').addEventListener('click', onNextPage);
    document.getElementById('go-to-page').addEventListener('click', goToPage);
    document.getElementById('page-input').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            goToPage();
        }
    });

    // Search functionality
    let searchText = '';
    let searchResults = [];
    let currentSearchIndex = -1;

    function performSearch() {
        searchText = document.getElementById('search-input').value;
        if (searchText.trim() === '') {
            document.getElementById('search-result').textContent = '';
            return;
        }

        currentSearchIndex = -1;
        searchResults = [];

        let loadingTask = pdfjsLib.getDocument('<?= base_url('katalog/get_decrypted_content/' . $fileId) ?>');
        loadingTask.promise.then(function(pdf) {
            let countPromises = [];
            for (let i = 1; i <= pdf.numPages; i++) {
                countPromises.push(pdf.getPage(i).then(function(page) {
                    return page.getTextContent().then(function(textContent) {
                        return { pageNum: i, textContent: textContent };
                    });
                }));
            }
            return Promise.all(countPromises);
        }).then(function(pages) {
            // Clear previous highlights
            clearHighlights();

            pages.forEach(content => {
                let items = content.textContent.items;
                let str = items.map(item => item.str).join(' ');

                if (str.toLowerCase().includes(searchText.toLowerCase())) {
                    searchResults.push({pageNum: content.pageNum, items: items});
                }
            });
            updateSearchResult();
        });
    }

    function updateSearchResult() {
        if (searchResults.length === 0) {
            document.getElementById('search-result').textContent = 'No results found.';
        } else {
            currentSearchIndex = (currentSearchIndex + 1) % searchResults.length;
            let currentResult = searchResults[currentSearchIndex];
            document.getElementById('search-result').textContent = `Result ${currentSearchIndex + 1} of ${searchResults.length} on page ${currentResult.pageNum}`;
            pageNum = currentResult.pageNum;
            queueRenderPage(pageNum);
        }
    }

    function highlightSearchResults(page) {
        if (searchResults.length === 0 || page.pageNumber !== pageNum) {
            return;
        }

        let currentResult = searchResults[currentSearchIndex];
        if (currentResult.pageNum !== page.pageNumber) {
            return;
        }

        let items = currentResult.items;
        let viewport = page.getViewport({scale: scale});

        items.forEach(item => {
            if (item.str.toLowerCase().includes(searchText.toLowerCase())) {
                let tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                let x = tx[4];
                let y = tx[5];
                let width = item.width * scale;
                let height = item.height * scale;

                ctx.fillStyle = 'yellow';
                ctx.globalAlpha = 0.5;
                ctx.fillRect(x, y - height, width, height);
            }
        });
    }

    function clearHighlights() {
        highlights.forEach(hl => hl.remove());
        highlights = [];
    }

    document.getElementById('search').addEventListener('click', performSearch);

    // Load PDF document
    let loadingTask = pdfjsLib.getDocument('<?= base_url('katalog/get_decrypted_content/' . $fileId) ?>');
    loadingTask.promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('page-count').textContent = pdfDoc.numPages;
        renderPage(pageNum);
    });
    </script>
</body>
</html>
