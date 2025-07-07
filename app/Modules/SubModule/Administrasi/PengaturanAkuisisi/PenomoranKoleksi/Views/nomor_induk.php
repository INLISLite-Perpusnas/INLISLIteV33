<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_add';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
.manual-input-field {
    margin-top: 8px;
}
.manual-input-field input {
    font-size: 14px;
    border: 2px solid #007bff;
    border-radius: 4px;
}
.manual-input-field input:focus {
    border-color: #0056b3;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Tambah Katalog
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Pengaturan Akuisisi</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Penomoran Koleksi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors:</strong>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <form id="frm_create" method="post" action="<?= base_url('master-penomoran-koleksi/create'); ?>">
                
                <!-- Pemberian Nomor Induk -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="NomorInduk" class="form-label"><strong>Pemberian Nomor Induk</strong></label>
                            <select class="form-control" id="NomorInduk" name="NomorInduk">
                                <option value="Manual" <?= (isset($NomorInduk) && $NomorInduk == "Manual") ? 'selected' : '' ?>>Manual</option>
                                <option value="Otomatis" <?= (isset($NomorInduk) && $NomorInduk == "Otomatis") ? 'selected' : '' ?>>Otomatis</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Format Nomor Induk -->
              <!-- Format Nomor Induk dengan Debug -->
<div class="form-group mb-3">
    <label class="form-label"><strong>Format Nomor Induk</strong></label>
    <div class="row">
        <?php 
        // Array untuk menyimpan format options
        $formatOptions = [
            '0' => '-Kosong-',
            '1' => 'Manual Input', 
            '2' => 'Kode Jenis Bahan',
            '3' => 'Kode Kategori Koleksi',
            '4' => 'Kode Bentuk Fisik', 
            '5' => 'Kode Jenis Sumber Pengadaan',
            '6' => '99999',
            '7' => 'YYYY'
        ];
        
        $separatorOptions = [
            '2' => '-Kosong-',
            '3' => '/',
            '4' => '-',
            '5' => '.'
        ];
        
        // Parse format dari database
        $dbFormatValues = [];
        $manualValues = [];
        if (isset($FormatNomorInduk) && !empty($FormatNomorInduk)) {
            $dbFormatValues = explode('|', $FormatNomorInduk);
            // Extract manual values jika ada
            foreach ($dbFormatValues as $index => $value) {
                if (preg_match('/\{(.+)\}/', $value, $matches)) {
                    $manualValues[$index] = $matches[1];
                    $dbFormatValues[$index] = '1'; // Set ke Manual Input
                }
            }
        }
        
        // Generate 9 select boxes untuk format
        for ($i = 1; $i <= 9; $i++): 
            $isEven = ($i % 2 == 0);
            $arrayIndex = $i - 1; // Index untuk array (0-8)
            
            // Ukuran kolom
            if ($i == 1 || $i == 3 || $i == 5 || $i == 7 || $i == 9) {
                $colSize = 'col-md-2';
            } else {
                $colSize = 'col-md-1';
            }
            
            // Ambil nilai dari database
            $currentValue = isset($dbFormatValues[$arrayIndex]) ? $dbFormatValues[$arrayIndex] : '0';
        ?>
            <div class="<?= $colSize ?>">
                <div class="form-group">
                    <small class="text-muted">Pos <?= $i ?></small>
                    
                    <select class="form-control format-select" id="FormatNomorInduk<?= $i ?>" name="FormatNomorInduk[]" data-position="<?= $i ?>">
                        <?php if ($isEven): ?>
                            <!-- Separator options for even positions -->
                            <?php foreach ($separatorOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($currentValue == $value) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Format options for odd positions -->
                            <?php foreach ($formatOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($currentValue == $value) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    
                    <!-- Manual Input Field untuk posisi ganjil -->
                    <?php if (!$isEven): ?>
                        <?php 
                        // Ambil nilai manual untuk posisi ini
                        $manualValue = isset($manualValues[$arrayIndex]) ? $manualValues[$arrayIndex] : '';
                        ?>
                        <div class="manual-input-field" id="manualField<?= $i ?>" style="display: <?= ($currentValue == '1') ? 'block' : 'none' ?>; margin-top: 8px;">
                            <input type="text" 
                                   name="ManualInput_<?= $arrayIndex ?>" 
                                   class="form-control manual-input" 
                                   placeholder="Masukkan teks manual"
                                   data-position="<?= $i ?>"
                                   data-array-index="<?= $arrayIndex ?>"
                                   value="<?= htmlspecialchars($manualValue) ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

                <!-- Preview Format -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Preview Format:</strong> <span id="formatPreview">-</span>
                        </div>
                    </div>
                </div>

                <!-- Sumber Nomor Barcode -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FormatNomorBarcode" class="form-label"><strong>Sumber Nomor Barcode</strong></label>
                            <select class="form-control" id="FormatNomorBarcode" name="FormatNomorBarcode">
                                <option value="No. Induk" <?= (isset($FormatNomorBarcode) && $FormatNomorBarcode == "No. Induk") ? 'selected' : '' ?>>No. Induk</option>
                                <option value="Item ID" <?= (isset($FormatNomorBarcode) && $FormatNomorBarcode == "Item ID") ? 'selected' : '' ?>>Item ID</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sumber Nomor RFID -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FormatNomorRFID" class="form-label"><strong>Sumber Nomor RFID</strong></label>
                            <select class="form-control" id="FormatNomorRFID" name="FormatNomorRFID">
                                <option value="No. Induk" <?= (isset($FormatNomorRFID) && $FormatNomorRFID == "No. Induk") ? 'selected' : '' ?>>No. Induk</option>
                                <option value="Item ID" <?= (isset($FormatNomorRFID) && $FormatNomorRFID == "Item ID") ? 'selected' : '' ?>>Item ID</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="<?= base_url('katalog') ?>" class="btn btn-secondary ml-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatSelects = document.querySelectorAll('[id^="FormatNomorInduk"]');
    const previewElement = document.getElementById('formatPreview');
    
    function updateManualInputFields() {
        formatSelects.forEach((select, index) => {
            const position = parseInt(select.dataset.position);
            const isOdd = (position % 2 !== 0);
            
            if (isOdd) { // Hanya untuk posisi ganjil (format options)
                const manualField = document.getElementById(`manualField${position}`);
                
                if (select.value === '1') { // Manual Input dipilih
                    manualField.style.display = 'block';
                } else {
                    manualField.style.display = 'none';
                    // Clear input value when hidden
                    const input = manualField.querySelector('input');
                    if (input) input.value = '';
                }
            }
        });
        
        updatePreview();
    }
    
    function updatePreview() {
        let preview = '';
        const formatLabels = {
            '0': '',
            '1': '[Manual]',
            '2': '[Jenis]', 
            '3': '[Kategori]',
            '4': '[Bentuk]',
            '5': '[Sumber]',
            '6': '99999',
            '7': 'YYYY'
        };
        
        const separatorLabels = {
            '2': '',
            '3': '/',
            '4': '-', 
            '5': '.'
        };
        
        formatSelects.forEach((select, index) => {
            const value = select.value;
            const position = parseInt(select.dataset.position);
            const isEven = (position % 2 === 0);
            
            if (value !== '0') {
                if (isEven) {
                    preview += separatorLabels[value] || '';
                } else {
                    if (value === '1') { // Manual Input
                        const manualField = document.getElementById(`manualField${position}`);
                        const input = manualField ? manualField.querySelector('input') : null;
                        
                        if (input && input.value.trim() !== '') {
                            preview += '[' + input.value + ']';
                        } else {
                            preview += '[Manual]';
                        }
                    } else {
                        preview += formatLabels[value] || '';
                    }
                }
            }
        });
        
        previewElement.textContent = preview || 'Format belum dipilih';
    }
    
    // Event listeners
    formatSelects.forEach(select => {
        select.addEventListener('change', updateManualInputFields);
    });
    
    document.querySelectorAll('.manual-input').forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    // Initial setup
    updateManualInputFields();
});
</script>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\add_script'); ?>
<?= $this->endSection('script'); ?>