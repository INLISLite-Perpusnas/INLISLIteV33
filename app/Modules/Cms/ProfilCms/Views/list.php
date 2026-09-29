<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<!-- Summernote Lite CSS (Compatible dengan Bootstrap 5) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Perbaikan Modal Overlay (Backdrop) di template AdminIgniter */
    #modal_layanan {
        z-index: 1055 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-config icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Kelola Profil & Layanan Perpustakaan
                    <div class="page-title-subheading">Pengaturan deskripsi profil dan daftar layanan perpustakaan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Beranda</a></li>
                        <li class="breadcrumb-item">CMS</li>
                        <li class="active breadcrumb-item" aria-current="page">Profil Layanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?= get_message('message'); ?>

    <!-- Card CMS Profil -->
    <div class="main-card mb-4 card">
        <div class="card-header font-weight-bold"><i class="header-icon lnr-user icon-gradient bg-plum-plate"></i> Edit
            Deskripsi Profil Perpustakaan</div>
        <div class="card-body">
            <form action="<?= base_url('cms/profil/edit') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label for="image">Gambar Utama Profil Perpustakaan</label>
                    <?php if (!empty($profil['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= base_url('uploads/profil/' . $profil['image']) ?>" alt="Gambar Profil Saat ini"
                                class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                    <small class="form-text text-muted">Format gambar: JPG, PNG, WEBP. Maksimal 2MB.</small>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Profil Perpustakaan</label>
                    <textarea name="deskripsi" id="summernote" class="form-control"
                        rows="8"><?= $profil['deskripsi'] ?? '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Profil</button>
            </form>
        </div>
    </div>

    <!-- Card CMS Layanan (DataTables) -->
    <div class="main-card mb-4 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Daftar Layanan
            Perpustakaan
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-success" id="btn_tambah_layanan">
                    <i class="fa fa-plus"></i> Tambah Layanan
                </button>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_layanan" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="40">No.</th>
                        <th width="90">Foto</th>
                        <th>Nama Layanan</th>
                        <th>Deskripsi</th>
                        <th>Lokasi Ruangan</th>
                        <th>Jam Layanan</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create / Edit Layanan -->
<div class="modal" id="modal_layanan" tabindex="-1" role="dialog" aria-labelledby="modalLayananLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('cms/profil/layanan/save') ?>" method="post" enctype="multipart/form-data"
                id="form_layanan">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="layanan_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLayananLabel">Form Layanan Perpustakaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_layanan">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_layanan" id="layanan_nama" class="form-control" required
                            placeholder="Contoh: Layanan Sirkulasi">
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto Layanan</label>
                        <div id="container_preview_foto" class="mb-2" style="display: none;">
                            <img src="" id="preview_foto" class="img-thumbnail" style="max-height: 120px;">
                        </div>
                        <input type="file" name="foto" id="layanan_foto" class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="layanan_deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="layanan_deskripsi" class="form-control" rows="3"
                            placeholder="Deskripsi singkat mengenai layanan ini..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_id">Lokasi Ruang</label>
                                <select name="location_id" id="layanan_location_id" class="form-control">
                                    <option value="">-- Pilih Lokasi Ruang --</option>
                                    <?php if (!empty($lokasiList)): ?>
                                        <?php foreach ($lokasiList as $lok): ?>
                                            <option value="<?= $lok->ID ?>">[<?= esc($lok->Code) ?>] <?= esc($lok->Name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="layanan_jam">Jam Layanan</label>
                                <input type="text" name="jam_layanan" id="layanan_jam" class="form-control"
                                    placeholder="Contoh: Senin - Jumat: 08.00 - 16.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script>
    if (typeof jQuery !== 'undefined' && !jQuery.now) {
        jQuery.now = function () {
            return Date.now();
        };
    }
</script>

<!-- 2. Baru panggil Library Summernote Lite -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

<script>
    var tblLayanan;

    $(document).ready(function () {
        <?php if (session()->getFlashdata('swal_icon')): ?>
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: '<?= session()->getFlashdata('swal_icon') ?>',
                    title: '<?= session()->getFlashdata('swal_title') ?>',
                    text: '<?= session()->getFlashdata('swal_text') ?>',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        <?php endif; ?>

        if ($.fn.summernote) {
            $('#summernote').summernote({
                height: 430,
                minHeight: null,
                maxHeight: null,
                focus: true,
                toolbar: [
                    ['style', ['style', 'undo', 'redo', 'codeview']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph', 'table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                ],
                fontNames: ['System Font',
                    'Dosis', 'Andale Mono', 'Arial', 'Arial Black', 'Book Antiqua',
                    'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact',
                    'Symbol', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'
                ],
                fontSizes: [
                    '12', '13', '14', '15', '16', '17', '18', '19', '20', '24',
                    '28', '32', '34', '36', '72'
                ],
                styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                callbacks: {
                    onInit: function () {
                        console.log('Summernote is initialized on #content');
                    }
                },
            });
        }

        tblLayanan = $('#tbl_layanan').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('cms/profil/layanan/datatable') ?>"
            },
            "columns": [
                { data: 'no', orderable: false },
                { data: 'foto', orderable: false },
                { data: 'nama_layanan' },
                { data: 'deskripsi' },
                { data: 'lokasi' },
                { data: 'jam_layanan' },
                { data: 'action', orderable: false }
            ]
        });

        // Pindahkan modal ke body di awal sekali agar instant dan bebas dari z-index stacking context
        $('#modal_layanan').appendTo("body");

        $('#btn_tambah_layanan').click(function () {
            $('#form_layanan')[0].reset();
            $('#layanan_id').val('');
            $('#layanan_location_id').val('');
            $('#container_preview_foto').hide();
            $('#modalLayananLabel').text('Tambah Layanan Perpustakaan');
            $('#modal_layanan').modal('show');
        });

        $('body').on('click', '.edit-layanan', function () {
            var id = $(this).data('id');
            $.ajax({
                url: "<?= base_url('cms/profil/layanan/get/') ?>" + id,
                type: "GET",
                dataType: "JSON",
                success: function (res) {
                    if (res.status) {
                        var d = res.data;
                        $('#layanan_id').val(d.id);
                        $('#layanan_nama').val(d.nama_layanan);
                        $('#layanan_deskripsi').val(d.deskripsi);
                        $('#layanan_location_id').val(d.location_id || '');
                        $('#layanan_jam').val(d.jam_layanan);

                        if (d.foto) {
                            $('#preview_foto').attr('src', "<?= base_url('uploads/layanan/') ?>/" + d.foto);
                            $('#container_preview_foto').show();
                        } else {
                            $('#container_preview_foto').hide();
                        }

                        $('#modalLayananLabel').text('Edit Layanan Perpustakaan');
                        $('#modal_layanan').modal('show');
                    }
                }
            });
        });

        $("body").on("click", ".remove-data", function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data layanan akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
                    window.location.href = url;
                }
            }
        });
    });
</script>
<?= $this->endSection('script'); ?>