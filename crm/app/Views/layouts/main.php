<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Training Center CRM') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<?php if (is_logged_in()): ?>
    <?php partial('nav'); ?>
<?php endif; ?>

<main class="container">
    <?php partial('flash'); ?>
    <?= $content ?? '' ?>
</main>

</body>
</html>
