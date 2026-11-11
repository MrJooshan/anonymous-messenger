<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

if (current_user()) {
    header('Location: dashboard.php');
    exit;
}

$errors   = [];
$username = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    // اعتبارسنجی
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $errors[] = 'نام کاربری باید ۳ تا ۳۰ کاراکتر و شامل حروف انگلیسی، عدد یا _ باشد.';
    }
    // کلمات رزرو شده
    $reserved = ['admin', 'root', 'api', 'login', 'register', 'dashboard', 'logout'];
    if (in_array(strtolower($username), $reserved, true)) {
        $errors[] = 'این نام کاربری قابل استفاده نیست.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ایمیل معتبر نیست.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'رمز عبور باید حداقل ۶ کاراکتر باشد.';
    }
    if ($password !== $confirm) {
        $errors[] = 'رمز عبور و تکرار آن یکسان نیستند.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'این نام کاربری یا ایمیل قبلاً ثبت شده است.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, $hash]);

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'       => (int)$pdo->lastInsertId(),
                'username' => $username,
            ];
            header('Location: dashboard.php');
            exit;
        }
    }
}

$pageTitle  = 'ثبت‌نام';
$activePage = 'register';
require __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <h1>ساخت حساب</h1>
    <p style="color:#a8adc7;font-size:14px;margin-top:-8px;">
        برای دریافت پیام‌های ناشناس به یک حساب نیاز داری.
    </p>

    <?php foreach ($errors as $err): ?>
        <div class="msg error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <label for="username">نام کاربری</label>
        <input id="username" name="username" required maxlength="30"
               value="<?= e($username) ?>" autofocus>
        <small class="hint">فقط حروف انگلیسی، عدد و _ (۳ تا ۳۰ کاراکتر). همین نام رو دیگران برای پیام دادن به تو استفاده می‌کنن.</small>

        <label for="email">ایمیل</label>
        <input id="email" name="email" type="email" required maxlength="120"
               value="<?= e($email) ?>">

        <label for="password">رمز عبور</label>
        <input id="password" name="password" type="password" required minlength="6">

        <label for="password_confirm">تکرار رمز عبور</label>
        <input id="password_confirm" name="password_confirm" type="password" required minlength="6">

        <button type="submit">ثبت‌نام</button>
    </form>

    <p style="text-align:center;margin-top:18px;font-size:14px;color:#a8adc7;">
        حساب داری؟
        <a class="link" href="login.php">وارد شو</a>
    </p>
    <p style="text-align:center;margin-top:4px;font-size:13px;">
        <a class="link" href="index.php">← بازگشت به صفحه ارسال ناشناس</a>
    </p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>