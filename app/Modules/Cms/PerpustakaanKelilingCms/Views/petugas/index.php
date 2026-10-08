<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .select2-container {
        width: 100% !important;
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
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Master Petugas Perpustakaan Keliling
                    <div class="page-title-subheading">Kelola data petugas pendamping dan visibilitas kontak</div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" class="btn btn-success" id="btn-tambah">
                    <i class="fa fa-plus"></i> Tambah Petugas
                </button>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="tbl_petugas" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Username / Nama</th>
                        <th>Email</th>
                        <th>No HP / Telepon</th>
                        <th width="120">Tampil Email</th>
                        <th width="120">Tampil HP</th>
                        <th width="100">Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Petugas -->
<div class="modal fade" id="modalPetugas" tabindex="-1" role="dialog" aria-labelledby="modalPetugasLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPetugas">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="petugas_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPetugasLabel">Form Petugas</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="group-select-user">
                        <label for="select_user_id">Pilih User <span class="text-danger">*</span></label>
                        <select class="form-control" name="user_id" id="select_user_id" required></select>
                        <small class="form-text text-muted">Cari dan pilih akun user yang akan ditugaskan sebagai
                            petugas perpustakaan keliling.</small>
                    </div>

                    <div class="form-group" id="group-info-user" style="display: none;">
                        <label>User Terpilih:</label>
                        <div class="p-2 border rounded bg-light" id="info-user-nama"></div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="tampilkan_email"
                                name="tampilkan_email" value="1">
                            <label class="custom-control-label" for="tampilkan_email">Tampilkan Email di Halaman
                                Publik</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="tampilkan_hp" name="tampilkan_hp"
                                value="1">
                            <label class="custom-control-label" for="tampilkan_hp">Tampilkan No HP di Halaman
                                Publik</label>
                        </div>
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
<script>
    var tablePetugas;

    $(document).ready(function () {
        tablePetugas = $('#tbl_petugas').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('cms/perpustakaan-keliling/petugas/data') ?>',
                type: 'POST',
                data: function (d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false },
                { data: 'username' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'tampilkan_email', className: 'text-center' },
                { data: 'tampilkan_hp', className: 'text-center' },
                { data: 'status', className: 'text-center' },
                { data: 'action', orderable: false, className: 'text-center' }
            ]
        });

        $('#select_user_id').select2({
            dropdownParent: $('#modalPetugas'),
            placeholder: 'Cari user berdasarkan username / email...',
            ajax: {
                url: '<?= site_url('cms/perpustakaan-keliling/petugas/cari-user') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                }
            }
        });

        $('#btn-tambah').click(function () {
            $('#formPetugas')[0].reset();
            $('#petugas_id').val('');
            $('#select_user_id').val(null).trigger('change');
            $('#group-select-user').show();
            $('#group-info-user').hide();
            $('#modalPetugasLabel').text('Tambah Petugas Baru');
            $('#modalPetugas').appendTo('body').modal('show');
        });

        $('#tbl_petugas').on('click', '.btn-edit', function () {
            var rowData = tablePetugas.row($(this).closest('tr')).data();
            if (rowData) {
                $('#petugas_id').val(rowData.id);
                $('#tampilkan_email').prop('checked', rowData.tampilkan_email_val == 1);
                $('#tampilkan_hp').prop('checked', rowData.tampilkan_hp_val == 1);

                $('#group-select-user').hide();
                $('#group-info-user').show();
                $('#info-user-nama').html('<strong>' + rowData.username + '</strong> (' + (rowData.email || 'Tanpa Email') + ' | ' + (rowData.phone || 'Tanpa HP') + ')');

                $('#modalPetugasLabel').text('Edit Hak Akses Kontak Petugas: ' + rowData.username);
                $('#modalPetugas').appendTo('body').modal('show');
            }
        });

        $('#formPetugas').submit(function (e) {
            e.preventDefault();
            $('#btn-simpan').prop('disabled', true).text('Menyimpan...');
            $.post('<?= site_url('cms/perpustakaan-keliling/petugas/simpan') ?>', $(this).serialize(), function (res) {
                $('#btn-simpan').prop('disabled', false).text('Simpan');
                if (res.status) {
                    $('#modalPetugas').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    tablePetugas.ajax.reload(null, false);
                } else {
                    var msg = res.message || 'Periksa inputan Anda';
                    if (res.errors) {
                        msg = Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        });

        $('#tbl_petugas').on('click', '.btn-toggle-status', function () {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var textAction = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';

            Swal.fire({
                title: 'Konfirmasi Status',
                text: 'Apakah Anda yakin ingin ' + textAction + ' petugas ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(function (result) {
                if (result.value) {
                    $.post('<?= site_url('cms/perpustakaan-keliling/petugas/status') ?>', {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                        id: id,
                        status: status
                    }, function (res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tablePetugas.ajax.reload(null, false);
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
