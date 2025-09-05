<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Anggota</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #475569;
            --accent-color: #f1f5f9;
            --success-color: #10b981;
            --error-color: #ef4444;
            --border-radius: 12px;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

     

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            padding: 2rem;
            text-align: center;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.5rem;
            margin: 0;
        }

        .page-header p {
            color: var(--secondary-color);
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
        }

        .registration-container {
            background: rgb(255, 255, 255);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent-color);
        }

        .section-header .icon {
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .section-header h3 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .required::after {
            content: " *";
            color: var(--error-color);
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
        }

        .form-control.is-valid {
            border-color: var(--success-color);
        }

        .help-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 25px -8px rgba(37, 99, 235, 0.5);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .checkbox-group {
            background: var(--accent-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-weight: 500;
            color: var(--secondary-color);
        }

        .progress-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), #3b82f6);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.875rem;
            color: #64748b;
        }

        .step.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon .form-control {
            padding-left: 3rem;
        }

        .input-group-icon .icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            z-index: 10;
        }

        .card-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .login-link {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow);
        }

        @media (max-width: 768px) {
            .registration-container {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .section-header {
                flex-direction: column;
                text-align: center;
            }
        }

        .shake {
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</head>
<body><br>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-user-plus me-3"></i>Pendaftaran Anggota</h1>
            <p>Daftarkan diri Anda sebagai anggota perpustakaan</p>
        </div>

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
            <form id="frm_register" action="">
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
                            <label class="form-label required">Jenis Identitas</label>
                            <select class="form-control" name="IdentityType_id" id="IdentityType_id" placeholder="Jenis identitas">
                                            <option value="" disabled selected>Jenis identitas</option>
                                            <?php foreach (get_table('master_jenis_identitas', 'id, Nama', null, 'data') as $row) : ?>
                                                <option value="<?= $row->id ?>" <?= set_select('IdentityType_id', $row->id) ?>><?= $row->Nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
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
                            <input type="date" name="DateOfBirth" id="DateOfBirth" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">Jenis Kelamin</label>
                            <select class="form-select" name="Sex_id" id="Sex_id">
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
                                <select class="form-select" id="Province" name="Province">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kota</label>
                                <select class="form-select" id="City" name="City">
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kecamatan</label>
                                <select class="form-select" id="District" name="Kecamatan">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kelurahan</label>
                                <select class="form-select" id="SubDistrict" name="Kelurahan">
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
                                <select class="form-select" id="ProvinceNow" name="ProvinceNow">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kota</label>
                                <select class="form-select" id="CityNow" name="CityNow">
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kecamatan</label>
                                <select class="form-select" id="DistrictNow" name="KecamatanNow">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label required">Kelurahan</label>
                                <select class="form-select" id="SubDistrictNow" name="KelurahanNow">
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
                        <input class="form-check-input" type="checkbox" id="check_agree" required>
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
                <input type="hidden" value="0" name="IsKeranjang" id="IsKeranjang" class="form-control">
            </form>
        </div>

        <!-- Login Link -->
        <div class="login-link">
            <p class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Sudah memiliki Nomor Anggota? 
                <a href="#" class="text-decoration-none fw-bold">Login Anggota</a>
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js"></script>

    <script>
        // Initialize form
        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Progress tracking
            updateProgress();
        });

        /* Axios - Original API calls */
        const getData = async (url, dom, selected = false) => {
            await axios.get(url).then(res => {
                console.log(res)
                $(dom).html('<option value="">Loading...</option>');
                var output = '<option value="">-Select-</option>';
                $.each(res.data, function(key, val) {
                    output += '<option value="' + val.code + '" data-text="' + val.name + '">' + val.name + '</option>';
                });
                $(dom).html(output);
                if (selected) {
                    $(dom).val(selected);
                }
            }).catch(err => {
                console.log(err)
            });
        }

        $("#check_agree").change((e) => {
          if (!e.target.checked) {
            $(e.target).removeClass("is-valid");
          }
        });

        // Load initial data - replace with your actual base_url
        const baseUrl = '<?php echo base_url(); ?>'; // Replace with actual base URL
        getData(`${baseUrl}/api/region/province`, `#Province`);
        
        $('#Province').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/city/${code}`, `#City`);
        });
        
        $('#City').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/district/${code}`, `#District`);
        });
        
        $('#District').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/sub_district/${code}`, `#SubDistrict`);
        });
        
        // $('#SubDistrict').change(function(e) {
        //     var name = $("#SubDistrict option:selected").text();
        //     $(this).val(name);
        // });

        getData(`${baseUrl}/api/region/province`, `#ProvinceNow`);
        
        $('#ProvinceNow').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/city/${code}`, `#CityNow`);
        });
        
        $('#CityNow').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/district/${code}`, `#DistrictNow`);
        });
        
        $('#DistrictNow').change(function(e) {
            var code = $(this).val();
            getData(`${baseUrl}/api/region/sub_district/${code}`, `#SubDistrictNow`);
        });

        // Copy address functionality - Original
        $("#check_copy").change(function(e) {
            if (this.checked) {
                var Address = $('#Address').val();
                var Province = $('#Province').val();
                var City = $('#City').val();
                var District = $('#District').val();
                var SubDistrict = $('#SubDistrict').val();

                $('#AddressNow').val(Address);
                // $('#ProvinceNow').val(Province);
                getData(`${baseUrl}/api/region/province`, `#ProvinceNow`, Province);
                getData(`${baseUrl}/api/region/city/${Province}`, `#CityNow`, City);
                getData(`${baseUrl}/api/region/district/${City}`, `#DistrictNow`, District);
                getData(`${baseUrl}/api/region/sub_district/${District}`, `#SubDistrictNow`, SubDistrict);
            } else {
                $('#AddressNow').val('');
                // $('#ProvinceNow').val('');
                $('#ProvinceNow').empty();
                $('#CityNow').empty();
                $('#DistrictNow').empty();
                $('#SubDistrictNow').empty();
            }
        });

        // Check button - Original
        $("#btnCheck").click(function() {
            var url = `${baseUrl}/api/member/check`;
            var data_post = 'email=' + $("#Email").val() + '&username=' + $("#IdentityNo").val();

            $("#btnCheck").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
            $("#btnCheck").attr('disabled', true);

            $.ajax({
                    url: url,
                    type: 'POST',
                    data: data_post,
                })
                .done(function(res) {
                    console.log(res)

                    if (res.error == false) {
                        Swal.fire({
                            title: 'Yeay',
                            html: res.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 5000,
                        }).then(() => {
                            $("#btnCheck").attr('disabled', false);
                            $("#btnCheck").html('<i class="fas fa-check me-2"></i>Terverifikasi');
                            $("#btnCheck").removeClass('btn-outline-primary').addClass('btn-success');
                            updateProgress(66);
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
                    console.log(res);

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

        // Form validation - Original with Bootstrap validator
        $("#frm_register").validator().on("submit", function(event) {
            if (event.isDefaultPrevented()) {
                formError();
            } else {
                event.preventDefault();
                submitForm();
            }
        });

        function submitForm() {
            // Initiate Variables With Form Content
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
                    console.log(res)

                    if (res.error == false) {
                        updateProgress(100);
                        
                        Swal.fire({
                            title: 'Berhasil',
                            html: 'Link verifikasi anggota berhasil terkirim.<br>Silakan cek email Anda segera!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 5000,
                        }).then(() => {
                            window.location.href = `${baseUrl}/`;
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
                    console.log(res);

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
        $('input[required], select[required], textarea[required]').on('blur', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });
    </script>
</body>

<?= $this->endSection() ?>