/* Import Excel buku tamu admin: satu file, seret atau pilih, lalu unggah. */
(function () {
    'use strict';

    var MAX_SIZE = 5 * 1024 * 1024;
    var modal = document.getElementById('gb_import_modal');
    if (!modal) {
        return;
    }

    // Di luar pembungkus halaman, agar tidak tertutup backdrop
    document.body.appendChild(modal);

    var byId = function (id) { return document.getElementById(id); };
    var field = byId('gb_upload_field');
    var input = byId('gb_upload_input');
    var pick = byId('gb_upload_pick');
    var item = byId('gb_upload_item');
    var nameEl = byId('gb_upload_name');
    var desc = byId('gb_upload_desc');
    var progress = byId('gb_upload_progress');
    var bar = byId('gb_upload_bar');
    var removeBtn = byId('gb_upload_remove');
    var errorList = byId('gb_import_errors');
    var cancel = byId('gb_import_cancel');
    var submit = byId('gb_import_submit');

    var selected = null;
    var finished = false;
    var request = null;

    function formatSize(bytes) {
        return bytes < 1024 * 1024
            ? Math.max(1, Math.round(bytes / 1024)) + ' KB'
            : (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function setFieldDisabled(disabled) {
        field.classList.toggle('is-disabled', disabled);
        pick.disabled = disabled;
    }

    function showErrors(errors) {
        errorList.textContent = '';
        (errors || []).forEach(function (message) {
            var li = document.createElement('li');
            li.textContent = message;
            errorList.appendChild(li);
        });
        errorList.hidden = errorList.children.length === 0;
    }

    function setItem(state, text) {
        item.hidden = false;
        item.dataset.state = state;
        desc.textContent = text;
        progress.hidden = state !== 'uploading';
        removeBtn.hidden = state === 'uploading' || state === 'success';
    }

    function reset() {
        if (request) {
            request.abort();
            request = null;
        }
        selected = null;
        finished = false;
        input.value = '';
        item.hidden = true;
        item.dataset.state = 'idle';
        bar.style.width = '0';
        showErrors([]);
        setFieldDisabled(false);
        cancel.hidden = false;
        cancel.disabled = false;
        submit.textContent = 'Import';
        submit.disabled = true;
    }

    function chooseFile(file) {
        if (!file || field.classList.contains('is-disabled')) {
            return;
        }

        reset();
        nameEl.textContent = file.name;
        setFieldDisabled(true);

        if (!/\.(xlsx|xls)$/i.test(file.name)) {
            setItem('error', 'File harus berformat Excel (.xlsx atau .xls). Hapus file ini, lalu pilih file lain.');
            return;
        }
        if (file.size > MAX_SIZE) {
            setItem('error', 'Ukuran file ' + formatSize(file.size) + ', maksimal 5 MB. Bagi data menjadi beberapa file.');
            return;
        }

        selected = file;
        setItem('selected', formatSize(file.size));
        submit.disabled = false;
    }

    function finishFailed(message, errors) {
        request = null;
        selected = null;
        setItem('error', message);
        showErrors(errors);
        cancel.disabled = false;
        submit.textContent = 'Import';
        submit.disabled = true;
    }

    function upload() {
        var body = new FormData();
        body.append('excel_file', selected);
        body.append(modal.dataset.csrfName, modal.dataset.csrfHash);

        setItem('uploading', 'Mengunggah dan memeriksa data...');
        showErrors([]);
        bar.style.width = '0';
        submit.disabled = true;
        submit.textContent = 'Memproses...';
        cancel.disabled = true;

        request = new XMLHttpRequest();
        request.open('POST', modal.dataset.url);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        request.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                bar.style.width = Math.round(event.loaded / event.total * 100) + '%';
            }
        };
        request.onerror = function () {
            finishFailed('Koneksi terputus. Periksa jaringan, hapus file ini, lalu coba lagi.');
        };
        request.onload = function () {
            var data = null;
            try {
                data = JSON.parse(request.responseText);
            } catch (e) {
                data = null;
            }

            if (request.status === 200 && data && data.status === 'success') {
                request = null;
                selected = null;
                finished = true;
                setItem('success', data.message);
                cancel.disabled = false;
                cancel.hidden = true;
                submit.textContent = 'Selesai';
                submit.disabled = false;
                if (window.t && window.t.ajax) {
                    window.t.ajax.reload(null, false);
                }
                return;
            }

            finishFailed(
                data && data.message ? data.message : 'Terjadi kendala di server. Muat ulang halaman, lalu coba lagi.',
                data && data.errors
            );
        };
        request.send(body);
    }

    pick.addEventListener('click', function () {
        input.click();
    });
    input.addEventListener('change', function () {
        chooseFile(input.files[0]);
    });
    removeBtn.addEventListener('click', function () {
        reset();
        pick.focus();
    });
    submit.addEventListener('click', function () {
        if (finished) {
            cancel.hidden = false;
            cancel.click();
        } else if (selected) {
            upload();
        }
    });

    ['dragenter', 'dragover'].forEach(function (type) {
        field.addEventListener(type, function (event) {
            event.preventDefault();
            if (!field.classList.contains('is-disabled')) {
                field.classList.add('is-dragover');
            }
        });
    });
    ['dragleave', 'drop'].forEach(function (type) {
        field.addEventListener(type, function (event) {
            event.preventDefault();
            field.classList.remove('is-dragover');
        });
    });
    field.addEventListener('drop', function (event) {
        chooseFile(event.dataTransfer.files[0]);
    });

    // Selama proses, modal tidak boleh tertutup agar hasilnya tidak terlewat
    modal.addEventListener('hide.bs.modal', function (event) {
        if (request) {
            event.preventDefault();
        }
    });
    modal.addEventListener('hidden.bs.modal', reset);
})();
