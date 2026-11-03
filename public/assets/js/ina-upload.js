/**
 * File Upload bergaya INA Digital (varian Upload Button di dalam form).
 *
 * Markup: <div class="ina-upload" data-accept="jpg,jpeg,png" data-max-mb="2" ...> ... </div>
 * Atribut:
 *  - data-required="1"        : wajib ada file saat kirim form
 *  - data-multiple="1"        : boleh banyak file (galeri)
 *  - data-current-url/-name   : file tersimpan (satu file, form ubah)
 *  - data-existing='[{"name","url"}]' + data-remove-name="remove_gallery[]"
 *                             : file tersimpan (banyak file); tombol hapus menandai file untuk dibuang
 *  - data-error               : pesan error dari server
 * Perilaku sesuai panduan:
 *  - Pilih lewat tombol atau drag & drop; validasi format & ukuran dengan pesan spesifik + solusi
 *  - Satu file: setelah dipilih, tombol unggah dinonaktifkan sampai file dihapus
 *  - Daftar menampilkan maksimal 5 upload item; sisanya lewat "Lihat Selengkapnya"
 */
(function () {
    var MAX_VISIBLE = 5;

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1).replace('.', ',') + ' MB';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function init(root) {
        var input = root.querySelector('.ina-upload-input');
        var base = root.querySelector('.ina-upload-base');
        var button = root.querySelector('.ina-upload-btn');
        var list = root.querySelector('.ina-upload-list');
        var error = root.querySelector('.ina-error');
        var field = root.closest('.ina-field');
        var form = root.closest('form');
        var accept = (root.dataset.accept || '').split(',').map(function (e) { return e.trim().toLowerCase(); }).filter(Boolean);
        var acceptText = accept.join(', ').toUpperCase();
        var maxBytes = parseFloat(root.dataset.maxMb || '0') * 1024 * 1024;
        var label = root.dataset.label || 'File';
        var multiple = root.dataset.multiple === '1';
        var btnText = button.innerHTML;

        var selected = [];   // file baru yang valid
        var invalid = [];    // file yang ditolak: {name, message}
        var existing = [];   // file tersimpan: {name, url, removed}
        var expanded = false;

        if (multiple && root.dataset.existing) {
            try {
                existing = JSON.parse(root.dataset.existing).map(function (f) { return { name: f.name, url: f.url, removed: false }; });
            } catch (e) {
                existing = [];
            }
        } else if (!multiple && root.dataset.currentUrl) {
            existing = [{ name: root.dataset.currentName || label, url: root.dataset.currentUrl, removed: false }];
        }

        // Wadah input tersembunyi untuk file tersimpan yang ditandai hapus
        var removeBox = document.createElement('div');
        removeBox.hidden = true;
        root.appendChild(removeBox);

        function setError(message) {
            error.querySelector('span').textContent = message || '';
            error.hidden = !message;
            if (field) field.classList.toggle('has-error', !!message);
            if (message) button.setAttribute('aria-invalid', 'true'); else button.removeAttribute('aria-invalid');
        }

        function validate(file) {
            var ext = (file.name.split('.').pop() || '').toLowerCase();
            if (accept.length && accept.indexOf(ext) === -1) {
                return 'Format file tidak didukung. Gunakan ' + acceptText + '.';
            }
            if (maxBytes && file.size > maxBytes) {
                return 'Ukuran file ' + formatSize(file.size) + ' melebihi batas ' + root.dataset.maxMb + ' MB. Kompres gambar atau pilih file yang lebih kecil.';
            }
            return '';
        }

        // Samakan isi <input type="file"> dengan daftar file yang valid
        function syncInput() {
            try {
                var dt = new DataTransfer();
                selected.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
            } catch (e) {
                if (!selected.length) input.value = '';
            }
        }

        function itemHtml(item, index) {
            var cls = 'ina-upload-item' + (item.error ? ' is-error' : '') + (item.removed ? ' is-removed' : '');
            var thumb = item.error
                ? '<span class="ina-upload-thumb is-error" aria-hidden="true">!</span>'
                : '<span class="ina-upload-thumb"><img src="' + escapeHtml(item.src) + '" alt="" onerror="this.style.visibility=\'hidden\'"></span>';
            var metaCls = item.error || item.removed ? 'is-error' : 'is-success';
            var action = '';
            if (item.action) {
                action = '<button type="button" class="ina-upload-remove' + (item.removed ? ' is-undo' : '') + '" data-kind="' + item.kind + '" data-index="' + index + '"' +
                    ' aria-label="' + escapeHtml(item.actionLabel) + '" title="' + escapeHtml(item.actionLabel) + '">' +
                    '<i class="fas ' + (item.removed ? 'fa-undo' : 'fa-times') + '" aria-hidden="true"></i></button>';
            }
            return '<div class="' + cls + '">' + thumb +
                '<span class="ina-upload-info">' +
                    '<span class="ina-upload-name" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</span>' +
                    '<span class="ina-upload-meta ' + metaCls + '">' + escapeHtml(item.meta) + '</span>' +
                '</span>' + action + '</div>';
        }

        function render() {
            var items = [];
            var hasNew = selected.length > 0;

            existing.forEach(function (f, i) {
                // Satu file: file tersimpan disembunyikan bila sudah dipilih pengganti
                if (!multiple && hasNew) return;
                items.push({
                    kind: 'existing', index: i, src: f.url, name: f.name, removed: f.removed,
                    meta: f.removed ? 'Akan dihapus saat disimpan' : 'File tersimpan',
                    action: multiple, actionLabel: f.removed ? 'Batalkan hapus ' + f.name : 'Hapus ' + f.name
                });
            });
            selected.forEach(function (f, i) {
                items.push({
                    kind: 'new', index: i, src: URL.createObjectURL(f), name: f.name,
                    meta: formatSize(f.size) + ' · Siap diunggah saat disimpan',
                    action: true, actionLabel: 'Hapus file ' + f.name
                });
            });
            invalid.forEach(function (f, i) {
                items.push({
                    kind: 'invalid', index: i, name: f.name, error: true, meta: 'Gagal: ' + f.message,
                    action: true, actionLabel: 'Hapus file ' + f.name
                });
            });

            var html = items.map(function (item, n) {
                var hidden = !expanded && n >= MAX_VISIBLE;
                return hidden ? itemHtml(item, item.index).replace('<div class="ina-upload-item', '<div hidden class="ina-upload-item') : itemHtml(item, item.index);
            }).join('');

            if (items.length > MAX_VISIBLE) {
                html += '<button type="button" class="ina-upload-more" aria-expanded="' + expanded + '">' +
                    (expanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Selengkapnya (' + (items.length - MAX_VISIBLE) + ' lainnya)') + '</button>';
            }
            list.innerHTML = html;

            // Input tersembunyi untuk file tersimpan yang dihapus
            removeBox.innerHTML = '';
            if (root.dataset.removeName) {
                existing.forEach(function (f) {
                    if (!f.removed) return;
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = root.dataset.removeName;
                    hidden.value = f.name;
                    removeBox.appendChild(hidden);
                });
            }

            // Satu file: kunci tombol setelah file dipilih
            var locked = !multiple && hasNew;
            button.disabled = locked;
            base.classList.toggle('is-disabled', locked);
            button.innerHTML = (!multiple && existing.length) ? btnText.replace(/Pilih File/, 'Ganti File') : btnText;
        }

        function addFiles(files) {
            invalid = [];
            var firstError = '';
            Array.prototype.forEach.call(files, function (file) {
                var message = validate(file);
                if (message) {
                    invalid.push({ name: file.name, message: message });
                    if (!firstError) firstError = message;
                } else if (multiple) {
                    selected.push(file);
                } else {
                    selected = [file];
                }
            });
            if (!multiple && firstError) selected = [];
            syncInput();
            setError(firstError);
            render();
        }

        list.addEventListener('click', function (e) {
            var more = e.target.closest('.ina-upload-more');
            if (more) {
                expanded = !expanded;
                render();
                return;
            }
            var btn = e.target.closest('.ina-upload-remove');
            if (!btn) return;
            var index = parseInt(btn.dataset.index, 10);
            if (btn.dataset.kind === 'existing') {
                existing[index].removed = !existing[index].removed;
            } else if (btn.dataset.kind === 'new') {
                selected.splice(index, 1);
                syncInput();
            } else {
                invalid.splice(index, 1);
                if (!invalid.length) setError('');
            }
            render();
            button.focus();
        });

        button.addEventListener('click', function () {
            if (!button.disabled) input.click();
        });

        // Browser mengganti isi input saat memilih lagi; gabungkan dengan pilihan sebelumnya
        input.addEventListener('change', function () {
            var files = Array.prototype.slice.call(input.files);
            if (!files.length) {
                syncInput();
                return;
            }
            addFiles(files);
        });

        ['dragenter', 'dragover'].forEach(function (type) {
            base.addEventListener(type, function (e) {
                e.preventDefault();
                if (!button.disabled) base.classList.add('is-dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (type) {
            base.addEventListener(type, function (e) {
                e.preventDefault();
                base.classList.remove('is-dragover');
            });
        });
        base.addEventListener('drop', function (e) {
            if (button.disabled || !e.dataTransfer.files.length) return;
            var files = Array.prototype.slice.call(e.dataTransfer.files);
            addFiles(multiple ? files : files.slice(0, 1));
        });

        // Wajib diisi: cegah kirim form tanpa file
        if (form && root.dataset.required === '1') {
            form.addEventListener('submit', function (e) {
                var hasExisting = existing.some(function (f) { return !f.removed; });
                if (!selected.length && !hasExisting) {
                    e.preventDefault();
                    setError(label + ' wajib diunggah. Pilih gambar ' + acceptText + '.');
                    button.focus();
                }
            });
        }

        if (root.dataset.error) setError(root.dataset.error);
        render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ina-upload').forEach(init);
    });
})();
