<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
$selected_npp = $request->getGet('npp') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
.select2 {
    text-transform: none;
    font-weight: normal;
}

.npp-selector {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    margin-bottom: 25px;
}

.npp-selector .card-header {
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px 15px 0 0;
}

.form-section {
    display: none;
    animation: fadeInUp 0.5s ease-out;
}

.form-section.show {
    display: block;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 10px 15px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 20px;
}

.warning-badge {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    padding: 10px 15px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 20px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 10px;
    padding: 10px 25px;
    font-weight: 600;
}

.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 10px 10px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

.suggestion-item:last-child {
    border-bottom: none;
}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Nama Perpustakaan
                    <div class="page-title-subheading">Pilih perpustakaan dan lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i
                                    class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Umum</li>
                        <li class="breadcrumb-item active">Nama Perpustakaan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- NPP Selector -->
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card npp-selector">
                <div class="card-header">
                    <i class="header-icon lnr-search icon-gradient bg-plum-plate text-white"></i>
                    <span class="text-white font-weight-bold">Cari Perpustakaan</span>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= current_url() ?>" id="nppSelectorForm">
                        <div class="row align-items-end">
                            <div class="col-md-10">
                                <div class="position-relative form-group">
                                    <label for="npp_input" class="text-white font-weight-bold">
                                        <i class="fa fa-search mr-2"></i>Masukkan Nomor Pokok Perpustakaan (NPP)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="npp" id="npp_input"
                                            placeholder="Ketik NPP atau nama perpustakaan..."
                                            value="<?= $selected_npp ?>" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-light">
                                                <i class="fa fa-search mr-2"></i>Cari
                                            </button>
                                        </div>
                                    </div>
                                    <div id="npp-suggestions" class="suggestions-dropdown"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <?php if (!empty($selected_npp)): ?>
                                <a href="<?= base_url('master-nama-perpustakaan') ?>"
                                    class="btn btn-outline-light btn-block">
                                    <i class="fa fa-times mr-2"></i>Reset
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status NPP -->
    <?php if (!empty($selected_npp)): ?>
    <?php if (isset($npp_not_found) && $npp_not_found): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                <strong>NPP "<?= esc($selected_npp) ?>" tidak ditemukan.</strong>
                Anda dapat menambahkan data perpustakaan baru dengan NPP ini.
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Form Section -->
    <?php if (!empty($selected_npp)): ?>
    <div class="row form-section show">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"></i>
                    <?php if (!empty($current_branch)): ?>
                    Edit Data Perpustakaan
                    <?php else: ?>
                    Tambah Data Perpustakaan Baru
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($current_branch)): ?>
                    <div class="info-badge">
                        <i class="fa fa-info-circle mr-2"></i>
                        Data perpustakaan ditemukan. Anda dapat mengedit informasi di bawah ini.
                    </div>
                    <?php elseif (!empty($selected_npp)): ?>
                    <div class="warning-badge">
                        <i class="fa fa-plus-circle mr-2"></i>
                        NPP "<?= esc($selected_npp) ?>" belum terdaftar. Silakan lengkapi informasi di bawah ini untuk
                        menambahkan data baru.
                    </div>
                    <?php endif; ?>

                    <div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>

                    <form id="frm_create" method="post" action="<?= base_url('master-nama-perpustakaan/update') ?>">
                        <input type="hidden" name="ID" value="<?= $current_branch->ID ?? '' ?>">
                        <input type="hidden" name="selected_npp" value="<?= $selected_npp ?>">

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="Code">
                                        <i class="fa fa-code mr-2 text-primary"></i>NPP
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" name="Code" id="Code"
                                            value="<?= $current_branch->Code ?? $selected_npp ?>" readonly
                                            style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="Name">
                                        <i class="fa fa-building mr-2 text-primary"></i>Nama Lembaga <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" name="Name" id="Name"
                                            placeholder="Masukkan nama perpustakaan"
                                            value="<?= $current_branch->Name ?? '' ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="Url">
                                        <i class="fa fa-link mr-2 text-primary"></i>URL Perpustakaan
                                        <small
                                            class="text-muted">(<?= env('app.libURL') ?>/<b>url-perpustakaan</b>)</small>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?= env('app.libURL') ?>/</span>
                                        </div>
                                        <input type="text" class="form-control" name="Url" id="Url"
                                            placeholder="url-perpustakaan" value="<?= $current_branch->slug ?? '' ?>"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="Address">
                                        <i class="fa fa-map-marker-alt mr-2 text-primary"></i>Alamat
                                    </label>
                                    <div>
                                        <textarea class="form-control" name="Address" id="Address"
                                            placeholder="Masukkan alamat lengkap perpustakaan"
                                            rows="3"><?= $current_branch->Address ?? '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="Email">
                                        <i class="fa fa-envelope mr-2 text-primary"></i>Email
                                    </label>
                                    <div>
                                        <input type="email" class="form-control" name="Email" id="Email"
                                            placeholder="email@perpustakaan.com"
                                            value="<?= $current_branch->Email ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="Phone">
                                        <i class="fa fa-phone mr-2 text-primary"></i>No. Telepon
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" name="Phone" id="Phone"
                                            placeholder="021-12345678" value="<?= $current_branch->Phone ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="IG">
                                        <i class="fab fa-instagram mr-2 text-primary"></i>Instagram
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input type="text" class="form-control" name="IG" id="IG"
                                            placeholder="username_instagram" value="<?= $current_branch->IG ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="YT">
                                        <i class="fab fa-youtube mr-2 text-primary"></i>Youtube
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">youtube.com/</span>
                                        </div>
                                        <input type="text" class="form-control" name="YT" id="YT"
                                            placeholder="channel_name" value="<?= $current_branch->YT ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="TW">
                                        <i class="fab fa-twitter mr-2 text-primary"></i>Twitter
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input type="text" class="form-control" name="TW" id="TW"
                                            placeholder="username_twitter" value="<?= $current_branch->TW ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="FB">
                                        <i class="fab fa-facebook mr-2 text-primary"></i>Facebook
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">facebook.com/</span>
                                        </div>
                                        <input type="text" class="form-control" name="FB" id="FB"
                                            placeholder="page_name" value="<?= $current_branch->FB ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="LayananOperasionl">
                                        <i class="fa fa-clock mr-2 text-primary"></i>Jam Operasional Layanan
                                    </label>
                                    <div>
                                        <?php
										$LayananOperasionl_Str = '<b>Jam Operasional Layanan</b><br>
