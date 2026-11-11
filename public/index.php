<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

$isAjax  = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $body     = trim($_POST['body'] ?? '');

    if ($username === '') {
        $errors[] = 'نام کاربری گیرنده را وارد کنید.';
    }
    if ($body === '' || mb_strlen($body) > 1000) {
        $errors[] = 'پیام باید بین ۱ تا ۱۰۰۰ کاراکتر باشد.';
    }

    if (!$errors) {
        $receiver = find_user_by_username($pdo, $username);
        if (!$receiver) {
            $errors[] = 'کاربری با این نام پیدا نشد.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO messages (receiver_id, body) VALUES (?, ?)');
            $stmt->execute([$receiver['id'], $body]);
            $success = 'پیام شما به صورت ناشناس ارسال شد ✨';
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['errors' => $errors, 'success' => $success], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$pageTitle  = 'ارسال پیام ناشناس';
$activePage = 'send';
require __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <h1>پیام ناشناس</h1>
    <p style="color:#a8adc7;font-size:14px;margin-top:-8px;">
        بدون ثبت‌نام پیام بفرست. گیرنده هرگز نمی‌فهمه از طرف کیه.
    </p>

    <?php foreach ($errors as $err): ?>
        <div class="msg error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <div class="msg success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off" data-ajax-send>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <label for="username">نام کاربری گیرنده</label>
        <input id="username" name="username" required maxlength="30"
               value="<?= e($_POST['username'] ?? '') ?>">

        <label for="body">متن پیام</label>
        <textarea id="body" name="body" required maxlength="1000"
                  placeholder="سلام... یه چیزی می‌خواستم بگم 👀"><?= e($_POST['body'] ?? '') ?></textarea>

        <button type="submit">ارسال ناشناس</button>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>