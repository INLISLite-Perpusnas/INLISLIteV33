<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/ina-guestbook.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-guestbook.css') ?>">
<style>
	/* Baca di Tempat - gaya INA Digital (token & komponen dasar dari ina-guestbook.css) */
	.rs-page {
		min-height: 100vh;
		padding: 110px 0 40px;
	}

	.rs-wrap {
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
	}

	.rs-title {
		font-size: 28px;
		line-height: 36px;
		font-weight: 700;
		color: var(--gb-text);
		margin: 0 0 4px;
	}

	.rs-subtitle {
		font-size: 16px;
		line-height: 24px;
		color: var(--gb-text-muted);
		margin: 0 0 24px;
	}

	.gb-ina .nav-tabs-custom {
		display: flex;
	}

	.gb-ina .nav-tab {
		flex: 1;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		cursor: pointer;
	}

	.rs-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 24px;
		margin-bottom: 24px;
	}

	.rs-card {
		background: var(--gb-surface);
		border: 1px solid var(--gb-border);
		border-radius: var(--gb-radius);
		box-shadow: var(--gb-shadow);
		padding: 24px;
	}

	.rs-card-title {
		display: flex;
		align-items: center;
		gap: 10px;
		font-size: 18px;
		line-height: 26px;
		font-weight: 600;
		color: var(--gb-text);
		padding-bottom: 12px;
		margin-bottom: 20px;
		border-bottom: 1px solid var(--gb-border);
	}

	.rs-card-title i {
		color: var(--gb-primary);
	}

	.rs-field {
		display: flex;
		gap: 8px;
	}

	.rs-field .form-control {
		flex: 1;
		min-width: 0;
	}

	.rs-icon-btn {
		flex-shrink: 0;
		width: 44px;
		min-height: 44px;
		padding: 0;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		color: var(--gb-primary);
		background: var(--gb-surface);
		border: 1px solid var(--gb-primary);
		border-radius: var(--gb-radius-sm);
	}

	.rs-icon-btn:hover {
		background: var(--gb-primary-subtle);
	}

	.rs-icon-btn:focus-visible {
		outline: none;
		box-shadow: var(--gb-focus);
	}

	.rs-help {
		display: block;
		margin-top: 6px;
		font-size: 12px;
		line-height: 16px;
		color: var(--gb-text-muted);
	}

	.rs-info {
		margin-top: 16px;
		padding: 12px 16px;
		font-size: 14px;
		line-height: 20px;
		color: var(--gb-text-subtle);
		background: var(--gb-primary-subtle);
		border: 1px solid #cfdeee;
		border-radius: var(--gb-radius-sm);
	}

	/* Status penyimpanan: alert INA (info / sukses / error) */
	.rs-status {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 16px;
		flex-wrap: wrap;
		margin-bottom: 24px;
	}

	.rs-status-msg {
		flex: 1;
		display: flex;
		align-items: center;
		gap: 12px;
		min-width: 240px;
		padding: 12px 16px;
		font-size: 14px;
		line-height: 20px;
		border: 1px solid #cfdeee;
		border-radius: var(--gb-radius-sm);
		background: var(--gb-primary-subtle);
		color: var(--gb-primary);
	}

	.rs-status-msg.is-success {
		background: var(--gb-success-subtle);
		border-color: #a7d9b3;
		color: var(--gb-success);
	}

	.rs-status-msg.is-error {
		background: var(--gb-danger-subtle);
		border-color: #f3b4b4;
		color: var(--gb-danger);
	}

	.rs-outline-btn {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		min-height: 44px;
		padding: 10px 20px;
		font-size: 16px;
		line-height: 24px;
		font-weight: 600;
		color: var(--gb-primary);
		background: var(--gb-surface);
		border: 1px solid var(--gb-primary);
		border-radius: var(--gb-radius-sm);
	}

	.rs-outline-btn:hover {
		background: var(--gb-primary-subtle);
	}

	.rs-outline-btn.is-sm {
		min-height: 36px;
		padding: 6px 14px;
		font-size: 14px;
		line-height: 20px;
	}

	/* Tabel INA */
	.rs-table-card {
		padding: 0;
		overflow: hidden;
	}

	.rs-table-head {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		padding: 16px 24px;
		border-bottom: 1px solid var(--gb-border);
	}

	.rs-table-head h2 {
		margin: 0;
		font-size: 18px;
		line-height: 26px;
		font-weight: 600;
		color: var(--gb-text);
	}

	.rs-table {
		width: 100%;
		margin: 0;
		border-collapse: collapse;
		font-size: 14px;
		line-height: 20px;
	}

	.rs-table th {
		padding: 12px 16px;
		text-align: left;
		font-weight: 600;
		color: var(--gb-text-subtle);
		background: #f9fafb;
		border-bottom: 1px solid var(--gb-border);
		white-space: nowrap;
	}

	.rs-table td {
		padding: 12px 16px;
		color: var(--gb-text);
		border-bottom: 1px solid var(--gb-border);
		vertical-align: top;
	}

	.rs-table tbody tr:hover td {
		background: #f9fafb;
	}

	.rs-table .num {
		width: 56px;
		text-align: right;
		font-variant-numeric: tabular-nums;
	}

	.rs-table .empty-state {
		padding: 40px 20px;
		text-align: center;
		color: var(--gb-text-muted);
	}

	.rs-table .empty-state i {
		display: block;
		font-size: 32px;
		margin-bottom: 12px;
		color: #9ca3af;
	}

	.rs-badge {
		display: inline-block;
		padding: 2px 10px;
		font-size: 12px;
		line-height: 16px;
		font-weight: 600;
		border-radius: 999px;
		white-space: nowrap;
	}

	.rs-badge.is-info { background: var(--gb-primary-subtle); color: var(--gb-primary); }
	.rs-badge.is-warning { background: var(--gb-warning-subtle); color: var(--gb-warning); }
	.rs-badge.is-success { background: var(--gb-success-subtle); color: var(--gb-success); }

	@media (max-width: 768px) {
		.rs-grid {
			grid-template-columns: 1fr;
		}

		.rs-card,
		.rs-table-head {
			padding: 20px;
		}

		.rs-title {
			font-size: 24px;
			line-height: 32px;
		}
	}
