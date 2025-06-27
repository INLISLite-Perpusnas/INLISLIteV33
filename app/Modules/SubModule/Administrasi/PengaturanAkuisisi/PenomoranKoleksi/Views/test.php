<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                
                <div class="card-body">
                    <?php 
                    // Display flash messages
                    $flashMessage = session()->getFlashdata('flash_message');
                    if ($flashMessage): ?>
                        <div class="alert alert-<?= $flashMessage['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                            <i class="<?= $flashMessage['icon'] ?>"></i>
                            <strong><?= $flashMessage['title'] ?>:</strong> <?= $flashMessage['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php 
                    // Display validation errors
                    if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?= form_open(current_url(), ['id' => 'settingForm']) ?>
                    
                    <!-- Header with Save Button -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>

                    <!-- Pemberian Nomor Induk -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Pemberian Nomor Induk</strong></label>
                                <div class="form-check-container mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="NomorInduk" id="otomatis" 
                                               value="Otomatis" <?= ($model['NomorInduk'] == 'Otomatis') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="otomatis">Otomatis</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="NomorInduk" id="manual" 
                                               value="Manual" <?= ($model['NomorInduk'] == 'Manual') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="manual">Manual</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Format Nomor Induk Template -->
                    <div id="templateForm" class="row mb-4">
                        <div class="col-12">
                            <label class="form-label"><strong>Format Nomor Induk</strong></label>
                            <div class="template-builder">
                                <?php 
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
                                    '2' => 'Kosong',
                                    '3' => '/',
                                    '4' => '-',
                                    '5' => '.'
                                ];

                                // Parse current format
                                $currentFormat = explode('|', $model['FormatNomorInduk'] ?? '0|2|0|2|0|2|0|2|0');
                                $currentFormatX = explode('|', $model['FormatNomorIndukx'] ?? '0|2|0|2|0|2|0|2|0');
                                
                                // Ensure we have 9 elements
                                while (count($currentFormat) < 9) $currentFormat[] = '0';
                                while (count($currentFormatX) < 9) $currentFormatX[] = '0';
                                
                                // Create 9 template fields (alternating format and separator)
                                for ($i = 0; $i < 9; $i++): 
                                    $isFormat = ($i % 2 == 0); // Even indexes are format fields
                                ?>
                                    <div class="template-field">
                                        <?php if ($isFormat): ?>
                                            <!-- Format Field -->
                                            <select name="cbTemplate[]" class="form-select form-select-sm template-select" 
                                                    id="cbTemplate<?= $i + 1 ?>" data-index="<?= $i ?>">
                                                <?php foreach ($formatOptions as $value => $text): ?>
                                                    <option value="<?= $value ?>" 
                                                        <?= (isset($currentFormatX[$i]) && $currentFormatX[$i] == $value) ? 'selected' : '' ?>>
                                                        <?= $text ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            
                                            <!-- Manual Input Field -->
                                            <?php 
                                            $manualValue = '';
                                            $showManual = false;
                                            if (isset($currentFormatX[$i]) && $currentFormatX[$i] == '1') {
                                                $showManual = true;
                                                if (isset($currentFormat[$i])) {
                                                    $manualValue = trim(str_replace(['{', '}'], '', $currentFormat[$i]));
                                                }
                                            }
                                            ?>
                                            <input type="text" name="cbTemplateInput[<?= $i ?>]" 
                                                   class="form-control form-control-sm mt-1 manual-input" 
                                                   id="cbTemplateInput<?= $i ?>" 
                                                   style="display: <?= $showManual ? 'block' : 'none' ?>;"
                                                   value="<?= esc($manualValue) ?>"
                                                   placeholder="Input manual">
                                            
                                            <!-- Hidden fields for collection sources -->
                                            <input type="hidden" name="cbTemplateInput[5<?= $i ?>]" value="dump">
                                            
                                        <?php else: ?>
                                            <!-- Separator Field -->
                                            <select name="cbTemplate[]" class="form-select form-select-sm">
                                                <?php foreach ($separatorOptions as $value => $text): ?>
                                                    <option value="<?= $value ?>" 
                                                        <?= (isset($currentFormatX[$i]) && $currentFormatX[$i] == $value) ? 'selected' : '' ?>>
                                                        <?= $text ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted">
                                Catatan: Format akan membuat nomor seperti: [Field1][Separator1][Field2][Separator2]...<br>
                                Untuk mode otomatis, tidak boleh ada duplikasi untuk "99999" dan "YYYY"
                            </small>
                        </div>
                    </div>

                    <!-- Format Nomor Barcode -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Format Nomor Barcode</strong></label>
                                <div class="form-check-container mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="FormatNomorBarcode" 
                                               id="barcode_item" value="Item ID" 
                                               <?= ($model['FormatNomorBarcode'] == 'Item ID') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="barcode_item">Item ID</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="FormatNomorBarcode" 
                                               id="barcode_induk" value="No. Induk" 
                                               <?= ($model['FormatNomorBarcode'] == 'No. Induk') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="barcode_induk">No. Induk</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Format Nomor RFID -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Format Nomor RFID</strong></label>
                                <div class="form-check-container mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="FormatNomorRFID" 
                                               id="rfid_item" value="Item ID" 
                                               <?= ($model['FormatNomorRFID'] == 'Item ID') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="rfid_item">Item ID</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="FormatNomorRFID" 
                                               id="rfid_induk" value="No. Induk" 
                                               <?= ($model['FormatNomorRFID'] == 'No. Induk') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="rfid_induk">No. Induk</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.template-builder {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background-color: #f8f9fa;
    margin-top: 10px;
}

