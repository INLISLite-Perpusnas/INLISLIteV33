<?php
$field = function (string $label, string $name, string $input) use ($errors) {
    return view('SurveiPemustaka\Views\_field', compact('label', 'name', 'input', 'errors'));
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
$checkbox = static function (string $name, string $label) use ($values) {
    return '<div class="custom-control custom-checkbox mt-2"><input type="hidden" name="' . $name . '" value="0">'
        . '<input type="checkbox" class="custom-control-input" id="frm_' . $name . '" name="' . $name . '" value="1"' . (($values[$name] ?? '0') === '1' ? ' checked' : '') . '>'
        . '<label class="custom-control-label" for="frm_' . $name . '">' . esc($label) . '</label></div>';
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
        'subtitle' => $survey['NamaSurvey'],
        'crumbs' => [
            'Survey' => site_url('surveipemustaka'),
            'Survey Pertanyaan' => $backUrl,
            $title => null,
        ],
    ]) ?>

    <form method="post" action="<?= $action ?>" class="main-card mb-3 card">
        <?= csrf_field() ?>
        <div class="card-header">
            <button type="submit" class="btn btn-catalog-add">Simpan</button>
            &nbsp;<a href="<?= $backUrl ?>" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <?= $field('Pertanyaan', 'Pertanyaan',
                '<textarea class="form-control' . $invalid('Pertanyaan') . '" id="frm_Pertanyaan" name="Pertanyaan" rows="6" placeholder="Masukkan Pertanyaan...">' . esc((string) ($values['Pertanyaan'] ?? '')) . '</textarea>') ?>
            <?= $field('Wajib diisi', 'IsMandatory', $checkbox('IsMandatory', 'Wajib diisi')) ?>
            <?= $field('Jawaban bisa lebih dari satu', 'IsCanMultipleAnswer', $checkbox('IsCanMultipleAnswer', 'Jawaban bisa lebih dari satu')) ?>
            <?= $field('Jenis Pertanyaan', 'JenisPertanyaan',
                '<select class="form-control' . $invalid('JenisPertanyaan') . '" id="frm_JenisPertanyaan" name="JenisPertanyaan">' . $options($jenisPertanyaan, $values['JenisPertanyaan'] ?? 'Pilihan') . '</select>') ?>
            <?= $field('Orientation', 'Orientation',
                '<select class="form-control' . $invalid('Orientation') . '" id="frm_Orientation" name="Orientation">' . $options($orientation, $values['Orientation'] ?? 'Vertikal') . '</select>') ?>
            <?= $field('No Urut', 'NoUrut',
                '<input type="number" class="form-control" id="frm_NoUrut" name="NoUrut" placeholder="Masukkan No Urut..." value="' . esc((string) ($values['NoUrut'] ?? ''), 'attr') . '">') ?>
        </div>
    </form>
</div>
<?= $this->endSection('page'); ?>
