<?php
require 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php'); exit;
    } else { $error = "Invalid credentials. " . $conn->error; }
}
?>
<!DOCTYPE html>
<html><head><title>Login</title>
<style>body{font-family:system-ui;max-width:420px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input{width:100%;padding:10px;margin:6px 0 14px;background:#111827;border:1px solid #1f2b45;color:#fff;border-radius:6px}
button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer;width:100%}
.err{color:#f87171;margin-bottom:14px}</style></head>
<body>
  <h1>Login</h1>
  <?php if ($error): ?><div class="err"><?= $error ?></div><?php endif; ?>
  <form method="POST">
    <label>Username</label><input name="username" autocomplete="off">
    <label>Password</label><input name="password" type="password">
    <button>Sign in</button>
  </form>
  <p style="color:#8b9bb4;font-size:13px">Hint: try <code>' OR '1'='1' -- </code> in username.</p>
</body></html>