.template-field {
    min-width: 120px;
}

.template-field select,
.template-field input {
    width: 100%;
}

.manual-input {
    font-family: monospace;
}

@media (max-width: 768px) {
    .template-builder {
        grid-template-columns: 1fr;
    }
    
    .form-check-container {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle radio button change for automatic/manual
    const nomorIndukRadios = document.querySelectorAll('input[name="NomorInduk"]');
    const templateForm = document.getElementById('templateForm');
    
    function toggleTemplateForm() {
        const isAutomatic = document.querySelector('input[name="NomorInduk"]:checked').value === 'Otomatis';
        const templateSelects = templateForm.querySelectorAll('select, input[type="text"]');
        
        templateSelects.forEach(function(element) {
            element.disabled = !isAutomatic;
        });
        
        templateForm.style.opacity = isAutomatic ? '1' : '0.5';
        templateForm.style.pointerEvents = isAutomatic ? 'auto' : 'none';
    }
    
    nomorIndukRadios.forEach(function(radio) {
        radio.addEventListener('change', toggleTemplateForm);
    });
    
    // Handle template select changes
    document.querySelectorAll('.template-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const manualInput = document.getElementById('cbTemplateInput' + index);
            
            if (this.value === '1') { // Manual Input
                manualInput.style.display = 'block';
                manualInput.focus();
            } else {
                manualInput.style.display = 'none';
                manualInput.value = '';
            }
        });
    });
    
    // Prevent curly braces in manual input (from original Yii2 validation)
    document.querySelectorAll('.manual-input').forEach(function(input) {
        input.addEventListener('keypress', function(e) {
            if (e.which === 123 || e.which === 125) { // { or }
                e.preventDefault();
                alert('Karakter { dan } tidak diperbolehkan');
                return false;
            }
        });
        
        // Also prevent paste of curly braces
        input.addEventListener('paste', function(e) {
            setTimeout(function() {
                if (input.value.includes('{') || input.value.includes('}')) {
                    input.value = input.value.replace(/[{}]/g, '');
                    alert('Karakter { dan } telah dihapus secara otomatis');
                }
            }, 10);
        });
    });
    
    // Form validation before submit
    document.getElementById('settingForm').addEventListener('submit', function(e) {
        const nomorInduk = document.querySelector('input[name="NomorInduk"]:checked').value;
        
        if (nomorInduk === 'Otomatis') {
            // Check for duplicate 99999 (value 6) and YYYY (value 7)
            const templateSelects = document.querySelectorAll('.template-select');
            const values = [];
            
            templateSelects.forEach(function(select) {
                values.push(select.value);
            });
            
            const count6 = values.filter(v => v === '6').length;
            const count7 = values.filter(v => v === '7').length;
            
            if (count6 > 1) {
                e.preventDefault();
                alert('Error: Tidak boleh ada lebih dari satu field "99999" dalam format penomoran');
                return false;
            }
            
            if (count7 > 1) {
                e.preventDefault();
                alert('Error: Tidak boleh ada lebih dari satu field "YYYY" dalam format penomoran');
                return false;
            }
            
            // Check if manual input fields are filled when selected
            templateSelects.forEach(function(select) {
                if (select.value === '1') {
                    const index = select.dataset.index;
                    const manualInput = document.getElementById('cbTemplateInput' + index);
                    if (!manualInput.value.trim()) {
                        e.preventDefault();
                        alert('Error: Field manual input tidak boleh kosong');
                        manualInput.focus();
                        return false;
                    }
                }
            });
        }
    });
    
    // Initialize form state
    toggleTemplateForm();
    
    // Add preview functionality
    function generatePreview() {
        const nomorInduk = document.querySelector('input[name="NomorInduk"]:checked').value;
        
        if (nomorInduk === 'Otomatis') {
            const templateSelects = document.querySelectorAll('.template-select');
            let preview = '';
            
            templateSelects.forEach(function(select, index) {
                const value = select.value;
                const isFormat = (index % 2 === 0);
                
                if (isFormat) {
                    switch (value) {
                        case '0':
                            // Kosong
                            break;
                        case '1':
                            const manualInput = document.getElementById('cbTemplateInput' + index);
                            preview += manualInput.value || '[Manual]';
                            break;
                        case '2':
                            preview += '[Jenis]';
                            break;
                        case '3':
                            preview += '[Kategori]';
                            break;
                        case '4':
                            preview += '[Bentuk]';
                            break;
                        case '5':
                            preview += '[Sumber]';
                            break;
                        case '6':
                            preview += '00001';
                            break;
                        case '7':
                            preview += new Date().getFullYear();
                            break;
                    }
                } else {
                    switch (value) {
                        case '2':
                            // Kosong
                            break;
                        case '3':
                            preview += '/';
                            break;
                        case '4':
                            preview += '-';
                            break;
                        case '5':
                            preview += '.';
                            break;
                    }
                }
            });
            
            updatePreview(preview || '[Tidak ada format]');
        } else {
            updatePreview('Manual input');
        }
    }
    
    function updatePreview(text) {
        let previewElement = document.getElementById('formatPreview');
        if (!previewElement) {
            previewElement = document.createElement('div');
            previewElement.id = 'formatPreview';
            previewElement.className = 'alert alert-info mt-2';
            previewElement.innerHTML = '<strong>Preview:</strong> <span id="previewText"></span>';
            templateForm.appendChild(previewElement);
        }
        document.getElementById('previewText').textContent = text;
    }
    
    // Add event listeners for preview updates
    document.querySelectorAll('input[name="NomorInduk"]').forEach(function(radio) {
        radio.addEventListener('change', generatePreview);
    });
    
    document.querySelectorAll('.template-select').forEach(function(select) {
        select.addEventListener('change', generatePreview);
    });
    
    document.querySelectorAll('.manual-input').forEach(function(input) {
        input.addEventListener('input', generatePreview);
    });
    
    // Generate initial preview
    generatePreview();
});
</script>