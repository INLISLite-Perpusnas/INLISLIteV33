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
		<div id="accordion" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-toggle="collapse" data-target="#collapse_madatory4"
						aria-expanded="true" aria-controls="collapse_madatory"
						class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0">
							<i class="header-icon lnr-layers icon-gradient bg-primary">
							</i>
							Upload Foto
						</h5>
					</button>
				</div>
				<div data-parent="#accordion" id="collapse_madatory4" class="collapse" style="">
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="file_image" class="">Foto
										Anggota</label>
									<div id="file_image" class="dropzone"></div>
									<div id="file_image_listed"></div>
									<div>
										<small class="info help-block text-muted">Format
											(JPG|PNG).
											Max 10 MB</small>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="camera_image" class="">Foto Anggota</label>
									<div id="my_camera"></div>
									<div id="results"></div>
									<input type=button class="btn btn-lg btn-primary" value="Open Camera" onClick="open_snapshot()">
									<input type=button class="btn btn-lg btn-primary" value="Take Snapshot" onClick="take_snapshot()">
									<input type=button class="btn btn-lg btn-primary" value="Save Snapshot" onClick="save_snapshot()">

									<input type="hidden" name="camera_image" id="camera_image" value="">
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

<!-- Kode untuk snapshot dan menampilkan picture -->
<script>
// Konfigurasi dan pengaturan kamera
function open_snapshot() {
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
    });

    Webcam.reset();
}

function save_snapshot() {
    // Get base64 value from <img id='imageprev'> source
    var base64image = document.getElementById("imageprev").src;

    Webcam.upload(base64image, '<?= base_url('anggota/camera') ?>', function(code, text) {
        console.log('Save successfully');
        //console.log(text);
    });
}
</script>
<?=$this->endSection('script');?>