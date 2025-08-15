<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .tox.tox-tinymce.tox-fullscreen {
        z-index: 1050;
        top: 60px !important;
        left: 85px !important;
        width: calc(100% - 85px) !important;
    }
    
    /* Fix form styling */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        display: block;
        width: 100%;
        padding: 0.625rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .btn {
        margin-top: 1rem;
    }
    
    .page-title-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .breadcrumb {
        background: transparent;
        margin-bottom: 0;
        padding: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #6c757d;
    }
    
    .main-card {
        margin-bottom: 2rem;
    }
    
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -5px;
        margin-left: -5px;
    }
    
    .form-row > .col,
    .form-row > [class*="col-"] {
        padding-right: 5px;
        padding-left: 5px;
    }
    
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-draft { background-color: #6c757d; color: white; }
    .status-active { background-color: #28a745; color: white; }
    .status-completed { background-color: #007bff; color: white; }
    .status-cancelled { background-color: #dc3545; color: white; }
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
                <div>
                    <h2>Edit Stock Opname</h2>
                    <div class="page-title-subheading">
                        Form untuk mengedit data stock opname
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('stockopname') ?>">Stock Opname</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Stock Opname
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <!-- Display Messages -->
            <?php if (session()->getFlashdata('message')): ?>
                <?php $message = session()->getFlashdata('message'); ?>
                <div class="alert alert-<?= $message['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $message['text'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

            <form id="frm_edit" method="post" action="<?= base_url('stockopname/update/' . $stockopname->ID); ?>">
                <?= csrf_field() ?>
                
                <div class="card card-shadow mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-edit me-2"></i>
                            Edit Informasi Stock Opname
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="ProjectName">Nama Projek <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= (validation_show_error('ProjectName')) ? 'is-invalid' : '' ?>" 
                                           id="ProjectName"
                                           name="ProjectName" 
                                           value="<?= set_value('ProjectName', $stockopname->ProjectName) ?>" 
                                           placeholder="Masukkan nama projek"
                                           required />
                                    <?= validation_show_error('ProjectName', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="Tahun">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control <?= (validation_show_error('Tahun')) ? 'is-invalid' : '' ?>" 
                                           id="Tahun"
                                           name="Tahun" 
                                           value="<?= set_value('Tahun', $stockopname->Tahun) ?>" 
                                           placeholder="<?= date('Y') ?>"
                                           min="2020" 
                                           max="2030"
                                           required />
                                    <?= validation_show_error('Tahun', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="Koordinator">Koordinator <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= (validation_show_error('Koordinator')) ? 'is-invalid' : '' ?>" 
                                           id="Koordinator"
                                           name="Koordinator" 
                                           value="<?= set_value('Koordinator', $stockopname->Koordinator) ?>" 
                                           placeholder="Masukkan nama koordinator"
                                           required />
                                    <?= validation_show_error('Koordinator', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="TglMulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control <?= (validation_show_error('TglMulai')) ? 'is-invalid' : '' ?>" 
                                           id="TglMulai"
                                           name="TglMulai" 
                                           value="<?= set_value('TglMulai', $stockopname->TglMulai) ?>" 
                                           required />
                                    <?= validation_show_error('TglMulai', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                        </div>

                     

                        <div class="form-row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="Keterangan">Keterangan</label>
                                    <textarea class="form-control <?= (validation_show_error('Keterangan')) ? 'is-invalid' : '' ?>" 
                                              id="Keterangan"
                                              name="Keterangan" 
                                              rows="3"
                                              placeholder="Masukkan keterangan (opsional)"><?= set_value('Keterangan', $stockopname->Keterangan) ?></textarea>
                                    <?= validation_show_error('Keterangan', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-info-circle me-2"></i>
                            Informasi Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-lg-6 col-md-12">
                                <p class="mb-1"><strong>Dibuat pada:</strong></p>
                                <p class="text-muted">
                                    <?= isset($stockopname->CreateDate) ? date('d M Y H:i', strtotime($stockopname->CreateDate)) : '-' ?>
                                </p>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <p class="mb-1"><strong>Terakhir diperbarui:</strong></p>
                                <p class="text-muted">
                                    <?= isset($stockopname->CreateDate) ? date('d M Y H:i', strtotime($stockopname->UpdateDate)) : '-' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="form-actions d-flex justify-content-between">
                            <a href="<?= base_url('stockopname') ?>" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Kembali
                            </a>
                            <div>
                                <a href="<?= base_url('stockopname/detail/' . $stockopname->ID) ?>" class="btn btn-info me-2">
                                    <i class="fa fa-eye me-1"></i> Detail
                                </a>
                                <button type="reset" class="btn btn-light me-2">
                                    <i class="fa fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary" name="submit">
                                    <i class="fa fa-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function() {
    // Form validation
    $('#frm_edit').on('submit', function(e) {
        let isValid = true;
        let firstErrorField = null;

        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                if (!firstErrorField) {
                    firstErrorField = $(this);
                }
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Validate year
        const year = parseInt($('#Tahun').val());
        if (year && (year < 2020 || year > 2030)) {
            $('#Tahun').addClass('is-invalid');
            if (!firstErrorField) {
                firstErrorField = $('#Tahun');
            }
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            if (firstErrorField) {
                firstErrorField.focus();
                $('html, body').animate({
                    scrollTop: firstErrorField.offset().top - 100
                }, 500);
            }
            
            // Show error message
            if (typeof toastr !== 'undefined') {
                toastr.error('Mohon lengkapi semua field yang wajib diisi!', 'Validasi Error');
            } else {
                alert('Mohon lengkapi semua field yang wajib diisi!');
            }
        }
    });

    // Real-time validation
    $('[required]').on('blur', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
        }
    });

    // Auto-focus first field
    $('#ProjectName').focus();

    // Reset form
    $('button[type="reset"]').on('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin mereset form ini?')) {
            $('#frm_edit')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
        }
    });

  
});
</script>
<?= $this->endSection('script'); ?>