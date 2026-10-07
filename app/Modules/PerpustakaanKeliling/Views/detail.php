<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapDetail {
        height: 260px;
        width: 100%;
        border-radius: 8px;
    }

    #mapDetailModal {
        height: 70vh;
        width: 100%;
    }

    #mapModal .modal-body {
        padding: 0;
    }

    #mapModal .leaflet-container {
        border-radius: 0 0 6px 6px;
    }

    .galeri-thumb {
        height: 140px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<div class="container mb-5 mt-4" style="padding-top: 100px;">
    <!-- Header Judul & Status -->
    <div class="mb-4">
        <h2 class="font-weight-bold">Perpustakaan Keliling - <?= esc($jadwal->nama_lokasi) ?></h2>
        <p class="text-muted lead">
            <i class="fa fa-calendar-alt"></i> <?= date('d F Y', strtotime($jadwal->tanggal)) ?> |
            <i class="fa fa-clock"></i> <?= date('H:i', strtotime($jadwal->jam_mulai)) ?> -
            <?= date('H:i', strtotime($jadwal->jam_selesai)) ?> |
            <?= $jadwal->getBadgeStatusHtml() ?>
        </p>

        <?php if ($jadwal->status === 'dibatalkan'): ?>
            <div class="alert alert-danger">
                <h5><i class="fa fa-exclamation-triangle"></i> Jadwal Dibatalkan</h5>
                <p class="mb-0"><strong>Alasan Pembatalan:</strong>
                    <?= esc($jadwal->alasan_batal ?: 'Tidak ada alasan dicantumkan.') ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content 2 Kolom (Desktop: Kiri 2/3, Kanan 1/3) -->
    <div class="row">
        <!-- Kolom Kiri: Informasi Kendaraan / Unit -->
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fa fa-bus"></i> Informasi Unit Kendaraan</h5>
                </div>
                <div class="card-body">
                    <h4><?= esc($jadwal->nama_unit) ?> <small
                            class="text-muted">(<?= esc($jadwal->nomor_kendaraan) ?>)</small></h4>
                    <?php if (!empty($jadwal->deskripsi_unit)): ?>
                        <p class="text-secondary"><?= esc($jadwal->deskripsi_unit) ?></p>
                    <?php endif; ?>

                    <!-- Galeri Foto Unit -->
                    <?php if (!empty($fotoUnit)): ?>
                        <h6 class="font-weight-bold mt-3 mb-2">Foto Unit Kendaraan:</h6>
                        <div class="row">
                            <?php foreach ($fotoUnit as $f): ?>
                                <div class="col-6 col-md-4 mb-3">
                                    <a href="<?= $f['url'] ?>" target="_blank">
                                        <img src="<?= $f['url'] ?>" class="img-fluid rounded border galeri-thumb w-100"
                                            alt="Foto Unit">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($jadwal->keterangan)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="fa fa-info-circle"></i> Catatan / Keterangan Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= esc($jadwal->keterangan) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan: Alamat, Peta, Tombol & Petugas -->
        <div class="col-lg-4 mb-4">
            <!-- Card Alamat & Map -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0"><i class="fa fa-map-marked-alt"></i> Lokasi & Navigasi</h5>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold"><?= esc($jadwal->nama_lokasi) ?></h6>
                    <p class="text-muted small"><?= esc($jadwal->alamat) ?></p>

                    <?php if (!empty($jadwal->keterangan_lokasi)): ?>
                        <p class="small text-info mb-3"><strong>Patokan:</strong> <?= esc($jadwal->keterangan_lokasi) ?></p>
                    <?php endif; ?>

                    <div id="mapDetail" class="mb-3"></div>
                    <!-- Tombol Perbesar Peta -->
                    <button type="button" class="btn btn-outline-secondary btn-block mb-2" data-bs-toggle="modal"
                        data-bs-target="#mapModal">
                        <i class="fa fa-expand"></i> Lihat Peta Lebih Besar
                    </button>
                    <!-- Tombol Google Maps -->
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= esc($jadwal->latitude) ?>,<?= esc($jadwal->longitude) ?>"
                        target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-block mb-2">
                        <i class="fa fa-map-marker-alt"></i> Buka di Google Maps
                    </a>

                    <!-- Tombol Google Calendar -->
                    <?php if ($jadwal->status === 'aktif' && $jadwal->getStatusTampil() === 'akan_datang'): ?>
                        <?php
                        $dateStart = date('Ymd\THis', strtotime($jadwal->tanggal . ' ' . $jadwal->jam_mulai));
                        $dateEnd = date('Ymd\THis', strtotime($jadwal->tanggal . ' ' . $jadwal->jam_selesai));
                        $gCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' .
                            '&text=' . rawurlencode('Perpustakaan Keliling - ' . $jadwal->nama_lokasi) .
                            '&dates=' . $dateStart . '/' . $dateEnd .
                            '&ctz=Asia/Jakarta' .
                            '&location=' . rawurlencode($jadwal->alamat) .
                            '&details=' . rawurlencode('Kunjungan Perpustakaan Keliling di ' . $jadwal->nama_lokasi);
                        ?>
                        <a href="<?= $gCalUrl ?>" target="_blank" rel="noopener noreferrer"
                            class="btn btn-outline-primary btn-block">
                            <i class="fa fa-calendar-plus"></i> Tambah ke Google Kalender
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Petugas -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0"><i class="fa fa-user-friends"></i> Petugas Pendamping</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($petugasList)): ?>
                            <?php foreach ($petugasList as $p): ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <img src="<?= base_url('assets/img/avatars/avatar.png') ?>" class="rounded-circle mr-3"
                                        style="width: 40px; height: 40px;">
                                    <div>
                                        <h6 class="mb-0 font-weight-bold"><?= esc($p['username']) ?></h6>
                                        <?php if ($p['tampilkan_email'] == 1 && !empty($p['email'])): ?>
                                            <div class="small"><a href="mailto:<?= esc($p['email']) ?>"><i
                                                        class="fa fa-envelope"></i> <?= esc($p['email']) ?></a></div>
                                        <?php endif; ?>
                                        <?php if ($p['tampilkan_hp'] == 1 && !empty($p['phone'])): ?>
                                            <div class="small"><a href="tel:<?= esc($p['phone']) ?>"><i class="fa fa-phone"></i>
                                                    <?= esc($p['phone']) ?></a></div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">Belum ada petugas terdaftar.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Dokumentasi Kegiatan -->
    <?php if (!empty($dokumentasi)): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><i class="fa fa-camera"></i> Dokumentasi Kegiatan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($dokumentasi as $d): ?>
                        <div class="col-6 col-md-3 mb-3 text-center">
                            <a href="<?= $d['url'] ?>" target="_blank">
                                <img src="<?= $d['url'] ?>" class="img-fluid rounded border galeri-thumb w-100"
                                    alt="Dokumentasi">
                            </a>
                            <?php if (!empty($d['keterangan'])): ?>
                                <p class="small text-muted mt-1 mb-0"><?= esc($d['keterangan']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Modal Peta -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 80vw;">
            <div class="modal-content shadow-lg border-0">

                <!-- Header Modal -->
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="mapModalLabel">
                        <i class="fa fa-map-marked-alt text-primary"></i>
                        <span><?= esc($jadwal->nama_lokasi) ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body Modal -->
                <div class="modal-body p-0">
                    <!-- Tinggi diset 70vh agar peta cukup tinggi dan seimbang di layar laptop/HP -->
                    <div id="mapDetailModal" style="height: 70vh; width: 100%;"></div>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer justify-content-between align-items-center bg-light">
                    <div class="small text-muted text-truncate me-3" style="max-width: 60%;"
                        title="<?= esc($jadwal->alamat) ?>">
                        <i class="fa fa-location-dot me-1"></i> <?= esc($jadwal->alamat) ?>
                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&query=<?= esc($jadwal->latitude) ?>,<?= esc($jadwal->longitude) ?>"
                        target="_blank" rel="noopener noreferrer"
                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2">
                        <i class="fa fa-map-marker-alt"></i> Buka di Google Maps
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('script'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    $(document).ready(function () {
        var lat = <?= (float) $jadwal->latitude ?>;
        var lng = <?= (float) $jadwal->longitude ?>;

        var namaLokasi = <?= json_encode($jadwal->nama_lokasi) ?>;
        var alamat = <?= json_encode($jadwal->alamat) ?>;

        var popupContent =
            '<b>' + namaLokasi + '</b><br>' + alamat;


        // ========================================
        // MAP PREVIEW
        // ========================================

        var mapDetail = L.map('mapDetail').setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapDetail);

        L.marker([lat, lng])
            .addTo(mapDetail)
            .bindPopup(popupContent)
            .openPopup();


        // ========================================
        // MAP MODAL
        // ========================================

        var mapDetailModal = L.map('mapDetailModal').setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapDetailModal);

        L.marker([lat, lng])
            .addTo(mapDetailModal)
            .bindPopup(popupContent)
            .openPopup();


        // ========================================
        // FIX LEAFLET SIZE SAAT MODAL DIBUKA
        // ========================================

        $('#mapModal').on('shown.bs.modal', function () {
            mapDetailModal.invalidateSize();
            mapDetailModal.setView([lat, lng], 16);
        });
    });
</script>
<?= $this->endSection('script'); ?>