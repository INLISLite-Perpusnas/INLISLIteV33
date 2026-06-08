<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .db-explorer {
        display: flex;
        height: calc(100vh - 200px);
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .sidebar {
        width: 300px;
        background: #f8f9fa;
        border-right: 1px solid #e0e0e0;
        overflow-y: auto;
    }
    
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
    }
    
    .ai-query-panel {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        background: #f8f9fa;
    }
    
    .query-tabs {
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        background: #f8f9fa;
    }
    
    .query-tabs .nav-link {
        border-radius: 0;
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
    }
    
    .query-tabs .nav-link.active {
        border-bottom-color: #007bff;
        color: #007bff;
        background: white;
    }
    
    .query-content {
        flex: 1;
        padding: 15px;
        overflow: auto;
    }
    
    .table-item {
        padding: 8px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }
    
    .table-item:hover {
        background: #e9ecef;
    }
    
    .table-item.active {
        background: #007bff;
        color: white;
    }
    
    .table-icon {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23007bff"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm0-4H9V5h10v2z"/></svg>') no-repeat center;
    }
    
    .table-info {
        font-size: 0.8em;
        color: #6c757d;
        margin-left: auto;
    }
    
    .active .table-info {
        color: rgba(255,255,255,0.8);
    }
    
    .sql-editor {
        height: 200px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        padding: 10px;
        resize: vertical;
    }
    
    .data-table-container {
        overflow: auto;
        max-height: 400px;
    }
    
    .data-table {
        font-size: 0.9em;
    }
    
    .data-table th {
        background: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .ai-input-group {
        position: relative;
    }
    
    .ai-input {
        padding-right: 50px;
    }
    
    .ai-button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: #007bff;
        color: white;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 0.8em;
    }
    
    .loading {
        display: none;
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }
    
    .pagination-info {
        display: flex;
        justify-content: between;
        align-items: center;
        margin: 10px 0;
    }
    
    .structure-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    .key-primary {
        background: #ffd700;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.8em;
        font-weight: bold;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Database Explorer - AI SQL Assistant</h2>
            <p class="text-muted">Explore your database with natural language queries powered by AI</p>
        </div>
    </div>
    
    <div class="db-explorer">
        <!-- Sidebar with tables -->
        <div class="sidebar">
            <div class="p-3 border-bottom">
                <h6 class="mb-0">Database Tables</h6>
                <small class="text-muted" id="db-info">Loading...</small>
            </div>
            <div id="tables-list">
                <div class="loading" style="display: block;">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2">Loading tables...</div>
                </div>
            </div>
        </div>
        
        <!-- Main content area -->
        <div class="main-content">
            <!-- AI Query Panel -->
            <div class="ai-query-panel">
                <div class="mb-2">
                    <label class="form-label">🤖 Ask in natural language:</label>
                    <div class="ai-input-group">
                        <input type="text" class="form-control ai-input" id="natural-query" 
                               placeholder="e.g., Show all users who registered last month">
                        <button class="ai-button" onclick="convertToSQL()">Ask AI</button>
                    </div>
                </div>
            </div>
            
            <!-- Query tabs -->
            <div class="query-tabs">
                <ul class="nav nav-tabs flex-fill" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#data-tab">Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#structure-tab">Structure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#query-tab">SQL Query</a>
                    </li>
                </ul>
            </div>
            
            <!-- Tab content -->
            <div class="query-content">
                <div class="tab-content">
                    <!-- Data Tab -->
                    <div class="tab-pane fade show active" id="data-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="pagination-info">
                                <span id="data-info">Select a table to view data</span>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportToExcel()" id="export-btn" disabled>
                                    📊 Export Excel
                                </button>
                            </div>
                        </div>
                        
                        <div class="data-table-container">
                            <div id="data-loading" class="loading">
                                <div class="spinner-border" role="status"></div>
                                <div class="mt-2">Loading data...</div>
                            </div>
                            <div id="data-content">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-table fa-3x mb-3"></i>
                                    <p>Select a table from the sidebar to view its data</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <nav id="data-pagination" style="display: none;">
                            <ul class="pagination justify-content-center mt-3">
                            </ul>
                        </nav>
                    </div>
                    
                    <!-- Structure Tab -->
                    <div class="tab-pane fade" id="structure-tab">
                        <div id="structure-content">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-sitemap fa-3x mb-3"></i>
                                <p>Select a table to view its structure</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Query Tab -->
                    <div class="tab-pane fade" id="query-tab">
                        <div class="mb-3">
                            <label class="form-label">SQL Query:</label>
                            <textarea class="sql-editor" id="sql-editor" placeholder="SELECT * FROM table_name LIMIT 10;"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <button class="btn btn-primary" onclick="executeQuery()">
                                ▶️ Execute Query
                            </button>
                            <button class="btn btn-outline-secondary" onclick="clearQuery()">
                                🗑️ Clear
                            </button>
                        </div>
                        
                        <div id="query-result">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-code fa-3x mb-3"></i>
                                <p>Write your SQL query above and click Execute</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let currentTable = '';
let currentPage = 1;
let currentSQL = '';

// Initialize
$(document).ready(function() {
    console.log('Document ready, loading tables...');
    loadTables();
    
    // Handle Enter key in natural query input
    $('#natural-query').keypress(function(e) {
        if (e.which === 13) {
            convertToSQL();
        }
    });
    
    // Initialize sample queries rotation
    initializeSampleQueries();
});

// Load database tables
function loadTables() {
    console.log('Loading tables...');
    $.ajax({
        url: '<?= base_url('api/laporan-ai/tables') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Tables response:', response);
            if (response.status === 'success') {
                renderTables(response.data);
                $('#db-info').text(`${response.data.length} tables found`);
            } else {
                showError('Failed to load tables: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Primary endpoint failed, trying alternative...');
            console.log('Error details:', xhr.responseText);
            loadTablesAlternative();
        }
    });
}

