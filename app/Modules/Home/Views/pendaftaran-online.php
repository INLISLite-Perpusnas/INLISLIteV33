<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Token INA Digital; layout publik tidak memuatnya, jadi nilainya disamakan dengan layout admin */
    .reg-page {
      --ina-primary: #1d5fd1;
      --ina-primary-hover: #174ea9;
      --ina-primary-subtle: #eef4ff;
      --ina-text: #1f2937;
      --ina-text-subtle: #374151;
      --ina-text-muted: #6b7280;
      --ina-icon: #4b5563;
      --ina-border: #e5e7eb;
      --ina-border-strong: #d1d5db;
      --ina-surface: #ffffff;
      --ina-page: #f7f7f8;
      --ina-success: #1f7a36;
      --ina-danger: #c62828;
      --ina-radius: 12px;
      --ina-shadow: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .06);
      color: var(--ina-text);
    }

    .reg-page .page-header {
      margin-bottom: 24px;
    }

    .reg-page .page-header h1 {
      color: var(--ina-text);
      font-weight: 700;
      font-size: 32px;
      line-height: 40px;
      letter-spacing: -.01em;
      margin: 0;
    }

    .reg-page .page-header p {
      color: var(--ina-text-muted);
      margin: 4px 0 0;
      font-size: 16px;
    }

    .reg-page .registration-container,
    .reg-page .progress-section,
    .reg-page .login-link,
    .reg-page .rules-card {
      background: var(--ina-surface);
      border: 1px solid var(--ina-border);
      border-radius: var(--ina-radius);
      box-shadow: var(--ina-shadow);
    }

    .reg-page .registration-container {
      padding: 32px;
      margin-bottom: 24px;
    }

    .reg-page .section-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--ina-border);
    }

    .reg-page .section-header .icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      width: 36px;
      height: 36px;
      font-size: 16px;
      color: var(--ina-primary);
      background: var(--ina-primary-subtle);
      border-radius: 8px;
    }

    .reg-page .section-header h3 {
      color: var(--ina-text);
      font-weight: 600;
      font-size: 18px;
      line-height: 26px;
      margin: 0;
    }

    .reg-page .form-group {
      margin-bottom: 20px;
    }

    .reg-page .form-label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      font-weight: 600;
      color: var(--ina-text);
    }

    .reg-page .required::after {
      content: " *";
      color: var(--ina-danger);
    }

    .reg-page .form-control,
    .reg-page .form-select {
      min-height: 44px;
      padding: 10px 14px;
      font-size: 15px;
      color: var(--ina-text);
      background-color: var(--ina-surface);
      border: 1px solid var(--ina-border-strong);
      border-radius: 8px;
      box-shadow: none;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    .reg-page .form-select {
      padding-right: 36px;
    }

    .reg-page .form-control::placeholder {
      color: #9ca3af;
    }

    .reg-page .form-control:hover:not(:disabled):not([readonly]),
    .reg-page .form-select:hover:not(:disabled) {
      border-color: #9ca3af;
    }

    .reg-page .form-control:focus,
    .reg-page .form-select:focus {
      border-color: var(--ina-primary);
      box-shadow: 0 0 0 3px rgba(29, 95, 209, .15);
      outline: none;
    }

    .reg-page .form-control:disabled,
    .reg-page .form-control[readonly],
    .reg-page .form-select:disabled {
      color: var(--ina-text-muted);
      background-color: #f3f4f6;
      border-color: var(--ina-border);
    }

    .reg-page .form-control.is-invalid,
    .reg-page .form-select.is-invalid {
      border-color: var(--ina-danger);
    }

    .reg-page .form-control.is-valid,
    .reg-page .form-select.is-valid {
      border-color: var(--ina-success);
    }

    .reg-page .help-text {
      margin-top: 6px;
      font-size: 13px;
      color: var(--ina-text-muted);
    }

    .reg-page .btn {
      min-height: 44px;
      padding: 10px 20px;
      font-size: 15px;
      font-weight: 600;
      border-radius: 8px;
      box-shadow: none;
      transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    .reg-page .btn:focus-visible {
      outline: 2px solid var(--ina-primary);
      outline-offset: 2px;
      box-shadow: none;
    }

    .reg-page .btn-primary {
      color: #fff;
      background: var(--ina-primary);
      border: 1px solid var(--ina-primary);
    }

    .reg-page .btn-primary:hover:not(:disabled) {
      background: var(--ina-primary-hover);
      border-color: var(--ina-primary-hover);
    }

    .reg-page .btn-outline-primary {
      color: var(--ina-primary);
      background: var(--ina-surface);
      border: 1px solid var(--ina-primary);
    }

    .reg-page .btn-outline-primary:hover:not(:disabled) {
      color: var(--ina-primary);
      background: var(--ina-primary-subtle);
      border-color: var(--ina-primary);
    }

    .reg-page .btn-success {
      color: #fff;
      background: var(--ina-success);
      border: 1px solid var(--ina-success);
    }

    .reg-page .checkbox-group {
      padding: 14px 16px;
      margin: 16px 0;
      background: var(--ina-surface);
      border: 1px solid var(--ina-border);
      border-radius: 8px;
    }

    .reg-page .form-check-input:checked {
      background-color: var(--ina-primary);
      border-color: var(--ina-primary);
    }

    .reg-page .form-check-label {
      font-size: 15px;
      font-weight: 500;
      color: var(--ina-text-subtle);
    }

    .reg-page .progress-section {
      padding: 20px 24px;
      margin-bottom: 24px;
    }

    .reg-page .progress {
      height: 8px;
      background: var(--ina-border);
      border-radius: 4px;
    }

    .reg-page .progress-bar {
      background: var(--ina-primary);
    }

    .reg-page .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-top: 12px;
    }

    .reg-page .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      font-size: 14px;
      color: var(--ina-text-muted);
    }

    .reg-page .step.active {
      color: var(--ina-primary);
      font-weight: 600;
    }

    .reg-page .input-group-icon {
      position: relative;
    }

    .reg-page .input-group-icon .form-control {
      padding-left: 42px;
    }

    .reg-page .input-group-icon .icon {
      position: absolute;
      left: 14px;
      top: 22px;
      transform: translateY(-50%);
      color: var(--ina-icon);
      z-index: 10;
    }

    .reg-page .card-section {
      padding: 24px 24px 4px;
      margin: 16px 0 24px;
      background: var(--ina-page);
      border: 1px solid var(--ina-border);
      border-radius: 8px;
    }

    .reg-page .login-link {
      padding: 16px 24px;
      margin-bottom: 32px;
      text-align: center;
      color: var(--ina-text-subtle);
    }

    .reg-page .login-link a {
      color: var(--ina-primary);
    }

    /* Persyaratan dan tata tertib */
    .reg-page .rules-card {
      height: 100%;
      padding: 24px;
    }

    .reg-page .rules-card h2 {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 0 0 16px;
      font-size: 18px;
      line-height: 26px;
      font-weight: 600;
      color: var(--ina-text);
    }

    .reg-page .rules-card h2 .icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      width: 36px;
      height: 36px;
      font-size: 16px;
      color: var(--ina-primary);
      background: var(--ina-primary-subtle);
      border-radius: 8px;
    }

    .reg-page .rules-content {
      font-size: 15px;
      line-height: 24px;
      color: var(--ina-text-subtle);
    }

    .reg-page .rules-content ul,
    .reg-page .rules-content ol {
      padding-left: 20px;
      margin: 0;
    }

    .reg-page .rules-content li {
      margin-bottom: 8px;
    }

    .reg-page .rules-content li:last-child,
    .reg-page .rules-content p:last-child {
      margin-bottom: 0;
    }

    @media (max-width: 768px) {
      .reg-page .registration-container {
        padding: 20px;
      }

      .reg-page .card-section {
        padding: 16px 16px 0;
      }

      .reg-page .page-header h1 {
        font-size: 26px;
        line-height: 34px;
      }

      .reg-page .step {
        font-size: 13px;
      }
    }

    .shake {
      animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
    }

    @keyframes shake {
      10%, 90% { transform: translate3d(-1px, 0, 0); }
      20%, 80% { transform: translate3d(2px, 0, 0); }
      30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
      40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    @media (prefers-reduced-motion: reduce) {
      .shake {
        animation: none;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

  <div class="container reg-page" style="margin-top: 120px;">
    <!-- Header -->
    <div class="page-header">
      <h1>Pendaftaran Anggota</h1>
      <p>Daftarkan diri Anda sebagai anggota perpustakaan</p>
    </div>

    <!-- Persyaratan dan Tata Tertib (dikelola di /master-redaksi-keanggotaan) -->
    <?php if (!empty($member_rules)) : ?>
      <div class="row g-4 mb-4">
        <?php foreach ($member_rules as $rule) : ?>
          <div class="col-md-6">
            <section class="rules-card">
              <h2>
                <span class="icon"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span>
                <?= esc($rule->NameCategory) ?>
              </h2>
              <div class="rules-content"><?= $rule->Contents ?></div>
            </section>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Progress Indicator -->
    <div class="progress-section">
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="step-indicator">
        <div class="step active">
          <i class="fas fa-user-check"></i>
          <span>Verifikasi</span>
        </div>
        <div class="step">
          <i class="fas fa-edit"></i>
          <span>Data Diri</span>
        </div>
        <div class="step">
          <i class="fas fa-check-circle"></i>
          <span>Selesai</span>
        </div>
      </div>
    </div>

    <!-- Registration Form -->
    <div class="registration-container">
      <form id="frm_register" method="post" novalidate>
        <!-- Section 1: Verifikasi -->
        <div class="section-header">
          <div class="icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <h3>Verifikasi Identitas</h3>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label required">No. Identitas</label>
              <div class="input-group-icon">
                <i class="fas fa-id-card icon"></i>
                <input type="text" name="IdentityNo" id="IdentityNo" class="form-control" required placeholder="Masukkan Nomor Identitas">
              </div>
              <div class="help-text">Akan digunakan sebagai Nomor Anggota</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label required">Jenis Anggota</label>
              <select class="form-select" name="JenisAnggota_id" id="JenisAnggota_id" required>
                <option value="" disabled selected>Pilih Jenis Anggota</option>
                <?php foreach (get_ref_table('jenis_anggota', 'id, jenisanggota, MasaBerlakuAnggota', null, 'data') as $row) : ?>
                  <option value="<?= esc($row->id, 'attr') ?>" data-days="<?= esc($row->MasaBerlakuAnggota, 'attr') ?>">
                    <?= esc($row->jenisanggota) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label required">Jenis Identitas</label>
              <select class="form-select" name="IdentityType_id" id="IdentityType_id" required>
                <option value="" disabled selected>Jenis identitas</option>
                <?php foreach (get_table('master_jenis_identitas', 'id, Nama', null, 'data') as $row) : ?>
                  <option value="<?= $row->id ?>" <?= set_select('IdentityType_id', $row->id) ?>><?= $row->Nama ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <?php
          $memberNumberSetting = db_connect()->table('settingparameters')
            ->where('Name', 'TipeNomorAnggota')->get()->getRow();
          $memberNumberType = $memberNumberSetting->Value ?? 'Manual';
        ?>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label<?= $memberNumberType === 'Manual' ? ' required' : '' ?>">Nomor Anggota</label>
              <input type="text" name="MemberNo" id="MemberNo" class="form-control"
                placeholder="<?= $memberNumberType === 'Manual' ? 'Masukkan Nomor Anggota' : 'Dibuat otomatis' ?>"
                <?= $memberNumberType === 'Manual' ? 'required' : 'readonly' ?>>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Tanggal Pendaftaran</label>
              <input type="date" name="RegisterDate" id="RegisterDate" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Masa Berlaku</label>
              <input type="datetime-local" name="EndDate" id="EndDate" class="form-control" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label class="form-label required">Email</label>
              <div class="input-group-icon">
                <i class="fas fa-envelope icon"></i>
                <input type="email" name="Email" id="Email" class="form-control" required placeholder="Masukkan Email Aktif">
              </div>
              <div class="help-text">Email aktif untuk menerima notifikasi pendaftaran</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">&nbsp;</label>
              <button type="button" class="btn btn-outline-primary w-100" id="btnCheck">
                <i class="fas fa-search me-2"></i>Verifikasi Data
              </button>
            </div>
          </div>
        </div>

        <!-- Section 2: Data Pribadi -->
        <div class="section-header mt-5">
          <div class="icon">
            <i class="fas fa-user"></i>
          </div>
          <h3>Data Pribadi</h3>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label required">Nama Lengkap</label>
              <div class="input-group-icon">
                <i class="fas fa-user icon"></i>
                <input type="text" name="Fullname" id="Fullname" class="form-control" required placeholder="Masukkan Nama Lengkap">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label required">No. HP</label>
              <div class="input-group-icon">
                <i class="fas fa-phone icon"></i>
                <input type="tel" name="Phone" id="Phone" class="form-control" required placeholder="Masukkan No. HP">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label required">Tempat Lahir</label>
              <input type="text" name="PlaceOfBirth" id="PlaceOfBirth" class="form-control" required placeholder="Tempat Lahir">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label required">Tanggal Lahir</label>
              <input type="date" name="DateOfBirth" id="DateOfBirth" class="form-control" max="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label required">Jenis Kelamin</label>
              <select class="form-select" name="Sex_id" id="Sex_id" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="1">Laki-laki</option>
                <option value="2">Perempuan</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 3: Alamat Identitas -->
        <div class="section-header mt-5">
          <div class="icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <h3>Alamat sesuai Identitas</h3>
        </div>

        <div class="card-section">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label class="form-label required">Alamat Lengkap</label>
                <textarea name="Address" id="Address" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap sesuai identitas"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Provinsi</label>
                <select class="form-select" id="Province" name="Province" required>
                  <option value="">Pilih Provinsi</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kota</label>
                <select class="form-select" id="City" name="City" required disabled>
                  <option value="">Pilih Kota</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kecamatan</label>
                <select class="form-select" id="District" name="Kecamatan" required disabled>
                  <option value="">Pilih Kecamatan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kelurahan</label>
                <select class="form-select" id="SubDistrict" name="Kelurahan" required disabled>
                  <option value="">Pilih Kelurahan</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 4: Alamat Saat Ini -->
        <div class="section-header mt-4">
          <div class="icon">
            <i class="fas fa-home"></i>
          </div>
          <h3>Alamat Saat Ini</h3>
        </div>

        <div class="checkbox-group">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check_copy">
            <label class="form-check-label" for="check_copy">
              <i class="fas fa-copy me-2"></i>Alamat saat ini sama dengan alamat identitas
            </label>
          </div>
        </div>

        <div class="card-section">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label class="form-label required">Alamat Lengkap</label>
                <textarea name="AddressNow" id="AddressNow" class="form-control" rows="3" required placeholder="Masukkan alamat saat ini"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Provinsi</label>
                <select class="form-select" id="ProvinceNow" name="ProvinceNow" required>
                  <option value="">Pilih Provinsi</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kota</label>
                <select class="form-select" id="CityNow" name="CityNow" required disabled>
                  <option value="">Pilih Kota</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kecamatan</label>
                <select class="form-select" id="DistrictNow" name="KecamatanNow" required disabled>
                  <option value="">Pilih Kecamatan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="form-group">
                <label class="form-label required">Kelurahan</label>
                <select class="form-select" id="SubDistrictNow" name="KelurahanNow" required disabled>
                  <option value="">Pilih Kelurahan</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 5: Pernyataan -->
        <div class="section-header mt-4">
          <div class="icon">
            <i class="fas fa-file-contract"></i>
          </div>
          <h3>Pernyataan</h3>
        </div>

        <div class="checkbox-group">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check_agree" name="check_agree" value="1" required>
            <label class="form-check-label" for="check_agree">
              <i class="fas fa-check-circle me-2"></i>
              Saya menyatakan bahwa data yang diisi adalah benar dan dapat dipertanggungjawabkan
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSubmit">
            <i class="fas fa-user-plus me-2"></i>Daftar Sebagai Anggota
          </button>
        </div>

        <div id="msgSubmit" class="text-center mt-3 d-none"></div>
      </form>
    </div>

    <!-- Login Link -->
    <div class="login-link">
      <p class="mb-0">
        <i class="fas fa-info-circle me-2"></i>
        Sudah memiliki Nomor Anggota?
        <a href="<?= base_url('login') ?>" class="text-decoration-none fw-bold">Login Anggota</a>
      </p>
    </div>
  </div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha384-I4Qw/vWb/sK/7VwepTtkaq636YLYClbEgEwKp3ueUCvjiLFrcoKUFAY5mOl40Fj3" crossorigin="anonymous"></script>

<script>
    let isIdentityVerified = false;
    let verifiedIdentityNo = '';
    let verifiedEmail = '';

    // Initialize form
    $(document).ready(function() {
      // Progress tracking
      updateProgress();
      updateMemberEndDate();
    });

    function updateMemberEndDate() {
      const selected = $('#JenisAnggota_id option:selected');
      const days = parseInt(selected.data('days'), 10);
      const registerDate = $('#RegisterDate').val();
      if (!registerDate || Number.isNaN(days)) {
        $('#EndDate').val('');
        return;
      }

      const endDate = new Date(`${registerDate}T00:00:00`);
      endDate.setDate(endDate.getDate() + days);
      const pad = value => String(value).padStart(2, '0');
      $('#EndDate').val(`${endDate.getFullYear()}-${pad(endDate.getMonth() + 1)}-${pad(endDate.getDate())}T23:59`);
    }

    $('#JenisAnggota_id').on('change', updateMemberEndDate);

    const resetSelect = (dom, label, disabled = true) => {
      $(dom)
        .html(`<option value="">${label}</option>`)
        .val('')
        .removeClass('is-valid is-invalid')
        .prop('disabled', disabled);
    };

    const getData = async (url, dom, selected = false) => {
      $(dom).html('<option value="">Memuat...</option>').prop('disabled', true);

      try {
        const res = await axios.get(url);
        let output = '<option value="">- Pilih -</option>';
        $.each(res.data, function(key, val) {
          const option = $('<option>', {
            value: val.code,
            text: val.name
          });
          output += option.prop('outerHTML');
        });
        $(dom).html(output).prop('disabled', false);
        if (selected !== false && selected !== null && selected !== '') {
          $(dom).val(String(selected));
        }
        return true;
      } catch (err) {
        console.error(err);
        $(dom).html('<option value="">Data gagal dimuat</option>').prop('disabled', true);
        return false;
      }
    }

    // Load initial data
    const baseUrl = '<?php echo base_url(); ?>';
    getData(`${baseUrl}/api/region/province`, `#Province`);

    $('#Province').change(async function() {
      const code = $(this).val();
      resetSelect('#City', 'Pilih Kota');
      resetSelect('#District', 'Pilih Kecamatan');
      resetSelect('#SubDistrict', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/city/${code}`, '#City');
    });

    $('#City').change(async function() {
      const code = $(this).val();
      resetSelect('#District', 'Pilih Kecamatan');
      resetSelect('#SubDistrict', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/district/${code}`, '#District');
    });

    $('#District').change(async function() {
      const code = $(this).val();
      resetSelect('#SubDistrict', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/sub_district/${code}`, '#SubDistrict');
    });

    getData(`${baseUrl}/api/region/province`, `#ProvinceNow`);

    $('#ProvinceNow').change(async function() {
      const code = $(this).val();
      resetSelect('#CityNow', 'Pilih Kota');
      resetSelect('#DistrictNow', 'Pilih Kecamatan');
      resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/city/${code}`, '#CityNow');
    });

    $('#CityNow').change(async function() {
      const code = $(this).val();
      resetSelect('#DistrictNow', 'Pilih Kecamatan');
      resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/district/${code}`, '#DistrictNow');
    });

    $('#DistrictNow').change(async function() {
      const code = $(this).val();
      resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
      if (code) await getData(`${baseUrl}/api/region/sub_district/${code}`, '#SubDistrictNow');
    });

    // Copy address functionality
    $("#check_copy").change(async function() {
      if (this.checked) {
        const Address = $('#Address').val();
        const Province = $('#Province').val();
        const City = $('#City').val();
        const District = $('#District').val();
        const SubDistrict = $('#SubDistrict').val();

        $('#AddressNow').val(Address);
        resetSelect('#CityNow', 'Pilih Kota');
        resetSelect('#DistrictNow', 'Pilih Kecamatan');
        resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
        await getData(`${baseUrl}/api/region/province`, '#ProvinceNow', Province);
        if (Province) await getData(`${baseUrl}/api/region/city/${Province}`, '#CityNow', City);
        if (City) await getData(`${baseUrl}/api/region/district/${City}`, '#DistrictNow', District);
        if (District) await getData(`${baseUrl}/api/region/sub_district/${District}`, '#SubDistrictNow', SubDistrict);
      } else {
        $('#AddressNow').val('');
        resetSelect('#CityNow', 'Pilih Kota');
        resetSelect('#DistrictNow', 'Pilih Kecamatan');
        resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
        await getData(`${baseUrl}/api/region/province`, '#ProvinceNow');
      }
    });

    $('#Address, #Province, #City, #District, #SubDistrict').on('input change', function() {
      if ($('#check_copy').is(':checked')) {
        $('#check_copy').prop('checked', false).trigger('change');
      }
    });

    function resetVerificationState() {
      isIdentityVerified = false;
      verifiedIdentityNo = '';
      verifiedEmail = '';
      $('#btnCheck')
        .prop('disabled', false)
        .removeClass('btn-success')
        .addClass('btn-outline-primary')
        .html('<i class="fas fa-search me-2"></i>Verifikasi Data');
      updateProgress(33);
    }

    $('#IdentityNo, #Email').on('input', function() {
      if (isIdentityVerified) resetVerificationState();
    });

    // Check button
    $("#btnCheck").click(function() {
      const identityInput = document.getElementById('IdentityNo');
      const emailInput = document.getElementById('Email');
      if (!identityInput.checkValidity() || !emailInput.checkValidity()) {
        identityInput.reportValidity();
        emailInput.reportValidity();
        return false;
      }

      const identityNo = identityInput.value.trim();
      const email = emailInput.value.trim();
      const url = `${baseUrl}/api/member/check`;
      const data_post = { email: email, username: identityNo };

      $("#btnCheck").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
      $("#btnCheck").attr('disabled', true);

      $.ajax({
          url: url,
          type: 'POST',
          data: data_post,
        })
        .done(function(res) {
          if (res.error == false) {
            isIdentityVerified = true;
            verifiedIdentityNo = identityNo;
            verifiedEmail = email;
            $('#btnCheck')
              .prop('disabled', true)
              .html('<i class="fas fa-check me-2"></i>Terverifikasi')
              .removeClass('btn-outline-primary')
              .addClass('btn-success');
            updateProgress(66);

            Swal.fire({
              title: 'Yeay',
              html: res.message,
              icon: 'success',
              showConfirmButton: false,
              timer: 5000,
            });
          } else {
            formError();
            Swal.fire({
              title: 'Oups',
              html: res.message,
              icon: 'error',
              showConfirmButton: false,
              timer: 5000
            }).then(() => {
              $("#btnCheck").attr('disabled', false);
              $("#btnCheck").html('Cek No. Identitas dan Email');
            });
          }
        })
        .fail(function(res) {
          Swal.fire({
            title: 'Oups',
            text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
            icon: 'error',
            showConfirmButton: false,
            timer: 5000
          }).then(() => {
            $("#btnCheck").attr('disabled', false);
            $("#btnCheck").html('Cek No. Identitas dan Email');
          });
        });

      return false;
    });

    $("#frm_register").on("submit", function(event) {
      event.preventDefault();

      const formElement = this;
      if (!formElement.checkValidity()) {
        formElement.classList.add('was-validated');
        formElement.reportValidity();
        formError();
        return false;
      }

      if (!isIdentityVerified ||
          verifiedIdentityNo !== $('#IdentityNo').val().trim() ||
          verifiedEmail !== $('#Email').val().trim()) {
        formError();
        Swal.fire({
          title: 'Verifikasi diperlukan',
          text: 'Silakan verifikasi Nomor Identitas dan Email sebelum mendaftar.',
          icon: 'warning',
          showConfirmButton: true,
        });
        return false;
      }

      submitForm();
    });

    function submitForm() {
      var form = $("#frm_register");
      var url = `${baseUrl}/api/member/register`;
      var data_post = form.serialize();

      $('#btnSubmit').html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
      $('#btnSubmit').attr('disabled', true);

      $.ajax({
          url: url,
          type: 'POST',
          data: data_post,
        })
        .done(function(res) {
          if (res.error == false) {
            updateProgress(100);
            Swal.fire({
              title: 'Berhasil',
              html: res.message || 'Pendaftaran berhasil. Silakan cek email Anda untuk verifikasi akun!',
              icon: 'success',
              showConfirmButton: true,
              confirmButtonText: 'OK',
            }).then(() => {
              $('#frm_register')[0].reset();
              $('#frm_register').removeClass('was-validated');
              $('#frm_register').find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
              resetVerificationState();
              resetSelect('#City', 'Pilih Kota');
              resetSelect('#District', 'Pilih Kecamatan');
              resetSelect('#SubDistrict', 'Pilih Kelurahan');
              resetSelect('#CityNow', 'Pilih Kota');
              resetSelect('#DistrictNow', 'Pilih Kecamatan');
              resetSelect('#SubDistrictNow', 'Pilih Kelurahan');
              getData(`${baseUrl}/api/region/province`, '#Province');
              getData(`${baseUrl}/api/region/province`, '#ProvinceNow');
              updateProgress(33);
              $('#btnSubmit').attr('disabled', false);
              $('#btnSubmit').html('<i class="fas fa-user-plus me-2"></i>Daftar Sebagai Anggota');
            });
          } else {
            formError();
            Swal.fire({
              title: 'Gagal',
              html: res.message,
              icon: 'error',
              showConfirmButton: false,
              timer: 5000
            }).then(() => {
              $('#btnSubmit').attr('disabled', false);
              $('#btnSubmit').html('Daftar Anggota');
            });
          }
        })
        .fail(function(res) {
          Swal.fire({
            title: 'Error',
            html: 'Link verifikasi anggota gagal terkirim.<br>Coba beberapa saat lagi atau hubungi Admin',
            icon: 'error',
            showConfirmButton: false,
            timer: 5000
          }).then(() => {
            $('#btnSubmit').attr('disabled', false);
            $('#btnSubmit').html('Daftar Anggota');
          });
        });

      return false;
    }

    function formError() {
      $("#frm_register").removeClass().addClass('shake animated').one(
        'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',
        function() {
          $(this).removeClass();
        });
    }

    // Progress tracking functions
    function updateProgress(percentage = 33) {
      $('.progress-bar').css('width', percentage + '%');

      $('.step').removeClass('active');
      if (percentage >= 33) $('.step:eq(0)').addClass('active');
      if (percentage >= 66) $('.step:eq(1)').addClass('active');
      if (percentage >= 100) $('.step:eq(2)').addClass('active');
    }

    // Real-time validation
    $('input[required]:not([type=checkbox]), select[required], textarea[required]').on('blur change', function(e) {
      if ($(this).val()) {
        if (e.target.type === 'email') {
          const email = $(this).val();
          const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!regex.test(email)) {
            $(this).removeClass('is-valid').addClass('is-invalid');
          } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
          }
        } else {
          $(this).removeClass('is-invalid').addClass('is-valid');
        }
      } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
      }
    });

    $("input[required][type=checkbox]").change((e) => {
      if (e.target.checked) {
        $(e.target).removeClass('is-invalid').addClass('is-valid');
      } else {
        $(e.target).removeClass('is-valid').addClass('is-invalid');
      }
    });

    $('input[type=tel]').on('input', function() {
      this.value = this.value.replace(/\D/g, '');
    });
</script>
<?= $this->endSection() ?>
