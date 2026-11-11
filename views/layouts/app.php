<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'پیام ناشناس') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="container"><?= $content ?></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/app.js"></script>
</body>
</html>