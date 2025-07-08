<?php
helper(['parameter']);
$request = service('request');


$container_header_class = get_parameter('container-header-class') . " " . get_parameter('container-sidebar-class') . " " . get_parameter('container-footer-class');
if (is_profiling()) {
    $container_header_class = 'fixed-header';
}

if ($request->getVar('fullscreen') == 1) {
    $container_header_class .= ' closed-sidebar';
}

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
            font-size: 25px;
            margin: .75rem 0;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            position: relative;
        }
    </style>
    <?= $this->include('App\Views\layout\partial\style'); ?>
    <?= $this->include('App\Views\layout\partial\style_custom'); ?>
    <?= $this->renderSection('style'); ?>
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow <?= $container_header_class ?>">
        <?php if (get_parameter('branch', 0) == '1') : ?>
            <?= $this->include('App\Views\layout\partial\header_branch'); ?>
        <?php else : ?>
            <?= $this->include('App\Views\layout\partial\header'); ?>
        <?php endif; ?>


        <?php if (is_member('admin')) : ?>
            <?php if (get_parameter('show-layout-setting') == '1') : ?>
                <?= $this->include('App\Views\layout\partial\setting'); ?>
            <?php endif; ?>
        <?php endif; ?>
        <div class="app-main">
            <?= $this->include('App\Views\layout\partial\sidebar'); ?>
            <div class="app-main__outer">
                <?= $this->renderSection('page'); ?>
                <?= $this->include('App\Views\layout\partial\footer'); ?>
            </div>
        </div>
    </div>
    <?= $this->include('App\Views\layout\partial\script'); ?>
    <?= $this->include('App\Views\layout\partial\script_custom'); ?>

    <script>
        $(document).ready(function() {
            function updateClock() {
                const now = new Date();
                const daysOfWeek = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
                const dayOfWeek = daysOfWeek[now.getDay()];
                const dayOfMonth = now.getDate();
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');

                const dayString = `${dayOfWeek}, ${dayOfMonth} ${month} ${year}`;
                const clockString = `${hours}:${minutes}:${seconds}`;
                $('#day').text(dayString);
                $('#clock').text(clockString);
            }

            setInterval(updateClock, 1000);

            $(document).keypress(
                function(event) {
                    if (event.which == '13') {
                        event.preventDefault();
                    }
                }
            );
        });
    </script>
    <?= $this->renderSection('script'); ?>
</body>

</html>