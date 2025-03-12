<?= $this->extend('Layout\Views\backend\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
      <div class="app-page-title">
            <div class="page-title-wrapper">
                  <div class="page-title-heading">
                        <div class="page-title-icon">
                              <i class="pe-7s-user icon-gradient bg-strong-bliss"></i>
                        </div>
                        <div>Ubah Avatar
                              <div class="page-title-subheading">Ubah Avatar akun Anda</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('user') ?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="active breadcrumb-item" aria-current="page">Ubah Avatar</li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Ubah Avatar
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>
                  <form id="frm_change_password" class="col-md-12 mx-auto" method="post" action="<?= base_url('user/change_avatar/'); ?>">
                  <div class="form-row">
                              <div class="col-md-12">
                                    <div class="form-row">
                                          <div class="col-md-12">
                                                <div class="position-relative form-group">
                                                      <label for="file_image" class="">Foto Avatar</label>
                                                      <div id="dropzone_file_image" class="dropzone"></div>
                                                      <div id="dropzone_file_image_listed"></div>
                                                      <div>
                                                            <small class="info help-block text-muted">Format (JPG|PNG). Max 10 MB</small>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <?php echo form_hidden('id', get_user_id()); ?>
                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                  </form>
            </div>
      </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
      Dropzone.autoDiscover = false;

      var dropzone_file_image = new Dropzone("#dropzone_file_image", {
            url: "<?= base_url('user/do_upload') ?>", // /do_uploads if multiple
            paramName: "file", // files if multiple
            maxFiles: 1,
            maxFilesize: 10,
            addRemoveLinks: true,
            acceptedFiles: 'image/*',
            renameFile: function(file) {
                  return new Date().getTime() + '_' + file.name.toLowerCase().replace(' ', '_');
            },
            accept: function(file, done) {
                  console.log("uploaded");
                  done();
            },
            init: function() {
            this.on("maxfilesexceeded", function(file) {
                  console.log("max file");
            });
            thisDropzone = this;

            var existingFile = "<?= $user->file_image ?>";
            if (existingFile) {
                  var files = existingFile.split(',');
                  files.forEach(function(file) {
                        var uuid = Date.now();
                        var modulePath = "<?= base_url('uploads/user/') ?>";
                        var filePath = modulePath + '/' + file;
                        var mockFile = {
                        name: file,
                        size: 68000
                        };
                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, filePath); {
                        $('[data-dz-thumbnail]').css('height', '120');
                        $('[data-dz-thumbnail]').css('width', '120');
                        $('[data-dz-thumbnail]').css('object-fit', 'cover');
                        };
                        $(thisDropzone.previewsContainer).find('.dz-progress').hide();
                        $('#file_image_listed').append('<input type="hidden" name="file_image[' + uuid + ']" value="' + file + '" />');
                  });
            }
            },
            success: function(file, response) {
                  console.log(file);
                  console.log(response);
                  // file.previewElement.querySelector("img").src = response.files[0].url;
                  // file.previewElement.classList.add("dz-success");
                  // var fileuploded = file.previewElement.querySelector("[data-dz-name]");
                  // fileuploded.innerHTML = response.files[0].name;
                  // file.name = response.files[0].name;

                  var uuid = file.upload.uuid;
                  var name = file.upload.filename;

                  $('#dropzone_file_image_listed').append('<input type="hidden" name="file_image[' + uuid + ']" value="' + name + '" />');
            },
            removedfile: function(file) {
            console.log(file);
            var name = "";
            var path = "<?= WRITEPATH . 'uploads/' ?>";
            if (file.upload !== undefined) {
                  name = file.upload.filename;
            } else {
                  name = file.name;
                  path = "<?= ROOTPATH . 'public/uploads/user/' ?>";
            }

            $.ajax({
                  type: 'POST',
                  url: "<?= base_url('user/do_delete') ?>",
                  data: "name=" + name + "&path=" + path,
                  dataType: 'html'
            });
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }

      });
</script>
<?= $this->endSection('script'); ?>