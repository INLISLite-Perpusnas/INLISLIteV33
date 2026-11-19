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

    .query-content {
        flex: 1;
        padding: 15px;
        overflow: auto;
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
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="container-fluid">
    <div class="row mb-4">

    </div>

    <div class="db-explorer">
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

            <!-- Hasil query -->
            <div class="query-content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="pagination-info">
                        <span id="data-info">Ajukan pertanyaan untuk melihat hasilnya</span>
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
                            <i class="fas fa-robot fa-3x mb-3"></i>
                            <p>Tulis pertanyaan di atas, lalu klik Ask AI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
let currentSQL = '';

// Initialize
$(document).ready(function() {
    // Handle Enter key in natural query input
    $('#natural-query').keypress(function(e) {
        if (e.which === 13) {
            convertToSQL();
        }
    });

    // Initialize sample queries rotation
    initializeSampleQueries();
});

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
                currentSQL = response.sql;

                // Tampilkan hasilnya langsung, tanpa menampilkan SQL-nya ke user
                executeQuery(response.sql);
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

// Execute SQL query (dipanggil internal setelah AI menghasilkan SQL dari natural-to-sql,
// SQL-nya sendiri tidak pernah ditampilkan ke user)
function executeQuery(sql) {
    if (!sql) {
        showError('Query tidak valid');
        return;
    }

    currentSQL = sql;

    $('#data-info').text('Menjalankan query...');
    $('#data-loading').show();
    $('#data-content').hide();

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
                $('#data-info').text('Query gagal dijalankan');
                $('#data-content').html(`
                    <div class="alert alert-danger">
                        <strong>Query Error:</strong> ${response.message}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error executing query:', xhr.responseText);
            $('#data-info').text('Query gagal dijalankan');
            $('#data-content').html(`
                <div class="alert alert-danger">
                    <strong>Error:</strong> Failed to execute query - ${error}
                </div>
            `);
        },
        complete: function() {
            $('#data-loading').hide();
            $('#data-content').show();
        }
    });
}

// Render query result (hasil dari natural-language query)
function renderQueryResult(data, affectedRows) {
    console.log('Rendering query result:', data);
    const container = $('#data-content');

    if (!data || data.length === 0) {
        $('#data-info').text(`${affectedRows || 0} rows affected`);
        container.html(`
            <div class="alert alert-info">
                <strong>Query berhasil dijalankan.</strong> ${affectedRows || 0} rows affected. Tidak ada data untuk ditampilkan.
            </div>
        `);
        return;
    }

    $('#data-info').text(`${affectedRows || data.length} rows returned`);

    // Create table
    const headers = Object.keys(data[0]);
    let tableHTML = `
        <div class="mb-3">
            <div class="alert alert-success">
                <strong>Query berhasil dijalankan!</strong> ${affectedRows || data.length} rows returned.
            </div>
        </div>
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
}

// Export to Excel
function exportToExcel() {
    if (!currentSQL) {
        showError('No data to export');
        return;
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
        value: currentSQL
    });

    form.append(input);
    $('body').append(form);
    form.submit();
    form.remove();

    showSuccess('Export started! Check your downloads.');
}

// Utility functions
function showSuccess(message) {
    console.log('Showing success:', message);
    const toast = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    // Ctrl + Enter to ask AI
    if ((e.ctrlKey || e.metaKey) && e.which === 13) {
        if ($('#natural-query').is(':focus')) {
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
