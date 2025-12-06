<?= $this->extend('App\Views\layout\opac\layout'); ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url() ?>">
                        <i class="fas fa-home me-1"></i>Beranda
                    </a>
                </li>
                <li class="breadcrumb-item active"><a href="<?= base_url('news') ?>">News</a></li>
                <li class="breadcrumb-item active">Detail News</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($news->file_cover)): ?>
                <img src="<?= base_url('uploads/berita/' . $news->file_cover) ?>" class="img-fluid rounded-3 mb-4 shadow-sm" alt="<?= esc($news->title) ?>">
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                <div>
                    <small> <?= date('d M Y', strtotime($news->created_at)) ?></small>
                    <span class="mx-2">•</span>
                    <small> <strong><?= esc($news->username) ?></strong></small>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-share-alt me-2"></i>Bagikan
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" onclick="shareViaEmail()">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="shareViaWhatsapp()">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="copyLink()">
                                <i class="fas fa-link me-2"></i>Salin Link
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <hr class="mb-4">

            <h1 class="mb-4 display-5 fw-bold"><?= esc($news->title) ?></h1>
            <div class="lead">
                <?= $news->content ?>
            </div>

            <?php if (!empty($news->file_image)): ?>
                <?php $galleryImages = explode(',', $news->file_image); ?>
                <hr class="mt-5 mb-4">
                <h3 class="mb-4">Galeri Foto</h3>
                <div class="row">
                    <?php foreach ($galleryImages as $key => $image): ?>
                        <!-- Memastikan tidak ada spasi di awal/akhir nama file -->
                        <?php $image = trim($image); ?>
                        <div class="col-12 col-md-4 mb-4">
                            <img src="<?= base_url('uploads/berita/' . esc($image)) ?>" class="img-fluid rounded-3 shadow-sm" alt="Galeri <?= $key + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    // Ambil URL dan Judul berita saat ini dari variabel PHP
    const currentUrl = "<?= current_url() ?>";
    const newsTitle = "<?= esc($news->title) ?>";

    /**
     * Menyalin URL saat ini ke clipboard.
     * Menggunakan navigator.clipboard.writeText jika tersedia,
     * jika tidak, menggunakan cara lama (execCommand).
     */
    function copyLink() {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(currentUrl).then(() => {
                alert('Tautan berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin tautan: ', err);
                alert('Gagal menyalin tautan.');
            });
        } else {
            // Fallback untuk browser lama
            const textArea = document.createElement("textarea");
            textArea.value = currentUrl;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                alert('Tautan berhasil disalin ke clipboard!');
            } catch (err) {
                console.error('Fallback: Gagal menyalin tautan: ', err);
                alert('Gagal menyalin tautan.');
            }
            document.body.removeChild(textArea);
        }
    }

    /**
     * Membuka client email dengan subjek dan isi yang sudah terisi.
     */
    function shareViaEmail() {
        const subject = encodeURIComponent(`Berita menarik: ${newsTitle}`);
        const body = encodeURIComponent(`Halo, saya ingin berbagi berita ini dengan Anda:\n\n${newsTitle}\n${currentUrl}`);
        window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
    }

    /**
     * Membuka aplikasi WhatsApp dengan pesan yang sudah terisi.
     */
    function shareViaWhatsapp() {
        const text = encodeURIComponent(`${newsTitle} - ${currentUrl}`);
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    }
</script>

<?= $this->endSection('content') ?>