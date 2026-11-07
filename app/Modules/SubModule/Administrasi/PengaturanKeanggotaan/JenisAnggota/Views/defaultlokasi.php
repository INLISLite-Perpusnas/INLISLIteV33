<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Default Lokasi
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><?= lang('Page.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Default Lokasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Setting Default Lokasi
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-catalog-add" data-bs-toggle="modal" data-bs-target="#modal_create"><i class="fa fa-plus" aria-hidden="true"></i> Tambah</button>
            </div>
        </div>

        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center" style="min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($joinedData as $row) : ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($row->Name) ?></td>
                            <td class="text-center">
                                <button type="button" class="ina-action ina-action-danger" data-record-id="<?= esc((string) $row->ID, 'attr') ?>" onclick="deleteRecord(this)" title="Hapus" aria-label="Hapus"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_create" tabindex="-1" aria-labelledby="modal_create_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_create_title"><i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate" aria-hidden="true"></i> Tambah Default Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="frm_add" method="post" action="<?= base_url('master-jenis-anggota/save') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frm_add_Location_Library_id">Lokasi</label>
                        <select required class="form-control" id="frm_add_Location_Library_id" name="Location_Library_id">
                            <option value="">Pilih lokasi</option>
                            <?php foreach ($locations as $location) : ?>
                                <option value="<?= esc((string) $location->ID, 'attr') ?>"><?= esc($location->Name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="JenisAnggota_id" value="<?= esc((string) $JenisAnggota_id, 'attr') ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(function () {
        $('#modal_create').appendTo('body');

        $('#frm_add').on('submit', function (event) {
            event.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            submitButton.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                dataType: 'json',
                data: form.serialize()
            }).done(function (response) {
                if (response.success === true) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message || 'Data berhasil disimpan.' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Gagal menyimpan data.' });
                }
            }).fail(function () {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menghubungi server.' });
            }).always(function () {
                submitButton.prop('disabled', false);
            });
        });
    });

    function deleteRecord(button) {
        const id = button.getAttribute('data-record-id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`<?= base_url('master-jenis-anggota/deletedefaultlokasi') ?>/${id}`, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': <?= json_encode(csrf_hash(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> }
            }).then((response) => {
                if (!response.ok) throw new Error('Gagal menghapus data.');
                return Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data berhasil dihapus.' });
            }).then(() => location.reload()).catch(() => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menghapus data.' });
            });
        });
    }
</script>
<?= $this->endSection('script'); ?>
