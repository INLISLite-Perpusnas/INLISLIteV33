<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'keanggotaan';
$member_id = $request->getGet('member_id') ?? 0;
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .main-card {
        max-width: 100%;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .card-header {
        min-width: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        width: 100%;
        overflow: visible !important;
    }

    .card-header .btn-actions-pane-right {
        margin-left: auto;
    }

    .card-body {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    #preview .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    #preview table {
        white-space: nowrap;
        min-width: 120%;
    }

    /* Loading overlay */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Import <?= lang('Anggota.module') ?>
                    <div class="page-title-subheading"><?= lang('Anggota.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                <?= lang('Anggota.label.home') ?></a></li>
                        <li class="breadcrumb-item"><a
                                href="<?= base_url('anggota') ?>"><?= lang('Anggota.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Import <?= lang('Anggota.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-layers icon-gradient bg-plum-plate"> </i>
            Form Upload <?= lang('Anggota.module') ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('uploads/master-template/template_anggota.xlsx') ?>" data-toggle="tooltip"
                    data-placement="top" title="Lihat Template" target="_blank" class="btn btn-secondary"
                    style="min-width:35px"><i class="fa fa-file-excel"> </i> Download Template</a>
            </div>
        </div>
        <div class="card-body">
            <form id="frm_import" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col-md-12">
                        <?= csrf_field() ?>
                        <label for="excel_file">Pilih file Excel:</label>
                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" onchange="previewExcel(event)" required>
                        <small class="form-text text-muted">Format file: .xlsx atau .xls (Max 5MB)</small>
                        <div id="preview" class="mt-3"></div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg mt-3" id="btn_submit">
                        <i class="fa fa-upload"></i> <?= lang('Anggota.action.save') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <h4>Sedang mengimport data...</h4>
        <p>Mohon tunggu, proses ini mungkin memakan waktu beberapa saat.</p>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewExcel(event) {
        const input = event.target;
        const reader = new FileReader();

        reader.onload = function() {
            const data = new Uint8Array(reader.result);
            const workbook = XLSX.read(data, {
                type: 'array'
            });
            const sheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[sheetName];

            const sheet_range = worksheet['!ref'];
            const json = XLSX.utils.sheet_to_json(worksheet, {
                header: 1,
                range: sheet_range
            });

            let table = '<table id="tbl_data" class="table table-hover table-striped table-bordered" border="1"><thead><tr>';
            const numColumns = json[0].length;

            // Header
            json[0].forEach(function(header) {
                table += `<th>${header}</th>`;
            });
            table += '</tr></thead><tbody>';

            // Data Rows (limit preview to 10 rows)
            const maxPreview = Math.min(json.length, 11); // 1 header + 10 data
            for (let i = 1; i < maxPreview; i++) {
                table += '<tr>';
                for (let j = 0; j < numColumns; j++) {
                    const cell = json[i][j] !== undefined && json[i][j] !== null ? json[i][j] : '';
                    table += `<td>${cell}</td>`;
                }
                table += '</tr>';
            }
            
            if (json.length > 11) {
                table += '<tr><td colspan="' + numColumns + '" class="text-center"><em>... dan ' + (json.length - 11) + ' baris lainnya</em></td></tr>';
            }
            
            table += '</tbody></table>';

            const wrappedTable = '<div class="table-responsive">' + table + '</div>';
            document.getElementById('preview').innerHTML = wrappedTable;
        };
        reader.readAsArrayBuffer(input.files[0]);
    }

    // Handle form submit dengan AJAX
    document.getElementById('frm_import').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('excel_file');
        const file = fileInput.files[0];
        
        // Validasi file
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Pilih file Excel terlebih dahulu.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Validasi ukuran file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar!',
                text: 'Ukuran file maksimal 5MB.',
                confirmButtonColor: '#d33'
            });
            return;
        }

        // Validasi extension
        const allowedExtensions = ['xlsx', 'xls'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Salah!',
                text: 'Hanya file Excel (.xlsx atau .xls) yang diperbolehkan.',
                confirmButtonColor: '#d33'
            });
            return;
        }

        // Tampilkan loading overlay
        document.getElementById('loadingOverlay').classList.add('active');
        document.getElementById('btn_submit').disabled = true;

        // Prepare form data
        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        // Kirim via AJAX
        fetch('<?= base_url('anggota/import') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading
            document.getElementById('loadingOverlay').classList.remove('active');
            document.getElementById('btn_submit').disabled = false;

            if (data.success) {
                // Tampilkan detail hasil import
                let detailHtml = `
                    <div class="text-left">
                        <p><strong>✅ Berhasil diimport:</strong> ${data.imported} data</p>
                        ${data.skipped > 0 ? `<p><strong>⚠️ Dilewati (duplikat):</strong> ${data.skipped} data</p>` : ''}
                        ${data.errors && data.errors.length > 0 ? `<p><strong>❌ Gagal:</strong> ${data.errors.length} data</p>` : ''}
                    </div>
                `;

                Swal.fire({
                    icon: 'success',
                    title: 'Import Berhasil!',
                    html: detailHtml,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect ke halaman daftar anggota
                        
                    }
                });

                // Reset form dan preview
                document.getElementById('frm_import').reset();
                document.getElementById('preview').innerHTML = '';

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal!',
                    text: data.message || 'Terjadi kesalahan saat mengimport data.',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            // Hide loading
            document.getElementById('loadingOverlay').classList.remove('active');
            document.getElementById('btn_submit').disabled = false;

            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Tidak dapat menghubungi server. Silakan coba lagi.',
                confirmButtonColor: '#d33'
            });
        });
    });
</script>
<?= $this->endSection('script'); ?>