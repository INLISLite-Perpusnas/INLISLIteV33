<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('page'); ?>
<?php $flag = static function ($value): bool { return $value === 1 || $value === '1' || $value === "\x01" || $value === true; }; ?>
<div class="app-main__inner">
    <div class="app-page-title"><div class="page-title-wrapper"><div class="page-title-heading">
        <div class="page-title-icon"><i class="pe-7s-note2 icon-gradient bg-strong-bliss"></i></div>
        <div>Survei Pemustaka<div class="page-title-subheading">Kelola survei, pertanyaan, dan pilihan jawaban.</div></div>
    </div></div></div>
    <?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
    <?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
    <div class="card mb-3"><div class="card-header">Daftar Survei</div><div class="card-body">
        <a href="<?= site_url('home') ?>#survei" class="btn btn-outline-primary btn-sm mb-3" target="_blank" rel="noopener">Lihat form di Home</a>
        <div class="list-group">
            <?php foreach ($surveys as $entry): ?>
                <a class="list-group-item list-group-item-action <?= (int) ($selectedSurvey['ID'] ?? 0) === (int) $entry['ID'] ? 'active' : '' ?>"
                    href="<?= site_url('surveipemustaka/manage') . '?survey=' . (int) $entry['ID'] ?>">
                    <?= esc($entry['NamaSurvey']) ?>
                    <small class="float-right"><?= esc($entry['TanggalMulai']) ?> - <?= esc($entry['TanggalSelesai']) ?></small>
                </a>
            <?php endforeach; ?>
            <?php if (!$surveys): ?><p class="text-muted mb-0">Belum ada survei.</p><?php endif; ?>
        </div>
    </div></div>
    <?php if ($canManage): ?>
        <div class="card mb-3"><div class="card-header"><?= $selectedSurvey ? 'Ubah Survei' : 'Buat Survei' ?></div><div class="card-body">
            <form method="post" action="<?= site_url('surveipemustaka/save-survey') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="ID" value="<?= (int) ($selectedSurvey['ID'] ?? 0) ?>">
                <div class="form-row">
                    <div class="form-group col-md-6"><label>Nama survei</label><input class="form-control" name="NamaSurvey" maxlength="200" required value="<?= esc($selectedSurvey['NamaSurvey'] ?? '', 'attr') ?>"></div>
                    <div class="form-group col-md-3"><label>Mulai</label><input class="form-control" type="datetime-local" name="TanggalMulai" required value="<?= esc(isset($selectedSurvey['TanggalMulai']) ? date('Y-m-d\TH:i', strtotime($selectedSurvey['TanggalMulai'])) : date('Y-m-d\TH:i'), 'attr') ?>"></div>
                    <div class="form-group col-md-3"><label>Selesai</label><input class="form-control" type="datetime-local" name="TanggalSelesai" required value="<?= esc(isset($selectedSurvey['TanggalSelesai']) ? date('Y-m-d\TH:i', strtotime($selectedSurvey['TanggalSelesai'])) : date('Y-m-d\TH:i', strtotime('+30 days')), 'attr') ?>"></div>
                    <div class="form-group col-md-3"><label>Status</label><select class="form-control" name="IsActive">
                        <option value="0" <?= !$flag($selectedSurvey['IsActive'] ?? null) ? 'selected' : '' ?>>Tidak aktif</option>
                        <option value="1" <?= $flag($selectedSurvey['IsActive'] ?? null) ? 'selected' : '' ?>>Aktif</option>
                    </select></div>
                    <div class="form-group col-md-3"><label>Responden</label><select class="form-control" name="TargetSurvey">
                        <option value="0" <?= (int) ($selectedSurvey['TargetSurvey'] ?? 0) === 0 ? 'selected' : '' ?>>Semua pengunjung</option>
                        <option value="1" <?= (int) ($selectedSurvey['TargetSurvey'] ?? 0) === 1 ? 'selected' : '' ?>>Anggota login</option>
                    </select></div>
                    <div class="form-group col-md-2"><label>Urutan</label><input class="form-control" type="number" name="NomorUrut" value="<?= (int) ($selectedSurvey['NomorUrut'] ?? 99) ?>"></div>
                    <div class="form-group col-md-12"><label>Pengantar</label><textarea name="RedaksiAwal" class="form-control" rows="2"><?= esc($selectedSurvey['RedaksiAwal'] ?? '') ?></textarea></div>
                    <div class="form-group col-md-12"><label>Penutup</label><textarea name="RedaksiAkhir" class="form-control" rows="2"><?= esc($selectedSurvey['RedaksiAkhir'] ?? '') ?></textarea></div>
                </div>
                <button class="btn btn-primary" type="submit">Simpan Survei</button>
                <?php if ($selectedSurvey): ?><a class="btn btn-outline-secondary" href="<?= site_url('surveipemustaka/manage') . '?survey=0&new=1' ?>">Survei Baru</a><?php endif; ?>
            </form>
        </div></div>
    <?php endif; ?>
    <?php if ($selectedSurvey): ?>
        <div class="card mb-3"><div class="card-header">Pertanyaan: <?= esc($selectedSurvey['NamaSurvey']) ?></div><div class="card-body">
            <?php foreach ($questions as $question): ?>
                <div class="border rounded p-3 mb-3">
                    <h6><?= esc($question['Pertanyaan']) ?></h6>
                    <?php if ($canManage): ?>
                        <form method="post" action="<?= site_url('surveipemustaka/save-question') ?>" class="mb-3">
                            <?= csrf_field() ?><input type="hidden" name="ID" value="<?= (int) $question['ID'] ?>">
                            <input type="hidden" name="Survey_id" value="<?= (int) $selectedSurvey['ID'] ?>">
                            <div class="form-row">
                                <div class="form-group col-md-12"><label>Pertanyaan</label><textarea name="Pertanyaan" class="form-control" maxlength="2000" required><?= esc($question['Pertanyaan']) ?></textarea></div>
                                <div class="form-group col-md-3"><label>Jenis</label><select name="JenisPertanyaan" class="form-control"><option value="Pilihan" <?= $question['JenisPertanyaan'] === 'Pilihan' ? 'selected' : '' ?>>Pilihan</option><option value="Isian" <?= $question['JenisPertanyaan'] === 'Isian' ? 'selected' : '' ?>>Isian</option></select></div>
                                <div class="form-group col-md-2"><label>Urutan</label><input type="number" name="NoUrut" class="form-control" value="<?= (int) $question['NoUrut'] ?>"></div>
                                <div class="form-group col-md-2"><label>Wajib?</label><select name="IsMandatory" class="form-control"><option value="1" <?= $flag($question['IsMandatory']) ? 'selected' : '' ?>>Ya</option><option value="0" <?= !$flag($question['IsMandatory']) ? 'selected' : '' ?>>Tidak</option></select></div>
                                <div class="form-group col-md-2"><label>Jawaban jamak?</label><select name="IsCanMultipleAnswer" class="form-control"><option value="0">Tidak</option><option value="1" <?= $flag($question['IsCanMultipleAnswer']) ? 'selected' : '' ?>>Ya</option></select></div>
                                <div class="form-group col-md-2"><label>Arah</label><select name="Orientation" class="form-control"><option value="Vertikal">Vertikal</option><option value="Horizontal" <?= $question['Orientation'] === 'Horizontal' ? 'selected' : '' ?>>Horizontal</option></select></div>
                                <div class="form-group col-md-1"><label>Status</label><select name="active" class="form-control"><option value="1">Aktif</option><option value="0" <?= !$flag($question['active']) ? 'selected' : '' ?>>Nonaktif</option></select></div>
                            </div><button type="submit" class="btn btn-sm btn-outline-primary">Simpan Pertanyaan</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($question['JenisPertanyaan'] === 'Pilihan'): ?>
                        <strong>Pilihan jawaban</strong>
                        <?php foreach ($question['options'] as $option): ?>
                            <?php if ($canManage): ?>
                                <form method="post" action="<?= site_url('surveipemustaka/save-option') ?>" class="form-inline my-2">
                                    <?= csrf_field() ?><input type="hidden" name="ID" value="<?= (int) $option['ID'] ?>"><input type="hidden" name="Survey_Pertanyaan_id" value="<?= (int) $question['ID'] ?>">
                                    <input class="form-control mr-2" name="Pilihan" maxlength="2000" required value="<?= esc($option['Pilihan'], 'attr') ?>">
                                    <select name="active" class="form-control mr-2"><option value="1">Aktif</option><option value="0" <?= !$flag($option['active']) ? 'selected' : '' ?>>Nonaktif</option></select>
                                    <span class="mr-2"><?= (int) $option['ChoosenCount'] ?> respon</span><button class="btn btn-sm btn-outline-primary">Simpan</button>
                                </form>
                            <?php else: ?><p><?= esc($option['Pilihan']) ?></p><?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($canManage): ?>
                            <form method="post" action="<?= site_url('surveipemustaka/save-option') ?>" class="form-inline mt-3">
                                <?= csrf_field() ?><input type="hidden" name="Survey_Pertanyaan_id" value="<?= (int) $question['ID'] ?>">
                                <input class="form-control mr-2" name="Pilihan" maxlength="2000" required placeholder="Pilihan baru">
                                <button class="btn btn-sm btn-success">Tambah Pilihan</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($canManage): ?>
                <h6>Tambah Pertanyaan</h6>
                <form method="post" action="<?= site_url('surveipemustaka/save-question') ?>">
                    <?= csrf_field() ?><input type="hidden" name="Survey_id" value="<?= (int) $selectedSurvey['ID'] ?>">
                    <div class="form-row">
                        <div class="form-group col-md-12"><textarea class="form-control" name="Pertanyaan" maxlength="2000" required placeholder="Isi pertanyaan"></textarea></div>
                        <div class="form-group col-md-3"><label>Jenis</label><select class="form-control" name="JenisPertanyaan"><option value="Pilihan">Pilihan</option><option value="Isian">Isian</option></select></div>
                        <div class="form-group col-md-2"><label>Urutan</label><input class="form-control" type="number" name="NoUrut" value="<?= count($questions) + 1 ?>"></div>
                        <div class="form-group col-md-2"><label>Wajib?</label><select name="IsMandatory" class="form-control"><option value="1">Ya</option><option value="0">Tidak</option></select></div>
                        <div class="form-group col-md-3"><label>Jawaban jamak?</label><select name="IsCanMultipleAnswer" class="form-control"><option value="0">Tidak</option><option value="1">Ya</option></select></div>
                        <div class="form-group col-md-2"><label>Arah</label><select name="Orientation" class="form-control"><option value="Vertikal">Vertikal</option><option value="Horizontal">Horizontal</option></select></div>
                    </div><button class="btn btn-success" type="submit">Tambah Pertanyaan</button>
                </form>
            <?php endif; ?>
        </div></div>
    <?php endif; ?>
</div>
<?= $this->endSection('page'); ?>