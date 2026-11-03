<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style'); ?>
<style>
    /* Profil Perpustakaan - gaya INA Digital (Inter, skala 28/36, 18/26, 16/24, 14/20, 12/16; biru #2d659e) */
    :root {
        --pf-primary: #2d659e;
        --pf-primary-hover: #24527f;
        --pf-primary-soft: #eef4fa;
        --pf-ink: #1f2937;
        --pf-ink-subtle: #374151;
        --pf-muted: #6b7280;
        --pf-border: #e5e7eb;
        --pf-bg: #f7f7f8;
        --pf-radius: 12px;
        --pf-radius-sm: 8px;
        --pf-shadow: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .04);
    }

    body { background: var(--pf-bg); }

    .profil-page {
        max-width: 1600px;
        margin: 0 auto;
        padding: 110px 2rem 48px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--pf-ink);
    }

    /* Breadcrumb INA: teks polos */
    .pf-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 0 16px;
        padding: 0;
        list-style: none;
        font-size: 14px;
        line-height: 20px;
        color: var(--pf-muted);
    }
    .pf-breadcrumb a { color: var(--pf-primary); font-weight: 500; text-decoration: none; }
    .pf-breadcrumb a:hover { text-decoration: underline; }
    .pf-breadcrumb li + li::before { content: "/"; margin-right: 8px; color: var(--pf-muted); }
    .pf-breadcrumb [aria-current] { color: var(--pf-ink); font-weight: 600; }

    /* Judul halaman: H4 */
    .pf-title { margin: 0 0 4px; font-size: 28px; line-height: 36px; font-weight: 700; color: var(--pf-ink); }
    .pf-subtitle { margin: 0 0 24px; font-size: 16px; line-height: 24px; color: var(--pf-muted); }

    /* Kartu */
    .pf-card {
        background: #fff;
        border: 1px solid var(--pf-border);
        border-radius: var(--pf-radius);
        box-shadow: var(--pf-shadow);
        overflow: hidden;
    }
    .pf-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--pf-border);
        font-size: 18px;
        line-height: 26px;
        font-weight: 600;
        color: var(--pf-ink);
    }
    .pf-card-header i { color: var(--pf-primary); }
    .pf-card-body { padding: 24px; }

    /* Profil: gambar + deskripsi berdampingan di layar lebar */
    .pf-profile {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 3fr);
        gap: 32px;
        align-items: start;
    }
    .pf-profile.no-image { grid-template-columns: 1fr; }
    .pf-profile-img {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        border-radius: var(--pf-radius-sm);
        border: 1px solid var(--pf-border);
    }

    /* Isi deskripsi (rich text dari admin) */
    .pf-richtext { font-size: 16px; line-height: 26px; color: var(--pf-ink-subtle); }
    .pf-richtext h1, .pf-richtext h2, .pf-richtext h3 { font-size: 18px; line-height: 26px; font-weight: 600; color: var(--pf-ink); margin: 24px 0 8px; }
    .pf-richtext h4, .pf-richtext h5, .pf-richtext h6 { font-size: 16px; line-height: 24px; font-weight: 600; color: var(--pf-ink); margin: 16px 0 8px; }
    .pf-richtext > :first-child { margin-top: 0; }
    .pf-richtext p { margin: 0 0 16px; }
    .pf-richtext a { color: var(--pf-primary); }
    .pf-richtext img { max-width: 100%; height: auto; border-radius: var(--pf-radius-sm); }
    .pf-richtext table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 14px; line-height: 20px; }
    .pf-richtext th, .pf-richtext td { padding: 8px 12px; border: 1px solid var(--pf-border); text-align: left; vertical-align: top; }
    .pf-richtext th { background: #f9fafb; font-weight: 600; }

    /* Empty state INA */
    .pf-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 40px 24px;
        text-align: center;
        color: var(--pf-muted);
        font-size: 14px;
        line-height: 20px;
    }
    .pf-empty i { font-size: 32px; color: #9ca3af; }
    .pf-empty strong { font-size: 16px; line-height: 24px; color: var(--pf-ink); font-weight: 600; }

    /* Layanan */
    .pf-section-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin: 32px 0 16px;
    }
    .pf-section-title { margin: 0; font-size: 24px; line-height: 32px; font-weight: 700; color: var(--pf-ink); }
    .pf-count {
        display: inline-block;
        padding: 2px 10px;
        font-size: 12px;
        line-height: 16px;
        font-weight: 600;
        color: var(--pf-primary);
        background: var(--pf-primary-soft);
        border-radius: 999px;
    }

    .pf-layanan-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }
    @media (max-width: 991.98px) { .pf-layanan-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 576px) { .pf-layanan-grid { grid-template-columns: 1fr; } }
    .layanan-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #fff;
        border: 1px solid var(--pf-border);
        border-radius: var(--pf-radius);
        box-shadow: var(--pf-shadow);
        overflow: hidden;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .layanan-card:hover { border-color: var(--pf-primary); box-shadow: 0 4px 12px rgba(16, 24, 40, .08); }
    .layanan-img-container {
        height: 200px;
        overflow: hidden;
        background: #f3f4f6;
        border-bottom: 1px solid var(--pf-border);
    }
    .layanan-img { width: 100%; height: 100%; object-fit: cover; }
    .layanan-img-empty {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 32px;
    }
    .layanan-body { display: flex; flex-direction: column; flex: 1; padding: 16px 20px 20px; }
    .layanan-title { margin: 0 0 8px; font-size: 18px; line-height: 26px; font-weight: 600; color: var(--pf-ink); }
    .layanan-desc { flex: 1; margin: 0 0 16px; font-size: 14px; line-height: 20px; color: var(--pf-muted); }
    .layanan-meta { display: flex; flex-direction: column; gap: 8px; padding-top: 12px; border-top: 1px solid var(--pf-border); }
    .layanan-badge {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        line-height: 20px;
        color: var(--pf-ink-subtle);
    }
    .layanan-badge i { width: 16px; margin-top: 2px; color: var(--pf-primary); text-align: center; }

    @media (max-width: 991.98px) {
        .profil-page { padding: 100px 16px 40px; }
        .pf-profile { grid-template-columns: 1fr; gap: 20px; }
    }

    @media (max-width: 576px) {
        .pf-title { font-size: 24px; line-height: 32px; }
        .pf-card-body { padding: 16px; }
        .pf-card-header { padding: 16px; }
        .pf-section-title { font-size: 20px; line-height: 28px; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<div class="profil-page">

    <nav aria-label="breadcrumb">
        <ol class="pf-breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fas fa-home me-1" aria-hidden="true"></i>Beranda</a></li>
            <li aria-current="page">Profil</li>
        </ol>
    </nav>

    <h1 class="pf-title">Profil Perpustakaan</h1>
    <p class="pf-subtitle"><?= esc($nama_perpustakaan) ?></p>

    <!-- Profil -->
    <section class="pf-card" aria-labelledby="pf-about-title">
        <div class="pf-card-header" id="pf-about-title">
            <i class="fas fa-landmark" aria-hidden="true"></i> Tentang Perpustakaan
        </div>
        <div class="pf-card-body">
            <div class="pf-profile<?= empty($profil['image']) ? ' no-image' : '' ?>">
                <?php if (!empty($profil['image'])): ?>
                    <img src="<?= base_url('uploads/profil/' . $profil['image']) ?>" alt="Foto <?= esc($nama_perpustakaan, 'attr') ?>"
                        class="pf-profile-img">
                <?php endif; ?>

                <div>
                    <?php if (!empty($profil['deskripsi'])): ?>
                        <div class="pf-richtext">
                            <?= $profil['deskripsi'] ?>
                        </div>
                    <?php else: ?>
                        <div class="pf-empty" role="status">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <strong>Deskripsi profil belum diisi</strong>
                            <span>Informasi tentang perpustakaan akan tampil di sini.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan -->
    <div class="pf-section-head">
        <h2 class="pf-section-title">Daftar Layanan Perpustakaan</h2>
        <?php if (!empty($layanan)): ?>
            <span class="pf-count"><?= count($layanan) ?> layanan</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($layanan)): ?>
        <div class="pf-layanan-grid">
            <?php foreach ($layanan as $item): ?>
                <article class="layanan-card">
                    <div class="layanan-img-container">
                        <?php if (!empty($item['foto'])): ?>
                            <img src="<?= base_url('uploads/layanan/' . $item['foto']) ?>" class="layanan-img"
                                alt="<?= esc($item['nama_layanan'], 'attr') ?>" loading="lazy">
                        <?php else: ?>
                            <div class="layanan-img-empty"><i class="fas fa-image" aria-hidden="true"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="layanan-body">
                        <h3 class="layanan-title"><?= esc($item['nama_layanan']) ?></h3>
                        <p class="layanan-desc"><?= nl2br(esc($item['deskripsi'])) ?></p>
                        <?php if (!empty($item['nama_lokasi']) || !empty($item['jam_layanan'])): ?>
                            <div class="layanan-meta">
                                <?php if (!empty($item['nama_lokasi'])): ?>
                                    <div class="layanan-badge">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        <span>[<?= esc($item['kode_lokasi']) ?>] <?= esc($item['nama_lokasi']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['jam_layanan'])): ?>
                                    <div class="layanan-badge">
                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                        <span><?= esc($item['jam_layanan']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="pf-card">
            <div class="pf-empty" role="status">
                <i class="fas fa-concierge-bell" aria-hidden="true"></i>
                <strong>Belum ada layanan</strong>
                <span>Daftar layanan perpustakaan akan tampil di sini setelah ditambahkan.</span>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection('content'); ?>
