<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Training Center CRM') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body style="background:#0f172a">
<main class="container" style="max-width:520px">
    <?php partial('flash'); ?>
    <?= $content ?? '' ?>
</main>
</body>
</html>