// Load tables using alternative method
function loadTablesAlternative() {
    console.log('Loading tables with alternative method...');
    $.ajax({
        url: '<?= base_url('api/laporan-ai/tables-alt') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Alternative tables response:', response);
            if (response.status === 'success') {
                renderTables(response.data);
                $('#db-info').text(`${response.data.length} tables found`);
                showSuccess('Database connected successfully!');
            } else {
                showError('Failed to load tables: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Alternative endpoint also failed:', xhr.responseText);
            showError('Failed to connect to database. Please check your database configuration.');
        }
    });
}

// Render tables list
function renderTables(tables) {
    console.log('Rendering tables:', tables);
    const container = $('#tables-list');
    container.empty();
    
    if (!tables || tables.length === 0) {
        container.html(`
            <div class="text-center text-muted py-5">
                <i class="fas fa-database fa-3x mb-3"></i>
                <p>No tables found in database</p>
            </div>
        `);
        return;
    }
    
    tables.forEach(table => {
        const item = $(`
            <div class="table-item" data-table="${table.name}">
                <div class="table-icon"></div>
                <div class="flex-grow-1">
                    <div class="fw-bold">${table.name}</div>
                </div>
                <div class="table-info">
                    ${table.row_count} rows
                </div>
            </div>
        `);
        
        item.click(function() {
            selectTable(table.name);
        });
        
        container.append(item);
    });
}

// Select table
function selectTable(tableName) {
    console.log('Selecting table:', tableName);
    $('.table-item').removeClass('active');
    $(`.table-item[data-table="${tableName}"]`).addClass('active');
    
    currentTable = tableName;
    currentPage = 1;
    
    // Load table data and structure
    loadTableData(tableName);
    loadTableStructure(tableName);
    
    // Switch to data tab
    $('.nav-link[href="#data-tab"]').tab('show');
}

