<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
.upload-area {
    border: 2px dashed #ddd;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #007bff;
    background-color: #f0f8ff;
}

.upload-area.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.upload-icon {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 20px;
}

.progress-container {
    display: none;
    margin-top: 20px;
}

.import-results {
    display: none;
    margin-top: 20px;
}

.format-info {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-top: 20px;
}

.sample-format {
    background-color: #f1f3f4;
    padding: 10px;
    border-radius: 5px;
    font-family: monospace;
    font-size: 0.85em;
    margin: 10px 0;
}

.app-main__inner {
    max-width: 100% !important;
    width: 100% !important;
    padding-left: 20px;
    padding-right: 20px;
}

</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-cloud-upload icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Import Eksemplar Excel
                    <div class="page-title-subheading">Import data katalog, MARC fields, dan collections dari file Excel</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('eksemplar/import') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('eksemplar/import') ?>">Katalog</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Import Excel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-cloud-upload icon-gradient bg-plum-plate"> </i>Upload File Excel
            <div class="btn-actions-pane-right">
                <a href="<?= base_url('eksemplar/download-template') ?>" class="btn btn-info">
                    <i class="fa fa-download"></i> Download Template
                </a>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            
            <form id="form_import" enctype="multipart/form-data">
                <div class="upload-area" id="upload-area">
                    <div class="upload-icon">
                        <i class="fa fa-cloud-upload"></i>
                    </div>
                    <h5>Drag & Drop file Excel di sini</h5>
                    <p class="text-muted">atau klik untuk memilih file</p>
                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" style="display: none;">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('excel_file').click();">
                        <i class="fa fa-folder-open"></i> Pilih File
                    </button>
                </div>
                
                <div id="file-info" style="display: none;" class="mt-3">
                    <div class="alert alert-info">
                        <strong>File dipilih:</strong> <span id="file-name"></span><br>
                        <strong>Ukuran:</strong> <span id="file-size"></span>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
            
            <div class="progress-container" id="progress-container">
                <h6>Proses Import...</h6>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted">Mohon tunggu, sedang memproses data...</small>
            </div>
            
            <div class="import-results" id="import-results">
                <!-- Results will be displayed here -->
            </div>
        </div>
    </div>
    
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-question-circle icon-gradient bg-plum-plate"> </i>Format File Excel
        </div>
        <div class="card-body">
            <div class="format-info">
                <h6><i class="fa fa-info-circle"></i> Informasi Format File</h6>
                <p>File Excel harus memiliki kolom-kolom berikut:</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Catalog (Wajib):</h6>
                        <ul class="small">
                            <li><strong>JUDUL_UTAMA</strong> <span class="text-danger">*</span> - Judul utama buku</li>
                            <li><strong>ANAK_JUDUL</strong> - Subtitle/anak judul</li>
                            <li><strong>TAJUK_PENGARANG</strong> - Nama pengarang utama</li>
                            <li><strong>PENERBIT</strong> - Nama penerbit</li>
                            <li><strong>KOTA_TERBIT</strong> - Tempat terbit</li>
                            <li><strong>TAHUN_TERBIT</strong> - Tahun terbit</li>
                            <li><strong>SUBJEK_TOPIK</strong> - Topik/subjek buku</li>
                            <li><strong>JUMLAH_HALAMAN</strong> - Deskripsi halaman</li>
                            <li><strong>DIMENSI</strong> - Ukuran buku</li>
                            <li><strong>ISBN</strong> - Nomor ISBN</li>
                            <li><strong>NO_DDC</strong> - Nomor klasifikasi DDC</li>
                            <li><strong>NOMOR_PANGGIL_KATALOG</strong> - Call number</li>
                            <li><strong>BAHASA</strong> - Bahasa buku</li>
                            <li><strong>ABSTRAK</strong> - Ringkasan/catatan</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Data Collection (Wajib):</h6>
                        <ul class="small">
                            <li><strong>NO_BARCODE</strong> <span class="text-danger">*</span> - Nomor barcode unik</li>
                            <li><strong>NO_INDUK</strong> <span class="text-danger">*</span> - Nomor induk</li>
                            <li><strong>NO_RFID</strong> - Nomor RFID</li>
                            <li><strong>TGL_PENGADAAN</strong> - Tanggal pengadaan (dd-mm-yyyy)</li>
                            <li><strong>JENIS_SUMBER</strong> - Pembelian/Hadiah/Hibah/dll</li>
                            <li><strong>NAMA_SUMBER</strong> - Nama sumber/vendor</li>
                            <li><strong>MATA_UANG</strong> - IDR/USD/dll</li>
                            <li><strong>HARGA</strong> - Harga satuan (angka)</li>
                            <li><strong>KODE_LOKASI_PERPUSTAKAAN</strong> - Pusat/Cabang1/dll</li>
                            <li><strong>KODE_LOKASI_RUANG</strong> - 0101/0102/dll</li>
                            <li><strong>AKSES</strong> - Dapat dipinjam/Tidak dapat dipinjam</li>
                            <li><strong>KATEGORI</strong> - Koleksi Umum/Referensi/dll</li>
                            <li><strong>MEDIA</strong> - Buku/CD/DVD/dll</li>
                            <li><strong>KETERSEDIAAN</strong> - Tersedia/Dipinjam/dll</li>
                            <li><strong>NOMOR_PANGGIL_EKSEMPLAR</strong> - Call number eksemplar</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Format Data:</strong>
                    <ul class="mb-0">
                        <li><strong>Tanggal:</strong> Gunakan format dd-mm-yyyy (contoh: 14-02-2015)</li>
                        <li><strong>Harga:</strong> Angka tanpa titik/koma (contoh: 75000)</li>
                        <li><strong>Judul Lengkap:</strong> Akan digabung dari JUDUL_UTAMA + ANAK_JUDUL</li>
                        <li><strong>Deskripsi Fisik:</strong> Akan digabung dari JUMLAH_HALAMAN + DIMENSI</li>
                    </ul>
                </div>
                
                <div class="alert alert-success mt-3">
                    <strong>Mapping ID Otomatis:</strong>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Jenis Sumber:</strong>
                            <ul class="small mb-2">
                                <li>Pembelian → ID: 1</li>
                                <li>Hadiah/Hibah → ID: 2</li>
                                <li>Tukar Menukar → ID: 3</li>
                                <li>Deposit → ID: 4</li>
                            </ul>
                            
                            <strong>Media:</strong>
                            <ul class="small mb-2">
                                <li>Buku → ID: 2</li>
                                <li>CD/DVD → ID: 3</li>
                                <li>Majalah → ID: 4</li>
                                <li>Jurnal → ID: 5</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Ketersediaan:</strong>
                            <ul class="small mb-2">
                                <li>Tersedia → ID: 1</li>
                                <li>Dipinjam → ID: 2</li>
                                <li>Hilang → ID: 3</li>
                                <li>Rusak → ID: 4</li>
                            </ul>
                            
                            <strong>Akses:</strong>
                            <ul class="small mb-2">
                                <li>Dapat dipinjam → ID: 1</li>
                                <li>Tidak dapat dipinjam → ID: 2</li>
                                <li>Referensi → ID: 3</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <strong>Catatan Penting:</strong>
                    <ul class="mb-0">
                        <li>Kolom yang bertanda <span class="text-danger">*</span> wajib diisi</li>
                        <li>ControlNumber dan NomorBarcode harus unik dalam sistem</li>
                        <li>Format file yang didukung: .xlsx, .xls</li>
                        <li>Maksimal ukuran file: 10MB</li>
                        <li>Gunakan template yang disediakan untuk memudahkan proses import</li>
                        <li>Data akan divalidasi sebelum disimpan ke database</li>
                        <li>Jika terjadi error pada satu baris, baris tersebut akan dilewati</li>
                    </ul>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Tips Penggunaan:</strong>
                    <ul class="mb-0">
                        <li>Download template terlebih dahulu untuk memahami format yang benar</li>
                        <li>Pastikan tidak ada baris kosong di tengah data</li>
                        <li>Gunakan format tanggal yang konsisten (YYYY-MM-DD)</li>
                        <li>Untuk MARC fields, gunakan separator "|" untuk subfields</li>
                        <li>Backup data sebelum melakukan import dalam jumlah besar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function() {
    const uploadArea = $('#upload-area');
    const fileInput = $('#excel_file');
    const fileInfo = $('#file-info');
    const fileName = $('#file-name');
    const fileSize = $('#file-size');
    const progressContainer = $('#progress-container');
    const importResults = $('#import-results');

    // Drag and drop functionality
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    // Click to upload
    uploadArea.on('click', function() {
        fileInput.click();
    });

    // File input change
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    function handleFileSelect(file) {
        // Validate file type
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        const allowedExtensions = ['.xlsx', '.xls'];
        const fileExtension = file.name.toLowerCase().substr(file.name.lastIndexOf('.'));
        
        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
            Swal.fire({
                title: 'Error!',
                text: 'File harus berformat Excel (.xlsx atau .xls)',
                type: 'error'
            });
            return;
        }

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                title: 'Error!',
                text: 'Ukuran file tidak boleh lebih dari 10MB',
                type: 'error'
            });
            return;
        }

        // Display file info
        fileName.text(file.name);
        fileSize.text(formatFileSize(file.size));
        fileInfo.show();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submit
    $('#form_import').on('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput[0].files.length) {
            Swal.fire({
                title: 'Error!',
                text: 'Silakan pilih file terlebih dahulu',
                type: 'error'
            });
            return;
        }

        const formData = new FormData(this);
        
        // Show progress
        progressContainer.show();
        importResults.hide();
        fileInfo.hide();
        
        // Animate progress bar
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $('.progress-bar').css('width', progress + '%');
        }, 500);

        $.ajax({
            url: '<?= base_url('eksemplar/uploadexcel') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                clearInterval(progressInterval);
                $('.progress-bar').css('width', '100%');
                
                setTimeout(function() {
                    progressContainer.hide();
                    displayResults(response);
                }, 1000);
            },
            error: function(xhr) {
                clearInterval(progressInterval);
                progressContainer.hide();
                
                let errorMessage = 'Terjadi kesalahan saat mengimport data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.messages) {
                    errorMessage = Object.values(xhr.responseJSON.messages).join(', ');
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    type: 'error'
                });
                
                // Reset form
                resetForm();
            }
        });
    });

    function displayResults(response) {
        let html = '<div class="alert alert-success">';
        html += '<h5><i class="fa fa-check-circle"></i> Import Selesai</h5>';
        html += '<p><strong>Data berhasil diimport:</strong> ' + response.data.success_count + ' record</p>';
        
        if (response.data.error_count > 0) {
            html += '<p><strong>Data gagal diimport:</strong> ' + response.data.error_count + ' record</p>';
            
            if (response.data.errors.length > 0) {
                html += '<div class="mt-3">';
                html += '<h6>Detail Error:</h6>';
                html += '<div style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
                response.data.errors.forEach(function(error) {
                    html += '<small class="text-danger">• ' + error + '</small><br>';
                });
                html += '</div>';
                html += '</div>';
            }
        }
        
        html += '</div>';
        
        // Add action buttons
        html += '<div class="text-center mt-3">';
        html += '<button type="button" class="btn btn-primary" onclick="location.reload()">Import Lagi</button> ';
        html += '<a href="<?= base_url('katalog') ?>" class="btn btn-success">Lihat Data Katalog</a>';
        html += '</div>';
        
        importResults.html(html).show();
        
        // Reset form
        resetForm();
    }

    function resetForm() {
        $('#form_import')[0].reset();
        fileInfo.hide();
        fileName.text('');
        fileSize.text('');
        $('.progress-bar').css('width', '0%');
    }
});
</script>
<?= $this->endSection('script'); ?>