<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-table.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-table.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-pagination.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-pagination.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-stockopname.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-stockopname.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<?php
$request    = service('request');
$search     = (string) ($request->getGet('search') ?? '');
$tahunAktif = (string) ($request->getGet('tahun') ?? '');
$scanCounts = $scanCounts ?? [];
$total      = (int) ($total ?? 0);
$firstRow   = isset($pager) ? ($pager->getCurrentPage() - 1) * $pager->getPerPage() : 0;
?>
<div class="app-main__inner so-page">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>
                    Stock Opname
                    <div class="page-title-subheading">Kelola proyek stock opname koleksi</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Stock Opname</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('stockopname') ?>" class="so-filter so-filter--list">
                <div>
                    <label for="search">Cari</label>
                    <input type="search" class="form-control" id="search" name="search" value="<?= esc($search, 'attr') ?>" placeholder="Nama proyek, koordinator, atau keterangan">
                </div>
                <div>
                    <label for="tahun">Tahun</label>
                    <select class="form-select" id="tahun" name="tahun">
                        <option value="">Semua tahun</option>
                        <?php for ($year = (int) date('Y'); $year >= 2020; $year--): ?>
                            <option value="<?= $year ?>" <?= $tahunAktif === (string) $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="so-filter__actions">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Cari</button>
                    <a href="<?= base_url('stockopname') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar -->
    <div class="card">
        <div class="card-header">
            <h2 class="so-card-title"><i class="fa fa-list" aria-hidden="true"></i> Daftar Stock Opname</h2>
            <span class="so-count"><?= number_format($total) ?> proyek</span>
            <div class="so-header-end">
                <a href="<?= base_url('stockopname/create') ?>" class="btn btn-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> Tambah Stock Opname
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="so-alert-slot" id="soAlert" role="status" aria-live="polite">
                <?php if ($message = session()->getFlashdata('message')): ?>
                    <div class="so-alert so-alert--<?= ($message['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= esc($message['text'] ?? '') ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($stockopnames)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="soListTable">
                        <thead>
                            <tr>
                                <th style="width:56px">No</th>
                                <th>Nama Proyek</th>
                                <th>Tahun</th>
                                <th>Koordinator</th>
                                <th>Tanggal Mulai</th>
                                <th class="text-end">Hasil Scan</th>
                                <th style="width:140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockopnames as $i => $so): ?>
                                <?php $scan = $scanCounts[(int) $so['ID']] ?? 0; ?>
                                <tr id="so-row-<?= (int) $so['ID'] ?>">
                                    <td><?= $firstRow + $i + 1 ?></td>
                                    <td>
                                        <a class="so-cell-main so-link" href="<?= base_url('stockopname/detail/' . (int) $so['ID']) ?>"><?= esc($so['ProjectName']) ?></a>
                                        <?php if (!empty($so['Keterangan'])): ?>
                                            <span class="so-cell-sub"><?= esc(mb_strimwidth($so['Keterangan'], 0, 80, '…')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($so['Tahun']) ?></td>
                                    <td><?= esc($so['Koordinator']) ?></td>
                                    <td><?= $so['TglMulai'] ? date('d/m/Y', strtotime($so['TglMulai'])) : '-' ?></td>
                                    <td class="text-end"><?= number_format($scan) ?> koleksi</td>
                                    <td>
                                        <div class="ina-actions">
                                            <a href="<?= base_url('stockopname/detail/' . (int) $so['ID']) ?>" class="ina-action" title="Lihat detail" aria-label="Lihat detail <?= esc($so['ProjectName'], 'attr') ?>">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a href="<?= base_url('stockopname/edit/' . (int) $so['ID']) ?>" class="ina-action" title="Ubah" aria-label="Ubah <?= esc($so['ProjectName'], 'attr') ?>">
                                                <i class="fas fa-pen" aria-hidden="true"></i>
                                            </a>
                                            <button type="button" class="ina-action ina-action-danger so-delete" title="Hapus"
                                                    aria-label="Hapus <?= esc($so['ProjectName'], 'attr') ?>"
                                                    data-id="<?= (int) $so['ID'] ?>"
                                                    data-name="<?= esc($so['ProjectName'], 'attr') ?>"
                                                    data-scan="<?= (int) $scan ?>">
                                                <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="ina-table-pagination">
                        <div class="ina-table-pagination__rows">
                            Menampilkan <?= number_format($firstRow + 1) ?>–<?= number_format($firstRow + count($stockopnames)) ?> dari <?= number_format($total) ?>
                        </div>
                        <div class="ina-table-pagination__end"><?= $pager->links('default', 'ina_pagination') ?></div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="so-empty">
                    <i class="fa fa-clipboard-list" aria-hidden="true"></i>
                    <strong><?= ($search !== '' || $tahunAktif !== '') ? 'Tidak ada stock opname yang cocok' : 'Belum ada stock opname' ?></strong>
                    <p class="mb-3"><?= ($search !== '' || $tahunAktif !== '') ? 'Ubah kata kunci atau tahun, lalu cari lagi.' : 'Buat proyek stock opname untuk mulai memindai koleksi.' ?></p>
                    <a href="<?= base_url('stockopname/create') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Stock Opname</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Konfirmasi hapus (INA Modal varian Confirm) -->
<div class="modal fade so-confirm" id="soDeleteModal" tabindex="-1" role="dialog" aria-labelledby="soDeleteTitle" aria-describedby="soDeleteDesc" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered so-confirm__dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="so-confirm__icon" aria-hidden="true"><i class="fas fa-trash-alt"></i></div>
                <h5 class="so-confirm__title" id="soDeleteTitle">Hapus stock opname?</h5>
                <p class="so-confirm__desc" id="soDeleteDesc"></p>
                <p class="so-confirm__error" id="soDeleteError" role="alert" hidden></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn so-btn-danger" id="soDeleteConfirm">Hapus</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
(function () {
    const modalEl = document.getElementById('soDeleteModal');
    // Modal dipindah ke <body> agar tidak tertutup backdrop (wrapper konten punya stacking context sendiri)
    document.body.appendChild(modalEl);
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const descEl = document.getElementById('soDeleteDesc');
    const errorEl = document.getElementById('soDeleteError');
    const confirmBtn = document.getElementById('soDeleteConfirm');
    const alertSlot = document.getElementById('soAlert');
    let target = null;

    function showAlert(type, text) {
        alertSlot.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'so-alert so-alert--' + type;
        div.textContent = text;
        alertSlot.appendChild(div);
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.so-delete');
        if (!btn) return;
        target = btn;
        const scan = parseInt(btn.dataset.scan, 10) || 0;
        descEl.textContent = '"' + btn.dataset.name + '" akan dihapus permanen'
            + (scan > 0 ? ' beserta ' + scan.toLocaleString('id-ID') + ' hasil scan koleksinya.' : '.')
            + ' Data koleksi tidak berubah. Tindakan ini tidak bisa dibatalkan.';
        errorEl.hidden = true;
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Hapus';
        modal.show();
    });

    modalEl.addEventListener('shown.bs.modal', function () {
        modalEl.querySelector('[data-bs-dismiss="modal"]').focus();
    });
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (target) target.focus();
    });

    confirmBtn.addEventListener('click', function () {
        if (!target) return;
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Menghapus…';
        errorEl.hidden = true;

        const body = new URLSearchParams();
        body.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('stockopname/delete') ?>/' + encodeURIComponent(target.dataset.id), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.status === 'success') {
                modal.hide();
                showAlert('success', res.message);
                // Muat ulang agar nomor urut, jumlah, dan halaman dihitung ulang server
                setTimeout(function () { window.location.reload(); }, 1200);
            } else {
                errorEl.textContent = res.message || 'Gagal menghapus data stock opname.';
                errorEl.hidden = false;
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Hapus';
            }
        })
        .catch(function () {
            errorEl.textContent = 'Koneksi bermasalah. Data belum dihapus, silakan coba lagi.';
            errorEl.hidden = false;
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Hapus';
        });
    });
})();
</script>
<?= $this->endSection('script'); ?>
