<?php if (is_allowed('bukutamu/create')) : ?>
    <div class="modal fade" id="gb_import_modal" tabindex="-1" role="dialog" aria-labelledby="gb_import_title" aria-hidden="true"
        data-url="<?= base_url('bukutamu/create/import/' . $importType) ?>"
        data-csrf-name="<?= csrf_token() ?>"
        data-csrf-hash="<?= csrf_hash() ?>">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable gb-import-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gb_import_title">Import buku tamu <?= esc($importLabel) ?></h5>
                    <button type="button" class="gb-import-close" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="gb-upload-field" id="gb_upload_field">
                        <i class="fa fa-cloud-upload-alt gb-upload-icon" aria-hidden="true"></i>
                        <p class="gb-upload-title">Seret file Excel ke sini atau</p>
                        <button type="button" class="btn btn-outline-primary gb-upload-pick" id="gb_upload_pick">Pilih file</button>
                        <p class="gb-upload-hint">Format .xlsx atau .xls, satu file, maksimal 5 MB dan 5.000 baris.</p>
                        <input type="file" id="gb_upload_input" accept=".xlsx,.xls" hidden>
                    </div>

                    <div class="gb-upload-item" id="gb_upload_item" data-state="idle" hidden>
                        <i class="fa fa-file-excel gb-upload-item-icon" aria-hidden="true"></i>
                        <div class="gb-upload-item-body">
                            <div class="gb-upload-item-name" id="gb_upload_name"></div>
                            <div class="gb-upload-item-desc" id="gb_upload_desc" role="status" aria-live="polite"></div>
                            <div class="gb-upload-progress" id="gb_upload_progress" hidden><span id="gb_upload_bar"></span></div>
                        </div>
                        <button type="button" class="gb-upload-remove" id="gb_upload_remove" aria-label="Hapus file">
                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                        </button>
                    </div>

                    <ul class="gb-import-errors" id="gb_import_errors" tabindex="0" aria-label="Daftar isian yang perlu diperbaiki" hidden></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary gb-import-btn-lg" data-bs-dismiss="modal" id="gb_import_cancel">Batal</button>
                    <button type="button" class="btn btn-primary gb-import-btn-lg" id="gb_import_submit" disabled>Import</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
