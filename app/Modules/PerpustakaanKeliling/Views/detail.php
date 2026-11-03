<?php
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$t = strtotime($jadwal->tanggal);
$tanggalIndo = (int) date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
$jam = date('H:i', strtotime($jadwal->jam_mulai)) . ' – ' . date('H:i', strtotime($jadwal->jam_selesai)) . ' WIB';

// Badge status gaya INA (label sama dengan getBadgeStatusHtml())
$statusMap = [
    'akan_datang' => ['Akan datang', 'is-info'],
    'berlangsung' => ['Sedang berlangsung', 'is-success'],
    'selesai'     => ['Selesai', 'is-neutral'],
    'dibatalkan'  => ['Dibatalkan', 'is-danger'],
];
$statusTampil = $jadwal->getStatusTampil();
[$statusLabel, $statusClass] = $statusMap[$statusTampil] ?? [ucfirst(str_replace('_', ' ', (string) $statusTampil)), 'is-neutral'];
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($jadwal->latitude . ',' . $jadwal->longitude);
?>
<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Detail Pusling - gaya INA Digital (Inter, skala 28/36, 18/26, 16/24, 14/20, 12/16; biru #2d659e) */
    :root {
        --pk-primary: #2d659e;
        --pk-primary-hover: #24527f;
        --pk-primary-soft: #eef4fa;
        --pk-ink: #1f2937;
        --pk-ink-subtle: #374151;
        --pk-muted: #6b7280;
        --pk-border: #e5e7eb;
        --pk-border-strong: #d1d5db;
        --pk-bg: #f7f7f8;
        --pk-radius: 12px;
        --pk-radius-sm: 8px;
        --pk-shadow: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .04);
        --pk-focus: 0 0 0 3px rgba(45, 101, 158, .2);
    }

    body { background: var(--pk-bg); }

    .pk-detail {
        max-width: 1600px;
        margin: 0 auto;
        padding: 110px 2rem 48px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--pk-ink);
    }

    /* Breadcrumb INA */
    .pk-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 16px; padding: 0; list-style: none; font-size: 14px; line-height: 20px; color: var(--pk-muted); }
    .pk-breadcrumb a { color: var(--pk-primary); font-weight: 500; text-decoration: none; }
    .pk-breadcrumb a:hover { text-decoration: underline; }
    .pk-breadcrumb li + li::before { content: "/"; margin-right: 8px; }
    .pk-breadcrumb [aria-current] { color: var(--pk-ink); font-weight: 600; }

    /* Header halaman */
    .pk-title { margin: 0 0 8px; font-size: 28px; line-height: 36px; font-weight: 700; color: var(--pk-ink); }
    .pk-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 20px; margin-bottom: 24px; font-size: 16px; line-height: 24px; color: var(--pk-ink-subtle); }
    .pk-meta i { margin-right: 6px; color: var(--pk-primary); }

    .pk-badge { display: inline-flex; align-items: center; padding: 2px 10px; font-size: 12px; line-height: 16px; font-weight: 600; border-radius: 999px; border: 1px solid transparent; }
    .pk-badge.is-info { color: var(--pk-primary); background: var(--pk-primary-soft); border-color: #cfdeee; }
    .pk-badge.is-success { color: #1f7a36; background: #e9f6ec; border-color: #a7d9b3; }
    .pk-badge.is-neutral { color: var(--pk-ink-subtle); background: #f3f4f6; border-color: var(--pk-border); }
    .pk-badge.is-danger { color: #c62828; background: #fdecec; border-color: #f3b4b4; }

    /* Alert dibatalkan */
    .pk-alert-danger { display: flex; gap: 12px; margin-bottom: 24px; padding: 16px; color: #c62828; background: #fdecec; border: 1px solid #f3b4b4; border-radius: var(--pk-radius-sm); }
    .pk-alert-danger strong { display: block; font-size: 16px; line-height: 24px; }
    .pk-alert-danger p { margin: 0; font-size: 14px; line-height: 20px; color: var(--pk-ink-subtle); }

    /* Layout 2 kolom */
    .pk-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr); gap: 24px; align-items: start; }
    .pk-col { display: flex; flex-direction: column; gap: 24px; min-width: 0; }

    /* Kartu INA */
    .pk-card { background: #fff; border: 1px solid var(--pk-border); border-radius: var(--pk-radius); box-shadow: var(--pk-shadow); overflow: hidden; }
    .pk-card-header { display: flex; align-items: center; gap: 8px; padding: 16px 24px; border-bottom: 1px solid var(--pk-border); font-size: 18px; line-height: 26px; font-weight: 600; color: var(--pk-ink); }
    .pk-card-header i { color: var(--pk-primary); }
    .pk-card-body { padding: 24px; }

    .pk-unit-name { margin: 0 0 4px; font-size: 18px; line-height: 26px; font-weight: 600; }
    .pk-unit-plate { display: inline-block; padding: 2px 10px; font-size: 12px; line-height: 16px; font-weight: 600; color: var(--pk-ink-subtle); background: #f3f4f6; border-radius: 999px; }
    .pk-text { margin: 12px 0 0; font-size: 16px; line-height: 24px; color: var(--pk-ink-subtle); }
    .pk-subhead { margin: 24px 0 12px; font-size: 16px; line-height: 24px; font-weight: 600; color: var(--pk-ink); }

    .pk-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .pk-gallery figure { margin: 0; }
    .galeri-thumb { display: block; width: 100%; height: 140px; object-fit: cover; border: 1px solid var(--pk-border); border-radius: var(--pk-radius-sm); }
    .pk-gallery a:focus-visible { outline: none; box-shadow: var(--pk-focus); border-radius: var(--pk-radius-sm); }
    .pk-gallery figcaption { margin-top: 6px; font-size: 12px; line-height: 16px; color: var(--pk-muted); text-align: center; }

    /* Lokasi */
    .pk-loc-name { margin: 0 0 4px; font-size: 16px; line-height: 24px; font-weight: 600; }
    .pk-loc-addr { margin: 0 0 12px; font-size: 14px; line-height: 20px; color: var(--pk-muted); }
    .pk-landmark { margin: 0 0 16px; padding: 8px 12px; font-size: 14px; line-height: 20px; color: var(--pk-ink-subtle); background: var(--pk-primary-soft); border-radius: var(--pk-radius-sm); }
    #mapDetail { height: 260px; width: 100%; margin-bottom: 16px; border: 1px solid var(--pk-border); border-radius: var(--pk-radius-sm); }
    #mapDetailModal { height: 70vh; width: 100%; }
    #mapModal .modal-body { padding: 0; }
    #mapModal .leaflet-container { border-radius: 0; }

    .pk-actions { display: flex; flex-direction: column; gap: 8px; }

    /* Tombol INA */
    .pk-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 8px 16px; font-size: 14px; line-height: 20px; font-weight: 600; border-radius: var(--pk-radius-sm); text-decoration: none; }
    .pk-btn-primary { color: #fff; background: var(--pk-primary); border: 1px solid var(--pk-primary); }
    .pk-btn-primary:hover { color: #fff; background: var(--pk-primary-hover); border-color: var(--pk-primary-hover); }
    .pk-btn-outline { color: var(--pk-primary); background: #fff; border: 1px solid var(--pk-primary); }
    .pk-btn-outline:hover { color: var(--pk-primary-hover); background: var(--pk-primary-soft); }
    .pk-btn:focus-visible { outline: none; box-shadow: var(--pk-focus); }

    /* Petugas */
    .pk-staff { margin: 0; padding: 0; list-style: none; }
    .pk-staff li { display: flex; align-items: center; gap: 12px; padding: 12px 24px; border-bottom: 1px solid var(--pk-border); }
    .pk-staff li:last-child { border-bottom: 0; }
    .pk-avatar { flex-shrink: 0; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: var(--pk-primary); background: var(--pk-primary-soft); border-radius: 50%; text-transform: uppercase; }
    .pk-staff-name { font-size: 16px; line-height: 24px; font-weight: 600; color: var(--pk-ink); }
    .pk-staff a { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; line-height: 20px; color: var(--pk-primary); text-decoration: none; }
    .pk-staff a:hover { text-decoration: underline; }
    .pk-empty { padding: 16px 24px; font-size: 14px; line-height: 20px; color: var(--pk-muted); }

    /* Modal peta */
    #mapModal .modal-content { border: 0; border-radius: var(--pk-radius); overflow: hidden; }
    #mapModal .modal-header,
    #mapModal .modal-footer { padding: 16px 24px; background: #fff; }
    #mapModal .modal-header { border-bottom: 1px solid var(--pk-border); }
    #mapModal .modal-footer { border-top: 1px solid var(--pk-border); }
    #mapModal .modal-title { font-size: 18px; line-height: 26px; font-weight: 600; color: var(--pk-ink); }
    #mapModal .modal-title i { color: var(--pk-primary); }

    @media (max-width: 991.98px) {
        .pk-detail { padding: 100px 16px 40px; }
        .pk-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 576px) {
        .pk-title { font-size: 24px; line-height: 32px; }
        .pk-card-body { padding: 16px; }
        .pk-card-header { padding: 16px; }
        .pk-staff li { padding: 12px 16px; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<div class="pk-detail">
    <nav aria-label="breadcrumb">
        <ol class="pk-breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-home me-1" aria-hidden="true"></i>Beranda</a></li>
            <li><a href="<?= site_url('perpustakaan-keliling') ?>">Perpustakaan Keliling</a></li>
            <li aria-current="page"><?= esc($jadwal->nama_lokasi) ?></li>
        </ol>
    </nav>

    <!-- Header Judul & Status -->
    <h1 class="pk-title"><?= esc($jadwal->nama_lokasi) ?></h1>
    <div class="pk-meta">
        <span><i class="fa fa-calendar-alt" aria-hidden="true"></i><?= $tanggalIndo ?></span>
        <span><i class="fa fa-clock" aria-hidden="true"></i><?= $jam ?></span>
        <span class="pk-badge <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
    </div>

    <?php if ($jadwal->status === 'dibatalkan'): ?>
        <div class="pk-alert-danger" role="alert">
            <i class="fa fa-exclamation-triangle mt-1" aria-hidden="true"></i>
            <div>
                <strong>Jadwal dibatalkan</strong>
                <p>Alasan: <?= esc($jadwal->alasan_batal ?: 'Tidak ada alasan dicantumkan.') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="pk-grid">
        <!-- Kolom kiri: unit & keterangan -->
        <div class="pk-col">
            <section class="pk-card">
                <div class="pk-card-header"><i class="fa fa-bus" aria-hidden="true"></i> Informasi Unit Kendaraan</div>
                <div class="pk-card-body">
                    <h2 class="pk-unit-name"><?= esc($jadwal->nama_unit) ?></h2>
                    <?php if (!empty($jadwal->nomor_kendaraan)): ?>
                        <span class="pk-unit-plate"><?= esc($jadwal->nomor_kendaraan) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($jadwal->deskripsi_unit)): ?>
                        <p class="pk-text"><?= esc($jadwal->deskripsi_unit) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($fotoUnit)): ?>
                        <h3 class="pk-subhead">Foto Unit Kendaraan</h3>
                        <div class="pk-gallery">
                            <?php foreach ($fotoUnit as $f): ?>
                                <a href="<?= esc($f['url'], 'attr') ?>" target="_blank" rel="noopener">
                                    <img src="<?= esc($f['url'], 'attr') ?>" class="galeri-thumb" alt="Foto unit <?= esc($jadwal->nama_unit, 'attr') ?>" loading="lazy">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if (!empty($jadwal->keterangan)): ?>
                <section class="pk-card">
                    <div class="pk-card-header"><i class="fa fa-info-circle" aria-hidden="true"></i> Catatan Kegiatan</div>
                    <div class="pk-card-body">
                        <p class="pk-text mt-0"><?= nl2br(esc($jadwal->keterangan)) ?></p>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($dokumentasi)): ?>
                <section class="pk-card">
                    <div class="pk-card-header"><i class="fa fa-camera" aria-hidden="true"></i> Dokumentasi Kegiatan</div>
                    <div class="pk-card-body">
                        <div class="pk-gallery">
                            <?php foreach ($dokumentasi as $d): ?>
                                <figure>
                                    <a href="<?= esc($d['url'], 'attr') ?>" target="_blank" rel="noopener">
                                        <img src="<?= esc($d['url'], 'attr') ?>" class="galeri-thumb" alt="<?= esc($d['keterangan'] ?: 'Dokumentasi kegiatan', 'attr') ?>" loading="lazy">
                                    </a>
                                    <?php if (!empty($d['keterangan'])): ?>
                                        <figcaption><?= esc($d['keterangan']) ?></figcaption>
                                    <?php endif; ?>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <!-- Kolom kanan: lokasi & petugas -->
        <div class="pk-col">
            <section class="pk-card">
                <div class="pk-card-header"><i class="fa fa-map-marked-alt" aria-hidden="true"></i> Lokasi &amp; Navigasi</div>
                <div class="pk-card-body">
                    <p class="pk-loc-name"><?= esc($jadwal->nama_lokasi) ?></p>
                    <p class="pk-loc-addr"><?= esc($jadwal->alamat) ?></p>

                    <?php if (!empty($jadwal->keterangan_lokasi)): ?>
                        <p class="pk-landmark"><strong>Patokan:</strong> <?= esc($jadwal->keterangan_lokasi) ?></p>
                    <?php endif; ?>

                    <div id="mapDetail" role="region" aria-label="Peta lokasi <?= esc($jadwal->nama_lokasi, 'attr') ?>"></div>

                    <div class="pk-actions">
                        <a href="<?= esc($mapsUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="pk-btn pk-btn-primary">
                            <i class="fa fa-map-marker-alt" aria-hidden="true"></i> Buka di Google Maps
                        </a>
                        <button type="button" class="pk-btn pk-btn-outline" data-bs-toggle="modal" data-bs-target="#mapModal">
                            <i class="fa fa-expand" aria-hidden="true"></i> Lihat Peta Lebih Besar
                        </button>

                        <?php if ($jadwal->status === 'aktif' && $statusTampil === 'akan_datang'): ?>
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
                            <a href="<?= esc($gCalUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="pk-btn pk-btn-outline">
                                <i class="fa fa-calendar-plus" aria-hidden="true"></i> Tambah ke Google Kalender
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="pk-card">
                <div class="pk-card-header"><i class="fa fa-user-friends" aria-hidden="true"></i> Petugas Pendamping</div>
                <?php if (!empty($petugasList)): ?>
                    <ul class="pk-staff">
                        <?php foreach ($petugasList as $p): ?>
                            <li>
                                <span class="pk-avatar" aria-hidden="true"><?= esc(mb_substr((string) $p['username'], 0, 2)) ?></span>
                                <div>
                                    <div class="pk-staff-name"><?= esc($p['username']) ?></div>
                                    <?php if ($p['tampilkan_email'] == 1 && !empty($p['email'])): ?>
                                        <div><a href="mailto:<?= esc($p['email'], 'attr') ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?= esc($p['email']) ?></a></div>
                                    <?php endif; ?>
                                    <?php if ($p['tampilkan_hp'] == 1 && !empty($p['phone'])): ?>
                                        <div><a href="tel:<?= esc($p['phone'], 'attr') ?>"><i class="fa fa-phone" aria-hidden="true"></i><?= esc($p['phone']) ?></a></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="pk-empty">Belum ada petugas terdaftar.</div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- Modal Peta -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 80vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="mapModalLabel">
                        <i class="fa fa-map-marked-alt" aria-hidden="true"></i>
                        <span><?= esc($jadwal->nama_lokasi) ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="mapDetailModal"></div>
                </div>
                <div class="modal-footer justify-content-between align-items-center">
                    <div class="small text-muted text-truncate me-3" style="max-width: 60%;" title="<?= esc($jadwal->alamat, 'attr') ?>">
                        <i class="fa fa-location-dot me-1" aria-hidden="true"></i> <?= esc($jadwal->alamat) ?>
                    </div>
                    <a href="<?= esc($mapsUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="pk-btn pk-btn-primary">
                        <i class="fa fa-map-marker-alt" aria-hidden="true"></i> Buka di Google Maps
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

        var namaLokasi = <?= json_encode((string) $jadwal->nama_lokasi, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        var alamat = <?= json_encode((string) $jadwal->alamat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        // Popup Leaflet dirender sebagai HTML: escape teks lokasi
        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, function (ch) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
            });
        }

        var popupContent = '<b>' + escapeHtml(namaLokasi) + '</b><br>' + escapeHtml(alamat);


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
