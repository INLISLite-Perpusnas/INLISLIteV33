<style>
    /* Gaya kartu yang sama dengan halaman login (authlogin.php) */
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
    .login-title { margin: 0 0 12px; color: #111; font-size: 28px; font-weight: 700; line-height: 1.3; }
    .login-description { margin: 0 0 28px; color: #595959; font-size: 16px; line-height: 1.5; }
    .login-container .form-group { margin-bottom: 18px; }
    .login-container .form-label { display: block; margin-bottom: 8px; color: #222; font-size: 14px; font-weight: 500; }
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
    .login-container .form-control:focus { border-color: var(--login-blue); box-shadow: 0 0 0 3px rgba(3, 67, 167, .15); }
    .login-container .form-text { color: #666; font-size: 13px; }
    .login-button {
        display: block;
        width: 100%;
        min-height: 52px;
        margin-top: 24px;
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
    .login-button:disabled { cursor: progress; }
    .back-link { display: inline-block; margin-top: 18px; color: var(--login-blue); font-size: 14px; font-weight: 600; text-decoration: none; }
    .back-link:hover { color: #002f79; text-decoration: underline; }
    .login-button:focus-visible, .back-link:focus-visible { outline: 3px solid var(--login-blue); outline-offset: 3px; }
    .login-container .alert { margin-bottom: 18px; border-radius: 8px; font-size: 14px; }
    .login-copyright { margin: 24px 0 0; color: #666; font-size: 13px; text-align: center; }
    @media (max-width: 600px) {
        .login-container { padding: 20px 16px; }
        .login-card { padding: 28px 20px; }
    }
</style>
