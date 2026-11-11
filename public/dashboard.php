<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();

// ساخت لینک اختصاصی
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$profileUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $dir . '/?u=' . urlencode($user['username']);

// گرفتن پیام‌ها (قبل از mark-as-read تا وضعیت اولیه هم داشته باشیم)
$stmt = $pdo->prepare('SELECT id, body, created_at, is_read
                       FROM messages
                       WHERE receiver_id = ?
                       ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$messages = $stmt->fetchAll();

$unreadCount = 0;
foreach ($messages as $m) {
    if (!(int)$m['is_read']) $unreadCount++;
}

// علامت‌گذاری به‌عنوان خوانده‌شده
if ($unreadCount > 0) {
    $pdo->prepare('UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND is_read = 0')
        ->execute([$user['id']]);
}

$pageTitle  = 'داشبورد';
$activePage = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>

<div class="card inbox">
    <div class="topbar">
        <h2>سلام <?= e($user['username']) ?> 👋</h2>
        <?php if ($unreadCount > 0): ?>
            <span class="badge badge-accent"><?= $unreadCount ?> پیام جدید</span>
        <?php endif; ?>
    </div>

    <p style="font-size:14px;color:#a8adc7;margin-bottom:6px;">
        لینک اختصاصی تو (با این لینک برات پیام ناشناس می‌فرستن):
    </p>
    <div class="copy-row">
        <input readonly value="<?= e($profileUrl) ?>" onclick="this.select()">
        <button type="button" class="btn-inline" data-copy="<?= e($profileUrl) ?>">کپی</button>
    </div>

    <h3 style="margin-top:28px;">
        صندوق پیام‌ها
        <span style="font-size:14px;color:#8b90a8;font-weight:400;">(<?= count($messages) ?>)</span>
    </h3>

    <?php if (!$messages): ?>
        <div class="empty-state">
            <p>هنوز پیامی نداری 🌙</p>
            <p style="font-size:13px;color:#8b90a8;">
                لینکت رو با دوستات به اشتراک بذار تا بتونن ناشناس بهت پیام بدن.
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
            <div class="message <?= (int)$m['is_read'] ? '' : 'unread' ?>">
                <div class="message-body"><?= nl2br(e($m['body'])) ?></div>
                <div class="message-meta">
                    <time datetime="<?= e($m['created_at']) ?>">
                        <?= e(date('Y/m/d H:i', strtotime($m['created_at']))) ?>
                    </time>
                    <?php if (!(int)$m['is_read']): ?>
                        <span class="dot-new"></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>