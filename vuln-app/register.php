<?php
require 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $e = $_POST['email'] ?? '';
    $sql = "INSERT INTO users (username, password, email) VALUES ('$u', '$p', '$e')";
    if ($conn->query($sql)) { $msg = "User '$u' registered. Password stored as: <b>$p</b>"; }
    else { $msg = "Error: " . $conn->error; }
}
?>
<!DOCTYPE html>
<html><head><title>Register</title>
<style>body{font-family:system-ui;max-width:420px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input{width:100%;padding:10px;margin:6px 0 14px;background:#111827;border:1px solid #1f2b45;color:#fff;border-radius:6px}
button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer;width:100%}
.msg{background:#111827;padding:10px;border-radius:6px;margin-bottom:14px}</style></head>
<body>
  <h1>Register</h1>
  <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
  <form method="POST">
    <label>Username</label><input name="username">
    <label>Password</label><input name="password">
    <label>Email</label><input name="email" type="email">
    <button>Create account</button>
  </form>
</body></html>
