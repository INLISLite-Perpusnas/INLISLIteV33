<?php
$prefix = '';
if (isset($branch->slug) && $branch->slug != null) {
	$prefix = $branch->slug . '/';
} else if (isset($branch->Name) && trim($branch->Name) != '') {
	$prefix = str_slugify($branch->Name) . '/';
}

$request = \Config\Services::request();
$request->uri->setSilent();
$cookie_location = cookie_location();
$mitra_perpustakaan = $cookie_location->Branch_name ?? '';
$lokasi_perpustakaan = $cookie_location->LocationLibrary_name ?? '';
$alamat_perpustakaan = $cookie_location->LocationLibrary_address ?? '';
$lokasi_ruang = $cookie_location->Name ?? '';
$kode_lokasi = $cookie_location->Code ?? '';
$slug = $request->getGet('slug') ?? 'anggota';
?>

<?= $this->extend(config('Core')->layout_landing); ?>
<?= $this->section('style'); ?>
<style>
	.card-horizontal {
		display: flex;
		flex: 1 1 auto;
	}

	tr.group,
	tr.group:hover {
		background-color: #F0F3F5 !important;
	}

	dl {
		display: grid;
		grid-template-columns: max-content auto;
	}

	dt {
		grid-column-start: 1;
		width: 100px;
		font-weight: normal;
	}

	dd {
		grid-column-start: 2;
	}

	#nav_profile li a.active {
		font-weight: bold !important;
		color: white !important;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('branch'); ?>
<div class="row">
	<div class="col-md-12">
		<h5><b><?= $lokasi_perpustakaan ?></b></h5>
		<?= $alamat_perpustakaan ?>
	</div>
</div>
<?= $this->endSection('branch'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div><b>Buku Tamu</b>
					<div class="page-title-subheading font-weight-bold"><?= $kode_lokasi ?> - <?= $lokasi_ruang ?></div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item" aria-current="page">Buku Tamu</li>
						<li class="active breadcrumb-item" aria-current="page">Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="nav-link active" href="<?= base_url($prefix . 'buku-tamu') ?>">
				<span>Anggota</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/non_anggota') ?>">
				<span>Bukan Anggota </span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/rombongan') ?>">
				<span>Rombongan</span>
			</a>
		</li>
	</ul>

	<h1>Face Recognition Example</h1>
    <video id="videoElement" autoplay></video>
    <canvas id="canvasElement"></canvas>
    <div id="result"></div>
	

</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', async (event) => {
        if (typeof faceapi === 'undefined') {
            console.error('face-api is not loaded. Please check your internet connection and make sure the script is loaded correctly.');
            document.getElementById('result').textContent = 'Error: face-api is not loaded. Please check your internet connection and refresh the page.';
            return;
        }

        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const result = document.getElementById('result');
        let knownFaces = [];
        let faceMatcher = null;

        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/uploads');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/uploads');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/uploads');
            startVideo();
        } catch (err) {
            console.error('Error loading face-api models:', err);
            result.textContent = 'Error loading face recognition models. Please check the console for details.';
        }

        function startVideo() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: {} })
                    .then(stream => {
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(err => {
                        console.error('Error accessing the camera:', err);
                        result.textContent = 'Error accessing the camera. Please check the console for details.';
                    });
            } else {
                console.error('getUserMedia is not supported in this browser');
                result.textContent = 'Video capture is not supported in this browser.';
            }
        }

        video.addEventListener('play', async () => {
            console.log('Video started playing');
            try {
                await loadKnownFaces();
                const displaySize = { width: video.videoWidth, height: video.videoHeight };
                faceapi.matchDimensions(canvas, displaySize);
                
                setInterval(async () => {
                    if (video.paused || video.ended) return;
                    
                    const detections = await faceapi.detectAllFaces(video, new faceapi.SsdMobilenetv1Options())
                        .withFaceLandmarks()
                        .withFaceDescriptors();
                    
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                    if (faceMatcher) {
                        const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

                        results.forEach((result, i) => {
                            const box = resizedDetections[i].detection.box;
                            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                            drawBox.draw(canvas);
                        });

                        result.textContent = results.map(r => r.toString()).join(', ');
                    }
                }, 100);
            } catch (err) {
                console.error('Error in face recognition process:', err);
                result.textContent = 'Error in face recognition process. Please check the console for details.';
            }
        });

        async function loadKnownFaces() {
            try {
                const response = await fetch('http://localhost:8080/getKnownFaces');
                const faces = await response.json();
                
                console.log('Loaded known faces:', faces);

                const labeledDescriptors = await Promise.all(faces.map(async face => {
                    const img = await faceapi.fetchImage(face.photoUrl);
                    const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                    if (detection) {
                        return new faceapi.LabeledFaceDescriptors(face.name, [detection.descriptor]);
                    } else {
                        console.warn(`No face detected in image for ${face.name}`);
                        return null;
                    }
                }));

                const validDescriptors = labeledDescriptors.filter(desc => desc !== null);
                if (validDescriptors.length > 0) {
                    faceMatcher = new faceapi.FaceMatcher(validDescriptors);
                } else {
                    console.warn('No valid face descriptors found');
                }
            } catch (err) {
                console.error('Error loading known faces:', err);
                throw err;
            }
        }

        video.addEventListener('canplay', function() {
            if (video.paused) {
                video.play();
            }
        });
    });
    </script>
<?= $this->endSection('script'); ?>