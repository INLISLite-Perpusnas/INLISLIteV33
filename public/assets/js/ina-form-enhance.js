/**
 * Perilaku form sesuai panduan INA Digital (tanpa mengubah logika halaman):
 *
 * Checkbox (design.inadigital.go.id/components/checkbox):
 *  - "Pilih Semua" (#select_all_columns) menampilkan state indeterminate saat hanya
 *    sebagian kolom (.column-checkbox) terpilih; state ini dikontrol sistem.
 *
 * Date Picker (design.inadigital.go.id/components/date-picker), varian Range:
 *  - Pasangan tanggal mulai/akhir (start_date/end_date, birth_start_date/birth_end_date)
 *    saling membatasi: tanggal akhir tidak bisa sebelum tanggal mulai.
 *  - Teks bantuan "Pilih tanggal ..." di bawah input.
 */
(function () {
    function syncSelectAll() {
        var all = document.getElementById('select_all_columns');
        if (!all) return;
        var boxes = document.querySelectorAll('.column-checkbox');
        var checked = 0;
        boxes.forEach(function (box) {
            if (box.checked) checked++;
        });
        all.checked = boxes.length > 0 && checked === boxes.length;
        all.indeterminate = checked > 0 && checked < boxes.length;
        all.setAttribute('aria-checked', all.indeterminate ? 'mixed' : String(all.checked));
    }

    function addHelper(input, text) {
        if (!input || input.dataset.inaHelper) return;
        var help = document.createElement('small');
        help.className = 'ina-date-help';
        help.id = (input.id || input.name) + '_help';
        help.textContent = text;
        input.insertAdjacentElement('afterend', help);
        input.setAttribute('aria-describedby', help.id);
        input.dataset.inaHelper = '1';
    }

    function linkRange(startName, endName) {
        var start = document.querySelector('input[type="date"][name="' + startName + '"]');
        var end = document.querySelector('input[type="date"][name="' + endName + '"]');
        if (!start || !end) return;

        addHelper(start, 'Pilih tanggal mulai.');
        addHelper(end, 'Pilih tanggal akhir (tidak sebelum tanggal mulai).');

        var apply = function () {
            end.min = start.value || '';
            start.max = end.value || '';
        };
        start.addEventListener('change', apply);
        end.addEventListener('change', apply);
        apply();
    }

    document.addEventListener('DOMContentLoaded', function () {
        syncSelectAll();
        linkRange('start_date', 'end_date');
        linkRange('birth_start_date', 'birth_end_date');
    });

    // Checkbox kolom diubah lewat klik, "Pilih Semua", atau tombol Hapus Filter:
    // sinkronkan setelah handler halaman selesai berjalan.
    ['change', 'click'].forEach(function (type) {
        document.addEventListener(type, function () {
            setTimeout(syncSelectAll, 0);
        });
    });

    // Reset tanggal dari tombol Hapus Filter: lepas batas min/max yang tersisa
    document.addEventListener('click', function () {
        setTimeout(function () {
            document.querySelectorAll('input[type="date"]').forEach(function (input) {
                if (input.value === '') {
                    var pair = { start_date: 'end_date', end_date: 'start_date', birth_start_date: 'birth_end_date', birth_end_date: 'birth_start_date' }[input.name];
                    var other = pair && document.querySelector('input[type="date"][name="' + pair + '"]');
                    if (other) {
                        if (/start/.test(input.name)) other.min = ''; else other.max = '';
                    }
                }
            });
        }, 0);
    });
})();