// Load table data
function loadTableData(tableName, page = 1) {
    console.log('Loading table data for:', tableName, 'page:', page);
    $('#data-loading').show();
    $('#data-content').hide();
    
    $.ajax({
        url: `<?= base_url('api/laporan-ai/table-data/') ?>${tableName}?page=${page}&limit=50`,
        method: 'GET',
        success: function(response) {
            console.log('Table data response:', response);
            if (response.status === 'success') {
                renderTableData(response.data, response.pagination);
                $('#export-btn').prop('disabled', false);
            } else {
                showError('Failed to load data: ' + response.message);
            }
            $('#data-loading').hide();
            $('#data-content').show();
        },
        error: function(xhr, status, error) {
            console.log('Error loading table data:', xhr.responseText);
            showError('Failed to load table data: ' + error);
            $('#data-loading').hide();
            $('#data-content').show();
        }
    });
}

// Render table data - FIXED VERSION
function renderTableData(data, pagination) {
    console.log('Rendering table data:', data);
    const container = $('#data-content');
    
    if (!data || data.length === 0) {
        container.html(`
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>No data found in this table</p>
            </div>
        `);
        return;
    }
    
    // Create table
    const headers = Object.keys(data[0]);
    let tableHTML = `
        <table class="table table-bordered table-hover data-table">
            <thead>
                <tr>
    `;
    
    headers.forEach(header => {
        tableHTML += `<th>${header}</th>`;
    });
    
    tableHTML += `
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(row => {
        tableHTML += '<tr>';
        headers.forEach(header => {
            let value = row[header];
            if (value === null) {
                value = '<span class="text-muted">NULL</span>';
            } else if (typeof value === 'string' && value.length > 100) {
                value = value.substring(0, 100) + '...';
            } else if (typeof value === 'object') {
                value = JSON.stringify(value);
            }
            tableHTML += `<td>${value}</td>`;
        });
        tableHTML += '</tr>';
    });
    
    tableHTML += `
            </tbody>
        </table>
    `;
    
    container.html(tableHTML);
    
    // Update pagination info
    if (pagination) {
        $('#data-info').text(
            `Showing ${((pagination.current_page - 1) * pagination.limit) + 1} to ${Math.min(pagination.current_page * pagination.limit, pagination.total_rows)} of ${pagination.total_rows} rows`
        );
        
        // Render pagination
        if (pagination.total_pages > 1) {
            renderPagination(pagination);
        } else {
            $('#data-pagination').hide();
        }
    }
}

// Load table structure
function loadTableStructure(tableName) {
    console.log('Loading table structure for:', tableName);
    $.ajax({
        url: `<?= base_url('api/laporan-ai/table-structure/') ?>${tableName}`,
        method: 'GET',
        success: function(response) {
            console.log('Table structure response:', response);
            if (response.status === 'success') {
                renderTableStructure(response.data);
            } else {
                showError('Failed to load structure: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error loading table structure:', xhr.responseText);
            showError('Failed to load table structure: ' + error);
        }
    });
}

// Render table structure
function renderTableStructure(structure) {
    console.log('Rendering table structure:', structure);
    const container = $('#structure-content');
    
    if (!structure || structure.length === 0) {
        container.html(`
            <div class="text-center text-muted py-5">
                <i class="fas fa-sitemap fa-3x mb-3"></i>
                <p>No structure information available</p>
            </div>
        `);
        return;
    }
    
    let tableHTML = `
        <table class="table table-bordered structure-table">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    structure.forEach(column => {
        let keyBadge = '';
        if (column.Key === 'PRI') {
            keyBadge = '<span class="key-primary">PRIMARY</span>';
        } else if (column.Key === 'UNI') {
            keyBadge = '<span class="badge bg-info">UNIQUE</span>';
        } else if (column.Key === 'MUL') {
            keyBadge = '<span class="badge bg-secondary">INDEX</span>';
        }
        
        tableHTML += `
            <tr>
                <td><strong>${column.Field}</strong></td>
                <td><code>${column.Type}</code></td>
                <td>${column.Null === 'YES' ? '✓' : '✗'}</td>
                <td>${keyBadge}</td>
                <td>${column.Default || '<span class="text-muted">NULL</span>'}</td>
                <td>${column.Extra || ''}</td>
            </tr>
        `;
    });
    
    tableHTML += `
            </tbody>
        </table>
    `;
    
    container.html(tableHTML);
}

