<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

 <style>
        .card-header {
            font-weight: 600;
        }
        .status-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-icon {
            background: #6c757d;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-header {
            background: #495057;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-scan {
            background: #1b3878;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-title {
            color: white;
            font-weight: 600;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title.member-section {
            background: #1b3878;
        }
        .section-title.book-section {
            background: #1b3878;
        }
        .section-title.status-section-header {
            background: #1b3878;
        }
    </style>

    <div class="container-fluid py-4" style="padding-top: 100px !important; padding-bottom: 40px !important;">
        <div class="row">

            <!-- Tab Toggle -->
            <div class="col-12 mb-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="tabAnggota" onclick="switchTab('anggota')">
                        <i class="fas fa-id-card"></i> Anggota
                    </button>
                    <button class="btn btn-outline-secondary" id="tabNonAnggota" onclick="switchTab('non-anggota')">
                        <i class="fas fa-user"></i> Non Anggota
                    </button>
                </div>
            </div>

            <!-- ===== FORM ANGGOTA ===== -->
            <div id="formAnggota">
                <div class="col-lg-6" style="display:inline-block; vertical-align:top; width:49%">
                    <div class="form-section">
                        <div class="section-title member-section">
                            <i class="fas fa-users"></i>
                            Data Anggota
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Anggota</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="memberNumber"
                                       placeholder="Masukkan nomor anggota..." autofocus>
                                <button class="btn btn-outline-secondary" type="button" id="searchMemberBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">Masukkan nomor anggota (otomatis cari nama)</small>
                        </div>
                        <div id="memberInfo" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Nama Anggota:</strong> <span id="memberName"></span><br>
                                <strong>No. Anggota:</strong> <span id="memberNum"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="display:inline-block; vertical-align:top; width:49%">
                    <div class="form-section">
                        <div class="section-title book-section">
                            <i class="fas fa-book"></i>
                            Data Buku
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Barcode Buku</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="bookBarcode"
                                       placeholder="Scan atau ketik barcode buku...">
                                <button class="btn btn-success btn-scan" type="button" id="addBookBtn">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <small class="text-muted">Scan barcode buku (otomatis simpan)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== FORM NON ANGGOTA ===== -->
            <div id="formNonAnggota" style="display:none;">
                <div class="col-lg-6" style="display:inline-block; vertical-align:top; width:49%">
                    <div class="form-section">
                        <div class="section-title member-section">
                            <i class="fas fa-user"></i>
                            Data Pengunjung
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="namaNonAnggota"
                                   placeholder="Masukkan nama lengkap...">
                            <small class="text-muted">Ketik nama pengunjung secara manual</small>
                        </div>
                        <div id="nonAnggotaInfo" class="mt-3" style="display:none;">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Nama: <strong><span id="nonAnggotaNamaDisplay"></span></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="display:inline-block; vertical-align:top; width:49%">
                    <div class="form-section">
                        <div class="section-title book-section">
                            <i class="fas fa-book"></i>
                            Data Buku
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Barcode Buku</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="bookBarcodeNon"
                                       placeholder="Scan atau ketik barcode buku...">
                                <button class="btn btn-success btn-scan" type="button" id="addBookBtnNon">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <small class="text-muted">Scan barcode buku (otomatis simpan)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Section (shared) -->
            <div class="col-12">
                <div class="form-section">
                    <div class="section-title status-section-header">
                        <i class="fas fa-check-circle"></i>
                        Status Penyimpanan
                    </div>
                    <div class="status-section">
                        <div class="info-icon">
                            <i class="fas fa-info"></i>
                        </div>
                        <div class="text-center mt-3">
                            <div class="text-info" id="statusMessage">
                                Pilih tipe pengunjung, isi data, lalu scan barcode buku
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-secondary" id="resetFormBtn">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="col-lg-12 mt-4">
                <div class="table-container">
                    <div class="table-header">
                        <div>
                            <i class="fas fa-clock"></i>
                            Data Baca Ditempat Hari Ini
                        </div>
                        <button class="btn btn-light btn-sm" id="refreshDataBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Waktu</th>
                                    <th>No. Anggota</th>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Barcode</th>
                                    <th>Judul Buku</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dataTableBody">
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <div>Belum ada data baca ditempat hari ini</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        let currentMember  = null;
        let currentTab     = 'anggota';
        let barcodeTimer   = null;

        function switchTab(tab) {
            currentTab = tab;
            if (tab === 'anggota') {
                $('#formAnggota').show();
                $('#formNonAnggota').hide();
                $('#tabAnggota').removeClass('btn-outline-secondary').addClass('btn-primary');
                $('#tabNonAnggota').removeClass('btn-primary').addClass('btn-outline-secondary');
                resetForm();
                setTimeout(() => $('#memberNumber').focus(), 100);
            } else {
                $('#formAnggota').hide();
                $('#formNonAnggota').show();
                $('#tabNonAnggota').removeClass('btn-outline-secondary').addClass('btn-primary');
                $('#tabAnggota').removeClass('btn-primary').addClass('btn-outline-secondary');
                resetForm();
                setTimeout(() => $('#namaNonAnggota').focus(), 100);
            }
        }

        $(document).ready(function() {

            loadTodayData();

            // ===== ANGGOTA =====
            $('#memberNumber').on('input', function() {
                const val = $(this).val().trim();
                if (val.length >= 3) searchMember(val);
                else { $('#memberInfo').hide(); currentMember = null; }
            });

            $('#searchMemberBtn').click(function() {
                const val = $('#memberNumber').val().trim();
                if (val) searchMember(val);
            });

            $('#bookBarcode').on('input', function() {
                clearTimeout(barcodeTimer);
                const barcode = $(this).val().trim();
                if (barcode && currentMember) {
                    barcodeTimer = setTimeout(() => addBookByBarcode(barcode), 500);
                }
            });

            $('#addBookBtn').click(function() {
                const barcode = $('#bookBarcode').val().trim();
                if (barcode && currentMember) addBookByBarcode(barcode);
                else if (!currentMember) updateStatus('Masukkan nomor anggota terlebih dahulu', 'error');
            });

            // ===== NON ANGGOTA =====
            $('#namaNonAnggota').on('input', function() {
                const nama = $(this).val().trim();
                if (nama.length >= 2) {
                    $('#nonAnggotaNamaDisplay').text(nama);
                    $('#nonAnggotaInfo').show();
                } else {
                    $('#nonAnggotaInfo').hide();
                }
            });

            $('#bookBarcodeNon').on('input', function() {
                clearTimeout(barcodeTimer);
                const barcode = $(this).val().trim();
                const nama    = $('#namaNonAnggota').val().trim();
                if (barcode && nama) {
                    barcodeTimer = setTimeout(() => addBookByBarcodeNonMember(barcode, nama), 500);
                }
            });

            $('#addBookBtnNon').click(function() {
                const barcode = $('#bookBarcodeNon').val().trim();
                const nama    = $('#namaNonAnggota').val().trim();
                if (!nama)   { updateStatus('Masukkan nama pengunjung terlebih dahulu', 'error'); return; }
                if (!barcode){ updateStatus('Masukkan barcode buku', 'error'); return; }
                addBookByBarcodeNonMember(barcode, nama);
            });

            // ===== SHARED =====
            $('#resetFormBtn').click(resetForm);
            $('#refreshDataBtn').click(loadTodayData);
            setInterval(loadTodayData, 30000);

            // ===========================
            function searchMember(memberNumber) {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/addByMemberNumber') ?>',
                    method: 'POST',
                    data: { member_number: memberNumber },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            currentMember = res.data;
                            $('#memberName').text(res.data.Fullname || '-');
                            $('#memberNum').text(res.data.MemberNo);
                            $('#memberInfo').show();
                            updateStatus('Anggota ditemukan: ' + res.data.Fullname, 'success');
                        } else {
                            $('#memberInfo').hide();
                            currentMember = null;
                            updateStatus(res.message, 'error');
                        }
                    },
                    error: function() { updateStatus('Kesalahan saat mencari anggota', 'error'); }
                });
            }

            function addBookByBarcode(barcode) {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/addByBarcode') ?>',
                    method: 'POST',
                    data: { barcode: barcode, member_number: currentMember.MemberNo },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            updateStatus('Data berhasil disimpan!', 'success');
                            $('#bookBarcode').val('');
                            loadTodayData();
                        } else {
                            updateStatus(res.message, 'error');
                            $('#bookBarcode').val('').focus();
                        }
                    },
                    error: function() { updateStatus('Kesalahan saat menyimpan data', 'error'); }
                });
            }

            function addBookByBarcodeNonMember(barcode, nama) {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/addByBarcodeNonMember') ?>',
                    method: 'POST',
                    data: { barcode: barcode, nama: nama },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            updateStatus('Data berhasil disimpan!', 'success');
                            $('#bookBarcodeNon').val('').focus();
                            loadTodayData();
                        } else {
                            updateStatus(res.message, 'error');
                            $('#bookBarcodeNon').val('').focus();
                        }
                    },
                    error: function() { updateStatus('Kesalahan saat menyimpan data', 'error'); }
                });
            }

            function loadTodayData() {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/getTodayData') ?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') renderDataTable(res.data);
                    }
                });
            }

            function renderDataTable(data) {
                const tbody = $('#dataTableBody');
                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.append(`<tr><td colspan="8" class="empty-state">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <div>Belum ada data baca ditempat hari ini</div>
                    </td></tr>`);
                    return;
                }

                data.forEach((item, index) => {
                    const time      = new Date(item.CreateDate).toLocaleTimeString('id-ID');
                    const status    = item.Is_return == '1'
                        ? '<span class="badge bg-success">Dikembalikan</span>'
                        : '<span class="badge bg-primary">Sedang Baca</span>';
                    const tipeBadge = item.IsNonAnggota == 1
                        ? '<span class="badge bg-warning text-dark">Non Anggota</span>'
                        : '<span class="badge bg-info">Anggota</span>';

                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${time}</td>
                            <td>${item.NoAnggota || '-'}</td>
                            <td>${item.NamaAnggota || '-'}</td>
                            <td>${tipeBadge}</td>
                            <td>${item.Barcode || '-'}</td>
                            <td>${item.JudulBuku || '-'}</td>
                            <td>${status}</td>
                        </tr>
                    `);
                });
            }

            function updateStatus(message, type) {
                const el = $('#statusMessage');
                el.removeClass('text-info text-success text-danger');
                el.addClass(type === 'success' ? 'text-success' : type === 'error' ? 'text-danger' : 'text-info');
                el.text(message);
            }
        });

        function resetForm() {
            currentMember = null;
            $('#memberNumber').val('');
            $('#bookBarcode').val('');
            $('#memberInfo').hide();
            $('#namaNonAnggota').val('');
            $('#bookBarcodeNon').val('');
            $('#nonAnggotaInfo').hide();
            const msg = currentTab === 'anggota'
                ? 'Masukkan nomor anggota, lalu scan barcode buku'
                : 'Masukkan nama pengunjung, lalu scan barcode buku';
            const el = $('#statusMessage');
            el.removeClass('text-success text-danger').addClass('text-info').text(msg);
        }
    </script>
<?= $this->endSection() ?>