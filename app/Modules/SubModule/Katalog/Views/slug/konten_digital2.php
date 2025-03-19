<div class="card-body">
	<div class="row">
		<div class="col-md-12">
			<div class="position-relative form-group">
				<label for="file_image" class="">Upload Konten Digital</label>
				<div id="file_image" class="dropzone"></div>
				<div id="file_image_listed"></div>
				<div>
					<small class="info help-block text-muted">Format (.pdf). Max 5 Files @2MB</small>
				</div>
			</div>
		</div>
	</div>

	<div class="card-footer p-0 pt-3">
		<div class="form-group">
			<div class="form-check form-check-inline">
				<input type="hidden" name="IsRedirect" value="0">
				<input class="form-check-input" type="checkbox" name="IsRedirect" value="1">
				<label class="form-check-label" for="IsRedirect">Tutup form setelah simpan</label>
			</div>

			<button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save mr-2"></i> Simpan</button>
		</div>
	</div>
</div>

<?= $this->section('script'); ?>
<script>
	Dropzone.autoDiscover = false;
	var file_image = setDropzone('file_image', 'katalog', '.pdf', 5, 2);
</script>
<?= $this->endSection('script'); ?>