// Render pagination
function renderPagination(pagination) {
    const container = $('#data-pagination ul');
    container.empty();
    
    const { current_page, total_pages } = pagination;
    
    // Previous button
    const prevDisabled = current_page === 1 ? 'disabled' : '';
    container.append(`
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="changePage(${current_page - 1})">Previous</a>
        </li>
    `);
    
    // Page numbers
    let startPage = Math.max(1, current_page - 2);
    let endPage = Math.min(total_pages, current_page + 2);
    
    if (startPage > 1) {
        container.append(`<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>`);
        if (startPage > 2) {
            container.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const active = i === current_page ? 'active' : '';
        container.append(`
            <li class="page-item ${active}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `);
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            container.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
        container.append(`<li class="page-item"><a class="page-link" href="#" onclick="changePage(${total_pages})">${total_pages}</a></li>`);
    }
    
    // Next button
    const nextDisabled = current_page === total_pages ? 'disabled' : '';
    container.append(`
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="changePage(${current_page + 1})">Next</a>
        </li>
    `);
    
    $('#data-pagination').show();
}

// Change page
function changePage(page) {
    if (page < 1 || !currentTable) return;
    currentPage = page;
    loadTableData(currentTable, page);
}

// Convert natural language to SQL
function convertToSQL() {
    const naturalQuery = $('#natural-query').val().trim();
    
    if (!naturalQuery) {
        showError('Please enter a natural language query');
        return;
    }
    
    $('.ai-button').prop('disabled', true).html('🤖 Processing...');
    
    $.ajax({
        url: '<?= base_url('api/laporan-ai/natural-to-sql') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ query: naturalQuery }),
        success: function(response) {
            console.log('Natural to SQL response:', response);
            if (response.status === 'success') {
                $('#sql-editor').val(response.sql);
                currentSQL = response.sql;
                
                // Switch to query tab
                $('.nav-link[href="#query-tab"]').tab('show');
                
                // Show success message
                showSuccess('Query generated successfully! You can now execute it.');
                
                // Auto-execute if user wants
                if (confirm('Would you like to execute this query automatically?')) {
                    executeQuery();
                }
            } else {
                showError('Failed to generate SQL: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error converting natural language:', xhr.responseText);
            showError('Failed to process natural language query: ' + error);
        },
        complete: function() {
            $('.ai-button').prop('disabled', false).html('Ask AI');
        }
    });
}

// Execute SQL query
function executeQuery() {
    const sql = $('#sql-editor').val().trim();
    
    if (!sql) {
        showError('Please enter a SQL query');
        return;
    }
    
    currentSQL = sql;
    
    $('#query-result').html(`
        <div class="loading" style="display: block;">
            <div class="spinner-border" role="status"></div>
            <div class="mt-2">Executing query...</div>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url('api/laporan-ai/execute-query') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ sql: sql }),
        success: function(response) {
            console.log('Query execution response:', response);
            if (response.status === 'success') {
                renderQueryResult(response.data, response.affected_rows);
                $('#export-btn').prop('disabled', false);
            } else {
                $('#query-result').html(`
                    <div class="alert alert-danger">
                        <strong>Query Error:</strong> ${response.message}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error executing query:', xhr.responseText);
            $('#query-result').html(`
                <div class="alert alert-danger">
                    <strong>Error:</strong> Failed to execute query - ${error}
                </div>
            `);
        }
    });
}

