<div class="card-header">
	<i class="header-icon lnr-picture icon-gradient bg-plum-plate"> </i> Cover Katalog
	<div class="btn-actions-pane-right actions-icon-btn">
		<a href="javascript:void(0);" data-id="<?= $catalog->ID ?>" data-ref-id="" data-field="CoverURL" data-title-header="Upload Konten Digital" data-title-file="File Cover" data-dropzone-url="<?= base_url('katalog/do_upload') ?>" data-upload-url="<?= base_url('api/katalog/upload_cover') ?>" data-max-files="1" data-max-size="12" data-format=".jpg,.jpeg,.png" data-format-title="Format (JPG|JPEG|PNG). Max 2MB" data-redirect-url="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=cover') ?>" title="" class="btn btn-success upload-data">
			<i class="fa fa-plus"></i> Tambah File
		</a>
	</div>
</div>
<div class="card-body">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-6">
			<?php
			$default = base_url('uploads/default/no_cover.jpg');
			$image = (!empty($catalog->CoverURL)) ? base_url('uploads/katalog/' . $catalog->CoverURL) : $default;

			$html = '<a href="' . $image . '" class="image-link"><img width="100%" class="rounded" src="' . $image . '" class="lazy" data-src="' . $image . '" onerror="this.onerror=null;this.src=' . $default . ';" alt=""></a>';
			echo $html;
			?>
		</div>
	</div>
</div>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\slug\upload_modal'); ?>
<?= $this->endSection('script'); ?>