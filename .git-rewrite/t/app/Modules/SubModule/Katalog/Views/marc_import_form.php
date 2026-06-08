<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-cloud-upload icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Impor Katalog dari File MARC
                    <div class="page-title-subheading">Upload file dengan ekstensi .mrc untuk membuat katalog baru.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">Formulir Impor File .mrc</h5>
                    
                    <form id="import-form" enctype="multipart/form-data">
                        <div class="position-relative form-group">
                            <label for="marc_file" class="">Pilih File MARC (.mrc)</label>
                            <input name="marc_file" id="marc_file" type="file" class="form-control-file" accept=".mrc">
                            <small class="form-text text-muted">Hanya file dengan ekstensi .mrc yang diizinkan.</small>
                        </div>
                        <button type="submit" class="mt-2 btn btn-primary">
                            <i class="fa fa-upload"></i> Impor Katalog
                        </button>
                    </form>
                    
                    <div id="response-message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('import-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const messageDiv = document.getElementById('response-message');
        const submitButton = form.querySelector('button[type="submit"]');

        if (!document.getElementById('marc_file').files.length) {
            messageDiv.innerHTML = '<div class="alert alert-danger">Silakan pilih file terlebih dahulu.</div>';
            return;
        }

        messageDiv.innerHTML = '<div class="alert alert-info">Mengunggah dan memproses file...</div>';
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

        fetch('<?= site_url("katalog/create-marc-from-file") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success"><strong>Berhasil!</strong> Katalog baru dibuat dengan ID: <strong>${data.catalog_id}</strong>. Judul: ${data.title}</div>`;
                form.reset();
            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger"><strong>Gagal!</strong> ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.innerHTML = `<div class="alert alert-danger"><strong>Error!</strong> Terjadi kesalahan pada sistem. Silakan cek console.</div>`;
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fa fa-upload"></i> Impor Katalog';
        });
    });
});
</script>
<?= $this->endSection('script'); ?>