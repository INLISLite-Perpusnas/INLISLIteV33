<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<?= view('Auth\Views\partials\auth_card_style') ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<main class="login-container">
    <div class="login-card">
        <form action="<?= base_url('login/reset-password') ?>" method="post" id="resetForm">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token ?? '', 'attr') ?>">
            <img class="login-logo"
                 src="<?= esc(!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'), 'attr') ?>"
                 alt="Logo perpustakaan" width="108" height="88" decoding="async">
            <h1 class="login-title">Atur ulang kata sandi</h1>
            <p class="login-description">Buat kata sandi baru untuk akun Anda.</p>

            <div id="infoMessage" role="status" aria-live="polite" aria-atomic="true">
                <?= view('Myth\Auth\Views\_message_block') ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata sandi baru</label>
                <input type="password" class="form-control" name="password" id="password" minlength="8" maxlength="72"
                       placeholder="Minimal 8 karakter" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label class="form-label" for="pass_confirm">Ulangi kata sandi baru</label>
                <input type="password" class="form-control" name="pass_confirm" id="pass_confirm" minlength="8" maxlength="72"
                       placeholder="Ketik ulang kata sandi baru" required autocomplete="new-password">
            </div>

            <button type="submit" class="login-button">Simpan kata sandi</button>
            <a class="back-link" href="<?= base_url('login') ?>">&larr; Kembali ke halaman masuk</a>
        </form>
    </div>
    <p class="login-copyright"><?= date('Y') ?> Copyright &copy; Perpustakaan Nasional RI</p>
</main>

<script>
document.getElementById('resetForm').addEventListener('submit', function(event) {
    const password = document.getElementById('password');
    const confirm = document.getElementById('pass_confirm');
    if (password.value !== confirm.value) {
        event.preventDefault();
        confirm.setCustomValidity('Kata sandi tidak sama.');
        confirm.reportValidity();
    }
});
document.getElementById('pass_confirm').addEventListener('input', function() {
    this.setCustomValidity('');
});
</script>
<?= $this->endSection('page'); ?>
