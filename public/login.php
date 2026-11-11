<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

if (current_user()) {
    header('Location: dashboard.php');
    exit;
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'نام کاربری و رمز عبور را وارد کنید.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // جلوگیری از session fixation
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
            ];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'نام کاربری یا رمز عبور اشتباه است.';
    }
}

$pageTitle  = 'ورود';
$activePage = 'login';
require __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <h1>ورود</h1>
    <p style="color:#a8adc7;font-size:14px;margin-top:-8px;">
        برای دیدن پیام‌های ناشناس وارد حساب خودت شو.
    </p>

    <?php if ($error): ?>
        <div class="msg error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <label for="username">نام کاربری یا ایمیل</label>
        <input id="username" name="username" required maxlength="120"
               value="<?= e($username) ?>" autofocus>

        <label for="password">رمز عبور</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">ورود</button>
    </form>

    <p style="text-align:center;margin-top:18px;font-size:14px;color:#a8adc7;">
        حساب نداری؟
        <a class="link" href="register.php">ثبت‌نام کن</a>
    </p>
    <p style="text-align:center;margin-top:4px;font-size:13px;">
        <a class="link" href="index.php">← بازگشت به صفحه ارسال ناشناس</a>
    </p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>