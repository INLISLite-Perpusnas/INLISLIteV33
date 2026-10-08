<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/vendors/flatpickr/flatpickr.min.css') ?>" />
<style>
    .select2-container {
        width: 100% !important;
    }

    /* Input jam tetap putih meski readonly (dari flatpickr) */
    .jam-picker.form-control[readonly],
    .jam-picker.flatpickr-input {
        background-color: #fff;
        cursor: pointer;
    }

    .jam-picker.form-control[readonly]:focus,
    .jam-picker.flatpickr-input.active {
        background-color: #fff;
        border-color: #80bdff;
        box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
    }

    /* Hilangkan abu-abu pada picker jam/menit */
    .flatpickr-time input:hover,
    .flatpickr-time input:focus,
    .flatpickr-time .flatpickr-am-pm:hover,
    .flatpickr-time .flatpickr-am-pm:focus,
    .flatpickr-time .numInputWrapper:hover {
        background: #fff;
    }

    .flatpickr-time input::selection {
        background: transparent;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-date icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Kelola Jadwal Perpustakaan Keliling
                    <div class="page-title-subheading">Kelola agenda kunjungan unit perpustakaan keliling</div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" class="btn btn-success" id="btn-tambah">
                    <i class="fa fa-plus"></i> Tambah Jadwal
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="main-card mb-3 card">
        <div class="card-body">
            <form id="formFilter" class="form-row">
                <div class="form-group col-md-3">
                    <label>Rentang Tanggal</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="dari" id="filter_dari">
                        <div class="input-group-append"><span class="input-group-text">s/d</span></div>
                        <input type="date" class="form-control" name="sampai" id="filter_sampai">
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <label>Unit</label>
                    <select class="form-control" name="unit_id" id="filter_unit_id">
                        <option value="">-- Semua Unit --</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['nama_unit']) ?> (<?= esc($u['nomor_kendaraan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Lokasi</label>
                    <select class="form-control" name="lokasi_id" id="filter_lokasi_id">
                        <option value="">-- Semua Lokasi --</option>
                        <?php foreach ($lokasis as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= esc($l['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Petugas</label>
                    <select class="form-control" name="petugas_id" id="filter_petugas_id">
                        <option value="">-- Semua Petugas --</option>
                        <?php foreach ($petugas as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3 align-self-end">
                    <button type="button" class="btn btn-primary" id="btn-terapkan-filter"><i class="fa fa-filter"></i>
                        Terapkan</button>
                    <button type="button" class="btn btn-secondary" id="btn-reset-filter"><i class="fa fa-sync"></i>
                        Reset</button>
                    <button type="button" class="btn btn-success" id="btn-export-excel"><i class="fa fa-file-excel"></i>
                        Export Excel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Datatable -->
    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="tbl_jadwal" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Unit</th>
                        <th>Lokasi</th>
                        <th>Petugas</th>
                        <th width="110">Status</th>
                        <th width="90">Dokumentasi</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Jadwal -->
<div class="modal fade" id="modalJadwal" tabindex="-1" role="dialog" aria-labelledby="modalJadwalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formJadwal">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="jadwal_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalJadwalLabel">Form Jadwal</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="unit_id">Unit Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-control" name="unit_id" id="unit_id" required>
                                <option value="">-- Pilih Unit --</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= esc($u['nama_unit']) ?>
                                        (<?= esc($u['nomor_kendaraan']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lokasi_id">Lokasi Kunjungan <span class="text-danger">*</span></label>
                            <select class="form-control" name="lokasi_id" id="lokasi_id" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach ($lokasis as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= esc($l['nama_lokasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="petugas_ids">Petugas Pendamping <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="petugas_ids[]" id="petugas_ids" multiple required>
                            <?php foreach ($petugas as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="tanggal">Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required
                                min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control jam-picker" name="jam_mulai" id="jam_mulai"
                                placeholder="HH:MM" autocomplete="off" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control jam-picker" name="jam_selesai" id="jam_selesai"
                                placeholder="HH:MM" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan / Catatan Kegiatan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Batalkan Jadwal -->
<div class="modal fade" id="modalBatalkan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formBatalkan">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="batal_jadwal_id">
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Jadwal</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alasan_batal">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="alasan_batal" id="alasan_batal" rows="3" required
                            placeholder="Sebutkan kendala / alasan pembatalan (min 5 karakter)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Batalkan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kelola Dokumentasi -->
<div class="modal fade" id="modalDokumentasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentasi Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUploadDokumentasi" class="mb-3" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="jadwal_id" id="dok_jadwal_id">
                    <div class="form-row align-items-center">
                        <div class="col-md-5">
                            <input type="file" class="form-control-file" name="file[]" multiple
                                accept="image/jpeg,image/png,image/webp" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="keterangan"
                                placeholder="Caption/Keterangan singkat foto...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-upload"></i>
                                Upload</button>
                        </div>
                    </div>
                </form>
                <hr>
                <div id="container-dokumentasi-list" class="row"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('assets/vendors/flatpickr/flatpickr.min.js') ?>"></script>
<script>
    var tableJadwal;

    $(document).ready(function () {
        $('.select2').select2({ dropdownParent: $('#modalJadwal') });
        // Time picker per 10 menit, langsung muncul saat input diklik
        var jamPickers = flatpickr('.jam-picker', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 10,
            allowInput: false,
            static: true,
            defaultHour: 8,      // ganti sesuai jam yang paling sering dipakai
            defaultMinute: 0,
            onOpen: function (selectedDates, dateStr, instance) {
                // lepas fokus/blok otomatis di kolom jam
                setTimeout(function () {
                    instance.hourElement.blur();
                    window.getSelection().removeAllRanges();
                }, 60);
            }
        });

        function setJam(selector, value) {
            var fp = document.querySelector(selector)._flatpickr;
            if (!value) { fp.clear(); return; }
            fp.setDate(value.substring(0, 5), true); // potong "08:00:00" jadi "08:00"
        }
        tableJadwal = $('#tbl_jadwal').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('cms/perpustakaan-keliling/jadwal/data') ?>',
                type: 'POST',
                data: function (d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                    d.dari = $('#filter_dari').val();
                    d.sampai = $('#filter_sampai').val();
                    d.unit_id = $('#filter_unit_id').val();
                    d.lokasi_id = $('#filter_lokasi_id').val();
                    d.petugas_id = $('#filter_petugas_id').val();
                }
            },
            columns: [
                { data: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false },
                { data: 'tanggal' },
                { data: 'jam' },
                { data: 'unit' },
                { data: 'nama_lokasi' },
                { data: 'daftar_petugas' },
                { data: 'status_tampil', className: 'text-center' },
                { data: 'total_dokumentasi', className: 'text-center' },
                { data: 'action', orderable: false, className: 'text-center' }
            ]
        });

        $('#btn-terapkan-filter').click(function () { tableJadwal.ajax.reload(); });
        $('#btn-reset-filter').click(function () { $('#formFilter')[0].reset(); tableJadwal.ajax.reload(); });

        $('#btn-export-excel').click(function () {
            var params = $('#formFilter').serialize();
            window.location.href = '<?= site_url('cms/perpustakaan-keliling/jadwal/export') ?>?' + params;
        });

        $('#btn-tambah').click(function () {
            $('#formJadwal')[0].reset();
            setJam('#jam_mulai', '');
            setJam('#jam_selesai', '');
            $('#jadwal_id').val('');
            $('#petugas_ids').val(null).trigger('change');
            $('#modalJadwalLabel').text('Tambah Jadwal Baru');
            $('#modalJadwal').appendTo('body').modal('show');
        });

        // Edit
        $('#tbl_jadwal').on('click', '.btn-edit', function () {
            var rowData = tableJadwal.row($(this).closest('tr')).data();
            if (rowData) {
                $('#jadwal_id').val(rowData.id);
                $('#unit_id').val(rowData.unit_id);
                $('#lokasi_id').val(rowData.lokasi_id);
                $('#tanggal').val(rowData.raw_tanggal || rowData.tanggal);
                setJam('#jam_mulai', rowData.jam_mulai);
                setJam('#jam_selesai', rowData.jam_selesai);
                $('#keterangan').val(rowData.raw_keterangan || rowData.keterangan || '');

                // Ambil daftar id petugas via AJAX detail
                $.get('<?= site_url('cms/perpustakaan-keliling/jadwal/detail/') ?>' + rowData.id, function (res) {
                    if (res.status && res.petugas) {
                        var pIds = res.petugas.map(function (p) { return p.id; });
                        $('#petugas_ids').val(pIds).trigger('change');
                    }
                });

                $('#modalJadwalLabel').text('Edit Jadwal Kunjungan');
                $('#modalJadwal').appendTo('body').modal('show');
            }
        });

        // Duplikat
        $('#tbl_jadwal').on('click', '.btn-duplikat', function () {
            var rowData = tableJadwal.row($(this).closest('tr')).data();
            if (rowData) {
                $('#formJadwal')[0].reset();
                $('#jadwal_id').val('');
                $('#unit_id').val(rowData.unit_id);
                $('#lokasi_id').val(rowData.lokasi_id);
                $('#tanggal').val(''); // Tanggal dikosongkan saat duplikat
                setJam('#jam_mulai', rowData.jam_mulai);
                setJam('#jam_selesai', rowData.jam_selesai);
                $('#keterangan').val(rowData.raw_keterangan || rowData.keterangan || '');

                $.get('<?= site_url('cms/perpustakaan-keliling/jadwal/detail/') ?>' + rowData.id, function (res) {
                    if (res.status && res.petugas) {
                        var pIds = res.petugas.map(function (p) { return p.id; });
                        $('#petugas_ids').val(pIds).trigger('change');
                    }
                });

                $('#modalJadwalLabel').text('Duplikat Jadwal Kunjungan');
                $('#modalJadwal').appendTo('body').modal('show');
            }
        });

        // Submit Form Simpan
        $('#formJadwal').submit(function (e) {
            e.preventDefault();
            $('#btn-simpan').prop('disabled', true).text('Menyimpan...');
            $.post('<?= site_url('cms/perpustakaan-keliling/jadwal/simpan') ?>', $(this).serialize(), function (res) {
                $('#btn-simpan').prop('disabled', false).text('Simpan Jadwal');
                if (res.status) {
                    $('#modalJadwal').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    tableJadwal.ajax.reload(null, false);
                } else {
                    var msg = res.message || 'Periksa inputan Anda';
                    if (res.errors) {
                        msg = Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        });

        // Batalkan Jadwal
        $('#tbl_jadwal').on('click', '.btn-batal', function () {
            var id = $(this).data('id');
            $('#batal_jadwal_id').val(id);
            $('#alasan_batal').val('');
            $('#modalBatalkan').appendTo('body').modal('show');
        });

        $('#formBatalkan').submit(function (e) {
            e.preventDefault();
            $.post('<?= site_url('cms/perpustakaan-keliling/jadwal/batalkan') ?>', $(this).serialize(), function (res) {
                if (res.status) {
                    $('#modalBatalkan').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    tableJadwal.ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            });
        });

        // Kelola Dokumentasi
        $('#tbl_jadwal').on('click', '.btn-dokumentasi', function () {
            var id = $(this).data('id');
            $('#dok_jadwal_id').val(id);
            loadDokumentasi(id);
            $('#modalDokumentasi').appendTo('body').modal('show');
        });

        function loadDokumentasi(jadwalId) {
            $.post('<?= site_url('cms/perpustakaan-keliling/dokumentasi/list') ?>', {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                jadwal_id: jadwalId
            }, function (res) {
                if (res.status) {
                    var container = $('#container-dokumentasi-list');
                    container.empty();
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(function (d) {
                            var html = `
                                <div class="col-md-4 mb-3 text-center">
                                    <div class="border rounded p-2 bg-light">
                                        <img src="${d.url}" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                                        <input type="text" class="form-control form-control-sm input-caption mb-1" data-id="${d.id}" value="${d.keterangan || ''}" placeholder="Caption...">
                                        <button type="button" class="btn btn-sm btn-danger btn-block btn-hapus-dok" data-id="${d.id}"><i class="fa fa-trash"></i> Hapus</button>
                                    </div>
                                </div>
                            `;
                            container.append(html);
                        });
                    } else {
                        container.html('<div class="col-12 text-center text-muted">Belum ada foto dokumentasi untuk jadwal ini.</div>');
                    }
                }
            });
        }

        $('#formUploadDokumentasi input[type="file"]').on('change', function () {
            var files = this.files;
            var maxByte = 2 * 1024 * 1024;
            for (var i = 0; i < files.length; i++) {
                if (files[i].size > maxByte) {
                    var mb = (files[i].size / (1024 * 1024)).toFixed(2);
                    Swal.fire('Ukuran File Terlalu Besar', 'File "' + files[i].name + '" (' + mb + ' MB) melebihi batas maksimal 2 MB.', 'warning');
                    $(this).val('');
                    return false;
                }
            }
        });

        $('#formUploadDokumentasi').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var jadwalId = $('#dok_jadwal_id').val();
            var $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: '<?= site_url('cms/perpustakaan-keliling/dokumentasi/upload') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        $('#formUploadDokumentasi')[0].reset();
                        $('#dok_jadwal_id').val(jadwalId);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                    // reload di kedua kondisi, karena sebagian file bisa sudah tersimpan
                    loadDokumentasi(jadwalId);
                    tableJadwal.ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengunggah. Cek ukuran file atau coba lagi.', 'error');
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });

        $(document).on('change', '.input-caption', function () {
            var dokId = $(this).data('id');
            var caption = $(this).val();

            $.post('<?= site_url('cms/perpustakaan-keliling/dokumentasi/caption') ?>', {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                id: dokId,
                keterangan: caption
            });
        });

        $(document).on('click', '.btn-hapus-dok', function () {
            var dokId = $(this).data('id');
            var jadwalId = $('#dok_jadwal_id').val();

            Swal.fire({
                title: 'Hapus foto dokumentasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then(function (result) {
                if (result.value) {
                    $.post('<?= site_url('cms/perpustakaan-keliling/dokumentasi/hapus') ?>', {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                        id: dokId
                    }, function (res) {
                        if (res.status) {
                            loadDokumentasi(jadwalId);
                            tableJadwal.ajax.reload(null, false);
                        }
                    });
                }
            });
        });

        // Dismiss Modal Fallback
        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"]', function () {
            $(this).closest('.modal').modal('hide');
        });
    });
</script>
<?= $this->endSection('script'); ?>
