<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/buttons.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(90deg, #4a00e0, #8e2de2);
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #e2ebf0 100%);
            --card-bg: rgba(255, 255, 255, 0.75);
            --border-color: rgba(255, 255, 255, 0.4);
            --code-bg: #2d3748;
            --code-color: #e2e8f0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
        }
        .main-card {
            background: var(--card-bg);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .main-title {
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; color: transparent;
        }
        #query-input {
            border-radius: 0.5rem; border-color: #ced4da;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
        #query-input:focus {
            border-color: #8e2de2;
            box-shadow: 0 0 0 0.25rem rgba(142, 45, 226, 0.25);
        }
        .query-type-container .form-check-input { display: none; }
        .query-type-container .form-check-label {
            cursor: pointer; padding: 0.5rem 1rem; border-radius: 0.5rem;
            border: 1px solid #ced4da; transition: all 0.2s ease-in-out;
        }
        .query-type-container .form-check-input:checked + .form-check-label {
            background: var(--primary-gradient); color: white; border-color: transparent;
        }
        #submit-btn {
            background: var(--primary-gradient); border: none; border-radius: 0.5rem;
            padding: 0.75rem 1.5rem; font-weight: 500; transition: all 0.2s ease-in-out;
        }
        #submit-btn:hover {
            transform: translateY(-2px); box-shadow: 0 4px 15px rgba(142, 45, 226, 0.4);
        }
        #generated-sql-card {
            background-color: var(--code-bg); color: var(--code-color);
            border-radius: 0.5rem; position: relative;
        }
        #generated-sql-card code {
            font-family: 'Courier New', Courier, monospace;
            white-space: pre-wrap; word-break: break-all;
        }
        #copy-sql-btn {
            position: absolute; top: 0.5rem; right: 0.5rem;
            background: rgba(255, 255, 255, 0.1); color: white;
            border: none; border-radius: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.8rem;
        }
        .table-responsive {
            border-radius: 0.5rem;
            overflow: visible;
        }
        .table thead {
            background: var(--primary-gradient); 
            color: white;
        }
        .table thead th {
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* DataTables Custom Styling */
        .dataTables_wrapper {
            font-family: 'Poppins', sans-serif;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 0.75rem 0;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #8e2de2;
            box-shadow: 0 0 0 0.25rem rgba(142, 45, 226, 0.25);
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.25rem 0.5rem;
            margin: 0 0.5rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            margin: 0 0.125rem;
            border: 1px solid transparent !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gradient) !important;
            border-color: transparent !important;
            color: white !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(142, 45, 226, 0.1) !important;
            border-color: #8e2de2 !important;
            color: #8e2de2 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #6c757d !important;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(142, 45, 226, 0.02);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(142, 45, 226, 0.05);
        }
        .dataTables_scrollBody::-webkit-scrollbar {
            height: 8px;
        }
        .dataTables_scrollBody::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: #8e2de2;
            border-radius: 10px;
        }
        .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
            background: #6b23b8;
        }
        .sidebar {
            height: calc(100vh - 4rem);
            overflow-y: auto;
        }
        .list-item {
            list-style: none;
            padding: 0;
        }
        .list-item-element {
            display: flex; justify-content: space-between; align-items: center;
            border-radius: 0.5rem; transition: background-color 0.2s ease;
        }
        .list-item-element:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .list-item-element a.item-link {
            flex-grow: 1; padding: 0.75rem 1rem; color: #495057; text-decoration: none;
        }
        .list-item-element a.item-link:hover {
            color: #000;
        }
        .list-item-element .schema-btn {
            color: #8e2de2; padding: 0.75rem; cursor: pointer;
            text-decoration: none;
        }
        .list-item-element a.item-link i {
            color: #8e2de2;
        }
        .modal-header {
            background: var(--primary-gradient); color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-lg-3">
                <div class="main-card sidebar">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-table me-2"></i>
                            Daftar Tabel
                        </h5>
                        
                        <div id="table-list-container">
                            <div class="text-center text-muted" id="list-loader" style="display: none;">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <span class="ms-2">Memuat...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="main-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="h2 fw-bold main-title"> 
                                <i class="fas fa-brain"></i> <?= $title ?>
                            </h1>
                            <p class="text-muted">Jalankan query SELECT atau minta AI membuat query kompleks.</p>
                        </div>

                        <form id="query-form">
                            <div class="mb-3">
                                <textarea class="form-control" id="query-input" rows="4" 
                                placeholder="Contoh: 'tampilkan semua pengguna' ATAU 'SELECT * FROM users;'" required></textarea>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="query-type-container d-flex gap-2 align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="queryType" id="ai-type" value="ai" checked>
                                        <label class="form-check-label" for="ai-type">🧠 AI Query</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="queryType" id="sql-type" value="sql">
                                        <label class="form-check-label" for="sql-type">⌨️ Manual SQL</label>
                                    </div>
                                    <div class="form-check form-switch ms-3" id="dangerous-mode-toggle-container">
                                        <input class="form-check-input" type="checkbox" role="switch" id="dangerous-mode-toggle">
                                        <label class="form-check-label small" for="dangerous-mode-toggle">Izinkan AI membuat query DDL/DML</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-play me-2"></i> Jalankan Query
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div id="results-area">
                            <div class="text-center d-none" id="loading"> 
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div> 
                                <p class="mt-2 text-muted">AI sedang berpikir...</p> 
                            </div>
                            <div class="alert alert-danger d-none" id="error-box"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3 d-none" id="export-actions-container"> 
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-sm" id="export-btn"> 
                                        <i class="fas fa-file-excel me-2"></i> Export to Excel 
                                    </button>
                                    <button class="btn btn-info btn-sm" id="export-csv-btn"> 
                                        <i class="fas fa-file-csv me-2"></i> Export to CSV 
                                    </button>
                                    <button class="btn btn-secondary btn-sm" id="print-btn"> 
                                        <i class="fas fa-print me-2"></i> Print 
                                    </button>
                                </div>
                                <div>
                                    <small class="text-muted" id="table-info"></small>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning d-none" id="dangerous-query-warning">
                                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Query Berbahaya Terdeteksi!</h5>
                                <p>AI telah menghasilkan query yang dapat mengubah data atau struktur tabel. Aplikasi ini <strong>tidak akan menjalankannya secara otomatis</strong> untuk keamanan.</p>
                                <p class="mb-0">Silakan periksa query yang dihasilkan di bawah ini.</p>
                            </div>

                            <div class="card mb-3 d-none" id="generated-sql-card">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-white-50">SQL yang Dihasilkan oleh AI:</h6>
                                    <pre class="mb-0"><code id="generated-sql-code"></code></pre>
                                    <button id="copy-sql-btn" title="Salin SQL"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="table-responsive" id="results-table"></div>
                        </div>
                    </div>
                </div>
                <footer class="text-center text-muted mt-4">
                    <small>Dibuat dengan CodeIgniter 4 dan Gemini AI</small>
                </footer>
            </div>
        </div>
    </div>

    <!-- Schema Modal -->
    <div class="modal fade" id="schemaModal" tabindex="-1" aria-labelledby="schemaModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="schemaModalLabel">
                <i class="fas fa-info-circle me-2"></i>Desain Tabel: <span id="modalTableName"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="modal-body-content"></div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons Extension -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/buttons.print.min.js"></script>
    <script>
        // Global DataTable variable
        let dataTable = null;
        const baseUrl = '<?= base_url("laporanai") ?>';
        
        $(document).ready(function() {
            // Convert existing vanilla JS to jQuery compatible
            const form = document.getElementById('query-form');
            const queryInput = document.getElementById('query-input');
            const submitBtn = document.getElementById('submit-btn');
            const sqlRadio = document.getElementById('sql-type');
            const aiRadio = document.getElementById('ai-type');
            const loading = document.getElementById('loading');
            const errorBox = document.getElementById('error-box');
            const generatedSqlCard = document.getElementById('generated-sql-card');
            const generatedSqlCode = document.getElementById('generated-sql-code');
            const resultsTable = document.getElementById('results-table');
            const copySqlBtn = document.getElementById('copy-sql-btn');
            const exportActionsContainer = document.getElementById('export-actions-container');
            const exportBtn = document.getElementById('export-btn');
            const exportCsvBtn = document.getElementById('export-csv-btn');
            const printBtn = document.getElementById('print-btn');
            const tableInfo = document.getElementById('table-info');
            const dangerousModeToggle = document.getElementById('dangerous-mode-toggle');
            const dangerousModeContainer = document.getElementById('dangerous-mode-toggle-container');
            const dangerousQueryWarning = document.getElementById('dangerous-query-warning');
            
            // Sidebar elements
            const tableListContainer = document.getElementById('table-list-container');
            const listLoader = document.getElementById('list-loader');
            
            // Modal elements
            const schemaModal = new bootstrap.Modal(document.getElementById('schemaModal'));
            const modalTableName = document.getElementById('modalTableName');
            const modalBodyContent = document.getElementById('modal-body-content');

            // State variables
            let lastSuccessfulQuery = { query: '', type: '' };

            // Event listeners
            [aiRadio, sqlRadio].forEach(radio => {
                radio.addEventListener('change', () => {
                    dangerousModeContainer.style.display = aiRadio.checked ? 'block' : 'none';
                });
            });

            // Load table list initially
            async function loadTableList() {
                try {
                    listLoader.style.display = 'block';
                    
                    const response = await fetch(`${baseUrl}/getTables`);
                    const data = await response.json();
                    
                    if (!response.ok) throw new Error(data.error || 'Gagal memuat tabel.');
                    
                    listLoader.style.display = 'none';
                    
                    if (data.tables && data.tables.length > 0) {
                        renderTableList(data.tables);
                    } else {
                        tableListContainer.innerHTML = '<p class="text-muted small">Tidak ada tabel ditemukan.</p>';
                    }
                } catch (err) {
                    listLoader.style.display = 'none';
                    tableListContainer.innerHTML = `<p class="text-danger small">${err.message}</p>`;
                }
            }

            function renderTableList(tables) {
                const ul = document.createElement('ul');
                ul.className = 'list-item';
                
                tables.forEach(table => {
                    const li = document.createElement('li');
                    li.className = 'list-item-element';
                    li.innerHTML = `
                        <a href="#" class="item-link" data-table-name="${table}">
                            <i class="fas fa-table me-2"></i>${table}
                        </a>
                        <a href="#" class="schema-btn" data-bs-toggle="modal" data-bs-target="#schemaModal" 
                           data-table-name="${table}" title="Lihat Desain Tabel">
                            <i class="fas fa-info-circle"></i>
                        </a>`;
                    ul.appendChild(li);
                });
                
                tableListContainer.innerHTML = '';
                tableListContainer.appendChild(ul);
                
                // Add click events for table selection and schema viewing
                ul.addEventListener('click', function(e) {
                    const target = e.target.closest('a');
                    if (!target) return;
                    e.preventDefault();
                    
                    const tableName = target.getAttribute('data-table-name');
                    
                    if (target.classList.contains('item-link')) {
                        queryInput.value = `SELECT * FROM ${tableName} LIMIT 20;`;
                        sqlRadio.checked = true;
                        dangerousModeContainer.style.display = 'none';
                        submitBtn.click();
                    }
                    
                    if (target.classList.contains('schema-btn')) {
                        showTableSchema(tableName);
                    }
                });
            }

            async function showTableSchema(tableName) {
                modalTableName.textContent = tableName;
                modalBodyContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
                
                try {
                    const response = await fetch(`${baseUrl}/getTableSchema/${tableName}`);
                    const data = await response.json();
                    
                    if (!response.ok) throw new Error(data.error);
                    
                    let tableHTML = '<table class="table table-sm table-bordered"><thead><tr><th>Nama Kolom</th><th>Tipe Data</th><th>PK</th><th>Boleh Kosong (Nullable)</th></tr></thead><tbody>';
                    data.schema.forEach(col => {
                        tableHTML += `
                            <tr>
                                <td><strong>${col.name}</strong></td>
                                <td>${col.type}</td>
                                <td>${col.primary_key ? '<i class="fas fa-key text-warning"></i>' : ''}</td>
                                <td>${col.nullable ? 'Ya' : 'Tidak'}</td>
                            </tr>`;
                    });
                    tableHTML += '</tbody></table>';
                    modalBodyContent.innerHTML = tableHTML;
                } catch (err) {
                    modalBodyContent.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
                }
            }

            // Form submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                submitBtn.disabled = true;
                loading.classList.remove('d-none');
                errorBox.classList.add('d-none');
                generatedSqlCard.classList.add('d-none');
                resultsTable.innerHTML = '';
                exportActionsContainer.classList.add('d-none');
                dangerousQueryWarning.classList.add('d-none');
                
                const query = queryInput.value;
                const type = document.querySelector('input[name="queryType"]:checked').value;
                const is_dangerous_mode = dangerousModeToggle.checked;
                
                try {
                    const response = await fetch(`${baseUrl}/query`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            query, 
                            type, 
                            is_dangerous_mode
                        }) 
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) { 
                        throw new Error(data.error || 'Terjadi kesalahan.'); 
                    }
                    
                    if(data.generated_sql) {
                        generatedSqlCode.textContent = data.generated_sql;
                        generatedSqlCard.classList.remove('d-none');
                    }
                    
                    if (data.is_dangerous) {
                        dangerousQueryWarning.classList.remove('d-none');
                        resultsTable.innerHTML = '';
                    } else {
                        renderTable(data.headers, data.rows);
                    }
                    
                    if (data.rows && data.rows.length > 0) {
                        lastSuccessfulQuery = { query, type };
                    } else {
                        exportActionsContainer.classList.add('d-none');
                    }
                } catch (err) {
                    errorBox.textContent = err.message;
                    errorBox.classList.remove('d-none');
                } finally {
                    loading.classList.add('d-none');
                    submitBtn.disabled = false;
                }
            });

            // Export functionality
            exportBtn.addEventListener('click', async () => {
                exportBtn.disabled = true;
                exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengekspor...';
                
                try {
                    const response = await fetch(`${baseUrl}/export`, { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json' }, 
                        body: JSON.stringify(lastSuccessfulQuery) 
                    });
                    
                    if (!response.ok) {
                        const errData = await response.json();
                        throw new Error(errData.error || 'Gagal mengekspor.');
                    }
                    
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a'); 
                    a.style.display = 'none'; 
                    a.href = url; 
                    a.download = 'hasil_query.xlsx'; 
                    document.body.appendChild(a); 
                    a.click(); 
                    window.URL.revokeObjectURL(url); 
                    a.remove();
                } catch (err) {
                    errorBox.textContent = err.message;
                    errorBox.classList.remove('d-none');
                } finally {
                    exportBtn.disabled = false;
                    exportBtn.innerHTML = '<i class="fas fa-file-excel me-2"></i> Export to Excel';
                }
            });

            // CSV Export functionality
            exportCsvBtn.addEventListener('click', () => {
                if (dataTable) {
                    dataTable.button('.buttons-csv').trigger();
                }
            });

            // Print functionality
            printBtn.addEventListener('click', () => {
                if (dataTable) {
                    dataTable.button('.buttons-print').trigger();
                }
            });

            // Copy SQL functionality
            copySqlBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(generatedSqlCode.textContent).then(() => {
                    const originalIcon = copySqlBtn.innerHTML;
                    copySqlBtn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
                    setTimeout(() => { copySqlBtn.innerHTML = originalIcon; }, 2000);
                });
            });

            function renderTable(headers, rows) {
                // Destroy existing DataTable if it exists
                if (dataTable) {
                    dataTable.destroy();
                    dataTable = null;
                }

                if (!rows) {
                    resultsTable.innerHTML = `<div class="alert alert-success">Perintah berhasil dieksekusi.</div>`;
                    return;
                }
                if (rows.length === 0) {
                    resultsTable.innerHTML = `<div class="alert alert-info">Query berhasil, namun tidak ada data yang ditemukan.</div>`;
                    return;
                }

                // Build table HTML
                let tableHTML = `<table id="resultsDataTable" class="table table-bordered table-striped table-hover w-100">`;
                tableHTML += `<thead><tr>`;
                headers.forEach(header => { 
                    tableHTML += `<th>${header}</th>`; 
                });
                tableHTML += `</tr></thead>`;
                tableHTML += `<tbody>`;
                rows.forEach(row => {
                    tableHTML += `<tr>`;
                    headers.forEach(header => { 
                        let cellValue = row[header];
                        if (cellValue === null || cellValue === undefined) {
                            cellValue = '<span class="text-muted fst-italic">null</span>';
                        } else if (cellValue === '') {
                            cellValue = '<span class="text-muted fst-italic">empty</span>';
                        }
                        tableHTML += `<td>${cellValue}</td>`; 
                    });
                    tableHTML += `</tr>`;
                });
                tableHTML += `</tbody></table>`;
                
                resultsTable.innerHTML = tableHTML;

                // Initialize DataTable
                setTimeout(() => {
                    dataTable = $('#resultsDataTable').DataTable({
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: {
                            decimal: "",
                            emptyTable: "Tidak ada data tersedia",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                            infoFiltered: "(disaring dari _MAX_ total entri)",
                            lengthMenu: "Tampilkan _MENU_ entri",
                            loadingRecords: "Memuat...",
                            processing: "Memproses...",
                            search: "Cari:",
                            zeroRecords: "Tidak ditemukan data yang sesuai",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        },
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'csv',
                                text: 'CSV',
                                className: 'btn btn-info btn-sm d-none',
                                filename: function() {
                                    return `hasil_query_${new Date().toISOString().slice(0, 10)}`;
                                }
                            },
                            {
                                extend: 'print',
                                text: 'Print',
                                className: 'btn btn-secondary btn-sm d-none',
                                title: 'Hasil Query Database',
                                messageTop: function() {
                                    const now = new Date().toLocaleString('id-ID');
                                    return `<div class="print-header">
                                        <h4>Hasil Query Database</h4>
                                        <p><strong>Tanggal:</strong> ${now}</p>
                                        <p><strong>Total Record:</strong> ${rows.length}</p>
                                    </div>`;
                                }
                            }
                        ],
                        order: [],
                        columnDefs: [
                            {
                                targets: '_all',
                                className: 'align-top'
                            }
                        ],
                        scrollX: true,
                        fixedHeader: true
                    });

                    // Show export actions and update table info
                    exportActionsContainer.classList.remove('d-none');
                    tableInfo.textContent = `${rows.length} record(s) ditemukan`;

                    // Custom search functionality
                    $('#resultsDataTable_filter input').attr('placeholder', 'Ketik untuk mencari...');
                }, 100);
            }

            // Initialize by loading table list
            loadTableList();
        });

        // Add custom print styles
        const printStyles = `
            <style type="text/css">
                @media print {
                    .print-header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #8e2de2;
                        padding-bottom: 10px;
                    }
                    .print-header h4 {
                        color: #8e2de2;
                        margin-bottom: 10px;
                    }
                    table {
                        font-size: 12px !important;
                    }
                    th {
                        background-color: #8e2de2 !important;
                        color: white !important;
                        -webkit-print-color-adjust: exact;
                    }
                }
            </style>
        `;
        $('head').append(printStyles);
    </script>
</body>
</html>