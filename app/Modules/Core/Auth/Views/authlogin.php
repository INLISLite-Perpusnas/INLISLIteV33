<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    * {
        font-family: 'Inter', sans-serif;
    }

    .login-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="600" cy="700" r="120" fill="url(%23a)"/><circle cx="300" cy="800" r="80" fill="url(%23a)"/></svg>') no-repeat center center;
        background-size: cover;
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    .login-card {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 40px;
        width: 100%;
        max-width: 440px;
        transition: all 0.3s ease;
        animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-card:hover {
        box-shadow: 0 35px 70px rgba(0, 0, 0, 0.2);
        transform: translateY(-5px);
    }

    .logo-container {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #0343A7, #00983A);
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .logo-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shine 3s ease-in-out infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    }

    .logo-icon i {
        color: white;
        font-size: 32px;
        z-index: 1;
    }

    .app-title {
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .app-subtitle {
        color: #718096;
        font-size: 16px;
        font-weight: 400;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .form-label {
        display: block;
        color: #4a5568;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        font-size: 16px;
        color: #2d3748;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .form-control:focus {
        outline: none;
        border-color: #0343A7;
        box-shadow: 0 0 0 3px rgba(3, 67, 167, 0.1);
        background: #ffffff;
    }

    .form-control::placeholder {
        color: #a0aec0;
        font-weight: 400;
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 18px;
        transition: color 0.3s ease;
        z-index: 1;
    }

    .form-control:focus + .input-icon {
        color: #0343A7;
    }

    /* reCAPTCHA Container Styling */
    .recaptcha-container {
        margin: 24px 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .g-recaptcha {
        transform: scale(0.9);
        transform-origin: center;
    }

    @media (max-width: 480px) {
        .g-recaptcha {
            transform: scale(0.8);
        }
    }

    .btn-login {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #0343A7, #00983A);
        border: none;
        border-radius: 16px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-top: 8px;
    }

    .btn-login:disabled {
        background: #cbd5e0;
        cursor: not-allowed;
        transform: none;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s;
    }

    .btn-login:hover:not(:disabled)::before {
        left: 100%;
    }

    .btn-login:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(3, 67, 167, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .copyright {
        text-align: center;
        margin-top: 30px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        font-weight: 400;
    }

    .alert {
        border-radius: 12px;
        border: none;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fed7d7, #feb2b2);
        color: #c53030;
    }

    .alert-success {
        background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
        color: #2f855a;
    }

    .alert-info {
        background: linear-gradient(135deg, #bee3f8, #90cdf4);
        color: #2b6cb0;
    }

    .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .floating-elements::before,
    .floating-elements::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        animation: floatUpDown 6s ease-in-out infinite;
    }

    .floating-elements::before {
        width: 200px;
        height: 200px;
        top: 20%;
        left: 10%;
        animation-delay: -2s;
    }

    .floating-elements::after {
        width: 150px;
        height: 150px;
        bottom: 20%;
        right: 15%;
        animation-delay: -4s;
    }

    @keyframes floatUpDown {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(180deg); }
    }

    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(3, 67, 167, 0.1);
        color: #0343A7;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .login-card {
            padding: 30px 24px;
            margin: 20px;
        }
        
        .app-title {
            font-size: 24px;
        }
        
        .form-control {
            padding: 14px 18px 14px 45px;
        }
        
        .btn-login {
            padding: 14px;
        }
    }

    /* Loading Animation */
    .btn-login.loading {
        pointer-events: none;
        color: transparent;
    }

    .btn-login.loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="login-container">
    <div class="floating-elements"></div>
    
    <div class="d-flex align-items-center justify-content-center min-vh-100 position-relative" style="z-index: 1;">
        <div class="login-card">
            <form action="<?= route_to('login') ?>" method="post" id="loginForm">
                <?= csrf_field() ?>
                
                <!-- Logo and Title -->
                <div class="logo-container">
                    <img style="width: 80px; height: 80px; object-fit: contain; border-radius: 16px; margin-bottom: 20px;" 
                         src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>" 
                         alt="Logo">
                    
                    <h1 class="app-title">Login INLISLite</h1>
                    <p class="app-subtitle">
                        <?= get_parameter('site-description', 'Sistem Manajemen Perpustakaan') ?>
                    </p>
                    
                    <div class="feature-badge">
                        <i class="fas fa-shield-alt"></i>
                        Secure Access
                    </div>
                </div>

                <!-- Messages -->
                <div id="infoMessage">
                    <?= view('Myth\Auth\Views\_message_block') ?>
                </div>

                <!-- Username Field -->
                <div class="form-group">
                    <label class="form-label" for="login">Username</label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control" 
                               name="login" 
                               id="login"
                               placeholder="Masukkan username Anda"
                               required
                               autocomplete="username">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="position-relative">
                        <input type="password" 
                               class="form-control" 
                               name="password" 
                               id="password"
                               placeholder="Masukkan password Anda"
                               required
                               autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" 
                                class="btn btn-link position-absolute" 
                                style="right: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; text-decoration: none; padding: 0;"
                                onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Google reCAPTCHA -->
                <div class="recaptcha-container">
                    <?php if (!empty($recaptcha_site_key)): ?>
                        <div class="g-recaptcha" 
                             data-sitekey="<?= $recaptcha_site_key ?>"
                             data-callback="enableSubmitBtn"
                             data-expired-callback="disableSubmitBtn"
                             data-error-callback="disableSubmitBtn"></div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            reCAPTCHA belum dikonfigurasi. Hubungi administrator.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login" id="loginBtn" disabled>
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Masuk ke Sistem
                </button>

                <!-- Additional Features -->
                <div class="text-center mt-4">
                    <div class="row text-muted" style="font-size: 12px;">
                        <div class="col-4">
                            <i class="fas fa-shield-alt text-success"></i><br>
                            <small>Aman</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-clock text-primary"></i><br>
                            <small>24/7</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-mobile-alt text-info"></i><br>
                            <small>Responsive</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="copyright">
        <?= get_parameter('site-copyright', '&copy; 2023 Perpustakaan Nasional RI') ?>
    </div>
</div>

<!-- Google reCAPTCHA Script -->
<?php if (!empty($recaptcha_site_key)): ?>
<script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
<?php endif; ?>

<script>
// reCAPTCHA callback functions
function enableSubmitBtn() {
    console.log('reCAPTCHA verified successfully');
    document.getElementById('loginBtn').disabled = false;
}

function disableSubmitBtn() {
    console.log('reCAPTCHA expired or failed');
    document.getElementById('loginBtn').disabled = true;
}

// Fungsi untuk memastikan reCAPTCHA dimuat dengan benar
function onRecaptchaLoad() {
    console.log('reCAPTCHA loaded successfully');
}

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

// Form submission with loading state
document.getElementById('loginForm').addEventListener('submit', function(e) {
    <?php if (!empty($recaptcha_site_key)): ?>
    // Cek apakah reCAPTCHA sudah dimuat
    if (typeof grecaptcha === 'undefined') {
        e.preventDefault();
        alert('reCAPTCHA belum dimuat. Silakan refresh halaman dan coba lagi.');
        return false;
    }
    
    const recaptchaResponse = grecaptcha.getResponse();
    
    if (!recaptchaResponse || recaptchaResponse.length === 0) {
        e.preventDefault();
        alert('Harap selesaikan verifikasi reCAPTCHA terlebih dahulu.');
        return false;
    }
    <?php endif; ?>
    
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.classList.add('loading');
    loginBtn.innerHTML = 'Memproses...';
    loginBtn.disabled = true;
});

// Focus enhancement
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.querySelector('.input-icon').style.color = '#0343A7';
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.querySelector('.input-icon').style.color = '#a0aec0';
        }
    });
});

