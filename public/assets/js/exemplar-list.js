document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    const config = window.exemplarListConfig;
    const byId = id => document.getElementById(id);
    const table = byId('tbl_data');
    const tbody = byId('exemplar_rows');
    const info = byId('exemplar_info');
    const selectAll = document.querySelector('.check_data');
    const library = byId('filter_location_library');
    const room = byId('filter_location_id');
    const media = byId('filter_media_id');
    const state = { page: 1, pages: 1, length: 10, search: '', sort: '', direction: 'desc' };
    let activeRequest;
    let roomRequest;
    let searchTimer;

    function cell(row, value, className) {
        const td = document.createElement('td');
        td.className = className || '';
        td.textContent = value == null || value === '' ? '-' : String(value);
        row.appendChild(td);
        return td;
    }

    function badge(row, text, color) {
        const td = cell(row, '', 'text-center');
        const span = document.createElement('span');
        span.className = 'badge badge-' + color;
        span.textContent = text;
        td.replaceChildren(span);
    }

    function renderRows(items, offset) {
        const fragment = document.createDocumentFragment();
        if (!items.length) {
            const row = document.createElement('tr');
            cell(row, 'Eksemplar tidak ditemukan.', 'text-center').colSpan = 12;
            fragment.appendChild(row);
        }
        items.forEach(function (item, index) {
            const row = document.createElement('tr');
            const label = item.NomorBarcode || item.ID;
            cell(row, offset + index + 1, 'text-center');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'check';
            checkbox.name = 'ID[]';
            checkbox.value = item.ID;
            checkbox.setAttribute('aria-label', 'Pilih eksemplar ' + label);
            cell(row, '', 'text-center').replaceChildren(checkbox);

            const barcode = document.createElement('button');
            barcode.type = 'button';
            barcode.className = 'barcode-copy';
            barcode.dataset.barcode = item.NomorBarcode || '';
            barcode.disabled = !item.NomorBarcode;
            barcode.setAttribute('aria-label', 'Salin barcode ' + label);
            barcode.append(document.createTextNode(item.NomorBarcode || '-'));
            const icon = document.createElement('i');
            icon.className = 'fa fa-copy';
            icon.setAttribute('aria-hidden', 'true');
            barcode.appendChild(icon);
            cell(row, '').replaceChildren(barcode);
            cell(row, item.TanggalPengadaan);
            cell(row, item.NoInduk);
            const title = cell(row, item.Title);
            if (item.Publikasi) {
                const publication = document.createElement('strong');
                publication.className = 'd-block text-primary';
                publication.textContent = item.Publikasi;
                title.appendChild(publication);
            }
            badge(row, Number(item.ISDRM) === 1 ? 'Ya' : 'Tdk', Number(item.ISDRM) === 1 ? 'success' : 'warning');
            badge(row, Number(item.IsQUARANTINE) === 1 ? 'Ya' : 'Tdk', Number(item.IsQUARANTINE) === 1 ? 'success' : 'warning');
            const toggle = document.createElement('input');
            toggle.type = 'checkbox';
            toggle.className = 'apply-status exemplar-switch';
            toggle.checked = Number(item.IsOPAC) === 1;
            toggle.dataset.href = item.switchUrl;
            toggle.setAttribute('aria-label', 'Tampilkan eksemplar ' + label + ' di OPAC');
            cell(row, '', 'text-center').replaceChildren(toggle);
            const status = item.StatusName || 'Tidak Diketahui';
            const colors = { tersedia: 'success', available: 'success', dipinjam: 'danger', borrowed: 'danger', rusak: 'warning', damaged: 'warning', hilang: 'dark', lost: 'dark' };
            badge(row, status, colors[status.toLowerCase()] || 'info');
            cell(row, item.LocationLibraryName, 'text-center');
            const edit = document.createElement('a');
            edit.href = item.editUrl;
            edit.className = 'btn btn-primary';
            edit.textContent = 'Ubah';
            edit.setAttribute('aria-label', 'Ubah eksemplar ' + label);
            cell(row, '', 'text-center').replaceChildren(edit);
            fragment.appendChild(row);
        });
        tbody.replaceChildren(fragment);
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }

    function pagination() {
        byId('exemplar_prev').disabled = state.page <= 1;
        byId('exemplar_next').disabled = state.page >= state.pages;
        const fragment = document.createDocumentFragment();
        let last = 0;
        const pages = new Set([1, state.pages]);
        for (let page = Math.max(1, state.page - 2); page <= Math.min(state.pages, state.page + 2); page++) pages.add(page);
        [...pages].sort((a, b) => a - b).forEach(function (page) {
            if (last && page - last > 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '…';
                ellipsis.setAttribute('aria-hidden', 'true');
                fragment.appendChild(ellipsis);
            }
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'exemplar-page-button' + (page === state.page ? ' is-active' : '');
            button.dataset.page = page;
            button.textContent = page;
            button.setAttribute('aria-label', 'Buka halaman ' + page);
            if (page === state.page) button.setAttribute('aria-current', 'page');
            fragment.appendChild(button);
            last = page;
        });
        byId('exemplar_page_numbers').replaceChildren(fragment);
    }

    async function json(url, options) {
        const response = await fetch(url, Object.assign({ credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } }, options));
        if (!response.ok || response.redirected) throw new Error('Permintaan gagal. Silakan muat ulang halaman.');
        return response.json();
    }

    async function loadRows() {
        if (activeRequest) activeRequest.abort();
        const request = new AbortController();
        activeRequest = request;
        table.setAttribute('aria-busy', 'true');
        info.textContent = 'Memuat data eksemplar...';
        byId('exemplar_retry').hidden = true;
        const params = new URLSearchParams({ lite: '1', page: state.page, length: state.length, search: state.search, sort: state.sort, direction: state.direction,
            location_library_id: library.value, location_id: room.value, media_id: media.value });
        try {
            const payload = await json(config.apiUrl + '?' + params, { signal: request.signal });
            if (request !== activeRequest) return;
            if (!Array.isArray(payload.rows)) throw new Error('Respons daftar tidak valid.');
            state.page = payload.page;
            state.pages = payload.pages;
            renderRows(payload.rows, payload.offset);
            const first = payload.filtered ? payload.offset + 1 : 0;
            info.textContent = 'Menampilkan ' + first + '-' + Math.min(payload.offset + state.length, payload.filtered) + ' dari ' + payload.filtered + ' eksemplar';
            pagination();
        } catch (error) {
            if (error.name === 'AbortError' || request !== activeRequest) return;
            renderRows([], 0);
            tbody.querySelector('td').textContent = 'Data eksemplar gagal dimuat.';
            info.textContent = error.message;
            byId('exemplar_retry').hidden = false;
        } finally {
            if (request === activeRequest) table.setAttribute('aria-busy', 'false');
        }
    }

    function reload() { state.page = 1; loadRows(); }
    function resetRoom() {
        if (roomRequest) roomRequest.abort();
        room.replaceChildren(new Option('-- Pilih Lokasi Perpustakaan Dahulu --', ''));
        room.disabled = true;
    }
    async function loadOptions(select, url, signal) {
        const payload = await json(url, { signal });
        const items = payload.data || payload;
        if (!Array.isArray(items)) throw new Error('Pilihan filter gagal dimuat.');
        const fragment = document.createDocumentFragment();
        items.forEach(item => fragment.appendChild(new Option(item.name, item.code)));
        select.appendChild(fragment);
    }
    library.addEventListener('change', async function () {
        resetRoom();
        reload();
        if (!library.value) return;
        const request = new AbortController();
        roomRequest = request;
        room.replaceChildren(new Option('-- Semua Lokasi Ruang --', ''));
        try {
            await loadOptions(room, config.roomsUrl + '/' + encodeURIComponent(library.value), request.signal);
            if (request === roomRequest && !request.signal.aborted) room.disabled = false;
        } catch (error) {
            if (error.name !== 'AbortError') info.textContent = 'Pilihan lokasi ruang gagal dimuat. Pilih ulang lokasi perpustakaan.';
        }
    });
    room.addEventListener('change', reload);
    media.addEventListener('change', reload);
    byId('btnResetFilter').addEventListener('click', function () { library.value = ''; media.value = ''; resetRoom(); reload(); });
    byId('exemplar_search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { state.search = byId('exemplar_search').value.trim(); reload(); }, 400);
    });
    byId('exemplar_page_length').addEventListener('change', function () { state.length = Number(this.value); reload(); });
    byId('exemplar_prev').addEventListener('click', function () { if (state.page > 1) { state.page--; loadRows(); } });
    byId('exemplar_next').addEventListener('click', function () { if (state.page < state.pages) { state.page++; loadRows(); } });
    byId('exemplar_page_numbers').addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');
        if (button) { state.page = Number(button.dataset.page); loadRows(); }
    });
    byId('exemplar_retry').addEventListener('click', loadRows);
    table.querySelector('thead').addEventListener('click', function (event) {
        const button = event.target.closest('[data-sort]');
        if (!button) return;
        state.direction = state.sort === button.dataset.sort && state.direction === 'asc' ? 'desc' : 'asc';
        state.sort = button.dataset.sort;
        table.querySelectorAll('[aria-sort]').forEach(th => th.removeAttribute('aria-sort'));
        button.closest('th').setAttribute('aria-sort', state.direction === 'asc' ? 'ascending' : 'descending');
        reload();
    });
    selectAll.addEventListener('change', function () { tbody.querySelectorAll('.check').forEach(input => { input.checked = selectAll.checked; }); });
    tbody.addEventListener('change', async function (event) {
        const input = event.target;
        if (input.matches('.check')) {
            const count = tbody.querySelectorAll('.check:checked').length;
            const total = tbody.querySelectorAll('.check').length;
            selectAll.checked = total > 0 && count === total;
            selectAll.indeterminate = count > 0 && count < total;
        }
        if (!input.matches('.apply-status')) return;
        const nextValue = input.checked;
        input.disabled = true;
        const body = new URLSearchParams({ field: 'IsOPAC', value: String(nextValue), [config.csrfName]: config.csrfHash });
        try {
            const payload = await json(input.dataset.href, { method: 'POST', body });
            if (payload.error !== false) throw new Error(payload.message || 'Status gagal disimpan.');
            info.textContent = payload.message || 'Status OPAC berhasil disimpan.';
        } catch (error) {
            input.checked = !nextValue;
            window.alert(error.message);
        } finally { input.disabled = false; }
    });
    tbody.addEventListener('click', async function (event) {
        const button = event.target.closest('.barcode-copy');
        if (!button) return;
        try {
            await navigator.clipboard.writeText(button.dataset.barcode);
            info.textContent = 'Barcode ' + button.dataset.barcode + ' berhasil disalin.';
        } catch (error) { window.prompt('Salin barcode berikut:', button.dataset.barcode); }
    });

    byId('action').addEventListener('change', function () {
        const printing = this.value === 'cetak-label';
        byId('cetak_panel').style.display = printing ? '' : 'none';
        if (!printing) {
            byId('paper_size').value = '';
            byId('label_model').replaceChildren(new Option('-- Pilih Model --', ''));
            byId('label_model_wrapper').style.display = 'none';
            byId('model_info_row').style.display = 'none';
        }
    });
    byId('paper_size').addEventListener('change', function () {
        const paper = config.paperSizes[this.value];
        const models = byId('label_model');
        models.replaceChildren(new Option('-- Pilih Model --', ''));
        byId('model_info_row').style.display = 'none';
        byId('label_model_wrapper').style.display = paper ? '' : 'none';
        if (paper) Object.entries(paper.models).forEach(([value, label]) => models.appendChild(new Option(label, value)));
    });
    byId('label_model').addEventListener('change', function () {
        byId('model_info_row').style.display = this.value ? '' : 'none';
        byId('model_info_text').textContent = byId('paper_size').selectedOptions[0].text + ' → ' + this.selectedOptions[0].text;
    });
    byId('btnProcess2').addEventListener('click', function () {
        const action = byId('action').value;
        const paper = byId('paper_size').value;
        const model = byId('label_model').value;
        const ids = [...tbody.querySelectorAll('.check:checked')].map(input => input.value);
        if (!action) return window.alert('Silakan pilih aksi terlebih dahulu!');
        if (action === 'cetak-label' && !paper) return window.alert('Silakan pilih jenis kertas terlebih dahulu!');
        if (action === 'cetak-label' && !model) return window.alert('Silakan pilih model label terlebih dahulu!');
        if (!ids.length) return window.alert('Silakan pilih minimal satu eksemplar!');
        const url = config.actions[action];
        if (!url) return;
        const form = document.createElement('form');
        form.method = 'post';
        form.action = url;
        const fields = { eksemplar_ids: ids.join(','), [config.csrfName]: config.csrfHash };
        if (action === 'cetak-label') Object.assign(fields, { eksemplar_tpl: model, paper_size: paper, output_format: byId('output_format').value || 'pdf' });
        Object.entries(fields).forEach(function ([name, value]) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = name; input.value = value;
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    });

    loadRows();
    Promise.allSettled([loadOptions(library, config.librariesUrl), loadOptions(media, config.mediaUrl)]).then(function (results) {
        if (results.some(result => result.status === 'rejected')) {
            byId('exemplar_filter_error').hidden = false;
        }
    });
});
