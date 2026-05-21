<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-cloud-upload icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Impor Katalog dari File MARC
                    <div class="page-title-subheading">Upload file .mrc atau .xml (MARC XML), preview data, lalu simpan ke database.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Upload -->
    <div class="row" id="step-upload">
        <div class="col-md-8 mx-auto">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-upload icon-gradient bg-plum-plate"></i> Step 1 — Pilih File MARC</div>
                <div class="card-body">
                    <form id="form-preview" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Jenis File <span class="text-danger">*</span></label>
                            <div class="mt-1">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="type_mrc" name="marc_type" value="mrc" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="type_mrc">
                                        <i class="fa fa-file-alt text-primary"></i> MARC Binary <small class="text-muted">(.mrc)</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="type_xml" name="marc_type" value="xml" class="custom-control-input">
                                    <label class="custom-control-label" for="type_xml">
                                        <i class="fa fa-file-code text-success"></i> MARC XML <small class="text-muted">(.xml)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>File MARC <span class="text-danger">*</span></label>
                            <input name="marc_file[]" id="marc_file" type="file" class="form-control" accept=".mrc" multiple>
                            <small class="text-muted" id="marc-file-hint">Pilih satu atau lebih file .mrc sekaligus. Semua record dari semua file akan digabung.</small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-preview">
                            <i class="fa fa-search"></i> Preview Data
                        </button>
                    </form>
                    <div id="upload-message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Preview (hidden until preview loaded) -->
    <div class="row d-none" id="step-preview">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <i class="header-icon lnr-list icon-gradient bg-plum-plate"></i>
                    Step 2 — Preview Data (<span id="preview-count">0</span> record ditemukan)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="tbl-preview">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Penerbit</th>
                                    <th width="80">Tahun</th>
                                    <th width="140">ISBN</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody id="preview-tbody"></tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-success" id="btn-save">
                            <i class="fa fa-save"></i> Simpan Semua (<span id="save-count">0</span> record)
                        </button>
                        <button class="btn btn-secondary" id="btn-cancel">
                            <i class="fa fa-times"></i> Batal / Upload Ulang
                        </button>
                    </div>
                    <div id="save-message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Hasil simpan (hidden until save done) -->
    <div class="row d-none" id="step-result">
        <div class="col-md-8 mx-auto">
            <div class="main-card mb-3 card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fa fa-check-circle"></i> Impor Selesai
                </div>
                <div class="card-body">
                    <div id="result-summary" class="mb-3"></div>
                    <table class="table table-sm table-bordered" id="tbl-result">
                        <thead class="thead-light">
                            <tr><th width="60">ID</th><th>Judul</th></tr>
                        </thead>
                        <tbody id="result-tbody"></tbody>
                    </table>
                    <div class="mt-3 d-flex gap-2">
                        <a href="<?= base_url('katalog') ?>" class="btn btn-primary">
                            <i class="fa fa-list"></i> Lihat Daftar Katalog
                        </a>
                        <button class="btn btn-outline-secondary" id="btn-import-again">
                            <i class="fa fa-redo"></i> Import Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
(function () {
    const urlPreview = '<?= site_url("katalog/preview-marc-file") ?>';
    const urlSave    = '<?= site_url("katalog/create-marc-from-file") ?>';

    // ── File type radio toggle ────────────────────────────────
    document.querySelectorAll('input[name="marc_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const fileInput = document.getElementById('marc_file');
            const hint      = document.getElementById('marc-file-hint');
            if (this.value === 'xml') {
                fileInput.setAttribute('accept', '.xml');
                hint.textContent = 'Pilih satu atau lebih file .xml (MARC XML) sekaligus. Semua record dari semua file akan digabung.';
            } else {
                fileInput.setAttribute('accept', '.mrc');
                hint.textContent = 'Pilih satu atau lebih file .mrc sekaligus. Semua record dari semua file akan digabung.';
            }
            fileInput.value = '';
        });
    });

    // ── Preview ──────────────────────────────────────────────
    document.getElementById('form-preview').addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('marc_file');
        if (!fileInput.files.length) {
            showMsg('upload-message', 'warning', 'Silakan pilih file terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('btn-preview');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        showMsg('upload-message', 'info', 'Membaca file MARC...');

        const formData = new FormData(this);

        fetch(urlPreview, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderPreview(data.records);
                    document.getElementById('step-preview').classList.remove('d-none');
                    document.getElementById('upload-message').innerHTML = '';
                    document.getElementById('step-upload').classList.add('d-none');
                } else {
                    showMsg('upload-message', 'danger', data.message);
                }
            })
            .catch(() => showMsg('upload-message', 'danger', 'Terjadi kesalahan saat membaca file.'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-search"></i> Preview Data';
            });
    });

    // ── Render preview table ─────────────────────────────────
    function renderPreview(records) {
        document.getElementById('preview-count').textContent = records.length;
        document.getElementById('save-count').textContent    = records.length;

        let html = '';
        records.forEach((r, i) => {
            html += `<tr>
                <td>${i + 1}</td>
                <td>${esc(r.title)}</td>
                <td>${esc(r.author)}</td>
                <td>${esc(r.publisher)}</td>
                <td>${esc(r.year)}</td>
                <td>${esc(r.isbn)}</td>
                <td><small class="text-muted">${esc(r.file)}</small></td>
            </tr>`;
        });
        document.getElementById('preview-tbody').innerHTML = html;
    }

    // ── Save ─────────────────────────────────────────────────
    document.getElementById('btn-save').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
        showMsg('save-message', 'info', 'Menyimpan data ke database...');

        fetch(urlSave, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderResult(data.results, data.total);
                    document.getElementById('step-preview').classList.add('d-none');
                    document.getElementById('step-result').classList.remove('d-none');
                } else {
                    showMsg('save-message', 'danger', data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-save"></i> Simpan Semua';
                }
            })
            .catch(() => {
                showMsg('save-message', 'danger', 'Terjadi kesalahan saat menyimpan.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-save"></i> Simpan Semua';
            });
    });

    // ── Render result table ──────────────────────────────────
    function renderResult(results, total) {
        document.getElementById('result-summary').innerHTML =
            `<div class="alert alert-success mb-0"><strong>${total} katalog</strong> berhasil diimpor.</div>`;

        let html = '';
        results.forEach(r => {
            html += `<tr><td>${r.id}</td><td>${esc(r.title)}</td></tr>`;
        });
        document.getElementById('result-tbody').innerHTML = html;
    }

    // ── Cancel / reset ───────────────────────────────────────
    document.getElementById('btn-cancel').addEventListener('click', resetToUpload);
    document.getElementById('btn-import-again').addEventListener('click', resetToUpload);

    function resetToUpload() {
        document.getElementById('step-upload').classList.remove('d-none');
        document.getElementById('step-preview').classList.add('d-none');
        document.getElementById('step-result').classList.add('d-none');
        document.getElementById('form-preview').reset();
        document.getElementById('upload-message').innerHTML = '';
        document.getElementById('save-message').innerHTML = '';
        document.getElementById('btn-save').disabled = false;
        document.getElementById('btn-save').innerHTML = '<i class="fa fa-save"></i> Simpan Semua (<span id="save-count">0</span> record)';
    }

    // ── Helper ───────────────────────────────────────────────
    function showMsg(id, type, text) {
        document.getElementById(id).innerHTML =
            `<div class="alert alert-${type}">${text}</div>`;
    }

    function esc(str) {
        if (!str || str === '-') return '-';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
})();
</script>
<?= $this->endSection('script'); ?>
