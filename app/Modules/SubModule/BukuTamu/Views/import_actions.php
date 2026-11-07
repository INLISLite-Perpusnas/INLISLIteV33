<?php if (is_allowed('bukutamu/create')) : ?>
    <div class="gb-import-actions">
        <a class="btn btn-outline-primary gb-import-btn" href="<?= base_url('bukutamu/create/template/' . $importType) ?>">
            <i class="fa fa-download" aria-hidden="true"></i> Unduh template
        </a>
        <button type="button" class="btn btn-primary gb-import-btn" data-bs-toggle="modal" data-bs-target="#gb_import_modal">
            <i class="fa fa-file-excel" aria-hidden="true"></i> Import Excel
        </button>
    </div>
<?php endif; ?>
