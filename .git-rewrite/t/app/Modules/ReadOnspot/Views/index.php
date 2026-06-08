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
            background: #28a745;
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
            background: #17a2b8;
        }
        .section-title.book-section {
            background: #28a745;
        }
        .section-title.status-section-header {
            background: #28a745;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-6">
                <!-- Member Data Section -->
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
			  <div class="col-lg-6">
                <!-- Book Data Section -->
                <div class="form-section">
                    <div class="section-title book-section">
                        <i class="fas fa-book"></i>
                        Data Buku
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Barcode Buku</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="bookBarcode" 
                                   placeholder="Scan atau ketik barcode buku..." autofocus>
                            <button class="btn btn-success btn-scan" type="button" id="addBookBtn">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <small class="text-muted">Scan barcode buku (otomatis simpan)</small>
                    </div>
                </div>
                </div>

                <!-- Status Section -->
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
                                Masukkan nomor anggota dan scan barcode buku untuk menyimpan otomatis
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
                                    <th>Nama Anggota</th>
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
        $(document).ready(function() {
            let currentMember = null;

            // Load initial data
            loadTodayData();

            // Search member by number
            $('#memberNumber').on('input', function() {
                const memberNumber = $(this).val().trim();
                if (memberNumber.length >= 3) {
                    searchMember(memberNumber);
                } else {
                    $('#memberInfo').hide();
                    currentMember = null;
                }
            });

            // Add book by barcode
            $('#bookBarcode').on('input', function() {
                const barcode = $(this).val().trim();
                if (barcode && currentMember) {
                    // Auto-submit when barcode is entered
                    setTimeout(() => {
                        addBookByBarcode(barcode);
                    }, 500);
                }
            });

            // Manual search member button
            $('#searchMemberBtn').click(function() {
                const memberNumber = $('#memberNumber').val().trim();
                if (memberNumber) {
                    searchMember(memberNumber);
                }
            });

            // Manual add book button
            $('#addBookBtn').click(function() {
                const barcode = $('#bookBarcode').val().trim();
                if (barcode && currentMember) {
                    addBookByBarcode(barcode);
                }
            });

            // Reset form
            $('#resetFormBtn').click(function() {
                resetForm();
            });

            // Refresh data
            $('#refreshDataBtn').click(function() {
                loadTodayData();
            });

            // Auto refresh every 30 seconds
            setInterval(loadTodayData, 30000);

            function searchMember(memberNumber) {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/addByMemberNumber') ?>',
                    method: 'POST',
                    data: { member_number: memberNumber },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            currentMember = response.data;
                            $('#memberName').text(response.data.Fullname || 'Tidak ada nama');
                            $('#memberNum').text(response.data.MemberNo);
                            $('#memberInfo').show();
                            updateStatus('Member ditemukan: ' + response.data.Fullname, 'success');
                        } else {
                            $('#memberInfo').hide();
                            currentMember = null;
                            updateStatus(response.message, 'error');
                        }
                    },
                    error: function() {
                        updateStatus('Terjadi kesalahan saat mencari member', 'error');
                    }
                });
            }

            function addBookByBarcode(barcode) {
                if (!currentMember) {
                    updateStatus('Silakan masukkan nomor anggota terlebih dahulu', 'error');
                    return;
                }

                $.ajax({
                    url: '<?= base_url('baca-ditempat/addByBarcode') ?>',
                    method: 'POST',
                    data: { 
                        barcode: barcode,
                        member_number: currentMember.MemberNo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            updateStatus('Data berhasil disimpan!', 'success');
                            $('#bookBarcode').val('');
                            loadTodayData();
                        } else {
                            updateStatus(response.message, 'error');
                        }
                    },
                    error: function() {
                        updateStatus('Terjadi kesalahan saat menyimpan data', 'error');
                    }
                });
            }

            function loadTodayData() {
                $.ajax({
                    url: '<?= base_url('baca-ditempat/getTodayData') ?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            renderDataTable(response.data);
                        }
                    },
                    error: function() {
                        console.log('Error loading today data');
                    }
                });
            }

            function renderDataTable(data) {
                const tbody = $('#dataTableBody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <div>Belum ada data baca ditempat hari ini</div>
                            </td>
                        </tr>
                    `);
                } else {
                    data.forEach((item, index) => {
                        const time = new Date(item.CreateDate).toLocaleTimeString('id-ID');
                        const status = item.Is_return === '1' ? 
                            '<span class="badge bg-success">Dikembalikan</span>' : 
                            '<span class="badge bg-primary">Sedang Baca</span>';

                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${time}</td>
                                <td>${item.NoAnggota || '-'}</td>
                                <td>${item.NamaAnggota || '-'}</td>
                                <td>${item.Barcode || '-'}</td>
                                <td>${item.JudulBuku || '-'}</td>
                                <td>${status}</td>
                            </tr>
                        `);
                    });
                }
            }

            function updateStatus(message, type) {
                const statusEl = $('#statusMessage');
                statusEl.removeClass('text-info text-success text-danger');
                
                if (type === 'success') {
                    statusEl.addClass('text-success');
                } else if (type === 'error') {
                    statusEl.addClass('text-danger');
                } else {
                    statusEl.addClass('text-info');
                }
                
                statusEl.text(message);
            }

            function resetForm() {
                $('#memberNumber').val('');
                $('#bookBarcode').val('');
                $('#memberInfo').hide();
                currentMember = null;
                updateStatus('Masukkan nomor anggota dan scan barcode buku untuk menyimpan otomatis', 'info');
            }
        });
    </script>
<?= $this->endSection() ?>