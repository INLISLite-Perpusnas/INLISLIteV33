<div class="card-header">
    <i class="header-icon lnr-file-empty icon-gradient bg-plum-plate"> </i> Konten Digital Artikel
    <div class="btn-actions-pane-right actions-icon-btn">
        <?php if (count($files) < 3) : ?>
            <a href="javascript:void(0);" 
              data-id="" 
              data-ref-id="<?= $catalog->ID ?>" 
              data-field="FileURL" 
              data-title-header="Upload Konten Digital Artikel" 
              data-title-file="File Konten Digital Artikel" 
              data-dropzone-url="<?= base_url('katalog/do_upload') ?>" 
              data-upload-url="<?= base_url('api/katalog/upload_file_digital_artikel') ?>" 
              data-max-files="3" 
              data-max-size="12" 
              data-format=".pdf" 
              data-format-title="Format (PDF). Max 2MB" 
              data-redirect-url="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=konten_digital_artikel') ?>" 
              title="" 
              class="btn btn-success upload-data">
                <i class="fa fa-plus"></i> Tambah File
            </a>
        <?php endif; ?>
    </div>
</div>
<div class="card-body">
    <?php foreach ($article_files as $row) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-2">
                    <strong>Edisi Serial:</strong> <?= isset($row->EDISISERIAL) ? $row->EDISISERIAL : '-' ?> |
                    <strong>Artikel:</strong> <?= isset($row->title) ? $row->title : '-' ?> | 
                    <strong>Nama File:</strong> <?= basename($row->FileURL) ?>
                </div>
                <div class="position-relative form-group">
                    <a href="<?= base_url('uploads/katalog/' . $row->FileURL) ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-file-pdf"></i> Lihat File</a>
                    <?php if (strpos($row->FileURL, 'encrypted_') === 0) : ?>
                        <a href="<?= base_url('katalog/view_decrypted_article/' . $row->ID) ?>" target="_blank" class="btn btn-primary btn-sm view-decrypted" data-id="<?= $row->ID ?>"><i class="fa fa-lock-open"></i> Lihat File Decrypt</a>
                    <?php endif; ?>
                    <a href="javascript:void(0);" 
                      data-id="<?= $row->ID ?>" 
                      data-ref-id="<?= $row->Articles_id ?>" 
                      data-field="FileURL" 
                      data-title-header="Upload Konten Digital" 
                      data-title-file="File Konten Digital" 
                      data-dropzone-url="<?= base_url('katalog/do_upload') ?>" 
                      data-upload-url="<?= base_url('api/katalog/upload_file_digital_artikel') ?>" 
                      data-max-files="1" 
                      data-max-size="12" 
                      data-format=".pdf" 
                      data-format-title="Format (PDF). Max 2MB" 
                      data-redirect-url="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=konten_digital_artikel') ?>" 
                      title="" 
                      class="btn btn-warning btn-sm upload-data">
                        <i class="fa fa-pencil"></i> Ubah File
                    </a>
                    <a href="<?= base_url('api/katalog/delete_file_article/' . $row->ID) ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus File</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\slug\upload_modal_digital_artikel'); ?>

<?= $this->endSection('script'); ?>