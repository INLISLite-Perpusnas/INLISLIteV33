<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<style>
    .login-container {
        --login-blue: #0343a7;
        min-height: 100vh;
        min-height: 100dvh;
        padding: 48px 20px 24px;
        background: #f7f7f7;
        color: #1d1d1d;
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        width: 100%;
        max-width: 610px;
        padding: 34px 35px 25px;
        background: #fff;
        border-radius: 14px;
    }
    .login-logo {
        display: block;
        width: 108px;
        height: 88px;
        object-fit: contain;
        object-position: left center;
        margin-bottom: 27px;
    }
    .login-title {
        margin: 0 0 12px;
        color: #111;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.3;
    }
    .login-description {
        margin: 0 0 34px;
        color: #595959;
        font-size: 18px;
        line-height: 1.5;
    }
    .login-container .form-group { margin-bottom: 18px; }
    .login-container .form-label {
        display: block;
        margin-bottom: 8px;
        color: #222;
        font-size: 14px;
        font-weight: 500;
    }
    .login-container .form-control {
        width: 100%;
        height: 48px;
        padding: 11px 16px;
        border: 1px solid #dedede;
        border-radius: 9px;
        background: #fff;
        color: #1d1d1d;
        font-size: 15px;
        box-shadow: none;
    }
    .login-container .form-control::placeholder { color: #929292; opacity: 1; }
    .login-container .form-control:focus {
        border-color: var(--login-blue);
        box-shadow: 0 0 0 3px rgba(3, 67, 167, .15);
    }
    .password-field { position: relative; }
    .password-field .form-control { padding-right: 56px; }
    .password-toggle {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #292929;
        cursor: pointer;
    }
    .password-toggle:hover { color: var(--login-blue); }
    .forgot-link {
        display: inline-block;
        margin-top: 8px;
        color: var(--login-blue);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .forgot-link:hover { color: #002f79; text-decoration: underline; }
    .hcaptcha-container { margin: 32px 0; }
    .login-button {
        display: block;
        width: 100%;
        min-height: 52px;
        padding: 12px 20px;
        border: 1px solid var(--login-blue);
        border-radius: 8px;
        background: var(--login-blue);
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .login-button:hover:not(:disabled) { background: #002f79; border-color: #002f79; }
    .login-button:disabled { background: var(--login-blue); cursor: not-allowed; }
    .login-button.loading { cursor: progress; }
    .login-container .alert { margin-bottom: 18px; border-radius: 8px; font-size: 14px; }
    .password-toggle:focus-visible, .forgot-link:focus-visible, .login-button:focus-visible {
        outline: 3px solid var(--login-blue);
        outline-offset: 3px;
    }
    .login-copyright { margin: 24px 0 0; color: #666; font-size: 13px; text-align: center; }
    @media (max-width: 600px) {
        .login-container { padding: 20px 16px; }
        .login-card { padding: 28px 20px; }
        .login-description { font-size: 16px; }
    }
    @media (max-width: 360px) {
        .hcaptcha-container { overflow-x: auto; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<main class="login-container">
    <div class="login-card">
        <form action="<?= route_to('login') ?>" method="post" id="loginForm">
            <?= csrf_field() ?>
            <img class="login-logo"
                 src="<?= esc(!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'), 'attr') ?>"
                 alt="Logo perpustakaan" width="108" height="88" fetchpriority="high" decoding="async">
            <h1 class="login-title">Masuk ke INLISLite</h1>
            <p class="login-description"><?= esc(get_parameter('site-description', 'Sistem Manajemen Perpustakaan')) ?></p>

            <div id="infoMessage" role="status" aria-live="polite" aria-atomic="true">
                <?= view('Myth\Auth\Views\_message_block') ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="login">Username</label>
                <input type="text" class="form-control" name="login" id="login"
                       placeholder="Masukkan username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Kata sandi</label>
                <div class="password-field">
                    <input type="password" class="form-control" name="password" id="password"
                           placeholder="Masukkan kata sandi" required autocomplete="current-password">
                    <button type="button" class="password-toggle" id="togglePasswordButton"
                            aria-label="Tampilkan kata sandi" aria-controls="password" aria-pressed="false">
                        <i class="fas fa-eye" id="toggleIcon" aria-hidden="true"></i>
                    </button>
                </div>
                <a class="forgot-link" href="<?= route_to('forgot') ?>">Lupa kata sandi?</a>
            </div>

            <div class="hcaptcha-container" aria-label="Verifikasi keamanan">
                <?php if (!empty($hcaptcha_site_key)): ?>
                    <div class="h-captcha" data-sitekey="<?= esc($hcaptcha_site_key, 'attr') ?>"
                         data-callback="onHcaptchaSuccess" data-expired-callback="onHcaptchaExpired"
                         data-error-callback="onHcaptchaError"></div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">hCaptcha belum dikonfigurasi. Hubungi administrator.</div>
                <?php endif; ?>
            </div>
            <div id="captchaStatus" class="visually-hidden" role="status" aria-live="polite"></div>
            <button type="submit" class="login-button" id="loginBtn" disabled>Masuk</button>
        </form>
    </div>
    <p class="login-copyright"><?= date('Y') ?> Copyright &copy; Perpustakaan Nasional RI</p>
</main>

<?php if (!empty($hcaptcha_site_key)): ?>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<?php endif; ?>
<script>
function onHcaptchaSuccess() {
    document.getElementById('loginBtn').disabled = false;
    document.getElementById('captchaStatus').textContent = 'Verifikasi keamanan berhasil.';
}
function onHcaptchaExpired() {
    document.getElementById('loginBtn').disabled = true;
    document.getElementById('captchaStatus').textContent = 'Verifikasi keamanan kedaluwarsa. Silakan verifikasi kembali.';
}
function onHcaptchaError() {
    document.getElementById('loginBtn').disabled = true;
    document.getElementById('captchaStatus').textContent = 'Verifikasi keamanan gagal dimuat. Silakan coba kembali.';
}
document.getElementById('togglePasswordButton').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const showing = passwordInput.type === 'password';
    passwordInput.type = showing ? 'text' : 'password';
    document.getElementById('toggleIcon').className = showing ? 'fas fa-eye-slash' : 'fas fa-eye';
    this.setAttribute('aria-label', showing ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    this.setAttribute('aria-pressed', String(showing));
});
document.getElementById('loginForm').addEventListener('submit', function(event) {
    <?php if (!empty($hcaptcha_site_key)): ?>
    if (typeof hcaptcha === 'undefined') {
        event.preventDefault();
        document.getElementById('captchaStatus').textContent = 'hCaptcha belum dimuat. Muat ulang halaman dan coba lagi.';
        return;
    }
    if (!hcaptcha.getResponse()) {
        event.preventDefault();
        document.getElementById('captchaStatus').textContent = 'Selesaikan verifikasi hCaptcha terlebih dahulu.';
        return;
    }
    <?php endif; ?>
    const loginButton = document.getElementById('loginBtn');
    loginButton.classList.add('loading');
    loginButton.textContent = 'Memproses...';
    loginButton.setAttribute('aria-busy', 'true');
    loginButton.disabled = true;
});
</script>
<?= $this->endSection('page'); ?>