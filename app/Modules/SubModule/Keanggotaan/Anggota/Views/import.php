<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-guestbook-import.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-guestbook-import.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-eksemplar-import.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-eksemplar-import.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner ei-page">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss" aria-hidden="true"></i>
                </div>
                <div>Import <?= lang('Anggota.module') ?>
                    <div class="page-title-subheading">Tambahkan data anggota sekaligus dari file Excel</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home" aria-hidden="true"></i> <?= lang('Anggota.label.home') ?></a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('anggota') ?>"><?= lang('Anggota.module') ?></a></li>
                        <li class="breadcrumb-item" aria-current="page">Import <?= lang('Anggota.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            Unggah file Excel
            <div class="gb-import-actions">
                <a href="<?= base_url('uploads/master-template/template_anggota.xlsx') ?>" class="btn btn-outline-primary gb-import-btn">
                    <i class="fa fa-download" aria-hidden="true"></i> Unduh template
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="frm_import" enctype="multipart/form-data">
                <div class="gb-upload-field" id="upload-area">
                    <i class="fa fa-cloud-upload-alt gb-upload-icon" aria-hidden="true"></i>
                    <p class="gb-upload-title">Seret file Excel ke sini atau</p>
                    <button type="button" class="btn btn-outline-primary gb-upload-pick" id="pick-file">Pilih file</button>
                    <p class="gb-upload-hint">Format .xlsx atau .xls, satu file, maksimal 5 MB.</p>
                    <input type="file" id="excel_file" accept=".xlsx,.xls" hidden>
                </div>

                <div class="gb-upload-item" id="upload-item" data-state="idle" hidden>
                    <i class="fa fa-file-excel gb-upload-item-icon" aria-hidden="true"></i>
                    <div class="gb-upload-item-body">
                        <div class="gb-upload-item-name" id="file-name"></div>
                        <div class="gb-upload-item-desc" id="file-desc" role="status" aria-live="polite"></div>
                        <div class="gb-upload-progress" id="file-progress" hidden><span id="file-bar"></span></div>
                    </div>
                    <button type="button" class="gb-upload-remove" id="file-remove" aria-label="Hapus file">
                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
                    </button>
                </div>

                <ul class="gb-import-errors" id="import-errors" tabindex="0" aria-label="Daftar baris yang gagal diimport" hidden></ul>

                <div class="ei-preview" id="preview" hidden>
                    <h3 class="ei-heading">Pratinjau data</h3>
                    <p class="ei-text" id="preview-note"></p>
                    <div class="table-responsive" tabindex="0" aria-label="Pratinjau isi file Excel">
                        <table class="table table-hover table-striped table-bordered" id="preview-table"></table>
                    </div>
                </div>

                <div class="ei-form-actions">
                    <a class="btn btn-outline-primary gb-import-btn-lg" id="view-members" href="<?= base_url('anggota') ?>" hidden>Lihat daftar anggota</a>
                    <button type="submit" class="btn btn-primary gb-import-btn-lg" id="btn_submit" disabled>Import</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">Cara data diproses</div>
        <div class="card-body">
            <ul class="ei-list">
                <li>Gunakan template yang tersedia. Baris pertama berisi nama kolom.</li>
                <li>Nomor anggota yang sudah terdaftar dilewati, datanya tidak diubah.</li>
                <li>Isian yang tidak cocok dengan data master, seperti agama atau pekerjaan, dikosongkan.</li>
                <li>Jika tanggal pendaftaran kosong, diisi tanggal hari ini.</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js" integrity="sha384-1PH7loFe9aVDAyHgsXQly6QE1ATOEHvmQt4vAuu6ppdeL5lm9vqAczXV0R6sbJkJ" crossorigin="anonymous"></script>
<script>
    (function() {
        const MAX_SIZE = 5 * 1024 * 1024;
        const PREVIEW_ROWS = 10;
        const byId = function(id) { return document.getElementById(id); };
        const form = byId('frm_import');
        const field = byId('upload-area');
        const input = byId('excel_file');
        const pick = byId('pick-file');
        const item = byId('upload-item');
        const nameEl = byId('file-name');
        const desc = byId('file-desc');
        const progress = byId('file-progress');
        const bar = byId('file-bar');
        const removeBtn = byId('file-remove');
        const errorList = byId('import-errors');
        const preview = byId('preview');
        const previewNote = byId('preview-note');
        const previewTable = byId('preview-table');
        const submit = byId('btn_submit');
        const viewMembers = byId('view-members');
        let selected = null;
        let finished = false;
        let uploading = false;

        function formatSize(bytes) {
            return bytes < 1024 * 1024
                ? Math.max(1, Math.round(bytes / 1024)) + ' KB'
                : (bytes / 1024 / 1024).toFixed(1) + ' MB';
        }

        function setFieldDisabled(disabled) {
            field.classList.toggle('is-disabled', disabled);
            pick.disabled = disabled;
        }

        function setItem(state, text) {
            item.hidden = false;
            item.dataset.state = state;
            desc.textContent = text;
            progress.hidden = state !== 'uploading';
            removeBtn.hidden = state === 'uploading' || finished;
        }

        function showErrors(errors) {
            errorList.textContent = '';
            (errors || []).forEach(function(message) {
                const li = document.createElement('li');
                li.textContent = message;
                errorList.appendChild(li);
            });
            errorList.hidden = errorList.children.length === 0;
        }

        function clearPreview() {
            preview.hidden = true;
            previewNote.textContent = '';
            previewTable.textContent = '';
        }

        function reset() {
            selected = null;
            finished = false;
            input.value = '';
            item.hidden = true;
            item.dataset.state = 'idle';
            bar.style.width = '0';
            showErrors([]);
            clearPreview();
            setFieldDisabled(false);
            viewMembers.hidden = true;
            submit.textContent = 'Import';
            submit.disabled = true;
        }

        function addRow(section, values, tag) {
            const row = section.insertRow();
            values.forEach(function(value) {
                const cell = document.createElement(tag);
                cell.textContent = value === undefined || value === null ? '' : value;
                row.appendChild(cell);
            });
        }

        // Pratinjau hanya pembantu: bila gagal, file tetap bisa diimport
        function renderPreview(file) {
            if (typeof XLSX === 'undefined') {
                return;
            }

            const reader = new FileReader();
            reader.onload = function() {
                try {
                    const workbook = XLSX.read(new Uint8Array(reader.result), { type: 'array' });
                    const rows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1, blankrows: false });
                    if (rows.length === 0 || selected !== file) {
                        return;
                    }

                    const columns = rows[0].length;
                    const body = rows.slice(1, PREVIEW_ROWS + 1);
                    const pad = function(row) { return Array.from({ length: columns }, function(_, i) { return row[i]; }); };

                    previewTable.textContent = '';
                    addRow(previewTable.createTHead(), pad(rows[0]), 'th');
                    const tbody = previewTable.createTBody();
                    body.forEach(function(row) { addRow(tbody, pad(row), 'td'); });

                    previewNote.textContent = 'Menampilkan ' + body.length + ' dari ' + (rows.length - 1) + ' baris data.';
                    preview.hidden = false;
                } catch (error) {
                    clearPreview();
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function chooseFile(file) {
            if (!file || field.classList.contains('is-disabled')) {
                return;
            }

            reset();
            nameEl.textContent = file.name;
            setFieldDisabled(true);

            if (!/\.(xlsx|xls)$/i.test(file.name)) {
                setItem('error', 'File harus berformat Excel (.xlsx atau .xls). Hapus file ini, lalu pilih file lain.');
                return;
            }
            if (file.size > MAX_SIZE) {
                setItem('error', 'Ukuran file ' + formatSize(file.size) + ', maksimal 5 MB. Bagi data menjadi beberapa file.');
                return;
            }

            selected = file;
            setItem('selected', formatSize(file.size));
            submit.disabled = false;
            renderPreview(file);
        }

        function failed(message) {
            uploading = false;
            selected = null;
            setItem('error', message);
            submit.textContent = 'Import';
            submit.disabled = true;
        }

        function finish(data) {
            const errors = (data.errors || []).map(function(row) {
                return 'Baris ' + row.row + ': ' + row.error;
            });
            const notes = [];
            if (data.skipped > 0) {
                notes.push(data.skipped + ' baris dilewati karena nomor anggota sudah ada');
            }
            if (errors.length > 0) {
                notes.push(errors.length + ' baris gagal');
            }

            uploading = false;
            selected = null;
            finished = true;
            setItem(errors.length > 0 || data.skipped > 0 ? 'warning' : 'success',
                data.imported + ' baris berhasil diimport' + (notes.length ? ', ' + notes.join(', ') : '') + '.');
            showErrors(errors);
            clearPreview();
            viewMembers.hidden = false;
            submit.textContent = 'Import file lain';
            submit.disabled = false;
        }

        pick.addEventListener('click', function() {
            input.click();
        });
        input.addEventListener('change', function() {
            chooseFile(input.files[0]);
        });
        removeBtn.addEventListener('click', function() {
            reset();
            pick.focus();
        });
        ['dragenter', 'dragover'].forEach(function(type) {
            field.addEventListener(type, function(event) {
                event.preventDefault();
                if (!field.classList.contains('is-disabled')) {
                    field.classList.add('is-dragover');
                }
            });
        });
        ['dragleave', 'drop'].forEach(function(type) {
            field.addEventListener(type, function(event) {
                event.preventDefault();
                field.classList.remove('is-dragover');
            });
        });
        field.addEventListener('drop', function(event) {
            chooseFile(event.dataTransfer.files[0]);
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            if (finished) {
                reset();
                pick.focus();
                return;
            }
            if (!selected || uploading) {
                return;
            }

            const body = new FormData();
            body.append('excel_file', selected);
            body.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            uploading = true;
            bar.style.width = '0';
            setItem('uploading', 'Mengunggah file...');
            submit.textContent = 'Memproses...';
            submit.disabled = true;

            const request = new XMLHttpRequest();
            request.open('POST', '<?= base_url('anggota/import') ?>');
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.upload.onprogress = function(progressEvent) {
                if (progressEvent.lengthComputable) {
                    bar.style.width = Math.round(progressEvent.loaded / progressEvent.total * 100) + '%';
                    if (progressEvent.loaded === progressEvent.total) {
                        desc.textContent = 'Memeriksa dan menyimpan data. Jangan tutup halaman ini.';
                    }
                }
            };
            request.onerror = function() {
                failed('Koneksi terputus. Baris yang sudah diproses tetap tersimpan. Periksa jaringan, lalu unggah ulang: nomor anggota yang sudah masuk akan dilewati.');
            };
            request.onload = function() {
                let data = null;
                try {
                    data = JSON.parse(request.responseText);
                } catch (error) {
                    data = null;
                }

                if (data && data.success) {
                    finish(data);
                } else if (request.status === 502 || request.status === 504 || request.status === 524) {
                    failed('Proses terlalu lama dan terputus. Baris yang sudah diproses tetap tersimpan. Bagi file menjadi beberapa bagian, lalu unggah ulang: nomor anggota yang sudah masuk akan dilewati.');
                } else {
                    failed(data && data.message ? data.message : 'Terjadi kendala di server. Muat ulang halaman, lalu coba lagi.');
                }
            };
            request.send(body);
        });
    })();
</script>
<?= $this->endSection('script'); ?>
