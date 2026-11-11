<?php
// includes/header.php
// قبل از include این متغیرها باید تعریف بشن:
$pageTitle  = $pageTitle  ?? 'پیام ناشناس';
$activePage = $activePage ?? '';
$user       = current_user();
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <a href="index.php" class="brand">
        <span class="dot"></span> ناشناس
    </a>
    <nav class="nav">
        <a href="index.php" class="<?= $activePage === 'send' ? 'active' : '' ?>">ارسال</a>
        <?php if ($user): ?>
            <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">داشبورد</a>
            <a href="logout.php" class="btn-ghost">خروج</a>
        <?php else: ?>
            <a href="login.php" class="<?= $activePage === 'login' ? 'active' : '' ?>">ورود</a>
            <a href="register.php" class="btn-ghost">ثبت‌نام</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">