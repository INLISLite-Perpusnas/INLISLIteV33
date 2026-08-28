<div class="modal fade" id="modal_update_kelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-graduation-hat icon-gradient bg-plum-plate"> </i> Update Batch Kelas
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Kelas akan diubah untuk <strong><span id="jml_anggota_terpilih">0</span></strong> anggota yang terpilih.</p>
                <div class="form-group">
                    <label for="target_kelas_id">Pindahkan ke Kelas</label>
                    <select class="form-control" id="target_kelas_id">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <?php foreach ($kelas_list as $row) : ?>
                            <option value="<?= $row->id ?>"><?= esc($row->namakelassiswa) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_simpan_update_kelas" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
