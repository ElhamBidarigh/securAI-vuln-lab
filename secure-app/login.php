<?php
require 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') { $error = 'Invalid credentials.'; }
    else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php'); exit;
        }
        $error = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html><head><title>Login</title>
<style>body{font-family:system-ui;max-width:420px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input{width:100%;padding:10px;margin:6px 0 14px;background:#111827;border:1px solid #1f2b45;color:#fff;border-radius:6px}
button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer;width:100%}
.err{color:#f87171;margin-bottom:14px}</style></head>
<body>
  <h1>Login — Secure</h1>
  <?php if ($error): ?><div class="err"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  <form method="POST">
    <?= csrf_field() ?>
    <label>Username</label><input name="username" autocomplete="username">
    <label>Password</label><input name="password" type="password" autocomplete="current-password">
    <button>Sign in</button>
  </form>
</body></html>