<i class="right-icon bx bx-chevrons-right"></i>Senin-Jumat 08.00 - 16.00 WIB<br>
<i class="right-icon bx bx-chevrons-right"></i>Sabtu-Minggu 08.00 - 15.00 WIB<br>
<i class="right-icon bx bx-chevrons-right"></i>Cuti Bersama dan Libur Nasional <b>Tutup</b><br>';
										$LayananOperasionl = $current_branch->LayananOperasionl ?? $LayananOperasionl_Str;
										?>
                                        <textarea class="form-control" name="LayananOperasionl" id="LayananOperasionl"
                                            placeholder="Masukkan jam operasional layanan"
                                            rows="5"><?= htmlspecialchars_decode($LayananOperasionl, ENT_QUOTES) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="IsUseKop"
                                            id="IsUseKop"
                                            <?= (!empty($current_branch) && get_setting_parameter('IsUseKop', is_profiling()) == 1) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="IsUseKop">
                                            <i class="fa fa-file-alt mr-2"></i>Gunakan Kop di Laporan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                     

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="submit">
                                <i class="fa fa-save mr-2"></i>
                                <?= !empty($current_branch) ? 'Update Data' : 'Simpan Data' ?>
                            </button>
                            <a href="<?= current_url() ?>" class="btn btn-secondary ml-2">
                                <i class="fa fa-times mr-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
			<div class="col_md-12">
		<div class="main-card mb-3 card">
                            <div class="card-header">
                                <i class="header-icon lnr-picture icon-gradient bg-plum-plate"></i> Pengaturan Logo
                                <?php if (!empty($current_branch)): ?>
                                <div class="btn-actions-pane-right actions-icon-btn">
                                    <div class="menu-header-btn-pane">
                                        <a href="javascript:void(0);" data-id="<?= $current_branch->ID ?? '' ?>"
                                            data-format=".jpg,.png"
                                            data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="Logo"
                                            data-title="Upload Logo"
                                            class="mb-2 mr-2 btn btn-warning upload-data btn-sm">
                                            <i class="fa fa-edit"></i> Update Logo
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body text-center">
                                <?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($current_branch->Logo)) ? base_url('uploads/branch/' . $current_branch->Logo) : $default;
					?>
                                <img width="150px" src="<?= $file_image ?>" alt="Logo Perpustakaan"
                                    class="img-fluid rounded shadow">
                                <?php if (empty($current_branch)): ?>
                                <p class="text-muted mt-3 mb-0">
                                    <small><i class="fa fa-info-circle mr-1"></i>Logo dapat diupload setelah data
                                        disimpan</small>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Kop Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-header">
                                <i class="header-icon lnr-file-empty icon-gradient bg-plum-plate"></i> Pengaturan Kop
                                <?php if (!empty($current_branch)): ?>
                                <div class="btn-actions-pane-right actions-icon-btn">
                                    <div class="menu-header-btn-pane">
                                        <a href="javascript:void(0);" data-id="<?= $current_branch->ID ?? '' ?>"
                                            data-format=".jpg,.png"
                                            data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB"
                                            data-field="CoverLetter" data-title="Upload Kop Surat"
                                            class="mb-2 mr-2 btn btn-warning upload-data btn-sm">
                                            <i class="fa fa-edit"></i> Update Kop
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body text-center">
                                <?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($current_branch->CoverLetter)) ? base_url('uploads/branch/' . $current_branch->CoverLetter) : $default;
					?>
                                <img width="150px" src="<?= $file_image ?>" alt="Kop Surat"
                                    class="img-fluid rounded shadow">
                                <?php if (empty($current_branch)): ?>
                                <p class="text-muted mt-3 mb-0">
                                    <small><i class="fa fa-info-circle mr-1"></i>Kop dapat diupload setelah data
                                        disimpan</small>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="main-card mb-3 card">
                            <div class="card-header bg-info text-white">
                                <i class="fa fa-info-circle mr-2"></i>Informasi
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success mr-2"></i>
                                        <small>NPP wajib dipilih terlebih dahulu</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success mr-2"></i>
                                        <small>Nama lembaga wajib diisi</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success mr-2"></i>
                                        <small>URL akan menjadi alamat website perpustakaan</small>
                                    </li>
                                    <li class="mb-0">
                                        <i class="fa fa-check text-success mr-2"></i>
                                        <small>Logo dan kop dapat diupload setelah data disimpan</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
		</div>
        </div>
	


    </div>
    <?php endif; ?>
