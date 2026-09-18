(function (window, $) {
    'use strict';
    let config;
    let sequence = 0;

    function addRow(group) {
        const container = $('#' + group + '_criteria_rows');
        if (container.children().length >= 20) {
            window.alert('Maksimal 20 kriteria untuk setiap kelompok.');
            return;
        }
        const id = 'circulation-criterion-' + (++sequence);
        const row = $('<div>', { class: 'criterion-row' });
        const field = $('<select>', { id: id + '-field', class: 'form-control criterion-field', 'aria-label': 'Pilih kriteria ' + (group === 'member' ? 'anggota' : 'koleksi') });
        field.append(new Option('-- Pilih Kriteria --', ''));
        Object.entries(config.labels[group]).forEach(function ([value, text]) {
            field.append(new Option(text, value));
        });
        const value = $('<select>', { id: id + '-value', class: 'form-control criterion-value', disabled: true, 'aria-label': 'Nilai kriteria' });
        value.append(new Option('-- Semua --', ''));
        const remove = $('<button>', { type: 'button', class: 'btn btn-outline-danger criterion-remove', title: 'Hapus kriteria', 'aria-label': 'Hapus kriteria' })
            .append($('<i>', { class: 'fas fa-minus', 'aria-hidden': 'true' }));
        row.append($('<div>', { class: 'criterion-control' }).append(field), $('<div>', { class: 'criterion-control' }).append(value), remove);
        container.append(row);
        field.select2({ width: '100%', placeholder: '-- Pilih Kriteria --' });
        value.select2({ width: '100%', placeholder: '-- Semua --' });
        field.on('change', function () {
            const key = field.val();
            const title = config.labels[group][key] || 'Nilai kriteria';
            // Recreate the value select so an old in-flight request cannot fill
            // the dropdown after the criterion changes.
            value.select2('destroy');
            value.empty().append(new Option('-- Semua --', '')).prop('disabled', !key);
            value.attr('aria-label', title);
            value.select2({
                width: '100%', placeholder: '-- Semua --', allowClear: true,
                ajax: {
                    url: config.url, dataType: 'json', delay: 300,
                    data: function (params) {
                        return { criterion_options: '1', group: group, field: key, q: params.term || '', page: params.page || 1 };
                    },
                    processResults: function (data) { return data; }
                },
                language: {
                    errorLoading: function () { return 'Daftar gagal dimuat. Tutup lalu buka kembali untuk mencoba lagi.'; },
                    searching: function () { return 'Mencari...'; },
                    noResults: function () { return 'Tidak ada pilihan yang sesuai.'; },
                    loadingMore: function () { return 'Memuat pilihan berikutnya...'; }
                }
            });
        });
        remove.on('click', function () {
            field.select2('destroy');
            value.select2('destroy');
            row.remove();
            if (!container.children().length) addRow(group);
        });
    }

    function reset() {
        ['member', 'catalog'].forEach(function (group) {
            const container = $('#' + group + '_criteria_rows');
            container.find('select').each(function () { $(this).select2('destroy'); });
            container.empty();
            addRow(group);
        });
    }

    window.CirculationCriteria = {
        init: function (options) {
            config = options;
            $('[data-add-criterion]').on('click', function () { addRow(this.dataset.addCriterion); });
            reset();
        },
        reset: reset,
        values: function () {
            const filters = {};
            ['member', 'catalog'].forEach(function (group) {
                const criteria = [];
                $('#' + group + '_criteria_rows .criterion-row').each(function () {
                    const field = $(this).find('.criterion-field').val();
                    const value = $(this).find('.criterion-value').val();
                    if (field && value !== null && value !== '') criteria.push({ field: field, value: value });
                });
                filters[group + '_criteria'] = JSON.stringify(criteria);
            });
            return filters;
        }
    };
})(window, jQuery);
