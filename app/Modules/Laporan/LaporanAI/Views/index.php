<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
.chat-container {
    height: 600px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
    max-width: 80%;
}

.message.user {
    background: #007bff;
    color: white;
    margin-left: auto;
    text-align: right;
}

.message.ai {
    background: white;
    border: 1px solid #ddd;
    margin-right: auto;
}

.query-input {
    border-radius: 25px;
    padding: 15px 25px;
    border: 2px solid #007bff;
}

.btn-send {
    border-radius: 50%;
    width: 50px;
    height: 50px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-container {
    max-height: 400px;
    overflow-y: auto;
    margin-top: 20px;
}

.chart-container {
    margin-top: 20px;
    height: 400px;
    position: relative;
}

.chart-canvas {
    max-height: 100%;
    max-width: 100%;
}

.export-buttons {
    margin-top: 15px;
}

.loading {
    display: none;
    text-align: center;
    padding: 20px;
}

.example-queries {
    background: #e9ecef;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.example-query {
    background: white;
    border: 1px solid #ddd;
    padding: 8px 12px;
    margin: 5px;
    border-radius: 15px;
    cursor: pointer;
    display: inline-block;
    font-size: 12px;
    transition: all 0.3s;
}

.example-query:hover {
    background: #007bff;
    color: white;
}

.sql-query {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    margin: 10px 0;
}

.summary {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    padding: 10px;
    margin: 10px 0;
}

.chart-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-graph icon-gradient bg-strong-bliss"></i>
                </div>
                <div>
                    Laporan AI 
                    <div class="page-title-subheading">
                        Tanyakan apa saja tentang data perpustakaan dan dapatkan laporan otomatis
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Queries -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fa fa-lightbulb"></i> Contoh Pertanyaan
            </h5>
        </div>
        <div class="card-body">
            <div class="example-queries">
                <div class="example-query" onclick="setQuery('Berapa total koleksi buku yang ada?')">
                    Berapa total koleksi buku yang ada?
                </div>
                <div class="example-query" onclick="setQuery('Tampilkan 10 anggota yang paling aktif meminjam')">
                    Tampilkan 10 anggota yang paling aktif meminjam
                </div>
                <div class="example-query" onclick="setQuery('Berapa buku yang dipinjam bulan ini?')">
                    Berapa buku yang dipinjam bulan ini?
                </div>
                <div class="example-query" onclick="setQuery('Tampilkan distribusi koleksi per kategori')">
                    Tampilkan distribusi koleksi per kategori
                </div>
                <div class="example-query" onclick="setQuery('Siapa anggota yang memiliki tunggakan terbanyak?')">
                    Siapa anggota yang memiliki tunggakan terbanyak?
                </div>
                <div class="example-query" onclick="setQuery('Trend peminjaman 6 bulan terakhir')">
                    Trend peminjaman 6 bulan terakhir
                </div>
                <div class="example-query" onclick="setQuery('Koleksi apa yang paling populer?')">
                    Koleksi apa yang paling populer?
                </div>
                <div class="example-query" onclick="setQuery('Tampilkan 10 catalog terbaru')">
                    Tampilkan 10 catalog terbaru
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-comments"></i> AI Assistant
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Chat Container -->
                    <div id="chatContainer" class="chat-container">
                        <div class="message ai">
                            <strong>AI Assistant:</strong><br>
                            Halo! Saya siap membantu Anda menganalisis data perpustakaan. 
                            Silakan tanyakan apa saja tentang koleksi, anggota, peminjaman, atau statistik lainnya.
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="loading">
                        <i class="fa fa-spinner fa-spin"></i> Sedang memproses query Anda...
                    </div>

                    <!-- Input -->
                    <div class="input-group mt-3">
                        <input type="text" id="queryInput" class="form-control query-input" 
                               placeholder="Tanyakan sesuatu tentang data perpustakaan..." 
                               onkeypress="handleKeyPress(event)">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-send" onclick="sendQuery()">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div id="resultsContainer" style="display: none;">
        <div class="row">
            <!-- Table Results -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-table"></i> Hasil Data
                        </h5>
                        <div class="export-buttons">
                            <button class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="fa fa-file-excel"></i> Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportPDF()">
                                <i class="fa fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="summaryContainer"></div>
                        <div id="sqlContainer"></div>
                        <div id="tableContainer" class="table-container"></div>
                    </div>
                </div>
            </div>

            <!-- Chart Results -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-chart-bar"></i> Visualisasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="chartContainer" class="chart-container">
                            <!-- Default placeholder -->
                            <div id="chartPlaceholder" class="chart-placeholder">
                                <p class="text-muted">Chart akan muncul di sini setelah query berhasil</p>
                            </div>
                            <!-- Chart canvas - always present -->
                            <canvas id="resultChart" class="chart-canvas" style="display: none;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Export -->
<form id="exportExcelForm" method="post" action="<?= base_url('laporan-ai/export-excel') ?>" style="display: none;">
    <input type="hidden" id="excelData" name="data">
    <input type="hidden" id="excelColumns" name="columns">
    <input type="hidden" id="excelQuery" name="query">
</form>

<form id="exportPDFForm" method="post" action="<?= base_url('laporan-ai/export-pdf') ?>" style="display: none;">
    <input type="hidden" id="pdfData" name="data">
    <input type="hidden" id="pdfColumns" name="columns">
    <input type="hidden" id="pdfQuery" name="query">
</form>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentData = null;
let currentColumns = null;
let currentQuery = '';
let currentChart = null;

function setQuery(query) {
    document.getElementById('queryInput').value = query;
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendQuery();
    }
}

