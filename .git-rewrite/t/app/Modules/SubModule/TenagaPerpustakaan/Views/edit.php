<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.required::after {
    content: " *";
    color: red;
}
.form-group label {
    font-weight: 600;
    color: #495057;
}
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(45deg, #0056b3, #003d82);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}
.custom-control-label::before {
    border-radius: 0.25rem;
}
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}
.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.section-divider {
    border-top: 2px solid #e9ecef;
    margin: 2rem 0 1.5rem 0;
    position: relative;
}
.section-divider::before {
    content: '';
    position: absolute;
    top: -1px;
    left: 0;
    width: 50px;
    height: 2px;
    background: #007bff;
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-edit icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Edit Tenaga Perpustakaan
                    <div class="page-title-subheading">Form untuk mengubah data tenaga perpustakaan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('tenaga-perpustakaan') ?>">Tenaga Perpustakaan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"></i>Form Edit Tenaga Perpustakaan
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('tenaga-perpustakaan') ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('message')): ?>
                <?php $message = session()->getFlashdata('message'); ?>
                <?php if (is_array($message)): ?>
                    <div class="alert alert-<?= $message['type'] == 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show" role="alert">
                        <i class="fa <?= $message['type'] == 'success' ? 'fa-check-circle' : ($message['type'] == 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle') ?> mr-2"></i>
                        <strong><?= ucfirst($message['type'] == 'error' ? 'Error' : $message['type']) ?>!</strong> <?= esc($message['text']) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fa fa-info-circle mr-2"></i>
                        <?= esc($message) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    <strong>Validation Error!</strong> Silakan perbaiki kesalahan berikut:
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= current_url() ?>" id="form-pustakawan-edit">
                <?= csrf_field() ?>
                
                <!-- Section 1: Data Pribadi -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 text-primary"><i class="fa fa-user"></i> Data Pribadi</h5>
                        <hr>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama" class="required">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= old('nama', $pustakawan->nama ?? '') ?>" required>
                            <small class="form-text text-muted">Masukkan nama lengkap pustakawan</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nip" class="required">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" 
                                   value="<?= old('nip', $pustakawan->nip ?? '') ?>" required>
                            <small class="form-text text-muted">Nomor Induk Pegawai</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                                   value="<?= old('tempat_lahir', $pustakawan->tempat_lahir ?? '') ?>" 
                                   placeholder="Contoh: Jakarta">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="text" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                   value="<?= old('tanggal_lahir', $pustakawan->tanggal_lahir ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?= old('jenis_kelamin', $pustakawan->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= old('jenis_kelamin', $pustakawan->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pendidikan">Pendidikan Terakhir</label>
                            <select class="form-control" id="pendidikan" name="pendidikan">
                                <option value="">-- Pilih Pendidikan --</option>
                                <?php 
                                $pendidikan_options = ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
                                $current_pendidikan = old('pendidikan', $pustakawan->pendidikan ?? '');
                                foreach ($pendidikan_options as $option): ?>
                                    <option value="<?= $option ?>" <?= $current_pendidikan == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bidang_pendidikan">Bidang Pendidikan</label>
                            <input type="text" class="form-control" id="bidang_pendidikan" name="bidang_pendidikan" 
                                   value="<?= old('bidang_pendidikan', $pustakawan->bidang_pendidikan ?? '') ?>" 
                                   placeholder="Contoh: Ilmu Perpustakaan, Informatika, Sastra Indonesia, dll">
                            <small class="form-text text-muted">Jurusan atau bidang studi yang diambil</small>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kontak -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3 text-primary"><i class="fa fa-phone"></i> Informasi Kontak</h5>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_hp">No. HP / WhatsApp</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                   value="<?= old('no_hp', $pustakawan->no_hp ?? '') ?>" 
                                   placeholder="Contoh: 081234567890">
                            <small class="form-text text-muted">Nomor telepon yang bisa dihubungi</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= old('email', $pustakawan->email ?? '') ?>" 
                                   placeholder="contoh@email.com">
                            <small class="form-text text-muted">Alamat email yang valid dan aktif</small>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kepegawaian -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3 text-primary"><i class="fa fa-briefcase"></i> Data Kepegawaian</h5>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status_pegawai">Status Pegawai</label>
                            <select class="form-control" id="status_pegawai" name="status_pegawai">
                                <option value="">-- Pilih Status --</option>
                                <?php 
                                $status_options = ['Pustakawan', 'Tenaga Teknis'];
                                $current_status = old('status_pegawai', $pustakawan->status_pegawai ?? '');
                                foreach ($status_options as $option): ?>
                                    <option value="<?= $option ?>" <?= $current_status == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jabatan">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" 
                                   value="<?= old('jabatan', $pustakawan->jabatan ?? '') ?>" 
                                   placeholder="Contoh: Pustakawan Ahli Madya, Kepala Perpustakaan">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pangkat">Pangkat / Golongan</label>
                            <select class="form-control" id="pangkat" name="pangkat">
                                <option value="">-- Pilih Pangkat --</option>
                                <optgroup label="Golongan I">
                                    <option value="Juru Muda - I/a" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Juru Muda - I/a' ? 'selected' : '' ?>>Juru Muda - I/a</option>
                                    <option value="Juru Muda Tingkat I - I/b" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Juru Muda Tingkat I - I/b' ? 'selected' : '' ?>>Juru Muda Tingkat I - I/b</option>
                                    <option value="Juru - I/c" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Juru - I/c' ? 'selected' : '' ?>>Juru - I/c</option>
                                    <option value="Juru Tingkat I - I/d" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Juru Tingkat I - I/d' ? 'selected' : '' ?>>Juru Tingkat I - I/d</option>
                                </optgroup>
                                <optgroup label="Golongan II">
                                    <option value="Pengatur Muda - II/a" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pengatur Muda - II/a' ? 'selected' : '' ?>>Pengatur Muda - II/a</option>
                                    <option value="Pengatur Muda Tingkat I - II/b" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pengatur Muda Tingkat I - II/b' ? 'selected' : '' ?>>Pengatur Muda Tingkat I - II/b</option>
                                    <option value="Pengatur - II/c" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pengatur - II/c' ? 'selected' : '' ?>>Pengatur - II/c</option>
                                    <option value="Pengatur Tingkat I - II/d" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pengatur Tingkat I - II/d' ? 'selected' : '' ?>>Pengatur Tingkat I - II/d</option>
                                </optgroup>
                                <optgroup label="Golongan III">
                                    <option value="Penata Muda - III/a" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Penata Muda - III/a' ? 'selected' : '' ?>>Penata Muda - III/a</option>
                                    <option value="Penata Muda Tingkat I - III/b" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Penata Muda Tingkat I - III/b' ? 'selected' : '' ?>>Penata Muda Tingkat I - III/b</option>
                                    <option value="Penata - III/c" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Penata - III/c' ? 'selected' : '' ?>>Penata - III/c</option>
                                    <option value="Penata Tingkat I - III/d" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Penata Tingkat I - III/d' ? 'selected' : '' ?>>Penata Tingkat I - III/d</option>
                                </optgroup>
                                <optgroup label="Golongan IV">
                                    <option value="Pembina - IV/a" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pembina - IV/a' ? 'selected' : '' ?>>Pembina - IV/a</option>
                                    <option value="Pembina Tingkat I - IV/b" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pembina Tingkat I - IV/b' ? 'selected' : '' ?>>Pembina Tingkat I - IV/b</option>
                                    <option value="Pembina Utama Muda - IV/c" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pembina Utama Muda - IV/c' ? 'selected' : '' ?>>Pembina Utama Muda - IV/c</option>
                                    <option value="Pembina Utama Madya - IV/d" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pembina Utama Madya - IV/d' ? 'selected' : '' ?>>Pembina Utama Madya - IV/d</option>
                                    <option value="Pembina Utama - IV/e" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Pembina Utama - IV/e' ? 'selected' : '' ?>>Pembina Utama - IV/e</option>
                                </optgroup>
                                <option value="Non PNS" <?= old('pangkat', $pustakawan->pangkat ?? '') == 'Non PNS' ? 'selected' : '' ?>>Non PNS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tmt_pangkat">TMT Pangkat</label>
                            <input type="text" class="form-control" id="tmt_pangkat" name="tmt_pangkat" 
                                   value="<?= old('tmt_pangkat', $pustakawan->tmt_pangkat ?? '') ?>">
                            <small class="form-text text-muted">Terhitung Mulai Tanggal Pangkat</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="tmt_jabatan">TMT Jabatan</label>
                            <input type="text" class="form-control" id="tmt_jabatan" name="tmt_jabatan" 
                                   value="<?= old('tmt_jabatan', $pustakawan->tmt_jabatan ?? '') ?>">
                            <small class="form-text text-muted">Terhitung Mulai Tanggal Jabatan</small>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Dokumen -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3 text-primary"><i class="fa fa-folder"></i> Dokumen & File</h5>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="linkdrive">Link Google Drive</label>
                            <input type="url" class="form-control" id="linkdrive" name="linkdrive" 
                                   value="<?= old('linkdrive', $pustakawan->linkdrive ?? '') ?>" 
                                   placeholder="https://drive.google.com/drive/folders/...">
                            <small class="form-text text-muted">Link folder Google Drive yang berisi dokumen-dokumen pendukung (CV, Ijazah, SK, dll)</small>
                        </div>
                    </div>
                </div>

                <!-- Confirmation -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm" required>
                                <label class="custom-control-label" for="confirm">
                                    <strong>Saya menyatakan bahwa perubahan data yang dilakukan sudah benar dan sesuai dengan dokumen resmi</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-group text-right mt-4">
                    <a href="<?= base_url('tenaga-perpustakaan') ?>" class="btn btn-secondary mr-2">
                        <i class="fa fa-times"></i> Batal
                    </a>
                    <button type="reset" class="btn btn-light mr-2" id="btn-reset">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-update">
                        <i class="fa fa-save"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // Initialize flatpickr for date inputs
    flatpickr("#tanggal_lahir", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        locale: {
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        }
    });
    
    flatpickr("#tmt_pangkat", {
        dateFormat: "Y-m-d",
        locale: {
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        }
    });
    
    flatpickr("#tmt_jabatan", {
        dateFormat: "Y-m-d",
        locale: {
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        }
    });
    
    // Auto format phone number
    $('#no_hp').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        if (value.length > 0) {
            // Format phone number (Indonesia)
            if (value.startsWith('62')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+62' + value.substring(1);
            } else if (!value.startsWith('+')) {
                value = '+62' + value;
            }
        }
        $(this).val(value);
    });
    
    // Auto format NIP
    $('#nip').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        $(this).val(value);
    });
    
    // Form validation
    $('#form-pustakawan-edit').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Validasi nama
        const nama = $('#nama').val().trim();
        if (nama.length < 3) {
            errorMessages.push('Nama harus minimal 3 karakter');
            $('#nama').addClass('is-invalid');
            isValid = false;
        } else {
            $('#nama').removeClass('is-invalid');
        }
        
        // Validasi NIP
        const nip = $('#nip').val().trim();
        if (nip.length < 8) {
            errorMessages.push('NIP harus minimal 8 karakter');
            $('#nip').addClass('is-invalid');
            isValid = false;
        } else {
            $('#nip').removeClass('is-invalid');
        }
        
        // Validasi email jika diisi
        const email = $('#email').val().trim();
        if (email && !validateEmail(email)) {
            errorMessages.push('Format email tidak valid');
            $('#email').addClass('is-invalid');
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }
        
        // Validasi nomor HP jika diisi
        const noHp = $('#no_hp').val().trim();
        if (noHp && noHp.length < 10) {
            errorMessages.push('Nomor HP minimal 10 digit');
            $('#no_hp').addClass('is-invalid');
            isValid = false;
        } else {
            $('#no_hp').removeClass('is-invalid');
        }
        
        // Validasi URL Google Drive jika diisi
        const linkDrive = $('#linkdrive').val().trim();
        if (linkDrive && !validateURL(linkDrive)) {
            errorMessages.push('Format URL Google Drive tidak valid');
            $('#linkdrive').addClass('is-invalid');
            isValid = false;
        } else {
            $('#linkdrive').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Terdapat kesalahan pada form:\n\n' + errorMessages.join('\n'));
            
            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
            return false;
        }
        
        // Show loading on submit button
        $('#btn-update').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    });
    
    // Reset form to original values
    $('#btn-reset').on('click', function() {
        if (confirm('Reset form ke data asli? Semua perubahan akan hilang.')) {
            location.reload();
        }
    });
    
    // Validation functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validateURL(url) {
        try {
            new URL(url);
            return url.includes('drive.google.com') || url.includes('docs.google.com');
        } catch {
            return false;
        }
    }
    
    // Real-time validation feedback
    $('#nama').on('blur', function() {
        if ($(this).val().trim().length < 3) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    $('#nip').on('blur', function() {
        if ($(this).val().trim().length < 8) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    $('#email').on('blur', function() {
        const email = $(this).val().trim();
        if (email && !validateEmail(email)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    $('#linkdrive').on('blur', function() {
        const url = $(this).val().trim();
        if (url && !validateURL(url)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Show character count for text inputs
    $('#nama, #tempat_lahir, #bidang_pendidikan, #jabatan').on('input', function() {
        const maxLength = 100;
        const currentLength = $(this).val().length;
        
        // Remove existing counter
        $(this).next('.char-counter').remove();
        
        // Add counter
        if (currentLength > 0) {
            const counter = $('<small class="char-counter form-text text-muted">' + currentLength + '/' + maxLength + ' karakter</small>');
            counter.insertAfter($(this));
            
            if (currentLength > maxLength * 0.8) {
                counter.removeClass('text-muted').addClass('text-warning');
            }
            if (currentLength >= maxLength) {
                counter.removeClass('text-warning').addClass('text-danger');
            }
        }
    });
    
    // Auto-capitalize first letter
    $('#nama, #tempat_lahir, #bidang_pendidikan, #jabatan').on('input', function() {
        let value = $(this).val();
        if (value.length > 0) {
            value = value.charAt(0).toUpperCase() + value.slice(1);
            $(this).val(value);
        }
    });
    
    // Format NIP with spaces for readability (display only)
    $('#nip').on('blur', function() {
        let value = $(this).val().replace(/\s/g, '');
        if (value.length >= 18) {
            // Format NIP: XXXXXXXX XXXXXX X XXX
            value = value.substring(0, 8) + ' ' + value.substring(8, 14) + ' ' + value.substring(14, 15) + ' ' + value.substring(15);
        }
        $(this).val(value);
    }).on('focus', function() {
        // Remove spaces when editing
        $(this).val($(this).val().replace(/\s/g, ''));
    });
    
    // Enhanced select styling
    $('select').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
    
    // Add floating labels effect
    $('.form-group input, .form-group select, .form-group textarea').on('focus blur', function(e) {
        const $this = $(this);
        const $parent = $this.closest('.form-group');
        
        if (e.type === 'focus' || $this.val() !== '') {
            $parent.addClass('focused');
        } else {
            $parent.removeClass('focused');
        }
    });
    
    // Initialize focused state for pre-filled fields
    $('.form-group input, .form-group select, .form-group textarea').each(function() {
        if ($(this).val() !== '') {
            $(this).closest('.form-group').addClass('focused');
        }
    });
    
    // Track changes for unsaved warning
    let formChanged = false;
    const originalFormData = $('#form-pustakawan-edit').serialize();
    
    $('#form-pustakawan-edit input, #form-pustakawan-edit select, #form-pustakawan-edit textarea').on('change input', function() {
        const currentFormData = $('#form-pustakawan-edit').serialize();
        formChanged = (originalFormData !== currentFormData);
        
        if (formChanged) {
            $('#btn-update').removeClass('btn-primary').addClass('btn-warning');
            $('#btn-update').html('<i class="fa fa-save"></i> Simpan Perubahan');
        } else {
            $('#btn-update').removeClass('btn-warning').addClass('btn-primary');
            $('#btn-update').html('<i class="fa fa-save"></i> Update Data');
        }
    });
    
    // Warn before leaving if changes are made
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        }
    });
    
    // Remove warning when form is submitted
    $('#form-pustakawan-edit').on('submit', function() {
        formChanged = false;
    });
    
    // Show info about last update
    <?php if (isset($pustakawan->updated_at)): ?>
    const lastUpdate = '<?= $pustakawan->updated_at ?? '' ?>';
    if (lastUpdate) {
        const updateDate = new Date(lastUpdate);
        const now = new Date();
        const diffTime = Math.abs(now - updateDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let timeText = '';
        if (diffDays === 1) {
            timeText = 'kemarin';
        } else if (diffDays < 7) {
            timeText = diffDays + ' hari yang lalu';
        } else if (diffDays < 30) {
            timeText = Math.ceil(diffDays / 7) + ' minggu yang lalu';
        } else {
            timeText = Math.ceil(diffDays / 30) + ' bulan yang lalu';
        }
        
        const infoHtml = `
            <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                <i class="fa fa-info-circle mr-2"></i>
                <strong>Info:</strong> Data ini terakhir diupdate ${timeText} pada ${updateDate.toLocaleDateString('id-ID')}.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('.card-body').prepend(infoHtml);
    }
    <?php endif; ?>
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});

// Additional CSS for enhanced styling
const additionalCSS = `
<style>
.focused label {
    color: #007bff;
    font-weight: 600;
}

.char-counter {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-group.focused .form-control {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    border: none;
    color: #212529;
}

.btn-warning:hover {
    background: linear-gradient(45deg, #e0a800, #d39e00);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    color: #212529;
}

.form-control[readonly] {
    background-color: #f8f9fa;
}

optgroup {
    font-weight: bold;
    color: #495057;
}

optgroup option {
    font-weight: normal;
    padding-left: 1rem;
}

.custom-control-label::before {
    border: 2px solid #007bff;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}

.form-text.text-muted {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 3px solid #007bff;
}

.page-title-icon {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

@media (max-width: 768px) {
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-group-mobile {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group-mobile .btn {
        width: 100%;
    }
}
</style>
`;

$('head').append(additionalCSS);
</script>
<?= $this->endSection('script'); ?><?= $this->extend('App\Views\layout\main'); ?>