</div>

<script>
// NPP Search dengan autocomplete
let searchTimeout;
const nppInput = document.getElementById('npp_input');
const suggestionsDiv = document.getElementById('npp-suggestions');

nppInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        hideSuggestions();
        return;
    }

    searchTimeout = setTimeout(() => {
        searchNpp(query);
    }, 300);
});

function searchNpp(query) {
    fetch(`<?= base_url('master-nama-perpustakaan/api/search-npp?term=') ?>${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            showSuggestions(data);
        })
        .catch(error => {
            console.error('Error:', error);
            hideSuggestions();
        });
}

function showSuggestions(suggestions) {
    if (suggestions.length === 0) {
        hideSuggestions();
        return;
    }

    let html = '';
    suggestions.forEach(item => {
        html +=
            `<div class="suggestion-item" onclick="selectNpp('${item.value}', '${item.label}')">${item.label}</div>`;
    });

    suggestionsDiv.innerHTML = html;
    suggestionsDiv.style.display = 'block';
}

function hideSuggestions() {
    suggestionsDiv.style.display = 'none';
}

function selectNpp(value, label) {
    nppInput.value = value;
    hideSuggestions();
    document.getElementById('nppSelectorForm').submit();
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!nppInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        hideSuggestions();
    }
});

// Auto-generate URL from name
document.getElementById('Name').addEventListener('input', function() {
    const name = this.value;
    const urlField = document.getElementById('Url');

    if (name && !urlField.value) {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        urlField.value = slug;
    }
});

// Real-time URL validation
document.getElementById('Url').addEventListener('input', function() {
    const url = this.value;
    const id = document.querySelector('input[name="ID"]').value;

    if (url.length > 3) {
        fetch('<?= base_url("master-nama-perpustakaan/api/check-url") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `url=${encodeURIComponent(url)}&id=${encodeURIComponent(id)}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    this.classList.add('is-invalid');
                    this.nextElementSibling?.remove();
                    this.insertAdjacentHTML('afterend',
                        '<div class="invalid-feedback">URL sudah digunakan</div>');
                } else {
                    this.classList.remove('is-invalid');
                    this.nextElementSibling?.remove();
                }
            });
    }
});
</script>

<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->include('NamaPerpustakaan\Views\upload_modal'); ?>
<?= $this->endSection('script') ?>