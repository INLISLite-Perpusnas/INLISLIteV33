<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .select2-container--default .select2-selection--single {
        padding: 6px; height: 37px; font-size: 1.1em; position: relative;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-edit icon-gradient bg-plum-plate"> </i> Form
            Edit <?= lang('Sumbangan.module') ?>
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_edit" class="col-md-12 mx-auto" method="post" action="<?= base_url('sumbangan/edit/' . $sumbangan->ID); ?>">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="Member_id">Nama Anggota</label>
                            <div>
                                <select class="form-control select2" name="Member_id" id="t_member_id" style="width:100%">
                                    <option value="">-Pilih Anggota-</option>
                                    <?php foreach ($anggotas as $row) : ?>
                                        <option value="<?= $row->ID ?>" <?= ($row->ID == $sumbangan->Member_id) ? 'selected' : '' ?>>
                                            <?= $row->Fullname ?> (<?= $row->MemberNo ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="Jumlah">Jumlah Sumbangan</label>
                            <div>
                                <input type="text" class="form-control" id="frm_edit_jumlah" name="Jumlah" 
                                       value="<?= old('Jumlah', $sumbangan->Jumlah); ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Keterangan"><?= lang('Sumbangan.field.description') ?> </label>
                    <div>
                        <textarea id="frm_edit_description" name="Keterangan" rows="2" class="form-control autosize-input" 
                                  style="min-height: 38px;"><?= old('Keterangan', $sumbangan->Keterangan) ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('Sumbangan.action.save') ?></button>
                    <a href="<?= base_url('sumbangan') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $('.select2').select2();
</script>
<?= $this->endSection('script'); ?>