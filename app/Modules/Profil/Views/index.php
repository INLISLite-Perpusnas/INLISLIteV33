<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<style>
    .profil-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        padding: 60px 0;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .profil-hero img {
        max-height: 380px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .profil-body {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333333;
    }

    .section-title {
        position: relative;
        font-weight: 700;
        margin-bottom: 35px;
        padding-bottom: 12px;
        color: #1e3c72;
    }



    .layanan-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .layanan-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .layanan-img-container {
        height: 200px;
        overflow: hidden;
        background: #f4f6f9;
        position: relative;
    }

    .layanan-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .layanan-badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 6px;
        background: rgba(30, 60, 114, 0.08);
        color: #1e3c72;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<!-- Tambahan pt-5 mt-4 untuk memberi space dari navbar/header -->
<div class="container pt-5 mt-4 mb-5">

    <!-- Profil Section (Vertikal Layout) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-4 p-lg-5">

            <?php if (!empty($profil['image'])): ?>
                <!-- 1. Gambar Utama (Tengah) -->
                <div class="text-center mb-4">
                    <img src="<?= base_url('uploads/profil/' . $profil['image']) ?>" alt="Foto Utama Profil Perpustakaan"
                        class="img-fluid rounded-4 shadow-sm object-fit-cover"
                        style="max-width: 600px; width: 100%; max-height: 380px;">
                </div>
            <?php endif; ?>

            <!-- 2. Title & Aksen Garis (Tengah) -->
            <div class="text-center mb-4">
                <h2 class="section-title fw-bold text-primary mb-2">Profil Perpustakaan
                    <?= esc($nama_perpustakaan) ?>
                </h2>
                <div class="bg-primary rounded mx-auto" style="width: 70px; height: 4px;"></div>
            </div>

            <!-- 3. Deskripsi Profil -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="profil-body text-secondary lh-lg fs-6">
                        <?php if (!empty($profil['deskripsi'])): ?>
                            <?= $profil['deskripsi'] ?>
                        <?php else: ?>
                            <div class="alert alert-light border text-center py-4 rounded-3 text-muted" role="alert">
                                <i class="fa fa-info-circle me-2"></i>Deskripsi profil perpustakaan belum diisi.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <hr class="my-5">

    <!-- Layanan Section (Tidak diubah) -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title">Daftar Layanan Perpustakaan</h2>
        </div>
    </div>

    <?php if (!empty($layanan)): ?>
        <div class="row">
            <?php foreach ($layanan as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card layanan-card">
                        <div class="layanan-img-container">
                            <?php if (!empty($item['foto'])): ?>
                                <img src="<?= base_url('uploads/layanan/' . $item['foto']) ?>" class="layanan-img"
                                    alt="<?= esc($item['nama_layanan']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                    <i class="fa fa-image fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title font-weight-bold text-dark mb-2"><?= esc($item['nama_layanan']) ?></h5>
                            <p class="card-text text-muted flex-grow-1 mb-3">
                                <?= nl2br(esc($item['deskripsi'])) ?>
                            </p>
                            <div class="pt-2 border-top">
                                <?php if (!empty($item['nama_lokasi'])): ?>
                                    <div class="layanan-badge mb-2 w-100">
                                        <i class="fa fa-map-marker-alt text-danger"></i>
                                        <span>[<?= esc($item['kode_lokasi']) ?>] <?= esc($item['nama_lokasi']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['jam_layanan'])): ?>
                                    <div class="layanan-badge w-100">
                                        <i class="fa fa-clock text-primary"></i>
                                        <span><?= esc($item['jam_layanan']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-4">
            <i class="fa fa-info-circle fa-2x mb-2"></i>
            <p class="mb-0">Belum ada daftar layanan yang ditambahkan.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection('content'); ?>