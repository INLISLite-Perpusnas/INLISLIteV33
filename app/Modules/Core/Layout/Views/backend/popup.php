<?php helper(['parameter']); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?? get_parameter('site-name') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="icon" href="<?= base_url(get_parameter('favicon')) ?>">
    <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <?= $this->renderSection('style'); ?>
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow">
        <?= $this->renderSection('page'); ?>
    </div>
    <?= $this->include('Layout\Views\backend\partial\script'); ?>
    <?= $this->renderSection('script'); ?>
</body>

</html>