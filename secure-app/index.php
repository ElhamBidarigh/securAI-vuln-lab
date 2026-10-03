<?php require 'config.php'; ?>
<!DOCTYPE html>
<html><head><title>SecurAI Secure App</title>
<style>body{font-family:system-ui;max-width:720px;margin:40px auto;padding:0 20px;background:#0b0f19;color:#e6edf7}
a{color:#10b981;margin-right:16px}.banner{background:#064e3b;padding:12px 16px;border-radius:8px;margin-bottom:24px}
.comment{background:#111827;padding:14px;border-radius:8px;margin-bottom:10px;white-space:pre-wrap;word-break:break-word}</style></head>
<body>
  <div class="banner">✅ SECURE APP — All vulnerabilities fixed</div>
  <h1>SecurAI Secure App</h1>
  <nav><a href="index.php">Home</a><a href="login.php">Login</a>
  <a href="register.php">Register</a><a href="dashboard.php">Dashboard</a></nav>
  <hr style="border-color:#1f2b45;margin:20px 0">
  <h2>Latest comments</h2>
  <?php
  $res = $conn->query("SELECT c.body, u.username FROM comments c JOIN users u ON u.id=c.user_id ORDER BY c.id DESC");
  while ($row = $res->fetch_assoc()) {
      echo '<div class="comment"><b>' . htmlspecialchars($row['username'], ENT_QUOTES) . '</b>: ' .
           htmlspecialchars($row['body'], ENT_QUOTES) . '</div>';
  }
  ?>
</body></html>
