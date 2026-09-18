<?php
$flashIcon = session()->getFlashdata('swal_icon');
$flashMessage = session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text');
$flashPayload = $flashIcon ? [
    'type' => $flashIcon,
    'title' => session()->getFlashdata('swal_title'),
    'html' => $flashMessage,
] : null;
$flashClass = in_array($flashIcon, ['success', 'info'], true) ? 'alert-success' : 'alert-warning';
?>

<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .catalog-list-page .page-title-heading h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .catalog-list-page .page-title-subheading { margin: .25rem 0 0; }
    .catalog-list-page .app-page-title .page-title-wrapper {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .catalog-list-page .app-page-title .page-title-heading {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        min-width: 0;
    }
    .catalog-list-page .app-page-title .page-title-icon {
        display: inline-flex;
        flex: 0 0 1.8rem;
        width: 1.8rem;
        height: 1.8rem;
        margin: 0;
        padding: 0;
        align-items: center;
        justify-content: center;
        color: inherit;
        background: transparent;
        border: 0;
        box-shadow: none;
    }
    .catalog-list-page .app-page-title .page-title-icon i {
        display: block;
        font-size: 1.5rem;
        line-height: 1.2;
    }
    .catalog-list-page .app-page-title .page-title-actions {
        margin-left: auto;
        text-align: right;
    }
    .catalog-list-page .app-page-title .breadcrumb {
        justify-content: flex-end;
        margin: 0;
        padding: 0;
        background: transparent;
    }
    .catalog-list-page .catalog-filter-label {
        margin: 0; color: #fff; background: #495057; border: 1px solid #495057;
        padding: .5rem .75rem; border-radius: .25rem 0 0 .25rem; white-space: nowrap;
    }
    .catalog-list-page .catalog-switch {
        appearance: none; width: 2.75rem; height: 1.5rem; margin: 0;
        border: 2px solid #6c757d; border-radius: 999px; background: #fff;
        cursor: pointer; vertical-align: middle;
        transition: background-color .15s ease, border-color .15s ease;
    }
    .catalog-list-page .catalog-switch::before {
        content: ""; display: block; width: 1rem; height: 1rem; margin: .125rem;
        border-radius: 50%; background: #6c757d; transition: transform .15s ease;
    }
    .catalog-list-page .catalog-switch:checked { background: #087f5b; border-color: #087f5b; }
    .catalog-list-page .catalog-switch:checked::before { background: #fff; transform: translateX(1.25rem); }
    .catalog-list-page .catalog-switch:disabled { cursor: wait; opacity: .65; }
    .catalog-list-page .catalog-switch:focus-visible,
    .catalog-list-page input[type="checkbox"]:focus-visible,
    .catalog-list-page select:focus-visible,
    .catalog-list-page button:focus-visible,
    .catalog-list-page a:focus-visible { outline: 3px solid #f59f00; outline-offset: 2px; }
    .catalog-list-page #tbl_data th { vertical-align: middle; }
    .catalog-list-page #tbl_data .badge {
        display: inline-block;
        padding: .35em .65em;
        color: #fff !important;
        font-size: .75em;
        font-weight: 700;
        line-height: 1;
        border-radius: .25rem;
    }
    .catalog-list-page #tbl_data .badge-secondary {
        color: #fff !important;
        background-color: #495057 !important;
    }
    .catalog-list-page #tbl_data .badge-success {
        color: #fff !important;
        background-color: #087f5b !important;
    }
    .catalog-list-page .catalog-controls { gap: .75rem; }
    .catalog-list-page .catalog-create-actions { gap: .6rem; }
    .catalog-list-page .catalog-search-wrapper {
        margin-left: auto;
    }
    .catalog-list-page .catalog-search { min-width: 14rem; }
    .catalog-list-page .catalog-pagination {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem;
        background: #f8fafc;
        border: 1px solid #dbe3ec;
        border-radius: .75rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
    }
    .catalog-list-page .catalog-page-numbers {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .catalog-list-page .catalog-page-button {
        display: inline-flex;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 .7rem;
        gap: .35rem;
        align-items: center;
        justify-content: center;
        color: #334155;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        font-weight: 600;
        line-height: 1;
        transition: color .15s ease, background-color .15s ease, border-color .15s ease, transform .15s ease;
    }
    .catalog-list-page .catalog-page-button:hover:not(:disabled) {
        color: #fff;
        background: #3155a6;
        border-color: #3155a6;
        transform: translateY(-1px);
    }
    .catalog-list-page .catalog-page-button.is-active {
        color: #fff;
        background: #243f7e;
        border-color: #243f7e;
        box-shadow: 0 3px 8px rgba(36, 63, 126, .25);
    }
    .catalog-list-page .catalog-page-button:disabled {
        color: #94a3b8;
        background: #f1f5f9;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .catalog-list-page .catalog-page-ellipsis {
        min-width: 1.5rem;
        color: #64748b;
        text-align: center;
    }
    .catalog-list-page #catalog_info { color: #495057; }
    @media (prefers-reduced-motion: reduce) {
        .catalog-list-page *, .catalog-list-page *::before { transition: none !important; }
    }
    @media (max-width: 576px) {
        .catalog-list-page .app-page-title .page-title-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }
        .catalog-list-page .app-page-title .page-title-actions {
            align-self: flex-end;
        }
        .catalog-list-page .catalog-search-wrapper {
            width: 100%;
            margin-left: 0;
        }
        .catalog-list-page .catalog-search {
            width: 100%;
        }
        .catalog-list-page .catalog-pagination-label {
            display: none;
        }
        .catalog-list-page .catalog-page-button {
            min-width: 2rem;
            height: 2rem;
            padding: 0 .5rem;
        }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<main class="app-main__inner" aria-labelledby="catalog-title">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon"><i class="pe-7s-server icon-gradient bg-strong-bliss" aria-hidden="true"></i></div>
                <div>
                    <h1 id="catalog-title">Katalog</h1>
                    <p class="page-title-subheading">Daftar semua katalog</p>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Katalog</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="main-card mb-3 card" aria-labelledby="catalog-table-title">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="h6 mb-0" id="catalog-table-title"><i class="fa fa-list" aria-hidden="true"></i> Tabel Daftar Katalog</h2>
            <div class="catalog-create-actions d-flex align-items-center flex-wrap">
                <?php if (is_allowed('katalog/create')) : ?>
                    <?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
                        <a href="<?= base_url('katalog/create?rda=1') ?>" class="btn btn-primary btn-sm mr-2"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Katalog RDA</a>
                        <a href="<?= base_url('katalog/create?rda=0') ?>" class="btn btn-success btn-sm mr-2"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Katalog AACR</a>
                    <?php else : ?>
                        <a href="<?= base_url('katalog/create_marc') ?>" class="btn btn-success btn-sm mr-2"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Katalog</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <?php if ($flashPayload) : ?>
                <div class="alert <?= $flashClass ?>" role="status">
                    <strong><?= esc($flashPayload['title']) ?></strong>
                    <?= esc(strip_tags((string) $flashPayload['html'])) ?>
                </div>
            <?php endif; ?>
            <div class="d-block mb-3 pb-3 border-bottom">
                <button type="button" id="proses_karantina" class="btn btn-warning" title="Memproses katalog yang dipilih"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Pindahkan ke Karantina</button>
                <button type="button" id="proses_opac" class="btn btn-primary ml-2" title="Memproses katalog yang dipilih"><i class="fa fa-check-square" aria-hidden="true"></i> Tampilkan di OPAC</button>
            </div>

            <div class="catalog-controls d-flex align-items-end flex-wrap mb-3">
                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <label class="catalog-filter-label" for="filter_worksheet_id"><i class="fa fa-filter" aria-hidden="true"></i> Jenis Bahan</label>
                    </div>
                    <select class="form-control" id="filter_worksheet_id">
                        <option value="">-- Semua Jenis Bahan --</option>
                        <?php foreach ($worksheets as $row) : ?>
                            <option value="<?= (int) $row->ID ?>"><?= esc($row->Name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-outline-secondary" id="btnResetFilterWorksheet" type="button" style="height:38px; flex-shrink:0;"><i class="fa fa-undo" aria-hidden="true"></i> Reset Filter</button>
                <div>
                    <label for="catalog_page_length" class="d-block mb-1">Data per halaman</label>
                    <select id="catalog_page_length" class="form-control" style="width:90px;">
                        <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                    </select>
                </div>
                <div class="catalog-search-wrapper">
                    <label for="catalog_search" class="d-block mb-1">Cari katalog</label>
                    <input type="search" id="catalog_search" class="form-control catalog-search" placeholder="Judul, BIBID, penerbit..." autocomplete="off">
                </div>
            </div>

            <div class="table-responsive">
                <form name="form_items" id="form_items">
                    <table style="width:100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                        <caption class="sr-only">Daftar katalog perpustakaan beserta status dan tindakan pengelolaannya.</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" width="35">No</th>
                                <th scope="col" class="text-center" width="35"><input type="checkbox" class="check_data" title="Pilih semua pada halaman ini" aria-label="Pilih semua katalog pada halaman ini"></th>
                                <th scope="col" class="text-center">BIBID</th>
                                <th scope="col" class="text-center" style="min-width:300px;">Judul</th>
                                <th scope="col" class="text-center">Jenis Bahan</th>
                                <th scope="col" class="text-center">Edisi</th>
                                <th scope="col" class="text-center">Penerbit</th>
                                <th scope="col" class="text-center">Deskripsi Fisik</th>
                                <th scope="col" class="text-center">No. Panggil</th>
                                <th scope="col" class="text-center">Eksemplar</th>
                                <th scope="col" class="text-center">OPAC</th>
                                <th scope="col" class="text-center">Populer</th>
                                <th scope="col" class="text-center">Pedoman Katalog</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="catalog_rows"><tr><td colspan="14" class="text-center">Memuat data katalog...</td></tr></tbody>
                    </table>
                </form>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
                <p id="catalog_info" class="mb-0" aria-live="polite">Memuat data katalog...</p>
                <nav aria-label="Navigasi halaman katalog">
                    <div class="catalog-pagination">
                        <button type="button" class="catalog-page-button" id="catalog_prev" aria-label="Halaman sebelumnya"><span aria-hidden="true">&#8249;</span><span class="catalog-pagination-label ml-1">Sebelumnya</span></button>
                        <div class="catalog-page-numbers" id="catalog_page_numbers"></div>
                        <span id="catalog_page_info" class="sr-only" aria-live="polite">Halaman 1</span>
                        <button type="button" class="catalog-page-button" id="catalog_next" aria-label="Halaman berikutnya"><span class="catalog-pagination-label mr-1">Berikutnya</span><span aria-hidden="true">&#8250;</span></button>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</main>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const apiUrl = <?= json_encode(site_url('api/katalog/list-lite')) ?>;
    const quarantineUrl = <?= json_encode(base_url('katalog/proses_karantina')) ?>;
    const opacUrl = <?= json_encode(base_url('katalog/proses_opac')) ?>;
    const tbody = document.getElementById('catalog_rows');
    const table = document.getElementById('tbl_data');
    const searchInput = document.getElementById('catalog_search');
    const worksheetInput = document.getElementById('filter_worksheet_id');
    const lengthInput = document.getElementById('catalog_page_length');
    const info = document.getElementById('catalog_info');
    const pageInfo = document.getElementById('catalog_page_info');
    const pageNumbers = document.getElementById('catalog_page_numbers');
    const previousButton = document.getElementById('catalog_prev');
    const nextButton = document.getElementById('catalog_next');
    const selectAll = document.querySelector('.check_data');
    const state = { page: 1, pages: 1, offset: 0, length: 10, search: '', worksheetId: '' };
    let activeRequest = null;
    let searchTimer = null;

    function appendTextCell(row, value, className) {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        cell.textContent = value == null || value === '' ? '-' : String(value);
        row.appendChild(cell);
    }

    function appendBadgeCell(row, value, badgeClass) {
        const cell = document.createElement('td');
        cell.className = 'text-center';
        const badge = document.createElement('span');
        badge.className = 'badge ' + badgeClass;
        badge.textContent = value || '-';
        cell.appendChild(badge);
        row.appendChild(cell);
    }

    function createStatusCell(row, item, field, label) {
        const cell = document.createElement('td');
        cell.className = 'text-center';
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'apply-status catalog-switch';
        input.checked = Number(item[field]) === 1;
        input.dataset.href = item.switchUrl;
        input.dataset.field = field;
        input.setAttribute('aria-label', label + ' ' + (item.BIBID || item.ID));
        cell.appendChild(input);
        row.appendChild(cell);
    }

    function renderRows(items, rowOffset) {
        const fragment = document.createDocumentFragment();
        if (!items.length) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 14;
            cell.className = 'text-center';
            cell.textContent = 'Katalog tidak ditemukan.';
            row.appendChild(cell);
            fragment.appendChild(row);
        }

        items.forEach(function(item, index) {
            const row = document.createElement('tr');
            appendTextCell(row, rowOffset + index + 1, 'text-center');

            const selectCell = document.createElement('td');
            selectCell.className = 'text-center';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'check';
            checkbox.name = 'ID[]';
            checkbox.value = item.ID;
            checkbox.setAttribute('aria-label', 'Pilih katalog ' + (item.BIBID || item.ID));
            selectCell.appendChild(checkbox);
            row.appendChild(selectCell);

            appendTextCell(row, item.BIBID, 'text-left');
            appendTextCell(row, item.Title);
            appendBadgeCell(row, item.WorksheetName, 'badge-secondary');
            appendTextCell(row, item.Edition, 'text-center');
            appendTextCell(row, item.Publisher, 'text-center');
            appendTextCell(row, item.PhysicalDescription, 'text-center');
            appendTextCell(row, item.CallNumber, 'text-center');
            appendTextCell(row, item.Eksemplar, 'text-center');
            createStatusCell(row, item, 'IsOPAC', 'Tampilkan di OPAC:');
            createStatusCell(row, item, 'ISPopuler', 'Tandai populer:');
            appendBadgeCell(row, Number(item.IsRDA) === 1 ? 'RDA' : 'AACR', Number(item.IsRDA) === 1 ? 'badge-success' : 'badge-secondary');

            const actionCell = document.createElement('td');
            actionCell.className = 'text-center';
            const editLink = document.createElement('a');
            editLink.href = item.editUrl;
            editLink.className = 'btn btn-primary';
            editLink.title = 'Ubah katalog';
            editLink.setAttribute('aria-label', 'Ubah katalog ' + (item.BIBID || item.ID));
            const editIcon = document.createElement('i');
            editIcon.className = 'pe-7s-note font-weight-bold';
            editIcon.setAttribute('aria-hidden', 'true');
            editLink.appendChild(editIcon);
            actionCell.appendChild(editLink);
            row.appendChild(actionCell);
            fragment.appendChild(row);
        });

        tbody.replaceChildren(fragment);
    }

    function renderPageNumbers() {
        const fragment = document.createDocumentFragment();
        const pages = [];

        if (state.pages <= 7) {
            for (let page = 1; page <= state.pages; page += 1) pages.push(page);
        } else {
            const start = Math.max(2, state.page - 1);
            const end = Math.min(state.pages - 1, state.page + 1);
            pages.push(1);
            if (start > 2) pages.push('start-ellipsis');
            for (let page = start; page <= end; page += 1) pages.push(page);
            if (end < state.pages - 1) pages.push('end-ellipsis');
            pages.push(state.pages);
        }

        pages.forEach(function(value) {
            if (typeof value !== 'number') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'catalog-page-ellipsis';
                ellipsis.textContent = '…';
                ellipsis.setAttribute('aria-hidden', 'true');
                fragment.appendChild(ellipsis);
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'catalog-page-button' + (value === state.page ? ' is-active' : '');
            button.textContent = String(value);
            button.dataset.page = String(value);
            button.setAttribute('aria-label', 'Buka halaman ' + value);
            if (value === state.page) button.setAttribute('aria-current', 'page');
            fragment.appendChild(button);
        });

        pageNumbers.replaceChildren(fragment);
    }

    function updatePagination(payload) {
        state.page = Number(payload.page) || 1;
        state.pages = Number(payload.pages) || 1;
        state.offset = Number(payload.offset) || 0;
        state.length = Number(payload.length) || state.length;
        const filtered = Number(payload.filtered) || 0;
        const first = filtered ? ((state.page - 1) * state.length) + 1 : 0;
        const last = Math.min(state.page * state.length, filtered);
        info.textContent = 'Menampilkan ' + first + '-' + last + ' dari ' + filtered + ' katalog';
        pageInfo.textContent = 'Halaman ' + state.page + ' dari ' + state.pages;
        previousButton.disabled = state.page <= 1;
        nextButton.disabled = state.page >= state.pages;
        selectAll.checked = false;
        renderPageNumbers();
    }

    async function loadRows() {
        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        const requestedPage = state.page;
        const requestedLength = state.length;
        table.setAttribute('aria-busy', 'true');
        info.textContent = 'Memuat data katalog...';

        const body = new URLSearchParams({
            page: String(state.page),
            length: String(state.length),
            search: state.search,
            worksheet_id: state.worksheetId
        });

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString(),
                signal: activeRequest.signal
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const payload = await response.json();
            updatePagination(payload);
            const responsePage = Number(payload.page) || requestedPage;
            const responseLength = Number(payload.length) || requestedLength;
            const responseOffset = Number(payload.offset);
            renderRows(
                Array.isArray(payload.rows) ? payload.rows : [],
                Number.isFinite(responseOffset) ? responseOffset : (responsePage - 1) * responseLength
            );
        } catch (error) {
            if (error.name === 'AbortError') return;
            tbody.replaceChildren();
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 14;
            cell.className = 'text-center text-danger';
            cell.textContent = 'Data katalog gagal dimuat. Silakan muat ulang halaman.';
            row.appendChild(cell);
            tbody.appendChild(row);
            info.textContent = 'Gagal memuat data katalog.';
        } finally {
            table.setAttribute('aria-busy', 'false');
        }
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            state.search = searchInput.value.trim();
            state.page = 1;
            loadRows();
        }, 400);
    });
    worksheetInput.addEventListener('change', function() {
        state.worksheetId = worksheetInput.value;
        state.page = 1;
        loadRows();
    });
    lengthInput.addEventListener('change', function() {
        state.length = Number(lengthInput.value) || 10;
        state.page = 1;
        loadRows();
    });
    document.getElementById('btnResetFilterWorksheet').addEventListener('click', function() {
        worksheetInput.value = '';
        state.worksheetId = '';
        state.page = 1;
        loadRows();
    });
    previousButton.addEventListener('click', function() {
        if (state.page > 1) { state.page -= 1; loadRows(); }
    });
    nextButton.addEventListener('click', function() {
        if (state.page < state.pages) { state.page += 1; loadRows(); }
    });
    pageNumbers.addEventListener('click', function(event) {
        const button = event.target.closest('[data-page]');
        if (!button) return;
        state.page = Number(button.dataset.page);
        loadRows();
    });
    selectAll.addEventListener('change', function() {
        tbody.querySelectorAll('input.check').forEach(function(input) { input.checked = selectAll.checked; });
    });

    tbody.addEventListener('change', async function(event) {
        const checkbox = event.target.closest('.apply-status');
        if (!checkbox) return;
        const nextValue = checkbox.checked;
        checkbox.disabled = true;
        const body = new URLSearchParams({ field: checkbox.dataset.field, value: String(nextValue) });

        try {
            const response = await fetch(checkbox.dataset.href, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            });
            const payload = await response.json();
            if (!response.ok || payload.error) throw new Error(payload.message || 'Status gagal disimpan.');
            info.textContent = payload.message;
        } catch (error) {
            checkbox.checked = !nextValue;
            window.alert(error.message || 'Status gagal disimpan.');
        } finally {
            checkbox.disabled = false;
        }
    });

    function selectedQuery() {
        const params = new URLSearchParams();
        tbody.querySelectorAll('input.check:checked').forEach(function(input) { params.append('ID[]', input.value); });
        return params.toString();
    }

    function processSelected(url, message) {
        const query = selectedQuery();
        if (!query) {
            window.alert('Pilih minimal satu katalog terlebih dahulu.');
            return;
        }
        if (window.confirm(message)) window.location.assign(url + '?' + query);
    }

    document.getElementById('proses_karantina').addEventListener('click', function() {
        processSelected(quarantineUrl, 'Pindahkan katalog yang dipilih ke karantina?');
    });
    document.getElementById('proses_opac').addEventListener('click', function() {
        processSelected(opacUrl, 'Tampilkan katalog yang dipilih di OPAC?');
    });

    requestAnimationFrame(function() { loadRows(); });
});
</script>
<?= $this->endSection('script'); ?>