function sendQuery() {
    const query = document.getElementById('queryInput').value.trim();
    
    if (!query) {
        alert('Silakan masukkan pertanyaan');
        return;
    }

    currentQuery = query;

    // Add user message to chat
    addMessageToChat(query, 'user');

    // Show loading
    document.getElementById('loading').style.display = 'block';
    document.getElementById('resultsContainer').style.display = 'none';

    // Clear input
    document.getElementById('queryInput').value = '';

    // Send to server
    fetch('<?= base_url('laporan-ai/process-query') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'query=' + encodeURIComponent(query)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            document.getElementById('loading').style.display = 'none';
            
            if (data.success) {
                handleSuccessResponse(data);
            } else {
                handleErrorResponse(data.message);
            }
        } catch (e) {
            console.error('JSON Parse Error:', e);
            console.error('Raw Response:', text);
            document.getElementById('loading').style.display = 'none';
            handleErrorResponse('Server returned invalid JSON. Check console for details.');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        document.getElementById('loading').style.display = 'none';
        handleErrorResponse('Terjadi kesalahan koneksi: ' + error.message);
    });
}

function addMessageToChat(message, type) {
    const chatContainer = document.getElementById('chatContainer');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    
    if (type === 'user') {
        messageDiv.innerHTML = `<strong>Anda:</strong><br>${message}`;
    } else {
        messageDiv.innerHTML = message;
    }
    
    chatContainer.appendChild(messageDiv);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function handleSuccessResponse(data) {
    currentData = data.data;
    currentColumns = data.columns;

    // Add AI response to chat
    let aiResponse = `<strong>AI Assistant:</strong><br>`;
    aiResponse += `${data.summary}<br><br>`;
    aiResponse += `<small>Query SQL yang dihasilkan:</small><br>`;
    aiResponse += `<code style="font-size: 10px;">${data.sql}</code>`;
    
    addMessageToChat(aiResponse, 'ai');

    // Show results
    displayResults(data);
}

function handleErrorResponse(message) {
    addMessageToChat(`<strong>AI Assistant:</strong><br>Maaf, ${message}`, 'ai');
}

function displayResults(data) {
    // Show summary
    const summaryContainer = document.getElementById('summaryContainer');
    if (summaryContainer) {
        summaryContainer.innerHTML = `
            <div class="summary">
                <strong>Ringkasan:</strong> ${data.summary}
            </div>
        `;
    }

    // Show SQL
    const sqlContainer = document.getElementById('sqlContainer');
    if (sqlContainer) {
        sqlContainer.innerHTML = `
            <div class="sql-query">
                <strong>SQL Query:</strong><br>
                <code>${data.sql}</code>
            </div>
        `;
    }

    // Show table
    displayTable(data.data, data.columns);

    // Show chart with improved error handling
    if (data.chart) {
        displayChart(data.chart);
    } else {
        showChartPlaceholder('Data tidak cocok untuk visualisasi chart');
    }

    // Show results container
    const resultsContainer = document.getElementById('resultsContainer');
    if (resultsContainer) {
        resultsContainer.style.display = 'block';
    }
}

function displayTable(data, columns) {
    const tableContainer = document.getElementById('tableContainer');
    if (!tableContainer) return;

    if (!data || data.length === 0) {
        tableContainer.innerHTML = '<p class="text-muted text-center">Tidak ada data ditemukan</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    
    // Header
    html += '<thead class="thead-dark"><tr>';
    columns.forEach(col => {
        html += `<th>${col}</th>`;
    });
    html += '</tr></thead>';

    // Body
    html += '<tbody>';
    data.forEach(row => {
        html += '<tr>';
        columns.forEach(col => {
            html += `<td>${row[col] || ''}</td>`;
        });
        html += '</tr>';
    });
    html += '</tbody></table></div>';

    tableContainer.innerHTML = html;
}

function displayChart(chartConfig) {
    const canvas = document.getElementById('resultChart');
    const placeholder = document.getElementById('chartPlaceholder');
    
    // Check if canvas exists
    if (!canvas) {
        console.error('Chart canvas not found');
        showChartPlaceholder('Canvas chart tidak ditemukan');
        return;
    }

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        showChartPlaceholder('Chart.js library tidak tersedia');
        return;
    }

    try {
        // Hide placeholder and show canvas
        if (placeholder) placeholder.style.display = 'none';
        canvas.style.display = 'block';

        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart
        if (currentChart) {
            currentChart.destroy();
            currentChart = null;
        }

        // Create new chart
        currentChart = new Chart(ctx, {
            type: chartConfig.type,
            data: {
                labels: chartConfig.labels,
                datasets: chartConfig.datasets.map((dataset, index) => ({
                    ...dataset,
                    backgroundColor: chartConfig.type === 'pie' 
                        ? generateColors(chartConfig.labels.length)
                        : generateColors(1)[0],
                    borderColor: chartConfig.type === 'line' 
                        ? generateColors(1)[0]
                        : undefined,
                    borderWidth: chartConfig.type === 'line' ? 2 : 1,
                    fill: chartConfig.type === 'line' ? false : undefined
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: chartConfig.type === 'pie' ? {} : {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        console.log('Chart created successfully');
    } catch (error) {
        console.error('Error creating chart:', error);
        showChartPlaceholder('Error membuat chart: ' + error.message);
    }
}

function showChartPlaceholder(message) {
    const canvas = document.getElementById('resultChart');
    const placeholder = document.getElementById('chartPlaceholder');
    
    if (canvas) canvas.style.display = 'none';
    if (placeholder) {
        placeholder.style.display = 'flex';
        placeholder.innerHTML = `<p class="text-muted">${message}</p>`;
    }
}

function generateColors(count) {
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
    ];
    
    return colors.slice(0, count);
}

function exportExcel() {
    if (!currentData || !currentColumns) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    const excelForm = document.getElementById('exportExcelForm');
    if (!excelForm) {
        alert('Form export tidak ditemukan');
        return;
    }

    document.getElementById('excelData').value = JSON.stringify(currentData);
    document.getElementById('excelColumns').value = JSON.stringify(currentColumns);
    document.getElementById('excelQuery').value = currentQuery;
    excelForm.submit();
}

function exportPDF() {
    if (!currentData || !currentColumns) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    const pdfForm = document.getElementById('exportPDFForm');
    if (!pdfForm) {
        alert('Form export tidak ditemukan');
        return;
    }

    document.getElementById('pdfData').value = JSON.stringify(currentData);
    document.getElementById('pdfColumns').value = JSON.stringify(currentColumns);
    document.getElementById('pdfQuery').value = currentQuery;
    pdfForm.submit();
}

// Auto focus input on load
document.addEventListener('DOMContentLoaded', function() {
    const queryInput = document.getElementById('queryInput');
    if (queryInput) {
        queryInput.focus();
    }
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js is not loaded. Charts will not be available.');
    }
    
    // Debug: Check if canvas exists
    const canvas = document.getElementById('resultChart');
    console.log('Canvas element found:', !!canvas);
});

// Add error handling for uncaught errors
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

// Add unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
});
</script>
<?= $this->endSection('script'); ?>