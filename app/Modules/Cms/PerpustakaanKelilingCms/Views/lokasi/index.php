<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #mapPicker {
        height: 350px;
        width: 100%;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-map-marker icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Master Lokasi Perpustakaan Keliling
                    <div class="page-title-subheading">Kelola data titik lokasi dan koordinat peta perpustakaan keliling
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" class="btn btn-success" id="btn-tambah">
                    <i class="fa fa-plus"></i> Tambah Lokasi
                </button>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="tbl_lokasi" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Lokasi</th>
                        <th>Alamat</th>
                        <th>Koordinat (Lat, Lng)</th>
                        <th>Keterangan</th>
                        <th width="100">Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Lokasi -->
<div class="modal fade" id="modalLokasi" tabindex="-1" role="dialog" aria-labelledby="modalLokasiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formLokasi">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="lokasi_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLokasiLabel">Form Lokasi</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_lokasi">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi" maxlength="150"
                            required placeholder="Contoh: Alun-Alun Kota">
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="alamat" id="alamat" rows="2" required
                            placeholder="Jl. Merdeka No. 1..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="latitude">Latitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="latitude" id="latitude" required
                                placeholder="-6.1753924">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="longitude">Longitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="longitude" id="longitude" required
                                placeholder="106.8271528">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-info mb-2" id="btn-my-location">
                            <i class="fa fa-crosshairs"></i> Gunakan Lokasi Saya
                        </button>
                        <div id="mapPicker"></div>
                        <small class="form-text text-muted">Klik pada peta atau geser pin penanda untuk menentukan
                            koordinat lokasi.</small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="2"
                            placeholder="Petunjuk arah / patokan lokasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var tableLokasi;
    var map, marker;
    var defaultLat = <?= config('PerpusKeliling')->defaultLat ?>;
    var defaultLng = <?= config('PerpusKeliling')->defaultLng ?>;
    var defaultZoom = <?= config('PerpusKeliling')->defaultZoom ?>;

    $(document).ready(function () {
        tableLokasi = $('#tbl_lokasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('cms/perpustakaan-keliling/lokasi/data') ?>',
                type: 'POST',
                data: function (d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false },
                { data: 'nama_lokasi' },
                { data: 'alamat' },
                { data: 'koordinat' },
                { data: 'keterangan' },
                { data: 'status', className: 'text-center' },
                { data: 'action', orderable: false, className: 'text-center' }
            ]
        });

        function initMap(lat, lng, zoom) {
            if (map) {
                map.remove();
            }

            map = L.map('mapPicker').setView([lat, lng], zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', function (e) {
                var pos = marker.getLatLng();
                $('#latitude').val(pos.lat.toFixed(7));
                $('#longitude').val(pos.lng.toFixed(7));
            });

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                $('#latitude').val(e.latlng.lat.toFixed(7));
                $('#longitude').val(e.latlng.lng.toFixed(7));
            });
        }

        $('#modalLokasi').on('shown.bs.modal', function () {
            var lat = parseFloat($('#latitude').val()) || defaultLat;
            var lng = parseFloat($('#longitude').val()) || defaultLng;
            var zoom = $('#lokasi_id').val() ? 16 : defaultZoom;

            initMap(lat, lng, zoom);
            setTimeout(function () { map.invalidateSize(); }, 200);
        });

        $('#latitude, #longitude').on('change keyup', function () {
            var lat = parseFloat($('#latitude').val());
            var lng = parseFloat($('#longitude').val());
            if (!isNaN(lat) && !isNaN(lng) && marker) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        });

        $('#btn-my-location').click(function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude.toFixed(7);
                    var lng = position.coords.longitude.toFixed(7);
                    $('#latitude').val(lat);
                    $('#longitude').val(lng);
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 16);
                    }
                }, function (err) {
                    Swal.fire('Info', 'Izin lokasi tidak diberikan atau lokasi tidak tersedia.', 'info');
                });
            } else {
                Swal.fire('Info', 'Geolokasi tidak didukung oleh peramban Anda.', 'info');
            }
        });

        $('#btn-tambah').click(function () {
            $('#formLokasi')[0].reset();
            $('#lokasi_id').val('');
            $('#modalLokasiLabel').text('Tambah Lokasi Baru');

            // Coba geolocation saat tambah baru jika ada
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (pos) {
                    $('#latitude').val(pos.coords.latitude.toFixed(7));
                    $('#longitude').val(pos.coords.longitude.toFixed(7));
                });
            }

            $('#modalLokasi').appendTo('body').modal('show');
        });

        $('#tbl_lokasi').on('click', '.btn-edit', function () {
            var rowData = tableLokasi.row($(this).closest('tr')).data();
            if (rowData) {
                $('#lokasi_id').val(rowData.id);
                $('#nama_lokasi').val(rowData.raw_nama || rowData.nama_lokasi);
                $('#alamat').val(rowData.raw_alamat || rowData.alamat);
                $('#latitude').val(rowData.latitude);
                $('#longitude').val(rowData.longitude);
                $('#keterangan').val(rowData.raw_ket || rowData.keterangan);
                $('#modalLokasiLabel').text('Edit Lokasi: ' + (rowData.raw_nama || rowData.nama_lokasi));

                $('#modalLokasi').appendTo('body').modal('show');
            }
        });

        $('#formLokasi').submit(function (e) {
            e.preventDefault();
            $('#btn-simpan').prop('disabled', true).text('Menyimpan...');
            $.post('<?= site_url('cms/perpustakaan-keliling/lokasi/simpan') ?>', $(this).serialize(), function (res) {
                $('#btn-simpan').prop('disabled', false).text('Simpan');
                if (res.status) {
                    $('#modalLokasi').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    tableLokasi.ajax.reload(null, false);
                } else {
                    var msg = res.message || 'Periksa inputan Anda';
                    if (res.errors) {
                        msg = Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        });

        $('#tbl_lokasi').on('click', '.btn-toggle-status', function () {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var textAction = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';

            Swal.fire({
                title: 'Konfirmasi Status',
                text: 'Apakah Anda yakin ingin ' + textAction + ' lokasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(function (result) {
                if (result.value) {
                    $.post('<?= site_url('cms/perpustakaan-keliling/lokasi/status') ?>', {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                        id: id,
                        status: status
                    }, function (res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tableLokasi.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
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