// Render query result
function renderQueryResult(data, affectedRows) {
    console.log('Rendering query result:', data);
    const container = $('#query-result');
    
    if (!data || data.length === 0) {
        container.html(`
            <div class="alert alert-info">
                <strong>Query executed successfully!</strong> ${affectedRows || 0} rows affected. No data to display.
            </div>
        `);
        return;
    }
    
    // Create table
    const headers = Object.keys(data[0]);
    let tableHTML = `
        <div class="mb-3">
            <div class="alert alert-success">
                <strong>Query executed successfully!</strong> ${affectedRows || data.length} rows returned.
            </div>
        </div>
        <div class="data-table-container">
            <table class="table table-bordered table-hover data-table">
                <thead>
                    <tr>
    `;
    
    headers.forEach(header => {
        tableHTML += `<th>${header}</th>`;
    });
    
    tableHTML += `
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.forEach(row => {
        tableHTML += '<tr>';
        headers.forEach(header => {
            let value = row[header];
            if (value === null) {
                value = '<span class="text-muted">NULL</span>';
            } else if (typeof value === 'string' && value.length > 100) {
                value = value.substring(0, 100) + '...';
            } else if (typeof value === 'object') {
                value = JSON.stringify(value);
            }
            tableHTML += `<td>${value}</td>`;
        });
        tableHTML += '</tr>';
    });
    
    tableHTML += `
                </tbody>
            </table>
        </div>
    `;
    
    container.html(tableHTML);
}

// Clear query
function clearQuery() {
    $('#sql-editor').val('');
    $('#query-result').html(`
        <div class="text-center text-muted py-5">
            <i class="fas fa-code fa-3x mb-3"></i>
            <p>Write your SQL query above and click Execute</p>
        </div>
    `);
    currentSQL = '';
}

// Export to Excel
function exportToExcel() {
    if (!currentSQL && !currentTable) {
        showError('No data to export');
        return;
    }
    
    let sql = currentSQL;
    if (!sql && currentTable) {
        sql = `SELECT * FROM \`${currentTable}\``;
    }
    
    // Create form and submit
    const form = $('<form>', {
        method: 'POST',
        action: '<?= base_url('api/laporan-ai/export-excel') ?>',
        target: '_blank'
    });
    
    const input = $('<input>', {
        type: 'hidden',
        name: 'sql',
        value: sql
    });
    
    form.append(input);
    $('body').append(form);
    form.submit();
    form.remove();
    
    showSuccess('Export started! Check your downloads.');
}

// Utility functions
function showError(message) {
    console.log('Showing error:', message);
    const toast = `
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    const $toast = $(toast);
    $('.toast-container').append($toast);
    const bsToast = new bootstrap.Toast($toast[0]);
    bsToast.show();
    
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Sample natural language queries for demo
const sampleQueries = [
    "Show all users who registered this month",
    "Find products with low stock (less than 10)",
    "Get total sales by category this year",
    "List top 10 customers by purchase amount",
    "Show orders placed in the last 7 days",
    "Find users with no recent activity",
    "Count total records in each table",
    "Show latest 20 transactions",
    "Find duplicate email addresses",
    "Get monthly revenue summary"
];

// Initialize sample queries rotation
function initializeSampleQueries() {
    let currentSample = 0;
    
    setInterval(function() {
        if (!$('#natural-query').is(':focus') && !$('#natural-query').val()) {
            $('#natural-query').attr('placeholder', sampleQueries[currentSample]);
            currentSample = (currentSample + 1) % sampleQueries.length;
        }
    }, 3000);
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl + Enter to execute query
    if ((e.ctrlKey || e.metaKey) && e.which === 13) {
        if ($('#query-tab').hasClass('active')) {
            executeQuery();
        } else if ($('#natural-query').is(':focus')) {
            convertToSQL();
        }
        e.preventDefault();
    }
    
    // Escape to clear natural query
    if (e.which === 27 && $('#natural-query').is(':focus')) {
        $('#natural-query').val('');
    }
});

// Add tooltip for keyboard shortcuts
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Auto-refresh table list every 30 seconds
setInterval(function() {
    if (!currentTable) {
        loadTables();
    }
}, 30000);

// Handle window resize
$(window).resize(function() {
    // Adjust table containers on resize
    $('.data-table-container').each(function() {
        const container = $(this);
        const maxHeight = $(window).height() * 0.4;
        container.css('max-height', maxHeight + 'px');
    });
});

// Initialize tooltips and responsive adjustments
$(document).ready(function() {
    // Initialize tooltips
    $('[title]').tooltip();
    
    // Responsive table container height
    $(window).resize();
    
    // Add loading animation to buttons
    $(document).on('click', 'button[onclick]', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        if (!$btn.prop('disabled')) {
            $btn.prop('disabled', true);
            setTimeout(() => {
                $btn.prop('disabled', false);
            }, 2000);
        }
    });
});
</script>
<?= $this->endSection('script'); ?>