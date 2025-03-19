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

<div class="row">
	<div class="col-md-12">
		<div id="accordion_upload" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-toggle="collapse" data-target="#collapse_upload" aria-expanded="true" aria-controls="collapse_upload" class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0">
							<i class="header-icon lnr-layers icon-gradient bg-primary"></i> Upload Foto
						</h5>
					</button>
				</div>
				<div data-parent="#accordion_upload" id="collapse_upload" class="collapse" style="">
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="form-relative form-check">
										<label class="form-check-label">
											<input type="checkbox" name="is_camera" id="is_camera" class="form-check-input"> Centang jika ingin mengaktifkan camera
										</label>
									</div>
									<small class="help-block with-errors"></small>
								</div>
							</div>

							<div class="col-md-6 is_upload">
								<div class="position-relative form-group">
									<label for="file_image" class="">File Foto</label>
									<div id="file_image" class="dropzone"></div>
									<div id="file_image_listed"></div>
									<div>
										<small class="info help-block text-muted">Format (JPG|PNG).Max 10 MB</small>
									</div>
								</div>
							</div>
							<div class="col-md-6 is_camera" style="display:none">
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
				</div>
			</div>
		</div>
	</div>
</div>

<?=$this->section('script');?>
<script>
	$('#is_camera').change(function() {
        if(this.checked) {
            $('.is_camera').show();
			$('.is_upload').hide();
        } else {
			$('.is_camera').hide();
			$('.is_upload').show();
		}    
    });

	(function() {
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

		window.addEventListener('load', startup, false);
	})();
</script>
<?=$this->endSection('script');?>