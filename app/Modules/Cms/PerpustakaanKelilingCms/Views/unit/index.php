<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .foto-unit-item {
        position: relative;
        display: inline-block;
        margin: 5px;
    }

    .foto-unit-item img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        border: 2px solid #ddd;
    }

    .foto-unit-item.is-utama img {
        border-color: #28a745;
    }

    .foto-unit-item .btn-remove-foto {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        line-height: 18px;
        text-align: center;
        cursor: pointer;
    }

    .foto-unit-item .badge-utama {
        position: absolute;
        bottom: 5px;
        left: 5px;
        font-size: 10px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-car icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Master Unit Perpustakaan Keliling
                    <div class="page-title-subheading">Kelola data kendaraan / unit perpustakaan keliling dan foto
                        galeri</div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" class="btn btn-success" id="btn-tambah">
                    <i class="fa fa-plus"></i> Tambah Unit
                </button>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="tbl_unit" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Unit</th>
                        <th>No. Kendaraan</th>
                        <th>Deskripsi</th>
                        <th width="100">Jumlah Foto</th>
                        <th width="100">Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Unit -->
<div class="modal fade" id="modalUnit" tabindex="-1" role="dialog" aria-labelledby="modalUnitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formUnit" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="unit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUnitLabel">Form Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_unit">Nama Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_unit" id="nama_unit" maxlength="150" required
                            placeholder="Contoh: Perpusling Bus 01">
                    </div>

                    <div class="form-group">
                        <label for="nomor_kendaraan">Nomor Kendaraan / Plat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="nomor_kendaraan"
                            id="nomor_kendaraan" maxlength="20" required placeholder="Contoh: B 1234 RPN">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3"
                            placeholder="Fasilitas / deskripsi kendaraan..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Foto (Maks 10 Foto per Unit, Max 2MB per file, format JPG/PNG/WebP)</label>
                        <input type="file" class="form-control-file" name="foto[]" id="foto" multiple
                            accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div id="section-foto-existing" style="display: none;">
                        <label>Kelola Foto Unit (Klik gambar untuk menjadikannya Foto Utama):</label>
                        <div id="container-foto-existing" class="d-flex flex-wrap p-2 border rounded bg-light"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    var tableUnit;

    $(document).ready(function () {
        tableUnit = $('#tbl_unit').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('cms/perpustakaan-keliling/unit/data') ?>',
                type: 'POST',
                data: function (d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false },
                { data: 'nama_unit' },
                { data: 'nomor_kendaraan' },
                { data: 'deskripsi' },
                { data: 'total_foto', className: 'text-center' },
                { data: 'status', className: 'text-center' },
                { data: 'action', orderable: false, className: 'text-center' }
            ]
        });

        $('#btn-tambah').click(function () {
            $('#formUnit')[0].reset();
            $('#unit_id').val('');
            $('#modalUnitLabel').text('Tambah Unit Baru');
            $('#section-foto-existing').hide();
            $('#container-foto-existing').empty();
            $('#modalUnit').appendTo('body').modal('show');
        });

        $('#tbl_unit').on('click', '.btn-edit', function () {
            var rowData = tableUnit.row($(this).closest('tr')).data();
            if (rowData) {
                $('#unit_id').val(rowData.id);
                $('#nama_unit').val(rowData.raw_nama_unit || rowData.nama_unit);
                $('#nomor_kendaraan').val(rowData.raw_nomor_kendaraan || rowData.nomor_kendaraan);
                $('#deskripsi').val(rowData.raw_deskripsi || rowData.deskripsi);
                $('#modalUnitLabel').text('Edit Unit: ' + (rowData.raw_nama_unit || rowData.nama_unit));

                // Ambil daftar foto unit
                $.get('<?= site_url('cms/perpustakaan-keliling/unit/get/') ?>' + rowData.id, function (res) {
                    if (res.status) {
                        renderFotoExisting(res.foto);
                    }
                });

                $('#modalUnit').appendTo('body').modal('show');
            }
        });

        function renderFotoExisting(fotos) {
            var container = $('#container-foto-existing');
            container.empty();
            if (fotos && fotos.length > 0) {
                $('#section-foto-existing').show();
                fotos.forEach(function (f) {
                    var isUtamaClass = f.is_utama == 1 ? 'is-utama' : '';
                    var badgeUtama = f.is_utama == 1 ? '<span class="badge badge-success badge-utama">Utama</span>' : '';
                    var html = `
                        <div class="foto-unit-item ${isUtamaClass}" data-id="${f.id}" data-unit="${f.unit_id}">
                            <span class="btn-remove-foto" data-id="${f.id}">&times;</span>
                            <img src="${f.url}" class="img-thumbnail btn-set-utama" style="cursor:pointer;" title="Klik untuk jadikan foto utama">
                            ${badgeUtama}
                        </div>
                    `;
                    container.append(html);
                });
            } else {
                $('#section-foto-existing').hide();
            }
        }

        // Action Set Utama Foto
        $(document).on('click', '.btn-set-utama', function () {
            var item = $(this).closest('.foto-unit-item');
            var fotoId = item.data('id');
            var unitId = item.data('unit');

            $.post('<?= site_url('cms/perpustakaan-keliling/unit/foto/utama') ?>', {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                unit_id: unitId,
                foto_id: fotoId
            }, function (res) {
                if (res.status) {
                    // Reload list foto modal edit
                    $.get('<?= site_url('cms/perpustakaan-keliling/unit/get/') ?>' + unitId, function (resGet) {
                        if (resGet.status) {
                            renderFotoExisting(resGet.foto);
                        }
                    });
                }
            });
        });

        // Action Hapus Foto
        $(document).on('click', '.btn-remove-foto', function () {
            var fotoId = $(this).data('id');
            var item = $(this).closest('.foto-unit-item');
            var unitId = item.data('unit');

            Swal.fire({
                title: 'Hapus Foto?',
                text: 'Foto akan dihapus dari server secara permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then(function (result) {
                if (result.value) {
                    $.post('<?= site_url('cms/perpustakaan-keliling/unit/foto/hapus') ?>', {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                        foto_id: fotoId
                    }, function (res) {
                        if (res.status) {
                            $.get('<?= site_url('cms/perpustakaan-keliling/unit/get/') ?>' + unitId, function (resGet) {
                                if (resGet.status) {
                                    renderFotoExisting(resGet.foto);
                                    tableUnit.ajax.reload(null, false);
                                }
                            });
                        }
                    });
                }
            });
        });

        // Validation size file foto max 2MB
        $('#foto').on('change', function () {
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

        // Submit Form Simpan
        $('#formUnit').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $('#btn-simpan').prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: '<?= site_url('cms/perpustakaan-keliling/unit/simpan') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    $('#btn-simpan').prop('disabled', false).text('Simpan');
                    if (res.status) {
                        $('#modalUnit').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        tableUnit.ajax.reload(null, false);
                    } else {
                        var msg = res.message || 'Periksa inputan Anda';
                        if (res.errors) {
                            msg = Object.values(res.errors).join('<br>');
                        }
                        Swal.fire('Gagal', msg, 'error');
                    }
                },
                error: function () {
                    $('#btn-simpan').prop('disabled', false).text('Simpan');
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        });

        // Toggle Status Aktif/Nonaktif
        $('#tbl_unit').on('click', '.btn-toggle-status', function () {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var textAction = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';

            Swal.fire({
                title: 'Konfirmasi Status',
                text: 'Apakah Anda yakin ingin ' + textAction + ' unit ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(function (result) {
                if (result.value) {
                    $.post('<?= site_url('cms/perpustakaan-keliling/unit/status') ?>', {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                        id: id,
                        status: status
                    }, function (res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tableUnit.ajax.reload(null, false);
                        } else {
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