<?=$this->section('style');?>
<style>
#video {
  border: 1px solid black;
  box-shadow: 2px 2px 3px black;
  width:320px;
  height:240px;
}

#photo {
  border: 1px solid black;
  box-shadow: 2px 2px 3px black;
  width:320px;
  height:240px;
}

#canvas {
  display:none;
}

.camera {
  width: 340px;
  display:inline-block;
}

.output {
  width: 340px;
  display:inline-block;
}

#startbutton {
  display:block;
  position:relative;
  margin-left:auto;
  margin-right:auto;
  bottom:32px;
  background-color: rgba(0, 150, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.7);
  box-shadow: 0px 0px 1px 2px rgba(0, 0, 0, 0.2);
  font-size: 14px;
  font-family: "Lucida Grande", "Arial", sans-serif;
  color: rgba(255, 255, 255, 1.0);
}

.contentarea {
  font-size: 16px;
  font-family: "Lucida Grande", "Arial", sans-serif;
  width: 760px;
}

/* File Upload INA Digital, varian Upload Button untuk form (design.inadigital.go.id/components/file-upload/guide) */
.ina-upload-current {
  display: block;
  max-width: 160px;
  max-height: 160px;
  margin-bottom: 16px;
  border: 1px solid var(--ina-border, #e5e7eb);
  border-radius: 8px;
}

.ina-upload-hint {
  display: block;
  margin-top: 8px;
  font-size: 13px;
  color: var(--ina-text-muted, #6b7280);
}

.ina-upload-feedback {
  margin-top: 8px;
  font-size: 13px;
  color: var(--ina-danger, #c62828);
}

.ina-upload-feedback:empty {
  display: none;
}

.ina-upload-items:not(:empty) {
  margin-top: 12px;
}

.ina-upload-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  background: var(--ina-surface, #fff);
  border: 1px solid var(--ina-border, #e5e7eb);
  border-radius: 8px;
}

.ina-upload-item.dz-error {
  border-color: var(--ina-danger, #c62828);
}

.ina-upload-thumb {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  object-fit: cover;
  background: var(--ina-hover, #f3f4f6);
  border-radius: 6px;
}

.ina-upload-thumb:not([src]) {
  visibility: hidden;
}

.ina-upload-body {
  flex: 1;
  min-width: 0;
}

.ina-upload-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--ina-text, #1f2937);
  overflow-wrap: anywhere;
}

.ina-upload-meta {
  margin-top: 2px;
  font-size: 13px;
  color: var(--ina-text-muted, #6b7280);
}

.ina-upload-meta strong {
  font-weight: 400;
}

.ina-upload-status {
  display: none;
}

.ina-upload-item.dz-processing:not(.dz-success):not(.dz-error) .ina-upload-status-uploading,
.ina-upload-item.dz-success .ina-upload-status-uploaded {
  display: inline;
}

.ina-upload-status-uploaded {
  color: var(--ina-success, #1f7a36);
}

.ina-upload-progress {
  display: none;
  height: 4px;
  margin-top: 6px;
  overflow: hidden;
  background: var(--ina-border, #e5e7eb);
  border-radius: 2px;
}

.ina-upload-item.dz-processing:not(.dz-success):not(.dz-error) .ina-upload-progress {
  display: block;
}

.ina-upload-progress > span {
  display: block;
  width: 0;
  height: 100%;
  background: var(--ina-primary, #1d5fd1);
  transition: width .2s ease;
}

.ina-upload-error {
  display: none;
  margin-top: 2px;
  font-size: 13px;
  color: var(--ina-danger, #c62828);
}

.ina-upload-item.dz-error .ina-upload-error {
  display: block;
}

.ina-upload-remove {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  padding: 0;
  font-size: 15px;
  color: var(--ina-danger, #c62828);
  background: transparent;
  border: 0;
  border-radius: 8px;
  cursor: pointer;
}

.ina-upload-remove:hover {
  background: var(--ina-danger-subtle, #fdecec);
}

.ina-upload-remove:focus-visible {
  outline: 2px solid var(--ina-primary, #1d5fd1);
  outline-offset: 2px;
}
</style>
<?=$this->endSection('style');?>

<div class="row">
	<div class="col-md-12">
		<div id="accordion_foto" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-toggle="collapse" data-target="#collapse_foto"
						data-bs-toggle="collapse" data-bs-target="#collapse_foto"
						aria-expanded="true" aria-controls="collapse_foto"
						class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0">
							<i class="header-icon lnr-layers icon-gradient bg-primary">
							</i>
							Upload Foto
						</h5>
					</button>
				</div>
				<div data-parent="#accordion_foto" data-bs-parent="#accordion_foto" id="collapse_foto" class="collapse show">
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<?php if (!empty($anggota->PhotoUrl)) : ?>
										<span class="d-block mb-2 font-weight-bold" style="font-size: 14px;">Foto Saat Ini</span>
										<img src="<?= esc(base_url('uploads/anggota/' . $anggota->PhotoUrl), 'attr') ?>" alt="Foto anggota saat ini" class="ina-upload-current">
									<?php endif; ?>
									<label for="file_image_button" class=""><?= !empty($anggota->PhotoUrl) ? 'Ganti Foto Anggota' : 'Unggah Foto Anggota' ?></label>
									<div id="file_image">
										<button type="button" id="file_image_button" class="btn btn-ina-outline" aria-describedby="file_image_hint">
											<i class="fa fa-upload" aria-hidden="true"></i> Pilih File
										</button>
										<small id="file_image_hint" class="ina-upload-hint">Format JPG atau PNG. Ukuran maksimal 2 MB.</small>
										<div id="file_image_feedback" class="ina-upload-feedback" role="alert"></div>
										<div id="file_image_items" class="ina-upload-items" aria-live="polite"></div>
									</div>
									<div id="file_image_listed"></div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="position-relative form-group">
									<label class="d-block">Ambil dari Kamera</label>
									<div id="my_camera"></div>
									<div id="results"></div>
									<input type=button class="btn btn-ina-outline" value="Buka Kamera" onClick="open_snapshot()">
									<input type=button class="btn btn-ina-outline" value="Ambil Foto" onClick="take_snapshot()">


									<input type="hidden" name="camera_image" id="camera_image" value="">
									<input type="hidden" name="is_camera" id="is_camera" value="">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?=$this->section('script');?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
  Dropzone.autoDiscover = false;
  var file_image = setDropzone('file_image', 'anggota', '.jpg,.jpeg,.png', 1, 2, false, {
    clickable: '#file_image_button',
    previewsContainer: '#file_image_items',
    addRemoveLinks: false,
    thumbnailWidth: 96,
    thumbnailHeight: 96,
    dictCancelUploadConfirmation: null,
    dictInvalidFileType: 'Format file tidak didukung. Pilih file JPG atau PNG.',
    dictFileTooBig: 'Ukuran file {{filesize}} MB melebihi batas {{maxFilesize}} MB. Pilih file yang lebih kecil.',
    dictResponseError: 'Foto gagal diunggah. Hapus file ini lalu coba lagi.',
    previewTemplate: `
      <div class="ina-upload-item dz-preview">
        <img class="ina-upload-thumb" data-dz-thumbnail alt="">
        <div class="ina-upload-body">
          <div class="ina-upload-name" data-dz-name></div>
          <div class="ina-upload-meta">
            <span data-dz-size></span>
            <span class="ina-upload-status ina-upload-status-uploading">· Mengunggah...</span>
            <span class="ina-upload-status ina-upload-status-uploaded">· Terunggah</span>
          </div>
          <div class="ina-upload-progress"><span data-dz-uploadprogress></span></div>
          <div class="ina-upload-error" data-dz-errormessage></div>
        </div>
        <button type="button" class="ina-upload-remove" data-dz-remove aria-label="Hapus file" title="Hapus file"><i class="fa fa-trash-alt" aria-hidden="true"></i></button>
      </div>`,
  });
  // Hanya satu foto: tombol nonaktif selama ada file, sampai file dihapus
  function updateFileImageButton() {
    $('#file_image_button').prop('disabled', file_image.files.length >= 1);
  }
  file_image.on('addedfile', function(file) {
    // File baru yang diseret menggantikan file yang sudah ditolak
    file_image.getRejectedFiles().forEach(function(rejected) {
      if (rejected !== file) {
        file_image.removeFile(rejected);
      }
    });
    $('#file_image_feedback').text('');
    updateFileImageButton();
    $('#btn-submit').prop('disabled', true);
  });
  file_image.on('maxfilesexceeded', function(file) {
    file_image.removeFile(file);
    $('#file_image_feedback').text('Hanya satu foto yang bisa diunggah. Hapus foto saat ini untuk menggantinya.');
  });
  file_image.on('error', function(file, message) {
    // Pesan dari server berbentuk JSON { msg }
    if (message && message.msg && file.previewElement) {
      file.previewElement.querySelector('[data-dz-errormessage]').textContent = message.msg;
    }
  });
  file_image.on('sending', function(file, xhr, formData) {
    formData.append('<?= csrf_token() ?>', $('input[name="<?= csrf_token() ?>"]').val());
  });
  file_image.on('success', function(file, response) {
    file.previewElement.classList.add('dz-success');
    if (response && response.csrfHash) {
      $('input[name="<?= csrf_token() ?>"]').val(response.csrfHash);
    }
  });
  function updateMemberSubmitState() {
    const hasPendingUploads = file_image.getQueuedFiles().length > 0 || file_image.getUploadingFiles().length > 0;
    $('#btn-submit').prop('disabled', hasPendingUploads);
  }
  file_image.on('complete', updateMemberSubmitState);
  file_image.on('removedfile', function() {
    updateFileImageButton();
    updateMemberSubmitState();
  });
</script>

<!-- Kode untuk snapshot dan menampilkan picture -->
<script>
// Konfigurasi dan pengaturan kamera
function open_snapshot() {
    // Foto lama dibuang agar yang tampil selalu sama dengan yang akan tersimpan
    document.getElementById('results').innerHTML = '';
    $('#camera_image').val('');
    $('#is_camera').val('');
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
}
// Tombol untuk menangkap

// preload shutter audio clip
var shutter = new Audio();
shutter.autoplay = false;
shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

function take_snapshot() {
    // play sound effect
    shutter.play();

    //  snapshot dan mendapatkan data gambar
    Webcam.snap(function(data_uri) {
        // display results in page
        document.getElementById('results').innerHTML = '<img id="imageprev" src="' + data_uri + '"/>';
        $('#camera_image').val(data_uri);
        $('#is_camera').val('1');
    });

    Webcam.reset();
    // Webcam.js menyisakan ukuran 320x240 pada wadah kamera, yang mendorong hasil foto ke bawah
    document.getElementById('my_camera').removeAttribute('style');
}
</script>
<?=$this->endSection('script');?>
