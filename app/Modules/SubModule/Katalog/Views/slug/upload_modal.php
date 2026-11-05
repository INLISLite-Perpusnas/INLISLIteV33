<?php
/*
 * Modal unggah file (Cover / Konten Digital) bergaya INA Digital.
 * - Modal varian Task: judul, tombol Tutup, isi, aksi Batal + Simpan di kanan bawah.
 * - File Upload varian Field (unggah = aksi utama modal): seret & lepas atau "Pilih File",
 *   deskripsi format & ukuran maksimal, upload item dengan status mengunggah / berhasil / gagal,
 *   satu file: area unggah disembunyikan setelah file masuk, pesan error spesifik + solusi.
 * Alur backend tidak berubah: Dropzone -> user/do_upload (file sementara) -> hidden upload_file[uuid]
 * -> data-upload-url (mis. api/katalog/upload_cover).
 */
?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-form.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-form.css') ?>">
<style>
    #modal_upload_img .modal-dialog { max-width: 560px; background: transparent; box-shadow: none; }
    #modal_upload_img .modal-header { align-items: center; background: #fff; }
    #modal_upload_img .modal-title { margin: 0; font-size: 18px; font-weight: 600; }
    #modal_upload_img .modal-subtitle { margin: 4px 0 0; font-size: 14px; font-weight: 400; color: var(--ina-text-muted, #6b7280); }
    #modal_upload_img .close { margin: 0 0 0 auto; padding: 0; width: 36px; height: 36px; font-size: 24px; line-height: 1; border-radius: 8px; opacity: .7; }
    #modal_upload_img .close:hover { opacity: 1; background: var(--ina-hover, #f3f4f6); }
    #modal_upload_img .close:focus-visible { outline: 2px solid var(--ina-primary, #1d5fd1); outline-offset: 2px; }
    #modal_upload_img .modal-footer { justify-content: flex-end; gap: 12px; }
    #modal_upload_img .modal-footer > * { margin: 0; }

    /* Upload Field */
    .ina-upload-field {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 28px 20px;
        text-align: center;
        background: #fafbfc;
        border: 1px dashed #c3cad5;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .ina-upload-field:hover,
    .ina-upload-field.dz-drag-hover {
        border-color: var(--ina-primary, #1d5fd1);
        background: var(--ina-primary-subtle, #eef4ff);
    }
    .ina-upload-field:focus { outline: none; }
    .ina-upload-field:focus-visible {
        border-color: var(--ina-primary, #1d5fd1);
        box-shadow: 0 0 0 3px rgba(29, 95, 209, .15);
    }
    .ina-upload-field[hidden] { display: none; }
    .ina-upload-field-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        font-size: 20px;
        color: var(--ina-primary, #1d5fd1);
        background: var(--ina-primary-subtle, #eef4ff);
        border-radius: 50%;
    }
    .ina-upload-field-text { margin: 0; font-size: 15px; color: var(--ina-text, #1f2937); }
    .ina-upload-field-text strong { color: var(--ina-primary, #1d5fd1); }

    /* Upload Item dari Dropzone */
    #upload_preview_list { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
    #upload_preview_list:empty { display: none; }
    #upload_preview_list .ina-upload-thumb img[src=""],
    #upload_preview_list .ina-upload-thumb img:not([src]) { display: none; }
    #upload_preview_list .ina-upload-thumb .ina-file-icon { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 22px; color: var(--ina-text-muted, #6b7280); }
    #upload_preview_list .ina-upload-thumb img + .ina-file-icon { display: none; }
    #upload_preview_list .ina-upload-thumb img:not([src]) + .ina-file-icon { display: flex; }
    .ina-upload-progress { display: block; height: 4px; margin-top: 6px; overflow: hidden; background: var(--ina-border, #e5e7eb); border-radius: 4px; }
    .ina-upload-progress > span { display: block; width: 0; height: 100%; background: var(--ina-primary, #1d5fd1); transition: width .2s ease; }
    .dz-success .ina-upload-progress,
    .dz-error .ina-upload-progress { display: none; }
    .dz-error .ina-upload-status { display: none; }
    .ina-upload-item [data-dz-errormessage]:empty { display: none; }
    .ina-upload-item.dz-error { border-color: #f5c2c2; background: #fff8f8; }
</style>

<div class="modal fade" id="modal_upload_img" tabindex="-1" role="dialog" aria-labelledby="upload_modal_title" aria-describedby="upload_modal_desc" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="upload_modal_title">Unggah File</h2>
                    <p class="modal-subtitle" id="upload_modal_desc">Pilih satu file untuk diunggah.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Tutup" title="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload" method="post" data-action="" data-id="" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <p class="ina-error" id="frm_upload_message" role="alert" hidden><span></span></p>

                    <div class="ina-field">
                        <span class="ina-label" id="upload_file_label"><span id="upload_title_file">File</span><span class="ina-required" aria-hidden="true">*</span></span>

                        <!-- Upload Field: klik / Enter / Spasi untuk memilih, atau seret file ke sini -->
                        <div id="upload_file" class="ina-upload-field" role="button" tabindex="0" aria-labelledby="upload_file_label" aria-describedby="upload_data_format_title">
                            <span class="ina-upload-field-icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
                            <p class="ina-upload-field-text">Seret file ke sini atau <strong>Pilih File</strong></p>
                            <p class="ina-upload-desc" id="upload_data_format_title"></p>
                        </div>

                        <div id="upload_preview_list" aria-live="polite"></div>
                        <div id="upload_file_listed"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="upload_id" id="upload_id" value="">
                    <input type="hidden" name="upload_ref_id" id="upload_ref_id" value="">
                    <input type="hidden" name="upload_field" id="upload_field" value="">
                    <input type="hidden" name="upload_title" id="upload_title" value="">

                    <input type="hidden" name="upload_data_dropzone_url" id="upload_data_dropzone_url" value="">
                    <input type="hidden" name="upload_data_url" id="upload_data_url" value="">
                    <input type="hidden" name="upload_data_format" id="upload_data_format" value="">
                    <input type="hidden" name="upload_data_max_files" id="upload_data_max_files" value="">
                    <input type="hidden" name="upload_data_max_size" id="upload_data_max_size" value="">
                    <input type="hidden" name="upload_data_redirect_url" id="upload_data_redirect_url" value="">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="upload_submit" name="submit" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="upload_item_template">
    <div class="ina-upload-item dz-preview">
        <span class="ina-upload-thumb">
            <img data-dz-thumbnail alt="">
            <span class="ina-file-icon" aria-hidden="true"><i class="fas fa-file-alt"></i></span>
        </span>
        <span class="ina-upload-info">
            <span class="ina-upload-name" data-dz-name></span>
            <span class="ina-upload-meta"><span data-dz-size></span><span class="ina-upload-status"> · Mengunggah...</span></span>
            <span class="ina-upload-meta is-error" data-dz-errormessage></span>
            <span class="ina-upload-progress" aria-hidden="true"><span data-dz-uploadprogress></span></span>
        </span>
        <button type="button" class="ina-upload-remove" data-dz-remove aria-label="Hapus file" title="Hapus file"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
</template>

<script>
    (function() {
        var dz = null;
        var $modal = $('#modal_upload_img');
        var field = document.getElementById('upload_file');
        var submitBtn = document.getElementById('upload_submit');

        // ".jpg,.jpeg,.png" -> "JPG, JPEG, PNG"
        function formatText(accepted) {
            return (accepted || '').split(',').map(function(f) {
                return f.trim().replace(/^\./, '').replace(/^application\//, '').toUpperCase();
            }).filter(Boolean).join(', ');
        }

        function showMessage(message) {
            var box = document.getElementById('frm_upload_message');
            box.querySelector('span').textContent = message || '';
            box.hidden = !message;
        }

        // Tombol Simpan aktif hanya bila ada file yang berhasil diunggah
        function refreshState() {
            if (!dz) return;
            var maxFiles = dz.options.maxFiles || 1;
            var uploaded = dz.getAcceptedFiles().filter(function(f) { return f.status === Dropzone.SUCCESS; }).length;
            submitBtn.disabled = uploaded === 0 || dz.getUploadingFiles().length > 0;
            // Satu file: sembunyikan area unggah selama ada file (gagal pun bisa dihapus lalu pilih lagi)
            field.hidden = dz.files.length >= maxFiles;
        }

        function createDropzone(accepted, maxFiles, maxSize) {
            var types = formatText(accepted);
            dz = new Dropzone(field, {
                url: '<?= base_url('user/do_upload') ?>',
                paramName: 'file',
                maxFiles: maxFiles,
                maxFilesize: maxSize,
                acceptedFiles: accepted,
                headers: {'<?= csrf_header() ?>': '<?= csrf_hash() ?>'},
                previewsContainer: '#upload_preview_list',
                previewTemplate: document.getElementById('upload_item_template').innerHTML,
                thumbnailWidth: 192,
                thumbnailHeight: 112,
                createImageThumbnails: true,
                dictInvalidFileType: 'Format file tidak didukung. Gunakan ' + types + '.',
                dictFileTooBig: 'Ukuran file {{filesize}} MB melebihi batas {{maxFilesize}} MB. Kompres file atau pilih file yang lebih kecil.',
                dictResponseError: 'Gagal mengunggah (kode {{statusCode}}). Periksa koneksi lalu coba lagi.',
                dictMaxFilesExceeded: 'Hanya ' + maxFiles + ' file yang diizinkan. Hapus file sebelumnya untuk menggantinya.',
                renameFile: function(file) {
                    return new Date().getTime() + '_' + file.name.toLowerCase().replace(/ /g, '_');
                },
                init: function() {
                    this.on('addedfile', function(file) {
                        // Gambar mini gagal dimuat: tampilkan ikon file
                        var img = file.previewElement.querySelector('[data-dz-thumbnail]');
                        img.addEventListener('error', function() { img.removeAttribute('src'); });
                        showMessage('');
                        refreshState();
                    });
                    this.on('maxfilesexceeded', function(file) {
                        this.removeFile(file);
                        showMessage('Hanya ' + maxFiles + ' file yang diizinkan. Hapus file sebelumnya untuk menggantinya.');
                    });
                    this.on('success', function(file, response) {
                        file.serverName = response.data.name;
                        var name = 'upload_file[' + file.upload.uuid + ']';
                        $('#upload_file_listed').append($('<input>', { type: 'hidden', name: name, value: response.data.name }));
                        var status = file.previewElement.querySelector('.ina-upload-status');
                        status.textContent = ' · Berhasil diunggah';
                        status.parentNode.classList.add('is-success');
                        refreshState();
                    });
                    this.on('error', function(file, message) {
                        if (typeof message === 'object') {
                            message = (message.messages && message.messages.error) || 'Gagal mengunggah. Coba lagi.';
                            file.previewElement.querySelector('[data-dz-errormessage]').textContent = message;
                        }
                        refreshState();
                    });
                    this.on('complete', refreshState);
                    this.on('removedfile', function(file) {
                        // Hapus file sementara di server bila sudah sempat terunggah
                        if (file.upload && file.status === Dropzone.SUCCESS) {
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('user/do_delete') ?>',
                                headers: {'<?= csrf_header() ?>': '<?= csrf_hash() ?>'},
                                data: { name: file.serverName },
                                dataType: 'json'
                            });
                            $('input[name="upload_file[' + file.upload.uuid + ']"]').remove();
                        }
                        refreshState();
                        field.focus();
                    });
                }
            });
        }

        // Akses keyboard untuk area unggah
        field.addEventListener('keydown', function(e) {
            if ((e.key === 'Enter' || e.key === ' ') && dz) {
                e.preventDefault();
                dz.hiddenFileInput.click();
            }
        });

        $('body').on('click', '.upload-data', function() {
            var $btn = $(this);
            Dropzone.autoDiscover = false;

            var accepted = $btn.attr('data-format') || '';
            var maxFiles = parseInt($btn.attr('data-max-files'), 10) || 1;
            var maxSize = parseFloat($btn.attr('data-max-size')) || 2;
            var title = $btn.attr('data-title-file') || 'File';

            $('#upload_modal_title').text('Unggah ' + title);
            $('#upload_modal_desc').text(maxFiles > 1 ? 'Pilih file untuk diunggah.' : 'Pilih satu file untuk diunggah.');
            $('#upload_title_file').text(title);
            $('#upload_data_format_title').html('<strong>Format:</strong> ' + formatText(accepted) + ' · <strong>Ukuran maks.:</strong> ' + maxSize + ' MB');

            $('#upload_id').val($btn.attr('data-id'));
            $('#upload_ref_id').val($btn.attr('data-ref-id'));
            $('#upload_field').val($btn.attr('data-field'));
            $('#upload_title').val($btn.attr('data-title'));
            $('#upload_data_dropzone_url').val($btn.attr('data-dropzone-url'));
            $('#upload_data_url').val($btn.attr('data-upload-url'));
            $('#upload_data_format').val(accepted);
            $('#upload_data_max_files').val(maxFiles);
            $('#upload_data_max_size').val(maxSize);
            $('#upload_data_redirect_url').val($btn.attr('data-redirect-url'));

            if (dz) dz.destroy();
            createDropzone(accepted, maxFiles, maxSize);
            refreshState();
            $modal.modal('show');
        });

        $modal.on('shown.bs.modal', function() {
            field.focus();
        });

        $('#frm_upload').on('submit', function(event) {
            event.preventDefault();
            if (submitBtn.disabled) return false;

            var originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
            showMessage('');

            $.ajax({
                    url: $('#upload_data_url').val(),
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serializeArray(),
                })
                .done(function(res) {
                    if (res.status === 201) {
                        $modal.modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'File berhasil disimpan',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(function() {
                            window.location.href = $('#upload_data_redirect_url').val();
                        }, 1500);
                    } else {
                        showMessage((res.messages && res.messages.error) || 'File gagal disimpan. Coba lagi.');
                    }
                })
                .fail(function(xhr) {
                    var json = xhr.responseJSON || {};
                    showMessage((json.messages && json.messages.error) || 'File gagal disimpan. Periksa koneksi lalu coba lagi.');
                })
                .always(function() {
                    submitBtn.textContent = originalText;
                    refreshState();
                });

            return false;
        });

        // Tutup modal: bersihkan file sementara dan status
        $modal.on('hidden.bs.modal', function() {
            if (dz) {
                dz.removeAllFiles(true);
                dz.destroy();
                dz = null;
            }
            $('#upload_file_listed').empty();
            $('#upload_preview_list').empty();
            field.hidden = false;
            showMessage('');
            submitBtn.disabled = true;
        });
    })();
</script>
