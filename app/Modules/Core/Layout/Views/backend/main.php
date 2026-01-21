<?php
helper(['parameter']);
$request = service('request');

$fullscreen =  '';
if ($request->getGet('fullscreen') == 1) {
    $fullscreen = 'closed-sidebar';
}
?>

<?php
$cart_ref_code = false;
$cart_total_items = 0;
$cart_total = 0;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?= base_url(get_parameter('favicon')) ?>">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?? get_parameter('site-name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.css">
    <?php if (get_parameter('show-logo-sidebar') == '1') : ?>
        <style>
            .app-header.header-text-dark .app-header__logo .logo-src {
                height: 23px;
                width: 97px;
                background: url("<?= base_url() . get_parameter('logo-small'); ?>");
            }

            .app-header.header-text-light .app-header__logo .logo-src {
                height: 23px;
                width: 97px;
                background: url("<?= base_url() . get_parameter('logo-small'); ?>");
            }
        </style>
    <?php else : ?>
        <style>
            .app-header.header-text-dark .app-header__logo .logo-src {
                background: none;
            }

            .app-header.header-text-light .app-header__logo .logo-src {
                background: none;
            }
        </style>
    <?php endif; ?>
    <style>
        .site-name {
            font-size: 18px;
            margin: .75rem 0;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            position: relative;
        }
    </style>
    <?= $this->include('Layout\Views\backend\partial\style'); ?>
    <?= $this->include('Layout\Views\backend\partial\style_custom'); ?>
    <?= $this->renderSection('style'); ?>
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow <?= $fullscreen ?> <?= get_parameter('container-header-class') ?> <?= get_parameter('container-sidebar-class') ?> <?= get_parameter('container-footer-class') ?>">
        <?= $this->include('Layout\Views\backend\partial\header'); ?>
        <div class="app-main">
            <?= $this->include('Layout\Views\backend\partial\sidebar'); ?>
            <div class="app-main__outer">
                <?= $this->renderSection('page'); ?>
                <?= $this->include('Layout\Views\backend\partial\footer'); ?>
            </div>
        </div>
    </div>
    <?php if ($cart_ref_code) : ?>
        <?= $this->include('Layout\Views\backend\partial\drawer'); ?>
    <?php endif; ?>
    <?= $this->include('Layout\Views\backend\partial\script'); ?>
    <?= $this->include('Layout\Views\backend\partial\script_custom'); ?>
    <?= $this->renderSection('script'); ?>
</body>

</html>