// Enhanced security: Clear form on page unload
window.addEventListener('beforeunload', function() {
    document.getElementById('password').value = '';
    <?php if (!empty($recaptcha_site_key)): ?>
    if (typeof grecaptcha !== 'undefined') {
        try {
            grecaptcha.reset();
        } catch (e) {
            console.log('reCAPTCHA reset failed:', e);
        }
    }
    <?php endif; ?>
});

// Auto-focus on first empty field
window.addEventListener('load', function() {
    const loginField = document.getElementById('login');
    const passwordField = document.getElementById('password');
    
    if (!loginField.value) {
        loginField.focus();
    } else if (!passwordField.value) {
        passwordField.focus();
    }
});

// Reset reCAPTCHA on form reset
document.getElementById('loginForm').addEventListener('reset', function() {
    <?php if (!empty($recaptcha_site_key)): ?>
    if (typeof grecaptcha !== 'undefined') {
        try {
            grecaptcha.reset();
        } catch (e) {
            console.log('reCAPTCHA reset failed:', e);
        }
    }
    disableSubmitBtn();
    <?php endif; ?>
});

// Debugging: Log reCAPTCHA status
<?php if (!empty($recaptcha_site_key)): ?>
document.addEventListener('DOMContentLoaded', function() {
    console.log('reCAPTCHA Site Key:', '<?= $recaptcha_site_key ?>');
    
    // Tunggu reCAPTCHA dimuat
    const checkRecaptcha = setInterval(function() {
        if (typeof grecaptcha !== 'undefined') {
            console.log('reCAPTCHA API loaded successfully');
            clearInterval(checkRecaptcha);
        }
    }, 100);
    
    // Timeout setelah 10 detik
    setTimeout(function() {
        clearInterval(checkRecaptcha);
        if (typeof grecaptcha === 'undefined') {
            console.error('reCAPTCHA failed to load after 10 seconds');
        }
    }, 10000);
});
<?php endif; ?>
</script>

<?= $this->endSection('page'); ?>