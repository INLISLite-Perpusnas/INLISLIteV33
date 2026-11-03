<script>
    if (!jQuery.now) {
        jQuery.now = function() {
            return Date.now();
        };
    }

    $(document).ready(function() {
        // Editor uraian (Summernote)
        $('#content').summernote({
            height: 360,
            minHeight: null,
            maxHeight: null,
            focus: false,
            placeholder: 'Tulis isi berita di sini',
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
        });

        // Validasi field wajib di browser; pesan tampil di bawah field terkait
        var form = document.querySelector('form.ina-form-card');
        var rules = [];
        var title = document.getElementById('title');
        var sort = document.getElementById('sort');
        if (title) rules.push({ el: title, check: function(v) { return v.trim() === '' ? 'Judul berita wajib diisi.' : ''; } });
        if (sort) rules.push({ el: sort, check: function(v) { return !/^[1-9]\d*$/.test(v.trim()) ? 'Urutan harus berupa angka bulat mulai dari 1.' : ''; } });

        var setError = function(el, message) {
            var box = document.getElementById(el.id + '_error');
            el.closest('.ina-field').classList.toggle('has-error', !!message);
            box.hidden = !message;
            box.querySelector('span').textContent = message || '';
            if (message) el.setAttribute('aria-invalid', 'true'); else el.removeAttribute('aria-invalid');
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
