<?php
require 'config.php';
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $e = trim($_POST['email'] ?? '');
    if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $u)) { $err = 'Username must be 3-32 chars.'; }
    elseif (strlen($p) < 10) { $err = 'Password must be at least 10 characters.'; }
    elseif (!filter_var($e, FILTER_VALIDATE_EMAIL)) { $err = 'Invalid email.'; }
    else {
        $hash = password_hash($p, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $u, $hash, $e);
        try { $stmt->execute(); $msg = 'Account created. You can now log in.'; }
        catch (mysqli_sql_exception $ex) { $err = 'Username already taken.'; }
    }
}
?>
<!DOCTYPE html>
<html><head><title>Register</title>
<style>body{font-family:system-ui;max-width:420px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input{width:100%;padding:10px;margin:6px 0 14px;background:#111827;border:1px solid #1f2b45;color:#fff;border-radius:6px}
button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer;width:100%}
.msg{color:#34d399;margin-bottom:12px}.err{color:#f87171;margin-bottom:12px}</style></head>
<body>
  <h1>Register — Secure</h1>
  <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg, ENT_QUOTES) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="err"><?= htmlspecialchars($err, ENT_QUOTES) ?></div><?php endif; ?>
  <form method="POST">
    <?= csrf_field() ?>
    <label>Username</label><input name="username">
    <label>Password (min 10 chars)</label><input name="password" type="password">
    <label>Email</label><input name="email" type="email">
    <button>Create account</button>
  </form>
</body></html>
