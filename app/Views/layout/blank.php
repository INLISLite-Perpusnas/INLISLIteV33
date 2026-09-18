<?php
helper(['parameter']);
$isAuthLogin = !empty($is_auth_login);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="icon" href="<?= esc(base_url(get_parameter('favicon') ?? ''), 'attr') ?>">
    <meta http-equiv="Content-Language" content="id">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= esc($title ?? get_parameter('site-name')) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($isAuthLogin): ?>
        <meta name="theme-color" content="#667eea">
    <?php endif; ?>
    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">
    <?php if ($isAuthLogin): ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <?php if (!empty($hcaptcha_site_key)): ?>
            <link rel="preconnect" href="https://js.hcaptcha.com">
            <link rel="dns-prefetch" href="//newassets.hcaptcha.com">
        <?php endif; ?>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
        <link rel="preload" href="<?= base_url('assets/fonts/auth/fa-solid-subset.woff2') ?>" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="<?= base_url('assets/css/auth-icons.css') ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <?php endif; ?>
    <style>
        .bg-corporate-primary{
            background-color: <?=get_parameter('corporate-primary','#C21B18')?> !important;
        }
        .bg-corporate-primary2{
            background-color: <?=get_parameter('corporate-primary2','#E9583C')?> !important;
        }
        .bg-corporate-secondary{
            background-color: <?=get_parameter('corporate-secondary','#7B0E23')?>  !important;
        }
        .bg-corporate-secondary2{
            background-color: <?=get_parameter('corporate-secondary2','#8D1230')?>  !important;
        }

        .app-page-title {
            padding: 15px 30px;
        }

        .app-page-title .page-title-icon {
            padding: 0px;
            width: 50px;
            height: 50px;
        }

        .errors {
            color: red;
        }

        .app-header__logo .logo-src {
            width: 150px;
        }
    </style>
    <?= $this->renderSection('style'); ?>
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow">
        <?= $this->renderSection('page'); ?>
    </div>
    <?php if (!$isAuthLogin): ?>
    <?= $this->include('App\Views\layout\partial\script'); ?>
    <?php endif; ?>
    <?= $this->renderSection('script'); ?>
    <?php if (!$isAuthLogin): ?>
    <script>
        var toastr_msg = '<?= get_message('toastr_msg'); ?>';
        var toastr_type = '<?= get_message('toastr_type'); ?>';
        if (toastr_msg.length > 0) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-full-width",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "3500",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr[toastr_type](toastr_msg, "Information");
        }
    </script>
    <?php endif; ?>
</body>

</html>
