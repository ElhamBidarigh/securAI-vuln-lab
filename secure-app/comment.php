<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $body = trim($_POST['body'] ?? '');
    if ($body === '' || mb_strlen($body) > 2000) { $msg = 'Comment must be 1-2000 characters.'; }
    else {
        $uid = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO comments (user_id, body) VALUES (?, ?)");
        $stmt->bind_param('is', $uid, $body);
        $stmt->execute();
        $msg = 'Comment posted.';
    }
}
?>
<!DOCTYPE html>
<html><head><title>Post comment</title>
<style>body{font-family:system-ui;max-width:620px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
textarea{width:100%;height:100px;padding:10px;background:#111827;border:1px solid #1f2b45;color:#fff;border-radius:6px}
button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer}
.comment{background:#111827;padding:14px;border-radius:8px;margin-bottom:10px;white-space:pre-wrap;word-break:break-word}</style></head>
<body>
  <h1>Post a comment — Secure</h1>
  <?php if ($msg): ?><p style="color:#34d399"><?= htmlspecialchars($msg, ENT_QUOTES) ?></p><?php endif; ?>
  <form method="POST">
    <?= csrf_field() ?>
    <textarea name="body" placeholder="Your comment..."></textarea>
    <button>Post</button>
  </form>
  <h2>All comments</h2>
  <?php
  $res = $conn->query("SELECT c.body, u.username FROM comments c JOIN users u ON u.id=c.user_id ORDER BY c.id DESC");
  while ($row = $res->fetch_assoc()) {
      echo '<div class="comment"><b>' . htmlspecialchars($row['username'], ENT_QUOTES) . '</b>: ' .
           htmlspecialchars($row['body'], ENT_QUOTES) . '</div>';
  }
  ?>
</body></html>
