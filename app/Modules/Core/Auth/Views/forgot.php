<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<?= view('Auth\Views\partials\auth_card_style') ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<main class="login-container">
    <div class="login-card">
        <form action="<?= base_url('login/forgot') ?>" method="post" id="forgotForm">
            <?= csrf_field() ?>
            <img class="login-logo"
                 src="<?= esc(!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'), 'attr') ?>"
                 alt="Logo perpustakaan" width="108" height="88" decoding="async">
            <h1 class="login-title">Lupa kata sandi</h1>
            <p class="login-description">Masukkan username atau email akun Anda. Kami akan mengirim link untuk mengatur ulang kata sandi ke email yang terdaftar.</p>

            <div id="infoMessage" role="status" aria-live="polite" aria-atomic="true">
                <?= view('Myth\Auth\Views\_message_block') ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="identity">Username atau email</label>
                <input type="text" class="form-control" name="identity" id="identity" maxlength="255"
                       value="<?= esc(old('identity') ?? '', 'attr') ?>"
                       placeholder="Masukkan username atau email" required autocomplete="username">
            </div>

            <button type="submit" class="login-button" id="forgotBtn">Kirim link reset</button>
            <a class="back-link" href="<?= base_url('login') ?>">&larr; Kembali ke halaman masuk</a>
        </form>
    </div>
    <p class="login-copyright"><?= date('Y') ?> Copyright &copy; Perpustakaan Nasional RI</p>
</main>

<script>
document.getElementById('forgotForm').addEventListener('submit', function() {
    const button = document.getElementById('forgotBtn');
    button.textContent = 'Mengirim...';
    button.setAttribute('aria-busy', 'true');
    button.disabled = true;
});
</script>
<?= $this->endSection('page'); ?>
