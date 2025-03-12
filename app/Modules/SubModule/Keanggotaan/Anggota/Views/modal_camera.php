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
</style>
<?=$this->endSection('style');?>

<div class="modal fade" id="modal_camera" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Ambil Gambar
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_capture" method="post" data-action="" data-id="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="form_capture_message"></div>
                    <div class="form-row">
						<div class="col-md-12 is_camera">
							<div class="position-relative form-group">
								<label for="file_image" class="">Camera</label>
								<div>
									<div class="row contentarea">
										<div class="col">
											<div class="camera">
												<video id="video">Video stream not available.</video>
												<button id="startbutton">Capture</button> 
											</div>
											<canvas id="canvas"></canvas>
										</div>
										<div class="col">
											<div class="output">
												<img id="photo" alt="The screen capture will appear in this box.">
												<input type="hidden" name="camera_image" id="camera_image" value=""> <br>
												<small class="text-muted">Hasil capture yang akan diupload</small>
											</div>
										</div>
									</div>
								</div>
							</div>
							</div>
                    </div>
                </div>
                <div class="modal-footer">
					<input type="hidden" name="capture_id" id="capture_id" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?=$this->section('script');?>
<script>
	$(document).ready(function() {
		var width = 350;    // We will scale the photo width to this
		var height = 0;     // This will be computed based on the input stream

		var streaming = false;

		var video = null;
		var canvas = null;
		var photo = null;
		var startbutton = null;

		function startup() {
			video = document.getElementById('video');
			canvas = document.getElementById('canvas');
			photo = document.getElementById('photo');
			camera_image = document.getElementById('camera_image');
			
			startbutton = document.getElementById('startbutton');

			navigator.mediaDevices.getUserMedia({video: true, audio: false})
			.then(function(stream) {
				video.srcObject = stream;
				video.play();
			})
			.catch(function(err) {
				console.log("An error occurred: " + err);
			});

			video.addEventListener('canplay', function(ev){
				if (!streaming) {
					height = video.videoHeight / (video.videoWidth/width);
				
					if (isNaN(height)) {
						height = width / (4/3);
					}
				
					video.setAttribute('width', width);
					video.setAttribute('height', height);
					canvas.setAttribute('width', width);
					canvas.setAttribute('height', height);
					streaming = true;
				}
			}, false);

			startbutton.addEventListener('click', function(ev){
				takepicture();
				ev.preventDefault();
			}, false);
			
			clearphoto();
		}

		function clearphoto() {
			var context = canvas.getContext('2d');
			context.fillStyle = "#AAA";
			context.fillRect(0, 0, canvas.width, canvas.height);

			var data = canvas.toDataURL('image/png');
			photo.setAttribute('src', data);
		}

		function takepicture() {
			var context = canvas.getContext('2d');
			if (width && height) {
				canvas.width = width;
				canvas.height = height;
				context.drawImage(video, 0, 0, width, height);
				
				var data = canvas.toDataURL('image/png');
				photo.setAttribute('src', data);
				camera_image.setAttribute('value', data);

			} else {
				clearphoto();
			}
		}

		// window.addEventListener('load', startup, false);

		$(document).on('show.bs.modal','#modal_camera', function (e) {
			var this_ = e.relatedTarget;
			var id = $(this_).attr('data-id');
			$('#capture_id').val(id);
			startup();
		});

		$('#form_capture').submit(function(e) {
			e.preventDefault()
			var data_post = $(this).serializeArray();
			var id = $('#capture_id').val();

			$('.loading').show()

			$.ajax({
					url: "<?= base_url('api/anggota/capture_file') ?>",
					type: 'POST',
					dataType: 'json',
					data: data_post,
				})
				.done(function(res) {
					console.log(res)
					if (res.status === 201) {
						Swal.fire({
							title: 'Success',
							text: 'File berhasil disimpan',
							type: 'success',
							showConfirmButton: false,
							timer: 3000
						})

						setTimeout(function() {
							window.location.href = "<?= base_url('anggota') ?>";
						}, 2000)
					} else {
						$('#form_capture_message').html(res.messages.error)
					}
				})
				.fail(function(res) {
					console.log(res)
					// $('#form_capture_message').html(res.responseJSON.messages.error)
				})
				.always(function() {
					$('.loading').hide()
				});

			return false;
		});
	});
</script>
<?=$this->endSection('script');?>