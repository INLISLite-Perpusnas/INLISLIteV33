<?php
$field = function (string $label, string $name, string $input) use ($errors) {
    return view('SurveiPemustaka\Views\_field', compact('label', 'name', 'input', 'errors'));
};
$v = static function (string $name) use ($values) {
    return esc((string) ($values[$name] ?? ''), 'attr');
};
$invalid = static function (string $name) use ($errors) {
    return isset($errors[$name]) ? ' is-invalid' : '';
};
$options = static function (array $items, $selected) {
    $html = '';
    foreach ($items as $key => $label) {
        $html .= '<option value="' . esc((string) $key, 'attr') . '"' . ((string) $key === (string) $selected ? ' selected' : '') . '>' . esc($label) . '</option>';
    }
    return $html;
};
?>
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => $title,
        'subtitle' => $survey ? $survey['NamaSurvey'] : 'Tambah survey baru',
        'crumbs' => ['Survey' => site_url('surveipemustaka'), $title => null],
    ]) ?>

    <form method="post" action="<?= $action ?>" class="main-card mb-3 card">
        <?= csrf_field() ?>
        <div class="card-header">
            <button type="submit" class="btn btn-catalog-add">Simpan</button>
            &nbsp;<a href="<?= site_url('surveipemustaka') ?>" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <?= $field('Nama Survey', 'NamaSurvey',
                '<input type="text" class="form-control' . $invalid('NamaSurvey') . '" id="frm_NamaSurvey" name="NamaSurvey" maxlength="200" placeholder="Masukkan Nama Survey..." value="' . $v('NamaSurvey') . '">') ?>
            <?= $field('Tgl.Mulai', 'TglMulai',
                '<input type="text" class="form-control datepicker' . $invalid('TglMulai') . '" id="frm_TglMulai" name="TglMulai" placeholder="dd-mm-yyyy" autocomplete="off" value="' . $v('TglMulai') . '">') ?>
            <?= $field('Tanggal Selesai', 'TglSelesai',
                '<input type="text" class="form-control datepicker' . $invalid('TglSelesai') . '" id="frm_TglSelesai" name="TglSelesai" placeholder="dd-mm-yyyy" autocomplete="off" value="' . $v('TglSelesai') . '">') ?>
            <?= $field('Aktif', 'IsActive',
                '<div class="custom-control custom-checkbox mt-2"><input type="hidden" name="IsActive" value="0">'
                . '<input type="checkbox" class="custom-control-input" id="frm_IsActive" name="IsActive" value="1"' . (($values['IsActive'] ?? '0') === '1' ? ' checked' : '') . '>'
                . '<label class="custom-control-label" for="frm_IsActive">Aktif</label></div>') ?>
            <?= $field('Nomor Urut', 'NomorUrut',
                '<input type="number" class="form-control" id="frm_NomorUrut" name="NomorUrut" placeholder="Masukkan Nomor Urut..." value="' . $v('NomorUrut') . '">') ?>
            <?= $field('Responden', 'TargetSurvey',
                '<select class="form-control" id="frm_TargetSurvey" name="TargetSurvey">' . $options($targetSurvey, $values['TargetSurvey'] ?? '1') . '</select>') ?>
            <?= $field('Perlihatkan Hasil Survey', 'HasilSurveyShow',
                '<select class="form-control" id="frm_HasilSurveyShow" name="HasilSurveyShow">' . $options($hasilSurveyShow, $values['HasilSurveyShow'] ?? '1') . '</select>') ?>
            <?= $field('Redaksi Awal', 'RedaksiAwal',
                '<textarea class="form-control" id="frm_RedaksiAwal" name="RedaksiAwal" rows="6" placeholder="Masukkan Redaksi Awal...">' . esc((string) ($values['RedaksiAwal'] ?? '')) . '</textarea>') ?>
            <?= $field('Redaksi Akhir', 'RedaksiAkhir',
                '<textarea class="form-control" id="frm_RedaksiAkhir" name="RedaksiAkhir" rows="6" placeholder="Masukkan Redaksi Akhir...">' . esc((string) ($values['RedaksiAkhir'] ?? '')) . '</textarea>') ?>
            <?= $field('Keterangan', 'Keterangan',
                '<textarea class="form-control' . $invalid('Keterangan') . '" id="frm_Keterangan" name="Keterangan" maxlength="255" placeholder="Masukkan Keterangan...">' . esc((string) ($values['Keterangan'] ?? '')) . '</textarea>') ?>
        </div>
    </form>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(function() {
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoHide: true,
        });
    });
</script>
<?= $this->endSection('script'); ?>
