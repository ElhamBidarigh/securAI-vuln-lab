<?php require 'config.php'; ?>
<!DOCTYPE html>
<html><head><title>SecurAI Vuln Lab</title>
<style>body{font-family:system-ui;max-width:720px;margin:40px auto;padding:0 20px;background:#0b0f19;color:#e6edf7}
a{color:#10b981;margin-right:16px}.banner{background:#7f1d1d;padding:12px 16px;border-radius:8px;margin-bottom:24px}
.comment{background:#111827;padding:14px;border-radius:8px;margin-bottom:10px}</style></head>
<body>
  <div class="banner">⚠️ VULNERABLE APP — Do not deploy publicly</div>
  <h1>SecurAI Vuln Lab</h1>
  <nav><a href="index.php">Home</a><a href="login.php">Login</a>
  <a href="register.php">Register</a><a href="dashboard.php">Dashboard</a></nav>
  <hr style="border-color:#1f2b45;margin:20px 0">
  <h2>Latest comments</h2>
  <?php
  $res = $conn->query("SELECT c.body, u.username FROM comments c JOIN users u ON u.id=c.user_id ORDER BY c.id DESC");
  while ($row = $res->fetch_assoc()) {
      echo '<div class="comment"><b>' . $row['username'] . '</b>: ' . $row['body'] . '</div>';
  }
  ?>
</body></html>
