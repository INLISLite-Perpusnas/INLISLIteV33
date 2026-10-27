<?= $this->extend('App\Views\layout\opac\layout') ?>

<?= $this->section('style') ?>
<style>
    .survey-page .section-title { color: #1b3878; }
    .survey-page {
        padding-top: 88px;
        min-height: 80vh;
        background: linear-gradient(160deg, #eff6ff 0%, #fff 55%, #f1f5f9 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="survey-page">
    <?= $this->include('SurveiPemustaka\Views\public_form') ?>
</div>
<?= $this->endSection() ?>
