<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <?= view('SurveiPemustaka\Views\_header', [
        'title' => $title,
        'subtitle' => $pertanyaan['Pertanyaan'],
        'crumbs' => [
            'Survey' => site_url('surveipemustaka'),
            'Survey Pertanyaan' => site_url('surveipemustaka/pertanyaan/' . $pertanyaan['Survey_id']),
            'Survey Pilihan' => $backUrl,
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
            <?= view('SurveiPemustaka\Views\_field', [
                'label' => 'Pilihan',
                'name' => 'Pilihan',
                'errors' => $errors,
                'input' => '<textarea class="form-control' . (isset($errors['Pilihan']) ? ' is-invalid' : '') . '" id="frm_Pilihan" name="Pilihan" rows="6" placeholder="Masukkan Pilihan...">'
                    . esc((string) ($values['Pilihan'] ?? '')) . '</textarea>',
            ]) ?>
        </div>
    </form>
</div>
<?= $this->endSection('page'); ?>