</style>

<div class="rs-page page-container gb-ina">
	<div class="rs-wrap">
		<h1 class="rs-title">Baca di Tempat</h1>
		<p class="rs-subtitle">Catat koleksi yang dibaca pengunjung di ruang perpustakaan.</p>

		<!-- Tab tipe pengunjung -->
		<div class="nav-tabs-custom" role="tablist" aria-label="Tipe pengunjung">
			<button type="button" class="nav-tab active" id="tabAnggota" role="tab" aria-selected="true" aria-controls="formAnggota" onclick="switchTab('anggota')">
				<i class="fas fa-id-card" aria-hidden="true"></i> Anggota
			</button>
			<button type="button" class="nav-tab" id="tabNonAnggota" role="tab" aria-selected="false" aria-controls="formNonAnggota" onclick="switchTab('non-anggota')">
				<i class="fas fa-user" aria-hidden="true"></i> Non Anggota
			</button>
		</div>

		<!-- ===== FORM ANGGOTA ===== -->
		<div id="formAnggota" class="rs-grid" role="tabpanel" aria-labelledby="tabAnggota">
			<div class="rs-card">
				<div class="rs-card-title"><i class="fas fa-users" aria-hidden="true"></i> Data Anggota</div>
				<label for="memberNumber">Nomor Anggota</label>
				<div class="rs-field">
					<input type="text" class="form-control" id="memberNumber" placeholder="Masukkan nomor anggota..." autocomplete="off" autofocus>
					<button class="rs-icon-btn" type="button" id="searchMemberBtn" aria-label="Cari anggota">
						<i class="fas fa-search" aria-hidden="true"></i>
					</button>
				</div>
				<small class="rs-help">Nama anggota dicari otomatis setelah 3 karakter.</small>
				<div id="memberInfo" class="rs-info" style="display: none;">
					<strong>Nama Anggota:</strong> <span id="memberName"></span><br>
					<strong>No. Anggota:</strong> <span id="memberNum"></span>
				</div>
			</div>
			<div class="rs-card">
				<div class="rs-card-title"><i class="fas fa-book" aria-hidden="true"></i> Data Buku</div>
				<label for="bookBarcode">Nomor Barcode Buku</label>
				<div class="rs-field">
					<input type="text" class="form-control" id="bookBarcode" placeholder="Scan atau ketik barcode buku..." autocomplete="off">
					<button class="rs-icon-btn" type="button" id="addBookBtn" aria-label="Simpan barcode buku">
						<i class="fas fa-barcode" aria-hidden="true"></i>
					</button>
				</div>
				<small class="rs-help">Data tersimpan otomatis setelah barcode dipindai.</small>
			</div>
		</div>

		<!-- ===== FORM NON ANGGOTA ===== -->
		<div id="formNonAnggota" class="rs-grid" role="tabpanel" aria-labelledby="tabNonAnggota" style="display:none;">
			<div class="rs-card">
				<div class="rs-card-title"><i class="fas fa-user" aria-hidden="true"></i> Data Pengunjung</div>
				<label for="namaNonAnggota">Nama Lengkap</label>
				<input type="text" class="form-control" id="namaNonAnggota" placeholder="Masukkan nama lengkap..." autocomplete="off" maxlength="255">
				<small class="rs-help">Ketik nama pengunjung secara manual.</small>
				<div id="nonAnggotaInfo" class="rs-info" style="display:none;">
					<strong>Nama:</strong> <span id="nonAnggotaNamaDisplay"></span>
				</div>
			</div>
			<div class="rs-card">
				<div class="rs-card-title"><i class="fas fa-book" aria-hidden="true"></i> Data Buku</div>
				<label for="bookBarcodeNon">Nomor Barcode Buku</label>
				<div class="rs-field">
					<input type="text" class="form-control" id="bookBarcodeNon" placeholder="Scan atau ketik barcode buku..." autocomplete="off">
					<button class="rs-icon-btn" type="button" id="addBookBtnNon" aria-label="Simpan barcode buku">
						<i class="fas fa-barcode" aria-hidden="true"></i>
					</button>
				</div>
				<small class="rs-help">Data tersimpan otomatis setelah barcode dipindai.</small>
			</div>
		</div>

		<!-- Status penyimpanan (bersama) -->
		<div class="rs-status">
			<div class="rs-status-msg" id="statusMessage" role="status" aria-live="polite">
				Pilih tipe pengunjung, isi data, lalu scan barcode buku
			</div>
			<button class="rs-outline-btn" type="button" id="resetFormBtn">
				<i class="fas fa-redo" aria-hidden="true"></i> Reset Form
			</button>
		</div>

		<!-- Data hari ini -->
		<div class="rs-card rs-table-card">
			<div class="rs-table-head">
				<h2>Data Baca di Tempat Hari Ini</h2>
				<button class="rs-outline-btn is-sm" type="button" id="refreshDataBtn">
					<i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
				</button>
			</div>
			<div class="table-responsive">
				<table class="rs-table">
					<thead>
						<tr>
							<th class="num">No</th>
							<th>Judul Buku</th>
							<th>Nama</th>
							<th>No. Anggota</th>
							<th>Tipe</th>
							<th>Barcode</th>
							<th>Waktu</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody id="dataTableBody">
						<tr>
							<td colspan="8" class="empty-state">
								<i class="fas fa-inbox" aria-hidden="true"></i>
								Belum ada data baca di tempat hari ini
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
        let currentMember  = null;
        let currentTab     = 'anggota';
        let barcodeTimer   = null;

        function switchTab(tab) {
            currentTab = tab;
            const anggota = tab === 'anggota';
            $('#formAnggota').toggle(anggota);
            $('#formNonAnggota').toggle(!anggota);
            $('#tabAnggota').toggleClass('active', anggota).attr('aria-selected', anggota);
            $('#tabNonAnggota').toggleClass('active', !anggota).attr('aria-selected', !anggota);
            resetForm();
            setTimeout(() => $(anggota ? '#memberNumber' : '#namaNonAnggota').focus(), 100);
        }

        // Data tabel berasal dari input pengunjung (mis. nama non anggota): selalu escape sebelum dirender
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
        }

        function updateStatus(message, type) {
            $('#statusMessage')
                .removeClass('is-success is-error')
                .addClass(type === 'success' ? 'is-success' : type === 'error' ? 'is-error' : '')
                .text(message);
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
                        <i class="fas fa-inbox" aria-hidden="true"></i>
                        Belum ada data baca di tempat hari ini
                    </td></tr>`);
                    return;
                }

                data.forEach((item, index) => {
                    const time      = new Date(item.CreateDate).toLocaleTimeString('id-ID');
                    const status    = item.Is_return == '1'
                        ? '<span class="rs-badge is-success">Dikembalikan</span>'
                        : '<span class="rs-badge is-info">Sedang Baca</span>';
                    const tipeBadge = item.IsNonAnggota == 1
                        ? '<span class="rs-badge is-warning">Non Anggota</span>'
                        : '<span class="rs-badge is-info">Anggota</span>';

                    tbody.append(`
                        <tr>
                            <td class="num">${index + 1}</td>
                            <td>${escapeHtml(item.JudulBuku || '-')}</td>
                            <td>${escapeHtml(item.NamaAnggota || '-')}</td>
                            <td>${escapeHtml(item.NoAnggota || '-')}</td>
                            <td>${tipeBadge}</td>
                            <td>${escapeHtml(item.Barcode || '-')}</td>
                            <td>${escapeHtml(time)}</td>
                            <td>${status}</td>
                        </tr>
                    `);
                });
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
            updateStatus(currentTab === 'anggota'
                ? 'Masukkan nomor anggota, lalu scan barcode buku'
                : 'Masukkan nama pengunjung, lalu scan barcode buku', 'info');
        }
    </script>
<?= $this->endSection('script') ?>
