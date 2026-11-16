<script>
    if (!jQuery.now) {
        jQuery.now = function() {
            return Date.now();
        };
    }

    $(document).ready(function() {
        // Editor uraian (TinyMCE)
        tinymce.init({
            selector: '#content',
            height: 360,
            menubar: false,
            plugins: 'link image table lists code fullscreen',
            toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | bullist numlist | link image table | fullscreen code',
            font_formats: "System Font=Dosis, sans-serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
            fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
            content_style: "body { font-size: 12pt; font-family: Dosis, sans-serif; }",
            setup: function(editor) {
                editor.on('init', function(e) {
                    editor.execCommand("fontName", false, "System Font");
                });
            },
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function() {
                        cb(reader.result, {
                            alt: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            }
        });

        // Validasi field wajib di browser; pesan tampil di bawah field terkait
        var form = document.querySelector('form.ina-form-card');
        var rules = [];
        var title = document.getElementById('title');
        var sort = document.getElementById('sort');
        if (title) rules.push({
            el: title,
            check: function(v) {
                return v.trim() === '' ? 'Judul berita wajib diisi.' : '';
            }
        });
        if (sort) rules.push({
            el: sort,
            check: function(v) {
                return !/^[1-9]\d*$/.test(v.trim()) ? 'Urutan harus berupa angka bulat mulai dari 1.' : '';
            }
        });

        var setError = function(el, message) {
            var box = document.getElementById(el.id + '_error');
            el.closest('.ina-field').classList.toggle('has-error', !!message);
            box.hidden = !message;
            box.querySelector('span').textContent = message || '';
            if (message) el.setAttribute('aria-invalid', 'true');
            else el.removeAttribute('aria-invalid');
        };

        form.addEventListener('submit', function(e) {
            var first = null;
            rules.forEach(function(rule) {
                var message = rule.check(rule.el.value);
                setError(rule.el, message);
                if (message && !first) first = rule.el;
            });
            if (first) {
                e.preventDefault();
                first.focus();
            }
        });

        rules.forEach(function(rule) {
            rule.el.addEventListener('input', function() {
                if (!rule.check(rule.el.value)) setError(rule.el, '');
            });
        });
    });
</script>