<?php
$request = service('request');

?>

<div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Edit - Format Kartu
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit" method="post" data-action="<?= base_url('api/master-format-kartu/edit') ?>" data-id="">
                <div class="modal-body">
                    <div id="frm_edit_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nama*</label>
                                <div>
                                    <input required type="text" class="form-control" id="name" name="name" placeholder="Nama" value="<?= set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="width">Panjang*</label>
                                <div>
                                    <input required type="text" class="form-control" id="width" name="width" placeholder="Panjang" value="<?= set_value('width'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="height">Lebar*</label>
                                <div>
                                    <input required type="text" class="form-control" id="height" name="height" placeholder="Lebar" value="<?= set_value('height'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="font_name">Nama Font*</label>
                                <div>
                                    <input required type="text" class="form-control" id="font_name" name="font_name" placeholder="Lebar" value="<?= set_value('font_name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="font_size">Ukuran Font*</label>
                                <div>
                                    <input required type="text" class="form-control" id="font_size" name="font_size" placeholder="Panjang" value="<?= set_value('font_size'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="format_text">Format (Ada Tag 1XX) </label>
                        <div>
                            <textarea id="format_text" name="format_text" placeholder="Format (Ada Tag 1XX)" rows="5" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('format_text') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="format_text_no_author">Format (Tidak Ada Tag 1XX) </label>
                        <div>
                            <textarea id="format_text_no_author" name="format_text_no_author" placeholder="Format (Tidak Ada Tag 1XX)" rows="5" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('format_text_no_author') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
   // Tampilkan data ke form saat klik tombol edit
   $('#tbl_data').on('click', '.edit-data', function() {
      var url = $(this).attr('data-href');
      $.ajax({
         url: url,
         type: 'get',
         dataType: 'json',
         success: function(response) {
            $('#frm_edit').attr("data-id", response.ID);
            $('#name').val(response.Name);
            $('#width').val(response.Width);
            $('#height').val(response.Height);
            $('#font_name').val(response.FontName);
            $('#font_size').val(response.FontSize);
            $('#format_text').val(response.FormatTeks);
            $('#format_text_no_author').val(response.FormatTeksNoAuthor);

            $('#modal_edit').modal('show');
         },
         error: function(xhr, status, error) {
            console.log('Error:', error);
            console.log('Response:', xhr.responseText);
         }
      });
   });

   // Submit form edit via AJAX
 $('#frm_edit').submit(function(event) {
		event.preventDefault();
		var url = $(this).data('action') + '/' + $(this).data('id');
		var data_post = $(this).serializeArray();

		$("#btnUpdate").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnUpdate").attr('disabled', true);

		$.ajax({
				url: url,
				type: 'POST',
				data: data_post,
			})
			.done(function(res) {
				console.log(res)

				if (res.error == false) {
					Swal.fire({
						title: 'Berhasil',
						html: 'Format Kartu Berhasil Diubah.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-format-kartu') ?>`;
					});
				} else {
					Swal.fire({
						title: 'Oups',
						text: res.message,
						type: 'error',
						showConfirmButton: false,
						timer: 5000
					}).then(() => {
						$("#btnUpdate").attr('disabled', false);
						$("#btnUpdate").html('Simpan');
					});
				}
			})
			.fail(function(res) {
				console.log(res);

				Swal.fire({
					title: 'Oups',
					text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
					type: 'error',
					showConfirmButton: false,
					timer: 5000
				}).then(() => {
					$("#btnUpdate").attr('disabled', false);
					$("#btnUpdate").html('Simpan');
				});
			});

		return false;
	});
</script>
